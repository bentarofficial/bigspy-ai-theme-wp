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
	);
}

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
