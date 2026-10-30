<?php
/**
 * Optional features page. Every feature is off by default and its file in inc/features/
 * is only loaded while it is on. A feature adds its settings through the bst_feature_fields filter.
 */
defined( 'ABSPATH' ) || exit;

/** Feature ids; each one has a file of the same name in inc/features/. */
function bst_feature_ids() {
	return array( 'webp', 'smtp', 'inline-related', 'code-inject' );
}

function bst_feature_catalog() {
	return array(
		'webp'           => array( __( 'WebP on upload', 'bigspy-ai-theme-wp' ), __( 'Convert new JPEG and PNG uploads to WebP. Existing images are not changed.', 'bigspy-ai-theme-wp' ) ),
		'smtp'           => array( __( 'SMTP email', 'bigspy-ai-theme-wp' ), __( 'Send WordPress email through your mail server.', 'bigspy-ai-theme-wp' ) ),
		'inline-related' => array( __( 'Related links in posts', 'bigspy-ai-theme-wp' ), __( 'Add a short list of related posts in the middle of each post.', 'bigspy-ai-theme-wp' ) ),
		'code-inject'    => array( __( 'Header and footer code', 'bigspy-ai-theme-wp' ), __( 'Add tracking or verification code to every front page.', 'bigspy-ai-theme-wp' ) ),
	);
}

function bst_feature_on( $id ) {
	return in_array( $id, (array) get_option( 'bst_features', array() ), true );
}

/** Saved value of one feature setting, or its default. */
function bst_feature_get( $id, $key, $default = '' ) {
	$saved = get_option( 'bst_feature_' . $id );
	return is_array( $saved ) && isset( $saved[ $key ] ) ? $saved[ $key ] : $default;
}

/** Secrets are stored encrypted with the site salt; a changed salt means typing them again. */
function bst_feature_encrypt( $value ) {
	if ( ! function_exists( 'openssl_encrypt' ) ) {
		return '';
	}
	$iv     = random_bytes( 16 );
	$cipher = openssl_encrypt( $value, 'aes-256-cbc', hash( 'sha256', wp_salt( 'auth' ), true ), OPENSSL_RAW_DATA, $iv );
	return false === $cipher ? '' : base64_encode( $iv . $cipher );
}

function bst_feature_decrypt( $value ) {
	$raw = base64_decode( (string) $value, true );
	if ( ! $raw || strlen( $raw ) < 17 || ! function_exists( 'openssl_decrypt' ) ) {
		return '';
	}
	$plain = openssl_decrypt( substr( $raw, 16 ), 'aes-256-cbc', hash( 'sha256', wp_salt( 'auth' ), true ), OPENSSL_RAW_DATA, substr( $raw, 0, 16 ) );
	return false === $plain ? '' : $plain;
}

foreach ( bst_feature_ids() as $bst_feature_id ) {
	if ( bst_feature_on( $bst_feature_id ) ) {
		require_once __DIR__ . '/features/' . $bst_feature_id . '.php';
	}
}

/** Clean one posted value by field type; null keeps what is stored. */
function bst_feature_clean( $field, $raw ) {
	$type = $field['type'];
	if ( 'checkbox' === $type ) {
		return null === $raw ? 0 : 1;
	}
	if ( null === $raw || 'note' === $type ) {
		return null;
	}
	$raw = wp_unslash( $raw );
	if ( 'code' === $type ) {
		return current_user_can( 'unfiltered_html' ) ? $raw : null;
	}
	if ( 'password' === $type ) {
		return '' === $raw ? null : bst_feature_encrypt( $raw );
	}
	if ( 'number' === $type ) {
		return max( $field['min'], min( $field['max'], (int) $raw ) );
	}
	if ( 'select' === $type ) {
		return isset( $field['choices'][ $raw ] ) ? $raw : $field['default'];
	}
	return 'email' === $type ? sanitize_email( $raw ) : sanitize_text_field( $raw );
}

/** Save the toggles and the settings of the features that were on when the page was opened. */
function bst_features_save( $post ) {
	$ids = array_values( array_intersect( bst_feature_ids(), array_map( 'strval', (array) ( $post['bst_on'] ?? array() ) ) ) );
	update_option( 'bst_features', $ids );
	foreach ( apply_filters( 'bst_feature_fields', array() ) as $id => $fields ) {
		$values = get_option( 'bst_feature_' . $id );
		$values = is_array( $values ) ? $values : array();
		foreach ( $fields as $key => $field ) {
			$clean = bst_feature_clean( $field, $post['bst_field'][ $id ][ $key ] ?? null );
			if ( null !== $clean ) {
				$values[ $key ] = $clean;
			}
		}
		update_option( 'bst_feature_' . $id, $values, false );
	}
}

add_action(
	'admin_post_bst_features_save',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'bigspy-ai-theme-wp' ) );
		}
		check_admin_referer( 'bst_features_save' );
		bst_features_save( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification -- checked above; values are cleaned by field type.
		wp_safe_redirect( admin_url( 'admin.php?page=bigspy-features&bst_msg=saved' ) );
		exit;
	}
);

add_action(
	'admin_menu',
	function () {
		add_submenu_page( 'bigspy-ai-theme-wp', __( 'Features', 'bigspy-ai-theme-wp' ), __( 'Features', 'bigspy-ai-theme-wp' ), 'manage_options', 'bigspy-features', 'bst_features_page' );
	},
	20
);

function bst_feature_field_html( $id, $key, $field ) {
	$name  = 'bst_field[' . $id . '][' . $key . ']';
	$value = bst_feature_get( $id, $key, $field['default'] ?? '' );
	$label = esc_html( $field['label'] );
	$help  = empty( $field['help'] ) ? '' : '<p class="description">' . esc_html( $field['help'] ) . '</p>';
	$attr  = 'name="' . esc_attr( $name ) . '" aria-label="' . esc_attr( $field['label'] ) . '"';
	switch ( $field['type'] ) {
		case 'note':
			$input = '';
			break;
		case 'checkbox':
			$input = '<input type="checkbox" ' . $attr . ' value="1"' . checked( (int) $value, 1, false ) . '>';
			break;
		case 'number':
			$input = '<input type="number" class="small-text" ' . $attr . ' min="' . (int) $field['min'] . '" max="' . (int) $field['max'] . '" value="' . (int) $value . '">';
			break;
		case 'select':
			$input = '<select ' . $attr . '>';
			foreach ( $field['choices'] as $choice => $text ) {
				$input .= '<option value="' . esc_attr( $choice ) . '"' . selected( $value, $choice, false ) . '>' . esc_html( $text ) . '</option>';
			}
			$input .= '</select>';
			break;
		case 'password':
			$input = '<input type="password" class="regular-text" autocomplete="new-password" ' . $attr . ' placeholder="' . esc_attr( $value ? __( 'Saved. Leave empty to keep it.', 'bigspy-ai-theme-wp' ) : '' ) . '">';
			break;
		case 'code':
			$locked = current_user_can( 'unfiltered_html' ) ? '' : ' readonly';
			$input  = '<textarea class="large-text code" rows="6" ' . $attr . $locked . '>' . esc_textarea( $value ) . '</textarea>';
			break;
		default:
			$input = '<input type="' . ( 'email' === $field['type'] ? 'email' : 'text' ) . '" class="regular-text" ' . $attr . ' value="' . esc_attr( $value ) . '">';
	}
	return '<tr><th scope="row">' . $label . '</th><td>' . $input . $help . '</td></tr>';
}

function bst_features_page() {
	$fields = apply_filters( 'bst_feature_fields', array() );
	echo '<div class="wrap"><h1>' . esc_html__( 'Features', 'bigspy-ai-theme-wp' ) . '</h1>';
	echo '<p>' . esc_html__( 'Each feature is off by default and loads no code until you turn it on. Turn one on and save to see its settings.', 'bigspy-ai-theme-wp' ) . '</p>';
	$msg = sanitize_key( $_GET['bst_msg'] ?? '' ); // phpcs:ignore WordPress.Security.NonceVerification -- display only.
	if ( 'saved' === $msg ) {
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Settings saved.', 'bigspy-ai-theme-wp' ) . '</p></div>';
	}
	do_action( 'bst_features_notices', $msg );
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	wp_nonce_field( 'bst_features_save' );
	echo '<input type="hidden" name="action" value="bst_features_save">';
	foreach ( bst_feature_catalog() as $id => $info ) {
		echo '<h2><label><input type="checkbox" name="bst_on[]" value="' . esc_attr( $id ) . '"' . checked( bst_feature_on( $id ), true, false ) . '> ' . esc_html( $info[0] ) . '</label></h2>';
		echo '<p>' . esc_html( $info[1] ) . '</p>';
		if ( ! empty( $fields[ $id ] ) ) {
			echo '<table class="form-table" role="presentation">';
			foreach ( $fields[ $id ] as $key => $field ) {
				echo bst_feature_field_html( $id, $key, $field ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped while building.
			}
			echo '</table>';
		}
	}
	submit_button();
	echo '</form>';
	do_action( 'bst_features_after_form' );
	echo '</div>';
}
