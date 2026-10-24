<?php
/**
 * Gallery block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a      = (array) $attributes;
$images = array_filter( (array) ( $a['images'] ?? array() ), 'is_array' );
if ( ! $images ) {
	return;
}
$layout   = bst_choice( $a['layout'] ?? 'grid', array( 'grid', 'masonry', 'slider' ), 'grid' );
$ratio    = 'masonry' === $layout ? 'auto' : bst_choice( $a['ratio'] ?? '1-1', array( 'auto', '1-1', '4-3', '3-2', '16-9', '3-4' ), '1-1' );
$size     = bst_choice( $a['size'] ?? 'large', array( 'thumbnail', 'medium', 'medium_large', 'large', 'full' ), 'large' );
$hover    = bst_choice( $a['hover'] ?? 'zoom', array( 'none', 'zoom', 'lift', 'grayscale', 'darken' ), 'zoom' );
$lightbox = ! empty( $a['lightbox'] );
$group    = 'g' . substr( md5( wp_json_encode( $images ) ), 0, 8 );
$items    = '';

foreach ( $images as $image ) {
	$id  = absint( $image['id'] ?? 0 );
	$alt = (string) ( $image['alt'] ?? '' );
	// No alt set in the block: use the alt text from the Media Library instead of an empty one.
	if ( '' === trim( $alt ) && $id ) {
		$alt = (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
	}
	$cap = (string) ( $image['caption'] ?? '' );
	$img = $id ? wp_get_attachment_image( $id, $size, false, array( 'class' => 'bs-gallery__img', 'alt' => $alt ) ) : '';
	if ( ! $img && ! empty( $image['url'] ) ) {
		$img = '<img class="bs-gallery__img" src="' . esc_url( $image['url'] ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy" decoding="async">';
	}
	if ( ! $img ) {
		continue;
	}
	$frame = '<span class="bs-gallery__frame' . ( 'auto' !== $ratio ? ' bs-ratio bs-ratio--' . $ratio : '' ) . '">' . $img . '</span>';
	if ( ! empty( $image['link'] ) ) {
		$frame = '<a href="' . esc_url( $image['link'] ) . '">' . $frame . '</a>';
	} elseif ( $lightbox ) {
		$full  = $id ? wp_get_attachment_image_url( $id, 'full' ) : '';
		$full  = $full ? $full : ( $image['url'] ?? '' );
		$frame = '<a href="' . esc_url( $full ) . '" class="bs-lightbox-link" data-bs-lightbox="' . esc_attr( $group ) . '" data-caption="' . esc_attr( wp_strip_all_tags( $cap ? $cap : $alt ) ) . '">' . $frame . '</a>';
	}
	$items .= '<figure class="bs-gallery__item">' . $frame . ( ! empty( $a['captions'] ) && '' !== $cap ? '<figcaption class="bs-gallery__caption">' . wp_kses_post( $cap ) . '</figcaption>' : '' ) . '</figure>';
}

$vars = bst_style_vars(
	array(
		'--bs-gal-cols'   => bst_block_num( $a, 'columns', 4, 1, 8 ),
		'--bs-gal-cols-t' => bst_block_num( $a, 'columnsTablet', 3, 1, 6 ),
		'--bs-gal-cols-m' => bst_block_num( $a, 'columnsMobile', 2, 1, 4 ),
		'--bs-gal-gap'    => bst_block_num( $a, 'gap', 12, 0, 80 ) . 'px',
		'--bs-gal-radius' => bst_block_num( $a, 'radius', 8, 0, 100 ) . 'px',
	)
);
$class = 'bs-gallery bs-gallery--' . $layout . ' bs-gallery--hover-' . $hover;

if ( 'slider' === $layout ) {
	$slider = bst_slider_markup(
		$items,
		array(
			'd'        => bst_block_num( $a, 'columns', 4, 1, 8 ),
			't'        => bst_block_num( $a, 'columnsTablet', 3, 1, 6 ),
			'm'        => bst_block_num( $a, 'columnsMobile', 2, 1, 4 ),
			'gap'      => bst_block_num( $a, 'gap', 12, 0, 80 ),
			'autoplay' => bst_block_num( $a, 'autoplay', 0, 0, 30 ) * 1000,
			'arrows'   => ! empty( $a['arrows'] ),
			'dots'     => ! empty( $a['dots'] ),
		)
	);
	printf( '<div %1$s>%2$s</div>', get_block_wrapper_attributes( array( 'class' => $class, 'style' => $vars ) ), $slider ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}
printf( '<div %1$s><div class="bs-gallery__grid">%2$s</div></div>', get_block_wrapper_attributes( array( 'class' => $class, 'style' => $vars ) ), $items ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
