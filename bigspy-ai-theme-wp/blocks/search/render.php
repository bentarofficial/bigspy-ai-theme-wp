<?php
/**
 * Search block: site search form (shared renderer in inc/search-block.php).
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

echo '<div ' . get_block_wrapper_attributes() . '>' . bst_render_search_form( (array) $attributes, 'site' ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped while building.
