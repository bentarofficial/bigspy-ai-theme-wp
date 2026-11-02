<?php
/**
 * Media ATND: a panel in the image details of the media picker. The user types a keyword and a short
 * description, picks a language and model, and presses Generate to write Alt, Title, Caption and
 * Description. Nothing calls the API until then. Uses the AI Designer connection.
 */
defined( 'ABSPATH' ) || exit;

/** Language name sent to the model => label shown to the user. */
function bst_atnd_languages() {
	return array(
		'Vietnamese' => __( 'Vietnamese', 'bigspy-ai-theme-wp' ),
		'English'    => __( 'English', 'bigspy-ai-theme-wp' ),
		'Japanese'   => __( 'Japanese', 'bigspy-ai-theme-wp' ),
		'Chinese'    => __( 'Chinese', 'bigspy-ai-theme-wp' ),
		'French'     => __( 'French', 'bigspy-ai-theme-wp' ),
	);
}

function bst_atnd_ready() {
	return function_exists( 'bst_aid_ready' ) && bst_aid_ready();
}

/** The old BIGSEO plugin adds the same panel; never show two. */
function bst_atnd_enabled() {
	return ! function_exists( 'bs_ai_media_processor_v17' );
}

function bst_atnd_default_language() {
	return 0 === strpos( get_locale(), 'vi' ) ? 'Vietnamese' : 'English';
}

function bst_atnd_model_ids() {
	return wp_list_pluck( bst_aid_models()['text'], 'id' );
}

function bst_atnd_panel( $id ) {
	if ( ! bst_atnd_ready() ) {
		return sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=bigspy-ai-designer' ) ), esc_html__( 'Connect the API to use this tool', 'bigspy-ai-theme-wp' ) );
	}
	$lang    = get_post_meta( $id, '_bst_atnd_lang', true ) ?: bst_atnd_default_language();
	$default = bst_aid_settings()['text_model'];
	$model   = get_post_meta( $id, '_bst_atnd_model', true ) ?: $default;
	$out     = '<div class="bst-atnd" data-id="' . (int) $id . '">';
	$out    .= '<p><label>' . esc_html__( 'SEO keyword', 'bigspy-ai-theme-wp' ) . '<input type="text" class="bst-atnd-keyword widefat" value="' . esc_attr( get_post_meta( $id, '_bst_atnd_keyword', true ) ) . '"></label></p>';
	$out    .= '<p><label>' . esc_html__( 'Short image description', 'bigspy-ai-theme-wp' ) . '<input type="text" class="bst-atnd-desc widefat" value="' . esc_attr( get_post_meta( $id, '_bst_atnd_desc', true ) ) . '"></label></p>';
	$out    .= '<p><label>' . esc_html__( 'Language', 'bigspy-ai-theme-wp' ) . '<select class="bst-atnd-lang widefat">';
	foreach ( bst_atnd_languages() as $value => $label ) {
		$out .= '<option value="' . esc_attr( $value ) . '"' . selected( $lang, $value, false ) . '>' . esc_html( $label ) . '</option>';
	}
	$out .= '</select></label></p><p><label>' . esc_html__( 'Model', 'bigspy-ai-theme-wp' ) . '<select class="bst-atnd-model widefat">';
	$ids  = array_unique( array_merge( array( $default ), bst_atnd_model_ids() ) );
	foreach ( $ids as $value ) {
		$out .= '<option value="' . esc_attr( $value ) . '"' . selected( $model, $value, false ) . '>' . esc_html( $value ) . '</option>';
	}
	$out .= '</select></label></p><p><button type="button" class="button button-primary bst-atnd-go">' . esc_html__( 'Generate', 'bigspy-ai-theme-wp' ) . '</button> <span class="bst-atnd-status" role="status"></span></p></div>';
	return $out;
}

add_filter(
	'attachment_fields_to_edit',
	function ( $fields, $post ) {
		if ( bst_atnd_enabled() && wp_attachment_is_image( $post->ID ) && current_user_can( 'edit_post', $post->ID ) ) {
			$fields['bst_atnd'] = array(
				'label' => __( 'Media ATND', 'bigspy-ai-theme-wp' ),
				'input' => 'html',
				'html'  => bst_atnd_panel( $post->ID ),
			);
		}
		return $fields;
	},
	10,
	2
);

function bst_atnd_enqueue() {
	if ( ! bst_atnd_enabled() || ! is_admin() ) {
		return;
	}
	wp_enqueue_script( 'bst-media-atnd', get_template_directory_uri() . '/assets/js/media-atnd.js', array( 'jquery' ), wp_get_theme( get_template() )->get( 'Version' ), true );
	wp_localize_script(
		'bst-media-atnd',
		'bstAtnd',
		array(
			'url'     => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'bst_atnd' ),
			'working' => __( 'Writing...', 'bigspy-ai-theme-wp' ),
			'done'    => __( 'Done.', 'bigspy-ai-theme-wp' ),
			'noKey'   => __( 'Enter a keyword first.', 'bigspy-ai-theme-wp' ),
			'failed'  => __( 'Could not generate the text.', 'bigspy-ai-theme-wp' ),
		)
	);
}
add_action( 'wp_enqueue_media', 'bst_atnd_enqueue' );
add_action(
	'admin_enqueue_scripts',
	function () {
		$screen = get_current_screen();
		if ( $screen && 'attachment' === $screen->post_type && 'post' === $screen->base ) {
			bst_atnd_enqueue();
		}
	}
);

/** The image as a data URI, so it also works on sites the API cannot reach. */
function bst_atnd_image( $id ) {
	$sized = image_get_intermediate_size( $id, 'medium' );
	$path  = $sized ? path_join( dirname( (string) get_attached_file( $id ) ), $sized['file'] ) : (string) get_attached_file( $id );
	$mime  = (string) ( $sized['mime-type'] ?? get_post_mime_type( $id ) );
	if ( $path && file_exists( $path ) && filesize( $path ) <= 4 * MB_IN_BYTES ) {
		return 'data:' . $mime . ';base64,' . base64_encode( (string) file_get_contents( $path ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions, WordPress.WP.AlternativeFunctions
	}
	return (string) wp_get_attachment_url( $id );
}

/** Ask the model for the four fields. Models without vision get the same prompt without the image. */
function bst_atnd_ask( $id, $keyword, $desc, $lang, $model ) {
	$prompt = sprintf(
		'You write image SEO text for the website "%1$s". File name: "%2$s". Target keyword: "%3$s". The user describes the image as: "%4$s". Write in %5$s. Stay faithful to the image and the description, and use the keyword naturally. Reply with JSON only, keys: alt (under 125 characters), title (short), caption (one sentence), description (two sentences). Do not start with "image of".',
		get_bloginfo( 'name' ),
		basename( (string) get_attached_file( $id ) ),
		$keyword,
		$desc,
		$lang
	);
	$parts = array(
		array( array( 'type' => 'text', 'text' => $prompt ), array( 'type' => 'image_url', 'image_url' => array( 'url' => bst_atnd_image( $id ) ) ) ),
		$prompt,
	);
	foreach ( $parts as $content ) {
		$res  = bst_aid_http( 'POST', '/chat/completions', array( 'model' => $model, 'max_tokens' => 600, 'messages' => array( array( 'role' => 'user', 'content' => $content ) ) ), true, null, 60 );
		$text = (string) ( $res['data']['choices'][0]['message']['content'] ?? '' );
		$json = preg_match( '/\{.*\}/s', $text, $m ) ? json_decode( $m[0], true ) : null;
		if ( ! empty( $res['ok'] ) && is_array( $json ) && ! empty( $json['alt'] ) ) {
			return $json;
		}
	}
	return new WP_Error( 'bst_atnd', empty( $res['ok'] ) ? (string) $res['error'] : __( 'The model did not return usable text.', 'bigspy-ai-theme-wp' ) );
}

/** Write all four fields: the user pressed Generate on this image. */
function bst_atnd_save( $id, $fields ) {
	$clean = array();
	foreach ( array( 'alt', 'title', 'caption', 'description' ) as $name ) {
		$clean[ $name ] = sanitize_text_field( (string) ( $fields[ $name ] ?? '' ) );
	}
	update_post_meta( $id, '_wp_attachment_image_alt', $clean['alt'] );
	$update = array( 'ID' => $id );
	foreach ( array( 'title' => 'post_title', 'caption' => 'post_excerpt', 'description' => 'post_content' ) as $name => $column ) {
		if ( '' !== $clean[ $name ] ) {
			$update[ $column ] = $clean[ $name ];
		}
	}
	wp_update_post( wp_slash( $update ) );
	return $clean;
}

/** Remember the inputs, ask the model and save. Returns the saved fields, or WP_Error. */
function bst_atnd_generate( $id, $keyword, $desc, $lang, $model ) {
	if ( ! bst_atnd_ready() ) {
		return new WP_Error( 'bst_atnd', __( 'Connect the API in AI Designer settings first.', 'bigspy-ai-theme-wp' ) );
	}
	if ( ! wp_attachment_is_image( $id ) || ! current_user_can( 'edit_post', $id ) ) {
		return new WP_Error( 'bst_atnd', __( 'You cannot edit this image.', 'bigspy-ai-theme-wp' ) );
	}
	if ( '' === $keyword ) {
		return new WP_Error( 'bst_atnd', __( 'Enter a keyword first.', 'bigspy-ai-theme-wp' ) );
	}
	$default = bst_aid_settings()['text_model'];
	$lang    = isset( bst_atnd_languages()[ $lang ] ) ? $lang : bst_atnd_default_language();
	$model   = in_array( $model, bst_atnd_model_ids(), true ) ? $model : $default;
	update_post_meta( $id, '_bst_atnd_keyword', $keyword );
	update_post_meta( $id, '_bst_atnd_desc', $desc );
	update_post_meta( $id, '_bst_atnd_lang', $lang );
	update_post_meta( $id, '_bst_atnd_model', $model );
	$fields = bst_atnd_ask( $id, $keyword, $desc, $lang, $model );
	return is_wp_error( $fields ) ? $fields : bst_atnd_save( $id, $fields );
}

add_action(
	'wp_ajax_bst_atnd_generate',
	function () {
		check_ajax_referer( 'bst_atnd', 'nonce' );
		$read   = function ( $key ) {
			return isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
		};
		$result = bst_atnd_generate( absint( $read( 'id' ) ), $read( 'keyword' ), $read( 'desc' ), $read( 'lang' ), $read( 'model' ) );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}
		wp_send_json_success( $result );
	}
);
