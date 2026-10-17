<?php
/** A reusable published page may supply the visual footer design. */
defined( 'ABSPATH' ) || exit;
add_filter( 'bst_defaults', function ( $d ) { return $d + array( 'footer_design_page' => 0 ); } );
add_filter( 'bst_customizer_fields', function ( $fields ) {
	$fields[] = array( 'id' => 'footer_design_page', 'section' => 'bst_footer_widgets', 'type' => 'dropdown-pages', 'label' => 'Footer thiết kế bằng khối BIGSPY', 'description' => 'Chọn trang đã xuất bản công khai để thay hai hàng widget. Thanh bản quyền vẫn giữ. Chọn Không để dùng footer mặc định.' );
	return $fields;
} );
function bst_footer_design() {
	$id = absint( bst_mod( 'footer_design_page' ) );
	if ( ! $id || is_page( $id ) ) { return false; }
	$page = get_post( $id );
	if ( ! $page || 'page' !== $page->post_type || 'publish' !== $page->post_status || $page->post_password ) { return false; }
	static $rendering = false;
	if ( $rendering ) { return false; }
	$rendering = true;
	echo '<div class="bs-footer-design">' . do_shortcode( do_blocks( $page->post_content ) ) . '</div>'; // WordPress-authored content; shortcode/block callbacks escape their output.
	$rendering = false;
	return true;
}
