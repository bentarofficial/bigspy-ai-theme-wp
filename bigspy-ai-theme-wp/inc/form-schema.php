<?php
/**
 * Form schema (G-57): stable field IDs, schema version, show / required conditions
 * and dependency validation for the built-in Form block.
 *
 * - Every field gets an `id` that never changes once saved. Old fields without one
 *   get `f_<name>`, so reading the same old form twice gives the same IDs and the
 *   saved block, field names and mail tags stay untouched.
 * - Conditions point at IDs, so renaming a label/name or reordering keeps them right.
 * - A hidden field sends nothing and is never required; a field shown by a rule that
 *   reads a hidden field sees an empty value.
 * - Broken rules (missing field, itself, a loop) are reported to the editor and
 *   ignored on the site: the field stays visible so no data is silently dropped.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

const BST_FORM_SCHEMA_VERSION = 1;

/** Condition operators. */
function bst_form_condition_ops() {
	return array( 'is', 'is_not', 'empty', 'not_empty', 'contains', 'gt', 'lt' );
}

/** A valid field ID, or ''. */
function bst_form_clean_id( $id ) {
	$id = strtolower( (string) $id );
	return preg_match( '/^f_[a-z0-9_-]{1,60}$/', $id ) ? $id : '';
}

/** Normalize one condition group to { logic, rules[] }; '' when it has no rule. */
function bst_form_condition_normalize( $c ) {
	if ( ! is_array( $c ) || empty( $c['rules'] ) || ! is_array( $c['rules'] ) ) {
		return '';
	}
	$rules = array();
	foreach ( $c['rules'] as $r ) {
		if ( ! is_array( $r ) ) {
			continue;
		}
		$rules[] = array(
			'field' => strtolower( (string) ( $r['field'] ?? '' ) ),
			'op'    => (string) ( $r['op'] ?? 'is' ),
			'value' => is_scalar( $r['value'] ?? '' ) ? (string) ( $r['value'] ?? '' ) : '',
		);
	}
	if ( ! $rules ) {
		return '';
	}
	return array(
		'logic' => 'any' === ( $c['logic'] ?? 'all' ) ? 'any' : 'all',
		'rules' => $rules,
	);
}

/**
 * Give every field a stable unique ID and normalized conditions.
 * Idempotent: normalizing the result again changes nothing.
 */
function bst_form_schema_normalize( $fields ) {
	$out  = array();
	$seen = array();
	foreach ( (array) $fields as $f ) {
		if ( ! is_array( $f ) || empty( $f['name'] ) ) {
			continue;
		}
		$id = bst_form_clean_id( $f['id'] ?? '' );
		if ( ! $id ) {
			$id = 'f_' . substr( preg_replace( '/[^a-z0-9_-]/', '', sanitize_key( $f['name'] ) ), 0, 58 );
		}
		$base = $id;
		$n    = 2;
		while ( isset( $seen[ $id ] ) ) {
			$id = $base . '_' . $n++;
		}
		$seen[ $id ] = true;
		$f['id']     = $id;
		foreach ( array( 'show', 'requiredWhen' ) as $k ) {
			$c = bst_form_condition_normalize( $f[ $k ] ?? '' );
			if ( $c ) {
				$f[ $k ] = $c;
			} else {
				unset( $f[ $k ] );
			}
		}
		$out[] = $f;
	}
	return $out;
}

/** IDs a field's conditions read. */
function bst_form_field_refs( $f ) {
	$refs = array();
	foreach ( array( 'show', 'requiredWhen' ) as $k ) {
		foreach ( (array) ( $f[ $k ]['rules'] ?? array() ) as $r ) {
			$refs[] = $r['field'];
		}
	}
	return array_values( array_unique( $refs ) );
}

/** Fields (IDs) whose conditions use this field: block deleting it until they are fixed. */
function bst_form_field_usages( $fields, $id ) {
	$used = array();
	foreach ( bst_form_schema_normalize( $fields ) as $f ) {
		if ( $f['id'] !== $id && in_array( $id, bst_form_field_refs( $f ), true ) ) {
			$used[] = $f['id'];
		}
	}
	return $used;
}

/**
 * Check the schema. Returns a list of problems:
 * [ 'field' => ID with the problem, 'code' => duplicate_name|missing_ref|self_ref|bad_op|cycle, 'ref' => ... ].
 */
function bst_form_schema_validate( $fields ) {
	$fields = bst_form_schema_normalize( $fields );
	$ids    = array_column( $fields, null, 'id' );
	$errors = array();
	$names  = array();
	foreach ( $fields as $f ) {
		$name = sanitize_key( $f['name'] );
		if ( isset( $names[ $name ] ) ) {
			$errors[] = array( 'field' => $f['id'], 'code' => 'duplicate_name', 'ref' => $name );
		}
		$names[ $name ] = true;
		foreach ( array( 'show', 'requiredWhen' ) as $k ) {
			foreach ( (array) ( $f[ $k ]['rules'] ?? array() ) as $r ) {
				if ( ! in_array( $r['op'], bst_form_condition_ops(), true ) ) {
					$errors[] = array( 'field' => $f['id'], 'code' => 'bad_op', 'ref' => $r['op'] );
				}
				if ( $r['field'] === $f['id'] ) {
					$errors[] = array( 'field' => $f['id'], 'code' => 'self_ref', 'ref' => $r['field'] );
				} elseif ( ! isset( $ids[ $r['field'] ] ) ) {
					$errors[] = array( 'field' => $f['id'], 'code' => 'missing_ref', 'ref' => $r['field'] );
				}
			}
		}
	}
	foreach ( bst_form_schema_cycles( $fields ) as $id ) {
		$errors[] = array( 'field' => $id, 'code' => 'cycle', 'ref' => $id );
	}
	return $errors;
}

/** IDs of fields that sit on a dependency loop (self references are reported apart). */
function bst_form_schema_cycles( $fields ) {
	$graph = array();
	foreach ( $fields as $f ) {
		$graph[ $f['id'] ] = array_values( array_diff( bst_form_field_refs( $f ), array( $f['id'] ) ) );
	}
	$state = array(); // 1 = visiting, 2 = done.
	$stack = array();
	$bad   = array();
	$visit = function ( $id ) use ( &$visit, &$graph, &$state, &$stack, &$bad ) {
		$state[ $id ] = 1;
		$stack[]      = $id;
		foreach ( $graph[ $id ] as $to ) {
			if ( ! isset( $graph[ $to ] ) ) {
				continue;
			}
			if ( 1 === ( $state[ $to ] ?? 0 ) ) {
				foreach ( array_slice( $stack, array_search( $to, $stack, true ) ) as $c ) {
					$bad[ $c ] = true;
				}
			} elseif ( ! isset( $state[ $to ] ) ) {
				$visit( $to );
			}
		}
		array_pop( $stack );
		$state[ $id ] = 2;
	};
	foreach ( array_keys( $graph ) as $id ) {
		if ( ! isset( $state[ $id ] ) ) {
			$visit( $id );
		}
	}
	return array_keys( $bad );
}

/** Does one rule hold for this value? Multi-choice values are "a, b, c". */
function bst_form_rule_met( $r, $v ) {
	$v    = (string) $v;
	$want = (string) $r['value'];
	$list = '' === $v ? array() : array_map( 'trim', explode( ', ', $v ) );
	switch ( $r['op'] ) {
		case 'empty':
			return '' === $v;
		case 'not_empty':
			return '' !== $v;
		case 'is':
			return $v === $want || in_array( $want, $list, true );
		case 'is_not':
			return $v !== $want && ! in_array( $want, $list, true );
		case 'contains':
			return '' !== $want && false !== mb_stripos( $v, $want );
		case 'gt':
			return is_numeric( $v ) && is_numeric( $want ) && (float) $v > (float) $want;
		case 'lt':
			return is_numeric( $v ) && is_numeric( $want ) && (float) $v < (float) $want;
	}
	return false;
}

/**
 * Which fields are shown and which are required for these values (keyed by field name).
 * Returns [ id => [ 'visible' => bool, 'required' => bool ] ]. Invalid rules are ignored.
 */
function bst_form_schema_state( $fields, $values ) {
	$fields = bst_form_schema_normalize( $fields );
	$bad    = array_flip( array_column( bst_form_schema_validate( $fields ), 'field' ) );
	$by_id  = array_column( $fields, null, 'id' );
	$state  = array();
	$met    = function ( $c, $self ) use ( &$resolve, $by_id, $values ) {
		$hits = array();
		foreach ( $c['rules'] as $r ) {
			$src    = $by_id[ $r['field'] ];
			$v      = $resolve( $r['field'] ) ? (string) ( $values[ sanitize_key( $src['name'] ) ] ?? '' ) : '';
			$hits[] = bst_form_rule_met( $r, $v );
		}
		return 'any' === $c['logic'] ? in_array( true, $hits, true ) : ! in_array( false, $hits, true );
	};
	$resolve = function ( $id ) use ( &$resolve, &$state, &$met, $by_id, $bad ) {
		if ( ! isset( $state[ $id ] ) ) {
			$f            = $by_id[ $id ];
			$ok           = ! isset( $bad[ $id ] );
			$visible      = ! ( $ok && ! empty( $f['show'] ) ) || $met( $f['show'], $id );
			$required     = $visible && ( ! empty( $f['required'] ) || ( $ok && ! empty( $f['requiredWhen'] ) && $met( $f['requiredWhen'], $id ) ) );
			$state[ $id ] = array( 'visible' => $visible, 'required' => $required );
		}
		return $state[ $id ]['visible'];
	};
	foreach ( array_keys( $by_id ) as $id ) {
		$resolve( $id );
	}
	return $state;
}
