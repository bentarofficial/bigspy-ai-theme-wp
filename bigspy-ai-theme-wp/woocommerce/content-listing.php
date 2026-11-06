<?php
/**
 * Listing card inside WooCommerce loops while BIGSPY AI List mode is on (see inc/listing/templates.php).
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bst_listing_card' ) ) {
	return;
}
?>
<li <?php wc_product_class( 'bst-litem', get_the_ID() ); ?>>
	<?php echo bst_listing_card( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped while building. ?>
</li>
