<?php
/** BIGSPY AI visual workspace backed by the native WordPress block document. */
defined( 'ABSPATH' ) || exit;

function bst_visual_builder_url( $id ) {
	return add_query_arg( array( 'page' => 'bigspy-ai-builder', 'post_id' => absint( $id ) ), admin_url( 'admin.php' ) );
}
function bst_visual_builder_menu() {
	add_submenu_page( null, 'BIGSPY AI Builder', 'BIGSPY AI Builder', 'edit_pages', 'bigspy-ai-builder', 'bst_visual_builder_page' );
}
add_action( 'admin_menu', 'bst_visual_builder_menu' );
function bst_visual_builder_post() {
	$id = isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : 0;
	$post = get_post( $id );
	return $post && 'page' === $post->post_type && current_user_can( 'edit_post', $id ) ? $post : null;
}
function bst_visual_builder_page() {
	if ( ! bst_visual_builder_post() ) {
		wp_die( esc_html__( 'Select a page you are allowed to edit.', 'bigspy-theme' ) );
	}
	echo '<div id="bst-visual-builder"><p>Đang mở BIGSPY AI Builder…</p></div>';
}
function bst_visual_builder_links( $actions, $post ) {
	if ( 'page' === $post->post_type && current_user_can( 'edit_post', $post->ID ) ) {
		$actions['bst_design'] = '<a href="' . esc_url( bst_visual_builder_url( $post->ID ) ) . '">Thiết kế với BIGSPY AI</a>';
	}
	return $actions;
}
add_filter( 'page_row_actions', 'bst_visual_builder_links', 10, 2 );
function bst_visual_builder_toolbar( $bar ) {
	if ( is_page() && current_user_can( 'edit_post', get_queried_object_id() ) ) {
		$bar->add_node( array( 'id' => 'bst-design', 'parent' => 'edit', 'title' => 'Thiết kế với BIGSPY AI', 'href' => bst_visual_builder_url( get_queried_object_id() ) ) );
	}
}
add_action( 'admin_bar_menu', 'bst_visual_builder_toolbar', 90 );
function bst_visual_builder_assets( $hook ) {
	if ( 'admin_page_bigspy-ai-builder' !== $hook ) { return; }
	$post = bst_visual_builder_post();
	if ( ! $post ) { return; }
	wp_enqueue_media( array( 'post' => $post->ID ) );
	wp_enqueue_script( 'wp-format-library' );
	wp_add_inline_script( 'wp-blocks', 'wp.blocks.unstable__bootstrapServerSideBlockDefinitions(' . wp_json_encode( get_block_editor_server_block_settings() ) . ');', 'after' );
	wp_enqueue_script( 'bst-blocks-editor' );
	wp_enqueue_script( 'bst-countdown-editor' );
	if ( class_exists( 'WooCommerce' ) ) { wp_enqueue_script( 'bst-product-part-editor' ); }
	wp_enqueue_style( 'wp-edit-blocks' );
	wp_enqueue_style( 'wp-block-library' );
	wp_enqueue_style( 'wp-components' );
	bst_block_assets();
	wp_enqueue_style( 'bst-builder', BST_URI . '/assets/css/visual-builder.css', array( 'bst-blocks' ), bst_asset_version( 'assets/css/visual-builder.css' ) );
	wp_enqueue_script( 'bst-builder', BST_URI . '/assets/js/visual-builder.js', array( 'wp-block-library', 'wp-block-editor', 'wp-media-utils', 'wp-components', 'wp-element', 'wp-data', 'wp-api-fetch', 'bst-blocks-editor' ), bst_asset_version( 'assets/js/visual-builder.js' ), true );
	$context = new WP_Block_Editor_Context( array( 'post' => $post ) );
	$settings = get_block_editor_settings( array(), $context );
	$settings['hasUploadPermissions'] = current_user_can( 'upload_files' );
	wp_localize_script( 'bst-builder', 'bstBuilderData', array( 'id' => $post->ID, 'title' => $post->post_title, 'content' => $post->post_content, 'modified' => $post->post_modified_gmt, 'status' => $post->post_status, 'settings' => $settings, 'editUrl' => get_edit_post_link( $post->ID, 'raw' ), 'viewUrl' => get_permalink( $post->ID ), 'patterns' => bst_pattern_studio_catalog() ) );
}
add_action( 'admin_enqueue_scripts', 'bst_visual_builder_assets' );

/** Save with WordPress capabilities, post locks, sanitization and revisions. */
function bst_visual_builder_save( WP_REST_Request $request ) {
	$id = absint( $request['id'] );
	$post = get_post( $id );
	if ( ! $post || 'page' !== $post->post_type ) { return new WP_Error( 'bst_missing', 'Không tìm thấy trang.', array( 'status' => 404 ) ); }
	require_once ABSPATH . 'wp-admin/includes/post.php';
	if ( wp_check_post_lock( $id ) || $post->post_modified_gmt !== $request->get_param( 'modified' ) ) {
		return new WP_Error( 'bst_conflict', 'Trang đang được người khác sửa hoặc đã thay đổi. Mở lại trang để tránh ghi đè.', array( 'status' => 409 ) );
	}
	$content = (string) $request->get_param( 'content' );
	if ( ! current_user_can( 'unfiltered_html' ) ) { $content = wp_kses_post( $content ); }
	$result = wp_update_post( wp_slash( array( 'ID' => $id, 'post_title' => sanitize_text_field( (string) $request->get_param( 'title' ) ), 'post_content' => $content ) ), true );
	if ( is_wp_error( $result ) ) { return $result; }
	$saved = get_post( $id );
	return rest_ensure_response( array( 'modified' => $saved->post_modified_gmt, 'content' => $saved->post_content, 'title' => $saved->post_title ) );
}
add_action( 'rest_api_init', function () {
	register_rest_route( 'bigspy/v1', '/builder/(?P<id>\d+)', array( 'methods' => 'POST', 'callback' => 'bst_visual_builder_save', 'permission_callback' => function ( $r ) { return current_user_can( 'edit_pages' ) && current_user_can( 'edit_post', absint( $r['id'] ) ); }, 'args' => array( 'content' => array( 'type' => 'string', 'required' => true ), 'title' => array( 'type' => 'string', 'required' => true ), 'modified' => array( 'type' => 'string', 'required' => true ) ) ) );
} );
