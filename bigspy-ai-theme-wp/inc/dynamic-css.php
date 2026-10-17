<?php
/**
 * Turns theme options into CSS custom properties.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Option key => array( css variable, unit ). Also used by the Customizer
 * preview script to update values live without a refresh.
 */
function bst_css_var_map() {
	return array(
		'color_primary'          => array( '--bs-primary', '' ),
		'color_secondary'        => array( '--bs-secondary', '' ),
		'color_success'          => array( '--bs-success', '' ),
		'color_alert'            => array( '--bs-alert', '' ),
		'color_text'             => array( '--bs-text', '' ),
		'color_heading'          => array( '--bs-heading', '' ),
		'color_link'             => array( '--bs-link', '' ),
		'color_link_hover'       => array( '--bs-link-hover', '' ),
		'color_border'           => array( '--bs-border', '' ),
		'color_light'            => array( '--bs-light', '' ),
		'color_body_bg'          => array( '--bs-body-bg', '' ),
		'boxed_bg'               => array( '--bs-boxed-bg', '' ),
		'content_bg'             => array( '--bs-content-bg', '' ),
		'container_width'        => array( '--bs-container', 'px' ),
		'sidebar_width'          => array( '--bs-sidebar-width', 'px' ),
		'font_size_base'         => array( '--bs-fs-base', 'px' ),
		'font_size_mobile'       => array( '--bs-fs-mobile', 'px' ),
		'line_height'            => array( '--bs-lh', '' ),
		'font_weight_body'       => array( '--bs-fw-body', '' ),
		'font_weight_heading'    => array( '--bs-fw-heading', '' ),
		'heading_transform'      => array( '--bs-heading-transform', '' ),
		'h1_size'                => array( '--bs-h1', 'px' ),
		'h2_size'                => array( '--bs-h2', 'px' ),
		'h3_size'                => array( '--bs-h3', 'px' ),
		'h4_size'                => array( '--bs-h4', 'px' ),
		'nav_size'               => array( '--bs-nav-size', 'px' ),
		'nav_weight'             => array( '--bs-nav-weight', '' ),
		'nav_transform'          => array( '--bs-nav-transform', '' ),
		'button_radius'          => array( '--bs-btn-radius', 'px' ),
		'button_transform'       => array( '--bs-btn-transform', '' ),
		'button_weight'          => array( '--bs-btn-weight', '' ),
		'header_top_height'      => array( '--bs-top-h', 'px' ),
		'header_top_bg'          => array( '--bs-top-bg', '' ),
		'header_top_color'       => array( '--bs-top-color', '' ),
		'header_main_height'     => array( '--bs-main-h', 'px' ),
		'header_main_bg'         => array( '--bs-main-bg', '' ),
		'header_main_color'      => array( '--bs-main-color', '' ),
		'header_bottom_height'   => array( '--bs-bottom-h', 'px' ),
		'header_bottom_bg'       => array( '--bs-bottom-bg', '' ),
		'header_bottom_color'    => array( '--bs-bottom-color', '' ),
		'header_mobile_height'   => array( '--bs-mobile-h', 'px' ),
		'header_sticky_height'   => array( '--bs-sticky-h', 'px' ),
		'header_transparent_color' => array( '--bs-transparent-color', '' ),
		'logo_width'             => array( '--bs-logo-w', 'px' ),
		'logo_width_mobile'      => array( '--bs-logo-w-m', 'px' ),
		'title_bar_bg'           => array( '--bs-titlebar-bg', '' ),
		'title_bar_color'        => array( '--bs-titlebar-color', '' ),
		'footer_cta_bg'          => array( '--bs-cta-bg', '' ),
		'footer_1_bg'            => array( '--bs-f1-bg', '' ),
		'footer_1_color'         => array( '--bs-f1-color', '' ),
		'footer_1_heading_color' => array( '--bs-f1-heading', '' ),
		'footer_2_bg'            => array( '--bs-f2-bg', '' ),
		'footer_2_color'         => array( '--bs-f2-color', '' ),
		'footer_bottom_bg'       => array( '--bs-fb-bg', '' ),
		'footer_bottom_color'    => array( '--bs-fb-color', '' ),
		'shop_columns'           => array( '--bs-shop-cols', '' ),
		'shop_columns_tablet'    => array( '--bs-shop-cols-t', '' ),
		'shop_columns_mobile'    => array( '--bs-shop-cols-m', '' ),
		'product_title_lines'    => array( '--bs-product-title-lines', '' ),
	);
}

function bst_dynamic_css() {
	$vars = array();
	foreach ( bst_css_var_map() as $key => $def ) {
		$value = bst_mod( $key );
		if ( '' === $value || null === $value ) {
			continue;
		}
		$vars[] = $def[0] . ':' . $value . $def[1];
	}
	$vars[] = '--bs-font-body:' . bst_font_stack( bst_mod( 'font_body' ) );
	$vars[] = '--bs-font-heading:' . bst_font_stack( bst_mod( 'font_heading' ) );
	$vars[] = '--bs-font-nav:' . bst_font_stack( bst_mod( 'font_nav' ) );

	$image = bst_mod( 'title_bar_image' );
	if ( $image ) {
		$vars[] = '--bs-titlebar-image:url("' . esc_url_raw( $image ) . '")';
	}

	$css = ':root{' . implode( ';', $vars ) . '}';
	$css = wp_strip_all_tags( $css );
	return apply_filters( 'bst_dynamic_css', $css );
}
