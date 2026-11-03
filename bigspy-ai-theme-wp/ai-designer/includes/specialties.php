<?php
/**
 * Specialties picked in the chat bar: Auto (no extra context), Designer, Images, Writing, SEO.
 * Each has a default context that applies as is; the user can edit it on the Guidelines page
 * and restore the default. All tools stay available in every specialty.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** key => array( label, default context ). */
function bst_aid_specialty_defaults() {
	return array(
		'designer' => array(
			__( 'Designer', 'bigspy-ai-theme-wp' ),
			"Focus on page design: structure, block choice, motion and visual rhythm.\nPlan each page as sections (page_plan), pick a section recipe or the best block for each, build with build_sections.\nOpen with a banner, vary layouts, keep one accent color for actions, check every section at 375px.\nKeep copy short and real; ask for missing facts instead of inventing them.",
		),
		'images'   => array(
			__( 'Images', 'bigspy-ai-theme-wp' ),
			"Focus on images: create, choose and place them.\nLook in the Media Library first (list_media); create new ones with ImageGen only when nothing fits, and say how many you will create.\nImages look like real photos of real people and places: natural light, natural expressions, no plastic skin, no distorted hands, no text in the image unless asked.\nGive every image a specific alt text and a sensible file title; optimize large images.",
		),
		'writing'  => array(
			__( 'Writing', 'bigspy-ai-theme-wp' ),
			"Focus on writing: pages, posts and product text.\nFollow the writing standard; write like a real person, short sentences, one idea each.\nStart from the reader's question, give practical steps or examples, end with a clear call to action.\nUse only facts from the guidelines, the site and the chat; mark anything missing as [to confirm].",
		),
		'seo'      => array(
			__( 'SEO', 'bigspy-ai-theme-wp' ),
			"Focus on search: keywords, headings, metadata and links.\nAgree on one main keyword per page; put it naturally in the H1, the first paragraph, one H2, the SEO title and description (seo_meta).\nCheck pages with check_page: one H1, heading order, alt texts, broken links, large media.\nSuggest internal links between related pages; never stuff keywords or promise rankings.",
		),
	);
}

function bst_aid_specialty_keys() {
	return array_merge( array( 'auto' ), array_keys( bst_aid_specialty_defaults() ) );
}

/** Context in use for a specialty: the user's edited text, or the default. '' for auto. */
function bst_aid_specialty_context( $key ) {
	$defaults = bst_aid_specialty_defaults();
	if ( ! isset( $defaults[ $key ] ) ) {
		return '';
	}
	$saved = (array) get_option( 'bst_aid_specialties', array() );
	return '' !== trim( (string) ( $saved[ $key ] ?? '' ) ) ? (string) $saved[ $key ] : $defaults[ $key ][1];
}

/** Save ('' restores the default). */
function bst_aid_save_specialty( $key, $text ) {
	if ( ! isset( bst_aid_specialty_defaults()[ $key ] ) ) {
		return false;
	}
	$saved = (array) get_option( 'bst_aid_specialties', array() );
	$text  = mb_substr( sanitize_textarea_field( (string) $text ), 0, 6000 );
	if ( '' === trim( $text ) || trim( $text ) === bst_aid_specialty_defaults()[ $key ][1] ) {
		unset( $saved[ $key ] );
	} else {
		$saved[ $key ] = $text;
	}
	update_option( 'bst_aid_specialties', $saved, false );
	return true;
}

/** Labels for the chat bar. */
function bst_aid_specialty_list() {
	$out = array( array( 'key' => 'auto', 'label' => __( 'Auto', 'bigspy-ai-theme-wp' ) ) );
	foreach ( bst_aid_specialty_defaults() as $key => $d ) {
		$out[] = array( 'key' => $key, 'label' => $d[0] );
	}
	return $out;
}

add_action(
	'admin_post_bst_aid_specialties',
	function () {
		if ( ! current_user_can( bst_aid_cap() ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'bigspy-ai-theme-wp' ) );
		}
		check_admin_referer( 'bst_aid_specialties' );
		$reset = sanitize_key( wp_unslash( $_POST['reset'] ?? '' ) );
		foreach ( array_keys( bst_aid_specialty_defaults() ) as $key ) {
			bst_aid_save_specialty( $key, $reset === $key ? '' : wp_unslash( $_POST[ 'bst_aid_sp_' . $key ] ?? '' ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized in bst_aid_save_specialty().
		}
		wp_safe_redirect( admin_url( 'admin.php?page=bst-ai-guidelines&saved=1#bst-aid-specialties' ) );
		exit;
	}
);

/** Specialty cards on the Guidelines page. */
function bst_aid_specialties_form() {
	echo '<h2 id="bst-aid-specialties">' . esc_html__( 'Specialties', 'bigspy-ai-theme-wp' ) . '</h2>';
	echo '<p class="bst-aid-lead">' . esc_html__( 'Pick a specialty next to the model in the chat. Each one adds this context to the task; it works as is, edit it if you want. Auto adds nothing.', 'bigspy-ai-theme-wp' ) . '</p>';
	echo '<div class="bst-aid-guides">';
	foreach ( bst_aid_specialty_defaults() as $key => $d ) {
		echo '<div class="bst-aid-card"><h3>' . esc_html( $d[0] ) . '</h3>';
		echo '<textarea id="bst-aid-sp-' . esc_attr( $key ) . '" rows="6" maxlength="6000" class="large-text" data-bst-aid-context="sp_' . esc_attr( $key ) . '" data-version="' . (int) bst_aid_context_version( 'sp_' . $key ) . '">' . esc_textarea( bst_aid_specialty_context( $key ) ) . '</textarea>';
		echo '<p><button type="button" class="button" data-bst-aid-reset="bst-aid-sp-' . esc_attr( $key ) . '">' . esc_html__( 'Restore default', 'bigspy-ai-theme-wp' ) . '</button> <span class="bst-aid-saved" aria-live="polite"></span></p></div>';
	}
	echo '</div>';
}
