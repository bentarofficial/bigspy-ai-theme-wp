<?php
/**
 * Shared product / category renderers.
 *
 * One implementation for the Products and Product Categories blocks, the
 * legacy Flatsome shortcodes and the "Load more" AJAX endpoint, so every
 * product list on the site uses the same card design.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whitelist and normalise product list options. Safe to call with untrusted input.
 *
 * @param array $raw Raw options.
 * @return array
 */
function bst_products_options( $raw ) {
	$raw = is_array( $raw ) ? $raw : array();
	$ids = function ( $value, $slugs = false ) {
		$value = is_array( $value ) ? $value : explode( ',', (string) $value );
		$out   = array();
		foreach ( $value as $item ) {
			$item = trim( (string) $item );
			if ( '' === $item ) {
				continue;
			}
			if ( ctype_digit( $item ) ) {
				$out[] = (int) $item;
			} elseif ( $slugs ) {
				$out[] = sanitize_title( $item );
			}
		}
		return array_slice( array_values( array_unique( $out, SORT_REGULAR ) ), 0, 100 );
	};

	return array(
		'type'      => bst_choice( $raw['type'] ?? 'latest', array( 'latest', 'featured', 'sale', 'best_selling', 'top_rated', 'random' ), 'latest' ),
		'cats'      => $ids( $raw['cats'] ?? array(), true ),
		'tags'      => $ids( $raw['tags'] ?? array(), true ),
		'ids'       => $ids( $raw['ids'] ?? array() ),
		'count'     => (int) bst_clamp( $raw['count'] ?? 8, 1, 60, 8 ),
		'offset'    => (int) bst_clamp( $raw['offset'] ?? 0, 0, 500, 0 ),
		'orderby'   => bst_choice( $raw['orderby'] ?? '', array( '', 'date', 'title', 'price', 'price-desc', 'rand', 'popularity', 'rating', 'menu_order' ), '' ),
		'layout'    => bst_choice( $raw['layout'] ?? 'grid', array( 'grid', 'slider', 'list' ), 'grid' ),
		'd'         => (int) bst_clamp( $raw['d'] ?? 4, 1, 8, 4 ),
		't'         => (int) bst_clamp( $raw['t'] ?? 3, 1, 6, 3 ),
		'm'         => (int) bst_clamp( $raw['m'] ?? 2, 1, 3, 2 ),
		'autoplay'  => (int) bst_clamp( $raw['autoplay'] ?? 0, 0, 30000, 0 ),
		'load_more' => ! empty( $raw['load_more'] ),
	);
}

/**
 * WP_Query arguments for a product list.
 *
 * @param array $o    Options from bst_products_options().
 * @param int   $page Page number (1-based).
 */
function bst_products_query_args( $o, $page = 1 ) {
	$tax_query = array(
		array(
			'taxonomy' => 'product_visibility',
			'field'    => 'name',
			'terms'    => array( 'exclude-from-catalog' ),
			'operator' => 'NOT IN',
		),
	);
	if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
		$tax_query[] = array(
			'taxonomy' => 'product_visibility',
			'field'    => 'name',
			'terms'    => array( 'outofstock' ),
			'operator' => 'NOT IN',
		);
	}
	foreach ( array( 'cats' => 'product_cat', 'tags' => 'product_tag' ) as $key => $taxonomy ) {
		$numeric = array_filter( $o[ $key ], 'is_int' );
		$slugs   = array_filter( $o[ $key ], 'is_string' );
		if ( $numeric ) {
			$tax_query[] = array( 'taxonomy' => $taxonomy, 'field' => 'term_id', 'terms' => $numeric );
		} elseif ( $slugs ) {
			$tax_query[] = array( 'taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => $slugs );
		}
	}

	$page = max( 1, (int) $page );
	$args = array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'posts_per_page'      => $o['count'],
		'offset'              => $o['offset'] + ( $page - 1 ) * $o['count'],
		'ignore_sticky_posts' => true,
		'no_found_rows'       => ! $o['load_more'],
		'orderby'             => 'date',
		'order'               => 'DESC',
	);

	switch ( $o['type'] ) {
		case 'featured':
			$tax_query[] = array( 'taxonomy' => 'product_visibility', 'field' => 'name', 'terms' => array( 'featured' ) );
			break;
		case 'sale':
			$args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
			break;
		case 'best_selling':
			$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$args['orderby']  = 'meta_value_num';
			break;
		case 'top_rated':
			$args['meta_key'] = '_wc_average_rating'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$args['orderby']  = 'meta_value_num';
			break;
		case 'random':
			$args['orderby'] = 'rand';
			break;
	}

	switch ( $o['orderby'] ) {
		case 'title':
			$args['orderby'] = 'title';
			$args['order']   = 'ASC';
			break;
		case 'menu_order':
			$args['orderby'] = 'menu_order title';
			$args['order']   = 'ASC';
			break;
		case 'rand':
			$args['orderby'] = 'rand';
			break;
		case 'date':
			$args['orderby'] = 'date';
			break;
		case 'price':
		case 'price-desc':
			$args['meta_key'] = '_price'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$args['orderby']  = 'meta_value_num';
			$args['order']    = 'price' === $o['orderby'] ? 'ASC' : 'DESC';
			break;
		case 'popularity':
			$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$args['orderby']  = 'meta_value_num';
			break;
		case 'rating':
			$args['meta_key'] = '_wc_average_rating'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$args['orderby']  = 'meta_value_num';
			break;
	}

	if ( $o['ids'] ) {
		$args['post__in'] = $o['ids'];
		if ( '' === $o['orderby'] && 'latest' === $o['type'] ) {
			$args['orderby'] = 'post__in';
		}
	}

	$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	return apply_filters( 'bst_products_query_args', $args, $o );
}

/**
 * Product cards (<li> items) for a query, plus whether more pages exist.
 *
 * @return array{items:string,more:bool,count:int}
 */
function bst_products_items( $o, $page = 1 ) {
	$query = new WP_Query( bst_products_query_args( $o, $page ) );
	$items = '';
	if ( $query->have_posts() ) {
		wc_setup_loop( array( 'columns' => $o['d'], 'name' => 'bst-products' ) );
		ob_start();
		while ( $query->have_posts() ) {
			$query->the_post();
			wc_get_template_part( 'content', 'product' );
		}
		$items = ob_get_clean();
		wp_reset_postdata();
		wc_reset_loop();
	}
	$shown = $o['offset'] + $page * $o['count'];
	return array(
		'items' => $items,
		'count' => (int) $query->post_count,
		'more'  => $o['load_more'] && (int) $query->found_posts > $shown,
	);
}

/**
 * Full product list markup: grid, slider or compact list.
 *
 * @param array  $raw   Options (see bst_products_options()).
 * @param string $class Extra wrapper class.
 */
function bst_products_html( $raw, $class = '' ) {
	$o = bst_products_options( $raw );
	if ( 'list' === $o['layout'] ) {
		return bst_products_list_html( $o, $class );
	}
	if ( 'slider' === $o['layout'] ) {
		$o['load_more'] = false;
	}

	$result = bst_products_items( $o, 1 );
	if ( '' === $result['items'] ) {
		return '';
	}

	if ( 'slider' === $o['layout'] ) {
		return '<div class="bs-products-block woocommerce ' . esc_attr( $class ) . '">' . bst_slider_markup(
			$result['items'],
			array(
				'd'           => $o['d'],
				't'           => $o['t'],
				'm'           => $o['m'],
				'gap'         => 20,
				'autoplay'    => $o['autoplay'],
				'track_tag'   => 'ul',
				'track_class' => 'products',
				'dots'        => false,
				'loop'        => false,
			),
			'bs-slider--cards'
		) . '</div>';
	}

	$style = bst_style_vars( array( '--bs-cols-d' => $o['d'], '--bs-cols-t' => $o['t'], '--bs-cols-m' => $o['m'] ) );
	$out   = '<div class="bs-products-block woocommerce ' . esc_attr( $class ) . '">';
	$out  .= '<ul class="products bs-products-grid" style="' . esc_attr( $style ) . '">' . $result['items'] . '</ul>';
	if ( $result['more'] ) {
		$out .= '<div class="bs-load-more"><button type="button" class="bs-btn bs-btn--outline" data-bs-load-more data-url="' . esc_url( admin_url( 'admin-ajax.php' ) ) . '" data-page="1" data-options="' . esc_attr( wp_json_encode( $o ) ) . '" data-label-loading="' . esc_attr__( 'Loading…', 'bigspy-theme' ) . '">' . esc_html__( 'Load more', 'bigspy-theme' ) . '</button></div>';
	}
	return $out . '</div>';
}

/**
 * AJAX: next page of a product grid.
 */
function bst_products_ajax() {
	$raw  = json_decode( wp_unslash( $_POST['options'] ?? '' ), true ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- public catalog data, options are whitelisted below.
	$page = max( 2, min( 500, absint( $_POST['page'] ?? 2 ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$o    = bst_products_options( $raw );

	$o['load_more'] = true;
	$result         = bst_products_items( $o, $page );
	wp_send_json_success(
		array(
			'html' => $result['items'],
			'more' => $result['more'],
		)
	);
}
add_action( 'wp_ajax_bst_products', 'bst_products_ajax' );
add_action( 'wp_ajax_nopriv_bst_products', 'bst_products_ajax' );

/**
 * Compact product list: small image, title, rating, price.
 */
function bst_products_list_html( $o, $class = '' ) {
	$o['load_more'] = false;
	$query          = new WP_Query( bst_products_query_args( $o, 1 ) );
	if ( ! $query->have_posts() ) {
		return '';
	}
	$catalog = (bool) bst_mod( 'catalog_mode' );
	$out     = '<ul class="bs-product-list ' . esc_attr( $class ) . '">';
	while ( $query->have_posts() ) {
		$query->the_post();
		$product = wc_get_product( get_the_ID() );
		if ( ! $product ) {
			continue;
		}
		$out .= '<li class="bs-product-list__item">';
		$out .= '<a class="bs-product-list__thumb" href="' . esc_url( get_permalink() ) . '" tabindex="-1" aria-hidden="true">' . $product->get_image( 'woocommerce_gallery_thumbnail' ) . '</a>';
		$out .= '<div class="bs-product-list__body"><a class="bs-product-list__title" href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a>';
		if ( wc_review_ratings_enabled() && $product->get_rating_count() ) {
			$out .= wc_get_rating_html( $product->get_average_rating() );
		}
		if ( ! $catalog ) {
			$out .= '<span class="price bs-product-list__price">' . $product->get_price_html() . '</span>';
		}
		$out .= '</div></li>';
	}
	wp_reset_postdata();
	return $out . '</ul>';
}

/**
 * Product categories with image, as grid or slider.
 *
 * @param array  $raw   Options.
 * @param string $class Extra wrapper class.
 */
function bst_product_categories_html( $raw, $class = '' ) {
	$raw    = is_array( $raw ) ? $raw : array();
	$style  = bst_choice( $raw['style'] ?? 'overlay', array( 'overlay', 'card', 'circle' ), 'overlay' );
	$ratio  = bst_choice( $raw['ratio'] ?? '1-1', array( '1-1', '4-3', '3-4', '16-9' ), '1-1' );
	$layout = bst_choice( $raw['layout'] ?? 'grid', array( 'grid', 'slider' ), 'grid' );
	$order  = bst_choice( $raw['orderby'] ?? 'menu_order', array( 'menu_order', 'name', 'count' ), 'menu_order' );

	$args = array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => ! empty( $raw['hide_empty'] ),
		'number'     => (int) bst_clamp( $raw['count'] ?? 6, 1, 60, 6 ),
		'orderby'    => 'menu_order' === $order ? 'name' : $order,
		'order'      => 'count' === $order ? 'DESC' : 'ASC',
		// WooCommerce turns this into its drag & drop category order (and ignores it when false).
		'menu_order' => 'menu_order' === $order ? 'ASC' : false,
	);
	$ids = array_filter( array_map( 'absint', is_array( $raw['ids'] ?? null ) ? $raw['ids'] : explode( ',', (string) ( $raw['ids'] ?? '' ) ) ) );
	if ( $ids ) {
		$args['include'] = $ids;
		$args['orderby'] = 'include';
		$args['number']  = count( $ids );
		unset( $args['menu_order'] );
	} elseif ( ! empty( $raw['parent'] ) ) {
		$args['parent'] = (int) $raw['parent'];
	} elseif ( ! empty( $raw['top_level'] ) ) {
		$args['parent'] = 0;
	}

	$terms = get_terms( $args );
	if ( is_wp_error( $terms ) || ! $terms ) {
		return '';
	}

	$default_cat = (int) get_option( 'default_product_cat' );
	$items       = '';
	foreach ( $terms as $term ) {
		if ( ! $ids && $default_cat && (int) $term->term_id === $default_cat && ! $term->count ) {
			continue;
		}
		$thumb = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
		$image = $thumb ? wp_get_attachment_image( $thumb, 'woocommerce_thumbnail', false, array( 'alt' => '', 'loading' => 'lazy' ) ) : '';
		if ( ! $image ) {
			$image = '<img src="' . esc_url( wc_placeholder_img_src() ) . '" alt="" loading="lazy">';
		}
		$items .= '<a class="bs-cat" href="' . esc_url( get_term_link( $term ) ) . '">';
		$items .= '<span class="bs-cat__media bs-ratio bs-ratio--' . esc_attr( $ratio ) . '">' . $image . '</span>';
		$items .= '<span class="bs-cat__body"><span class="bs-cat__name">' . esc_html( $term->name ) . '</span>';
		if ( ! empty( $raw['show_count'] ) ) {
			/* translators: %s: number of products */
			$items .= '<span class="bs-cat__count">' . esc_html( sprintf( _n( '%s product', '%s products', $term->count, 'bigspy-theme' ), number_format_i18n( $term->count ) ) ) . '</span>';
		}
		$items .= '</span></a>';
	}
	if ( '' === $items ) {
		return '';
	}

	$d = (int) bst_clamp( $raw['d'] ?? 6, 1, 8, 6 );
	$t = (int) bst_clamp( $raw['t'] ?? 3, 1, 6, 3 );
	$m = (int) bst_clamp( $raw['m'] ?? 2, 1, 3, 2 );

	if ( 'slider' === $layout ) {
		$html = bst_slider_markup( $items, array( 'd' => $d, 't' => $t, 'm' => $m, 'gap' => 20, 'autoplay' => (int) bst_clamp( $raw['autoplay'] ?? 0, 0, 30000, 0 ), 'dots' => false, 'loop' => false ), 'bs-slider--cards' );
	} else {
		$html = '<div class="bs-block-grid" style="' . esc_attr( bst_style_vars( array( '--bs-cols-d' => $d, '--bs-cols-t' => $t, '--bs-cols-m' => $m ) ) ) . '">' . $items . '</div>';
	}
	return '<div class="bs-cats bs-cats--' . esc_attr( $style ) . ' ' . esc_attr( $class ) . '">' . $html . '</div>';
}
