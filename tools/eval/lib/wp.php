<?php
/**
 * G-70 eval: isolated WordPress subset (no WordPress install).
 *
 * - Minimal stubs for the WordPress functions the scored theme code calls.
 * - parse_blocks()/serialize_block() compatible with the block comment format.
 * - bst_eval_load_functions(): copies named functions out of a theme file with
 *   the PHP tokenizer, so the scorer runs the real server rules
 *   (bst_aid_editable_percent, bst_aid_block_rule_error...) without loading the
 *   whole AI Designer.
 *
 * Limits: no database, options, hooks, kses or i18n. Functions not loaded here
 * return '' when called through the render stubs (see render.php).
 */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
define( 'BST_EVAL_THEME', dirname( __DIR__, 3 ) . '/bigspy-ai-theme-wp' );
if ( ! defined( 'KB_IN_BYTES' ) ) {
	define( 'KB_IN_BYTES', 1024 );
}

$GLOBALS['bst_eval_stub_calls'] = array();

function bst_eval_define( $name, $code ) {
	if ( ! function_exists( $name ) ) {
		eval( $code ); // phpcs:ignore -- isolated test harness.
	}
}

foreach ( array(
	'__'                  => 'function __( $s, $d = null ) { return $s; }',
	'_x'                  => 'function _x( $s, $c = null, $d = null ) { return $s; }',
	'_n'                  => 'function _n( $a, $b, $n, $d = null ) { return 1 == $n ? $a : $b; }',
	'esc_html__'          => 'function esc_html__( $s, $d = null ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }',
	'esc_attr__'          => 'function esc_attr__( $s, $d = null ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }',
	'esc_html_e'          => 'function esc_html_e( $s, $d = null ) { echo htmlspecialchars( (string) $s, ENT_QUOTES ); }',
	'esc_attr_e'          => 'function esc_attr_e( $s, $d = null ) { echo htmlspecialchars( (string) $s, ENT_QUOTES ); }',
	'_e'                  => 'function _e( $s, $d = null ) { echo $s; }',
	'esc_attr'            => 'function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }',
	'esc_html'            => 'function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }',
	'esc_textarea'        => 'function esc_textarea( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }',
	'esc_url'             => 'function esc_url( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }',
	'esc_url_raw'         => 'function esc_url_raw( $s ) { return (string) $s; }',
	'esc_js'              => 'function esc_js( $s ) { return addslashes( (string) $s ); }',
	'wp_kses_post'        => 'function wp_kses_post( $s ) { return (string) $s; }',
	'wp_kses'             => 'function wp_kses( $s, $a = array() ) { return (string) $s; }',
	'sanitize_key'        => 'function sanitize_key( $s ) { return preg_replace( "/[^a-z0-9_\\-]/", "", strtolower( (string) $s ) ); }',
	'sanitize_text_field' => 'function sanitize_text_field( $s ) { return trim( strip_tags( (string) $s ) ); }',
	'sanitize_title'      => 'function sanitize_title( $s ) { return trim( preg_replace( "/[^a-z0-9]+/", "-", strtolower( (string) $s ) ), "-" ); }',
	'sanitize_html_class' => 'function sanitize_html_class( $s ) { return preg_replace( "/[^A-Za-z0-9_-]/", "", (string) $s ); }',
	'wp_strip_all_tags'   => 'function wp_strip_all_tags( $s ) { return trim( strip_tags( (string) $s ) ); }',
	'wp_json_encode'      => 'function wp_json_encode( $v, $f = 0 ) { return json_encode( $v, $f | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); }',
	'wp_unslash'          => 'function wp_unslash( $v ) { return $v; }',
	'wp_slash'            => 'function wp_slash( $v ) { return $v; }',
	'absint'              => 'function absint( $v ) { return abs( (int) $v ); }',
	'wp_parse_args'       => 'function wp_parse_args( $a, $d = array() ) { return array_merge( (array) $d, (array) $a ); }',
	'wp_trim_words'       => 'function wp_trim_words( $s, $n = 55, $more = "" ) { return implode( " ", array_slice( preg_split( "/\\s+/", trim( (string) $s ) ), 0, $n ) ) . $more; }',
	'is_admin'            => 'function is_admin() { return false; }',
	'is_feed'             => 'function is_feed() { return false; }',
	'wp_is_serving_rest_request' => 'function wp_is_serving_rest_request() { return false; }',
	'add_action'          => 'function add_action( ...$a ) { return true; }',
	'add_filter'          => 'function add_filter( ...$a ) { return true; }',
	'apply_filters'       => 'function apply_filters( $h, $v, ...$a ) { return $v; }',
	'do_action'           => 'function do_action( ...$a ) {}',
	'get_theme_mod'       => 'function get_theme_mod( $k, $d = false ) { return $d; }',
	'get_option'          => 'function get_option( $k, $d = false ) { return $d; }',
	'home_url'            => 'function home_url( $p = "" ) { return "https://example.test" . $p; }',
	'get_template_directory_uri' => 'function get_template_directory_uri() { return "file://" . BST_EVAL_THEME; }',
	'get_template_directory'     => 'function get_template_directory() { return BST_EVAL_THEME; }',
	'wp_unique_id'        => 'function wp_unique_id( $p = "" ) { static $i = 0; return $p . ( ++$i ); }',
	'current_user_can'    => 'function current_user_can( ...$a ) { return false; }',
	'is_user_logged_in'   => 'function is_user_logged_in() { return false; }',
	'wp_enqueue_script'   => 'function wp_enqueue_script( ...$a ) {}',
	'wp_enqueue_style'    => 'function wp_enqueue_style( ...$a ) {}',
	'is_wp_error'         => 'function is_wp_error( $v ) { return false; }',
	'number_format_i18n'  => 'function number_format_i18n( $n, $d = 0 ) { return number_format( (float) $n, $d, ",", "." ); }',
	'wp_get_attachment_image_url' => 'function wp_get_attachment_image_url( ...$a ) { return ""; }',
	'wp_get_attachment_image'     => 'function wp_get_attachment_image( ...$a ) { return ""; }',
	'get_post_field'      => 'function get_post_field( $f, $id = 0 ) { return "post_author" === $f ? 1 : ""; }',
	'user_can'            => 'function user_can( $u, $cap ) { return true; }', // AI Designer pages are written by an administrator.
	'get_the_ID'          => 'function get_the_ID() { return 1; }',
	'wpautop'             => 'function wpautop( $s ) { return (string) $s; }',
	'do_shortcode'        => 'function do_shortcode( $s ) { return (string) $s; }',
) as $name => $code ) {
	bst_eval_define( $name, $code );
}

/* ------------------------------------------------------------- Block parser */

/**
 * parse_blocks() for the block comment grammar (same token regex as WordPress'
 * WP_Block_Parser). Returns the same array shape.
 */
function bst_eval_parse_blocks( $doc ) {
	$re  = '/<!--\s+(?P<closer>\/)?wp:(?P<namespace>[a-z][a-z0-9_-]*\/)?(?P<name>[a-z][a-z0-9_-]*)\s+(?P<attrs>{(?:(?:[^}]+|}+(?=})|(?!}\s+\/?-->).)*+)?}\s+)?(?P<void>\/)?-->/s';
	$out = array();
	$stack = array();
	$pos = 0;
	$len = strlen( $doc );
	$new = function ( $name, $attrs ) {
		return array( 'blockName' => $name, 'attrs' => $attrs, 'innerBlocks' => array(), 'innerHTML' => '', 'innerContent' => array() );
	};
	$add_html = function ( $html ) use ( &$stack, &$out, $new ) {
		if ( '' === $html ) {
			return;
		}
		if ( $stack ) {
			$top = &$stack[ count( $stack ) - 1 ];
			$top['innerHTML']     .= $html;
			$top['innerContent'][] = $html;
		} else {
			$b                 = $new( null, array() );
			$b['innerHTML']    = $html;
			$b['innerContent'] = array( $html );
			$out[]             = $b;
		}
	};
	$push_child = function ( $block ) use ( &$stack, &$out ) {
		if ( $stack ) {
			$top = &$stack[ count( $stack ) - 1 ];
			$top['innerBlocks'][]  = $block;
			$top['innerContent'][] = null;
		} else {
			$out[] = $block;
		}
	};
	while ( $pos < $len && preg_match( $re, $doc, $m, PREG_OFFSET_CAPTURE, $pos ) ) {
		$start = $m[0][1];
		$add_html( substr( $doc, $pos, $start - $pos ) );
		$pos   = $start + strlen( $m[0][0] );
		$name  = ( '' !== ( $m['namespace'][0] ?? '' ) && -1 !== $m['namespace'][1] ? $m['namespace'][0] : 'core/' ) . $m['name'][0];
		$attrs = ( isset( $m['attrs'] ) && -1 !== $m['attrs'][1] && '' !== $m['attrs'][0] ) ? json_decode( $m['attrs'][0], true ) : array();
		$attrs = is_array( $attrs ) ? $attrs : array();
		$void  = isset( $m['void'] ) && -1 !== $m['void'][1] && '' !== $m['void'][0];
		$close = '' !== $m['closer'][0] && -1 !== $m['closer'][1];
		if ( $close ) {
			if ( ! $stack ) {
				continue;
			}
			$done = array_pop( $stack );
			$push_child( $done );
		} elseif ( $void ) {
			$push_child( $new( $name, $attrs ) );
		} else {
			$stack[] = $new( $name, $attrs );
		}
	}
	$add_html( substr( $doc, $pos ) );
	while ( $stack ) {
		$push_child( array_pop( $stack ) );
	}
	return $out;
}
bst_eval_define( 'parse_blocks', 'function parse_blocks( $c ) { return bst_eval_parse_blocks( (string) $c ); }' );

/** serialize_block() for model trees and parsed blocks. */
function bst_eval_serialize_block( $b ) {
	$name  = (string) $b['blockName'];
	if ( '' === $name ) {
		return (string) ( $b['innerHTML'] ?? '' );
	}
	$short = 0 === strpos( $name, 'core/' ) ? substr( $name, 5 ) : $name;
	$attrs = empty( $b['attrs'] ) ? '' : wp_json_encode( $b['attrs'] ) . ' ';
	$inner = '';
	$i     = 0;
	$content = $b['innerContent'] ?? array();
	if ( ! $content && ! empty( $b['innerBlocks'] ) ) {
		$content = array_fill( 0, count( $b['innerBlocks'] ), null );
	}
	foreach ( $content as $chunk ) {
		$inner .= is_string( $chunk ) ? $chunk : bst_eval_serialize_block( $b['innerBlocks'][ $i++ ] );
	}
	if ( '' === $inner ) {
		return "<!-- wp:$short $attrs/-->";
	}
	return "<!-- wp:$short $attrs-->" . $inner . "<!-- /wp:$short -->";
}

/**
 * Model tree (as sent to create_page: {name, attrs, inner} or {raw}) to block markup.
 * Fixtures use it; live runs store the saved post_content instead.
 */
function bst_eval_tree_to_markup( $nodes ) {
	$out = '';
	foreach ( (array) $nodes as $n ) {
		if ( isset( $n['raw'] ) ) {
			$out .= (string) $n['raw'];
			continue;
		}
		$b = array(
			'blockName'    => (string) ( $n['name'] ?? '' ),
			'attrs'        => (array) ( $n['attrs'] ?? array() ),
			'innerBlocks'  => array(),
			'innerContent' => array(),
		);
		if ( isset( $n['html'] ) ) {
			$b['innerContent'][] = (string) $n['html'];
		}
		foreach ( (array) ( $n['inner'] ?? array() ) as $c ) {
			$b['innerBlocks'][]  = bst_eval_parse_blocks( bst_eval_tree_to_markup( array( $c ) ) )[0];
			$b['innerContent'][] = null;
		}
		$out .= bst_eval_serialize_block( $b ) . "\n";
	}
	return $out;
}

/* ------------------------------------------------- Real theme functions */

/**
 * Copy top-level functions out of a theme file and define them here.
 *
 * @param string $file  Path relative to the theme root.
 * @param array  $names Function names to load.
 * @return array Names that were not found.
 */
function bst_eval_load_functions( $file, $names ) {
	$src    = file_get_contents( BST_EVAL_THEME . '/' . $file );
	$tokens = token_get_all( $src );
	$want   = array_flip( $names );
	$found  = array();
	$n      = count( $tokens );
	$depth  = 0;
	for ( $i = 0; $i < $n; $i++ ) {
		$t = $tokens[ $i ];
		if ( is_string( $t ) ) {
			$depth += '{' === $t ? 1 : ( '}' === $t ? -1 : 0 );
			continue;
		}
		if ( in_array( $t[0], array( T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES ), true ) ) {
			$depth++;
			continue;
		}
		if ( T_FUNCTION !== $t[0] || 0 !== $depth ) {
			continue;
		}
		$j = $i + 1;
		while ( $j < $n && is_array( $tokens[ $j ] ) && T_WHITESPACE === $tokens[ $j ][0] ) {
			$j++;
		}
		if ( ! is_array( $tokens[ $j ] ) || T_STRING !== $tokens[ $j ][0] || ! isset( $want[ $tokens[ $j ][1] ] ) ) {
			continue;
		}
		$name = $tokens[ $j ][1];
		$code = '';
		$d    = 0;
		$open = false;
		for ( $k = $i; $k < $n; $k++ ) {
			$tk    = $tokens[ $k ];
			$code .= is_string( $tk ) ? $tk : $tk[1];
			if ( '{' === $tk || ( is_array( $tk ) && in_array( $tk[0], array( T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES ), true ) ) ) {
				$d++;
				$open = true;
			} elseif ( '}' === $tk ) {
				$d--;
				if ( $open && 0 === $d ) {
					break;
				}
			}
		}
		$i = $k;
		if ( ! function_exists( $name ) ) {
			eval( $code ); // phpcs:ignore -- copies the real server rule.
			$GLOBALS['bst_eval_sources'][ strtolower( $name ) ] = $code;
		}
		$found[] = $name;
	}
	return array_values( array_diff( $names, $found ) );
}

/** Theme form rules: inc/form-schema.php and inc/form-pricing.php, loaded unchanged. */
function bst_eval_load_forms() {
	require_once BST_EVAL_THEME . '/inc/form-schema.php';
	require_once BST_EVAL_THEME . '/inc/form-pricing.php';
	bst_eval_load_functions( 'inc/form-native.php', array( 'bst_native_field_types', 'bst_native_field_options' ) );
}
