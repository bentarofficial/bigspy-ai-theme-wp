<?php
/** One Video Hover Card: the video source stays in data-src until the visitor hovers or taps. */
defined( 'ABSPATH' ) || exit;
$a      = $attributes;
$poster = ! empty( $a['posterId'] ) ? wp_get_attachment_image_url( (int) $a['posterId'], 'large' ) : '';
$poster = $poster ? $poster : esc_url_raw( (string) ( $a['posterUrl'] ?? '' ) );
$video  = ! empty( $a['videoId'] ) ? wp_get_attachment_url( (int) $a['videoId'] ) : '';
$video  = $video ? $video : esc_url_raw( (string) ( $a['videoUrl'] ?? '' ) );
$title  = trim( (string) ( $a['title'] ?? '' ) );
$alt    = ! empty( $a['posterId'] ) ? (string) get_post_meta( (int) $a['posterId'], '_wp_attachment_image_alt', true ) : '';
$link   = trim( (string) ( $a['link'] ?? '' ) );
$inner  = '<span class="bs-vcard__media">'
	. ( $poster ? '<img class="bs-vcard__poster" src="' . esc_url( $poster ) . '" alt="' . esc_attr( '' !== $alt ? $alt : $title ) . '" loading="lazy" decoding="async">' : '' )
	. ( $video ? '<video class="bs-vcard__video" muted loop playsinline preload="none" data-src="' . esc_url( $video ) . '"' . ( $poster ? ' poster="' . esc_url( $poster ) . '"' : '' ) . ' aria-hidden="true"></video><span class="bs-vcard__play" aria-hidden="true"></span>' : '' )
	. '</span>'
	. ( '' !== $title || '' !== trim( (string) ( $a['text'] ?? '' ) ) ? '<span class="bs-vcard__body">' . ( '' !== $title ? '<strong class="bs-vcard__title">' . esc_html( $title ) . '</strong>' : '' ) . ( '' !== trim( (string) ( $a['text'] ?? '' ) ) ? '<span class="bs-vcard__text">' . esc_html( $a['text'] ) . '</span>' : '' ) . '</span>' : '' );
printf(
	'<div %1$s>%2$s</div>',
	get_block_wrapper_attributes( array( 'class' => 'bs-vcard' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	'' !== $link ? '<a class="bs-vcard__link" href="' . esc_url( $link ) . '">' . $inner . '</a>' : '<div class="bs-vcard__link">' . $inner . '</div>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
