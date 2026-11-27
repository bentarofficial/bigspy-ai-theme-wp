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
		'button_text_color'      => '',
		'button_size'            => 'md',
		'field_height'           => 46,

		// Header.
		'header_layout'          => '',
		'header_full_width'      => false,
		'header_round_bottom'    => false,
		'header_round_size'      => 16,
		'fluid_gutter'           => 0,
		'fullwidth_text_edge'    => 'header',
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
		'page_title_h1'          => false,
		'header_banner_desktop'  => '',
		'header_banner_mobile'   => '',
		'title_bar_bg'           => '#f5f7fa',
		'title_bar_color'        => '#111827',
		'title_bar_image'        => '',
		'breadcrumbs'            => true,
		'breadcrumbs_pages'      => false,

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
		$value = translate( $value, 'bigspy-ai-theme-wp' ); // phpcs:ignore WordPress.WP.I18n
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
		'logo'          => __( 'Logo', 'bigspy-ai-theme-wp' ),
		'nav'           => __( 'Main menu', 'bigspy-ai-theme-wp' ),
		'nav_secondary' => __( 'Top bar menu', 'bigspy-ai-theme-wp' ),
		'nav_icon'      => __( 'Menu icon (off-canvas)', 'bigspy-ai-theme-wp' ),
		'search'        => __( 'Search icon', 'bigspy-ai-theme-wp' ),
		'search_form'   => __( 'Search form', 'bigspy-ai-theme-wp' ),
		'account'       => __( 'Account', 'bigspy-ai-theme-wp' ),
		'cart'          => __( 'Cart (WooCommerce)', 'bigspy-ai-theme-wp' ),
		'button_1'      => __( 'Button 1', 'bigspy-ai-theme-wp' ),
		'button_2'      => __( 'Button 2', 'bigspy-ai-theme-wp' ),
		'html_1'        => __( 'HTML 1', 'bigspy-ai-theme-wp' ),
		'html_2'        => __( 'HTML 2', 'bigspy-ai-theme-wp' ),
		'html_3'        => __( 'HTML 3', 'bigspy-ai-theme-wp' ),
		'contact'       => __( 'Contact info', 'bigspy-ai-theme-wp' ),
		'social'        => __( 'Social icons', 'bigspy-ai-theme-wp' ),
		'divider'       => __( 'Divider', 'bigspy-ai-theme-wp' ),
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

/** Build simple, editable header layouts for the one-click Customizer presets. */
function bst_header_layout_preset( $kind ) {
	$layouts = array(
		'shop' => array(
			'desktop' => array(
				'top'    => array( 'left' => array( 'nav_secondary' ), 'center' => array(), 'right' => array( 'contact' ) ),
				'main'   => array( 'left' => array( 'logo' ), 'center' => array( 'search_form' ), 'right' => array( 'account', 'cart' ) ),
				'bottom' => array( 'left' => array(), 'center' => array( 'nav' ), 'right' => array() ),
			),
			'mobile'  => array(
				'top'    => array( 'left' => array(), 'center' => array(), 'right' => array() ),
				'main'   => array( 'left' => array( 'nav_icon' ), 'center' => array( 'logo' ), 'right' => array( 'search', 'cart' ) ),
				'bottom' => array( 'left' => array(), 'center' => array(), 'right' => array() ),
			),
		),
		'service' => array(
			'desktop' => array(
				'top'    => array( 'left' => array( 'contact' ), 'center' => array(), 'right' => array( 'social' ) ),
				'main'   => array( 'left' => array( 'logo' ), 'center' => array(), 'right' => array( 'nav' ) ),
				'bottom' => array( 'left' => array(), 'center' => array(), 'right' => array() ),
			),
			'mobile'  => array(
				'top'    => array( 'left' => array(), 'center' => array(), 'right' => array() ),
				'main'   => array( 'left' => array( 'nav_icon' ), 'center' => array( 'logo' ), 'right' => array( 'button_1' ) ),
				'bottom' => array( 'left' => array(), 'center' => array(), 'right' => array() ),
			),
		),
		'minimal' => array(
			'desktop' => array(
				'top'    => array( 'left' => array(), 'center' => array(), 'right' => array() ),
				'main'   => array( 'left' => array( 'logo' ), 'center' => array(), 'right' => array( 'nav', 'search' ) ),
				'bottom' => array( 'left' => array(), 'center' => array(), 'right' => array() ),
			),
			'mobile'  => array(
				'top'    => array( 'left' => array(), 'center' => array(), 'right' => array() ),
				'main'   => array( 'left' => array( 'nav_icon' ), 'center' => array( 'logo' ), 'right' => array( 'search' ) ),
				'bottom' => array( 'left' => array(), 'center' => array(), 'right' => array() ),
			),
		),
	);
	return bst_normalize_header_layout( $layouts[ $kind ] ?? bst_default_header_layout() );
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
/** Main menu styles: key => label (shared by the Header and Main menu sections). */
function bst_nav_styles() {
	return array( 'plain' => __( 'Plain', 'bigspy-ai-theme-wp' ), 'underline' => __( 'Underline (grows from left)', 'bigspy-ai-theme-wp' ), 'underline-center' => __( 'Underline (grows from center)', 'bigspy-ai-theme-wp' ), 'underline-thick' => __( 'Thick bar under active', 'bigspy-ai-theme-wp' ), 'overline' => __( 'Bar on top', 'bigspy-ai-theme-wp' ), 'dot' => __( 'Dot below', 'bigspy-ai-theme-wp' ), 'pill' => __( 'Pill background', 'bigspy-ai-theme-wp' ), 'pill-soft' => __( 'Soft tinted pill', 'bigspy-ai-theme-wp' ), 'pill-outline' => __( 'Outlined pill', 'bigspy-ai-theme-wp' ), 'pill-gradient' => __( 'Gradient pill', 'bigspy-ai-theme-wp' ), 'pill-shadow' => __( 'Floating pill with shadow', 'bigspy-ai-theme-wp' ), 'box' => __( 'Square block', 'bigspy-ai-theme-wp' ), 'tabs' => __( 'Tabs', 'bigspy-ai-theme-wp' ), 'bracket' => __( 'Brackets [ ]', 'bigspy-ai-theme-wp' ), 'highlight' => __( 'Highlighter marker', 'bigspy-ai-theme-wp' ), 'bold' => __( 'Colour + bold only', 'bigspy-ai-theme-wp' ), 'lift' => __( 'Lift on hover', 'bigspy-ai-theme-wp' ), 'glow' => __( 'Soft glow', 'bigspy-ai-theme-wp' ), 'divided' => __( 'Divided by lines', 'bigspy-ai-theme-wp' ), 'divided-dot' => __( 'Divided by dots', 'bigspy-ai-theme-wp' ) );
}

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
	// Admins still see the column with an "Add widgets" link so the setting does not look broken.
	if ( 'none' !== $position && ! is_active_sidebar( bst_sidebar_id() ) && ! current_user_can( 'edit_theme_options' ) ) {
		$position = 'none';
	}
	return apply_filters( 'bst_sidebar_position', $position );
}

/**
 * Widget area that belongs to the current request.
 */
function bst_sidebar_id() {
	// Own areas for single posts, archives and single products; empty ones fall back.
	if ( function_exists( 'is_product' ) && is_product() ) {
		return is_active_sidebar( 'sidebar-product' ) ? 'sidebar-product' : 'sidebar-shop';
	}
	if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
		return 'sidebar-shop';
	}
	if ( is_page() ) {
		return 'sidebar-page';
	}
	if ( is_single() && is_active_sidebar( 'sidebar-single' ) ) {
		return 'sidebar-single';
	}
	if ( ( is_category() || is_tag() || is_tax() || is_search() || is_date() || is_author() ) && is_active_sidebar( 'sidebar-archive' ) ) {
		return 'sidebar-archive';
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
		'instagram' => '<path fill="currentColor" d="M7.0301.084c-1.2768.0602-2.1487.264-2.911.5634-.7888.3075-1.4575.72-2.1228 1.3877-.6652.6677-1.075 1.3368-1.3802 2.127-.2954.7638-.4956 1.6365-.552 2.914-.0564 1.2775-.0689 1.6882-.0626 4.947.0062 3.2586.0206 3.6671.0825 4.9473.061 1.2765.264 2.1482.5635 2.9107.308.7889.72 1.4573 1.388 2.1228.6679.6655 1.3365 1.0743 2.1285 1.38.7632.295 1.6361.4961 2.9134.552 1.2773.056 1.6884.069 4.9462.0627 3.2578-.0062 3.668-.0207 4.9478-.0814 1.28-.0607 2.147-.2652 2.9098-.5633.7889-.3086 1.4578-.72 2.1228-1.3881.665-.6682 1.0745-1.3378 1.3795-2.1284.2957-.7632.4966-1.636.552-2.9124.056-1.2809.0692-1.6898.063-4.948-.0063-3.2583-.021-3.6668-.0817-4.9465-.0607-1.2797-.264-2.1487-.5633-2.9117-.3084-.7889-.72-1.4568-1.3876-2.1228C21.2982 1.33 20.628.9208 19.8378.6165 19.074.321 18.2017.1197 16.9244.0645 15.6471.0093 15.236-.005 11.977.0014 8.718.0076 8.31.0215 7.0301.0839m.1402 21.6932c-1.17-.0509-1.8053-.2453-2.2287-.408-.5606-.216-.96-.4771-1.3819-.895-.422-.4178-.6811-.8186-.9-1.378-.1644-.4234-.3624-1.058-.4171-2.228-.0595-1.2645-.072-1.6442-.079-4.848-.007-3.2037.0053-3.583.0607-4.848.05-1.169.2456-1.805.408-2.2282.216-.5613.4762-.96.895-1.3816.4188-.4217.8184-.6814 1.3783-.9003.423-.1651 1.0575-.3614 2.227-.4171 1.2655-.06 1.6447-.072 4.848-.079 3.2033-.007 3.5835.005 4.8495.0608 1.169.0508 1.8053.2445 2.228.408.5608.216.96.4754 1.3816.895.4217.4194.6816.8176.9005 1.3787.1653.4217.3617 1.056.4169 2.2263.0602 1.2655.0739 1.645.0796 4.848.0058 3.203-.0055 3.5834-.061 4.848-.051 1.17-.245 1.8055-.408 2.2294-.216.5604-.4763.96-.8954 1.3814-.419.4215-.8181.6811-1.3783.9-.4224.1649-1.0577.3617-2.2262.4174-1.2656.0595-1.6448.072-4.8493.079-3.2045.007-3.5825-.006-4.848-.0608M16.953 5.5864A1.44 1.44 0 1 0 18.39 4.144a1.44 1.44 0 0 0-1.437 1.4424M5.8385 12.012c.0067 3.4032 2.7706 6.1557 6.173 6.1493 3.4026-.0065 6.157-2.7701 6.1506-6.1733-.0065-3.4032-2.771-6.1565-6.174-6.1498-3.403.0067-6.156 2.771-6.1496 6.1738M8 12.0077a4 4 0 1 1 4.008 3.9921A3.9996 3.9996 0 0 1 8 12.0077"/>',
		'youtube'   => '<path fill="currentColor" d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>',
		'tiktok'    => '<path fill="currentColor" d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>',
		'zalo'      => '<path ' . $s . ' d="M12 3C6.8 3 2.5 6.6 2.5 11c0 2.5 1.3 4.7 3.4 6.1L5.2 21l3.9-2c.9.2 1.9.3 2.9.3 5.2 0 9.5-3.6 9.5-8.2S17.2 3 12 3z"/><text x="12" y="14.6" text-anchor="middle" font-family="Arial,sans-serif" font-size="8.5" font-weight="700" fill="currentColor">Z</text>',
		'x'         => '<path fill="currentColor" d="M17.8 3h3.1l-6.8 7.7L22 21h-6.2l-4.9-6.4L5.3 21H2.2l7.2-8.3L1.8 3h6.4l4.4 5.8zm-1.1 16.2h1.7L7.4 4.7H5.6z"/>',
		'linkedin'  => '<path fill="currentColor" d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zM3 9.5h4V21H3zM9.5 9.5h3.8v1.6h.1c.5-1 1.8-2 3.8-2 4 0 4.8 2.6 4.8 6V21h-4v-5.2c0-1.2 0-2.8-1.7-2.8s-2 1.3-2 2.7V21h-4z"/>',
		'pinterest' => '<path fill="currentColor" d="M12 2a10 10 0 0 0-3.6 19.3c-.1-.8-.2-2 0-2.9l1.2-5s-.3-.6-.3-1.5c0-1.4.8-2.5 1.8-2.5.9 0 1.3.7 1.3 1.4 0 .9-.6 2.2-.9 3.4-.2 1 .5 1.9 1.6 1.9 1.9 0 3.3-2 3.3-4.9 0-2.5-1.8-4.3-4.4-4.3-3 0-4.8 2.3-4.8 4.6 0 .9.4 1.9.8 2.4l.1.4-.3 1.2c0 .2-.2.3-.4.2-1.4-.7-2.2-2.7-2.2-4.3 0-3.5 2.5-6.7 7.3-6.7 3.8 0 6.8 2.7 6.8 6.4 0 3.8-2.4 6.9-5.8 6.9-1.1 0-2.2-.6-2.5-1.3l-.7 2.6c-.3 1-.9 2.2-1.4 2.9A10 10 0 1 0 12 2z"/>',
		'calendar'  => '<rect ' . $s . ' x="3" y="4.5" width="18" height="16.5" rx="2.5"/><path ' . $s . ' d="M3 9.5h18M8 2.5v4M16 2.5v4M8.5 14l2.2 2.2 4.8-4.7"/>',
		'clipboard' => '<path ' . $s . ' d="M9 4H6.5A1.5 1.5 0 0 0 5 5.5v15A1.5 1.5 0 0 0 6.5 22h11a1.5 1.5 0 0 0 1.5-1.5v-15A1.5 1.5 0 0 0 17.5 4H15"/><rect ' . $s . ' x="9" y="2.5" width="6" height="3.5" rx="1"/><path ' . $s . ' d="M8.5 11h7M8.5 14.5h7M8.5 18h4"/>',
		'send'      => '<path ' . $s . ' d="M22 2 11 13M22 2l-7 20-4-9-9-4 20-7z"/>',
		'zalo-brand' => '<path fill="#fff" d="M12 2.2C6.3 2.2 1.8 6 1.8 10.9c0 2.7 1.4 5.1 3.6 6.7l-.9 3.9c-.1.4.3.7.7.5l4.2-2.2c.8.2 1.7.3 2.6.3 5.7 0 10.2-3.9 10.2-8.8S17.7 2.2 12 2.2z"/><path fill="#0068ff" transform="translate(5.4 4.3) scale(.55)" d="M12.49 10.2722v-.4496h1.3467v6.3218h-.7704a.576.576 0 01-.5763-.5729l-.0006.0005a3.273 3.273 0 01-1.9372.6321c-1.8138 0-3.2844-1.4697-3.2844-3.2823 0-1.8125 1.4706-3.2822 3.2844-3.2822a3.273 3.273 0 011.9372.6321l.0006.0005zM6.9188 7.7896v.205c0 .3823-.051.6944-.2995 1.0605l-.03.0343c-.0542.0615-.1815.206-.2421.2843L2.024 14.8h4.8948v.7682a.5764.5764 0 01-.5767.5761H0v-.3622c0-.4436.1102-.6414.2495-.8476L4.8582 9.23H.1922V7.7896h6.7266zm8.5513 8.3548a.4805.4805 0 01-.4803-.4798v-7.875h1.4416v8.3548H15.47zM20.6934 9.6C22.52 9.6 24 11.0807 24 12.9044c0 1.8252-1.4801 3.306-3.3066 3.306-1.8264 0-3.3066-1.4808-3.3066-3.306 0-1.8237 1.4802-3.3044 3.3066-3.3044zm-10.1412 5.253c1.0675 0 1.9324-.8645 1.9324-1.9312 0-1.065-.865-1.9295-1.9324-1.9295s-1.9324.8644-1.9324 1.9295c0 1.0667.865 1.9312 1.9324 1.9312zm10.1412-.0033c1.0737 0 1.945-.8707 1.945-1.9453 0-1.073-.8713-1.9436-1.945-1.9436-1.0753 0-1.945.8706-1.945 1.9436 0 1.0746.8697 1.9453 1.945 1.9453z"/>',
		'messenger' => '<path fill="currentColor" d="M.001 11.639C.001 4.949 5.241 0 12.001 0S24 4.95 24 11.639c0 6.689-5.24 11.638-12 11.638-1.21 0-2.38-.16-3.47-.46a.96.96 0 00-.64.05l-2.39 1.05a.96.96 0 01-1.35-.85l-.07-2.14a.97.97 0 00-.32-.68A11.39 11.389 0 01.002 11.64zm8.32-2.19l-3.52 5.6c-.35.53.32 1.139.82.75l3.79-2.87c.26-.2.6-.2.87 0l2.8 2.1c.84.63 2.04.4 2.6-.48l3.52-5.6c.35-.53-.32-1.13-.82-.75l-3.79 2.87c-.25.2-.6.2-.86 0l-2.8-2.1a1.8 1.8 0 00-2.61.48z"/>',
		'github'    => '<path fill="currentColor" d="M12 .3a12 12 0 0 0-3.8 23.4c.6.1.8-.3.8-.6v-2c-3.3.7-4-1.6-4-1.6-.6-1.4-1.4-1.8-1.4-1.8-1-.7.1-.7.1-.7 1.2.1 1.8 1.2 1.8 1.2 1.1 1.8 2.8 1.3 3.5 1 0-.8.4-1.3.7-1.6-2.7-.3-5.5-1.3-5.5-5.9 0-1.3.5-2.4 1.2-3.2-.1-.3-.5-1.5.1-3.2 0 0 1-.3 3.3 1.2a11.5 11.5 0 0 1 6 0C17.3 4.6 18.3 5 18.3 5c.7 1.7.2 2.9.1 3.2.8.8 1.2 1.9 1.2 3.2 0 4.6-2.8 5.6-5.5 5.9.4.4.8 1.1.8 2.2v3.3c0 .3.2.7.8.6A12 12 0 0 0 12 .3z"/>',
		'discord'   => '<path fill="currentColor" d="M19.5 5.2A17 17 0 0 0 15.3 4l-.5 1a15 15 0 0 0-5.6 0l-.5-1a17 17 0 0 0-4.2 1.2C1.8 9.2 1.1 13.1 1.5 17a17 17 0 0 0 5.1 2.6l1.1-1.8a11 11 0 0 1-1.7-.8l.4-.3a12 12 0 0 0 11.2 0l.4.3a11 11 0 0 1-1.7.8l1.1 1.8a17 17 0 0 0 5.1-2.6c.5-4.5-.8-8.4-3-11.8zM8.7 14.6c-1 0-1.8-.9-1.8-2.1s.8-2.1 1.8-2.1 1.8 1 1.8 2.1-.8 2.1-1.8 2.1zm6.6 0c-1 0-1.8-.9-1.8-2.1s.8-2.1 1.8-2.1 1.8 1 1.8 2.1-.8 2.1-1.8 2.1z"/>',
		'threads'   => '<path ' . $s . ' d="M16 11.5c0-2.6-1.8-4-4-4s-3.5 1.6-3.5 3.6 1.4 3.4 3.3 3.4c2.2 0 4.2-1.6 4.2-5.2M16 11c0 3 1.3 4.5 3 4.5 1.5 0 2.5-1.6 2.5-4.5A9.5 9.5 0 1 0 12 21.5c2 0 3.6-.5 5-1.4"/>',
		'shopee'    => '<path fill="currentColor" d="M12 2a4 4 0 0 0-4 4H4.6L3.5 21.5h17L19.4 6H16a4 4 0 0 0-4-4zm0 1.6A2.4 2.4 0 0 1 14.4 6H9.6A2.4 2.4 0 0 1 12 3.6zM12 9.3c1 0 1.9.3 2.6 1l-.8 1.1a2.7 2.7 0 0 0-1.8-.7c-.8 0-1.3.3-1.3.8 0 1.3 4.4.7 4.4 3.6 0 1.4-1.2 2.4-3.1 2.4-1.2 0-2.3-.4-3.1-1.2l.8-1.1c.6.6 1.4.9 2.3.9.9 0 1.5-.4 1.5-.9 0-1.5-4.4-.7-4.4-3.6 0-1.3 1.1-2.3 2.9-2.3z" fill-rule="evenodd"/>',
		'lazada'    => '<path fill="currentColor" d="M12 2 2.5 7.4v9.2L12 22l9.5-5.4V7.4L12 2zm-2.6 5.5h1.8v7.4h4.2v1.6h-6V7.5z"/>',
		'link'      => '<path ' . $s . ' d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"/>',
		'print'     => '<path ' . $s . ' d="M7 9V3h10v6M7 17H4v-7h16v7h-3M7 14h10v7H7z"/>',
		'telegram'  => '<path fill="currentColor" d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>',
		'viber'     => '<path fill="currentColor" d="M11.4 0C9.473.028 5.333.344 3.02 2.467 1.302 4.187.696 6.7.633 9.817.57 12.933.488 18.776 6.12 20.36h.003l-.004 2.416s-.037.977.61 1.177c.777.242 1.234-.5 1.98-1.302.407-.44.972-1.084 1.397-1.58 3.85.326 6.812-.416 7.15-.525.776-.252 5.176-.816 5.892-6.657.74-6.02-.36-9.83-2.34-11.546-.596-.55-3.006-2.3-8.375-2.323 0 0-.395-.025-1.037-.017zm.058 1.693c.545-.004.88.017.88.017 4.542.02 6.717 1.388 7.222 1.846 1.675 1.435 2.53 4.868 1.906 9.897v.002c-.604 4.878-4.174 5.184-4.832 5.395-.28.09-2.882.737-6.153.524 0 0-2.436 2.94-3.197 3.704-.12.12-.26.167-.352.144-.13-.033-.166-.188-.165-.414l.02-4.018c-4.762-1.32-4.485-6.292-4.43-8.895.054-2.604.543-4.738 1.996-6.173 1.96-1.773 5.474-2.018 7.11-2.03zm.38 2.602c-.167 0-.303.135-.304.302 0 .167.133.303.3.305 1.624.01 2.946.537 4.028 1.592 1.073 1.046 1.62 2.468 1.633 4.334.002.167.14.3.307.3.166-.002.3-.138.3-.304-.014-1.984-.618-3.596-1.816-4.764-1.19-1.16-2.692-1.753-4.447-1.765zm-3.96.695c-.19-.032-.4.005-.616.117l-.01.002c-.43.247-.816.562-1.146.932-.002.004-.006.004-.008.008-.267.323-.42.638-.46.948-.008.046-.01.093-.007.14 0 .136.022.27.065.4l.013.01c.135.48.473 1.276 1.205 2.604.42.768.903 1.5 1.446 2.186.27.344.56.673.87.984l.132.132c.31.308.64.6.984.87.686.543 1.418 1.027 2.186 1.447 1.328.733 2.126 1.07 2.604 1.206l.01.014c.13.042.265.064.402.063.046.002.092 0 .138-.008.31-.036.627-.19.948-.46.004 0 .003-.002.008-.005.37-.33.683-.72.93-1.148l.003-.01c.225-.432.15-.842-.18-1.12-.004 0-.698-.58-1.037-.83-.36-.255-.73-.492-1.113-.71-.51-.285-1.032-.106-1.248.174l-.447.564c-.23.283-.657.246-.657.246-3.12-.796-3.955-3.955-3.955-3.955s-.037-.426.248-.656l.563-.448c.277-.215.456-.737.17-1.248-.217-.383-.454-.756-.71-1.115-.25-.34-.826-1.033-.83-1.035-.137-.165-.31-.265-.502-.297zm4.49.88c-.158.002-.29.124-.3.282-.01.167.115.312.282.324 1.16.085 2.017.466 2.645 1.15.63.688.93 1.524.906 2.57-.002.168.13.306.3.31.166.003.305-.13.31-.297.025-1.175-.334-2.193-1.067-2.994-.74-.81-1.777-1.253-3.05-1.346h-.024zm.463 1.63c-.16.002-.29.127-.3.287-.008.167.12.31.288.32.523.028.875.175 1.113.422.24.245.388.62.416 1.164.01.167.15.295.318.287.167-.008.295-.15.287-.317-.03-.644-.215-1.178-.58-1.557-.367-.378-.893-.574-1.52-.607h-.018z"/>',
		'whatsapp'  => '<path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>',
		'phone-solid' => '<path fill="currentColor" d="M15,12H17A5,5 0 0,0 12,7V9A3,3 0 0,1 15,12M19,12H21C21,7 16.97,3 12,3V5C15.86,5 19,8.13 19,12M20,15.5C18.75,15.5 17.55,15.3 16.43,14.93C16.08,14.82 15.69,14.9 15.41,15.18L13.21,17.38C10.38,15.94 8.06,13.62 6.62,10.79L8.82,8.59C9.1,8.31 9.18,7.92 9.07,7.57C8.7,6.45 8.5,5.25 8.5,4A1,1 0 0,0 7.5,3H4A1,1 0 0,0 3,4A17,17 0 0,0 20,21A1,1 0 0,0 21,20V16.5A1,1 0 0,0 20,15.5Z"/>',
		'gmaps' => '<path fill="currentColor" d="M19.527 4.799c1.212 2.608.937 5.678-.405 8.173-1.101 2.047-2.744 3.74-4.098 5.614-.619.858-1.244 1.75-1.669 2.727-.141.325-.263.658-.383.992-.121.333-.224.673-.34 1.008-.109.314-.236.684-.627.687h-.007c-.466-.001-.579-.53-.695-.887-.284-.874-.581-1.713-1.019-2.525-.51-.944-1.145-1.817-1.79-2.671L19.527 4.799zM8.545 7.705l-3.959 4.707c.724 1.54 1.821 2.863 2.871 4.18.247.31.494.622.737.936l4.984-5.925-.029.01c-1.741.601-3.691-.291-4.392-1.987a3.377 3.377 0 0 1-.209-.716c-.063-.437-.077-.761-.004-1.198l.001-.007zM5.492 3.149l-.003.004c-1.947 2.466-2.281 5.88-1.117 8.77l4.785-5.689-.058-.05-3.607-3.035zM14.661.436l-3.838 4.563a.295.295 0 0 1 .027-.01c1.6-.551 3.403.15 4.22 1.626.176.319.323.683.377 1.045.068.446.085.773.012 1.22l-.003.016 3.836-4.561A8.382 8.382 0 0 0 14.67.439l-.009-.003zM9.466 5.868L14.162.285l-.047-.012A8.31 8.31 0 0 0 11.986 0a8.439 8.439 0 0 0-6.169 2.766l-.016.018 3.665 3.084z"/>',
		'gmail' => '<path fill="currentColor" d="M24 5.457v13.909c0 .904-.732 1.636-1.636 1.636h-3.819V11.73L12 16.64l-6.545-4.91v9.273H1.636A1.636 1.636 0 0 1 0 19.366V5.457c0-2.023 2.309-3.178 3.927-1.964L5.455 4.64 12 9.548l6.545-4.91 1.528-1.145C21.69 2.28 24 3.434 24 5.457z"/>',
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
