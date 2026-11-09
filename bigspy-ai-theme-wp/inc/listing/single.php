<?php
/**
 * Listing page sections ported from the old BIGSPY AI List theme: table of contents and
 * dividers in the description, key features, pros & cons, integrations, related resources
 * (internal links written by the Generator) and the last updated date.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_feature_fields',
	function ( $fields ) {
		$fields['listing']['single_toc']      = array( 'type' => 'checkbox', 'label' => __( 'Table of contents and section dividers in the listing description', 'bigspy-ai-theme-wp' ), 'default' => 1 );
		$fields['listing']['single_sections'] = array( 'type' => 'checkbox', 'label' => __( 'Key features, pros & cons and integrations on the listing page', 'bigspy-ai-theme-wp' ), 'default' => 1 );
		$fields['listing']['single_links']    = array( 'type' => 'checkbox', 'label' => __( 'Related resources (internal links) on the listing page', 'bigspy-ai-theme-wp' ), 'default' => 1 );
		$fields['listing']['single_updated']  = array( 'type' => 'checkbox', 'label' => __( 'Last updated date on the listing page', 'bigspy-ai-theme-wp' ), 'default' => 1 );
		return $fields;
	}
);

/** Lines of a list field: "a\nb", ACF rows or an array of strings. */
function bst_listing_lines( $key, $post_id = 0 ) {
	$value = bst_listing_field( $key, $post_id, array() );
	$lines = array();
	foreach ( (array) $value as $row ) {
		$row = is_array( $row ) ? (string) ( $row['label'] ?? ( $row['text'] ?? ( $row['name'] ?? reset( $row ) ) ) ) : (string) $row;
		foreach ( preg_split( '/\r\n|\r|\n/', $row ) as $line ) {
			$line = trim( wp_strip_all_tags( $line ) );
			if ( '' !== $line ) {
				$lines[] = $line;
			}
		}
	}
	return $lines;
}

/** Up to 8 same-site links from _bigspy_internal_links (written by the Generator). */
function bst_listing_internal_links( $post_id = 0 ) {
	$links = get_post_meta( bst_listing_id( $post_id ), '_bigspy_internal_links', true );
	$home  = wp_parse_url( home_url( '/' ), PHP_URL_HOST );
	$out   = array();
	foreach ( is_array( $links ) ? $links : array() as $link ) {
		$label = is_array( $link ) ? sanitize_text_field( $link['label'] ?? '' ) : '';
		$url   = is_array( $link ) ? esc_url_raw( $link['url'] ?? '' ) : '';
		$host  = $url ? wp_parse_url( $url, PHP_URL_HOST ) : '';
		if ( '' === $label || '' === $url || ( $host && $home && 0 !== strcasecmp( $host, $home ) ) ) {
			continue;
		}
		$out[] = array( 'label' => $label, 'url' => $url );
		if ( count( $out ) >= 8 ) {
			break;
		}
	}
	return $out;
}

/** A thin divider before every H2 but the first. */
function bst_listing_h2_dividers( $html ) {
	if ( ! is_string( $html ) || preg_match_all( '/<h2\b[^>]*>/i', $html ) < 2 ) {
		return $html;
	}
	$n = 0;
	return preg_replace_callback(
		'/<h2\b[^>]*>/i',
		function ( $m ) use ( &$n ) {
			return ( 1 === ++$n ? '' : '<hr class="bst-listing-divider" aria-hidden="true">' ) . $m[0];
		},
		$html
	);
}

/** Key features, pros & cons and integrations. */
function bst_listing_sections_html( $post_id = 0 ) {
	$features = bst_listing_lines( 'key_features', $post_id );
	$pros     = bst_listing_lines( 'pros', $post_id );
	$cons     = bst_listing_lines( 'cons', $post_id );
	$integr   = bst_listing_lines( 'integrations', $post_id );
	$list     = function ( $items ) {
		return '<ul>' . implode( '', array_map( fn( $i ) => '<li>' . esc_html( $i ) . '</li>', $items ) ) . '</ul>';
	};
	$out = '';
	if ( $features ) {
		$out .= '<section class="bst-listing-section bst-listing-features"><h2>' . esc_html__( 'Key features', 'bigspy-ai-theme-wp' ) . '</h2>' . $list( $features ) . '</section>';
	}
	if ( $pros || $cons ) {
		$out .= '<section class="bst-listing-section"><h2>' . esc_html__( 'Pros & Cons', 'bigspy-ai-theme-wp' ) . '</h2><div class="bst-listing-proscons">';
		$out .= $pros ? '<div class="bst-listing-pros"><h3>' . esc_html__( 'Pros', 'bigspy-ai-theme-wp' ) . '</h3>' . $list( $pros ) . '</div>' : '';
		$out .= $cons ? '<div class="bst-listing-cons"><h3>' . esc_html__( 'Cons', 'bigspy-ai-theme-wp' ) . '</h3>' . $list( $cons ) . '</div>' : '';
		$out .= '</div></section>';
	}
	if ( $integr ) {
		$out .= '<section class="bst-listing-section"><h2>' . esc_html__( 'Integrations', 'bigspy-ai-theme-wp' ) . '</h2><div class="bst-listing-chips">' . implode( '', array_map( fn( $i ) => '<span>' . esc_html( $i ) . '</span>', $integr ) ) . '</div></section>';
	}
	return $out;
}

/** "Related resources" list of internal links. */
function bst_listing_links_html( $post_id = 0 ) {
	$links = bst_listing_internal_links( $post_id );
	if ( ! $links ) {
		return '';
	}
	$out = '<section class="bst-listing-section bst-listing-links"><h2>' . esc_html__( 'Related resources', 'bigspy-ai-theme-wp' ) . '</h2><div>';
	foreach ( $links as $link ) {
		$out .= '<a href="' . esc_url( $link['url'] ) . '">' . esc_html( $link['label'] ) . ' <span aria-hidden="true">→</span></a>';
	}
	return $out . '</div></section>';
}

add_filter(
	'the_content',
	function ( $content ) {
		if ( ! function_exists( 'is_product' ) || ! is_product() || ! in_the_loop() || ! is_main_query() || get_the_ID() !== get_queried_object_id() || ! bst_feature_get( 'listing', 'single_toc', 1 ) ) {
			return $content;
		}
		return bst_toc_apply( bst_listing_h2_dividers( $content ), 3 );
	},
	21
);

add_action(
	'woocommerce_after_single_product_summary',
	function () {
		if ( bst_feature_get( 'listing', 'single_sections', 1 ) ) {
			echo bst_listing_sections_html( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped while building.
		}
	},
	11
);

add_action(
	'woocommerce_after_single_product_summary',
	function () {
		if ( bst_feature_get( 'listing', 'single_links', 1 ) ) {
			echo bst_listing_links_html( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped while building.
		}
		if ( bst_feature_get( 'listing', 'single_updated', 1 ) ) {
			echo '<p class="bst-listing-updated">' . esc_html__( 'Last updated:', 'bigspy-ai-theme-wp' ) . ' <time datetime="' . esc_attr( get_the_modified_date( DATE_W3C ) ) . '">' . esc_html( get_the_modified_date() ) . '</time></p>';
		}
	},
	14
);
