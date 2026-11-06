<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

$a = is_array( $attributes ?? null ) ? $attributes : array();
$html = (string) ( $a['code'] ?? '' );
if ( '' === trim( $html ) ) return;

/*
 * Render author-provided HTML instead of displaying it as a code sample.
 * wp_kses_post() keeps normal content markup and safe inline style properties,
 * while removing scripts, event handlers and executable content.
 */
echo '<div ' . get_block_wrapper_attributes( array( 'class' => 'bigspy-html-content' ) ) . '>';
echo wp_kses_post( $html );
echo '</div>';
