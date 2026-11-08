<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$a = is_array( $attributes ?? null ) ? $attributes : array();
$width = in_array( $a['contentWidth'] ?? 'boxed', array( 'boxed', 'full' ), true ) ? $a['contentWidth'] : 'boxed';
$valign = in_array( $a['verticalAlign'] ?? 'center', array( 'start', 'center', 'end' ), true ) ? $a['verticalAlign'] : 'center';
$overflow = ( $a['overflow'] ?? 'hidden' ) === 'visible' ? 'visible' : 'hidden';
$classes = 'bigspy-section bigspy-section--valign-' . $valign . ' bigspy-section--overflow-' . $overflow;
foreach ( array( 'Desktop', 'Tablet', 'Mobile' ) as $device ) { if ( ! empty( $a['hide' . $device] ) ) $classes .= ' bigspy-layout-hide-' . strtolower( $device ); }
$num = static function( $value, $default, $min, $max ) { return max( $min, min( $max, is_numeric( $value ) ? (int) $value : $default ) ); };
$overlay = sanitize_hex_color( $a['overlayColor'] ?? '' ) ?: 'transparent';
$style = '--bigspy-section-max:' . $num( $a['maxWidth'] ?? 1200, 1200, 640, 1920 ) . 'px;--bigspy-section-py-d:' . $num( $a['paddingYDesktop'] ?? 64, 64, 0, 200 ) . 'px;--bigspy-section-py-t:' . $num( $a['paddingYTablet'] ?? 48, 48, 0, 160 ) . 'px;--bigspy-section-py-m:' . $num( $a['paddingYMobile'] ?? 32, 32, 0, 120 ) . 'px;--bigspy-section-px:' . $num( $a['paddingX'] ?? 24, 24, 0, 100 ) . 'px;--bigspy-section-min:' . $num( $a['minHeight'] ?? 0, 0, 0, 1000 ) . 'px;--bigspy-section-bg-opacity:' . ( $num( $a['backgroundOpacity'] ?? 100, 100, 0, 100 ) / 100 ) . ';--bigspy-section-bg-blur:' . $num( $a['backgroundBlur'] ?? 0, 0, 0, 30 ) . 'px;--bigspy-section-overlay:' . $overlay . ';--bigspy-section-overlay-opacity:' . ( $num( $a['overlayOpacity'] ?? 0, 0, 0, 100 ) / 100 ) . ';';
$url = esc_url( $a['backgroundImageUrl'] ?? '' );
$size = in_array( $a['backgroundSize'] ?? 'cover', array( 'cover', 'contain', 'auto' ), true ) ? $a['backgroundSize'] : 'cover';
$position = in_array( $a['backgroundPosition'] ?? 'center', array( 'center', 'top', 'bottom', 'left', 'right' ), true ) ? $a['backgroundPosition'] : 'center';
$bg_style = $url ? 'background-image:url(' . $url . ');background-size:' . $size . ';background-position:' . $position . ';' : '';
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => $classes, 'style' => $style ) ); ?>><?php if ( $url ) : ?><span class="bigspy-layout-background" style="<?php echo esc_attr( $bg_style ); ?>" aria-hidden="true"></span><?php endif; ?><?php if ( $overlay !== 'transparent' && ! empty( $a['overlayOpacity'] ) ) : ?><span class="bigspy-layout-overlay" aria-hidden="true"></span><?php endif; ?><div class="bigspy-section__container bigspy-section__container--<?php echo esc_attr( $width ); ?>"><?php echo $content; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?></div></section>