<?php
/**
 * Customizer sanitizers.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

function bst_sanitize_callback_for( $type ) {
	$map = array(
		'color'          => 'bst_sanitize_color',
		'number'         => 'bst_sanitize_number',
		'dropdown-pages' => 'absint',
		'select'         => 'bst_sanitize_choice',
		'radio'          => 'bst_sanitize_choice',
		'checkbox'       => 'bst_sanitize_checkbox',
		'text'           => 'sanitize_text_field',
		'url'            => 'esc_url_raw',
		'email'          => 'sanitize_email',
		'image'          => 'esc_url_raw',
		'html'           => 'wp_kses_post',
		'code'           => 'bst_sanitize_code',
		'header_builder' => 'bst_sanitize_header_layout',
	);
	return $map[ $type ] ?? 'sanitize_text_field';
}

function bst_sanitize_color( $value, $setting ) {
	$value = sanitize_hex_color( $value );
	return $value ? $value : $setting->default;
}

function bst_sanitize_checkbox( $value ) {
	return ! empty( $value ) && 'false' !== $value;
}

function bst_sanitize_choice( $value, $setting ) {
	$control = $setting->manager->get_control( $setting->id );
	$choices = $control ? $control->choices : array();
	$value   = (string) $value;
	return array_key_exists( $value, $choices ) ? $value : $setting->default;
}

function bst_sanitize_number( $value, $setting ) {
	if ( ! is_numeric( $value ) ) {
		return $setting->default;
	}
	$value   = $value + 0;
	$control = $setting->manager->get_control( $setting->id );
	$attrs   = $control ? (array) $control->input_attrs : array();
	if ( isset( $attrs['min'] ) ) {
		$value = max( $attrs['min'], $value );
	}
	if ( isset( $attrs['max'] ) ) {
		$value = min( $attrs['max'], $value );
	}
	return $value;
}

/**
 * Header/footer scripts: raw only for users allowed to post unfiltered HTML.
 */
function bst_sanitize_code( $value ) {
	return current_user_can( 'unfiltered_html' ) ? (string) $value : wp_kses_post( $value );
}

function bst_sanitize_header_layout( $value ) {
	return wp_json_encode( bst_normalize_header_layout( $value ) );
}
