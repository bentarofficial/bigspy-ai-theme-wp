<?php
/**
 * Share buttons block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a = (array) $attributes;
$url   = is_singular() ? get_permalink() : home_url( add_query_arg( array() ) );
$title = wp_strip_all_tags( is_singular() ? get_the_title() : get_bloginfo( 'name' ) );
$u     = rawurlencode( $url );
$t     = rawurlencode( $title );
$map   = array(
	'facebook'  => array( 'https://www.facebook.com/sharer/sharer.php?u=' . $u, 'Facebook' ),
	'zalo'      => array( 'https://sp.zalo.me/share_inline?u=' . $u, 'Zalo' ),
	'x'         => array( 'https://twitter.com/intent/tweet?url=' . $u . '&text=' . $t, 'X' ),
	'linkedin'  => array( 'https://www.linkedin.com/sharing/share-offsite/?url=' . $u, 'LinkedIn' ),
	'pinterest' => array( 'https://pinterest.com/pin/create/button/?url=' . $u . '&description=' . $t, 'Pinterest' ),
	'whatsapp'  => array( 'https://wa.me/?text=' . $t . '%20' . $u, 'WhatsApp' ),
	'mail'      => array( 'mailto:?subject=' . $t . '&body=' . $u, 'Email' ),
);
$links = '';
foreach ( array_map( 'trim', explode( ',', (string) ( $a['networks'] ?? '' ) ) ) as $key ) {
	if ( 'copy' === $key ) {
		$links .= '<button type="button" class="bs-follow__link bs-follow__link--copy" data-bs-copy="' . esc_attr( $url ) . '" aria-label="' . esc_attr__( 'Copy link', 'bigspy-ai-theme-wp' ) . '">' . bst_icon( 'check', 16 ) . '</button>';
	} elseif ( isset( $map[ $key ] ) ) {
		$links .= '<a class="bs-follow__link bs-follow__link--' . esc_attr( $key ) . '" href="' . esc_url( $map[ $key ][0], array( 'https', 'mailto' ) ) . '" target="_blank" rel="noopener nofollow" aria-label="' . esc_attr( $map[ $key ][1] ) . '">' . bst_icon( $key, (int) round( bst_block_num( $a, 'size', 38, 24, 80 ) * .45 ) ) . '</a>';
	}
}
if ( '' === $links ) {
	return;
}
printf(
	'<div %1$s>%2$s<div class="bs-follow__icons">%3$s</div></div>',
	get_block_wrapper_attributes( array( 'class' => 'bs-follow bs-share-block bs-follow--' . bst_choice( $a['style'] ?? 'fill', array( 'fill', 'outline', 'plain' ), 'fill' ) . ' bs-follow--' . bst_choice( $a['shape'] ?? 'circle', array( 'circle', 'rounded', 'square' ), 'circle' ) . ' bs-follow--align-' . bst_choice( $a['align'] ?? 'left', array( 'left', 'center', 'right' ), 'left' ) . ( ! empty( $a['brand'] ) ? ' is-brand' : '' ), 'style' => '--bs-follow-size:' . bst_block_num( $a, 'size', 38, 24, 80 ) . 'px' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	! empty( $a['label'] ) ? '<span class="bs-follow__label">' . esc_html( $a['label'] ) . '</span>' : '',
	$links // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
