<?php
/**
 * Social follow icons.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a     = (array) $attributes;
$names = array( 'facebook' => 'Facebook', 'instagram' => 'Instagram', 'youtube' => 'YouTube', 'tiktok' => 'TikTok', 'zalo' => 'Zalo', 'messenger' => 'Messenger', 'whatsapp' => 'WhatsApp', 'x' => 'X', 'linkedin' => 'LinkedIn', 'pinterest' => 'Pinterest', 'mail' => 'Email', 'phone' => __( 'Phone', 'bigspy-ai-theme-wp' ) );
$links = '';
$size  = bst_block_num( $a, 'size', 40, 24, 96 );
foreach ( $names as $key => $label ) {
	$url = trim( (string) ( $a[ $key ] ?? '' ) );
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
	$links   .= '<a class="bs-follow__link bs-follow__link--' . esc_attr( $key ) . '" href="' . esc_url( $url, array( 'http', 'https', 'mailto', 'tel' ) ) . '"' . ( $external ? ' target="_blank" rel="noopener"' : '' ) . ' aria-label="' . esc_attr( $label ) . '" title="' . esc_attr( $label ) . '">' . bst_icon( $key, (int) round( $size * .48 ) ) . '</a>';
}
if ( '' === $links ) {
	return;
}
$class = 'bs-follow bs-follow--' . bst_choice( $a['style'] ?? 'fill', array( 'fill', 'outline', 'plain' ), 'fill' ) . ' bs-follow--' . bst_choice( $a['shape'] ?? 'circle', array( 'circle', 'rounded', 'square' ), 'circle' ) . ' bs-follow--align-' . bst_choice( $a['align'] ?? 'left', array( 'left', 'center', 'right' ), 'left' ) . ( ! empty( $a['brand'] ) ? ' is-brand' : '' );
$style = bst_style_vars(
	array(
		'--bs-follow-size'  => $size . 'px',
		'--bs-follow-gap'   => bst_block_num( $a, 'gap', 10, 0, 40 ) . 'px',
		'--bs-follow-color' => bst_block_color( $a, 'color' ),
	)
);
$label = ! empty( $a['label'] ) ? '<span class="bs-follow__label">' . esc_html( $a['label'] ) . '</span>' : '';
printf( '<div %1$s>%2$s<div class="bs-follow__icons">%3$s</div></div>', get_block_wrapper_attributes( array( 'class' => $class, 'style' => $style ) ), $label, $links ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
