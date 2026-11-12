<?php
/**
 * Divider block.
 *
 * Attributes added in G-129 are empty/0/false by default and then print nothing, so
 * older dividers render exactly as before.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a     = (array) $attributes;
$style = bst_choice( $a['style'] ?? 'solid', array( 'solid', 'dashed', 'dotted', 'double', 'fade', 'wave', 'zigzag', 'dots', 'thick-thin', 'shadow' ), 'solid' );
$align = bst_choice( $a['align'] ?? 'center', array( 'left', 'center', 'right' ), 'center' );
$is_px = 'px' === ( $a['widthUnit'] ?? '' );
$shape = in_array( $style, array( 'wave', 'zigzag', 'dots', 'thick-thin', 'shadow' ), true );
$width = function ( $key, $default ) use ( $a, $is_px ) {
	if ( '' === $default && ( ! isset( $a[ $key ] ) || ! is_numeric( $a[ $key ] ) || 0 >= $a[ $key ] ) ) {
		return '';
	}
	return $is_px ? bst_block_num( $a, $key, 100, 1, 4000 ) . 'px' : bst_block_num( $a, $key, 100, 1, 100 ) . '%';
};
$space = function ( $key ) use ( $a ) {
	return isset( $a[ $key ] ) && is_numeric( $a[ $key ] ) ? bst_block_num( $a, $key, 24, 0, 200 ) . 'px' : '';
};
$vars  = bst_style_vars(
	array(
		'--bs-divider-width'   => $width( 'width', 100 ),
		'--bs-divider-size'    => bst_block_num( $a, 'thickness', 1, 1, 20 ) . 'px',
		'--bs-divider-color'   => bst_block_color( $a, 'color' ),
		'--bs-divider-spacing' => bst_block_num( $a, 'spacing', 24, 0, 200 ) . 'px',
		'--bs-divider-style'   => $shape ? 'solid' : $style,
	)
);
// New options: only non-empty values reach the markup.
$new_vars = bst_style_vars(
	array_merge(
		array(
			'--bs-divider-width-t'   => $width( 'widthTablet', '' ),
			'--bs-divider-width-m'   => $width( 'widthMobile', '' ),
			'--bs-divider-spacing-t' => $space( 'spacingTablet' ),
			'--bs-divider-spacing-m' => $space( 'spacingMobile' ),
			'--bs-divider-from'      => bst_block_color( $a, 'gradientFrom' ),
			'--bs-divider-to'        => bst_block_color( $a, 'gradientTo' ),
			'--bs-divider-icon-size' => ! empty( $a['iconSize'] ) ? bst_block_num( $a, 'iconSize', 20, 8, 120 ) . 'px' : '',
			'--bs-divider-icon-bg'   => bst_block_color( $a, 'iconBg' ),
		),
		bst_part_vars( $a, 'divider', 'text', array( 'Size', 'Weight', 'LetterSpacing', 'Transform' ) )
	)
);
$vars .= ( $new_vars && $vars ? ';' : '' ) . $new_vars;
$mods  = ( $shape ? ' is-' . $style : '' ) . ( ! empty( $a['draw'] ) ? ' is-draw' : '' );
$pos   = bst_choice( $a['contentPosition'] ?? '', array( 'left', 'right' ), '' );
$icon_size = ! empty( $a['iconSize'] ) ? bst_block_num( $a, 'iconSize', 20, 8, 120 ) : 20;
?>
<?php if ( ! empty( $a['text'] ) || ! empty( $a['icon'] ) ) : ?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-divider-x bs-divider-x--' . $align . ( ! empty( $a['gradient'] ) ? ' is-gradient' : '' ) . ( 'fade' === $style ? ' is-fade' : '' ) . $mods . ( $pos ? ' is-content-' . $pos : '' ) . ( bst_block_color( $a, 'iconBg' ) ? ' has-icon-bg' : '' ), 'style' => $vars . ( bst_block_color( $a, 'contentColor' ) ? ';--bs-divider-content:' . bst_block_color( $a, 'contentColor' ) : '' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> role="separator">
	<span class="bs-divider-x__content"><?php echo ! empty( $a['icon'] ) ? bst_icon( sanitize_key( $a['icon'] ), $icon_size ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo ! empty( $a['text'] ) ? '<span>' . esc_html( $a['text'] ) . '</span>' : ''; ?></span>
</div>
<?php else : ?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-divider-wrap bs-divider-wrap--' . $align,'style' => $vars ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><hr class="<?php echo esc_attr( 'bs-divider bs-divider--' . $align . ( ! empty( $a['gradient'] ) ? ' is-gradient' : '' ) . ( 'fade' === $style ? ' is-fade' : '' ) . $mods ); ?>"></div>
<?php endif; ?>
