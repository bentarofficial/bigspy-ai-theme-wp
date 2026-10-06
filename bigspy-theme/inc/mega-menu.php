<?php
/** Native menu checkbox enables a column-based desktop dropdown. */
defined( 'ABSPATH' ) || exit;
add_action( 'wp_nav_menu_item_custom_fields', function ( $id, $item, $depth ) {
	if ( 0 !== (int) $depth ) { return; }
	echo '<p class="description description-wide"><label><input type="checkbox" name="bst_mega_menu[' . absint( $id ) . ']" value="1" ' . checked( get_post_meta( $id, '_bst_mega_menu', true ), '1', false ) . '> BIGSPY AI: Mega menu (các mục con thành cột trên desktop)</label></p>';
}, 10, 3 );
add_action( 'wp_update_nav_menu_item', function ( $menu_id, $item_id ) {
	if ( ! current_user_can( 'edit_theme_options' ) || empty( $_POST['update-nav-menu-nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['update-nav-menu-nonce'] ) ), 'update-nav_menu' ) ) { return; }
	$value = isset( $_POST['bst_mega_menu'] ) && is_array( $_POST['bst_mega_menu'] ) && isset( $_POST['bst_mega_menu'][ $item_id ] ) ? '1' : '';
	update_post_meta( $item_id, '_bst_mega_menu', $value );
}, 10, 2 );
add_filter( 'nav_menu_css_class', function ( $classes, $item, $args, $depth ) {
	if ( 0 === (int) $depth && '1' === get_post_meta( $item->ID, '_bst_mega_menu', true ) ) { $classes[] = 'bs-mega-menu'; }
	return $classes;
}, 10, 4 );
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'bst-mega-menu', BST_URI . '/assets/css/mega-menu.css', array( 'bst-main' ), bst_asset_version( 'assets/css/mega-menu.css' ) );
} );
