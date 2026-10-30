<?php
/** One Hover Reveal row. */
defined( 'ABSPATH' ) || exit;
$a     = $attributes;
$title = trim( (string) ( $a['title'] ?? '' ) );
if ( '' === $title ) {
	return;
}
$url = ! empty( $a['imageId'] ) ? wp_get_attachment_image_url( (int) $a['imageId'], 'medium_large' ) : '';
$url = $url ? $url : esc_url_raw( (string) ( $a['imageUrl'] ?? '' ) );
$alt = ! empty( $a['imageId'] ) ? (string) get_post_meta( (int) $a['imageId'], '_wp_attachment_image_alt', true ) : '';
$tag = '' !== trim( (string) ( $a['link'] ?? '' ) ) ? 'a' : 'span';
$img = $url ? '<img class="bs-hreveal__img" src="' . esc_url( $url ) . '" alt="' . esc_attr( '' !== $alt ? $alt : $title ) . '" loading="lazy" decoding="async">' : '';
printf(
	'<li %1$s><%2$s class="bs-hreveal__link"%3$s>%4$s<span class="bs-hreveal__title">%5$s</span>%6$s</%2$s></li>',
	get_block_wrapper_attributes( array( 'class' => 'bs-hreveal__item' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$tag, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- a or span.
	'a' === $tag ? ' href="' . esc_url( $a['link'] ) . '"' : '', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$img, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_html( $title ),
	'' !== trim( (string) ( $a['meta'] ?? '' ) ) ? '<span class="bs-hreveal__meta">' . esc_html( $a['meta'] ) . '</span>' : ''
);
