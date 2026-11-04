<?php
/**
 * G-70 eval: render a block tree to HTML through the real blocks/<slug>/render.php
 * templates, without WordPress. Theme helpers they call are copied from inc/
 * (bst_eval_load_functions); any other function becomes a stub returning ''.
 *
 * Limits: no dynamic CSS from the Customizer, no render_block filters
 * (block design width/spacing), no kses, no database (posts/products/menus
 * blocks render empty), icons render empty. Good enough to spot fixed widths,
 * wide tables/code and overflowing layouts at 375px; not a pixel-exact page.
 */

require_once __DIR__ . '/wp.php';

/** Index of theme functions: name => file (relative to the theme). */
function bst_eval_function_index() {
	static $index = null;
	if ( null !== $index ) {
		return $index;
	}
	$index = array();
	$it    = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( BST_EVAL_THEME . '/inc', FilesystemIterator::SKIP_DOTS ) );
	foreach ( $it as $f ) {
		if ( 'php' !== $f->getExtension() ) {
			continue;
		}
		$rel = substr( $f->getPathname(), strlen( BST_EVAL_THEME ) + 1 );
		if ( preg_match_all( '/^function\s+(bst_[a-z0-9_]+)\s*\(/mi', file_get_contents( $f->getPathname() ), $m ) ) {
			foreach ( $m[1] as $name ) {
				$index[ $name ] = $index[ $name ] ?? $rel;
			}
		}
	}
	return $index;
}

/** Function names called in a piece of PHP source. */
function bst_eval_called( $src ) {
	$tokens = token_get_all( $src );
	$names  = array();
	$prev   = null;
	$n      = count( $tokens );
	for ( $i = 0; $i < $n; $i++ ) {
		$t = $tokens[ $i ];
		if ( is_array( $t ) && in_array( $t[0], array( T_WHITESPACE, T_COMMENT, T_DOC_COMMENT ), true ) ) {
			continue;
		}
		if ( is_array( $t ) && T_STRING === $t[0] ) {
			$j = $i + 1;
			while ( $j < $n && is_array( $tokens[ $j ] ) && T_WHITESPACE === $tokens[ $j ][0] ) {
				$j++;
			}
			$before = is_array( $prev ) ? $prev[0] : $prev;
			if ( '(' === ( $tokens[ $j ] ?? null ) && ! in_array( $before, array( T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR, T_DOUBLE_COLON, T_FUNCTION, T_NEW, T_CONST ), true ) ) {
				$names[ strtolower( $t[1] ) ] = $t[1];
			}
		}
		$prev = $t;
	}
	return $names;
}

/** Load theme helpers used by the render templates; stub everything else. */
function bst_eval_render_setup() {
	static $done = false;
	if ( $done ) {
		return;
	}
	$done = true;
	bst_eval_load_forms();
	$index   = bst_eval_function_index();
	$sources = array();
	foreach ( glob( BST_EVAL_THEME . '/blocks/*/render.php' ) as $f ) {
		$sources[] = file_get_contents( $f );
	}
	$seen = array();
	for ( $round = 0; $round < 6 && $sources; $round++ ) {
		$wanted = array();
		foreach ( $sources as $src ) {
			$wanted += bst_eval_called( $src );
		}
		$sources = array();
		$byfile  = array();
		foreach ( $wanted as $lower => $name ) {
			if ( isset( $seen[ $lower ] ) || function_exists( $name ) || ! isset( $index[ $name ] ) ) {
				continue;
			}
			$seen[ $lower ]               = true;
			$byfile[ $index[ $name ] ][] = $name;
		}
		foreach ( $byfile as $file => $names ) {
			bst_eval_load_functions( $file, $names );
			foreach ( $names as $name ) {
				if ( function_exists( $name ) ) {
					$sources[] = '<?php ' . bst_eval_function_source( $name );
				}
			}
		}
	}
	// Remaining unknown functions anywhere in the loaded code and templates.
	$all = array();
	foreach ( glob( BST_EVAL_THEME . '/blocks/*/render.php' ) as $f ) {
		$all += bst_eval_called( file_get_contents( $f ) );
	}
	foreach ( array_keys( $seen ) as $lower ) {
		if ( function_exists( $lower ) ) {
			$all += bst_eval_called( '<?php ' . bst_eval_function_source( $lower ) );
		}
	}
	foreach ( $all as $name ) {
		if ( ! function_exists( $name ) && preg_match( '/^[a-z_][a-z0-9_]*$/i', $name ) && ! in_array( strtolower( $name ), array( 'isset', 'empty', 'array', 'list', 'unset', 'echo', 'print', 'exit', 'die', 'eval', 'include', 'require', 'fn', 'match', 'static', 'self', 'parent' ), true ) ) {
			eval( 'function ' . $name . '( ...$a ) { $GLOBALS["bst_eval_stub_calls"]["' . $name . '"] = true; return ""; }' ); // phpcs:ignore
		}
	}
}

function bst_eval_function_source( $name ) {
	if ( isset( $GLOBALS['bst_eval_sources'][ strtolower( $name ) ] ) ) {
		return $GLOBALS['bst_eval_sources'][ strtolower( $name ) ];
	}
	$r = new ReflectionFunction( $name );
	if ( ! $r->getFileName() || false !== strpos( $r->getFileName(), 'eval()' ) ) {
		return '';
	}
	$lines = file( $r->getFileName() );
	return implode( '', array_slice( $lines, $r->getStartLine() - 1, $r->getEndLine() - $r->getStartLine() + 1 ) );
}

/** WP_Block stand-in for templates that read $block. */
if ( ! class_exists( 'WP_Block' ) ) {
	class WP_Block { // phpcs:ignore
		public $name;
		public $attributes;
		public $parsed_block;
		public $inner_blocks = array();
		public $context      = array();
		public function __construct( $parsed, $attrs ) {
			$this->name         = $parsed['blockName'];
			$this->attributes   = $attrs;
			$this->parsed_block = $parsed;
			foreach ( $parsed['innerBlocks'] as $c ) {
				$this->inner_blocks[] = (object) array( 'name' => $c['blockName'], 'attributes' => $c['attrs'], 'parsed_block' => $c );
			}
		}
	}
}

$GLOBALS['bst_eval_current'] = array();
bst_eval_define(
	'get_block_wrapper_attributes',
	'function get_block_wrapper_attributes( $extra = array() ) {
		$a     = $GLOBALS["bst_eval_current"];
		$align = ! empty( $a["attrs"]["align"] ) ? " align" . $a["attrs"]["align"] : "";
		$class = trim( "wp-block-" . str_replace( "/", "-", (string) $a["name"] ) . " " . ( $extra["class"] ?? "" ) . $align . " " . ( $a["attrs"]["className"] ?? "" ) );
		$out   = "class=\"" . esc_attr( $class ) . "\"";
		if ( ! empty( $extra["style"] ) ) { $out .= " style=\"" . esc_attr( $extra["style"] ) . "\""; }
		if ( ! empty( $a["attrs"]["anchor"] ) ) { $out .= " id=\"" . esc_attr( $a["attrs"]["anchor"] ) . "\""; }
		foreach ( $extra as $k => $v ) { if ( ! in_array( $k, array( "class", "style" ), true ) ) { $out .= " " . $k . "=\"" . esc_attr( $v ) . "\""; } }
		return $out;
	}'
);

/** Defaults from block.json. */
function bst_eval_block_meta( $name ) {
	static $cache = array();
	if ( ! isset( $cache[ $name ] ) ) {
		$slug           = 0 === strpos( $name, 'bst/' ) ? substr( $name, 4 ) : '';
		$file           = BST_EVAL_THEME . "/blocks/$slug/block.json";
		$cache[ $name ] = ( $slug && is_file( $file ) ) ? json_decode( file_get_contents( $file ), true ) : null;
	}
	return $cache[ $name ];
}

/**
 * Render parsed blocks. Returns [html, stats] where stats counts rendered and
 * failed blocks.
 */
function bst_eval_render_blocks( $blocks, &$stats ) {
	bst_eval_render_setup();
	$html = '';
	foreach ( $blocks as $b ) {
		$html .= bst_eval_render_block( $b, $stats );
	}
	return $html;
}

function bst_eval_render_block( $b, &$stats ) {
	$name = $b['blockName'];
	if ( ! $name ) {
		return (string) $b['innerHTML'];
	}
	$content = '';
	$i       = 0;
	foreach ( $b['innerContent'] as $chunk ) {
		$content .= is_string( $chunk ) ? $chunk : bst_eval_render_block( $b['innerBlocks'][ $i++ ], $stats );
	}
	$meta = bst_eval_block_meta( $name );
	$slug = substr( $name, 4 );
	$file = BST_EVAL_THEME . "/blocks/$slug/render.php";
	if ( ! $meta || ! is_file( $file ) ) {
		$stats['static'] = ( $stats['static'] ?? 0 ) + 1;
		return $content; // Core blocks and static blocks keep their saved HTML.
	}
	$attributes = (array) $b['attrs'];
	foreach ( $meta['attributes'] ?? array() as $k => $def ) {
		if ( ! array_key_exists( $k, $attributes ) && array_key_exists( 'default', $def ) ) {
			$attributes[ $k ] = $def['default'];
		}
	}
	$block = new WP_Block( $b, $attributes );
	$prev  = $GLOBALS['bst_eval_current'];
	$GLOBALS['bst_eval_current'] = array( 'name' => $name, 'attrs' => $attributes );
	$level = ob_get_level();
	ob_start();
	try {
		include $file;
		$out = ob_get_clean();
		$stats['rendered'] = ( $stats['rendered'] ?? 0 ) + 1;
	} catch ( Throwable $e ) {
		while ( ob_get_level() > $level ) {
			ob_end_clean();
		}
		$stats['failed'][] = $name . ': ' . $e->getMessage();
		$out = '<div data-eval-unrendered="' . esc_attr( $name ) . '">' . $content . '</div>';
	}
	$GLOBALS['bst_eval_current'] = $prev;
	return $out;
}

/** Full HTML document with the theme front CSS, for the 375px check. */
function bst_eval_page_html( $body, $title = 'eval' ) {
	$css  = array( 'assets/css/main.css', 'assets/css/blocks.css' );
	$html = '<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>' . esc_html( $title ) . '</title>';
	foreach ( $css as $c ) {
		if ( is_file( BST_EVAL_THEME . '/' . $c ) ) {
			$html .= '<link rel="stylesheet" href="file://' . BST_EVAL_THEME . '/' . $c . '">';
		}
	}
	$html .= '<style>:root{--bs-primary:#2563eb;--bs-secondary:#0f172a;--bs-heading:#111827;--bs-text:#1f2937;--bs-border:#e5e7eb;--bs-light:#f3f4f6;--bs-body-bg:#fff}body{margin:0}</style></head><body><main class="site-main"><div class="entry-content">';
	return $html . $body . '</div></main></body></html>';
}
