<?php
/**
 * Navigation menu block: any WordPress menu, horizontal or vertical, with a mobile toggle.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a    = (array) $attributes;
$menu = (int) ( $a['menuId'] ?? 0 );
if ( ! $menu ) {
	$menus = wp_get_nav_menus();
	$menu  = $menus ? (int) $menus[0]->term_id : 0;
}
if ( ! $menu ) {
	return;
}
$layout = 'vertical' === ( $a['layout'] ?? '' ) ? 'vertical' : 'horizontal';
$mobile = bst_choice( (string) ( $a['mobile'] ?? 'toggle' ), array( 'toggle', 'stack', 'scroll', 'drawer', 'accordion' ), 'toggle' );
$mstyle = bst_choice( (string) ( $a['style'] ?? 'plain' ), array( 'plain', 'underline', 'pill', 'boxed', 'divider', 'accordion' ), 'plain' );
if ( 'accordion' === $mstyle ) {
	$layout = 'vertical';
}
$depth  = ! empty( $a['depth'] ) ? max( 1, min( 4, (int) $a['depth'] ) ) : ( 'vertical' === $layout ? 3 : 2 );
$sub    = 'accordion' === $mstyle ? 'click' : bst_choice( (string) ( $a['subOpen'] ?? '' ), array( '', 'click' ), '' );
$items  = wp_nav_menu(
	array(
		'menu'        => $menu,
		'container'   => false,
		'menu_class'  => 'bs-navmenu__list',
		'echo'        => false,
		'fallback_cb' => false,
		'depth'       => $depth,
	)
);
if ( ! $items ) {
	return;
}
$uid   = 'bs-navmenu-' . wp_unique_id();
$class = 'bs-navmenu bs-navmenu--' . $layout . ' bs-navmenu--' . $mstyle . ' bs-navmenu--m-' . $mobile . ' has-text-align-' . bst_choice( (string) ( $a['align'] ?? 'left' ), array( 'left', 'center', 'right' ), 'left' );
$style = bst_style_vars(
	array(
		'--bs-nm-gap'   => bst_block_num( $a, 'gap', 24, 0, 80 ) . 'px',
		'--bs-nm-size'  => ! empty( $a['size'] ) ? bst_block_num( $a, 'size', 0, 10, 40 ) . 'px' : '',
		'--bs-nm-color' => bst_block_color( $a, 'color' ),
		'--bs-nm-hover' => bst_block_color( $a, 'hover' ),
		'--bs-nm-act'   => bst_block_color( $a, 'activeColor' ),
		'--bs-nm-actbg' => bst_block_color( $a, 'activeBg' ),
		'--bs-nm-pad'   => isset( $a['itemPad'] ) && $a['itemPad'] >= 0 ? min( 40, (int) $a['itemPad'] ) . 'px' : '',
		'--bs-nm-gap-m' => ! empty( $a['gapMobile'] ) ? bst_block_num( $a, 'gapMobile', 8, 0, 60 ) . 'px' : '',
		'--bs-nm-fw'    => in_array( (string) ( $a['weight'] ?? '' ), array( '400', '500', '600', '700', '800' ), true ) ? $a['weight'] : '',
		'--bs-nm-tt'    => bst_choice( (string) ( $a['textCase'] ?? '' ), array( '', 'uppercase', 'capitalize', 'none' ), '' ),
	)
);
if ( $sub ) {
	$class .= ' is-sub-click';
}
$toggle = in_array( $mobile, array( 'toggle', 'drawer' ), true ) ? '<button type="button" class="bs-navmenu__toggle" aria-expanded="false" aria-controls="' . esc_attr( $uid ) . '" onclick="var o=this.getAttribute(\'aria-expanded\')===\'true\';this.setAttribute(\'aria-expanded\',o?\'false\':\'true\');this.parentNode.classList.toggle(\'is-open\',!o)">' . bst_icon( 'menu', 20 ) . '<span>' . esc_html__( 'Menu', 'bigspy-ai-theme-wp' ) . '</span></button>' : '';
printf(
	'<nav %1$s aria-label="%2$s">%3$s<div class="bs-navmenu__panel" id="%4$s">%5$s</div>' . ( 'drawer' === $mobile ? '<div class="bs-navmenu__shade" onclick="var t=this.parentNode.querySelector(\'.bs-navmenu__toggle\');t&&t.click()"></div>' : '' ) . '</nav>',
	get_block_wrapper_attributes( array( 'class' => $class, 'style' => $style ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_attr( wp_get_nav_menu_object( $menu ) ? wp_get_nav_menu_object( $menu )->name : __( 'Menu', 'bigspy-ai-theme-wp' ) ),
	$toggle, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_attr( $uid ),
	$items // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
