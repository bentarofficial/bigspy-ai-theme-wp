<?php
/**
 * Image block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a     = (array) $attributes;
$alt   = (string) ( $a['alt'] ?? '' );
$size  = bst_choice( $a['size'] ?? 'large', array( 'thumbnail', 'medium', 'medium_large', 'large', 'full' ), 'large' );
$ratio = bst_choice( $a['ratio'] ?? 'auto', array( 'auto', '1-1', '4-3', '3-2', '16-9', '3-4' ), 'auto' );
$align = bst_choice( $a['align'] ?? 'center', array( 'left', 'center', 'right' ), 'center' );
$hover = bst_choice( $a['hover'] ?? 'none', array( 'none', 'zoom', 'zoom-fade', 'lift', 'grayscale', 'blur', 'brighten', 'darken' ), 'none' );
$height = trim( (string) ( $a['height'] ?? '' ) );
$height = preg_match( '/^[0-9.]+(px|%|vh)$/', $height ) ? $height : ( is_numeric( $height ) && $height > 0 ? $height . 'px' : '' );

$img = '';
if ( ! empty( $a['imageId'] ) ) {
	$attr = array( 'class' => 'bs-image__img' );
	if ( '' !== $alt ) {
		$attr['alt'] = $alt;
	}
	$img = wp_get_attachment_image( (int) $a['imageId'], $size, false, $attr );
}
if ( ! $img && ! empty( $a['imageUrl'] ) ) {
	$img = '<img class="bs-image__img" src="' . esc_url( $a['imageUrl'] ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy" decoding="async">';
}
if ( ! $img ) {
	return;
}

$overlay = bst_block_color( $a, 'overlay' );
$over    = $overlay ? '<span class="bs-image__overlay' . ( ! empty( $a['overlayHover'] ) ? ' is-hover' : '' ) . '" style="' . esc_attr( 'background:' . $overlay . ';--bs-ov-o:' . ( bst_block_num( $a, 'overlayOpacity', 30, 0, 100 ) / 100 ) ) . '" aria-hidden="true"></span>' : '';
$fixed   = 'auto' === $ratio && $height;
$frame   = '<span class="bs-image__frame' . ( 'auto' !== $ratio ? ' bs-ratio bs-ratio--' . $ratio : '' ) . ( $fixed ? ' has-height' : '' ) . '"' . ( $fixed ? ' style="' . esc_attr( '--bs-image-h:' . $height ) . '"' : '' ) . '>' . $img . $over . '</span>';
$link    = esc_url( $a['link'] ?? '' );
if ( $link ) {
	$rel   = trim( ( ! empty( $a['newTab'] ) ? 'noopener ' : '' ) . preg_replace( '/[^a-z ]/', '', strtolower( (string) ( $a['linkRel'] ?? '' ) ) ) );
	$frame = '<a href="' . $link . '"' . ( ! empty( $a['newTab'] ) ? ' target="_blank"' : '' ) . ( $rel ? ' rel="' . esc_attr( $rel ) . '"' : '' ) . '>' . $frame . '</a>';
} elseif ( ! empty( $a['lightbox'] ) ) {
	$full  = ! empty( $a['imageId'] ) ? wp_get_attachment_image_url( (int) $a['imageId'], 'full' ) : '';
	$full  = $full ? $full : ( $a['imageUrl'] ?? '' );
	$frame = '<a href="' . esc_url( $full ) . '" class="bs-lightbox-link" data-bs-lightbox data-caption="' . esc_attr( wp_strip_all_tags( (string) ( $a['caption'] ? $a['caption'] : $alt ) ) ) . '">' . $frame . '</a>';
}

$classes = 'bs-image bs-image--' . $align . ' bs-image--hover-' . $hover . ( ! empty( $a['shadow'] ) ? ' has-shadow' : '' );
$style   = bst_style_vars(
	array(
		'--bs-image-width'  => bst_block_num( $a, 'width', 100, 5, 100 ) . '%',
		'--bs-image-radius' => bst_block_num( $a, 'radius', 0, 0, 200 ) . 'px',
		'--bs-image-position' => bst_block_num( $a, 'focalX', 50, 0, 100 ) . '% ' . bst_block_num( $a, 'focalY', 50, 0, 100 ) . '%',
	)
);
?>
<figure <?php echo get_block_wrapper_attributes( array( 'class' => $classes, 'style' => $style ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php echo $frame; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<?php if ( ! empty( $a['caption'] ) ) : ?>
		<figcaption class="bs-image__caption"><?php echo wp_kses_post( $a['caption'] ); ?></figcaption>
	<?php endif; ?>
</figure>
