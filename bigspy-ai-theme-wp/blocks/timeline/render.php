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
$layout = bst_choice( (string) ( $a['layout'] ?? 'alternate' ), array( 'alternate', 'left' ), 'alternate' );
$marker = bst_choice( (string) ( $a['marker'] ?? 'dot' ), array( 'dot', 'number', 'icon' ), 'dot' );
$out    = '';
$i      = 0;
foreach ( $items as $it ) {
	++$i;
	$dot = 'number' === $marker ? (string) $i : ( 'icon' === $marker && ! empty( $it['icon'] ) ? bst_icon( sanitize_key( $it['icon'] ), 16 ) : '' );
	$out .= '<li class="bs-timeline__item"><span class="bs-timeline__marker" aria-hidden="true">' . $dot . '</span><div class="bs-timeline__card">'
		. ( ! empty( $it['date'] ) ? '<span class="bs-timeline__date">' . esc_html( $it['date'] ) . '</span>' : '' )
		. ( ! empty( $it['title'] ) ? '<h3 class="bs-timeline__title">' . wp_kses_post( $it['title'] ) . '</h3>' : '' )
		. ( ! empty( $it['text'] ) ? '<div class="bs-timeline__text">' . wp_kses_post( wpautop( $it['text'] ) ) . '</div>' : '' )
		. '</div></li>';
}
printf(
	'<ol %1$s>%2$s</ol>',
	get_block_wrapper_attributes( array( 'class' => 'bs-timeline bs-timeline--' . $layout . ' bs-timeline--' . $marker, 'style' => bst_style_vars( array( '--bs-tl-accent' => bst_block_color( $a, 'accent' ) ) ) ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$out // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
