<?php
/** Zoom Hero: framed image grows to full screen with scroll (motion-scroll.js sets --p); content fades in on top. */
defined( 'ABSPATH' ) || exit;
$a   = $attributes;
$url = ! empty( $a['imageId'] ) ? wp_get_attachment_image_url( (int) $a['imageId'], 'full' ) : '';
$url = $url ? $url : esc_url_raw( (string) ( $a['imageUrl'] ?? '' ) );
$alt = ! empty( $a['imageId'] ) ? (string) get_post_meta( (int) $a['imageId'], '_wp_attachment_image_alt', true ) : '';
$alt = '' !== $alt ? $alt : (string) ( $a['imageAlt'] ?? '' );
$style = '--bs-zh-inset:' . bst_block_num( $a, 'startInset', 18, 0, 40 ) . '%;--bs-zh-radius:' . bst_block_num( $a, 'radius', 28, 0, 80 ) . 'px;--bs-zh-overlay:' . ( bst_block_num( $a, 'overlay', 35, 0, 90 ) / 100 ) . ';--bs-zh-length:' . bst_block_num( $a, 'length', 160, 100, 400 ) . 'vh';
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-zhero alignfull', 'style' => $style, 'data-bs-zhero' => '1' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="bs-zhero__sticky">
		<div class="bs-zhero__media"><?php echo $url ? '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '" fetchpriority="high" decoding="async">' : ''; ?></div>
		<div class="bs-zhero__content"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
	</div>
</div>
