<?php
/** Scroll Story: steps scroll normally; a sticky media column follows the active step. */
defined( 'ABSPATH' ) || exit;
$a      = $attributes;
$side   = bst_choice( $a['mediaSide'] ?? 'right', array( 'left', 'right' ), 'right' );
$fx     = bst_choice( $a['transition'] ?? 'fade', array( 'fade', 'slide', 'zoom' ), 'fade' );
$ratio  = bst_choice( $a['ratio'] ?? '4-5', array( '1-1', '4-5', '3-4', '4-3', '16-9' ), '4-5' );
$media  = '';
$dots   = '';
foreach ( $block->inner_blocks as $i => $step ) {
	$s   = $step->attributes;
	$url = ! empty( $s['mediaId'] ) ? wp_get_attachment_image_url( (int) $s['mediaId'], 'large' ) : '';
	$url = $url ? $url : esc_url_raw( (string) ( $s['mediaUrl'] ?? '' ) );
	$alt = ! empty( $s['mediaId'] ) ? (string) get_post_meta( (int) $s['mediaId'], '_wp_attachment_image_alt', true ) : '';
	$alt = '' !== $alt ? $alt : (string) ( $s['mediaAlt'] ?? '' );
	$media .= '<figure class="bs-story__media-item' . ( 0 === $i ? ' is-active' : '' ) . '">' . ( $url ? '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '" loading="' . ( 0 === $i ? 'eager' : 'lazy' ) . '" decoding="async">' : '<span class="bs-story__placeholder"></span>' ) . '</figure>';
	$dots  .= '<span class="bs-story__dot' . ( 0 === $i ? ' is-active' : '' ) . '"></span>';
}
$style = '--bs-story-top:' . bst_block_num( $a, 'stickyTop', 100, 0, 400 ) . 'px;--bs-story-radius:' . bst_block_num( $a, 'radius', 16, 0, 80 ) . 'px;--bs-story-gap:' . bst_block_num( $a, 'gap', 48, 0, 160 ) . 'px';
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-story bs-story--media-' . $side . ' bs-story--' . $fx . ' bs-story--ratio-' . $ratio, 'style' => $style, 'data-bs-story' => '1', 'role' => 'region', 'aria-label' => '' !== trim( (string) ( $a['label'] ?? '' ) ) ? trim( (string) $a['label'] ) : __( 'Scroll Story', 'bigspy-ai-theme-wp' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="bs-story__steps"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered child blocks. ?></div>
	<div class="bs-story__media" aria-hidden="true"><div class="bs-story__frame"><?php echo $media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div><?php echo ! empty( $a['progress'] ) ? '<div class="bs-story__dots">' . $dots . '</div>' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
</div>
