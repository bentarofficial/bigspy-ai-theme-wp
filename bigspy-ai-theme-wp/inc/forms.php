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
	$out = array();
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
	);
	wp_add_inline_script( 'bst-editor-helpers', 'Object.assign(window.bstEditor || {}, ' . wp_json_encode( $data ) . ');', 'after' );
}
add_action( 'enqueue_block_editor_assets', 'bst_form_editor_data' );
