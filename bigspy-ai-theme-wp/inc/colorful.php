<?php
/**
 * Color: one-click site palettes (Customizer → Colors), more product card styles
 * and the background presets used by the Section block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** key => [ label, description, primary, secondary, success, alert, heading, text, light, footer, footer bottom ]. */
function bst_color_palettes() {
	return array(
		'ocean'    => array( __( 'Ocean blue', 'bigspy-ai-theme-wp' ), __( 'Trust, services, laundry, clinics', 'bigspy-ai-theme-wp' ), '#0868c9', '#ffb703', '#16a34a', '#e11d48', '#0f2a44', '#3d5266', '#eef6fd', '#0b2540', '#081b2f' ),
		'sunset'   => array( __( 'Sunset', 'bigspy-ai-theme-wp' ), __( 'Food, cafés, travel', 'bigspy-ai-theme-wp' ), '#f97316', '#db2777', '#16a34a', '#dc2626', '#3b1d0f', '#5b4636', '#fff4ec', '#2a140b', '#1c0d07' ),
		'forest'   => array( __( 'Fresh green', 'bigspy-ai-theme-wp' ), __( 'Organic shops, farms, health', 'bigspy-ai-theme-wp' ), '#16a34a', '#f59e0b', '#15803d', '#dc2626', '#123524', '#3f5247', '#effaf2', '#0e2a1c', '#0a1f14' ),
		'berry'    => array( __( 'Berry pink', 'bigspy-ai-theme-wp' ), __( 'Beauty, spa, fashion', 'bigspy-ai-theme-wp' ), '#db2777', '#7c3aed', '#16a34a', '#e11d48', '#3a1029', '#5c4452', '#fdf0f6', '#2b0b1f', '#1e0715' ),
		'royal'    => array( __( 'Royal purple', 'bigspy-ai-theme-wp' ), __( 'Tech, education, agencies', 'bigspy-ai-theme-wp' ), '#6d28d9', '#06b6d4', '#16a34a', '#e11d48', '#1e1238', '#4a4560', '#f4f0ff', '#170d2e', '#0f0820' ),
		'tomato'   => array( __( 'Bold red', 'bigspy-ai-theme-wp' ), __( 'Sales, electronics, marts', 'bigspy-ai-theme-wp' ), '#dc2626', '#f59e0b', '#16a34a', '#b91c1c', '#2b0f0f', '#4f3d3d', '#fff2f2', '#1f0b0b', '#150707' ),
		'teal'     => array( __( 'Teal & coral', 'bigspy-ai-theme-wp' ), __( 'Modern, friendly brands', 'bigspy-ai-theme-wp' ), '#0d9488', '#fb7185', '#16a34a', '#e11d48', '#0f2e2c', '#3e5552', '#ecfbf9', '#0b2523', '#071a18' ),
		'gold'     => array( __( 'Black & gold', 'bigspy-ai-theme-wp' ), __( 'Luxury, jewelry, real estate', 'bigspy-ai-theme-wp' ), '#b8860b', '#111827', '#16a34a', '#b91c1c', '#111111', '#44403c', '#faf6ec', '#111111', '#000000' ),
		'sky'      => array( __( 'Sky & lemon', 'bigspy-ai-theme-wp' ), __( 'Kids, toys, playful shops', 'bigspy-ai-theme-wp' ), '#0ea5e9', '#facc15', '#22c55e', '#ef4444', '#0c2d48', '#3f5568', '#effaff', '#0b2a40', '#071d2d' ),
		'classic'  => array( __( 'BIGSPY classic', 'bigspy-ai-theme-wp' ), __( 'The original navy & orange', 'bigspy-ai-theme-wp' ), '#0a4d8c', '#f26b21', '#2e9d5b', '#d63638', '#111827', '#3a3f47', '#f5f7fa', '#111827', '#0b1220' ),
		'navy_gold' => array( __( 'Navy & gold', 'bigspy-ai-theme-wp' ), __( 'Law, finance, consulting', 'bigspy-ai-theme-wp' ), '#1e3a8a', '#d4a017', '#15803d', '#b91c1c', '#0f1b3d', '#3f4a63', '#f1f4fb', '#0c1733', '#081026' ),
		'steel' => array( __( 'Steel blue', 'bigspy-ai-theme-wp' ), __( 'Industry, logistics, B2B', 'bigspy-ai-theme-wp' ), '#2563eb', '#64748b', '#16a34a', '#dc2626', '#0f172a', '#475569', '#f1f5f9', '#0f172a', '#020617' ),
		'cyan_fresh' => array( __( 'Fresh cyan', 'bigspy-ai-theme-wp' ), __( 'Cleaning, water, pools', 'bigspy-ai-theme-wp' ), '#0891b2', '#22d3ee', '#16a34a', '#e11d48', '#083344', '#3b5560', '#ecfeff', '#06303d', '#04222b' ),
		'mint' => array( __( 'Mint', 'bigspy-ai-theme-wp' ), __( 'Pharmacy, dental, wellness', 'bigspy-ai-theme-wp' ), '#10b981', '#0ea5e9', '#059669', '#ef4444', '#063b2c', '#3f564e', '#ecfdf5', '#05301f', '#032116' ),
		'olive' => array( __( 'Olive & sand', 'bigspy-ai-theme-wp' ), __( 'Coffee, tea, handmade', 'bigspy-ai-theme-wp' ), '#65a30d', '#ca8a04', '#15803d', '#b91c1c', '#1f2a10', '#4a5139', '#f7f9ee', '#1a230d', '#111808' ),
		'earth' => array( __( 'Earth brown', 'bigspy-ai-theme-wp' ), __( 'Furniture, wood, bakery', 'bigspy-ai-theme-wp' ), '#92400e', '#d97706', '#15803d', '#b91c1c', '#2a1608', '#57483d', '#fbf5ef', '#231206', '#170b03' ),
		'terracotta' => array( __( 'Terracotta', 'bigspy-ai-theme-wp' ), __( 'Restaurants, ceramics, decor', 'bigspy-ai-theme-wp' ), '#c2410c', '#0f766e', '#15803d', '#b91c1c', '#2b1309', '#5b4a42', '#fdf3ee', '#241007', '#180a04' ),
		'peach' => array( __( 'Soft peach', 'bigspy-ai-theme-wp' ), __( 'Bakery, kids, gifts', 'bigspy-ai-theme-wp' ), '#fb923c', '#f472b6', '#22c55e', '#ef4444', '#3b1f12', '#5f4b40', '#fff7f0', '#2d170c', '#1e0f07' ),
		'rose_gold' => array( __( 'Rose gold', 'bigspy-ai-theme-wp' ), __( 'Wedding, jewelry, beauty', 'bigspy-ai-theme-wp' ), '#be7c6b', '#3f3a3a', '#15803d', '#b91c1c', '#2e2220', '#5a4a47', '#fbf4f2', '#2a1f1d', '#1b1312' ),
		'lavender' => array( __( 'Lavender', 'bigspy-ai-theme-wp' ), __( 'Spa, yoga, flowers', 'bigspy-ai-theme-wp' ), '#8b5cf6', '#f0abfc', '#16a34a', '#e11d48', '#25174a', '#4d4763', '#f6f3ff', '#1d123c', '#130b29' ),
		'magenta' => array( __( 'Magenta pop', 'bigspy-ai-theme-wp' ), __( 'Events, music, nightlife', 'bigspy-ai-theme-wp' ), '#c026d3', '#facc15', '#22c55e', '#ef4444', '#2a0b30', '#55415a', '#fdf2ff', '#220826', '#16051a' ),
		'indigo' => array( __( 'Indigo', 'bigspy-ai-theme-wp' ), __( 'SaaS, apps, startups', 'bigspy-ai-theme-wp' ), '#4f46e5', '#22d3ee', '#16a34a', '#e11d48', '#1e1b4b', '#4b4a66', '#eef2ff', '#17153d', '#0e0c29' ),
		'electric' => array( __( 'Electric blue & lime', 'bigspy-ai-theme-wp' ), __( 'Gaming, sports, energy', 'bigspy-ai-theme-wp' ), '#2563eb', '#84cc16', '#22c55e', '#ef4444', '#0b1530', '#3e4a64', '#eef4ff', '#0a1229', '#060b1c' ),
		'orange_tech' => array( __( 'Orange & charcoal', 'bigspy-ai-theme-wp' ), __( 'Tools, auto, hardware', 'bigspy-ai-theme-wp' ), '#ea580c', '#1f2937', '#16a34a', '#dc2626', '#111827', '#4b5563', '#fff5ed', '#111827', '#030712' ),
		'ruby' => array( __( 'Ruby wine', 'bigspy-ai-theme-wp' ), __( 'Wine, restaurants, premium', 'bigspy-ai-theme-wp' ), '#9f1239', '#d4a373', '#15803d', '#dc2626', '#2a0a14', '#56404a', '#fdf2f5', '#22070f', '#16040a' ),
		'emerald_lux' => array( __( 'Emerald luxury', 'bigspy-ai-theme-wp' ), __( 'Hotels, resorts, real estate', 'bigspy-ai-theme-wp' ), '#047857', '#c9a227', '#15803d', '#b91c1c', '#06281d', '#3e544b', '#effaf5', '#052219', '#03160f' ),
		'ocean_coral' => array( __( 'Ocean & coral', 'bigspy-ai-theme-wp' ), __( 'Travel, beach, tours', 'bigspy-ai-theme-wp' ), '#0369a1', '#f97362', '#16a34a', '#e11d48', '#082f49', '#3f5466', '#eff8ff', '#072a41', '#041c2c' ),
		'monochrome' => array( __( 'Monochrome', 'bigspy-ai-theme-wp' ), __( 'Minimal, studios, portfolios', 'bigspy-ai-theme-wp' ), '#111827', '#6b7280', '#16a34a', '#dc2626', '#0b0b0b', '#3f3f46', '#f4f4f5', '#0b0b0b', '#000000' ),
		'graphite_cyan' => array( __( 'Graphite & cyan', 'bigspy-ai-theme-wp' ), __( 'Tech shops, phones, IT', 'bigspy-ai-theme-wp' ), '#0e7490', '#f59e0b', '#16a34a', '#dc2626', '#18181b', '#52525b', '#f0fdff', '#18181b', '#09090b' ),
		'candy' => array( __( 'Candy', 'bigspy-ai-theme-wp' ), __( 'Sweets, toys, party', 'bigspy-ai-theme-wp' ), '#ec4899', '#06b6d4', '#22c55e', '#ef4444', '#3a0d24', '#5e4552', '#fff0f7', '#2c0a1c', '#1d0612' ),
		'sage' => array( __( 'Sage', 'bigspy-ai-theme-wp' ), __( 'Interior, eco, skincare', 'bigspy-ai-theme-wp' ), '#5f8a6b', '#c08457', '#15803d', '#b91c1c', '#1d2b22', '#4b564f', '#f3f7f4', '#18241c', '#0f1812' ),
		'sunflower' => array( __( 'Sunflower', 'bigspy-ai-theme-wp' ), __( 'Construction, delivery, energy', 'bigspy-ai-theme-wp' ), '#eab308', '#1e293b', '#16a34a', '#dc2626', '#1e293b', '#475569', '#fefce8', '#1e293b', '#0f172a' ),
		'medical' => array( __( 'Medical blue', 'bigspy-ai-theme-wp' ), __( 'Hospitals, labs, care', 'bigspy-ai-theme-wp' ), '#0284c7', '#14b8a6', '#16a34a', '#dc2626', '#0c2a43', '#3e5568', '#f0f9ff', '#0a2439', '#061827' ),
		'vietnam_red' => array( __( 'Lucky red & gold', 'bigspy-ai-theme-wp' ), __( 'Tết, gifts, traditional', 'bigspy-ai-theme-wp' ), '#c8102e', '#f2b705', '#15803d', '#991b1b', '#2b0a0f', '#57403f', '#fff5f0', '#220709', '#150405' ),
	);
}

add_filter(
	'bst_defaults',
	function ( $defaults ) {
		return $defaults + array( 'color_use_palette' => true );
	}
);

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$presets = array();
		foreach ( bst_color_palettes() as $key => $p ) {
			$presets[ $key ] = array(
				'label'       => $p[0],
				'description' => $p[1],
				'swatches'    => array( $p[2], $p[3], $p[6], $p[8] ),
				'settings'    => array(
					'color_primary'    => $p[2],
					'color_secondary'  => $p[3],
					'color_success'    => $p[4],
					'color_alert'      => $p[5],
					'color_heading'    => $p[6],
					'color_text'       => $p[7],
					'color_link'       => $p[2],
					'color_link_hover' => $p[3],
					'color_light'      => $p[8],
					'header_top_bg'    => $p[2],
					'footer_cta_bg'    => $p[2],
					'footer_1_bg'      => $p[9],
					'footer_2_bg'      => $p[10],
					'footer_bottom_bg' => $p[10],
				),
			);
		}
		array_unshift(
			$fields,
			array(
				'id'          => 'color_use_palette',
				'section'     => 'bst_colors',
				'type'        => 'checkbox',
				'priority'    => 0,
				'label'       => __( 'Choose from the color palette library', 'bigspy-ai-theme-wp' ),
				'description' => __( 'On: pick a ready-made palette. Off: set every color by hand below.', 'bigspy-ai-theme-wp' ),
			),
			array(
				'id'          => 'color_palette_presets',
				'section'     => 'bst_colors',
				'type'        => 'layout_presets',
				'priority'    => 0,
				'label'       => __( 'Color palettes', 'bigspy-ai-theme-wp' ),
				'description' => __( 'One click sets the brand colors, links, top bar and footer together. Fine-tune any color below, then Publish.', 'bigspy-ai-theme-wp' ),
				'presets'     => $presets,
			)
		);

		// More product card styles for the shop, category pages and related products.
		foreach ( $fields as $i => $field ) {
			if ( isset( $field['id'] ) && 'product_card_style' === $field['id'] ) {
				$fields[ $i ]['choices'] = array(
					'bordered' => __( 'Bordered', 'bigspy-ai-theme-wp' ),
					'shadow'   => __( 'Shadow on hover', 'bigspy-ai-theme-wp' ),
					'plain'    => __( 'Plain', 'bigspy-ai-theme-wp' ),
					'boxed'    => __( 'Boxed card', 'bigspy-ai-theme-wp' ),
					'elevated' => __( 'Elevated (soft shadow, lifts on hover)', 'bigspy-ai-theme-wp' ),
					'colorful' => __( 'Colorful (tinted, gradient button)', 'bigspy-ai-theme-wp' ),
					'minimal'  => __( 'Minimal (image + name + price)', 'bigspy-ai-theme-wp' ),
				);
			}
		}
		return $fields;
	}
);

/** Section background presets: key => label. Colors follow the site palette. */
function bst_section_backgrounds() {
	return array(
		'soft-primary'     => __( 'Soft primary', 'bigspy-ai-theme-wp' ),
		'soft-secondary'   => __( 'Soft secondary', 'bigspy-ai-theme-wp' ),
		'primary'          => __( 'Primary (light text)', 'bigspy-ai-theme-wp' ),
		'gradient'         => __( 'Brand gradient (light text)', 'bigspy-ai-theme-wp' ),
		'gradient-soft'    => __( 'Soft gradient', 'bigspy-ai-theme-wp' ),
		'sunrise'          => __( 'Sunrise (light text)', 'bigspy-ai-theme-wp' ),
		'ocean'            => __( 'Ocean (light text)', 'bigspy-ai-theme-wp' ),
		'mint'             => __( 'Mint', 'bigspy-ai-theme-wp' ),
		'peach'            => __( 'Peach', 'bigspy-ai-theme-wp' ),
		'lavender'         => __( 'Lavender', 'bigspy-ai-theme-wp' ),
		'dark'             => __( 'Dark (light text)', 'bigspy-ai-theme-wp' ),
		'dots'             => __( 'Dotted pattern', 'bigspy-ai-theme-wp' ),
		'grid'             => __( 'Grid pattern', 'bigspy-ai-theme-wp' ),
		'mesh'             => __( 'Color mesh', 'bigspy-ai-theme-wp' ),
	);
}

/** Presets that need light text. */
function bst_section_dark_backgrounds() {
	return array( 'primary', 'gradient', 'sunrise', 'ocean', 'dark' );
}
