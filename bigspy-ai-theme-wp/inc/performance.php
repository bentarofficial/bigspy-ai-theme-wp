<?php
/**
 * Speed: block CSS only where it is used, Google Fonts served from this site and
 * generated CSS delivered as cached files.
 *
 * Block CSS: assets/css/blocks.css stays the source (the editor loads all of it).
 * tools/split-blocks-css.py tags each rule with the class family it needs
 * (blocks/segments.json). For visitors, the finished page HTML is scanned and the
 * stylesheet link points to a cached file holding the shared rules plus the families on
 * the page, in the original order, so the cascade is exactly the same as blocks.css.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_defaults',
	function ( $defaults ) {
		return $defaults + array(
			'perf_split_css'   => true,
			'perf_local_fonts' => true,
			'perf_css_file'    => true,
		);
	}
);

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$s        = 'bst_site_extras';
		$fields[] = array( 'id' => 'perf_split_css', 'section' => $s, 'type' => 'checkbox', 'label' => __( 'Load block styles only on pages that use them', 'bigspy-ai-theme-wp' ), 'description' => __( 'Each page gets the shared styles plus the styles of the blocks it contains. The editor always loads everything.', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'perf_local_fonts', 'section' => $s, 'type' => 'checkbox', 'label' => __( 'Host Google Fonts on this site', 'bigspy-ai-theme-wp' ), 'description' => __( 'Fonts are downloaded once to the uploads folder, so visitors never connect to Google. Until the download finishes, fonts load from Google as before.', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'perf_css_file', 'section' => $s, 'type' => 'checkbox', 'label' => __( 'Serve generated CSS as a cached file', 'bigspy-ai-theme-wp' ), 'description' => __( 'Customizer colors, fonts and layout CSS go into a file the browser caches, instead of being repeated in every page.', 'bigspy-ai-theme-wp' ) );
		return $fields;
	},
	20
);

/** A normal visitor-facing page view (not admin, editor previews, feeds, REST or the Customizer). */
function bst_perf_front() {
	return ! is_admin() && ! wp_doing_ajax() && ! wp_is_json_request() && ! ( defined( 'REST_REQUEST' ) && REST_REQUEST )
		&& ! is_customize_preview() && ! is_feed() && ! is_embed() && ! is_robots() && ! is_trackback();
}

/** Writable folder in uploads for generated files: array( path, url ) or null. */
function bst_perf_dir( $sub ) {
	$up = wp_upload_dir( null, false );
	if ( ! empty( $up['error'] ) ) {
		return null;
	}
	$path = trailingslashit( $up['basedir'] ) . 'bigspy/' . $sub;
	if ( ! wp_mkdir_p( $path ) || ! wp_is_writable( $path ) ) {
		return null;
	}
	return array( $path, set_url_scheme( trailingslashit( $up['baseurl'] ) . 'bigspy/' . $sub ) );
}

/* ------------------------------------------------------------------ Block CSS per page */

/** Class families with their own CSS file: family => class prefixes. */
function bst_perf_families() {
	static $families = null;
	if ( null === $families ) {
		$file     = BST_DIR . '/assets/css/blocks/families.json';
		$families = file_exists( $file ) ? (array) json_decode( (string) file_get_contents( $file ), true ) : array(); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	}
	return $families;
}

function bst_perf_split_on() {
	$segments = BST_DIR . '/assets/css/blocks/segments.json';
	// A segments file older than blocks.css means the split was not rebuilt: use blocks.css as is.
	return bst_mod( 'perf_split_css' ) && bst_perf_front() && file_exists( $segments )
		&& filemtime( $segments ) >= filemtime( BST_DIR . '/assets/css/blocks.css' ) && bst_perf_families();
}

add_action(
	'template_redirect',
	function () {
		if ( bst_perf_split_on() ) {
			ob_start( 'bst_perf_block_css_buffer' );
		}
	},
	-1000
);

/** Output buffer callback: point the blocks.css link at the file for this page's families. */
function bst_perf_block_css_buffer( $html ) {
	if ( ! preg_match( '/<link[^>]+id=["\']bst-blocks-css["\'][^>]*>/', $html, $tag ) ) {
		return $html;
	}
	$tokens = array();
	if ( preg_match_all( '/\sclass\s*=\s*(["\'])(.*?)\1/s', $html, $m ) ) {
		foreach ( $m[2] as $value ) {
			foreach ( preg_split( '/\s+/', $value ) as $t ) {
				if ( 0 === strpos( $t, 'bs-' ) ) {
					$tokens[ $t ] = true;
				}
			}
		}
	}
	$need = array();
	foreach ( bst_perf_families() as $family => $prefixes ) {
		foreach ( array_keys( $tokens ) as $t ) {
			foreach ( (array) $prefixes as $p ) {
				if ( $t === $p || 0 === strpos( $t, $p . '-' ) || 0 === strpos( $t, $p . '_' ) ) {
					$need[] = $family;
					continue 3;
				}
			}
		}
	}
	$url = bst_perf_block_css_file( $need );
	if ( ! $url ) {
		return $html;
	}
	$new = preg_replace( '/href=(["\'])[^"\']*\1/', 'href="' . esc_url( $url ) . '"', $tag[0], 1 );
	return str_replace( $tag[0], $new, $html );
}

/** URL of the cached stylesheet with the shared rules plus these families, or '' on failure. */
function bst_perf_block_css_file( $families ) {
	$source = BST_DIR . '/assets/css/blocks/segments.json';
	$dir    = bst_perf_dir( 'css' );
	if ( ! $dir ) {
		return '';
	}
	sort( $families );
	$name = 'blocks-' . substr( md5( BST_VERSION . '|url2|' . filemtime( $source ) . implode( ',', $families ) ), 0, 12 ) . '.css';
	if ( ! file_exists( $dir[0] . '/' . $name ) ) {
		$segments = json_decode( (string) file_get_contents( $source ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( ! is_array( $segments ) ) {
			return '';
		}
		$keep = array_flip( array_merge( array( 'core' ), $families ) );
		$css  = '';
		foreach ( $segments as $segment ) {
			if ( isset( $keep[ $segment[0] ] ) ) {
				$css .= $segment[1] . "\n";
			}
		}
		// Relative url() values in blocks.css point into the theme; keep them working from uploads.
		// Possessive quote: a quoted data: URI must not be re-read as unquoted (inner url(%23id) stays too).
		$css = preg_replace( '#url\((["\']?+)(?![a-z]+:|/|\#|%23)#i', 'url($1' . BST_URI . '/assets/css/', $css );
		if ( false === file_put_contents( $dir[0] . '/' . $name, $css ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
			return '';
		}
	}
	return $dir[1] . '/' . $name;
}

/* ------------------------------------------------------------------ Google Fonts on this site */

/** Local stylesheet URL for a Google Fonts URL, or '' while it is not downloaded yet. */
function bst_perf_local_fonts_url( $url ) {
	$dir = bst_perf_dir( 'fonts' );
	if ( ! $dir ) {
		return '';
	}
	$key = md5( $url );
	if ( file_exists( $dir[0] . '/' . $key . '.css' ) ) {
		return $dir[1] . '/' . $key . '.css';
	}
	if ( ! get_transient( 'bst_fonts_wait_' . $key ) && ! wp_next_scheduled( 'bst_perf_fetch_fonts', array( $url ) ) ) {
		wp_schedule_single_event( time(), 'bst_perf_fetch_fonts', array( $url ) );
	}
	return '';
}

/** Download the Google stylesheet (woff2) and its font files, rewrite it to the local copies. */
function bst_perf_fetch_fonts( $url ) {
	$dir = bst_perf_dir( 'fonts' );
	if ( ! $dir || 0 !== strpos( $url, 'https://fonts.googleapis.com/' ) ) {
		return;
	}
	$key = md5( $url );
	set_transient( 'bst_fonts_wait_' . $key, 1, HOUR_IN_SECONDS ); // No retry storm when Google is unreachable.
	$ua  = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36';
	$res = wp_remote_get( $url, array( 'timeout' => 15, 'user-agent' => $ua ) );
	$css = 200 === (int) wp_remote_retrieve_response_code( $res ) ? (string) wp_remote_retrieve_body( $res ) : '';
	if ( '' === $css || ! preg_match_all( '#url\((https://fonts\.gstatic\.com/[^)\s]+)\)#', $css, $m ) ) {
		return;
	}
	foreach ( array_unique( $m[1] ) as $font ) {
		$ext  = preg_match( '/\.(woff2|woff|ttf)$/', $font, $e ) ? $e[1] : 'woff2';
		$name = md5( $font ) . '.' . $ext;
		if ( ! file_exists( $dir[0] . '/' . $name ) ) {
			$file = wp_remote_get( $font, array( 'timeout' => 15 ) );
			if ( 200 !== (int) wp_remote_retrieve_response_code( $file ) ) {
				return;
			}
			file_put_contents( $dir[0] . '/' . $name, wp_remote_retrieve_body( $file ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		}
		$css = str_replace( $font, $name, $css );
	}
	file_put_contents( $dir[0] . '/' . $key . '.css', $css ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	delete_transient( 'bst_fonts_wait_' . $key );
}
add_action( 'bst_perf_fetch_fonts', 'bst_perf_fetch_fonts' );

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( ! bst_mod( 'perf_local_fonts' ) || ! bst_perf_front() ) {
			return;
		}
		$styles = wp_styles();
		if ( empty( $styles->registered['bst-fonts'] ) ) {
			return;
		}
		$local = bst_perf_local_fonts_url( (string) $styles->registered['bst-fonts']->src );
		if ( $local ) {
			$styles->registered['bst-fonts']->src = $local;
			remove_filter( 'wp_resource_hints', 'bst_font_resource_hints', 10 );
		}
	},
	100
);

/* ------------------------------------------------------------------ Generated CSS as files */

/*
 * Inline CSS the theme attaches to its own stylesheets moves to a file named after its
 * content, linked right after the same stylesheet, so the order does not change. Small
 * snippets stay inline (a request would cost more than the bytes).
 */
add_action(
	'wp_print_styles',
	function () {
		if ( ! bst_mod( 'perf_css_file' ) || ! bst_perf_front() ) {
			return;
		}
		$dir = bst_perf_dir( 'css' );
		if ( ! $dir ) {
			return;
		}
		$styles = wp_styles();
		$queue  = $styles->queue;
		$new    = array();
		foreach ( $queue as $handle ) {
			$new[] = $handle;
			if ( 0 !== strpos( $handle, 'bst-' ) || empty( $styles->registered[ $handle ] ) || empty( $styles->registered[ $handle ]->src ) ) {
				continue;
			}
			$after = $styles->get_data( $handle, 'after' );
			$css   = is_array( $after ) ? trim( implode( "\n", $after ) ) : '';
			if ( strlen( $css ) < 1024 ) {
				continue;
			}
			$name = md5( $css ) . '.css';
			$file = $dir[0] . '/' . $name;
			if ( ! file_exists( $file ) && false === file_put_contents( $file, $css ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
				continue;
			}
			$styles->add_data( $handle, 'after', array() );
			wp_register_style( $handle . '-gen', $dir[1] . '/' . $name, array( $handle ), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
			$new[] = $handle . '-gen';
		}
		$styles->queue = $new;
	},
	1
);

/*
 * Files are named after their content, so a change simply creates a new file. Cached pages
 * may still link old ones, so only files older than 30 days are removed, once the folder
 * holds many (per-page CSS can produce variants).
 */
add_action(
	'bst_perf_daily',
	function () {
		$dir   = bst_perf_dir( 'css' );
		$files = $dir ? (array) glob( $dir[0] . '/*.css' ) : array();
		if ( count( $files ) > 300 ) {
			foreach ( $files as $file ) {
				if ( filemtime( $file ) < time() - 30 * DAY_IN_SECONDS ) {
					wp_delete_file( $file );
				}
			}
		}
	}
);
add_action(
	'init',
	function () {
		if ( ! wp_next_scheduled( 'bst_perf_daily' ) ) {
			wp_schedule_event( time() + DAY_IN_SECONDS, 'daily', 'bst_perf_daily' );
		}
	}
);
add_action(
	'switch_theme',
	function () {
		wp_clear_scheduled_hook( 'bst_perf_daily' );
	}
);
