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
$class = 'bs-alert bs-alert--' . $type . ' bs-alert--' . bst_choice( $a['style'] ?? 'soft', array( 'soft', 'solid', 'outline', 'left-bar' ), 'soft' );
$style = bst_style_vars(
	array(
		'--bs-alert-bg'     => bst_block_color( $a, 'bgColor' ),
		'--bs-alert-color'  => bst_block_color( $a, 'textColor' ),
		'--bs-alert-accent' => bst_block_color( $a, 'accent' ),
	)
);
$role = in_array( $type, array( 'warning', 'error' ), true ) ? 'alert' : 'note';
printf(
	'<div %1$s role="%2$s">%3$s<div class="bs-alert__body">%4$s%5$s</div>%6$s</div>',
	get_block_wrapper_attributes( array( 'class' => $class, 'style' => $style ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_attr( $role ),
	! empty( $a['showIcon'] ) ? '<span class="bs-alert__icon">' . bst_icon( $icons[ $type ], 22 ) . '</span>' : '', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$title ? '<p class="bs-alert__title">' . $title . '</p>' : '', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$text ? '<div class="bs-alert__text">' . wpautop( $text ) . '</div>' : '', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	! empty( $a['dismiss'] ) ? '<button type="button" class="bs-alert__close" aria-label="' . esc_attr__( 'Close', 'bigspy-ai-theme-wp' ) . '" onclick="this.parentNode.remove()">' . bst_icon( 'close', 16 ) . '</button>' : '' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
