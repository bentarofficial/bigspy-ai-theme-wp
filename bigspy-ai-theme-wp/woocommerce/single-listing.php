<?php
/**
 * Listing page in the directory layout (see inc/listing/single-layout.php).
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );
$GLOBALS['bst_listing_layout'] = true;
// The description is printed below, so WooCommerce tabs would repeat it.
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );

echo '<div class="bs-container bst-ls">';
bst_breadcrumbs();
while ( have_posts() ) {
	the_post();
	$bst_id = get_the_ID();
	do_action( 'woocommerce_before_single_product' );
	?>
	<article id="product-<?php the_ID(); ?>" <?php wc_product_class( 'bst-ls-article', $bst_id ); ?>>
		<?php
		echo bst_listing_header_html( $bst_id ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped while building.
		echo bst_listing_facts_html( $bst_id ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped while building.
		$bst_sidebar = bst_listing_sidebar_html( $bst_id );
		?>
		<div class="bst-ls-layout<?php echo $bst_sidebar ? ' bst-ls-layout--sidebar' : ''; ?>">
			<div class="bst-ls-main">
				<?php
				if ( bst_feature_get( 'listing', 'single_sections', 1 ) ) {
					echo bst_listing_sections_html( $bst_id ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped while building.
				}
				?>
				<div class="bst-ls-content bs-content"><?php the_content(); ?></div>
				<?php do_action( 'woocommerce_after_single_product_summary' ); ?>
			</div>
			<?php echo $bst_sidebar; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped while building. ?>
		</div>
	</article>
	<?php
	do_action( 'woocommerce_after_single_product' );
}
echo '</div>';
$GLOBALS['bst_listing_layout'] = false;
get_footer( 'shop' );
