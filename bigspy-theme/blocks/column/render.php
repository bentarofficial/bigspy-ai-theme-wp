<?php
/**
 * Column block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a       = (array) $attributes;
$desktop = bst_block_num( $a, 'spanDesktop', 6, 1, 12 );
$tablet  = bst_block_num( $a, 'spanTablet', 0, 0, 12 );
$mobile  = bst_block_num( $a, 'spanMobile', 12, 1, 12 );
$valign  = bst_choice( $a['verticalAlign'] ?? 'top', array( 'top', 'center', 'bottom' ), 'top' );
$align   = bst_choice( $a['textAlign'] ?? '', array( '', 'left', 'center', 'right' ), '' );

$classes = 'bs-column bs-column--valign-' . $valign . ( $align ? ' has-text-align-' . $align : '' );
$style   = bst_style_vars(
	array(
		'--bs-span-d' => $desktop,
		'--bs-span-t' => $tablet ? $tablet : $desktop,
		'--bs-span-m' => $mobile,
	)
);
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => $classes, 'style' => $style ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
