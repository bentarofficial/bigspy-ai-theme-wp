<?php
/**
 * Shortcode block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a = (array) $attributes;
$code = trim( (string) ( $a['code'] ?? '' ) );
if ( '' === $code ) {
	return;
}
printf( '<div %1$s>%2$s</div>', get_block_wrapper_attributes( array( 'class' => 'bs-shortcode' ) ), do_shortcode( shortcode_unautop( wpautop( $code ) ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
