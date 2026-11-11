<?php
/**
 * Text block: rich HTML content (paragraphs, headings, lists, links…).
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a    = (array) $attributes;
$html = trim( (string) ( $a['html'] ?? '' ) );
if ( '' === $html ) {
	return;
}

$cols  = ! empty( $a['columns'] ) && $a['columns'] > 1;
$clamp = ! empty( $a['clampLines'] ) ? (int) bst_block_num( $a, 'clampLines', 0, 1, 40 ) : 0;
$align = bst_choice( $a['align'] ?? '', array( '', 'left', 'center', 'right', 'justify' ), '' );
$style = bst_style_vars(
	array(
		'--bs-text-size'  => ! empty( $a['size'] ) ? bst_block_num( $a, 'size', 0, 10, 80 ) . 'px' : '',
		'--bs-text-color' => bst_block_color( $a, 'color' ),
		'--bs-text-max'   => ! empty( $a['maxWidth'] ) ? bst_block_num( $a, 'maxWidth', 0, 100, 2000 ) . 'px' : '',
		'--bs-text-size-t' => ! empty( $a['sizeTablet'] ) ? bst_block_num( $a, 'sizeTablet', 0, 10, 80 ) . 'px' : '',
		'--bs-text-size-m' => ! empty( $a['sizeMobile'] ) ? bst_block_num( $a, 'sizeMobile', 0, 10, 80 ) . 'px' : '',
		'--bs-text-lh'     => ! empty( $a['lineHeight'] ) ? min( 3, max( 0.8, (float) $a['lineHeight'] ) ) : '',
		'--bs-text-ls'     => ! empty( $a['letterSpacing'] ) ? min( 10, max( -2, (float) $a['letterSpacing'] ) ) . 'px' : '',
		'--bs-text-weight' => in_array( (string) ( $a['weight'] ?? '' ), array( '300', '400', '500', '600', '700', '800' ), true ) ? $a['weight'] : '',
		'--bs-text-transform' => bst_choice( $a['transform'] ?? '', array( '', 'uppercase', 'lowercase', 'capitalize' ), '' ),
		'--bs-text-link'   => bst_block_color( $a, 'linkColor' ),
		'--bs-text-cols'   => ! empty( $a['columns'] ) && $a['columns'] > 1 ? bst_block_num( $a, 'columns', 1, 1, 4 ) : '',
		'--bs-text-link-h' => bst_block_color( $a, 'linkColorHover' ),
		'--bs-text-col-gap' => $cols && ! empty( $a['columnGap'] ) ? bst_block_num( $a, 'columnGap', 0, 0, 200 ) . 'px' : '',
		'--bs-text-col-rule' => $cols ? bst_block_color( $a, 'columnRuleColor' ) : '',
		'--bs-text-clamp'  => $clamp ? $clamp : '',
	)
);
$list  = bst_choice( (string) ( $a['listStyle'] ?? '' ), array( '', 'check', 'arrow', 'number' ), '' );
$quote = bst_choice( (string) ( $a['quoteStyle'] ?? '' ), array( '', 'card', 'big', 'line' ), '' );
$links = bst_choice( (string) ( $a['linkStyle'] ?? '' ), array( '', 'underline', 'hover', 'highlight' ), '' );
$extra = ( ! empty( $a['columns'] ) && $a['columns'] > 1 ? ' has-columns' . ( ! empty( $a['columnsMobile'] ) ? ' keep-columns' : '' ) : '' ) . ( ! empty( $a['dropCap'] ) ? ' has-drop-cap' : '' )
	. ( $list ? ' bs-text--list-' . $list : '' ) . ( $quote ? ' bs-text--quote-' . $quote : '' ) . ( $links ? ' bs-text--link-' . $links : '' )
	. ( $cols && '' !== bst_block_color( $a, 'columnRuleColor' ) ? ' has-column-rule' : '' ) . ( $clamp ? ' bs-text--clamp' : '' );

// Plain text typed without a block tag still becomes a paragraph.
if ( ! preg_match( '/^\s*<(p|h[1-6]|ul|ol|blockquote|div|table|figure|pre)\b/i', $html ) ) {
	$html = wpautop( $html );
}

$body = do_shortcode( wp_kses_post( $html ) );
// Line limit: the body is clipped and main.js adds the “Read more” toggle; without script everything shows.
if ( $clamp ) {
	$more = trim( (string) ( $a['moreText'] ?? '' ) );
	$body = '<div class="bs-text__body" id="' . esc_attr( wp_unique_id( 'bs-text-' ) ) . '">' . $body . '</div><button type="button" class="bs-text__more" aria-expanded="false" data-less="' . esc_attr__( 'Show less', 'bigspy-ai-theme-wp' ) . '" hidden>' . esc_html( '' !== $more ? $more : __( 'Read more', 'bigspy-ai-theme-wp' ) ) . '</button>';
}

printf(
	'<div %1$s>%2$s</div>',
	get_block_wrapper_attributes( array( 'class' => 'bs-text' . ( $align ? ' has-text-align-' . $align : '' ) . $extra, 'style' => $style ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$body // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
