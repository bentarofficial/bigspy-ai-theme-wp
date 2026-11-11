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
$shapes = array(
	''      => 'M12 2.5l2.9 6.1 6.6.8-4.9 4.6 1.3 6.6L12 17.3l-5.9 3.3 1.3-6.6L2.5 9.4l6.6-.8z',
	'heart' => 'M12 20.5s-7.4-4.5-9.3-9.1C1.3 8 3.4 4.3 7 4.3c2 0 3.6 1.1 5 3 1.4-1.9 3-3 5-3 3.6 0 5.7 3.7 4.3 7.1-1.9 4.6-9.3 9.1-9.3 9.1z',
	'thumb' => 'M2.5 10h3.5v10.5H2.5zM8 20.5h9.3a2 2 0 0 0 2-1.6l1.4-6.9A2 2 0 0 0 18.7 9.5h-5.2l.8-3.9a1.9 1.9 0 0 0-3.4-1.5L8 9.6z',
	'dot'   => 'M12 4.5a7.5 7.5 0 1 0 0 15 7.5 7.5 0 1 0 0-15z',
);
$shape  = bst_choice( (string) ( $a['icon'] ?? '' ), array_keys( $shapes ), '' );
$layout = bst_choice( (string) ( $a['layout'] ?? '' ), array( '', 'score', 'badge' ), '' );
$lpos   = bst_choice( (string) ( $a['labelPosition'] ?? '' ), array( '', 'left', 'below', 'above' ), '' );
$round  = bst_choice( (string) ( $a['round'] ?? '' ), array( '', 'half', 'whole' ), '' );
$count  = ! empty( $a['count'] ) ? (int) bst_block_num( $a, 'count', 0, 0, 100000000 ) : 0;
$shown  = 'half' === $round ? round( $rating * 2 ) / 2 : ( 'whole' === $round ? round( $rating ) : $rating );
$star   = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="' . $shapes[ $shape ] . '"/></svg>';
$stars  = str_repeat( $star, $max );
$class  = 'bs-stars bs-stars--align-' . bst_choice( $a['align'] ?? 'left', array( 'left', 'center', 'right' ), 'left' )
	. ( $shape ? ' bs-stars--icon-' . $shape : '' ) . ( $layout ? ' bs-stars--' . $layout : '' ) . ( $lpos ? ' bs-stars--label-' . $lpos : '' );
$score  = $layout ? '<span class="bs-stars__score"><b>' . esc_html( number_format_i18n( $rating, 1 ) ) . '</b><span>/' . esc_html( $max ) . '</span></span>' : '';
$cnt    = $count ? '<span class="bs-stars__count">' . esc_html( sprintf( /* translators: %s: number of reviews */ __( '(%s reviews)', 'bigspy-ai-theme-wp' ), number_format_i18n( $count ) ) ) . '</span>' : '';
$style  = bst_style_vars(
	array(
		'--bs-star-size'  => bst_block_num( $a, 'size', 22, 10, 80 ) . 'px',
		'--bs-star-color' => bst_block_color( $a, 'color' ),
		'--bs-star-empty' => bst_block_color( $a, 'emptyColor' ),
		'--bs-star-fill'  => round( $shown / $max * 100, 2 ) . '%',
		'--bs-star-gap'   => ! empty( $a['gap'] ) ? bst_block_num( $a, 'gap', 0, 0, 40 ) . 'px' : '',
	)
);
printf(
	'<div %1$s>%6$s<span class="bs-stars__icons" role="img" aria-label="%2$s"><span class="bs-stars__empty">%3$s</span><span class="bs-stars__full">%3$s</span></span>%4$s%5$s</div>',
	get_block_wrapper_attributes( array( 'class' => $class, 'style' => $style ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_attr( $text ),
	$stars, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	! empty( $a['showNumber'] ) ? '<span class="bs-stars__number">' . esc_html( $rating ) . '</span>' : '',
	'' !== $label || $cnt ? '<span class="bs-stars__label">' . esc_html( $label ) . ( '' !== $label && $cnt ? ' ' : '' ) . $cnt . '</span>' : '',
	$score // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
