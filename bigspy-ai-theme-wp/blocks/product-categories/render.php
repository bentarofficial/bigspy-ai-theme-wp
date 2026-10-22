<?php
/**
 * WooCommerce product categories block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bst_product_categories_html' ) ) {
	return;
}

$a    = (array) $attributes;
$html = bst_product_categories_html(
	array(
		'count'      => $a['count'] ?? 6,
		'parent'     => $a['parent'] ?? 0,
		'top_level'  => ! empty( $a['topLevelOnly'] ),
		'hide_empty' => ! empty( $a['hideEmpty'] ),
		'orderby'    => $a['orderBy'] ?? 'menu_order',
		'style'      => $a['style'] ?? 'overlay',
		'ratio'      => $a['ratio'] ?? '1-1',
		'show_count' => ! empty( $a['showCount'] ),
		'layout'     => $a['layout'] ?? 'grid',
		'd'          => $a['columns'] ?? 6,
		't'          => $a['columnsTablet'] ?? 3,
		'm'          => $a['columnsMobile'] ?? 2,
		'autoplay'   => (int) ( $a['autoplay'] ?? 0 ) * 1000,
	)
);

if ( '' === $html ) {
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		echo '<p>' . esc_html__( 'No product categories found.', 'bigspy-ai-theme-wp' ) . '</p>';
	}
	return;
}
?>
<?php
$vars = bst_style_vars(
	array(
		'--bs-cat-accent' => bst_block_color( $a, 'accent' ),
		'--bs-cat-name'   => ! empty( $a['nameSize'] ) ? bst_block_num( $a, 'nameSize', 15, 10, 40 ) . 'px' : '',
	)
);
$hover = bst_choice( $a['hover'] ?? 'zoom', array( 'zoom', 'lift', 'none' ), 'zoom' );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-cats-hover--' . $hover, 'style' => $vars ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
