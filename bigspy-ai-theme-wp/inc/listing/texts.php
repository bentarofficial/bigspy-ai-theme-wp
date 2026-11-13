<?php
/**
 * Listing texts: every word BIGSPY AI List mode shows to visitors, editable in
 * Customizer > Layout > Listing texts. "Call a listing" (Listings, Tools, Products…)
 * builds the default of each text; an empty field keeps that default, which still goes
 * through __() so translations work. Text typed by the user is printed as typed.
 *
 * @package BigSpyAITheme
 */

defined( 'ABSPATH' ) || exit;

/** Ready-made names for a listing: choice => array( singular, plural, label in the Customizer ). */
function bst_listing_nouns() {
	return array(
		'listings'  => array( __( 'listing', 'bigspy-ai-theme-wp' ), __( 'listings', 'bigspy-ai-theme-wp' ), __( 'Listings', 'bigspy-ai-theme-wp' ) ),
		'tools'     => array( __( 'tool', 'bigspy-ai-theme-wp' ), __( 'tools', 'bigspy-ai-theme-wp' ), __( 'Tools', 'bigspy-ai-theme-wp' ) ),
		'products'  => array( __( 'product', 'bigspy-ai-theme-wp' ), __( 'products', 'bigspy-ai-theme-wp' ), __( 'Products', 'bigspy-ai-theme-wp' ) ),
		'services'  => array( __( 'service', 'bigspy-ai-theme-wp' ), __( 'services', 'bigspy-ai-theme-wp' ), __( 'Services', 'bigspy-ai-theme-wp' ) ),
		'places'    => array( __( 'place', 'bigspy-ai-theme-wp' ), __( 'places', 'bigspy-ai-theme-wp' ), __( 'Places', 'bigspy-ai-theme-wp' ) ),
		'companies' => array( __( 'company', 'bigspy-ai-theme-wp' ), __( 'companies', 'bigspy-ai-theme-wp' ), __( 'Companies', 'bigspy-ai-theme-wp' ) ),
	);
}

/** What the site calls a listing: array( singular, plural ). */
function bst_listing_noun() {
	$nouns  = bst_listing_nouns();
	$choice = (string) get_theme_mod( 'lt_noun', 'listings' );
	if ( 'custom' === $choice ) {
		$one  = trim( (string) get_theme_mod( 'lt_noun_singular', '' ) );
		$many = trim( (string) get_theme_mod( 'lt_noun_plural', '' ) );
		if ( '' !== $one || '' !== $many ) {
			$one  = '' !== $one ? $one : $many;
			$many = '' !== $many ? $many : $one;
			return array( bst_listing_text_translate( 'noun_singular', $one ), bst_listing_text_translate( 'noun_plural', $many ) );
		}
	}
	$pair = $nouns[ $choice ] ?? $nouns['listings'];
	return array( $pair[0], $pair[1] );
}

/** First letter in upper case, also for non-Latin text. */
function bst_listing_ucfirst( $text ) {
	$text = (string) $text;
	return function_exists( 'mb_substr' ) ? mb_strtoupper( mb_substr( $text, 0, 1 ) ) . mb_substr( $text, 1 ) : ucfirst( $text );
}

/** Sponsored button text saved in Features > BIGSPY AI List, else the old theme's, else "Open site →". */
function bst_listing_sponsored_button_default( $old ) {
	$fallback = is_array( $old ) && isset( $old['button_text'] ) ? (string) $old['button_text'] : __( 'Open site →', 'bigspy-ai-theme-wp' );
	return function_exists( 'bst_feature_get' ) ? (string) bst_feature_get( 'listing', 'sponsored_button', $fallback ) : $fallback;
}

/** Groups of the Customizer section, in order. */
function bst_listing_text_groups() {
	return array(
		'toolbar'    => __( 'Filter toolbar texts', 'bigspy-ai-theme-wp' ),
		'card'       => __( 'Listing card texts', 'bigspy-ai-theme-wp' ),
		'single'     => __( 'Listing page texts', 'bigspy-ai-theme-wp' ),
		'sponsored'  => __( 'Sponsored texts', 'bigspy-ai-theme-wp' ),
		'categories' => __( 'Category texts', 'bigspy-ai-theme-wp' ),
	);
}

/**
 * Every editable text: key => array( group, label, default, needs ).
 * "needs" lists the placeholders an override must keep; without them the default is used.
 */
function bst_listing_text_defs() {
	static $cache = array();
	list( $one, $many ) = bst_listing_noun();
	$cache_key = $one . '|' . $many . '|' . ( function_exists( 'determine_locale' ) ? determine_locale() : '' );
	// Not cached: the Sponsored button default follows the List settings, which can change.
	$old = get_option( 'bigspy_categories_promotions', array() );
	$btn = bst_listing_sponsored_button_default( $old );
	if ( isset( $cache[ $cache_key ] ) ) {
		$cache[ $cache_key ]['sponsored_button'][2] = $btn;
		return $cache[ $cache_key ];
	}
	$n   = function ( $template, $noun ) {
		return bst_listing_ucfirst( sprintf( $template, $noun ) );
	};
	$d   = array(
		// Filter toolbar.
		/* translators: %s: what the site calls its listings, plural (tools, products…) */
		'search_label'       => array( 'toolbar', __( 'Search field label', 'bigspy-ai-theme-wp' ), $n( __( 'Search %s', 'bigspy-ai-theme-wp' ), $many ) ),
		/* translators: %s: what the site calls a listing, singular (tool, product…) */
		'search_placeholder' => array( 'toolbar', __( 'Search field placeholder', 'bigspy-ai-theme-wp' ), $n( __( '%s name or keyword', 'bigspy-ai-theme-wp' ), $one ) ),
		'category_label'     => array( 'toolbar', __( 'Category field label', 'bigspy-ai-theme-wp' ), __( 'Category', 'bigspy-ai-theme-wp' ) ),
		'all_categories'     => array( 'toolbar', __( 'First category choice', 'bigspy-ai-theme-wp' ), __( 'All categories', 'bigspy-ai-theme-wp' ) ),
		'sort_label'         => array( 'toolbar', __( 'Sort field label', 'bigspy-ai-theme-wp' ), __( 'Sort by', 'bigspy-ai-theme-wp' ) ),
		'sort_date'          => array( 'toolbar', __( 'Sort: newest', 'bigspy-ai-theme-wp' ), __( 'Newest first', 'bigspy-ai-theme-wp' ) ),
		'sort_popularity'    => array( 'toolbar', __( 'Sort: popular', 'bigspy-ai-theme-wp' ), __( 'Most popular', 'bigspy-ai-theme-wp' ) ),
		'sort_rating'        => array( 'toolbar', __( 'Sort: rating', 'bigspy-ai-theme-wp' ), __( 'Top rated', 'bigspy-ai-theme-wp' ) ),
		'sort_title'         => array( 'toolbar', __( 'Sort: name', 'bigspy-ai-theme-wp' ), __( 'Name A–Z', 'bigspy-ai-theme-wp' ) ),
		/* translators: %s: what the site calls its listings, plural */
		'search_button'      => array( 'toolbar', __( 'Button', 'bigspy-ai-theme-wp' ), $n( __( 'Find %s', 'bigspy-ai-theme-wp' ), $many ) ),
		/* translators: %s: what the site calls a listing, singular */
		'count_one'          => array( 'toolbar', __( 'Result count: one result', 'bigspy-ai-theme-wp' ), sprintf( __( '1 %s found', 'bigspy-ai-theme-wp' ), $one ) ),
		/* translators: 1: number of results, 2: what the site calls its listings, plural */
		'count_many'         => array( 'toolbar', __( 'Result count: several results (keep %s for the number)', 'bigspy-ai-theme-wp' ), sprintf( __( '%1$s %2$s found', 'bigspy-ai-theme-wp' ), '%s', $many ), array( '%s' ) ),
		'clear_filters'      => array( 'toolbar', __( 'Clear filters link', 'bigspy-ai-theme-wp' ), __( 'Clear filters', 'bigspy-ai-theme-wp' ) ),
		/* translators: %s: what the site calls its listings, plural */
		'search_block'       => array( 'toolbar', __( 'Listing search block placeholder', 'bigspy-ai-theme-wp' ), $n( __( 'Search %s...', 'bigspy-ai-theme-wp' ), $many ) ),

		// Cards.
		'featured'           => array( 'card', __( 'Featured badge and heading', 'bigspy-ai-theme-wp' ), __( 'Featured', 'bigspy-ai-theme-wp' ) ),
		'card_visit'         => array( 'card', __( 'Card button', 'bigspy-ai-theme-wp' ), __( 'Visit', 'bigspy-ai-theme-wp' ) ),
		'view_more'          => array( 'card', __( 'View more link', 'bigspy-ai-theme-wp' ), __( 'View more →', 'bigspy-ai-theme-wp' ) ),
		'price_from'         => array( 'card', __( 'Price: from', 'bigspy-ai-theme-wp' ), __( 'From', 'bigspy-ai-theme-wp' ) ),
		'price_contact'      => array( 'card', __( 'Price: contact', 'bigspy-ai-theme-wp' ), __( 'Contact for pricing', 'bigspy-ai-theme-wp' ) ),
		'price_quote'        => array( 'card', __( 'Price: quote', 'bigspy-ai-theme-wp' ), __( 'Quote required', 'bigspy-ai-theme-wp' ) ),
		'price_varies'       => array( 'card', __( 'Price: varies', 'bigspy-ai-theme-wp' ), __( 'Price varies', 'bigspy-ai-theme-wp' ) ),
		'price_on_website'   => array( 'card', __( 'Price: on website', 'bigspy-ai-theme-wp' ), __( 'Price on website', 'bigspy-ai-theme-wp' ) ),
		/* translators: %s: rating number */
		'rating_label'       => array( 'card', __( 'Rating, read by screen readers (keep %s for the number)', 'bigspy-ai-theme-wp' ), __( 'Rating %s out of 5', 'bigspy-ai-theme-wp' ), array( '%s' ) ),

		// Listing page.
		'key_features'       => array( 'single', __( 'Key features heading', 'bigspy-ai-theme-wp' ), __( 'Key features', 'bigspy-ai-theme-wp' ) ),
		'pros_cons'          => array( 'single', __( 'Pros & Cons heading', 'bigspy-ai-theme-wp' ), __( 'Pros & Cons', 'bigspy-ai-theme-wp' ) ),
		'pros'               => array( 'single', __( 'Pros heading', 'bigspy-ai-theme-wp' ), __( 'Pros', 'bigspy-ai-theme-wp' ) ),
		'cons'               => array( 'single', __( 'Cons heading', 'bigspy-ai-theme-wp' ), __( 'Cons', 'bigspy-ai-theme-wp' ) ),
		'integrations'       => array( 'single', __( 'Integrations heading', 'bigspy-ai-theme-wp' ), __( 'Integrations', 'bigspy-ai-theme-wp' ) ),
		'faq'                => array( 'single', __( 'FAQ heading', 'bigspy-ai-theme-wp' ), __( 'Frequently asked questions', 'bigspy-ai-theme-wp' ) ),
		'related_resources'  => array( 'single', __( 'Related resources heading', 'bigspy-ai-theme-wp' ), __( 'Related resources', 'bigspy-ai-theme-wp' ) ),
		/* translators: %s: what the site calls its listings, plural */
		'related_listings'   => array( 'single', __( 'Related listings heading', 'bigspy-ai-theme-wp' ), $n( __( 'Related %s', 'bigspy-ai-theme-wp' ), $many ) ),
		'categories'         => array( 'single', __( 'Categories heading', 'bigspy-ai-theme-wp' ), __( 'Categories', 'bigspy-ai-theme-wp' ) ),
		'last_updated'       => array( 'single', __( 'Last updated label', 'bigspy-ai-theme-wp' ), __( 'Last updated:', 'bigspy-ai-theme-wp' ) ),
		'fact_pricing'       => array( 'single', __( 'Fact: pricing', 'bigspy-ai-theme-wp' ), __( 'Pricing', 'bigspy-ai-theme-wp' ) ),
		'fact_company'       => array( 'single', __( 'Fact: company', 'bigspy-ai-theme-wp' ), __( 'Company', 'bigspy-ai-theme-wp' ) ),
		'fact_founded'       => array( 'single', __( 'Fact: founded', 'bigspy-ai-theme-wp' ), __( 'Founded', 'bigspy-ai-theme-wp' ) ),
		'fact_free_trial'    => array( 'single', __( 'Fact: free trial', 'bigspy-ai-theme-wp' ), __( 'Free trial', 'bigspy-ai-theme-wp' ) ),
		'fact_api'           => array( 'single', __( 'Fact: API', 'bigspy-ai-theme-wp' ), __( 'API', 'bigspy-ai-theme-wp' ) ),
		'fact_category'      => array( 'single', __( 'Fact: category', 'bigspy-ai-theme-wp' ), __( 'Category', 'bigspy-ai-theme-wp' ) ),
		'fact_rating'        => array( 'single', __( 'Fact: rating', 'bigspy-ai-theme-wp' ), __( 'Rating', 'bigspy-ai-theme-wp' ) ),
		'yes'                => array( 'single', __( 'Fact value: yes', 'bigspy-ai-theme-wp' ), __( 'Yes', 'bigspy-ai-theme-wp' ) ),
		'no'                 => array( 'single', __( 'Fact value: no', 'bigspy-ai-theme-wp' ), __( 'No', 'bigspy-ai-theme-wp' ) ),

		// Sponsored.
		'sponsored'          => array( 'sponsored', __( 'Sponsored badge and label', 'bigspy-ai-theme-wp' ), __( 'Sponsored', 'bigspy-ai-theme-wp' ) ),
		'sponsored_by'       => array( 'sponsored', __( 'Sponsored-by pill', 'bigspy-ai-theme-wp' ), __( 'Sponsored by', 'bigspy-ai-theme-wp' ) ),
		'sponsored_button'   => array( 'sponsored', __( 'Sponsored button', 'bigspy-ai-theme-wp' ), $btn ),
		'sidebar_sponsor'    => array( 'sponsored', __( 'Sponsor heading in the sidebar', 'bigspy-ai-theme-wp' ), __( 'Sponsor', 'bigspy-ai-theme-wp' ) ),
		/* translators: %s: what the site calls a listing, singular */
		'sponsored_listing'  => array( 'sponsored', __( 'Disclosure: first words', 'bigspy-ai-theme-wp' ), $n( __( 'Sponsored %s.', 'bigspy-ai-theme-wp' ), $one ) ),
		/* translators: 1: sponsor name, 2: site name */
		'sponsored_paid'     => array( 'sponsored', __( 'Disclosure: sentence (keep %1$s for the sponsor and %2$s for the site name)', 'bigspy-ai-theme-wp' ), __( '%1$s paid to be listed on %2$s. Our editorial rules still apply.', 'bigspy-ai-theme-wp' ), array( '%1$s', '%2$s' ) ),
		'affiliate_line'     => array( 'sponsored', __( 'Affiliate disclosure', 'bigspy-ai-theme-wp' ), __( 'We may earn a commission if you buy through links on this page, at no extra cost to you. This does not affect our editorial review.', 'bigspy-ai-theme-wp' ) ),
		'learn_more'         => array( 'sponsored', __( 'Affiliate disclosure link', 'bigspy-ai-theme-wp' ), __( 'Learn more', 'bigspy-ai-theme-wp' ) ),

		// Categories.
		'view_all_cats'      => array( 'categories', __( 'View all categories link', 'bigspy-ai-theme-wp' ), __( 'View all categories →', 'bigspy-ai-theme-wp' ) ),
		/* translators: %s: what the site calls a listing, singular */
		'cat_count_one'      => array( 'categories', __( 'Listings in a category: one', 'bigspy-ai-theme-wp' ), sprintf( __( '1 %s', 'bigspy-ai-theme-wp' ), $one ) ),
		/* translators: 1: number, 2: what the site calls its listings, plural */
		'cat_count_many'     => array( 'categories', __( 'Listings in a category: several (keep %s for the number)', 'bigspy-ai-theme-wp' ), sprintf( __( '%1$s %2$s', 'bigspy-ai-theme-wp' ), '%s', $many ), array( '%s' ) ),
		/* translators: %s: site name */
		'explore'            => array( 'categories', __( 'Homepage shortcuts, read by screen readers (keep %s for the site name)', 'bigspy-ai-theme-wp' ), __( 'Explore %s', 'bigspy-ai-theme-wp' ), array( '%s' ) ),
	);
	$cache[ $cache_key ] = $d;
	return $d;
}

/** Text typed by the user, in the visitor's language when Polylang or WPML translates it. */
function bst_listing_text_translate( $key, $value ) {
	if ( function_exists( 'pll__' ) ) {
		return (string) pll__( $value );
	}
	return (string) apply_filters( 'wpml_translate_single_string', $value, 'bigspy-ai-theme-wp', 'lt_' . $key );
}

/**
 * One listing text: the Customizer override, else the default built from the listing name.
 * Extra arguments fill %s (or %1$s, %2$s) in the text.
 *
 * @param string $key     Text key from bst_listing_text_defs().
 * @param mixed  ...$args Values for the placeholders.
 * @return string
 */
function bst_listing_text( $key, ...$args ) {
	$defs = bst_listing_text_defs();
	if ( ! isset( $defs[ $key ] ) ) {
		return '';
	}
	$value = trim( (string) get_theme_mod( 'lt_' . $key, '' ) );
	foreach ( $defs[ $key ][3] ?? array() as $need ) {
		// An override that lost its placeholder would hide the number or name: use the default.
		if ( '' !== $value && false === strpos( $value, $need ) ) {
			$value = '';
		}
	}
	$value = '' !== $value ? bst_listing_text_translate( $key, $value ) : (string) $defs[ $key ][2];
	if ( $args ) {
		$args  = array_map( 'strval', $args );
		$value = strtr( $value, array( '%1$s' => $args[0], '%2$s' => $args[1] ?? '', '%s' => $args[0] ) );
	}
	return (string) apply_filters( 'bst_listing_text', $value, $key, $args );
}

/** "1 tool found" / "12 tools found", or the category count with $context 'cat'. */
function bst_listing_count_text( $count, $context = 'count' ) {
	$count = (int) $count;
	return 1 === $count ? bst_listing_text( $context . '_one' ) : bst_listing_text( $context . '_many', number_format_i18n( $count ) );
}

/** Sort choices of the filter toolbar: value => label. */
function bst_listing_sort_choices() {
	return array(
		'date'       => bst_listing_text( 'sort_date' ),
		'popularity' => bst_listing_text( 'sort_popularity' ),
		'rating'     => bst_listing_text( 'sort_rating' ),
		'title'      => bst_listing_text( 'sort_title' ),
	);
}

/** Sort choices the toolbar shows, the chosen default first in line for "selected". */
function bst_listing_toolbar_sorts() {
	$sorts = array();
	foreach ( bst_listing_sort_choices() as $value => $label ) {
		$on = bst_mod( 'lt_bar_sort_' . $value );
		if ( null === $on || $on ) {
			$sorts[ $value ] = $label;
		}
	}
	return $sorts;
}

/** Sort used when the visitor has not chosen one: '' keeps the store's own order. */
function bst_listing_toolbar_default_sort() {
	$default = (string) bst_mod( 'lt_bar_sort_default' );
	return isset( bst_listing_sort_choices()[ $default ] ) ? $default : '';
}

add_filter(
	'woocommerce_default_catalog_orderby',
	function ( $orderby ) {
		$default = bst_listing_toolbar_default_sort();
		return '' !== $default && bst_feature_get( 'listing', 'shop_toolbar', 1 ) ? $default : $orderby;
	}
);

add_filter(
	'bst_defaults',
	function ( $d ) {
		$d += array( 'lt_noun' => 'listings', 'lt_noun_singular' => '', 'lt_noun_plural' => '', 'lt_bar_sort_default' => '', 'lt_bar_cat_depth' => '1' );
		foreach ( array( 'search', 'category', 'sort', 'count', 'clear', 'sort_date', 'sort_popularity', 'sort_rating', 'sort_title' ) as $part ) {
			$d += array( 'lt_bar_' . $part => true );
		}
		// Text keys are listed by hand: building them here would translate before the locale is ready.
		return $d;
	}
);

add_filter(
	'bst_customizer_sections',
	function ( $sections ) {
		$sections['bst_listing_texts'] = array(
			'title'       => __( 'Listing texts', 'bigspy-ai-theme-wp' ),
			'panel'       => 'bst_layout',
			'priority'    => 6,
			'description' => __( 'Every word BIGSPY AI List mode shows to visitors. Choose what the site calls a listing and all texts follow; fill a field only to replace that one text. An empty field uses the text shown in grey.', 'bigspy-ai-theme-wp' ),
		);
		return $sections;
	}
);

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$s       = 'bst_listing_texts';
		$choices = array();
		foreach ( bst_listing_nouns() as $id => $noun ) {
			$choices[ $id ] = $noun[2];
		}
		$choices['custom'] = __( 'Custom', 'bigspy-ai-theme-wp' );
		$fields[]          = array( 'id' => 'lt_noun', 'section' => $s, 'type' => 'select', 'label' => __( 'Call a listing', 'bigspy-ai-theme-wp' ), 'description' => __( 'Used in the search field, button, result count and headings. Publish and reload to see the new defaults in the fields below.', 'bigspy-ai-theme-wp' ), 'choices' => $choices );
		$fields[]          = array( 'id' => 'lt_noun_singular', 'section' => $s, 'type' => 'text', 'label' => __( 'Custom name, singular', 'bigspy-ai-theme-wp' ), 'description' => __( 'Only with "Custom". Example: hotel', 'bigspy-ai-theme-wp' ) );
		$fields[]          = array( 'id' => 'lt_noun_plural', 'section' => $s, 'type' => 'text', 'label' => __( 'Custom name, plural', 'bigspy-ai-theme-wp' ), 'description' => __( 'Only with "Custom". Example: hotels', 'bigspy-ai-theme-wp' ) );

		$fields[] = array( 'id' => 'lt_h_bar', 'section' => $s, 'type' => 'heading', 'label' => __( 'Filter toolbar', 'bigspy-ai-theme-wp' ), 'description' => __( 'The bar above listings on the Listings page and category pages. With everything off, no bar is printed.', 'bigspy-ai-theme-wp' ) );
		$toggle   = function ( $id, $label ) use ( $s ) {
			return array( 'id' => 'lt_bar_' . $id, 'section' => $s, 'type' => 'checkbox', 'label' => $label );
		};
		$fields[] = $toggle( 'search', __( 'Show the search field', 'bigspy-ai-theme-wp' ) );
		$fields[] = $toggle( 'category', __( 'Show the category field', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'lt_bar_cat_depth', 'section' => $s, 'type' => 'select', 'label' => __( 'Categories in the field', 'bigspy-ai-theme-wp' ), 'choices' => array( '1' => __( 'Top level only', 'bigspy-ai-theme-wp' ), '2' => __( 'Top level and subcategories', 'bigspy-ai-theme-wp' ) ) );
		$fields[] = $toggle( 'sort', __( 'Show the sort field', 'bigspy-ai-theme-wp' ) );
		$fields[] = $toggle( 'sort_date', __( 'Sort choice: newest first', 'bigspy-ai-theme-wp' ) );
		$fields[] = $toggle( 'sort_popularity', __( 'Sort choice: most popular', 'bigspy-ai-theme-wp' ) );
		$fields[] = $toggle( 'sort_rating', __( 'Sort choice: top rated', 'bigspy-ai-theme-wp' ) );
		$fields[] = $toggle( 'sort_title', __( 'Sort choice: name A–Z', 'bigspy-ai-theme-wp' ) );
		$fields[] = array(
			'id'          => 'lt_bar_sort_default',
			'section'     => $s,
			'type'        => 'select',
			'label'       => __( 'Default sort', 'bigspy-ai-theme-wp' ),
			'description' => __( 'The order before a visitor picks one.', 'bigspy-ai-theme-wp' ),
			'choices'     => array( '' => __( 'Store default', 'bigspy-ai-theme-wp' ), 'date' => __( 'Newest first', 'bigspy-ai-theme-wp' ), 'popularity' => __( 'Most popular', 'bigspy-ai-theme-wp' ), 'rating' => __( 'Top rated', 'bigspy-ai-theme-wp' ), 'title' => __( 'Name A–Z', 'bigspy-ai-theme-wp' ) ),
		);
		$fields[] = $toggle( 'count', __( 'Show the result count', 'bigspy-ai-theme-wp' ) );
		$fields[] = $toggle( 'clear', __( 'Show the "Clear filters" link', 'bigspy-ai-theme-wp' ) );

		$groups = bst_listing_text_groups();
		$last   = '';
		foreach ( bst_listing_text_defs() as $key => $def ) {
			if ( $def[0] !== $last ) {
				$last     = $def[0];
				$fields[] = array( 'id' => 'lt_h_' . $last, 'section' => $s, 'type' => 'heading', 'label' => $groups[ $last ] );
			}
			$fields[] = array( 'id' => 'lt_' . $key, 'section' => $s, 'type' => 'text', 'label' => $def[1], 'input_attrs' => array( 'placeholder' => $def[2] ) );
		}
		return $fields;
	}
);

// Polylang and WPML: list the texts the user typed so they can be translated per language.
add_action(
	'init',
	function () {
		if ( ! is_admin() || ( ! function_exists( 'pll_register_string' ) && ! has_action( 'wpml_register_single_string' ) ) ) {
			return;
		}
		$keys = array_merge( array( 'noun_singular', 'noun_plural' ), array_keys( bst_listing_text_defs() ) );
		foreach ( $keys as $key ) {
			$value = trim( (string) get_theme_mod( 'lt_' . $key, '' ) );
			if ( '' === $value ) {
				continue;
			}
			if ( function_exists( 'pll_register_string' ) ) {
				pll_register_string( 'lt_' . $key, $value, 'BIGSPY AI Theme WP' );
			}
			do_action( 'wpml_register_single_string', 'bigspy-ai-theme-wp', 'lt_' . $key, $value );
		}
	},
	20
);
