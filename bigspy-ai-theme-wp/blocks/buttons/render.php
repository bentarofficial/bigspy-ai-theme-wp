<?php
/**
 * Buttons block (a row of Button blocks).
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a     = (array) $attributes;
$align = bst_choice( $a['align'] ?? 'left', array( 'left', 'center', 'right', 'stretch' ), 'left' );
$class = 'bs-buttons bs-buttons--' . $align . ( ! empty( $a['stackMobile'] ) ? ' bs-buttons--stack-mobile' : '' );
$style = '--bs-buttons-gap:' . bst_block_num( $a, 'gap', 12, 0, 80 ) . 'px';
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => $class, 'style' => $style ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
