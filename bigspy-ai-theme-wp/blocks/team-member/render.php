<?php
/**
 * Team member block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a = (array) $attributes;
$style = bst_choice( $a['style'] ?? 'card', array( 'card', 'overlay', 'round', 'minimal' ), 'card' );
$ratio = 'round' === $style ? '1-1' : bst_choice( $a['ratio'] ?? '1-1', array( '1-1', '3-4', '4-3' ), '1-1' );
$img   = ! empty( $a['imageId'] ) ? wp_get_attachment_image( (int) $a['imageId'], 'large', false, array( 'class' => 'bs-team__img', 'alt' => (string) ( $a['name'] ?? '' ) ) ) : '';
if ( ! $img && ! empty( $a['imageUrl'] ) ) {
	$img = '<img class="bs-team__img" src="' . esc_url( $a['imageUrl'] ) . '" alt="' . esc_attr( (string) ( $a['name'] ?? '' ) ) . '" loading="lazy">';
}
$social = '';
foreach ( array( 'facebook', 'zalo', 'instagram', 'tiktok', 'linkedin', 'mail', 'phone' ) as $key ) {
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
	$social .= '<a href="' . esc_url( $url, array( 'http', 'https', 'mailto', 'tel' ) ) . '" aria-label="' . esc_attr( ucfirst( $key ) ) . '"' . ( 0 === strpos( $url, 'http' ) ? ' target="_blank" rel="noopener"' : '' ) . '>' . bst_icon( $key, 16 ) . '</a>';
}
printf(
	'<div %1$s>%2$s<div class="bs-team__body"><h3 class="bs-team__name">%3$s</h3>%4$s%5$s%6$s</div></div>',
	get_block_wrapper_attributes( array( 'class' => 'bs-team bs-team--' . $style . ' bs-team--align-' . bst_choice( $a['align'] ?? 'center', array( 'left', 'center' ), 'center' ), 'style' => bst_style_vars( array( '--bs-team-accent' => bst_block_color( $a, 'accent' ) ) ) ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$img ? '<div class="bs-team__media bs-ratio bs-ratio--' . esc_attr( $ratio ) . '">' . $img . '</div>' : '', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_html( (string) ( $a['name'] ?? '' ) ),
	! empty( $a['role'] ) ? '<p class="bs-team__role">' . esc_html( $a['role'] ) . '</p>' : '',
	! empty( $a['bio'] ) ? '<p class="bs-team__bio">' . esc_html( $a['bio'] ) . '</p>' : '',
	$social ? '<div class="bs-team__social">' . $social . '</div>' : '' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
