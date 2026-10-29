<?php
/** Visual product templates composed from native WooCommerce pieces. */
defined( 'ABSPATH' ) || exit;
add_filter( 'bst_defaults', function ( $d ) { return $d + array( 'product_design_page' => 0 ); } );
add_filter( 'bst_customizer_fields', function ( $fields ) {
	$fields[] = array( 'id' => 'product_design_page', 'section' => 'bst_product', 'type' => 'dropdown-pages', 'label' => 'Trang mẫu bố cục sản phẩm', 'description' => 'Trong Sửa trang, dùng các khối BIGSPY và Product Part để thiết kế trang mẫu. Chỉ dùng trang công khai đã xuất bản; Không để giữ bố cục mặc định.' );
	return $fields;
} );
function bst_product_design_page() {
	$id = absint( bst_mod( 'product_design_page' ) );
	if ( ! $id ) { return null; }
	$page = get_post( $id );
	return $page && 'page' === $page->post_type && 'publish' === $page->post_status && ! $page->post_password ? $page : null;
}
add_filter( 'wc_get_template_part', function ( $template, $slug, $name ) {
	if ( 'content' === $slug && 'single-product' === $name && bst_product_design_page() ) {
		return BST_DIR . '/templates/product-design.php';
	}
	return $template;
}, 20, 3 );
add_action( 'init', function () {
	wp_register_script( 'bst-product-part-editor', BST_URI . '/assets/js/product-part-editor.js', array( 'bst-block-previews', 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render' ), bst_asset_version( 'assets/js/product-part-editor.js' ), true );
	register_block_type( 'bst/product-part', array( 'api_version' => 3, 'title' => __( 'Product Part', 'bigspy-ai-theme-wp' ), 'description' => __( 'One part of the single product page, such as the title, gallery, price, add to cart or tabs. Use it in the product layout page to design your own product page.', 'bigspy-ai-theme-wp' ), 'category' => 'bigspy', 'icon' => 'products', 'attributes' => array( 'part' => array( 'type' => 'string', 'default' => 'title' ), 'previewId' => array( 'type' => 'number', 'default' => 0 ) ), 'editor_script' => 'bst-product-part-editor', 'render_callback' => 'bst_product_part_render' ) );
}, 21 );
function bst_product_part_render( $a ) {
	global $product, $post;
	$callbacks = array( 'title' => 'woocommerce_template_single_title', 'gallery' => 'woocommerce_show_product_images', 'price' => 'woocommerce_template_single_price', 'rating' => 'woocommerce_template_single_rating', 'description' => 'woocommerce_template_single_excerpt', 'cart' => 'woocommerce_template_single_add_to_cart', 'meta' => 'woocommerce_template_single_meta', 'tabs' => 'woocommerce_output_product_data_tabs', 'related' => 'woocommerce_output_related_products', 'upsell' => 'woocommerce_upsell_display' );
	$part = $a['part'] ?? 'title';
	if ( ! isset( $callbacks[ $part ] ) ) { return ''; }
	if ( bst_mod( 'catalog_mode' ) && in_array( $part, array( 'cart', 'price' ), true ) ) { return ''; }
	$original = $product;
	$original_post = $post;
	$preview = false;
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		$preview = true;
		$product = wc_get_product( absint( $a['previewId'] ?? 0 ) );
		if ( ! $product || 'publish' !== $product->get_status() || ! $product->is_visible() || post_password_required( $product->get_id() ) ) { $product = $original; return '<p>Chọn ID sản phẩm công khai để xem trước phần này.</p>'; }
	}
	if ( ! $product instanceof WC_Product ) { return ''; }
	if ( $preview ) { $post = get_post( $product->get_id() ); setup_postdata( $post ); }
	ob_start();
	if ( $preview ) { echo '<div style="pointer-events:none" inert>'; }
	call_user_func( $callbacks[ $part ] );
	if ( $preview ) { echo '</div>'; }
	$html = ob_get_clean();
	$product = $original;
	if ( $preview ) { $post = $original_post; if ( $post instanceof WP_Post ) { setup_postdata( $post ); } else { wp_reset_postdata(); } }
	return '<div class="bs-product-part bs-product-part--' . esc_attr( $part ) . '">' . $html . '</div>';
}
