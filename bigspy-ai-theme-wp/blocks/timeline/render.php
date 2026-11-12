<?php
/**
 * Timeline block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a     = (array) $attributes;
$items = array_filter( (array) ( $a['items'] ?? array() ), 'is_array' );
if ( ! $items ) {
	return;
}
$layout = bst_choice( (string) ( $a['layout'] ?? 'alternate' ), array( 'alternate', 'left', 'right', 'horizontal', 'scroll', 'compact' ), 'alternate' );
$marker = bst_choice( (string) ( $a['marker'] ?? 'dot' ), array( 'dot', 'number', 'icon', 'date', 'image' ), 'dot' );
$out    = '';
$date_pos = bst_choice( (string) ( $a['datePos'] ?? '' ), array( '', 'opposite' ), '' );
$card     = bst_choice( (string) ( $a['cardStyle'] ?? '' ), array( '', 'none', 'outline', 'arrow' ), '' );
$line     = bst_choice( (string) ( $a['lineStyle'] ?? '' ), array( '', 'dashed', 'dotted', 'none' ), '' );
$cls      = ( $card ? ' bs-timeline--card-' . $card : '' ) . ( $line ? ' bs-timeline--line-' . $line : '' ) . ( $date_pos ? ' bs-timeline--date-' . $date_pos : '' ) . ( ! empty( $a['lineFill'] ) ? ' has-line-fill' : '' );
$vars     = bst_style_vars(
	array(
		'--bs-tl-accent' => bst_block_color( $a, 'accent' ),
		'--bs-tl-lw'     => ! empty( $a['lineWidth'] ) ? bst_block_num( $a, 'lineWidth', 2, 1, 12 ) . 'px' : '',
		'--bs-tl-lc'     => bst_block_color( $a, 'lineColor' ),
		'--bs-tl-title'  => bst_block_color( $a, 'titleColor' ),
		'--bs-tl-date'   => bst_block_color( $a, 'dateColor' ),
		'--bs-tl-text'   => bst_block_color( $a, 'textColor' ),
		'--bs-tl-bg'     => bst_block_color( $a, 'cardBg' ),
	)
);
$i      = 0;
foreach ( $items as $it ) {
	++$i;
	$dot = 'number' === $marker ? (string) $i : ( 'icon' === $marker && ! empty( $it['icon'] ) ? bst_icon( sanitize_key( $it['icon'] ), 16 ) : '' );
	if ( 'date' === $marker ) {
		$dot = esc_html( (string) ( $it['date'] ?? '' ) );
	} elseif ( 'image' === $marker ) {
		$img = ! empty( $it['imageId'] ) ? wp_get_attachment_image( (int) $it['imageId'], 'thumbnail', false, array( 'alt' => '' ) ) : '';
		$dot = $img ? $img : ( ! empty( $it['imageUrl'] ) ? '<img src="' . esc_url( $it['imageUrl'] ) . '" alt="" loading="lazy">' : '' );
	}
	$date  = ! empty( $it['date'] ) && 'date' !== $marker ? '<span class="bs-timeline__date">' . esc_html( $it['date'] ) . '</span>' : '';
	$extra = '';
	if ( ! empty( $it['badge'] ) ) {
		$extra .= '<span class="bs-timeline__badge">' . esc_html( $it['badge'] ) . '</span>';
	}
	if ( 'image' !== $marker && ( ! empty( $it['imageId'] ) || ! empty( $it['imageUrl'] ) ) ) {
		$extra .= '<div class="bs-timeline__media">' . ( ! empty( $it['imageId'] ) ? wp_get_attachment_image( (int) $it['imageId'], 'medium_large', false, array( 'loading' => 'lazy' ) ) : '<img src="' . esc_url( $it['imageUrl'] ) . '" alt="" loading="lazy">' ) . '</div>';
	}
	$link = ! empty( $it['link'] ) ? esc_url( $it['link'] ) : '';
	$btn  = $link && ! empty( $it['btnText'] ) ? '<a class="bs-timeline__btn" href="' . $link . '">' . esc_html( $it['btnText'] ) . '</a>' : '';
	$tit  = ! empty( $it['title'] ) ? wp_kses_post( $it['title'] ) : '';
	if ( $tit && $link && ! $btn ) {
		$tit = '<a href="' . $link . '">' . $tit . '</a>';
	}
	$side = 'opposite' === $date_pos && $date ? '<span class="bs-timeline__side">' . $date . '</span>' : '';
	$out .= '<li class="bs-timeline__item"><span class="bs-timeline__marker" aria-hidden="true">' . $dot . '</span>' . $side . '<div class="bs-timeline__card">'
		. $extra
		. ( $side ? '' : $date )
		. ( $tit ? '<h3 class="bs-timeline__title">' . $tit . '</h3>' : '' )
		. ( ! empty( $it['text'] ) ? '<div class="bs-timeline__text">' . wp_kses_post( wpautop( $it['text'] ) ) . '</div>' : '' )
		. $btn
		. '</div></li>';
}
printf(
	'<ol %1$s>%2$s</ol>',
	get_block_wrapper_attributes( array( 'class' => 'bs-timeline bs-timeline--' . $layout . ' bs-timeline--' . $marker . $cls, 'style' => $vars ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$out // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
