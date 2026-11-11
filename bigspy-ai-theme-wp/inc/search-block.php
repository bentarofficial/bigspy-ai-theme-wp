<?php
/**
 * Search form shared by the Search block (bst/search) and Listing search (bigspy/search).
 *
 * Options added in G-134-a are empty by default: Listing search with old attributes
 * renders the same markup as before.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Front assets: the stylesheet is the blocks' "style" handle; the script loads only when a form needs it. */
function bst_search_block_assets() {
	wp_register_style( 'bst-search', BST_URI . '/assets/css/search.css', array(), bst_asset_version( 'assets/css/search.css' ) );
	wp_register_script( 'bst-search', BST_URI . '/assets/js/search-block.js', array(), bst_asset_version( 'assets/js/search-block.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_register_script( 'bst-search-editor', BST_URI . '/assets/js/search-editor.js', array( 'bst-editor-helpers', 'bst-editor-kit', 'bst-blocks-listing', 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-server-side-render' ), bst_asset_version( 'assets/js/search-editor.js' ), true );
}
add_action( 'init', 'bst_search_block_assets', 19 );

/** Post types a visitor may search (public and not excluded from search). */
function bst_search_types() {
	$types = get_post_types( array( 'public' => true, 'exclude_from_search' => false ) );
	unset( $types['attachment'] );
	return array_values( $types );
}

/** Lines of a textarea attribute, trimmed, empty lines dropped. */
function bst_search_lines( $text, $max = 12 ) {
	$lines = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n|,/', (string) $text ) ), 'strlen' );
	return array_slice( array_values( $lines ), 0, $max );
}

/** Magnifier icon. */
function bst_search_icon( $class ) {
	return '<svg class="' . esc_attr( $class ) . '" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="7" fill="none" stroke="currentColor" stroke-width="2"/><path d="m20 20-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
}

/**
 * Render a search form.
 *
 * @param array  $a       Block attributes.
 * @param string $context 'listing' (products only, old markup) or 'site'.
 * @return string
 */
function bst_render_search_form( $a, $context = 'site' ) {
	$listing = 'listing' === $context;
	$id      = wp_unique_id( $listing ? 'bst-lsearch-' : 'bst-search-' );
	$holder  = (string) ( $a['placeholder'] ?? '' );
	if ( '' === $holder ) {
		$holder = $listing && ! array_key_exists( 'placeholder', $a ) ? __( 'Search tools, software and services...', 'bigspy-ai-theme-wp' ) : ( $listing ? '' : __( 'Search…', 'bigspy-ai-theme-wp' ) );
	}
	$align = in_array( $a['alignment'] ?? 'left', array( 'left', 'center', 'right' ), true ) ? $a['alignment'] : 'left';
	$width = max( 240, min( 1400, (int) ( $a['maxWidth'] ?? 760 ) ) );
	$style = 'max-width:' . $width . 'px;--bst-ls-radius:' . max( 0, min( 40, (int) ( $a['radius'] ?? 14 ) ) ) . 'px';
	if ( ! empty( $a['height'] ) ) {
		$style .= ';--bst-ls-h:' . max( 36, min( 80, (int) $a['height'] ) ) . 'px';
	}
	foreach ( array( 'buttonColor' => '--bst-ls-btn', 'buttonTextColor' => '--bst-ls-btn-text', 'borderColor' => '--bst-ls-border' ) as $key => $var ) {
		$color = sanitize_hex_color( (string) ( $a[ $key ] ?? '' ) );
		$style .= $color ? ';' . $var . ':' . $color : '';
	}

	/* G-134-a options. */
	$kind     = bst_choice( (string) ( $a['style'] ?? '' ), array( '', 'pill', 'boxed', 'underline', 'split', 'icon', 'hero', 'overlay' ), '' );
	$icon     = bst_choice( (string) ( $a['icon'] ?? '' ), array( '', 'left', 'right' ), '' );
	$btn_mode = bst_choice( (string) ( $a['buttonMode'] ?? '' ), array( '', 'text', 'icon', 'both' ), '' );
	$btn_mode = 'icon' === $kind && '' === $btn_mode ? 'icon' : $btn_mode;
	$btn_pos  = bst_choice( (string) ( $a['buttonPosition'] ?? '' ), array( '', 'inside', 'outside', 'below-mobile' ), '' );
	$align_m  = bst_choice( (string) ( $a['alignmentMobile'] ?? '' ), array( '', 'left', 'center', 'right', 'full' ), '' );
	$num      = function ( $key, $min, $max, $unset ) use ( $a ) {
		if ( ! isset( $a[ $key ] ) || ! is_numeric( $a[ $key ] ) || (float) $a[ $key ] === (float) $unset ) {
			return '';
		}
		return bst_clamp( $a[ $key ], $min, $max, $min ) . 'px';
	};
	$shadows  = array( '', '0 1px 2px rgba(0,0,0,.08)', '0 2px 6px rgba(0,0,0,.1)', '0 6px 16px rgba(0,0,0,.12)', '0 12px 28px rgba(0,0,0,.16)', '0 20px 48px rgba(0,0,0,.2)' );
	$new_vars = bst_style_vars(
		array(
			'--bst-ls-bw'    => $num( 'borderWidth', 0, 10, -1 ),
			'--bst-ls-br'    => $num( 'buttonRadius', 0, 999, -1 ),
			'--bst-ls-hm'    => $num( 'heightMobile', 32, 80, 0 ),
			'--bst-ls-fs'    => $num( 'fontSize', 11, 40, 0 ),
			'--bst-ls-padx'  => $num( 'padX', 0, 60, -1 ),
			'--bst-ls-mwt'   => $num( 'maxWidthTablet', 200, 1400, 0 ),
			'--bst-ls-mwm'   => $num( 'maxWidthMobile', 200, 1400, 0 ),
			'--bst-ls-bg'    => bst_block_color( $a, 'inputBg' ),
			'--bst-ls-c'     => bst_block_color( $a, 'inputColor' ),
			'--bst-ls-ph'    => bst_block_color( $a, 'placeholderColor' ),
			'--bst-ls-focus' => bst_block_color( $a, 'focusColor' ),
			'--bst-ls-btnh'  => bst_block_color( $a, 'buttonHoverColor' ),
			'--bst-ls-btnht' => bst_block_color( $a, 'buttonHoverTextColor' ),
			'--bst-ls-sh'    => $shadows[ (int) bst_clamp( $a['shadow'] ?? 0, 0, 5, 0 ) ],
		)
	);
	$style .= '' !== $new_vars ? ';' . $new_vars : '';

	$class = 'bst-lsearch bst-lsearch--' . $align;
	foreach ( array( 's-' . $kind => $kind, 'icon-' . $icon => $icon, 'btn-' . $btn_mode => $btn_mode && 'text' !== $btn_mode, 'bp-' . $btn_pos => $btn_pos && 'inside' !== $btn_pos, 'am-' . $align_m => $align_m, 'focus-shadow' => ! empty( $a['shadowFocus'] ) ) as $suffix => $on ) {
		$class .= $on ? ' bst-lsearch--' . $suffix : '';
	}

	/* Scope: post types and category. */
	$types = array( 'product' );
	if ( ! $listing ) {
		$types = array_values( array_intersect( array_map( 'sanitize_key', bst_search_lines( $a['postTypes'] ?? '', 10 ) ), bst_search_types() ) );
	}
	$tax      = ( $listing || array( 'product' ) === $types ) ? 'product_cat' : 'category';
	$cat_id   = ! empty( $a['category'] ) ? absint( $a['category'] ) : 0;
	$cat_term = $cat_id ? get_term( $cat_id, $tax ) : null;
	$cat_term = $cat_term instanceof WP_Term ? $cat_term : null;
	$cat_var  = 'product_cat' === $tax ? 'product_cat' : 'category_name';

	/* Behaviour handled by assets/js/search-block.js. */
	$data = array();
	if ( ! empty( $a['live'] ) ) {
		$data['bst-live'] = '1';
		$data['url']      = admin_url( 'admin-ajax.php' );
		$data['count']    = (int) bst_clamp( $a['liveCount'] ?? 6, 1, 12, 6 );
		$data['min']      = (int) bst_clamp( $a['minChars'] ?? 2, 1, 5, 2 );
		$data['img']      = ( $a['liveImage'] ?? true ) ? '1' : '0';
		$data['price']    = ( $a['livePrice'] ?? true ) ? '1' : '0';
		$data['cat']      = ! empty( $a['liveCategory'] ) ? '1' : '0';
		$data['types']    = implode( ',', $types );
		$data['term']     = $cat_term ? $cat_term->term_id : '';
		$data['tax']      = $tax;
		$data['empty']    = __( 'No results found.', 'bigspy-ai-theme-wp' );
		$data['all']      = __( 'View all results', 'bigspy-ai-theme-wp' );
	}
	$rotating = bst_search_lines( $a['placeholders'] ?? '', 10 );
	if ( count( $rotating ) > 1 ) {
		$data['placeholders'] = wp_json_encode( $rotating );
	}
	if ( ! empty( $a['shortcut'] ) ) {
		$data['shortcut'] = '1';
	}
	$attr = '';
	foreach ( $data as $key => $value ) {
		$attr .= ' data-' . $key . '="' . esc_attr( (string) $value ) . '"';
	}

	$label = trim( (string) ( $a['ariaLabel'] ?? '' ) );
	$label = '' !== $label ? $label : ( $listing ? __( 'Search listings', 'bigspy-ai-theme-wp' ) : __( 'Search', 'bigspy-ai-theme-wp' ) );
	$text  = (string) ( $a['buttonText'] ?? '' );
	$text  = '' !== $text || ( $listing && array_key_exists( 'buttonText', $a ) ) ? $text : __( 'Search', 'bigspy-ai-theme-wp' );

	$out  = '<form role="search" method="get" class="' . esc_attr( $class ) . '" style="' . esc_attr( $style ) . '" action="' . esc_url( home_url( '/' ) ) . '"' . $attr . '>';
	$out .= '<label class="screen-reader-text" for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
	if ( ! empty( $a['showCategorySelect'] ) && taxonomy_exists( $tax ) ) {
		$terms = get_terms( array( 'taxonomy' => $tax, 'hide_empty' => true, 'parent' => $cat_term ? $cat_term->term_id : 0, 'number' => 50 ) );
		if ( ! is_wp_error( $terms ) && $terms ) {
			$out .= '<select class="bst-lsearch__cat" name="' . esc_attr( $cat_var ) . '" aria-label="' . esc_attr__( 'Category', 'bigspy-ai-theme-wp' ) . '"><option value="' . esc_attr( $cat_term ? $cat_term->slug : '' ) . '">' . esc_html( $cat_term ? $cat_term->name : __( 'All categories', 'bigspy-ai-theme-wp' ) ) . '</option>';
			foreach ( $terms as $term ) {
				$out .= '<option value="' . esc_attr( $term->slug ) . '" data-id="' . (int) $term->term_id . '">' . esc_html( $term->name ) . '</option>';
			}
			$out     .= '</select>';
			$cat_term = null; // The select carries the category now.
		}
	}
	$out .= 'left' === $icon ? bst_search_icon( 'bst-lsearch__icon' ) : '';
	$out .= '<input id="' . esc_attr( $id ) . '" type="search" name="s" value="' . esc_attr( get_search_query() ) . '" placeholder="' . esc_attr( $holder ) . '">';
	if ( $listing ) {
		$out .= '<input type="hidden" name="post_type" value="product">';
	} elseif ( 1 === count( $types ) ) {
		$out .= '<input type="hidden" name="post_type" value="' . esc_attr( $types[0] ) . '">';
	} else {
		foreach ( $types as $type ) {
			$out .= '<input type="hidden" name="post_type[]" value="' . esc_attr( $type ) . '">';
		}
	}
	$out .= $cat_term ? '<input type="hidden" name="' . esc_attr( $cat_var ) . '" value="' . esc_attr( $cat_term->slug ) . '">' : '';
	$out .= 'right' === $icon ? bst_search_icon( 'bst-lsearch__icon' ) : '';
	$out .= ! empty( $a['shortcut'] ) ? '<kbd class="bst-lsearch__key" aria-hidden="true">/</kbd>' : '';
	if ( $a['showButton'] ?? true ) {
		if ( 'icon' === $btn_mode ) {
			$out .= '<button type="submit" aria-label="' . esc_attr( '' !== $text ? $text : __( 'Search', 'bigspy-ai-theme-wp' ) ) . '">' . bst_search_icon( 'bst-lsearch__btn-icon' ) . '</button>';
		} elseif ( 'both' === $btn_mode ) {
			$out .= '<button type="submit">' . bst_search_icon( 'bst-lsearch__btn-icon' ) . '<span>' . esc_html( $text ) . '</span></button>';
		} else {
			$out .= '<button type="submit">' . esc_html( $text ) . '</button>';
		}
	}
	$out .= '</form>';

	$chips = bst_search_lines( $a['chips'] ?? '', 12 );
	if ( $chips ) {
		$query = $listing ? array( 'post_type' => 'product' ) : ( 1 === count( $types ) ? array( 'post_type' => $types[0] ) : array() );
		$html  = '<div class="bst-lsearch-chips bst-lsearch-chips--' . esc_attr( $align ) . '">';
		$html .= '' !== trim( (string) ( $a['chipsLabel'] ?? '' ) ) ? '<span class="bst-lsearch-chips__label">' . esc_html( $a['chipsLabel'] ) . '</span>' : '';
		foreach ( $chips as $chip ) {
			$html .= '<a class="bst-lsearch-chips__chip" href="' . esc_url( add_query_arg( array( 's' => rawurlencode( $chip ) ) + $query, home_url( '/' ) ) ) . '">' . esc_html( $chip ) . '</a>';
		}
		$out .= $html . '</div>';
	}

	if ( 'overlay' === $kind ) {
		$open = '' !== $text ? $text : __( 'Search', 'bigspy-ai-theme-wp' );
		$out  = '<button type="button" class="bst-lsearch-open" aria-haspopup="dialog" aria-expanded="false" aria-controls="' . esc_attr( $id ) . '-dlg">' . bst_search_icon( 'bst-lsearch__btn-icon' ) . '<span>' . esc_html( $open ) . '</span></button>'
			. '<div class="bst-lsearch-overlay" id="' . esc_attr( $id ) . '-dlg" role="dialog" aria-modal="true" aria-label="' . esc_attr( $label ) . '" hidden><div class="bst-lsearch-overlay__inner">'
			. '<button type="button" class="bst-lsearch-overlay__close" aria-label="' . esc_attr__( 'Close', 'bigspy-ai-theme-wp' ) . '">&times;</button>' . $out . '</div></div>';
	}

	if ( $data || 'overlay' === $kind ) {
		wp_enqueue_script( 'bst-search' );
	}
	return $out;
}

/* ------------------------------------------------------------------ Instant suggestions (bst_live_search with bst_block=1) */

/** Block parameters of a suggestion request: count, post types, category. */
function bst_search_block_ajax_args() {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- public search.
	$types = array_values( array_intersect( array_map( 'sanitize_key', explode( ',', (string) wp_unslash( $_GET['types'] ?? '' ) ) ), bst_search_types() ) );
	$tax   = 'product_cat' === sanitize_key( $_GET['tax'] ?? '' ) ? 'product_cat' : 'category';
	$args  = array(
		'count' => (int) bst_clamp( $_GET['count'] ?? 6, 1, 12, 6 ),
		'types' => $types ? $types : bst_search_types(),
		'term'  => absint( $_GET['term'] ?? 0 ),
		'tax'   => $tax,
	);
	// phpcs:enable
	return $args;
}

/** WP_Query arguments for a block suggestion request. */
function bst_search_block_query( $base, $args ) {
	$query = $base + array( 'post_type' => $args['types'] );
	$tax   = array();
	if ( $args['term'] && taxonomy_exists( $args['tax'] ) ) {
		$tax[] = array( 'taxonomy' => $args['tax'], 'field' => 'term_id', 'terms' => array( $args['term'] ) );
	}
	if ( array( 'product' ) === $args['types'] && taxonomy_exists( 'product_visibility' ) ) {
		$tax[] = array( 'taxonomy' => 'product_visibility', 'field' => 'name', 'terms' => array( 'exclude-from-search' ), 'operator' => 'NOT IN' );
	}
	if ( $tax ) {
		$query['tax_query'] = $tax; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	}
	return $query;
}

/** First category name of a suggestion (product category for products). */
function bst_search_item_category( $post ) {
	$terms = get_the_terms( $post, 'product' === $post->post_type ? 'product_cat' : 'category' );
	return is_array( $terms ) && $terms ? html_entity_decode( $terms[0]->name, ENT_QUOTES, 'UTF-8' ) : '';
}
