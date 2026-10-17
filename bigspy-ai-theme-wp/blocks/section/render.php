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
if ( $scheme ) {
	$classes[] = 'is-scheme-' . $scheme;
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
	)
);

$bg_style = '';
if ( $image ) {
	$size     = bst_choice( $a['bgSize'] ?? 'cover', array( 'cover', 'contain', 'auto' ), 'cover' );
	$position = preg_match( '/^[a-z ]+$|^[0-9.]+% [0-9.]+%$/', (string) ( $a['bgPosition'] ?? '' ) ) ? $a['bgPosition'] : 'center center';
	$bg_style = 'background-image:url(' . $image . ');background-size:' . $size . ';background-position:' . $position;
}
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ), 'style' => $style ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $image ) : ?>
		<div class="bs-section__bg" style="<?php echo esc_attr( $bg_style ); ?>" aria-hidden="true"></div>
	<?php endif; ?>
	<?php if ( $overlay ) : ?>
		<div class="bs-section__overlay" style="<?php echo esc_attr( 'background:' . $overlay . ';opacity:' . ( bst_block_num( $a, 'overlayOpacity', 50, 0, 100 ) / 100 ) ); ?>" aria-hidden="true"></div>
	<?php endif; ?>
	<div class="bs-section__inner"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
</section>
