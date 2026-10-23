<?php
/** A reusable published UX Block or page may supply the visual footer design. */
defined( 'ABSPATH' ) || exit;
add_filter( 'bst_defaults', function ( $d ) { return $d + array( 'footer_block' => '0', 'footer_design_page' => 0 ); } );
add_filter( 'bst_customizer_fields', function ( $fields ) {
	$choices = array( '0' => '— Không dùng —' );
	if ( ( is_admin() || is_customize_preview() ) && post_type_exists( 'blocks' ) ) {
		foreach ( get_posts( array( 'post_type' => 'blocks', 'post_status' => 'publish', 'numberposts' => 200, 'orderby' => 'title', 'order' => 'ASC' ) ) as $block ) {
			$choices[ (string) $block->ID ] = $block->post_title ? $block->post_title : '#' . $block->ID;
		}
	}
	$fields[] = array( 'id' => 'footer_block', 'section' => 'bst_footer_widgets', 'type' => 'select', 'label' => 'Footer từ UX Block', 'description' => 'Thiết kế footer bằng khối BIGSPY trong UX Blocks rồi chọn ở đây. Thay hai hàng widget; thanh bản quyền vẫn giữ.', 'choices' => $choices );
	$fields[] = array( 'id' => 'footer_design_page', 'section' => 'bst_footer_widgets', 'type' => 'dropdown-pages', 'label' => 'Footer thiết kế bằng khối BIGSPY', 'description' => 'Chọn trang đã xuất bản công khai để thay hai hàng widget. Thanh bản quyền vẫn giữ. Chọn Không để dùng footer mặc định.' );
	return $fields;
} );

/** The published UX Block chosen as footer, or null. */
function bst_footer_block_post() {
	$id   = absint( bst_mod( 'footer_block' ) );
	$post = $id ? get_post( $id ) : null;
	return ( $post && 'blocks' === $post->post_type && 'publish' === $post->post_status ) ? $post : null;
}

function bst_footer_design() {
	$source = bst_footer_block_post();
	if ( ! $source ) {
		$id = absint( bst_mod( 'footer_design_page' ) );
		if ( ! $id || is_page( $id ) ) { return false; }
		$source = get_post( $id );
		if ( ! $source || 'page' !== $source->post_type || 'publish' !== $source->post_status || $source->post_password ) { return false; }
	}
	static $rendering = false;
	if ( $rendering ) { return false; }
	$rendering = true;
	echo '<div class="bs-footer-design">' . do_shortcode( do_blocks( $source->post_content ) ) . '</div>'; // WordPress-authored content; shortcode/block callbacks escape their output.
	$rendering = false;
	return true;
}
