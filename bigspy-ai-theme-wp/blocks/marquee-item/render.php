<?php
/**
 * Marquee item block: text with an optional icon or image, link and color.
 * The parent (blocks/marquee/render.php) places the items, separators and rows.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a    = (array) $attributes;
$text = trim( wp_kses_post( (string) ( $a['text'] ?? '' ) ) );

$media = '';
$id    = absint( $a['imageId'] ?? 0 );
$alt   = (string) ( $a['imageAlt'] ?? '' );
if ( $id ) {
	// Eager: lazy images inside a moving strip can enter the screen before they load.
	$img_attr = array( 'class' => 'bs-marquee__img', 'loading' => 'eager', 'decoding' => 'async' );
	if ( '' !== $alt ) {
		$img_attr['alt'] = $alt;
	}
	$media = wp_get_attachment_image( $id, 'medium', false, $img_attr );
}
if ( '' === $media && ! empty( $a['imageUrl'] ) ) {
	$media = sprintf( '<img class="bs-marquee__img" src="%s" alt="%s" decoding="async" />', esc_url( (string) $a['imageUrl'] ), esc_attr( $alt ) );
}
if ( '' === $media && ! empty( $a['icon'] ) ) {
	$icon = bst_icon( sanitize_key( (string) $a['icon'] ), 24 );
	if ( $icon ) {
		$media = '<span class="bs-marquee__icon" aria-hidden="true">' . $icon . '</span>';
	}
}
if ( '' === $text && '' === $media ) {
	return;
}

$url   = esc_url( (string) ( $a['url'] ?? '' ) );
$color = bst_block_color( $a, 'color' );
$class = 'bs-marquee__item' . ( $media ? ' bs-marquee__item--media' : '' ) . ( $color ? ' bs-marquee__item--color' : '' );

// A plain text item prints exactly what the old one-per-line list printed.
$inner = $media . ( '' !== $text && ( $media || $url ) ? '<span class="bs-marquee__text">' . $text . '</span>' : $text );
if ( $url ) {
	$rel = trim( preg_replace( '/[^a-z ]/', '', strtolower( (string) ( $a['rel'] ?? '' ) ) ) );
	if ( ! empty( $a['newTab'] ) && false === strpos( $rel, 'noopener' ) ) {
		$rel = trim( $rel . ' noopener' );
	}
	$label = trim( (string) ( $a['ariaLabel'] ?? '' ) );
	$inner = sprintf(
		'<a class="bs-marquee__link" href="%1$s"%2$s%3$s%4$s>%5$s</a>',
		$url,
		! empty( $a['newTab'] ) ? ' target="_blank"' : '',
		$rel ? ' rel="' . esc_attr( $rel ) . '"' : '',
		'' !== $label ? ' aria-label="' . esc_attr( $label ) . '"' : '',
		$inner
	);
}

printf(
	'<span class="%1$s"%2$s>%3$s</span>',
	esc_attr( $class ),
	$color ? ' style="' . esc_attr( '--bs-mq-item-color:' . $color ) . '"' : '',
	$inner // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
