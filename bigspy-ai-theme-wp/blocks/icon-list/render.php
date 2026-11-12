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
$num    = 0;
$numbered = ! empty( $a['numbered'] );
foreach ( $items as $item ) {
	$text = wp_kses_post( (string) ( $item['text'] ?? '' ) );
	if ( '' === trim( wp_strip_all_tags( $text ) ) ) {
		continue;
	}
	++$num;
	$ic    = sanitize_hex_color( (string) ( $item['color'] ?? '' ) );
	$istyle = $ic ? ' style="--bs-il-color:' . esc_attr( $ic ) . '"' : '';
	if ( $numbered ) {
		$icon = '<span class="bs-icon-list__icon bs-icon-list__num"' . $istyle . '>' . $num . '</span>';
	} else {
		$icon = ! empty( $item['icon'] ) ? '<span class="bs-icon-list__icon"' . $istyle . '>' . bst_icon( sanitize_key( $item['icon'] ), $size ) . '</span>' : '';
	}
	$sub   = trim( (string) ( $item['sub'] ?? '' ) );
	$badge = trim( (string) ( $item['badge'] ?? '' ) );
	$body  = '' !== $sub || '' !== $badge ? '<span class="bs-icon-list__text"><span class="bs-icon-list__main">' . $text . ( '' !== $badge ? ' <span class="bs-icon-list__badge">' . esc_html( $badge ) . '</span>' : '' ) . '</span>' . ( '' !== $sub ? '<span class="bs-icon-list__sub">' . esc_html( $sub ) . '</span>' : '' ) . '</span>' : '<span class="bs-icon-list__text">' . $text . '</span>';
	$inner = $icon . $body;
	if ( ! empty( $item['link'] ) ) {
		$inner = '<a href="' . esc_url( $item['link'] ) . '">' . $inner . '</a>';
	}
	$lis .= '<li class="bs-icon-list__item">' . $inner . '</li>';
}
$shape  = bst_choice( $a['iconShape'] ?? '', array( '', 'circle', 'square' ), '' );
$dstyle = bst_choice( $a['dividerStyle'] ?? '', array( '', 'dashed', 'dotted' ), '' );
$extra  = ( $shape ? ' bs-icon-list--shape-' . $shape : '' ) . ( 'center' === ( $a['iconAlign'] ?? '' ) ? ' bs-icon-list--icon-center' : '' ) . ( $numbered ? ' is-numbered' : '' ) . ( $dstyle ? ' bs-icon-list--div-' . $dstyle : '' ) . ( '' !== trim( (string) ( $a['separator'] ?? '' ) ) && 'inline' === $layout ? ' has-separator' : '' );
$class = 'bs-icon-list bs-icon-list--' . $layout . ' bs-icon-list--align-' . bst_choice( $a['align'] ?? 'left', array( 'left', 'center', 'right' ), 'left' ) . ( ! empty( $a['divider'] ) ? ' has-divider' : '' ) . ( ! empty( $a['iconBg'] ) ? ' has-icon-bg' : '' ) . $extra;
$style = bst_style_vars(
	array(
		'--bs-il-size'  => $size . 'px',
		'--bs-il-color' => bst_block_color( $a, 'iconColor' ),
		'--bs-il-gap'   => bst_block_num( $a, 'gap', 10, 0, 60 ) . 'px',
		'--bs-il-cols'  => bst_block_num( $a, 'columns', 2, 1, 4 ),
		'--bs-il-text'  => ! empty( $a['textSize'] ) ? bst_block_num( $a, 'textSize', 16, 10, 40 ) . 'px' : '',
		'--bs-il-cols-t' => ! empty( $a['columnsTablet'] ) ? bst_block_num( $a, 'columnsTablet', 2, 1, 4 ) : '',
		'--bs-il-cols-m' => ! empty( $a['columnsMobile'] ) ? bst_block_num( $a, 'columnsMobile', 1, 1, 4 ) : '',
		'--bs-il-colgap' => ! empty( $a['colGap'] ) ? bst_block_num( $a, 'colGap', 24, 0, 120 ) . 'px' : '',
		'--bs-il-dc'     => bst_block_color( $a, 'dividerColor' ),
		'--bs-il-hover'  => bst_block_color( $a, 'linkHover' ),
		'--bs-il-sub'    => bst_block_color( $a, 'subColor' ),
		'--bs-il-sep'    => 'inline' === $layout && '' !== trim( (string) ( $a['separator'] ?? '' ) ) ? '"' . str_replace( array( '"', '\\', '<', '>' ), '', mb_substr( trim( (string) $a['separator'] ), 0, 3 ) ) . '"' : '',
	)
);
printf( '<ul %1$s>%2$s</ul>', get_block_wrapper_attributes( array( 'class' => $class, 'style' => $style ) ), $lis ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
