<?php
/**
 * Built-in form schema (G-57): stable field IDs, schema version, show/hide
 * conditions and dependency checks. Shared by the server submit, the front-end
 * markup and the editor (the editor mirrors these rules in JS).
 *
 * Field keys: id (stable, never changes after creation), name (mail tag, kept
 * for old forms), when = { logic: all|any, rules: [ { field: <id>, op, value } ] }.
 * A field without id gets "n_<name>", so old forms map the same way every time
 * and saving them again does not change their data.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Current schema version written by the editor. */
const BST_FORM_SCHEMA_VERSION = 1;

/** Condition operators. */
function bst_form_rule_ops() {
	return array( 'filled', 'empty', 'is', 'not', 'gt', 'lt' );
}

/** Stable ID of a field: its own id, else derived from its name. */
function bst_form_field_id( $f ) {
	$id = isset( $f['id'] ) ? sanitize_key( (string) $f['id'] ) : '';
	return '' !== $id ? $id : 'n_' . sanitize_key( (string) ( $f['name'] ?? '' ) );
}

/**
 * Normalize fields: every field gets a unique id; conditions are cleaned to the
 * known shape. Idempotent: normalizing twice gives the same result.
 *
 * @param array $fields Raw fields.
 * @return array
 */
function bst_form_schema_normalize( $fields ) {
	$out  = array();
	$seen = array();
	foreach ( (array) $fields as $f ) {
		if ( ! is_array( $f ) || empty( $f['name'] ) ) {
			continue;
		}
		$id = bst_form_field_id( $f );
		$base = $id;
		$i    = 2;
		while ( isset( $seen[ $id ] ) ) {
			$id = $base . '_' . $i++;
		}
		$seen[ $id ] = true;
		$f['id']     = $id;
		$f['when']   = bst_form_when_clean( $f['when'] ?? null );
		if ( ! $f['when'] ) {
			unset( $f['when'] );
		}
		$out[] = $f;
	}
	return $out;
}

/** Clean one condition group; null when it has no rules. */
function bst_form_when_clean( $when ) {
	if ( ! is_array( $when ) || empty( $when['rules'] ) || ! is_array( $when['rules'] ) ) {
		return null;
	}
	$rules = array();
	foreach ( $when['rules'] as $r ) {
		if ( ! is_array( $r ) ) {
			continue;
		}
		$field = sanitize_key( (string) ( $r['field'] ?? '' ) );
		$op    = in_array( $r['op'] ?? '', bst_form_rule_ops(), true ) ? $r['op'] : 'filled';
		if ( '' === $field ) {
			continue;
		}
		$rules[] = array( 'field' => $field, 'op' => $op, 'value' => (string) ( $r['value'] ?? '' ) );
	}
	if ( ! $rules ) {
		return null;
	}
	return array( 'logic' => 'any' === ( $when['logic'] ?? 'all' ) ? 'any' : 'all', 'rules' => $rules );
}

/**
 * Schema problems: rules pointing to a missing field, to the field itself, or
 * forming a loop. Each item: { field: id, code: missing|self|cycle, ref: id }.
 *
 * @param array $fields Normalized fields.
 * @return array
 */
function bst_form_schema_errors( $fields ) {
	$ids    = array();
	$deps   = array();
	$errors = array();
	foreach ( $fields as $f ) {
		$ids[ $f['id'] ] = true;
	}
	foreach ( $fields as $f ) {
		$deps[ $f['id'] ] = array();
		foreach ( $f['when']['rules'] ?? array() as $r ) {
			if ( $r['field'] === $f['id'] ) {
				$errors[] = array( 'field' => $f['id'], 'code' => 'self', 'ref' => $r['field'] );
			} elseif ( ! isset( $ids[ $r['field'] ] ) ) {
				$errors[] = array( 'field' => $f['id'], 'code' => 'missing', 'ref' => $r['field'] );
			} else {
				$deps[ $f['id'] ][] = $r['field'];
			}
		}
	}
	// Depth-first search; a grey node seen again closes a loop.
	$state = array();
	$walk  = function ( $id ) use ( &$walk, &$state, &$errors, $deps ) {
		$state[ $id ] = 1;
		foreach ( $deps[ $id ] as $to ) {
			if ( 1 === ( $state[ $to ] ?? 0 ) ) {
				$errors[] = array( 'field' => $id, 'code' => 'cycle', 'ref' => $to );
			} elseif ( ! isset( $state[ $to ] ) ) {
				$walk( $to );
			}
		}
		$state[ $id ] = 2;
	};
	foreach ( array_keys( $deps ) as $id ) {
		if ( ! isset( $state[ $id ] ) ) {
			$walk( $id );
		}
	}
	return $errors;
}

/** Fields whose conditions use this field id (shown before deleting it). */
function bst_form_schema_used_by( $fields, $id ) {
	$used = array();
	foreach ( $fields as $f ) {
		foreach ( $f['when']['rules'] ?? array() as $r ) {
			if ( $r['field'] === $id && $f['id'] !== $id ) {
				$used[] = $f['id'];
				break;
			}
		}
	}
	return $used;
}

/** Whether one rule matches a value (checkbox values are joined with ", "). */
function bst_form_rule_match( $r, $v ) {
	$v = (string) $v;
	switch ( $r['op'] ) {
		case 'empty':
			return '' === $v;
		case 'is':
		case 'not':
			$hit = '' !== $v && in_array( $r['value'], explode( ', ', $v ), true );
			return 'is' === $r['op'] ? $hit : ! $hit;
		case 'gt':
		case 'lt':
			if ( ! is_numeric( $v ) || ! is_numeric( $r['value'] ) ) {
				return false;
			}
			return 'gt' === $r['op'] ? (float) $v > (float) $r['value'] : (float) $v < (float) $r['value'];
		default:
			return '' !== $v;
	}
}

/**
 * Visibility of every field for these values. A hidden field counts as empty
 * for the fields that depend on it. Broken rules (missing field, self, loop)
 * are ignored, so a bad condition never hides a field.
 *
 * @param array $fields Normalized fields.
 * @param array $values name => value.
 * @return array id => bool
 */
function bst_form_visibility( $fields, $values ) {
	$by_id = array();
	foreach ( $fields as $f ) {
		$by_id[ $f['id'] ] = $f;
	}
	// A rule is ignored when its field can be reached back from the field it
	// points to (self or loop), so every rule inside a loop is dropped, not one.
	$deps = array();
	foreach ( $fields as $f ) {
		$deps[ $f['id'] ] = array();
		foreach ( $f['when']['rules'] ?? array() as $r ) {
			if ( isset( $by_id[ $r['field'] ] ) ) {
				$deps[ $f['id'] ][] = $r['field'];
			}
		}
	}
	$reaches = function ( $from, $to ) use ( $deps ) {
		$todo = array( $from );
		$seen = array();
		while ( $todo ) {
			$id = array_pop( $todo );
			if ( $id === $to ) {
				return true;
			}
			if ( isset( $seen[ $id ] ) ) {
				continue;
			}
			$seen[ $id ] = true;
			$todo        = array_merge( $todo, $deps[ $id ] );
		}
		return false;
	};
	$bad = array();
	foreach ( $deps as $id => $refs ) {
		foreach ( $refs as $ref ) {
			if ( $reaches( $ref, $id ) ) {
				$bad[ $id . '>' . $ref ] = true;
			}
		}
	}
	$memo  = array();
	$visit = function ( $id, $stack ) use ( &$visit, &$memo, $by_id, $values, $bad ) {
		if ( isset( $memo[ $id ] ) ) {
			return $memo[ $id ];
		}
		$f     = $by_id[ $id ];
		$rules = $f['when']['rules'] ?? array();
		$any   = 'any' === ( $f['when']['logic'] ?? 'all' );
		$res   = array();
		foreach ( $rules as $r ) {
			$ref = $r['field'];
			if ( isset( $bad[ $id . '>' . $ref ] ) || ! isset( $by_id[ $ref ] ) || isset( $stack[ $ref ] ) ) {
				continue;
			}
			$shown = $visit( $ref, $stack + array( $id => true ) );
			$v     = $shown ? ( $values[ sanitize_key( $by_id[ $ref ]['name'] ) ] ?? '' ) : '';
			$res[] = bst_form_rule_match( $r, $v );
		}
		$memo[ $id ] = ! $res || ( $any ? in_array( true, $res, true ) : ! in_array( false, $res, true ) );
		return $memo[ $id ];
	};
	$out = array();
	foreach ( array_keys( $by_id ) as $id ) {
		$out[ $id ] = $visit( $id, array() );
	}
	return $out;
}
