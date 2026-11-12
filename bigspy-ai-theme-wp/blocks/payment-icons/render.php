<?php
/**
 * Payment icons block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a = (array) $attributes;
$all = array(
	'visa'       => array( 'VISA', '#1a1f71', '#fff' ),
	'mastercard' => array( 'Mastercard', '#eb001b', '#fff' ),
	'jcb'        => array( 'JCB', '#0e4c96', '#fff' ),
	'amex'       => array( 'AMEX', '#2e77bc', '#fff' ),
	'paypal'     => array( 'PayPal', '#003087', '#fff' ),
	'momo'       => array( 'MoMo', '#a50064', '#fff' ),
	'zalopay'    => array( 'ZaloPay', '#0068ff', '#fff' ),
	'vnpay'      => array( 'VNPAY', '#005baa', '#fff' ),
	'shopeepay'  => array( 'ShopeePay', '#ee4d2d', '#fff' ),
	'vietqr'     => array( 'VietQR', '#00a651', '#fff' ),
	'bank'       => array( __( 'Bank transfer', 'bigspy-ai-theme-wp' ), '#0f766e', '#fff' ),
	'cod'        => array( 'COD', '#374151', '#fff' ),
	'applepay'   => array( 'Apple Pay', '#000', '#fff' ),
	'googlepay'  => array( 'G Pay', '#fff', '#3c4043' ),
	'napas'      => array( 'NAPAS', '#0b4ea2', '#fff' ),
);
$style = bst_choice( $a['style'] ?? 'color', array( 'color', 'outline', 'mono', 'card', 'dark' ), 'color' );
$out   = '';
foreach ( (array) ( $a['methods'] ?? array() ) as $m ) {
	if ( isset( $all[ $m ] ) ) {
		$out .= '<li class="bs-pay__item bs-pay--' . esc_attr( $m ) . '" style="--bs-pay-bg:' . esc_attr( $all[ $m ][1] ) . ';--bs-pay-c:' . esc_attr( $all[ $m ][2] ) . '">' . esc_html( $all[ $m ][0] ) . '</li>';
	}
}
foreach ( (array) ( $a['customImages'] ?? array() ) as $img ) {
	if ( ! is_array( $img ) ) {
		continue;
	}
	$alt  = sanitize_text_field( (string) ( $img['label'] ?? '' ) );
	$html = ! empty( $img['imageId'] ) ? wp_get_attachment_image( (int) $img['imageId'], 'thumbnail', false, array( 'alt' => $alt, 'loading' => 'lazy' ) ) : ( ! empty( $img['imageUrl'] ) ? '<img src="' . esc_url( $img['imageUrl'] ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy">' : '' );
	if ( $html ) {
		$out .= '<li class="bs-pay__item bs-pay__item--img">' . $html . '</li>';
	}
}
if ( '' === $out ) {
	return;
}
$pvars = bst_style_vars(
	array(
		'--bs-pay-gap' => ! empty( $a['gap'] ) ? bst_block_num( $a, 'gap', 8, 0, 40 ) . 'px' : '',
		'--bs-pay-r'   => isset( $a['radius'] ) && $a['radius'] >= 0 ? min( 30, (int) $a['radius'] ) . 'px' : '',
	)
);
$plabel = trim( (string) ( $a['label'] ?? '' ) );
if ( '' !== $plabel || ! empty( $a['grayHover'] ) || $pvars ) {
	$align = bst_choice( $a['align'] ?? 'left', array( 'left', 'center', 'right' ), 'left' );
	printf(
		'<div %1$s>%2$s<ul class="bs-pay bs-pay--style-%3$s bs-pay--align-%4$s%5$s" style="%6$s" aria-label="%7$s">%8$s</ul></div>',
		get_block_wrapper_attributes( array( 'class' => 'bs-pay-wrap bs-pay-wrap--align-' . $align ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		'' !== $plabel ? '<span class="bs-pay__label">' . esc_html( $plabel ) . '</span>' : '', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_attr( $style ),
		esc_attr( $align ),
		! empty( $a['grayHover'] ) ? ' is-gray-hover' : '',
		esc_attr( '--bs-pay-h:' . bst_block_num( $a, 'size', 36, 20, 64 ) . 'px' . ( $pvars ? ';' . $pvars : '' ) ),
		esc_attr__( 'Accepted payment methods', 'bigspy-ai-theme-wp' ),
		$out // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	);
	return;
}
$align = bst_choice( $a['align'] ?? 'left', array( 'left', 'center', 'right' ), 'left' );
printf( '<ul %1$s aria-label="%2$s">%3$s</ul>', get_block_wrapper_attributes( array( 'class' => 'bs-pay bs-pay--style-' . $style . ' bs-pay--align-' . $align, 'style' => '--bs-pay-h:' . bst_block_num( $a, 'size', 36, 20, 64 ) . 'px' ) ), esc_attr__( 'Accepted payment methods', 'bigspy-ai-theme-wp' ), $out ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
