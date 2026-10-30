<?php
/** One stacking card; motion-scroll.js sets its deck position (--i). */
defined( 'ABSPATH' ) || exit;
$a     = $attributes;
$style = bst_style_vars( array( '--bs-stack-bg' => bst_block_color( $a, 'background' ), '--bs-stack-fg' => bst_block_color( $a, 'textColor' ) ) );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-stack__card', 'style' => $style ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><div class="bs-stack__inner"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div></div>
