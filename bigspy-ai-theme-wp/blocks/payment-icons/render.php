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
	'bank'       => array( 'Chuyển khoản', '#0f766e', '#fff' ),
	'cod'        => array( 'COD', '#374151', '#fff' ),
	'applepay'   => array( 'Apple Pay', '#000', '#fff' ),
	'googlepay'  => array( 'G Pay', '#fff', '#3c4043' ),
);
$style = bst_choice( $a['style'] ?? 'color', array( 'color', 'outline', 'mono' ), 'color' );
$out   = '';
foreach ( (array) ( $a['methods'] ?? array() ) as $m ) {
	if ( isset( $all[ $m ] ) ) {
		$out .= '<li class="bs-pay__item bs-pay--' . esc_attr( $m ) . '" style="--bs-pay-bg:' . esc_attr( $all[ $m ][1] ) . ';--bs-pay-c:' . esc_attr( $all[ $m ][2] ) . '">' . esc_html( $all[ $m ][0] ) . '</li>';
	}
}
if ( '' === $out ) {
	return;
}
$align = bst_choice( $a['align'] ?? 'left', array( 'left', 'center', 'right' ), 'left' );
printf( '<ul %1$s aria-label="%2$s">%3$s</ul>', get_block_wrapper_attributes( array( 'class' => 'bs-pay bs-pay--style-' . $style . ' bs-pay--align-' . $align, 'style' => '--bs-pay-h:' . bst_block_num( $a, 'size', 36, 20, 64 ) . 'px' ) ), esc_attr__( 'Accepted payment methods', 'bigspy-ai-theme-wp' ), $out ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
