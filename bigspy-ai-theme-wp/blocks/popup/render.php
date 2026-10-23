<?php
/**
 * Popup block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a = (array) $attributes;
$id = sanitize_html_class( $a['popupId'] ?? '' );
if ( '' === $id ) {
	$id = 'popup-' . substr( md5( wp_json_encode( $a ) . $content ), 0, 6 );
}
$trigger  = bst_choice( $a['trigger'] ?? 'button', array( 'button', 'link', 'delay', 'scroll', 'exit' ), 'button' );
$size     = bst_choice( $a['size'] ?? 'md', array( 'sm', 'md', 'lg', 'full' ), 'md' );
$position = bst_choice( $a['position'] ?? 'center', array( 'center', 'bottom-right', 'bottom-left', 'side-right', 'top' ), 'center' );
$style    = bst_style_vars( array( '--bs-pop-ov' => ( bst_block_num( $a, 'overlay', 60, 0, 95 ) / 100 ), '--bs-pop-radius' => bst_block_num( $a, 'radius', 16, 0, 40 ) . 'px', '--bs-pop-bg' => bst_block_color( $a, 'bgColor' ) ) );
$data     = array(
	'data-bs-popup'         => $id,
	'data-trigger'          => $trigger,
	'data-delay'            => (string) bst_block_num( $a, 'delay', 5, 0, 120 ),
	'data-scroll'           => (string) bst_block_num( $a, 'scroll', 50, 0, 100 ),
	'data-frequency'        => (string) bst_block_num( $a, 'frequency', 1, 0, 365 ),
	'data-close-outside'    => ! empty( $a['closeOutside'] ) ? '1' : '0',
);
$attrs = '';
foreach ( $data as $k => $v ) {
	$attrs .= ' ' . $k . '="' . esc_attr( $v ) . '"';
}
$button = 'button' === $trigger ? '<button type="button" class="bs-btn bs-popup__open" data-bs-popup-open="' . esc_attr( $id ) . '">' . esc_html( $a['buttonText'] ?? '' ) . '</button>' : '';
printf(
	'%1$s<div %2$s%3$s hidden><div class="bs-popup__overlay" data-bs-popup-close></div><div class="bs-popup__dialog" role="dialog" aria-modal="true" tabindex="-1"><button type="button" class="bs-popup__close" data-bs-popup-close aria-label="%4$s">&times;</button><div class="bs-popup__body">%5$s</div></div></div>',
	$button, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	get_block_wrapper_attributes( array( 'class' => 'bs-popup bs-popup--' . $size . ' bs-popup--' . $position, 'style' => $style, 'id' => $id ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$attrs, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_attr__( 'Close', 'bigspy-ai-theme-wp' ),
	$content // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
