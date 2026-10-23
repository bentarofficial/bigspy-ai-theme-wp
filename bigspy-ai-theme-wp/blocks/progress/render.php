<?php
/**
 * Progress bars block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a = (array) $attributes;
$items = array_filter( (array) ( $a['items'] ?? array() ), 'is_array' );
if ( ! $items ) {
	return;
}
$bars = '';
foreach ( $items as $item ) {
	$value = max( 0, min( 100, (float) ( $item['value'] ?? 0 ) ) );
	$label = esc_html( (string) ( $item['label'] ?? '' ) );
	$color = sanitize_hex_color( (string) ( $item['color'] ?? '' ) );
	$num   = ! empty( $a['showValue'] ) ? '<span class="bs-progress__value">' . esc_html( rtrim( rtrim( number_format( $value, 1, '.', '' ), '0' ), '.' ) ) . '%</span>' : '';
	$head  = ! empty( $a['labelInside'] ) ? '' : '<div class="bs-progress__head"><span class="bs-progress__label">' . $label . '</span>' . $num . '</div>';
	$in    = ! empty( $a['labelInside'] ) ? '<span class="bs-progress__inside">' . $label . ' ' . wp_strip_all_tags( $num ) . '</span>' : '';
	$bars .= '<div class="bs-progress__item">' . $head . '<div class="bs-progress__track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' . esc_attr( $value ) . '" aria-label="' . esc_attr( wp_strip_all_tags( $label ) ) . '"><div class="bs-progress__fill" style="' . esc_attr( '--v:' . $value . '%' . ( $color ? ';--bs-pg-color:' . $color : '' ) ) . '">' . $in . '</div></div></div>';
}
$class = 'bs-progress' . ( ! empty( $a['striped'] ) ? ' is-striped' : '' ) . ( ! empty( $a['animate'] ) ? ' is-animated' : '' ) . ( ! empty( $a['labelInside'] ) ? ' has-label-inside' : '' );
$style = bst_style_vars(
	array(
		'--bs-pg-h'      => bst_block_num( $a, 'height', 10, 2, 60 ) . 'px',
		'--bs-pg-radius' => bst_block_num( $a, 'radius', 999, 0, 999 ) . 'px',
		'--bs-pg-color'  => bst_block_color( $a, 'color' ),
		'--bs-pg-track'  => bst_block_color( $a, 'track' ),
	)
);
printf( '<div %1$s data-bs-progress>%2$s</div>', get_block_wrapper_attributes( array( 'class' => $class, 'style' => $style ) ), $bars ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
