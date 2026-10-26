<?php
defined( 'ABSPATH' ) || exit;
$a = $attributes;
$id = wp_unique_id( 'bs-expand-content-' );
$x = max( 0, min( 1, (float) ( $a['focalPoint']['x'] ?? .5 ) ) ) * 100;
$y = max( 0, min( 1, (float) ( $a['focalPoint']['y'] ?? .5 ) ) ) * 100;
$overlay = (string) ( $a['overlay'] ?? '#00000066' );
// Only color/gradient CSS values, with no URL or declaration injection.
if ( preg_match( '/[;{}<>]|url\s*\(/i', $overlay ) || ! preg_match( '/^(#[a-f0-9]{3,8}|rgba?\([0-9.,% ]+\)|(?:linear|radial)-gradient\([a-z0-9#.,%() -]+\))$/iD', $overlay ) ) { $overlay = '#00000066'; }
$style = '--bs-expand-overlay:' . $overlay . ';';
if ( ! empty( $a['backgroundUrl'] ) ) { $style .= 'background-image:url("' . esc_url( $a['backgroundUrl'] ) . '");background-position:' . $x . '% ' . $y . '%;'; }
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-expand__panel', 'style' => $style, 'tabindex' => '0', 'role' => 'group', 'aria-label' => $a['title'] ) ); ?>>
<button type="button" class="bs-expand__title" aria-expanded="false" aria-controls="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $a['title'] ); ?></button>
<div class="bs-expand__content" id="<?php echo esc_attr( $id ); ?>"><?php echo $content; ?></div>
</div>
