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
$hover = bst_choice( $a['hover'] ?? 'none', array( 'none', 'zoom', 'lift', 'grayscale' ), 'none' );

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

$frame = '<span class="bs-image__frame' . ( 'auto' !== $ratio ? ' bs-ratio bs-ratio--' . $ratio : '' ) . '">' . $img . '</span>';
$link  = esc_url( $a['link'] ?? '' );
if ( $link ) {
	$frame = '<a href="' . $link . '"' . ( ! empty( $a['newTab'] ) ? ' target="_blank" rel="noopener"' : '' ) . '>' . $frame . '</a>';
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
