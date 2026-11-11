<?php
/**
 * Attribute presets (G-131): ready looks shown as pictures at the top of the Styles tab.
 * Clicking one sets block attributes (undoable); users can save the current look as a site preset.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/* ------------------------------------------------------------------ Attribute presets (G-131) */

/**
 * Built-in looks that set attributes: block => id => array( label, svg (56x20 inner markup), attrs ).
 * Applying one calls setAttributes( attrs ) in the editor, so Ctrl+Z undoes it.
 */
function bst_block_attr_presets() {
	$line = function ( $d, $w = 2, $extra = '' ) {
		return '<path d="' . $d . '" fill="none" stroke="currentColor" stroke-width="' . $w . '" stroke-linecap="round"' . $extra . '/>';
	};
	$btn = function ( $fill, $r ) {
		$bar = false !== strpos( $fill, 'none' ) || false !== strpos( $fill, 'opacity' ) ? 'currentColor' : '#fff';
		return '<rect x="6" y="3" width="44" height="14" rx="' . $r . '" ' . $fill . '/><rect x="18" y="9" width="20" height="2" rx="1" fill="' . $bar . '"/>';
	};
	$div = function ( $style, $thick, $width = 100, $unit = '', $gradient = false ) {
		return array( 'style' => $style, 'thickness' => $thick, 'width' => $width, 'widthUnit' => $unit, 'align' => 'center', 'gradient' => $gradient );
	};
	$b = function ( $style, $size, $radius, $effect, $case = '', $shadow = '' ) {
		return array( 'style' => $style, 'size' => $size, 'radius' => $radius, 'hoverEffect' => $effect, 'letterCase' => $case, 'btnShadow' => $shadow );
	};
	$presets = array(
		'bst/divider' => array(
			'hairline' => array( __( 'Hairline', 'bigspy-ai-theme-wp' ), $line( 'M4 10H52', 1 ), $div( 'solid', 1 ) ),
			'accent'   => array( __( 'Short accent', 'bigspy-ai-theme-wp' ), $line( 'M20 10H36', 4 ), $div( 'solid', 4, 64, 'px', true ) ),
			'fade'     => array( __( 'Fade', 'bigspy-ai-theme-wp' ), '<defs><linearGradient id="bstpf"><stop offset="0" stop-color="currentColor" stop-opacity="0"/><stop offset=".5" stop-color="currentColor"/><stop offset="1" stop-color="currentColor" stop-opacity="0"/></linearGradient></defs><rect x="4" y="9" width="48" height="2" fill="url(#bstpf)"/>', $div( 'fade', 2 ) ),
			'wave'     => array( __( 'Wave', 'bigspy-ai-theme-wp' ), $line( 'M4 10q6-6 12 0t12 0 12 0 12 0', 2 ), $div( 'wave', 2 ) ),
			'dots'     => array( __( 'Round dots', 'bigspy-ai-theme-wp' ), $line( 'M10 10H46', 4, ' stroke-dasharray="0 8"' ), $div( 'dots', 4, 40 ) ),
			'double'   => array( __( 'Thick and thin', 'bigspy-ai-theme-wp' ), $line( 'M4 8H52', 3 ) . $line( 'M4 13H52', 1 ), $div( 'thick-thin', 3 ) ),
		),
		'bst/button'  => array(
			'solid'    => array( __( 'Solid', 'bigspy-ai-theme-wp' ), $btn( 'fill="currentColor"', 3 ), $b( 'primary', 'md', -1, '' ) ),
			'pill'     => array( __( 'Pill outline', 'bigspy-ai-theme-wp' ), $btn( 'fill="none" stroke="currentColor" stroke-width="1.5"', 7 ), $b( 'outline', 'md', 999, 'fill' ) ),
			'gradient' => array( __( 'Gradient lift', 'bigspy-ai-theme-wp' ), '<defs><linearGradient id="bstpg"><stop offset="0" stop-color="#6366f1"/><stop offset="1" stop-color="#ec4899"/></linearGradient></defs>' . $btn( 'fill="url(#bstpg)"', 7 ), $b( 'gradient', 'lg', 999, 'lift', '', 'md' ) ),
			'soft'     => array( __( 'Soft', 'bigspy-ai-theme-wp' ), $btn( 'fill="currentColor" fill-opacity=".18"', 4 ), $b( 'soft', 'md', 8, 'grow' ) ),
			'caps'     => array( __( 'Square caps', 'bigspy-ai-theme-wp' ), $btn( 'fill="currentColor"', 0 ), $b( 'primary', 'lg', 0, 'shine', 'uppercase' ) ),
			'link'     => array( __( 'Text link', 'bigspy-ai-theme-wp' ), '<rect x="10" y="9" width="28" height="2" rx="1" fill="currentColor"/>' . $line( 'M42 10h6m-3-3 3 3-3 3', 1.5 ), $b( 'link', 'md', -1, 'icon-move' ) ),
		),
		'bst/tabs'    => array(
			'segmented-dark' => array( __( 'Dark segmented', 'bigspy-ai-theme-wp' ), '<rect x="2" y="3" width="52" height="14" rx="5" fill="currentColor" fill-opacity=".85"/><rect x="4" y="5" width="17" height="10" rx="4" fill="#fff"/>', array( 'style' => 'segmented', 'align' => 'center', 'navBg' => '#111827', 'tabColor' => '#d1d5db', 'tabBgActive' => '#ffffff', 'activeColor' => '#111827', 'navPosition' => '', 'mobile' => 'scroll' ) ),
			'folder'         => array( __( 'Folder', 'bigspy-ai-theme-wp' ), '<path d="M4 17V7q0-3 3-3h10q3 0 3 3v10M22 17V9q0-2 2-2h8q2 0 2 2v8M2 17H54" fill="none" stroke="currentColor" stroke-width="1.5"/>', array( 'style' => 'folder', 'align' => 'left', 'navPosition' => '', 'panelRadius' => -1, 'mobile' => 'scroll' ) ),
			'soft-pills'     => array( __( 'Soft pills', 'bigspy-ai-theme-wp' ), '<rect x="4" y="5" width="18" height="10" rx="5" fill="currentColor" fill-opacity=".25"/><rect x="26" y="5" width="18" height="10" rx="5" fill="none" stroke="currentColor" stroke-opacity=".3"/>', array( 'style' => 'pills', 'align' => 'center', 'tabBg' => '#f3f4f6', 'navGap' => 8, 'navPosition' => '', 'mobile' => 'scroll' ) ),
			'thick-line'     => array( __( 'Thick underline', 'bigspy-ai-theme-wp' ), '<rect x="6" y="11" width="14" height="4" fill="currentColor"/><path d="M2 14H54" stroke="currentColor" stroke-opacity=".3" stroke-width="2"/>', array( 'style' => 'underline-thick', 'align' => 'justify', 'navWeight' => '700', 'navPosition' => '', 'mobile' => 'scroll' ) ),
			'shadow-cards'   => array( __( 'Shadow cards', 'bigspy-ai-theme-wp' ), '<rect x="3" y="3" width="16" height="14" rx="3" fill="currentColor"/><rect x="22" y="3" width="16" height="14" rx="3" fill="none" stroke="currentColor" stroke-opacity=".4"/>', array( 'style' => 'cards', 'align' => 'center', 'iconPosition' => 'top', 'navPosition' => '', 'mobile' => 'stack' ) ),
			'side-left'      => array( __( 'Vertical left', 'bigspy-ai-theme-wp' ), '<rect x="4" y="3" width="14" height="4" fill="currentColor"/><rect x="4" y="9" width="14" height="3" fill="currentColor" fill-opacity=".3"/><rect x="22" y="3" width="30" height="14" fill="currentColor" fill-opacity=".12"/>', array( 'style' => 'vertical', 'navPosition' => 'left', 'sideWidth' => 260, 'mobile' => 'accordion' ) ),
		),
	);
	return (array) apply_filters( 'bst_block_attr_presets', $presets );
}

/** Attribute names a block declares in its block.json (bst/* blocks only). */
function bst_block_json_attr_names( $block ) {
	static $cache = array();
	if ( ! isset( $cache[ $block ] ) ) {
		$cache[ $block ] = array();
		if ( preg_match( '#^bst/([a-z0-9-]+)$#', (string) $block, $m ) ) {
			$file = BST_DIR . '/blocks/' . $m[1] . '/block.json';
			$json = is_readable( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : null; // phpcs:ignore WordPress.WP.AlternativeFunctions
			if ( is_array( $json ) && ! empty( $json['attributes'] ) ) {
				$cache[ $block ] = array_keys( $json['attributes'] );
			}
		}
	}
	return $cache[ $block ];
}

/** Site presets saved by users: block => id => array( 'label' => , 'attrs' => ). */
function bst_user_block_presets() {
	$all = get_option( 'bst_user_block_presets', array() );
	return is_array( $all ) ? $all : array();
}

/** Keep only block.json attributes whose values match their schema. */
function bst_sanitize_preset_attrs( $block, $attrs ) {
	$allowed = bst_block_json_attr_names( $block );
	$type    = WP_Block_Type_Registry::get_instance()->get_registered( $block );
	$out     = array();
	foreach ( (array) $attrs as $key => $value ) {
		if ( ! in_array( $key, $allowed, true ) || is_object( $value ) ) {
			continue;
		}
		$schema = $type && isset( $type->attributes[ $key ] ) ? $type->attributes[ $key ] : array();
		if ( $schema && true !== rest_validate_value_from_schema( $value, $schema, $key ) ) {
			continue;
		}
		$out[ $key ] = is_string( $value ) ? wp_kses_post( $value ) : $value;
	}
	return $out;
}

/** REST: POST saves (block, label, attrs), DELETE removes (block, id) a site preset. */
function bst_user_presets_rest() {
	$can = function () {
		return current_user_can( 'edit_theme_options' );
	};
	register_rest_route(
		'bst/v1',
		'/block-presets',
		array(
			array( 'methods' => 'POST', 'callback' => 'bst_user_presets_save', 'permission_callback' => $can ),
			array( 'methods' => 'DELETE', 'callback' => 'bst_user_presets_delete', 'permission_callback' => $can ),
		)
	);
}
add_action( 'rest_api_init', 'bst_user_presets_rest' );

/** Save the current look of a block as a site preset (max 50 per block). */
function bst_user_presets_save( WP_REST_Request $req ) {
	$block = sanitize_text_field( (string) $req['block'] );
	$label = sanitize_text_field( (string) $req['label'] );
	if ( ! bst_block_json_attr_names( $block ) ) {
		return new WP_Error( 'bst_preset_block', __( 'This block cannot have saved styles.', 'bigspy-ai-theme-wp' ), array( 'status' => 400 ) );
	}
	$attrs = bst_sanitize_preset_attrs( $block, $req['attrs'] );
	if ( ! $attrs || '' === $label ) {
		return new WP_Error( 'bst_preset_empty', __( 'Nothing to save.', 'bigspy-ai-theme-wp' ), array( 'status' => 400 ) );
	}
	$all = bst_user_block_presets();
	$own = isset( $all[ $block ] ) ? (array) $all[ $block ] : array();
	if ( count( $own ) >= 50 ) {
		return new WP_Error( 'bst_preset_limit', __( 'Limit reached: 50 saved styles per block. Delete one first.', 'bigspy-ai-theme-wp' ), array( 'status' => 400 ) );
	}
	$own[ 'u' . substr( md5( $label . microtime() ), 0, 8 ) ] = array( 'label' => mb_substr( $label, 0, 60 ), 'attrs' => $attrs );
	$all[ $block ] = $own;
	update_option( 'bst_user_block_presets', $all, false );
	return rest_ensure_response( (object) $own );
}

/** Delete a site preset. */
function bst_user_presets_delete( WP_REST_Request $req ) {
	$block = sanitize_text_field( (string) $req['block'] );
	$id    = sanitize_key( (string) $req['id'] );
	$all   = bst_user_block_presets();
	unset( $all[ $block ][ $id ] );
	if ( empty( $all[ $block ] ) ) {
		unset( $all[ $block ] );
	}
	update_option( 'bst_user_block_presets', $all, false );
	return rest_ensure_response( (object) ( isset( $all[ $block ] ) ? $all[ $block ] : array() ) );
}

/** Editor data: built-in and saved attribute presets. */
function bst_block_attr_presets_editor_data() {
	$builtin = array();
	foreach ( bst_block_attr_presets() as $block => $list ) {
		foreach ( (array) $list as $id => $p ) {
			$builtin[ $block ][] = array( 'id' => $id, 'label' => $p[0], 'svg' => $p[1], 'attrs' => $p[2] );
		}
	}
	$data = array(
		'builtin' => (object) $builtin,
		'user'    => (object) bst_user_block_presets(),
		'canSave' => current_user_can( 'edit_theme_options' ),
	);
	wp_add_inline_script( 'bst-editor-kit', 'window.bstAttrPresets=' . wp_json_encode( $data ) . ';', 'before' );
}
add_action( 'enqueue_block_editor_assets', 'bst_block_attr_presets_editor_data', 20 );

/** Presets of a block for AI tools: id => array( label, attrs ). */
function bst_block_attr_preset_list( $block ) {
	$out     = array();
	$builtin = bst_block_attr_presets();
	$user    = bst_user_block_presets();
	foreach ( (array) ( $builtin[ $block ] ?? array() ) as $id => $p ) {
		$out[ $id ] = array( 'label' => $p[0], 'attrs' => $p[2] );
	}
	foreach ( (array) ( $user[ $block ] ?? array() ) as $id => $p ) {
		$out[ $id ] = array( 'label' => $p['label'], 'attrs' => $p['attrs'] );
	}
	return $out;
}
