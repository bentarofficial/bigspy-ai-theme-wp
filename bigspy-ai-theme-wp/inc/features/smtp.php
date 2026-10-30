<?php
/**
 * SMTP: points wp_mail at the configured server. Without a host WordPress mail stays as it is.
 * The password can live in wp-config.php as BST_SMTP_PASS instead of the database.
 */
defined( 'ABSPATH' ) || exit;

function bst_smtp_password() {
	return defined( 'BST_SMTP_PASS' ) ? (string) BST_SMTP_PASS : bst_feature_decrypt( bst_feature_get( 'smtp', 'password' ) );
}

function bst_smtp_configure( $mailer ) {
	$host = bst_feature_get( 'smtp', 'host' );
	if ( ! $host ) {
		return;
	}
	$secure = bst_feature_get( 'smtp', 'secure', 'tls' );
	$user   = bst_feature_get( 'smtp', 'user' );
	$mailer->isSMTP();
	$mailer->Host        = $host;
	$mailer->Port        = (int) bst_feature_get( 'smtp', 'port', 587 );
	$mailer->SMTPSecure  = 'none' === $secure ? '' : $secure;
	$mailer->SMTPAutoTLS = 'none' !== $secure;
	$mailer->SMTPAuth    = '' !== $user;
	if ( $mailer->SMTPAuth ) {
		$mailer->Username = $user;
		$mailer->Password = bst_smtp_password();
	}
	$from = bst_feature_get( 'smtp', 'from' );
	if ( $from ) {
		$mailer->setFrom( $from, bst_feature_get( 'smtp', 'from_name' ) ?: $mailer->FromName, false );
		$mailer->Sender = $from;
	}
}
add_action( 'phpmailer_init', 'bst_smtp_configure' );

add_filter(
	'bst_feature_fields',
	function ( $fields ) {
		$fields['smtp'] = array(
			'host'      => array( 'type' => 'text', 'label' => __( 'SMTP host', 'bigspy-ai-theme-wp' ) ),
			'port'      => array( 'type' => 'number', 'label' => __( 'Port', 'bigspy-ai-theme-wp' ), 'default' => 587, 'min' => 1, 'max' => 65535 ),
			'secure'    => array(
				'type'    => 'select',
				'label'   => __( 'Encryption', 'bigspy-ai-theme-wp' ),
				'default' => 'tls',
				'choices' => array( 'tls' => 'TLS', 'ssl' => 'SSL', 'none' => __( 'None', 'bigspy-ai-theme-wp' ) ),
			),
			'user'      => array( 'type' => 'text', 'label' => __( 'Username', 'bigspy-ai-theme-wp' ) ),
			'password'  => array( 'type' => 'password', 'label' => __( 'Password', 'bigspy-ai-theme-wp' ), 'help' => __( 'Stored encrypted. You can define BST_SMTP_PASS in wp-config.php instead.', 'bigspy-ai-theme-wp' ) ),
			'from'      => array( 'type' => 'email', 'label' => __( 'From email', 'bigspy-ai-theme-wp' ) ),
			'from_name' => array( 'type' => 'text', 'label' => __( 'From name', 'bigspy-ai-theme-wp' ) ),
		);
		return $fields;
	}
);

/** Send a test email to the current admin; the mail error stays in a short per-user transient. */
function bst_smtp_send_test() {
	$user  = wp_get_current_user();
	$error = '';
	$catch = function ( $wp_error ) use ( &$error ) {
		$error = $wp_error->get_error_message();
	};
	add_action( 'wp_mail_failed', $catch );
	$sent = wp_mail( $user->user_email, __( 'SMTP test email', 'bigspy-ai-theme-wp' ), __( 'Your SMTP settings work.', 'bigspy-ai-theme-wp' ) );
	remove_action( 'wp_mail_failed', $catch );
	if ( ! $sent ) {
		set_transient( 'bst_smtp_error_' . $user->ID, $error, 300 );
	}
	return $sent;
}

add_action(
	'admin_post_bst_smtp_test',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'bigspy-ai-theme-wp' ) );
		}
		check_admin_referer( 'bst_smtp_test' );
		$msg = bst_smtp_send_test() ? 'smtp_sent' : 'smtp_failed';
		wp_safe_redirect( admin_url( 'admin.php?page=bigspy-features&bst_msg=' . $msg ) );
		exit;
	}
);

add_action(
	'bst_features_notices',
	function ( $msg ) {
		if ( 'smtp_sent' === $msg ) {
			echo '<div class="notice notice-success"><p>' . esc_html__( 'Test email sent. Check your inbox.', 'bigspy-ai-theme-wp' ) . '</p></div>';
		} elseif ( 'smtp_failed' === $msg ) {
			echo '<div class="notice notice-error"><p>' . esc_html__( 'Test email failed:', 'bigspy-ai-theme-wp' ) . ' ' . esc_html( (string) get_transient( 'bst_smtp_error_' . get_current_user_id() ) ) . '</p></div>';
		}
	}
);

add_action(
	'bst_features_after_form',
	function () {
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'bst_smtp_test' );
		echo '<input type="hidden" name="action" value="bst_smtp_test">';
		submit_button( __( 'Send a test email to me', 'bigspy-ai-theme-wp' ), 'secondary', 'submit', false );
		echo '</form>';
	}
);
