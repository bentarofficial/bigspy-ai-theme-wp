<?php
/**
 * G-204: advanced header options.
 *
 * Sticky behaviour (slide in, hide on scroll down / show on scroll up, shrink),
 * sticky colors, and the mobile menu style (side panel, full screen, drop-down)
 * with width and colors. Defaults keep the old look: slide-in sticky, side panel.
 * Per-page transparent header already exists in the page layout box.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$g   = 'bst_header_general';
		$n   = 'bst_header_nav';
		$add = array(
			array( 'id' => 'header_sticky_style', 'section' => $g, 'type' => 'image_radio', 'label' => __( 'Sticky style', 'bigspy-ai-theme-wp' ), 'choices' => array(
				'slide'  => __( 'Slide in', 'bigspy-ai-theme-wp' ),
				'smart'  => __( 'Hide on scroll down', 'bigspy-ai-theme-wp' ),
				'shrink' => __( 'Shrink', 'bigspy-ai-theme-wp' ),
			), 'description' => __( '“Hide on scroll down” shows the header again as soon as the visitor scrolls up.', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'header_sticky_bg', 'section' => $g, 'type' => 'color', 'label' => __( 'Sticky header background', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'header_sticky_color', 'section' => $g, 'type' => 'color', 'label' => __( 'Sticky header text color', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'offcanvas_style', 'section' => $n, 'type' => 'image_radio', 'label' => __( 'Mobile menu style', 'bigspy-ai-theme-wp' ), 'choices' => array(
				'panel'      => __( 'Side panel', 'bigspy-ai-theme-wp' ),
				'fullscreen' => __( 'Full screen', 'bigspy-ai-theme-wp' ),
				'dropdown'   => __( 'Drop-down', 'bigspy-ai-theme-wp' ),
			) ),
			array( 'id' => 'offcanvas_width', 'section' => $n, 'type' => 'number', 'label' => __( 'Side panel width (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 240, 'max' => 600, 'placeholder' => '340' ) ),
			array( 'id' => 'offcanvas_bg', 'section' => $n, 'type' => 'color', 'label' => __( 'Mobile menu background', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'offcanvas_color', 'section' => $n, 'type' => 'color', 'label' => __( 'Mobile menu text color', 'bigspy-ai-theme-wp' ) ),
		);
		return array_merge( $fields, $add );
	}
);

/** Pictures for the two new pickers (60×40 boxes: x, y, w, h, accent). */
add_filter(
	'bst_layout_pictures',
	function ( $all ) {
		$all['sticky']    = array(
			'slide'  => array( array( 2, 2, 56, 8, 1 ), array( 2, 13, 56, 25, 0 ) ),
			'smart'  => array( array( 2, 2, 56, 4, 1 ), array( 26, 8, 8, 4, 1 ), array( 2, 15, 56, 23, 0 ) ),
			'shrink' => array( array( 2, 2, 56, 5, 1 ), array( 2, 10, 56, 28, 0 ) ),
		);
		$all['offcanvas'] = array(
			'panel'      => array( array( 2, 2, 22, 36, 1 ), array( 26, 2, 32, 36, 0 ) ),
			'fullscreen' => array( array( 2, 2, 56, 36, 1 ) ),
			'dropdown'   => array( array( 2, 2, 56, 6, 0 ), array( 2, 9, 56, 16, 1 ), array( 2, 27, 56, 11, 0 ) ),
		);
		return $all;
	}
);
add_filter(
	'bst_layout_picker_map',
	function ( $map ) {
		$map['header_sticky_style'] = 'sticky';
		$map['offcanvas_style']     = 'offcanvas';
		return $map;
	}
);

/** Show the panel width only for the side panel; sticky colors only when sticky. */
add_action(
	'customize_register',
	function ( $wp_customize ) {
		$is_sticky = function () {
			return (bool) bst_mod( 'header_sticky' );
		};
		$when      = array(
			'offcanvas_width'     => function () {
				return 'panel' === get_theme_mod( 'offcanvas_style', 'panel' );
			},
			'header_sticky_style' => $is_sticky,
			'header_sticky_bg'    => $is_sticky,
			'header_sticky_color' => $is_sticky,
		);
		foreach ( $when as $id => $callback ) {
			$control = $wp_customize->get_control( $id );
			if ( $control ) {
				$control->active_callback = $callback;
			}
		}
	},
	30
);

/** CSS for the options above; '' while none is set. */
function bst_header_advanced_css() {
	$rules  = array();
	$bg     = sanitize_hex_color( (string) get_theme_mod( 'header_sticky_bg', '' ) );
	$color  = sanitize_hex_color( (string) get_theme_mod( 'header_sticky_color', '' ) );
	if ( $bg ) {
		$rules[] = '.bs-header.is-stuck .bs-header__sticky,.bs-header.is-stuck .bs-header__sticky .bs-header-row{background:' . $bg . '}';
	}
	if ( $color ) {
		$rules[] = '.bs-header.is-stuck .bs-header__sticky,.bs-header.is-stuck .bs-header__sticky .bs-header-row,.bs-header.is-stuck .bs-header__sticky .bs-menu>li>a{color:' . $color . '}';
	}
	$panel = array();
	$w     = get_theme_mod( 'offcanvas_width', '' );
	if ( '' !== $w && is_numeric( $w ) ) {
		$panel[] = '--bs-offcanvas-w:' . max( 240, min( 600, (int) $w ) ) . 'px';
	}
	$obg = sanitize_hex_color( (string) get_theme_mod( 'offcanvas_bg', '' ) );
	$ocl = sanitize_hex_color( (string) get_theme_mod( 'offcanvas_color', '' ) );
	if ( $obg ) {
		$panel[] = 'background:' . $obg;
	}
	if ( $ocl ) {
		$panel[] = 'color:' . $ocl;
		$rules[] = '.bs-offcanvas .bs-offcanvas-menu a,.bs-offcanvas .bs-icon-btn{color:' . $ocl . '}';
	}
	if ( $panel ) {
		$rules[] = '.bs-offcanvas .bs-offcanvas__panel{' . implode( ';', $panel ) . '}';
	}
	return wp_strip_all_tags( implode( '', $rules ) );
}
add_filter(
	'bst_dynamic_css',
	function ( $css ) {
		return $css . bst_header_advanced_css();
	}
);
