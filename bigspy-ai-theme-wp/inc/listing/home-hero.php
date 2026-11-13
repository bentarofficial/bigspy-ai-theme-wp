<?php
/**
 * Helpers kept from the old BIGSPY AI List homepage hero: the
 * [bigspy_home_destination_buttons] shortcode (buttons saved in the old theme, or the
 * directory pages) and {{listing_count}}-style counters. The hero itself is designed
 * with the Banner block in the page content; the theme adds no separate hero.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** A setting saved by the old BIGSPY AI List theme. */
function bst_legacy_list_mod( $key, $default = null ) {
	$mods = get_option( 'theme_mods_bigspy-ai-list', array() );
	$mods = is_array( $mods ) ? $mods : array();
	return array_key_exists( $key, $mods ) ? $mods[ $key ] : $default;
}

/** Destination buttons: saved list, or the five directory pages. */
function bst_legacy_home_destinations() {
	$saved = bst_legacy_list_mod( 'bigspy_home_destination_buttons', null );
	if ( ! is_array( $saved ) ) {
		$shop  = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/listings/' );
		$saved = array(
			array( 'label' => __( 'All Listings', 'bigspy-ai-theme-wp' ), 'url' => $shop ),
			array( 'label' => __( 'Categories', 'bigspy-ai-theme-wp' ), 'url' => home_url( '/categories/' ) ),
			array( 'label' => __( 'Featured', 'bigspy-ai-theme-wp' ), 'url' => home_url( '/featured/' ) ),
			array( 'label' => __( 'Popular', 'bigspy-ai-theme-wp' ), 'url' => home_url( '/popular/' ) ),
			array( 'label' => __( 'Blog', 'bigspy-ai-theme-wp' ), 'url' => home_url( '/blog/' ) ),
		);
	}
	$html = '';
	foreach ( $saved as $item ) {
		$label = is_array( $item ) ? sanitize_text_field( $item['label'] ?? '' ) : '';
		$url   = is_array( $item ) ? esc_url( $item['url'] ?? '', array( 'http', 'https' ) ) : '';
		if ( '' !== $label && '' !== $url ) {
			$html .= '<a class="bigspy-home-hero-destinations__button" href="' . $url . '">' . esc_html( $label ) . '</a>';
		}
	}
	wp_enqueue_style( 'bst-listing', BST_URI . '/assets/css/listing.css', array(), bst_asset_version( 'assets/css/listing.css' ) );
	return '' === $html ? '' : '<nav class="bigspy-home-hero-destinations" aria-label="' . esc_attr( bst_listing_text( 'explore', get_bloginfo( 'name' ) ) ) . '">' . $html . '</nav>';
}
if ( ! shortcode_exists( 'bigspy_home_destination_buttons' ) ) {
	add_shortcode( 'bigspy_home_destination_buttons', 'bst_legacy_home_destinations' );
}

/** {{listing_count}} and the other counters the old hero editor offered. */
function bst_legacy_hero_tokens( $content ) {
	if ( ! is_string( $content ) || false === strpos( $content, '{{' ) ) {
		return $content;
	}
	$sponsored = function_exists( 'bst_sponsored_items' ) ? count( bst_sponsored_items() ) : 0;
	$terms     = taxonomy_exists( 'product_cat' ) ? (int) wp_count_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) ) : 0;
	$users     = count_users();
	$current   = ( is_tax() || is_category() ) && isset( get_queried_object()->count ) ? (int) get_queried_object()->count : 0;
	return strtr(
		$content,
		array(
			'{{listing_count}}'          => number_format_i18n( (int) ( wp_count_posts( 'product' )->publish ?? 0 ) ),
			'{{category_count}}'         => number_format_i18n( $terms ),
			'{{sponsored_count}}'        => number_format_i18n( $sponsored ),
			'{{post_count}}'             => number_format_i18n( (int) ( wp_count_posts( 'post' )->publish ?? 0 ) ),
			'{{user_count}}'             => number_format_i18n( (int) ( $users['total_users'] ?? 0 ) ),
			'{{current_category_count}}' => number_format_i18n( $current ),
		)
	);
}

// Counters such as {{listing_count}} work in any content, so a Banner hero can show them.
add_filter( 'the_content', 'bst_legacy_hero_tokens', 12 );

// Widgets and UX Blocks (footer, sidebar) render outside the_content.
add_filter( 'widget_block_content', 'bst_legacy_hero_tokens', 12 );
add_filter( 'widget_text_content', 'bst_legacy_hero_tokens', 12 );

/**
 * [bigspy_stat type="listing|category|sponsored|post|user|current_category"]:
 * one live counter, usable in a Counter, Shortcode or Code block.
 */
function bst_listing_stat_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'type' => 'listing' ), $atts, 'bigspy_stat' );
	$type = sanitize_key( $atts['type'] );
	$out  = bst_legacy_hero_tokens( '{{' . $type . '_count}}' );
	return '{{' === substr( $out, 0, 2 ) ? '' : esc_html( $out );
}
add_shortcode( 'bigspy_stat', 'bst_listing_stat_shortcode' );
