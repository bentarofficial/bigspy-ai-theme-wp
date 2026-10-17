<?php
/** Opt-in purchase controls using WooCommerce's own variation and cart flow. */
defined( 'ABSPATH' ) || exit;
add_filter( 'bst_defaults', function ( $defaults ) {
	return $defaults + array( 'product_swatches' => false, 'product_archive_swatches' => false, 'product_buy_now' => false, 'product_navigation' => false );
} );
add_filter( 'bst_customizer_fields', function ( $fields ) {
	$fields[] = array( 'id' => 'product_swatches', 'section' => 'bst_product', 'type' => 'checkbox', 'label' => 'Chọn biến thể bằng nút (thay dropdown)', 'description' => 'Tùy chọn độc lập; mặc định tắt. Giữ luồng giỏ hàng và biến thể của WooCommerce.' );
	$fields[] = array( 'id' => 'product_archive_swatches', 'section' => 'bst_product', 'type' => 'checkbox', 'label' => 'Hiện swatch màu/ảnh trên thẻ sản phẩm', 'description' => 'Hiện các lựa chọn của thuộc tính toàn cục đã cấu hình swatch bên dưới tên sản phẩm. Bấm để mở trang sản phẩm và chọn sẵn giá trị tương ứng. Mặc định tắt.' );
	foreach ( array( 'product_buy_now' => 'Nút Mua ngay trên trang sản phẩm', 'product_navigation' => 'Điều hướng sản phẩm trước / sau' ) as $id => $label ) {
		$fields[] = array( 'id' => $id, 'section' => 'bst_product', 'type' => 'checkbox', 'label' => $label, 'description' => 'Tùy chọn độc lập; mặc định tắt.' );
	}
	return $fields;
} );
add_action( 'wp_enqueue_scripts', function () {
	if ( is_product() && bst_mod( 'product_swatches' ) ) {
		wp_enqueue_script( 'bst-swatches', BST_URI . '/assets/js/product-swatches.js', array( 'jquery', 'wc-add-to-cart-variation' ), bst_asset_version( 'assets/js/product-swatches.js' ), true );
	}
	if ( is_product() || ( bst_mod( 'product_archive_swatches' ) && ( is_shop() || is_product_taxonomy() ) ) ) {
		wp_enqueue_style( 'bst-product-options', BST_URI . '/assets/css/product-options.css', array(), bst_asset_version( 'assets/css/product-options.css' ) );
	}
	if ( is_product() ) {
		if ( bst_mod( 'product_buy_now' ) ) { wp_enqueue_script( 'bst-buy-now', BST_URI . '/assets/js/buy-now.js', array(), bst_asset_version( 'assets/js/buy-now.js' ), true ); }
	}
} );
add_action( 'woocommerce_after_add_to_cart_button', function () {
	global $product;
	if ( ! bst_mod( 'catalog_mode' ) && bst_mod( 'product_buy_now' ) && $product && in_array( $product->get_type(), array( 'simple', 'variable' ), true ) && $product->is_purchasable() && $product->is_in_stock() ) {
		echo '<button type="submit" name="add-to-cart" value="' . absint( $product->get_id() ) . '" class="button alt bst-buy-now">' . esc_html__( 'Mua ngay', 'bigspy-ai-theme-wp' ) . '</button>';
	}
} );
add_filter( 'woocommerce_add_to_cart_redirect', function ( $url ) {
	// Reached only after WooCommerce validates and adds the requested product.
	if ( bst_mod( 'product_buy_now' ) && isset( $_REQUEST['bst_buy_now'] ) && '1' === (string) $_REQUEST['bst_buy_now'] ) {
		return wc_get_checkout_url();
	}
	return $url;
} );
add_action( 'woocommerce_single_product_summary', function () {
	if ( ! bst_mod( 'product_navigation' ) ) { return; }
	$before = get_adjacent_post( false, '', true );
	$after = get_adjacent_post( false, '', false );
	if ( ! $before && ! $after ) { return; }
	echo '<nav class="bst-product-navigation" aria-label="Điều hướng sản phẩm">';
	foreach ( array( array( $before, '← Sản phẩm trước' ), array( $after, 'Sản phẩm sau →' ) ) as $item ) {
		if ( $item[0] && 'publish' === $item[0]->post_status && ! post_password_required( $item[0] ) ) {
			$adjacent = wc_get_product( $item[0]->ID );
			if ( $adjacent && $adjacent->is_visible() ) { echo '<a href="' . esc_url( get_permalink( $item[0]->ID ) ) . '">' . esc_html( $item[1] ) . '</a>'; }
		}
	}
	echo '</nav>';
}, 4 );
