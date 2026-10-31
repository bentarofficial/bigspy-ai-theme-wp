<?php
/**
 * Section block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a       = (array) $attributes;
$width   = bst_choice( $a['contentWidth'] ?? 'boxed', array( 'boxed', 'narrow', 'full' ), 'boxed' );
$valign  = bst_choice( $a['verticalAlign'] ?? 'center', array( 'top', 'center', 'bottom' ), 'center' );
$scheme  = bst_choice( $a['textScheme'] ?? '', array( '', 'light', 'dark' ), '' );
$unit    = 'vh' === ( $a['minHeightUnit'] ?? 'px' ) ? 'vh' : 'px';
$min     = bst_block_num( $a, 'minHeight', 0, 0, 'vh' === $unit ? 100 : 2000 );
$image   = esc_url( $a['bgImage'] ?? '' );
$overlay = bst_block_color( $a, 'overlayColor' );

$classes = array( 'bs-section', 'bs-section--' . $width, 'bs-section--valign-' . $valign );
$preset = sanitize_key( $a['bgPreset'] ?? '' );
if ( $preset && array_key_exists( $preset, bst_section_backgrounds() ) ) {
	$classes[] = 'bs-bg--' . $preset;
	if ( ! $scheme && in_array( $preset, bst_section_dark_backgrounds(), true ) ) {
		$scheme = 'light';
	}
}
if ( $scheme ) {
	$classes[] = 'is-scheme-' . $scheme;
}
if ( ! empty( $a['dividerTop'] ) || ! empty( $a['dividerBottom'] ) ) {
	$classes[] = 'has-shape-divider';
}
if ( $image && ! empty( $a['bgFixed'] ) ) {
	$classes[] = 'has-fixed-bg';
}

$style = bst_style_vars(
	array(
		'--bs-sec-pd' => bst_block_num( $a, 'paddingDesktop', 80, 0, 400 ) . 'px',
		'--bs-sec-pt' => bst_block_num( $a, 'paddingTablet', 60, 0, 400 ) . 'px',
		'--bs-sec-pm' => bst_block_num( $a, 'paddingMobile', 40, 0, 400 ) . 'px',
		'--bs-sec-min' => $min ? $min . $unit : '',
		'--bs-shape-top' => ! empty( $a['dividerTop'] ) ? bst_block_num( $a, 'dividerTopHeight', 80, 10, 400 ) . 'px' : '',
		'--bs-shape-bottom' => ! empty( $a['dividerBottom'] ) ? bst_block_num( $a, 'dividerBottomHeight', 80, 10, 400 ) . 'px' : '',
	)
);

$bg_style = '';
if ( $image ) {
	$size     = bst_choice( $a['bgSize'] ?? 'cover', array( 'cover', 'contain', 'auto' ), 'cover' );
	$position = preg_match( '/^[a-z ]+$|^[0-9.]+% [0-9.]+%$/', (string) ( $a['bgPosition'] ?? '' ) ) ? $a['bgPosition'] : 'center center';
	$bg_style = 'background-image:url(' . $image . ');background-size:' . $size . ';background-position:' . $position;
	$blur     = bst_block_num( $a, 'bgBlur', 0, 0, 30 );
	$bright   = bst_block_num( $a, 'bgBrightness', 100, 20, 150 );
	if ( 100 !== (int) $bright ) {
		$bg_style .= ';filter:brightness(' . ( $bright / 100 ) . ')';
	}
}
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ), 'style' => $style ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $image ) : ?>
		<div class="bs-section__bg<?php echo ! empty( $a['parallax'] ) ? ' is-parallax' : ''; ?>" style="<?php echo esc_attr( $bg_style ); ?>"<?php echo ! empty( $a['parallax'] ) ? ' data-bs-parallax="' . esc_attr( bst_block_num( $a, 'parallax', 0, 0, 60 ) / 100 ) . '"' : ''; ?> aria-hidden="true"></div>
	<?php endif; ?>
	<?php if ( $image && $blur ) : ?>
		<div class="bs-section__blur" style="<?php echo esc_attr( '--bs-bg-blur:' . $blur . 'px' ); ?>" aria-hidden="true"></div>
	<?php endif; ?>
	<?php if ( $overlay ) : ?>
		<div class="bs-section__overlay" style="<?php echo esc_attr( 'background:' . $overlay . ';opacity:' . ( bst_block_num( $a, 'overlayOpacity', 50, 0, 100 ) / 100 ) ); ?>" aria-hidden="true"></div>
	<?php endif; ?>
	<?php
	if ( ! empty( $a['videoUrl'] ) ) {
		$video = bst_section_video( $a['videoUrl'] );
		echo empty( $a['videoMobile'] ) ? str_replace( 'class="bs-section__video', 'class="bs-section__video bs-hide-mobile', $video ) : $video; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	foreach ( array( 'Top' => 'top', 'Bottom' => 'bottom' ) as $key => $pos ) {
		if ( ! empty( $a[ 'divider' . $key ] ) ) {
			echo bst_shape_divider( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				sanitize_key( $a[ 'divider' . $key ] ),
				$pos,
				array(
					'color'  => bst_block_color( $a, 'divider' . $key . 'Color' ),
					'height' => bst_block_num( $a, 'divider' . $key . 'Height', 80, 10, 400 ),
					'flip'   => ! empty( $a[ 'divider' . $key . 'Flip' ] ),
					'front'  => ! empty( $a[ 'divider' . $key . 'Front' ] ),
				)
			);
		}
	}
	?>
	<div class="bs-section__inner"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
</section>
