<?php
/**
 * AI tools for the built-in Form (G-59): read a form, then change its fields,
 * conditions and price formulas through the same schema the editor uses.
 *
 * - form_configure validates first (schema + pricing) and refuses to write a broken
 *   form; dry_run returns the diff and a sample quote without saving.
 * - Only the Form block's own settings change: layout, other blocks and mail
 *   settings stay. The page is snapshotted, so task undo restores it.
 * - Formulas are the limited quote language (no code); prices must come from the
 *   user. Changing prices or formulas of a published page needs approval.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'bst_aid_tools', function ( $tools ) {
	$cond  = array(
		'type'        => 'object',
		'description' => '{ "logic": "all"|"any", "rules": [ { "field": "f_…", "op": "is|is_not|empty|not_empty|contains|gt|lt", "value": "…" } ] }',
	);
	$field = array(
		'type'       => 'object',
		'properties' => array(
			'id'           => array( 'type' => 'string', 'description' => 'Keep the existing id (f_…). New field: omit or give f_<short-name>.' ),
			'type'         => array( 'type' => 'string', 'enum' => bst_native_field_types() ),
			'name'         => array( 'type' => 'string', 'description' => 'Mail tag name, a-z 0-9 -. Keep existing names: mail templates use them.' ),
			'label'        => array( 'type' => 'string' ),
			'placeholder'  => array( 'type' => 'string' ),
			'options'      => array( 'type' => 'string', 'description' => 'Choices, one per line (select/radio/checkbox).' ),
			'prices'       => array( 'type' => 'string', 'description' => 'Option prices, one per line: "Option = 25000". Only prices the user gave.' ),
			'default'      => array( 'type' => 'string' ),
			'required'     => array( 'type' => 'boolean' ),
			'width'        => array( 'type' => 'string', 'enum' => array( 'full', 'half' ) ),
			'show'         => $cond,
			'requiredWhen' => $cond,
		),
		'required'   => array( 'type', 'label' ),
	);
	$tools['form_get']       = array(
		'description' => 'Read the built-in Form blocks (bst/form) of a page: index, form key, fields with ids, conditions, price lines, problems and the pricing revision. Call before form_configure.',
		'parameters'  => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'integer', 'description' => 'Page, post or UX block id.' ) ), 'required' => array( 'id' ) ),
		'cap'         => 'edit_pages',
		'callback'    => 'bst_aid_tool_form_get',
	);
	$tools['form_configure'] = array(
		'description' => 'Change one built-in Form block: the full field list and/or price quote settings. Send the complete list you want (existing fields keep their id and name). Price formula language: numbers, + - * / ( ), > < >= <= == !=, min(), max(), round(x, digits), ceil(), floor(), if(cond, a, b); {f_id} is a field (number, or summed option prices), {f_id=Option} is 1 when picked, {l_id} is another line. Never invent prices, routes, availability or taxes: ask the user. Run with dry_run: true first and show the user the diff and sample quote; save only after they agree. Turns the form into the built-in provider. Not for booking or payment.',
		'parameters'  => array(
			'type'       => 'object',
			'properties' => array(
				'id'          => array( 'type' => 'integer', 'description' => 'Page, post or UX block id.' ),
				'index'       => array( 'type' => 'integer', 'description' => 'Which Form block on the page (from form_get), default 0.' ),
				'fields'      => array( 'type' => 'array', 'items' => $field ),
				'pricing'     => array(
					'type'       => 'object',
					'properties' => array(
						'enabled'  => array( 'type' => 'boolean' ),
						'symbol'   => array( 'type' => 'string' ),
						'decimals' => array( 'type' => 'integer', 'minimum' => 0, 'maximum' => 3 ),
						'note'     => array( 'type' => 'string', 'description' => 'What is included or not; mark demo prices as demo.' ),
						'lines'    => array(
							'type'  => 'array',
							'items' => array(
								'type'       => 'object',
								'properties' => array( 'id' => array( 'type' => 'string', 'description' => 'l_…' ), 'label' => array( 'type' => 'string' ), 'expr' => array( 'type' => 'string' ) ),
								'required'   => array( 'label', 'expr' ),
							),
						),
					),
				),
				'submit_text' => array( 'type' => 'string' ),
				'test_values' => array( 'type' => 'object', 'additionalProperties' => true, 'description' => 'Sample answers by field name, to show a sample quote.' ),
				'dry_run'     => array( 'type' => 'boolean' ),
			),
			'required'   => array( 'id' ),
		),
		'cap'         => 'edit_pages',
		'callback'    => 'bst_aid_tool_form_configure',
	);
	return $tools;
} );

/** Form blocks of a block list, in document order, each with its path. */
function bst_aid_form_blocks( $blocks, $path = array() ) {
	$out = array();
	foreach ( $blocks as $i => $b ) {
		if ( 'bst/form' === ( $b['blockName'] ?? '' ) ) {
			$out[] = array( 'path' => array_merge( $path, array( $i ) ), 'attrs' => (array) $b['attrs'] );
		}
		if ( ! empty( $b['innerBlocks'] ) ) {
			$out = array_merge( $out, bst_aid_form_blocks( $b['innerBlocks'], array_merge( $path, array( $i ) ) ) );
		}
	}
	return $out;
}

/** The post, if this user may edit it; else an error array. */
function bst_aid_form_post( $id ) {
	$p = get_post( (int) $id );
	if ( ! $p || 'trash' === $p->post_status ) {
		return array( 'error' => 'Post not found.' );
	}
	if ( ! current_user_can( 'edit_post', $p->ID ) ) {
		return array( 'error' => 'You cannot edit this post.' );
	}
	return $p;
}

/** Human-readable problems of a form. */
function bst_aid_form_problems( $fields, $pricing ) {
	$out = array();
	foreach ( bst_form_schema_validate( $fields ) as $e ) {
		$out[] = 'field ' . $e['field'] . ': ' . $e['code'] . ( $e['ref'] !== $e['field'] ? ' (' . $e['ref'] . ')' : '' );
	}
	foreach ( bst_quote_validate( $pricing, $fields ) as $e ) {
		$out[] = 'price line ' . $e['line'] . ': ' . $e['code'] . ( $e['ref'] && $e['ref'] !== $e['line'] ? ' (' . $e['ref'] . ')' : '' );
	}
	return $out;
}

/** Compact view of one form for the model. */
function bst_aid_form_view( $a, $index ) {
	$s = bst_native_form_settings( $a );
	return array(
		'index'    => $index,
		'provider' => (string) ( $a['provider'] ?? 'cf7' ),
		'form_key' => (string) ( $a['formKey'] ?? '' ),
		'name'     => $s['formName'],
		'fields'   => $s['fields'],
		'pricing'  => bst_quote_settings( $s['pricing'] ),
		'revision' => bst_quote_revision( $s['pricing'], $s['fields'] ),
		'problems' => bst_aid_form_problems( $s['fields'], $s['pricing'] ),
	);
}

function bst_aid_tool_form_get( $a ) {
	$p = bst_aid_form_post( $a['id'] ?? 0 );
	if ( is_array( $p ) ) {
		return $p;
	}
	$forms = array();
	foreach ( bst_aid_form_blocks( parse_blocks( $p->post_content ) ) as $i => $f ) {
		$forms[] = bst_aid_form_view( $f['attrs'], $i );
	}
	return array( 'id' => $p->ID, 'forms' => $forms, 'note' => $forms ? '' : 'No Form block on this page. Add bst/form with the page tools first.' );
}

/** What changed between two field lists and pricing settings, for the user to review. */
function bst_aid_form_diff( $old_f, $new_f, $old_p, $new_p ) {
	$old  = array_column( $old_f, null, 'id' );
	$new  = array_column( $new_f, null, 'id' );
	$diff = array();
	foreach ( $new as $id => $f ) {
		if ( ! isset( $old[ $id ] ) ) {
			$diff[] = 'add field ' . $id . ' "' . ( $f['label'] ?? '' ) . '" (' . $f['type'] . ')';
			continue;
		}
		foreach ( array_unique( array_merge( array_keys( $old[ $id ] ), array_keys( $f ) ) ) as $k ) {
			$a = $old[ $id ][ $k ] ?? null;
			$b = $f[ $k ] ?? null;
			if ( wp_json_encode( $a ) !== wp_json_encode( $b ) && ! ( empty( $a ) && empty( $b ) ) ) {
				$diff[] = 'field ' . $id . ' ' . $k . ': ' . wp_json_encode( $a, JSON_UNESCAPED_UNICODE ) . ' → ' . wp_json_encode( $b, JSON_UNESCAPED_UNICODE );
			}
		}
	}
	foreach ( array_diff_key( $old, $new ) as $id => $f ) {
		$diff[] = 'remove field ' . $id . ' "' . ( $f['label'] ?? '' ) . '"';
	}
	if ( array_column( $old_f, 'id' ) !== array_values( array_intersect( array_column( $new_f, 'id' ), array_column( $old_f, 'id' ) ) ) ) {
		$diff[] = 'field order changed';
	}
	$op = bst_quote_settings( $old_p );
	$np = bst_quote_settings( $new_p );
	foreach ( array( 'enabled', 'symbol', 'decimals', 'note' ) as $k ) {
		if ( $op[ $k ] !== $np[ $k ] ) {
			$diff[] = 'pricing ' . $k . ': ' . wp_json_encode( $op[ $k ], JSON_UNESCAPED_UNICODE ) . ' → ' . wp_json_encode( $np[ $k ], JSON_UNESCAPED_UNICODE );
		}
	}
	$ol = array_column( $op['lines'], null, 'id' );
	$nl = array_column( $np['lines'], null, 'id' );
	foreach ( $nl as $id => $l ) {
		if ( ! isset( $ol[ $id ] ) ) {
			$diff[] = 'add price line ' . $id . ' "' . $l['label'] . '" = ' . $l['expr'];
		} elseif ( $ol[ $id ] !== $l ) {
			$diff[] = 'price line ' . $id . ': "' . $ol[ $id ]['label'] . '" = ' . $ol[ $id ]['expr'] . ' → "' . $l['label'] . '" = ' . $l['expr'];
		}
	}
	foreach ( array_diff_key( $ol, $nl ) as $id => $l ) {
		$diff[] = 'remove price line ' . $id . ' "' . $l['label'] . '"';
	}
	return $diff;
}

/** Clean what the model sent into stored field objects (unknown keys dropped). */
function bst_aid_form_clean_fields( $in, $old ) {
	$keep  = array( 'id', 'type', 'name', 'label', 'placeholder', 'options', 'prices', 'default', 'required', 'width', 'show', 'requiredWhen' );
	$names = array();
	$out   = array();
	$old   = array_column( $old, null, 'id' );
	foreach ( (array) $in as $f ) {
		if ( ! is_array( $f ) ) {
			continue;
		}
		$f    = array_intersect_key( $f, array_flip( $keep ) );
		$prev = isset( $f['id'] ) ? ( $old[ strtolower( (string) $f['id'] ) ] ?? null ) : null;
		$f    = array_merge( $prev ? array_intersect_key( $prev, array_flip( array( 'lockName', 'autoName' ) ) ) : array(), $f );
		foreach ( array( 'label', 'placeholder', 'default' ) as $k ) {
			if ( isset( $f[ $k ] ) ) {
				$f[ $k ] = sanitize_text_field( (string) $f[ $k ] );
			}
		}
		foreach ( array( 'options', 'prices' ) as $k ) {
			if ( isset( $f[ $k ] ) ) {
				$f[ $k ] = sanitize_textarea_field( (string) $f[ $k ] );
			}
		}
		$f['type']     = in_array( $f['type'] ?? '', bst_native_field_types(), true ) ? $f['type'] : 'text';
		$f['required'] = ! empty( $f['required'] );
		$f['width']    = 'half' === ( $f['width'] ?? '' ) ? 'half' : 'full';
		$name          = sanitize_key( $f['name'] ?? ( $prev['name'] ?? '' ) );
		if ( '' === $name ) {
			$name = sanitize_key( remove_accents( str_replace( ' ', '-', strtolower( (string) $f['label'] ) ) ) );
		}
		$base = $name ? $name : 'field';
		$n    = 2;
		while ( isset( $names[ $name ] ) || '' === $name ) {
			$name = $base . '-' . $n++;
		}
		$names[ $name ] = true;
		$f['name']      = $name;
		if ( empty( $f['id'] ) ) {
			$f['id'] = 'f_' . substr( preg_replace( '/[^a-z0-9_-]/', '', $name ), 0, 58 );
		}
		$out[] = $f;
	}
	return bst_form_schema_normalize( $out );
}

function bst_aid_tool_form_configure( $a ) {
	$p = bst_aid_form_post( $a['id'] ?? 0 );
	if ( is_array( $p ) ) {
		return $p;
	}
	$blocks = parse_blocks( $p->post_content );
	$forms  = bst_aid_form_blocks( $blocks );
	$index  = (int) ( $a['index'] ?? 0 );
	if ( ! isset( $forms[ $index ] ) ) {
		return array( 'error' => $forms ? 'No Form block with this index; call form_get.' : 'No Form block on this page. Add bst/form first.' );
	}
	$attrs = $forms[ $index ]['attrs'];
	$old   = bst_native_form_settings( $attrs );
	$new   = $attrs;
	if ( isset( $a['fields'] ) ) {
		$new['fields'] = bst_aid_form_clean_fields( $a['fields'], $old['fields'] );
		if ( ! $new['fields'] ) {
			return array( 'error' => 'A form needs at least one field.' );
		}
	} else {
		$new['fields'] = $old['fields'];
	}
	if ( isset( $a['pricing'] ) && is_array( $a['pricing'] ) ) {
		$pr = array_merge( bst_quote_settings( $old['pricing'] ), $a['pricing'] );
		foreach ( (array) ( $pr['lines'] ?? array() ) as $k => $l ) {
			$pr['lines'][ $k ] = array(
				'id'    => ! empty( $l['id'] ) ? strtolower( (string) $l['id'] ) : 'l_' . substr( md5( wp_json_encode( $l ) . $k ), 0, 6 ),
				'label' => sanitize_text_field( (string) ( $l['label'] ?? '' ) ),
				'expr'  => (string) ( $l['expr'] ?? '' ),
			);
		}
		$pr['symbol'] = sanitize_text_field( (string) ( $pr['symbol'] ?? '₫' ) );
		$pr['note']   = sanitize_text_field( (string) ( $pr['note'] ?? '' ) );
		$new['pricing'] = array_intersect_key( $pr, array_flip( array( 'enabled', 'symbol', 'decimals', 'note', 'lines' ) ) );
	}
	if ( isset( $a['submit_text'] ) ) {
		$new['submitText'] = sanitize_text_field( (string) $a['submit_text'] );
	}
	$new['provider']   = 'bigspy';
	$new['formSchema'] = BST_FORM_SCHEMA_VERSION;
	if ( empty( $new['formKey'] ) ) {
		$new['formKey'] = 'f' . strtolower( wp_generate_password( 8, false ) );
	}
	$pricing  = $new['pricing'] ?? array();
	$problems = bst_aid_form_problems( $new['fields'], $pricing );
	$diff     = bst_aid_form_diff( $old['fields'], $new['fields'], $old['pricing'], $pricing );
	if ( 'bigspy' !== ( $attrs['provider'] ?? 'cf7' ) ) {
		array_unshift( $diff, 'switch provider ' . ( $attrs['provider'] ?? 'cf7' ) . ' → built-in form' );
	}
	$out = array( 'id' => $p->ID, 'index' => $index, 'diff' => $diff, 'problems' => $problems );
	if ( ! empty( $a['test_values'] ) && is_array( $a['test_values'] ) ) {
		$values = array();
		foreach ( $a['test_values'] as $k => $v ) {
			$values[ sanitize_key( $k ) ] = is_array( $v ) ? implode( ', ', array_map( 'sanitize_text_field', $v ) ) : sanitize_text_field( (string) $v );
		}
		$q                   = bst_quote_compute( $pricing, $new['fields'], $values );
		$out['sample_quote'] = 'ok' === $q['status'] ? bst_quote_text( $q ) : $q['status'];
	}
	if ( $problems ) {
		return $out + array( 'saved' => false, 'error' => 'Fix the problems first; nothing was saved.' );
	}
	if ( ! empty( $a['dry_run'] ) || ! $diff ) {
		return $out + array( 'saved' => false, 'note' => $diff ? 'Dry run: show the user the diff and sample quote, then call again without dry_run.' : 'Nothing to change.' );
	}
	// Replace only this block's attributes; everything else in the page is kept as saved.
	$ref = &$blocks;
	$path = $forms[ $index ]['path'];
	$last = array_pop( $path );
	foreach ( $path as $i ) {
		$ref = &$ref[ $i ]['innerBlocks'];
	}
	$ref[ $last ]['attrs'] = $new;
	unset( $ref );
	bst_aid_snapshot_post( $p->ID );
	$r = wp_update_post( array( 'ID' => $p->ID, 'post_content' => wp_slash( serialize_blocks( $blocks ) ) ), true );
	if ( is_wp_error( $r ) ) {
		return array( 'error' => $r->get_error_message() );
	}
	return $out + array( 'saved' => true, 'revision' => bst_quote_revision( $pricing, $new['fields'] ), 'links' => bst_aid_links( $p->ID, get_the_title( $p->ID ) ), 'preview' => get_preview_post_link( $p->ID ) );
}
