<?php
/**
 * Social follow icons.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a     = (array) $attributes;
$names = array( 'facebook' => 'Facebook', 'instagram' => 'Instagram', 'youtube' => 'YouTube', 'tiktok' => 'TikTok', 'zalo' => 'Zalo', 'messenger' => 'Messenger', 'whatsapp' => 'WhatsApp', 'x' => 'X', 'linkedin' => 'LinkedIn', 'pinterest' => 'Pinterest', 'mail' => 'Email', 'phone' => __( 'Phone', 'bigspy-ai-theme-wp' ), 'telegram' => 'Telegram', 'threads' => 'Threads', 'github' => 'GitHub', 'discord' => 'Discord', 'shopee' => 'Shopee', 'lazada' => 'Lazada', 'maps' => 'Google Maps', 'website' => __( 'Website', 'bigspy-ai-theme-wp' ) );
$icon_of  = array( 'maps' => 'pin', 'website' => 'globe' );
$show_txt = ! empty( $a['showText'] );
$tip      = ! empty( $a['tooltip'] );
$isz      = ! empty( $a['iconSize'] ) ? bst_block_num( $a, 'iconSize', 18, 10, 64 ) : 0;
$entries  = array();
foreach ( $names as $key => $label ) {
	$entries[] = array( $key, $label, trim( (string) ( $a[ $key ] ?? '' ) ), $icon_of[ $key ] ?? $key );
}
foreach ( (array) ( $a['custom'] ?? array() ) as $ci => $c ) {
	if ( is_array( $c ) && ! empty( $c['url'] ) ) {
		$entries[] = array( 'custom-' . (int) $ci, sanitize_text_field( (string) ( $c['label'] ?? '' ) ), trim( (string) $c['url'] ), sanitize_key( (string) ( $c['icon'] ?? 'link' ) ), sanitize_hex_color( (string) ( $c['color'] ?? '' ) ) );
	}
}
$links = '';
$size  = bst_block_num( $a, 'size', 40, 24, 96 );
foreach ( $entries as $e ) {
	list( $key, $label, $url, $icon_name ) = $e;
	if ( '' === $url ) {
		continue;
	}
	if ( 'mail' === $key && false === strpos( $url, ':' ) ) {
		$url = 'mailto:' . $url;
	} elseif ( 'phone' === $key && false === strpos( $url, ':' ) ) {
		$url = 'tel:' . preg_replace( '/[^0-9+]/', '', $url );
	} elseif ( 'zalo' === $key && preg_match( '/^[0-9 .+]+$/', $url ) ) {
		$url = 'https://zalo.me/' . preg_replace( '/[^0-9]/', '', $url );
	}
	$external = 0 === strpos( $url, 'http' );
	$cstyle   = ! empty( $e[4] ) ? ' style="--c:' . esc_attr( $e[4] ) . '"' : '';
	$icon_svg = bst_icon( $icon_name, $isz ? $isz : (int) round( $size * .48 ) );
	if ( '' === $icon_svg ) {
		$icon_svg = bst_icon( 'link', $isz ? $isz : (int) round( $size * .48 ) );
	}
	$links .= '<a class="bs-follow__link bs-follow__link--' . esc_attr( $key ) . '" href="' . esc_url( $url, array( 'http', 'https', 'mailto', 'tel' ) ) . '"' . ( $external ? ' target="_blank" rel="noopener"' : '' ) . ' aria-label="' . esc_attr( $label ) . '"' . ( $tip ? ' data-tip="' . esc_attr( $label ) . '"' : ' title="' . esc_attr( $label ) . '"' ) . $cstyle . '>' . $icon_svg . ( $show_txt && '' !== $label ? '<span class="bs-follow__text">' . esc_html( $label ) . '</span>' : '' ) . '</a>';
}
if ( '' === $links ) {
	return;
}
$class = 'bs-follow bs-follow--' . bst_choice( $a['style'] ?? 'fill', array( 'fill', 'outline', 'plain' ), 'fill' ) . ' bs-follow--' . bst_choice( $a['shape'] ?? 'circle', array( 'circle', 'rounded', 'square' ), 'circle' ) . ' bs-follow--align-' . bst_choice( $a['align'] ?? 'left', array( 'left', 'center', 'right' ), 'left' ) . ( ! empty( $a['brand'] ) ? ' is-brand' : '' );
$lay    = bst_choice( $a['layout'] ?? '', array( '', 'grid', 'vertical' ), '' );
$hov    = bst_choice( $a['hover'] ?? '', array( '', 'lift', 'color', 'rotate', 'none' ), '' );
$am     = bst_choice( $a['alignMobile'] ?? '', array( '', 'left', 'center', 'right' ), '' );
$class .= ( $lay ? ' bs-follow--' . $lay : '' ) . ( $show_txt ? ' has-text' : '' ) . ( $hov ? ' bs-follow--hover-' . $hov : '' ) . ( $tip ? ' has-tip' : '' ) . ( $am ? ' bs-follow--m-' . $am : '' );
$style = bst_style_vars(
	array(
		'--bs-follow-size'  => $size . 'px',
		'--bs-follow-gap'   => bst_block_num( $a, 'gap', 10, 0, 40 ) . 'px',
		'--bs-follow-color' => bst_block_color( $a, 'color' ),
		'--bs-follow-bg'    => bst_block_color( $a, 'bgColor' ),
		'--bs-follow-bc'    => bst_block_color( $a, 'borderColor' ),
		'--bs-follow-gap-m' => ! empty( $a['gapMobile'] ) ? bst_block_num( $a, 'gapMobile', 10, 0, 40 ) . 'px' : '',
	)
);
$label = ! empty( $a['label'] ) ? '<span class="bs-follow__label">' . esc_html( $a['label'] ) . '</span>' : '';
printf( '<div %1$s>%2$s<div class="bs-follow__icons">%3$s</div></div>', get_block_wrapper_attributes( array( 'class' => $class, 'style' => $style ) ), $label, $links ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
