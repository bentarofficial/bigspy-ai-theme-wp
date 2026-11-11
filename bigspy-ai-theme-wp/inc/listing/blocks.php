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
	$vars   = '--bst-lc-title-lines:' . max( 1, min( 3, (int) $args['titleLines'] ) ) . ';--bst-lc-desc-lines:' . max( 1, min( 6, (int) $args['descriptionLines'] ) ) . ';--bst-lc-cta:' . ( array( 'left' => 'start', 'center' => 'center', 'right' => 'end' )[ $args['ctaAlignment'] ?? 'left' ] ?? 'start' );
	$out    = '<article class="bst-lcard bst-lcard--' . esc_attr( $layout ) . ( $args['showCTA'] && 'top' === ( $args['ctaPosition'] ?? 'bottom' ) ? ' is-cta-top' : '' ) . ( $paid ? ' is-sponsored' : '' ) . ( $feat ? ' is-featured' : '' ) . '" style="' . esc_attr( $vars ) . '">';
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
	$cta = '';
	if ( $args['showCTA'] ) {
		$url = bst_listing_url( $post_id );
		$cta = '<a class="bst-lcard__cta" href="' . esc_url( $url ) . '"' . ( $url !== $link ? ' target="_blank" rel="sponsored noopener"' : '' ) . '>' . esc_html__( 'Visit', 'bigspy-ai-theme-wp' ) . '</a>';
	}
	$top  = 'top' === ( $args['ctaPosition'] ?? 'bottom' );
	$body .= $top ? '' : $cta;
	$out  .= $top ? $cta : '';
	$out  .= '' !== $body ? '<div class="bst-lcard__body">' . $body . '</div>' : '';
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
			$ad   = bst_sponsored_slot( (int) ( $pos / $every ) - 1, 'elements', (array) ( $card_args['adOpts'] ?? array() ) );
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
	return '<div class="bst-lcats" style="' . esc_attr( bst_listing_cols_style( $a, 5, 3, 2 ) . ';' . ( $a['style'] ?? '' ) ) . '">' . $out . '</div>';
}

/** bigspy/search: searches listings (products). */
function bst_render_listing_search( $a ) {
	wp_enqueue_style( 'bst-listing', BST_URI . '/assets/css/listing.css', array(), bst_asset_version( 'assets/css/listing.css' ) );
	return bst_render_search_form( (array) $a, 'listing' );
}

/** Sponsored card options from listing block attributes (label, button, colors, sizes). */
function bst_le_sponsored_opts( $a ) {
	return array(
		'buttonStyle'     => $a['sponsoredButtonStyle'] ?? 'default',
		'buttonText'      => $a['sponsoredButtonText'] ?? '',
		'showLabel'       => $a['sponsoredShowLabel'] ?? true,
		'showDescription' => $a['sponsoredShowDescription'] ?? true,
		'openNewTab'      => $a['sponsoredOpenNewTab'] ?? 'default',
	) + ( function_exists( 'bst_sponsored_style_keys' ) ? array_intersect_key( $a, array_flip( bst_sponsored_style_keys() ) ) : array() );
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
		'adOpts'           => bst_le_sponsored_opts( $a ),
	);
}

/** Choice helper for block attributes. */
function bst_le_pick( $a, $key, $allowed, $default ) {
	$v = isset( $a[ $key ] ) ? (string) $a[ $key ] : $default;
	return in_array( $v, $allowed, true ) ? $v : $default;
}

/** "View more" link for a listing type: the category, the chosen page, or the matching collection page. */
function bst_le_view_more_url( $a, $type, $context_terms = array() ) {
	if ( ! empty( $a['categoryId'] ) && 'related' !== $type ) {
		$link = get_term_link( absint( $a['categoryId'] ), 'product_cat' );
		if ( ! is_wp_error( $link ) ) {
			return $link;
		}
	}
	$dest = sanitize_key( $a['viewMoreDestination'] ?? 'automatic' );
	if ( 'custom' === $dest && ! empty( $a['viewMoreUrl'] ) ) {
		return esc_url_raw( $a['viewMoreUrl'] );
	}
	if ( 'related' === $type && 'automatic' === $dest && $context_terms ) {
		$link = get_term_link( $context_terms[0] );
		if ( ! is_wp_error( $link ) ) {
			return $link;
		}
	}
	$key = 'automatic' === $dest ? ( in_array( $type, array( 'featured', 'popular', 'blog', 'categories' ), true ) ? $type : 'listings' ) : $dest;
	$id  = function_exists( 'bst_collection_page_id' ) ? bst_collection_page_id( $key ) : 0;
	if ( $id ) {
		return get_permalink( $id );
	}
	if ( 'listings' === $key ) {
		return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : (string) get_post_type_archive_link( 'product' );
	}
	return home_url( '/' . $key . '/' );
}

/** View more: a link, or a button that loads the next items in place (assets/js/listing-elements.js). */
function bst_le_view_more( $a, $type, $shown, $text, $url ) {
	$align = bst_le_pick( $a, 'seeMoreAlignment', array( 'left', 'center', 'right' ), 'right' );
	$text  = '' !== trim( (string) $text ) ? $text : __( 'View more →', 'bigspy-ai-theme-wp' );
	if ( 'categories' !== $type && 'load' === ( $a['viewMoreBehavior'] ?? 'redirect' ) ) {
		$next = $a;
		unset( $next['className'], $next['style'], $next['lock'], $next['metadata'] );
		$next['startPosition'] = max( 1, (int) ( $a['startPosition'] ?? 1 ) ) + $shown;
		wp_enqueue_script( 'bst-listing-elements', BST_URI . '/assets/js/listing-elements.js', array(), bst_asset_version( 'assets/js/listing-elements.js' ), true );
		return '<div class="bst-lelements__more bst-lelements__more--' . esc_attr( $align ) . '"><button type="button" class="bst-lelements__more-btn" data-bst-le-load="' . esc_attr( wp_json_encode( $next ) ) . '" data-endpoint="' . esc_url( rest_url( 'bst/v1/listing-elements' ) ) . '" data-loading="' . esc_attr__( 'Loading…', 'bigspy-ai-theme-wp' ) . '">' . esc_html( $text ) . '</button></div>';
	}
	return '' !== $url ? '<div class="bst-lelements__more bst-lelements__more--' . esc_attr( $align ) . '"><a class="bst-lelements__more-btn" href="' . esc_url( $url ) . '">' . esc_html( $text ) . '</a></div>' : '';
}

/** Blog posts for bigspy/elements: cards, compact rows or a numbered title list. */
function bst_le_blog( $a, $count ) {
	$layout = bst_le_pick( $a, 'blogLayout', array( 'cards', 'compact', 'titles' ), 'cards' );
	$layout = 'homepage-sidebar' === ( $a['displayLocation'] ?? '' ) ? 'titles' : $layout;
	$query  = array( 'post_type' => 'post', 'posts_per_page' => $count, 'offset' => max( 0, (int) ( $a['startPosition'] ?? 1 ) - 1 ), 'no_found_rows' => true, 'ignore_sticky_posts' => true );
	if ( ! empty( $a['blogCategoryId'] ) ) {
		$query['cat'] = absint( $a['blogCategoryId'] );
	}
	$posts = get_posts( $query );
	if ( ! $posts ) {
		return array( '', 0 );
	}
	$img   = ( $a['blogShowImage'] ?? true ) && 'titles' !== $layout;
	$words = max( 5, min( 80, (int) ( $a['blogExcerptWords'] ?? 24 ) ) );
	$out   = '';
	foreach ( $posts as $i => $post ) {
		$meta = array();
		if ( $a['blogShowDate'] ?? true ) {
			$meta[] = '<time datetime="' . esc_attr( get_the_date( 'c', $post ) ) . '">' . esc_html( get_the_date( '', $post ) ) . '</time>';
		}
		if ( ! empty( $a['blogShowAuthor'] ) ) {
			$meta[] = '<span>' . esc_html( get_the_author_meta( 'display_name', $post->post_author ) ) . '</span>';
		}
		if ( ! empty( $a['blogShowCategory'] ) ) {
			$cat    = get_the_category( $post->ID );
			$meta[] = $cat ? '<span>' . esc_html( $cat[0]->name ) . '</span>' : '';
		}
		$meta  = array_filter( $meta );
		$num   = 'titles' === $layout && ( $a['blogShowNumber'] ?? true ) ? '<span class="bst-lpost__num">' . ( $i + 1 + max( 0, (int) ( $a['startPosition'] ?? 1 ) - 1 ) ) . '</span>' : '';
		$thumb = $img ? get_the_post_thumbnail( $post, 'medium', array( 'class' => 'bst-lpost__img', 'alt' => '', 'loading' => 'lazy' ) ) : '';
		$out  .= '<li class="bst-lpost">' . $num . ( $thumb ? '<a class="bst-lpost__media" href="' . esc_url( get_permalink( $post ) ) . '" tabindex="-1" aria-hidden="true">' . $thumb . '</a>' : '' ) . '<div class="bst-lpost__body"><a class="bst-lpost__title" href="' . esc_url( get_permalink( $post ) ) . '">' . esc_html( get_the_title( $post ) ) . '</a>';
		$out  .= $meta ? '<span class="bst-lpost__meta">' . implode( ' · ', $meta ) . '</span>' : '';
		$out  .= ! empty( $a['blogShowExcerpt'] ) && 'titles' !== $layout ? '<p class="bst-lpost__excerpt">' . esc_html( wp_trim_words( get_the_excerpt( $post ), $words, '…' ) ) . '</p>' : '';
		$out  .= '</div></li>';
	}
	return array( '<ul class="bst-lposts bst-lposts--' . esc_attr( $layout ) . '" style="' . esc_attr( bst_listing_cols_style( $a, 3, 2, 1 ) ) . '">' . $out . '</ul>', count( $posts ) );
}

/** Look of the category grid: border and hover from the block, as CSS variables. */
function bst_le_category_style( $a ) {
	$c = function ( $key ) use ( $a ) {
		return function_exists( 'sanitize_hex_color' ) ? (string) sanitize_hex_color( (string) ( $a[ $key ] ?? '' ) ) : '';
	};
	$v = array(
		'--bst-lcat-bw'     => ( ( $a['categoryBorderEnabled'] ?? true ) ? max( 0, min( 6, (int) ( $a['categoryBorderWidth'] ?? 1 ) ) ) : 0 ) . 'px',
		'--bst-lcat-radius' => max( 0, min( 40, (int) ( $a['categoryBorderRadius'] ?? 14 ) ) ) . 'px',
	);
	if ( $c( 'categoryBorderColor' ) ) {
		$v['--bst-lcat-bc'] = $c( 'categoryBorderColor' );
	}
	if ( $a['categoryHoverEnabled'] ?? true ) {
		$v['--bst-lcat-lift'] = -max( 0, min( 12, (int) ( $a['categoryHoverLift'] ?? 2 ) ) ) . 'px';
		$v['--bst-lcat-zoom'] = max( 1, min( 1.3, (float) ( $a['categoryHoverImageZoom'] ?? 1.02 ) ) );
		$v['--bst-lcat-hbw']  = max( 0, min( 6, (int) ( $a['categoryHoverBorderWidth'] ?? 1 ) ) ) . 'px';
		$v['--bst-lcat-dur']  = max( 0, min( 1000, (int) ( $a['categoryHoverTransition'] ?? 200 ) ) ) . 'ms';
		$shadow               = bst_le_pick( $a, 'categoryHoverShadow', array( 'none', 'soft', 'medium', 'strong' ), 'soft' );
		$v['--bst-lcat-hsh']  = array( 'none' => 'none', 'soft' => '0 6px 18px rgba(15,23,42,.08)', 'medium' => '0 10px 28px rgba(15,23,42,.14)', 'strong' => '0 16px 40px rgba(15,23,42,.22)' )[ $shadow ];
		if ( $c( 'categoryHoverBorderColor' ) ) {
			$v['--bst-lcat-hbc'] = $c( 'categoryHoverBorderColor' );
		}
	} else {
		$v['--bst-lcat-lift'] = '0px';
		$v['--bst-lcat-zoom'] = 1;
		$v['--bst-lcat-hsh']  = 'none';
	}
	$out = '';
	foreach ( $v as $k => $val ) {
		$out .= $k . ':' . $val . ';';
	}
	return $out;
}

/**
 * bigspy/elements: featured, latest, popular, related or all listings, categories or blog posts,
 * as a grid or a slider, with a View more link or Load more button.
 */
function bst_render_listing_elements( $a ) {
	$type     = sanitize_key( $a['elementType'] ?? 'featured' );
	$count    = max( 1, min( 40, (int) ( $a['count'] ?? 12 ) ) );
	$title    = trim( (string) ( $a['title'] ?? '' ) );
	$mode     = 'slider' === ( $a['displayMode'] ?? 'grid' ) && 'categories' !== $type ? 'slider' : 'grid';
	$gap      = max( 0, min( 64, (int) ( $a['gap'] ?? 16 ) ) );
	$body     = '';
	$more     = '';
	if ( in_array( $type, array( 'categories', 'category_grid' ), true ) ) {
		$type = 'categories';
		$body = bst_render_category_grid(
			array(
				'count'           => $a['categoryCount'] ?? 24,
				'showCount'       => $a['categoryShowCount'] ?? true,
				'showImage'       => $a['categoryShowImage'] ?? true,
				'showDescription' => $a['categoryShowDescription'] ?? false,
				'columnsDesktop'  => $a['categoryColumnsDesktop'] ?? 5,
				'columnsTablet'   => $a['categoryColumnsTablet'] ?? 3,
				'columnsMobile'   => $a['categoryColumnsMobile'] ?? 2,
				'style'           => bst_le_category_style( $a ),
			)
		);
		if ( $body && ( $a['categoryShowViewMore'] ?? false ) ) {
			$more = bst_le_view_more( $a, 'categories', 0, $a['categoryViewMoreText'] ?? __( 'View all categories →', 'bigspy-ai-theme-wp' ), bst_le_view_more_url( array( 'viewMoreDestination' => 'categories' ), 'categories' ) );
		}
	} elseif ( 'sponsored' === $type ) {
		// The old [bigspy_sponsored_grid]: rotating Sponsored cards, as a Listing elements option.
		$mode = 'grid';
		$body = function_exists( 'bst_render_sponsored' ) ? bst_render_sponsored(
			array(
				'count'           => min( 6, $count ),
				'rotateStep'      => (int) ( $a['sponsoredRotateStep'] ?? 0 ),
				'columnsDesktop'  => $a['columnsDesktop'] ?? 3,
				'columnsTablet'   => $a['columnsTablet'] ?? 2,
				'columnsMobile'   => $a['columnsMobile'] ?? 1,
				'startPosition'   => max( 0, (int) ( $a['startPosition'] ?? 1 ) - 1 ),
			) + bst_le_sponsored_opts( $a )
		) : '';
	} elseif ( 'blog' === $type ) {
		list( $body, $shown ) = bst_le_blog( $a, $count );
		if ( $body && ( $a['blogShowViewMore'] ?? true ) ) {
			$more = bst_le_view_more( $a, 'blog', $shown, $a['blogViewMoreText'] ?? __( 'Read more articles →', 'bigspy-ai-theme-wp' ), bst_le_view_more_url( $a, 'blog' ) );
		}
	} else {
		$query = array( 'posts_per_page' => $count, 'offset' => max( 0, (int) ( $a['startPosition'] ?? 1 ) - 1 ) );
		$tax   = array();
		$terms = array();
		if ( 'featured' === $type ) {
			$tax[] = bst_featured_filter();
		}
		if ( 'popular' === $type ) {
			$query = array_merge( $query, array( 'orderby' => 'date', 'order' => 'DESC' ) );
			$query = function_exists( 'bigspy_popular_query_args' ) ? bigspy_popular_query_args( $query ) : $query;
			// Ranking source, read by BIGSPY AI List Suite: clicks on this site, the tool website's traffic (Site Insights) or both.
			$query['bigspy_popular_source'] = bst_le_pick( $a, 'popularSource', array( 'clicks', 'traffic', 'blend' ), 'clicks' );
		} else {
			$query['orderby'] = in_array( $a['orderBy'] ?? 'date', array( 'date', 'title', 'modified', 'comment_count', 'rand' ), true ) ? $a['orderBy'] : 'date';
			$query['order']   = 'ASC' === strtoupper( $a['order'] ?? 'DESC' ) ? 'ASC' : 'DESC';
		}
		if ( 'related' === $type ) {
			$context = absint( $a['relatedContextId'] ?? 0 );
			$context = $context ? $context : ( is_singular( 'product' ) ? get_queried_object_id() : 0 );
			$terms   = $context ? bst_listing_terms( $context, 'product_cat' ) : array();
			if ( ! $terms ) {
				return '';
			}
			$tax[]                  = array( 'taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => wp_list_pluck( $terms, 'term_id' ) );
			$query['post__not_in'] = array( $context );
			$a['relatedContextId'] = $context;
		} elseif ( ! empty( $a['categoryId'] ) ) {
			$tax[] = array( 'taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => absint( $a['categoryId'] ) );
		}
		if ( $tax ) {
			$query['tax_query'] = bst_listing_tax( $tax ); // phpcs:ignore WordPress.DB.SlowDBQuery
		}
		$card = array(
			'layout'          => sanitize_key( $a['layout'] ?? 'square' ),
			'showDescription' => $a['showDescription'] ?? true,
			'showCategory'    => $a['showCategory'] ?? true,
			'showPricing'     => $a['showPricing'] ?? true,
			'showTags'        => ! empty( $a['showTags'] ),
			'showCTA'         => ! empty( $a['showCTA'] ),
			'ctaPosition'     => bst_le_pick( $a, 'ctaPosition', array( 'top', 'bottom' ), 'bottom' ),
			'ctaAlignment'    => bst_le_pick( $a, 'ctaAlignment', array( 'left', 'center', 'right' ), 'right' ),
		) + bst_listing_card_style_args( 'slider' === $mode ? array( 'sponsoredPosition' => 'top' ) + $a : $a, $type );
		$body = bst_listing_grid( $query, $card, bst_listing_cols_style( $a, 4, 2, 1 ) . ';--bst-lgap:' . $gap . 'px' );
		if ( $body && ( $a['showSponsored'] ?? true ) && 'top' === ( 'slider' === $mode ? 'top' : ( $a['sponsoredPosition'] ?? 'between' ) ) && function_exists( 'bst_sponsored_slot' ) && empty( $a['bstLoadMore'] ) ) {
			$body = bst_sponsored_slot( 0, 'elements', bst_le_sponsored_opts( $a ) ) . $body;
		}
		if ( $body && ( $a['showViewMore'] ?? true ) ) {
			$more = bst_le_view_more( $a, $type, $count, $a['viewMoreText'] ?? '', bst_le_view_more_url( $a, $type, $terms ) );
		}
	}
	if ( '' === $body ) {
		return '';
	}
	if ( ! empty( $a['bstLoadMore'] ) ) {
		return $body . $more;
	}
	if ( 'slider' === $mode ) {
		wp_enqueue_script( 'bst-listing-elements', BST_URI . '/assets/js/listing-elements.js', array(), bst_asset_version( 'assets/js/listing-elements.js' ), true );
		$body = '<div class="bst-lslider" data-bst-le-slider><button type="button" class="bst-lslider__btn bst-lslider__btn--prev" data-dir="-1" aria-label="' . esc_attr__( 'Previous', 'bigspy-ai-theme-wp' ) . '">&#8592;</button>' . $body . '<button type="button" class="bst-lslider__btn bst-lslider__btn--next" data-dir="1" aria-label="' . esc_attr__( 'Next', 'bigspy-ai-theme-wp' ) . '">&#8594;</button></div>';
	}
	$class = 'bst-lelements bst-lelements--' . $type . ' bst-lelements--' . $mode . ( 'homepage-sidebar' === ( $a['displayLocation'] ?? '' ) ? ' bst-lelements--sidebar' : '' );
	return '<div class="' . esc_attr( $class ) . '" style="--bst-lgap:' . $gap . 'px">' . ( '' !== $title ? '<h2 class="bst-lelements__title">' . esc_html( $title ) . '</h2>' : '' ) . $body . $more . '</div>';
}

/* Load more: the next items of a bigspy/elements block, rendered with the same attributes. */
add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'bst/v1',
			'/listing-elements',
			array(
				'methods'             => 'POST',
				'permission_callback' => '__return_true',
				'args'                => array( 'attrs' => array( 'type' => 'object', 'required' => true ) ),
				'callback'            => function ( WP_REST_Request $req ) {
					$a = (array) $req->get_param( 'attrs' );
					$a = array_intersect_key( $a, (array) ( WP_Block_Type_Registry::get_instance()->get_registered( 'bigspy/elements' )->attributes ?? array() ) );
					$a['bstLoadMore'] = true;
					$a['title']       = '';
					$a['count']       = max( 1, min( 24, (int) ( $a['count'] ?? 12 ) ) );
					return array( 'html' => bst_render_listing_elements( $a ) );
				},
			)
		);
	}
);

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
