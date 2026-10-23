<?php
/**
 * Video popup block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a = (array) $attributes;
$url = trim( (string) ( $a['url'] ?? '' ) );
if ( '' === $url ) {
	return;
}
$embed = '';
if ( preg_match( '~(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{6,})~', $url, $m ) ) {
	$embed = 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?autoplay=1&rel=0';
} elseif ( preg_match( '~vimeo\.com/(?:video/)?([0-9]+)~', $url, $m ) ) {
	$embed = 'https://player.vimeo.com/video/' . $m[1] . '?autoplay=1';
}
$img = ! empty( $a['imageId'] ) ? wp_get_attachment_image_url( (int) $a['imageId'], 'large' ) : ( $a['imageUrl'] ?? '' );
if ( ! $img && preg_match( '~youtube~', $embed ) && isset( $m[1] ) ) {
	$img = 'https://i.ytimg.com/vi/' . $m[1] . '/hqdefault.jpg';
}
$label = '' !== ( $a['label'] ?? '' ) ? (string) $a['label'] : '';
$btn   = '<span class="bs-vbtn__play' . ( ! empty( $a['pulse'] ) ? ' is-pulse' : '' ) . '">' . bst_icon( 'play', 28 ) . '</span>' . ( $label ? '<span class="bs-vbtn__label">' . esc_html( $label ) . '</span>' : '' );
printf(
	'<div %1$s><button type="button" class="bs-vbtn__trigger%2$s" data-bs-video-popup="%3$s" data-bs-video-file="%4$s" aria-label="%5$s">%6$s%7$s</button></div>',
	get_block_wrapper_attributes( array( 'class' => 'bs-vbtn bs-vbtn--align-' . bst_choice( $a['align'] ?? 'center', array( 'left', 'center', 'right' ), 'center' ), 'style' => bst_style_vars( array( '--bs-vbtn-size' => bst_block_num( $a, 'size', 72, 40, 140 ) . 'px', '--bs-vbtn-color' => bst_block_color( $a, 'color' ), '--bs-vbtn-radius' => bst_block_num( $a, 'radius', 16, 0, 60 ) . 'px' ) ) ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$img ? ' has-image bs-ratio bs-ratio--' . esc_attr( bst_choice( $a['ratio'] ?? '16-9', array( '16-9', '4-3', '1-1', '21-9' ), '16-9' ) ) : '',
	esc_attr( $embed ),
	esc_attr( $embed ? '' : $url ),
	esc_attr( $label ? $label : __( 'Play video', 'bigspy-ai-theme-wp' ) ),
	$img ? '<img src="' . esc_url( $img ) . '" alt="" loading="lazy">' : '',
	'<span class="bs-vbtn__inner">' . $btn . '</span>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
