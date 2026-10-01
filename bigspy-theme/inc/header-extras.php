<?php
/**
 * Header extras: vertical "categories" menu element and live search suggestions.
 *
 * Both plug into the header builder through its filters, and both work with
 * or without WooCommerce (products and product categories when it is active,
 * posts / pages and a regular menu otherwise).
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/* ------------------------------------------------------------------------ Options */

function bst_header_extras_defaults( $defaults ) {
	return $defaults + array(
		'vmenu_label'      => 'All categories',
		'vmenu_source'     => 'auto',
		'vmenu_limit'      => 12,
		'vmenu_home_open'  => false,
		'live_search'      => true,
		'live_search_count' => 6,
	);
}
add_filter( 'bst_defaults', 'bst_header_extras_defaults' );

function bst_header_extras_fields( $fields ) {
	$fields[] = array( 'id' => 'bst_h_vmenu', 'section' => 'bst_header_nav', 'type' => 'heading', 'label' => __( 'Vertical categories menu', 'bigspy-theme' ), 'description' => __( 'Add the “Categories menu” element in the header builder.', 'bigspy-theme' ) );
	$fields[] = array( 'id' => 'vmenu_label', 'section' => 'bst_header_nav', 'type' => 'text', 'label' => __( 'Button label', 'bigspy-theme' ) );
	$fields[] = array(
		'id'          => 'vmenu_source',
		'section'     => 'bst_header_nav',
		'type'        => 'select',
		'label'       => __( 'Menu content', 'bigspy-theme' ),
		'choices'     => array(
			'auto'       => __( 'Automatic (menu if assigned, else product categories)', 'bigspy-theme' ),
			'menu'       => __( 'Menu assigned to “Vertical menu”', 'bigspy-theme' ),
			'categories' => __( 'Product categories (WooCommerce)', 'bigspy-theme' ),
		),
	);
	$fields[] = array( 'id' => 'vmenu_limit', 'section' => 'bst_header_nav', 'type' => 'number', 'label' => __( 'Max product categories', 'bigspy-theme' ), 'input_attrs' => array( 'min' => 1, 'max' => 40, 'step' => 1 ) );
	$fields[] = array( 'id' => 'vmenu_home_open', 'section' => 'bst_header_nav', 'type' => 'checkbox', 'label' => __( 'Keep the menu open on the homepage (desktop)', 'bigspy-theme' ) );

	$fields[] = array( 'id' => 'live_search', 'section' => 'bst_header_search', 'type' => 'checkbox', 'label' => __( 'Live search suggestions while typing', 'bigspy-theme' ) );
	$fields[] = array( 'id' => 'live_search_count', 'section' => 'bst_header_search', 'type' => 'number', 'label' => __( 'Number of suggestions', 'bigspy-theme' ), 'input_attrs' => array( 'min' => 3, 'max' => 12, 'step' => 1 ) );
	return $fields;
}
add_filter( 'bst_customizer_fields', 'bst_header_extras_fields' );

function bst_header_extras_setup() {
	register_nav_menus( array( 'vertical' => __( 'Vertical menu (categories)', 'bigspy-theme' ) ) );
}
add_action( 'after_setup_theme', 'bst_header_extras_setup' );

/* ------------------------------------------------------------------------ Vertical menu */

function bst_vmenu_register_element( $elements ) {
	$elements['categories'] = __( 'Categories menu (vertical)', 'bigspy-theme' );
	return $elements;
}
add_filter( 'bst_header_elements', 'bst_vmenu_register_element' );

/**
 * Product categories as a nested list (two levels).
 */
function bst_vmenu_categories_list() {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return '';
	}
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'parent'     => 0,
			'number'     => (int) bst_mod( 'vmenu_limit' ),
			'orderby'    => 'name',
			'menu_order' => 'ASC',
		)
	);
	if ( is_wp_error( $terms ) || ! $terms ) {
		return '';
	}

	$default = (int) get_option( 'default_product_cat' );
	$out     = '';
	foreach ( $terms as $term ) {
		if ( (int) $term->term_id === $default && ! $term->count ) {
			continue;
		}
		$children = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'parent'     => $term->term_id,
				'number'     => 20,
				'orderby'    => 'name',
				'menu_order' => 'ASC',
			)
		);
		$sub = '';
		if ( ! is_wp_error( $children ) && $children ) {
			foreach ( $children as $child ) {
				$sub .= '<li class="menu-item"><a href="' . esc_url( get_term_link( $child ) ) . '">' . esc_html( $child->name ) . '</a></li>';
			}
		}
		$thumb = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
		$icon  = $thumb ? wp_get_attachment_image( $thumb, 'thumbnail', false, array( 'class' => 'bs-vmenu__thumb', 'alt' => '', 'loading' => 'lazy' ) ) : '';

		$out .= '<li class="menu-item' . ( $sub ? ' menu-item-has-children' : '' ) . '"><a href="' . esc_url( get_term_link( $term ) ) . '">' . $icon . '<span>' . esc_html( $term->name ) . '</span></a>';
		$out .= $sub ? '<ul class="sub-menu">' . $sub . '</ul>' : '';
		$out .= '</li>';
	}
	return $out ? '<ul class="bs-vmenu__list">' . $out . '</ul>' : '';
}

function bst_vmenu_element( $html, $device ) {
	$source = bst_choice( bst_mod( 'vmenu_source' ), array( 'auto', 'menu', 'categories' ), 'auto' );
	$list   = '';

	if ( 'categories' !== $source && has_nav_menu( 'vertical' ) ) {
		$list = wp_nav_menu( array( 'theme_location' => 'vertical', 'container' => false, 'menu_class' => 'bs-vmenu__list', 'depth' => 2, 'fallback_cb' => false, 'echo' => false ) );
	}
	if ( ! $list && 'menu' !== $source ) {
		$list = bst_vmenu_categories_list();
	}
	if ( ! $list ) {
		return current_user_can( 'edit_theme_options' ) ? '<a class="bs-header-hint" href="' . esc_url( admin_url( 'nav-menus.php?action=locations' ) ) . '">' . esc_html__( 'Assign a menu to “Vertical menu”', 'bigspy-theme' ) . '</a>' : '';
	}

	$label = trim( (string) bst_mod( 'vmenu_label' ) );
	$open  = 'desktop' === $device && bst_mod( 'vmenu_home_open' ) && is_front_page();
	$id    = wp_unique_id( 'bs-vmenu-' );

	$out  = '<div class="bs-vmenu' . ( $open ? ' is-pinned' : '' ) . '" data-bs-vmenu>';
	$out .= '<button type="button" class="bs-vmenu__toggle" aria-expanded="' . ( $open ? 'true' : 'false' ) . '" aria-controls="' . esc_attr( $id ) . '">' . bst_icon( 'menu', 20 ) . '<span>' . esc_html( $label ) . '</span>' . bst_icon( 'chevron-down', 16 ) . '</button>';
	$out .= '<nav id="' . esc_attr( $id ) . '" class="bs-vmenu__panel" aria-label="' . esc_attr( $label ? $label : __( 'Categories', 'bigspy-theme' ) ) . '">' . $list . '</nav>';
	$out .= '</div>';
	return $out;
}
add_filter( 'bst_header_element_categories', 'bst_vmenu_element', 10, 2 );

/* ------------------------------------------------------------------------ Live search */

function bst_live_search_assets() {
	if ( ! bst_mod( 'live_search' ) ) {
		return;
	}
	wp_localize_script(
		'bst-main',
		'bstLiveSearch',
		array(
			'url'     => admin_url( 'admin-ajax.php' ),
			'empty'   => __( 'No results found.', 'bigspy-theme' ),
			'viewAll' => __( 'View all results', 'bigspy-theme' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'bst_live_search_assets', 30 );

function bst_live_search_ajax() {
	$term = sanitize_text_field( wp_unslash( $_GET['s'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public search.
	$term = function_exists( 'mb_substr' ) ? mb_substr( $term, 0, 80 ) : substr( $term, 0, 80 );
	if ( strlen( $term ) < 2 ) {
		wp_send_json_success( array( 'items' => array() ) );
	}

	$shop     = class_exists( 'WooCommerce' );
	$products = $shop && ( bst_mod( 'search_products' ) || 'product' === sanitize_key( $_GET['post_type'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$args     = array(
		's'                   => $term,
		'post_status'         => 'publish',
		'posts_per_page'      => (int) bst_clamp( bst_mod( 'live_search_count' ), 3, 12, 6 ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'post_type'           => $products ? 'product' : ( $shop ? array( 'product', 'post', 'page' ) : array( 'post', 'page' ) ),
	);
	if ( $shop ) {
		$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => array( 'exclude-from-search' ),
				'operator' => 'NOT IN',
			),
		);
	}

	$query = new WP_Query( apply_filters( 'bst_live_search_args', $args, $term ) );
	$items = array();
	foreach ( $query->posts as $post ) {
		$price = '';
		if ( $shop && 'product' === $post->post_type && ! bst_mod( 'catalog_mode' ) ) {
			$product = wc_get_product( $post->ID );
			$price   = $product ? wp_kses_post( $product->get_price_html() ) : '';
		}
		$type    = get_post_type_object( $post->post_type );
		$items[] = array(
			'title' => html_entity_decode( get_the_title( $post ), ENT_QUOTES, 'UTF-8' ),
			'url'   => get_permalink( $post ),
			'image' => (string) get_the_post_thumbnail_url( $post, 'thumbnail' ),
			'price' => $price,
			'type'  => ( 'product' === $post->post_type || ! $type ) ? '' : $type->labels->singular_name,
		);
	}
	wp_send_json_success( array( 'items' => $items ) );
}
add_action( 'wp_ajax_bst_live_search', 'bst_live_search_ajax' );
add_action( 'wp_ajax_nopriv_bst_live_search', 'bst_live_search_ajax' );
