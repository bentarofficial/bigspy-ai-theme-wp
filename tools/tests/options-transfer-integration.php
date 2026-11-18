<?php
/* Theme options export → import → undo → reset round trip. Results go to /adt/result.txt. */
require_once '/wordpress/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/admin.php';
wp_set_current_user( 1 );
$GLOBALS['bst_log'] = array();
function bst_t( $ok, $name ) {
	$GLOBALS['bst_log'][] = ( $ok ? 'PASS ' : 'FAIL ' ) . $name;
}
try {
	$keys = bst_transfer_keys();
	bst_t( isset( $keys['h1_size_tablet'], $keys['h1_size_mobile'], $keys['title_bar_padding'], $keys['gs_section_space_mobile'] ), 'keys include responsive and spacing fields' );

	// Source site state.
	$up   = wp_upload_dir();
	$file = $up['path'] . '/bst-logo-test.png';
	file_put_contents( $file, base64_decode( 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==' ) );
	$logo = wp_insert_attachment( array( 'post_mime_type' => 'image/png', 'post_title' => 'logo', 'post_status' => 'inherit' ), $file );
	set_theme_mod( 'custom_logo', $logo );
	set_theme_mod( 'color_primary', '#123456' );
	set_theme_mod( 'h1_size', 44 );
	set_theme_mod( 'h1_size_tablet', 36 );
	set_theme_mod( 'h1_size_mobile', 28 );
	set_theme_mod( 'title_bar_padding', '{"unit":"px","desktop":["10","0","10","0"]}' );
	set_theme_mod( 'container_width', 1320 );
	wp_update_custom_css_post( '.a > .b { color: red; }' );

	$data = bst_export_options_data();
	$json = wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE );
	bst_t( is_string( $json ) && strlen( $json ) < bst_import_max_bytes(), 'export encodes to JSON (' . strlen( (string) $json ) . ' bytes)' );
	bst_t( 36 === $data['options']['h1_size_tablet'] && 28 === $data['options']['h1_size_mobile'], 'export has tablet/mobile values' );
	bst_t( ! empty( $data['options']['title_bar_padding'] ) && '#123456' === $data['options']['color_primary'], 'export has spacing and colors' );
	bst_t( '' !== $data['media']['custom_logo'], 'export has logo URL' );

	// Wipe, then import the decoded file like the upload handler does.
	foreach ( array_keys( $keys ) as $k ) { remove_theme_mod( $k ); }
	remove_theme_mod( 'custom_logo' );
	wp_update_custom_css_post( '' );
	$tmp = wp_tempnam( 'bst' );
	file_put_contents( $tmp, "\xEF\xBB\xBF" . $json );
	$decoded = json_decode( preg_replace( '/^\xEF\xBB\xBF/', '', file_get_contents( $tmp ) ), true );
	$res     = bst_import_options_data( $decoded );
	bst_t( $res['applied'] > 50 && 0 === $res['media_failed'], 'import applied ' . $res['applied'] . ', skipped ' . $res['skipped'] . ', media ' . $res['media'] );
	bst_t( '#123456' === get_theme_mod( 'color_primary' ) && 1320 === (int) get_theme_mod( 'container_width' ), 'colors and numbers restored' );
	bst_t( 36 === get_theme_mod( 'h1_size_tablet' ) && 28 === get_theme_mod( 'h1_size_mobile' ) && 44 === get_theme_mod( 'h1_size' ), 'responsive values restored' );
	bst_t( false !== strpos( (string) get_theme_mod( 'title_bar_padding' ), '"10"' ), 'spacing restored' );
	bst_t( (int) $logo === (int) get_theme_mod( 'custom_logo' ), 'logo restored (same attachment reused)' );
	bst_t( false !== strpos( wp_get_custom_css(), '.a > .b' ), 'Additional CSS restored with > selectors' );
	$again = bst_export_options_data();
	unset( $again['date'], $data['date'] );
	$diff = array();
	foreach ( $data['options'] as $k => $v ) {
		if ( ! array_key_exists( $k, $again['options'] ) || $again['options'][ $k ] !== $v ) {
			$diff[] = $k . '=' . wp_json_encode( $v ) . '->' . wp_json_encode( $again['options'][ $k ] ?? null );
		}
	}
	bst_t( ! $diff, 'second export equals first (lossless round trip)' . ( $diff ? ': ' . substr( implode( ' | ', $diff ), 0, 1500 ) : '' ) );

	// Hostile values are cleaned.
	bst_import_options_data( array( 'options' => array( 'color_primary' => 'red;}</style><script>', 'h1_size' => 9999, 'h1_size_mobile' => 'abc', 'not_an_option' => 'x' ) ) );
	bst_t( '#123456' !== get_theme_mod( 'color_primary' ) && false === strpos( (string) get_theme_mod( 'color_primary' ), '<' ), 'bad color rejected' );
	bst_t( (int) get_theme_mod( 'h1_size' ) <= 90 && '' === get_theme_mod( 'h1_size_mobile' ), 'numbers clamped, junk emptied' );
	bst_t( false === get_theme_mod( 'not_an_option' ), 'unknown keys ignored' );

	// Undo brings back the state before that import.
	$_REQUEST['_wpnonce'] = wp_create_nonce( 'bst_undo' );
	add_filter( 'wp_redirect', function () { throw new Exception( 'redirect' ); } );
	try { bst_handle_undo(); } catch ( Exception $e ) { /* redirect */ }
	bst_t( '#123456' === get_theme_mod( 'color_primary' ) && 28 === get_theme_mod( 'h1_size_mobile' ), 'undo restores previous options' );

	// Reset then undo.
	$_REQUEST['_wpnonce'] = wp_create_nonce( 'bst_reset' );
	try { bst_handle_reset(); } catch ( Exception $e ) { /* redirect */ }
	bst_t( false === get_theme_mod( 'h1_size_tablet', false ) && '#123456' !== get_theme_mod( 'color_primary', bst_defaults()['color_primary'] ), 'reset clears options' );
	$_REQUEST['_wpnonce'] = wp_create_nonce( 'bst_undo' );
	try { bst_handle_undo(); } catch ( Exception $e ) { /* redirect */ }
	bst_t( 36 === get_theme_mod( 'h1_size_tablet' ), 'undo after reset restores options' );

	// File checks.
	bst_t( 'too-big' === bst_import_read_upload( array( 'error' => UPLOAD_ERR_INI_SIZE ) ), 'too-big upload reported' );
	bst_t( 'no-file' === bst_import_read_upload( array( 'error' => UPLOAD_ERR_NO_FILE ) ), 'missing file reported' );

	// Dashboard renders.
	ob_start(); bst_dashboard_page(); $html = ob_get_clean();
	bst_t( false !== strpos( $html, 'bst_undo' ) && false !== strpos( $html, 'Maximum file size' ), 'dashboard shows undo and size' );
} catch ( Throwable $e ) {
	bst_t( false, 'exception: ' . $e->getMessage() . ' @' . $e->getLine() );
}
file_put_contents( '/adt/result.txt', implode( "\n", $GLOBALS['bst_log'] ) . "\n" );
