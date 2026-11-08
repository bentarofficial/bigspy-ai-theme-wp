<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$a = is_array( $attributes ?? null ) ? $attributes : array();
$num = static function( $value, $default, $min, $max ) { return max( $min, min( $max, is_numeric( $value ) ? (int) $value : $default ) ); };
$align = in_array( $a['verticalAlign'] ?? 'start', array( 'start', 'center', 'end', 'between' ), true ) ? $a['verticalAlign'] : 'start';
$classes = 'bigspy-column bigspy-column--valign-' . $align;
foreach ( array( 'Desktop', 'Tablet', 'Mobile' ) as $device ) { if ( ! empty( $a['hide' . $device] ) ) $classes .= ' bigspy-layout-hide-' . strtolower( $device ); }
$style = '--bigspy-column-span-d:' . $num( $a['spanDesktop'] ?? 1, 1, 1, 6 ) . ';--bigspy-column-span-t:' . $num( $a['spanTablet'] ?? 1, 1, 1, 4 ) . ';--bigspy-column-span-m:' . $num( $a['spanMobile'] ?? 1, 1, 1, 2 ) . ';--bigspy-column-gap:' . $num( $a['contentGap'] ?? 16, 16, 0, 80 ) . 'px;';
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => $classes, 'style' => $style ) ); ?>><?php echo $content; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?></div>