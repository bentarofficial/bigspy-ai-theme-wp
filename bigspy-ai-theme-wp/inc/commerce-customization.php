<?php
/** Optional WooCommerce presentation controls, using native forms and hooks. */
defined( 'ABSPATH' ) || exit;

function bst_commerce_defaults( $defaults ) {
	return $defaults + array(
		'cart_layout' => 'default', 'checkout_layout' => 'default',
		'commerce_sticky_totals' => false, 'cart_auto_update' => false,
		'shop_ajax_pagination' => false,
		'shop_ajax_filters' => false, 'shop_load_mode' => 'pages',
		'product_tabs_style' => 'default', 'product_extra_tab_title' => '',
		'product_extra_tab_content' => '', 'product_before_cart' => '', 'product_after_cart' => '',
	);
}
add_filter( 'bst_defaults', 'bst_commerce_defaults' );

function bst_commerce_sections( $sections ) {
	$sections['bst_checkout'] = array( 'title' => __( 'BIGSPY AI: cart & checkout', 'bigspy-ai-theme-wp' ), 'panel' => 'woocommerce', 'priority' => 4 );
	return $sections;
}
add_filter( 'bst_customizer_sections', 'bst_commerce_sections' );

function bst_commerce_fields( $fields ) {
	$fields[] = array( 'id' => 'shop_ajax_pagination', 'section' => 'bst_shop', 'type' => 'checkbox', 'label' => __( 'Load classic shop pagination without a full page reload', 'bigspy-ai-theme-wp' ), 'description' => __( 'Keeps page URLs and browser history. Filters and sorting keep their native navigation.', 'bigspy-ai-theme-wp' ) );
	$fields[] = array( 'id' => 'shop_ajax_filters', 'section' => 'bst_shop', 'type' => 'checkbox', 'label' => 'Lọc sản phẩm WooCommerce không tải lại trang', 'description' => 'Hỗ trợ bộ lọc danh mục, thuộc tính, đánh giá, giá và sắp xếp chuẩn WooCommerce; đồng bộ URL, nút quay lại và widget lọc. Bộ lọc của plugin khác tiếp tục dùng điều hướng riêng.' );
	$fields[] = array( 'id' => 'shop_load_mode', 'section' => 'bst_shop', 'type' => 'select', 'label' => 'Cách tải thêm sản phẩm', 'choices' => array( 'pages' => 'Phân trang', 'more' => 'Nút tải thêm', 'infinite' => 'Tự tải khi cuộn' ) );
	$layouts = array( 'default' => __( 'Default', 'bigspy-ai-theme-wp' ), 'simple' => __( 'Simple', 'bigspy-ai-theme-wp' ), 'focused' => __( 'Focused', 'bigspy-ai-theme-wp' ) );
	$fields[] = array( 'id' => 'cart_layout', 'section' => 'bst_checkout', 'type' => 'select', 'label' => __( 'Cart layout', 'bigspy-ai-theme-wp' ), 'choices' => $layouts );
	$fields[] = array( 'id' => 'checkout_layout', 'section' => 'bst_checkout', 'type' => 'select', 'label' => __( 'Checkout layout', 'bigspy-ai-theme-wp' ), 'choices' => $layouts );
	$fields[] = array( 'id' => 'commerce_sticky_totals', 'section' => 'bst_checkout', 'type' => 'checkbox', 'label' => __( 'Keep order totals visible on desktop', 'bigspy-ai-theme-wp' ), 'description' => __( 'For classic WooCommerce cart/checkout. Block checkout keeps its native layout.', 'bigspy-ai-theme-wp' ) );
	$fields[] = array( 'id' => 'cart_auto_update', 'section' => 'bst_checkout', 'type' => 'checkbox', 'label' => __( 'Update classic cart after quantity changes', 'bigspy-ai-theme-wp' ), 'description' => __( 'Uses the native Update cart button. WooCommerce Cart blocks already update quantities automatically.', 'bigspy-ai-theme-wp' ) );
	$fields[] = array( 'id' => 'product_tabs_style', 'section' => 'bst_product', 'type' => 'select', 'label' => __( 'Product tab appearance', 'bigspy-ai-theme-wp' ), 'choices' => array( 'default' => __( 'Default', 'bigspy-ai-theme-wp' ), 'pills' => __( 'Pill buttons', 'bigspy-ai-theme-wp' ), 'underline' => __( 'Underline', 'bigspy-ai-theme-wp' ) ) );
	foreach ( array( 'product_extra_tab_title' => array( 'text', __( 'Additional product tab title', 'bigspy-ai-theme-wp' ) ), 'product_extra_tab_content' => array( 'html', __( 'Additional product tab content', 'bigspy-ai-theme-wp' ) ), 'product_before_cart' => array( 'html', __( 'Content before purchase form', 'bigspy-ai-theme-wp' ) ), 'product_after_cart' => array( 'html', __( 'Content after purchase form', 'bigspy-ai-theme-wp' ) ) ) as $id => $spec ) {
		$fields[] = array( 'id' => $id, 'section' => 'bst_product', 'type' => $spec[0], 'label' => $spec[1], 'description' => __( 'Shown on all products. Leave empty to hide.', 'bigspy-ai-theme-wp' ) );
	}
	return $fields;
}
add_filter( 'bst_customizer_fields', 'bst_commerce_fields' );

function bst_commerce_classes( $classes ) {
	if ( is_cart() ) {
		$classes[] = 'bs-cart-layout--' . bst_choice( bst_mod( 'cart_layout' ), array( 'default', 'simple', 'focused' ), 'default' );
	}
	if ( is_checkout() && ! is_order_received_page() ) {
		$classes[] = 'bs-checkout-layout--' . bst_choice( bst_mod( 'checkout_layout' ), array( 'default', 'simple', 'focused' ), 'default' );
	}
	if ( ( is_cart() || is_checkout() ) && bst_mod( 'commerce_sticky_totals' ) ) {
		$classes[] = 'bs-commerce-sticky-totals';
	}
	if ( is_product() ) {
		$classes[] = 'bs-product-tabs--' . bst_choice( bst_mod( 'product_tabs_style' ), array( 'default', 'pills', 'underline' ), 'default' );
	}
	return $classes;
}
add_filter( 'body_class', 'bst_commerce_classes' );

function bst_commerce_assets() {
	if ( ( is_shop() || is_product_taxonomy() ) && bst_shop_ajax_enabled() ) {
		wp_enqueue_script( 'bst-shop-pagination', BST_URI . '/assets/js/shop-pagination.js', array(), bst_asset_version( 'assets/js/shop-pagination.js' ), true );
		wp_localize_script( 'bst-shop-pagination', 'bstShopAjax', array( 'filters' => (bool) bst_mod( 'shop_ajax_filters' ), 'mode' => bst_choice( bst_mod( 'shop_load_mode' ), array( 'pages', 'more', 'infinite' ), 'pages' ) ) );
	}
	if ( is_product() || is_cart() || is_checkout() ) {
		wp_enqueue_style( 'bst-commerce-controls', BST_URI . '/assets/css/commerce-controls.css', array( 'bst-woocommerce' ), bst_asset_version( 'assets/css/commerce-controls.css' ) );
	}
	if ( is_cart() && ! has_block( 'woocommerce/cart', get_queried_object_id() ) && bst_mod( 'cart_auto_update' ) ) {
		wp_enqueue_script( 'bst-cart-quantity', BST_URI . '/assets/js/cart-quantity.js', array( 'jquery', 'wc-cart' ), bst_asset_version( 'assets/js/cart-quantity.js' ), true );
	}
}
add_action( 'wp_enqueue_scripts', 'bst_commerce_assets', 30 );

function bst_product_extra_tab( $tabs ) {
	$title = trim( (string) bst_mod( 'product_extra_tab_title' ) );
	if ( $title && trim( (string) bst_mod( 'product_extra_tab_content' ) ) ) {
		$tabs['bst_additional'] = array( 'title' => $title, 'priority' => 25, 'callback' => 'bst_product_extra_tab_render' );
	}
	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'bst_product_extra_tab' );

function bst_product_extra_tab_render() {
	echo wp_kses_post( wpautop( (string) bst_mod( 'product_extra_tab_content' ) ) );
}
function bst_product_before_cart_content() {
	$content = trim( (string) bst_mod( 'product_before_cart' ) );
	if ( $content ) {
		echo '<div class="bs-purchase-content bs-purchase-content--before">' . wp_kses_post( wpautop( $content ) ) . '</div>';
	}
}
function bst_product_after_cart_content() {
	$content = trim( (string) bst_mod( 'product_after_cart' ) );
	if ( $content ) {
		echo '<div class="bs-purchase-content bs-purchase-content--after">' . wp_kses_post( wpautop( $content ) ) . '</div>';
	}
}
add_action( 'woocommerce_before_add_to_cart_form', 'bst_product_before_cart_content' );
add_action( 'woocommerce_after_add_to_cart_form', 'bst_product_after_cart_content' );

function bst_product_gallery_slider_enabled( $enabled ) {
	return 'stacked' === bst_mod( 'product_gallery_layout' ) ? false : $enabled;
}
add_filter( 'woocommerce_single_product_flexslider_enabled', 'bst_product_gallery_slider_enabled' );

function bst_shop_results_open() {
	if ( ( is_shop() || is_product_taxonomy() ) && bst_shop_ajax_enabled() ) {
		echo '<div data-bst-shop-results tabindex="-1" aria-label="' . esc_attr__( 'Products', 'bigspy-ai-theme-wp' ) . '">';
	}
}
function bst_shop_results_close() {
	if ( ( is_shop() || is_product_taxonomy() ) && bst_shop_ajax_enabled() ) {
		echo '</div>';
	}
}
add_action( 'woocommerce_before_shop_loop', 'bst_shop_results_open', 10 );
add_action( 'woocommerce_after_shop_loop', 'bst_shop_results_close', 99 );
function bst_shop_ajax_enabled() {
	return bst_mod( 'shop_ajax_pagination' ) || bst_mod( 'shop_ajax_filters' ) || in_array( bst_mod( 'shop_load_mode' ), array( 'more', 'infinite' ), true );
}
