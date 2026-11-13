<?php
/**
 * Popup block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a = (array) $attributes;
$id = sanitize_html_class( $a['popupId'] ?? '' );
if ( '' === $id ) {
	$id = 'popup-' . substr( md5( wp_json_encode( $a ) . $content ), 0, 6 );
}
// Who and when: logged-in state and a date window are decided on the server.
$login = bst_choice( $a['loginState'] ?? '', array( '', 'in', 'out' ), '' );
if ( ( 'in' === $login && ! is_user_logged_in() ) || ( 'out' === $login && is_user_logged_in() ) ) {
	return;
}
$today = current_time( 'Y-m-d' );
foreach ( array( 'dateFrom' => 1, 'dateTo' => -1 ) as $dk => $dir ) {
	$dv = (string) ( $a[ $dk ] ?? '' );
	if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $dv ) && ( ( 1 === $dir && $today < $dv ) || ( -1 === $dir && $today > $dv ) ) ) {
		return;
	}
}
$trigger  = bst_choice( $a['trigger'] ?? 'button', array( 'button', 'link', 'delay', 'scroll', 'exit', 'selector', 'idle', 'end' ), 'button' );
$size     = bst_choice( $a['size'] ?? 'md', array( 'sm', 'md', 'lg', 'full' ), 'md' );
$position = bst_choice( $a['position'] ?? 'center', array( 'center', 'bottom-right', 'bottom-left', 'side-right', 'side-left', 'top', 'bar-top', 'bar-bottom', 'fullscreen' ), 'center' );
$shadows  = array( 'none' => 'none', 'sm' => '0 8px 24px rgba(0,0,0,.18)', 'lg' => '0 40px 100px rgba(0,0,0,.5)' );
$more     = bst_style_vars(
	array(
		'--bs-pop-ovc'   => bst_block_color( $a, 'overlayColor' ),
		'--bs-pop-w'     => ! empty( $a['width'] ) ? bst_block_num( $a, 'width', 640, 200, 1600 ) . 'px' : '',
		'--bs-pop-pad'   => isset( $a['padding'] ) && $a['padding'] >= 0 ? min( 120, (int) $a['padding'] ) . 'px' : '',
		'--bs-pop-sh'    => $shadows[ $a['shadow'] ?? '' ] ?? '',
		'--bs-pop-bc'    => bst_block_color( $a, 'borderColor' ),
		'--bs-pop-bw'    => ! empty( $a['borderWidth'] ) ? min( 10, (int) $a['borderWidth'] ) . 'px' : '',
		'--bs-pop-close' => bst_block_color( $a, 'closeColor' ),
	)
);
$anim     = bst_choice( $a['anim'] ?? '', array( '', 'zoom', 'slide-up', 'fade', 'none' ), '' );
$cstyle   = bst_choice( $a['closeStyle'] ?? '', array( '', 'circle', 'text' ), '' );
$cpos     = bst_choice( $a['closePos'] ?? '', array( '', 'outside', 'left' ), '' );
$show_on  = bst_choice( $a['showOn'] ?? '', array( '', 'desktop', 'mobile' ), '' );
$pclass   = ( $anim ? ' bs-popup--anim-' . $anim : '' ) . ( $cstyle ? ' bs-popup--close-' . $cstyle : '' ) . ( $cpos ? ' bs-popup--close-' . $cpos : '' );
$style    = bst_style_vars( array( '--bs-pop-ov' => ( bst_block_num( $a, 'overlay', 60, 0, 95 ) / 100 ), '--bs-pop-radius' => bst_block_num( $a, 'radius', 16, 0, 40 ) . 'px', '--bs-pop-bg' => bst_block_color( $a, 'bgColor' ) ) );
$data     = array(
	'data-bs-popup'         => $id,
	'data-trigger'          => $trigger,
	'data-delay'            => (string) bst_block_num( $a, 'delay', 5, 0, 120 ),
	'data-scroll'           => (string) bst_block_num( $a, 'scroll', 50, 0, 100 ),
	'data-frequency'        => (string) bst_block_num( $a, 'frequency', 1, 0, 365 ),
	'data-close-outside'    => ! empty( $a['closeOutside'] ) ? '1' : '0',
);
if ( 'selector' === $trigger ) {
	$data['data-selector'] = trim( wp_strip_all_tags( (string) ( $a['selector'] ?? '' ) ) );
}
if ( 'idle' === $trigger ) {
	$data['data-idle'] = (string) bst_block_num( $a, 'idle', 30, 3, 600 );
}
if ( $show_on ) {
	$data['data-show-on'] = $show_on;
}
if ( ! empty( $a['closeOnSubmit'] ) ) {
	$data['data-close-submit'] = '1';
}
$attrs = '';
foreach ( $data as $k => $v ) {
	$attrs .= ' ' . $k . '="' . esc_attr( $v ) . '"';
}
$end    = 'end' === $trigger ? '<span class="bs-popup__end" data-bs-popup-end="' . esc_attr( $id ) . '" aria-hidden="true"></span>' : '';
$button = 'button' === $trigger ? '<button type="button" class="bs-btn bs-popup__open" data-bs-popup-open="' . esc_attr( $id ) . '">' . esc_html( $a['buttonText'] ?? '' ) . '</button>' : '';
printf(
	'%1$s<div %2$s%3$s hidden><div class="bs-popup__overlay" data-bs-popup-close></div><div class="bs-popup__dialog" role="dialog" aria-modal="true" aria-label="%6$s" tabindex="-1"><button type="button" class="bs-popup__close" data-bs-popup-close aria-label="%4$s">%7$s</button><div class="bs-popup__body">%5$s</div></div></div>',
	$button . $end, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	get_block_wrapper_attributes( array( 'class' => 'bs-popup bs-popup--' . $size . ' bs-popup--' . $position . $pclass, 'style' => $style . ( $more ? ';' . $more : '' ), 'id' => $id ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$attrs, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_attr__( 'Close', 'bigspy-ai-theme-wp' ),
	$content, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_attr( '' !== trim( (string) ( $a['buttonText'] ?? '' ) ) ? $a['buttonText'] : __( 'Popup', 'bigspy-ai-theme-wp' ) ),
	'text' === $cstyle ? esc_html__( 'Close', 'bigspy-ai-theme-wp' ) . ' &times;' : '&times;'
);
