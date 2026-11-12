<?php
/**
 * Buttons block (a row of Button blocks).
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a     = (array) $attributes;
$aligns = array( 'left', 'center', 'right', 'stretch' );
$align = bst_choice( $a['align'] ?? 'left', $aligns, 'left' );
$class = 'bs-buttons bs-buttons--' . $align . ( ! empty( $a['stackMobile'] ) ? ' bs-buttons--stack-mobile' : '' );
$style = '--bs-buttons-gap:' . bst_block_num( $a, 'gap', 12, 0, 80 ) . 'px';
// G-132: per-device align and direction, wrapping, equal widths, separator text.
foreach ( array( 'Tablet' => 't', 'Mobile' => 'm' ) as $dev => $p ) {
	$v = bst_choice( $a[ 'align' . $dev ] ?? '', array_merge( array( '' ), $aligns ), '' );
	$class .= $v ? " bs-buttons--$p-$v" : '';
}
foreach ( array( '' => 'd', 'Tablet' => 't', 'Mobile' => 'm' ) as $dev => $p ) {
	$v = bst_choice( $a[ 'direction' . $dev ] ?? '', array( '', 'row', 'column' ), '' );
	$class .= $v ? " bs-buttons--$p-$v" : '';
}
$class .= ! empty( $a['noWrap'] ) ? ' bs-buttons--nowrap' : '';
$class .= ! empty( $a['equalWidth'] ) ? ' bs-buttons--equal' : '';
if ( ! empty( $a['gapMobile'] ) ) {
	$style .= ';--bs-buttons-gap-m:' . bst_block_num( $a, 'gapMobile', 0, 0, 80 ) . 'px';
}
$sep = trim( sanitize_text_field( (string) ( $a['separator'] ?? '' ) ) );
if ( '' !== $sep ) {
	$class .= ' has-separator';
	$style .= ';--bs-buttons-sep:"' . str_replace( array( '"', '\\', ';' ), '', mb_substr( $sep, 0, 20 ) ) . '"';
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => $class, 'style' => $style ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
