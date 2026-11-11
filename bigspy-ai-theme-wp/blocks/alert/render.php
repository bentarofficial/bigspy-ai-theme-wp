<?php
/**
 * Message box block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a = (array) $attributes;
$type  = bst_choice( $a['type'] ?? 'info', array( 'info', 'success', 'warning', 'error', 'custom' ), 'info' );
$icons = array( 'info' => 'lightbulb', 'success' => 'check', 'warning' => 'shield', 'error' => 'close', 'custom' => 'message' );
$title = wp_kses_post( (string) ( $a['title'] ?? '' ) );
$text  = wp_kses_post( (string) ( $a['text'] ?? '' ) );
if ( '' === $title && '' === $text ) {
	return;
}
$class = 'bs-alert bs-alert--' . $type . ' bs-alert--' . bst_choice( $a['style'] ?? 'soft', array( 'soft', 'solid', 'outline', 'left-bar', 'gradient', 'glass', 'bar', 'toast' ), 'soft' );
$style = bst_style_vars(
	array(
		'--bs-alert-bg'     => bst_block_color( $a, 'bgColor' ),
		'--bs-alert-color'  => bst_block_color( $a, 'textColor' ),
		'--bs-alert-accent' => bst_block_color( $a, 'accent' ),
		'--bs-alert-radius' => isset( $a['radius'] ) && $a['radius'] >= 0 ? min( 60, (int) $a['radius'] ) . 'px' : '',
		'--bs-alert-bw'     => isset( $a['borderWidth'] ) && $a['borderWidth'] >= 0 ? min( 10, (int) $a['borderWidth'] ) . 'px' : '',
		'--bs-alert-is'     => ! empty( $a['iconSize'] ) ? max( 12, min( 64, (int) $a['iconSize'] ) ) . 'px' : '',
	)
);
if ( false !== strpos( $class, 'bs-alert--toast' ) ) {
	$class .= ' bs-alert--at-' . bst_choice( $a['toastPos'] ?? 'bottom-right', array( 'bottom-right', 'bottom-left', 'top-right', 'top-left' ), 'bottom-right' );
}
$icon_name = ! empty( $a['icon'] ) ? sanitize_key( $a['icon'] ) : $icons[ $type ];
$ttag      = bst_choice( $a['titleTag'] ?? 'p', array( 'p', 'h2', 'h3', 'h4', 'h5', 'strong' ), 'p' );
$days      = ! empty( $a['dismiss'] ) ? max( 0, min( 365, (int) ( $a['rememberDays'] ?? 0 ) ) ) : 0;
$wrap      = array( 'class' => $class, 'style' => $style );
if ( $days ) {
	$wrap['data-bs-alert-key']  = substr( md5( $title . '|' . $text ), 0, 10 );
	$wrap['data-bs-alert-days'] = $days;
}
$btn_text = trim( (string) ( $a['btnText'] ?? '' ) );
$btn      = '' !== $btn_text ? '<a class="bs-alert__btn" href="' . esc_url( (string) ( $a['btnUrl'] ?? '' ) ? $a['btnUrl'] : '#' ) . '">' . esc_html( $btn_text ) . '</a>' : '';
$role = in_array( $type, array( 'warning', 'error' ), true ) ? 'alert' : 'note';
printf(
	'<div %1$s role="%2$s">%3$s<div class="bs-alert__body">%4$s%5$s</div>%6$s</div>',
	get_block_wrapper_attributes( $wrap ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_attr( $role ),
	! empty( $a['showIcon'] ) ? '<span class="bs-alert__icon">' . bst_icon( $icon_name, 22 ) . '</span>' : '', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$title ? '<' . $ttag . ' class="bs-alert__title">' . $title . '</' . $ttag . '>' : '', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	( $text ? '<div class="bs-alert__text">' . wpautop( $text ) . '</div>' : '' ) . $btn, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	! empty( $a['dismiss'] ) ? '<button type="button" class="bs-alert__close" aria-label="' . esc_attr__( 'Close', 'bigspy-ai-theme-wp' ) . '" onclick="this.parentNode.remove()">' . bst_icon( 'close', 16 ) . '</button>' : '' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
