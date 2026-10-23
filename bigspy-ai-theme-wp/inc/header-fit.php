<?php
/**
 * Header fit options: keep the desktop header on one line on narrower screens
 * (hide the search field below a width, no-wrap menu, compact menu and logo).
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_defaults',
	function ( $defaults ) {
		return $defaults + array(
			'header_search_hide_below' => 0,
			'header_menu_nowrap'       => false,
			'header_compact_below'     => 0,
			'header_compact_logo'      => 0,
		);
	}
);

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$fields[] = array( 'id' => 'header_search_hide_below', 'section' => 'bst_he_search_form', 'type' => 'number', 'label' => __( 'Hide the search field when the screen is narrower than (px, 0 = never)', 'bigspy-ai-theme-wp' ), 'description' => __( 'Keeps menu and buttons on one line on small laptops, e.g. 1150.', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 0, 'max' => 1600, 'step' => 10 ) );
		$fields[] = array( 'id' => 'header_menu_nowrap', 'section' => 'bst_he_nav', 'type' => 'checkbox', 'label' => __( 'Keep the menu and header buttons on one line', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'header_compact_below', 'section' => 'bst_he_nav', 'type' => 'number', 'label' => __( 'Compact menu below this screen width (px, 0 = off)', 'bigspy-ai-theme-wp' ), 'description' => __( 'Smaller menu text and spacing between the desktop breakpoint and this width, e.g. 1100.', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 0, 'max' => 1600, 'step' => 10 ) );
		$fields[] = array( 'id' => 'header_compact_logo', 'section' => 'bst_he_nav', 'type' => 'number', 'label' => __( 'Logo width in compact mode (px, 0 = unchanged)', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 0, 'max' => 400, 'step' => 5 ) );
		return $fields;
	},
	35
);

add_action(
	'wp_enqueue_scripts',
	function () {
		$css  = '';
		$hide = (int) bst_mod( 'header_search_hide_below' );
		if ( $hide > 0 ) {
			$css .= '@media(max-width:' . $hide . 'px){.bs-header-row--desktop .bs-search-form--header,.bs-header-row--desktop .bs-header-el--search-form{display:none}}';
		}
		if ( bst_mod( 'header_menu_nowrap' ) ) {
			$css .= '.bs-header-row--desktop .bs-menu{flex-wrap:nowrap}.bs-header-row--desktop .bs-btn{white-space:nowrap}';
		}
		$compact = (int) bst_mod( 'header_compact_below' );
		if ( $compact > 0 ) {
			$logo = (int) bst_mod( 'header_compact_logo' );
			$css .= '@media(min-width:850px) and (max-width:' . $compact . 'px){.bs-header-row--desktop .bs-menu>li>a{font-size:14px;padding-left:8px;padding-right:8px}' . ( $logo ? '.bs-header-row--desktop .bs-logo{width:' . $logo . 'px}' : '' ) . '}';
		}
		if ( $css ) {
			wp_add_inline_style( 'bst-main', $css );
		}
	},
	30
);
