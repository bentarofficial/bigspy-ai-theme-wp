<?php
/**
 * G-201: site-wide design tokens (Style → Global style).
 *
 * One place for corner radius, shadow (3 levels) and section spacing. Cards
 * (blog, products, listings, boxed sidebar widgets) and the Section block read
 * them. Empty = the theme's current look, so updating changes nothing until set.
 * A more specific option (Shop card radius, Listing design radius/shadow) still wins.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Shadow levels, also printed as --bs-shadow-sm/md/lg for blocks and custom CSS. */
function bst_token_shadows() {
	return array(
		'sm' => '0 1px 3px rgba(15,23,42,.08)',
		'md' => '0 4px 14px rgba(15,23,42,.1)',
		'lg' => '0 10px 30px rgba(15,23,42,.16)',
	);
}

add_filter(
	'bst_customizer_sections',
	function ( $sections ) {
		$sections['bst_global_style'] = array(
			'title'       => __( 'Global style (radius, shadow, spacing)', 'bigspy-ai-theme-wp' ),
			'panel'       => 'bst_style',
			'priority'    => 0,
			'description' => __( 'Change once, applies to blog cards, product cards, listing cards, boxed sidebar widgets and Section blocks. Empty = keep the theme default. Options in Shop or Listing design still override these.', 'bigspy-ai-theme-wp' ),
		);
		return $sections;
	}
);

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$s        = 'bst_global_style';
		$fields[] = array( 'id' => 'gs_radius', 'section' => $s, 'type' => 'number', 'label' => __( 'Card corner radius (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 0, 'max' => 40, 'step' => 1, 'placeholder' => __( 'Theme default', 'bigspy-ai-theme-wp' ) ) );
		$fields[] = array(
			'id'      => 'gs_shadow',
			'section' => $s,
			'type'    => 'select',
			'label'   => __( 'Card shadow', 'bigspy-ai-theme-wp' ),
			'choices' => array(
				''     => __( 'Theme default', 'bigspy-ai-theme-wp' ),
				'none' => __( 'None', 'bigspy-ai-theme-wp' ),
				'sm'   => __( 'Small', 'bigspy-ai-theme-wp' ),
				'md'   => __( 'Medium', 'bigspy-ai-theme-wp' ),
				'lg'   => __( 'Large', 'bigspy-ai-theme-wp' ),
			),
		);
		$fields[] = array( 'id' => 'gs_section_space', 'section' => $s, 'type' => 'responsive', 'label' => __( 'Section block spacing, top and bottom (px)', 'bigspy-ai-theme-wp' ), 'description' => __( 'Used by Section blocks that keep the default padding (80 / 60 / 40 px).', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 0, 'max' => 300, 'step' => 1 ) );
		return $fields;
	}
);

/** CSS for the tokens; '' while nothing is set except the shadow scale. */
function bst_design_tokens_css() {
	$shadows = bst_token_shadows();
	$vars    = array();
	foreach ( $shadows as $k => $v ) {
		$vars[] = '--bs-shadow-' . $k . ':' . $v;
	}
	$space = array( '' => 'gs_section_space', '-t' => 'gs_section_space_tablet', '-m' => 'gs_section_space_mobile' );
	foreach ( $space as $suffix => $id ) {
		$v = get_theme_mod( $id, '' );
		if ( '' !== $v && null !== $v && is_numeric( $v ) ) {
			$vars[] = '--bs-gs-sec' . $suffix . ':' . max( 0, min( 300, (int) $v ) ) . 'px';
		}
	}
	$css = ':root{' . implode( ';', $vars ) . '}';

	$listing_own = function ( $key ) {
		return function_exists( 'bst_listing_design_get' ) && '' !== (string) bst_listing_design_get( $key );
	};
	$radius = get_theme_mod( 'gs_radius', '' );
	if ( '' !== $radius && null !== $radius && is_numeric( $radius ) ) {
		$r    = max( 0, min( 40, (int) $radius ) ) . 'px';
		$sel  = array( 'html .bs-card', 'html .bs-sb-boxed .bs-layout__sidebar .widget', 'html .bs-sb-shadow .bs-layout__sidebar .widget', 'html .bs-sb-panel .bs-layout__sidebar' );
		$css .= ':root{--bs-radius:' . $r . '}' . implode( ',', $sel ) . '{border-radius:' . $r . '}html .bs-product{--bs-card-radius:' . $r . '}';
		if ( ! $listing_own( 'ld_radius' ) ) {
			$css .= 'html .bst-lcard{border-radius:' . $r . '}';
		}
	}
	$shadow = (string) get_theme_mod( 'gs_shadow', '' );
	if ( 'none' === $shadow || isset( $shadows[ $shadow ] ) ) {
		$s    = 'none' === $shadow ? 'none' : 'var(--bs-shadow-' . $shadow . ')';
		$css .= 'html .bs-card,html .bs-product,html .bs-sb-shadow .bs-layout__sidebar .widget{box-shadow:' . $s . '}';
		if ( ! $listing_own( 'ld_shadow' ) ) {
			$css .= 'html .bst-lcard{box-shadow:' . $s . '}';
		}
	}
	return wp_strip_all_tags( $css );
}
add_filter(
	'bst_dynamic_css',
	function ( $css ) {
		return $css . bst_design_tokens_css();
	}
);
