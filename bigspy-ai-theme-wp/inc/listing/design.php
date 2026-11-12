<?php
/**
 * Listing design: one place to style every listing card on the site (frame, corners,
 * shadow, hover, spacing, logo). Empty fields fall back to what the old BIGSPY AI List
 * theme saved, so a switched site keeps its look. Block settings still win.
 *
 * @package BigSpyAITheme
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_customizer_sections',
	function ( $sections ) {
		$sections['bst_listing_design'] = array( 'title' => __( 'Listing design', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_layout', 'priority' => 5 );
		return $sections;
	}
);

/** Keys here => keys the old theme used. */
function bst_listing_design_keys() {
	return array(
		'ld_bg'         => 'bigspy_card_background',
		'ld_border'     => 'bigspy_card_border',
		'ld_radius'     => 'bigspy_card_radius',
		'ld_shadow'     => 'bigspy_card_shadow',
		'ld_hover'      => 'bigspy_card_hover_effect',
		'ld_hover_c'    => 'bigspy_card_hover_color',
		'ld_padding'    => 'bigspy_card_padding',
		'ld_gap'        => 'bigspy_card_gap',
		'ld_min_h'      => 'bigspy_card_min_height',
		'ld_logo'       => 'bigspy_card_logo_size',
		'ld_logo_shape' => 'bigspy_card_logo_shape',
	);
}

add_filter(
	'bst_defaults',
	function ( $d ) {
		return $d + array_fill_keys( array_keys( bst_listing_design_keys() ), '' ) + array( 'ld_disclosure' => '' );
	}
);

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$s   = 'bst_listing_design';
		$num = function ( $id, $label, $max ) use ( $s ) {
			return array( 'id' => $id, 'section' => $s, 'type' => 'number', 'label' => $label, 'input_attrs' => array( 'min' => 0, 'max' => $max ), 'description' => __( 'Empty = default.', 'bigspy-ai-theme-wp' ) );
		};
		$fields[] = array( 'id' => 'ld_bg', 'section' => $s, 'type' => 'color', 'label' => __( 'Card background', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'ld_border', 'section' => $s, 'type' => 'color', 'label' => __( 'Card border color', 'bigspy-ai-theme-wp' ) );
		$fields[] = $num( 'ld_radius', __( 'Corner radius (px)', 'bigspy-ai-theme-wp' ), 40 );
		$fields[] = array(
			'id'      => 'ld_shadow',
			'section' => $s,
			'type'    => 'select',
			'label'   => __( 'Shadow', 'bigspy-ai-theme-wp' ),
			'choices' => array( '' => __( 'Default', 'bigspy-ai-theme-wp' ), 'none' => __( 'None', 'bigspy-ai-theme-wp' ), 'soft' => __( 'Soft', 'bigspy-ai-theme-wp' ), 'medium' => __( 'Medium', 'bigspy-ai-theme-wp' ), 'strong' => __( 'Strong', 'bigspy-ai-theme-wp' ) ),
		);
		$fields[] = array(
			'id'      => 'ld_hover',
			'section' => $s,
			'type'    => 'select',
			'label'   => __( 'Hover effect', 'bigspy-ai-theme-wp' ),
			'choices' => array( '' => __( 'Default (lift + shadow)', 'bigspy-ai-theme-wp' ), 'lift' => __( 'Lift', 'bigspy-ai-theme-wp' ), 'shadow' => __( 'Shadow', 'bigspy-ai-theme-wp' ), 'border' => __( 'Border color', 'bigspy-ai-theme-wp' ), 'none' => __( 'None', 'bigspy-ai-theme-wp' ) ),
		);
		$fields[] = array( 'id' => 'ld_hover_c', 'section' => $s, 'type' => 'color', 'label' => __( 'Hover border color', 'bigspy-ai-theme-wp' ) );
		$fields[] = $num( 'ld_padding', __( 'Card padding (px)', 'bigspy-ai-theme-wp' ), 48 );
		$fields[] = $num( 'ld_gap', __( 'Gap between cards (px)', 'bigspy-ai-theme-wp' ), 60 );
		$fields[] = $num( 'ld_min_h', __( 'Minimum card height (px)', 'bigspy-ai-theme-wp' ), 600 );
		$fields[] = $num( 'ld_logo', __( 'Logo size (px)', 'bigspy-ai-theme-wp' ), 160 );
		$fields[] = array(
			'id'      => 'ld_logo_shape',
			'section' => $s,
			'type'    => 'select',
			'label'   => __( 'Logo shape', 'bigspy-ai-theme-wp' ),
			'choices' => array( '' => __( 'Default (rounded)', 'bigspy-ai-theme-wp' ), 'square' => __( 'Square', 'bigspy-ai-theme-wp' ), 'rounded' => __( 'Rounded', 'bigspy-ai-theme-wp' ), 'circle' => __( 'Circle', 'bigspy-ai-theme-wp' ) ),
		);
		$fields[] = array(
			'id'          => 'ld_disclosure',
			'section'     => $s,
			'type'        => 'select',
			'label'       => __( 'Disclosure line on listings', 'bigspy-ai-theme-wp' ),
			'description' => __( 'The line under the button of sponsored and affiliate listings. Hide it when the site has its own disclosure page linked from every page.', 'bigspy-ai-theme-wp' ),
			'choices'     => array( '' => __( 'Sponsored and affiliate lines', 'bigspy-ai-theme-wp' ), 'sponsored' => __( 'Sponsored line only', 'bigspy-ai-theme-wp' ), 'none' => __( 'Hide', 'bigspy-ai-theme-wp' ) ),
		);
		return $fields;
	}
);

/** A setting, or the old theme's value when empty. */
function bst_listing_design_get( $key ) {
	$v = bst_mod( $key );
	if ( ( null === $v || '' === $v ) && function_exists( 'bst_legacy_list_mod' ) ) {
		$keys = bst_listing_design_keys();
		$v    = bst_legacy_list_mod( $keys[ $key ], '' );
	}
	return is_scalar( $v ) ? (string) $v : '';
}

/** CSS for the chosen look; empty when nothing is set. */
function bst_listing_design_css() {
	$px  = function ( $k, $max ) {
		$v = bst_listing_design_get( $k );
		return is_numeric( $v ) ? max( 0, min( $max, (int) $v ) ) . 'px' : '';
	};
	$col = function ( $k ) {
		return (string) sanitize_hex_color( bst_listing_design_get( $k ) );
	};
	$card = array(
		'background'    => $col( 'ld_bg' ),
		'border-color'  => $col( 'ld_border' ),
		'border-radius' => $px( 'ld_radius', 40 ),
		'padding'       => $px( 'ld_padding', 48 ),
		'min-height'    => $px( 'ld_min_h', 600 ),
		'box-shadow'    => array( 'none' => 'none', 'soft' => '0 1px 3px rgba(15,23,42,.08)', 'medium' => '0 4px 14px rgba(15,23,42,.1)', 'strong' => '0 10px 30px rgba(15,23,42,.16)' )[ bst_listing_design_get( 'ld_shadow' ) ] ?? '',
	);
	$css  = '';
	$decl = '';
	foreach ( $card as $p => $v ) {
		$decl .= '' !== $v ? $p . ':' . $v . ';' : '';
	}
	if ( $decl ) {
		$css .= '.bst-lcard{' . $decl . '}';
	}
	$hover = bst_listing_design_get( 'ld_hover' );
	$hc    = $col( 'ld_hover_c' );
	$rules = array(
		'none'   => 'transform:none;box-shadow:' . ( $card['box-shadow'] ? $card['box-shadow'] : 'none' ),
		'lift'   => 'transform:translateY(-3px)',
		'shadow' => 'transform:none;box-shadow:0 10px 28px rgba(15,23,42,.14)',
		'border' => 'transform:none;box-shadow:none',
	);
	if ( isset( $rules[ $hover ] ) || $hc ) {
		$css .= '.bst-lcard:hover{' . ( $rules[ $hover ] ?? '' ) . ( $hc ? ';border-color:' . $hc : '' ) . '}';
	}
	$gap = $px( 'ld_gap', 60 );
	if ( $gap ) {
		$css .= ':root{--bst-lgap:' . $gap . '}';
	}
	$logo  = $px( 'ld_logo', 160 );
	$shape = array( 'square' => '0', 'rounded' => '10px', 'circle' => '50%' )[ bst_listing_design_get( 'ld_logo_shape' ) ] ?? '';
	if ( $logo || $shape ) {
		$css .= '.bst-lcard__logo{' . ( $logo ? 'width:' . $logo . ';height:' . $logo . ';' : '' ) . ( '' !== $shape ? 'border-radius:' . $shape : '' ) . '}';
	}
	return $css;
}

/** Register the listing stylesheet early so the look applies wherever a block enqueues it. */
function bst_listing_design_register() {
	static $done = false;
	if ( $done ) {
		return;
	}
	$done = true;
	if ( ! wp_style_is( 'bst-listing', 'registered' ) ) {
		wp_register_style( 'bst-listing', BST_URI . '/assets/css/listing.css', array(), bst_asset_version( 'assets/css/listing.css' ) );
	}
	$css = bst_listing_design_css();
	if ( '' !== $css ) {
		wp_add_inline_style( 'bst-listing', $css );
	}
}
add_action( 'wp_enqueue_scripts', 'bst_listing_design_register', 1 );
add_action( 'enqueue_block_assets', 'bst_listing_design_register', 1 );
