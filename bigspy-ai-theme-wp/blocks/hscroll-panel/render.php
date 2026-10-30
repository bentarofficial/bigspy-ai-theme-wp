<?php
/** One Horizontal Scroll panel. */
defined( 'ABSPATH' ) || exit;
$a     = $attributes;
$style = bst_style_vars( array( '--bs-hs-bg' => bst_block_color( $a, 'background' ), '--bs-hs-fg' => bst_block_color( $a, 'textColor' ) ) );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-hscroll__panel', 'style' => $style ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
