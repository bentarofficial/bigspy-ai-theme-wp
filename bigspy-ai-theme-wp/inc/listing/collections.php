<?php
/**
 * Collection pages from the old BIGSPY AI List theme: the category directory on the
 * Categories page and the search toolbar on listing archives. Page heroes are
 * designed with the Banner block in the page content.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_feature_fields',
	function ( $fields ) {
		$fields['listing']['category_dir']  = array( 'type' => 'checkbox', 'label' => __( 'Category directory on the Categories page (when the page has no content)', 'bigspy-ai-theme-wp' ), 'default' => 1 );
		$fields['listing']['shop_toolbar']  = array( 'type' => 'checkbox', 'label' => __( 'Search, category and sort toolbar on listing archives', 'bigspy-ai-theme-wp' ), 'default' => 1 );
		return $fields;
	}
);

/** Page ID of a collection page: categories, listings, featured, popular or blog. */
function bst_collection_page_id( $key ) {
	if ( 'listings' === $key && function_exists( 'wc_get_page_id' ) ) {
		return (int) wc_get_page_id( 'shop' );
	}
	if ( 'blog' === $key && get_option( 'page_for_posts' ) ) {
		return (int) get_option( 'page_for_posts' );
	}
	foreach ( (array) get_option( 'bigspy_page_destinations', array() ) as $id => $item ) {
		$id = is_array( $item ) && isset( $item['id'] ) ? $item['id'] : $id;
		if ( $key === $id && is_array( $item ) && ! empty( $item['page_id'] ) ) {
			return (int) $item['page_id'];
		}
	}
	$page = get_page_by_path( $key );
	return $page ? (int) $page->ID : 0;
}

/** Which collection the current view is, or ''. */
function bst_collection_context() {
	if ( is_front_page() ) {
		return '';
	}
	if ( is_home() ) {
		return 'blog';
	}
	if ( function_exists( 'is_shop' ) && is_shop() ) {
		return 'listings';
	}
	if ( is_page() ) {
		foreach ( array( 'categories', 'featured', 'popular', 'blog' ) as $key ) {
			if ( bst_collection_page_id( $key ) === (int) get_queried_object_id() ) {
				return $key;
			}
		}
	}
	return '';
}

/** Featured on the Categories page changes order on every load unless the old theme turned rotation off. */
function bst_category_featured_order() {
	$old = get_option( 'bigspy_categories_promotions', array() );
	return is_array( $old ) && isset( $old['featured_rotate'] ) && ! $old['featured_rotate'] ? 'date' : 'rand';
}

/** Category directory: left navigation, Featured listings, then each top category with its subcategories. */
function bst_category_directory_html() {
	$tops = get_terms( array( 'taxonomy' => 'product_cat', 'parent' => 0, 'hide_empty' => true, 'orderby' => 'name' ) );
	$tops = is_wp_error( $tops ) ? array() : array_values( array_filter( $tops, fn( $t ) => 'uncategorized' !== $t->slug ) );
	if ( ! $tops ) {
		return '';
	}
	$nav = '';
	$dir = '';
	foreach ( $tops as $i => $top ) {
		$anchor   = 'bst-cat-' . $top->term_id;
		$nav     .= '<li><a href="#' . esc_attr( $anchor ) . '">' . esc_html( $top->name ) . '</a><span>' . esc_html( number_format_i18n( $top->count ) ) . '</span></li>';
		$children = get_terms( array( 'taxonomy' => 'product_cat', 'child_of' => $top->term_id, 'hide_empty' => true, 'orderby' => 'name' ) );
		$dir     .= '<section class="bst-catdir__section" id="' . esc_attr( $anchor ) . '"><header><h2><a href="' . esc_url( get_term_link( $top ) ) . '">' . esc_html( $top->name ) . '</a></h2><span>' . esc_html( 1 === (int) $top->count ? __( '1 listing', 'bigspy-ai-theme-wp' ) : sprintf( /* translators: %s: number */ __( '%s listings', 'bigspy-ai-theme-wp' ), number_format_i18n( $top->count ) ) ) . '</span></header>';
		if ( ! is_wp_error( $children ) && $children ) {
			$dir .= '<div class="bst-catdir__grid">';
			foreach ( array_slice( $children, 0, 60 ) as $child ) {
				$dir .= '<a href="' . esc_url( get_term_link( $child ) ) . '"><span>' . esc_html( $child->name ) . '</span><strong>' . esc_html( number_format_i18n( $child->count ) ) . '</strong></a>';
			}
			$dir .= '</div>';
		}
		$dir .= '</section>';
		if ( function_exists( 'bst_sponsored_slot' ) && 0 === $i % 2 ) {
			$dir .= bst_sponsored_slot( $i, 'categories' );
		}
	}
	$featured = function_exists( 'bst_render_listing_elements' ) ? bst_render_listing_elements( array( 'elementType' => 'featured', 'title' => __( 'Featured', 'bigspy-ai-theme-wp' ), 'count' => 4, 'showSponsored' => false, 'orderBy' => bst_category_featured_order() ) ) : '';
	return '<div class="bst-catdir"><nav class="bst-catdir__nav" aria-label="' . esc_attr__( 'Categories', 'bigspy-ai-theme-wp' ) . '"><ul>' . $nav . '</ul></nav><div class="bst-catdir__main">' . $featured . $dir . '</div></div>';
}

add_filter(
	'the_content',
	function ( $content ) {
		if ( 'categories' !== bst_collection_context() || ! in_the_loop() || ! is_main_query() || ! bst_feature_get( 'listing', 'category_dir', 1 ) || '' !== trim( wp_strip_all_tags( (string) $content ) ) ) {
			return $content;
		}
		return $content . bst_category_directory_html();
	},
	30
);

/** [bigspy_category_directory]: the category directory placed by hand, so the Categories page can have its own hero above it. */
add_shortcode(
	'bigspy_category_directory',
	function () {
		return bst_category_directory_html();
	}
);

/** Search, category and sort toolbar for listing archives. */
function bst_shop_toolbar_html() {
	$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	$cat  = isset( $_GET['product_cat'] ) ? sanitize_title( wp_unslash( $_GET['product_cat'] ) ) : ( is_product_category() ? get_queried_object()->slug : '' ); // phpcs:ignore WordPress.Security.NonceVerification
	$sort = isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : 'date'; // phpcs:ignore WordPress.Security.NonceVerification
	$opts = '<option value="">' . esc_html__( 'All categories', 'bigspy-ai-theme-wp' ) . '</option>';
	$cats = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'orderby' => 'name', 'parent' => 0 ) );
	foreach ( is_wp_error( $cats ) ? array() : $cats as $term ) {
		$opts .= '<option value="' . esc_attr( $term->slug ) . '"' . selected( $cat, $term->slug, false ) . '>' . esc_html( $term->name ) . '</option>';
	}
	$sorts = array(
		'date'       => __( 'Newest first', 'bigspy-ai-theme-wp' ),
		'popularity' => __( 'Most popular', 'bigspy-ai-theme-wp' ),
		'rating'     => __( 'Top rated', 'bigspy-ai-theme-wp' ),
		'title'      => __( 'Name A–Z', 'bigspy-ai-theme-wp' ),
	);
	$sort_opts = '';
	foreach ( $sorts as $value => $label ) {
		$sort_opts .= '<option value="' . esc_attr( $value ) . '"' . selected( $sort, $value, false ) . '>' . esc_html( $label ) . '</option>';
	}
	global $wp_query;
	$out  = '<form class="bst-shopbar" method="get" action="' . esc_url( $shop ) . '" role="search">';
	$out .= '<label><span>' . esc_html__( 'Search tools', 'bigspy-ai-theme-wp' ) . '</span><input type="search" name="s" value="' . esc_attr( get_search_query() ) . '" placeholder="' . esc_attr__( 'Tool name or keyword', 'bigspy-ai-theme-wp' ) . '"></label>';
	$out .= '<label><span>' . esc_html__( 'Category', 'bigspy-ai-theme-wp' ) . '</span><select name="product_cat">' . $opts . '</select></label>';
	$out .= '<label><span>' . esc_html__( 'Sort by', 'bigspy-ai-theme-wp' ) . '</span><select name="orderby">' . $sort_opts . '</select></label>';
	$out .= '<input type="hidden" name="post_type" value="product"><button type="submit">' . esc_html__( 'Find tools', 'bigspy-ai-theme-wp' ) . '</button>';
	$out .= '<p class="bst-shopbar__count">' . esc_html( 1 === (int) $wp_query->found_posts ? __( '1 listing found', 'bigspy-ai-theme-wp' ) : sprintf( /* translators: %s: number */ __( '%s listings found', 'bigspy-ai-theme-wp' ), number_format_i18n( (int) $wp_query->found_posts ) ) ) . ( get_search_query() || $cat ? ' <a href="' . esc_url( $shop ) . '">' . esc_html__( 'Clear filters', 'bigspy-ai-theme-wp' ) . '</a>' : '' ) . '</p>';
	return $out . '</form>';
}

add_action(
	'wp',
	function () {
		if ( ! function_exists( 'is_shop' ) || ! ( is_shop() || is_product_taxonomy() ) || ! bst_feature_get( 'listing', 'shop_toolbar', 1 ) ) {
			return;
		}
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
		add_action(
			'woocommerce_before_shop_loop',
			function () {
				echo bst_shop_toolbar_html(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped while building.
			},
			15
		);
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( '' === bst_collection_context() && ! ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) ) {
			return;
		}
		wp_enqueue_style( 'bst-listing', BST_URI . '/assets/css/listing.css', array(), bst_asset_version( 'assets/css/listing.css' ) );
	}
);
