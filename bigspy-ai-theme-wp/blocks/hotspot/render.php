<?php
/**
 * Image hotspots block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a = (array) $attributes;
$url = esc_url( $a['imageUrl'] ?? '' );
if ( ! $url ) {
	return;
}
$marker  = bst_choice( $a['marker'] ?? 'pulse', array( 'pulse', 'plus', 'number', 'dot', 'icon' ), 'pulse' );
$trigger = bst_choice( $a['trigger'] ?? 'hover', array( 'hover', 'click' ), 'hover' );
$style   = bst_style_vars( array( '--bs-hs-color' => bst_block_color( $a, 'color' ), '--bs-hs-radius' => bst_block_num( $a, 'radius', 12, 0, 60 ) . 'px', '--bs-hs-tip' => bst_block_num( $a, 'tipWidth', 240, 140, 420 ) . 'px' ) );
$img     = ! empty( $a['imageId'] ) ? wp_get_attachment_image( (int) $a['imageId'], 'full', false, array( 'class' => 'bs-hotspot__img', 'alt' => (string) ( $a['alt'] ?? '' ) ) ) : '';
if ( ! $img ) {
	$img = '<img class="bs-hotspot__img" src="' . $url . '" alt="' . esc_attr( $a['alt'] ?? '' ) . '" loading="lazy">';
}
$points = '';
foreach ( (array) ( $a['points'] ?? array() ) as $i => $p ) {
	$x     = max( 0, min( 100, (float) ( $p['x'] ?? 50 ) ) );
	$y     = max( 0, min( 100, (float) ( $p['y'] ?? 50 ) ) );
	$title = esc_html( $p['title'] ?? '' );
	$text  = wp_kses_post( $p['text'] ?? '' );
	$link  = esc_url( $p['link'] ?? '' );
	$label = 'number' === $marker ? (string) ( $i + 1 ) : ( 'plus' === $marker ? '+' : '' );
	$side  = $x > 60 ? ' is-left' : '';
	$points .= '<div class="bs-hotspot__point' . $side . '" style="left:' . $x . '%;top:' . $y . '%">'
		. '<button type="button" class="bs-hotspot__marker" aria-expanded="false" aria-label="' . esc_attr( $p['title'] ?? sprintf( 'Point %d', $i + 1 ) ) . '"><span>' . esc_html( $label ) . '</span></button>'
		. '<div class="bs-hotspot__tip" role="tooltip">' . ( $title ? '<strong>' . $title . '</strong>' : '' ) . ( $text ? '<p>' . $text . '</p>' : '' ) . ( $link ? '<a href="' . $link . '">' . esc_html( $p['linkText'] ?? __( 'View', 'bigspy-ai-theme-wp' ) ) . ' →</a>' : '' ) . '</div></div>';
}
printf( '<div %1$s data-bs-hotspot="%2$s">%3$s%4$s</div>', get_block_wrapper_attributes( array( 'class' => 'bs-hotspot bs-hotspot--' . $marker . ' bs-hotspot--' . $trigger, 'style' => $style ) ), esc_attr( $trigger ), $img, $points ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
