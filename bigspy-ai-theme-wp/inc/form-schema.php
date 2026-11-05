<?php
/**
 * Built-in form schema (G-57): stable field IDs, a schema version, show/hide
 * conditions and dependency checks.
 *
 * - Every field gets an `id` that never changes when its label, mail-tag
 *   `name` or position changes. Conditions (and later formulas) point at IDs.
 * - Old forms (no IDs, no version) keep their names, mail tags and entries.
 *   Missing IDs are derived from the field name, so running the migration
 *   again gives the same result.
 * - A hidden field is skipped: not required, not validated, value ''.
 *
 * Pure functions: no database, no output. The engine is in form-native.php.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Current schema version written by the editor. */
function bst_form_schema_version() {
	return 2;
}

/** Condition operators. */
function bst_form_condition_ops() {
	return array( 'is', 'is_not', 'empty', 'not_empty', 'contains', 'gt', 'lt' );
}

/** Valid field ID: a-z start, then a-z 0-9 _ (2–32 chars). */
function bst_form_valid_id( $id ) {
	return is_string( $id ) && (bool) preg_match( '/^[a-z][a-z0-9_]{1,31}$/', $id );
}

/** ID for a field saved before IDs existed: derived from its name, so it is stable. */
function bst_form_legacy_id( $name ) {
	$id = 'n_' . substr( preg_replace( '/[^a-z0-9]+/', '_', strtolower( (string) $name ) ), 0, 29 );
	return rtrim( $id, '_' );
}

/** Clean the conditions of one field. Returns null when the field has none. */
function bst_form_clean_conditions( $c ) {
	if ( ! is_array( $c ) || empty( $c['rules'] ) || ! is_array( $c['rules'] ) ) {
		return null;
	}
	$rules = array();
	foreach ( $c['rules'] as $r ) {
		if ( ! is_array( $r ) || ! isset( $r['field'] ) ) {
			continue;
		}
		$op      = in_array( $r['op'] ?? 'is', bst_form_condition_ops(), true ) ? $r['op'] : 'is';
		$rules[] = array(
			'field' => (string) $r['field'],
			'op'    => $op,
			'value' => mb_substr( (string) ( $r['value'] ?? '' ), 0, 200 ),
		);
		if ( count( $rules ) >= 10 ) {
			break;
		}
	}
	if ( ! $rules ) {
		return null;
	}
	return array(
		'action' => 'hide' === ( $c['action'] ?? 'show' ) ? 'hide' : 'show',
		'match'  => 'any' === ( $c['match'] ?? 'all' ) ? 'any' : 'all',
		'rules'  => $rules,
	);
}

/**
 * Normalize fields to the current schema: every field has a unique ID and
 * clean conditions. Names, labels and other keys are kept as saved.
 * Idempotent: normalize( normalize( x ) ) === normalize( x ).
 */
function bst_form_schema_normalize( $fields ) {
	$out  = array();
	$used = array();
	foreach ( (array) $fields as $f ) {
		if ( ! is_array( $f ) || empty( $f['name'] ) ) {
			continue;
		}
		$id = isset( $f['id'] ) && bst_form_valid_id( $f['id'] ) ? $f['id'] : bst_form_legacy_id( $f['name'] );
		if ( ! bst_form_valid_id( $id ) ) {
			$id = 'n_field';
		}
		$base = $id;
		$i    = 2;
		while ( isset( $used[ $id ] ) ) {
			$id = substr( $base, 0, 28 ) . '_' . $i++;
		}
		$used[ $id ] = true;
		$f['id']     = $id;
		$c           = bst_form_clean_conditions( $f['conditions'] ?? null );
		if ( $c ) {
			$f['conditions'] = $c;
		} else {
			unset( $f['conditions'] );
		}
		$out[] = $f;
	}
	return $out;
}

/** IDs a field depends on (its condition rules). */
function bst_form_field_deps( $f ) {
	$deps = array();
	foreach ( (array) ( $f['conditions']['rules'] ?? array() ) as $r ) {
		$deps[] = (string) $r['field'];
	}
	return array_values( array_unique( $deps ) );
}

/** Fields (IDs) that reference this ID. Used to block deleting a field in use. */
function bst_form_schema_references( $fields, $id ) {
	$hits = array();
	foreach ( (array) $fields as $f ) {
		if ( in_array( $id, bst_form_field_deps( $f ), true ) ) {
			$hits[] = $f['id'];
		}
	}
	return $hits;
}

/**
 * Check a normalized schema. Returns a list of problems, each
 * array( 'field' => id, 'code' => ..., 'ref' => id ). Empty = valid.
 * Codes: duplicate_id, duplicate_name, missing_ref, self_ref, cycle.
 */
function bst_form_schema_check( $fields ) {
	$errors = array();
	$ids    = array();
	$names  = array();
	foreach ( (array) $fields as $f ) {
		$id = (string) ( $f['id'] ?? '' );
		if ( isset( $ids[ $id ] ) ) {
			$errors[] = array( 'field' => $id, 'code' => 'duplicate_id', 'ref' => $id );
		}
		$ids[ $id ] = $f;
		$name       = (string) ( $f['name'] ?? '' );
		if ( isset( $names[ $name ] ) ) {
			$errors[] = array( 'field' => $id, 'code' => 'duplicate_name', 'ref' => $names[ $name ] );
		} else {
			$names[ $name ] = $id;
		}
	}
	foreach ( (array) $fields as $f ) {
		foreach ( bst_form_field_deps( $f ) as $dep ) {
			if ( $dep === $f['id'] ) {
				$errors[] = array( 'field' => $f['id'], 'code' => 'self_ref', 'ref' => $dep );
			} elseif ( ! isset( $ids[ $dep ] ) ) {
				$errors[] = array( 'field' => $f['id'], 'code' => 'missing_ref', 'ref' => $dep );
			}
		}
	}
	if ( null === bst_form_schema_order( $fields ) ) {
		foreach ( bst_form_schema_cycle( $fields ) as $id ) {
			$errors[] = array( 'field' => $id, 'code' => 'cycle', 'ref' => $id );
		}
	}
	return $errors;
}

/**
 * Evaluation order (dependencies first), or null when the conditions loop.
 * Self and missing references are ignored here; the check reports them.
 */
function bst_form_schema_order( $fields ) {
	$deps = array();
	foreach ( (array) $fields as $f ) {
		$deps[ $f['id'] ] = array();
	}
	foreach ( (array) $fields as $f ) {
		foreach ( bst_form_field_deps( $f ) as $d ) {
			if ( $d !== $f['id'] && isset( $deps[ $d ] ) ) {
				$deps[ $f['id'] ][] = $d;
			}
		}
	}
	$order = array();
	$done  = array();
	while ( count( $order ) < count( $deps ) ) {
		$moved = false;
		foreach ( $deps as $id => $list ) {
			if ( isset( $done[ $id ] ) || array_diff( $list, array_keys( $done ) ) ) {
				continue;
			}
			$done[ $id ] = true;
			$order[]     = $id;
			$moved       = true;
		}
		if ( ! $moved ) {
			return null;
		}
	}
	return $order;
}

/** IDs left over when ordering fails: the fields in or behind a loop. */
function bst_form_schema_cycle( $fields ) {
	$order = array();
	$left  = array();
	foreach ( (array) $fields as $f ) {
		$left[ $f['id'] ] = array_filter(
			bst_form_field_deps( $f ),
			function ( $d ) use ( $f ) {
				return $d !== $f['id'];
			}
		);
	}
	do {
		$moved = false;
		foreach ( $left as $id => $list ) {
			if ( ! array_intersect( $list, array_keys( $left ) ) ) {
				unset( $left[ $id ] );
				$moved = true;
			}
		}
	} while ( $moved && $left );
	return array_keys( $left );
}

/** Does one rule match the value of the field it points at? */
function bst_form_rule_match( $r, $value ) {
	$value = (string) $value;
	$want  = (string) $r['value'];
	$parts = '' === $value ? array() : array_map( 'trim', explode( ', ', $value ) );
	switch ( $r['op'] ) {
		case 'empty':
			return '' === $value;
		case 'not_empty':
			return '' !== $value;
		case 'is_not':
			return ! in_array( $want, $parts, true ) && $value !== $want;
		case 'contains':
			return '' !== $want && false !== mb_stripos( $value, $want );
		case 'gt':
		case 'lt':
			if ( ! is_numeric( $value ) || ! is_numeric( $want ) ) {
				return false;
			}
			return 'gt' === $r['op'] ? (float) $value > (float) $want : (float) $value < (float) $want;
		default:
			return in_array( $want, $parts, true ) || $value === $want;
	}
}

/**
 * Visibility of every field for these values (keyed by field name).
 * Returns array( id => bool ). A hidden field counts as empty for the fields
 * that depend on it. A schema that loops or points at missing fields shows
 * the affected fields, so a broken setup never hides inputs silently.
 */
function bst_form_schema_visibility( $fields, $values ) {
	$by_id = array();
	foreach ( (array) $fields as $f ) {
		$by_id[ $f['id'] ] = $f;
	}
	$order = bst_form_schema_order( $fields );
	$seen  = array();
	if ( null === $order ) {
		foreach ( $by_id as $id => $f ) {
			$seen[ $id ] = true;
		}
		return $seen;
	}
	foreach ( $order as $id ) {
		$f = $by_id[ $id ];
		$c = $f['conditions'] ?? null;
		if ( ! $c ) {
			$seen[ $id ] = true;
			continue;
		}
		$hits  = array();
		$valid = true;
		foreach ( $c['rules'] as $r ) {
			if ( ! isset( $by_id[ $r['field'] ] ) || $r['field'] === $id ) {
				$valid = false;
				break;
			}
			$ref    = $by_id[ $r['field'] ];
			$v      = ! empty( $seen[ $ref['id'] ] ) ? ( $values[ sanitize_key( $ref['name'] ) ] ?? '' ) : '';
			$hits[] = bst_form_rule_match( $r, $v );
		}
		if ( ! $valid ) {
			$seen[ $id ] = true;
			continue;
		}
		$met         = 'any' === $c['match'] ? in_array( true, $hits, true ) : ! in_array( false, $hits, true );
		$seen[ $id ] = 'show' === $c['action'] ? $met : ! $met;
	}
	return $seen;
}
