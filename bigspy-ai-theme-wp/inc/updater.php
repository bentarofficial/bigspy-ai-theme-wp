<?php
/** GitHub Releases updates for this theme. */
defined( 'ABSPATH' ) || exit;

const BST_UPDATE_SLUG = 'bigspy-ai-theme-wp';
const BST_UPDATE_CACHE = 'bst_github_release';

/** The standard WordPress manual check must also bypass our provider cache. */
function bst_update_manual_check() {
	global $pagenow;
	if ( 'update-core.php' === $pagenow && ! empty( $_GET['force-check'] ) && current_user_can( 'update_themes' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- WordPress core's read-only Check again link.
		delete_transient( BST_UPDATE_CACHE );
	}
}
add_action( 'admin_init', 'bst_update_manual_check' );

/** Explicit refresh from About uses a nonce and the WordPress update capability. */
add_action( 'admin_post_bst_check_theme_updates', function () {
	if ( ! current_user_can( 'update_themes' ) ) {
		wp_die( esc_html__( 'You are not allowed to update themes.', 'bigspy-ai-theme-wp' ), '', array( 'response' => 403 ) );
	}
	check_admin_referer( 'bst_check_theme_updates' );
	delete_transient( BST_UPDATE_CACHE );
	wp_clean_themes_cache( true );
	wp_update_themes();
	wp_safe_redirect( admin_url( 'admin.php?page=bigspy-about' ) );
	exit;
} );

function bst_update_release() {
	$cached = get_transient( BST_UPDATE_CACHE );
	if ( false !== $cached ) {
		return $cached;
	}
	$response = wp_remote_get(
		'https://api.github.com/repos/bentarofficial/bigspy-ai-theme-wp/releases/latest',
		array( 'timeout' => 10, 'headers' => array( 'Accept' => 'application/vnd.github+json', 'User-Agent' => 'BIGSPY-Theme-Updater/' . BST_VERSION ) )
	);
	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		set_transient( BST_UPDATE_CACHE, array(), HOUR_IN_SECONDS );
		return array();
	}
	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! is_array( $data ) || ! empty( $data['draft'] ) || ! empty( $data['prerelease'] ) || empty( $data['tag_name'] ) || ! preg_match( '/^v?(\d+\.\d+\.\d+)$/', $data['tag_name'], $match ) ) {
		set_transient( BST_UPDATE_CACHE, array(), HOUR_IN_SECONDS );
		return array();
	}
	$version = $match[1];
	$filename = 'bigspy-ai-theme-wp-' . $version . '.zip';
	$prefix = 'https://github.com/bentarofficial/bigspy-ai-theme-wp/releases/download/' . rawurlencode( $data['tag_name'] ) . '/';
	$package = '';
	foreach ( (array) ( $data['assets'] ?? array() ) as $asset ) {
		if ( is_array( $asset ) && ( $asset['name'] ?? '' ) === $filename && ( $asset['browser_download_url'] ?? '' ) === $prefix . $filename ) {
			$package = $asset['browser_download_url'];
			break;
		}
	}
	if ( ! $package ) {
		set_transient( BST_UPDATE_CACHE, array(), HOUR_IN_SECONDS );
		return array();
	}
	$release = array( 'version' => $version, 'package' => $package, 'url' => esc_url_raw( $data['html_url'] ?? '' ), 'body' => (string) ( $data['body'] ?? '' ) );
	set_transient( BST_UPDATE_CACHE, $release, 12 * HOUR_IN_SECONDS );
	return $release;
}

add_filter( 'pre_set_site_transient_update_themes', function ( $transient ) {
	if ( ! is_object( $transient ) ) {
		return $transient;
	}
	$release = bst_update_release();
	$theme = wp_get_theme( BST_UPDATE_SLUG );
	if ( $release && $theme->exists() && version_compare( $release['version'], $theme->get( 'Version' ), '>' ) ) {
		$transient->response[ BST_UPDATE_SLUG ] = array( 'theme' => BST_UPDATE_SLUG, 'new_version' => $release['version'], 'url' => $release['url'], 'package' => $release['package'] );
		unset( $transient->no_update[ BST_UPDATE_SLUG ] );
	} else {
		unset( $transient->response[ BST_UPDATE_SLUG ] );
	}
	return $transient;
} );

add_filter( 'themes_api', function ( $result, $action, $args ) {
	if ( 'theme_information' !== $action || empty( $args->slug ) || BST_UPDATE_SLUG !== $args->slug ) {
		return $result;
	}
	$release = bst_update_release();
	if ( ! $release ) {
		return $result;
	}
	return (object) array( 'name' => 'BIGSPY AI Theme WP', 'slug' => BST_UPDATE_SLUG, 'version' => $release['version'], 'author' => 'Bentar', 'homepage' => $release['url'], 'download_link' => $release['package'], 'sections' => array( 'changelog' => wp_kses_post( wpautop( esc_html( $release['body'] ) ) ) ) );
}, 10, 3 );

add_filter( 'upgrader_source_selection', function ( $source, $remote_source, $upgrader, $hook_extra ) {
	if ( empty( $hook_extra['theme'] ) || BST_UPDATE_SLUG !== $hook_extra['theme'] || ! is_dir( $source ) ) {
		return $source;
	}
	$target = trailingslashit( $remote_source ) . BST_UPDATE_SLUG;
	if ( untrailingslashit( $source ) === $target ) {
		return $source;
	}
	if ( ! file_exists( trailingslashit( $source ) . 'style.css' ) || file_exists( $target ) ) {
		return new WP_Error( 'bst_update_package', __( 'Invalid BIGSPY AI update package.', 'bigspy-ai-theme-wp' ) );
	}
	global $wp_filesystem;
	if ( ! $wp_filesystem || ! $wp_filesystem->move( untrailingslashit( $source ), $target ) ) {
		return new WP_Error( 'bst_update_move', __( 'Could not prepare BIGSPY AI update package.', 'bigspy-ai-theme-wp' ) );
	}
	return trailingslashit( $target );
}, 10, 4 );
