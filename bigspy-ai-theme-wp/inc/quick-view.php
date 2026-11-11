<?php
/** Read-only product preview; purchases continue on the native product page. */
defined( 'ABSPATH' ) || exit;
add_filter( 'bst_defaults', function ( $d ) { return $d + array( 'product_quick_view' => false ); } );
add_filter( 'bst_customizer_fields', function ( $fields ) {
	$fields[] = array( 'id' => 'product_quick_view', 'section' => 'bst_shop', 'type' => 'checkbox', 'label' => __( 'Product quick view', 'bigspy-ai-theme-wp' ), 'description' => __( 'Opens the image, price and description in a window; buying continues on the full product page.', 'bigspy-ai-theme-wp' ) );
	return $fields;
} );
add_action( 'bst_product_card_media', function ( $product ) {
	if ( bst_mod( 'product_quick_view' ) ) { echo '<a class="bs-quick-view" href="' . esc_url( $product->get_permalink() ) . '" data-bst-quick-view="' . absint( $product->get_id() ) . '">' . esc_html__( 'Quick view', 'bigspy-ai-theme-wp' ) . '</a>'; }
} );
add_action( 'wp_enqueue_scripts', function () {
	if ( ! bst_mod( 'product_quick_view' ) ) { return; }
	wp_enqueue_script( 'bst-quick-view', BST_URI . '/assets/js/quick-view.js', array(), bst_asset_version( 'assets/js/quick-view.js' ), true );
	wp_localize_script( 'bst-quick-view', 'bstQuickView', array( 'url' => rest_url( 'bigspy/v1/quick-view/' ), 'close' => __( 'Close quick view', 'bigspy-ai-theme-wp' ), 'loading' => __( 'Loading product…', 'bigspy-ai-theme-wp' ) ) );
	wp_enqueue_style( 'bst-quick-view', BST_URI . '/assets/css/quick-view.css', array(), bst_asset_version( 'assets/css/quick-view.css' ) );
} );
add_action( 'rest_api_init', function () {
	register_rest_route( 'bigspy/v1', '/quick-view/(?P<id>\d+)', array( 'methods' => 'GET', 'permission_callback' => '__return_true', 'callback' => function ( $r ) {
		$p = wc_get_product( absint( $r['id'] ) );
		if ( ! bst_mod( 'product_quick_view' ) || ! $p || 'publish' !== $p->get_status() || 'hidden' === $p->get_catalog_visibility() || post_password_required( $p->get_id() ) || ! $p->is_visible() ) { return new WP_Error( 'bst_unavailable', __( 'This product is not available.', 'bigspy-ai-theme-wp' ), array( 'status' => 404 ) ); }
		$html = '<div class="bs-quick-view-grid">' . $p->get_image( 'large' ) . '<div><h2 id="bst-quick-view-title">' . esc_html( $p->get_name() ) . '</h2>';
		if ( ! bst_mod( 'catalog_mode' ) ) { $html .= '<div class="price">' . wp_kses_post( $p->get_price_html() ) . '</div>'; }
		$html .= wp_kses_post( wpautop( $p->get_short_description() ) ) . '<a class="button" href="' . esc_url( $p->get_permalink() ) . '">' . esc_html__( 'View product details', 'bigspy-ai-theme-wp' ) . '</a></div></div>';
		return rest_ensure_response( array( 'html' => $html ) );
	} ) );
} );
