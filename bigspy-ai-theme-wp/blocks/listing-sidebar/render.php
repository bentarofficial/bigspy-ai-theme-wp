<?php
/**
 * Listing sidebar part for the listing being viewed. Renders nothing outside a listing page,
 * or unless BIGSPY AI List mode and WooCommerce are on.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bst_listing_sidebar_part' ) ) {
	return;
}

$a       = (array) $attributes;
$post_id = (int) ( $GLOBALS['bst_listing_sidebar_post'] ?? 0 );
if ( ! $post_id ) {
	$post_id = (int) ( $block->context['postId'] ?? 0 );
}
if ( ! $post_id && is_singular( 'product' ) ) {
	$post_id = (int) get_queried_object_id();
}
if ( ! $post_id && defined( 'REST_REQUEST' ) && REST_REQUEST ) {
	// Editor preview (Widgets screen): show the newest listing.
	$latest  = get_posts( array( 'post_type' => 'product', 'numberposts' => 1, 'fields' => 'ids' ) );
	$post_id = $latest ? (int) $latest[0] : 0;
}
if ( ! $post_id || 'product' !== get_post_type( $post_id ) ) {
	return;
}
$html = bst_listing_sidebar_part( $post_id, sanitize_key( $a['part'] ?? 'categories' ), (string) ( $a['title'] ?? '' ) );
if ( '' === $html ) {
	return;
}
echo '<div ' . get_block_wrapper_attributes() . '>' . $html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped while building.
