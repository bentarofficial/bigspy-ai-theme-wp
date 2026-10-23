<?php
/**
 * Button block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a    = (array) $attributes;
$text = wp_kses_post( (string) ( $a['text'] ?? '' ) );
if ( '' === trim( wp_strip_all_tags( $text ) ) ) {
	return;
}

$style = bst_choice( $a['style'] ?? 'primary', array( 'primary', 'secondary', 'outline', 'white', 'link', 'gradient', 'soft', 'shade', 'bevel', 'gloss', 'underline', 'glass' ), 'primary' );
$scheme = bst_choice( $a['scheme'] ?? '', array( '', 'primary', 'secondary', 'success', 'alert', 'warning', 'dark', 'white' ), '' );
$effect = bst_choice( $a['hoverEffect'] ?? '', array( '', 'lift', 'grow', 'shine', 'pulse', 'fill', 'icon-move', 'icon-reveal' ), '' );
$size  = bst_choice( $a['size'] ?? 'md', array( 'xs', 'sm', 'md', 'lg', 'xl' ), 'md' );
$url   = trim( (string) ( $a['url'] ?? '' ) );
$icon  = ! empty( $a['icon'] ) ? bst_icon( sanitize_key( $a['icon'] ), 'lg' === $size ? 20 : 17 ) : '';
$after = 'before' !== ( $a['iconPosition'] ?? 'after' );

$classes = 'bs-btn bs-btn--' . $style . ( 'md' !== $size ? ' bs-btn--' . $size : '' ) . ( ! empty( $a['fullWidth'] ) ? ' bs-btn--block' : '' );
$css     = isset( $a['radius'] ) && $a['radius'] >= 0 ? 'border-radius:' . bst_block_num( $a, 'radius', 0, 0, 100 ) . 'px' : '';
$colors  = bst_style_vars(
	array(
		'--bsb-bg'  => bst_block_color( $a, 'bgColor' ),
		'--bsb-c'   => bst_block_color( $a, 'textColor' ),
		'--bsb-bc'  => bst_block_color( $a, 'borderColor' ),
		'--bsb-hbg' => bst_block_color( $a, 'hoverBg' ),
		'--bsb-hc'  => bst_block_color( $a, 'hoverText' ),
	)
);
if ( $colors || $scheme || in_array( $style, array( 'gradient', 'soft', 'shade', 'bevel', 'gloss', 'underline', 'glass' ), true ) ) {
	$classes .= ' has-custom-colors';
}
if ( $scheme ) {
	$classes .= ' bs-btn--c-' . $scheme;
}
if ( $effect ) {
	$classes .= ' bs-btn--fx-' . $effect;
}
$grad = bst_style_vars(
	array(
		'--bsb-bg2'   => bst_block_color( $a, 'bgColor2' ),
		'--bsb-angle' => 'gradient' === $style ? bst_block_num( $a, 'gradientAngle', 135, 0, 360 ) . 'deg' : '',
	)
);
$num     = function ( $key, $max ) use ( $a ) {
	$v = isset( $a[ $key ] ) ? (float) $a[ $key ] : 0;
	return $v > 0 ? min( $max, $v ) . 'px' : '';
};
$shadows = array(
	'sm'   => '0 2px 6px rgba(0,0,0,.12)',
	'md'   => '0 8px 20px -6px rgba(0,0,0,.25)',
	'lg'   => '0 16px 34px -10px rgba(0,0,0,.35)',
	'glow' => '0 6px 22px -4px color-mix(in srgb, var(--bsb-bg, var(--bs-primary)) 60%, transparent)',
);
$sizing  = bst_style_vars(
	array(
		'--bsb-h'   => $num( 'btnHeight', 200 ),
		'--bsb-hm'  => $num( 'btnHeightMobile', 200 ),
		'--bsb-w'   => $num( 'btnWidth', 800 ),
		'--bsb-px'  => isset( $a['padX'] ) && $a['padX'] >= 0 ? min( 100, (int) $a['padX'] ) . 'px' : '',
		'--bsb-fs'  => $num( 'fontSize', 60 ),
		'--bsb-fsm' => $num( 'fontSizeMobile', 60 ),
		'--bsb-fw'  => in_array( (string) ( $a['fontWeight'] ?? '' ), array( '400', '500', '600', '700', '800' ), true ) ? $a['fontWeight'] : '',
		'--bsb-bw'  => isset( $a['borderWidth'] ) && $a['borderWidth'] >= 0 ? min( 10, (int) $a['borderWidth'] ) . 'px' : '',
		'--bsb-sh'  => $shadows[ $a['btnShadow'] ?? '' ] ?? '',
		'--bsb-is'  => $num( 'iconSize', 60 ),
	)
);
if ( $sizing ) {
	$css     .= ( $css ? ';' : '' ) . $sizing;
	$classes .= ' bs-btn--sized';
}
if ( $grad ) {
	$css .= ( $css ? ';' : '' ) . $grad;
}
if ( $colors ) {
	$css     .= ( $css ? ';' : '' ) . $colors;
}
$case = bst_choice( $a['letterCase'] ?? '', array( '', 'uppercase', 'none', 'capitalize' ), '' );
if ( $case ) {
	$classes .= ' bs-btn--case-' . $case;
}
$sub     = trim( (string) ( $a['subtext'] ?? '' ) );
$inner   = '' !== $sub ? '<span class="bs-btn__text"><span>' . $text . '</span><small class="bs-btn__sub">' . esc_html( $sub ) . '</small></span>' : '<span>' . $text . '</span>';
$label   = ( $icon && ! $after ? $icon : '' ) . $inner . ( $icon && $after ? $icon : '' );
$extra   = array( 'class' => $classes, 'style' => $css );
if ( ! empty( $a['ariaLabel'] ) ) {
	$extra['aria-label'] = sanitize_text_field( $a['ariaLabel'] );
}
$rel = trim( ( ! empty( $a['newTab'] ) ? 'noopener ' : '' ) . preg_replace( '/[^a-z ]/', '', strtolower( (string) ( $a['linkRel'] ?? '' ) ) ) );

if ( '' === $url ) {
	printf( '<span %1$s>%2$s</span>', get_block_wrapper_attributes( $extra ), $label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}

printf(
	'<a %1$s href="%2$s"%3$s>%4$s</a>',
	get_block_wrapper_attributes( $extra ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_url( $url ),
	( ! empty( $a['newTab'] ) ? ' target="_blank"' : '' ) . ( $rel ? ' rel="' . esc_attr( $rel ) . '"' : '' ),
	$label // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
