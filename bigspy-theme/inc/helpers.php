<?php
/**
 * Option defaults and shared helpers.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Every theme option with its default value. Customizer fields, dynamic CSS
 * and the import/export tool all read from this single list.
 *
 * No translation calls here: defaults can be read before `init`.
 */
function bst_defaults() {
	static $defaults = null;
	if ( null !== $defaults ) {
		return $defaults;
	}

	$defaults = array(
		'css_presets'            => array(),
		// Layout.
		'site_layout'            => 'full',
		'boxed_bg'               => '#e9edf2',
		'container_width'        => 1200,
		'sidebar_width'          => 300,
		'content_bg'             => '#ffffff',

		// Colors.
		'color_primary'          => '#0a4d8c',
		'color_secondary'        => '#f26b21',
		'color_success'          => '#2e9d5b',
		'color_alert'            => '#d63638',
		'color_text'             => '#3a3f47',
		'color_heading'          => '#111827',
		'color_link'             => '#0a4d8c',
		'color_link_hover'       => '#f26b21',
		'color_border'           => '#e5e7eb',
		'color_light'            => '#f5f7fa',
		'color_body_bg'          => '#ffffff',

		// Typography.
		'font_body'              => 'system',
		'font_heading'           => 'system',
		'font_nav'               => 'system',
		'font_size_base'         => 16,
		'font_size_mobile'       => 15,
		'line_height'            => 1.65,
		'font_weight_body'       => '400',
		'font_weight_heading'    => '700',
		'heading_transform'      => 'none',
		'h1_size'                => 40,
		'h2_size'                => 32,
		'h3_size'                => 24,
		'h4_size'                => 20,
		'nav_size'               => 15,
		'nav_weight'             => '600',
		'nav_transform'          => 'none',

		// Buttons.
		'button_radius'          => 6,
		'button_transform'       => 'none',
		'button_weight'          => '600',

		// Header.
		'header_layout'          => '',
		'header_full_width'      => false,
		'header_sticky'          => true,
		'header_sticky_height'   => 64,
		'header_shadow'          => true,
		'header_transparent_home'=> false,
		'header_transparent_color' => '#ffffff',
		'header_top_height'      => 38,
		'header_top_bg'          => '#0a4d8c',
		'header_top_color'       => '#ffffff',
		'header_main_height'     => 84,
		'header_main_bg'         => '#ffffff',
		'header_main_color'      => '#111827',
		'header_bottom_height'   => 48,
		'header_bottom_bg'       => '#f5f7fa',
		'header_bottom_color'    => '#111827',
		'header_mobile_height'   => 64,
		'logo_width'             => 180,
		'logo_width_mobile'      => 130,
		'nav_style'              => 'underline',
		'search_style'           => 'dropdown',
		'search_products'        => false,
		'offcanvas_side'         => 'left',
		'header_button_1_text'   => 'Contact us',
		'header_button_1_url'    => '#',
		'header_button_1_style'  => 'primary',
		'header_button_1_new_tab'=> false,
		'header_button_2_text'   => '',
		'header_button_2_url'    => '',
		'header_button_2_style'  => 'outline',
		'header_button_2_new_tab'=> false,
		'header_html_1'          => '',
		'header_html_2'          => '',
		'header_html_3'          => '',
		'contact_phone'          => '',
		'contact_email'          => '',
		'contact_address'        => '',
		'contact_hours'          => '',

		// Social profiles (used by header, footer and the off-canvas menu).
		'social_facebook'        => '',
		'social_instagram'       => '',
		'social_youtube'         => '',
		'social_tiktok'          => '',
		'social_zalo'            => '',
		'social_x'               => '',
		'social_linkedin'        => '',
		'social_pinterest'       => '',

		// Page title bar.
		'title_bar_style'        => 'simple',
		'title_bar_bg'           => '#f5f7fa',
		'title_bar_color'        => '#111827',
		'title_bar_image'        => '',
		'breadcrumbs'            => true,

		// Blog.
		'blog_layout'            => 'grid',
		'blog_columns'           => 3,
		'blog_sidebar'           => 'right',
		'blog_card_style'        => 'shadow',
		'blog_image_ratio'       => '16-9',
		'blog_excerpt_length'    => 22,
		'blog_show_meta'         => true,
		'blog_show_excerpt'      => true,
		'blog_read_more'         => 'Read more',
		'single_sidebar'         => 'right',
		'single_featured_image'  => true,
		'single_author_box'      => true,
		'single_share'           => true,
		'single_related'         => true,
		'single_nav'             => true,
		'page_sidebar'           => 'none',

		// Footer call-to-action band.
		'footer_cta_enable'      => false,
		'footer_cta_title'       => 'Ready to start your project?',
		'footer_cta_text'        => 'Get a free consultation and a quote within 24 hours.',
		'footer_cta_button'      => 'Contact us',
		'footer_cta_url'         => '#',
		'footer_cta_button_2'    => '',
		'footer_cta_url_2'       => '',
		'footer_cta_bg'          => '#0a4d8c',
		'footer_cta_hide_pages'  => '',

		// Footer.
		'footer_1_columns'       => 4,
		'footer_1_bg'            => '#111827',
		'footer_1_color'         => '#cbd5e1',
		'footer_1_heading_color' => '#ffffff',
		'footer_2_columns'       => 0,
		'footer_2_bg'            => '#0b1220',
		'footer_2_color'         => '#cbd5e1',
		'footer_bottom_enable'   => true,
		'footer_bottom_bg'       => '#070c16',
		'footer_bottom_color'    => '#94a3b8',
		'footer_bottom_layout'   => 'split',
		'footer_copyright'       => 'Copyright © [year] [site_title]. All rights reserved.',
		'footer_bottom_html'     => '',
		'footer_social'          => true,
		'back_to_top'            => true,

		// Floating contact buttons.
		'float_enable'           => false,
		'float_position'         => 'right',
		'float_phone'            => '',
		'float_zalo'             => '',
		'float_messenger'        => '',
		'float_whatsapp'         => '',
		'float_email'            => '',
		'float_mobile_bar'       => false,

		// WooCommerce.
		'shop_columns'           => 4,
		'shop_columns_tablet'    => 3,
		'shop_columns_mobile'    => 2,
		'shop_per_page'          => 12,
		'shop_sidebar'           => 'left',
		'product_sidebar'        => 'none',
		'product_hover'          => 'swap',
		'product_card_style'     => 'bordered',
		'product_title_lines'    => 2,
		'sale_badge_style'       => 'pill',
		'show_sale_percent'      => true,
		'new_badge_days'         => 30,
		'featured_badge'         => false,
		'product_image_ratio'    => '1-1',
		'product_button'         => 'always',
		'product_show_category'  => true,
		'catalog_mode'           => false,
		'catalog_mode_text'      => 'Contact for price',
		'catalog_mode_url'       => '',
		'cart_icon'              => 'bag',
		'mini_cart'              => true,
		'related_columns'        => 4,
		'product_gallery_layout' => 'default',
		'product_sticky_summary' => false,
		'product_mobile_bar'     => false,

		// Advanced.
		'scripts_head'           => '',
		'scripts_body'           => '',
		'scripts_footer'         => '',
		'disable_emoji'          => true,
		'animations'             => true,
	);

	$defaults = apply_filters( 'bst_defaults', $defaults );
	return $defaults;
}

/**
 * Read a theme option, falling back to its registered default.
 *
 * @param string $key Option key.
 * @return mixed
 */
function bst_mod( $key ) {
	$defaults = bst_defaults();
	$value    = get_theme_mod( $key, $defaults[ $key ] ?? null );

	// Untouched default labels follow the site language; text typed by the user is kept as is.
	if ( is_string( $value ) && '' !== $value && isset( $defaults[ $key ] ) && $value === $defaults[ $key ] && in_array( $key, bst_translatable_defaults(), true ) ) {
		$value = translate( $value, 'bigspy-theme' ); // phpcs:ignore WordPress.WP.I18n
	}
	return $value;
}

/**
 * Option keys whose English default text is shown to visitors and must be translated.
 */
function bst_translatable_defaults() {
	return array( 'header_button_1_text', 'blog_read_more', 'footer_cta_title', 'footer_cta_text', 'footer_cta_button', 'footer_copyright', 'catalog_mode_text' );
}

/**
 * Header elements available in the header builder.
 */
function bst_header_elements() {
	$elements = array(
		'logo'          => __( 'Logo', 'bigspy-theme' ),
		'nav'           => __( 'Main menu', 'bigspy-theme' ),
		'nav_secondary' => __( 'Top bar menu', 'bigspy-theme' ),
		'nav_icon'      => __( 'Menu icon (off-canvas)', 'bigspy-theme' ),
		'search'        => __( 'Search icon', 'bigspy-theme' ),
		'search_form'   => __( 'Search form', 'bigspy-theme' ),
		'account'       => __( 'Account', 'bigspy-theme' ),
		'cart'          => __( 'Cart (WooCommerce)', 'bigspy-theme' ),
		'button_1'      => __( 'Button 1', 'bigspy-theme' ),
		'button_2'      => __( 'Button 2', 'bigspy-theme' ),
		'html_1'        => __( 'HTML 1', 'bigspy-theme' ),
		'html_2'        => __( 'HTML 2', 'bigspy-theme' ),
		'html_3'        => __( 'HTML 3', 'bigspy-theme' ),
		'contact'       => __( 'Contact info', 'bigspy-theme' ),
		'social'        => __( 'Social icons', 'bigspy-theme' ),
		'divider'       => __( 'Divider', 'bigspy-theme' ),
	);
	return apply_filters( 'bst_header_elements', $elements );
}

function bst_header_devices() {
	return array( 'desktop', 'mobile' );
}

function bst_header_rows() {
	return array( 'top', 'main', 'bottom' );
}

function bst_header_zones() {
	return array( 'left', 'center', 'right' );
}

function bst_default_header_layout() {
	$empty = array( 'left' => array(), 'center' => array(), 'right' => array() );
	return array(
		'desktop' => array(
			'top'    => array( 'left' => array( 'contact' ), 'center' => array(), 'right' => array( 'nav_secondary', 'social' ) ),
			'main'   => array( 'left' => array( 'logo' ), 'center' => array(), 'right' => array( 'nav', 'search', 'cart', 'button_1' ) ),
			'bottom' => $empty,
		),
		'mobile'  => array(
			'top'    => $empty,
			'main'   => array( 'left' => array( 'nav_icon' ), 'center' => array( 'logo' ), 'right' => array( 'search', 'cart' ) ),
			'bottom' => $empty,
		),
	);
}

/**
 * Normalise a header layout (JSON string or array) to the full structure,
 * dropping unknown elements and duplicates within a device.
 *
 * @param mixed $value Raw value.
 * @return array
 */
function bst_normalize_header_layout( $value ) {
	if ( is_string( $value ) && '' !== $value ) {
		$value = json_decode( $value, true );
	}
	if ( ! is_array( $value ) || empty( $value ) ) {
		return bst_default_header_layout();
	}

	$allowed = array_keys( bst_header_elements() );
	$clean   = array();
	foreach ( bst_header_devices() as $device ) {
		$used = array();
		foreach ( bst_header_rows() as $row ) {
			foreach ( bst_header_zones() as $zone ) {
				$items = $value[ $device ][ $row ][ $zone ] ?? array();
				$items = is_array( $items ) ? $items : array();
				$clean[ $device ][ $row ][ $zone ] = array();
				foreach ( $items as $item ) {
					$item = sanitize_key( $item );
					if ( in_array( $item, $allowed, true ) && ! in_array( $item, $used, true ) ) {
						$clean[ $device ][ $row ][ $zone ][] = $item;
						$used[] = $item;
					}
				}
			}
		}
	}
	return $clean;
}

function bst_get_header_layout() {
	return bst_normalize_header_layout( bst_mod( 'header_layout' ) );
}

/**
 * Replace simple tokens used in footer / header HTML fields.
 *
 * @param string $text Text with tokens.
 * @return string
 */
function bst_replace_tokens( $text ) {
	$tokens = array(
		'[year]'       => gmdate( 'Y' ),
		'[site_title]' => get_bloginfo( 'name' ),
		'[site_url]'   => home_url( '/' ),
		'[phone]'      => bst_mod( 'contact_phone' ),
		'[email]'      => bst_mod( 'contact_email' ),
	);
	return strtr( (string) $text, $tokens );
}

/**
 * Render a user-provided HTML field: tokens, shortcodes and safe markup.
 *
 * @param string $key Option key.
 * @return string
 */
function bst_html_field( $key ) {
	$html = trim( (string) bst_mod( $key ) );
	if ( '' === $html ) {
		return '';
	}
	return do_shortcode( wp_kses_post( bst_replace_tokens( $html ) ) );
}

/**
 * Social networks known to the theme.
 */
function bst_social_networks() {
	return array(
		'facebook'  => 'Facebook',
		'instagram' => 'Instagram',
		'youtube'   => 'YouTube',
		'tiktok'    => 'TikTok',
		'zalo'      => 'Zalo',
		'x'         => 'X',
		'linkedin'  => 'LinkedIn',
		'pinterest' => 'Pinterest',
	);
}

/**
 * Sidebar position for the current request: none, left or right.
 */
function bst_sidebar_position() {
	$position = 'none';

	if ( function_exists( 'is_woocommerce' ) && ( is_shop() || is_product_taxonomy() ) ) {
		$position = bst_mod( 'shop_sidebar' );
	} elseif ( function_exists( 'is_product' ) && is_product() ) {
		$position = bst_mod( 'product_sidebar' );
	} elseif ( function_exists( 'is_woocommerce' ) && ( is_cart() || is_checkout() || is_account_page() ) ) {
		$position = 'none';
	} elseif ( is_page() ) {
		$position = bst_mod( 'page_sidebar' );
	} elseif ( is_singular() ) {
		$position = bst_mod( 'single_sidebar' );
	} elseif ( is_home() || is_archive() || is_search() ) {
		$position = bst_mod( 'blog_sidebar' );
	}

	if ( is_singular() ) {
		$override = get_post_meta( get_queried_object_id(), '_bst_sidebar', true );
		if ( in_array( $override, array( 'none', 'left', 'right' ), true ) ) {
			$position = $override;
		}
		if ( is_page_template( array( 'templates/full-width.php', 'templates/blank.php' ) ) ) {
			$position = 'none';
		}
	}

	$position = in_array( $position, array( 'none', 'left', 'right' ), true ) ? $position : 'none';

	// A sidebar without widgets must not reserve an empty column.
	if ( 'none' !== $position && ! is_active_sidebar( bst_sidebar_id() ) ) {
		$position = 'none';
	}
	return apply_filters( 'bst_sidebar_position', $position );
}

/**
 * Widget area that belongs to the current request.
 */
function bst_sidebar_id() {
	if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
		return 'sidebar-shop';
	}
	if ( is_page() ) {
		return 'sidebar-page';
	}
	return 'sidebar-blog';
}

/**
 * Read a per-post layout option.
 *
 * @param string $key Meta key without the _bst_ prefix.
 * @return string
 */
function bst_post_option( $key ) {
	if ( ! is_singular() && ! ( function_exists( 'is_shop' ) && is_shop() ) ) {
		return '';
	}
	$id = ( function_exists( 'is_shop' ) && is_shop() ) ? wc_get_page_id( 'shop' ) : get_queried_object_id();
	return $id ? (string) get_post_meta( $id, '_bst_' . $key, true ) : '';
}

function bst_show_header() {
	if ( is_page_template( 'templates/blank.php' ) ) {
		return false;
	}
	return 'hide' !== bst_post_option( 'header' );
}

function bst_show_footer() {
	if ( is_page_template( 'templates/blank.php' ) ) {
		return false;
	}
	return '1' !== bst_post_option( 'hide_footer' );
}

function bst_header_is_transparent() {
	$mode = bst_post_option( 'header' );
	if ( 'transparent' === $mode ) {
		return true;
	}
	if ( 'solid' === $mode ) {
		return false;
	}
	return is_front_page() && bst_mod( 'header_transparent_home' );
}

/**
 * Inline SVG icon set used by the theme and its blocks.
 *
 * @param string $name Icon name.
 * @param int    $size Size in px.
 * @return string
 */
function bst_icon( $name, $size = 20 ) {
	$paths = bst_icon_paths();
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return sprintf(
		'<svg class="bs-icon bs-icon--%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" aria-hidden="true" focusable="false">%3$s</svg>',
		esc_attr( $name ),
		(int) $size,
		$paths[ $name ]
	);
}

function bst_icon_paths() {
	$s = 'fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';
	return array(
		'menu'      => '<path ' . $s . ' d="M3 6h18M3 12h18M3 18h18"/>',
		'close'     => '<path ' . $s . ' d="M6 6l12 12M18 6L6 18"/>',
		'search'    => '<circle ' . $s . ' cx="11" cy="11" r="7"/><path ' . $s . ' d="M20 20l-3.5-3.5"/>',
		'bag'       => '<path ' . $s . ' d="M6 7h12l1 14H5L6 7z"/><path ' . $s . ' d="M9 7a3 3 0 0 1 6 0"/>',
		'cart'      => '<circle ' . $s . ' cx="9" cy="20" r="1.5"/><circle ' . $s . ' cx="18" cy="20" r="1.5"/><path ' . $s . ' d="M2 3h3l2.7 12.4a1.5 1.5 0 0 0 1.5 1.1h8.9a1.5 1.5 0 0 0 1.4-1.1L22 7H6"/>',
		'user'      => '<circle ' . $s . ' cx="12" cy="8" r="4"/><path ' . $s . ' d="M4 21a8 8 0 0 1 16 0"/>',
		'phone'     => '<path ' . $s . ' d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/>',
		'mail'      => '<rect ' . $s . ' x="3" y="5" width="18" height="14" rx="2"/><path ' . $s . ' d="M3 7l9 6 9-6"/>',
		'pin'       => '<path ' . $s . ' d="M12 21s-7-6.2-7-12a7 7 0 0 1 14 0c0 5.8-7 12-7 12z"/><circle ' . $s . ' cx="12" cy="9" r="2.5"/>',
		'clock'     => '<circle ' . $s . ' cx="12" cy="12" r="9"/><path ' . $s . ' d="M12 7v5l3 2"/>',
		'arrow-up'  => '<path ' . $s . ' d="M12 19V5M5 12l7-7 7 7"/>',
		'arrow-right' => '<path ' . $s . ' d="M5 12h14M13 5l7 7-7 7"/>',
		'chevron-down' => '<path ' . $s . ' d="M6 9l6 6 6-6"/>',
		'chevron-left' => '<path ' . $s . ' d="M15 18l-6-6 6-6"/>',
		'chevron-right' => '<path ' . $s . ' d="M9 18l6-6-6-6"/>',
		'check'     => '<path ' . $s . ' d="M20 6L9 17l-5-5"/>',
		'star'      => '<path fill="currentColor" d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4l-5.9 3.1 1.2-6.5L2.5 9.4l6.6-.9z"/>',
		'heart'     => '<path ' . $s . ' d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1.1L12 21l7.8-7.5 1-1.1a5.5 5.5 0 0 0 0-7.8z"/>',
		'shield'    => '<path ' . $s . ' d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path ' . $s . ' d="M9 12l2 2 4-4"/>',
		'truck'     => '<path ' . $s . ' d="M1 4h14v12H1zM15 9h4l3 3v4h-7"/><circle ' . $s . ' cx="5.5" cy="18.5" r="2"/><circle ' . $s . ' cx="18.5" cy="18.5" r="2"/>',
		'gift'      => '<path ' . $s . ' d="M20 12v9H4v-9M2 7h20v5H2zM12 21V7M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7zM12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>',
		'support'   => '<path ' . $s . ' d="M3 18v-6a9 9 0 0 1 18 0v6"/><path ' . $s . ' d="M21 19a2 2 0 0 1-2 2h-1v-6h3zM3 19a2 2 0 0 0 2 2h1v-6H3z"/>',
		'rocket'    => '<path ' . $s . ' d="M4.5 16.5c-1.5 1.3-2 5-2 5s3.7-.5 5-2c.7-.8.7-2.1-.1-2.9a2.2 2.2 0 0 0-2.9-.1zM12 15l-3-3a22 22 0 0 1 2-3.9A12.9 12.9 0 0 1 22 2c0 2.7-.8 7.5-6 11a22.4 22.4 0 0 1-4 2z"/><path ' . $s . ' d="M9 12H4s.6-3 2-4c1.6-1.1 5 0 5 0M12 15v5s3-.6 4-2c1.1-1.6 0-5 0-5"/>',
		'chart'     => '<path ' . $s . ' d="M3 3v18h18M7 15l4-4 3 3 5-6"/>',
		'code'      => '<path ' . $s . ' d="M16 18l6-6-6-6M8 6l-6 6 6 6"/>',
		'globe'     => '<circle ' . $s . ' cx="12" cy="12" r="9"/><path ' . $s . ' d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>',
		'lightbulb' => '<path ' . $s . ' d="M9 18h6M10 22h4M12 2a7 7 0 0 0-4 12.7V17h8v-2.3A7 7 0 0 0 12 2z"/>',
		'users'     => '<circle ' . $s . ' cx="9" cy="8" r="4"/><path ' . $s . ' d="M2 21a7 7 0 0 1 14 0M16 3.1a4 4 0 0 1 0 7.8M22 21a7 7 0 0 0-5-6.7"/>',
		'settings'  => '<circle ' . $s . ' cx="12" cy="12" r="3"/><path ' . $s . ' d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>',
		'home'      => '<path ' . $s . ' d="M3 11l9-8 9 8v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z"/>',
		'camera'    => '<path ' . $s . ' d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle ' . $s . ' cx="12" cy="13" r="4"/>',
		'message'   => '<path ' . $s . ' d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
		'leaf'      => '<path ' . $s . ' d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.5 19 2c1 2 2 4.2 2 8 0 5.5-4.8 10-10 10z"/><path ' . $s . ' d="M2 21c0-3 1.9-5.4 5.1-6 2.4-.5 4.9-2 5.9-3"/>',
		'droplet'   => '<path ' . $s . ' d="M12 2.7l5.7 5.7a8 8 0 1 1-11.3 0z"/>',
		'sun'       => '<circle ' . $s . ' cx="12" cy="12" r="4"/><path ' . $s . ' d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M6.3 17.7l-1.4 1.4M19.1 4.9l-1.4 1.4"/>',
		'sparkles'  => '<path ' . $s . ' d="M11 3l1.9 5.1L18 10l-5.1 1.9L11 17l-1.9-5.1L4 10l5.1-1.9z"/><path ' . $s . ' d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8z"/>',
		'flask'     => '<path ' . $s . ' d="M9 2h6M10 2v6.5L4.5 18A2.5 2.5 0 0 0 6.7 22h10.6a2.5 2.5 0 0 0 2.2-4L14 8.5V2M7 15h10"/>',
		'award'     => '<circle ' . $s . ' cx="12" cy="8" r="6"/><path ' . $s . ' d="M8.2 13.3L7 22l5-3 5 3-1.2-8.7"/>',
		'tag'       => '<path ' . $s . ' d="M20.6 13.4l-7.2 7.2a2 2 0 0 1-2.8 0L2 12V2h10l8.6 8.6a2 2 0 0 1 0 2.8z"/><circle fill="currentColor" cx="7" cy="7" r="1.5"/>',
		'refresh'   => '<path ' . $s . ' d="M3 12a9 9 0 0 1 15.5-6.2L21 8M21 3v5h-5M21 12a9 9 0 0 1-15.5 6.2L3 16M3 21v-5h5"/>',
		'card'      => '<rect ' . $s . ' x="2" y="5" width="20" height="14" rx="2"/><path ' . $s . ' d="M2 10h20M6 15h4"/>',
		'smile'     => '<circle ' . $s . ' cx="12" cy="12" r="9"/><path ' . $s . ' d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01"/>',
		'quote'     => '<path fill="currentColor" d="M7.2 6C4.3 7.3 2.5 9.9 2.5 13.3V18h7v-7H6.1c.2-1.7 1.2-3 3-3.8zm11 0c-2.9 1.3-4.7 3.9-4.7 7.3V18h7v-7h-3.4c.2-1.7 1.2-3 3-3.8z"/>',
		'play'      => '<path fill="currentColor" d="M8 5v14l11-7z"/>',
		'facebook'  => '<path fill="currentColor" d="M14 8.5V6.6c0-.9.2-1.4 1.6-1.4H17V2.1C16.7 2 15.7 2 14.6 2 12.2 2 10.6 3.4 10.6 6.1v2.4H8V12h2.6v10H14V12h2.7l.4-3.5z"/>',
		'instagram' => '<rect ' . $s . ' x="3" y="3" width="18" height="18" rx="5"/><circle ' . $s . ' cx="12" cy="12" r="4"/><circle fill="currentColor" cx="17.5" cy="6.5" r="1.2"/>',
		'youtube'   => '<path fill="currentColor" d="M23 7.2a3 3 0 0 0-2.1-2.1C19 4.6 12 4.6 12 4.6s-7 0-8.9.5A3 3 0 0 0 1 7.2 31 31 0 0 0 .5 12a31 31 0 0 0 .5 4.8 3 3 0 0 0 2.1 2.1c1.9.5 8.9.5 8.9.5s7 0 8.9-.5a3 3 0 0 0 2.1-2.1 31 31 0 0 0 .5-4.8 31 31 0 0 0-.5-4.8zM9.7 15.1V8.9l5.8 3.1z"/>',
		'tiktok'    => '<path fill="currentColor" d="M16.6 5.8A4.3 4.3 0 0 1 15.5 3h-3.3v12.4a2.6 2.6 0 1 1-2.6-2.6c.3 0 .5 0 .8.1V9.5a6 6 0 1 0 5.1 5.9V9.1a7.6 7.6 0 0 0 4.4 1.4V7.2a4.3 4.3 0 0 1-3.3-1.4z"/>',
		'zalo'      => '<path ' . $s . ' d="M12 3C6.8 3 2.5 6.6 2.5 11c0 2.5 1.3 4.7 3.4 6.1L5.2 21l3.9-2c.9.2 1.9.3 2.9.3 5.2 0 9.5-3.6 9.5-8.2S17.2 3 12 3z"/><text x="12" y="14.6" text-anchor="middle" font-family="Arial,sans-serif" font-size="8.5" font-weight="700" fill="currentColor">Z</text>',
		'x'         => '<path fill="currentColor" d="M17.8 3h3.1l-6.8 7.7L22 21h-6.2l-4.9-6.4L5.3 21H2.2l7.2-8.3L1.8 3h6.4l4.4 5.8zm-1.1 16.2h1.7L7.4 4.7H5.6z"/>',
		'linkedin'  => '<path fill="currentColor" d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zM3 9.5h4V21H3zM9.5 9.5h3.8v1.6h.1c.5-1 1.8-2 3.8-2 4 0 4.8 2.6 4.8 6V21h-4v-5.2c0-1.2 0-2.8-1.7-2.8s-2 1.3-2 2.7V21h-4z"/>',
		'pinterest' => '<path fill="currentColor" d="M12 2a10 10 0 0 0-3.6 19.3c-.1-.8-.2-2 0-2.9l1.2-5s-.3-.6-.3-1.5c0-1.4.8-2.5 1.8-2.5.9 0 1.3.7 1.3 1.4 0 .9-.6 2.2-.9 3.4-.2 1 .5 1.9 1.6 1.9 1.9 0 3.3-2 3.3-4.9 0-2.5-1.8-4.3-4.4-4.3-3 0-4.8 2.3-4.8 4.6 0 .9.4 1.9.8 2.4l.1.4-.3 1.2c0 .2-.2.3-.4.2-1.4-.7-2.2-2.7-2.2-4.3 0-3.5 2.5-6.7 7.3-6.7 3.8 0 6.8 2.7 6.8 6.4 0 3.8-2.4 6.9-5.8 6.9-1.1 0-2.2-.6-2.5-1.3l-.7 2.6c-.3 1-.9 2.2-1.4 2.9A10 10 0 1 0 12 2z"/>',
		'messenger' => '<path fill="currentColor" d="M12 2C6.4 2 2 6.1 2 11.7c0 2.9 1.2 5.5 3.2 7.2v3.1l2.9-1.6c1.2.3 2.5.5 3.9.5 5.6 0 10-4.1 10-9.7S17.6 2 12 2zm1 13l-2.5-2.7-5 2.7 5.5-5.8 2.6 2.7 4.9-2.7z"/>',
		'whatsapp'  => '<path fill="currentColor" d="M17.5 14.4c-.3-.1-1.8-.9-2-1-.3-.1-.5-.1-.7.1l-.9 1.2c-.2.2-.3.2-.6.1a8 8 0 0 1-4-3.5c-.3-.5.3-.5.9-1.6.1-.2 0-.4 0-.5l-.9-2.2c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.5s1.1 2.9 1.2 3.1c.1.2 2.1 3.2 5.1 4.5 1.9.8 2.7.9 3.6.7.6-.1 1.8-.7 2-1.4.3-.7.3-1.3.2-1.4l-.8-.5zM12 21.8c-1.8 0-3.5-.5-5-1.4l-.4-.2-3.7 1 1-3.6-.2-.4A9.8 9.8 0 1 1 12 21.8zM12 0a12 12 0 0 0-10.3 18L0 24l6.2-1.6A12 12 0 1 0 12 0z"/>',
	);
}

/**
 * Icon names offered to editors in blocks.
 */
function bst_block_icon_names() {
	return array( 'check', 'star', 'heart', 'shield', 'truck', 'gift', 'support', 'rocket', 'chart', 'code', 'globe', 'lightbulb', 'users', 'settings', 'home', 'camera', 'message', 'phone', 'mail', 'pin', 'clock', 'search', 'cart', 'bag', 'user', 'arrow-right', 'play', 'quote', 'leaf', 'droplet', 'sun', 'sparkles', 'flask', 'award', 'tag', 'refresh', 'card', 'smile' );
}

/**
 * Clamp a number into a range.
 */
function bst_clamp( $value, $min, $max, $default ) {
	if ( ! is_numeric( $value ) ) {
		return $default;
	}
	return max( $min, min( $max, $value + 0 ) );
}

/**
 * Allow-list a string value.
 */
function bst_choice( $value, $allowed, $default ) {
	return in_array( $value, $allowed, true ) ? $value : $default;
}
