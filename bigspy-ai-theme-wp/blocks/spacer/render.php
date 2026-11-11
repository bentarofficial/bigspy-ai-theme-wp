<?php
/**
 * Spacer block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a    = (array) $attributes;
$unit = bst_choice( $a['unit'] ?? '', array( '', 'vh', 'rem' ), '' );
$max  = array( '' => 600, 'vh' => 100, 'rem' => 40 );
$max  = $max[ $unit ];
// Negative values pull the next block up (overlap); old content is always >= 0.
$vars = bst_style_vars(
	array(
		'--bs-spacer-d' => bst_block_num( $a, 'heightDesktop', 60, -$max, $max ) . ( $unit ? $unit : 'px' ),
		'--bs-spacer-t' => bst_block_num( $a, 'heightTablet', 40, -$max, $max ) . ( $unit ? $unit : 'px' ),
		'--bs-spacer-m' => bst_block_num( $a, 'heightMobile', 30, -$max, $max ) . ( $unit ? $unit : 'px' ),
	)
);
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-spacer', 'style' => $vars ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-hidden="true"></div>
