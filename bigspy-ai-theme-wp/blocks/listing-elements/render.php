<?php
/**
 * Listing elements: renders nothing unless BIGSPY AI List mode and WooCommerce are on.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bst_render_listing_elements' ) ) {
	return;
}
$html = bst_render_listing_elements( (array) $attributes );
if ( '' !== $html ) {
	echo '<div ' . get_block_wrapper_attributes() . '>' . $html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped while building.
}
