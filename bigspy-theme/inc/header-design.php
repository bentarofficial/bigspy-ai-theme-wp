<?php
/** Optional free-form header designed as a reusable page of native blocks. */
defined( 'ABSPATH' ) || exit;
add_filter( 'bst_defaults', function ( $d ) { return $d + array( 'header_design_page' => 0 ); } );
add_filter( 'bst_customizer_fields', function ( $fields ) {
	$fields[] = array( 'id' => 'header_design_page', 'section' => 'bst_header_builder', 'type' => 'dropdown-pages', 'label' => 'Header tự do bằng BIGSPY AI Builder', 'description' => 'Trang công khai đã xuất bản thay toàn bộ header hàng/cột. Dùng các khối logo, menu và tìm kiếm WordPress; các tùy chọn sticky/transparent của header mặc định không áp dụng.' );
	return $fields;
} );
function bst_header_design() {
	$page = get_post( absint( bst_mod( 'header_design_page' ) ) );
	if ( ! $page || 'page' !== $page->post_type || 'publish' !== $page->post_status || $page->post_password ) { return false; }
	// A page cannot also be its own site-wide header, or it swallows the whole view.
	if ( is_page( $page->ID ) ) { return false; }
	static $rendering = false;
	if ( $rendering ) { return false; }
	$rendering = true;
	echo '<header class="bs-custom-header">' . do_shortcode( do_blocks( $page->post_content ) ) . '</header>';
	$rendering = false;
	return true;
}
