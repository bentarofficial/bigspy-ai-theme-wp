<?php
/**
 * Listing info: renders nothing unless BIGSPY AI List mode and WooCommerce are on.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bst_listing_info_html' ) || ! class_exists( 'WooCommerce' ) ) {
	return;
}

$a       = (array) $attributes;
$post_id = ! empty( $a['productId'] ) ? (int) $a['productId'] : (int) ( $block->context['postId'] ?? get_the_ID() );
$html    = bst_listing_info_html(
	$post_id,
	array(
		'tagline' => ! empty( $a['showTagline'] ),
		'facts'   => ! empty( $a['showFacts'] ),
		'button'  => ! empty( $a['showButton'] ),
		'limit'   => max( 1, min( 20, (int) ( $a['limit'] ?? 8 ) ) ),
	)
);
if ( '' === $html ) {
	return;
}
wp_enqueue_style( 'bst-listing', BST_URI . '/assets/css/listing.css', array(), bst_asset_version( 'assets/css/listing.css' ) );
echo '<div ' . get_block_wrapper_attributes() . '>' . $html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped while building.
