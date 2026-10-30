<?php
/**
 * Playground-only login. tools/playground-page.cjs mounts this file; it is outside
 * the theme folder and never ships in a ZIP.
 */
add_action(
	'init',
	function () {
		$host = wp_parse_url( home_url(), PHP_URL_HOST );
		if ( empty( $_GET['bst_autologin'] ) || ! in_array( $host, array( '127.0.0.1', 'localhost' ), true ) ) {
			return;
		}
		$user = get_user_by( 'login', 'admin' );
		if ( $user ) {
			wp_set_current_user( $user->ID );
			wp_set_auth_cookie( $user->ID, true );
			wp_safe_redirect( admin_url() );
			exit;
		}
	}
);
