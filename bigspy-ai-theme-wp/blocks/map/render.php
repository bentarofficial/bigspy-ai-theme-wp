<?php
/**
 * Google Map block (embed, no API key).
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a       = (array) $attributes;
$embed_src = '';
$embed_raw = trim( (string) ( $a['embed'] ?? '' ) );
if ( '' !== $embed_raw ) {
	if ( preg_match( '/src=["\']([^"\']+)["\']/i', $embed_raw, $m ) ) {
		$embed_raw = $m[1];
	}
	$embed_raw = html_entity_decode( $embed_raw, ENT_QUOTES );
	if ( preg_match( '#^https://(www\.)?google\.[a-z.]+/maps/embed\?#i', $embed_raw ) || preg_match( '#^https://maps\.google\.[a-z.]+/maps\?.*output=embed#i', $embed_raw ) ) {
		$embed_src = $embed_raw;
	}
}
$address = trim( wp_strip_all_tags( (string) ( $a['address'] ?? '' ) ) );
$lat     = is_numeric( $a['lat'] ?? '' ) ? (float) $a['lat'] : null;
$lng     = is_numeric( $a['lng'] ?? '' ) ? (float) $a['lng'] : null;
if ( null !== $lat && null !== $lng ) {
	$address = $lat . ',' . $lng;
}
if ( '' === $address && '' === $embed_src ) {
	$address = (string) bst_mod( 'contact_address' );
}
// Coordinates inside a Google embed (…!2d<lng>!3d<lat>…) keep "Get directions" working.
if ( '' === $address && $embed_src && preg_match( '/!2d(-?[0-9.]+)!3d(-?[0-9.]+)/', $embed_src, $c ) ) {
	$address = $c[2] . ',' . $c[1];
}
if ( '' === $address && '' === $embed_src ) {
	return;
}

$src = add_query_arg(
	array(
		'q'      => rawurlencode( $address ),
		'z'      => (int) bst_block_num( $a, 'zoom', 15, 1, 21 ),
		'output' => 'embed',
	),
	'https://maps.google.com/maps'
);
if ( $embed_src ) {
	$src = $embed_src;
}
$style = bst_style_vars(
	array(
		'--bs-map-h'      => bst_block_num( $a, 'height', 400, 150, 1200 ) . 'px',
		'--bs-map-radius' => bst_block_num( $a, 'radius', 8, 0, 60 ) . 'px',
	)
);
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-map' . ( ! empty( $a['grayscale'] ) ? ' is-grayscale' : '' ) . ( ! empty( $a['boxTitle'] ) || ! empty( $a['boxText'] ) ? ' has-box bs-map--box-' . bst_choice( $a['boxPosition'] ?? 'left', array( 'left', 'right' ), 'left' ) : '' ), 'style' => $style ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( ! empty( $a['boxTitle'] ) || ! empty( $a['boxText'] ) ) : ?>
		<div class="bs-map__box">
			<?php if ( ! empty( $a['boxTitle'] ) ) : ?><h3 class="bs-map__title"><?php echo esc_html( $a['boxTitle'] ); ?></h3><?php endif; ?>
			<?php if ( ! empty( $a['boxText'] ) ) : ?><div class="bs-map__text"><?php echo wp_kses_post( wpautop( $a['boxText'] ) ); ?></div><?php endif; ?>
			<?php if ( ! isset( $a['directions'] ) || ! empty( $a['directions'] ) ) : ?>
				<a class="bs-btn bs-btn--sm" href="<?php echo esc_url( 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode( $address ) ); ?>" target="_blank" rel="noopener"><?php echo bst_icon( 'pin', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Get directions', 'bigspy-ai-theme-wp' ); ?></span></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<iframe src="<?php echo esc_url( $src ); ?>" title="<?php echo esc_attr( $address ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
</div>
