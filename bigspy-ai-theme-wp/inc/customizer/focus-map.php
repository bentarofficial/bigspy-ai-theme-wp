<?php
/**
 * Customizer quick-edit labels: hover an element in the preview, click its label, and the
 * matching Customizer section opens. Works in both directions: opening a section highlights
 * (and if needed navigates to) the element in the preview.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Title of a registered Customizer section, panel or control; '' when it does not exist.
 *
 * @param string $type section|panel|control.
 * @param string $id   ID.
 */
function bst_customize_target_title( $type, $id ) {
	global $wp_customize;
	if ( ! $wp_customize instanceof WP_Customize_Manager ) {
		return '';
	}
	if ( 'panel' === $type ) {
		$obj = $wp_customize->get_panel( $id );
		return $obj ? (string) $obj->title : '';
	}
	if ( 'control' === $type ) {
		$obj = $wp_customize->get_control( $id );
		return $obj ? (string) $obj->label : '';
	}
	$obj = $wp_customize->get_section( $id );
	return $obj ? (string) $obj->title : '';
}

/**
 * Raw map. Each item: selector, type (section|panel|control|url), id, label (optional, defaults
 * to the target's title), preview_url (optional, a page where the element exists).
 */
function bst_customize_focus_map_raw() {
	$shop     = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '';
	$cart     = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '';
	$checkout = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : '';
	$blog     = get_option( 'page_for_posts' ) ? get_permalink( (int) get_option( 'page_for_posts' ) ) : home_url( '/' );
	$latest   = static function ( $post_type ) {
		$ids = get_posts( array( 'post_type' => $post_type, 'numberposts' => 1, 'fields' => 'ids', 'post_status' => 'publish', 'no_found_rows' => true ) );
		return $ids ? get_permalink( $ids[0] ) : '';
	};
	$post     = $latest( 'post' );
	$product  = post_type_exists( 'product' ) ? $latest( 'product' ) : '';

	$map = array(
		array( 'selector' => '.bs-ann', 'type' => 'section', 'id' => 'bst_announcement' ),
		array( 'selector' => '.bs-header', 'type' => 'section', 'id' => 'bst_header_builder' ),
		array( 'selector' => '.bs-header__sticky', 'type' => 'section', 'id' => 'bst_header_general' ),
		array( 'selector' => '.bs-header-row', 'type' => 'section', 'id' => 'bst_header_rows' ),
		array( 'selector' => '.bs-header-el--nav nav, .bs-header-el--nav-secondary nav', 'type' => 'panel', 'id' => 'nav_menus', 'label' => __( 'Menus', 'bigspy-ai-theme-wp' ) ),
		array( 'selector' => '.bs-mega-menu', 'type' => 'section', 'id' => 'bst_header_nav' ),
		array( 'selector' => '.bs-title-bar', 'type' => 'section', 'id' => 'bst_title_bar' ),
		array( 'selector' => '.bs-breadcrumbs', 'type' => 'section', 'id' => 'bst_title_bar' ),
		array( 'selector' => '.bs-layout', 'type' => 'section', 'id' => 'bst_layout_site' ),
		array( 'selector' => '.bs-layout__sidebar', 'type' => 'section', 'id' => 'bst_sidebar' ),
		array( 'selector' => '.bs-posts[data-bst-posts]', 'type' => 'section', 'id' => 'bst_blog_archive', 'preview_url' => $blog ),
		array( 'selector' => 'body.single-post .bs-entry--single', 'type' => 'section', 'id' => 'bst_blog_single', 'preview_url' => $post ),
		array( 'selector' => '.bs-shop-sidebar, .bs-shop-filter-trigger, body.woocommerce-shop ul.products, body.tax-product_cat ul.products', 'type' => 'section', 'id' => 'bst_shop', 'preview_url' => $shop ),
		array( 'selector' => 'li.bs-product, .bs-product-part, body.single-product div.product', 'type' => 'section', 'id' => 'bst_product', 'preview_url' => $product ),
		array( 'selector' => '.bs-product__contact', 'type' => 'section', 'id' => 'bst_catalog' ),
		array( 'selector' => '.woocommerce-cart-form, .cart-collaterals, form.woocommerce-checkout', 'type' => 'section', 'id' => 'bst_checkout', 'preview_url' => $cart ? $cart : $checkout ),
		array( 'selector' => '.bst-ls', 'type' => 'section', 'id' => 'bst_listing_single' ),
		array( 'selector' => '.bs-footer', 'type' => 'panel', 'id' => 'bst_footer' ),
		array( 'selector' => '.bs-footer-cta', 'type' => 'section', 'id' => 'bst_footer_cta' ),
		array( 'selector' => '.bs-footer-widgets', 'type' => 'section', 'id' => 'bst_footer_widgets' ),
		array( 'selector' => '.bs-footer-bottom', 'type' => 'section', 'id' => 'bst_footer_bottom' ),
		array( 'selector' => '.bs-fc', 'type' => 'section', 'id' => 'bst_float' ),
		array( 'selector' => '.bs-cookie', 'type' => 'section', 'id' => 'bst_site_extras' ),
	);

	// Page content built with blocks: link to the block editor.
	if ( is_singular() && ! is_singular( 'post' ) && ! is_singular( 'product' ) ) {
		$edit = get_edit_post_link( get_queried_object_id(), 'raw' );
		if ( $edit ) {
			$map[] = array( 'selector' => '.bs-layout__content > .bs-entry > .bs-entry__content', 'type' => 'url', 'id' => $edit, 'label' => __( 'Edit page content', 'bigspy-ai-theme-wp' ) );
		}
	}
	return $map;
}

/**
 * Validated map: items whose target does not exist are dropped, labels filled in.
 */
function bst_customize_focus_map() {
	$out = array();
	foreach ( (array) apply_filters( 'bst_customize_focus_map', bst_customize_focus_map_raw() ) as $item ) {
		if ( empty( $item['selector'] ) || empty( $item['id'] ) || empty( $item['type'] ) || ! in_array( $item['type'], array( 'section', 'panel', 'control', 'url' ), true ) ) {
			continue;
		}
		$title = 'url' === $item['type'] ? '' : bst_customize_target_title( $item['type'], $item['id'] );
		if ( 'url' !== $item['type'] && '' === $title ) {
			continue;
		}
		$out[] = array(
			'selector'    => (string) $item['selector'],
			'type'        => $item['type'],
			'id'          => 'url' === $item['type'] ? esc_url_raw( $item['id'] ) : (string) $item['id'],
			'label'       => wp_strip_all_tags( ! empty( $item['label'] ) ? $item['label'] : $title ),
			'preview_url' => empty( $item['preview_url'] ) ? '' : esc_url_raw( $item['preview_url'] ),
		);
	}
	return $out;
}

/**
 * data-bst-edit attribute for elements printed dynamically. Empty outside the Customizer preview.
 * Each entry is a list of alternatives array( type, id [, label] ); the preview keeps the first
 * alternative that exists in the Customizer (checked by the controls frame, which knows them all).
 *
 * @param array $entries Entries.
 */
function bst_edit_attr( $entries ) {
	if ( ! is_customize_preview() ) {
		return '';
	}
	$list = array();
	foreach ( (array) $entries as $entry ) {
		$alternatives = isset( $entry[0] ) && is_array( $entry[0] ) ? $entry : array( $entry );
		$alts         = array();
		foreach ( $alternatives as $alt ) {
			$id = (string) $alt[1];
			if ( '' === $id ) {
				continue;
			}
			$alts[] = array( 't' => $alt[0], 'id' => 'url' === $alt[0] ? esc_url_raw( $id ) : $id, 'l' => empty( $alt[2] ) ? '' : wp_strip_all_tags( $alt[2] ) );
		}
		if ( $alts ) {
			$list[] = $alts;
		}
	}
	return $list ? ' data-bst-edit="' . esc_attr( wp_json_encode( $list ) ) . '"' : '';
}

/** Fallback section of each header element when it has no own section. */
function bst_header_element_edit_attr( $element ) {
	$fallback = array(
		'logo'          => 'bst_header_logo',
		'nav'           => 'bst_header_nav',
		'nav_secondary' => 'bst_header_nav',
		'nav_icon'      => 'bst_header_nav',
		'search'        => 'bst_header_search',
		'search_form'   => 'bst_header_search',
		'button_1'      => 'bst_header_buttons',
		'button_2'      => 'bst_header_buttons',
		'html_1'        => 'bst_header_html',
		'html_2'        => 'bst_header_html',
		'html_3'        => 'bst_header_html',
		'contact'       => 'bst_contact',
		'social'        => 'bst_contact',
	);
	$alts = array( array( 'section', 'bst_he_' . $element ) );
	if ( isset( $fallback[ $element ] ) ) {
		$alts[] = array( 'section', $fallback[ $element ] );
	}
	return bst_edit_attr( array( $alts ) );
}

/** Widget area target ("Widgets" sub-label). */
function bst_sidebar_edit_attr( $sidebar_id, $main = '' ) {
	$targets = array();
	if ( $main ) {
		$targets[] = array( 'section', $main );
	}
	$targets[] = array( 'section', 'sidebar-widgets-' . $sidebar_id, __( 'Widgets', 'bigspy-ai-theme-wp' ) );
	return bst_edit_attr( $targets );
}

/** Map for the preview: not checked here (the preview request may not list every section); the controls frame filters it. */
function bst_customize_focus_map_preview() {
	$out = array();
	foreach ( (array) apply_filters( 'bst_customize_focus_map', bst_customize_focus_map_raw() ) as $item ) {
		if ( empty( $item['selector'] ) || empty( $item['id'] ) || empty( $item['type'] ) ) {
			continue;
		}
		$out[] = array(
			'selector' => (string) $item['selector'],
			'type'     => (string) $item['type'],
			'id'       => 'url' === $item['type'] ? esc_url_raw( $item['id'] ) : (string) $item['id'],
			'label'    => empty( $item['label'] ) ? '' : wp_strip_all_tags( $item['label'] ),
		);
	}
	return $out;
}

function bst_customize_focus_preview_assets() {
	wp_enqueue_style( 'bst-customizer-preview', BST_URI . '/assets/css/customizer-preview.css', array(), bst_asset_version( 'assets/css/customizer-preview.css' ) );
	wp_localize_script(
		'bst-customizer-preview',
		'bstFocus',
		array(
			'items' => bst_customize_focus_map_preview(),
			'edit'  => __( 'Edit: %s', 'bigspy-ai-theme-wp' ),
		)
	);
}
add_action( 'customize_preview_init', 'bst_customize_focus_preview_assets', 20 );

function bst_customize_focus_controls_data() {
	$urls = array();
	foreach ( bst_customize_focus_map() as $item ) {
		if ( $item['preview_url'] && 'url' !== $item['type'] && ! isset( $urls[ $item['id'] ] ) ) {
			$urls[ $item['id'] ] = $item['preview_url'];
		}
	}
	wp_localize_script(
		'bst-customizer',
		'bstFocusControls',
		array(
			'urls'  => (object) $urls,
			'label' => __( 'Quick-edit labels', 'bigspy-ai-theme-wp' ),
			'help'  => __( 'Hover an element in the preview, then click its label to open its settings.', 'bigspy-ai-theme-wp' ),
		)
	);
}
add_action( 'customize_controls_enqueue_scripts', 'bst_customize_focus_controls_data', 20 );
