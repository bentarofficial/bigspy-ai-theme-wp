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
$bars   = '';
$layout = bst_choice( $a['layout'] ?? 'bar', array( 'bar', 'circle', 'semi', 'steps', 'dashed' ), 'bar' );
$max    = isset( $a['max'] ) && (float) $a['max'] > 0 ? min( 1000000, (float) $a['max'] ) : 100;
$suffix = esc_html( (string) ( $a['suffix'] ?? '%' ) );
$steps  = max( 2, min( 30, (int) ( $a['steps'] ?? 10 ) ) );
$fmt    = function ( $n ) {
	return rtrim( rtrim( number_format( $n, 1, '.', '' ), '0' ), '.' );
};
foreach ( $items as $item ) {
	$raw   = max( 0, min( $max, (float) ( $item['value'] ?? 0 ) ) );
	$value = 100 === (int) $max && 100.0 === (float) $max ? $raw : round( $raw / $max * 100, 2 );
	$label = esc_html( (string) ( $item['label'] ?? '' ) );
	$color = sanitize_hex_color( (string) ( $item['color'] ?? '' ) );
	$num   = ! empty( $a['showValue'] ) ? '<span class="bs-progress__value">' . esc_html( $fmt( $raw ) ) . $suffix . '</span>' : '';
	if ( 'circle' === $layout || 'semi' === $layout ) {
		$semi  = 'semi' === $layout;
		$path  = $semi ? 'M5 50a45 45 0 0 1 90 0' : 'M50 5a45 45 0 1 1 0 90a45 45 0 1 1 0-90';
		$svg   = '<svg class="bs-progress__ring" viewBox="0 0 100 ' . ( $semi ? '55' : '100' ) . '" aria-hidden="true"><path class="bs-progress__ring-track" d="' . $path . '" pathLength="100"/><path class="bs-progress__ring-fill" d="' . $path . '" pathLength="100"' . ( $color ? ' style="stroke:' . esc_attr( $color ) . '"' : '' ) . '/></svg>';
		$bars .= '<div class="bs-progress__item" role="progressbar" aria-valuemin="0" aria-valuemax="' . esc_attr( $max ) . '" aria-valuenow="' . esc_attr( $raw ) . '" aria-label="' . esc_attr( wp_strip_all_tags( $label ) ) . '" style="' . esc_attr( '--v:' . $value ) . '"><div class="bs-progress__dial">' . $svg . $num . '</div><span class="bs-progress__label">' . $label . '</span></div>';
		continue;
	}
	if ( 'steps' === $layout ) {
		$on   = (int) round( $value / 100 * $steps );
		$segs = '';
		for ( $i = 1; $i <= $steps; $i++ ) {
			$segs .= '<span class="bs-progress__step' . ( $i <= $on ? ' is-on' : '' ) . '" style="--i:' . $i . '"></span>';
		}
		$head  = '<div class="bs-progress__head"><span class="bs-progress__label">' . $label . '</span>' . $num . '</div>';
		$bars .= '<div class="bs-progress__item">' . $head . '<div class="bs-progress__steps" role="progressbar" aria-valuemin="0" aria-valuemax="' . esc_attr( $max ) . '" aria-valuenow="' . esc_attr( $raw ) . '" aria-label="' . esc_attr( wp_strip_all_tags( $label ) ) . '"' . ( $color ? ' style="' . esc_attr( '--bs-pg-color:' . $color ) . '"' : '' ) . '>' . $segs . '</div></div>';
		continue;
	}
	$head  = ! empty( $a['labelInside'] ) ? '' : '<div class="bs-progress__head"><span class="bs-progress__label">' . $label . '</span>' . $num . '</div>';
	$in    = ! empty( $a['labelInside'] ) ? '<span class="bs-progress__inside">' . $label . ' ' . wp_strip_all_tags( $num ) . '</span>' : '';
	$below = ! empty( $a['labelBelow'] ) && empty( $a['labelInside'] );
	$track = '<div class="bs-progress__track" role="progressbar" aria-valuemin="0" aria-valuemax="' . esc_attr( $max ) . '" aria-valuenow="' . esc_attr( $raw ) . '" aria-label="' . esc_attr( wp_strip_all_tags( $label ) ) . '"><div class="bs-progress__fill" style="' . esc_attr( '--v:' . $value . '%' . ( $color ? ';--bs-pg-color:' . $color : '' ) ) . '">' . $in . '</div></div>';
	$bars .= '<div class="bs-progress__item">' . ( $below ? $track . $head : $head . $track ) . '</div>';
}
$class = 'bs-progress' . ( 'bar' !== $layout ? ' bs-progress--' . $layout : '' ) . ( ! empty( $a['labelBelow'] ) ? ' has-label-below' : '' ) . ( ! empty( $a['striped'] ) ? ' is-striped' : '' ) . ( ! empty( $a['animate'] ) ? ' is-animated' : '' ) . ( ! empty( $a['labelInside'] ) ? ' has-label-inside' : '' );
$style = bst_style_vars(
	array(
		'--bs-pg-h'      => bst_block_num( $a, 'height', 10, 2, 60 ) . 'px',
		'--bs-pg-radius' => bst_block_num( $a, 'radius', 999, 0, 999 ) . 'px',
		'--bs-pg-color'  => bst_block_color( $a, 'color' ),
		'--bs-pg-track'  => bst_block_color( $a, 'track' ),
		'--bs-pg-color2' => bst_block_color( $a, 'color2' ),
		'--bs-pg-dur'    => ! empty( $a['duration'] ) ? bst_block_num( $a, 'duration', 1200, 100, 10000 ) . 'ms' : '',
		'--bs-pg-size'   => in_array( $layout, array( 'circle', 'semi' ), true ) && 120 !== (int) ( $a['circleSize'] ?? 120 ) ? bst_block_num( $a, 'circleSize', 120, 50, 400 ) . 'px' : '',
	)
);
printf( '<div %1$s data-bs-progress>%2$s</div>', get_block_wrapper_attributes( array( 'class' => $class, 'style' => $style ) ), $bars ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
