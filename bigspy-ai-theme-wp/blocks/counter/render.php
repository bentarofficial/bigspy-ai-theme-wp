<?php
/**
 * Counter block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a      = (array) $attributes;
$number = preg_replace( '/[^0-9.]/', '', (string) ( $a['number'] ?? '0' ) );
$number = '' === $number ? '0' : $number;
$sep    = bst_choice( $a['separator'] ?? '.', array( '.', ',', ' ', '' ), '.' );
$align  = bst_choice( $a['align'] ?? 'center', array( 'left', 'center', 'right' ), 'center' );
$style  = bst_style_vars(
	array(
		'--bs-counter-size'  => bst_block_num( $a, 'size', 44, 16, 140 ) . 'px',
		'--bs-counter-color' => bst_block_color( $a, 'color' ),
		'--bs-counter-text-size' => ! empty( $a['textSize'] ) ? bst_block_num( $a, 'textSize', 0, 10, 80 ) . 'px' : '',
		'--bs-counter-text-color' => bst_block_color( $a, 'textColor' ),
		'--bs-counter-affix-size' => ! empty( $a['affixSize'] ) ? bst_block_num( $a, 'affixSize', 0, 8, 140 ) . 'px' : '',
		'--bs-counter-affix-color' => bst_block_color( $a, 'affixColor' ),
		'--bs-counter-weight' => in_array( (string) ( $a['weight'] ?? '' ), array( '400', '500', '600', '700', '800', '900' ), true ) ? $a['weight'] : '',
		'--bs-counter-icon-size' => ! empty( $a['iconSize'] ) ? bst_block_num( $a, 'iconSize', 0, 12, 160 ) . 'px' : '',
		'--bs-counter-icon-color' => bst_block_color( $a, 'iconColor' ),
	)
);
$cstyle = bst_choice( $a['style'] ?? '', array( '', 'card', 'divided', 'side' ), '' );
$cicon  = ! empty( $a['icon'] ) ? '<span class="bs-counter__icon" aria-hidden="true">' . bst_icon( sanitize_key( $a['icon'] ), 32 ) . '</span>' : '';
$inline = 'inline' === ( $a['layout'] ?? 'stack' );
$from   = isset( $a['from'] ) ? (float) bst_block_num( $a, 'from', 0, 0, 1000000000 ) : 0;
$ease   = bst_choice( $a['ease'] ?? 'out', array( 'out', 'linear', 'bounce' ), 'out' );

$parts     = explode( '.', $number );
$formatted = number_format( (float) $number, isset( $parts[1] ) ? strlen( $parts[1] ) : 0, '.' === $sep ? ',' : '.', $sep );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-counter has-text-align-' . $align . ( $inline ? ' bs-counter--inline' : '' ) . ( $cstyle ? ' bs-counter--' . $cstyle : '' ) . ( $cicon ? ' has-icon' : '' ), 'style' => $style ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php echo $cicon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<?php if ( 'side' === $cstyle ) : ?>
	<div class="bs-counter__main">
	<?php endif; ?>
	<div class="bs-counter__value">
		<?php if ( ! empty( $a['prefix'] ) ) : ?>
			<span class="bs-counter__affix"><?php echo esc_html( $a['prefix'] ); ?></span>
		<?php endif; ?>
		<span class="bs-counter__number" data-bs-counter data-to="<?php echo esc_attr( $number ); ?>" data-sep="<?php echo esc_attr( $sep ); ?>" data-duration="<?php echo (int) bst_block_num( $a, 'duration', 1600, 200, 10000 ); ?>" data-from="<?php echo esc_attr( $from ); ?>" data-ease="<?php echo esc_attr( $ease ); ?>"<?php echo ! empty( $a['replay'] ) ? ' data-replay' : ''; ?>><?php echo esc_html( $formatted ); ?></span>
		<?php if ( ! empty( $a['suffix'] ) ) : ?>
			<span class="bs-counter__affix"><?php echo esc_html( $a['suffix'] ); ?></span>
		<?php endif; ?>
		<?php if ( $inline && ! empty( $a['label'] ) ) : ?>
			<span class="bs-counter__label"><?php echo wp_kses_post( $a['label'] ); ?></span>
		<?php endif; ?>
	</div>
	<?php if ( ! $inline && ! empty( $a['label'] ) ) : ?>
		<div class="bs-counter__label"><?php echo wp_kses_post( $a['label'] ); ?></div>
	<?php endif; ?>
	<?php if ( 'side' === $cstyle ) : ?>
	</div>
	<?php endif; ?>
</div>
