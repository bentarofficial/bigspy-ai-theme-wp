<?php
/**
 * Customizer usability: group headings for long sections. Every heading becomes a
 * collapsible group in customizer-controls.js, and the search box finds any option.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Field id => heading inserted right before it. */
function bst_customizer_group_breaks() {
	return array(
		'product_card_style'     => __( 'Product cards (shop & categories)', 'bigspy-ai-theme-wp' ),
		'sale_badge_style'       => __( 'Sale & new badges', 'bigspy-ai-theme-wp' ),
		'product_sidebar'        => __( 'Single product page', 'bigspy-ai-theme-wp' ),
		'related_columns'        => __( 'Related products', 'bigspy-ai-theme-wp' ),
		'product_tabs_style'     => __( 'Tabs, swatches & page design', 'bigspy-ai-theme-wp' ),
		'font_body'              => __( 'Body text', 'bigspy-ai-theme-wp' ),
		'font_heading'           => __( 'Headings', 'bigspy-ai-theme-wp' ),
		'h1_size'                => __( 'Heading sizes (H1–H4)', 'bigspy-ai-theme-wp' ),
		'color_primary'          => __( 'Brand colors', 'bigspy-ai-theme-wp' ),
		'color_text'             => __( 'Text & links', 'bigspy-ai-theme-wp' ),
		'color_border'           => __( 'Backgrounds & borders', 'bigspy-ai-theme-wp' ),
		'blog_layout'            => __( 'Layout', 'bigspy-ai-theme-wp' ),
		'blog_card_style'        => __( 'Post cards', 'bigspy-ai-theme-wp' ),
		'footer_cta_enable'      => __( 'Content', 'bigspy-ai-theme-wp' ),
		'footer_cta_button'      => __( 'Buttons', 'bigspy-ai-theme-wp' ),
		'footer_cta_bg'          => __( 'Style & visibility', 'bigspy-ai-theme-wp' ),
		'footer_1_columns'       => __( 'Footer row 1', 'bigspy-ai-theme-wp' ),
		'footer_2_columns'       => __( 'Footer row 2', 'bigspy-ai-theme-wp' ),
		'footer_block'           => __( 'Footer designed with blocks', 'bigspy-ai-theme-wp' ),
		'footer_bottom_enable'   => __( 'Content', 'bigspy-ai-theme-wp' ),
		'footer_bottom_bg'       => __( 'Colors', 'bigspy-ai-theme-wp' ),
		'back_to_top'            => __( 'Back to top button', 'bigspy-ai-theme-wp' ),
		'shop_columns'           => __( 'Product grid', 'bigspy-ai-theme-wp' ),
		'shop_ajax_pagination'   => __( 'Loading & filters', 'bigspy-ai-theme-wp' ),
		'product_quick_view'     => __( 'Quick view', 'bigspy-ai-theme-wp' ),
		'nav_style'              => __( 'Main menu', 'bigspy-ai-theme-wp' ),
		'offcanvas_side'         => __( 'Mobile menu', 'bigspy-ai-theme-wp' ),
		'site_layout'            => __( 'Page layout', 'bigspy-ai-theme-wp' ),
		'page_sidebar'           => __( 'Sidebars & extras', 'bigspy-ai-theme-wp' ),
		'title_bar_style'        => __( 'Title bar', 'bigspy-ai-theme-wp' ),
		'breadcrumbs'            => __( 'Breadcrumbs', 'bigspy-ai-theme-wp' ),
		'single_sidebar'         => __( 'Layout', 'bigspy-ai-theme-wp' ),
		'single_author_box'      => __( 'Below the post', 'bigspy-ai-theme-wp' ),
		'contact_phone'          => __( 'Contact info', 'bigspy-ai-theme-wp' ),
		'cart_layout'            => __( 'Cart & checkout layout', 'bigspy-ai-theme-wp' ),
	);
}

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$breaks = bst_customizer_group_breaks();
		$out    = array();
		foreach ( $fields as $field ) {
			$id = $field['id'] ?? '';
			if ( isset( $breaks[ $id ], $field['section'] ) ) {
				$heading = array(
					'id'      => 'bst_g_' . $id,
					'section' => $field['section'],
					'type'    => 'heading',
					'label'   => $breaks[ $id ],
				);
				if ( isset( $field['priority'] ) ) {
					$heading['priority'] = $field['priority'];
				}
				$out[] = $heading;
			}
			$out[] = $field;
		}
		return $out;
	},
	40
);
