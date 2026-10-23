<?php
/**
 * Image box block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a = (array) $attributes;
$layout = bst_choice( $a['layout'] ?? 'top', array( 'top', 'left', 'right', 'overlay' ), 'top' );
$ratio  = bst_choice( $a['ratio'] ?? '4-3', array( 'auto', '1-1', '4-3', '3-2', '16-9', '3-4' ), '4-3' );
$tag    = bst_choice( $a['titleTag'] ?? 'h3', array( 'h2', 'h3', 'h4', 'p' ), 'h3' );
$alt    = (string) ( $a['alt'] ?? '' );
$img    = ! empty( $a['imageId'] ) ? wp_get_attachment_image( (int) $a['imageId'], 'large', false, array( 'class' => 'bs-image-box__img', 'alt' => $alt ) ) : '';
if ( ! $img && ! empty( $a['imageUrl'] ) ) {
	$img = '<img class="bs-image-box__img" src="' . esc_url( $a['imageUrl'] ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy" decoding="async">';
}
$link   = esc_url( $a['link'] ?? '' );
$target = ! empty( $a['newTab'] ) ? ' target="_blank" rel="noopener"' : '';
$title  = wp_kses_post( (string) ( $a['title'] ?? '' ) );
if ( $link && $title ) {
	$title = '<a href="' . $link . '"' . $target . '>' . $title . '</a>';
}
$media = $img ? '<div class="bs-image-box__media' . ( 'auto' !== $ratio ? ' bs-ratio bs-ratio--' . $ratio : '' ) . '">' . ( $link ? '<a href="' . $link . '"' . $target . ' tabindex="-1" aria-hidden="true">' . $img . '</a>' : $img ) . '</div>' : '';
$body  = '<div class="bs-image-box__body">' . ( $title ? '<' . $tag . ' class="bs-image-box__title">' . $title . '</' . $tag . '>' : '' ) . ( ! empty( $a['text'] ) ? '<div class="bs-image-box__text">' . wp_kses_post( wpautop( $a['text'] ) ) . '</div>' : '' ) . ( $link && ! empty( $a['linkText'] ) ? '<a class="bs-image-box__more" href="' . $link . '"' . $target . '>' . esc_html( $a['linkText'] ) . bst_icon( 'arrow-right', 16 ) . '</a>' : '' ) . '</div>';
$class = 'bs-image-box bs-image-box--' . $layout . ' bs-image-box--align-' . bst_choice( $a['align'] ?? 'left', array( 'left', 'center' ), 'left' ) . ' bs-image-box--hover-' . bst_choice( $a['hover'] ?? 'zoom', array( 'none', 'zoom', 'lift' ), 'zoom' ) . ( ! empty( $a['card'] ) ? ' is-card' : '' );
$style = bst_style_vars(
	array(
		'--bs-ib-radius' => bst_block_num( $a, 'radius', 12, 0, 60 ) . 'px',
		'--bs-ib-img-w'  => bst_block_num( $a, 'imageWidth', 40, 15, 70 ) . '%',
	)
);
printf( '<div %1$s>%2$s%3$s</div>', get_block_wrapper_attributes( array( 'class' => $class, 'style' => $style ) ), $media, $body ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
