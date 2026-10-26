<?php
defined( 'ABSPATH' ) || exit;
$a = $attributes;
$style = bst_style_vars( array( '--bs-expand-factor' => bst_block_num( $a, 'factor', 2, 1, 8 ), '--bs-expand-time' => bst_block_num( $a, 'duration', .5, 0, 3 ) . 's', '--bs-expand-hd' => bst_block_num( $a, 'heightDesktop', 480, 180, 1200 ) . 'px', '--bs-expand-ht' => bst_block_num( $a, 'heightTablet', 420, 180, 1200 ) . 'px', '--bs-expand-hm' => bst_block_num( $a, 'heightMobile', 400, 120, 1000 ) . 'px', '--bs-expand-gap' => bst_block_num( $a, 'gap', 12, 0, 80 ) . 'px', '--bs-expand-radius' => bst_block_num( $a, 'radius', 16, 0, 100 ) . 'px', '--bs-expand-ratio' => bst_block_num( $a, 'peekRatio', 75, 55, 90 ) . '%' ) );
$layout = bst_choice( $a['mobileLayout'] ?? 'swipe', array( 'swipe', 'accordion', 'horizontal' ), 'swipe' );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-expand bs-expand--' . $layout . ( ! empty( $a['verticalTitle'] ) ? ' bs-expand--vertical-title' : '' ), 'style' => $style, 'data-bs-expand' => wp_json_encode( array( 'open' => (int) ( $a['openPanel'] ?? 0 ), 'keep' => ! empty( $a['keepOpen'] ), 'dots' => ! empty( $a['dots'] ) ) ), 'role' => 'region', 'aria-label' => $a['label'] ?: __( 'Expanding gallery', 'bigspy-ai-theme-wp' ) ) ); ?>>
<div class="bs-expand__track"><?php echo $content; ?></div>
</div>
