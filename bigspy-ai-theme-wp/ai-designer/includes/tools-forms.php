<?php
/**
 * AI Designer: read and configure built-in forms (G-59).
 *
 * - get_form reads the forms of a page in the G-57/G-58 schema: stable field
 *   IDs, conditions, option prices, formulas, problems.
 * - form_config changes one form. dry_run (the default) only validates and
 *   returns the diff and a worked example; nothing is saved. A real write
 *   keeps a snapshot so the task can be undone, refuses an invalid schema,
 *   and needs approval when prices or formulas change or the page is live.
 * - Forms stay blocks the user edits visually: no Code block, no script.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Field keys the AI may set. Anything else is dropped. */
function bst_aid_form_field_keys() {
	return array( 'id', 'type', 'name', 'label', 'placeholder', 'required', 'width', 'options', 'default', 'optionPrices', 'formula', 'isTotal', 'min', 'max', 'conditions' );
}

/** Form-level settings the AI may set. Mail recipients are left to the user. */
function bst_aid_form_setting_keys() {
	return array( 'formName', 'submitText', 'priceCurrency', 'priceDecimals', 'priceNote', 'msgSuccess' );
}

/** All built-in forms in a block list, with their path. */
function bst_aid_form_walk( $blocks, $path = array() ) {
	$out = array();
	foreach ( $blocks as $i => $b ) {
		$here = array_merge( $path, array( $i ) );
		if ( 'bst/form' === ( $b['blockName'] ?? '' ) ) {
			$out[] = array( 'path' => $here, 'attrs' => (array) $b['attrs'] );
		}
		if ( ! empty( $b['innerBlocks'] ) ) {
			$out = array_merge( $out, bst_aid_form_walk( $b['innerBlocks'], $here ) );
		}
	}
	return $out;
}

/** Set the attributes of the block at $path. */
function bst_aid_form_set_attrs( &$blocks, $path, $attrs ) {
	$i = array_shift( $path );
	if ( $path ) {
		bst_aid_form_set_attrs( $blocks[ $i ]['innerBlocks'], $path, $attrs );
	} else {
		$blocks[ $i ]['attrs'] = $attrs;
	}
}

/** A form as the AI sees it. */
function bst_aid_form_view( $attrs ) {
	$s      = bst_native_form_settings( $attrs );
	$fields = array();
	foreach ( $s['fields'] as $f ) {
		$fields[] = array_intersect_key( $f, array_flip( bst_aid_form_field_keys() ) );
	}
	$view = array(
		'key'      => (string) ( $attrs['formKey'] ?? '' ),
		'provider' => (string) ( $attrs['provider'] ?? 'cf7' ),
		'fields'   => $fields,
		'settings' => array_intersect_key( $s, array_flip( bst_aid_form_setting_keys() ) ),
		'problems' => bst_aid_form_problems( $s['fields'] ),
	);
	if ( 'bigspy' !== $view['provider'] ) {
		$view['note'] = 'This form comes from a plugin (Contact Form 7 / WPForms). Its fields are edited in that plugin, not here.';
	}
	return $view;
}

/** Schema and formula problems as short readable lines. */
function bst_aid_form_problems( $fields ) {
	$labels = array();
	foreach ( $fields as $f ) {
		$labels[ $f['id'] ] = ( $f['label'] ?? '' ) ? $f['label'] . ' (' . $f['id'] . ')' : $f['id'];
	}
	$out = array();
	foreach ( bst_form_schema_check( $fields ) as $e ) {
		$out[] = ( $labels[ $e['field'] ] ?? $e['field'] ) . ': ' . $e['code'] . ( $e['ref'] !== $e['field'] ? ' ' . $e['ref'] : '' );
	}
	foreach ( bst_calc_check( $fields ) as $id => $code ) {
		$out[] = ( $labels[ $id ] ?? $id ) . ': formula ' . $code;
	}
	return array_values( array_unique( $out ) );
}

function bst_aid_tool_get_form( $a ) {
	$p = get_post( (int) ( $a['id'] ?? 0 ) );
	if ( ! $p || ! current_user_can( 'edit_post', $p->ID ) ) {
		return array( 'error' => 'Post not found or not editable.' );
	}
	$forms = array();
	foreach ( bst_aid_form_walk( parse_blocks( $p->post_content ) ) as $hit ) {
		if ( empty( $a['key'] ) || ( $hit['attrs']['formKey'] ?? '' ) === $a['key'] ) {
			$forms[] = bst_aid_form_view( $hit['attrs'] );
		}
	}
	return array( 'id' => $p->ID, 'status' => $p->post_status, 'forms' => $forms, 'note' => $forms ? 'Edit with form_config (dry_run first).' : 'No Form block on this page. Add a bst/form block with provider "bigspy" through update_page or page_sections, then configure it here.' );
}

/**
 * Build the new field list from the AI's fields, keeping IDs stable:
 * a field without an id reuses the id of the old field with the same name.
 */
function bst_aid_form_merge_fields( $old, $incoming ) {
	$by_name = array();
	$ids     = array();
	foreach ( $old as $f ) {
		$by_name[ sanitize_key( $f['name'] ) ] = $f['id'];
		$ids[]                                 = $f['id'];
	}
	$out  = array();
	$used = array();
	foreach ( array_slice( (array) $incoming, 0, 40 ) as $f ) {
		if ( ! is_array( $f ) ) {
			continue;
		}
		$f = array_intersect_key( $f, array_flip( bst_aid_form_field_keys() ) );
		if ( empty( $f['label'] ) && empty( $f['name'] ) ) {
			continue;
		}
		$f['type'] = in_array( $f['type'] ?? 'text', bst_native_field_types(), true ) ? $f['type'] : 'text';
		$f['name'] = sanitize_key( remove_accents( (string) ( $f['name'] ?? '' ) ) );
		if ( '' === $f['name'] ) {
			$f['name'] = sanitize_title( remove_accents( (string) $f['label'] ) );
		}
		foreach ( array( 'label', 'placeholder', 'options', 'default', 'formula' ) as $k ) {
			if ( isset( $f[ $k ] ) ) {
				$f[ $k ] = 'options' === $k || 'default' === $k ? sanitize_textarea_field( (string) $f[ $k ] ) : sanitize_text_field( (string) $f[ $k ] );
			}
		}
		if ( isset( $f['required'] ) ) {
			$f['required'] = (bool) $f['required'];
		}
		if ( empty( $f['id'] ) || ! bst_form_valid_id( $f['id'] ) ) {
			$f['id'] = $by_name[ $f['name'] ] ?? '';
		}
		if ( '' === $f['id'] || isset( $used[ $f['id'] ] ) ) {
			do {
				$f['id'] = 'f_' . strtolower( wp_generate_password( 6, false, false ) );
			} while ( in_array( $f['id'], $ids, true ) || isset( $used[ $f['id'] ] ) );
		}
		$used[ $f['id'] ] = true;
		$out[]            = $f;
	}
	return bst_form_schema_normalize( $out );
}

/** What changed between two field lists (by ID) and settings. */
function bst_aid_form_diff( $old, $new, $old_set, $new_set ) {
	$o = array();
	$n = array();
	foreach ( $old as $f ) {
		$o[ $f['id'] ] = $f;
	}
	foreach ( $new as $f ) {
		$n[ $f['id'] ] = $f;
	}
	$diff = array( 'added' => array(), 'removed' => array(), 'changed' => array(), 'settings' => array(), 'order_changed' => array_values( array_intersect( array_keys( $o ), array_keys( $n ) ) ) !== array_values( array_intersect( array_keys( $n ), array_keys( $o ) ) ), 'pricing_changed' => false );
	$price_keys = array( 'formula', 'optionPrices', 'isTotal', 'type' );
	foreach ( $n as $id => $f ) {
		if ( ! isset( $o[ $id ] ) ) {
			$diff['added'][] = $id . ' ' . ( $f['label'] ?? $f['name'] );
			if ( 'calc' === $f['type'] || ! empty( $f['optionPrices'] ) ) {
				$diff['pricing_changed'] = true;
			}
			continue;
		}
		$keys = array();
		foreach ( array_unique( array_merge( array_keys( $o[ $id ] ), array_keys( $f ) ) ) as $k ) {
			if ( wp_json_encode( $o[ $id ][ $k ] ?? null ) !== wp_json_encode( $f[ $k ] ?? null ) ) {
				$keys[] = $k;
				if ( in_array( $k, $price_keys, true ) && ( 'type' !== $k || 'calc' === $f['type'] || 'calc' === $o[ $id ]['type'] ) ) {
					$diff['pricing_changed'] = true;
				}
			}
		}
		if ( $keys ) {
			$diff['changed'][ $id ] = $keys;
		}
	}
	foreach ( $o as $id => $f ) {
		if ( ! isset( $n[ $id ] ) ) {
			$diff['removed'][] = $id . ' ' . ( $f['label'] ?? $f['name'] );
			if ( 'calc' === $f['type'] || ! empty( $f['optionPrices'] ) ) {
				$diff['pricing_changed'] = true;
			}
		}
	}
	foreach ( $new_set as $k => $v ) {
		if ( (string) ( $old_set[ $k ] ?? '' ) !== (string) $v ) {
			$diff['settings'][] = $k;
			if ( in_array( $k, array( 'priceCurrency', 'priceDecimals' ), true ) ) {
				$diff['pricing_changed'] = true;
			}
		}
	}
	return $diff;
}

/** Worked example: the price lines for sample values (by field name). */
function bst_aid_form_example( $fields, $settings, $sample ) {
	if ( ! array_filter( $fields, function ( $f ) { return 'calc' === $f['type']; } ) ) {
		return null;
	}
	$values = array();
	foreach ( (array) $sample as $k => $v ) {
		$values[ sanitize_key( $k ) ] = is_array( $v ) ? implode( ', ', array_map( 'strval', $v ) ) : (string) $v;
	}
	$seen  = bst_form_schema_visibility( $fields, $values );
	$cur   = bst_calc_currency( $settings );
	$q     = bst_calc_quote( $fields, $values, $seen, $cur );
	$lines = array();
	foreach ( $fields as $f ) {
		if ( 'calc' !== $f['type'] ) {
			continue;
		}
		$l       = $q['lines'][ $f['id'] ] ?? array( 'error', 0 );
		$lines[] = ( $f['label'] ?? $f['name'] ) . ': ' . ( empty( $seen[ $f['id'] ] ) ? 'hidden' : ( 'ok' === $l[0] ? bst_calc_money( $l[1], $cur ) : ( 'incomplete' === $l[0] ? 'waiting for input' : 'needs confirmation' ) ) );
	}
	return array( 'input' => $values, 'lines' => $lines );
}

function bst_aid_tool_form_config( $a ) {
	$p = get_post( (int) ( $a['id'] ?? 0 ) );
	if ( ! $p || ! current_user_can( 'edit_post', $p->ID ) ) {
		return array( 'error' => 'Post not found or not editable.' );
	}
	$blocks = parse_blocks( $p->post_content );
	$hit    = null;
	foreach ( bst_aid_form_walk( $blocks ) as $h ) {
		if ( ( $h['attrs']['formKey'] ?? '' ) === (string) ( $a['key'] ?? '' ) ) {
			$hit = $h;
			break;
		}
	}
	if ( ! $hit ) {
		return array( 'error' => 'Form not found. Call get_form for the keys.' );
	}
	if ( 'bigspy' !== ( $hit['attrs']['provider'] ?? '' ) ) {
		return array( 'error' => 'Only built-in forms (provider "bigspy") can be configured here.' );
	}
	$old_s   = bst_native_form_settings( $hit['attrs'] );
	$old     = $old_s['fields'];
	$fields  = isset( $a['fields'] ) ? bst_aid_form_merge_fields( $old, $a['fields'] ) : $old;
	$set_old = array_intersect_key( $old_s, array_flip( bst_aid_form_setting_keys() ) );
	$set_new = array();
	foreach ( array_intersect_key( (array) ( $a['settings'] ?? array() ), array_flip( bst_aid_form_setting_keys() ) ) as $k => $v ) {
		$set_new[ $k ] = sanitize_text_field( (string) $v );
	}
	if ( ! $fields ) {
		return array( 'error' => 'A form needs at least one field.' );
	}
	$merged   = array_merge( $set_old, $set_new );
	$problems = bst_aid_form_problems( $fields );
	$diff     = bst_aid_form_diff( $old, $fields, $set_old, $set_new );
	$report   = array(
		'diff'     => $diff,
		'example'  => bst_aid_form_example( $fields, $merged, $a['sample'] ?? array() ),
		'problems' => $problems,
	);
	if ( $problems ) {
		return array( 'error' => 'invalid_schema', 'message' => 'Fix these problems, then dry-run again. Nothing was saved.' ) + $report;
	}
	if ( ! isset( $a['dry_run'] ) || false !== $a['dry_run'] ) {
		return array( 'dry_run' => true, 'message' => 'Nothing saved. Show the user the changes and the example; save with dry_run: false after they agree.' ) + $report;
	}
	if ( ! function_exists( 'wp_check_post_lock' ) ) {
		require_once ABSPATH . 'wp-admin/includes/post.php';
	}
	if ( wp_check_post_lock( $p->ID ) ) {
		return array( 'error' => 'Someone is editing this page right now. Try again when they are done.' );
	}
	$attrs                  = $hit['attrs'];
	$attrs['fields']        = $fields;
	$attrs['schemaVersion'] = bst_form_schema_version();
	foreach ( $set_new as $k => $v ) {
		$attrs[ $k ] = $v;
	}
	bst_aid_form_set_attrs( $blocks, $hit['path'], $attrs );
	bst_aid_snapshot_post( $p->ID );
	$r = wp_update_post( array( 'ID' => $p->ID, 'post_content' => wp_slash( serialize_blocks( $blocks ) ) ), true );
	if ( is_wp_error( $r ) ) {
		return array( 'error' => $r->get_error_message() );
	}
	return array( 'saved' => true, 'id' => $p->ID, 'preview' => get_preview_post_link( $p->ID ), 'note' => 'Saved. The task can be undone. Ask the user to check the live price on the preview.' ) + $report;
}

/** Does a form_config call change prices or formulas? Used by the approval policy. */
function bst_aid_form_call_prices( $a ) {
	if ( ! empty( $a['dry_run'] ) || ! isset( $a['dry_run'] ) ) {
		return false;
	}
	$p = get_post( (int) ( $a['id'] ?? 0 ) );
	if ( ! $p ) {
		return true;
	}
	foreach ( bst_aid_form_walk( parse_blocks( $p->post_content ) ) as $h ) {
		if ( ( $h['attrs']['formKey'] ?? '' ) === (string) ( $a['key'] ?? '' ) ) {
			$s      = bst_native_form_settings( $h['attrs'] );
			$fields = isset( $a['fields'] ) ? bst_aid_form_merge_fields( $s['fields'], $a['fields'] ) : $s['fields'];
			$set    = array_intersect_key( (array) ( $a['settings'] ?? array() ), array_flip( bst_aid_form_setting_keys() ) );
			return bst_aid_form_diff( $s['fields'], $fields, array_intersect_key( $s, array_flip( bst_aid_form_setting_keys() ) ), $set )['pricing_changed'];
		}
	}
	return true;
}

add_filter(
	'bst_aid_tools',
	function ( $tools ) {
		$field = array(
			'type'       => 'object',
			'properties' => array(
				'id'           => array( 'type' => 'string', 'description' => 'Keep the id from get_form for existing fields. Omit for a new field.' ),
				'type'         => array( 'type' => 'string', 'enum' => bst_native_field_types() ),
				'name'         => array( 'type' => 'string', 'description' => 'Mail tag, a-z 0-9 -. Keep existing names: mail templates use them.' ),
				'label'        => array( 'type' => 'string' ),
				'placeholder'  => array( 'type' => 'string' ),
				'required'     => array( 'type' => 'boolean' ),
				'width'        => array( 'type' => 'string', 'enum' => array( 'full', 'half' ) ),
				'options'      => array( 'type' => 'string', 'description' => 'select/radio/checkbox choices, one per line.' ),
				'optionPrices' => array( 'type' => 'object', 'additionalProperties' => array( 'type' => 'number' ), 'description' => 'Option text => price the user gave. Use 1 for a yes/no flag. No price = the total shows "needs confirmation".' ),
				'formula'      => array( 'type' => 'string', 'description' => 'calc only: numbers, {field_id}, + - * / ( ), > < >= <= == !=, && ||, min max round ceil floor abs if(cond,a,b). A field reference gives: number = its value, choice = sum of its option prices, hidden = 0.' ),
				'isTotal'      => array( 'type' => 'boolean', 'description' => 'calc only: the total line.' ),
				'min'          => array( 'type' => 'number' ),
				'max'          => array( 'type' => 'number' ),
				'conditions'   => array( 'type' => 'object', 'description' => '{ "action": "show"|"hide", "match": "all"|"any", "rules": [ { "field": "<id>", "op": "is|is_not|empty|not_empty|contains|gt|lt", "value": "…" } ] }' ),
			),
			'required'   => array( 'type' ),
		);
		$tools['get_form']    = array(
			'description' => 'Read the Form blocks of a page: key, fields with stable ids, conditions, option prices, formulas, currency and current problems. Call before form_config.',
			'parameters'  => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'integer' ), 'key' => array( 'type' => 'string' ) ), 'required' => array( 'id' ) ),
			'cap'         => 'edit_pages',
			'callback'    => 'bst_aid_tool_get_form',
		);
		$tools['form_config'] = array(
			'description' => 'Change a built-in form: fields (send the full list in order; keep ids), conditions, option prices, formulas, currency. dry_run defaults to true: it validates and returns the diff and the price lines for sample values (by field name) without saving. Show the user the rules and a worked example; save with dry_run: false only after they agree. Use only prices and rules the user gave; ask when any is missing. Never put formulas or scripts in Code blocks.',
			'parameters'  => array(
				'type'       => 'object',
				'properties' => array(
					'id'       => array( 'type' => 'integer' ),
					'key'      => array( 'type' => 'string' ),
					'fields'   => array( 'type' => 'array', 'maxItems' => 40, 'items' => $field ),
					'settings' => array( 'type' => 'object', 'properties' => array( 'formName' => array( 'type' => 'string' ), 'submitText' => array( 'type' => 'string' ), 'priceCurrency' => array( 'type' => 'string' ), 'priceDecimals' => array( 'type' => 'string' ), 'priceNote' => array( 'type' => 'string' ), 'msgSuccess' => array( 'type' => 'string' ) ) ),
					'sample'   => array( 'type' => 'object', 'additionalProperties' => true, 'description' => 'Example input by field name, e.g. { "so-kg": "3", "giao-nhan": ["Giao nhận tận nơi"] }.' ),
					'dry_run'  => array( 'type' => 'boolean' ),
				),
				'required'   => array( 'id', 'key' ),
			),
			'cap'         => 'edit_pages',
			'callback'    => 'bst_aid_tool_form_config',
		);
		return $tools;
	}
);
