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
$mobile = bst_choice( (string) ( $a['mobile'] ?? 'toggle' ), array( 'toggle', 'stack', 'scroll' ), 'toggle' );
$items  = wp_nav_menu(
	array(
		'menu'        => $menu,
		'container'   => false,
		'menu_class'  => 'bs-navmenu__list',
		'echo'        => false,
		'fallback_cb' => false,
		'depth'       => 'vertical' === $layout ? 3 : 2,
	)
);
if ( ! $items ) {
	return;
}
$uid   = 'bs-navmenu-' . wp_unique_id();
$class = 'bs-navmenu bs-navmenu--' . $layout . ' bs-navmenu--' . bst_choice( (string) ( $a['style'] ?? 'plain' ), array( 'plain', 'underline', 'pill', 'boxed' ), 'plain' ) . ' bs-navmenu--m-' . $mobile . ' has-text-align-' . bst_choice( (string) ( $a['align'] ?? 'left' ), array( 'left', 'center', 'right' ), 'left' );
$style = bst_style_vars(
	array(
		'--bs-nm-gap'   => bst_block_num( $a, 'gap', 24, 0, 80 ) . 'px',
		'--bs-nm-size'  => ! empty( $a['size'] ) ? bst_block_num( $a, 'size', 0, 10, 40 ) . 'px' : '',
		'--bs-nm-color' => bst_block_color( $a, 'color' ),
		'--bs-nm-hover' => bst_block_color( $a, 'hover' ),
	)
);
$toggle = 'toggle' === $mobile ? '<button type="button" class="bs-navmenu__toggle" aria-expanded="false" aria-controls="' . esc_attr( $uid ) . '" onclick="var o=this.getAttribute(\'aria-expanded\')===\'true\';this.setAttribute(\'aria-expanded\',o?\'false\':\'true\');this.parentNode.classList.toggle(\'is-open\',!o)">' . bst_icon( 'menu', 20 ) . '<span>' . esc_html__( 'Menu', 'bigspy-ai-theme-wp' ) . '</span></button>' : '';
printf(
	'<nav %1$s aria-label="%2$s">%3$s<div class="bs-navmenu__panel" id="%4$s">%5$s</div></nav>',
	get_block_wrapper_attributes( array( 'class' => $class, 'style' => $style ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_attr( wp_get_nav_menu_object( $menu ) ? wp_get_nav_menu_object( $menu )->name : __( 'Menu', 'bigspy-ai-theme-wp' ) ),
	$toggle, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_attr( $uid ),
	$items // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
