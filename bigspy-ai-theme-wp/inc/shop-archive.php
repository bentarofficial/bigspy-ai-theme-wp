<?php
/**
 * G-209: shop and product category pages.
 *
 * Shop sidebar picked with pictures; category header with a cover image (the
 * WooCommerce category thumbnail, or the Shop page featured image); category
 * description above, below or hidden; grid/list switch and a products-per-page
 * dropdown in the toolbar. Hover second image, off-canvas filters on mobile,
 * sorting and result count already exist (Shop → Product cards / toolbar).
 * Defaults keep the old look.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$s = 'bst_shop';
		foreach ( $fields as $i => $f ) {
			if ( 'shop_sidebar' === $f['id'] ) {
				$fields[ $i ]['type'] = 'image_radio';
			}
		}
		$add = array(
			array( 'id' => 'bst_g_shop_cat_page', 'section' => $s, 'type' => 'heading', 'label' => __( 'Shop & category header', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'shop_cat_header', 'section' => $s, 'type' => 'image_radio', 'label' => __( 'Header style', 'bigspy-ai-theme-wp' ), 'choices' => array(
				'title' => __( 'Title', 'bigspy-ai-theme-wp' ),
				'cover' => __( 'Cover image', 'bigspy-ai-theme-wp' ),
			), 'description' => __( 'Cover uses the category thumbnail (Products → Categories) or the Shop page featured image.', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'shop_cover_height', 'section' => $s, 'type' => 'number', 'label' => __( 'Cover height (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 120, 'max' => 600, 'placeholder' => '280' ) ),
			array( 'id' => 'shop_cover_overlay', 'section' => $s, 'type' => 'number', 'label' => __( 'Cover darkness (%)', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 0, 'max' => 90, 'placeholder' => '45' ) ),
			array( 'id' => 'shop_cat_desc', 'section' => $s, 'type' => 'select', 'label' => __( 'Category description', 'bigspy-ai-theme-wp' ), 'choices' => array(
				'top'    => __( 'Above products', 'bigspy-ai-theme-wp' ),
				'bottom' => __( 'Below products', 'bigspy-ai-theme-wp' ),
				'hide'   => __( 'Hidden', 'bigspy-ai-theme-wp' ),
			) ),
			array( 'id' => 'bst_g_shop_view', 'section' => $s, 'type' => 'heading', 'label' => __( 'Grid / list and products per page', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'shop_view_switch', 'section' => $s, 'type' => 'checkbox', 'label' => __( 'Show grid / list switch', 'bigspy-ai-theme-wp' ), 'description' => __( 'The visitor\'s choice is remembered in their browser.', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'shop_view_default', 'section' => $s, 'type' => 'image_radio', 'label' => __( 'Default view', 'bigspy-ai-theme-wp' ), 'choices' => array(
				'grid' => __( 'Grid', 'bigspy-ai-theme-wp' ),
				'list' => __( 'List', 'bigspy-ai-theme-wp' ),
			) ),
			array( 'id' => 'shop_per_page_choices', 'section' => $s, 'type' => 'text', 'label' => __( 'Products per page choices', 'bigspy-ai-theme-wp' ), 'description' => __( 'E.g. 12,24,48 shows a "Show" dropdown. Empty = no dropdown.', 'bigspy-ai-theme-wp' ) ),
		);
		return array_merge( $fields, $add );
	}
);

add_filter(
	'bst_layout_pictures',
	function ( $all ) {
		$all['shop_header'] = array(
			'title' => array( array( 2, 4, 30, 5, 1 ), array( 2, 14, 17, 12, 0 ), array( 21, 14, 17, 12, 0 ), array( 40, 14, 18, 12, 0 ) ),
			'cover' => array( array( 2, 2, 56, 14, 1 ), array( 2, 19, 17, 12, 0 ), array( 21, 19, 17, 12, 0 ), array( 40, 19, 18, 12, 0 ) ),
		);
		$all['shop_view']   = array(
			'grid' => $all['blog']['grid'],
			'list' => $all['blog']['list'],
		);
		return $all;
	}
);
add_filter(
	'bst_layout_picker_map',
	function ( $map ) {
		$map['shop_sidebar']      = 'sidebar';
		$map['shop_cat_header']   = 'shop_header';
		$map['shop_view_default'] = 'shop_view';
		return $map;
	}
);

/** Shop, product category/tag/attribute archive. */
function bst_shop_is_archive() {
	return ! is_admin() && ( is_shop() || is_product_taxonomy() );
}

function bst_shop_option( $id, $choices, $default ) {
	return bst_choice( get_theme_mod( $id, $default ), $choices, $default );
}

/* ---------------------------------------------------------------- Cover header */

/** Cover image id for the current shop page (0 = none). */
function bst_shop_cover_image_id() {
	if ( is_product_taxonomy() ) {
		return (int) get_term_meta( get_queried_object_id(), 'thumbnail_id', true );
	}
	return is_shop() ? (int) get_post_thumbnail_id( wc_get_page_id( 'shop' ) ) : 0;
}

/** True once the cover header has been printed on this request. */
function bst_shop_cover_done( $set = null ) {
	static $done = false;
	if ( null !== $set ) {
		$done = (bool) $set;
	}
	return $done;
}

function bst_shop_cover() {
	if ( ! bst_shop_is_archive() || 'cover' !== bst_shop_option( 'shop_cat_header', array( 'title', 'cover' ), 'title' ) || ! bst_show_title() ) {
		return;
	}
	$img   = bst_shop_cover_image_id();
	$url   = $img ? wp_get_attachment_image_url( $img, 'full' ) : '';
	$h     = get_theme_mod( 'shop_cover_height', '' );
	$dark  = get_theme_mod( 'shop_cover_overlay', '' );
	$style = '';
	if ( '' !== $h && is_numeric( $h ) ) {
		$style .= '--bs-shop-cover-h:' . max( 120, min( 600, (int) $h ) ) . 'px;';
	}
	if ( '' !== $dark && is_numeric( $dark ) ) {
		$style .= '--bs-shop-cover-dark:' . ( max( 0, min( 90, (int) $dark ) ) / 100 ) . ';';
	}
	if ( $url ) {
		$style .= 'background-image:url(' . esc_url( $url ) . ');';
	}
	$desc = 'top' === bst_shop_option( 'shop_cat_desc', array( 'top', 'bottom', 'hide' ), 'top' ) && is_product_taxonomy() ? term_description() : '';
	echo '<header class="bs-shop-cover' . ( $url ? ' has-image' : '' ) . '" style="' . esc_attr( $style ) . '"><div class="bs-container bs-shop-cover__inner">';
	bst_breadcrumbs();
	echo '<h1 class="bs-page-title bs-shop-cover__title">' . esc_html( woocommerce_page_title( false ) ) . '</h1>';
	if ( $desc ) {
		echo '<div class="bs-shop-cover__desc">' . wp_kses_post( $desc ) . '</div>';
	}
	echo '</div></header>';
	bst_shop_cover_done( true );
}
add_action( 'woocommerce_before_main_content', 'bst_shop_cover', 19 );

/* ---------------------------------------------------------------- Description */

add_action(
	'wp',
	function () {
		if ( ! bst_shop_is_archive() ) {
			return;
		}
		$mode  = bst_shop_option( 'shop_cat_desc', array( 'top', 'bottom', 'hide' ), 'top' );
		$cover = 'cover' === bst_shop_option( 'shop_cat_header', array( 'title', 'cover' ), 'title' );
		if ( 'top' !== $mode || $cover ) {
			// The cover prints the description itself; otherwise it moves or hides.
			remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
			add_filter( 'get_the_archive_description', 'bst_shop_hide_archive_description', 20 );
		}
		if ( 'bottom' === $mode ) {
			add_action( 'woocommerce_after_main_content', 'bst_shop_bottom_description', 5 );
		}
	}
);

function bst_shop_hide_archive_description( $description ) {
	return is_product_taxonomy() ? '' : $description;
}

function bst_shop_bottom_description() {
	$desc = is_product_taxonomy() ? term_description() : '';
	if ( $desc ) {
		echo '<div class="bs-archive-description bs-shop-desc-bottom">' . wp_kses_post( $desc ) . '</div>';
	}
}

/* ---------------------------------------------------------------- Toolbar */

/** Allowed per-page numbers from the option (empty = no dropdown). */
function bst_shop_per_page_choices() {
	$out = array();
	foreach ( explode( ',', (string) get_theme_mod( 'shop_per_page_choices', '' ) ) as $n ) {
		$n = (int) trim( $n );
		if ( $n >= 1 && $n <= 200 ) {
			$out[] = $n;
		}
	}
	return array_values( array_unique( $out ) );
}

add_filter(
	'loop_shop_per_page',
	function ( $per ) {
		$choices = bst_shop_per_page_choices();
		$want    = isset( $_GET['per_page'] ) ? (int) $_GET['per_page'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only view option.
		return ( $want && in_array( $want, $choices, true ) ) ? $want : $per;
	},
	30
);

function bst_shop_view_default() {
	return bst_shop_option( 'shop_view_default', array( 'grid', 'list' ), 'grid' );
}

/** Grid/list buttons and the per-page dropdown. */
function bst_shop_view_tools() {
	if ( ! bst_shop_is_archive() || ! wc_get_loop_prop( 'is_paginated' ) || ! woocommerce_products_will_display() ) {
		return;
	}
	$html    = '';
	$choices = bst_shop_per_page_choices();
	if ( $choices ) {
		$current = (int) wc_get_loop_prop( 'per_page' );
		$opts    = '';
		foreach ( $choices as $n ) {
			$opts .= '<option value="' . (int) $n . '"' . selected( $current, $n, false ) . '>' . (int) $n . '</option>';
		}
		$keep = '';
		foreach ( $_GET as $k => $v ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( 'per_page' !== $k && 'paged' !== $k && is_string( $v ) ) {
				$keep .= '<input type="hidden" name="' . esc_attr( sanitize_key( $k ) ) . '" value="' . esc_attr( wp_unslash( $v ) ) . '">';
			}
		}
		$html .= '<form class="bs-shop-perpage" method="get"><label>' . esc_html__( 'Show', 'bigspy-ai-theme-wp' ) . ' <select name="per_page" onchange="this.form.submit()">' . $opts . '</select></label>' . $keep . '<noscript><button type="submit">' . esc_html__( 'Apply', 'bigspy-ai-theme-wp' ) . '</button></noscript></form>';
	}
	if ( get_theme_mod( 'shop_view_switch', false ) ) {
		$def   = bst_shop_view_default();
		$html .= '<div class="bs-shop-view" role="group" aria-label="' . esc_attr__( 'Product view', 'bigspy-ai-theme-wp' ) . '" data-bs-shop-view data-default="' . esc_attr( $def ) . '">';
		foreach ( array( 'grid' => __( 'Grid view', 'bigspy-ai-theme-wp' ), 'list' => __( 'List view', 'bigspy-ai-theme-wp' ) ) as $k => $label ) {
			$html .= '<button type="button" class="bs-shop-view__btn bs-shop-view__btn--' . $k . '" data-view="' . $k . '" aria-pressed="' . ( $def === $k ? 'true' : 'false' ) . '" aria-label="' . esc_attr( $label ) . '" title="' . esc_attr( $label ) . '"><span aria-hidden="true"></span></button>';
		}
		$html .= '</div>';
		// Apply the remembered view before the products paint.
		$html .= '<script>(function(){try{var v=localStorage.getItem("bsShopView");if(v==="grid"||v==="list"){document.body.classList.toggle("bs-view-list",v==="list");}}catch(e){}})();</script>';
	}
	if ( $html ) {
		echo '<div class="bs-shop-tools">' . $html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'woocommerce_before_shop_loop', 'bst_shop_view_tools', 35 );

add_filter(
	'body_class',
	function ( $classes ) {
		if ( function_exists( 'is_shop' ) && bst_shop_is_archive() && 'list' === bst_shop_view_default() ) {
			$classes[] = 'bs-view-list';
		}
		return $classes;
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( bst_shop_is_archive() && get_theme_mod( 'shop_view_switch', false ) ) {
			wp_enqueue_script( 'bst-shop-view', BST_URI . '/assets/js/shop-view.js', array(), bst_asset_version( 'assets/js/shop-view.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
		}
	}
);
