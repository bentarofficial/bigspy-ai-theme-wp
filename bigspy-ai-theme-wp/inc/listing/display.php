<?php
/**
 * Listing details on WooCommerce product pages: summary under the title and the FAQ.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_register_style( 'bst-listing', BST_URI . '/assets/css/listing.css', array(), bst_asset_version( 'assets/css/listing.css' ) );
		if ( function_exists( 'is_product' ) && is_product() ) {
			wp_enqueue_style( 'bst-listing' );
		}
	}
);

add_action(
	'woocommerce_single_product_summary',
	function () {
		echo bst_listing_info_html( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped while building.
	},
	7
);

add_action(
	'woocommerce_after_single_product_summary',
	function () {
		$items = bst_listing_faq( get_the_ID() );
		if ( ! $items ) {
			return;
		}
		echo '<section class="bst-listing-faq"><h2>' . esc_html__( 'Frequently asked questions', 'bigspy-ai-theme-wp' ) . '</h2>';
		foreach ( $items as $item ) {
			echo '<details><summary>' . esc_html( $item['question'] ) . '</summary><div>' . wp_kses_post( wpautop( $item['answer'] ) ) . '</div></details>';
		}
		echo '</section>';
	},
	12
);
