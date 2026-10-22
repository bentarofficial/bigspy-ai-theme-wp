<?php
/**
 * Form block: renders a Contact Form 7 / WPForms form inside a styled wrapper.
 * Every look (templates, fields, labels, button, box) is set from block options – no CSS needed.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a    = (array) $attributes;
$form = function_exists( 'bst_form_markup' ) ? bst_form_markup( sanitize_key( $a['provider'] ?? 'cf7' ), $a['formId'] ?? 0 ) : '';
if ( '' === trim( $form ) ) {
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		echo '<p class="bs-form__empty">' . esc_html__( 'Choose a form in the block settings (Contact Form 7 or WPForms must be active).', 'bigspy-ai-theme-wp' ) . '</p>';
	}
	return;
}

$pick   = function ( $key, $allowed, $default ) use ( $a ) {
	return bst_choice( (string) ( $a[ $key ] ?? $default ), $allowed, $default );
};
$box    = $pick( 'boxStyle', array( 'none', 'card', 'soft', 'glass', 'dark', 'gradient-border', 'gradient' ), ! empty( $a['card'] ) ? 'card' : 'none' );
$layout = $pick( 'layout', array( 'stack', '2col', 'inline' ), ! empty( $a['twoColumns'] ) ? '2col' : 'stack' );
$labels = $pick( 'labelStyle', array( 'top', 'small', 'hidden' ), ! empty( $a['hideLabels'] ) ? 'hidden' : 'top' );

$classes = array(
	'bs-form',
	'bs-form--' . $pick( 'formStyle', array( 'default', 'outline', 'underline', 'filled', 'rounded', 'shadow' ), 'default' ),
	'bs-form--box-' . $box,
	'bs-form--layout-' . $layout,
	'bs-form--labels-' . $labels,
	'bs-form--size-' . $pick( 'fieldSize', array( 'sm', 'md', 'lg' ), 'md' ),
	'bs-form--btn-' . $pick( 'buttonStyle', array( 'solid', 'gradient', 'outline', 'pill', 'shade', 'glow' ), 'solid' ),
	'bs-form--btn-align-' . $pick( 'buttonAlign', array( 'left', 'center', 'right', 'full' ), ! empty( $a['buttonFull'] ) ? 'full' : 'left' ),
	'bs-form--align-' . $pick( 'align', array( 'left', 'center' ), 'left' ),
	'bs-form--fx-' . $pick( 'buttonEffect', array( 'none', 'lift', 'grow', 'shine', 'pulse', 'fill', 'bounce' ), 'lift' ),
);
if ( ! empty( $a['buttonIcon'] ) ) {
	$classes[] = 'has-btn-arrow';
}
if ( ! empty( $a['buttonBg'] ) || ! empty( $a['buttonText'] ) ) {
	$classes[] = 'has-btn-colors';
}
$shadows = function_exists( 'bst_design_shadows' ) ? bst_design_shadows() : array();
$depth   = isset( $a['boxShadow'] ) ? (int) $a['boxShadow'] : -1;
if ( ! empty( $a['imageUrl'] ) ) {
	$classes[] = 'has-image';
	$classes[] = 'bs-form--image-' . $pick( 'imagePosition', array( 'left', 'right', 'background' ), 'left' );
}

$vars = bst_style_vars(
	array(
		'--bs-form-accent'  => bst_block_color( $a, 'accent' ),
		'--bs-form-accent2' => bst_block_color( $a, 'accent2' ),
		'--bs-form-field'   => bst_block_color( $a, 'fieldBg' ),
		'--bs-form-border'  => bst_block_color( $a, 'borderColor' ),
		'--bs-form-label'   => bst_block_color( $a, 'labelColor' ),
		'--bs-form-text'    => bst_block_color( $a, 'textColor' ),
		'--bs-form-boxbg'   => bst_block_color( $a, 'boxBg' ),
		'--bs-form-radius'  => isset( $a['radius'] ) && $a['radius'] >= 0 ? bst_block_num( $a, 'radius', 8, 0, 40 ) . 'px' : '',
		'--bs-form-gap'     => isset( $a['gap'] ) && $a['gap'] >= 0 ? bst_block_num( $a, 'gap', 14, 0, 40 ) . 'px' : '',
		'--bs-form-bw'      => isset( $a['borderWidth'] ) && $a['borderWidth'] >= 0 ? bst_block_num( $a, 'borderWidth', 1, 0, 4 ) . 'px' : '',
		'--bs-form-max'     => ! empty( $a['maxWidth'] ) ? bst_block_num( $a, 'maxWidth', 600, 240, 1400 ) . 'px' : '',
		'--bs-form-btn'      => bst_block_color( $a, 'buttonBg' ),
		'--bs-form-btn-text' => bst_block_color( $a, 'buttonText' ),
		'--bs-form-btn-hbg'  => bst_block_color( $a, 'buttonHoverBg' ),
		'--bs-form-btn-htext' => bst_block_color( $a, 'buttonHoverText' ),
		'--bs-form-focus'    => bst_block_color( $a, 'focusColor' ),
		'--bs-form-box-bc'   => bst_block_color( $a, 'boxBorderColor' ),
		'--bs-form-box-bw'   => isset( $a['boxBorderWidth'] ) && $a['boxBorderWidth'] >= 0 ? bst_block_num( $a, 'boxBorderWidth', 0, 0, 8 ) . 'px' : '',
		'--bs-form-box-pad'  => isset( $a['boxPadding'] ) && $a['boxPadding'] >= 0 ? bst_block_num( $a, 'boxPadding', 32, 0, 100 ) . 'px' : '',
		'--bs-form-box-shadow' => 0 === $depth ? 'none' : ( isset( $shadows[ $depth ] ) ? $shadows[ $depth ] : '' ),
	)
);

$head = '';
if ( ! empty( $a['kicker'] ) ) {
	$head .= '<p class="bs-form__kicker">' . esc_html( $a['kicker'] ) . '</p>';
}
if ( ! empty( $a['title'] ) ) {
	$head .= '<h3 class="bs-form__title">' . wp_kses_post( $a['title'] ) . '</h3>';
}
if ( ! empty( $a['description'] ) ) {
	$head .= '<p class="bs-form__desc">' . wp_kses_post( $a['description'] ) . '</p>';
}
$note  = ! empty( $a['note'] ) ? '<p class="bs-form__note">' . bst_icon( 'shield', 14 ) . '<span>' . esc_html( $a['note'] ) . '</span></p>' : '';
$image = '';
if ( ! empty( $a['imageUrl'] ) ) {
	$image = '<div class="bs-form__image" style="' . esc_attr( 'background-image:url(' . esc_url( $a['imageUrl'] ) . ')' ) . '" role="img" aria-label="' . esc_attr( (string) ( $a['imageAlt'] ?? '' ) ) . '"></div>';
}
printf(
	'<div %1$s>%2$s<div class="bs-form__inner">%3$s<div class="bs-form__body">%4$s</div>%5$s</div></div>',
	get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ), 'style' => $vars ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$image, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$head, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$form, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$note // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
