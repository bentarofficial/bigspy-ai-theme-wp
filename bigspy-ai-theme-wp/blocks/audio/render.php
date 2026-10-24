<?php
/**
 * Audio player block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a   = (array) $attributes;
$src = ! empty( $a['fileId'] ) ? (string) wp_get_attachment_url( (int) $a['fileId'] ) : '';
$src = $src ? $src : (string) ( $a['fileUrl'] ?? '' );
if ( '' === $src ) {
	return;
}
$cover = ! empty( $a['coverId'] ) ? (string) wp_get_attachment_image_url( (int) $a['coverId'], 'medium' ) : '';
$cover = $cover ? $cover : (string) ( $a['coverUrl'] ?? '' );
$title = trim( wp_strip_all_tags( (string) ( $a['title'] ?? '' ) ) );
$style = bst_choice( (string) ( $a['style'] ?? 'card' ), array( 'card', 'minimal', 'dark' ), 'card' );
printf(
	'<figure %1$s>%2$s<div class="bs-audio__body">%3$s%4$s<audio class="bs-audio__player" controls preload="metadata" src="%5$s"></audio></div></figure>',
	get_block_wrapper_attributes( array( 'class' => 'bs-audio bs-audio--' . $style, 'style' => bst_style_vars( array( '--bs-audio-accent' => bst_block_color( $a, 'accent' ) ) ) ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$cover ? '<img class="bs-audio__cover" src="' . esc_url( $cover ) . '" alt="' . esc_attr( $title ) . '" loading="lazy" decoding="async">' : '<span class="bs-audio__cover bs-audio__cover--icon" aria-hidden="true">' . bst_icon( 'play', 28 ) . '</span>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$title ? '<figcaption class="bs-audio__title">' . esc_html( $title ) . '</figcaption>' : '',
	! empty( $a['artist'] ) ? '<span class="bs-audio__artist">' . esc_html( $a['artist'] ) . '</span>' : '',
	esc_url( $src )
);
