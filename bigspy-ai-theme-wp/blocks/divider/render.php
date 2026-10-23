<?php
/**
 * Divider block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a     = (array) $attributes;
$style = bst_choice( $a['style'] ?? 'solid', array( 'solid', 'dashed', 'dotted', 'double' ), 'solid' );
$align = bst_choice( $a['align'] ?? 'center', array( 'left', 'center', 'right' ), 'center' );
$vars  = bst_style_vars(
	array(
		'--bs-divider-width'   => bst_block_num( $a, 'width', 100, 1, 100 ) . '%',
		'--bs-divider-size'    => bst_block_num( $a, 'thickness', 1, 1, 20 ) . 'px',
		'--bs-divider-color'   => bst_block_color( $a, 'color' ),
		'--bs-divider-spacing' => bst_block_num( $a, 'spacing', 24, 0, 200 ) . 'px',
		'--bs-divider-style'   => $style,
	)
);
?>
<?php if ( ! empty( $a['text'] ) || ! empty( $a['icon'] ) ) : ?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-divider-x bs-divider-x--' . $align . ( ! empty( $a['gradient'] ) ? ' is-gradient' : '' ), 'style' => $vars . ( bst_block_color( $a, 'contentColor' ) ? ';--bs-divider-content:' . bst_block_color( $a, 'contentColor' ) : '' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> role="separator">
	<span class="bs-divider-x__content"><?php echo ! empty( $a['icon'] ) ? bst_icon( sanitize_key( $a['icon'] ), 20 ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo ! empty( $a['text'] ) ? '<span>' . esc_html( $a['text'] ) . '</span>' : ''; ?></span>
</div>
<?php else : ?>
<hr <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-divider bs-divider--' . $align . ( ! empty( $a['gradient'] ) ? ' is-gradient' : '' ), 'style' => $vars ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
<?php endif; ?>
