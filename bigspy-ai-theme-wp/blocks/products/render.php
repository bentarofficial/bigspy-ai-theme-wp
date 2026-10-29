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
		'tags'      => ! empty( $a['tag'] ) ? array( (int) $a['tag'] ) : array(),
		'ids'       => (string) ( $a['ids'] ?? '' ),
		'offset'    => (int) ( $a['offset'] ?? 0 ),
		'count'     => $a['count'] ?? 8,
		'orderby'   => $a['orderBy'] ?? '',
		'layout'    => $a['layout'] ?? 'grid',
		'd'         => $a['columns'] ?? 4,
		't'         => $a['columnsTablet'] ?? 3,
		'm'         => $a['columnsMobile'] ?? 2,
		'autoplay'  => (int) ( $a['autoplay'] ?? 0 ) * 1000,
		'load_more' => ! empty( $a['loadMore'] ) || 'infinite' === ( $a['pagination'] ?? '' ),
		'infinite'  => 'infinite' === ( $a['pagination'] ?? '' ),
		'ratio'     => $a['imageRatio'] ?? '',
		'hover'     => $a['imageHover'] ?? '',
		'stock'     => $a['stock'] ?? 'include',
	)
);

if ( '' === $html ) {
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		echo '<p>' . esc_html__( 'No products found.', 'bigspy-ai-theme-wp' ) . '</p>';
	}
	return;
}
?>
<?php
$classes = array( 'bs-products-wrap' );
$card    = bst_choice( $a['cardStyle'] ?? '', array( '', 'bordered', 'shadow', 'boxed', 'minimal', 'colorful' ), '' );
if ( $card ) {
	$classes[] = 'bs-pcard--' . $card;
}
if ( 'center' === ( $a['align'] ?? '' ) ) {
	$classes[] = 'bs-pcard--center';
}
foreach ( array( 'showCat' => 'cat', 'showTitle' => 'title', 'showRating' => 'rating', 'showPrice' => 'price', 'showButton' => 'button', 'showBadge' => 'badge' ) as $key => $part ) {
	if ( isset( $a[ $key ] ) && ! $a[ $key ] ) {
		$classes[] = 'bs-pcard--no-' . $part;
	}
}
$vars = bst_style_vars(
	array(
		'--bs-pc-gap'    => isset( $a['gap'] ) && $a['gap'] >= 0 ? bst_block_num( $a, 'gap', 20, 0, 60 ) . 'px' : '',
		'--bs-pc-lines'  => ! empty( $a['titleLines'] ) ? bst_block_num( $a, 'titleLines', 2, 1, 4 ) : '',
		'--bs-pc-price'  => bst_block_color( $a, 'priceColor' ),
		'--bs-pc-badge'  => bst_block_color( $a, 'badgeColor' ),
		'--bs-pc-btn'    => bst_block_color( $a, 'buttonColor' ),
		'--bs-pc-bg'     => bst_block_color( $a, 'cardBg' ),
	)
);
if ( ! empty( $a['titleLines'] ) ) {
	$classes[] = 'bs-pcard--clamp';
}
$hover = bst_choice( $a['imageHover'] ?? '', array( '', 'none', 'zoom', 'swap', 'fade', 'gray', 'lift' ), '' );
if ( $hover ) {
	$classes[] = 'bs-pcard-hover--' . $hover;
}
if ( ! empty( $a['equalize'] ) ) {
	$classes[] = 'bs-pcard--equal';
}
$size = bst_choice( $a['textSize'] ?? '', array( '', 'xs', 's', 'm', 'l', 'xl' ), '' );
if ( $size ) {
	$classes[] = 'bs-pcard--text-' . $size;
}
// Shadow levels are classes: style attributes lose values with parentheses such as rgba().
$depth = isset( $a['depth'] ) ? (int) bst_block_num( $a, 'depth', 0, 0, 5 ) : 0;
$lift  = isset( $a['depthHover'] ) ? (int) bst_block_num( $a, 'depthHover', 0, 0, 5 ) : 0;
if ( $depth || $lift ) {
	$classes[] = 'bs-pcard--depth bs-pcard--d' . $depth . ' bs-pcard--dh' . ( $lift ? $lift : $depth );
}
$radius = isset( $a['imageRadius'] ) && $a['imageRadius'] >= 0 ? bst_block_num( $a, 'imageRadius', 0, 0, 40 ) . 'px' : '';
$vars   = trim( $vars . ( '' !== $vars && '' !== $radius ? ';' : '' ) . ( '' !== $radius ? '--bs-pc-radius:' . $radius : '' ), ';' );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ), 'style' => $vars ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
