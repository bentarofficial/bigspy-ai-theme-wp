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
	$embed_src = bst_map_embed_url( $embed_raw );
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
		'--bs-map-ht'     => ! empty( $a['heightTablet'] ) ? bst_block_num( $a, 'heightTablet', 400, 150, 1200 ) . 'px' : '',
		'--bs-map-hm'     => ! empty( $a['heightMobile'] ) ? bst_block_num( $a, 'heightMobile', 300, 150, 1200 ) . 'px' : '',
		'--bs-map-ratio'  => in_array( (string) ( $a['ratio'] ?? '' ), array( '16/9', '4/3', '1/1', '21/9', '3/4' ), true ) ? $a['ratio'] : '',
		'--bs-map-bbg'    => bst_block_color( $a, 'boxBg' ),
		'--bs-map-bc'     => bst_block_color( $a, 'boxColor' ),
		'--bs-map-bdc'    => bst_block_color( $a, 'borderColor' ),
		'--bs-map-bw'     => ! empty( $a['borderWidth'] ) ? min( 10, (int) $a['borderWidth'] ) . 'px' : '',
	)
);
$has_box  = ! empty( $a['boxTitle'] ) || ! empty( $a['boxText'] );
$box_pos  = bst_choice( $a['boxPosition'] ?? 'left', array( 'left', 'right', 'side', 'side-right', 'top', 'bottom' ), 'left' );
$btn_cls  = array( '' => 'bs-btn bs-btn--sm', 'outline' => 'bs-btn bs-btn--sm bs-btn--outline', 'link' => 'bs-map__link' );
$btn_cls  = $btn_cls[ bst_choice( $a['btnStyle'] ?? '', array( '', 'outline', 'link' ), '' ) ];
$lazy     = ! empty( $a['clickToLoad'] );
$extra    = ( ! empty( $a['fullWidth'] ) ? ' alignfull bs-map--full' : '' ) . ( ! empty( $a['shadow'] ) ? ' has-shadow' : '' ) . ( $lazy ? ' is-click-load' : '' ) . ( ! empty( $style ) && false !== strpos( $style, '--bs-map-ratio' ) ? ' has-ratio' : '' );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-map' . ( ! empty( $a['grayscale'] ) ? ' is-grayscale' : '' ) . ( $has_box ? ' has-box bs-map--box-' . $box_pos : '' ) . $extra, 'style' => $style ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( ! empty( $a['boxTitle'] ) || ! empty( $a['boxText'] ) ) : ?>
		<div class="bs-map__box">
			<?php if ( ! empty( $a['boxTitle'] ) ) : ?><h3 class="bs-map__title"><?php echo esc_html( $a['boxTitle'] ); ?></h3><?php endif; ?>
			<?php if ( ! empty( $a['boxText'] ) ) : ?><div class="bs-map__text"><?php echo wp_kses_post( wpautop( $a['boxText'] ) ); ?></div><?php endif; ?>
			<?php if ( ! isset( $a['directions'] ) || ! empty( $a['directions'] ) ) : ?>
				<a class="<?php echo esc_attr( $btn_cls ); ?>" href="<?php echo esc_url( 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode( $address ) ); ?>" target="_blank" rel="noopener"><?php echo bst_icon( 'pin', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Get directions', 'bigspy-ai-theme-wp' ); ?></span></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<?php if ( $lazy ) : ?>
		<?php
		$ph = ! empty( $a['placeholderId'] ) ? wp_get_attachment_image( (int) $a['placeholderId'], 'large', false, array( 'class' => 'bs-map__ph', 'alt' => '', 'loading' => 'lazy' ) ) : ( ! empty( $a['placeholderUrl'] ) ? '<img class="bs-map__ph" src="' . esc_url( $a['placeholderUrl'] ) . '" alt="" loading="lazy">' : '' );
		?>
		<button type="button" class="bs-map__load" data-bs-map-src="<?php echo esc_url( $src ); ?>" data-bs-map-title="<?php echo esc_attr( '' !== $address ? $address : __( 'Map', 'bigspy-ai-theme-wp' ) ); ?>"><?php echo $ph; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="bs-map__load-label"><?php echo bst_icon( 'pin', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Show the map', 'bigspy-ai-theme-wp' ); ?></span></button>
	<?php else : ?>
	<iframe src="<?php echo esc_url( $src ); ?>" title="<?php echo esc_attr( '' !== $address ? $address : __( 'Map', 'bigspy-ai-theme-wp' ) ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
	<?php endif; ?>
</div>
