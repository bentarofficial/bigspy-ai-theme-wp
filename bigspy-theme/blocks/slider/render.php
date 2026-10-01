<?php
/**
 * Slider block. Every inner block becomes a slide.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a     = (array) $attributes;
$style = bst_choice( $a['arrowStyle'] ?? 'circle', array( 'circle', 'square', 'minimal' ), 'circle' );

$slider = bst_slider_markup(
	$content,
	array(
		'd'        => bst_block_num( $a, 'slidesDesktop', 1, 1, 6 ),
		't'        => bst_block_num( $a, 'slidesTablet', 1, 1, 4 ),
		'm'        => bst_block_num( $a, 'slidesMobile', 1, 1, 3 ),
		'gap'      => bst_block_num( $a, 'gap', 0, 0, 80 ),
		'autoplay' => bst_block_num( $a, 'autoplay', 5, 0, 30 ) * 1000,
		'arrows'   => ! empty( $a['arrows'] ),
		'dots'     => ! empty( $a['dots'] ),
		'loop'     => ! empty( $a['loop'] ),
	),
	'bs-slider--arrows-' . $style
);
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-slider-block' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $slider; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
