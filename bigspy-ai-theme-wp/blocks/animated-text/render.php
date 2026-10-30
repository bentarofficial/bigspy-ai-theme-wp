<?php
/** Animated Text: the full sentence is server-rendered; motion-text.js only enhances it. */
defined( 'ABSPATH' ) || exit;
$a      = $attributes;
$text   = trim( wp_strip_all_tags( (string) ( $a['text'] ?? '' ) ) );
$after  = trim( wp_strip_all_tags( (string) ( $a['after'] ?? '' ) ) );
$words  = array_values( array_filter( array_map( 'trim', preg_split( '/[\r\n|]+/', wp_strip_all_tags( (string) ( $a['words'] ?? '' ) ) ) ) ) );
$effect = bst_choice( $a['effect'] ?? 'reveal', array( 'reveal', 'words', 'typewriter', 'rotate', 'scramble', 'gradient', 'marquee' ), 'reveal' );
if ( '' === $text && ! $words ) {
	return;
}
$tag   = bst_choice( $a['tag'] ?? 'h2', array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div' ), 'h2' );
$align = bst_choice( $a['align'] ?? '', array( '', 'left', 'center', 'right' ), '' );
$style = bst_style_vars(
	array(
		'--bs-atext-size'   => ! empty( $a['size'] ) ? bst_block_num( $a, 'size', 0, 10, 240 ) . 'px' : '',
		'--bs-atext-size-t' => ! empty( $a['sizeTablet'] ) ? bst_block_num( $a, 'sizeTablet', 0, 10, 240 ) . 'px' : '',
		'--bs-atext-size-m' => ! empty( $a['sizeMobile'] ) ? bst_block_num( $a, 'sizeMobile', 0, 10, 240 ) . 'px' : '',
		'--bs-atext-weight' => bst_choice( (string) ( $a['weight'] ?? '' ), array( '', '300', '400', '500', '600', '700', '800', '900' ), '' ),
		'--bs-atext-color'  => bst_block_color( $a, 'color' ),
		'--bs-atext-accent' => bst_block_color( $a, 'accent' ),
		'--bs-atext-accent2' => bst_block_color( $a, 'accent2' ),
	)
);
$config = array( 'effect' => $effect, 'speed' => bst_block_num( $a, 'speed', 1, 0.25, 4 ), 'loop' => ! empty( $a['loop'] ) );
// Rotating / typed words: every word is in the markup; the script shows one at a time.
$list = '';
if ( $words && in_array( $effect, array( 'rotate', 'typewriter' ), true ) ) {
	$list = ' <span class="bs-atext__words">';
	foreach ( $words as $i => $word ) {
		$list .= '<span class="bs-atext__word' . ( 0 === $i ? ' is-active' : '' ) . '">' . esc_html( $word ) . '</span>';
	}
	$list .= '</span>';
} elseif ( $words ) {
	$text = trim( $text . ' ' . $words[0] );
}
$body  = esc_html( $text ) . $list . ( '' !== $after ? ' ' . esc_html( $after ) : '' );
$plain = trim( $text . ( $words && $list ? ' ' . implode( ', ', $words ) : '' ) . ( '' !== $after ? ' ' . $after : '' ) );
$inner = 'marquee' === $effect ? '<span class="bs-atext__track"><span class="bs-atext__run">' . $body . '</span><span class="bs-atext__run" aria-hidden="true">' . $body . '</span></span>' : '<span class="bs-atext__text">' . $body . '</span>';
printf(
	'<%1$s %2$s data-bs-text="%3$s">%4$s</%1$s>',
	esc_html( $tag ),
	get_block_wrapper_attributes( array( 'class' => 'bs-atext bs-atext--' . $effect . ( $align ? ' has-text-align-' . $align : '' ), 'style' => $style, 'aria-label' => '' !== trim( (string) ( $a['label'] ?? '' ) ) ? trim( (string) $a['label'] ) : $plain ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_attr( wp_json_encode( $config ) ),
	$inner // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
);
