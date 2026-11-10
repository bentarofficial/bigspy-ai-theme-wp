<?php
/**
 * Sponsored: renders nothing unless BIGSPY AI List mode is on and there are Sponsored listings.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bst_render_sponsored' ) ) {
	return;
}
$html = bst_render_sponsored( (array) $attributes );
if ( '' !== $html ) {
	echo '<div ' . get_block_wrapper_attributes() . '>' . $html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped while building.
}
