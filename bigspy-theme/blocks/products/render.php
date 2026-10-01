<?php
/**
 * WooCommerce products block.
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
		'type'      => $a['type'] ?? 'latest',
		'cats'      => ! empty( $a['category'] ) ? array( (int) $a['category'] ) : array(),
		'count'     => $a['count'] ?? 8,
		'layout'    => $a['layout'] ?? 'grid',
		'd'         => $a['columns'] ?? 4,
		't'         => $a['columnsTablet'] ?? 3,
		'm'         => $a['columnsMobile'] ?? 2,
		'autoplay'  => (int) ( $a['autoplay'] ?? 0 ) * 1000,
		'load_more' => ! empty( $a['loadMore'] ),
	)
);

if ( '' === $html ) {
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		echo '<p>' . esc_html__( 'No products found.', 'bigspy-theme' ) . '</p>';
	}
	return;
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-products-wrap' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
