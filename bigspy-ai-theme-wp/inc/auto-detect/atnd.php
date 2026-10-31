<?php
/**
 * Media ATND: finds images with missing Alt, Title, Caption or Description and writes them with the
 * theme's AI connection. Scanning is free; only pressing Generate calls the API. Existing text is kept.
 */
defined( 'ABSPATH' ) || exit;

define( 'BST_AD_ATND_BATCH', 3 );

add_filter(
	'bst_ad_modules',
	function ( $modules ) {
		$modules['atnd'] = array(
			'label'  => __( 'Media ATND', 'bigspy-ai-theme-wp' ),
			'intro'  => __( 'Finds images missing Alt text, Title, Caption or Description, and writes them with AI after you select the images. Text already there is kept. Uses the API connection in AI Designer settings; the model must support images.', 'bigspy-ai-theme-wp' ),
			'step'   => 'bst_ad_atnd_step',
			'action' => 'bst_ad_atnd_action',
		);
		return $modules;
	}
);

add_filter(
	'bst_ad_public_extra',
	function ( $extra, $id ) {
		if ( 'atnd' !== $id ) {
			return $extra;
		}
		return array( 'ready' => function_exists( 'bst_aid_ready' ) && bst_aid_ready(), 'connect' => admin_url( 'admin.php?page=bigspy-ai-designer' ), 'batch' => BST_AD_ATND_BATCH );
	},
	10,
	2
);

/** Names of the empty fields. A title that only repeats the file name counts as empty. */
function bst_ad_atnd_missing( $id ) {
	$post = get_post( $id );
	$file = sanitize_title( pathinfo( (string) get_post_meta( $id, '_wp_attached_file', true ), PATHINFO_FILENAME ) );
	$out  = array();
	if ( '' === trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) ) ) {
		$out[] = 'alt';
	}
	if ( '' === trim( $post->post_title ) || sanitize_title( $post->post_title ) === $file ) {
		$out[] = 'title';
	}
	if ( '' === trim( $post->post_excerpt ) ) {
		$out[] = 'caption';
	}
	if ( '' === trim( $post->post_content ) ) {
		$out[] = 'description';
	}
	return $out;
}

function bst_ad_atnd_item( $id ) {
	return array(
		'key'      => 'atnd-' . $id,
		'severity' => 'review',
		'title'    => get_the_title( $id ),
		'detail'   => implode( ', ', bst_ad_atnd_missing( $id ) ),
		'posts'    => array(),
		'select'   => $id,
		'thumb'    => bst_ad_media_thumb( $id ),
		'edit'     => get_edit_post_link( $id, 'raw' ),
		'ignore'   => true,
	);
}

function bst_ad_atnd_step( $state ) {
	$ids = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_mime_type' => 'image', 'numberposts' => 100, 'offset' => $state['cursor'], 'orderby' => 'ID', 'order' => 'ASC', 'fields' => 'ids' ) );
	foreach ( $ids as $id ) {
		if ( bst_ad_atnd_missing( $id ) ) {
			$state['items'][] = bst_ad_atnd_item( $id );
		}
	}
	$state['cursor'] += count( $ids );
	$state['done']    = $state['cursor'];
	if ( count( $ids ) < 100 ) {
		$state['status'] = 'done';
		/* translators: %d: image count. */
		$state['note'] = sprintf( __( '%d images need text.', 'bigspy-ai-theme-wp' ), count( $state['items'] ) );
	}
	return $state;
}

/** The image as a data URI, so it also works on sites the API cannot reach. */
function bst_ad_atnd_image( $id ) {
	$sized = image_get_intermediate_size( $id, 'medium' );
	$path  = $sized ? path_join( dirname( (string) get_attached_file( $id ) ), $sized['file'] ) : (string) get_attached_file( $id );
	$mime  = (string) ( $sized['mime-type'] ?? get_post_mime_type( $id ) );
	if ( $path && file_exists( $path ) && filesize( $path ) <= 4 * MB_IN_BYTES ) {
		return 'data:' . $mime . ';base64,' . base64_encode( (string) file_get_contents( $path ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions, WordPress.WP.AlternativeFunctions
	}
	return (string) wp_get_attachment_url( $id );
}

/** Ask the model for the four fields. Returns an array, or WP_Error. */
function bst_ad_atnd_ask( $id ) {
	$parent  = (int) wp_get_post_parent_id( $id );
	$context = $parent ? get_the_title( $parent ) : '';
	$prompt  = sprintf(
		'Look at this image from the website "%1$s". Page it belongs to: "%2$s". File name: "%3$s". Write in the language %4$s, for image SEO. Reply with JSON only, keys: alt (what the image shows, under 125 characters), title (short and natural), caption (one sentence for readers), description (two sentences). Describe only what you can see. Do not start with "image of".',
		get_bloginfo( 'name' ),
		$context,
		basename( (string) get_attached_file( $id ) ),
		get_locale()
	);
	$body = array(
		'model'      => bst_aid_settings()['text_model'],
		'max_tokens' => 600,
		'messages'   => array(
			array(
				'role'    => 'user',
				'content' => array(
					array( 'type' => 'text', 'text' => $prompt ),
					array( 'type' => 'image_url', 'image_url' => array( 'url' => bst_ad_atnd_image( $id ) ) ),
				),
			),
		),
	);
	$res = bst_aid_http( 'POST', '/chat/completions', $body, true, null, 60 );
	if ( empty( $res['ok'] ) ) {
		return new WP_Error( 'bst_ad_atnd', $res['error'], array( 'status' => 502 ) );
	}
	$text = (string) ( $res['data']['choices'][0]['message']['content'] ?? '' );
	$json = preg_match( '/\{.*\}/s', $text, $m ) ? json_decode( $m[0], true ) : null;
	if ( ! is_array( $json ) || empty( $json['alt'] ) ) {
		return new WP_Error( 'bst_ad_atnd', __( 'The model did not return usable text. Choose a model that supports images.', 'bigspy-ai-theme-wp' ), array( 'status' => 502 ) );
	}
	return $json;
}

/** Fill empty fields only. */
function bst_ad_atnd_save( $id, $fields ) {
	$missing = bst_ad_atnd_missing( $id );
	$update  = array( 'ID' => $id );
	if ( in_array( 'alt', $missing, true ) ) {
		update_post_meta( $id, '_wp_attachment_image_alt', sanitize_text_field( (string) $fields['alt'] ) );
	}
	foreach ( array( 'title' => 'post_title', 'caption' => 'post_excerpt', 'description' => 'post_content' ) as $name => $column ) {
		if ( in_array( $name, $missing, true ) && ! empty( $fields[ $name ] ) ) {
			$update[ $column ] = sanitize_text_field( (string) $fields[ $name ] );
		}
	}
	if ( count( $update ) > 1 ) {
		wp_update_post( wp_slash( $update ) );
	}
}

/** Generate for a few listed images; the page sends the next few after each reply. */
function bst_ad_atnd_action( WP_REST_Request $request ) {
	if ( 'generate' !== $request->get_param( 'op' ) ) {
		return new WP_Error( 'bst_ad_op', 'Unknown action', array( 'status' => 400 ) );
	}
	if ( ! function_exists( 'bst_aid_ready' ) || ! bst_aid_ready() ) {
		return new WP_Error( 'bst_ad_atnd', __( 'Connect the API in AI Designer settings first.', 'bigspy-ai-theme-wp' ), array( 'status' => 409 ) );
	}
	$state  = bst_ad_state( 'atnd' );
	$listed = array_map( 'intval', wp_list_pluck( $state['items'], 'select' ) );
	$ids    = array_slice( array_values( array_intersect( wp_parse_id_list( $request->get_param( 'ids' ) ), $listed ) ), 0, BST_AD_ATND_BATCH );
	$error  = null;
	foreach ( $ids as $id ) {
		if ( ! current_user_can( 'edit_post', $id ) ) {
			continue;
		}
		$fields = bst_ad_atnd_ask( $id );
		if ( is_wp_error( $fields ) ) {
			$error = $fields;
			break;
		}
		bst_ad_atnd_save( $id, $fields );
		foreach ( $state['items'] as $i => $item ) {
			if ( (int) $item['select'] === $id ) {
				unset( $state['items'][ $i ] );
			}
		}
	}
	$state['items'] = array_values( $state['items'] );
	bst_ad_save_state( 'atnd', $state );
	return $error ? $error : true;
}
