<?php
/**
 * Icon list block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a = (array) $attributes;
$items = array_filter( (array) ( $a['items'] ?? array() ), 'is_array' );
if ( ! $items ) {
	return;
}
$layout = bst_choice( $a['layout'] ?? 'vertical', array( 'vertical', 'inline', 'columns' ), 'vertical' );
$size   = bst_block_num( $a, 'iconSize', 20, 10, 64 );
$lis    = '';
foreach ( $items as $item ) {
	$text = wp_kses_post( (string) ( $item['text'] ?? '' ) );
	if ( '' === trim( wp_strip_all_tags( $text ) ) ) {
		continue;
	}
	$icon  = ! empty( $item['icon'] ) ? '<span class="bs-icon-list__icon">' . bst_icon( sanitize_key( $item['icon'] ), $size ) . '</span>' : '';
	$inner = $icon . '<span class="bs-icon-list__text">' . $text . '</span>';
	if ( ! empty( $item['link'] ) ) {
		$inner = '<a href="' . esc_url( $item['link'] ) . '">' . $inner . '</a>';
	}
	$lis .= '<li class="bs-icon-list__item">' . $inner . '</li>';
}
$class = 'bs-icon-list bs-icon-list--' . $layout . ' bs-icon-list--align-' . bst_choice( $a['align'] ?? 'left', array( 'left', 'center', 'right' ), 'left' ) . ( ! empty( $a['divider'] ) ? ' has-divider' : '' ) . ( ! empty( $a['iconBg'] ) ? ' has-icon-bg' : '' );
$style = bst_style_vars(
	array(
		'--bs-il-size'  => $size . 'px',
		'--bs-il-color' => bst_block_color( $a, 'iconColor' ),
		'--bs-il-gap'   => bst_block_num( $a, 'gap', 10, 0, 60 ) . 'px',
		'--bs-il-cols'  => bst_block_num( $a, 'columns', 2, 1, 4 ),
		'--bs-il-text'  => ! empty( $a['textSize'] ) ? bst_block_num( $a, 'textSize', 16, 10, 40 ) . 'px' : '',
	)
);
printf( '<ul %1$s>%2$s</ul>', get_block_wrapper_attributes( array( 'class' => $class, 'style' => $style ) ), $lis ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
