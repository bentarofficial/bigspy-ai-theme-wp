<?php
/**
 * Lottie animation block. The player script loads only on pages that use the block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a   = (array) $attributes;
$src = esc_url( (string) ( $a['src'] ?? '' ) );
if ( '' === $src ) {
	return;
}
wp_enqueue_script( 'bst-lottie-player', 'https://unpkg.com/@lottiefiles/lottie-player@2.0.8/dist/lottie-player.js', array(), '2.0.8', array( 'in_footer' => true, 'strategy' => 'defer' ) );
$align = bst_choice( (string) ( $a['align'] ?? 'center' ), array( 'left', 'center', 'right', 'wide', 'full' ), 'center' );
$label = trim( (string) ( $a['label'] ?? '' ) );
printf(
	'<div %1$s><lottie-player src="%2$s" background="transparent" speed="%3$s"%4$s%5$s%6$s style="width:100%%;max-width:%7$dpx"%8$s></lottie-player></div>',
	get_block_wrapper_attributes( array( 'class' => 'bs-lottie bs-lottie--' . $align ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$src, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_attr( (string) bst_block_num( $a, 'speed', 1, 0.1, 5 ) ),
	! empty( $a['loop'] ) ? ' loop' : '',
	( ! empty( $a['autoplay'] ) && empty( $a['hover'] ) ) ? ' autoplay' : '',
	! empty( $a['hover'] ) ? ' hover' : '',
	(int) bst_block_num( $a, 'width', 320, 40, 1600 ),
	$label ? ' role="img" aria-label="' . esc_attr( $label ) . '"' : ' aria-hidden="true"'
);
