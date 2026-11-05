<?php
/**
 * Section title block: title on the left (any tag, optional link), custom link text on the right.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a     = (array) $attributes;
$tag   = bst_choice( $a['tag'] ?? 'h2', array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div' ), 'h2' );
$style = bst_choice( $a['style'] ?? 'bottom-line', array( 'plain', 'bottom-line', 'center-line', 'left-bar', 'underline-full' ), 'bottom-line' );
$align = bst_choice( $a['align'] ?? 'center', array( 'left', 'center', 'right' ), 'center' );
$text  = wp_kses_post( $a['text'] ?? '' );
$link  = esc_url( $a['linkUrl'] ?? '' );
$t_url = esc_url( $a['titleUrl'] ?? '' );

if ( '' === $text ) {
	return;
}

/* Shared target/rel for both links. */
$rel   = array_filter( array( ! empty( $a['newTab'] ) ? 'noopener' : '', ! empty( $a['nofollow'] ) ? 'nofollow' : '' ) );
$extra = ( ! empty( $a['newTab'] ) ? ' target="_blank"' : '' ) . ( $rel ? ' rel="' . esc_attr( implode( ' ', $rel ) ) . '"' : '' );
$plain = trim( wp_strip_all_tags( $text ) );

if ( $t_url ) {
	$text = '<a class="bs-title__text-link" href="' . $t_url . '"' . $extra . '>' . wp_kses( $text, array( 'strong' => array(), 'em' => array(), 'b' => array(), 'i' => array(), 'span' => array( 'class' => true, 'style' => true ), 'br' => array(), 'mark' => array( 'class' => true, 'style' => true ) ) ) . '</a>';
}

$vars = bst_style_vars(
	array(
		'--bs-title-size'  => ! empty( $a['size'] ) ? bst_block_num( $a, 'size', 0, 12, 120 ) . 'px' : '',
		'--bs-title-color' => bst_block_color( $a, 'color' ),
		'--bs-title-line'  => bst_block_color( $a, 'lineColor' ),
	)
);
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => "bs-title bs-title--$style bs-title--align-$align bs-title--tag-$tag", 'style' => $vars ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( ! empty( $a['subtitle'] ) ) : ?>
		<p class="bs-title__subtitle"><?php echo wp_kses_post( $a['subtitle'] ); ?></p>
	<?php endif; ?>
	<div class="bs-title__row">
		<<?php echo esc_html( $tag ); ?> class="bs-title__text"><?php echo $text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></<?php echo esc_html( $tag ); ?>>
		<?php if ( $link && ! empty( $a['linkText'] ) ) : ?>
			<a class="bs-title__link" href="<?php echo esc_url( $link ); ?>"<?php echo $extra; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-label="<?php echo esc_attr( $a['linkText'] . ( $plain ? ' – ' . $plain : '' ) ); ?>"><?php echo esc_html( $a['linkText'] ); ?><?php echo bst_icon( 'arrow-right', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
		<?php endif; ?>
	</div>
</div>
