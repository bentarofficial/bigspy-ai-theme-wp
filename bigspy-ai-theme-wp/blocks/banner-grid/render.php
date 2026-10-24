<?php
/**
 * Banner grid block: image banners in a magazine-style grid.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a     = (array) $attributes;
$items = array_filter( (array) ( $a['items'] ?? array() ), 'is_array' );
if ( ! $items ) {
	return;
}
$layout = bst_choice( (string) ( $a['layout'] ?? '1-2' ), array( '1-2', '2-1', '1-3', '2x2', 'row', 'mosaic' ), '1-2' );
$cells  = '';
foreach ( $items as $i => $it ) {
	$url = ! empty( $it['imageId'] ) ? (string) wp_get_attachment_image_url( (int) $it['imageId'], 'large' ) : '';
	$url = $url ? $url : (string) ( $it['imageUrl'] ?? '' );
	$tag = ! empty( $it['link'] ) ? 'a' : 'div';
	$cells .= '<' . $tag . ' class="bs-bgrid__item bs-bgrid__item--' . ( $i + 1 ) . '"' . ( 'a' === $tag ? ' href="' . esc_url( $it['link'] ) . '"' : '' ) . '>'
		. ( $url ? '<span class="bs-bgrid__bg" style="background-image:url(' . esc_url( $url ) . ')"></span>' : '<span class="bs-bgrid__bg bs-bgrid__bg--empty"></span>' )
		. '<span class="bs-bgrid__text">'
		. ( ! empty( $it['subtitle'] ) ? '<span class="bs-bgrid__sub">' . esc_html( $it['subtitle'] ) . '</span>' : '' )
		. ( ! empty( $it['title'] ) ? '<span class="bs-bgrid__title">' . esc_html( $it['title'] ) . '</span>' : '' )
		. '</span></' . $tag . '>';
}
$style = bst_style_vars(
	array(
		'--bs-bg-h'       => bst_block_num( $a, 'height', 460, 160, 1200 ) . 'px',
		'--bs-bg-gap'     => bst_block_num( $a, 'gap', 12, 0, 60 ) . 'px',
		'--bs-bg-radius'  => bst_block_num( $a, 'radius', 14, 0, 60 ) . 'px',
		'--bs-bg-overlay' => round( bst_block_num( $a, 'overlay', 35, 0, 90 ) / 100, 2 ),
	)
);
printf(
	'<div %1$s>%2$s</div>',
	get_block_wrapper_attributes( array( 'class' => 'bs-bgrid bs-bgrid--' . $layout . ' bs-bgrid--hover-' . bst_choice( (string) ( $a['hover'] ?? 'zoom' ), array( 'zoom', 'lift', 'none' ), 'zoom' ), 'style' => $style ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$cells // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
