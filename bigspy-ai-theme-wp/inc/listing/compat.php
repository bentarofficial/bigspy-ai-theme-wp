<?php
/**
 * Functions and hooks of the old BIGSPY AI List theme that its plugins call
 * (List Generator, Post Writer, Site Insights, Popular Analytics). Keep the names.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bigspy_theme_integration_version' ) ) {
	/** Integration API version the plugins check; 1.6.0 matches the old theme. */
	function bigspy_theme_integration_version() {
		return '1.6.0';
	}
}

if ( ! function_exists( 'bigspy_dynamic_attributes' ) ) {
	function bigspy_dynamic_attributes( $post_id = 0 ) {
		return bst_listing_attributes( $post_id );
	}
}

if ( ! function_exists( 'bigspy_get_field' ) ) {
	function bigspy_get_field( $key, $post_id = 0, $default = '' ) {
		return bst_listing_field( $key, $post_id, $default );
	}
}

if ( ! function_exists( 'bigspy_listing_url' ) ) {
	function bigspy_listing_url( $post_id = 0 ) {
		return bst_listing_url( $post_id );
	}
}

if ( ! function_exists( 'bigspy_theme_external_url' ) ) {
	/** Affiliate, official, external product URL, then the listing page. */
	function bigspy_theme_external_url( $post_id = 0 ) {
		$post_id  = bst_listing_id( $post_id );
		$external = '';
		if ( function_exists( 'wc_get_product' ) ) {
			$product  = wc_get_product( $post_id );
			$external = $product && $product->is_type( 'external' ) ? $product->get_product_url() : '';
		}
		$url = bst_listing_field( 'affiliate_url', $post_id, '' );
		$url = $url ? $url : bst_listing_field( 'official_url', $post_id, '' );
		$url = $url ? $url : $external;
		return apply_filters( 'bigspy_theme_external_url', $url ? $url : get_permalink( $post_id ), $post_id );
	}
}

if ( ! function_exists( 'bigspy_theme_integration_status' ) ) {
	function bigspy_theme_integration_status() {
		return array(
			'theme_version' => bigspy_theme_integration_version(),
			'woocommerce'   => class_exists( 'WooCommerce' ),
			'acf'           => function_exists( 'get_field' ),
			'rank_math'     => defined( 'RANK_MATH_VERSION' ),
			'generator'     => defined( 'BIGSPY_AI_VERSION' ),
		);
	}
}

// The old theme sent listing buttons to the affiliate link first, then the official site.
add_filter(
	'bigspy_listing_url',
	function ( $url, $post_id ) {
		$affiliate = bst_listing_field( 'affiliate_url', $post_id, '' );
		$official  = bst_listing_field( 'official_url', $post_id, '' );
		return $affiliate ? $affiliate : ( $official ? $official : $url );
	},
	20,
	2
);
