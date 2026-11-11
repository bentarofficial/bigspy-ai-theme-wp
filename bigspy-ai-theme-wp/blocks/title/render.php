<?php
/**
 * Section title block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a     = (array) $attributes;
$tag   = bst_choice( $a['tag'] ?? 'h2', array( 'h1', 'h2', 'h3', 'h4' ), 'h2' );
$style = bst_choice( $a['style'] ?? 'bottom-line', array( 'plain', 'bottom-line', 'center-line', 'left-bar', 'underline-full', 'double', 'label', 'dots', 'gradient' ), 'bottom-line' );
$align = bst_choice( $a['align'] ?? 'center', array( 'left', 'center', 'right' ), 'center' );
$text  = wp_kses_post( $a['text'] ?? '' );
$link  = esc_url( $a['linkUrl'] ?? '' );

if ( '' === $text ) {
	return;
}

$vars = bst_style_vars(
	array(
		'--bs-title-size'  => ! empty( $a['size'] ) ? bst_block_num( $a, 'size', 0, 12, 120 ) . 'px' : '',
		'--bs-title-color' => bst_block_color( $a, 'color' ),
		'--bs-title-line'  => bst_block_color( $a, 'lineColor' ),
		// G-132: per-device size, kicker size/color, line thickness and length.
		'--bs-title-size-t' => ! empty( $a['sizeTablet'] ) ? bst_block_num( $a, 'sizeTablet', 0, 12, 120 ) . 'px' : '',
		'--bs-title-size-m' => ! empty( $a['sizeMobile'] ) ? bst_block_num( $a, 'sizeMobile', 0, 12, 120 ) . 'px' : '',
		'--bs-title-sub-fs' => ! empty( $a['subtitleSize'] ) ? bst_block_num( $a, 'subtitleSize', 0, 8, 60 ) . 'px' : '',
		'--bs-title-sub-c'  => bst_block_color( $a, 'subtitleColor' ),
		'--bs-title-line-w' => ! empty( $a['lineWidth'] ) ? bst_block_num( $a, 'lineWidth', 0, 1, 20 ) . 'px' : '',
		'--bs-title-line-l' => ! empty( $a['lineLength'] ) ? bst_block_num( $a, 'lineLength', 0, 8, 600 ) . 'px' : '',
	)
);
$pre = '';
if ( '' !== trim( (string) ( $a['number'] ?? '' ) ) ) {
	$pre .= '<span class="bs-title__num">' . esc_html( mb_substr( trim( (string) $a['number'] ), 0, 6 ) ) . '</span>';
}
if ( ! empty( $a['icon'] ) ) {
	$pre .= '<span class="bs-title__icon">' . bst_icon( sanitize_key( $a['icon'] ), 24 ) . '</span>';
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => "bs-title bs-title--$style bs-title--align-$align", 'style' => $vars ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( ! empty( $a['subtitle'] ) ) : ?>
		<p class="bs-title__subtitle"><?php echo wp_kses_post( $a['subtitle'] ); ?></p>
	<?php endif; ?>
	<div class="bs-title__row">
		<<?php echo esc_html( $tag ); ?> class="bs-title__text<?php echo $pre ? ' has-pre' : ''; ?>"><?php echo $pre . $text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></<?php echo esc_html( $tag ); ?>>
		<?php if ( $link && ! empty( $a['linkText'] ) ) : ?>
			<a class="bs-title__link" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $a['linkText'] ); ?><?php echo bst_icon( 'arrow-right', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
		<?php endif; ?>
	</div>
</div>
