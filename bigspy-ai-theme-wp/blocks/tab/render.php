<?php
/**
 * Tab panel block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;
$bst_tab_anchor = sanitize_title( (string) ( $attributes['anchor'] ?? '' ) );
?>
<div class="bs-tab"<?php echo $bst_tab_anchor ? ' id="' . esc_attr( $bst_tab_anchor ) . '"' : ''; ?>><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
