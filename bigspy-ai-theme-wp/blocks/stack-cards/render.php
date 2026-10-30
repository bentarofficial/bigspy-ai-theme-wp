<?php
/** Stacking Cards: CSS sticky does the stacking; motion-scroll.js adds the shrink/dim depth. */
defined( 'ABSPATH' ) || exit;
$a     = $attributes;
$style = '--bs-stack-top:' . bst_block_num( $a, 'top', 90, 0, 400 ) . 'px;--bs-stack-step:' . bst_block_num( $a, 'step', 24, 0, 80 ) . 'px;--bs-stack-min:' . bst_block_num( $a, 'minHeight', 320, 120, 1200 ) . 'px;--bs-stack-radius:' . bst_block_num( $a, 'radius', 24, 0, 80 ) . 'px;--bs-stack-gap:' . bst_block_num( $a, 'gap', 40, 0, 200 ) . 'px';
$class = 'bs-stack' . ( ! empty( $a['shadow'] ) ? ' bs-stack--shadow' : '' ) . ( ! empty( $a['scaleEffect'] ) ? ' bs-stack--scale' : '' );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => $class, 'style' => $style, 'data-bs-stack' => '1', 'role' => 'region', 'aria-label' => '' !== trim( (string) ( $a['label'] ?? '' ) ) ? trim( (string) $a['label'] ) : __( 'Stacking Cards', 'bigspy-ai-theme-wp' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
