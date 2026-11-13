<?php
/**
 * Listing templates: listing cards on the shop, category and search pages, related
 * listings on the listing page, and page patterns for a directory home and Categories page.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_feature_fields',
	function ( $fields ) {
		$fields['listing']['archive_cards'] = array( 'type' => 'checkbox', 'label' => __( 'Listing cards on shop, category and search pages', 'bigspy-ai-theme-wp' ), 'default' => 1 );
		$fields['listing']['related']       = array( 'type' => 'checkbox', 'label' => __( 'Related listings on the listing page', 'bigspy-ai-theme-wp' ), 'default' => 1 );
		return $fields;
	}
);

/** Main shop, category, tag and product search loops, not product blocks or shortcodes. */
function bst_listing_archive_loop() {
	return (bool) bst_feature_get( 'listing', 'archive_cards', 1 ) && ! is_admin() && ! wc_get_loop_prop( 'is_shortcode' ) && ( is_shop() || is_product_taxonomy() || ( is_search() && 'product' === get_query_var( 'post_type' ) ) );
}

add_filter(
	'wc_get_template_part',
	function ( $template, $slug, $name ) {
		return 'content' === $slug && 'product' === $name && bst_listing_archive_loop() ? BST_DIR . '/woocommerce/content-listing.php' : $template;
	},
	30,
	3
);

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( is_shop() || is_product_taxonomy() || is_search() ) {
			wp_enqueue_style( 'bst-listing', BST_URI . '/assets/css/listing.css', array(), bst_asset_version( 'assets/css/listing.css' ) );
		}
	}
);

// Directory listings have no cart: related listings replace WooCommerce related products.
add_action(
	'wp',
	function () {
		if ( is_product() && bst_feature_get( 'listing', 'related', 1 ) ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
			add_action(
				'woocommerce_after_single_product_summary',
				function () {
					echo bst_render_listing_elements( array( 'elementType' => 'related', 'title' => bst_listing_text( 'related_listings' ), 'count' => 8, 'showSponsored' => false ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped while building.
				},
				20
			);
		}
	}
);

/** Directory sections and full pages in the BIGSPY AI List pattern category. */
function bst_listing_patterns() {
		register_block_pattern_category( 'bigspy-list', array( 'label' => __( 'BIGSPY AI List', 'bigspy-ai-theme-wp' ) ) );
		$search = '<!-- wp:bst/section {"paddingDesktop":80} --><!-- wp:bst/heading {"text":"' . esc_attr__( 'Find the right tool', 'bigspy-ai-theme-wp' ) . '","tag":"h1","align":"center"} /--><!-- wp:bigspy/search {"alignment":"center"} /--><!-- /wp:bst/section -->';
		$cats   = '<!-- wp:bst/section {"paddingDesktop":60} --><!-- wp:bst/title {"text":"' . esc_attr__( 'Browse by category', 'bigspy-ai-theme-wp' ) . '"} /--><!-- wp:bigspy/category-grid {"count":12,"columnsDesktop":6,"columnsTablet":3,"columnsMobile":2} /--><!-- /wp:bst/section -->';
		$feat   = '<!-- wp:bst/section {"paddingDesktop":60,"backgroundColor":"light"} --><!-- wp:bst/title {"text":"' . esc_attr__( 'Featured', 'bigspy-ai-theme-wp' ) . '"} /--><!-- wp:bigspy/listing-grid {"featuredOnly":true,"limit":8,"columnsDesktop":4} /--><!-- /wp:bst/section -->';
		$latest = '<!-- wp:bst/section {"paddingDesktop":60} --><!-- wp:bst/title {"text":"' . esc_attr__( 'Latest listings', 'bigspy-ai-theme-wp' ) . '"} /--><!-- wp:bigspy/elements {"elementType":"latest","count":12,"columnsDesktop":4} /--><!-- /wp:bst/section -->';
		$all    = '<!-- wp:bst/section {"paddingDesktop":60} --><!-- wp:bst/heading {"text":"' . esc_attr__( 'Categories', 'bigspy-ai-theme-wp' ) . '","tag":"h1"} /--><!-- wp:bigspy/search /--><!-- wp:bigspy/category-grid {"count":60,"columnsDesktop":4,"columnsTablet":2,"columnsMobile":1,"showDescription":true} /--><!-- /wp:bst/section -->';
		$list   = array(
			'listing-search-hero' => array( __( 'Directory – search hero', 'bigspy-ai-theme-wp' ), $search ),
			'listing-categories'  => array( __( 'Directory – categories', 'bigspy-ai-theme-wp' ), $cats ),
			'listing-featured'    => array( __( 'Directory – featured listings', 'bigspy-ai-theme-wp' ), $feat ),
			'listing-latest'      => array( __( 'Directory – latest listings', 'bigspy-ai-theme-wp' ), $latest ),
			'page-listing-home'   => array( __( 'Homepage – directory', 'bigspy-ai-theme-wp' ), $search . $cats . $feat . $latest ),
			'page-listing-cats'   => array( __( 'Categories page – directory', 'bigspy-ai-theme-wp' ), $all ),
		);
		foreach ( $list as $slug => $pattern ) {
			$page = 0 === strpos( $slug, 'page-' );
			register_block_pattern(
				'bigspy-ai-theme-wp/' . $slug,
				array_merge(
					array( 'title' => $pattern[0], 'categories' => $page ? array( 'bigspy-list', 'bigspy-pages' ) : array( 'bigspy-list' ), 'content' => $pattern[1] ),
					$page ? array( 'postTypes' => array( 'page' ) ) : array()
				)
			);
		}
}
add_action( 'init', 'bst_listing_patterns', 20 );
