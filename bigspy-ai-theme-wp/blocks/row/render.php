<?php
/**
 * Row block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a       = (array) $attributes;
$gap     = bst_choice( $a['gap'] ?? 'medium', array( 'none', 'small', 'medium', 'large' ), 'medium' );
$valign  = bst_choice( $a['verticalAlign'] ?? 'stretch', array( 'stretch', 'top', 'center', 'bottom' ), 'stretch' );
$justify = bst_choice( $a['justify'] ?? 'start', array( 'start', 'center', 'end' ), 'start' );

$classes = 'bs-row bs-row--gap-' . $gap . ' bs-row--valign-' . $valign . ' bs-row--justify-' . $justify;
if ( ! empty( $a['reverseMobile'] ) ) {
	$classes .= ' bs-row--reverse-mobile';
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => $classes ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
