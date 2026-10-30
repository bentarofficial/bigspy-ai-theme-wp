<?php
/** Bento Grid. */
defined( 'ABSPATH' ) || exit;
$a     = $attributes;
$style = '--bs-bento-gap:' . bst_block_num( $a, 'gap', 16, 0, 60 ) . 'px;--bs-bento-row:' . bst_block_num( $a, 'rowHeight', 200, 80, 600 ) . 'px;--bs-bento-radius:' . bst_block_num( $a, 'radius', 22, 0, 60 ) . 'px';
$attrs = array( 'class' => 'bs-bento' . ( ! empty( $a['glow'] ) ? ' bs-bento--glow' : '' ), 'style' => $style, 'data-bs-bento' => '1' );
if ( '' !== trim( (string) ( $a['label'] ?? '' ) ) ) {
	$attrs['aria-label'] = trim( (string) $a['label'] );
}
?>
<div <?php echo get_block_wrapper_attributes( $attrs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
