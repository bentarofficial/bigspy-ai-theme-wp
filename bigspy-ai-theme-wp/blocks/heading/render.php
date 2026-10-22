<?php
/**
 * Heading block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a    = (array) $attributes;
$text = wp_kses_post( (string) ( $a['text'] ?? '' ) );
if ( '' === trim( wp_strip_all_tags( $text ) ) ) {
	return;
}

$tag   = bst_choice( $a['tag'] ?? 'h2', array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div' ), 'h2' );
$align = bst_choice( $a['align'] ?? '', array( '', 'left', 'center', 'right' ), '' );
$link  = esc_url( $a['link'] ?? '' );
$style = bst_style_vars(
	array(
		'--bs-heading-size'   => ! empty( $a['size'] ) ? bst_block_num( $a, 'size', 0, 10, 160 ) . 'px' : '',
		'--bs-heading-size-tablet' => ! empty( $a['sizeTablet'] ) ? bst_block_num( $a, 'sizeTablet', 0, 10, 160 ) . 'px' : '',
		'--bs-heading-size-mobile' => ! empty( $a['sizeMobile'] ) ? bst_block_num( $a, 'sizeMobile', 0, 10, 160 ) . 'px' : '',
		'--bs-heading-leading' => ! empty( $a['lineHeight'] ) ? bst_block_num( $a, 'lineHeight', 0, 0.8, 2.5 ) : '',
		'--bs-heading-max' => ! empty( $a['maxWidth'] ) ? bst_block_num( $a, 'maxWidth', 0, 200, 1600 ) . 'px' : '',
		'--bs-heading-color'  => bst_block_color( $a, 'color' ),
		'--bs-heading-weight' => bst_choice( (string) ( $a['weight'] ?? '' ), array( '', '300', '400', '500', '600', '700', '800', '900' ), '' ),
		'--bs-grad-from'      => bst_block_color( $a, 'gradientFrom' ),
		'--bs-grad-to'        => bst_block_color( $a, 'gradientTo' ),
		'--bs-grad-angle'     => ! empty( $a['gradient'] ) ? bst_block_num( $a, 'gradientAngle', 90, 0, 360 ) . 'deg' : '',
		'--bs-hl-color'       => bst_block_color( $a, 'highlightColor' ),
	)
);
$hl     = bst_choice( (string) ( $a['highlight'] ?? '' ), array( '', 'underline', 'marker', 'brush', 'strike' ), '' );
$extra  = ( ! empty( $a['gradient'] ) ? ' bs-heading--gradient' : '' ) . ( $hl ? ' bs-heading--hl bs-heading--hl-' . $hl : '' );
if ( ! empty( $a['gradient'] ) || $hl ) {
	$text = '<span class="bs-heading__text">' . $text . '</span>';
}

printf(
	'<%1$s %2$s>%3$s</%1$s>',
	esc_html( $tag ),
	get_block_wrapper_attributes( array( 'class' => 'bs-heading' . ( ! empty( $a['balance'] ) ? ' bs-heading--balanced' : '' ) . ( $align ? ' has-text-align-' . $align : '' ) . $extra, 'style' => $style ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$link ? '<a href="' . $link . '">' . $text . '</a>' : $text // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
