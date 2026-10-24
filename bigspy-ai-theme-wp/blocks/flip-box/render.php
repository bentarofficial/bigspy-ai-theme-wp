<?php
/**
 * Flip box block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a     = (array) $attributes;
$dir   = 'vertical' === ( $a['direction'] ?? '' ) ? 'vertical' : 'horizontal';
$align = bst_choice( (string) ( $a['align'] ?? 'center' ), array( 'left', 'center', 'right' ), 'center' );
$image = '';
if ( ! empty( $a['frontImageId'] ) ) {
	$image = (string) wp_get_attachment_image_url( (int) $a['frontImageId'], 'large' );
}
if ( ! $image && ! empty( $a['frontImageUrl'] ) ) {
	$image = (string) $a['frontImageUrl'];
}
$style = bst_style_vars(
	array(
		'--bs-flip-h'      => bst_block_num( $a, 'height', 300, 120, 900 ) . 'px',
		'--bs-flip-radius' => bst_block_num( $a, 'radius', 16, 0, 60 ) . 'px',
		'--bs-flip-fbg'    => bst_block_color( $a, 'frontBg' ),
		'--bs-flip-fc'     => bst_block_color( $a, 'frontColor' ),
		'--bs-flip-bbg'    => bst_block_color( $a, 'backBg' ),
		'--bs-flip-bc'     => bst_block_color( $a, 'backColor' ),
	)
);
$icon   = ! empty( $a['icon'] ) ? '<span class="bs-flip__icon">' . bst_icon( sanitize_key( $a['icon'] ), 40 ) . '</span>' : '';
$button = ( ! empty( $a['buttonText'] ) && ! empty( $a['buttonUrl'] ) ) ? '<a class="bs-flip__btn" href="' . esc_url( $a['buttonUrl'] ) . '">' . esc_html( $a['buttonText'] ) . '</a>' : '';
printf(
	'<div %1$s tabindex="0"><div class="bs-flip__inner"><div class="bs-flip__face bs-flip__front"%2$s>%3$s%4$s%5$s</div><div class="bs-flip__face bs-flip__back">%6$s%7$s%8$s</div></div></div>',
	get_block_wrapper_attributes( array( 'class' => 'bs-flip bs-flip--' . $dir . ' has-text-align-' . $align . ( $image ? ' has-image' : '' ), 'style' => $style ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$image ? ' style="background-image:url(' . esc_url( $image ) . ')"' : '',
	$icon, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	! empty( $a['frontTitle'] ) ? '<h3 class="bs-flip__title">' . wp_kses_post( $a['frontTitle'] ) . '</h3>' : '',
	! empty( $a['frontText'] ) ? '<p class="bs-flip__text">' . wp_kses_post( $a['frontText'] ) . '</p>' : '',
	! empty( $a['backTitle'] ) ? '<h3 class="bs-flip__title">' . wp_kses_post( $a['backTitle'] ) . '</h3>' : '',
	! empty( $a['backText'] ) ? '<p class="bs-flip__text">' . wp_kses_post( $a['backText'] ) . '</p>' : '',
	$button // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
