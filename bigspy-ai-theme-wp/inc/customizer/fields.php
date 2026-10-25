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
		'bst_header_builder' => array( 'title' => __( 'Header builder', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_header', 'priority' => 1, 'description' => __( 'Start with a ready-made layout or drag elements between rows. Click the gear on an element to edit it. Preview desktop and mobile separately, then press Publish to save.', 'bigspy-ai-theme-wp' ) ),
		'bst_header_general' => array( 'title' => __( 'Header layout & sticky', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_header', 'priority' => 2 ),
		'bst_header_rows'    => array( 'title' => __( 'Rows: height & colors', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_header', 'priority' => 3 ),
		'bst_header_logo'    => array( 'title' => __( 'Logo', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_header', 'priority' => 4, 'description' => __( 'Upload the logo in Site Identity.', 'bigspy-ai-theme-wp' ) ),
		'bst_header_nav'     => array( 'title' => __( 'Navigation', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_header', 'priority' => 5 ),
		'bst_header_search'  => array( 'title' => __( 'Search', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_header', 'priority' => 6 ),
		'bst_header_buttons' => array( 'title' => __( 'Buttons', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_header', 'priority' => 7 ),
		'bst_header_html'    => array( 'title' => __( 'HTML blocks', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_header', 'priority' => 8, 'description' => __( 'HTML and shortcodes are allowed. Tokens: [year], [site_title], [phone], [email].', 'bigspy-ai-theme-wp' ) ),
		'bst_contact'        => array( 'title' => __( 'Contact info & social', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_header', 'priority' => 9, 'description' => __( 'Used by the header, the mobile menu and the footer.', 'bigspy-ai-theme-wp' ) ),

		// Style.
		'bst_colors'         => array( 'title' => __( 'Colors', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_style', 'priority' => 1, 'description' => __( 'These colors also appear in the block editor color palette.', 'bigspy-ai-theme-wp' ) ),
		'bst_typography'     => array( 'title' => __( 'Typography', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_style', 'priority' => 2 ),
		'bst_buttons'        => array( 'title' => __( 'Buttons', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_style', 'priority' => 3 ),

		// Layout.
		'bst_layout_site'    => array( 'title' => __( 'Site layout', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_layout', 'priority' => 1 ),
		'bst_title_bar'      => array( 'title' => __( 'Page title & breadcrumbs', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_layout', 'priority' => 2 ),

		// Blog.
		'bst_blog_archive'   => array( 'title' => __( 'Blog archive', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_blog', 'priority' => 1 ),
		'bst_blog_single'    => array( 'title' => __( 'Single post', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_blog', 'priority' => 2 ),

		// Footer.
		'bst_footer_cta'     => array( 'title' => __( 'Call-to-action band', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_footer', 'priority' => 0, 'description' => __( 'A colored band shown above the footer on every page, e.g. “Ready to start? → Contact us”.', 'bigspy-ai-theme-wp' ) ),
		'bst_footer_widgets' => array( 'title' => __( 'Footer widgets', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_footer', 'priority' => 1, 'description' => __( 'Choose a column layout here, then add content to each column in Widgets. Your existing widget content is kept when changing a layout.', 'bigspy-ai-theme-wp' ) ),
		'bst_footer_bottom'  => array( 'title' => __( 'Footer bottom (copyright)', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_footer', 'priority' => 2 ),
		'bst_float'          => array( 'title' => __( 'Floating contact buttons', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_footer', 'priority' => 3 ),

		// Advanced.
		'bst_scripts'        => array( 'title' => __( 'Custom scripts', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_advanced', 'priority' => 1, 'description' => __( 'Google Analytics, Facebook Pixel, chat widgets… Custom CSS goes in “Additional CSS”.', 'bigspy-ai-theme-wp' ) ),
		'bst_performance'    => array( 'title' => __( 'Performance & effects', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_advanced', 'priority' => 2 ),
	);

	if ( class_exists( 'WooCommerce' ) ) {
		$sections['bst_shop']    = array( 'title' => __( 'BIGSPY: shop layout', 'bigspy-ai-theme-wp' ), 'panel' => 'woocommerce', 'priority' => 1 );
		$sections['bst_product'] = array( 'title' => __( 'BIGSPY: product cards & page', 'bigspy-ai-theme-wp' ), 'panel' => 'woocommerce', 'priority' => 2 );
		$sections['bst_catalog'] = array( 'title' => __( 'BIGSPY: catalog mode', 'bigspy-ai-theme-wp' ), 'panel' => 'woocommerce', 'priority' => 3, 'description' => __( 'Hide prices and add-to-cart buttons, show a contact button instead. Useful for showroom / quotation websites.', 'bigspy-ai-theme-wp' ) );
	}

	return apply_filters( 'bst_customizer_sections', $sections );
}

function bst_customizer_fields() {
	$px = function ( $min, $max, $step = 1 ) {
		return array( 'min' => $min, 'max' => $max, 'step' => $step );
	};
	$sidebar = array(
		'none'  => __( 'No sidebar', 'bigspy-ai-theme-wp' ),
		'left'  => __( 'Left sidebar', 'bigspy-ai-theme-wp' ),
		'right' => __( 'Right sidebar', 'bigspy-ai-theme-wp' ),
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
		'none'      => __( 'Normal', 'bigspy-ai-theme-wp' ),
		'uppercase' => __( 'UPPERCASE', 'bigspy-ai-theme-wp' ),
		'capitalize'=> __( 'Capitalize', 'bigspy-ai-theme-wp' ),
	);
	$button_styles = array(
		'primary'   => __( 'Primary', 'bigspy-ai-theme-wp' ),
		'secondary' => __( 'Secondary', 'bigspy-ai-theme-wp' ),
		'outline'   => __( 'Outline', 'bigspy-ai-theme-wp' ),
		'white'     => __( 'White', 'bigspy-ai-theme-wp' ),
		'link'      => __( 'Plain link', 'bigspy-ai-theme-wp' ),
	);
	$fonts = bst_font_choices();

	$fields = array(
		// ---------------------------------------------------------------- Header builder.
		array(
			'id' => 'header_layout_presets', 'section' => 'bst_header_builder', 'type' => 'layout_presets', 'priority' => 1,
			'label' => __( 'Start with a header layout', 'bigspy-ai-theme-wp' ),
			'description' => __( 'Choose a starting layout. It changes only the arrangement of header items; logo, menus, colors and button text stay yours. You can still drag every item below.', 'bigspy-ai-theme-wp' ),
			'help_url' => admin_url( 'customize.php?autofocus[section]=title_tagline' ), 'help_label' => __( 'Set logo and site title', 'bigspy-ai-theme-wp' ),
			'help_url_2' => admin_url( 'nav-menus.php' ), 'help_label_2' => __( 'Create and assign menus', 'bigspy-ai-theme-wp' ),
			'presets' => array(
				'shop' => array( 'label' => __( 'Online store', 'bigspy-ai-theme-wp' ), 'description' => __( 'Logo, search, account, cart and navigation.', 'bigspy-ai-theme-wp' ), 'settings' => array( 'header_layout' => bst_header_layout_preset( 'shop' ) ) ),
				'service' => array( 'label' => __( 'Services', 'bigspy-ai-theme-wp' ), 'description' => __( 'Contact details, social links and clear navigation.', 'bigspy-ai-theme-wp' ), 'settings' => array( 'header_layout' => bst_header_layout_preset( 'service' ) ) ),
				'minimal' => array( 'label' => __( 'Minimal', 'bigspy-ai-theme-wp' ), 'description' => __( 'Logo, menu and search with fewer rows.', 'bigspy-ai-theme-wp' ), 'settings' => array( 'header_layout' => bst_header_layout_preset( 'minimal' ) ) ),
			),
		),
		array( 'id' => 'header_layout', 'section' => 'bst_header_builder', 'type' => 'header_builder', 'label' => __( 'Header layout', 'bigspy-ai-theme-wp' ) ),

		// Header general.
		array( 'id' => 'header_full_width', 'section' => 'bst_header_general', 'type' => 'checkbox', 'label' => __( 'Full-width header', 'bigspy-ai-theme-wp' ), 'description' => __( 'Logo, menu and the text of full-width banners / sections line up on the same side spacing.', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'header_round_bottom', 'section' => 'bst_header_general', 'type' => 'checkbox', 'label' => __( 'Round the two bottom corners of the header', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'header_round_size', 'section' => 'bst_header_general', 'type' => 'number', 'label' => __( 'Bottom corner radius (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 2, 'max' => 60 ) ),
		array( 'id' => 'fluid_gutter', 'section' => 'bst_header_general', 'type' => 'number', 'label' => __( 'Side spacing for full-width header & blocks (px, 0 = auto)', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 0, 'max' => 200 ) ),
		array( 'id' => 'header_shadow', 'section' => 'bst_header_general', 'type' => 'checkbox', 'label' => __( 'Shadow under header', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'header_sticky', 'section' => 'bst_header_general', 'type' => 'checkbox', 'label' => __( 'Sticky header', 'bigspy-ai-theme-wp' ), 'description' => __( 'The main and bottom rows stay on screen while scrolling.', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'header_sticky_height', 'section' => 'bst_header_general', 'type' => 'number', 'label' => __( 'Sticky main row height (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $px( 40, 140 ) ),
		array( 'id' => 'bst_h_transparent', 'section' => 'bst_header_general', 'type' => 'heading', 'label' => __( 'Transparent header', 'bigspy-ai-theme-wp' ), 'description' => __( 'Can also be enabled per page in the “BIGSPY – Page layout” box.', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'header_transparent_home', 'section' => 'bst_header_general', 'type' => 'checkbox', 'label' => __( 'Transparent header on the homepage', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'header_transparent_color', 'section' => 'bst_header_general', 'type' => 'color', 'label' => __( 'Text color on transparent header', 'bigspy-ai-theme-wp' ) ),

		// Rows.
		array( 'id' => 'bst_h_top', 'section' => 'bst_header_rows', 'type' => 'heading', 'label' => __( 'Top bar', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'header_top_height', 'section' => 'bst_header_rows', 'type' => 'number', 'label' => __( 'Height (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $px( 24, 80 ) ),
		array( 'id' => 'header_top_bg', 'section' => 'bst_header_rows', 'type' => 'color', 'label' => __( 'Background', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'header_top_color', 'section' => 'bst_header_rows', 'type' => 'color', 'label' => __( 'Text color', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'bst_h_main', 'section' => 'bst_header_rows', 'type' => 'heading', 'label' => __( 'Main header', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'header_main_height', 'section' => 'bst_header_rows', 'type' => 'number', 'label' => __( 'Height (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $px( 40, 200 ) ),
		array( 'id' => 'header_main_bg', 'section' => 'bst_header_rows', 'type' => 'color', 'label' => __( 'Background', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'header_main_color', 'section' => 'bst_header_rows', 'type' => 'color', 'label' => __( 'Text color', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'bst_h_bottom', 'section' => 'bst_header_rows', 'type' => 'heading', 'label' => __( 'Bottom header', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'header_bottom_height', 'section' => 'bst_header_rows', 'type' => 'number', 'label' => __( 'Height (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $px( 30, 120 ) ),
		array( 'id' => 'header_bottom_bg', 'section' => 'bst_header_rows', 'type' => 'color', 'label' => __( 'Background', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'header_bottom_color', 'section' => 'bst_header_rows', 'type' => 'color', 'label' => __( 'Text color', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'bst_h_mobile', 'section' => 'bst_header_rows', 'type' => 'heading', 'label' => __( 'Mobile', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'header_mobile_height', 'section' => 'bst_header_rows', 'type' => 'number', 'label' => __( 'Mobile main row height (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $px( 40, 120 ) ),

		// Logo.
		array( 'id' => 'logo_width', 'section' => 'bst_header_logo', 'type' => 'number', 'label' => __( 'Logo max width – desktop (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $px( 40, 600 ) ),
		array( 'id' => 'logo_width_mobile', 'section' => 'bst_header_logo', 'type' => 'number', 'label' => __( 'Logo max width – mobile (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $px( 40, 400 ) ),

		// Navigation.
		array( 'id' => 'nav_style', 'section' => 'bst_header_nav', 'type' => 'select', 'label' => __( 'Main menu style', 'bigspy-ai-theme-wp' ), 'choices' => bst_nav_styles() ),
		array( 'id' => 'font_nav', 'section' => 'bst_header_nav', 'type' => 'select', 'label' => __( 'Menu font', 'bigspy-ai-theme-wp' ), 'choices' => $fonts ),
		array( 'id' => 'nav_size', 'section' => 'bst_header_nav', 'type' => 'number', 'label' => __( 'Menu font size (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $px( 11, 24 ) ),
		array( 'id' => 'nav_weight', 'section' => 'bst_header_nav', 'type' => 'select', 'label' => __( 'Menu font weight', 'bigspy-ai-theme-wp' ), 'choices' => $weights ),
		array( 'id' => 'nav_transform', 'section' => 'bst_header_nav', 'type' => 'select', 'label' => __( 'Menu text case', 'bigspy-ai-theme-wp' ), 'choices' => $transform ),
		array( 'id' => 'offcanvas_side', 'section' => 'bst_header_nav', 'type' => 'radio', 'label' => __( 'Off-canvas (mobile) menu opens from', 'bigspy-ai-theme-wp' ), 'choices' => array( 'left' => __( 'Left', 'bigspy-ai-theme-wp' ), 'right' => __( 'Right', 'bigspy-ai-theme-wp' ) ) ),

		// Search.
		array( 'id' => 'search_style', 'section' => 'bst_header_search', 'type' => 'radio', 'label' => __( 'Search icon opens', 'bigspy-ai-theme-wp' ), 'choices' => array( 'dropdown' => __( 'Dropdown under header', 'bigspy-ai-theme-wp' ), 'lightbox' => __( 'Full-screen lightbox', 'bigspy-ai-theme-wp' ) ) ),
		array( 'id' => 'search_products', 'section' => 'bst_header_search', 'type' => 'checkbox', 'label' => __( 'Force products-only search (overrides scope)', 'bigspy-ai-theme-wp' ), 'description' => __( 'For older settings and sites that always want product results.', 'bigspy-ai-theme-wp' ) ),

		// Buttons.
		array( 'id' => 'bst_h_btn1', 'section' => 'bst_header_buttons', 'type' => 'heading', 'label' => __( 'Button 1', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'header_button_1_text', 'section' => 'bst_header_buttons', 'type' => 'text', 'label' => __( 'Text', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'header_button_1_url', 'section' => 'bst_header_buttons', 'type' => 'text', 'label' => __( 'Link', 'bigspy-ai-theme-wp' ), 'description' => __( 'URL, tel:+15551234567 or mailto:…', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'header_button_1_style', 'section' => 'bst_header_buttons', 'type' => 'select', 'label' => __( 'Style', 'bigspy-ai-theme-wp' ), 'choices' => $button_styles ),
		array( 'id' => 'header_button_1_new_tab', 'section' => 'bst_header_buttons', 'type' => 'checkbox', 'label' => __( 'Open in new tab', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'bst_h_btn2', 'section' => 'bst_header_buttons', 'type' => 'heading', 'label' => __( 'Button 2', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'header_button_2_text', 'section' => 'bst_header_buttons', 'type' => 'text', 'label' => __( 'Text', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'header_button_2_url', 'section' => 'bst_header_buttons', 'type' => 'text', 'label' => __( 'Link', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'header_button_2_style', 'section' => 'bst_header_buttons', 'type' => 'select', 'label' => __( 'Style', 'bigspy-ai-theme-wp' ), 'choices' => $button_styles ),
		array( 'id' => 'header_button_2_new_tab', 'section' => 'bst_header_buttons', 'type' => 'checkbox', 'label' => __( 'Open in new tab', 'bigspy-ai-theme-wp' ) ),

		// HTML.
		array( 'id' => 'header_html_1', 'section' => 'bst_header_html', 'type' => 'html', 'label' => __( 'HTML 1', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'header_html_2', 'section' => 'bst_header_html', 'type' => 'html', 'label' => __( 'HTML 2', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'header_html_3', 'section' => 'bst_header_html', 'type' => 'html', 'label' => __( 'HTML 3', 'bigspy-ai-theme-wp' ) ),

		// Contact & social.
		array( 'id' => 'contact_phone', 'section' => 'bst_contact', 'type' => 'text', 'label' => __( 'Phone / hotline', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'contact_email', 'section' => 'bst_contact', 'type' => 'text', 'label' => __( 'Email', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'contact_address', 'section' => 'bst_contact', 'type' => 'text', 'label' => __( 'Address', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'contact_hours', 'section' => 'bst_contact', 'type' => 'text', 'label' => __( 'Opening hours', 'bigspy-ai-theme-wp' ) ),
		array( 'id' => 'bst_h_social', 'section' => 'bst_contact', 'type' => 'heading', 'label' => __( 'Social profiles (full URLs)', 'bigspy-ai-theme-wp' ), 'description' => __( 'For Zalo you can enter just the phone number.', 'bigspy-ai-theme-wp' ) ),
	);

	foreach ( bst_social_networks() as $key => $label ) {
		$fields[] = array( 'id' => 'social_' . $key, 'section' => 'bst_contact', 'type' => 'text', 'label' => $label );
	}

	$fields = array_merge(
		$fields,
		array(
			// ---------------------------------------------------------------- Colors.
			array( 'id' => 'color_primary', 'section' => 'bst_colors', 'type' => 'color', 'label' => __( 'Primary', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'color_secondary', 'section' => 'bst_colors', 'type' => 'color', 'label' => __( 'Secondary', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'color_success', 'section' => 'bst_colors', 'type' => 'color', 'label' => __( 'Success', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'color_alert', 'section' => 'bst_colors', 'type' => 'color', 'label' => __( 'Alert / sale', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'color_text', 'section' => 'bst_colors', 'type' => 'color', 'label' => __( 'Body text', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'color_heading', 'section' => 'bst_colors', 'type' => 'color', 'label' => __( 'Headings', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'color_link', 'section' => 'bst_colors', 'type' => 'color', 'label' => __( 'Links', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'color_link_hover', 'section' => 'bst_colors', 'type' => 'color', 'label' => __( 'Links on hover', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'color_border', 'section' => 'bst_colors', 'type' => 'color', 'label' => __( 'Borders', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'color_light', 'section' => 'bst_colors', 'type' => 'color', 'label' => __( 'Light background (cards, boxes)', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'color_body_bg', 'section' => 'bst_colors', 'type' => 'color', 'label' => __( 'Page background', 'bigspy-ai-theme-wp' ) ),

			// Typography.
			array( 'id' => 'font_body', 'section' => 'bst_typography', 'type' => 'select', 'label' => __( 'Body font', 'bigspy-ai-theme-wp' ), 'choices' => $fonts, 'description' => __( 'All Google fonts in this list support Vietnamese.', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'font_weight_body', 'section' => 'bst_typography', 'type' => 'select', 'label' => __( 'Body font weight', 'bigspy-ai-theme-wp' ), 'choices' => $weights ),
			array( 'id' => 'font_size_base', 'section' => 'bst_typography', 'type' => 'number', 'label' => __( 'Body font size – desktop (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $px( 12, 22 ) ),
			array( 'id' => 'font_size_mobile', 'section' => 'bst_typography', 'type' => 'number', 'label' => __( 'Body font size – mobile (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $px( 12, 20 ) ),
			array( 'id' => 'line_height', 'section' => 'bst_typography', 'type' => 'number', 'label' => __( 'Line height', 'bigspy-ai-theme-wp' ), 'input_attrs' => $px( 1, 2.4, 0.05 ) ),
			array( 'id' => 'font_heading', 'section' => 'bst_typography', 'type' => 'select', 'label' => __( 'Heading font', 'bigspy-ai-theme-wp' ), 'choices' => $fonts ),
			array( 'id' => 'font_weight_heading', 'section' => 'bst_typography', 'type' => 'select', 'label' => __( 'Heading font weight', 'bigspy-ai-theme-wp' ), 'choices' => $weights ),
			array( 'id' => 'heading_transform', 'section' => 'bst_typography', 'type' => 'select', 'label' => __( 'Heading text case', 'bigspy-ai-theme-wp' ), 'choices' => $transform ),
			array( 'id' => 'h1_size', 'section' => 'bst_typography', 'type' => 'number', 'label' => __( 'H1 size (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $px( 20, 90 ) ),
			array( 'id' => 'h2_size', 'section' => 'bst_typography', 'type' => 'number', 'label' => __( 'H2 size (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $px( 18, 72 ) ),
			array( 'id' => 'h3_size', 'section' => 'bst_typography', 'type' => 'number', 'label' => __( 'H3 size (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $px( 16, 56 ) ),
			array( 'id' => 'h4_size', 'section' => 'bst_typography', 'type' => 'number', 'label' => __( 'H4 size (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $px( 14, 40 ) ),

			// Buttons.
			array( 'id' => 'button_radius', 'section' => 'bst_buttons', 'type' => 'number', 'label' => __( 'Corner radius (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $px( 0, 60 ) ),
			array( 'id' => 'button_weight', 'section' => 'bst_buttons', 'type' => 'select', 'label' => __( 'Font weight', 'bigspy-ai-theme-wp' ), 'choices' => $weights ),
			array( 'id' => 'button_transform', 'section' => 'bst_buttons', 'type' => 'select', 'label' => __( 'Text case', 'bigspy-ai-theme-wp' ), 'choices' => $transform ),

			// ---------------------------------------------------------------- Layout.
			array( 'id' => 'site_layout', 'section' => 'bst_layout_site', 'type' => 'radio', 'label' => __( 'Site layout', 'bigspy-ai-theme-wp' ), 'choices' => array( 'full' => __( 'Full width', 'bigspy-ai-theme-wp' ), 'boxed' => __( 'Boxed', 'bigspy-ai-theme-wp' ) ) ),
			array( 'id' => 'boxed_bg', 'section' => 'bst_layout_site', 'type' => 'color', 'label' => __( 'Outside background (boxed layout)', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'fullwidth_text_edge', 'section' => 'bst_layout_site', 'type' => 'select', 'label' => __( 'Text in full-width banners, sliders and sections lines up with', 'bigspy-ai-theme-wp' ), 'choices' => array( 'header' => __( 'The header (logo) edge', 'bigspy-ai-theme-wp' ), 'container' => __( 'The container width (page content)', 'bigspy-ai-theme-wp' ) ), 'description' => __( 'Desktop and tablet. With a boxed header both are the same.', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'container_width', 'section' => 'bst_layout_site', 'type' => 'number', 'label' => __( 'Container width (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $px( 960, 1920, 10 ) ),
			array( 'id' => 'sidebar_width', 'section' => 'bst_layout_site', 'type' => 'number', 'label' => __( 'Sidebar width (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $px( 200, 480, 10 ) ),
			array( 'id' => 'page_sidebar', 'section' => 'bst_layout_site', 'type' => 'select', 'label' => __( 'Default sidebar on pages', 'bigspy-ai-theme-wp' ), 'choices' => $sidebar ),

			array( 'id' => 'title_bar_style', 'section' => 'bst_title_bar', 'type' => 'radio', 'label' => __( 'Page title style', 'bigspy-ai-theme-wp' ), 'choices' => array( 'simple' => __( 'Simple (inside content)', 'bigspy-ai-theme-wp' ), 'bar' => __( 'Full-width title bar', 'bigspy-ai-theme-wp' ), 'none' => __( 'Hidden', 'bigspy-ai-theme-wp' ) ) ),
			array( 'id' => 'page_title_h1', 'section' => 'bst_title_bar', 'type' => 'checkbox', 'label' => __( 'Show the page name as H1 on pages', 'bigspy-ai-theme-wp' ), 'description' => __( 'Off: pages print no automatic title, so you set the H1 yourself with a Heading block. Posts, archives and shop still show their title.', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'title_bar_bg', 'section' => 'bst_title_bar', 'type' => 'color', 'label' => __( 'Title bar background', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'title_bar_color', 'section' => 'bst_title_bar', 'type' => 'color', 'label' => __( 'Title bar text color', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'title_bar_image', 'section' => 'bst_title_bar', 'type' => 'image', 'label' => __( 'Title bar background image', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'breadcrumbs', 'section' => 'bst_title_bar', 'type' => 'checkbox', 'label' => __( 'Show breadcrumbs', 'bigspy-ai-theme-wp' ), 'description' => __( 'Uses Rank Math / Yoast breadcrumbs when those plugins are active.', 'bigspy-ai-theme-wp' ) ),

			// ---------------------------------------------------------------- Blog.
			array( 'id' => 'blog_layout', 'section' => 'bst_blog_archive', 'type' => 'radio', 'label' => __( 'Layout', 'bigspy-ai-theme-wp' ), 'choices' => array( 'grid' => __( 'Grid', 'bigspy-ai-theme-wp' ), 'list' => __( 'List (image left)', 'bigspy-ai-theme-wp' ), 'zigzag' => __( 'Zigzag list (image left / right)', 'bigspy-ai-theme-wp' ), 'masonry' => __( 'Masonry', 'bigspy-ai-theme-wp' ), 'featured' => __( 'First post large, then grid', 'bigspy-ai-theme-wp' ), 'classic' => __( 'Classic (one large post per row)', 'bigspy-ai-theme-wp' ), 'overlay' => __( 'Text over image', 'bigspy-ai-theme-wp' ), 'minimal' => __( 'Minimal (titles only)', 'bigspy-ai-theme-wp' ) ) ),
			array( 'id' => 'blog_columns', 'section' => 'bst_blog_archive', 'type' => 'select', 'label' => __( 'Grid columns', 'bigspy-ai-theme-wp' ), 'choices' => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4' ) ),
			array( 'id' => 'blog_sidebar', 'section' => 'bst_blog_archive', 'type' => 'select', 'label' => __( 'Sidebar', 'bigspy-ai-theme-wp' ), 'choices' => $sidebar ),
			array( 'id' => 'blog_card_style', 'section' => 'bst_blog_archive', 'type' => 'select', 'label' => __( 'Card style', 'bigspy-ai-theme-wp' ), 'choices' => array( 'shadow' => __( 'Shadow', 'bigspy-ai-theme-wp' ), 'bordered' => __( 'Bordered', 'bigspy-ai-theme-wp' ), 'plain' => __( 'Plain', 'bigspy-ai-theme-wp' ), 'overlay' => __( 'Text over image', 'bigspy-ai-theme-wp' ) ) ),
			array( 'id' => 'blog_image_ratio', 'section' => 'bst_blog_archive', 'type' => 'select', 'label' => __( 'Image ratio', 'bigspy-ai-theme-wp' ), 'choices' => array( '16-9' => '16:9', '4-3' => '4:3', '3-2' => '3:2', '1-1' => '1:1', 'auto' => __( 'Original', 'bigspy-ai-theme-wp' ) ) ),
			array( 'id' => 'blog_show_meta', 'section' => 'bst_blog_archive', 'type' => 'checkbox', 'label' => __( 'Show meta (choose items below)', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'blog_show_excerpt', 'section' => 'bst_blog_archive', 'type' => 'checkbox', 'label' => __( 'Show excerpt', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'blog_excerpt_length', 'section' => 'bst_blog_archive', 'type' => 'number', 'label' => __( 'Excerpt length (words)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $px( 5, 80 ) ),
			array( 'id' => 'blog_read_more', 'section' => 'bst_blog_archive', 'type' => 'text', 'label' => __( '“Read more” text (empty to hide)', 'bigspy-ai-theme-wp' ) ),

			array( 'id' => 'single_sidebar', 'section' => 'bst_blog_single', 'type' => 'select', 'label' => __( 'Sidebar', 'bigspy-ai-theme-wp' ), 'choices' => $sidebar ),
			array( 'id' => 'single_featured_image', 'section' => 'bst_blog_single', 'type' => 'checkbox', 'label' => __( 'Show featured image', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'single_share', 'section' => 'bst_blog_single', 'type' => 'checkbox', 'label' => __( 'Share buttons', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'single_author_box', 'section' => 'bst_blog_single', 'type' => 'checkbox', 'label' => __( 'Author box (when the author has a bio)', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'single_nav', 'section' => 'bst_blog_single', 'type' => 'checkbox', 'label' => __( 'Previous / next post links', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'single_related', 'section' => 'bst_blog_single', 'type' => 'checkbox', 'label' => __( 'Related posts', 'bigspy-ai-theme-wp' ) ),

			// ---------------------------------------------------------------- Footer.
			array(
				'id' => 'footer_layout_presets', 'section' => 'bst_footer_widgets', 'type' => 'layout_presets', 'priority' => 1,
				'label' => __( 'Choose a footer layout', 'bigspy-ai-theme-wp' ),
				'description' => __( 'A preset changes column counts and display toggles only. It does not delete or rewrite your widgets, text, links or colors.', 'bigspy-ai-theme-wp' ),
				'help_url' => admin_url( 'widgets.php' ), 'help_label' => __( 'Add or edit footer content in Widgets', 'bigspy-ai-theme-wp' ),
				'presets' => array(
					'store' => array( 'label' => __( 'Store', 'bigspy-ai-theme-wp' ), 'description' => __( 'Four columns for help, policies, contact and payments.', 'bigspy-ai-theme-wp' ), 'settings' => array( 'footer_1_columns' => 4, 'footer_2_columns' => 0, 'footer_cta_enable' => false, 'footer_bottom_enable' => true, 'footer_social' => true ) ),
				'service' => array( 'label' => __( 'Services', 'bigspy-ai-theme-wp' ), 'description' => __( 'Three content columns; keeps your current contact-band setting.', 'bigspy-ai-theme-wp' ), 'settings' => array( 'footer_1_columns' => 3, 'footer_2_columns' => 0, 'footer_bottom_enable' => true, 'footer_social' => true ) ),
					'minimal' => array( 'label' => __( 'Minimal', 'bigspy-ai-theme-wp' ), 'description' => __( 'Two columns and a simple copyright bar.', 'bigspy-ai-theme-wp' ), 'settings' => array( 'footer_1_columns' => 2, 'footer_2_columns' => 0, 'footer_cta_enable' => false, 'footer_bottom_enable' => true, 'footer_social' => false ) ),
				),
			),
			array( 'id' => 'footer_cta_enable', 'section' => 'bst_footer_cta', 'type' => 'checkbox', 'label' => __( 'Show call-to-action band', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'footer_cta_title', 'section' => 'bst_footer_cta', 'type' => 'text', 'label' => __( 'Title', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'footer_cta_text', 'section' => 'bst_footer_cta', 'type' => 'html', 'label' => __( 'Text', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'footer_cta_button', 'section' => 'bst_footer_cta', 'type' => 'text', 'label' => __( 'Button text', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'footer_cta_url', 'section' => 'bst_footer_cta', 'type' => 'text', 'label' => __( 'Button link', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'footer_cta_button_2', 'section' => 'bst_footer_cta', 'type' => 'text', 'label' => __( 'Second button text (optional)', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'footer_cta_url_2', 'section' => 'bst_footer_cta', 'type' => 'text', 'label' => __( 'Second button link', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'footer_cta_bg', 'section' => 'bst_footer_cta', 'type' => 'color', 'label' => __( 'Background', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'footer_cta_hide_pages', 'section' => 'bst_footer_cta', 'type' => 'text', 'label' => __( 'Hide on pages (IDs or slugs, comma separated)', 'bigspy-ai-theme-wp' ), 'description' => __( 'Example: contact, 42', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'footer_1_columns', 'section' => 'bst_footer_widgets', 'type' => 'select', 'label' => __( 'Footer 1 columns', 'bigspy-ai-theme-wp' ), 'choices' => array( '0' => __( 'Disabled', 'bigspy-ai-theme-wp' ), '1' => '1', '2' => '2', '3' => '3', '4' => '4' ) ),
			array( 'id' => 'footer_1_bg', 'section' => 'bst_footer_widgets', 'type' => 'color', 'label' => __( 'Footer 1 background', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'footer_1_color', 'section' => 'bst_footer_widgets', 'type' => 'color', 'label' => __( 'Footer 1 text', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'footer_1_heading_color', 'section' => 'bst_footer_widgets', 'type' => 'color', 'label' => __( 'Footer 1 widget titles', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'footer_2_columns', 'section' => 'bst_footer_widgets', 'type' => 'select', 'label' => __( 'Footer 2 columns', 'bigspy-ai-theme-wp' ), 'choices' => array( '0' => __( 'Disabled', 'bigspy-ai-theme-wp' ), '1' => '1', '2' => '2', '3' => '3', '4' => '4' ) ),
			array( 'id' => 'footer_2_bg', 'section' => 'bst_footer_widgets', 'type' => 'color', 'label' => __( 'Footer 2 background', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'footer_2_color', 'section' => 'bst_footer_widgets', 'type' => 'color', 'label' => __( 'Footer 2 text', 'bigspy-ai-theme-wp' ) ),

			array( 'id' => 'footer_bottom_enable', 'section' => 'bst_footer_bottom', 'type' => 'checkbox', 'label' => __( 'Show footer bottom bar', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'footer_bottom_layout', 'section' => 'bst_footer_bottom', 'type' => 'radio', 'label' => __( 'Alignment', 'bigspy-ai-theme-wp' ), 'choices' => array( 'split' => __( 'Left / right', 'bigspy-ai-theme-wp' ), 'center' => __( 'Centered', 'bigspy-ai-theme-wp' ) ) ),
			array( 'id' => 'footer_copyright', 'section' => 'bst_footer_bottom', 'type' => 'html', 'label' => __( 'Copyright text', 'bigspy-ai-theme-wp' ), 'description' => __( 'Tokens: [year], [site_title]', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'footer_bottom_html', 'section' => 'bst_footer_bottom', 'type' => 'html', 'label' => __( 'Right side HTML (payment icons, badges…)', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'footer_social', 'section' => 'bst_footer_bottom', 'type' => 'checkbox', 'label' => __( 'Show social icons', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'footer_bottom_bg', 'section' => 'bst_footer_bottom', 'type' => 'color', 'label' => __( 'Background', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'footer_bottom_color', 'section' => 'bst_footer_bottom', 'type' => 'color', 'label' => __( 'Text color', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'back_to_top', 'section' => 'bst_footer_bottom', 'type' => 'checkbox', 'label' => __( 'Back-to-top button', 'bigspy-ai-theme-wp' ) ),


			// ---------------------------------------------------------------- Advanced.
			array( 'id' => 'scripts_head', 'section' => 'bst_scripts', 'type' => 'code', 'label' => __( 'Scripts in <head>', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'scripts_body', 'section' => 'bst_scripts', 'type' => 'code', 'label' => __( 'Scripts after <body>', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'scripts_footer', 'section' => 'bst_scripts', 'type' => 'code', 'label' => __( 'Scripts before </body>', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'disable_emoji', 'section' => 'bst_performance', 'type' => 'checkbox', 'label' => __( 'Disable WordPress emoji script', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'animations', 'section' => 'bst_performance', 'type' => 'checkbox', 'label' => __( 'Enable scroll animations on blocks', 'bigspy-ai-theme-wp' ) ),
		)
	);

	if ( class_exists( 'WooCommerce' ) ) {
		$cols = array( '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6' );
		$fields = array_merge(
			$fields,
			array(
				array( 'id' => 'shop_columns', 'section' => 'bst_shop', 'type' => 'select', 'label' => __( 'Products per row – desktop', 'bigspy-ai-theme-wp' ), 'choices' => $cols ),
				array( 'id' => 'shop_columns_tablet', 'section' => 'bst_shop', 'type' => 'select', 'label' => __( 'Products per row – tablet', 'bigspy-ai-theme-wp' ), 'choices' => array_slice( $cols, 0, 4, true ) ),
				array( 'id' => 'shop_columns_mobile', 'section' => 'bst_shop', 'type' => 'select', 'label' => __( 'Products per row – mobile', 'bigspy-ai-theme-wp' ), 'choices' => array_slice( $cols, 0, 2, true ) ),
				array( 'id' => 'shop_per_page', 'section' => 'bst_shop', 'type' => 'number', 'label' => __( 'Products per page', 'bigspy-ai-theme-wp' ), 'input_attrs' => $px( 1, 100 ) ),
				array( 'id' => 'shop_sidebar', 'section' => 'bst_shop', 'type' => 'select', 'label' => __( 'Shop sidebar', 'bigspy-ai-theme-wp' ), 'choices' => $sidebar ),
				array( 'id' => 'cart_icon', 'section' => 'bst_shop', 'type' => 'radio', 'label' => __( 'Header cart icon', 'bigspy-ai-theme-wp' ), 'choices' => array( 'bag' => __( 'Bag', 'bigspy-ai-theme-wp' ), 'cart' => __( 'Cart', 'bigspy-ai-theme-wp' ) ) ),
				array( 'id' => 'mini_cart', 'section' => 'bst_shop', 'type' => 'checkbox', 'label' => __( 'Mini cart dropdown on hover (desktop)', 'bigspy-ai-theme-wp' ) ),

				array( 'id' => 'product_card_style', 'section' => 'bst_product', 'type' => 'select', 'label' => __( 'Product card style', 'bigspy-ai-theme-wp' ), 'choices' => array( 'bordered' => __( 'Bordered', 'bigspy-ai-theme-wp' ), 'shadow' => __( 'Shadow on hover', 'bigspy-ai-theme-wp' ), 'plain' => __( 'Plain', 'bigspy-ai-theme-wp' ) ) ),
				array( 'id' => 'product_hover', 'section' => 'bst_product', 'type' => 'select', 'label' => __( 'Image hover effect', 'bigspy-ai-theme-wp' ), 'choices' => array( 'swap' => __( 'Show second gallery image', 'bigspy-ai-theme-wp' ), 'zoom' => __( 'Zoom', 'bigspy-ai-theme-wp' ), 'none' => __( 'None', 'bigspy-ai-theme-wp' ) ) ),
				array( 'id' => 'product_title_lines', 'section' => 'bst_product', 'type' => 'select', 'label' => __( 'Product title max lines', 'bigspy-ai-theme-wp' ), 'choices' => array( '1' => '1', '2' => '2', '3' => '3', '99' => __( 'Unlimited', 'bigspy-ai-theme-wp' ) ) ),
				array( 'id' => 'product_image_ratio', 'section' => 'bst_product', 'type' => 'select', 'label' => __( 'Product image ratio', 'bigspy-ai-theme-wp' ), 'choices' => array( '1-1' => __( 'Square 1:1', 'bigspy-ai-theme-wp' ), '3-4' => __( 'Portrait 3:4 (fashion)', 'bigspy-ai-theme-wp' ), '4-3' => __( 'Landscape 4:3', 'bigspy-ai-theme-wp' ), '3-2' => __( 'Landscape 3:2', 'bigspy-ai-theme-wp' ), '16-9' => __( 'Widescreen 16:9 (banners)', 'bigspy-ai-theme-wp' ), '2-3' => __( 'Portrait 2:3 (books, posters)', 'bigspy-ai-theme-wp' ), 'auto' => __( 'Original', 'bigspy-ai-theme-wp' ) ) ),
				array( 'id' => 'product_button', 'section' => 'bst_product', 'type' => 'select', 'label' => __( 'Add to cart button', 'bigspy-ai-theme-wp' ), 'choices' => array( 'always' => __( 'Always visible', 'bigspy-ai-theme-wp' ), 'hover' => __( 'Show on hover (desktop)', 'bigspy-ai-theme-wp' ), 'icon' => __( 'Round icon on the image', 'bigspy-ai-theme-wp' ), 'none' => __( 'Hidden', 'bigspy-ai-theme-wp' ) ) ),
				array( 'id' => 'product_show_category', 'section' => 'bst_product', 'type' => 'checkbox', 'label' => __( 'Show category above the title', 'bigspy-ai-theme-wp' ) ),
				array( 'id' => 'sale_badge_style', 'section' => 'bst_product', 'type' => 'select', 'label' => __( 'Badge shape', 'bigspy-ai-theme-wp' ), 'choices' => array( 'pill' => __( 'Pill', 'bigspy-ai-theme-wp' ), 'square' => __( 'Square', 'bigspy-ai-theme-wp' ), 'circle' => __( 'Circle', 'bigspy-ai-theme-wp' ) ) ),
				array( 'id' => 'show_sale_percent', 'section' => 'bst_product', 'type' => 'checkbox', 'label' => __( 'Show discount percentage on sale badge', 'bigspy-ai-theme-wp' ) ),
				array( 'id' => 'new_badge_days', 'section' => 'bst_product', 'type' => 'number', 'label' => __( '“New” badge for products added in the last N days (0 = off)', 'bigspy-ai-theme-wp' ), 'input_attrs' => $px( 0, 365 ) ),
				array( 'id' => 'featured_badge', 'section' => 'bst_product', 'type' => 'checkbox', 'label' => __( '“Hot” badge on featured products', 'bigspy-ai-theme-wp' ) ),
				array( 'id' => 'product_sidebar', 'section' => 'bst_product', 'type' => 'select', 'label' => __( 'Product page sidebar', 'bigspy-ai-theme-wp' ), 'choices' => $sidebar ),
				array( 'id' => 'product_gallery_layout', 'section' => 'bst_product', 'type' => 'select', 'label' => __( 'Product gallery layout', 'bigspy-ai-theme-wp' ), 'choices' => array( 'default' => __( 'Default', 'bigspy-ai-theme-wp' ), 'stacked' => __( 'Stacked images', 'bigspy-ai-theme-wp' ), 'thumbnails-left' => __( 'Thumbnails on the left', 'bigspy-ai-theme-wp' ) ) ),
				array( 'id' => 'product_sticky_summary', 'section' => 'bst_product', 'type' => 'checkbox', 'label' => __( 'Keep purchase details visible while scrolling (desktop)', 'bigspy-ai-theme-wp' ) ),
				array( 'id' => 'product_mobile_bar', 'section' => 'bst_product', 'type' => 'checkbox', 'label' => __( 'Show a quick purchase bar on mobile', 'bigspy-ai-theme-wp' ) ),
				array( 'id' => 'related_columns', 'section' => 'bst_product', 'type' => 'select', 'label' => __( 'Related products per row', 'bigspy-ai-theme-wp' ), 'choices' => array_slice( $cols, 1, 4, true ) ),

				array( 'id' => 'catalog_mode', 'section' => 'bst_catalog', 'type' => 'checkbox', 'label' => __( 'Enable catalog mode', 'bigspy-ai-theme-wp' ) ),
				array( 'id' => 'catalog_mode_text', 'section' => 'bst_catalog', 'type' => 'text', 'label' => __( 'Button text', 'bigspy-ai-theme-wp' ) ),
				array( 'id' => 'catalog_mode_url', 'section' => 'bst_catalog', 'type' => 'text', 'label' => __( 'Button link (empty = phone number from Contact info)', 'bigspy-ai-theme-wp' ) ),
			)
		);
	}

	return apply_filters( 'bst_customizer_fields', $fields );
}
