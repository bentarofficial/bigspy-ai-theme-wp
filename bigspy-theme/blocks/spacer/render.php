<?php
/**
 * Spacer block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a    = (array) $attributes;
$vars = bst_style_vars(
	array(
		'--bs-spacer-d' => bst_block_num( $a, 'heightDesktop', 60, 0, 600 ) . 'px',
		'--bs-spacer-t' => bst_block_num( $a, 'heightTablet', 40, 0, 600 ) . 'px',
		'--bs-spacer-m' => bst_block_num( $a, 'heightMobile', 30, 0, 600 ) . 'px',
	)
);
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-spacer', 'style' => $vars ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-hidden="true"></div>
