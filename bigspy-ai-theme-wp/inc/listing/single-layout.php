<?php
/**
 * Directory layout for listing pages, as in the old BIGSPY AI List theme: a compact
 * header card (logo, price, title, tagline, category, Visit button), a facts grid,
 * then the description and sections beside a sidebar (sponsor, categories, related
 * resources). Turn it off in Features to use the WooCommerce product layout.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_feature_fields',
	function ( $fields ) {
		$fields['listing']['single_layout'] = array( 'type' => 'checkbox', 'label' => __( 'Directory layout on listing pages (header card, facts and sidebar instead of the product layout)', 'bigspy-ai-theme-wp' ), 'default' => 1 );
		return $fields;
	}
);

/** True while the directory layout template is printing a listing. */
function bst_listing_layout_active() {
	return ! empty( $GLOBALS['bst_listing_layout'] );
}

add_filter(
	'template_include',
	function ( $template ) {
		if ( function_exists( 'is_product' ) && is_product() && bst_feature_get( 'listing', 'single_layout', 1 ) ) {
			return BST_DIR . '/woocommerce/single-listing.php';
		}
		return $template;
	},
	99
);

add_filter(
	'bst_defaults',
	function ( $defaults ) {
		return $defaults + array( 'listing_single_author' => false );
	}
);

add_filter(
	'bst_customizer_sections',
	function ( $sections ) {
		$sections['bst_listing_single'] = array( 'title' => __( 'Listing page', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_layout', 'priority' => 4 );
		return $sections;
	}
);

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		if ( ! is_customize_preview() && ! is_admin() ) {
			return $fields;
		}
		$fields[] = array( 'id' => 'listing_single_author', 'section' => 'bst_listing_single', 'type' => 'checkbox', 'label' => __( 'Show the author on listing pages', 'bigspy-ai-theme-wp' ), 'description' => __( 'Shows the WordPress user who published the listing, with their avatar, in the header card.', 'bigspy-ai-theme-wp' ) );
		return $fields;
	}
);

/** Author line for the listing header (off by default, Customizer → Layout → Listing page). */
function bst_listing_author_html( $post_id ) {
	if ( ! bst_mod( 'listing_single_author' ) ) {
		return '';
	}
	$uid = (int) get_post_field( 'post_author', $post_id );
	if ( ! $uid ) {
		return '';
	}
	$name = get_the_author_meta( 'display_name', $uid );
	return '<a class="bst-ls-author" href="' . esc_url( get_author_posts_url( $uid ) ) . '">' . get_avatar( $uid, 28, '', '', array( 'class' => 'bst-ls-author__avatar' ) ) . '<span>' . esc_html( $name ) . '</span></a>';
}

/** Header card of the directory layout. */
function bst_listing_header_html( $post_id ) {
	$price   = bst_listing_price_label( $post_id );
	$tagline = trim( (string) bst_listing_field( 'tool_tagline', $post_id, '' ) );
	$tagline = '' !== $tagline ? $tagline : wp_strip_all_tags( get_the_excerpt( $post_id ) );
	$cats    = bst_listing_terms( $post_id, 'product_cat' );
	$url     = bst_listing_url( $post_id );
	$thumb   = get_the_post_thumbnail( $post_id, 'medium', array( 'class' => 'bst-ls-hero__logo', 'alt' => get_the_title( $post_id ) ) );
	$out     = '<section class="bst-ls-hero">';
	$out    .= $thumb ? '<span class="bst-ls-hero__mark" aria-hidden="true">' . get_the_post_thumbnail( $post_id, 'medium', array( 'alt' => '', 'loading' => 'lazy' ) ) . '</span><div class="bst-ls-hero__visual">' . $thumb . '</div>' : '';
	$out    .= '<div class="bst-ls-hero__intro">';
	$out    .= bst_listing_is_paid( $post_id ) ? '<span class="bst-listing-badge">' . esc_html__( 'Sponsored', 'bigspy-ai-theme-wp' ) . '</span>' : '';
	$out    .= '' !== $price ? '<p class="bst-ls-hero__eyebrow">' . esc_html( $price ) . '</p>' : '';
	$out    .= '<h1 class="bst-ls-hero__title">' . esc_html( get_the_title( $post_id ) ) . '</h1>';
	$out    .= '' !== $tagline ? '<p class="bst-ls-hero__tagline">' . esc_html( $tagline ) . '</p>' : '';
	$meta    = bst_listing_rating_html( $post_id ) . ( $cats ? '<a class="bst-ls-chip" href="' . esc_url( get_term_link( $cats[0] ) ) . '">' . esc_html( $cats[0]->name ) . '</a>' : '' ) . bst_listing_author_html( $post_id );
	$out    .= '' !== $meta ? '<div class="bst-ls-hero__meta">' . $meta . '</div>' : '';
	$out    .= bst_listing_disclosure( $post_id );
	if ( $url && get_permalink( $post_id ) !== $url ) {
		$rel  = bst_listing_has_affiliate( $post_id ) || bst_listing_is_paid( $post_id ) ? 'nofollow sponsored noopener' : 'noopener';
		$out .= '<p class="bst-ls-hero__cta"><a class="bst-listing-cta" href="' . esc_url( $url ) . '" target="_blank" rel="' . esc_attr( $rel ) . '">' . esc_html__( 'Visit Website →', 'bigspy-ai-theme-wp' ) . '</a></p>';
	}
	return $out . '</div></section>';
}

/** Facts grid under the header card. */
function bst_listing_facts_html( $post_id ) {
	$facts = bst_listing_facts( $post_id );
	if ( ! $facts ) {
		return '';
	}
	$out = '<dl class="bst-ls-facts">';
	foreach ( $facts as $fact ) {
		$out .= '<div><dt>' . esc_html( $fact['label'] ) . '</dt><dd>' . esc_html( $fact['value'] ) . '</dd></div>';
	}
	return $out . '</dl>';
}

/** Sidebar: sponsor, categories and related resources. */
function bst_listing_sidebar_html( $post_id ) {
	$out = bst_listing_is_ad( $post_id ) ? '' : bst_sponsored_slot( 0, 'single' );
	$cats = bst_listing_terms( $post_id, 'product_cat' );
	if ( $cats ) {
		$out .= '<section class="bst-ls-box"><h3>' . esc_html__( 'Categories', 'bigspy-ai-theme-wp' ) . '</h3><div class="bst-listing-chips">';
		foreach ( $cats as $cat ) {
			$out .= '<a href="' . esc_url( get_term_link( $cat ) ) . '">' . esc_html( $cat->name ) . '</a>';
		}
		$out .= '</div></section>';
	}
	$links = bst_listing_internal_links( $post_id );
	if ( $links && bst_feature_get( 'listing', 'single_links', 1 ) ) {
		$out .= '<section class="bst-ls-box bst-listing-links"><h3>' . esc_html__( 'Related resources', 'bigspy-ai-theme-wp' ) . '</h3><div>';
		foreach ( $links as $link ) {
			$out .= '<a href="' . esc_url( $link['url'] ) . '">' . esc_html( $link['label'] ) . ' <span aria-hidden="true">→</span></a>';
		}
		$out .= '</div></section>';
	}
	return '' === $out ? '' : '<aside class="bst-ls-sidebar">' . $out . '</aside>';
}
