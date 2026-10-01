<?php
/**
 * Icon box block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a          = (array) $attributes;
$position   = bst_choice( $a['position'] ?? 'top', array( 'top', 'left' ), 'top' );
$align      = bst_choice( $a['align'] ?? 'center', array( 'left', 'center' ), 'center' );
$icon_style = bst_choice( $a['iconStyle'] ?? 'circle', array( 'plain', 'circle', 'square', 'outline' ), 'circle' );
$box_style  = bst_choice( $a['boxStyle'] ?? 'none', array( 'none', 'card', 'bordered' ), 'none' );
$tag        = bst_choice( $a['titleTag'] ?? 'h3', array( 'h2', 'h3', 'h4', 'h5', 'p' ), 'h3' );
$size       = bst_block_num( $a, 'iconSize', 32, 12, 160 );
$link       = esc_url( $a['link'] ?? '' );

$media = '';
if ( ! empty( $a['imageId'] ) ) {
	$media = wp_get_attachment_image( (int) $a['imageId'], 'medium', false, array( 'class' => 'bs-icon-box__img', 'alt' => '' ) );
} elseif ( ! empty( $a['imageUrl'] ) ) {
	$media = '<img class="bs-icon-box__img" src="' . esc_url( $a['imageUrl'] ) . '" alt="" loading="lazy">';
} elseif ( ! empty( $a['icon'] ) ) {
	$media = bst_icon( sanitize_key( $a['icon'] ), $size );
}

$classes = "bs-icon-box bs-icon-box--$position bs-icon-box--align-$align bs-icon-box--icon-$icon_style bs-icon-box--box-$box_style";
$style   = bst_style_vars(
	array(
		'--bs-ib-size'  => $size . 'px',
		'--bs-ib-color' => bst_block_color( $a, 'iconColor' ),
		'--bs-ib-bg'    => bst_block_color( $a, 'iconBg' ),
	)
);
$title = wp_kses_post( $a['title'] ?? '' );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => $classes, 'style' => $style ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $media ) : ?>
		<div class="bs-icon-box__icon"><?php echo $media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
	<?php endif; ?>
	<div class="bs-icon-box__body">
		<?php if ( $title ) : ?>
			<<?php echo esc_html( $tag ); ?> class="bs-icon-box__title"><?php echo $link ? '<a href="' . esc_url( $link ) . '">' . $title . '</a>' : $title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></<?php echo esc_html( $tag ); ?>>
		<?php endif; ?>
		<?php if ( ! empty( $a['text'] ) ) : ?>
			<div class="bs-icon-box__text"><?php echo wp_kses_post( wpautop( $a['text'] ) ); ?></div>
		<?php endif; ?>
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
</div>
