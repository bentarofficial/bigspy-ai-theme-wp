<?php
/**
 * Button block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a    = (array) $attributes;
$text = wp_kses_post( (string) ( $a['text'] ?? '' ) );
if ( '' === trim( wp_strip_all_tags( $text ) ) ) {
	return;
}

$style = bst_choice( $a['style'] ?? 'primary', array( 'primary', 'secondary', 'outline', 'white', 'link' ), 'primary' );
$size  = bst_choice( $a['size'] ?? 'md', array( 'sm', 'md', 'lg' ), 'md' );
$url   = trim( (string) ( $a['url'] ?? '' ) );
$icon  = ! empty( $a['icon'] ) ? bst_icon( sanitize_key( $a['icon'] ), 'lg' === $size ? 20 : 17 ) : '';
$after = 'before' !== ( $a['iconPosition'] ?? 'after' );

$classes = 'bs-btn bs-btn--' . $style . ( 'md' !== $size ? ' bs-btn--' . $size : '' ) . ( ! empty( $a['fullWidth'] ) ? ' bs-btn--block' : '' );
$css     = isset( $a['radius'] ) && $a['radius'] >= 0 ? 'border-radius:' . bst_block_num( $a, 'radius', 0, 0, 100 ) . 'px' : '';
$label   = ( $icon && ! $after ? $icon : '' ) . '<span>' . $text . '</span>' . ( $icon && $after ? $icon : '' );
$extra   = array( 'class' => $classes, 'style' => $css );

if ( '' === $url ) {
	printf( '<span %1$s>%2$s</span>', get_block_wrapper_attributes( $extra ), $label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}

printf(
	'<a %1$s href="%2$s"%3$s>%4$s</a>',
	get_block_wrapper_attributes( $extra ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_url( $url ),
	! empty( $a['newTab'] ) ? ' target="_blank" rel="noopener"' : '',
	$label // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
