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

// The listing blocks render on the server; the editor needs the same CSS so cards look as published.
add_action(
	'enqueue_block_assets',
	function () {
		if ( is_admin() ) {
			wp_enqueue_style( 'bst-listing', BST_URI . '/assets/css/listing.css', array(), bst_asset_version( 'assets/css/listing.css' ) );
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
		// Without a saved FAQ the description already shows its own FAQ section.
		$items = bst_listing_has_faq_field( get_the_ID() ) ? bst_listing_faq( get_the_ID() ) : array();
		if ( ! $items ) {
			return;
		}
		echo '<section class="bst-listing-faq"><h2>' . esc_html( bst_listing_text( 'faq' ) ) . '</h2>';
		foreach ( $items as $item ) {
			echo '<details><summary>' . esc_html( $item['question'] ) . '</summary><div>' . wp_kses_post( wpautop( $item['answer'] ) ) . '</div></details>';
		}
		echo '</section>';
	},
	12
);

// Once the FAQ has its own saved field it shows in the FAQ section above, so drop the copy in the description.
add_filter(
	'the_content',
	function ( $content ) {
		if ( function_exists( 'is_product' ) && is_product() && in_the_loop() && is_main_query() && get_the_ID() === get_queried_object_id() && bst_listing_has_faq_field( get_the_ID() ) ) {
			return bst_listing_remove_faq_section( $content );
		}
		return $content;
	},
	8
);
