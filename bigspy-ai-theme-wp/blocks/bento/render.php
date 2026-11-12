<?php
/** Bento Grid. */
defined( 'ABSPATH' ) || exit;
$a     = $attributes;
$style = '--bs-bento-gap:' . bst_block_num( $a, 'gap', 16, 0, 60 ) . 'px;--bs-bento-row:' . bst_block_num( $a, 'rowHeight', 200, 80, 600 ) . 'px;--bs-bento-radius:' . bst_block_num( $a, 'radius', 22, 0, 60 ) . 'px';
$class = 'bs-bento' . ( ! empty( $a['glow'] ) ? ' bs-bento--glow' : '' );
$class .= 'content' === ( $a['fit'] ?? 'fixed' ) ? ' bs-bento--fit' : '';
$align  = bst_choice( (string) ( $a['contentAlign'] ?? 'end' ), array( 'start', 'center', 'end' ), 'end' );
$class .= 'end' !== $align ? ' bs-bento--align-' . $align : '';
$class .= ( isset( $a['hoverLift'] ) && ! $a['hoverLift'] ) ? ' bs-bento--no-lift' : '';
$class .= ! empty( $a['dense'] ) ? ' bs-bento--dense' : '';
$attrs = array( 'class' => $class, 'style' => $style, 'data-bs-bento' => '1' );
if ( '' !== trim( (string) ( $a['label'] ?? '' ) ) ) {
	$attrs['aria-label'] = trim( (string) $a['label'] );
}
?>
<div <?php echo get_block_wrapper_attributes( $attrs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
