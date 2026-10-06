<?php
/**
 * Product card – BIGSPY AI Theme WP design.
 *
 * Overrides woocommerce/templates/content-product.php. Used by the shop,
 * category pages, related / up-sell products and the Products block.
 *
 * @package BigspyTheme
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

/*
 * Safety net: WooCommerce caches template paths in the object cache. After a
 * theme preview, a site running another theme can still be handed this file.
 * Without the theme's functions it must not fatal – use the active theme's
 * or WooCommerce's own template instead.
 */
if ( ! function_exists( 'bst_mod' ) || ! function_exists( 'bst_wc_badges_html' ) ) {
	$bst_fallback = '';
	foreach ( array_unique( array( get_stylesheet_directory(), get_template_directory() ) ) as $bst_dir ) {
		$bst_file = $bst_dir . '/woocommerce/content-product.php';
		if ( __FILE__ !== wp_normalize_path( $bst_file ) && realpath( $bst_file ) !== realpath( __FILE__ ) && is_readable( $bst_file ) ) {
			$bst_fallback = $bst_file;
			break;
		}
	}
	if ( ! $bst_fallback && function_exists( 'WC' ) ) {
		$bst_fallback = WC()->plugin_path() . '/templates/content-product.php';
	}
	if ( $bst_fallback && is_readable( $bst_fallback ) ) {
		include $bst_fallback;
	}
	return;
}

global $product;

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

$bst_link     = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );
$bst_ratio    = bst_choice( bst_mod( 'product_image_ratio' ), array( '1-1', '3-4', '4-3', 'auto' ), '1-1' );
$bst_button   = bst_choice( bst_mod( 'product_button' ), array( 'always', 'hover', 'icon', 'none' ), 'always' );
$bst_catalog  = (bool) bst_mod( 'catalog_mode' );
$bst_image_id = $product->get_image_id();
$bst_gallery  = $product->get_gallery_image_ids();
$bst_alt_id   = ( 'swap' === bst_mod( 'product_hover' ) && $bst_gallery ) ? (int) $bst_gallery[0] : 0;
$bst_title    = get_the_title();

$bst_category = '';
if ( bst_mod( 'product_show_category' ) ) {
	$bst_terms = get_the_terms( $product->get_id(), 'product_cat' );
	if ( $bst_terms && ! is_wp_error( $bst_terms ) ) {
		$bst_category = '<a class="bs-product__cat" href="' . esc_url( get_term_link( $bst_terms[0] ) ) . '">' . esc_html( $bst_terms[0]->name ) . '</a>';
	}
}
?>
<li <?php wc_product_class( 'bs-product', $product ); ?>>
	<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>

	<div class="bs-product__media bs-ratio bs-ratio--<?php echo esc_attr( $bst_ratio ); ?>">
		<a class="bs-product__image" href="<?php echo esc_url( $bst_link ); ?>" tabindex="-1" aria-hidden="true">
			<?php
			echo $bst_image_id
				? wp_get_attachment_image( $bst_image_id, 'woocommerce_thumbnail', false, array( 'class' => 'bs-product__img', 'alt' => $bst_title ) )
				: wc_placeholder_img( 'woocommerce_thumbnail', array( 'class' => 'bs-product__img' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			if ( $bst_alt_id ) {
				echo wp_get_attachment_image( $bst_alt_id, 'woocommerce_thumbnail', false, array( 'class' => 'bs-product__img bs-product__img--alt', 'alt' => '', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</a>
		<?php echo bst_wc_badges_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php if ( 'icon' === $bst_button && ! $bst_catalog ) : ?>
			<div class="bs-product__quick"><?php woocommerce_template_loop_add_to_cart(); ?></div>
		<?php endif; ?>
		<?php do_action( 'bst_product_card_media', $product ); ?>
	</div>

	<div class="bs-product__body">
		<?php echo $bst_category; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<h2 class="woocommerce-loop-product__title bs-product__title"><a href="<?php echo esc_url( $bst_link ); ?>"><?php echo esc_html( $bst_title ); ?></a></h2>

		<?php if ( wc_review_ratings_enabled() && $product->get_rating_count() ) : ?>
			<div class="bs-product__rating">
				<?php echo wc_get_rating_html( $product->get_average_rating() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="bs-product__reviews">(<?php echo (int) $product->get_review_count(); ?>)</span>
			</div>
		<?php endif; ?>

		<?php if ( ! $bst_catalog ) : ?>
			<?php $bst_price = $product->get_price_html(); ?>
			<?php if ( $bst_price ) : ?>
				<div class="price bs-product__price"><?php echo $bst_price; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php endif; ?>
			<?php if ( in_array( $bst_button, array( 'always', 'hover' ), true ) ) : ?>
				<div class="bs-product__actions"><?php woocommerce_template_loop_add_to_cart(); ?></div>
			<?php endif; ?>
		<?php else : ?>
			<a class="bs-product__contact" href="<?php echo esc_url( $bst_link ); ?>"><?php echo esc_html( bst_mod( 'catalog_mode_text' ) ); ?></a>
		<?php endif; ?>

		<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
	</div>
</li>
