<?php
define( 'FS_METHOD', 'direct' );
require '/wordpress/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/update.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
wp_set_current_user( 1 );

function bst_updater_check( $ok, $label ) {
	if ( ! $ok ) { throw new Exception( $label ); }
	$GLOBALS['bst_updater_passed'][] = $label;
	echo "PASS: $label\n";
}

$zip = get_template_directory() . '/bst-test-update.zip';
$fixture = array(
	'tag_name' => 'v9.9.9', 'draft' => false, 'prerelease' => false,
	'html_url' => 'https://github.com/bentarofficial/bigspy-ai-theme-wp/releases/tag/v9.9.9',
	'body' => "Update test\nFixed one item.",
	'assets' => array( array( 'name' => 'bigspy-ai-theme-wp-9.9.9.zip', 'browser_download_url' => 'https://github.com/bentarofficial/bigspy-ai-theme-wp/releases/download/v9.9.9/bigspy-ai-theme-wp-9.9.9.zip' ) ),
);
$GLOBALS['bst_release_fixture'] = $fixture;
add_filter( 'pre_http_request', function ( $pre, $args, $url ) {
	if ( false === strpos( $url, 'api.github.com/repos/bentarofficial/bigspy-ai-theme-wp/releases/latest' ) ) { return $pre; }
	if ( null === $GLOBALS['bst_release_fixture'] ) { return new WP_Error( 'test_network', 'Offline' ); }
	return array( 'response' => array( 'code' => 200, 'message' => 'OK' ), 'headers' => array(), 'body' => wp_json_encode( $GLOBALS['bst_release_fixture'] ), 'cookies' => array() );
}, 10, 3 );

delete_transient( BST_UPDATE_CACHE );
$release = bst_update_release();
bst_updater_check( '9.9.9' === $release['version'], 'Release and asset accepted' );
set_transient( BST_UPDATE_CACHE, array( 'version' => '1.73.3' ), 12 * HOUR_IN_SECONDS );
bst_updater_check( '1.73.3' === bst_update_release()['version'], 'Automatic checks reuse the provider cache' );
$GLOBALS['pagenow'] = 'update-core.php';
$_GET['force-check'] = '1';
wp_set_current_user( 0 );
bst_update_manual_check();
bst_updater_check( false !== get_transient( BST_UPDATE_CACHE ), 'Unauthorized request cannot clear provider cache' );
wp_set_current_user( 1 );
bst_update_manual_check();
bst_updater_check( false === get_transient( BST_UPDATE_CACHE ), 'WordPress Check again clears provider cache immediately' );
bst_updater_check( '9.9.9' === bst_update_release()['version'], 'Manual check discovers the latest release without waiting' );
unset( $_GET['force-check'] );
$transient = apply_filters( 'pre_set_site_transient_update_themes', (object) array( 'response' => array(), 'no_update' => array() ) );
bst_updater_check( isset( $transient->response['bigspy-ai-theme-wp'] ), 'WordPress update offered' );
$info = apply_filters( 'themes_api', false, 'theme_information', (object) array( 'slug' => 'bigspy-ai-theme-wp' ) );
bst_updater_check( is_object( $info ) && false !== strpos( $info->sections['changelog'], 'Fixed one item' ), 'Release notes displayed' );
ob_start();
bst_panel_about_page();
$about = ob_get_clean();
bst_updater_check( false !== strpos( $about, 'Latest version: 9.9.9' ) && false !== strpos( $about, 'action=bst_check_theme_updates' ) && false !== strpos( $about, '_wpnonce=' ), 'About displays latest release and a protected refresh action' );
$GLOBALS['bst_release_fixture']['assets'][0]['browser_download_url'] = 'https://evil.example/update.zip';
delete_transient( BST_UPDATE_CACHE );
bst_updater_check( array() === bst_update_release(), 'Foreign package rejected' );
$GLOBALS['bst_release_fixture'] = null;
delete_transient( BST_UPDATE_CACHE );
bst_updater_check( array() === bst_update_release(), 'API failure is harmless' );
$GLOBALS['bst_release_fixture'] = $fixture;
delete_transient( BST_UPDATE_CACHE );

set_theme_mod( 'bst_updater_test', 'preserved' );
update_option( 'bst_updater_test', 'preserved' );
set_site_transient( 'update_themes', (object) array( 'response' => array(), 'no_update' => array() ) );
add_filter( 'upgrader_pre_download', function ( $reply, $package ) use ( $zip ) {
	return false !== strpos( $package, '/releases/download/v9.9.9/' ) ? $zip : $reply;
}, 10, 2 );
class BST_Test_Upgrader_Skin extends Automatic_Upgrader_Skin {
	public function request_filesystem_credentials( $error = false, $context = '', $allow_relaxed_file_ownership = false ) { return true; }
}
$upgrader = new Theme_Upgrader( new BST_Test_Upgrader_Skin() );
$result = $upgrader->upgrade( 'bigspy-ai-theme-wp' );
bst_updater_check( ! is_wp_error( $result ) && $result, 'WordPress Theme_Upgrader installed release ZIP' );
wp_clean_themes_cache();
bst_updater_check( '9.9.9' === wp_get_theme( 'bigspy-ai-theme-wp' )->get( 'Version' ), 'Installed theme version updated' );
bst_updater_check( 'bigspy-ai-theme-wp' === get_stylesheet(), 'Theme remains active' );
bst_updater_check( 'preserved' === get_theme_mod( 'bst_updater_test' ) && 'preserved' === get_option( 'bst_updater_test' ), 'Theme mods and options preserved' );
echo "UPDATER_TEST_COMPLETE\n";
file_put_contents( '/wordpress/wp-content/bst-updater-source/updater-results.json', wp_json_encode( $GLOBALS['bst_updater_passed'] ) );
