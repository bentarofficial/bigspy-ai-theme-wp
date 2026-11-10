<?php
/**
 * Code backgrounds: animated CSS / canvas presets and custom code layers
 * for the Section and Banner blocks (no image or video needed).
 *
 * Layer order: image → code background → overlay → content.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Preset key => engine (css | canvas | custom). */
function bst_code_bg_presets() {
	return array(
		'gradient'  => 'css',
		'aurora'    => 'css',
		'blobs'     => 'css',
		'grid'      => 'css',
		'dots'      => 'css',
		'rays'      => 'css',
		'particles' => 'canvas',
		'waves'     => 'canvas',
		'stars'     => 'canvas',
		'custom'    => 'custom',
	);
}

/** Attributes shared by every block that supports a code background. */
function bst_code_bg_attributes() {
	return array(
		'codeBg'        => array( 'type' => 'string', 'default' => '' ),
		'codeBgC1'      => array( 'type' => 'string', 'default' => '' ),
		'codeBgC2'      => array( 'type' => 'string', 'default' => '' ),
		'codeBgC3'      => array( 'type' => 'string', 'default' => '' ),
		'codeBgSpeed'   => array( 'type' => 'number', 'default' => 100 ),
		'codeBgDensity' => array( 'type' => 'number', 'default' => 50 ),
		'codeBgOpacity' => array( 'type' => 'number', 'default' => 100 ),
		'codeBgMobile'  => array( 'type' => 'boolean', 'default' => true ),
		'codeBgCss'     => array( 'type' => 'string', 'default' => '' ),
		'codeBgHtml'    => array( 'type' => 'string', 'default' => '' ),
	);
}

/** Add the attributes to Section and Banner without touching their block.json. */
add_filter(
	'register_block_type_args',
	function ( $args, $name ) {
		if ( in_array( $name, array( 'bst/section', 'bst/banner' ), true ) ) {
			$args['attributes'] = array_merge( isset( $args['attributes'] ) ? (array) $args['attributes'] : array(), bst_code_bg_attributes() );
		}
		return $args;
	},
	10,
	2
);

function bst_code_bg_register_assets() {
	wp_register_style( 'bst-code-bg', BST_URI . '/assets/css/code-bg.css', array(), bst_asset_version( 'assets/css/code-bg.css' ) );
	wp_register_script( 'bst-code-bg', BST_URI . '/assets/js/code-bg.js', array(), bst_asset_version( 'assets/js/code-bg.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
}
add_action( 'init', 'bst_code_bg_register_assets' );

/** The editor (and its iframe) always gets both files so previews animate. */
add_action(
	'enqueue_block_assets',
	function () {
		if ( is_admin() ) {
			wp_enqueue_style( 'bst-code-bg' );
			wp_enqueue_script( 'bst-code-bg' );
		}
	}
);

/** Strip anything that could leave the <style> element or load remote code. */
function bst_code_bg_clean_css( $css ) {
	$css = wp_strip_all_tags( (string) $css );
	$css = str_replace( array( '<', '>' ), '', $css );
	$css = preg_replace( '/@import[^;]*;?|expression\s*\(|javascript\s*:|behavior\s*:|-moz-binding/i', '', $css );
	return trim( substr( $css, 0, 20000 ) );
}

/** Allowed markup for custom layers: plain HTML + animated SVG, no scripts or handlers. */
function bst_code_bg_allowed_html() {
	$common = array( 'class' => true, 'style' => true, 'id' => true, 'aria-hidden' => true );
	$svg    = array_merge(
		$common,
		array_fill_keys(
			array( 'viewbox', 'xmlns', 'width', 'height', 'preserveaspectratio', 'fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'stroke-dasharray', 'stroke-dashoffset', 'opacity', 'fill-opacity', 'stroke-opacity', 'transform', 'd', 'cx', 'cy', 'r', 'rx', 'ry', 'x', 'y', 'x1', 'y1', 'x2', 'y2', 'points', 'offset', 'stop-color', 'stop-opacity', 'gradientunits', 'gradienttransform', 'fx', 'fy', 'attributename', 'attributetype', 'from', 'to', 'by', 'values', 'keytimes', 'keysplines', 'calcmode', 'dur', 'begin', 'repeatcount', 'type', 'path', 'rotate', 'additive', 'mask', 'clip-path', 'filter', 'stddeviation', 'result', 'in', 'in2', 'mode', 'basefrequency', 'numoctaves', 'seed', 'scale', 'href', 'patternunits', 'filterunits' ),
			true
		)
	);
	$tags = array();
	foreach ( array( 'div', 'span', 'i', 'b', 'p', 'figure' ) as $t ) {
		$tags[ $t ] = $common;
	}
	foreach ( array( 'svg', 'g', 'path', 'circle', 'ellipse', 'rect', 'line', 'polyline', 'polygon', 'defs', 'lineargradient', 'radialgradient', 'stop', 'animate', 'animatetransform', 'animatemotion', 'mpath', 'use', 'symbol', 'pattern', 'mask', 'clippath', 'filter', 'fegaussianblur', 'feturbulence', 'fedisplacementmap', 'feblend', 'feoffset', 'fecolormatrix', 'femerge', 'femergenode' ) as $t ) {
		$tags[ $t ] = $svg;
	}
	return $tags;
}

/**
 * Markup of the code background layer.
 *
 * @param array  $a     Block attributes.
 * @param string $block section|banner (class prefix).
 */
function bst_code_bg( $a, $block ) {
	$presets = bst_code_bg_presets();
	$key     = sanitize_key( $a['codeBg'] ?? '' );
	if ( ! isset( $presets[ $key ] ) ) {
		return '';
	}
	$engine = $presets[ $key ];
	$uid    = wp_unique_id( 'bs-cbg-' );
	$speed  = bst_block_num( $a, 'codeBgSpeed', 100, 10, 400 );
	$style  = bst_style_vars(
		array(
			'--cbg-c1'      => bst_block_color( $a, 'codeBgC1' ),
			'--cbg-c2'      => bst_block_color( $a, 'codeBgC2' ),
			'--cbg-c3'      => bst_block_color( $a, 'codeBgC3' ),
			'--cbg-speed'   => round( 100 / $speed, 3 ),
			'--cbg-density' => bst_block_num( $a, 'codeBgDensity', 50, 5, 100 ),
			'opacity'       => 100 === (int) bst_block_num( $a, 'codeBgOpacity', 100, 0, 100 ) ? '' : bst_block_num( $a, 'codeBgOpacity', 100, 0, 100 ) / 100,
		)
	);
	$class = 'bs-codebg bs-codebg--' . $key . ' bs-' . $block . '__codebg' . ( isset( $a['codeBgMobile'] ) && ! $a['codeBgMobile'] ? ' bs-hide-mobile' : '' );

	wp_enqueue_style( 'bst-code-bg' );
	$inner = '';
	$data  = '';
	if ( 'canvas' === $engine ) {
		wp_enqueue_script( 'bst-code-bg' );
		$data  = ' data-bs-codebg="' . esc_attr( $key ) . '" data-speed="' . esc_attr( $speed / 100 ) . '" data-density="' . esc_attr( bst_block_num( $a, 'codeBgDensity', 50, 5, 100 ) ) . '"';
		$inner = '<canvas></canvas>';
	} elseif ( 'custom' === $engine ) {
		$inner = wp_kses( preg_replace( '#<(script|style)\b[^>]*>.*?</\1\s*>#is', '', (string) ( $a['codeBgHtml'] ?? '' ) ), bst_code_bg_allowed_html() );
		$css   = bst_code_bg_clean_css( $a['codeBgCss'] ?? '' );
		if ( '' !== $css ) {
			$inner = '<style>@scope (#' . $uid . ') {' . $css . '}</style>' . $inner;
		}
	} else {
		$inner = '<i></i><i></i><i></i>';
	}
	return '<div id="' . esc_attr( $uid ) . '" class="' . esc_attr( $class ) . '"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . $data . ' aria-hidden="true">' . $inner . '</div>';
}
