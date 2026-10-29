<?php
/**
 * WooCommerce integration. Loaded only when WooCommerce is active.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

require_once BST_DIR . '/inc/woocommerce-elements.php';
require_once BST_DIR . '/inc/commerce-customization.php';
require_once BST_DIR . '/inc/checkout-extras.php';
require_once BST_DIR . '/inc/product-options.php';
require_once BST_DIR . '/inc/variation-swatches.php';
require_once BST_DIR . '/inc/product-design.php';
require_once BST_DIR . '/inc/quick-view.php';

/**
 * Is this request rendering the theme only as a preview (it is not the site's saved theme)?
 */
function bst_is_theme_preview() {
	if ( ! empty( $_GET['wp_theme_preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return true;
	}
	global $wp_customize;
	return is_customize_preview() && $wp_customize && ! $wp_customize->is_theme_active();
}

/**
 * WooCommerce stores resolved template paths in the object cache. A preview
 * request would leave paths of this theme behind for the live site (and read
 * the live theme's paths itself), so the template cache is emptied before and
 * after every preview request.
 */
function bst_wc_purge_template_cache() {
	if ( function_exists( 'wc_clear_template_cache' ) ) {
		wc_clear_template_cache();
	}
	// Some object caches lose WooCommerce's list of cached templates, so the
	// entries for the templates this theme overrides are also removed by key.
	$version = defined( 'WC_VERSION' ) ? WC_VERSION : '';
	foreach ( array( 'content-product' ) as $template ) {
		$parts = explode( '-', $template, 2 );
		wp_cache_delete( sanitize_key( implode( '-', array( 'template-part', $parts[0], $parts[1] ?? '', $version ) ) ), 'woocommerce' );
		wp_cache_delete( sanitize_key( implode( '-', array( 'template', $template . '.php', '', '', $version ) ) ), 'woocommerce' );
	}
}

function bst_wc_isolate_preview_templates() {
	if ( bst_is_theme_preview() ) {
		bst_wc_purge_template_cache();
		add_action( 'shutdown', 'bst_wc_purge_template_cache', 0 );
	}
}
add_action( 'init', 'bst_wc_isolate_preview_templates', 1 );
add_action( 'switch_theme', 'bst_wc_purge_template_cache' );
add_action( 'after_switch_theme', 'bst_wc_purge_template_cache' );

function bst_wc_setup() {
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 450,
			'single_image_width'    => 800,
			'product_grid'          => array(
				'default_columns' => 4,
				'min_columns'     => 1,
				'max_columns'     => 6,
			),
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'bst_wc_setup' );

function bst_wc_assets() {
	wp_enqueue_style( 'bst-woocommerce', BST_URI . '/assets/css/woocommerce.css', array( 'bst-main' ), bst_asset_version( 'assets/css/woocommerce.css' ) );
	if ( ! is_product() && ( is_shop() || is_product_taxonomy() ) && 'none' !== bst_mod( 'shop_sidebar' ) && is_active_sidebar( 'sidebar-shop' ) ) {
		wp_enqueue_script( 'bst-woocommerce-shop', BST_URI . '/assets/js/woocommerce-shop.js', array(), bst_asset_version( 'assets/js/woocommerce-shop.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	}
	if ( is_product() && ( bst_mod( 'product_mobile_bar' ) || bst_mod( 'product_sticky_summary' ) ) ) {
		wp_enqueue_script( 'bst-woocommerce-product', BST_URI . '/assets/js/woocommerce-product.js', array(), bst_asset_version( 'assets/js/woocommerce-product.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	}
}
add_action( 'wp_enqueue_scripts', 'bst_wc_assets', 20 );

/**
 * Product card styles inside the block editor (Products block preview).
 */
function bst_wc_editor_assets() {
	if ( is_admin() ) {
		wp_enqueue_style( 'bst-woocommerce-editor', BST_URI . '/assets/css/woocommerce.css', array(), bst_asset_version( 'assets/css/woocommerce.css' ) );
	}
}
add_action( 'enqueue_block_assets', 'bst_wc_editor_assets' );

/* ---------------------------------------------------------------- Page wrappers with theme layout */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

add_action( 'woocommerce_before_main_content', 'bst_layout_open', 10 );
add_action( 'woocommerce_after_main_content', 'bst_layout_close', 10 );

/**
 * Shop / category title handled by the theme (simple or bar style).
 */
add_filter( 'woocommerce_show_page_title', '__return_false' );

function bst_wc_archive_header() {
	if ( is_product() ) {
		if ( bst_mod( 'product_breadcrumb' ) ) {
			bst_breadcrumbs();
		}
		return;
	}
	bst_simple_title();
}
add_action( 'woocommerce_before_main_content', 'bst_wc_archive_header', 20 );

/** Mobile drawer trigger for the existing shop sidebar and filter widgets. */
function bst_wc_shop_filter_trigger() {
	if ( ! is_product() && ( is_shop() || is_product_taxonomy() ) && 'none' !== bst_mod( 'shop_sidebar' ) && is_active_sidebar( 'sidebar-shop' ) ) {
		echo '<button class="bs-shop-filter-trigger" type="button" aria-controls="bs-shop-sidebar" aria-expanded="false" data-bs-shop-filter-open data-close-label="' . esc_attr__( 'Close filters', 'bigspy-ai-theme-wp' ) . '">' . esc_html__( 'Filters', 'bigspy-ai-theme-wp' ) . '</button><div class="bs-shop-filter-scrim" data-bs-shop-filter-close hidden></div>';
	}
}
add_action( 'woocommerce_before_shop_loop', 'bst_wc_shop_filter_trigger', 5 );

/* ---------------------------------------------------------------- Loop */
function bst_wc_loop_columns() {
	return (int) bst_mod( 'shop_columns' );
}
add_filter( 'loop_shop_columns', 'bst_wc_loop_columns', 20 );

function bst_wc_per_page() {
	return (int) bst_mod( 'shop_per_page' );
}
add_filter( 'loop_shop_per_page', 'bst_wc_per_page', 20 );

function bst_wc_related_args( $args ) {
	$cols                   = (int) bst_mod( 'related_columns' );
	$args['posts_per_page'] = $cols;
	$args['columns']        = $cols;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'bst_wc_related_args' );
add_filter( 'woocommerce_upsell_display_args', 'bst_wc_related_args' );

function bst_wc_body_class( $classes ) {
	$classes[] = 'bs-product-card--' . sanitize_html_class( bst_mod( 'product_card_style' ) );
	$classes[] = 'bs-product-hover--' . sanitize_html_class( bst_mod( 'product_hover' ) );
	$classes[] = 'bs-sale-badge--' . sanitize_html_class( bst_mod( 'sale_badge_style' ) );
	$classes[] = 'bs-product-btn--' . sanitize_html_class( bst_mod( 'product_button' ) );
	if ( is_product() ) {
		$classes[] = 'bs-product-gallery--' . sanitize_html_class( bst_choice( bst_mod( 'product_gallery_layout' ), array( 'default', 'stacked', 'thumbnails-left' ), 'default' ) );
		if ( bst_mod( 'product_sticky_summary' ) ) {
			$classes[] = 'bs-product-sticky-summary';
		}
		if ( bst_mod( 'product_mobile_bar' ) && ! bst_mod( 'catalog_mode' ) ) {
			$classes[] = 'bs-product-mobile-bar';
		}
	}
	if ( bst_mod( 'catalog_mode' ) ) {
		$classes[] = 'bs-catalog-mode';
	}
	return $classes;
}
add_filter( 'body_class', 'bst_wc_body_class' );

/*
 * Product cards use the theme template woocommerce/content-product.php, which
 * renders its own markup (image, badges, category, title, rating, price,
 * button). The default callbacks that the template no longer needs are
 * removed so they are not printed twice by other plugins firing the hooks.
 */
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

/**
 * Highest discount of a product (variable products: best variation), in %.
 */
function bst_wc_sale_percent( $product ) {
	if ( ! $product || ! $product->is_on_sale() ) {
		return 0;
	}
	$percent = 0;
	$items   = $product->is_type( 'variable' ) ? array_filter( array_map( 'wc_get_product', $product->get_children() ) ) : array( $product );
	foreach ( $items as $item ) {
		$regular = (float) $item->get_regular_price();
		$sale    = (float) $item->get_sale_price();
		if ( $item->is_on_sale() && $regular > 0 && $sale < $regular ) {
			$percent = max( $percent, (int) round( 100 - $sale / $regular * 100 ) );
		}
	}
	return $percent;
}

/**
 * Badges shown on product cards and on the single product image.
 *
 * @param WC_Product $product Product.
 * @return array list of array( class, label ).
 */
function bst_wc_badges( $product ) {
	$badges = array();
	if ( ! $product ) {
		return $badges;
	}
	if ( ! $product->is_in_stock() ) {
		$badges[] = array( 'out', __( 'Sold out', 'bigspy-ai-theme-wp' ) );
	} elseif ( $product->is_on_sale() ) {
		$percent  = bst_mod( 'show_sale_percent' ) ? bst_wc_sale_percent( $product ) : 0;
		$badges[] = array( 'sale', $percent ? '-' . $percent . '%' : __( 'Sale', 'bigspy-ai-theme-wp' ) );
	}
	$days    = (int) bst_mod( 'new_badge_days' );
	$created = $product->get_date_created();
	if ( $days > 0 && $created && $created->getTimestamp() > time() - $days * DAY_IN_SECONDS ) {
		$badges[] = array( 'new', __( 'New', 'bigspy-ai-theme-wp' ) );
	}
	if ( $product->is_featured() && bst_mod( 'featured_badge' ) ) {
		$badges[] = array( 'hot', __( 'Hot', 'bigspy-ai-theme-wp' ) );
	}
	return apply_filters( 'bst_product_badges', $badges, $product );
}

function bst_wc_badges_html( $product ) {
	$out = '';
	foreach ( bst_wc_badges( $product ) as $badge ) {
		$out .= '<span class="bs-badge bs-badge--' . esc_attr( $badge[0] ) . '">' . esc_html( $badge[1] ) . '</span>';
	}
	return $out ? '<div class="bs-product__badges">' . $out . '</div>' : '';
}

/**
 * Single product page: replace the default "Sale!" flash with the theme badges.
 */
function bst_wc_sale_flash( $html, $post, $product ) {
	return is_product() ? '' : $html;
}
add_filter( 'woocommerce_sale_flash', 'bst_wc_sale_flash', 10, 3 );

function bst_wc_single_badges() {
	global $product;
	echo bst_wc_badges_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'woocommerce_before_single_product_summary', 'bst_wc_single_badges', 9 );

/** Add a compact mobile purchase bar using the native WooCommerce form as its source. */
function bst_wc_mobile_purchase_bar() {
	if ( ! is_product() || ! bst_mod( 'product_mobile_bar' ) || bst_mod( 'catalog_mode' ) ) {
		return;
	}
	global $product;
	if ( ! $product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		return;
	}
	$label = $product->is_type( 'variable' ) ? __( 'Choose options', 'bigspy-ai-theme-wp' ) : __( 'Add to cart', 'bigspy-ai-theme-wp' );
	echo '<div class="bs-product-mobile-purchase" data-product-purchase-bar><span class="bs-product-mobile-purchase__price">' . wp_kses_post( $product->get_price_html() ) . '</span><button type="button" class="bs-btn bs-btn--primary" data-product-purchase-trigger>' . esc_html( $label ) . '</button></div>';
}
add_action( 'wp_footer', 'bst_wc_mobile_purchase_bar', 30 );

/**
 * Loop add-to-cart link with the theme icon and classes.
 */
function bst_wc_loop_add_to_cart_args( $args, $product ) {
	$args['class'] = trim( ( $args['class'] ?? '' ) . ' bs-product__cart' );
	return $args;
}
add_filter( 'woocommerce_loop_add_to_cart_args', 'bst_wc_loop_add_to_cart_args', 10, 2 );

/* ---------------------------------------------------------------- Catalog mode */
function bst_wc_catalog_mode() {
	if ( ! bst_mod( 'catalog_mode' ) ) {
		return;
	}
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	add_action( 'woocommerce_single_product_summary', 'bst_wc_catalog_button', 30 );
	add_filter( 'woocommerce_is_purchasable', '__return_false' );
}
add_action( 'wp', 'bst_wc_catalog_mode' );

function bst_wc_catalog_button() {
	$url = trim( (string) bst_mod( 'catalog_mode_url' ) );
	if ( '' === $url ) {
		$phone = preg_replace( '/[^0-9+]/', '', (string) bst_mod( 'contact_phone' ) );
		$url   = $phone ? 'tel:' . $phone : '';
	}
	if ( '' === $url ) {
		return;
	}
	echo '<p class="bs-catalog-button"><a class="bs-btn bs-btn--primary bs-btn--lg" href="' . esc_url( $url ) . '">' . bst_icon( 'phone', 18 ) . esc_html( bst_mod( 'catalog_mode_text' ) ) . '</a></p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/* ---------------------------------------------------------------- Header cart */
/**
 * Header cart element.
 *
 * @param string $device desktop|mobile.
 */
function bst_header_cart( $device = 'desktop' ) {
	if ( bst_mod( 'catalog_mode' ) || ! function_exists( 'WC' ) || ! WC()->cart ) {
		return '';
	}
	$icon = bst_choice( bst_mod( 'cart_icon' ), array( 'bag', 'cart' ), 'bag' );
	$out  = '<div class="bs-cart' . ( 'desktop' === $device && bst_mod( 'mini_cart' ) ? ' has-dropdown' : '' ) . '">';
	$extra = '';
	if ( function_exists( 'bst_he' ) ) {
		$label = trim( (string) bst_he( 'cart', 'label' ) );
		if ( '' !== $label ) {
			$extra .= '<span class="bs-he-label">' . esc_html( $label ) . '</span>';
		}
		if ( bst_he( 'cart', 'total' ) ) {
			$extra .= '<span class="bs-cart__total">' . wp_kses_post( WC()->cart->get_cart_subtotal() ) . '</span>';
		}
	}
	$out .= '<a class="bs-cart__link" href="' . esc_url( wc_get_cart_url() ) . '" aria-label="' . esc_attr__( 'Cart', 'bigspy-ai-theme-wp' ) . '"><span class="bs-cart__icon">' . bst_icon( $icon, 22 ) . bst_cart_count_html() . '</span>' . $extra . '</a>';
	if ( 'desktop' === $device && bst_mod( 'mini_cart' ) && ! is_cart() && ! is_checkout() ) {
		$out .= '<div class="bs-cart__dropdown"><div class="widget_shopping_cart_content">';
		ob_start();
		woocommerce_mini_cart();
		$out .= ob_get_clean();
		$out .= '</div></div>';
	}
	$out .= '</div>';
	return $out;
}

function bst_cart_count_html() {
	$count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	return '<span class="bs-cart__count' . ( $count ? '' : ' is-empty' ) . '">' . (int) $count . '</span>';
}

function bst_wc_cart_fragments( $fragments ) {
	$fragments['span.bs-cart__count'] = bst_cart_count_html();
	if ( WC()->cart ) {
		$fragments['span.bs-cart__total'] = '<span class="bs-cart__total">' . wp_kses_post( WC()->cart->get_cart_subtotal() ) . '</span>';
	}
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'bst_wc_cart_fragments' );

/* ---------------------------------------------------------------- Misc */
function bst_wc_breadcrumb_defaults( $defaults ) {
	$defaults['delimiter']   = '<span class="bs-breadcrumbs__sep">/</span>';
	$defaults['wrap_before'] = '<nav class="bs-breadcrumbs woocommerce-breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'bigspy-ai-theme-wp' ) . '">';
	$defaults['wrap_after']  = '</nav>';
	return $defaults;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'bst_wc_breadcrumb_defaults' );

function bst_wc_gallery_thumbnail_columns() {
	return 5;
}
add_filter( 'woocommerce_product_thumbnails_columns', 'bst_wc_gallery_thumbnail_columns' );
