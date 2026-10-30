<?php
/** One Scroll Story step; its image is repeated inline for phones, where there is no sticky column. */
defined( 'ABSPATH' ) || exit;
$a   = $attributes;
$url = ! empty( $a['mediaId'] ) ? wp_get_attachment_image_url( (int) $a['mediaId'], 'large' ) : '';
$url = $url ? $url : esc_url_raw( (string) ( $a['mediaUrl'] ?? '' ) );
$alt = ! empty( $a['mediaId'] ) ? (string) get_post_meta( (int) $a['mediaId'], '_wp_attachment_image_alt', true ) : '';
$alt = '' !== $alt ? $alt : (string) ( $a['mediaAlt'] ?? '' );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-story__step' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php echo $url ? '<img class="bs-story__inline" src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy" decoding="async">' : ''; ?>
	<div class="bs-story__body"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
</div>
