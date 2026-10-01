<?php
/**
 * Section title block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a     = (array) $attributes;
$tag   = bst_choice( $a['tag'] ?? 'h2', array( 'h1', 'h2', 'h3', 'h4' ), 'h2' );
$style = bst_choice( $a['style'] ?? 'bottom-line', array( 'plain', 'bottom-line', 'center-line', 'left-bar', 'underline-full' ), 'bottom-line' );
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
	)
);
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => "bs-title bs-title--$style bs-title--align-$align", 'style' => $vars ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( ! empty( $a['subtitle'] ) ) : ?>
		<p class="bs-title__subtitle"><?php echo wp_kses_post( $a['subtitle'] ); ?></p>
	<?php endif; ?>
	<div class="bs-title__row">
		<<?php echo esc_html( $tag ); ?> class="bs-title__text"><?php echo $text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></<?php echo esc_html( $tag ); ?>>
		<?php if ( $link && ! empty( $a['linkText'] ) ) : ?>
			<a class="bs-title__link" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $a['linkText'] ); ?><?php echo bst_icon( 'arrow-right', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
		<?php endif; ?>
	</div>
</div>
