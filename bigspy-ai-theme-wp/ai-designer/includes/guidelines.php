<?php
/**
 * Guidelines: the user's writing standard, image standard and business facts. Writing and
 * business go into the system prompt; the image standard is added to every ImageGen call.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

function bst_aid_guideline_fields() {
	return array(
		'business' => array(
			__( 'Business information', 'bigspy-ai-theme-wp' ),
			__( 'Name, field, target customers, phone, address, what makes you different. The designer only uses facts written here or told in chat.', 'bigspy-ai-theme-wp' ),
			__( "Business name:\nField / services:\nTarget customers:\nPhone / Zalo:\nEmail:\nAddress:\nOpening hours:\nWhat makes us different:\nPrices or packages (if any):", 'bigspy-ai-theme-wp' ),
		),
		'writing'  => array(
			__( 'Writing standard', 'bigspy-ai-theme-wp' ),
			__( 'Voice, how to address readers, length, headings, words to avoid, SEO rules. Used for every page, post and product text.', 'bigspy-ai-theme-wp' ),
			__( "Voice: friendly, clear, professional; write like a real person, no hype.\nAddress the reader as: you.\nSentences: short, one idea each; no filler or clichés.\nHeadings: one H1 per page, H2/H3 in order, each heading says what the section gives.\nSEO: main keyword in the H1, first paragraph and one H2; natural, never stuffed.\nPosts: 1,200–1,800 words, a short intro, practical steps or examples, a clear call to action.\nAvoid: unverified claims, made-up numbers, words like “best” or “number 1” without proof.", 'bigspy-ai-theme-wp' ),
		),
		'images'   => array(
			__( 'Image standard', 'bigspy-ai-theme-wp' ),
			__( 'Style, main colors, ratio, people or not, text on images, what to avoid. Added to every image ImageGen creates.', 'bigspy-ai-theme-wp' ),
			__( "Style: clean, modern, realistic photo look, soft natural light.\nColors: follow the website colors.\nPeople: Asian people when people appear; natural expressions.\nText on images: none, unless asked.\nRatio: 16:9 for banners, 4:3 for cards, 1:1 for products.\nAvoid: real celebrities, other brands' logos, watermarks, distorted hands or faces.", 'bigspy-ai-theme-wp' ),
		),
	);
}

function bst_aid_guidelines() {
	$g = get_option( 'bst_aid_guidelines', array() );
	$g = is_array( $g ) ? $g : array();
	return array(
		'business' => (string) ( $g['business'] ?? '' ),
		'writing'  => (string) ( $g['writing'] ?? '' ),
		'images'   => (string) ( $g['images'] ?? '' ),
	);
}

/** Save some or all guidelines (plain text, max 6,000 characters each). */
function bst_aid_save_guidelines( $changes ) {
	$g = bst_aid_guidelines();
	foreach ( array_keys( $g ) as $k ) {
		if ( isset( $changes[ $k ] ) ) {
			$g[ $k ] = mb_substr( sanitize_textarea_field( (string) $changes[ $k ] ), 0, 6000 );
		}
	}
	update_option( 'bst_aid_guidelines', $g, false );
	return $g;
}

add_action(
	'admin_post_bst_aid_guidelines',
	function () {
		if ( ! current_user_can( bst_aid_cap() ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'bigspy-ai-theme-wp' ) );
		}
		check_admin_referer( 'bst_aid_guidelines' );
		$in = array();
		foreach ( array_keys( bst_aid_guideline_fields() ) as $k ) {
			$in[ $k ] = wp_unslash( $_POST[ 'bst_aid_' . $k ] ?? '' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized in bst_aid_save_guidelines().
		}
		bst_aid_save_guidelines( $in );
		wp_safe_redirect( admin_url( 'admin.php?page=bst-ai-guidelines&saved=1' ) );
		exit;
	}
);

function bst_aid_guidelines_page() {
	$g = bst_aid_guidelines();
	echo '<div class="wrap bst-aid"><h1>' . esc_html__( 'BIGSPY AI Designer – Guidelines', 'bigspy-ai-theme-wp' ) . '</h1>';
	echo '<p class="bst-aid-lead">' . esc_html__( 'The designer follows these standards in every task. Leave a box empty and it will ask you in chat first, then offer to save your answers here.', 'bigspy-ai-theme-wp' ) . '</p>';
	if ( isset( $_GET['saved'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Guidelines saved.', 'bigspy-ai-theme-wp' ) . '</p></div>';
	}
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="bst-aid-guides">';
	wp_nonce_field( 'bst_aid_guidelines' );
	echo '<input type="hidden" name="action" value="bst_aid_guidelines">';
	foreach ( bst_aid_guideline_fields() as $k => $f ) {
		echo '<div class="bst-aid-card"><h2>' . esc_html( $f[0] ) . '</h2><p class="description">' . esc_html( $f[1] ) . '</p>';
		echo '<textarea name="bst_aid_' . esc_attr( $k ) . '" id="bst-aid-' . esc_attr( $k ) . '" rows="9" maxlength="6000" class="large-text">' . esc_textarea( $g[ $k ] ) . '</textarea>';
		echo '<p><button type="button" class="button" data-bst-aid-template="bst-aid-' . esc_attr( $k ) . '" data-template="' . esc_attr( $f[2] ) . '">' . esc_html__( 'Insert suggested template', 'bigspy-ai-theme-wp' ) . '</button></p></div>';
	}
	submit_button( __( 'Save guidelines', 'bigspy-ai-theme-wp' ) );
	echo '</form></div>';
}
