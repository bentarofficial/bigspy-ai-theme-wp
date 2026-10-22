<?php
/**
 * Star rating block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a = (array) $attributes;
$max    = bst_block_num( $a, 'max', 5, 1, 10 );
$rating = max( 0, min( $max, (float) ( $a['rating'] ?? 5 ) ) );
$label  = trim( (string) ( $a['label'] ?? '' ) );
$text   = sprintf( /* translators: 1: rating, 2: max */ __( 'Rated %1$s out of %2$s', 'bigspy-ai-theme-wp' ), $rating, $max );
$star   = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.5l2.9 6.1 6.6.8-4.9 4.6 1.3 6.6L12 17.3l-5.9 3.3 1.3-6.6L2.5 9.4l6.6-.8z"/></svg>';
$stars  = str_repeat( $star, $max );
$class  = 'bs-stars bs-stars--align-' . bst_choice( $a['align'] ?? 'left', array( 'left', 'center', 'right' ), 'left' );
$style  = bst_style_vars(
	array(
		'--bs-star-size'  => bst_block_num( $a, 'size', 22, 10, 80 ) . 'px',
		'--bs-star-color' => bst_block_color( $a, 'color' ),
		'--bs-star-empty' => bst_block_color( $a, 'emptyColor' ),
		'--bs-star-fill'  => round( $rating / $max * 100, 2 ) . '%',
	)
);
printf(
	'<div %1$s><span class="bs-stars__icons" role="img" aria-label="%2$s"><span class="bs-stars__empty">%3$s</span><span class="bs-stars__full">%3$s</span></span>%4$s%5$s</div>',
	get_block_wrapper_attributes( array( 'class' => $class, 'style' => $style ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_attr( $text ),
	$stars, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	! empty( $a['showNumber'] ) ? '<span class="bs-stars__number">' . esc_html( $rating ) . '</span>' : '',
	'' !== $label ? '<span class="bs-stars__label">' . esc_html( $label ) . '</span>' : ''
);
