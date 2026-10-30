<?php
/** Hover Reveal List: rows are links; motion-fx.js floats the row image beside the cursor. */
defined( 'ABSPATH' ) || exit;
$a     = $attributes;
$style = bst_style_vars( array( '--bs-hr-size' => ! empty( $a['size'] ) ? bst_block_num( $a, 'size', 0, 14, 160 ) . 'px' : '', '--bs-hr-img' => bst_block_num( $a, 'imageWidth', 280, 120, 600 ) . 'px' ) );
?>
<?php
$attrs = array( 'class' => 'bs-hreveal' . ( ! empty( $a['divider'] ) ? ' bs-hreveal--lines' : '' ), 'style' => $style, 'data-bs-hreveal' => '1' );
if ( '' !== trim( (string) ( $a['label'] ?? '' ) ) ) {
	$attrs['aria-label'] = trim( (string) $a['label'] );
}
?>
<ul <?php echo get_block_wrapper_attributes( $attrs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></ul>
