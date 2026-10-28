<?php
require_once '/wordpress/wp-load.php';
wp_set_current_user( 1 );
$passed = array();
function bst_welcome_test( $ok, $name ) {
	if ( ! $ok ) { throw new Exception( $name ); }
	$GLOBALS['passed'][] = $name;
}
$theme = bst_welcome_data( 'theme' );
$aid = bst_welcome_data( 'designer' );
bst_welcome_test( 16 === count( $theme['features'] ) && 12 === count( $aid['features'] ), 'Both product catalogs are complete' );
foreach ( array_merge( $theme['features'], $aid['features'] ) as $item ) {
	bst_welcome_test( ! empty( $item['title'] ) && ! empty( $item['summary'] ) && ! empty( $item['detail'] ) && ! empty( $item['when'] ) && count( $item['steps'] ) >= 3 && ! empty( $item['tips'] ) && 'available' === $item['status'], 'Complete guide: ' . $item['title'] );
}
foreach ( $aid['features'] as $item ) { bst_welcome_test( count( $item['prompts'] ) >= 2, 'At least two prompts: ' . $item['title'] ); }
$knowledge = bst_aid_tool_get_knowledge( array( 'topic' => 'theme' ) );
bst_welcome_test( $knowledge['product_guides']['theme'] === $theme && $knowledge['product_guides']['designer'] === $aid, 'Knowledge and welcome use identical catalogs' );
delete_user_meta( 1, 'bst_welcome_seen_theme' );
bst_welcome_test( false !== strpos( bst_welcome_label( 'theme' ), 'update-plugins' ), 'Unseen major release shows a badge' );
set_transient( BST_UPDATE_CACHE, array( 'version' => '1.74.0', 'package' => '', 'body' => '', 'url' => '' ), HOUR_IN_SECONDS );
ob_start(); bst_welcome_screen(); $html = ob_get_clean();
bst_welcome_test( false === strpos( bst_welcome_label( 'theme' ), 'update-plugins' ), 'Viewing About marks its major release seen' );
bst_welcome_test( 16 === substr_count( $html, 'data-bst-welcome-card' ) && false !== strpos( $html, '/screenshot.webp' ) && false !== strpos( $html, 'Quick start' ), 'Theme screen renders checklist, local screenshot and guides' );
ob_start(); bst_welcome_screen( 'designer' ); $html = ob_get_clean();
bst_welcome_test( 24 === substr_count( $html, 'bst_prompt=' ) && false !== strpos( $html, 'Coming soon' ), 'AI screen renders sample links and planned feature label' );
bst_welcome_test( 'en_US' === bst_defaults()['theme_lang_admin'] && 'en_US' === bst_defaults()['theme_lang_front'], 'Both language defaults are English' );
$original_locale = get_locale();
set_theme_mod( 'theme_lang_front', 'vi' );
bst_theme_load_selected_language();
bst_welcome_test( 'Bắt đầu nhanh' === __( 'Quick start', 'bigspy-ai-theme-wp' ), 'Explicit Vietnamese loads PHP translations on modern WordPress' );
bst_welcome_test( $original_locale === get_locale(), 'Theme translation leaves the WordPress locale unchanged' );
set_theme_mod( 'theme_lang_front', 'en_US' );
bst_theme_load_selected_language();
bst_welcome_test( 'Quick start' === __( 'Quick start', 'bigspy-ai-theme-wp' ), 'Explicit English restores source text' );
$uploads = wp_upload_dir();
file_put_contents( $uploads['basedir'] . '/bst-welcome-results.json', wp_json_encode( array( 'passed' => count( $passed ), 'checks' => $passed ) ) );
