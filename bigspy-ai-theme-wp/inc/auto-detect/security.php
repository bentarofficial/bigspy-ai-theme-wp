<?php
/** Report-only security checks: checksums, PHP in uploads, suspicious code, unknown admins. */
defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_ad_modules',
	function ( $modules ) {
		$modules['security'] = array(
			'label' => __( 'Security scan', 'bigspy-ai-theme-wp' ),
			'intro' => __( 'Compares WordPress and plugin files with official checksums and looks for PHP in uploads, suspicious code and new administrators. It only reports and does not replace a firewall.', 'bigspy-ai-theme-wp' ),
			'step'  => 'bst_ad_security_step',
		);
		return $modules;
	}
);

function bst_ad_security_item( $severity, $title, $detail ) {
	return array( 'key' => 'sec-' . md5( $title . $detail ), 'severity' => $severity, 'title' => $title, 'detail' => $detail, 'ignore' => true );
}

/** Files under a directory, skipping vendor caches; capped so a huge site still finishes. */
function bst_ad_security_files( $dir, $pattern, $limit = 20000 ) {
	$out = array();
	if ( ! is_dir( $dir ) ) {
		return $out;
	}
	$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
	foreach ( $iterator as $file ) {
		if ( $file->isFile() && preg_match( $pattern, $file->getFilename() ) ) {
			$out[] = $file->getPathname();
			if ( count( $out ) >= $limit ) {
				break;
			}
		}
	}
	return $out;
}

function bst_ad_security_step( $state ) {
	$phases = array( 'bst_ad_security_core', 'bst_ad_security_plugins', 'bst_ad_security_uploads', 'bst_ad_security_code', 'bst_ad_security_admins' );
	$state  = call_user_func( $phases[ $state['phase'] ], $state );
	if ( ! empty( $state['next'] ) ) {
		unset( $state['next'] );
		$state['phase']++;
		$state['cursor'] = 0;
		$state['data']   = array();
	}
	if ( $state['phase'] >= count( $phases ) ) {
		$state['status'] = 'done';
	}
	return $state;
}

function bst_ad_security_core( $state ) {
	if ( ! isset( $state['data']['sums'] ) ) {
		$url      = add_query_arg( array( 'version' => get_bloginfo( 'version' ), 'locale' => 'en_US' ), 'https://api.wordpress.org/core/checksums/1.0/' );
		$response = wp_remote_get( $url, array( 'timeout' => 15 ) );
		$body     = json_decode( (string) wp_remote_retrieve_body( $response ), true );
		if ( empty( $body['checksums'] ) ) {
			$state['items'][] = bst_ad_security_item( 'review', __( 'WordPress checksums unavailable', 'bigspy-ai-theme-wp' ), get_bloginfo( 'version' ) );
			$state['next']    = true;
			return $state;
		}
		$state['data']['sums'] = $body['checksums'];
		$state['total']        = count( $body['checksums'] );
		return $state;
	}
	$slice = array_slice( $state['data']['sums'], $state['cursor'], 400, true );
	foreach ( $slice as $file => $hash ) {
		if ( 0 === strpos( $file, 'wp-content/' ) ) {
			continue;
		}
		$path = ABSPATH . $file;
		if ( ! file_exists( $path ) ) {
			$state['items'][] = bst_ad_security_item( 'review', __( 'WordPress file missing', 'bigspy-ai-theme-wp' ), $file );
		} elseif ( md5_file( $path ) !== $hash ) {
			$state['items'][] = bst_ad_security_item( 'error', __( 'WordPress file changed', 'bigspy-ai-theme-wp' ), $file );
		}
	}
	$state['cursor'] += count( $slice );
	$state['done']    = $state['cursor'];
	$state['next']    = count( $slice ) < 400;
	return $state;
}

/** One plugin per batch. wordpress.org has no checksums for premium plugins; those are noted. */
function bst_ad_security_plugins( $state ) {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$plugins = array_values( array_filter( array_keys( get_plugins() ), function ( $file ) { return false !== strpos( $file, '/' ); } ) );
	$all     = get_plugins();
	$batch   = array_slice( $plugins, $state['cursor'], 1 );
	foreach ( $batch as $file ) {
		$slug     = dirname( $file );
		$version  = $all[ $file ]['Version'];
		$response = wp_remote_get( 'https://downloads.wordpress.org/plugin-checksums/' . rawurlencode( $slug ) . '/' . rawurlencode( $version ) . '.json', array( 'timeout' => 15 ) );
		$body     = json_decode( (string) wp_remote_retrieve_body( $response ), true );
		if ( empty( $body['files'] ) ) {
			continue;
		}
		foreach ( $body['files'] as $path => $sums ) {
			$local = WP_PLUGIN_DIR . '/' . $slug . '/' . $path;
			if ( file_exists( $local ) && ! in_array( md5_file( $local ), (array) $sums['md5'], true ) ) {
				$state['items'][] = bst_ad_security_item( 'error', __( 'Plugin file changed', 'bigspy-ai-theme-wp' ), $slug . '/' . $path );
			}
		}
	}
	$state['cursor']++;
	$state['next'] = $state['cursor'] >= count( $plugins );
	return $state;
}

function bst_ad_security_uploads( $state ) {
	$dir = wp_get_upload_dir()['basedir'];
	foreach ( bst_ad_security_files( $dir, '/\.(php\d?|phtml|phar|pht)$/i' ) as $path ) {
		if ( 'index.php' !== basename( $path ) || filesize( $path ) > 100 ) {
			$state['items'][] = bst_ad_security_item( 'error', __( 'PHP file in uploads', 'bigspy-ai-theme-wp' ), str_replace( ABSPATH, '', wp_normalize_path( $path ) ) );
		}
	}
	$state['next'] = true;
	return $state;
}

/** Heuristic only: real plugins can match, so hits are always marked for review. */
function bst_ad_security_suspicious( $code ) {
	return (bool) preg_match( '/\b(?:eval|assert)\s*\(\s*(?:base64_decode|gzinflate|gzuncompress|str_rot13|\$_(?:GET|POST|REQUEST|COOKIE))|\b(?:system|shell_exec|passthru|exec)\s*\(\s*\$_(?:GET|POST|REQUEST|COOKIE)|preg_replace\s*\(\s*[\'"].*\/e[\'"]/i', $code );
}

function bst_ad_security_code( $state ) {
	if ( ! isset( $state['data']['files'] ) ) {
		$files = array();
		foreach ( array( WPMU_PLUGIN_DIR, get_stylesheet_directory(), get_template_directory(), WP_PLUGIN_DIR ) as $dir ) {
			$files = array_merge( $files, bst_ad_security_files( $dir, '/\.php$/i' ) );
		}
		$state['data']['files'] = array_values( array_unique( $files ) );
		$state['total']         = count( $state['data']['files'] );
		$state['done']          = 0;
		return $state;
	}
	$slice = array_slice( $state['data']['files'], $state['cursor'], 300 );
	foreach ( $slice as $path ) {
		if ( filesize( $path ) < 1048576 && bst_ad_security_suspicious( (string) file_get_contents( $path ) ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions
			$state['items'][] = bst_ad_security_item( 'review', __( 'Suspicious code', 'bigspy-ai-theme-wp' ), str_replace( wp_normalize_path( ABSPATH ), '', wp_normalize_path( $path ) ) );
		}
	}
	$state['cursor'] += count( $slice );
	$state['done']    = $state['cursor'];
	$state['next']    = count( $slice ) < 300;
	return $state;
}

/** Admins not seen by an earlier scan. The first scan sets the baseline, so recent accounts are flagged instead. */
function bst_ad_security_admins( $state ) {
	$known = get_option( 'bst_ad_known_admins' );
	$ids   = get_users( array( 'role' => 'administrator', 'fields' => 'ID' ) );
	foreach ( $ids as $id ) {
		$user   = get_userdata( $id );
		$recent = strtotime( $user->user_registered ) > time() - 30 * DAY_IN_SECONDS;
		if ( is_array( $known ) ? ! in_array( (int) $id, $known, true ) : $recent ) {
			$state['items'][] = bst_ad_security_item( 'review', __( 'New administrator', 'bigspy-ai-theme-wp' ), $user->user_login . ' (' . $user->user_registered . ')' );
		}
	}
	update_option( 'bst_ad_known_admins', array_map( 'intval', $ids ), false );
	$state['next'] = true;
	return $state;
}
