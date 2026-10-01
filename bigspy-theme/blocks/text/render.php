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
	)
);

// Plain text typed without a block tag still becomes a paragraph.
if ( ! preg_match( '/^\s*<(p|h[1-6]|ul|ol|blockquote|div|table|figure|pre)\b/i', $html ) ) {
	$html = wpautop( $html );
}

printf(
	'<div %1$s>%2$s</div>',
	get_block_wrapper_attributes( array( 'class' => 'bs-text' . ( $align ? ' has-text-align-' . $align : '' ), 'style' => $style ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	do_shortcode( wp_kses_post( $html ) ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
