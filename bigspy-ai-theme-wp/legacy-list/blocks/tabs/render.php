<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$a = is_array( $attributes ?? null ) ? $attributes : array();
$allowed_style = array( 'outline', 'pills', 'underline', 'minimal' );
$allowed_size = array( 'xsmall', 'small', 'medium', 'large', 'xlarge' );
$style_name = in_array( $a['style'] ?? 'outline', $allowed_style, true ) ? $a['style'] : 'outline';
$orientation = ( $a['orientation'] ?? 'horizontal' ) === 'vertical' ? 'vertical' : 'horizontal';
$size = in_array( $a['size'] ?? 'medium', $allowed_size, true ) ? $a['size'] : 'medium';
$align = in_array( $a['align'] ?? 'left', array( 'left', 'center', 'right' ), true ) ? $a['align'] : 'left';
$activation = ( $a['activation'] ?? 'click' ) === 'hover' ? 'hover' : 'click';
$classes = 'bigspy-tabs bigspy-tabs--' . $style_name . ' bigspy-tabs--' . $orientation . ' bigspy-tabs--size-' . $size . ' bigspy-tabs--align-' . $align;
if ( ( $a['navCase'] ?? 'normal' ) === 'uppercase' ) $classes .= ' bigspy-tabs--uppercase';
$num = static function( $value, $default, $min, $max ) { return max( $min, min( $max, is_numeric( $value ) ? (int) $value : $default ) ); };
$inline_style = '--bigspy-tabs-gap:' . $num( $a['navGap'] ?? 8, 8, 0, 40 ) . 'px;--bigspy-tabs-panel-padding:' . $num( $a['panelPadding'] ?? 24, 24, 0, 80 ) . 'px;';
preg_match_all( '/<section class="bigspy-tab-panel" data-bigspy-tab-title="([^"]*)">/', $content, $matches );
$titles = isset( $matches[1] ) ? array_map( 'html_entity_decode', $matches[1] ) : array();
if ( empty( $titles ) ) return;
static $instance = 0;
$instance++;
$base = 'bigspy-tabs-' . $instance;
$active = min( count( $titles ) - 1, $num( $a['activeTab'] ?? 0, 0, 0, 99 ) );
$panel_index = 0;
$panels = preg_replace_callback( '/<section class="bigspy-tab-panel" data-bigspy-tab-title="[^"]*">/', static function() use ( &$panel_index, $base, $active ) { $index = $panel_index++; return '<section class="bigspy-tab-panel" id="' . esc_attr( $base . '-panel-' . $index ) . '" role="tabpanel" aria-labelledby="' . esc_attr( $base . '-tab-' . $index ) . '"' . ( $index === $active ? '' : ' hidden' ) . '>'; }, $content );
echo '<div ' . get_block_wrapper_attributes( array( 'class'=>$classes, 'style'=>$inline_style, 'data-bigspy-tabs'=>'', 'data-activation'=>$activation ) ) . '>';
echo '<div class="bigspy-tabs__nav" role="tablist" aria-orientation="'.esc_attr($orientation).'">';
foreach ( $titles as $index => $title ) { $selected = $index === $active; echo '<button type="button" class="bigspy-tabs__tab'.($selected?' is-active':'').'" id="'.esc_attr($base.'-tab-'.$index).'" role="tab" aria-selected="'.($selected?'true':'false').'" aria-controls="'.esc_attr($base.'-panel-'.$index).'" tabindex="'.($selected?'0':'-1').'">'.esc_html($title ?: sprintf(__('Tab %d','bigspy-ai-theme-wp'),$index+1)).'</button>'; }
echo '</div><div class="bigspy-tabs__panels">'.$panels.'</div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped