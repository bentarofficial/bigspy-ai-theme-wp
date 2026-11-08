<?php
/**
 * Listing data read from product meta. ACF is optional: every value falls back to post meta.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** The 21 listing fields of the old theme (ACF group_bigspy_listing): meta key => type. */
function bst_listing_fields() {
	return array(
		'tool_tagline'    => 'text',
		'pricing_model'   => 'text',
		'rating'          => 'number',
		'starting_price'  => 'text',
		'free_trial'      => 'bool',
		'api_available'   => 'bool',
		'api_url'         => 'url',
		'company_name'    => 'text',
		'founded_year'    => 'number',
		'key_features'    => 'textarea',
		'pros'            => 'textarea',
		'cons'            => 'textarea',
		'integrations'    => 'textarea',
		'faq'             => 'textarea',
		'source_url'      => 'url',
		'ai_generated'    => 'bool',
		'ai_model'        => 'text',
		'ai_generated_at' => 'text',
		'official_url'    => 'url',
		'homepage_url'    => 'url',
		'affiliate_url'   => 'url',
	);
}

function bst_listing_id( $post_id ) {
	return $post_id ? (int) $post_id : (int) get_the_ID();
}

function bst_listing_field( $key, $post_id = 0, $default = '' ) {
	$post_id = bst_listing_id( $post_id );
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $key, $post_id );
		if ( null !== $value && '' !== $value && array() !== $value && false !== $value ) {
			return $value;
		}
	}
	$value = get_post_meta( $post_id, $key, true );
	return ( '' !== $value && array() !== $value ) ? $value : $default;
}

/** A company paid for the listing: it always shows a "Sponsored" label and disclosure. */
function bst_listing_is_paid( $post_id = 0 ) {
	return '1' === (string) get_post_meta( bst_listing_id( $post_id ), '_bigspy_paid_listing', true );
}

/** Where the listing's button goes: affiliate, official site, external product URL, then the listing page. */
function bst_listing_url( $post_id = 0 ) {
	$post_id = bst_listing_id( $post_id );
	$url     = '';
	if ( function_exists( 'wc_get_product' ) ) {
		$product = wc_get_product( $post_id );
		if ( $product && $product->is_type( 'external' ) ) {
			$url = $product->get_product_url();
		}
	}
	if ( ! $url ) {
		$url = bst_listing_field( 'official_url', $post_id, '' );
	}
	$url = apply_filters( 'bigspy_listing_url', $url ? $url : get_permalink( $post_id ), $post_id );
	return (string) apply_filters( 'bst_listing_url', $url, $post_id );
}

/** The listing's outbound links earn a commission. */
function bst_listing_has_affiliate( $post_id = 0 ) {
	$post_id = bst_listing_id( $post_id );
	if ( '' !== (string) bst_listing_field( 'affiliate_url', $post_id, '' ) || '' !== (string) get_post_meta( $post_id, '_bigspy_affiliate_url', true ) ) {
		return true;
	}
	// Older listings only store the tracked link as the external product URL.
	$url = bst_listing_url( $post_id );
	return '' !== (string) wp_parse_url( $url, PHP_URL_QUERY ) && false === strpos( $url, (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
}

/** Label/value rows from _bigspy_dynamic_attributes (JSON, "Label|Value" lines or an array). */
function bst_listing_attributes( $post_id = 0 ) {
	$post_id = bst_listing_id( $post_id );
	$raw     = '';
	foreach ( array( '_bigspy_dynamic_attributes', 'bigspy_dynamic_attributes', 'dynamic_attributes', 'listing_attributes' ) as $key ) {
		$raw = get_post_meta( $post_id, $key, true );
		if ( '' !== $raw && array() !== $raw && null !== $raw ) {
			break;
		}
		if ( function_exists( 'get_field' ) ) {
			$acf = get_field( $key, $post_id );
			if ( null !== $acf && '' !== $acf && array() !== $acf && false !== $acf ) {
				$raw = $acf;
				break;
			}
		}
	}
	if ( is_string( $raw ) ) {
		$decoded = json_decode( trim( $raw ), true );
		if ( JSON_ERROR_NONE === json_last_error() ) {
			$raw = $decoded;
		} else {
			$rows = array();
			foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $row ) {
				$parts = explode( '|', $row, 2 );
				if ( 2 === count( $parts ) ) {
					$rows[] = array( 'label' => trim( $parts[0] ), 'value' => trim( $parts[1] ) );
				}
			}
			$raw = $rows;
		}
	}
	$attrs = array();
	foreach ( is_array( $raw ) ? $raw : array() as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}
		$label = $item['label'] ?? ( $item['name'] ?? '' );
		$value = $item['value'] ?? ( $item['content'] ?? '' );
		if ( is_array( $value ) ) {
			$value = implode( ', ', array_filter( array_map( 'wp_strip_all_tags', $value ) ) );
		}
		$label = trim( wp_strip_all_tags( (string) $label ) );
		$value = trim( wp_strip_all_tags( (string) $value ) );
		if ( '' === $label || '' === $value ) {
			continue;
		}
		$attrs[] = array(
			'label' => $label,
			'value' => $value,
			'icon'  => isset( $item['icon'] ) ? sanitize_key( (string) $item['icon'] ) : '',
			'type'  => isset( $item['type'] ) ? sanitize_key( (string) $item['type'] ) : 'text',
			'order' => isset( $item['order'] ) ? (int) $item['order'] : count( $attrs ),
		);
	}
	usort(
		$attrs,
		function ( $a, $b ) {
			return $a['order'] <=> $b['order'];
		}
	);
	return apply_filters( 'bigspy_dynamic_listing_attributes', $attrs, $post_id );
}

/** "From USD 10 / month", "Contact for pricing"… built from the pricing_* meta. */
function bst_listing_price_label( $post_id = 0 ) {
	$post_id = bst_listing_id( $post_id );
	$get     = function ( $key ) use ( $post_id ) {
		return (string) bst_listing_field( $key, $post_id, '' );
	};
	if ( '' !== $get( 'price_custom_text' ) ) {
		return $get( 'price_custom_text' );
	}
	$types  = array(
		'starting_at'      => __( 'From', 'bigspy-ai-theme-wp' ),
		'from'             => __( 'From', 'bigspy-ai-theme-wp' ),
		'price'            => '',
		'price_range'      => '',
		'custom'           => '',
		'contact'          => __( 'Contact for pricing', 'bigspy-ai-theme-wp' ),
		'quote'            => __( 'Quote required', 'bigspy-ai-theme-wp' ),
		'variable'         => __( 'Price varies', 'bigspy-ai-theme-wp' ),
		'price_on_website' => __( 'Price on website', 'bigspy-ai-theme-wp' ),
	);
	$type   = sanitize_key( $get( 'pricing_type' ) );
	$prefix = isset( $types[ $type ] ) ? $types[ $type ] : $get( 'pricing_model' );
	$value  = '' !== $get( 'price_min' ) && '' !== $get( 'price_max' ) ? $get( 'price_min' ) . '–' . $get( 'price_max' ) : $get( 'starting_price' );
	if ( '' !== $value && '' !== $get( 'price_currency' ) ) {
		$value = strtoupper( $get( 'price_currency' ) ) . ' ' . $value;
	}
	if ( '' !== $value && '' !== $get( 'price_unit' ) ) {
		$value .= ' / ' . $get( 'price_unit' );
	}
	return trim( $prefix . ( $prefix && '' !== $value ? ' ' : '' ) . $value );
}

/** Editorial rating (0–5), or the WooCommerce review average. */
function bst_listing_rating( $post_id = 0 ) {
	$post_id = bst_listing_id( $post_id );
	$rating  = bst_listing_field( 'rating', $post_id, '' );
	if ( '' === $rating && function_exists( 'wc_get_product' ) ) {
		$product = wc_get_product( $post_id );
		$rating  = $product ? $product->get_average_rating() : '';
	}
	return '' !== $rating ? max( 0, min( 5, (float) $rating ) ) : 0;
}

function bst_listing_terms( $post_id, $taxonomy ) {
	$terms = get_the_terms( $post_id, $taxonomy );
	return is_array( $terms ) ? $terms : array();
}

/** Facts table rows: dynamic attributes first, else the fixed fields of older listings. */
function bst_listing_facts( $post_id = 0 ) {
	$post_id  = bst_listing_id( $post_id );
	$category = bst_listing_terms( $post_id, 'product_cat' );
	$rating   = bst_listing_rating( $post_id );
	$facts    = bst_listing_attributes( $post_id );
	if ( ! $facts ) {
		$add = function ( $label, $value ) use ( &$facts ) {
			if ( '' !== (string) $value ) {
				$facts[] = array( 'label' => $label, 'value' => (string) $value );
			}
		};
		$add( __( 'Pricing', 'bigspy-ai-theme-wp' ), bst_listing_price_label( $post_id ) );
		$add( __( 'Company', 'bigspy-ai-theme-wp' ), bst_listing_field( 'company_name', $post_id, '' ) );
		$add( __( 'Founded', 'bigspy-ai-theme-wp' ), bst_listing_field( 'founded_year', $post_id, '' ) );
		foreach ( array( 'free_trial' => __( 'Free trial', 'bigspy-ai-theme-wp' ), 'api_available' => __( 'API', 'bigspy-ai-theme-wp' ) ) as $key => $label ) {
			$value = bst_listing_field( $key, $post_id, '' );
			$add( $label, '' === $value ? '' : ( $value ? __( 'Yes', 'bigspy-ai-theme-wp' ) : __( 'No', 'bigspy-ai-theme-wp' ) ) );
		}
	}
	if ( $category ) {
		$facts[] = array( 'label' => __( 'Category', 'bigspy-ai-theme-wp' ), 'value' => $category[0]->name );
	}
	if ( $rating ) {
		$facts[] = array( 'label' => __( 'Rating', 'bigspy-ai-theme-wp' ), 'value' => number_format_i18n( $rating, 1 ) . ' / 5' );
	}
	return $facts;
}

/** FAQ rows from the faq field ("Question | Answer" per line, or ACF rows). */
function bst_listing_faq( $post_id = 0 ) {
	$faq   = bst_listing_field( 'faq', $post_id, array() );
	$faq   = is_string( $faq ) ? preg_split( '/\r\n|\r|\n/', $faq ) : (array) $faq;
	$items = array();
	foreach ( $faq as $row ) {
		if ( is_array( $row ) ) {
			$question = $row['question'] ?? ( $row['q'] ?? '' );
			$answer   = $row['answer'] ?? ( $row['a'] ?? '' );
		} else {
			$parts    = explode( '|', (string) $row, 2 );
			$question = $parts[0];
			$answer   = $parts[1] ?? '';
		}
		$question = trim( wp_strip_all_tags( (string) $question ) );
		$answer   = trim( (string) $answer );
		if ( '' !== $question && '' !== trim( wp_strip_all_tags( $answer ) ) ) {
			$items[] = array( 'question' => $question, 'answer' => $answer );
		}
	}
	return $items;
}

/** Disclosure line for paid and affiliate listings, shown next to the call to action. */
function bst_listing_disclosure( $post_id = 0 ) {
	$post_id = bst_listing_id( $post_id );
	$lines   = array();
	if ( bst_listing_is_paid( $post_id ) ) {
		$sponsor = trim( (string) get_post_meta( $post_id, '_bigspy_sponsor_name', true ) );
		$sponsor = '' !== $sponsor ? $sponsor : trim( wp_strip_all_tags( (string) bst_listing_field( 'company_name', $post_id, '' ) ) );
		$sponsor = '' !== $sponsor ? $sponsor : wp_strip_all_tags( get_the_title( $post_id ) );
		/* translators: 1: sponsor name, 2: site name */
		$lines[] = '<strong>' . esc_html__( 'Sponsored listing.', 'bigspy-ai-theme-wp' ) . '</strong> ' . esc_html( sprintf( __( '%1$s paid to be listed on %2$s. Our editorial rules still apply.', 'bigspy-ai-theme-wp' ), $sponsor, get_bloginfo( 'name' ) ) );
	}
	if ( bst_listing_has_affiliate( $post_id ) ) {
		$text = esc_html__( 'We may earn a commission if you buy through links on this page, at no extra cost to you. This does not affect our editorial review.', 'bigspy-ai-theme-wp' );
		$page = get_page_by_path( 'affiliate-disclosure' );
		if ( $page && 'publish' === $page->post_status ) {
			$text .= ' <a href="' . esc_url( get_permalink( $page ) ) . '">' . esc_html__( 'Learn more', 'bigspy-ai-theme-wp' ) . '</a>';
		}
		$lines[] = $text;
	}
	return $lines ? '<p class="bst-listing-disclosure">' . implode( ' ', $lines ) . '</p>' : '';
}

function bst_listing_rating_html( $post_id = 0 ) {
	$rating = bst_listing_rating( $post_id );
	if ( ! $rating ) {
		return '';
	}
	/* translators: %s: rating number */
	return '<span class="bst-listing-rating" aria-label="' . esc_attr( sprintf( __( 'Rating %s out of 5', 'bigspy-ai-theme-wp' ), number_format_i18n( $rating, 1 ) ) ) . '"><span aria-hidden="true">★</span> ' . esc_html( number_format_i18n( $rating, 1 ) ) . '</span>';
}

/** Listing summary: tagline, rating, price, facts, button and disclosure. Used by the block and product pages. */
function bst_listing_info_html( $post_id = 0, $args = array() ) {
	$post_id = bst_listing_id( $post_id );
	if ( ! $post_id || 'product' !== get_post_type( $post_id ) ) {
		return '';
	}
	$args    = wp_parse_args( $args, array( 'tagline' => true, 'facts' => true, 'button' => true, 'limit' => 8 ) );
	$tagline = (string) bst_listing_field( 'tool_tagline', $post_id, '' );
	$price   = bst_listing_price_label( $post_id );
	$facts   = $args['facts'] ? array_slice( bst_listing_facts( $post_id ), 0, max( 1, (int) $args['limit'] ) ) : array();
	$out     = '<div class="bst-listing-info">';
	if ( bst_listing_is_paid( $post_id ) ) {
		$out .= '<span class="bst-listing-badge">' . esc_html__( 'Sponsored', 'bigspy-ai-theme-wp' ) . '</span>';
	}
	if ( $args['tagline'] && '' !== $tagline ) {
		$out .= '<p class="bst-listing-tagline">' . esc_html( $tagline ) . '</p>';
	}
	$meta = bst_listing_rating_html( $post_id ) . ( '' !== $price ? '<span class="bst-listing-price">' . esc_html( $price ) . '</span>' : '' );
	$out .= '' !== $meta ? '<div class="bst-listing-meta">' . $meta . '</div>' : '';
	if ( $facts ) {
		$out .= '<dl class="bst-listing-facts">';
		foreach ( $facts as $fact ) {
			$out .= '<div><dt>' . esc_html( $fact['label'] ) . '</dt><dd>' . esc_html( $fact['value'] ) . '</dd></div>';
		}
		$out .= '</dl>';
	}
	$url = bst_listing_url( $post_id );
	if ( $args['button'] && $url && get_permalink( $post_id ) !== $url ) {
		$rel  = bst_listing_has_affiliate( $post_id ) || bst_listing_is_paid( $post_id ) ? 'sponsored noopener' : 'noopener';
		$out .= '<p><a class="bst-listing-cta" href="' . esc_url( $url ) . '" target="_blank" rel="' . esc_attr( $rel ) . '">' . esc_html__( 'Visit website', 'bigspy-ai-theme-wp' ) . '</a></p>';
	}
	return $out . bst_listing_disclosure( $post_id ) . '</div>';
}
