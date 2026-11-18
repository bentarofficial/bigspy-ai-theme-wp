<?php
/**
 * G-200: style kits and per-section reset.
 *
 * Style kits (Global style → Style kits): one click sets colors, fonts, buttons,
 * card radius/shadow, header and footer colors and the product card together.
 * Like the other presets it only changes the preview; Publish keeps it, closing
 * the Customizer without publishing drops it.
 * Every BIGSPY section gets a "Reset this section" link that puts its settings
 * back to the theme defaults in the preview (same Publish / discard rule).
 * Whole-site export, import, reset and undo live in Appearance → BIGSPY AI Theme WP.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Kit key => label, description, swatches, settings. */
function bst_style_kits() {
	$kit = function ( $label, $desc, $c, $font_h, $font_b, $radius, $btn, $shadow, $card, $dark_footer ) {
		return array(
			'label'       => $label,
			'description' => $desc,
			'swatches'    => array( $c[0], $c[1], $c[2] ),
			'settings'    => array(
				'color_primary'       => $c[0],
				'color_secondary'     => $c[1],
				'color_heading'       => $c[2],
				'color_text'          => $c[3],
				'color_link'          => $c[0],
				'color_link_hover'    => $c[1],
				'color_light'         => $c[4],
				'color_body_bg'       => $c[5],
				'header_top_bg'       => $c[0],
				'header_main_bg'      => $c[5],
				'footer_1_bg'         => $dark_footer ? $c[2] : $c[4],
				'footer_1_color'      => $dark_footer ? '#cbd5e1' : $c[3],
				'footer_1_heading_color' => $dark_footer ? '#ffffff' : $c[2],
				'footer_bottom_bg'    => $dark_footer ? '#070c16' : $c[5],
				'footer_bottom_color' => $dark_footer ? '#94a3b8' : $c[3],
				'font_heading'        => $font_h,
				'font_body'           => $font_b,
				'button_radius'       => $btn,
				'gs_radius'           => $radius,
				'gs_shadow'           => $shadow,
				'product_card_style'  => $card,
			),
		);
	};
	$kits = array(
		'corporate' => $kit( __( 'Corporate', 'bigspy-ai-theme-wp' ), __( 'Navy and orange, clean sans-serif.', 'bigspy-ai-theme-wp' ), array( '#0a4d8c', '#f26b21', '#111827', '#3a3f47', '#f5f7fa', '#ffffff' ), 'Be Vietnam Pro', 'Inter', 8, 6, 'sm', 'bordered', true ),
		'startup'   => $kit( __( 'Startup', 'bigspy-ai-theme-wp' ), __( 'Violet, rounded, soft shadows.', 'bigspy-ai-theme-wp' ), array( '#6d28d9', '#06b6d4', '#1e1b4b', '#475569', '#f5f3ff', '#ffffff' ), 'Plus Jakarta Sans', 'Inter', 16, 60, 'md', 'elevated', true ),
		'elegant'   => $kit( __( 'Elegant', 'bigspy-ai-theme-wp' ), __( 'Serif headings, warm neutrals, gold.', 'bigspy-ai-theme-wp' ), array( '#1f2937', '#b8893b', '#111111', '#44403c', '#faf7f2', '#fffdf9' ), 'Playfair Display', 'Lora', 2, 0, 'none', 'plain', false ),
		'fresh'     => $kit( __( 'Fresh', 'bigspy-ai-theme-wp' ), __( 'Green and lime for food, health, nature.', 'bigspy-ai-theme-wp' ), array( '#15803d', '#84cc16', '#14281d', '#3f4b43', '#f0fdf4', '#ffffff' ), 'Nunito', 'Nunito Sans', 14, 10, 'sm', 'boxed', true ),
		'bold'      => $kit( __( 'Bold', 'bigspy-ai-theme-wp' ), __( 'Black, red, tall condensed headings.', 'bigspy-ai-theme-wp' ), array( '#dc2626', '#111111', '#0a0a0a', '#27272a', '#f4f4f5', '#ffffff' ), 'Oswald', 'Roboto', 0, 0, 'none', 'minimal', true ),
		'soft'      => $kit( __( 'Soft', 'bigspy-ai-theme-wp' ), __( 'Pastel pink and peach, friendly shapes.', 'bigspy-ai-theme-wp' ), array( '#db2777', '#fb923c', '#3b1d2e', '#57534e', '#fdf2f8', '#fffbfd' ), 'Quicksand', 'Mulish', 20, 60, 'md', 'colorful', false ),
		'tech'      => $kit( __( 'Tech', 'bigspy-ai-theme-wp' ), __( 'Blue and cyan, geometric font, sharp cards.', 'bigspy-ai-theme-wp' ), array( '#2563eb', '#0891b2', '#0f172a', '#334155', '#f1f5f9', '#ffffff' ), 'Space Grotesk', 'IBM Plex Sans', 6, 4, 'lg', 'shadow', true ),
		'minimal'   => $kit( __( 'Minimal', 'bigspy-ai-theme-wp' ), __( 'Black and white, lots of space.', 'bigspy-ai-theme-wp' ), array( '#111111', '#6b7280', '#000000', '#374151', '#f9fafb', '#ffffff' ), 'Inter Tight', 'Inter', 0, 0, 'none', 'plain', false ),
	);
	if ( ! class_exists( 'WooCommerce' ) ) {
		// The product card option exists only with WooCommerce.
		foreach ( $kits as $k => $v ) {
			unset( $kits[ $k ]['settings']['product_card_style'] );
		}
	}
	return apply_filters( 'bst_style_kits', $kits );
}

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$fields[] = array(
			'id'          => 'style_kit_presets',
			'section'     => 'bst_global_style',
			'type'        => 'layout_presets',
			'priority'    => 0,
			'label'       => __( 'Style kits', 'bigspy-ai-theme-wp' ),
			'description' => __( 'One click sets colors, fonts, buttons, cards, header and footer colors together. See it in the preview, fine-tune, then Publish. Close without publishing to drop it.', 'bigspy-ai-theme-wp' ),
			'presets'     => bst_style_kits(),
		);
		return $fields;
	}
);

/** Section id => array( setting id => default ) for every BIGSPY section. */
function bst_section_reset_map( $wp_customize ) {
	$map = array();
	foreach ( $wp_customize->controls() as $control ) {
		$section = (string) $control->section;
		if ( 0 !== strpos( $section, 'bst_' ) ) {
			continue;
		}
		foreach ( $control->settings as $setting ) {
			if ( $setting instanceof WP_Customize_Setting && 'theme_mod' === $setting->type ) {
				$map[ $section ][ $setting->id ] = $setting->default;
			}
		}
	}
	return $map;
}

add_action(
	'customize_controls_enqueue_scripts',
	function () {
		global $wp_customize;
		if ( ! $wp_customize ) {
			return;
		}
		wp_enqueue_script( 'bst-section-reset', BST_URI . '/assets/js/section-reset.js', array( 'customize-controls' ), bst_asset_version( 'assets/js/section-reset.js' ), true );
		wp_localize_script(
			'bst-section-reset',
			'bstSectionReset',
			array(
				'map'  => bst_section_reset_map( $wp_customize ),
				'i18n' => array(
					'button'  => __( 'Reset this section', 'bigspy-ai-theme-wp' ),
					'confirm' => __( 'Put every option in this section back to the theme default? Nothing is saved until you Publish.', 'bigspy-ai-theme-wp' ),
					'done'    => __( 'Section reset in the preview. Publish to keep it.', 'bigspy-ai-theme-wp' ),
				),
			)
		);
	},
	20
);
