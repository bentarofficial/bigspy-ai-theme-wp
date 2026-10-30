<?php
/** Line Draw: an SVG path drawn by motion-fx.js on view or with scroll; static when motion is reduced. */
defined( 'ABSPATH' ) || exit;
$a      = $attributes;
$shapes = array(
	'wave'      => 'M5 60 C 55 10, 105 10, 155 60 S 255 110, 305 60 S 380 10, 395 40',
	'curve'     => 'M5 90 C 120 90, 140 10, 395 10',
	'arrow'     => 'M5 80 C 120 80, 220 70, 380 25 M350 15 L382 24 L366 52',
	'loop'      => 'M5 70 C 80 70, 120 20, 170 30 C 230 42, 200 100, 160 80 C 120 60, 200 20, 260 40 C 320 60, 340 70, 395 60',
	'zigzag'    => 'M5 70 L55 30 L105 70 L155 30 L205 70 L255 30 L305 70 L355 30 L395 60',
	'underline' => 'M10 60 C 120 40, 280 40, 390 55',
);
$shape = bst_choice( $a['shape'] ?? 'wave', array_merge( array_keys( $shapes ), array( 'custom' ) ), 'wave' );
$path  = 'custom' === $shape ? preg_replace( '/[^MmLlHhVvCcSsQqTtAaZz0-9.,\s-]/', '', (string) ( $a['path'] ?? '' ) ) : $shapes[ $shape ];
if ( '' === trim( (string) $path ) ) {
	return;
}
$style = bst_style_vars( array( '--bs-line-color' => bst_block_color( $a, 'color' ), '--bs-line-h' => bst_block_num( $a, 'height', 120, 20, 600 ) . 'px', '--bs-line-dur' => bst_block_num( $a, 'duration', 1.6, 0.3, 6 ) . 's' ) );
$mode  = bst_choice( $a['mode'] ?? 'view', array( 'view', 'scroll' ), 'view' );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-line' . ( ! empty( $a['flip'] ) ? ' bs-line--flip' : '' ), 'style' => $style, 'data-bs-line' => $mode, 'aria-hidden' => 'true' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><svg viewBox="0 0 400 100" preserveAspectRatio="none" focusable="false"><path d="<?php echo esc_attr( $path ); ?>" pathLength="1" fill="none" stroke="currentColor" stroke-width="<?php echo esc_attr( bst_block_num( $a, 'width', 3, 1, 20 ) ); ?>" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke"/></svg></div>
