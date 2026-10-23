<?php
/**
 * Price table block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a = (array) $attributes;
$style    = bst_choice( $a['style'] ?? 'card', array( 'card', 'bordered', 'gradient', 'minimal' ), 'card' );
$featured = ! empty( $a['featured'] );
$lines    = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) ( $a['features'] ?? '' ) ) ) );
$list     = '';
foreach ( $lines as $line ) {
	$off   = 0 === strpos( $line, '-' );
	$list .= '<li class="' . ( $off ? 'is-off' : 'is-on' ) . '">' . bst_icon( $off ? 'close' : 'check', 16 ) . '<span>' . esc_html( ltrim( $line, '-+ ' ) ) . '</span></li>';
}
printf(
	'<div %1$s>%2$s<h3 class="bs-price__title">%3$s</h3>%4$s<div class="bs-price__amount">%5$s<span class="bs-price__value">%6$s</span><span class="bs-price__period">%7$s</span></div>%8$s%9$s</div>',
	get_block_wrapper_attributes( array( 'class' => 'bs-price bs-price--' . $style . ( $featured ? ' is-featured' : '' ) . ' bs-price--align-' . bst_choice( $a['align'] ?? 'center', array( 'left', 'center' ), 'center' ), 'style' => bst_style_vars( array( '--bs-price-accent' => bst_block_color( $a, 'accent' ) ) ) ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$featured && ! empty( $a['badge'] ) ? '<span class="bs-price__badge">' . esc_html( $a['badge'] ) . '</span>' : '',
	esc_html( (string) ( $a['title'] ?? '' ) ),
	! empty( $a['description'] ) ? '<p class="bs-price__desc">' . esc_html( $a['description'] ) . '</p>' : '',
	! empty( $a['oldPrice'] ) ? '<del class="bs-price__old">' . esc_html( $a['oldPrice'] ) . '</del>' : '',
	esc_html( (string) ( $a['price'] ?? '' ) ),
	esc_html( (string) ( $a['period'] ?? '' ) ),
	$list ? '<ul class="bs-price__features">' . $list . '</ul>' : '', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	! empty( $a['buttonText'] ) ? '<a class="bs-btn bs-price__btn' . ( $featured ? '' : ' bs-btn--outline' ) . '" href="' . esc_url( $a['buttonUrl'] ?? '#' ) . '">' . esc_html( $a['buttonText'] ) . '</a>' : ''
);
