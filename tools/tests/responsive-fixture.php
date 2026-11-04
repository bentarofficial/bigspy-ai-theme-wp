<?php
/**
 * G-67 fixture: renders section/row/column, tabs and banner through the real
 * render.php templates with minimal WordPress stubs (no WordPress install).
 * Usage: php tools/tests/responsive-fixture.php <out.html> <banner-image-url>
 * Limits: wrapper attributes, kses and icons are stubbed; no dynamic CSS, no
 * editor. Used by tools/tests/responsive-a11y-check.cjs.
 */

define( 'ABSPATH', __DIR__ . '/' );
$theme = dirname( __DIR__, 2 ) . '/bigspy-ai-theme-wp';
$out   = $argv[1] ?? 'fixture.html';
$img   = $argv[2] ?? '';

function __( $s ) { return $s; }
function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function esc_url( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function esc_url_raw( $s ) { return (string) $s; }
function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function wp_kses_post( $s ) { return (string) $s; }
function sanitize_key( $s ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $s ) ); }
function wp_strip_all_tags( $s ) { return trim( strip_tags( (string) $s ) ); }
function wp_trim_words( $s, $n, $more = '' ) { return implode( ' ', array_slice( preg_split( '/\s+/', trim( $s ) ), 0, $n ) ) . $more; }
function is_admin() { return false; }
function wp_is_serving_rest_request() { return false; }
function is_feed() { return false; }
function bst_icon( $name, $size = 20 ) { return ''; }
function bst_clamp( $value, $min, $max, $default ) { return is_numeric( $value ) ? max( $min, min( $max, $value + 0 ) ) : $default; }
function bst_choice( $value, $allowed, $default ) { return in_array( $value, $allowed, true ) ? $value : $default; }
function bst_block_num( $attrs, $key, $default, $min, $max ) { return bst_clamp( $attrs[ $key ] ?? $default, $min, $max, $default ); }
function bst_url_img_loading_attrs( $priority = false ) { return ' decoding="async"'; }
function bst_section_backgrounds() { return array(); }
function bst_section_dark_backgrounds() { return array(); }
function bst_block_color( $attrs, $key ) {
	$v = isset( $attrs[ $key ] ) ? (string) $attrs[ $key ] : '';
	return preg_match( '/^#[0-9a-fA-F]{3,8}$/', $v ) ? $v : '';
}
function bst_style_vars( $vars ) {
	$o = array();
	foreach ( $vars as $k => $v ) {
		if ( '' !== $v && null !== $v && false !== $v ) {
			$o[] = $k . ':' . $v;
		}
	}
	return implode( ';', $o );
}
$GLOBALS['bst_current'] = array();
function get_block_wrapper_attributes( $extra = array() ) {
	$a     = $GLOBALS['bst_current'];
	$class = trim( 'wp-block-' . str_replace( '/', '-', $a['name'] ) . ' ' . ( $extra['class'] ?? '' ) . ( ! empty( $a['attrs']['align'] ) && 'bst/section' === $a['name'] ? ' align' . $a['attrs']['align'] : '' ) );
	$out   = 'class="' . esc_attr( $class ) . '"';
	if ( ! empty( $extra['style'] ) ) {
		$out .= ' style="' . esc_attr( $extra['style'] ) . '"';
	}
	if ( ! empty( $a['attrs']['anchor'] ) ) {
		$out .= ' id="' . esc_attr( $a['attrs']['anchor'] ) . '"';
	}
	return $out;
}

/** Render [name, attrs, children] where children are nodes or raw HTML strings. */
function bst_fx( $node ) {
	global $theme;
	if ( is_string( $node ) ) {
		return $node;
	}
	list( $name, $attrs, $children ) = $node + array( '', array(), array() );
	$content = implode( '', array_map( 'bst_fx', $children ) );
	$slug    = substr( $name, 4 );
	$meta    = json_decode( file_get_contents( "$theme/blocks/$slug/block.json" ), true );
	foreach ( $meta['attributes'] ?? array() as $k => $def ) {
		if ( ! array_key_exists( $k, $attrs ) && array_key_exists( 'default', $def ) ) {
			$attrs[ $k ] = $def['default'];
		}
	}
	$inner = array();
	foreach ( $children as $c ) {
		if ( is_array( $c ) ) {
			$inner[] = array( 'blockName' => $c[0], 'attrs' => $c[1] ?? array() );
		}
	}
	$block      = (object) array( 'parsed_block' => array( 'innerBlocks' => $inner ) );
	$attributes = $attrs;
	$prev       = $GLOBALS['bst_current'];
	$GLOBALS['bst_current'] = array( 'name' => $name, 'attrs' => $attrs );
	ob_start();
	include "$theme/blocks/$slug/render.php";
	$GLOBALS['bst_current'] = $prev;
	return ob_get_clean();
}

function bst_fx_tabs( $id, $style = 'underline', $extra = array() ) {
	$panel = function ( $n ) use ( $id ) {
		return "<h3>Panel $n</h3><p>Nội dung $id/$n với <a href=\"#$id-link-$n\">liên kết $n</a>.</p>";
	};
	return array( 'bst/tabs', array( 'anchor' => $id, 'style' => $style ) + $extra, array(
		array( 'bst/tab', array( 'title' => 'Thông số' ), array( $panel( 1 ) ) ),
		array( 'bst/tab', array( 'title' => 'Dịch vụ' ), array( $panel( 2 ) ) ),
		array( 'bst/tab', array( 'title' => 'Bảng giá' ), array( $panel( 3 ) ) ),
	) );
}

$col = function ( $label, $span, $tablet = 0 ) {
	return array( 'bst/column', array( 'spanDesktop' => $span, 'spanTablet' => $tablet ), array( "<h2 id=\"h-$label\">Cột $label</h2><p>Mô tả cột $label đủ dài để kiểm tra xuống dòng ở màn hẹp.</p><p><a class=\"fx-link\" href=\"#$label\">Liên kết $label</a></p>" ) );
};

$fixture = array(
	array( 'bst/section', array( 'anchor' => 'fx-section' ), array(
		array( 'bst/row', array( 'anchor' => 'fx-row' ), array( $col( 'A', 6 ), $col( 'B', 6 ) ) ),
	) ),
	array( 'bst/section', array( 'anchor' => 'fx-section-rev' ), array(
		array( 'bst/row', array( 'anchor' => 'fx-row-rev', 'reverseMobile' => true ), array( $col( 'Chữ', 7 ), $col( 'Ảnh', 5 ) ) ),
	) ),
	array( 'bst/section', array( 'anchor' => 'fx-section-tabs' ), array(
		bst_fx_tabs( 'fx-tabs-1' ),
		bst_fx_tabs( 'fx-tabs-2', 'vertical' ),
		bst_fx_tabs( 'fx-tabs-3', 'pills', array( 'event' => 'hover' ) ),
	) ),
	array( 'bst/banner', array( 'anchor' => 'fx-banner', 'imageUrl' => $img, 'link' => '#banner' ), array(
		'<h2>Ưu đãi mùa đông cho toàn bộ dịch vụ thiết kế website trọn gói</h2><p>Đăng ký trong tuần này để nhận tư vấn miễn phí, bản phác thảo giao diện và hỗ trợ cài đặt nội dung mẫu cho cửa hàng.</p><div class="wp-block-buttons"><div class="wp-block-button"><a class="wp-block-button__link" href="#cta">Nhận tư vấn</a></div></div>',
	) ),
	// Hand-written copy of bst_slider_markup() output (inc/blocks.php needs WordPress).
	'<div class="bs-slider" id="fx-slider" data-bs-slider data-autoplay="700" data-loop="1" style="--bs-slides-d:1;--bs-slides-t:1;--bs-slides-m:1;--bs-slide-gap:20px"><div class="bs-slider__track" data-bs-slider-track><div><p>Slide 1</p></div><div><p>Slide 2</p></div><div><p>Slide 3</p></div></div><div class="bs-slider__dots" data-bs-slider-dots></div></div>',
);

$css  = array( 'assets/css/blocks.css', 'assets/css/main.css' );
$html = '<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
foreach ( $css as $c ) {
	$html .= '<link rel="stylesheet" href="file://' . $theme . '/' . $c . '">';
}
$html .= '<style>:root{--bs-primary:#2563eb;--bs-text:#1f2937;--bs-border:#e5e7eb;--bs-light:#f3f4f6;--bs-body-bg:#fff}</style></head><body><main>';
$html .= implode( "\n", array_map( 'bst_fx', $fixture ) );
$html .= '</main><script src="file://' . $theme . '/assets/js/blocks.js"></script></body></html>';
file_put_contents( $out, $html );
echo "wrote $out\n";
