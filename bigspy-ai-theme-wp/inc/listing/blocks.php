<?php
/**
 * Renderers for the BIGSPY AI List blocks and the old theme's shortcodes. The blocks keep
 * the old names (bigspy/listing-grid, bigspy/category-grid, bigspy/search, bigspy/elements)
 * and attribute names, so pages built with the old theme render without edits.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Site-wide default for one card part: the old theme's saved setting when there is one. */
function bst_listing_card_default( $part, $fallback ) {
	$legacy = function_exists( 'bst_legacy_list_mod' ) ? bst_legacy_list_mod( 'bigspy_card_show_' . $part, null ) : null;
	return null === $legacy ? $fallback : (bool) $legacy;
}

/** "default" / "show" / "hide" (block setting) to true or false. */
function bst_listing_card_toggle( $value, $part, $fallback ) {
	if ( true === $value || 'show' === $value ) {
		return true;
	}
	if ( false === $value || 'hide' === $value ) {
		return false;
	}
	return bst_listing_card_default( $part, $fallback );
}

/**
 * One listing card: logo, name, category, rating, price, short text, tags, Sponsored/Featured
 * badge and a faint logo watermark. The card measures its own width (container query), so the
 * same layout fits a wide row, a narrow column or a sidebar without page-specific CSS.
 */
function bst_listing_card( $post_id, $args = array() ) {
	$args   = wp_parse_args( $args, array( 'layout' => 'square', 'showLogo' => true, 'showDescription' => true, 'showCategory' => true, 'showPricing' => true, 'showTags' => false, 'showRating' => true, 'showCTA' => false, 'showFeatured' => 'default', 'showWatermark' => 'default', 'titleLines' => 1, 'descriptionLines' => 2 ) );
	$layout = sanitize_key( $args['layout'] );
	$layout = 'horizontal-2' === $layout ? 'horizontal' : $layout;
	$layout = in_array( $layout, array( 'square', 'vertical', 'horizontal', 'featured', 'minimal' ), true ) ? $layout : 'square';
	$title  = get_the_title( $post_id );
	$link   = get_permalink( $post_id );
	$cats   = bst_listing_terms( $post_id, 'product_cat' );
	$text   = trim( wp_strip_all_tags( (string) get_post_field( 'post_excerpt', $post_id ) ) );
	$text   = '' !== $text ? $text : (string) bst_listing_field( 'tool_tagline', $post_id, '' );
	$text   = '' !== $text ? $text : wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) );
	$logo   = get_the_post_thumbnail( $post_id, 'thumbnail', array( 'class' => 'bst-lcard__logo', 'alt' => '', 'loading' => 'lazy' ) );
	$logo   = $logo ? $logo : '<span class="bst-lcard__logo bst-lcard__logo--text" aria-hidden="true">' . esc_html( mb_strtoupper( mb_substr( $title, 0, 1 ) ) ) . '</span>';
	$paid   = bst_listing_is_paid( $post_id );
	$feat   = ! $paid && bst_listing_card_toggle( $args['showFeatured'], 'featured', true ) && function_exists( 'wc_get_product' ) && ( $product = wc_get_product( $post_id ) ) && $product->is_featured(); // phpcs:ignore Generic.CodeAnalysis.AssignmentInCondition
	$mark   = 'minimal' !== $layout && bst_listing_card_toggle( $args['showWatermark'], 'watermark', true ) ? get_the_post_thumbnail( $post_id, 'medium', array( 'class' => 'bst-lcard__watermark-img', 'alt' => '', 'loading' => 'lazy' ) ) : '';
	$price  = $args['showPricing'] ? bst_listing_price_label( $post_id ) : '';
	$vars   = '--bst-lc-title-lines:' . max( 1, min( 3, (int) $args['titleLines'] ) ) . ';--bst-lc-desc-lines:' . max( 1, min( 6, (int) $args['descriptionLines'] ) );
	$out    = '<article class="bst-lcard bst-lcard--' . esc_attr( $layout ) . ( $paid ? ' is-sponsored' : '' ) . ( $feat ? ' is-featured' : '' ) . '" style="' . esc_attr( $vars ) . '">';
	$out   .= $mark ? '<span class="bst-lcard__watermark" aria-hidden="true">' . $mark . '</span>' : '';
	$out   .= '<a class="bst-lcard__cover" href="' . esc_url( $link ) . '" aria-label="' . esc_attr( $title ) . '"></a>';
	if ( $paid || $feat ) {
		$out .= '<span class="bst-lcard__badge">' . esc_html( $paid ? __( 'Sponsored', 'bigspy-ai-theme-wp' ) : __( 'Featured', 'bigspy-ai-theme-wp' ) ) . '</span>';
	}
	$out .= '<div class="bst-lcard__in">';
	$out .= $args['showLogo'] ? '<div class="bst-lcard__brand">' . $logo . '</div>' : '';
	$out .= '<div class="bst-lcard__head"><h3 class="bst-lcard__title"><a href="' . esc_url( $link ) . '">' . esc_html( $title ) . '</a></h3>';
	if ( $args['showCategory'] && $cats ) {
		$out .= '<span class="bst-lcard__cat">' . esc_html( $cats[0]->name ) . '</span>';
	}
	$out .= ( $args['showRating'] ? bst_listing_rating_html( $post_id ) : '' ) . '</div>';
	$body = '';
	if ( '' !== $price ) {
		$body .= '<span class="bst-lcard__price">' . esc_html( $price ) . '</span>';
	}
	if ( $args['showDescription'] && '' !== trim( $text ) ) {
		$body .= '<p class="bst-lcard__text">' . esc_html( wp_trim_words( $text, 40, '…' ) ) . '</p>';
	}
	if ( $args['showTags'] ) {
		$tags  = array_slice( bst_listing_terms( $post_id, 'product_tag' ), 0, 6 );
		$body .= $tags ? '<div class="bst-lcard__tags">' . implode( '', array_map( function ( $t ) { return '<span>#' . esc_html( $t->name ) . '</span>'; }, $tags ) ) . '</div>' : '';
	}
	if ( $args['showCTA'] ) {
		$url   = bst_listing_url( $post_id );
		$body .= '<a class="bst-lcard__cta" href="' . esc_url( $url ) . '"' . ( $url !== $link ? ' target="_blank" rel="sponsored noopener"' : '' ) . '>' . esc_html__( 'Visit', 'bigspy-ai-theme-wp' ) . '</a>';
	}
	$out .= '' !== $body ? '<div class="bst-lcard__body">' . $body . '</div>' : '';
	return $out . '</div></article>';
}

function bst_listing_cols_style( $a, $d, $t, $m ) {
	$c = function ( $key, $def ) use ( $a ) {
		return max( 1, min( 6, (int) ( $a[ $key ] ?? $def ) ) );
	};
	return '--bst-lc-d:' . $c( 'columnsDesktop', $d ) . ';--bst-lc-t:' . $c( 'columnsTablet', $t ) . ';--bst-lc-m:' . $c( 'columnsMobile', $m );
}

/** Grid of listing cards from WP_Query arguments. */
function bst_listing_grid( $query_args, $card_args = array(), $style = '' ) {
	$ids = get_posts( array_merge( array( 'post_type' => 'product', 'post_status' => 'publish', 'fields' => 'ids', 'no_found_rows' => true, 'suppress_filters' => false ), $query_args ) );
	if ( ! $ids ) {
		return '';
	}
	wp_enqueue_style( 'bst-listing', BST_URI . '/assets/css/listing.css', array(), bst_asset_version( 'assets/css/listing.css' ) );
	// Sponsored rows: after every N cards (adEvery), a full-width Sponsored card, as in the old theme.
	$every = max( 0, (int) ( $card_args['adEvery'] ?? 0 ) );
	$start = max( 0, (int) ( $card_args['adOffset'] ?? 0 ) );
	$out   = '';
	foreach ( $ids as $i => $id ) {
		$out .= bst_listing_card( $id, $card_args );
		$pos  = $start + $i + 1;
		if ( $every && 0 === $pos % $every && $i < count( $ids ) - 1 && function_exists( 'bst_sponsored_slot' ) ) {
			$ad   = bst_sponsored_slot( (int) ( $pos / $every ) - 1, 'elements' );
			$out .= '' !== $ad ? '<div class="bst-lgrid__ad">' . $ad . '</div>' : '';
		}
	}
	return '<div class="bst-lgrid" style="' . esc_attr( $style ) . '">' . $out . '</div>';
}

function bst_listing_tax( $filters ) {
	return count( $filters ) > 1 ? array_merge( array( 'relation' => 'AND' ), $filters ) : $filters;
}

function bst_featured_filter() {
	return array( 'taxonomy' => 'product_visibility', 'field' => 'name', 'terms' => array( 'featured' ), 'operator' => 'IN' );
}

/** bigspy/listing-grid */
function bst_render_listing_grid( $a ) {
	$layout = sanitize_key( $a['layout'] ?? 'square' );
	$limit  = max( 0, (int) ( $a['limit'] ?? 10 ) );
	$tax    = array();
	if ( ! empty( $a['categoryId'] ) ) {
		$tax[] = array( 'taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => absint( $a['categoryId'] ) );
	}
	if ( ! empty( $a['featuredOnly'] ) ) {
		$tax[] = bst_featured_filter();
	}
	$query = array( 'posts_per_page' => $limit ? min( 60, $limit ) : 60 );
	if ( $tax ) {
		$query['tax_query'] = bst_listing_tax( $tax ); // phpcs:ignore WordPress.DB.SlowDBQuery
	}
	$style = 'horizontal-2' === $layout ? '--bst-lc-d:2;--bst-lc-t:2;--bst-lc-m:1' : bst_listing_cols_style( $a, 5, 3, 2 );
	return bst_listing_grid(
		$query,
		array(
			'layout'          => 'horizontal-2' === $layout ? 'horizontal' : $layout,
			'showDescription' => $a['showDescription'] ?? true,
			'showCategory'    => $a['showCategory'] ?? true,
			'showPricing'     => $a['showPricing'] ?? true,
			'showTags'        => ! empty( $a['showTags'] ),
			'showCTA'         => ! empty( $a['showCTA'] ),
		) + bst_listing_card_style_args( $a ),
		$style
	);
}

/** bigspy/category-grid: product categories with image, name, count and description. */
function bst_render_category_grid( $a ) {
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'number'     => max( 1, min( 60, (int) ( $a['count'] ?? 8 ) ) ),
			'parent'     => (int) ( $a['parent'] ?? 0 ),
			'orderby'    => in_array( $a['orderBy'] ?? '', array( 'name', 'count' ), true ) ? $a['orderBy'] : 'name',
			'order'      => 'count' === ( $a['orderBy'] ?? '' ) ? 'DESC' : 'ASC',
			'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
		)
	);
	if ( ! is_array( $terms ) || ! $terms ) {
		return '';
	}
	wp_enqueue_style( 'bst-listing', BST_URI . '/assets/css/listing.css', array(), bst_asset_version( 'assets/css/listing.css' ) );
	$out = '';
	foreach ( $terms as $term ) {
		$img  = ( $a['showImage'] ?? true ) ? wp_get_attachment_image( (int) get_term_meta( $term->term_id, 'thumbnail_id', true ), 'thumbnail', false, array( 'alt' => '', 'loading' => 'lazy' ) ) : '';
		$out .= '<a class="bst-lcat" href="' . esc_url( get_term_link( $term ) ) . '">' . $img . '<span class="bst-lcat__name">' . esc_html( $term->name ) . '</span>';
		if ( $a['showCount'] ?? true ) {
			/* translators: %s: number of listings */
			$out .= '<span class="bst-lcat__count">' . esc_html( sprintf( _n( '%s listing', '%s listings', $term->count, 'bigspy-ai-theme-wp' ), number_format_i18n( $term->count ) ) ) . '</span>';
		}
		if ( ! empty( $a['showDescription'] ) && '' !== $term->description ) {
			$out .= '<span class="bst-lcat__desc">' . esc_html( wp_trim_words( $term->description, 18, '…' ) ) . '</span>';
		}
		$out .= '</a>';
	}
	return '<div class="bst-lcats" style="' . esc_attr( bst_listing_cols_style( $a, 5, 3, 2 ) ) . '">' . $out . '</div>';
}

/** bigspy/search: searches listings (products). */
function bst_render_listing_search( $a ) {
	$id     = wp_unique_id( 'bst-lsearch-' );
	$holder = (string) ( $a['placeholder'] ?? __( 'Search tools, software and services...', 'bigspy-ai-theme-wp' ) );
	$align  = in_array( $a['alignment'] ?? 'left', array( 'left', 'center', 'right' ), true ) ? $a['alignment'] : 'left';
	$width  = max( 240, min( 1400, (int) ( $a['maxWidth'] ?? 760 ) ) );
	wp_enqueue_style( 'bst-listing', BST_URI . '/assets/css/listing.css', array(), bst_asset_version( 'assets/css/listing.css' ) );
	$out  = '<form role="search" method="get" class="bst-lsearch bst-lsearch--' . esc_attr( $align ) . '" style="max-width:' . $width . 'px" action="' . esc_url( home_url( '/' ) ) . '">';
	$out .= '<label class="screen-reader-text" for="' . esc_attr( $id ) . '">' . esc_html__( 'Search listings', 'bigspy-ai-theme-wp' ) . '</label>';
	$out .= '<input id="' . esc_attr( $id ) . '" type="search" name="s" value="' . esc_attr( get_search_query() ) . '" placeholder="' . esc_attr( $holder ) . '"><input type="hidden" name="post_type" value="product">';
	if ( $a['showButton'] ?? true ) {
		$out .= '<button type="submit">' . esc_html( (string) ( $a['buttonText'] ?? __( 'Search', 'bigspy-ai-theme-wp' ) ) ) . '</button>';
	}
	return $out . '</form>';
}

/** Card look and Sponsored rows from block attributes (shared by the listing blocks). */
function bst_listing_card_style_args( $a, $type = '' ) {
	$cols  = max( 1, min( 6, (int) ( $a['columnsDesktop'] ?? 4 ) ) );
	$rows  = max( 0, min( 20, (int) ( $a['sponsoredEvery'] ?? 3 ) ) );
	$inner = ( $a['showSponsored'] ?? true ) && 'between' === ( $a['sponsoredPosition'] ?? 'between' ) && 'related' !== $type;
	return array(
		'showLogo'         => $a['showLogo'] ?? true,
		'showRating'       => $a['showRating'] ?? true,
		'showFeatured'     => $a['showFeatured'] ?? 'default',
		'showWatermark'    => $a['showWatermark'] ?? 'default',
		'titleLines'       => $a['titleLines'] ?? 1,
		'descriptionLines' => $a['descriptionLines'] ?? 2,
		'adEvery'          => $inner ? $rows * $cols : 0,
		'adOffset'         => max( 0, (int) ( $a['startPosition'] ?? 1 ) - 1 ),
	);
}

/** bigspy/elements: featured, latest, popular, related listings, categories or blog posts. */
function bst_render_listing_elements( $a ) {
	$type  = sanitize_key( $a['elementType'] ?? 'featured' );
	$count = max( 1, min( 40, (int) ( $a['count'] ?? 12 ) ) );
	$title = trim( (string) ( $a['title'] ?? '' ) );
	$body  = '';
	if ( in_array( $type, array( 'categories', 'category_grid' ), true ) ) {
		$body = bst_render_category_grid(
			array(
				'count'           => $a['categoryCount'] ?? 12,
				'showCount'       => $a['categoryShowCount'] ?? true,
				'showImage'       => $a['categoryShowImage'] ?? true,
				'showDescription' => $a['categoryShowDescription'] ?? false,
				'columnsDesktop'  => $a['categoryColumnsDesktop'] ?? 4,
				'columnsTablet'   => $a['categoryColumnsTablet'] ?? 2,
				'columnsMobile'   => $a['categoryColumnsMobile'] ?? 1,
			)
		);
	} elseif ( 'blog' === $type ) {
		$query = array( 'post_type' => 'post', 'posts_per_page' => $count, 'no_found_rows' => true, 'ignore_sticky_posts' => true );
		if ( ! empty( $a['blogCategoryId'] ) ) {
			$query['cat'] = absint( $a['blogCategoryId'] );
		}
		foreach ( get_posts( $query ) as $post ) {
			$body .= '<li><a href="' . esc_url( get_permalink( $post ) ) . '">' . esc_html( get_the_title( $post ) ) . '</a></li>';
		}
		$body = $body ? '<ul class="bst-lposts">' . $body . '</ul>' : '';
	} else {
		$query = array( 'posts_per_page' => $count, 'offset' => max( 0, (int) ( $a['startPosition'] ?? 1 ) - 1 ) );
		$tax   = array();
		if ( 'featured' === $type ) {
			$tax[] = bst_featured_filter();
		}
		if ( 'popular' === $type ) {
			$query = array_merge( $query, array( 'orderby' => 'date', 'order' => 'DESC' ) );
			$query = function_exists( 'bigspy_popular_query_args' ) ? bigspy_popular_query_args( $query ) : $query;
		} else {
			$query['orderby'] = in_array( $a['orderBy'] ?? 'date', array( 'date', 'title', 'modified', 'comment_count', 'rand' ), true ) ? $a['orderBy'] : 'date';
			$query['order']   = 'ASC' === strtoupper( $a['order'] ?? 'DESC' ) ? 'ASC' : 'DESC';
		}
		if ( 'related' === $type ) {
			$context = absint( $a['relatedContextId'] ?? 0 );
			$context = $context ? $context : ( is_singular( 'product' ) ? get_queried_object_id() : 0 );
			$cats    = $context ? wp_list_pluck( bst_listing_terms( $context, 'product_cat' ), 'term_id' ) : array();
			if ( ! $cats ) {
				return '';
			}
			$tax[]                  = array( 'taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $cats );
			$query['post__not_in'] = array( $context );
		} elseif ( ! empty( $a['categoryId'] ) ) {
			$tax[] = array( 'taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => absint( $a['categoryId'] ) );
		}
		if ( $tax ) {
			$query['tax_query'] = bst_listing_tax( $tax ); // phpcs:ignore WordPress.DB.SlowDBQuery
		}
		$body = bst_listing_grid(
			$query,
			array(
				'layout'          => sanitize_key( $a['layout'] ?? 'square' ),
				'showDescription' => $a['showDescription'] ?? true,
				'showCategory'    => $a['showCategory'] ?? true,
				'showPricing'     => $a['showPricing'] ?? true,
				'showTags'        => ! empty( $a['showTags'] ),
				'showCTA'         => ! empty( $a['showCTA'] ),
			) + bst_listing_card_style_args( $a, $type ),
			bst_listing_cols_style( $a, 4, 2, 1 )
		);
		if ( $body && ( $a['showSponsored'] ?? true ) && 'top' === ( $a['sponsoredPosition'] ?? 'between' ) && function_exists( 'bst_sponsored_slot' ) ) {
			$body = bst_sponsored_slot( 0, 'elements' ) . $body;
		}
	}
	if ( '' === $body ) {
		return '';
	}
	return '<div class="bst-lelements bst-lelements--' . esc_attr( $type ) . '">' . ( '' !== $title ? '<h2 class="bst-lelements__title">' . esc_html( $title ) . '</h2>' : '' ) . $body . '</div>';
}

/** Render one of the listing blocks with attributes, for the old shortcodes. */
function bst_listing_render_block( $name, $attrs = array() ) {
	return render_block( array( 'blockName' => $name, 'attrs' => $attrs, 'innerBlocks' => array(), 'innerHTML' => '', 'innerContent' => array() ) );
}

add_shortcode(
	'bigspy_search',
	function ( $atts ) {
		$atts  = shortcode_atts( array( 'placeholder' => '', 'button' => '' ), $atts, 'bigspy_search' );
		$attrs = array_filter( array( 'placeholder' => sanitize_text_field( $atts['placeholder'] ), 'buttonText' => sanitize_text_field( $atts['button'] ) ) );
		return bst_listing_render_block( 'bigspy/search', $attrs );
	}
);
add_shortcode(
	'bigspy_elements',
	function ( $atts ) {
		$atts = shortcode_atts( array( 'type' => 'featured' ), $atts, 'bigspy_elements' );
		return bst_listing_render_block( 'bigspy/elements', array( 'elementType' => sanitize_key( $atts['type'] ) ) );
	}
);
add_shortcode(
	'bigspy_categories',
	function () {
		return bst_listing_render_block( 'bigspy/elements', array( 'elementType' => 'categories' ) );
	}
);
// The old homepage "Sponsored by" brand: the Sponsored listings, rotating on every visit.
add_shortcode(
	'bigspy_home_sponsor',
	function () {
		$items = function_exists( 'bst_sponsored_items' ) && bst_sponsored_config()['on'] ? bst_sponsored_items() : array();
		if ( ! $items ) {
			return '';
		}
		// A one-line slot: sponsored.js moves it to the next sponsor on every visit, like the old theme.
		$config = bst_sponsored_config();
		return '<span class="bst-home-sponsor-slot" data-bst-sponsored="0" data-context="home" data-version="' . (int) $config['version'] . '" data-jump="1"><a class="bst-home-sponsor" data-link data-name href="' . esc_url( $items[0]['url'] ) . '" target="_blank" rel="sponsored nofollow noopener">' . esc_html( $items[0]['name'] ) . '</a></span>' . bst_sponsored_pool_html();
	}
);
