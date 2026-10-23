<?php
/**
 * Widget area (sidebar) block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a = (array) $attributes;
$area = sanitize_key( $a['area'] ?? '' );
if ( ! $area || ! is_active_sidebar( $area ) ) {
	if ( current_user_can( 'edit_theme_options' ) && ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		echo '<p class="bs-header-hint">' . esc_html__( 'This widget area is empty. Add widgets in Appearance → Widgets.', 'bigspy-ai-theme-wp' ) . '</p>';
	}
	return;
}
ob_start();
dynamic_sidebar( $area );
printf( '<div %1$s>%2$s</div>', get_block_wrapper_attributes( array( 'class' => 'bs-widget-area widget-area' ) ), ob_get_clean() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
