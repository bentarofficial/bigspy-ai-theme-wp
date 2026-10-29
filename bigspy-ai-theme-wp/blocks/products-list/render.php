<?php
/**
 * Compact products list (small image, name, rating, price) for sidebars and footers.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bst_products_html' ) ) {
	return;
}

$a    = (array) $attributes;
$html = bst_products_html(
	array(
		'type'   => $a['type'] ?? 'best_selling',
		'cats'   => ! empty( $a['category'] ) ? array( (int) $a['category'] ) : array(),
		'count'  => $a['count'] ?? 4,
		'layout' => 'list',
		'stock'  => $a['stock'] ?? 'include',
	)
);

if ( '' === $html ) {
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		echo '<p>' . esc_html__( 'No products found.', 'bigspy-ai-theme-wp' ) . '</p>';
	}
	return;
}
$heading = trim( (string) ( $a['heading'] ?? '' ) );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-products-list-block' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( '' !== $heading ) : ?>
		<p class="bs-products-list-block__title"><?php echo esc_html( $heading ); ?></p>
	<?php endif; ?>
	<?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
