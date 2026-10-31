<?php
/** Horizontal Scroll: a tall wrapper with a sticky viewport; motion-scroll.js maps vertical progress to a sideways move. */
defined( 'ABSPATH' ) || exit;
$a     = $attributes;
$style = '--bs-hs-w:' . bst_block_num( $a, 'panelWidth', 70, 30, 100 ) . 'vw;--bs-hs-w-m:' . bst_block_num( $a, 'panelWidthMobile', 85, 50, 100 ) . 'vw;--bs-hs-gap:' . bst_block_num( $a, 'gap', 32, 0, 120 ) . 'px';
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-hscroll', 'style' => $style, 'data-bs-hscroll' => '1', 'data-prev' => __( 'Previous panel', 'bigspy-ai-theme-wp' ), 'data-next' => __( 'Next panel', 'bigspy-ai-theme-wp' ), 'role' => 'region', 'aria-label' => '' !== trim( (string) ( $a['label'] ?? '' ) ) ? trim( (string) $a['label'] ) : __( 'Horizontal Scroll', 'bigspy-ai-theme-wp' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><div class="bs-hscroll__sticky"><div class="bs-hscroll__track"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div></div></div>
