<?php
/**
 * Accordion block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a     = (array) $attributes;
$style = bst_choice( $a['style'] ?? 'bordered', array( 'bordered', 'separated', 'minimal' ), 'bordered' );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-accordion bs-accordion--' . $style ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-single="<?php echo ! empty( $a['single'] ) ? '1' : '0'; ?>"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
