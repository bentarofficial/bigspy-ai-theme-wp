<?php
/**
 * Declarative list of Customizer sections and fields.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

function bst_customizer_sections() {
	$sections = array(
		// Header.
		'bst_header_builder' => array( 'title' => __( 'Header builder', 'bigspy-theme' ), 'panel' => 'bst_header', 'priority' => 1, 'description' => __( 'Drag elements between the rows. Rows with no visible element are hidden automatically. Click an element to open its settings.', 'bigspy-theme' ) ),
		'bst_header_general' => array( 'title' => __( 'Header layout & sticky', 'bigspy-theme' ), 'panel' => 'bst_header', 'priority' => 2 ),
		'bst_header_rows'    => array( 'title' => __( 'Rows: height & colors', 'bigspy-theme' ), 'panel' => 'bst_header', 'priority' => 3 ),
		'bst_header_logo'    => array( 'title' => __( 'Logo', 'bigspy-theme' ), 'panel' => 'bst_header', 'priority' => 4, 'description' => __( 'Upload the logo in Site Identity.', 'bigspy-theme' ) ),
		'bst_header_nav'     => array( 'title' => __( 'Navigation', 'bigspy-theme' ), 'panel' => 'bst_header', 'priority' => 5 ),
		'bst_header_search'  => array( 'title' => __( 'Search', 'bigspy-theme' ), 'panel' => 'bst_header', 'priority' => 6 ),
		'bst_header_buttons' => array( 'title' => __( 'Buttons', 'bigspy-theme' ), 'panel' => 'bst_header', 'priority' => 7 ),
		'bst_header_html'    => array( 'title' => __( 'HTML blocks', 'bigspy-theme' ), 'panel' => 'bst_header', 'priority' => 8, 'description' => __( 'HTML and shortcodes are allowed. Tokens: [year], [site_title], [phone], [email].', 'bigspy-theme' ) ),
		'bst_contact'        => array( 'title' => __( 'Contact info & social', 'bigspy-theme' ), 'panel' => 'bst_header', 'priority' => 9, 'description' => __( 'Used by the header, the mobile menu and the footer.', 'bigspy-theme' ) ),

		// Style.
		'bst_colors'         => array( 'title' => __( 'Colors', 'bigspy-theme' ), 'panel' => 'bst_style', 'priority' => 1, 'description' => __( 'These colors also appear in the block editor color palette.', 'bigspy-theme' ) ),
		'bst_typography'     => array( 'title' => __( 'Typography', 'bigspy-theme' ), 'panel' => 'bst_style', 'priority' => 2 ),
		'bst_buttons'        => array( 'title' => __( 'Buttons', 'bigspy-theme' ), 'panel' => 'bst_style', 'priority' => 3 ),

		// Layout.
		'bst_layout_site'    => array( 'title' => __( 'Site layout', 'bigspy-theme' ), 'panel' => 'bst_layout', 'priority' => 1 ),
		'bst_title_bar'      => array( 'title' => __( 'Page title & breadcrumbs', 'bigspy-theme' ), 'panel' => 'bst_layout', 'priority' => 2 ),

		// Blog.
		'bst_blog_archive'   => array( 'title' => __( 'Blog archive', 'bigspy-theme' ), 'panel' => 'bst_blog', 'priority' => 1 ),
		'bst_blog_single'    => array( 'title' => __( 'Single post', 'bigspy-theme' ), 'panel' => 'bst_blog', 'priority' => 2 ),

		// Footer.
		'bst_footer_cta'     => array( 'title' => __( 'Call-to-action band', 'bigspy-theme' ), 'panel' => 'bst_footer', 'priority' => 0, 'description' => __( 'A colored band shown above the footer on every page, e.g. “Ready to start? → Contact us”.', 'bigspy-theme' ) ),
		'bst_footer_widgets' => array( 'title' => __( 'Footer widgets', 'bigspy-theme' ), 'panel' => 'bst_footer', 'priority' => 1, 'description' => __( 'Add widgets in Appearance → Widgets → “Footer 1/2 – Column N”.', 'bigspy-theme' ) ),
		'bst_footer_bottom'  => array( 'title' => __( 'Footer bottom (copyright)', 'bigspy-theme' ), 'panel' => 'bst_footer', 'priority' => 2 ),
		'bst_float'          => array( 'title' => __( 'Floating contact buttons', 'bigspy-theme' ), 'panel' => 'bst_footer', 'priority' => 3 ),

		// Advanced.
		'bst_scripts'        => array( 'title' => __( 'Custom scripts', 'bigspy-theme' ), 'panel' => 'bst_advanced', 'priority' => 1, 'description' => __( 'Google Analytics, Facebook Pixel, chat widgets… Custom CSS goes in “Additional CSS”.', 'bigspy-theme' ) ),
		'bst_performance'    => array( 'title' => __( 'Performance & effects', 'bigspy-theme' ), 'panel' => 'bst_advanced', 'priority' => 2 ),
	);

	if ( class_exists( 'WooCommerce' ) ) {
		$sections['bst_shop']    = array( 'title' => __( 'BIGSPY: shop layout', 'bigspy-theme' ), 'panel' => 'woocommerce', 'priority' => 1 );
		$sections['bst_product'] = array( 'title' => __( 'BIGSPY: product cards & page', 'bigspy-theme' ), 'panel' => 'woocommerce', 'priority' => 2 );
		$sections['bst_catalog'] = array( 'title' => __( 'BIGSPY: catalog mode', 'bigspy-theme' ), 'panel' => 'woocommerce', 'priority' => 3, 'description' => __( 'Hide prices and add-to-cart buttons, show a contact button instead. Useful for showroom / quotation websites.', 'bigspy-theme' ) );
	}

	return $sections;
}

function bst_customizer_fields() {
	$px = function ( $min, $max, $step = 1 ) {
		return array( 'min' => $min, 'max' => $max, 'step' => $step );
	};
	$sidebar = array(
		'none'  => __( 'No sidebar', 'bigspy-theme' ),
		'left'  => __( 'Left sidebar', 'bigspy-theme' ),
		'right' => __( 'Right sidebar', 'bigspy-theme' ),
	);
	$weights = array(
		'300' => '300 Light',
		'400' => '400 Regular',
		'500' => '500 Medium',
		'600' => '600 Semi-bold',
		'700' => '700 Bold',
		'800' => '800 Extra-bold',
	);
	$transform = array(
		'none'      => __( 'Normal', 'bigspy-theme' ),
		'uppercase' => __( 'UPPERCASE', 'bigspy-theme' ),
		'capitalize'=> __( 'Capitalize', 'bigspy-theme' ),
	);
	$button_styles = array(
		'primary'   => __( 'Primary', 'bigspy-theme' ),
		'secondary' => __( 'Secondary', 'bigspy-theme' ),
		'outline'   => __( 'Outline', 'bigspy-theme' ),
		'white'     => __( 'White', 'bigspy-theme' ),
		'link'      => __( 'Plain link', 'bigspy-theme' ),
	);
	$fonts = bst_font_choices();

	$fields = array(
		// ---------------------------------------------------------------- Header builder.
		array( 'id' => 'header_layout', 'section' => 'bst_header_builder', 'type' => 'header_builder', 'label' => __( 'Header layout', 'bigspy-theme' ) ),

		// Header general.
		array( 'id' => 'header_full_width', 'section' => 'bst_header_general', 'type' => 'checkbox', 'label' => __( 'Full-width header', 'bigspy-theme' ) ),
		array( 'id' => 'header_shadow', 'section' => 'bst_header_general', 'type' => 'checkbox', 'label' => __( 'Shadow under header', 'bigspy-theme' ) ),
		array( 'id' => 'header_sticky', 'section' => 'bst_header_general', 'type' => 'checkbox', 'label' => __( 'Sticky header', 'bigspy-theme' ), 'description' => __( 'The main and bottom rows stay on screen while scrolling.', 'bigspy-theme' ) ),
		array( 'id' => 'header_sticky_height', 'section' => 'bst_header_general', 'type' => 'number', 'label' => __( 'Sticky main row height (px)', 'bigspy-theme' ), 'input_attrs' => $px( 40, 140 ) ),
		array( 'id' => 'bst_h_transparent', 'section' => 'bst_header_general', 'type' => 'heading', 'label' => __( 'Transparent header', 'bigspy-theme' ), 'description' => __( 'Can also be enabled per page in the “BIGSPY – Page layout” box.', 'bigspy-theme' ) ),
		array( 'id' => 'header_transparent_home', 'section' => 'bst_header_general', 'type' => 'checkbox', 'label' => __( 'Transparent header on the homepage', 'bigspy-theme' ) ),
		array( 'id' => 'header_transparent_color', 'section' => 'bst_header_general', 'type' => 'color', 'label' => __( 'Text color on transparent header', 'bigspy-theme' ) ),

		// Rows.
		array( 'id' => 'bst_h_top', 'section' => 'bst_header_rows', 'type' => 'heading', 'label' => __( 'Top bar', 'bigspy-theme' ) ),
		array( 'id' => 'header_top_height', 'section' => 'bst_header_rows', 'type' => 'number', 'label' => __( 'Height (px)', 'bigspy-theme' ), 'input_attrs' => $px( 24, 80 ) ),
		array( 'id' => 'header_top_bg', 'section' => 'bst_header_rows', 'type' => 'color', 'label' => __( 'Background', 'bigspy-theme' ) ),
		array( 'id' => 'header_top_color', 'section' => 'bst_header_rows', 'type' => 'color', 'label' => __( 'Text color', 'bigspy-theme' ) ),
		array( 'id' => 'bst_h_main', 'section' => 'bst_header_rows', 'type' => 'heading', 'label' => __( 'Main header', 'bigspy-theme' ) ),
		array( 'id' => 'header_main_height', 'section' => 'bst_header_rows', 'type' => 'number', 'label' => __( 'Height (px)', 'bigspy-theme' ), 'input_attrs' => $px( 40, 200 ) ),
		array( 'id' => 'header_main_bg', 'section' => 'bst_header_rows', 'type' => 'color', 'label' => __( 'Background', 'bigspy-theme' ) ),
		array( 'id' => 'header_main_color', 'section' => 'bst_header_rows', 'type' => 'color', 'label' => __( 'Text color', 'bigspy-theme' ) ),
		array( 'id' => 'bst_h_bottom', 'section' => 'bst_header_rows', 'type' => 'heading', 'label' => __( 'Bottom header', 'bigspy-theme' ) ),
		array( 'id' => 'header_bottom_height', 'section' => 'bst_header_rows', 'type' => 'number', 'label' => __( 'Height (px)', 'bigspy-theme' ), 'input_attrs' => $px( 30, 120 ) ),
		array( 'id' => 'header_bottom_bg', 'section' => 'bst_header_rows', 'type' => 'color', 'label' => __( 'Background', 'bigspy-theme' ) ),
		array( 'id' => 'header_bottom_color', 'section' => 'bst_header_rows', 'type' => 'color', 'label' => __( 'Text color', 'bigspy-theme' ) ),
		array( 'id' => 'bst_h_mobile', 'section' => 'bst_header_rows', 'type' => 'heading', 'label' => __( 'Mobile', 'bigspy-theme' ) ),
		array( 'id' => 'header_mobile_height', 'section' => 'bst_header_rows', 'type' => 'number', 'label' => __( 'Mobile main row height (px)', 'bigspy-theme' ), 'input_attrs' => $px( 40, 120 ) ),

		// Logo.
		array( 'id' => 'logo_width', 'section' => 'bst_header_logo', 'type' => 'number', 'label' => __( 'Logo max width – desktop (px)', 'bigspy-theme' ), 'input_attrs' => $px( 40, 600 ) ),
		array( 'id' => 'logo_width_mobile', 'section' => 'bst_header_logo', 'type' => 'number', 'label' => __( 'Logo max width – mobile (px)', 'bigspy-theme' ), 'input_attrs' => $px( 40, 400 ) ),

		// Navigation.
		array( 'id' => 'nav_style', 'section' => 'bst_header_nav', 'type' => 'select', 'label' => __( 'Main menu style', 'bigspy-theme' ), 'choices' => array( 'plain' => __( 'Plain', 'bigspy-theme' ), 'underline' => __( 'Underline on hover', 'bigspy-theme' ), 'pill' => __( 'Pill background', 'bigspy-theme' ), 'divided' => __( 'Divided', 'bigspy-theme' ) ) ),
		array( 'id' => 'font_nav', 'section' => 'bst_header_nav', 'type' => 'select', 'label' => __( 'Menu font', 'bigspy-theme' ), 'choices' => $fonts ),
		array( 'id' => 'nav_size', 'section' => 'bst_header_nav', 'type' => 'number', 'label' => __( 'Menu font size (px)', 'bigspy-theme' ), 'input_attrs' => $px( 11, 24 ) ),
		array( 'id' => 'nav_weight', 'section' => 'bst_header_nav', 'type' => 'select', 'label' => __( 'Menu font weight', 'bigspy-theme' ), 'choices' => $weights ),
		array( 'id' => 'nav_transform', 'section' => 'bst_header_nav', 'type' => 'select', 'label' => __( 'Menu text case', 'bigspy-theme' ), 'choices' => $transform ),
		array( 'id' => 'offcanvas_side', 'section' => 'bst_header_nav', 'type' => 'radio', 'label' => __( 'Off-canvas (mobile) menu opens from', 'bigspy-theme' ), 'choices' => array( 'left' => __( 'Left', 'bigspy-theme' ), 'right' => __( 'Right', 'bigspy-theme' ) ) ),

		// Search.
		array( 'id' => 'search_style', 'section' => 'bst_header_search', 'type' => 'radio', 'label' => __( 'Search icon opens', 'bigspy-theme' ), 'choices' => array( 'dropdown' => __( 'Dropdown under header', 'bigspy-theme' ), 'lightbox' => __( 'Full-screen lightbox', 'bigspy-theme' ) ) ),
		array( 'id' => 'search_products', 'section' => 'bst_header_search', 'type' => 'checkbox', 'label' => __( 'Search products only (WooCommerce)', 'bigspy-theme' ) ),

		// Buttons.
		array( 'id' => 'bst_h_btn1', 'section' => 'bst_header_buttons', 'type' => 'heading', 'label' => __( 'Button 1', 'bigspy-theme' ) ),
		array( 'id' => 'header_button_1_text', 'section' => 'bst_header_buttons', 'type' => 'text', 'label' => __( 'Text', 'bigspy-theme' ) ),
		array( 'id' => 'header_button_1_url', 'section' => 'bst_header_buttons', 'type' => 'text', 'label' => __( 'Link', 'bigspy-theme' ), 'description' => __( 'URL, tel:+15551234567 or mailto:…', 'bigspy-theme' ) ),
		array( 'id' => 'header_button_1_style', 'section' => 'bst_header_buttons', 'type' => 'select', 'label' => __( 'Style', 'bigspy-theme' ), 'choices' => $button_styles ),
		array( 'id' => 'header_button_1_new_tab', 'section' => 'bst_header_buttons', 'type' => 'checkbox', 'label' => __( 'Open in new tab', 'bigspy-theme' ) ),
		array( 'id' => 'bst_h_btn2', 'section' => 'bst_header_buttons', 'type' => 'heading', 'label' => __( 'Button 2', 'bigspy-theme' ) ),
		array( 'id' => 'header_button_2_text', 'section' => 'bst_header_buttons', 'type' => 'text', 'label' => __( 'Text', 'bigspy-theme' ) ),
		array( 'id' => 'header_button_2_url', 'section' => 'bst_header_buttons', 'type' => 'text', 'label' => __( 'Link', 'bigspy-theme' ) ),
		array( 'id' => 'header_button_2_style', 'section' => 'bst_header_buttons', 'type' => 'select', 'label' => __( 'Style', 'bigspy-theme' ), 'choices' => $button_styles ),
		array( 'id' => 'header_button_2_new_tab', 'section' => 'bst_header_buttons', 'type' => 'checkbox', 'label' => __( 'Open in new tab', 'bigspy-theme' ) ),

		// HTML.
		array( 'id' => 'header_html_1', 'section' => 'bst_header_html', 'type' => 'html', 'label' => __( 'HTML 1', 'bigspy-theme' ) ),
		array( 'id' => 'header_html_2', 'section' => 'bst_header_html', 'type' => 'html', 'label' => __( 'HTML 2', 'bigspy-theme' ) ),
		array( 'id' => 'header_html_3', 'section' => 'bst_header_html', 'type' => 'html', 'label' => __( 'HTML 3', 'bigspy-theme' ) ),

		// Contact & social.
		array( 'id' => 'contact_phone', 'section' => 'bst_contact', 'type' => 'text', 'label' => __( 'Phone / hotline', 'bigspy-theme' ) ),
		array( 'id' => 'contact_email', 'section' => 'bst_contact', 'type' => 'text', 'label' => __( 'Email', 'bigspy-theme' ) ),
		array( 'id' => 'contact_address', 'section' => 'bst_contact', 'type' => 'text', 'label' => __( 'Address', 'bigspy-theme' ) ),
		array( 'id' => 'contact_hours', 'section' => 'bst_contact', 'type' => 'text', 'label' => __( 'Opening hours', 'bigspy-theme' ) ),
		array( 'id' => 'bst_h_social', 'section' => 'bst_contact', 'type' => 'heading', 'label' => __( 'Social profiles (full URLs)', 'bigspy-theme' ), 'description' => __( 'For Zalo you can enter just the phone number.', 'bigspy-theme' ) ),
	);

	foreach ( bst_social_networks() as $key => $label ) {
		$fields[] = array( 'id' => 'social_' . $key, 'section' => 'bst_contact', 'type' => 'text', 'label' => $label );
	}

	$fields = array_merge(
		$fields,
		array(
			// ---------------------------------------------------------------- Colors.
			array( 'id' => 'color_primary', 'section' => 'bst_colors', 'type' => 'color', 'label' => __( 'Primary', 'bigspy-theme' ) ),
			array( 'id' => 'color_secondary', 'section' => 'bst_colors', 'type' => 'color', 'label' => __( 'Secondary', 'bigspy-theme' ) ),
			array( 'id' => 'color_success', 'section' => 'bst_colors', 'type' => 'color', 'label' => __( 'Success', 'bigspy-theme' ) ),
			array( 'id' => 'color_alert', 'section' => 'bst_colors', 'type' => 'color', 'label' => __( 'Alert / sale', 'bigspy-theme' ) ),
			array( 'id' => 'color_text', 'section' => 'bst_colors', 'type' => 'color', 'label' => __( 'Body text', 'bigspy-theme' ) ),
			array( 'id' => 'color_heading', 'section' => 'bst_colors', 'type' => 'color', 'label' => __( 'Headings', 'bigspy-theme' ) ),
			array( 'id' => 'color_link', 'section' => 'bst_colors', 'type' => 'color', 'label' => __( 'Links', 'bigspy-theme' ) ),
			array( 'id' => 'color_link_hover', 'section' => 'bst_colors', 'type' => 'color', 'label' => __( 'Links on hover', 'bigspy-theme' ) ),
			array( 'id' => 'color_border', 'section' => 'bst_colors', 'type' => 'color', 'label' => __( 'Borders', 'bigspy-theme' ) ),
			array( 'id' => 'color_light', 'section' => 'bst_colors', 'type' => 'color', 'label' => __( 'Light background (cards, boxes)', 'bigspy-theme' ) ),
			array( 'id' => 'color_body_bg', 'section' => 'bst_colors', 'type' => 'color', 'label' => __( 'Page background', 'bigspy-theme' ) ),

			// Typography.
			array( 'id' => 'font_body', 'section' => 'bst_typography', 'type' => 'select', 'label' => __( 'Body font', 'bigspy-theme' ), 'choices' => $fonts, 'description' => __( 'All Google fonts in this list support Vietnamese.', 'bigspy-theme' ) ),
			array( 'id' => 'font_weight_body', 'section' => 'bst_typography', 'type' => 'select', 'label' => __( 'Body font weight', 'bigspy-theme' ), 'choices' => $weights ),
			array( 'id' => 'font_size_base', 'section' => 'bst_typography', 'type' => 'number', 'label' => __( 'Body font size – desktop (px)', 'bigspy-theme' ), 'input_attrs' => $px( 12, 22 ) ),
			array( 'id' => 'font_size_mobile', 'section' => 'bst_typography', 'type' => 'number', 'label' => __( 'Body font size – mobile (px)', 'bigspy-theme' ), 'input_attrs' => $px( 12, 20 ) ),
			array( 'id' => 'line_height', 'section' => 'bst_typography', 'type' => 'number', 'label' => __( 'Line height', 'bigspy-theme' ), 'input_attrs' => $px( 1, 2.4, 0.05 ) ),
			array( 'id' => 'font_heading', 'section' => 'bst_typography', 'type' => 'select', 'label' => __( 'Heading font', 'bigspy-theme' ), 'choices' => $fonts ),
			array( 'id' => 'font_weight_heading', 'section' => 'bst_typography', 'type' => 'select', 'label' => __( 'Heading font weight', 'bigspy-theme' ), 'choices' => $weights ),
			array( 'id' => 'heading_transform', 'section' => 'bst_typography', 'type' => 'select', 'label' => __( 'Heading text case', 'bigspy-theme' ), 'choices' => $transform ),
			array( 'id' => 'h1_size', 'section' => 'bst_typography', 'type' => 'number', 'label' => __( 'H1 size (px)', 'bigspy-theme' ), 'input_attrs' => $px( 20, 90 ) ),
			array( 'id' => 'h2_size', 'section' => 'bst_typography', 'type' => 'number', 'label' => __( 'H2 size (px)', 'bigspy-theme' ), 'input_attrs' => $px( 18, 72 ) ),
			array( 'id' => 'h3_size', 'section' => 'bst_typography', 'type' => 'number', 'label' => __( 'H3 size (px)', 'bigspy-theme' ), 'input_attrs' => $px( 16, 56 ) ),
			array( 'id' => 'h4_size', 'section' => 'bst_typography', 'type' => 'number', 'label' => __( 'H4 size (px)', 'bigspy-theme' ), 'input_attrs' => $px( 14, 40 ) ),

			// Buttons.
			array( 'id' => 'button_radius', 'section' => 'bst_buttons', 'type' => 'number', 'label' => __( 'Corner radius (px)', 'bigspy-theme' ), 'input_attrs' => $px( 0, 60 ) ),
			array( 'id' => 'button_weight', 'section' => 'bst_buttons', 'type' => 'select', 'label' => __( 'Font weight', 'bigspy-theme' ), 'choices' => $weights ),
			array( 'id' => 'button_transform', 'section' => 'bst_buttons', 'type' => 'select', 'label' => __( 'Text case', 'bigspy-theme' ), 'choices' => $transform ),

			// ---------------------------------------------------------------- Layout.
			array( 'id' => 'site_layout', 'section' => 'bst_layout_site', 'type' => 'radio', 'label' => __( 'Site layout', 'bigspy-theme' ), 'choices' => array( 'full' => __( 'Full width', 'bigspy-theme' ), 'boxed' => __( 'Boxed', 'bigspy-theme' ) ) ),
			array( 'id' => 'boxed_bg', 'section' => 'bst_layout_site', 'type' => 'color', 'label' => __( 'Outside background (boxed layout)', 'bigspy-theme' ) ),
			array( 'id' => 'container_width', 'section' => 'bst_layout_site', 'type' => 'number', 'label' => __( 'Container width (px)', 'bigspy-theme' ), 'input_attrs' => $px( 960, 1920, 10 ) ),
			array( 'id' => 'sidebar_width', 'section' => 'bst_layout_site', 'type' => 'number', 'label' => __( 'Sidebar width (px)', 'bigspy-theme' ), 'input_attrs' => $px( 200, 480, 10 ) ),
			array( 'id' => 'page_sidebar', 'section' => 'bst_layout_site', 'type' => 'select', 'label' => __( 'Default sidebar on pages', 'bigspy-theme' ), 'choices' => $sidebar ),

			array( 'id' => 'title_bar_style', 'section' => 'bst_title_bar', 'type' => 'radio', 'label' => __( 'Page title style', 'bigspy-theme' ), 'choices' => array( 'simple' => __( 'Simple (inside content)', 'bigspy-theme' ), 'bar' => __( 'Full-width title bar', 'bigspy-theme' ), 'none' => __( 'Hidden', 'bigspy-theme' ) ) ),
			array( 'id' => 'title_bar_bg', 'section' => 'bst_title_bar', 'type' => 'color', 'label' => __( 'Title bar background', 'bigspy-theme' ) ),
			array( 'id' => 'title_bar_color', 'section' => 'bst_title_bar', 'type' => 'color', 'label' => __( 'Title bar text color', 'bigspy-theme' ) ),
			array( 'id' => 'title_bar_image', 'section' => 'bst_title_bar', 'type' => 'image', 'label' => __( 'Title bar background image', 'bigspy-theme' ) ),
			array( 'id' => 'breadcrumbs', 'section' => 'bst_title_bar', 'type' => 'checkbox', 'label' => __( 'Show breadcrumbs', 'bigspy-theme' ), 'description' => __( 'Uses Rank Math / Yoast breadcrumbs when those plugins are active.', 'bigspy-theme' ) ),

			// ---------------------------------------------------------------- Blog.
			array( 'id' => 'blog_layout', 'section' => 'bst_blog_archive', 'type' => 'radio', 'label' => __( 'Layout', 'bigspy-theme' ), 'choices' => array( 'grid' => __( 'Grid', 'bigspy-theme' ), 'list' => __( 'List (image left)', 'bigspy-theme' ) ) ),
			array( 'id' => 'blog_columns', 'section' => 'bst_blog_archive', 'type' => 'select', 'label' => __( 'Grid columns', 'bigspy-theme' ), 'choices' => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4' ) ),
			array( 'id' => 'blog_sidebar', 'section' => 'bst_blog_archive', 'type' => 'select', 'label' => __( 'Sidebar', 'bigspy-theme' ), 'choices' => $sidebar ),
			array( 'id' => 'blog_card_style', 'section' => 'bst_blog_archive', 'type' => 'select', 'label' => __( 'Card style', 'bigspy-theme' ), 'choices' => array( 'shadow' => __( 'Shadow', 'bigspy-theme' ), 'bordered' => __( 'Bordered', 'bigspy-theme' ), 'plain' => __( 'Plain', 'bigspy-theme' ), 'overlay' => __( 'Text over image', 'bigspy-theme' ) ) ),
			array( 'id' => 'blog_image_ratio', 'section' => 'bst_blog_archive', 'type' => 'select', 'label' => __( 'Image ratio', 'bigspy-theme' ), 'choices' => array( '16-9' => '16:9', '4-3' => '4:3', '3-2' => '3:2', '1-1' => '1:1', 'auto' => __( 'Original', 'bigspy-theme' ) ) ),
			array( 'id' => 'blog_show_meta', 'section' => 'bst_blog_archive', 'type' => 'checkbox', 'label' => __( 'Show category & date', 'bigspy-theme' ) ),
			array( 'id' => 'blog_show_excerpt', 'section' => 'bst_blog_archive', 'type' => 'checkbox', 'label' => __( 'Show excerpt', 'bigspy-theme' ) ),
			array( 'id' => 'blog_excerpt_length', 'section' => 'bst_blog_archive', 'type' => 'number', 'label' => __( 'Excerpt length (words)', 'bigspy-theme' ), 'input_attrs' => $px( 5, 80 ) ),
			array( 'id' => 'blog_read_more', 'section' => 'bst_blog_archive', 'type' => 'text', 'label' => __( '“Read more” text (empty to hide)', 'bigspy-theme' ) ),

			array( 'id' => 'single_sidebar', 'section' => 'bst_blog_single', 'type' => 'select', 'label' => __( 'Sidebar', 'bigspy-theme' ), 'choices' => $sidebar ),
			array( 'id' => 'single_featured_image', 'section' => 'bst_blog_single', 'type' => 'checkbox', 'label' => __( 'Show featured image', 'bigspy-theme' ) ),
			array( 'id' => 'single_share', 'section' => 'bst_blog_single', 'type' => 'checkbox', 'label' => __( 'Share buttons', 'bigspy-theme' ) ),
			array( 'id' => 'single_author_box', 'section' => 'bst_blog_single', 'type' => 'checkbox', 'label' => __( 'Author box (when the author has a bio)', 'bigspy-theme' ) ),
			array( 'id' => 'single_nav', 'section' => 'bst_blog_single', 'type' => 'checkbox', 'label' => __( 'Previous / next post links', 'bigspy-theme' ) ),
			array( 'id' => 'single_related', 'section' => 'bst_blog_single', 'type' => 'checkbox', 'label' => __( 'Related posts', 'bigspy-theme' ) ),

			// ---------------------------------------------------------------- Footer.
			array( 'id' => 'footer_cta_enable', 'section' => 'bst_footer_cta', 'type' => 'checkbox', 'label' => __( 'Show call-to-action band', 'bigspy-theme' ) ),
			array( 'id' => 'footer_cta_title', 'section' => 'bst_footer_cta', 'type' => 'text', 'label' => __( 'Title', 'bigspy-theme' ) ),
			array( 'id' => 'footer_cta_text', 'section' => 'bst_footer_cta', 'type' => 'html', 'label' => __( 'Text', 'bigspy-theme' ) ),
			array( 'id' => 'footer_cta_button', 'section' => 'bst_footer_cta', 'type' => 'text', 'label' => __( 'Button text', 'bigspy-theme' ) ),
			array( 'id' => 'footer_cta_url', 'section' => 'bst_footer_cta', 'type' => 'text', 'label' => __( 'Button link', 'bigspy-theme' ) ),
			array( 'id' => 'footer_cta_button_2', 'section' => 'bst_footer_cta', 'type' => 'text', 'label' => __( 'Second button text (optional)', 'bigspy-theme' ) ),
			array( 'id' => 'footer_cta_url_2', 'section' => 'bst_footer_cta', 'type' => 'text', 'label' => __( 'Second button link', 'bigspy-theme' ) ),
			array( 'id' => 'footer_cta_bg', 'section' => 'bst_footer_cta', 'type' => 'color', 'label' => __( 'Background', 'bigspy-theme' ) ),
			array( 'id' => 'footer_cta_hide_pages', 'section' => 'bst_footer_cta', 'type' => 'text', 'label' => __( 'Hide on pages (IDs or slugs, comma separated)', 'bigspy-theme' ), 'description' => __( 'Example: contact, 42', 'bigspy-theme' ) ),
			array( 'id' => 'footer_1_columns', 'section' => 'bst_footer_widgets', 'type' => 'select', 'label' => __( 'Footer 1 columns', 'bigspy-theme' ), 'choices' => array( '0' => __( 'Disabled', 'bigspy-theme' ), '1' => '1', '2' => '2', '3' => '3', '4' => '4' ) ),
			array( 'id' => 'footer_1_bg', 'section' => 'bst_footer_widgets', 'type' => 'color', 'label' => __( 'Footer 1 background', 'bigspy-theme' ) ),
			array( 'id' => 'footer_1_color', 'section' => 'bst_footer_widgets', 'type' => 'color', 'label' => __( 'Footer 1 text', 'bigspy-theme' ) ),
			array( 'id' => 'footer_1_heading_color', 'section' => 'bst_footer_widgets', 'type' => 'color', 'label' => __( 'Footer 1 widget titles', 'bigspy-theme' ) ),
			array( 'id' => 'footer_2_columns', 'section' => 'bst_footer_widgets', 'type' => 'select', 'label' => __( 'Footer 2 columns', 'bigspy-theme' ), 'choices' => array( '0' => __( 'Disabled', 'bigspy-theme' ), '1' => '1', '2' => '2', '3' => '3', '4' => '4' ) ),
			array( 'id' => 'footer_2_bg', 'section' => 'bst_footer_widgets', 'type' => 'color', 'label' => __( 'Footer 2 background', 'bigspy-theme' ) ),
			array( 'id' => 'footer_2_color', 'section' => 'bst_footer_widgets', 'type' => 'color', 'label' => __( 'Footer 2 text', 'bigspy-theme' ) ),

			array( 'id' => 'footer_bottom_enable', 'section' => 'bst_footer_bottom', 'type' => 'checkbox', 'label' => __( 'Show footer bottom bar', 'bigspy-theme' ) ),
			array( 'id' => 'footer_bottom_layout', 'section' => 'bst_footer_bottom', 'type' => 'radio', 'label' => __( 'Alignment', 'bigspy-theme' ), 'choices' => array( 'split' => __( 'Left / right', 'bigspy-theme' ), 'center' => __( 'Centered', 'bigspy-theme' ) ) ),
			array( 'id' => 'footer_copyright', 'section' => 'bst_footer_bottom', 'type' => 'html', 'label' => __( 'Copyright text', 'bigspy-theme' ), 'description' => __( 'Tokens: [year], [site_title]', 'bigspy-theme' ) ),
			array( 'id' => 'footer_bottom_html', 'section' => 'bst_footer_bottom', 'type' => 'html', 'label' => __( 'Right side HTML (payment icons, badges…)', 'bigspy-theme' ) ),
			array( 'id' => 'footer_social', 'section' => 'bst_footer_bottom', 'type' => 'checkbox', 'label' => __( 'Show social icons', 'bigspy-theme' ) ),
			array( 'id' => 'footer_bottom_bg', 'section' => 'bst_footer_bottom', 'type' => 'color', 'label' => __( 'Background', 'bigspy-theme' ) ),
			array( 'id' => 'footer_bottom_color', 'section' => 'bst_footer_bottom', 'type' => 'color', 'label' => __( 'Text color', 'bigspy-theme' ) ),
			array( 'id' => 'back_to_top', 'section' => 'bst_footer_bottom', 'type' => 'checkbox', 'label' => __( 'Back-to-top button', 'bigspy-theme' ) ),

			array( 'id' => 'float_enable', 'section' => 'bst_float', 'type' => 'checkbox', 'label' => __( 'Enable floating contact buttons', 'bigspy-theme' ) ),
			array( 'id' => 'float_position', 'section' => 'bst_float', 'type' => 'radio', 'label' => __( 'Position', 'bigspy-theme' ), 'choices' => array( 'left' => __( 'Bottom left', 'bigspy-theme' ), 'right' => __( 'Bottom right', 'bigspy-theme' ) ) ),
			array( 'id' => 'float_mobile_bar', 'section' => 'bst_float', 'type' => 'checkbox', 'label' => __( 'Show as a bottom bar on mobile', 'bigspy-theme' ) ),
			array( 'id' => 'float_phone', 'section' => 'bst_float', 'type' => 'text', 'label' => __( 'Phone number', 'bigspy-theme' ) ),
			array( 'id' => 'float_zalo', 'section' => 'bst_float', 'type' => 'text', 'label' => __( 'Zalo (phone number or zalo.me link)', 'bigspy-theme' ) ),
			array( 'id' => 'float_messenger', 'section' => 'bst_float', 'type' => 'text', 'label' => __( 'Messenger (page username or m.me link)', 'bigspy-theme' ) ),
			array( 'id' => 'float_whatsapp', 'section' => 'bst_float', 'type' => 'text', 'label' => __( 'WhatsApp number (with country code)', 'bigspy-theme' ) ),
			array( 'id' => 'float_email', 'section' => 'bst_float', 'type' => 'text', 'label' => __( 'Email', 'bigspy-theme' ) ),

			// ---------------------------------------------------------------- Advanced.
			array( 'id' => 'scripts_head', 'section' => 'bst_scripts', 'type' => 'code', 'label' => __( 'Scripts in <head>', 'bigspy-theme' ) ),
			array( 'id' => 'scripts_body', 'section' => 'bst_scripts', 'type' => 'code', 'label' => __( 'Scripts after <body>', 'bigspy-theme' ) ),
			array( 'id' => 'scripts_footer', 'section' => 'bst_scripts', 'type' => 'code', 'label' => __( 'Scripts before </body>', 'bigspy-theme' ) ),
			array( 'id' => 'disable_emoji', 'section' => 'bst_performance', 'type' => 'checkbox', 'label' => __( 'Disable WordPress emoji script', 'bigspy-theme' ) ),
			array( 'id' => 'animations', 'section' => 'bst_performance', 'type' => 'checkbox', 'label' => __( 'Enable scroll animations on blocks', 'bigspy-theme' ) ),
		)
	);

	if ( class_exists( 'WooCommerce' ) ) {
		$cols = array( '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6' );
		$fields = array_merge(
			$fields,
			array(
				array( 'id' => 'shop_columns', 'section' => 'bst_shop', 'type' => 'select', 'label' => __( 'Products per row – desktop', 'bigspy-theme' ), 'choices' => $cols ),
				array( 'id' => 'shop_columns_tablet', 'section' => 'bst_shop', 'type' => 'select', 'label' => __( 'Products per row – tablet', 'bigspy-theme' ), 'choices' => array_slice( $cols, 0, 4, true ) ),
				array( 'id' => 'shop_columns_mobile', 'section' => 'bst_shop', 'type' => 'select', 'label' => __( 'Products per row – mobile', 'bigspy-theme' ), 'choices' => array_slice( $cols, 0, 2, true ) ),
				array( 'id' => 'shop_per_page', 'section' => 'bst_shop', 'type' => 'number', 'label' => __( 'Products per page', 'bigspy-theme' ), 'input_attrs' => $px( 1, 100 ) ),
				array( 'id' => 'shop_sidebar', 'section' => 'bst_shop', 'type' => 'select', 'label' => __( 'Shop sidebar', 'bigspy-theme' ), 'choices' => $sidebar ),
				array( 'id' => 'cart_icon', 'section' => 'bst_shop', 'type' => 'radio', 'label' => __( 'Header cart icon', 'bigspy-theme' ), 'choices' => array( 'bag' => __( 'Bag', 'bigspy-theme' ), 'cart' => __( 'Cart', 'bigspy-theme' ) ) ),
				array( 'id' => 'mini_cart', 'section' => 'bst_shop', 'type' => 'checkbox', 'label' => __( 'Mini cart dropdown on hover (desktop)', 'bigspy-theme' ) ),

				array( 'id' => 'product_card_style', 'section' => 'bst_product', 'type' => 'select', 'label' => __( 'Product card style', 'bigspy-theme' ), 'choices' => array( 'bordered' => __( 'Bordered', 'bigspy-theme' ), 'shadow' => __( 'Shadow on hover', 'bigspy-theme' ), 'plain' => __( 'Plain', 'bigspy-theme' ) ) ),
				array( 'id' => 'product_hover', 'section' => 'bst_product', 'type' => 'select', 'label' => __( 'Image hover effect', 'bigspy-theme' ), 'choices' => array( 'swap' => __( 'Show second gallery image', 'bigspy-theme' ), 'zoom' => __( 'Zoom', 'bigspy-theme' ), 'none' => __( 'None', 'bigspy-theme' ) ) ),
				array( 'id' => 'product_title_lines', 'section' => 'bst_product', 'type' => 'select', 'label' => __( 'Product title max lines', 'bigspy-theme' ), 'choices' => array( '1' => '1', '2' => '2', '3' => '3', '99' => __( 'Unlimited', 'bigspy-theme' ) ) ),
				array( 'id' => 'product_image_ratio', 'section' => 'bst_product', 'type' => 'select', 'label' => __( 'Product image ratio', 'bigspy-theme' ), 'choices' => array( '1-1' => __( 'Square 1:1', 'bigspy-theme' ), '3-4' => __( 'Portrait 3:4 (fashion)', 'bigspy-theme' ), '4-3' => __( 'Landscape 4:3', 'bigspy-theme' ), 'auto' => __( 'Original', 'bigspy-theme' ) ) ),
				array( 'id' => 'product_button', 'section' => 'bst_product', 'type' => 'select', 'label' => __( 'Add to cart button', 'bigspy-theme' ), 'choices' => array( 'always' => __( 'Always visible', 'bigspy-theme' ), 'hover' => __( 'Show on hover (desktop)', 'bigspy-theme' ), 'icon' => __( 'Round icon on the image', 'bigspy-theme' ), 'none' => __( 'Hidden', 'bigspy-theme' ) ) ),
				array( 'id' => 'product_show_category', 'section' => 'bst_product', 'type' => 'checkbox', 'label' => __( 'Show category above the title', 'bigspy-theme' ) ),
				array( 'id' => 'sale_badge_style', 'section' => 'bst_product', 'type' => 'select', 'label' => __( 'Badge shape', 'bigspy-theme' ), 'choices' => array( 'pill' => __( 'Pill', 'bigspy-theme' ), 'square' => __( 'Square', 'bigspy-theme' ), 'circle' => __( 'Circle', 'bigspy-theme' ) ) ),
				array( 'id' => 'show_sale_percent', 'section' => 'bst_product', 'type' => 'checkbox', 'label' => __( 'Show discount percentage on sale badge', 'bigspy-theme' ) ),
				array( 'id' => 'new_badge_days', 'section' => 'bst_product', 'type' => 'number', 'label' => __( '“New” badge for products added in the last N days (0 = off)', 'bigspy-theme' ), 'input_attrs' => $px( 0, 365 ) ),
				array( 'id' => 'featured_badge', 'section' => 'bst_product', 'type' => 'checkbox', 'label' => __( '“Hot” badge on featured products', 'bigspy-theme' ) ),
				array( 'id' => 'product_sidebar', 'section' => 'bst_product', 'type' => 'select', 'label' => __( 'Product page sidebar', 'bigspy-theme' ), 'choices' => $sidebar ),
				array( 'id' => 'product_gallery_layout', 'section' => 'bst_product', 'type' => 'select', 'label' => __( 'Product gallery layout', 'bigspy-theme' ), 'choices' => array( 'default' => __( 'Default', 'bigspy-theme' ), 'stacked' => __( 'Stacked images', 'bigspy-theme' ), 'thumbnails-left' => __( 'Thumbnails on the left', 'bigspy-theme' ) ) ),
				array( 'id' => 'product_sticky_summary', 'section' => 'bst_product', 'type' => 'checkbox', 'label' => __( 'Keep purchase details visible while scrolling (desktop)', 'bigspy-theme' ) ),
				array( 'id' => 'product_mobile_bar', 'section' => 'bst_product', 'type' => 'checkbox', 'label' => __( 'Show a quick purchase bar on mobile', 'bigspy-theme' ) ),
				array( 'id' => 'related_columns', 'section' => 'bst_product', 'type' => 'select', 'label' => __( 'Related products per row', 'bigspy-theme' ), 'choices' => array_slice( $cols, 1, 4, true ) ),

				array( 'id' => 'catalog_mode', 'section' => 'bst_catalog', 'type' => 'checkbox', 'label' => __( 'Enable catalog mode', 'bigspy-theme' ) ),
				array( 'id' => 'catalog_mode_text', 'section' => 'bst_catalog', 'type' => 'text', 'label' => __( 'Button text', 'bigspy-theme' ) ),
				array( 'id' => 'catalog_mode_url', 'section' => 'bst_catalog', 'type' => 'text', 'label' => __( 'Button link (empty = phone number from Contact info)', 'bigspy-theme' ) ),
			)
		);
	}

	return apply_filters( 'bst_customizer_fields', $fields );
}
