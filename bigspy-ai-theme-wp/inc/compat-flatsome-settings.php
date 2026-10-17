<?php
/**
 * Inherit the design settings of a Flatsome site.
 *
 * When Flatsome (or a Flatsome child theme) was configured on this site, its
 * saved options are translated into BIGSPY *defaults*: logo, colors, fonts,
 * header layout, heights, buttons, contact details, shop columns… Nothing is
 * written to the database – every option the user later changes in the
 * Customizer simply overrides the inherited value. Turn it off with
 * `add_filter( 'bst_inherit_flatsome', '__return_false' )`.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Saved theme mods of the Flatsome child / parent theme, or an empty array.
 */
function bst_flatsome_mods() {
	static $mods = null;
	if ( null !== $mods ) {
		return $mods;
	}
	$mods = array();
	if ( ! apply_filters( 'bst_inherit_flatsome', true ) ) {
		return $mods;
	}

	$candidates = array( 'flatsome-child', 'flatsome' );
	foreach ( wp_get_themes() as $stylesheet => $theme ) {
		if ( 'flatsome' === $theme->get_template() && ! in_array( $stylesheet, $candidates, true ) ) {
			array_unshift( $candidates, $stylesheet );
		}
	}
	foreach ( $candidates as $stylesheet ) {
		$saved = get_option( 'theme_mods_' . $stylesheet );
		if ( is_array( $saved ) && count( $saved ) > count( $mods ) ) {
			$mods = $saved;
		}
	}
	return $mods;
}

/**
 * Hex color from "#rgb", "#rrggbb" or "rgb()/rgba()" (alpha is dropped).
 */
function bst_flatsome_hex( $value ) {
	$value = trim( (string) $value );
	if ( preg_match( '/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $value ) ) {
		return strtolower( $value );
	}
	if ( preg_match( '/^rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})/i', $value, $m ) ) {
		return sprintf( '#%02x%02x%02x', min( 255, $m[1] ), min( 255, $m[2] ), min( 255, $m[3] ) );
	}
	return '';
}

function bst_flatsome_font( $mods, $key ) {
	$family = isset( $mods[ $key ]['font-family'] ) ? trim( (string) $mods[ $key ]['font-family'] ) : '';
	return preg_match( '/^[A-Za-z0-9 ]{2,40}$/', $family ) ? $family : '';
}

function bst_flatsome_font_weight( $mods, $key, $default ) {
	$variant = isset( $mods[ $key ]['variant'] ) ? (string) $mods[ $key ]['variant'] : '';
	if ( 'regular' === $variant || 'italic' === $variant ) {
		return '400';
	}
	$weight = (string) (int) $variant;
	return in_array( $weight, array( '300', '400', '500', '600', '700', '800' ), true ) ? $weight : $default;
}

/**
 * Fonts chosen in Flatsome are offered (and loaded) even when they are not in the built-in list.
 */
function bst_flatsome_fonts( $fonts ) {
	$mods = bst_flatsome_mods();
	foreach ( array( 'type_texts', 'type_headings', 'type_nav' ) as $key ) {
		$family = bst_flatsome_font( $mods, $key );
		if ( $family && ! isset( $fonts[ $family ] ) ) {
			$fonts[ $family ] = array( 'label' => $family, 'stack' => '"' . $family . '", sans-serif', 'google' => true );
		}
	}
	return $fonts;
}
add_filter( 'bst_fonts', 'bst_flatsome_fonts' );

/**
 * Flatsome header element => BIGSPY header element.
 */
function bst_flatsome_elements( $list ) {
	$map = array(
		'search-form'  => 'search_form',
		'search'       => 'search',
		'cart'         => 'cart',
		'account'      => 'account',
		'nav'          => 'nav',
		'nav-top'      => 'nav_secondary',
		'nav-vertical' => 'categories',
		'button-1'     => 'button_1',
		'button-2'     => 'button_2',
		'social'       => 'social',
		'contact'      => 'contact',
		'html'         => 'html_1',
		'html-2'       => 'html_2',
		'html-3'       => 'html_3',
		'menu-icon'    => 'nav_icon',
	);
	$out = array();
	foreach ( (array) $list as $element ) {
		$element = (string) $element;
		if ( 0 === strpos( $element, 'divider' ) ) {
			$out[] = 'divider';
		} elseif ( isset( $map[ $element ] ) ) {
			$out[] = $map[ $element ];
		}
	}
	return array_values( array_unique( $out ) );
}

/**
 * Header builder layout equivalent to the Flatsome header.
 */
function bst_flatsome_header_layout( $m ) {
	$get = function ( $key, $default ) use ( $m ) {
		return bst_flatsome_elements( isset( $m[ $key ] ) && is_array( $m[ $key ] ) ? $m[ $key ] : $default );
	};

	$main_left  = $get( 'header_elements_left', array( 'search', 'nav' ) );
	$main_right = $get( 'header_elements_right', array( 'account', 'divider', 'cart' ) );
	$center     = array();
	if ( 'center' === ( $m['logo_position'] ?? 'left' ) ) {
		$center = array( 'logo' );
	} else {
		array_unshift( $main_left, 'logo' );
	}

	$mobile_left  = $get( 'header_mobile_elements_left', array( 'menu-icon' ) );
	$mobile_right = $get( 'header_mobile_elements_right', array( 'cart' ) );
	$mobile_mid   = array( 'logo' );
	if ( 'left' === ( $m['logo_position_mobile'] ?? 'center' ) ) {
		$mobile_mid = array();
		array_unshift( $mobile_left, 'logo' );
	}

	$top_bar = ! isset( $m['topbar_show'] ) || $m['topbar_show'];
	$empty   = array( 'left' => array(), 'center' => array(), 'right' => array() );

	return array(
		'desktop' => array(
			'top'    => $top_bar ? array(
				'left'   => $get( 'topbar_elements_left', array( 'html' ) ),
				'center' => $get( 'topbar_elements_center', array() ),
				'right'  => $get( 'topbar_elements_right', array() ),
			) : $empty,
			'main'   => array( 'left' => $main_left, 'center' => $center, 'right' => $main_right ),
			'bottom' => array(
				'left'   => $get( 'header_elements_bottom_left', array() ),
				'center' => $get( 'header_elements_bottom_center', array() ),
				'right'  => $get( 'header_elements_bottom_right', array() ),
			),
		),
		'mobile'  => array(
			'top'    => array( 'left' => array(), 'center' => $get( 'header_mobile_elements_top', array() ), 'right' => array() ),
			'main'   => array( 'left' => $mobile_left, 'center' => $mobile_mid, 'right' => $mobile_right ),
			'bottom' => array( 'left' => array(), 'center' => $get( 'header_mobile_elements_bottom', array() ), 'right' => array() ),
		),
	);
}

/**
 * Translate Flatsome options into BIGSPY option values.
 *
 * @param array $m Flatsome theme mods.
 * @return array BIGSPY option key => value (only for options Flatsome defines).
 */
function bst_flatsome_translate( $m ) {
	$out = array();
	$set = function ( $key, $value ) use ( &$out ) {
		if ( '' !== $value && null !== $value ) {
			$out[ $key ] = $value;
		}
	};
	$num = function ( $key, $min, $max ) use ( $m ) {
		return isset( $m[ $key ] ) && is_numeric( $m[ $key ] ) ? (int) max( $min, min( $max, (float) $m[ $key ] ) ) : '';
	};
	$text = function ( $key ) use ( $m ) {
		return isset( $m[ $key ] ) && is_scalar( $m[ $key ] ) ? trim( (string) $m[ $key ] ) : '';
	};
	$on_dark = function ( $scheme ) {
		return 'dark' === $scheme ? '#ffffff' : '#111827';
	};

	// Colors.
	foreach ( array( 'color_primary' => 'color_primary', 'color_secondary' => 'color_secondary', 'color_success' => 'color_success', 'color_alert' => 'color_alert', 'color_texts' => 'color_text', 'type_headings_color' => 'color_heading', 'color_links' => 'color_link', 'color_links_hover' => 'color_link_hover' ) as $from => $to ) {
		$set( $to, bst_flatsome_hex( $m[ $from ] ?? '' ) );
	}

	// Typography.
	$set( 'font_body', bst_flatsome_font( $m, 'type_texts' ) );
	$set( 'font_heading', bst_flatsome_font( $m, 'type_headings' ) );
	$set( 'font_nav', bst_flatsome_font( $m, 'type_nav' ) );
	if ( isset( $m['type_texts'] ) ) {
		$set( 'font_weight_body', bst_flatsome_font_weight( $m, 'type_texts', '400' ) );
	}
	if ( isset( $m['type_headings'] ) ) {
		$set( 'font_weight_heading', bst_flatsome_font_weight( $m, 'type_headings', '700' ) );
	}
	if ( isset( $m['type_nav'] ) ) {
		$set( 'nav_weight', bst_flatsome_font_weight( $m, 'type_nav', '600' ) );
	}
	if ( '' !== $num( 'type_size', 50, 200 ) ) {
		$set( 'font_size_base', (int) max( 12, min( 22, round( 16 * $num( 'type_size', 50, 200 ) / 100 ) ) ) );
	}
	if ( '' !== $num( 'type_size_mobile', 50, 200 ) ) {
		$set( 'font_size_mobile', (int) max( 12, min( 20, round( 16 * $num( 'type_size_mobile', 50, 200 ) / 100 ) ) ) );
	}
	if ( '' !== $text( 'button_radius' ) ) {
		$set( 'button_radius', (int) max( 0, min( 60, (float) $text( 'button_radius' ) ) ) );
	}

	// Layout.
	if ( isset( $m['body_layout'] ) ) {
		$set( 'site_layout', in_array( $m['body_layout'], array( 'boxed', 'framed' ), true ) ? 'boxed' : 'full' );
	}
	$set( 'container_width', $num( 'site_width', 960, 1920 ) );

	// Header.
	$has_header = false;
	foreach ( array( 'header_elements_left', 'header_elements_right', 'header_elements_bottom_left', 'topbar_elements_left', 'header_mobile_elements_left', 'logo_position' ) as $key ) {
		$has_header = $has_header || isset( $m[ $key ] );
	}
	if ( $has_header ) {
		$out['header_layout'] = wp_json_encode( bst_flatsome_header_layout( $m ) );
	}
	if ( isset( $m['header_width'] ) ) {
		$out['header_full_width'] = 'full-width' === $m['header_width'];
	}
	if ( isset( $m['header_sticky'] ) ) {
		$out['header_sticky'] = (bool) $m['header_sticky'];
	}
	$set( 'header_sticky_height', $num( 'header_height_sticky', 40, 140 ) );
	$set( 'header_top_height', $num( 'header_top_height', 24, 80 ) );
	$set( 'header_main_height', $num( 'header_height', 40, 200 ) );
	$set( 'header_bottom_height', $num( 'header_bottom_height', 30, 120 ) );
	$set( 'header_mobile_height', $num( 'header_height_mobile', 40, 120 ) );
	$set( 'logo_width', $num( 'logo_width', 40, 600 ) );
	if ( '' !== $num( 'logo_width', 40, 600 ) ) {
		$set( 'logo_width_mobile', min( 400, $num( 'logo_width', 40, 600 ) ) );
	}

	$set( 'header_top_bg', bst_flatsome_hex( $m['topbar_bg'] ?? '' ) );
	if ( isset( $m['topbar_color'] ) ) {
		$set( 'header_top_color', $on_dark( $m['topbar_color'] ) );
	}
	$set( 'header_main_bg', bst_flatsome_hex( $m['header_bg'] ?? '' ) );
	$nav_color = bst_flatsome_hex( $m['type_nav_color'] ?? '' );
	if ( $nav_color ) {
		$set( 'header_main_color', $nav_color );
	} elseif ( isset( $m['header_color'] ) ) {
		$set( 'header_main_color', $on_dark( $m['header_color'] ) );
	}
	$set( 'header_bottom_bg', bst_flatsome_hex( $m['nav_position_bg'] ?? '' ) );
	$bottom_color = bst_flatsome_hex( $m['type_nav_bottom_color'] ?? '' );
	if ( $bottom_color ) {
		$set( 'header_bottom_color', $bottom_color );
	} elseif ( isset( $m['nav_position_color'] ) ) {
		$set( 'header_bottom_color', $on_dark( $m['nav_position_color'] ) );
	}

	$nav_styles = array( 'divided' => 'divided', 'line' => 'underline', 'line-grow' => 'underline', 'line-bottom' => 'underline', 'box' => 'pill', 'outline' => 'pill', 'pills' => 'pill', 'tabs' => 'pill' );
	if ( isset( $m['nav_style'] ) ) {
		$set( 'nav_style', $nav_styles[ $m['nav_style'] ] ?? 'plain' );
	}
	$nav_sizes = array( 'xsmall' => 12, 'small' => 13, 'medium' => 14, 'large' => 16, 'xlarge' => 18 );
	if ( isset( $m['nav_size'], $nav_sizes[ $m['nav_size'] ] ) ) {
		$set( 'nav_size', $nav_sizes[ $m['nav_size'] ] );
	}
	if ( isset( $m['nav_uppercase'] ) ) {
		$set( 'nav_transform', $m['nav_uppercase'] ? 'uppercase' : 'none' );
	}
	if ( isset( $m['mobile_overlay'] ) ) {
		$set( 'offcanvas_side', 'right' === $m['mobile_overlay'] ? 'right' : 'left' );
	}
	if ( isset( $m['header_search_style'] ) ) {
		$set( 'search_style', 'lightbox' === $m['header_search_style'] ? 'lightbox' : 'dropdown' );
	}
	$set( 'vmenu_label', $text( 'header_nav_vertical_text' ) );
	$set( 'search_placeholder', $text( 'search_placeholder' ) );

	// Header buttons and HTML.
	foreach ( array( 1, 2 ) as $i ) {
		$label = $text( 'header_button_' . $i );
		$link  = $text( 'header_button_' . $i . '_link' );
		if ( '' === $label || '' === $link ) {
			continue;
		}
		$style = $text( 'header_button_' . $i . '_style' );
		$color = $text( 'header_button_' . $i . '_color' );
		$out[ 'header_button_' . $i . '_text' ]    = $label;
		$out[ 'header_button_' . $i . '_url' ]     = $link;
		$out[ 'header_button_' . $i . '_new_tab' ] = '_blank' === $text( 'header_button_' . $i . '_link_target' );
		$out[ 'header_button_' . $i . '_style' ]   = in_array( $style, array( 'outline', 'link' ), true ) ? $style : ( 'secondary' === $color ? 'secondary' : 'primary' );
	}
	$set( 'header_html_1', $text( 'topbar_left' ) );
	$set( 'header_html_2', $text( 'topbar_right' ) );
	$set( 'header_html_3', $text( 'nav_position_text' ) );

	// Contact and social.
	$set( 'contact_phone', $text( 'contact_phone' ) );
	$set( 'contact_email', $text( 'contact_email' ) );
	$set( 'contact_address', $text( 'contact_location' ) );
	$set( 'contact_hours', $text( 'contact_hours' ) );
	foreach ( array( 'facebook' => 'facebook', 'instagram' => 'instagram', 'youtube' => 'youtube', 'tiktok' => 'tiktok', 'twitter' => 'x', 'linkedin' => 'linkedin', 'pinterest' => 'pinterest' ) as $from => $to ) {
		$set( 'social_' . $to, $text( 'follow_' . $from ) );
	}

	// Footer.
	$set( 'footer_1_bg', bst_flatsome_hex( $m['footer_1_bg_color'] ?? '' ) );
	$set( 'footer_2_bg', bst_flatsome_hex( $m['footer_2_bg_color'] ?? '' ) );
	$set( 'footer_bottom_bg', bst_flatsome_hex( $m['footer_bottom_color'] ?? '' ) );
	if ( isset( $m['footer_bottom_text'] ) ) {
		$set( 'footer_bottom_color', 'dark' === $m['footer_bottom_text'] ? '#ffffff' : '#374151' );
	}
	$copyright = $text( 'footer_left_text' );
	if ( '' !== $copyright ) {
		$set( 'footer_copyright', str_replace( '[ux_current_year]', '[year]', $copyright ) );
	}
	$set( 'footer_bottom_html', $text( 'footer_right_text' ) );
	if ( isset( $m['back_to_top'] ) ) {
		$out['back_to_top'] = (bool) $m['back_to_top'];
	}

	// Blog and shop.
	$sidebars = array( 'left-sidebar' => 'left', 'right-sidebar' => 'right', 'none' => 'none', 'off-canvas' => 'none', 'top' => 'none' );
	if ( isset( $m['blog_layout'], $sidebars[ $m['blog_layout'] ] ) ) {
		$set( 'blog_sidebar', $sidebars[ $m['blog_layout'] ] );
	}
	if ( isset( $m['category_sidebar'], $sidebars[ $m['category_sidebar'] ] ) ) {
		$set( 'shop_sidebar', $sidebars[ $m['category_sidebar'] ] );
	}
	$set( 'shop_columns', $num( 'category_row_count', 1, 6 ) );
	$set( 'shop_columns_tablet', $num( 'category_row_count_tablet', 1, 4 ) );
	$set( 'shop_columns_mobile', $num( 'category_row_count_mobile', 1, 2 ) );
	$set( 'shop_per_page', $num( 'products_pr_page', 1, 100 ) );

	return apply_filters( 'bst_flatsome_translate', $out, $m );
}

/**
 * Flatsome settings become the theme's defaults.
 */
function bst_flatsome_defaults( $defaults ) {
	$mods = bst_flatsome_mods();
	if ( ! $mods ) {
		return $defaults;
	}
	foreach ( bst_flatsome_translate( $mods ) as $key => $value ) {
		if ( array_key_exists( $key, $defaults ) ) {
			$defaults[ $key ] = $value;
		}
	}
	return $defaults;
}
add_filter( 'bst_defaults', 'bst_flatsome_defaults', 20 );

/**
 * The Flatsome logo is used until a logo is chosen in Site Identity.
 */
function bst_flatsome_logo( $value ) {
	if ( $value ) {
		return $value;
	}
	$mods = bst_flatsome_mods();
	$logo = $mods['site_logo'] ?? '';
	if ( is_numeric( $logo ) && (int) $logo > 0 ) {
		return (int) $logo;
	}
	if ( is_string( $logo ) && '' !== $logo ) {
		return attachment_url_to_postid( $logo );
	}
	return $value;
}
add_filter( 'theme_mod_custom_logo', 'bst_flatsome_logo' );

/**
 * Menus assigned to Flatsome locations fill the matching BIGSPY locations that are still empty.
 */
function bst_flatsome_menu_locations( $locations ) {
	$mods = bst_flatsome_mods();
	$old  = isset( $mods['nav_menu_locations'] ) && is_array( $mods['nav_menu_locations'] ) ? $mods['nav_menu_locations'] : array();
	if ( ! $old ) {
		return $locations;
	}
	$locations = is_array( $locations ) ? $locations : array();
	$map       = array( 'primary' => 'primary', 'top_bar_nav' => 'secondary', 'primary_mobile' => 'mobile', 'footer' => 'footer', 'vertical' => 'vertical' );
	foreach ( $map as $from => $to ) {
		if ( empty( $locations[ $to ] ) && ! empty( $old[ $from ] ) ) {
			$locations[ $to ] = (int) $old[ $from ];
		}
	}
	return $locations;
}
add_filter( 'theme_mod_nav_menu_locations', 'bst_flatsome_menu_locations' );
