<?php
/**
 * G-202: fuller typography and an extended color palette.
 *
 * Adds H5/H6 sizes per device, heading line-height and letter-spacing, body
 * letter-spacing, menu/button letter-spacing, button size, small meta text, and
 * four named custom colors that join the block editor palette. Every option is
 * empty by default and prints nothing, so existing sites look the same.
 * Google Fonts hosting on the site already exists: Advanced → Performance.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Number of extra palette colors. */
function bst_custom_color_count() {
	return 4;
}

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$num   = function ( $min, $max, $step ) {
			return array( 'min' => $min, 'max' => $max, 'step' => $step, 'placeholder' => __( 'Theme default', 'bigspy-ai-theme-wp' ) );
		};
		$case  = array(
			''           => __( 'Theme default', 'bigspy-ai-theme-wp' ),
			'none'       => __( 'Normal', 'bigspy-ai-theme-wp' ),
			'uppercase'  => __( 'UPPERCASE', 'bigspy-ai-theme-wp' ),
			'capitalize' => __( 'Capitalize', 'bigspy-ai-theme-wp' ),
		);
		$t     = 'bst_typography';
		$add   = array(
			array( 'id' => 'h5_size', 'section' => $t, 'type' => 'responsive', 'label' => __( 'H5 size (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $num( 12, 32, 1 ) ),
			array( 'id' => 'h6_size', 'section' => $t, 'type' => 'responsive', 'label' => __( 'H6 size (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $num( 10, 28, 1 ) ),
			array( 'id' => 'heading_line_height', 'section' => $t, 'type' => 'number', 'label' => __( 'Heading line height', 'bigspy-ai-theme-wp' ), 'input_attrs' => $num( 0.9, 2, 0.05 ) ),
			array( 'id' => 'heading_letter_spacing', 'section' => $t, 'type' => 'number', 'label' => __( 'Heading letter spacing (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $num( -3, 10, 0.1 ) ),
			array( 'id' => 'body_letter_spacing', 'section' => $t, 'type' => 'number', 'label' => __( 'Body letter spacing (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $num( -2, 5, 0.1 ) ),
			array( 'id' => 'meta_font_size', 'section' => $t, 'type' => 'number', 'label' => __( 'Small meta text size (px)', 'bigspy-ai-theme-wp' ), 'description' => __( 'Date, author, category and reading time under post titles.', 'bigspy-ai-theme-wp' ), 'input_attrs' => $num( 10, 20, 1 ) ),
			array( 'id' => 'meta_transform', 'section' => $t, 'type' => 'select', 'label' => __( 'Small meta text case', 'bigspy-ai-theme-wp' ), 'choices' => $case ),
			array( 'id' => 'meta_letter_spacing', 'section' => $t, 'type' => 'number', 'label' => __( 'Small meta letter spacing (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $num( -2, 5, 0.1 ) ),
			array( 'id' => 'nav_letter_spacing', 'section' => 'bst_header_nav', 'type' => 'number', 'label' => __( 'Menu letter spacing (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $num( -2, 6, 0.1 ) ),
			array( 'id' => 'button_font_size', 'section' => 'bst_buttons', 'type' => 'number', 'label' => __( 'Font size (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $num( 11, 24, 1 ) ),
			array( 'id' => 'button_letter_spacing', 'section' => 'bst_buttons', 'type' => 'number', 'label' => __( 'Letter spacing (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $num( -2, 6, 0.1 ) ),
		);
		for ( $i = 1; $i <= bst_custom_color_count(); $i++ ) {
			/* translators: %d: color number. */
			$add[] = array( 'id' => 'color_custom_' . $i, 'section' => 'bst_colors', 'type' => 'color', 'label' => sprintf( __( 'Extra color %d', 'bigspy-ai-theme-wp' ), $i ), 'description' => 1 === $i ? __( 'Extra colors join the block editor palette and the CSS variables --bs-custom-1 … --bs-custom-4.', 'bigspy-ai-theme-wp' ) : '' );
			/* translators: %d: color number. */
			$add[] = array( 'id' => 'color_custom_' . $i . '_name', 'section' => 'bst_colors', 'type' => 'text', 'label' => sprintf( __( 'Extra color %d name', 'bigspy-ai-theme-wp' ), $i ) );
		}
		return array_merge( $fields, $add );
	}
);

/** The extra colors that are set: slug => array( name, hex ). */
function bst_custom_colors() {
	$out = array();
	for ( $i = 1; $i <= bst_custom_color_count(); $i++ ) {
		$hex = sanitize_hex_color( (string) get_theme_mod( 'color_custom_' . $i, '' ) );
		if ( $hex ) {
			$name = trim( (string) get_theme_mod( 'color_custom_' . $i . '_name', '' ) );
			/* translators: %d: color number. */
			$out[ 'custom-' . $i ] = array( '' !== $name ? $name : sprintf( 'Extra color %d', $i ), $hex );
		}
	}
	return $out;
}

/** Editor palette: append the extra colors. */
add_filter(
	'wp_theme_json_data_theme',
	function ( $theme_json ) {
		$colors = bst_custom_colors();
		if ( ! $colors ) {
			return $theme_json;
		}
		$data    = $theme_json->get_data();
		$palette = $data['settings']['color']['palette']['theme'] ?? ( $data['settings']['color']['palette'] ?? array() );
		foreach ( $colors as $slug => $c ) {
			$palette[] = array( 'slug' => $slug, 'name' => $c[0], 'color' => $c[1] );
		}
		return $theme_json->update_with( array( 'version' => 3, 'settings' => array( 'color' => array( 'palette' => $palette ) ) ) );
	},
	20
);

/** CSS for the options above; '' while none is set. */
function bst_typography_css() {
	$n = function ( $id, $min, $max ) {
		$v = get_theme_mod( $id, '' );
		return ( '' !== $v && null !== $v && is_numeric( $v ) ) ? max( $min, min( $max, round( (float) $v, 2 ) ) ) : null;
	};
	$rules = array();
	$vars  = array();
	foreach ( bst_custom_colors() as $slug => $c ) {
		$vars[] = '--bs-' . $slug . ':' . $c[1];
	}
	$heading = array();
	if ( null !== ( $v = $n( 'heading_line_height', 0.9, 2 ) ) ) {
		$heading[] = 'line-height:' . $v;
	}
	if ( null !== ( $v = $n( 'heading_letter_spacing', -3, 10 ) ) ) {
		$heading[] = 'letter-spacing:' . $v . 'px';
	}
	if ( $heading ) {
		$rules[] = 'h1,h2,h3,h4,h5,h6{' . implode( ';', $heading ) . '}';
	}
	if ( null !== ( $v = $n( 'body_letter_spacing', -2, 5 ) ) ) {
		$rules[] = 'body{letter-spacing:' . $v . 'px}';
	}
	$meta = array();
	if ( null !== ( $v = $n( 'meta_font_size', 10, 20 ) ) ) {
		$meta[] = 'font-size:' . $v . 'px';
	}
	$case = (string) get_theme_mod( 'meta_transform', '' );
	if ( in_array( $case, array( 'none', 'uppercase', 'capitalize' ), true ) ) {
		$meta[] = 'text-transform:' . $case;
	}
	if ( null !== ( $v = $n( 'meta_letter_spacing', -2, 5 ) ) ) {
		$meta[] = 'letter-spacing:' . $v . 'px';
	}
	if ( $meta ) {
		$rules[] = '.bs-meta{' . implode( ';', $meta ) . '}';
	}
	if ( null !== ( $v = $n( 'nav_letter_spacing', -2, 6 ) ) ) {
		$rules[] = '.bs-menu>li>a{letter-spacing:' . $v . 'px}';
	}
	$btn = array();
	if ( null !== ( $v = $n( 'button_font_size', 11, 24 ) ) ) {
		$btn[] = 'font-size:' . $v . 'px';
	}
	if ( null !== ( $v = $n( 'button_letter_spacing', -2, 6 ) ) ) {
		$btn[] = 'letter-spacing:' . $v . 'px';
	}
	if ( $btn ) {
		$rules[] = '.wp-element-button,.wp-block-button__link,.bs-btn,.button{' . implode( ';', $btn ) . '}';
	}
	$css = ( $vars ? ':root{' . implode( ';', $vars ) . '}' : '' ) . implode( '', $rules );
	return wp_strip_all_tags( $css );
}
add_filter(
	'bst_dynamic_css',
	function ( $css ) {
		return $css . bst_typography_css();
	}
);
