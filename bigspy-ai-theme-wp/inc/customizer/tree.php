<?php
/**
 * G-214: one Customizer tree.
 *
 * Global style · Header · Footer · Pages & title · Blog · Shop (WooCommerce) ·
 * Listing (List mode) · Advanced, with "Contact info & social" on the top level.
 * Section and panel ids stay the same, so quick-edit labels, dashboard links and
 * saved settings keep working; only titles, parents and order change here.
 * Sections may carry 'preview' (a URL): the section shows "View on a sample page".
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Panel id => array( title, priority, description ). */
function bst_customizer_panels() {
	$panels = array(
		'bst_style'    => array( __( 'Global style', 'bigspy-ai-theme-wp' ), 21, __( 'Colors, fonts, buttons and the radius, shadow and spacing every card and section shares.', 'bigspy-ai-theme-wp' ) ),
		'bst_header'   => array( __( 'Header', 'bigspy-ai-theme-wp' ), 22, __( 'Header builder, logo, menu, search, buttons and the announcement bar.', 'bigspy-ai-theme-wp' ) ),
		'bst_footer'   => array( __( 'Footer', 'bigspy-ai-theme-wp' ), 23, __( 'Call-to-action band, widget columns, copyright and floating contact buttons.', 'bigspy-ai-theme-wp' ) ),
		'bst_layout'   => array( __( 'Pages & title', 'bigspy-ai-theme-wp' ), 24, __( 'Site width, page title bar, breadcrumbs and sidebars.', 'bigspy-ai-theme-wp' ) ),
		'bst_blog'     => array( __( 'Blog', 'bigspy-ai-theme-wp' ), 25, __( 'Posts page, featured posts, category pages and single posts.', 'bigspy-ai-theme-wp' ) ),
		'bst_listing'  => array( __( 'Listing (List mode)', 'bigspy-ai-theme-wp' ), 27, __( 'Listing cards, the listing page, homepage sidebar and every listing text.', 'bigspy-ai-theme-wp' ) ),
		'bst_advanced' => array( __( 'Advanced', 'bigspy-ai-theme-wp' ), 200, __( 'Scripts, performance, site tools, block list and WordPress tweaks.', 'bigspy-ai-theme-wp' ) ),
	);
	return apply_filters( 'bst_customizer_panels', $panels );
}

/** Section id => array( panel, priority, one-line description used when the section has none ). */
function bst_customizer_tree() {
	return array(
		// Global style.
		'bst_global_style'   => array( 'bst_style', 1 ),
		'bst_colors'         => array( 'bst_style', 2 ),
		'bst_typography'     => array( 'bst_style', 3, __( 'Fonts, weights, body size and heading sizes per device.', 'bigspy-ai-theme-wp' ) ),
		'bst_buttons'        => array( 'bst_style', 4, __( 'Shape and text of buttons across the site.', 'bigspy-ai-theme-wp' ) ),
		// Header.
		'bst_header_buttons' => array( 'bst_header', 7, __( 'Call-to-action buttons in the header.', 'bigspy-ai-theme-wp' ) ),
		'bst_header_search' => array( 'bst_header', 6, __( 'Search icon or box, live results and what it searches.', 'bigspy-ai-theme-wp' ) ),
		'bst_header_nav' => array( 'bst_header', 5, __( 'Menu font, size, colors and dropdowns.', 'bigspy-ai-theme-wp' ) ),
		'bst_header_rows' => array( 'bst_header', 3, __( 'Height and colors of the top, main, bottom and mobile rows.', 'bigspy-ai-theme-wp' ) ),
		'bst_header_general' => array( 'bst_header', 2, __( 'Full-width or boxed header, sticky and transparent header.', 'bigspy-ai-theme-wp' ) ),
		'bst_announcement'   => array( 'bst_header', 0, __( 'A thin bar above the header for news or offers.', 'bigspy-ai-theme-wp' ) ),
		// Footer.
		'bst_footer_bottom' => array( 'bst_footer', 3, __( 'Copyright line, footer menu and social icons.', 'bigspy-ai-theme-wp' ) ),
		'bst_float'          => array( 'bst_footer', 4, __( 'Call, Zalo, Messenger… buttons fixed to the screen corner.', 'bigspy-ai-theme-wp' ) ),
		// Pages & title.
		'bst_layout_site'    => array( 'bst_layout', 1, __( 'Full or boxed site, container and sidebar width.', 'bigspy-ai-theme-wp' ) ),
		'bst_title_bar'      => array( 'bst_layout', 2, __( 'Title above pages and archives, and the breadcrumb trail.', 'bigspy-ai-theme-wp' ) ),
		'bst_sidebar'        => array( 'bst_layout', 3, __( 'Sidebar style, sticky sidebar, widget spacing and mobile behavior.', 'bigspy-ai-theme-wp' ) ),
		// Blog.
		'bst_blog_archive'   => array( 'bst_blog', 1, __( 'Layout, columns, card details and pagination of the posts page.', 'bigspy-ai-theme-wp' ) ),
		'bst_blog_featured'  => array( 'bst_blog', 2 ),
		'bst_blog_category'  => array( 'bst_blog', 3 ),
		'bst_blog_single'    => array( 'bst_blog', 4, __( 'Header style, table of contents, author box and related posts.', 'bigspy-ai-theme-wp' ) ),
		// Listing (List mode).
		'bst_listing_design' => array( 'bst_listing', 1 ),
		'bst_listing_single' => array( 'bst_listing', 2 ),
		'bst_home_sidebar'   => array( 'bst_listing', 3, __( 'Where the homepage sidebar starts and ends (List mode).', 'bigspy-ai-theme-wp' ) ),
		'bst_listing_texts'  => array( 'bst_listing', 4 ),
		// Advanced.
		'bst_scripts'        => array( 'bst_advanced', 1 ),
		'bst_performance'    => array( 'bst_advanced', 2, __( 'Speed options, lazy loading and site-wide effects.', 'bigspy-ai-theme-wp' ) ),
		'bst_site_tools'     => array( 'bst_advanced', 3, __( '404 page and maintenance mode.', 'bigspy-ai-theme-wp' ) ),
		'bst_site_extras'    => array( 'bst_advanced', 4, __( 'Cookie notice, reading progress bar and other small extras.', 'bigspy-ai-theme-wp' ) ),
		'bst_block_manager'  => array( 'bst_advanced', 5 ),
		'bst_tweaks'         => array( 'bst_advanced', 6, __( 'Turn off WordPress features you do not use and the page loader.', 'bigspy-ai-theme-wp' ) ),
		// Top level (G-193): easy to find from the header and the footer.
		'bst_contact'        => array( '', 26 ),
	);
}

/** Latest published item of a post type, for sample-page links. */
function bst_customizer_sample_url( $type ) {
	$ids = get_posts( array( 'post_type' => $type, 'post_status' => 'publish', 'numberposts' => 1, 'fields' => 'ids', 'no_found_rows' => true ) );
	return $ids ? (string) get_permalink( $ids[0] ) : '';
}

/** Section id => sample page URL. */
function bst_customizer_preview_urls() {
	$posts_page = (int) get_option( 'page_for_posts' );
	$blog       = $posts_page ? get_permalink( $posts_page ) : home_url( '/' );
	$post       = bst_customizer_sample_url( 'post' );
	$page       = bst_customizer_sample_url( 'page' );
	$product    = post_type_exists( 'product' ) ? bst_customizer_sample_url( 'product' ) : '';
	$cats       = get_categories( array( 'number' => 1, 'orderby' => 'count', 'order' => 'DESC' ) );
	$urls       = array(
		'bst_blog_archive'   => $blog,
		'bst_blog_featured'  => $blog,
		'bst_blog_category'  => $cats ? get_category_link( $cats[0] ) : '',
		'bst_blog_single'    => $post,
		'bst_title_bar'      => $page,
		'bst_layout_site'    => $page,
		'bst_listing_single' => $product,
		'bst_listing_design' => home_url( '/' ),
		'bst_home_sidebar'   => home_url( '/' ),
		'bst_site_tools'     => home_url( '/bst-404-sample-' . wp_rand( 100, 999 ) . '/' ),
	);
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		$urls['bst_shop']     = wc_get_page_permalink( 'shop' );
		$urls['bst_catalog']  = wc_get_page_permalink( 'shop' );
		$urls['bst_product']  = $product;
		$urls['bst_checkout'] = wc_get_page_permalink( 'cart' );
	}
	return array_filter( apply_filters( 'bst_customizer_preview_urls', $urls ) );
}

/** Apply the tree to the section list (runs last). */
add_filter(
	'bst_customizer_sections',
	function ( $sections ) {
		foreach ( bst_customizer_tree() as $id => $place ) {
			if ( ! isset( $sections[ $id ] ) ) {
				continue;
			}
			$sections[ $id ]['panel']    = $place[0];
			$sections[ $id ]['priority'] = $place[1];
			if ( empty( $sections[ $id ]['description'] ) && ! empty( $place[2] ) ) {
				$sections[ $id ]['description'] = $place[2];
			}
		}
		return $sections;
	},
	999
);

/** Sample-page links for the controls script. */
add_action(
	'customize_controls_enqueue_scripts',
	function () {
		wp_localize_script(
			'bst-customizer',
			'bstTree',
			array(
				'urls'  => is_admin() ? bst_customizer_preview_urls() : array(),
				'label' => __( 'View on a sample page', 'bigspy-ai-theme-wp' ),
			)
		);
	},
	20
);
