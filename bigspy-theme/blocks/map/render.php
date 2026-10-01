<?php
/**
 * Google Map block (embed, no API key).
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a       = (array) $attributes;
$address = trim( wp_strip_all_tags( (string) ( $a['address'] ?? '' ) ) );
if ( '' === $address ) {
	$address = (string) bst_mod( 'contact_address' );
}
if ( '' === $address ) {
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
$style = bst_style_vars(
	array(
		'--bs-map-h'      => bst_block_num( $a, 'height', 400, 150, 1200 ) . 'px',
		'--bs-map-radius' => bst_block_num( $a, 'radius', 8, 0, 60 ) . 'px',
	)
);
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-map', 'style' => $style ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<iframe src="<?php echo esc_url( $src ); ?>" title="<?php echo esc_attr( $address ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
</div>
