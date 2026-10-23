<?php
/**
 * Slider block. Every inner block becomes a slide.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a     = (array) $attributes;
$style = bst_choice( $a['arrowStyle'] ?? 'circle', array( 'circle', 'square', 'minimal', 'line' ), 'circle' );

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
		'fade'     => 'fade' === ( $a['effect'] ?? 'slide' ),
		'peek'     => 'fade' === ( $a['effect'] ?? 'slide' ) ? 0 : bst_block_num( $a, 'peek', 0, 0, 40 ),
		'progress' => ! empty( $a['progress'] ),
		'pause'    => ! isset( $a['pauseHover'] ) || ! empty( $a['pauseHover'] ),
	),
	implode(
		' ',
		array_filter(
			array(
				'bs-slider--arrows-' . $style,
				( $a['arrowsMobile'] ?? true ) ? '' : 'bs-slider--no-arrows-m',
				'bs-slider--arrows-pos-' . bst_choice( $a['arrowPosition'] ?? 'inside', array( 'inside', 'outside', 'bottom' ), 'inside' ),
				'bs-slider--arrows-' . bst_choice( $a['arrowColor'] ?? 'light', array( 'light', 'dark', 'brand' ), 'light' ),
				'bs-slider--arrows-' . bst_choice( $a['arrowSize'] ?? 'md', array( 'sm', 'md', 'lg' ), 'md' ),
				! empty( $a['arrowsAlways'] ) ? 'bs-slider--arrows-always' : '',
				'bs-slider--dots-' . bst_choice( $a['dotStyle'] ?? 'pill', array( 'pill', 'dot', 'dash', 'square' ), 'pill' ),
				! empty( $a['dotsInside'] ) ? 'bs-slider--dots-inside' : '',
			)
		)
	)
);
$sh_d = bst_block_num( $a, 'slideHeightD', 0, 0, 1400 );
$sh_t = bst_block_num( $a, 'slideHeightT', 0, 0, 1400 );
$sh_m = bst_block_num( $a, 'slideHeightM', 0, 0, 1400 );
if ( $sh_d ) {
	$slider = str_replace( 'class="bs-slider ', 'class="bs-slider bs-slider--same-h ', $slider );
}
$slider = str_replace( 'class="bs-slider ', 'style="' . esc_attr( bst_style_vars( array( '--bs-sh-d' => $sh_d ? $sh_d . 'px' : '', '--bs-sh-t' => $sh_d ? ( $sh_t ? $sh_t : $sh_d ) . 'px' : '', '--bs-sh-m' => $sh_d ? ( $sh_m ? $sh_m : ( $sh_t ? $sh_t : $sh_d ) ) . 'px' : '', '--bs-slider-min' => ! empty( $a['minHeight'] ) ? bst_block_num( $a, 'minHeight', 0, 0, 1200 ) . 'px' : '', '--bs-slide-radius' => ! empty( $a['slideRadius'] ) ? bst_block_num( $a, 'slideRadius', 0, 0, 60 ) . 'px' : '' ) ) ) . '" class="bs-slider ', $slider );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-slider-block' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $slider; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
