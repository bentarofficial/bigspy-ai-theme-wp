<?php
/**
 * Logo showcase block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a = (array) $attributes;
$images = array_filter( (array) ( $a['images'] ?? array() ), function ( $i ) {
	return ! empty( $i['url'] );
} );
if ( ! $images ) {
	return;
}
$layout = bst_choice( $a['layout'] ?? 'grid', array( 'grid', 'marquee' ), 'grid' );
$style  = bst_style_vars( array( '--bs-logo-cols' => bst_block_num( $a, 'columns', 5, 1, 8 ), '--bs-logo-cols-m' => bst_block_num( $a, 'columnsMobile', 3, 1, 4 ), '--bs-logo-h' => bst_block_num( $a, 'height', 56, 16, 200 ) . 'px', '--bs-logo-gap' => bst_block_num( $a, 'gap', 32, 0, 120 ) . 'px', '--bs-logo-speed' => bst_block_num( $a, 'speed', 30, 5, 120 ) . 's' ) );
$items  = '';
foreach ( $images as $img ) {
	$tag    = '<img src="' . esc_url( $img['url'] ) . '" alt="' . esc_attr( $img['alt'] ?? '' ) . '" loading="lazy">';
	$link   = esc_url( $img['link'] ?? '' );
	$items .= '<li class="bs-logos__item">' . ( $link ? '<a href="' . $link . '" target="_blank" rel="noopener">' . $tag . '</a>' : $tag ) . '</li>';
}
$track = '<ul class="bs-logos__track">' . $items . ( 'marquee' === $layout ? str_replace( '<li class="bs-logos__item">', '<li class="bs-logos__item" aria-hidden="true">', $items ) : '' ) . '</ul>';
$class = 'bs-logos bs-logos--' . $layout . ( ! empty( $a['grayscale'] ) ? ' is-gray' : '' ) . ( ! empty( $a['boxed'] ) ? ' is-boxed' : '' );
printf( '<div %1$s>%2$s</div>', get_block_wrapper_attributes( array( 'class' => $class, 'style' => $style ) ), $track ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
