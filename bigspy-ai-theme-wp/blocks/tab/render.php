<?php
/**
 * Tab panel block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a    = (array) $attributes;
$slug = sanitize_title( '' !== trim( (string) ( $a['slug'] ?? '' ) ) ? $a['slug'] : wp_strip_all_tags( (string) ( $a['title'] ?? '' ) ) );
$link = (string) ( $a['link'] ?? '' );
?>
<div class="bs-tab"<?php echo $slug ? ' data-slug="' . esc_attr( $slug ) . '"' : ''; ?><?php echo $link ? ' data-link="' . esc_url( $link ) . '"' : ''; ?>><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
