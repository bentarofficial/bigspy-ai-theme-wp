<?php
/**
 * BIGSPY AI List mode: WooCommerce products become directory listings.
 * Only loaded while the feature is on. Listing data lives in product meta with the
 * keys of the old BIGSPY AI List theme, so switching themes or the mode loses nothing.
 */
defined( 'ABSPATH' ) || exit;

require_once BST_DIR . '/inc/listing/core.php';
require_once BST_DIR . '/inc/listing/compat.php';
require_once BST_DIR . '/inc/listing/meta-box.php';
require_once BST_DIR . '/inc/listing/sponsored.php';
if ( class_exists( 'WooCommerce' ) ) {
	require_once BST_DIR . '/inc/listing/display.php';
	require_once BST_DIR . '/inc/listing/schema.php';
}

add_action(
	'admin_notices',
	function () {
		if ( ! class_exists( 'WooCommerce' ) && current_user_can( 'install_plugins' ) && 'bigspy-features' === sanitize_key( $_GET['page'] ?? '' ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- display only.
			echo '<div class="notice notice-warning"><p>' . esc_html__( 'BIGSPY AI List mode uses WooCommerce products as listings. Install and activate WooCommerce to show listings on the site.', 'bigspy-ai-theme-wp' ) . '</p></div>';
		}
	}
);
