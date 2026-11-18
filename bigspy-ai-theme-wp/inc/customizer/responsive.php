<?php
/**
 * G-199: shared "Responsive" (value per device) and "Spacing" (4 sides) options.
 *
 * Field types in bst_customizer_fields():
 * - 'responsive': the field id keeps the desktop value (old values stay as desktop);
 *   "<id>_tablet" and "<id>_mobile" are added, empty = same as the larger screen.
 * - 'spacing': one JSON theme mod {unit, desktop:[t,r,b,l], tablet:[…], mobile:[…]}.
 * Both print CSS variables: desktop in :root, tablet and mobile inside the theme's
 * breakpoints (849px / 549px, the same as main.css).
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Devices, their Customizer preview names and the max-width they apply below. */
function bst_responsive_devices() {
	return array(
		'desktop' => array( 'label' => __( 'Desktop', 'bigspy-ai-theme-wp' ), 'preview' => 'desktop', 'icon' => 'dashicons-desktop', 'max' => 0 ),
		'tablet'  => array( 'label' => __( 'Tablet', 'bigspy-ai-theme-wp' ), 'preview' => 'tablet', 'icon' => 'dashicons-tablet', 'max' => 849 ),
		'mobile'  => array( 'label' => __( 'Mobile', 'bigspy-ai-theme-wp' ), 'preview' => 'mobile', 'icon' => 'dashicons-smartphone', 'max' => 549 ),
	);
}

/** Empty string (inherit) or a number clamped to the desktop control's min/max. */
function bst_sanitize_number_or_empty( $value, $setting ) {
	if ( '' === $value || null === $value || ! is_numeric( $value ) ) {
		return '';
	}
	$value   = $value + 0;
	$control = $setting->manager->get_control( preg_replace( '/_(tablet|mobile)$/', '', $setting->id ) );
	$attrs   = $control ? (array) $control->input_attrs : array();
	if ( isset( $attrs['min'] ) ) {
		$value = max( $attrs['min'], $value );
	}
	if ( isset( $attrs['max'] ) ) {
		$value = min( $attrs['max'], $value );
	}
	return $value;
}

/** Spacing JSON: whitelisted unit, four numbers or empty strings per device. */
function bst_sanitize_spacing( $value ) {
	$data = is_array( $value ) ? $value : json_decode( (string) $value, true );
	if ( ! is_array( $data ) ) {
		return '';
	}
	$units = array( 'px', 'em', 'rem', '%', 'vw', 'vh' );
	$out   = array( 'unit' => in_array( $data['unit'] ?? 'px', $units, true ) ? $data['unit'] : 'px' );
	$any   = false;
	foreach ( array_keys( bst_responsive_devices() ) as $device ) {
		$sides = array_values( (array) ( $data[ $device ] ?? array() ) );
		$clean = array();
		for ( $i = 0; $i < 4; $i++ ) {
			$side      = $sides[ $i ] ?? '';
			$clean[]   = ( '' !== $side && is_numeric( $side ) ) ? (string) max( -500, min( 1000, round( (float) $side, 2 ) ) ) : '';
		}
		if ( implode( '', $clean ) !== '' ) {
			$out[ $device ] = $clean;
			$any            = true;
		}
	}
	return $any ? wp_json_encode( $out ) : '';
}

/** "48px 0px 48px 0px" for one device, '' when that device has no value. */
function bst_spacing_css_value( $json, $device ) {
	$data = json_decode( (string) $json, true );
	if ( ! is_array( $data ) || empty( $data[ $device ] ) ) {
		return '';
	}
	$unit  = $data['unit'] ?? 'px';
	$parts = array();
	foreach ( $data[ $device ] as $side ) {
		$parts[] = ( '' === $side ? '0' : $side ) . ( '' === $side || '0' === (string) $side ? '' : $unit );
	}
	return implode( ' ', $parts );
}

/** Spacing option id => CSS variable. */
function bst_spacing_var_map() {
	return apply_filters(
		'bst_spacing_var_map',
		array(
			'title_bar_padding' => '--bs-titlebar-pad',
		)
	);
}

/** Responsive option id => array( css variable, unit ), taken from the field list. */
function bst_responsive_var_map() {
	static $map = null;
	if ( null !== $map ) {
		return $map;
	}
	$map  = array();
	$vars = bst_css_var_map();
	foreach ( bst_customizer_fields() as $field ) {
		if ( 'responsive' === ( $field['type'] ?? '' ) && isset( $vars[ $field['id'] ] ) ) {
			$map[ $field['id'] ] = $vars[ $field['id'] ];
		}
	}
	return $map;
}

/** CSS for tablet/mobile overrides and spacing variables. */
function bst_responsive_css() {
	$buckets = array( 'desktop' => array(), 'tablet' => array(), 'mobile' => array() );
	foreach ( bst_responsive_var_map() as $id => $def ) {
		foreach ( array( 'tablet', 'mobile' ) as $device ) {
			$value = get_theme_mod( $id . '_' . $device, '' );
			if ( '' !== $value && null !== $value && is_numeric( $value ) ) {
				$buckets[ $device ][] = $def[0] . ':' . ( $value + 0 ) . $def[1];
			}
		}
	}
	foreach ( bst_spacing_var_map() as $id => $var ) {
		$json = get_theme_mod( $id, '' );
		foreach ( array_keys( $buckets ) as $device ) {
			$value = bst_spacing_css_value( $json, $device );
			if ( '' !== $value ) {
				$buckets[ $device ][] = $var . ':' . $value;
			}
		}
	}
	$css     = $buckets['desktop'] ? ':root{' . implode( ';', $buckets['desktop'] ) . '}' : '';
	$devices = bst_responsive_devices();
	foreach ( array( 'tablet', 'mobile' ) as $device ) {
		if ( $buckets[ $device ] ) {
			$css .= '@media (max-width:' . (int) $devices[ $device ]['max'] . 'px){:root{' . implode( ';', $buckets[ $device ] ) . '}}';
		}
	}
	// Headings scale down with clamp(); an explicit smaller-screen size should win as typed.
	foreach ( array( 'tablet', 'mobile' ) as $device ) {
		$rules = array();
		foreach ( array( 'h1', 'h2', 'h3' ) as $tag ) {
			$value = get_theme_mod( $tag . '_size_' . $device, '' );
			if ( '' !== $value && null !== $value && is_numeric( $value ) ) {
				$rules[] = $tag . '{font-size:var(--bs-' . $tag . ')}';
			}
		}
		if ( $rules ) {
			$css .= '@media (max-width:' . (int) $devices[ $device ]['max'] . 'px){' . implode( '', $rules ) . '}';
		}
	}
	return wp_strip_all_tags( $css );
}
add_filter(
	'bst_dynamic_css',
	function ( $css ) {
		return $css . bst_responsive_css();
	}
);
