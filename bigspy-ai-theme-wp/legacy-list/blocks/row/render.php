<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$a = is_array( $attributes ?? null ) ? $attributes : array();
$num = static function( $value, $default, $min, $max ) { return max( $min, min( $max, is_numeric( $value ) ? (int) $value : $default ) ); };
$align = in_array( $a['verticalAlign'] ?? 'stretch', array( 'stretch', 'start', 'center', 'end' ), true ) ? $a['verticalAlign'] : 'stretch';
$classes = 'bigspy-row bigspy-row--align-' . $align . ( ! empty( $a['reverseMobile'] ) ? ' bigspy-row--reverse-mobile' : '' );
foreach ( array( 'Desktop', 'Tablet', 'Mobile' ) as $device ) { if ( ! empty( $a['hide' . $device] ) ) $classes .= ' bigspy-layout-hide-' . strtolower( $device ); }
$style = '--bigspy-row-cols-d:' . $num( $a['columnsDesktop'] ?? 2, 2, 1, 6 ) . ';--bigspy-row-cols-t:' . $num( $a['columnsTablet'] ?? 2, 2, 1, 4 ) . ';--bigspy-row-cols-m:' . $num( $a['columnsMobile'] ?? 1, 1, 1, 2 ) . ';--bigspy-row-gap-d:' . $num( $a['gapDesktop'] ?? 24, 24, 0, 100 ) . 'px;--bigspy-row-gap-t:' . $num( $a['gapTablet'] ?? 20, 20, 0, 80 ) . 'px;--bigspy-row-gap-m:' . $num( $a['gapMobile'] ?? 16, 16, 0, 60 ) . 'px;';
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => $classes, 'style' => $style ) ); ?>><?php echo $content; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?></div>