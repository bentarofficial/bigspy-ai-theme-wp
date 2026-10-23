<?php
/**
 * Form block support: lists forms from Contact Form 7 and WPForms so the
 * BIGSPY "Form" block can show a picker (like Flatsome's "Form (CF7)" element).
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Active form plugins: key => label. */
function bst_form_providers() {
	$out = array( 'bigspy' => __( 'Built-in BIGSPY form', 'bigspy-ai-theme-wp' ) );
	if ( defined( 'WPCF7_VERSION' ) || post_type_exists( 'wpcf7_contact_form' ) ) {
		$out['cf7'] = 'Contact Form 7';
	}
	if ( function_exists( 'wpforms' ) || post_type_exists( 'wpforms' ) ) {
		$out['wpforms'] = 'WPForms';
	}
	return $out;
}

/** All forms: [ [ provider, id, title ], … ]. */
function bst_form_list() {
	$types = array( 'cf7' => 'wpcf7_contact_form', 'wpforms' => 'wpforms' );
	$list  = array();
	foreach ( bst_form_providers() as $key => $label ) {
		$posts = get_posts( array( 'post_type' => $types[ $key ], 'post_status' => 'publish', 'numberposts' => 200, 'orderby' => 'title', 'order' => 'ASC' ) );
		foreach ( $posts as $post ) {
			$list[] = array( $key, (int) $post->ID, $post->post_title ? $post->post_title : '#' . $post->ID );
		}
	}
	return $list;
}

/** Shortcode markup for one form, or ''. */
function bst_form_markup( $provider, $id ) {
	$id = absint( $id );
	if ( ! $id ) {
		return '';
	}
	if ( 'cf7' === $provider && shortcode_exists( 'contact-form-7' ) ) {
		return do_shortcode( '[contact-form-7 id="' . $id . '"]' );
	}
	if ( 'wpforms' === $provider && shortcode_exists( 'wpforms' ) ) {
		return do_shortcode( '[wpforms id="' . $id . '"]' );
	}
	return '';
}

/** Editor data for the picker (only loaded in the block editor). */
function bst_form_editor_data() {
	$providers = bst_form_providers();
	$new_url   = isset( $providers['cf7'] ) ? admin_url( 'admin.php?page=wpcf7-new' ) : ( isset( $providers['wpforms'] ) ? admin_url( 'admin.php?page=wpforms-builder' ) : admin_url( 'plugin-install.php?s=contact+form+7&tab=search&type=term' ) );
	$data      = array(
		'forms'         => bst_form_list(),
		'formProviders' => (object) $providers,
		'formNewUrl'    => $new_url,
		'formEditUrl'   => admin_url( 'admin.php?page=wpcf7&action=edit&post=' ),
		'formMail'      => bst_form_mail_status(),
		'formNative'    => function_exists( 'bst_native_form_defaults' ) ? bst_native_form_defaults() : array(),
		'formEntries'   => admin_url( 'edit.php?post_type=bst_form_entry' ),
	);
	wp_add_inline_script( 'bst-editor-helpers', 'Object.assign(window.bstEditor || {}, ' . wp_json_encode( $data ) . ');', 'after' );
}
add_action( 'enqueue_block_editor_assets', 'bst_form_editor_data' );

/**
 * How WordPress sends email on this site, for the Form block's "Sending" panel.
 *
 * @return array{smtp:bool,mailer:string,from:string,settings:string,test:string,install:string,redirectPlugin:bool}
 */
function bst_form_mail_status() {
	$smtp   = defined( 'WPMS_PLUGIN_VER' ) || class_exists( 'WPMailSMTP\\Core' );
	$opts   = get_option( 'wp_mail_smtp', array() );
	$mailer = is_array( $opts ) && ! empty( $opts['mail']['mailer'] ) ? (string) $opts['mail']['mailer'] : '';
	$from   = is_array( $opts ) && ! empty( $opts['mail']['from_email'] ) ? (string) $opts['mail']['from_email'] : (string) get_option( 'admin_email' );
	return array(
		'smtp'           => $smtp,
		'mailer'         => $smtp ? ( $mailer ? $mailer : 'mail' ) : 'mail',
		'from'           => $from,
		'settings'       => admin_url( 'admin.php?page=wp-mail-smtp' ),
		'test'           => admin_url( 'admin.php?page=wp-mail-smtp-tools&tab=test' ),
		'install'        => admin_url( 'plugin-install.php?s=wp+mail+smtp&tab=search&type=term' ),
		'redirectPlugin' => defined( 'WPCF7_PRO_REDIRECT_PLUGIN_VERSION' ) || class_exists( 'WPCF7r_Form' ),
	);
}
