<?php
/** Video Hover Cards grid. */
defined( 'ABSPATH' ) || exit;
$a     = $attributes;
$ratio = bst_choice( $a['ratio'] ?? '4-5', array( '1-1', '4-5', '3-4', '16-9', '9-16' ), '4-5' );
$style = '--bs-vc-cols:' . bst_block_num( $a, 'columns', 3, 1, 6 ) . ';--bs-vc-cols-t:' . bst_block_num( $a, 'columnsTablet', 2, 1, 4 ) . ';--bs-vc-cols-m:' . bst_block_num( $a, 'columnsMobile', 1, 1, 2 ) . ';--bs-vc-gap:' . bst_block_num( $a, 'gap', 24, 0, 80 ) . 'px;--bs-vc-radius:' . bst_block_num( $a, 'radius', 18, 0, 60 ) . 'px';
$attrs = array( 'class' => 'bs-vcards bs-vcards--ratio-' . $ratio, 'style' => $style, 'data-bs-vcards' => '1' );
if ( '' !== trim( (string) ( $a['label'] ?? '' ) ) ) {
	$attrs['aria-label'] = trim( (string) $a['label'] );
}
?>
<div <?php echo get_block_wrapper_attributes( $attrs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
