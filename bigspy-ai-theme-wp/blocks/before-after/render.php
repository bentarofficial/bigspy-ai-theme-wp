<?php
/**
 * Before / after image comparison block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a   = (array) $attributes;
$img = function ( $id, $url, $class, $alt ) {
	if ( $id ) {
		$html = wp_get_attachment_image( (int) $id, 'large', false, array( 'class' => $class, 'alt' => $alt, 'draggable' => 'false' ) );
		if ( $html ) {
			return $html;
		}
	}
	return $url ? '<img class="' . esc_attr( $class ) . '" src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy" decoding="async" draggable="false">' : '';
};
$alt    = (string) ( $a['alt'] ?? '' );
$before = $img( $a['beforeId'] ?? 0, $a['beforeUrl'] ?? '', 'bs-ba__img bs-ba__img--before', $alt );
$after  = $img( $a['afterId'] ?? 0, $a['afterUrl'] ?? '', 'bs-ba__img bs-ba__img--after', $alt );
if ( ! $before || ! $after ) {
	return;
}
$start = bst_block_num( $a, 'start', 50, 0, 100 );
$style = bst_style_vars( array( '--bs-ba-pos' => $start . '%', '--bs-ba-radius' => bst_block_num( $a, 'radius', 12, 0, 60 ) . 'px' ) );
$lb    = trim( wp_strip_all_tags( (string) ( $a['beforeLabel'] ?? '' ) ) );
$la    = trim( wp_strip_all_tags( (string) ( $a['afterLabel'] ?? '' ) ) );
printf(
	'<div %1$s>%2$s<div class="bs-ba__top">%3$s</div>%4$s%5$s<span class="bs-ba__handle" aria-hidden="true"><span></span></span><input class="bs-ba__range" type="range" min="0" max="100" value="%6$d" aria-label="%7$s" oninput="this.parentNode.style.setProperty(\'--bs-ba-pos\',this.value+\'%%\')"></div>',
	get_block_wrapper_attributes( array( 'class' => 'bs-ba', 'style' => $style ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$after, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$before, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$lb ? '<span class="bs-ba__label bs-ba__label--before">' . esc_html( $lb ) . '</span>' : '',
	$la ? '<span class="bs-ba__label bs-ba__label--after">' . esc_html( $la ) . '</span>' : '',
	(int) $start,
	esc_attr__( 'Drag to compare the images', 'bigspy-ai-theme-wp' )
);
