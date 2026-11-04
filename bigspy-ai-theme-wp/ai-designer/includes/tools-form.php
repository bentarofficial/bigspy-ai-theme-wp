<?php
/**
 * AI Designer: built-in Form fields and price rules (G-59).
 *
 * One tool, form_config: read the form of a page, check a proposed config
 * (dry run: errors, rules table, example quotes before/after, diff) and save it.
 * Save needs the check token of exactly that config, keeps a snapshot for task
 * undo, never changes the page status and asks for approval on a page that is
 * not a draft. Page tools cannot set fields or prices directly (see
 * bst_aid_form_page_attrs()), so every price change goes through the check.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Limits for one form. */
const BST_AID_FORM_MAX_FIELDS = 40;
const BST_AID_FORM_MAX_LINES  = 30;

add_filter(
	'bst_aid_tools',
	function ( $tools ) {
		$tools['form_config'] = array(
			'description' => 'Fields and price rules of a built-in Form block (provider "bigspy") on a page. Read get_knowledge(forms) first. mode "read": the forms on the page and the current config. mode "check": dry run of a full new config (fields + pricing): errors, a rules table, example quotes before/after for samples and the changes; nothing is saved; show the table and examples to the user. mode "save": the same config with the check token from that dry run, after the user agreed. Never invent prices: ask for missing prices and units, or use price_source "demo" only when the user wants sample prices.',
			'parameters'  => array(
				'type'       => 'object',
				'properties' => array(
					'mode'         => array( 'type' => 'string', 'enum' => array( 'read', 'check', 'save' ) ),
					'id'           => array( 'type' => 'integer', 'description' => 'Page id.' ),
					'form_key'     => array( 'type' => 'string', 'description' => 'formKey of the Form block; optional when the page has one form.' ),
					'fields'       => array( 'type' => 'array', 'maxItems' => BST_AID_FORM_MAX_FIELDS, 'items' => array( 'type' => 'object', 'description' => '{ id?, type, name, label, placeholder?, required?, width: half|full, options? (one choice per line), default?, when? }' ) ),
					'pricing'      => array( 'type' => 'object', 'description' => '{ enabled, currency, decimals, minTotal?, roundStep?, note?, lines: [ { id, label, kind, when?, … } ] }. Omit to keep the current price rules; { "enabled": false } turns them off.' ),
					'samples'      => array( 'type' => 'array', 'maxItems' => 5, 'items' => array( 'type' => 'object', 'properties' => array( 'label' => array( 'type' => 'string' ), 'values' => array( 'type' => 'object', 'description' => 'field name or id => value; checkbox choices joined with ", ".' ) ) ) ),
					'price_source' => array( 'type' => 'string', 'enum' => array( 'user', 'demo' ), 'description' => 'user: every price came from the user. demo: sample prices; labels and note are marked demo.' ),
					'check'        => array( 'type' => 'string', 'description' => 'Token from mode "check" for this exact config (save only).' ),
				),
				'required'   => array( 'mode', 'id' ),
			),
			'cap'         => 'edit_pages',
			'callback'    => 'bst_aid_tool_form_config',
		);
		return $tools;
	},
	20
);

/* ------------------------------------------------------------------ Cleaning */

/** Plain one-line text. */
function bst_aid_form_text( $v, $max = 200 ) {
	return is_scalar( $v ) ? mb_substr( sanitize_text_field( wp_strip_all_tags( (string) $v ) ), 0, $max ) : '';
}

/** Number or null. */
function bst_aid_form_num( $v ) {
	return is_numeric( $v ) ? $v + 0 : null;
}

/** Mail tag name the same way the editor makes it: a-z, 0-9, "-" and "_". */
function bst_aid_form_tag( $v ) {
	$v = strtolower( remove_accents( str_replace( array( 'Đ', 'đ' ), 'd', (string) $v ) ) );
	return trim( preg_replace( '/[^a-z0-9_-]+/', '-', $v ), '-' );
}

/** Map a field reference given as id or name to the field id. */
function bst_aid_form_ref( $ref, $fields ) {
	$ref = (string) $ref;
	foreach ( $fields as $f ) {
		if ( $f['id'] === sanitize_key( $ref ) ) {
			return $f['id'];
		}
	}
	foreach ( $fields as $f ) {
		if ( $f['name'] === bst_aid_form_tag( $ref ) ) {
			return $f['id'];
		}
	}
	return sanitize_key( $ref );
}

/** Clean a condition and map its field references. */
function bst_aid_form_when( $when, $fields ) {
	$when = bst_form_when_clean( $when );
	if ( $when ) {
		foreach ( $when['rules'] as $i => $r ) {
			$when['rules'][ $i ]['field'] = bst_aid_form_ref( $r['field'], $fields );
			$when['rules'][ $i ]['value'] = bst_aid_form_text( $r['value'] );
		}
	}
	return $when;
}

/**
 * Clean proposed fields. A field without id keeps the id of the current field
 * with the same name, so price rules and conditions keep pointing to it.
 */
function bst_aid_form_clean_fields( $raw, $old, &$errors ) {
	$old_ids = array();
	foreach ( $old as $f ) {
		$old_ids[ $f['name'] ] = $f['id'];
	}
	$out   = array();
	$names = array();
	foreach ( array_slice( array_values( (array) $raw ), 0, BST_AID_FORM_MAX_FIELDS ) as $i => $f ) {
		if ( ! is_array( $f ) ) {
			continue;
		}
		$label = bst_aid_form_text( $f['label'] ?? '' );
		$name  = bst_aid_form_tag( '' !== (string) ( $f['name'] ?? '' ) ? $f['name'] : $label );
		$type  = (string) ( $f['type'] ?? 'text' );
		$where = 'fields/' . $i . ( '' !== $name ? ' (' . $name . ')' : '' );
		if ( ! in_array( $type, bst_native_field_types(), true ) ) {
			$errors[] = "$where: unknown type \"$type\". Use " . implode( ', ', bst_native_field_types() ) . '.';
			continue;
		}
		if ( '' === $name ) {
			$errors[] = "$where: needs a name (mail tag) or a label.";
			continue;
		}
		if ( '_' === $name[0] || 0 === strpos( $name, 'price-' ) || 'all-fields' === $name ) {
			$errors[] = "$where: the name \"$name\" is reserved for mail tags.";
			continue;
		}
		if ( isset( $names[ $name ] ) ) {
			$errors[] = "$where: the name \"$name\" is used twice; mail tags must be unique.";
			continue;
		}
		$names[ $name ] = true;
		$options        = $f['options'] ?? '';
		$options        = is_array( $options ) ? implode( "\n", array_map( 'bst_aid_form_text', $options ) ) : sanitize_textarea_field( (string) $options );
		$id             = sanitize_key( (string) ( $f['id'] ?? '' ) );
		$row            = array(
			'id'       => '' !== $id ? $id : ( $old_ids[ $name ] ?? 'f_' . str_replace( '-', '_', $name ) ),
			'type'     => $type,
			'name'     => $name,
			'label'    => $label,
			'required' => ! empty( $f['required'] ),
			'width'    => 'half' === ( $f['width'] ?? '' ) ? 'half' : 'full',
			'lockName' => true, // The editor must not rename a tag the mail or the AI relies on.
		);
		foreach ( array( 'placeholder' => bst_aid_form_text( $f['placeholder'] ?? '' ), 'options' => $options, 'default' => sanitize_textarea_field( (string) ( is_scalar( $f['default'] ?? '' ) ? $f['default'] ?? '' : '' ) ) ) as $k => $v ) {
			if ( '' !== $v ) {
				$row[ $k ] = $v;
			}
		}
		if ( in_array( $type, array( 'select', 'radio', 'checkbox' ), true ) && ! bst_native_field_options( $row ) ) {
			$errors[] = "$where: a $type field needs options, one per line.";
		}
		if ( isset( $f['when'] ) ) {
			$row['when'] = $f['when']; // Mapped below, when every field is known.
		}
		$out[] = $row;
	}
	$out = bst_form_schema_normalize( $out );
	foreach ( $out as $i => $f ) {
		if ( isset( $f['when'] ) ) {
			$out[ $i ]['when'] = bst_aid_form_when( $f['when'], $out );
		}
	}
	foreach ( bst_form_schema_errors( $out ) as $e ) {
		$msg      = array( 'missing' => 'its condition uses a field that does not exist', 'self' => 'its condition uses the field itself', 'cycle' => 'its conditions form a loop' );
		$errors[] = 'field ' . $e['field'] . ': ' . $msg[ $e['code'] ] . ' (' . $e['ref'] . ').';
	}
	return $out;
}

/** Clean proposed price rules and check every line against the fields. */
function bst_aid_form_clean_pricing( $raw, $fields, $old, &$errors, &$warnings ) {
	$raw = is_array( $raw ) ? $raw : array();
	$by  = array();
	foreach ( $fields as $f ) {
		$by[ $f['id'] ] = $f;
	}
	$cur = strtoupper( substr( preg_replace( '/[^A-Za-z]/', '', (string) ( $raw['currency'] ?? ( $old['currency'] ?? 'VND' ) ) ), 0, 3 ) );
	$out = array(
		'enabled'  => array_key_exists( 'enabled', $raw ) ? ! empty( $raw['enabled'] ) : ! empty( $raw['lines'] ),
		'currency' => 3 === strlen( $cur ) ? $cur : 'VND',
		'decimals' => max( 0, min( 3, (int) ( $raw['decimals'] ?? ( 'VND' === $cur ? 0 : 2 ) ) ) ),
		'lines'    => array(),
	);
	foreach ( array( 'minTotal', 'roundStep' ) as $k ) {
		$n = bst_aid_form_num( $raw[ $k ] ?? null );
		if ( null !== $n && $n > 0 ) {
			$out[ $k ] = $n;
		}
	}
	$note = bst_aid_form_text( $raw['note'] ?? '', 300 );
	if ( '' !== $note ) {
		$out['note'] = $note;
	}
	$ids = array();
	foreach ( array_slice( array_values( (array) ( $raw['lines'] ?? array() ) ), 0, BST_AID_FORM_MAX_LINES ) as $i => $l ) {
		if ( ! is_array( $l ) ) {
			continue;
		}
		$id    = sanitize_key( (string) ( $l['id'] ?? '' ) );
		$id    = '' !== $id ? $id : 'l_' . ( $i + 1 );
		$kind  = (string) ( $l['kind'] ?? '' );
		$where = "line $id";
		if ( isset( $ids[ $id ] ) ) {
			$errors[] = "$where: the id is used twice.";
			continue;
		}
		if ( ! in_array( $kind, bst_pricing_kinds(), true ) ) {
			$errors[] = "$where: unknown kind \"$kind\". Use " . implode( ', ', bst_pricing_kinds() ) . '.';
			continue;
		}
		$row  = array( 'id' => $id, 'kind' => $kind, 'label' => bst_aid_form_text( $l['label'] ?? '' ) );
		$need = function ( $key, $types = null ) use ( $l, $fields, $by, $where, &$row, &$errors, &$warnings ) {
			$ref = bst_aid_form_ref( $l[ $key ] ?? '', $fields );
			if ( '' === $ref || ! isset( $by[ $ref ] ) ) {
				$errors[] = "$where: $key must be an existing field (got \"" . bst_aid_form_text( $l[ $key ] ?? '' ) . '").';
				return null;
			}
			if ( $types && ! in_array( $by[ $ref ]['type'], $types, true ) ) {
				if ( array( 'number' ) === $types ) {
					$warnings[] = "$where: $key \"{$by[ $ref ]['name']}\" is not a number field; visitors may type text and get no price.";
				} else {
					$errors[] = "$where: $key \"{$by[ $ref ]['name']}\" must be a " . implode( ', ', $types ) . ' field.';
					return null;
				}
			}
			$row[ $key ] = $ref;
			return $by[ $ref ];
		};
		$money = function ( $key, $required = true ) use ( $l, $where, &$row, &$errors ) {
			$n = bst_aid_form_num( $l[ $key ] ?? null );
			if ( null === $n ) {
				if ( $required ) {
					$errors[] = "$where: $key must be a number. Ask the user for it instead of guessing.";
				}
				return;
			}
			$row[ $key ] = $n;
		};
		switch ( $kind ) {
			case 'fixed':
				$money( 'amount' );
				break;
			case 'qty':
				$need( 'field', array( 'number' ) );
				$money( 'unit' );
				break;
			case 'area':
				$need( 'field', array( 'number' ) );
				$need( 'field2', array( 'number' ) );
				$money( 'unit' );
				$money( 'minQty', false );
				break;
			case 'option':
				$f      = $need( 'field', array( 'select', 'radio', 'checkbox' ) );
				$prices = array();
				foreach ( (array) ( $l['prices'] ?? array() ) as $choice => $price ) {
					$choice = bst_aid_form_text( $choice );
					if ( null === bst_aid_form_num( $price ) ) {
						$errors[] = "$where: the price of \"$choice\" must be a number.";
						continue;
					}
					$prices[ $choice ] = $price + 0;
				}
				if ( $f ) {
					$opts = bst_native_field_options( $f );
					foreach ( array_diff( array_keys( $prices ), $opts ) as $bad ) {
						$errors[] = "$where: \"$bad\" is not a choice of field {$f['name']}.";
					}
					foreach ( array_diff( $opts, array_keys( $prices ) ) as $miss ) {
						$warnings[] = "$where: no price for \"$miss\"; the total shows \"price to be confirmed\" when it is chosen.";
					}
				}
				$row['prices'] = (object) $prices;
				if ( ! empty( $l['qtyField'] ) ) {
					$need( 'qtyField', array( 'number' ) );
				}
				break;
			case 'tier':
				$need( 'field', array( 'number' ) );
				$tiers = array();
				foreach ( (array) ( $l['tiers'] ?? array() ) as $t ) {
					$from = bst_aid_form_num( $t['from'] ?? null );
					$unit = bst_aid_form_num( $t['unit'] ?? null );
					if ( null === $from || null === $unit ) {
						$errors[] = "$where: every tier needs a number from and unit.";
						continue;
					}
					$tiers[] = array( 'from' => $from, 'unit' => $unit );
				}
				if ( ! $tiers ) {
					$errors[] = "$where: a tier line needs at least one tier.";
				}
				usort( $tiers, function ( $x, $y ) { return $x['from'] <=> $y['from']; } );
				$row['tiers'] = $tiers;
				$row['mode']  = 'step' === ( $l['mode'] ?? '' ) ? 'step' : 'all';
				break;
			case 'percent':
				$money( 'pct' );
				$targets = array();
				foreach ( (array) ( $l['targets'] ?? array() ) as $t ) {
					$t = sanitize_key( (string) $t );
					if ( ! isset( $ids[ $t ] ) ) {
						$errors[] = "$where: target \"$t\" must be a line above this one.";
						continue;
					}
					$targets[] = $t;
				}
				$row['targets'] = $targets;
				break;
			case 'formula':
				$expr  = trim( preg_replace( '/\s+/', ' ', (string) ( $l['expr'] ?? '' ) ) );
				$known = array_merge( array_keys( $by ), array_keys( $ids ) );
				// Field names are accepted and stored as ids, like the editor does.
				$expr  = preg_replace_callback( '/\{([^}]*)\}/', function ( $m ) use ( $fields ) { return '{' . bst_aid_form_ref( $m[1], $fields ) . '}'; }, mb_substr( $expr, 0, 500 ) );
				$p     = bst_pricing_check_expr( $expr, $known );
				if ( 0 === strpos( $p, 'unknown:' ) ) {
					$errors[] = "$where: unknown ids " . substr( $p, 8 ) . ' (only fields and lines above this one can be used).';
				} elseif ( '' !== $p || '' === $expr ) {
					$errors[] = "$where: the formula is not valid. Use numbers, {fieldId}, {lineId}, + - * / ( ), comparisons, min max round ceil floor if.";
				}
				$row['expr'] = $expr;
				break;
		}
		if ( isset( $l['when'] ) ) {
			$w = bst_aid_form_when( $l['when'], $fields );
			foreach ( $w['rules'] ?? array() as $r ) {
				if ( ! isset( $by[ $r['field'] ] ) ) {
					$errors[] = "$where: its condition uses a field that does not exist ({$r['field']}).";
				}
			}
			if ( $w ) {
				$row['when'] = $w;
			}
		}
		$ids[ $id ]      = true;
		$out['lines'][] = $row;
	}
	if ( $out['enabled'] && ! $out['lines'] ) {
		$warnings[] = 'Pricing is on but has no lines, so no price is shown.';
	}
	return $out;
}

/** Mark sample prices as demo, so they are never taken for real prices. */
function bst_aid_form_mark_demo( $p ) {
	foreach ( $p['lines'] as $i => $l ) {
		if ( false === stripos( $l['label'], 'demo' ) ) {
			$p['lines'][ $i ]['label'] = trim( $l['label'] . ' (demo)' );
		}
	}
	if ( false === stripos( (string) ( $p['note'] ?? '' ), 'demo' ) ) {
		$p['note'] = trim( __( 'Demo prices: replace them before publishing.', 'bigspy-ai-theme-wp' ) . ' ' . ( $p['note'] ?? '' ) );
	}
	return $p;
}

/* ------------------------------------------------------------------ Dry run */

/** Field label for the rules table. */
function bst_aid_form_label( $id, $fields ) {
	foreach ( $fields as $f ) {
		if ( $f['id'] === $id ) {
			return '' !== $f['label'] ? $f['label'] : $f['name'];
		}
	}
	return $id;
}

/** Human-readable condition. */
function bst_aid_form_when_text( $when, $fields ) {
	if ( ! $when ) {
		return '';
	}
	$ops   = array( 'filled' => 'is filled', 'empty' => 'is empty', 'is' => 'is', 'not' => 'is not', 'gt' => '>', 'lt' => '<' );
	$parts = array();
	foreach ( $when['rules'] as $r ) {
		$parts[] = bst_aid_form_label( $r['field'], $fields ) . ' ' . $ops[ $r['op'] ] . ( in_array( $r['op'], array( 'filled', 'empty' ), true ) ? '' : ' "' . $r['value'] . '"' );
	}
	return implode( 'any' === $when['logic'] ? ' or ' : ' and ', $parts );
}

/** One row per price line: what it charges and when. */
function bst_aid_form_rules_table( $p, $fields ) {
	if ( empty( $p['enabled'] ) ) {
		return array();
	}
	$cur  = $p['currency'];
	$dec  = (int) $p['decimals'];
	$m    = function ( $v ) use ( $cur, $dec ) {
		return bst_pricing_format( bst_pricing_minor( (float) $v, $dec ), $cur, $dec );
	};
	$lab  = function ( $id ) use ( $fields ) {
		return bst_aid_form_label( $id, $fields );
	};
	$rows = array();
	foreach ( $p['lines'] as $l ) {
		switch ( $l['kind'] ) {
			case 'fixed':
				$rule = $m( $l['amount'] ?? 0 );
				break;
			case 'qty':
				$rule = $lab( $l['field'] ?? '' ) . ' × ' . $m( $l['unit'] ?? 0 );
				break;
			case 'area':
				$rule = $lab( $l['field'] ?? '' ) . ' × ' . $lab( $l['field2'] ?? '' ) . ' × ' . $m( $l['unit'] ?? 0 ) . ( ! empty( $l['minQty'] ) ? ' (min ' . $l['minQty'] . ')' : '' );
				break;
			case 'option':
				$list = array();
				foreach ( (array) $l['prices'] as $c => $v ) {
					$list[] = $c . ' = ' . $m( $v );
				}
				$rule = $lab( $l['field'] ?? '' ) . ': ' . implode( '; ', $list ) . ( ! empty( $l['qtyField'] ) ? ' × ' . $lab( $l['qtyField'] ) : '' );
				break;
			case 'tier':
				$list = array();
				foreach ( $l['tiers'] as $t ) {
					$list[] = 'from ' . $t['from'] . ': ' . $m( $t['unit'] );
				}
				$rule = $lab( $l['field'] ?? '' ) . ' × (' . implode( '; ', $list ) . ')' . ( 'step' === $l['mode'] ? ', each tier on its own part' : ', one rate for the whole quantity' );
				break;
			case 'percent':
				$rule = $l['pct'] . '% of ' . ( $l['targets'] ? implode( ' + ', $l['targets'] ) : 'all lines above' );
				break;
			default:
				$rule = (string) $l['expr'];
		}
		$rows[] = array( 'id' => $l['id'], 'label' => $l['label'], 'kind' => $l['kind'], 'rule' => $rule, 'when' => bst_aid_form_when_text( $l['when'] ?? null, $fields ) );
	}
	if ( ! empty( $p['minTotal'] ) ) {
		$rows[] = array( 'id' => 'minTotal', 'label' => 'Minimum total', 'kind' => 'setting', 'rule' => $m( $p['minTotal'] ), 'when' => '' );
	}
	if ( ! empty( $p['roundStep'] ) ) {
		$rows[] = array( 'id' => 'roundStep', 'label' => 'Round total to', 'kind' => 'setting', 'rule' => $m( $p['roundStep'] ), 'when' => '' );
	}
	return $rows;
}

/** Sample values keyed by mail tag; accepts field ids or names. */
function bst_aid_form_sample_values( $values, $fields ) {
	$out = array();
	foreach ( (array) $values as $k => $v ) {
		$id = bst_aid_form_ref( $k, $fields );
		foreach ( $fields as $f ) {
			if ( $f['id'] === $id ) {
				$out[ sanitize_key( $f['name'] ) ] = is_array( $v ) ? implode( ', ', array_map( 'strval', $v ) ) : (string) $v;
			}
		}
	}
	return $out;
}

/** A sample made from the fields when the AI sends none: defaults, first choices, quantity 1. */
function bst_aid_form_auto_sample( $fields ) {
	$v = array();
	foreach ( $fields as $f ) {
		$opts = bst_native_field_options( $f );
		if ( 'number' === $f['type'] ) {
			$v[ $f['name'] ] = is_numeric( $f['default'] ?? '' ) ? (string) $f['default'] : '1';
		} elseif ( in_array( $f['type'], array( 'select', 'radio', 'checkbox' ), true ) && $opts ) {
			$v[ $f['name'] ] = $opts[0];
		}
	}
	return array( 'label' => 'auto', 'values' => $v );
}

/** Quote text for one sample, or null when pricing is off. */
function bst_aid_form_quote( $pricing, $fields, $values ) {
	$p = bst_pricing_settings( $pricing );
	if ( ! $p ) {
		return null;
	}
	$q = bst_pricing_quote( $p, $fields, $values );
	$lines = array();
	foreach ( $q['lines'] as $l ) {
		$lines[] = ( '' !== $l['label'] ? $l['label'] : $l['id'] ) . ': ' . bst_pricing_format( $l['amount'], $q['currency'], $q['decimals'] );
	}
	return array( 'status' => $q['status'], 'lines' => $lines, 'total' => bst_pricing_total_text( $q ) );
}

/** Changes between the current and the proposed config. */
function bst_aid_form_diff( $old_f, $new_f, $old_p, $new_p, $attrs ) {
	$index = function ( $rows ) {
		$o = array();
		foreach ( $rows as $r ) {
			$o[ $r['id'] ] = $r;
		}
		return $o;
	};
	$diff = array();
	foreach ( array( 'fields' => array( $index( $old_f ), $index( $new_f ) ), 'lines' => array( $index( $old_p['lines'] ?? array() ), $index( $new_p['lines'] ?? array() ) ) ) as $what => $pair ) {
		list( $a, $b ) = $pair;
		$rows = array();
		foreach ( $b as $id => $r ) {
			if ( ! isset( $a[ $id ] ) ) {
				$rows[] = "added $id" . ( '' !== $r['label'] ? ' (' . $r['label'] . ')' : '' );
				continue;
			}
			$keys = array();
			foreach ( array_unique( array_merge( array_keys( $a[ $id ] ), array_keys( $r ) ) ) as $k ) {
				if ( ! in_array( $k, array( 'lockName', 'autoName' ), true ) && wp_json_encode( $a[ $id ][ $k ] ?? null ) !== wp_json_encode( $r[ $k ] ?? null ) ) {
					$keys[] = $k;
				}
			}
			if ( $keys ) {
				$rows[] = "changed $id: " . implode( ', ', $keys );
			}
		}
		foreach ( array_diff_key( $a, $b ) as $id => $r ) {
			$rows[] = "removed $id" . ( '' !== $r['label'] ? ' (' . $r['label'] . ')' : '' );
		}
		$diff[ $what ] = $rows;
	}
	$set = array();
	foreach ( array( 'enabled', 'currency', 'decimals', 'minTotal', 'roundStep', 'note' ) as $k ) {
		if ( wp_json_encode( $old_p[ $k ] ?? null ) !== wp_json_encode( $new_p[ $k ] ?? null ) ) {
			$set[] = $k;
		}
	}
	$diff['pricing_settings'] = $set;
	// Mail templates that still use the tag of a removed field.
	$names = wp_list_pluck( $new_f, 'name' );
	$mail  = '';
	foreach ( $attrs as $k => $v ) {
		if ( 0 === strpos( $k, 'mail' ) && is_string( $v ) ) {
			$mail .= $v;
		}
	}
	$mail .= implode( '', array_filter( bst_native_form_defaults(), 'is_string' ) );
	$gone  = array();
	foreach ( $old_f as $f ) {
		if ( ! in_array( $f['name'], $names, true ) && false !== strpos( $mail, '[' . $f['name'] . ']' ) ) {
			$gone[] = $f['name'];
		}
	}
	$diff['mail_tags_lost'] = $gone;
	return $diff;
}

/* ------------------------------------------------------------------ Locating the form */

/** Paths (index lists) of every bst/form block in a parsed tree. */
function bst_aid_form_paths( $blocks, $path = array() ) {
	$out = array();
	foreach ( $blocks as $i => $b ) {
		if ( 'bst/form' === ( $b['blockName'] ?? '' ) ) {
			$out[] = array_merge( $path, array( $i ) );
		}
		if ( ! empty( $b['innerBlocks'] ) ) {
			$out = array_merge( $out, bst_aid_form_paths( $b['innerBlocks'], array_merge( $path, array( $i ) ) ) );
		}
	}
	return $out;
}

/** Block at a path (by reference, so it can be changed in place). */
function &bst_aid_form_at( &$blocks, $path ) {
	$node = &$blocks[ array_shift( $path ) ];
	foreach ( $path as $i ) {
		$node = &$node['innerBlocks'][ $i ];
	}
	return $node;
}

/** Current config of a form block. */
function bst_aid_form_current( $attrs ) {
	$s = bst_native_form_settings( $attrs );
	$p = is_array( $attrs['pricing'] ?? null ) ? $attrs['pricing'] : array();
	return array( 'fields' => $s['fields'], 'pricing' => $p ? $p : array( 'enabled' => false ) );
}

/** Check token: changes when the page, the form or the proposed config changes. */
function bst_aid_form_token( $page, $key, $fields, $pricing ) {
	return substr( hash( 'sha256', wp_json_encode( array( (int) $page, (string) $key, $fields, $pricing ) ) ), 0, 16 );
}

/* ------------------------------------------------------------------ Tool */

function bst_aid_tool_form_config( $a ) {
	$post = get_post( (int) ( $a['id'] ?? 0 ) );
	if ( ! $post || ! in_array( $post->post_type, array( 'page', 'post', 'bst_block' ), true ) || ! current_user_can( 'edit_post', $post->ID ) ) {
		return array( 'error' => 'Page not found or not editable.' );
	}
	$blocks = parse_blocks( $post->post_content );
	$paths  = bst_aid_form_paths( $blocks );
	$forms  = array();
	foreach ( $paths as $path ) {
		$b       = bst_aid_form_at( $blocks, $path );
		$forms[] = array( 'form_key' => (string) ( $b['attrs']['formKey'] ?? '' ), 'provider' => (string) ( $b['attrs']['provider'] ?? 'cf7' ), 'title' => (string) ( $b['attrs']['title'] ?? '' ), 'fields' => count( (array) ( $b['attrs']['fields'] ?? array() ) ), 'price_lines' => count( (array) ( $b['attrs']['pricing']['lines'] ?? array() ) ) );
	}
	$mode = (string) ( $a['mode'] ?? 'read' );
	$key  = sanitize_key( (string) ( $a['form_key'] ?? '' ) );
	$pick = null;
	foreach ( $paths as $i => $path ) {
		if ( ( $key && $forms[ $i ]['form_key'] === $key ) || ( ! $key && 1 === count( $paths ) ) ) {
			$pick = $i;
		}
	}
	if ( null === $pick ) {
		if ( 'read' === $mode && ! $key ) {
			return array( 'id' => $post->ID, 'forms' => $forms, 'note' => $forms ? 'Several forms: call again with form_key.' : 'No Form block on this page. Add a bst/form block with provider "bigspy" using page_sections, then use form_config.' );
		}
		return array( 'error' => $forms ? 'Pass the form_key of one of these forms.' : 'No Form block on this page. Add a bst/form block with provider "bigspy" using page_sections first.', 'forms' => $forms );
	}
	$block = bst_aid_form_at( $blocks, $paths[ $pick ] );
	$attrs = (array) $block['attrs'];
	if ( in_array( $attrs['provider'] ?? 'cf7', array( 'cf7', 'wpforms' ), true ) && ! empty( $attrs['formId'] ) ) {
		return array( 'error' => 'This Form block shows a ' . strtoupper( (string) ( $attrs['provider'] ?? 'cf7' ) ) . ' form; its fields are edited in that plugin. Prices work only with the built-in form (provider "bigspy").', 'forms' => $forms );
	}
	$cur = bst_aid_form_current( $attrs );
	if ( 'read' === $mode ) {
		return array(
			'id'       => $post->ID,
			'status'   => $post->post_status,
			'forms'    => $forms,
			'form_key' => (string) ( $attrs['formKey'] ?? '' ),
			'provider' => (string) ( $attrs['provider'] ?? 'cf7' ),
			'fields'   => $cur['fields'],
			'pricing'  => $cur['pricing'],
			'rules'    => bst_aid_form_rules_table( (array) bst_pricing_settings( $cur['pricing'] ) + array( 'enabled' => (bool) bst_pricing_settings( $cur['pricing'] ) ), $cur['fields'] ),
		);
	}

	// check and save: clean, validate, dry run.
	$errors   = array();
	$warnings = array();
	$fields   = isset( $a['fields'] ) ? bst_aid_form_clean_fields( $a['fields'], $cur['fields'], $errors ) : $cur['fields'];
	if ( ! $fields ) {
		$errors[] = 'The form needs at least one field.';
	}
	$pricing = bst_aid_form_clean_pricing( $a['pricing'] ?? $cur['pricing'], $fields, $cur['pricing'], $errors, $warnings );
	$source  = (string) ( $a['price_source'] ?? '' );
	$e2      = array();
	$w2      = array();
	$changed = isset( $a['pricing'] ) && wp_json_encode( $pricing ) !== wp_json_encode( bst_aid_form_clean_pricing( $cur['pricing'], $fields, $cur['pricing'], $e2, $w2 ) );
	if ( $pricing['lines'] && $changed && ! in_array( $source, array( 'user', 'demo' ), true ) ) {
		$errors[] = 'Set price_source: "user" when every price came from the user, or "demo" for sample prices. Ask the user for missing prices; never invent them.';
	}
	if ( 'demo' === $source && $pricing['lines'] ) {
		$pricing = bst_aid_form_mark_demo( $pricing );
	}
	$token = bst_aid_form_token( $post->ID, $attrs['formKey'] ?? '', $fields, $pricing );
	if ( 'save' !== $mode || $errors ) {
		$samples = array_slice( (array) ( $a['samples'] ?? array() ), 0, 5 );
		if ( ! $samples ) {
			$samples = array( bst_aid_form_auto_sample( $fields ) );
		}
		$examples = array();
		if ( ! $errors ) {
			foreach ( $samples as $s ) {
				$values     = bst_aid_form_sample_values( $s['values'] ?? array(), $fields );
				$examples[] = array(
					'label'  => bst_aid_form_text( $s['label'] ?? '' ),
					'values' => $values,
					'before' => bst_aid_form_quote( $cur['pricing'], $cur['fields'], $values ),
					'after'  => bst_aid_form_quote( $pricing, $fields, $values ),
				);
			}
		}
		$out = array(
			'ok'       => ! $errors,
			'errors'   => $errors,
			'warnings' => $warnings,
			'rules'    => bst_aid_form_rules_table( $pricing, $fields ),
			'examples' => $examples,
			'changes'  => bst_aid_form_diff( $cur['fields'], $fields, $cur['pricing'], $pricing, $attrs ),
			'fields'   => $fields,
			'pricing'  => $pricing,
		);
		if ( $errors ) {
			$out['note'] = 'Nothing was saved. Fix the errors, or ask the user for the missing facts, and check again.';
		} else {
			$out['check'] = $token;
			$out['note']  = 'Nothing was saved. Show the user the rules and examples (and changes). After they agree, call mode "save" with the same fields, pricing and price_source and this check token.' . ( 'publish' === $post->post_status ? ' The page is published, so saving needs the approval card.' : '' );
		}
		return 'save' === $mode ? array( 'error' => 'Not saved: fix these errors and check again.' ) + $out : $out;
	}
	if ( ! hash_equals( $token, (string) ( $a['check'] ?? '' ) ) ) {
		return array( 'error' => 'The check token does not match this config. Call mode "check" with exactly this config, show the result to the user, then save with the new token.' );
	}
	if ( ! function_exists( 'wp_check_post_lock' ) ) {
		require_once ABSPATH . 'wp-admin/includes/post.php';
	}
	if ( wp_check_post_lock( $post->ID ) ) {
		return array( 'error' => 'Someone is editing this page right now. Try again when they are done.' );
	}
	$form_key                       = ! empty( $attrs['formKey'] ) ? (string) $attrs['formKey'] : 'f' . strtolower( wp_generate_password( 8, false ) );
	$node                           = &bst_aid_form_at( $blocks, $paths[ $pick ] );
	$node['attrs']['provider']      = 'bigspy';
	$node['attrs']['formKey']       = $form_key;
	$node['attrs']['fields']        = $fields;
	$node['attrs']['pricing']       = $pricing;
	$node['attrs']['schemaVersion'] = BST_FORM_SCHEMA_VERSION;
	unset( $node );
	$content = serialize_blocks( $blocks );
	$error   = bst_aid_block_rule_error( $content, $post->post_content );
	if ( $error ) {
		return array( 'error' => $error );
	}
	bst_aid_snapshot_post( $post->ID );
	$r = wp_update_post( array( 'ID' => $post->ID, 'post_content' => wp_slash( $content ) ), true ); // Status is never changed here.
	if ( is_wp_error( $r ) ) {
		return array( 'error' => $r->get_error_message() );
	}
	return array(
		'saved'    => true,
		'id'       => $post->ID,
		'status'   => get_post_status( $post->ID ),
		'form_key' => $form_key,
		'fields'   => count( $fields ),
		'lines'    => count( $pricing['lines'] ),
		'note'     => 'Saved. The user can undo it with the task. The page status did not change.',
		'links'    => function_exists( 'bst_aid_links' ) ? bst_aid_links( $post->ID, get_the_title( $post->ID ) ) : array(),
		'preview'  => get_preview_post_link( $post->ID ),
	);
}

/* ------------------------------------------------------------------ Page tools */

/**
 * Form attributes sent through page tools (create_page, update_page,
 * page_sections, build_sections). Fields and prices are kept only as they are
 * on the source page for the same formKey; anything new goes through
 * form_config, so its check and approval cannot be skipped. A built-in form
 * without a key gets one, and a key copied twice in one write gets a new one.
 *
 * @param array $attrs  Attributes after coercion.
 * @param array $raw    Attributes as the model sent them.
 * @param array $issues Issues (source post id, warnings, form keys seen).
 */
function bst_aid_form_page_attrs( $attrs, $raw, &$issues, $where ) {
	// Read once per write (not per request: the page may change between tool calls).
	if ( ! isset( $issues['form_source'] ) ) {
		$issues['form_source'] = array();
		$p                     = ! empty( $issues['source'] ) ? get_post( (int) $issues['source'] ) : null;
		if ( $p ) {
			$tree = parse_blocks( $p->post_content );
			foreach ( bst_aid_form_paths( $tree ) as $path ) {
				$b = bst_aid_form_at( $tree, $path );
				if ( ! empty( $b['attrs']['formKey'] ) ) {
					$issues['form_source'][ (string) $b['attrs']['formKey'] ] = $b['attrs'];
				}
			}
		}
	}
	$key = sanitize_key( (string) ( $attrs['formKey'] ?? '' ) );
	if ( $key && isset( $issues['form_keys'][ $key ] ) ) {
		$key = ''; // A copy of a form: it gets its own key below.
	}
	$old = $key ? ( $issues['form_source'][ $key ] ?? null ) : null;
	foreach ( array( 'fields', 'pricing', 'schemaVersion' ) as $k ) {
		$sent = $raw[ $k ] ?? null;
		unset( $attrs[ $k ] );
		if ( $old && isset( $old[ $k ] ) ) {
			$attrs[ $k ] = $old[ $k ]; // Unchanged, or restored when the model left it out.
			if ( null !== $sent && wp_json_encode( $sent ) !== wp_json_encode( $old[ $k ] ) ) {
				$issues['warnings'][] = "$where (bst/form): $k kept as saved. Change fields and prices with form_config (check, then save).";
			}
		} elseif ( null !== $sent && 'schemaVersion' !== $k ) {
			$issues['warnings'][] = "$where (bst/form): $k dropped. Set fields and prices with form_config (check, then save).";
		}
	}
	if ( 'bigspy' === ( $attrs['provider'] ?? '' ) && ! $key ) {
		$key = 'f' . strtolower( wp_generate_password( 8, false ) );
	}
	if ( $key ) {
		$attrs['formKey']             = $key;
		$issues['form_keys'][ $key ] = true;
	}
	return $attrs;
}
