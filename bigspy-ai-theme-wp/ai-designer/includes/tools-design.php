<?php
/**
 * Phase 2 tools: design pages with BIGSPY blocks, theme design settings, logo, Additional
 * CSS, menus, header layout and palette previews. Every change to settings, CSS, logo,
 * menus or header is snapshotted on the task so the whole task can be undone.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/* ------------------------------------------------------------------ Block rules */

/** Blocks that hold child blocks, and the only children some of them accept. */
function bst_aid_block_containers() {
	return array(
		'bst/section'        => array(),
		'bst/row'            => array( 'bst/column' ),
		'bst/column'         => array(),
		'bst/banner'         => array(),
		'bst/slider'         => array(),
		'bst/icon-box'       => array(),
		'bst/tabs'           => array( 'bst/tab' ),
		'bst/tab'            => array(),
		'bst/accordion'      => array( 'bst/accordion-item' ),
		'bst/accordion-item' => array(),
		'bst/buttons'        => array( 'bst/button' ),
		'bst/popup'          => array(),
	);
}

/** Blocks that must sit inside a given parent. */
function bst_aid_block_parents() {
	return array(
		'bst/column'         => 'bst/row',
		'bst/tab'            => 'bst/tabs',
		'bst/accordion-item' => 'bst/accordion',
		'bst/button'         => 'bst/buttons',
	);
}

/** WordPress core blocks the designer may use (patterns mix them into BIGSPY sections). */
function bst_aid_core_blocks() {
	return array( 'core/paragraph', 'core/heading', 'core/list', 'core/list-item', 'core/buttons', 'core/button', 'core/image', 'core/spacer', 'core/separator', 'core/group', 'core/columns', 'core/column', 'core/quote', 'core/html', 'core/shortcode', 'core/table', 'core/details' );
}

/** Registered BIGSPY block types the user has not switched off. */
function bst_aid_block_types() {
	$off = function_exists( 'bst_disabled_blocks' ) ? (array) bst_disabled_blocks() : array();
	$out = array();
	foreach ( WP_Block_Type_Registry::get_instance()->get_all_registered() as $name => $type ) {
		if ( 0 === strpos( $name, 'bst/' ) && ! in_array( substr( $name, 4 ), $off, true ) && ! in_array( $name, $off, true ) ) {
			$out[ $name ] = $type;
		}
	}
	if ( ! class_exists( 'WooCommerce' ) ) {
		unset( $out['bst/products'], $out['bst/product-categories'] );
	}
	return $out;
}

/** Coerce one attribute value to its schema; returns array( ok, value ). */
function bst_aid_coerce_attr( $value, $schema ) {
	$type = $schema['type'] ?? null;
	$type = is_array( $type ) ? reset( $type ) : $type;
	switch ( $type ) {
		case 'string':
			if ( ! is_scalar( $value ) ) {
				return array( false, null );
			}
			$value = (string) $value;
			break;
		case 'number':
		case 'integer':
			if ( ! is_numeric( $value ) ) {
				return array( false, null );
			}
			$value = 'integer' === $type ? (int) $value : $value + 0;
			break;
		case 'boolean':
			$value = is_string( $value ) ? in_array( strtolower( $value ), array( '1', 'true', 'yes', 'on' ), true ) : (bool) $value;
			break;
		case 'array':
		case 'object':
			if ( ! is_array( $value ) ) {
				return array( false, null );
			}
			break;
	}
	if ( isset( $schema['enum'] ) && ! in_array( $value, (array) $schema['enum'], true ) ) {
		return array( false, null );
	}
	return array( true, $value );
}

/** Strip scripts and event handlers from code when the user may not post unfiltered HTML. */
function bst_aid_clean_code( $code ) {
	if ( current_user_can( 'unfiltered_html' ) ) {
		return (string) $code;
	}
	$allowed = wp_kses_allowed_html( 'post' );
	$allowed['style'] = array();
	$allowed['svg']   = array( 'xmlns' => true, 'viewbox' => true, 'width' => true, 'height' => true, 'fill' => true, 'class' => true, 'style' => true, 'aria-hidden' => true, 'role' => true );
	foreach ( array( 'path' => array( 'd', 'fill', 'stroke', 'stroke-width' ), 'circle' => array( 'cx', 'cy', 'r', 'fill', 'stroke' ), 'rect' => array( 'x', 'y', 'width', 'height', 'rx', 'fill', 'stroke' ), 'g' => array( 'fill', 'transform' ), 'line' => array( 'x1', 'y1', 'x2', 'y2', 'stroke', 'stroke-width' ), 'polygon' => array( 'points', 'fill' ) ) as $tag => $attrs ) {
		$allowed[ $tag ] = array_fill_keys( array_merge( $attrs, array( 'class', 'style' ) ), true );
	}
	return wp_kses( (string) $code, $allowed );
}

/**
 * Validate a block tree from the model and turn it into WordPress block arrays.
 * Unknown attributes are dropped; structural mistakes are returned as errors so the
 * model can fix them.
 *
 * @param array  $nodes  [{ name, attrs, inner }].
 * @param string $parent Parent block name ('' at the top).
 * @param array  $issues Collected warnings / errors.
 */
function bst_aid_build_blocks( $nodes, $parent, &$issues, $path = '' ) {
	$types      = bst_aid_block_types();
	$containers = bst_aid_block_containers();
	$parents    = bst_aid_block_parents();
	$out        = array();
	foreach ( array_values( (array) $nodes ) as $i => $node ) {
		$where = ltrim( $path . '/' . $i, '/' );
		$name  = (string) ( $node['name'] ?? '' );
		if ( '' !== $name && false === strpos( $name, '/' ) ) {
			$name = 'bst/' . $name;
		}
		if ( 0 === strpos( $name, 'core/' ) ) {
			$core = bst_aid_build_core_block( $name, $node, $where, $issues );
			if ( $core ) {
				$out[] = $core;
			}
			continue;
		}
		if ( ! isset( $types[ $name ] ) ) {
			$issues['errors'][] = "$where: unknown or disabled block \"$name\". Use list_blocks.";
			continue;
		}
		if ( isset( $parents[ $name ] ) && $parents[ $name ] !== $parent ) {
			$issues['errors'][] = "$where: $name must be placed inside {$parents[ $name ]}.";
			continue;
		}
		if ( $parent && ! empty( $containers[ $parent ] ) && ! in_array( $name, $containers[ $parent ], true ) ) {
			$issues['errors'][] = "$where: $parent only accepts " . implode( ', ', $containers[ $parent ] ) . '.';
			continue;
		}
		$schema = (array) $types[ $name ]->attributes;
		$attrs  = array();
		foreach ( (array) ( $node['attrs'] ?? array() ) as $key => $value ) {
			if ( ! isset( $schema[ $key ] ) ) {
				$issues['warnings'][] = "$where ($name): dropped unknown attribute \"$key\".";
				continue;
			}
			list( $ok, $value ) = bst_aid_coerce_attr( $value, $schema[ $key ] );
			if ( ! $ok ) {
				$issues['warnings'][] = "$where ($name): dropped invalid value for \"$key\".";
				continue;
			}
			if ( array_key_exists( 'default', $schema[ $key ] ) && $schema[ $key ]['default'] === $value ) {
				continue; // Defaults are not stored, like the editor does.
			}
			$attrs[ $key ] = $value;
		}
		$attrs = bst_aid_clean_block_attrs( $name, $attrs );
		if ( 'bst/code' === $name && isset( $attrs['code'] ) ) {
			$attrs['code'] = bst_aid_clean_code( $attrs['code'] );
		}
		$inner = array();
		if ( ! empty( $node['inner'] ) ) {
			if ( ! isset( $containers[ $name ] ) ) {
				$issues['warnings'][] = "$where: $name cannot hold blocks; its inner blocks were dropped.";
			} else {
				$inner = bst_aid_build_blocks( $node['inner'], $name, $issues, $where );
			}
		}
		$out[] = array(
			'blockName'    => $name,
			'attrs'        => $attrs,
			'innerBlocks'  => $inner,
			'innerHTML'    => '',
			'innerContent' => array_fill( 0, count( $inner ), null ),
		);
	}
	return $out;
}

/**
 * A core block from the model tree. Blocks read with get_page / get_pattern carry their
 * exact markup in "raw" and are kept as they are; new ones are made from "html" (paragraph,
 * heading with attrs.level, list, html, shortcode).
 */
function bst_aid_build_core_block( $name, $node, $where, &$issues ) {
	if ( ! in_array( $name, bst_aid_core_blocks(), true ) ) {
		$issues['errors'][] = "$where: core block \"$name\" is not allowed. Use a BIGSPY block.";
		return null;
	}
	if ( ! empty( $node['raw'] ) ) {
		$parsed = parse_blocks( (string) $node['raw'] );
		foreach ( $parsed as $b ) {
			if ( ( $b['blockName'] ?? '' ) === $name ) {
				return $b;
			}
		}
		$issues['errors'][] = "$where: the raw markup of $name could not be read; send it again unchanged or use html.";
		return null;
	}
	$html  = (string) ( $node['html'] ?? '' );
	$attrs = is_array( $node['attrs'] ?? null ) ? $node['attrs'] : array();
	switch ( $name ) {
		case 'core/heading':
			$level = max( 1, min( 6, (int) ( $attrs['level'] ?? 2 ) ) );
			$text  = bst_aid_clean_heading( wp_strip_all_tags( $html, false ) === $html ? $html : preg_replace( '#^<h[1-6][^>]*>(.*)</h[1-6]>$#is', '$1', trim( $html ) ) );
			$html  = '<h' . $level . ' class="wp-block-heading">' . wp_kses_post( $text ) . '</h' . $level . '>';
			$attrs = 2 === $level ? array() : array( 'level' => $level );
			break;
		case 'core/paragraph':
			$text = preg_replace( '#^<p[^>]*>(.*)</p>$#is', '$1', trim( $html ) );
			$html = '<p>' . wp_kses_post( bst_aid_md_inline( $text ) ) . '</p>';
			$attrs = array();
			break;
		case 'core/html':
			$html  = bst_aid_clean_code( $html );
			$attrs = array();
			break;
		case 'core/shortcode':
			$html  = sanitize_text_field( $html );
			$attrs = array();
			break;
		case 'core/spacer':
		case 'core/separator':
			$parsed = parse_blocks( 'core/spacer' === $name ? '<!-- wp:spacer --><div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div><!-- /wp:spacer -->' : '<!-- wp:separator --><hr class="wp-block-separator has-alpha-channel-opacity"/><!-- /wp:separator -->' );
			return $parsed[0] ?? null;
		default:
			// Lists, quotes, tables, groups…: easiest as clean HTML in a Custom HTML block.
			$issues['warnings'][] = "$where: $name was saved as a Custom HTML block; prefer BIGSPY blocks (bst/text, bst/buttons…).";
			$name  = 'core/html';
			$html  = wp_kses_post( bst_aid_clean_html( $html ) );
			$attrs = array();
	}
	return array( 'blockName' => $name, 'attrs' => $attrs, 'innerBlocks' => array(), 'innerHTML' => $html, 'innerContent' => array( $html ) );
}

/** Block markup from a model tree, or WP_Error with the problems. */
function bst_aid_tree_to_markup( $tree ) {
	$issues = array( 'errors' => array(), 'warnings' => array() );
	$blocks = bst_aid_build_blocks( $tree, '', $issues );
	if ( $issues['errors'] ) {
		return new WP_Error( 'bst_aid_blocks', 'Fix these block errors and call again', $issues );
	}
	if ( ! $blocks ) {
		return new WP_Error( 'bst_aid_blocks', 'No blocks given.', $issues );
	}
	return array( 'markup' => serialize_blocks( $blocks ), 'warnings' => array_slice( $issues['warnings'], 0, 30 ) );
}

/** Compact tree of parsed blocks for the model: only non-default attributes. */
function bst_aid_compact_blocks( $blocks ) {
	$out = array();
	foreach ( (array) $blocks as $b ) {
		if ( empty( $b['blockName'] ) ) {
			$html = trim( (string) $b['innerHTML'] );
			if ( '' !== $html ) {
				$out[] = array( 'name' => 'html', 'html' => mb_substr( $html, 0, 600 ) );
			}
			continue;
		}
		$node = array( 'name' => $b['blockName'] );
		if ( ! empty( $b['attrs'] ) ) {
			$node['attrs'] = $b['attrs'];
		}
		if ( 0 !== strpos( $b['blockName'], 'bst/' ) ) {
			// Core blocks: keep the exact markup ("raw") so they can be sent back unchanged.
			$node['raw']  = serialize_block( $b );
			$node['text'] = mb_substr( trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( render_block( $b ) ) ) ), 0, 300 );
			unset( $node['attrs'] );
			$out[] = $node;
			continue;
		}
		if ( ! empty( $b['innerBlocks'] ) ) {
			$node['inner'] = bst_aid_compact_blocks( $b['innerBlocks'] );
		}
		$out[] = $node;
	}
	return $out;
}

/* ------------------------------------------------------------------ Snapshots (undo) */

/** Remember the value before the first change in this task. */
function bst_aid_snapshot( $kind, $key, $old ) {
	$task = (int) ( $GLOBALS['bst_aid_task_id'] ?? 0 );
	if ( ! $task ) {
		return;
	}
	$snaps = bst_aid_task_get( $task, '_bst_aid_snapshots' );
	$id    = $kind . ':' . $key;
	if ( ! isset( $snaps[ $id ] ) ) {
		$snaps[ $id ] = array( 'kind' => $kind, 'key' => $key, 'old' => $old, 't' => time() );
		bst_aid_task_set( $task, '_bst_aid_snapshots', $snaps );
	}
}

/** Record content the task created, so undo can move it to the trash. */
function bst_aid_track_created( $post_id ) {
	$task = (int) ( $GLOBALS['bst_aid_task_id'] ?? 0 );
	if ( $task ) {
		$made   = bst_aid_task_get( $task, '_bst_aid_created' );
		$made[] = (int) $post_id;
		bst_aid_task_set( $task, '_bst_aid_created', array_values( array_unique( $made ) ) );
	}
}

/** Undo a task: restore settings, CSS, logo, menus and header; trash what it created; restore edited pages. */
function bst_aid_undo_task( $task ) {
	$done  = array();
	$snaps = array_reverse( bst_aid_task_get( $task, '_bst_aid_snapshots' ) );
	foreach ( $snaps as $s ) {
		switch ( $s['kind'] ) {
			case 'mod':
				null === $s['old'] ? remove_theme_mod( $s['key'] ) : set_theme_mod( $s['key'], $s['old'] );
				break;
			case 'option':
				update_option( $s['key'], $s['old'] );
				break;
			case 'css':
				wp_update_custom_css_post( (string) $s['old'] );
				break;
			case 'menu':
				bst_aid_restore_menu( $s['old'] );
				break;
			case 'menu_created':
				if ( wp_get_nav_menu_object( (int) $s['key'] ) ) {
					wp_delete_nav_menu( (int) $s['key'] ); // Only a menu this task created.
				}
				break;
			case 'post':
				$rev_id = (int) $s['old'];
				$rev    = wp_get_post_revision( $rev_id );
				if ( $rev ) {
					wp_restore_post_revision( $rev->ID );
				}
				break;
		}
		$done[] = $s['kind'] . ':' . $s['key'];
	}
	foreach ( bst_aid_task_get( $task, '_bst_aid_created' ) as $pid ) {
		if ( get_post( $pid ) && 'trash' !== get_post_status( $pid ) ) {
			wp_trash_post( $pid ); // Trash only, never a permanent delete.
			$done[] = 'trash:' . $pid;
		}
	}
	bst_aid_task_set( $task, '_bst_aid_snapshots', array() );
	bst_aid_task_set( $task, '_bst_aid_created', array() );
	return $done;
}

/** Before editing a post, keep a revision id so undo can restore it. */
function bst_aid_snapshot_post( $post_id ) {
	$rev = wp_save_post_revision( $post_id );
	if ( ! $rev ) {
		$revs = wp_get_post_revisions( $post_id, array( 'numberposts' => 1 ) );
		$rev  = $revs ? (int) key( $revs ) : 0;
	}
	if ( $rev ) {
		bst_aid_snapshot( 'post', (string) $post_id, (int) $rev );
	}
}

/* ------------------------------------------------------------------ Settings */

/** Sections the AI must not touch (system, scripts, maintenance, speed, tweaks). */
function bst_aid_blocked_setting( $field ) {
	$id   = (string) ( $field['id'] ?? '' );
	$type = (string) ( $field['type'] ?? '' );
	if ( in_array( (string) ( $field['section'] ?? '' ), array( 'bst_tweaks', 'bst_site_tools', 'bst_performance', 'bst_block_manager' ), true ) ) {
		return true;
	}
	if ( preg_match( '/^(perf_|scripts_|tweak_|maintenance_|custom_404|block_off_)/', $id ) ) {
		return true;
	}
	return ! in_array( $type, array( 'color', 'number', 'select', 'radio', 'checkbox', 'text', 'url', 'image', 'html', 'dropdown-pages' ), true );
}

/** Design settings the AI may read and change: id => field. */
function bst_aid_design_fields() {
	static $fields = null;
	if ( null === $fields ) {
		$fields = array();
		foreach ( (array) bst_customizer_fields() as $f ) {
			if ( ! empty( $f['id'] ) && ! bst_aid_blocked_setting( $f ) ) {
				$fields[ $f['id'] ] = $f;
			}
		}
	}
	return $fields;
}

function bst_aid_sanitize_setting( $field, $value ) {
	switch ( $field['type'] ) {
		case 'color':
			$v = sanitize_hex_color( (string) $value );
			return $v ? $v : new WP_Error( 'color', 'Use a HEX color like #1a73e8.' );
		case 'number':
			if ( ! is_numeric( $value ) ) {
				return new WP_Error( 'number', 'Not a number.' );
			}
			$v = $value + 0;
			if ( isset( $field['min'] ) ) {
				$v = max( $field['min'], $v );
			}
			if ( isset( $field['max'] ) ) {
				$v = min( $field['max'], $v );
			}
			return $v;
		case 'select':
		case 'radio':
			return array_key_exists( (string) $value, (array) ( $field['choices'] ?? array() ) ) ? (string) $value : new WP_Error( 'choice', 'Not one of the choices.' );
		case 'checkbox':
			return is_string( $value ) ? in_array( strtolower( $value ), array( '1', 'true', 'yes', 'on' ), true ) : (bool) $value;
		case 'url':
		case 'image':
			return esc_url_raw( (string) $value );
		case 'html':
			return wp_kses_post( (string) $value );
		case 'dropdown-pages':
			return absint( $value );
	}
	return sanitize_text_field( (string) $value );
}

/* ------------------------------------------------------------------ Menus */

function bst_aid_menu_state( $menu_id ) {
	$items = array();
	foreach ( (array) wp_get_nav_menu_items( $menu_id ) as $it ) {
		$items[] = array( 'id' => $it->ID, 'parent' => (int) $it->menu_item_parent, 'title' => $it->title, 'type' => $it->type, 'object' => $it->object, 'object_id' => (int) $it->object_id, 'url' => $it->url, 'order' => (int) $it->menu_order );
	}
	return array( 'menu_id' => (int) $menu_id, 'items' => $items, 'locations' => get_nav_menu_locations() );
}

function bst_aid_restore_menu( $state ) {
	$menu_id = (int) ( $state['menu_id'] ?? 0 );
	if ( ! $menu_id || ! wp_get_nav_menu_object( $menu_id ) ) {
		return;
	}
	foreach ( (array) wp_get_nav_menu_items( $menu_id ) as $it ) {
		wp_delete_post( $it->ID, true ); // Menu item posts are rebuilt from the snapshot below.
	}
	$map = array();
	foreach ( (array) $state['items'] as $it ) {
		$args = array( 'menu-item-title' => $it['title'], 'menu-item-status' => 'publish', 'menu-item-position' => $it['order'], 'menu-item-parent-id' => $map[ $it['parent'] ] ?? 0 );
		if ( 'custom' === $it['type'] ) {
			$args += array( 'menu-item-type' => 'custom', 'menu-item-url' => $it['url'] );
		} else {
			$args += array( 'menu-item-type' => $it['type'], 'menu-item-object' => $it['object'], 'menu-item-object-id' => $it['object_id'] );
		}
		$map[ $it['id'] ] = wp_update_nav_menu_item( $menu_id, 0, $args );
	}
	if ( isset( $state['locations'] ) ) {
		set_theme_mod( 'nav_menu_locations', $state['locations'] );
	}
}

/** Add menu items recursively. */
function bst_aid_add_menu_items( $menu_id, $items, $parent = 0 ) {
	$pos = 1;
	foreach ( (array) $items as $it ) {
		$args = array( 'menu-item-title' => sanitize_text_field( (string) ( $it['title'] ?? '' ) ), 'menu-item-status' => 'publish', 'menu-item-position' => $pos++, 'menu-item-parent-id' => $parent );
		if ( ! empty( $it['page_id'] ) && get_post( (int) $it['page_id'] ) ) {
			$p     = get_post( (int) $it['page_id'] );
			$args += array( 'menu-item-type' => 'post_type', 'menu-item-object' => $p->post_type, 'menu-item-object-id' => $p->ID );
			if ( '' === $args['menu-item-title'] ) {
				$args['menu-item-title'] = get_the_title( $p );
			}
		} else {
			$args += array( 'menu-item-type' => 'custom', 'menu-item-url' => esc_url_raw( (string) ( $it['url'] ?? '#' ) ) );
		}
		$id = wp_update_nav_menu_item( $menu_id, 0, $args );
		if ( ! is_wp_error( $id ) && ! empty( $it['children'] ) ) {
			bst_aid_add_menu_items( $menu_id, $it['children'], $id );
		}
	}
}

/* ------------------------------------------------------------------ Registration */

add_filter(
	'bst_aid_tools',
	function ( $tools ) {
		$tree = array(
			'type'        => 'array',
			'description' => 'Block tree: [{ "name": "bst/section", "attrs": {…}, "inner": [ … ] }]. Use block_schema for attribute names. Containers: section, row (only columns), column, banner, slider, icon-box, tabs (only tab), tab, accordion (only accordion-item), accordion-item, buttons (only button), popup. Text attributes accept simple HTML (strong, em, a, br, span with style). Core blocks from get_page / get_pattern have a "raw" field: keep it unchanged to keep the block, or replace it with { "name": "core/paragraph", "html": "…" } / { "name": "core/heading", "attrs": { "level": 2 }, "html": "…" } to change the text.',
			'items'       => array( 'type' => 'object' ),
		);
		$tools['list_blocks']   = array(
			'description' => 'List the BIGSPY blocks (name, title, description, container or not) and the structure rules. Call before designing.',
			'parameters'  => array( 'type' => 'object', 'properties' => new stdClass() ),
			'cap'         => 'edit_pages',
			'callback'    => 'bst_aid_tool_list_blocks',
		);
		$tools['block_schema']  = array(
			'description' => 'Attributes of the given blocks: type, default, allowed values. Ask only for the blocks you will use.',
			'parameters'  => array( 'type' => 'object', 'properties' => array( 'names' => array( 'type' => 'array', 'items' => array( 'type' => 'string' ) ) ), 'required' => array( 'names' ) ),
			'cap'         => 'edit_pages',
			'callback'    => 'bst_aid_tool_block_schema',
		);
		$tools['list_patterns'] = array(
			'description' => 'List the ready-made BIGSPY section and page patterns (slug, title, description).',
			'parameters'  => array( 'type' => 'object', 'properties' => new stdClass() ),
			'cap'         => 'edit_pages',
			'callback'    => 'bst_aid_tool_list_patterns',
		);
		$tools['get_pattern']   = array(
			'description' => 'A pattern as a block tree. Good starting point: adapt its texts, colors and images instead of building every section from zero.',
			'parameters'  => array( 'type' => 'object', 'properties' => array( 'slug' => array( 'type' => 'string' ) ), 'required' => array( 'slug' ) ),
			'cap'         => 'edit_pages',
			'callback'    => 'bst_aid_tool_get_pattern',
		);
		$tools['create_page']   = array(
			'description' => 'Create a page from a block tree. Draft unless the user clearly asked to publish. Each page has exactly one H1 (a bst/heading with tag "h1").',
			'parameters'  => array(
				'type'       => 'object',
				'properties' => array(
					'title'    => array( 'type' => 'string' ),
					'slug'     => array( 'type' => 'string' ),
					'blocks'   => $tree,
					'template' => array( 'type' => 'string', 'enum' => array( 'default', 'full-width', 'blank' ), 'description' => 'full-width (recommended for designed pages): no title, no container.' ),
					'status'   => array( 'type' => 'string', 'enum' => array( 'draft', 'publish' ) ),
					'seo'      => array( 'type' => 'object', 'properties' => array( 'focus_keyword' => array( 'type' => 'string' ), 'title' => array( 'type' => 'string' ), 'description' => array( 'type' => 'string' ) ) ),
				),
				'required'   => array( 'title', 'blocks' ),
			),
			'cap'         => 'edit_pages',
			'callback'    => 'bst_aid_tool_create_page',
		);
		$tools['get_page']      = array(
			'description' => 'Read a page as a block tree (non-default attributes only), to edit it.',
			'parameters'  => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'integer' ) ), 'required' => array( 'id' ) ),
			'cap'         => 'edit_pages',
			'callback'    => 'bst_aid_tool_get_page',
		);
		$tools['update_page']   = array(
			'description' => 'Replace a page\'s blocks and/or title, status, template. A revision is kept. For a published page, ask first and pass confirmed: true.',
			'parameters'  => array(
				'type'       => 'object',
				'properties' => array(
					'id'        => array( 'type' => 'integer' ),
					'title'     => array( 'type' => 'string' ),
					'blocks'    => $tree,
					'template'  => array( 'type' => 'string', 'enum' => array( 'default', 'full-width', 'blank' ) ),
					'status'    => array( 'type' => 'string', 'enum' => array( 'draft', 'publish' ) ),
					'confirmed' => array( 'type' => 'boolean' ),
				),
				'required'   => array( 'id' ),
			),
			'cap'         => 'edit_pages',
			'callback'    => 'bst_aid_tool_update_page',
		);
		$tools['page_sections'] = array(
			'description' => 'Edit a page section by section (top-level blocks) without resending the whole page: append, insert at index, replace the block at index, or remove it. Use get_page to see the indexes. Best way to build long pages: create_page with the first sections, then append the rest one or two at a time.',
			'parameters'  => array(
				'type'       => 'object',
				'properties' => array(
					'id'        => array( 'type' => 'integer' ),
					'mode'      => array( 'type' => 'string', 'enum' => array( 'append', 'insert', 'replace', 'remove' ) ),
					'index'     => array( 'type' => 'integer', 'minimum' => 0, 'description' => 'Top-level block index (0 = first) for insert, replace, remove.' ),
					'blocks'    => $tree,
					'confirmed' => array( 'type' => 'boolean' ),
				),
				'required'   => array( 'id', 'mode' ),
			),
			'cap'         => 'edit_pages',
			'callback'    => 'bst_aid_tool_page_sections',
		);
		$tools['get_settings']  = array(
			'description' => 'Theme design settings. Without section: the list of sections and the main colors / fonts. With section: every field (id, label, type, choices, min/max, current value).',
			'parameters'  => array( 'type' => 'object', 'properties' => array( 'section' => array( 'type' => 'string' ) ) ),
			'cap'         => 'edit_theme_options',
			'callback'    => 'bst_aid_tool_get_settings',
		);
		$tools['update_settings'] = array(
			'description' => 'Change theme design settings: { "values": { "color_primary": "#0a4d8c", "font_heading": "Inter" } }. Agree on colors, fonts and other big changes with the user first. Changes can be undone with the task.',
			'parameters'  => array( 'type' => 'object', 'properties' => array( 'values' => array( 'type' => 'object' ) ), 'required' => array( 'values' ) ),
			'cap'         => 'edit_theme_options',
			'callback'    => 'bst_aid_tool_update_settings',
		);
		$tools['set_logo']      = array(
			'description' => 'Set the site logo and/or favicon from a Media Library image id (for example one ImageGen made).',
			'parameters'  => array( 'type' => 'object', 'properties' => array( 'logo_id' => array( 'type' => 'integer' ), 'favicon_id' => array( 'type' => 'integer' ) ) ),
			'cap'         => 'edit_theme_options',
			'callback'    => 'bst_aid_tool_set_logo',
		);
		$tools['custom_css']    = array(
			'description' => 'Read or write the AI Designer part of Additional CSS. mode "read" returns it; "replace" sets the AI part to css. CSS the user wrote outside that part is never changed. Prefer block and theme settings; use CSS only for what they cannot do.',
			'parameters'  => array( 'type' => 'object', 'properties' => array( 'mode' => array( 'type' => 'string', 'enum' => array( 'read', 'replace' ) ), 'css' => array( 'type' => 'string' ) ), 'required' => array( 'mode' ) ),
			'cap'         => 'edit_css',
			'callback'    => 'bst_aid_tool_custom_css',
		);
		$tools['set_menu']      = array(
			'description' => 'Create a menu or replace a menu\'s items, and optionally assign it to a location. Replacing a menu that already has items needs the user\'s OK: pass confirmed: true after asking. Items: [{ "title": "…", "page_id": 12 } or { "title": "…", "url": "https://…" }, children: [ … ]].',
			'parameters'  => array(
				'type'       => 'object',
				'properties' => array(
					'name'      => array( 'type' => 'string' ),
					'items'     => array( 'type' => 'array', 'items' => array( 'type' => 'object' ) ),
					'location'  => array( 'type' => 'string', 'description' => 'One of the locations from get_menus.' ),
					'confirmed' => array( 'type' => 'boolean' ),
				),
				'required'   => array( 'name', 'items' ),
			),
			'cap'         => 'edit_theme_options',
			'callback'    => 'bst_aid_tool_set_menu',
		);
		$tools['get_menus']     = array(
			'description' => 'Menus with their items, and the menu locations of the theme.',
			'parameters'  => array( 'type' => 'object', 'properties' => new stdClass() ),
			'cap'         => 'edit_theme_options',
			'callback'    => 'bst_aid_tool_get_menus',
		);
		$tools['header_layout'] = array(
			'description' => 'Read or set the header builder layout. Layout: { "desktop": { "top|main|bottom": { "left|center|right": [element keys] } }, "mobile": { … } }. mode "read" also lists the element keys.',
			'parameters'  => array( 'type' => 'object', 'properties' => array( 'mode' => array( 'type' => 'string', 'enum' => array( 'read', 'set' ) ), 'layout' => array( 'type' => 'object' ) ), 'required' => array( 'mode' ) ),
			'cap'         => 'edit_theme_options',
			'callback'    => 'bst_aid_tool_header_layout',
		);
		$tools['show_palette']  = array(
			'description' => 'Show the user 1–3 color palettes (and fonts) as swatches in the chat so they can choose. Changes nothing on the site.',
			'parameters'  => array(
				'type'       => 'object',
				'properties' => array(
					'palettes' => array(
						'type'  => 'array',
						'items' => array(
							'type'       => 'object',
							'properties' => array(
								'name'   => array( 'type' => 'string' ),
								'colors' => array( 'type' => 'object', 'description' => 'role => HEX, e.g. primary, secondary, heading, text, light, background.' ),
								'fonts'  => array( 'type' => 'object', 'description' => 'heading, body' ),
								'note'   => array( 'type' => 'string' ),
							),
						),
					),
				),
				'required'   => array( 'palettes' ),
			),
			'cap'         => 'edit_pages',
			'callback'    => 'bst_aid_tool_show_palette',
		);
		return $tools;
	}
);

/* ------------------------------------------------------------------ Callbacks */

function bst_aid_tool_list_blocks() {
	$containers = bst_aid_block_containers();
	$parents    = bst_aid_block_parents();
	$list       = array();
	foreach ( bst_aid_block_types() as $name => $t ) {
		$item = array( 'name' => $name, 'title' => $t->title, 'description' => $t->description );
		if ( isset( $containers[ $name ] ) ) {
			$item['container'] = true;
			if ( $containers[ $name ] ) {
				$item['children'] = $containers[ $name ];
			}
		}
		if ( isset( $parents[ $name ] ) ) {
			$item['parent'] = $parents[ $name ];
		}
		$list[] = $item;
	}
	return array(
		'blocks' => $list,
		'rules'  => array(
			'A page is a list of bst/section blocks. Inside a section use bst/row > bst/column for columns (column width via its attributes), or put blocks directly.',
			'Use bst/heading for headings (attribute tag h1–h6; one h1 per page) and bst/text for paragraphs (html attribute).',
			'Images: set the image id and URL attributes (from ImageGen or the Media Library). Never invent image URLs.',
			'bst/code holds custom HTML / CSS / JS for things blocks cannot do (illustrations, mockups, embeds).',
			'Start from get_pattern when a pattern fits, then change texts, colors and images.',
		),
	);
}

function bst_aid_tool_block_schema( $a ) {
	$types = bst_aid_block_types();
	$out   = array();
	foreach ( array_slice( (array) ( $a['names'] ?? array() ), 0, 12 ) as $name ) {
		$name = false === strpos( (string) $name, '/' ) ? 'bst/' . $name : (string) $name;
		if ( ! isset( $types[ $name ] ) ) {
			$out[ $name ] = 'unknown block';
			continue;
		}
		$attrs = array();
		foreach ( (array) $types[ $name ]->attributes as $key => $s ) {
			if ( in_array( $key, array( 'lock', 'metadata' ), true ) ) {
				continue;
			}
			$row = array( 'type' => $s['type'] ?? 'string' );
			if ( array_key_exists( 'default', $s ) && '' !== $s['default'] && array() !== $s['default'] ) {
				$row['default'] = $s['default'];
			}
			if ( isset( $s['enum'] ) ) {
				$row['enum'] = $s['enum'];
			}
			$attrs[ $key ] = $row;
		}
		$out[ $name ] = array( 'description' => $types[ $name ]->description, 'attributes' => $attrs );
	}
	return array( 'schemas' => $out );
}

function bst_aid_tool_list_patterns() {
	$out = array();
	foreach ( WP_Block_Patterns_Registry::get_instance()->get_all_registered() as $p ) {
		if ( 0 === strpos( (string) $p['name'], 'bigspy-ai-theme-wp/' ) ) {
			$out[] = array( 'slug' => substr( $p['name'], strlen( 'bigspy-ai-theme-wp/' ) ), 'title' => $p['title'], 'description' => (string) ( $p['description'] ?? '' ) );
		}
	}
	return array( 'patterns' => $out );
}

function bst_aid_tool_get_pattern( $a ) {
	$slug = sanitize_title( (string) ( $a['slug'] ?? '' ) );
	$p    = WP_Block_Patterns_Registry::get_instance()->get_registered( 'bigspy-ai-theme-wp/' . $slug );
	if ( ! $p ) {
		return array( 'error' => 'Unknown pattern. Use list_patterns.' );
	}
	return array( 'slug' => $slug, 'title' => $p['title'], 'blocks' => bst_aid_compact_blocks( parse_blocks( $p['content'] ) ) );
}

function bst_aid_page_template( $key ) {
	$map = array( 'full-width' => 'templates/full-width.php', 'blank' => 'templates/blank.php', 'default' => '' );
	return $map[ $key ] ?? null;
}

function bst_aid_tool_create_page( $a ) {
	$markup = bst_aid_tree_to_markup( $a['blocks'] ?? array() );
	if ( is_wp_error( $markup ) ) {
		return array( 'error' => $markup->get_error_message(), 'details' => $markup->get_error_data() );
	}
	$id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_title'   => sanitize_text_field( bst_aid_clean_plain( (string) $a['title'] ) ),
			'post_name'    => sanitize_title( (string) ( $a['slug'] ?? '' ) ),
			'post_content' => wp_slash( $markup['markup'] ),
			'post_status'  => ( 'publish' === ( $a['status'] ?? '' ) && current_user_can( 'publish_pages' ) ) ? 'publish' : 'draft',
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		return array( 'error' => $id->get_error_message() );
	}
	$tpl = bst_aid_page_template( (string) ( $a['template'] ?? 'full-width' ) );
	if ( $tpl ) {
		update_post_meta( $id, '_wp_page_template', $tpl );
	}
	bst_aid_set_seo( $id, $a['seo'] ?? null );
	update_post_meta( $id, '_bst_aid_created', 1 );
	bst_aid_track_created( $id );
	return array( 'id' => $id, 'status' => get_post_status( $id ), 'warnings' => $markup['warnings'], 'links' => bst_aid_links( $id, get_the_title( $id ) ), 'preview' => get_preview_post_link( $id ) );
}

function bst_aid_tool_get_page( $a ) {
	$p = get_post( (int) ( $a['id'] ?? 0 ) );
	if ( ! $p || 'page' !== $p->post_type || ! current_user_can( 'edit_post', $p->ID ) ) {
		return array( 'error' => 'Page not found or not editable.' );
	}
	$tpl = (string) get_post_meta( $p->ID, '_wp_page_template', true );
	return array(
		'id'       => $p->ID,
		'title'    => $p->post_title,
		'status'   => $p->post_status,
		'template' => false !== strpos( $tpl, 'full-width' ) ? 'full-width' : ( false !== strpos( $tpl, 'blank' ) ? 'blank' : 'default' ),
		'link'     => get_permalink( $p ),
		'blocks'   => bst_aid_compact_blocks( parse_blocks( $p->post_content ) ),
	);
}

function bst_aid_tool_update_page( $a ) {
	$p = get_post( (int) ( $a['id'] ?? 0 ) );
	if ( ! $p || 'page' !== $p->post_type || ! current_user_can( 'edit_post', $p->ID ) ) {
		return array( 'error' => 'Page not found or not editable.' );
	}
	if ( ! function_exists( 'wp_check_post_lock' ) ) {
		require_once ABSPATH . 'wp-admin/includes/post.php';
	}
	if ( wp_check_post_lock( $p->ID ) ) {
		return array( 'error' => 'Someone is editing this page right now. Try again when they are done.' );
	}
	if ( 'publish' === $p->post_status && empty( $a['confirmed'] ) ) {
		return array( 'error' => 'needs_confirmation', 'message' => 'This page is published. Ask the user to confirm, then call again with confirmed: true.' );
	}
	$data     = array( 'ID' => $p->ID );
	$warnings = array();
	if ( isset( $a['blocks'] ) ) {
		$markup = bst_aid_tree_to_markup( $a['blocks'] );
		if ( is_wp_error( $markup ) ) {
			return array( 'error' => $markup->get_error_message(), 'details' => $markup->get_error_data() );
		}
		$data['post_content'] = wp_slash( $markup['markup'] );
		$warnings             = $markup['warnings'];
	}
	if ( isset( $a['title'] ) ) {
		$data['post_title'] = sanitize_text_field( bst_aid_clean_plain( (string) $a['title'] ) );
	}
	if ( isset( $a['status'] ) ) {
		$data['post_status'] = ( 'publish' === $a['status'] && current_user_can( 'publish_pages' ) ) ? 'publish' : 'draft';
	}
	bst_aid_snapshot_post( $p->ID );
	$r = wp_update_post( $data, true );
	if ( is_wp_error( $r ) ) {
		return array( 'error' => $r->get_error_message() );
	}
	if ( isset( $a['template'] ) ) {
		$tpl = bst_aid_page_template( (string) $a['template'] );
		if ( null !== $tpl ) {
			update_post_meta( $p->ID, '_wp_page_template', $tpl );
		}
	}
	return array( 'id' => $p->ID, 'status' => get_post_status( $p->ID ), 'warnings' => $warnings, 'links' => bst_aid_links( $p->ID, get_the_title( $p->ID ) ), 'preview' => get_preview_post_link( $p->ID ) );
}

function bst_aid_tool_page_sections( $a ) {
	$p = get_post( (int) ( $a['id'] ?? 0 ) );
	if ( ! $p || 'page' !== $p->post_type || ! current_user_can( 'edit_post', $p->ID ) ) {
		return array( 'error' => 'Page not found or not editable.' );
	}
	if ( 'publish' === $p->post_status && empty( $a['confirmed'] ) ) {
		return array( 'error' => 'needs_confirmation', 'message' => 'This page is published. Ask the user to confirm, then call again with confirmed: true.' );
	}
	// Top-level blocks only; whitespace between blocks is dropped.
	$top  = array_values( array_filter( parse_blocks( $p->post_content ), function ( $b ) { return ! empty( $b['blockName'] ) || '' !== trim( (string) $b['innerHTML'] ); } ) );
	$mode = (string) ( $a['mode'] ?? 'append' );
	$idx  = isset( $a['index'] ) ? (int) $a['index'] : count( $top );
	$new  = array();
	$warn = array();
	if ( 'remove' !== $mode ) {
		$issues = array( 'errors' => array(), 'warnings' => array() );
		$new    = bst_aid_build_blocks( $a['blocks'] ?? array(), '', $issues );
		if ( $issues['errors'] || ! $new ) {
			return array( 'error' => 'Fix these block errors and call again', 'details' => $issues );
		}
		$warn = array_slice( $issues['warnings'], 0, 30 );
	}
	if ( in_array( $mode, array( 'replace', 'remove' ), true ) && ! isset( $top[ $idx ] ) ) {
		return array( 'error' => 'No block at index ' . $idx . '. The page has ' . count( $top ) . ' top-level blocks.' );
	}
	switch ( $mode ) {
		case 'insert':
			array_splice( $top, max( 0, min( $idx, count( $top ) ) ), 0, $new );
			break;
		case 'replace':
			array_splice( $top, $idx, 1, $new );
			break;
		case 'remove':
			array_splice( $top, $idx, 1 );
			break;
		default:
			$top = array_merge( $top, $new );
	}
	bst_aid_snapshot_post( $p->ID );
	$r = wp_update_post( array( 'ID' => $p->ID, 'post_content' => wp_slash( serialize_blocks( $top ) ) ), true );
	if ( is_wp_error( $r ) ) {
		return array( 'error' => $r->get_error_message() );
	}
	return array( 'id' => $p->ID, 'sections' => count( $top ), 'warnings' => $warn, 'links' => bst_aid_links( $p->ID, get_the_title( $p->ID ) ), 'preview' => get_preview_post_link( $p->ID ) );
}

function bst_aid_tool_get_settings( $a ) {
	$fields   = bst_aid_design_fields();
	$sections = bst_customizer_sections();
	$want     = (string) ( $a['section'] ?? '' );
	if ( '' === $want ) {
		$count = array();
		foreach ( $fields as $f ) {
			$count[ $f['section'] ] = ( $count[ $f['section'] ] ?? 0 ) + 1;
		}
		$list = array();
		foreach ( $count as $sid => $n ) {
			$list[] = array( 'section' => $sid, 'title' => (string) ( $sections[ $sid ]['title'] ?? $sid ), 'fields' => $n );
		}
		$main = array();
		foreach ( array( 'color_primary', 'color_secondary', 'color_text', 'color_heading', 'color_link', 'color_light', 'color_body_bg', 'font_body', 'font_heading', 'font_size_base' ) as $k ) {
			$main[ $k ] = bst_mod( $k );
		}
		// The fields a redesign needs, ready to use with update_settings (no extra calls).
		$design = array();
		foreach ( $fields as $fid => $f ) {
			if ( preg_match( '/^(color_|font_|button_radius|radius|container_width|heading_)/', $fid ) ) {
				$row = array( 'type' => $f['type'], 'value' => bst_mod( $fid ) );
				if ( ! empty( $f['choices'] ) && 'font_' === substr( $fid, 0, 5 ) && count( $f['choices'] ) > 12 ) {
					$row['choices'] = 'see fonts';
				} elseif ( ! empty( $f['choices'] ) ) {
					$row['choices'] = array_keys( $f['choices'] );
				}
				foreach ( array( 'min', 'max' ) as $k ) {
					if ( isset( $f[ $k ] ) ) {
						$row[ $k ] = $f[ $k ];
					}
				}
				$design[ $fid ] = $row;
			}
		}
		$fonts = function_exists( 'bst_font_choices' ) ? (array) bst_font_choices() : array();
		return array( 'design_fields' => $design, 'fonts' => $fonts, 'sections' => $list, 'note' => 'Font fields take a key from "fonts" (e.g. the key, not the label). Use update_settings directly with these ids.' );
	}
	$out = array();
	foreach ( $fields as $id => $f ) {
		if ( $f['section'] !== $want ) {
			continue;
		}
		$row = array( 'id' => $id, 'label' => wp_strip_all_tags( (string) ( $f['label'] ?? '' ) ), 'type' => $f['type'], 'value' => bst_mod( $id ) );
		foreach ( array( 'choices', 'min', 'max' ) as $k ) {
			if ( isset( $f[ $k ] ) ) {
				$row[ $k ] = $f[ $k ];
			}
		}
		$out[] = $row;
	}
	return $out ? array( 'section' => $want, 'fields' => $out ) : array( 'error' => 'Unknown section. Call get_settings without a section.' );
}

function bst_aid_tool_update_settings( $a ) {
	$fields  = bst_aid_design_fields();
	$changed = array();
	$errors  = array();
	foreach ( (array) ( $a['values'] ?? array() ) as $id => $value ) {
		if ( ! isset( $fields[ $id ] ) ) {
			$errors[ $id ] = 'Unknown or not allowed setting.';
			continue;
		}
		$v = bst_aid_sanitize_setting( $fields[ $id ], $value );
		if ( is_wp_error( $v ) ) {
			$errors[ $id ] = $v->get_error_message();
			continue;
		}
		$old = get_theme_mod( $id, null );
		bst_aid_snapshot( 'mod', $id, $old );
		set_theme_mod( $id, $v );
		$changed[ $id ] = $v;
	}
	if ( $changed && function_exists( 'bst_perf_clear_css' ) ) {
		bst_perf_clear_css();
	}
	return array( 'changed' => $changed, 'errors' => $errors, 'links' => array( array( 'label' => __( 'Site', 'bigspy-ai-theme-wp' ), 'edit' => admin_url( 'customize.php' ), 'view' => home_url( '/' ) ) ) );
}

function bst_aid_tool_set_logo( $a ) {
	$out = array();
	foreach ( array( 'logo_id' => 'custom_logo', 'favicon_id' => 'site_icon' ) as $param => $key ) {
		if ( empty( $a[ $param ] ) ) {
			continue;
		}
		$id = (int) $a[ $param ];
		if ( ! wp_attachment_is_image( $id ) ) {
			$out[ $param ] = 'Not an image in the Media Library.';
			continue;
		}
		if ( 'custom_logo' === $key ) {
			bst_aid_snapshot( 'mod', 'custom_logo', get_theme_mod( 'custom_logo', null ) );
			set_theme_mod( 'custom_logo', $id );
		} else {
			bst_aid_snapshot( 'option', 'site_icon', get_option( 'site_icon' ) );
			update_option( 'site_icon', $id );
		}
		$out[ $param ] = 'set';
	}
	return array( 'result' => $out, 'links' => array( array( 'label' => __( 'Site', 'bigspy-ai-theme-wp' ), 'edit' => admin_url( 'customize.php?autofocus[section]=title_tagline' ), 'view' => home_url( '/' ) ) ) );
}

function bst_aid_css_markers() {
	return array( '/* BIGSPY AI Designer – start (edited by the AI Designer) */', '/* BIGSPY AI Designer – end */' );
}

function bst_aid_tool_custom_css( $a ) {
	list( $start, $end ) = bst_aid_css_markers();
	$all = (string) wp_get_custom_css();
	$re  = '#' . preg_quote( $start, '#' ) . '(.*?)' . preg_quote( $end, '#' ) . '#s';
	$cur = preg_match( $re, $all, $m ) ? trim( $m[1] ) : '';
	if ( 'replace' !== ( $a['mode'] ?? 'read' ) ) {
		return array( 'ai_css' => $cur, 'user_css_length' => strlen( trim( preg_replace( $re, '', $all ) ) ) );
	}
	$css = trim( wp_strip_all_tags( (string) ( $a['css'] ?? '' ) ) );
	if ( preg_match( '#</?style|@import#i', $css ) ) {
		return array( 'error' => 'Plain CSS only (no <style> tags or @import).' );
	}
	bst_aid_snapshot( 'css', 'custom_css', $all );
	$block = '' === $css ? '' : $start . "\n" . $css . "\n" . $end;
	$new   = preg_match( $re, $all ) ? preg_replace( $re, str_replace( array( '\\', '$' ), array( '\\\\', '\\$' ), $block ), $all ) : rtrim( $all ) . ( '' === $block ? '' : "\n\n" . $block );
	$r     = wp_update_custom_css_post( trim( $new ) );
	if ( is_wp_error( $r ) ) {
		return array( 'error' => $r->get_error_message() );
	}
	return array( 'saved' => true, 'length' => strlen( $css ), 'links' => array( array( 'label' => __( 'Additional CSS', 'bigspy-ai-theme-wp' ), 'edit' => admin_url( 'customize.php?autofocus[section]=custom_css' ), 'view' => home_url( '/' ) ) ) );
}

function bst_aid_tool_get_menus() {
	$menus = array();
	foreach ( wp_get_nav_menus() as $m ) {
		$items = array();
		foreach ( (array) wp_get_nav_menu_items( $m->term_id ) as $it ) {
			$items[] = array( 'title' => $it->title, 'url' => $it->url, 'parent' => (int) $it->menu_item_parent, 'id' => $it->ID, 'page_id' => 'page' === $it->object ? (int) $it->object_id : 0 );
		}
		$menus[] = array( 'id' => $m->term_id, 'name' => $m->name, 'items' => $items );
	}
	$locations = array();
	$assigned  = get_nav_menu_locations();
	foreach ( get_registered_nav_menus() as $loc => $label ) {
		$locations[] = array( 'location' => $loc, 'label' => $label, 'menu_id' => (int) ( $assigned[ $loc ] ?? 0 ) );
	}
	return array( 'menus' => $menus, 'locations' => $locations );
}

function bst_aid_tool_set_menu( $a ) {
	$name = sanitize_text_field( (string) ( $a['name'] ?? '' ) );
	if ( '' === $name ) {
		return array( 'error' => 'A menu name is needed.' );
	}
	$menu = wp_get_nav_menu_object( $name );
	if ( $menu ) {
		$items = (array) wp_get_nav_menu_items( $menu->term_id );
		if ( $items && empty( $a['confirmed'] ) ) {
			return array( 'error' => 'needs_confirmation', 'message' => 'This menu already has ' . count( $items ) . ' items that will be replaced. Ask the user, then call again with confirmed: true.' );
		}
		bst_aid_snapshot( 'menu', (string) $menu->term_id, bst_aid_menu_state( $menu->term_id ) );
		foreach ( $items as $it ) {
			wp_delete_post( $it->ID, true ); // Menu item rows only; the snapshot can rebuild them.
		}
		$menu_id = $menu->term_id;
	} else {
		$menu_id = wp_create_nav_menu( $name );
		if ( is_wp_error( $menu_id ) ) {
			return array( 'error' => $menu_id->get_error_message() );
		}
		bst_aid_snapshot( 'menu_created', (string) $menu_id, null ); // Undo removes a menu the task made.
	}
	bst_aid_add_menu_items( $menu_id, $a['items'] ?? array() );
	$loc = (string) ( $a['location'] ?? '' );
	if ( '' !== $loc ) {
		if ( ! array_key_exists( $loc, get_registered_nav_menus() ) ) {
			return array( 'menu_id' => $menu_id, 'error' => 'Unknown location; the menu was saved but not assigned.' );
		}
		$locs = get_nav_menu_locations();
		bst_aid_snapshot( 'mod', 'nav_menu_locations', get_theme_mod( 'nav_menu_locations', null ) );
		$locs[ $loc ] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locs );
	}
	return array( 'menu_id' => $menu_id, 'links' => array( array( 'label' => $name, 'edit' => admin_url( 'nav-menus.php?menu=' . $menu_id ), 'view' => home_url( '/' ) ) ) );
}

function bst_aid_tool_header_layout( $a ) {
	if ( 'set' !== ( $a['mode'] ?? 'read' ) ) {
		return array( 'layout' => bst_get_header_layout(), 'elements' => bst_header_elements() );
	}
	$layout = (array) ( $a['layout'] ?? array() );
	$known  = array_keys( bst_header_elements() );
	foreach ( array( 'desktop', 'mobile' ) as $dev ) {
		foreach ( (array) ( $layout[ $dev ] ?? array() ) as $row => $zones ) {
			foreach ( (array) $zones as $zone => $keys ) {
				$layout[ $dev ][ $row ][ $zone ] = array_values( array_intersect( (array) $keys, $known ) );
			}
		}
	}
	$clean = function_exists( 'bst_sanitize_header_layout' ) ? bst_sanitize_header_layout( wp_json_encode( $layout ) ) : wp_json_encode( $layout );
	bst_aid_snapshot( 'mod', 'header_layout', get_theme_mod( 'header_layout', null ) );
	set_theme_mod( 'header_layout', $clean );
	return array( 'saved' => true, 'layout' => bst_get_header_layout(), 'links' => array( array( 'label' => __( 'Header builder', 'bigspy-ai-theme-wp' ), 'edit' => admin_url( 'customize.php?autofocus[section]=bst_header_builder' ), 'view' => home_url( '/' ) ) ) );
}

function bst_aid_tool_show_palette( $a ) {
	$out = array();
	foreach ( array_slice( (array) ( $a['palettes'] ?? array() ), 0, 3 ) as $p ) {
		$colors = array();
		foreach ( (array) ( $p['colors'] ?? array() ) as $role => $hex ) {
			$hex = sanitize_hex_color( (string) $hex );
			if ( $hex ) {
				$colors[ sanitize_key( $role ) ] = $hex;
			}
		}
		$out[] = array( 'name' => sanitize_text_field( (string) ( $p['name'] ?? '' ) ), 'colors' => $colors, 'fonts' => array_map( 'sanitize_text_field', (array) ( $p['fonts'] ?? array() ) ), 'note' => sanitize_text_field( (string) ( $p['note'] ?? '' ) ) );
	}
	return array( 'shown' => count( $out ), 'palettes' => $out, 'note' => 'The user sees these swatches. Wait for their choice before changing settings.' );
}
