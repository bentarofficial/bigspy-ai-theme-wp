<?php
/**
 * Video block: YouTube / Vimeo embed or a self-hosted file. With a poster, the player loads only on click.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a   = (array) $attributes;
$url = trim( (string) ( $a['url'] ?? '' ) );
if ( '' === $url ) {
	return;
}
$ratio  = bst_choice( $a['ratio'] ?? '16-9', array( '16-9', '4-3', '1-1', '9-16', '21-9' ), '16-9' );
$auto   = ! empty( $a['autoplay'] );
$muted  = ! empty( $a['muted'] ) || $auto;
$loop   = ! empty( $a['loop'] );
$ctrl   = ! empty( $a['controls'] );
$title  = '' !== ( $a['title'] ?? '' ) ? (string) $a['title'] : __( 'Video', 'bigspy-ai-theme-wp' );
$poster = ! empty( $a['posterId'] ) ? wp_get_attachment_image_url( (int) $a['posterId'], 'large' ) : ( $a['posterUrl'] ?? '' );
$player = '';

if ( preg_match( '~(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{6,})~', $url, $m ) ) {
	$q      = array( 'rel' => 0, 'playsinline' => 1, 'autoplay' => ( $auto || $poster ) ? 1 : 0, 'mute' => $muted ? 1 : 0, 'controls' => $ctrl ? 1 : 0 );
	if ( $loop ) {
		$q['loop']     = 1;
		$q['playlist'] = $m[1];
	}
	$src    = add_query_arg( $q, 'https://www.youtube-nocookie.com/embed/' . $m[1] );
	$player = '<iframe src="' . esc_url( $src ) . '" title="' . esc_attr( $title ) . '" allow="autoplay; encrypted-media; picture-in-picture; fullscreen" allowfullscreen loading="lazy"></iframe>';
	if ( ! $poster ) {
		$poster = 'https://i.ytimg.com/vi/' . $m[1] . '/hqdefault.jpg';
		$poster = $auto ? '' : $poster;
	}
} elseif ( preg_match( '~vimeo\.com/(?:video/)?([0-9]+)~', $url, $m ) ) {
	$q      = array( 'autoplay' => ( $auto || $poster ) ? 1 : 0, 'muted' => $muted ? 1 : 0, 'loop' => $loop ? 1 : 0, 'controls' => $ctrl ? 1 : 0, 'dnt' => 1 );
	$player = '<iframe src="' . esc_url( add_query_arg( $q, 'https://player.vimeo.com/video/' . $m[1] ) ) . '" title="' . esc_attr( $title ) . '" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen loading="lazy"></iframe>';
} else {
	$player = '<video src="' . esc_url( $url ) . '"' . ( $ctrl ? ' controls' : '' ) . ( $auto || $poster ? ' autoplay' : '' ) . ( $muted ? ' muted' : '' ) . ( $loop ? ' loop' : '' ) . ' playsinline preload="metadata"></video>';
}

$class = 'bs-video bs-ratio bs-ratio--' . $ratio;
$style = '--bs-video-radius:' . bst_block_num( $a, 'radius', 8, 0, 60 ) . 'px';
if ( $poster && ! $auto ) {
	printf(
		'<div %1$s><button type="button" class="bs-video__poster" data-bs-video="%2$s" aria-label="%3$s"><img src="%4$s" alt="" loading="lazy" decoding="async"><span class="bs-video__play">%5$s</span></button></div>',
		get_block_wrapper_attributes( array( 'class' => $class, 'style' => $style ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_attr( $player ),
		/* translators: %s: video title */
		esc_attr( sprintf( __( 'Play: %s', 'bigspy-ai-theme-wp' ), $title ) ),
		esc_url( $poster ),
		bst_icon( 'play', 30 ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	);
	return;
}
printf( '<div %1$s>%2$s</div>', get_block_wrapper_attributes( array( 'class' => $class, 'style' => $style ) ), $player ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
