<?php
/**
 * Listing page schema: ItemPage, BreadcrumbList, the listed brand as Organization and
 * FAQPage. No Product schema: listings are directory entries without offers.
 * Added to Rank Math's graph when it is active, printed on its own otherwise.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Rank Math primary category, else the deepest product category. */
function bst_listing_primary_category( $post_id ) {
	$primary = absint( get_post_meta( $post_id, 'rank_math_primary_product_cat', true ) );
	$best    = null;
	$depth   = -1;
	foreach ( bst_listing_terms( $post_id, 'product_cat' ) as $term ) {
		if ( (int) $term->term_id === $primary ) {
			return $term;
		}
		$d = count( get_ancestors( $term->term_id, 'product_cat', 'taxonomy' ) );
		if ( $d > $depth ) {
			$best  = $term;
			$depth = $d;
		}
	}
	return $best;
}

function bst_listing_schema_nodes( $post_id ) {
	$link   = get_permalink( $post_id );
	$home   = home_url( '/' );
	$title  = wp_strip_all_tags( get_the_title( $post_id ) );
	$crumbs = array( array( get_bloginfo( 'name' ), $home ) );
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		$crumbs[] = array( __( 'Listings', 'bigspy-ai-theme-wp' ), wc_get_page_permalink( 'shop' ) );
	}
	$category = bst_listing_primary_category( $post_id );
	if ( $category ) {
		$chain   = array_reverse( get_ancestors( $category->term_id, 'product_cat', 'taxonomy' ) );
		$chain[] = $category->term_id;
		foreach ( $chain as $term_id ) {
			$term = get_term( $term_id, 'product_cat' );
			$url  = $term && ! is_wp_error( $term ) ? get_term_link( $term ) : '';
			if ( $url && ! is_wp_error( $url ) ) {
				// Rank Math HTML-encodes "&" in JSON-LD strings, so spell it out.
				$crumbs[] = array( str_replace( ' & ', ' and ', html_entity_decode( html_entity_decode( $term->name, ENT_QUOTES, 'UTF-8' ), ENT_QUOTES, 'UTF-8' ) ), $url );
			}
		}
	}
	$crumbs[] = array( $title, $link );
	$items    = array();
	foreach ( $crumbs as $i => $crumb ) {
		$items[] = array( '@type' => 'ListItem', 'position' => $i + 1, 'name' => $crumb[0], 'item' => $crumb[1] );
	}
	$nodes = array( 'bigspy_listing_breadcrumb' => array( '@type' => 'BreadcrumbList', '@id' => $link . '#breadcrumb', 'itemListElement' => $items ) );

	// The listed brand's own site, never the affiliate link, without query strings.
	$brand_url = '';
	foreach ( array( 'homepage_url', 'source_url', '_bigspy_tool_url' ) as $key ) {
		$candidate = (string) bst_listing_field( $key, $post_id, '' );
		if ( '' !== $candidate && wp_http_validate_url( $candidate ) ) {
			$brand_url = strtok( $candidate, '?#' );
			break;
		}
	}
	if ( '' === $brand_url && class_exists( 'BSI_Domain' ) && is_callable( array( 'BSI_Domain', 'for_post' ) ) ) {
		$domain    = BSI_Domain::for_post( $post_id, false );
		$brand_url = '' !== $domain ? 'https://' . $domain . '/' : '';
	}
	$company = wp_strip_all_tags( (string) bst_listing_field( 'company_name', $post_id, '' ) );
	$brand   = array( '@type' => 'Organization', '@id' => $link . '#listed-organization', 'name' => '' !== $company ? $company : $title );
	if ( '' !== $brand_url ) {
		$brand['url'] = $brand_url;
	}
	$nodes['bigspy_listing_organization'] = $brand;

	$description = (string) get_post_meta( $post_id, 'rank_math_description', true );
	$description = '' !== $description ? $description : wp_strip_all_tags( get_post_field( 'post_excerpt', $post_id ) );
	$page        = array(
		'@type'         => 'ItemPage',
		'@id'           => $link . '#webpage',
		'url'           => $link,
		'name'          => $title,
		'inLanguage'    => get_bloginfo( 'language' ),
		'isPartOf'      => array( '@type' => 'WebSite', '@id' => $home . '#website', 'url' => $home, 'name' => get_bloginfo( 'name' ) ),
		'breadcrumb'    => array( '@id' => $link . '#breadcrumb' ),
		'about'         => array( '@id' => $link . '#listed-organization' ),
		'datePublished' => get_post_time( DATE_W3C, true, $post_id ),
		'dateModified'  => get_post_modified_time( DATE_W3C, true, $post_id ),
	);
	if ( '' !== $description ) {
		$page['description'] = $description;
	}
	$image = get_the_post_thumbnail_url( $post_id, 'full' );
	if ( $image ) {
		$page['primaryImageOfPage'] = array( '@type' => 'ImageObject', 'url' => $image );
	}
	$nodes['bigspy_listing_webpage'] = $page;
	return $nodes;
}

/** FAQPage for the FAQ shown on the page. */
function bst_listing_faq_schema( $post_id ) {
	$entities = array();
	foreach ( bst_listing_faq( $post_id ) as $item ) {
		$answer = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( strip_shortcodes( $item['answer'] ) ) ) );
		if ( '' !== $answer ) {
			$entities[] = array( '@type' => 'Question', 'name' => $item['question'], 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $answer ) );
		}
	}
	return $entities ? array( '@type' => 'FAQPage', '@id' => get_permalink( $post_id ) . '#faq', 'mainEntity' => $entities ) : array();
}

add_filter(
	'rank_math/json_ld',
	function ( $data ) {
		if ( ! is_array( $data ) || ! function_exists( 'is_product' ) || ! is_product() ) {
			return $data;
		}
		$post_id = get_queried_object_id();
		$present = array();
		foreach ( $data as $node ) {
			$present = array_merge( $present, is_array( $node ) && isset( $node['@type'] ) ? (array) $node['@type'] : array() );
		}
		$faq = in_array( 'FAQPage', $present, true ) ? array() : bst_listing_faq_schema( $post_id );
		if ( $faq ) {
			$data['bigspy_listing_faq'] = $faq;
		}
		// Keep richer page or breadcrumb schema Rank Math already made.
		if ( ! array_intersect( array( 'WebPage', 'ItemPage', 'BreadcrumbList' ), $present ) ) {
			$data = array_merge( $data, bst_listing_schema_nodes( $post_id ) );
		}
		return $data;
	},
	99
);

add_action(
	'wp_head',
	function () {
		if ( defined( 'RANK_MATH_VERSION' ) || ! function_exists( 'is_product' ) || ! is_product() ) {
			return;
		}
		$post_id = get_queried_object_id();
		$graph   = array_values( bst_listing_schema_nodes( $post_id ) );
		$faq     = bst_listing_faq_schema( $post_id );
		if ( $faq ) {
			$graph[] = $faq;
		}
		echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG ) . '</script>' . "\n";
	},
	99
);
