<?php
/**
 * Scrolling text (marquee) block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a     = (array) $attributes;
$items = array_filter( array_map( 'trim', explode( "\n", (string) ( $a['items'] ?? '' ) ) ) );
if ( ! $items ) {
	return;
}
$sep  = sanitize_key( (string) ( $a['separator'] ?? 'star' ) );
$sep  = 'none' === $sep ? '' : ( 'dot' === $sep ? '<span class="bs-marquee__dot" aria-hidden="true"></span>' : '<span class="bs-marquee__sep" aria-hidden="true">' . bst_icon( $sep, 18 ) . '</span>' );
$row  = '';
foreach ( $items as $item ) {
	$row .= '<span class="bs-marquee__item">' . wp_kses_post( $item ) . '</span>' . $sep;
}
$dir   = 'right' === ( $a['direction'] ?? 'left' ) ? 'right' : 'left';
$class = 'bs-marquee bs-marquee--' . $dir . ( ! empty( $a['pauseHover'] ) ? ' bs-marquee--pause' : '' ) . ( ! empty( $a['outline'] ) ? ' bs-marquee--outline' : '' );
$style = bst_style_vars(
	array(
		'--bs-mq-speed' => bst_block_num( $a, 'speed', 30, 5, 200 ) . 's',
		'--bs-mq-size'  => bst_block_num( $a, 'size', 28, 10, 160 ) . 'px',
		'--bs-mq-gap'   => bst_block_num( $a, 'gap', 40, 0, 200 ) . 'px',
		'--bs-mq-py'    => bst_block_num( $a, 'paddingY', 18, 0, 120 ) . 'px',
		'--bs-mq-color' => bst_block_color( $a, 'color' ),
		'--bs-mq-bg'    => bst_block_color( $a, 'bgColor' ),
		'--bs-mq-weight' => bst_choice( (string) ( $a['weight'] ?? '700' ), array( '400', '500', '600', '700', '800', '900' ), '700' ),
	)
);
// Two identical tracks make the loop seamless; the copy is hidden from screen readers.
printf(
	'<div %1$s><div class="bs-marquee__track">%2$s</div><div class="bs-marquee__track" aria-hidden="true">%2$s</div></div>',
	get_block_wrapper_attributes( array( 'class' => $class, 'style' => $style ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$row // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
