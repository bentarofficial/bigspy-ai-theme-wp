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
		'system'           => array( 'label' => __( 'System (fastest)', 'bigspy-ai-theme-wp' ), 'stack' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif', 'google' => false ),
		'system-serif'     => array( 'label' => __( 'System serif', 'bigspy-ai-theme-wp' ), 'stack' => 'Georgia, "Times New Roman", Times, serif', 'google' => false ),
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
		// More Google fonts with full Vietnamese support (checked against the Google Fonts subsets list).
		// Sans-serif.
		'Plus Jakarta Sans'     => array( 'stack' => '"Plus Jakarta Sans"' . $sans ),
		'Manrope'               => array( 'stack' => '"Manrope"' . $sans ),
		'Raleway'               => array( 'stack' => '"Raleway"' . $sans ),
		'Work Sans'             => array( 'stack' => '"Work Sans"' . $sans ),
		'Nunito Sans'           => array( 'stack' => '"Nunito Sans"' . $sans ),
		'Arimo'                 => array( 'stack' => '"Arimo"' . $sans ),
		'Space Grotesk'         => array( 'stack' => '"Space Grotesk"' . $sans ),
		'Cabin'                 => array( 'stack' => '"Cabin"' . $sans ),
		'Archivo'               => array( 'stack' => '"Archivo"' . $sans ),
		'Overpass'              => array( 'stack' => '"Overpass"' . $sans ),
		'Hanken Grotesk'        => array( 'stack' => '"Hanken Grotesk"' . $sans ),
		'IBM Plex Sans'         => array( 'stack' => '"IBM Plex Sans"' . $sans ),
		'Fira Sans'             => array( 'stack' => '"Fira Sans"' . $sans ),
		'Inter Tight'           => array( 'stack' => '"Inter Tight"' . $sans ),
		'League Spartan'        => array( 'stack' => '"League Spartan"' . $sans ),
		'Encode Sans'           => array( 'stack' => '"Encode Sans"' . $sans ),
		'Libre Franklin'        => array( 'stack' => '"Libre Franklin"' . $sans ),
		'Public Sans'           => array( 'stack' => '"Public Sans"' . $sans ),
		'Geist'                 => array( 'stack' => '"Geist"' . $sans ),
		'Bricolage Grotesque'   => array( 'stack' => '"Bricolage Grotesque"' . $sans ),
		'Merriweather Sans'     => array( 'stack' => '"Merriweather Sans"' . $sans ),
		'Signika'               => array( 'stack' => '"Signika"' . $sans ),
		'Epilogue'              => array( 'stack' => '"Epilogue"' . $sans ),
		'Asap'                  => array( 'stack' => '"Asap"' . $sans ),
		'Maven Pro'             => array( 'stack' => '"Maven Pro"' . $sans ),
		'Exo 2'                 => array( 'stack' => '"Exo 2"' . $sans ),
		'Varela Round'          => array( 'stack' => '"Varela Round"' . $sans ),
		'Dosis'                 => array( 'stack' => '"Dosis"' . $sans ),
		'Saira'                 => array( 'stack' => '"Saira"' . $sans ),
		'Chakra Petch'          => array( 'stack' => '"Chakra Petch"' . $sans ),
		'Questrial'             => array( 'stack' => '"Questrial"' . $sans ),
		'Philosopher'           => array( 'stack' => '"Philosopher"' . $sans ),
		'Comfortaa'             => array( 'stack' => '"Comfortaa"' . $sans ),
		// Condensed / display.
		'Barlow Condensed'      => array( 'stack' => '"Barlow Condensed"' . $sans ),
		'Archivo Narrow'        => array( 'stack' => '"Archivo Narrow"' . $sans ),
		'Fira Sans Condensed'   => array( 'stack' => '"Fira Sans Condensed"' . $sans ),
		'Anton'                 => array( 'stack' => '"Anton"' . $sans ),
		'Fjalla One'            => array( 'stack' => '"Fjalla One"' . $sans ),
		// Serif.
		'Roboto Slab'           => array( 'stack' => '"Roboto Slab"' . $serif ),
		'EB Garamond'           => array( 'stack' => '"EB Garamond"' . $serif ),
		'Cormorant Garamond'    => array( 'stack' => '"Cormorant Garamond"' . $serif ),
		'Playfair'              => array( 'stack' => '"Playfair"' . $serif ),
		'Fraunces'              => array( 'stack' => '"Fraunces"' . $serif ),
		'Spectral'              => array( 'stack' => '"Spectral"' . $serif ),
		'IBM Plex Serif'        => array( 'stack' => '"IBM Plex Serif"' . $serif ),
		'Crimson Pro'           => array( 'stack' => '"Crimson Pro"' . $serif ),
		'Bitter'                => array( 'stack' => '"Bitter"' . $serif ),
		'Source Serif 4'        => array( 'stack' => '"Source Serif 4"' . $serif ),
		'Newsreader'            => array( 'stack' => '"Newsreader"' . $serif ),
		'Roboto Serif'          => array( 'stack' => '"Roboto Serif"' . $serif ),
		'Libre Bodoni'          => array( 'stack' => '"Libre Bodoni"' . $serif ),
		'Alfa Slab One'         => array( 'stack' => '"Alfa Slab One"' . $serif ),
		// Monospace (code).
		'Roboto Mono'           => array( 'stack' => '"Roboto Mono", monospace' ),
		'JetBrains Mono'        => array( 'stack' => '"JetBrains Mono", monospace' ),
		'Source Code Pro'       => array( 'stack' => '"Source Code Pro", monospace' ),
		'IBM Plex Mono'         => array( 'stack' => '"IBM Plex Mono", monospace' ),
		// Handwriting & display.
		'Pacifico'              => array( 'stack' => '"Pacifico", cursive' ),
		'Merienda'              => array( 'stack' => '"Merienda", cursive' ),
		'Amatic SC'             => array( 'stack' => '"Amatic SC", cursive' ),
		'Bungee'                => array( 'stack' => '"Bungee", cursive' ),
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
