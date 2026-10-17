<?php
/**
 * Banner block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a       = (array) $attributes;
$pos_x   = bst_choice( $a['posX'] ?? 'center', array( 'left', 'center', 'right' ), 'center' );
$pos_y   = bst_choice( $a['posY'] ?? 'middle', array( 'top', 'middle', 'bottom' ), 'middle' );
$align   = bst_choice( $a['textAlign'] ?? 'center', array( 'left', 'center', 'right' ), 'center' );
$scheme  = bst_choice( $a['textScheme'] ?? 'light', array( 'light', 'dark' ), 'light' );
$hover   = bst_choice( $a['hover'] ?? 'zoom', array( 'none', 'zoom', 'glow' ), 'zoom' );
$overlay = bst_block_color( $a, 'overlayColor' );
$link    = esc_url( $a['link'] ?? '' );

$classes = "bs-banner bs-banner--x-$pos_x bs-banner--y-$pos_y bs-banner--hover-$hover is-scheme-$scheme has-text-align-$align";
$style   = bst_style_vars(
	array(
		'--bs-banner-hd'     => bst_block_num( $a, 'heightDesktop', 500, 100, 1400 ) . 'px',
		'--bs-banner-ht'     => bst_block_num( $a, 'heightTablet', 420, 100, 1400 ) . 'px',
		'--bs-banner-hm'     => bst_block_num( $a, 'heightMobile', 340, 100, 1400 ) . 'px',
		'--bs-banner-width'  => bst_block_num( $a, 'contentWidth', 70, 20, 100 ) . '%',
		'--bs-banner-radius' => bst_block_num( $a, 'radius', 0, 0, 80 ) . 'px',
	)
);
$focal = bst_block_num( $a, 'focalX', 50, 0, 100 ) . '% ' . bst_block_num( $a, 'focalY', 50, 0, 100 ) . '%';

$image = '';
if ( ! empty( $a['imageId'] ) ) {
	$image = wp_get_attachment_image( (int) $a['imageId'], 'full', false, array( 'class' => 'bs-banner__img', 'alt' => '', 'style' => 'object-position:' . $focal, 'sizes' => '100vw' ) );
}
if ( ! $image && ! empty( $a['imageUrl'] ) ) {
	$image = '<img class="bs-banner__img" src="' . esc_url( $a['imageUrl'] ) . '" alt="" loading="lazy" decoding="async" style="object-position:' . esc_attr( $focal ) . '">';
}

// Optional phone image (<= 549px), e.g. a portrait crop with the text area kept clear.
if ( $image ) {
	$mobile_src = '';
	$mobile_set = '';
	if ( ! empty( $a['imageMobileId'] ) ) {
		$mobile_src = (string) wp_get_attachment_image_url( (int) $a['imageMobileId'], 'large' );
		$mobile_set = (string) wp_get_attachment_image_srcset( (int) $a['imageMobileId'], 'large' );
	}
	if ( '' === $mobile_src && ! empty( $a['imageMobileUrl'] ) ) {
		$mobile_src = esc_url_raw( $a['imageMobileUrl'] );
	}
	if ( '' !== $mobile_src ) {
		$image = '<picture class="bs-banner__picture"><source media="(max-width: 549px)" srcset="' . esc_attr( '' !== $mobile_set ? $mobile_set : $mobile_src ) . '" sizes="100vw">' . $image . '</picture>';
	}
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => $classes, 'style' => $style ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="bs-banner__bg" aria-hidden="true"><?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
	<?php if ( $overlay ) : ?>
		<div class="bs-banner__overlay" style="<?php echo esc_attr( 'background:' . $overlay . ';opacity:' . ( bst_block_num( $a, 'overlayOpacity', 35, 0, 100 ) / 100 ) ); ?>" aria-hidden="true"></div>
	<?php endif; ?>
	<?php if ( $link ) : ?>
		<a class="bs-banner__link" href="<?php echo esc_url( $link ); ?>"<?php echo ! empty( $a['linkNewTab'] ) ? ' target="_blank" rel="noopener"' : ''; ?> aria-label="<?php echo esc_attr( wp_strip_all_tags( $content ) ? wp_trim_words( wp_strip_all_tags( $content ), 8, '' ) : __( 'Open link', 'bigspy-ai-theme-wp' ) ); ?>"></a>
	<?php endif; ?>
	<div class="bs-banner__content"><div class="bs-banner__box"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div></div>
</div>
