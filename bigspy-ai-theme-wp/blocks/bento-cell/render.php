<?php
/** One Bento tile. */
defined( 'ABSPATH' ) || exit;
$a     = $attributes;
$size  = bst_choice( $a['size'] ?? 'small', array( 'small', 'wide', 'tall', 'large' ), 'small' );
$img   = ! empty( $a['imageId'] ) ? wp_get_attachment_image_url( (int) $a['imageId'], 'large' ) : '';
$img   = $img ? $img : esc_url_raw( (string) ( $a['imageUrl'] ?? '' ) );
$style = bst_style_vars( array( '--bs-bento-bg' => bst_block_color( $a, 'background' ), '--bs-bento-fg' => bst_block_color( $a, 'textColor' ) ) );
if ( $img ) {
	$style .= ( $style ? ';' : '' ) . 'background-image:url(' . esc_url( $img ) . ')';
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-bento__cell bs-bento__cell--' . $size . ( $img ? ' has-image' : '' ), 'style' => $style ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><div class="bs-bento__inner"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div></div>
