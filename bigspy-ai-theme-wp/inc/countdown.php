<?php
/** Fixed-date countdown: no visitor-specific resetting or artificial urgency. */
defined( 'ABSPATH' ) || exit;
add_action( 'init', function () {
	register_block_type( 'bst/countdown', array( 'api_version' => 3, 'title' => 'Countdown', 'category' => 'bigspy', 'icon' => 'clock', 'attributes' => array( 'deadline' => array( 'type' => 'string', 'default' => '' ), 'label' => array( 'type' => 'string', 'default' => 'Thời gian còn lại' ), 'ended' => array( 'type' => 'string', 'default' => 'Đã kết thúc' ) ), 'editor_script' => 'bst-countdown-editor', 'render_callback' => 'bst_countdown_render' ) );
	wp_register_script( 'bst-countdown-editor', BST_URI . '/assets/js/countdown-editor.js', array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components' ), bst_asset_version( 'assets/js/countdown-editor.js' ), true );
}, 21 );
function bst_countdown_render( $a ) {
	$date = isset( $a['deadline'] ) ? (string) $a['deadline'] : '';
	// Require a timezone so editor and visitor agree on the same instant.
	if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}(?::\d{2})?(?:Z|[+-]\d{2}:\d{2})$/', $date ) ) { return ''; }
	$deadline = strtotime( $date );
	if ( false === $deadline ) { return ''; }
	wp_enqueue_script( 'bst-countdown', BST_URI . '/assets/js/countdown.js', array(), bst_asset_version( 'assets/js/countdown.js' ), true );
	wp_enqueue_style( 'bst-countdown', BST_URI . '/assets/css/countdown.css', array(), bst_asset_version( 'assets/css/countdown.css' ) );
	return '<div class="bs-countdown" data-bs-deadline="' . esc_attr( gmdate( 'c', $deadline ) ) . '" data-bs-ended="' . esc_attr( $a['ended'] ?? 'Đã kết thúc' ) . '"><span>' . esc_html( $a['label'] ?? 'Thời gian còn lại' ) . '</span><time datetime="' . esc_attr( gmdate( 'c', $deadline ) ) . '">' . esc_html( wp_date( 'd/m/Y H:i T', $deadline ) ) . '</time></div>';
}
add_action( 'init', function () {
	global $shortcode_tags;
	if ( shortcode_exists( 'ux_countdown' ) && '__return_empty_string' !== $shortcode_tags['ux_countdown'] ) { return; }
	add_shortcode( 'ux_countdown', function ( $a ) {
		$a = shortcode_atts( array( 'year' => '', 'month' => '', 'day' => '', 'time' => '00:00', 'text' => 'Thời gian còn lại' ), $a );
		if ( ! $a['year'] || ! $a['month'] || ! $a['day'] ) { return ''; }
		$date = sprintf( '%04d-%02d-%02d %s', absint( $a['year'] ), absint( $a['month'] ), absint( $a['day'] ), sanitize_text_field( $a['time'] ) );
		try { $deadline = new DateTimeImmutable( $date, wp_timezone() ); } catch ( Exception $e ) { return ''; }
		return bst_countdown_render( array( 'deadline' => $deadline->format( 'c' ), 'label' => $a['text'] ) );
	} );
}, 30 );
