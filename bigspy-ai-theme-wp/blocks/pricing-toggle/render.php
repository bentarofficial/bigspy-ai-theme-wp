<?php
/**
 * Pricing cards with a switch between two billing periods.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a     = (array) $attributes;
$plans = array_filter( (array) ( $a['plans'] ?? array() ), 'is_array' );
if ( ! $plans ) {
	return;
}
$start = 'b' === ( $a['start'] ?? 'a' ) ? 'b' : 'a';
$uid   = 'bs-pt-' . wp_unique_id();
$cards = '';
foreach ( $plans as $p ) {
	$features = array_filter( array_map( 'trim', explode( "\n", (string) ( $p['features'] ?? '' ) ) ) );
	$cards   .= '<div class="bs-ptg__card' . ( ! empty( $p['featured'] ) ? ' is-featured' : '' ) . '">'
		. '<h3 class="bs-ptg__title">' . esc_html( $p['title'] ?? '' ) . '</h3>'
		. ( ! empty( $p['desc'] ) ? '<p class="bs-ptg__desc">' . esc_html( $p['desc'] ) . '</p>' : '' )
		. '<div class="bs-ptg__price"><span class="bs-ptg__amount" data-a="' . esc_attr( $p['priceA'] ?? '' ) . '" data-b="' . esc_attr( $p['priceB'] ?? '' ) . '">' . esc_html( 'b' === $start ? ( $p['priceB'] ?? '' ) : ( $p['priceA'] ?? '' ) ) . '</span>'
		. '<span class="bs-ptg__period" data-a="' . esc_attr( $a['periodA'] ?? '' ) . '" data-b="' . esc_attr( $a['periodB'] ?? '' ) . '">' . esc_html( 'b' === $start ? ( $a['periodB'] ?? '' ) : ( $a['periodA'] ?? '' ) ) . '</span></div>'
		. ( $features ? '<ul class="bs-ptg__features">' . implode( '', array_map( function ( $f ) { return '<li>' . bst_icon( 'check', 16 ) . '<span>' . esc_html( $f ) . '</span></li>'; }, $features ) ) . '</ul>' : '' )
		. ( ! empty( $p['button'] ) ? '<a class="bs-ptg__btn" href="' . esc_url( ! empty( $p['url'] ) ? $p['url'] : '#' ) . '">' . esc_html( $p['button'] ) . '</a>' : '' )
		. '</div>';
}
$badge = trim( (string) ( $a['badgeB'] ?? '' ) );
printf(
	'<div %1$s data-bs-pricing-toggle data-state="%2$s"><div class="bs-ptg__switch" role="group" aria-label="%3$s"><button type="button" data-set="a" aria-pressed="%4$s">%5$s</button><button type="button" data-set="b" aria-pressed="%6$s">%7$s%8$s</button></div><div class="bs-ptg__grid" id="%9$s" style="--bs-ptg-cols:%10$d">%11$s</div></div>',
	get_block_wrapper_attributes( array( 'class' => 'bs-ptg', 'style' => bst_style_vars( array( '--bs-ptg-accent' => bst_block_color( $a, 'accent' ) ) ) ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_attr( $start ),
	esc_attr__( 'Billing period', 'bigspy-ai-theme-wp' ),
	'a' === $start ? 'true' : 'false',
	esc_html( $a['labelA'] ?? '' ),
	'b' === $start ? 'true' : 'false',
	esc_html( $a['labelB'] ?? '' ),
	$badge ? ' <span class="bs-ptg__badge">' . esc_html( $badge ) . '</span>' : '',
	esc_attr( $uid ),
	min( 4, count( $plans ) ),
	$cards // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
