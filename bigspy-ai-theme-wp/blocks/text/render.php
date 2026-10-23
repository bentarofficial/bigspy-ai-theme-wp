<?php
/**
 * Text block: rich HTML content (paragraphs, headings, lists, links…).
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a    = (array) $attributes;
$html = trim( (string) ( $a['html'] ?? '' ) );
if ( '' === $html ) {
	return;
}

$align = bst_choice( $a['align'] ?? '', array( '', 'left', 'center', 'right', 'justify' ), '' );
$style = bst_style_vars(
	array(
		'--bs-text-size'  => ! empty( $a['size'] ) ? bst_block_num( $a, 'size', 0, 10, 80 ) . 'px' : '',
		'--bs-text-color' => bst_block_color( $a, 'color' ),
		'--bs-text-max'   => ! empty( $a['maxWidth'] ) ? bst_block_num( $a, 'maxWidth', 0, 100, 2000 ) . 'px' : '',
		'--bs-text-size-t' => ! empty( $a['sizeTablet'] ) ? bst_block_num( $a, 'sizeTablet', 0, 10, 80 ) . 'px' : '',
		'--bs-text-size-m' => ! empty( $a['sizeMobile'] ) ? bst_block_num( $a, 'sizeMobile', 0, 10, 80 ) . 'px' : '',
		'--bs-text-lh'     => ! empty( $a['lineHeight'] ) ? min( 3, max( 0.8, (float) $a['lineHeight'] ) ) : '',
		'--bs-text-ls'     => ! empty( $a['letterSpacing'] ) ? min( 10, max( -2, (float) $a['letterSpacing'] ) ) . 'px' : '',
		'--bs-text-weight' => in_array( (string) ( $a['weight'] ?? '' ), array( '300', '400', '500', '600', '700', '800' ), true ) ? $a['weight'] : '',
		'--bs-text-transform' => bst_choice( $a['transform'] ?? '', array( '', 'uppercase', 'lowercase', 'capitalize' ), '' ),
		'--bs-text-link'   => bst_block_color( $a, 'linkColor' ),
		'--bs-text-cols'   => ! empty( $a['columns'] ) && $a['columns'] > 1 ? bst_block_num( $a, 'columns', 1, 1, 4 ) : '',
	)
);
$extra = ( ! empty( $a['columns'] ) && $a['columns'] > 1 ? ' has-columns' . ( ! empty( $a['columnsMobile'] ) ? ' keep-columns' : '' ) : '' ) . ( ! empty( $a['dropCap'] ) ? ' has-drop-cap' : '' );

// Plain text typed without a block tag still becomes a paragraph.
if ( ! preg_match( '/^\s*<(p|h[1-6]|ul|ol|blockquote|div|table|figure|pre)\b/i', $html ) ) {
	$html = wpautop( $html );
}

printf(
	'<div %1$s>%2$s</div>',
	get_block_wrapper_attributes( array( 'class' => 'bs-text' . ( $align ? ' has-text-align-' . $align : '' ) . $extra, 'style' => $style ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	do_shortcode( wp_kses_post( $html ) ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
