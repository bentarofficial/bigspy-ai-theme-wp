<?php
/**
 * Font library. Every Google font listed here supports Vietnamese.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

function bst_fonts() {
	$sans  = ', sans-serif';
	$serif = ', serif';
	$fonts = array(
		'system'           => array( 'label' => __( 'System (fastest)', 'bigspy-theme' ), 'stack' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif', 'google' => false ),
		'system-serif'     => array( 'label' => __( 'System serif', 'bigspy-theme' ), 'stack' => 'Georgia, "Times New Roman", Times, serif', 'google' => false ),
		'Be Vietnam Pro'   => array( 'stack' => '"Be Vietnam Pro"' . $sans ),
		'Inter'            => array( 'stack' => '"Inter"' . $sans ),
		'Roboto'           => array( 'stack' => '"Roboto"' . $sans ),
		'Open Sans'        => array( 'stack' => '"Open Sans"' . $sans ),
		'Montserrat'       => array( 'stack' => '"Montserrat"' . $sans ),
		'Nunito'           => array( 'stack' => '"Nunito"' . $sans ),
		'Mulish'           => array( 'stack' => '"Mulish"' . $sans ),
		'Quicksand'        => array( 'stack' => '"Quicksand"' . $sans ),
		'Lexend'           => array( 'stack' => '"Lexend"' . $sans ),
		'Noto Sans'        => array( 'stack' => '"Noto Sans"' . $sans ),
		'Source Sans 3'    => array( 'stack' => '"Source Sans 3"' . $sans ),
		'Barlow'           => array( 'stack' => '"Barlow"' . $sans ),
		'Roboto Condensed' => array( 'stack' => '"Roboto Condensed"' . $sans ),
		'Oswald'           => array( 'stack' => '"Oswald"' . $sans ),
		'Josefin Sans'     => array( 'stack' => '"Josefin Sans"' . $sans ),
		'Noto Serif'       => array( 'stack' => '"Noto Serif"' . $serif ),
		'Lora'             => array( 'stack' => '"Lora"' . $serif ),
		'Merriweather'     => array( 'stack' => '"Merriweather"' . $serif ),
		'Playfair Display' => array( 'stack' => '"Playfair Display"' . $serif ),
		'Dancing Script'   => array( 'stack' => '"Dancing Script", cursive' ),
	);

	foreach ( $fonts as $key => $font ) {
		$fonts[ $key ] = wp_parse_args( $font, array( 'label' => $key, 'google' => true ) );
	}
	return apply_filters( 'bst_fonts', $fonts );
}

function bst_font_choices() {
	return wp_list_pluck( bst_fonts(), 'label' );
}

function bst_font_stack( $key ) {
	$fonts = bst_fonts();
	return isset( $fonts[ $key ] ) ? $fonts[ $key ]['stack'] : $fonts['system']['stack'];
}

/**
 * Google Fonts stylesheet URL for the selected fonts, or '' when only system fonts are used.
 */
function bst_google_fonts_url() {
	$fonts    = bst_fonts();
	$families = array();
	$map      = array(
		'font_body'    => array( bst_mod( 'font_weight_body' ), '700' ),
		'font_heading' => array( bst_mod( 'font_weight_heading' ) ),
		'font_nav'     => array( bst_mod( 'nav_weight' ) ),
	);

	foreach ( $map as $setting => $weights ) {
		$key = bst_mod( $setting );
		if ( empty( $fonts[ $key ]['google'] ) ) {
			continue;
		}
		$families[ $key ] = array_merge( $families[ $key ] ?? array(), array_map( 'absint', $weights ) );
	}

	if ( ! $families ) {
		return '';
	}

	// The v1 endpoint skips weights a family does not have (css2 rejects the
	// whole request), which matters for single-weight fonts such as Questrial.
	$parts = array();
	foreach ( $families as $family => $weights ) {
		$weights = array_unique( array_filter( $weights ) );
		sort( $weights );
		$parts[] = str_replace( ' ', '+', $family ) . ':' . implode( ',', $weights );
	}
	return 'https://fonts.googleapis.com/css?family=' . implode( '%7C', $parts ) . '&subset=latin,latin-ext,vietnamese&display=swap';
}

function bst_font_resource_hints( $urls, $relation ) {
	if ( 'preconnect' === $relation && bst_google_fonts_url() ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'bst_font_resource_hints', 10, 2 );
