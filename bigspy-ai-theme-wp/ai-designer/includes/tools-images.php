<?php
/**
 * Phase 3: ImageGen (create images with the chosen image model), image_status for slow
 * jobs, optimize_image, and the list of generated images nothing uses.
 *
 * Images go into the Media Library as WebP with title, alt text, caption and description,
 * and are marked as AI-generated. Nothing here deletes media: WordPress has no media
 * trash by default, so removing unused images is left to the user.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Default size per purpose (the API maps WxH to the nearest supported ratio). */
function bst_aid_image_sizes() {
	return array(
		'banner'     => '1920x1080',
		'background' => '1920x1080',
		'section'    => '1600x900',
		'post'       => '1600x900',
		'card'       => '1200x900',
		'product'    => '1024x1024',
		'logo'       => '1024x1024',
		'icon'       => '1024x1024',
		'portrait'   => '900x1200',
		'other'      => '1600x900',
	);
}

add_filter(
	'bst_aid_tools',
	function ( $tools ) {
		$tools['ImageGen']       = array(
			'description' => 'Create one image with the image model chosen in Settings and add it to the Media Library (WebP, with title, alt, caption, description). The image standard from the guidelines is added automatically. Never ask for real people who are public figures or for other brands\' logos. Returns the media id and URLs to put into blocks, posts or products. If it returns status "pending", call image_status later.',
			'parameters'  => array(
				'type'       => 'object',
				'properties' => array(
					'prompt'      => array( 'type' => 'string', 'description' => 'Detailed visual description in English: subject, setting, composition, light, mood, colors. No text in the image unless the user asked.' ),
					'purpose'     => array( 'type' => 'string', 'enum' => array_keys( bst_aid_image_sizes() ) ),
					'size'        => array( 'type' => 'string', 'description' => 'WIDTHxHEIGHT, e.g. 1920x1080. Defaults by purpose.' ),
					'quality'     => array( 'type' => 'string', 'enum' => array( 'low', 'medium', 'high' ) ),
					'transparent' => array( 'type' => 'boolean', 'description' => 'Transparent background (logos, icons) when the model supports it.' ),
					'reference_image_ids' => array( 'type' => 'array', 'items' => array( 'type' => 'integer' ), 'description' => 'Media Library images to edit or use as reference (models that support it).' ),
					'alt'         => array( 'type' => 'string', 'description' => 'Alt text in the site language: what the image shows, with the keyword if natural.' ),
					'title'       => array( 'type' => 'string' ),
					'caption'     => array( 'type' => 'string' ),
					'description' => array( 'type' => 'string' ),
					'filename'    => array( 'type' => 'string', 'description' => 'Short SEO file name without extension, e.g. "spa-da-nang-phong-tri-lieu".' ),
				),
				'required'   => array( 'prompt', 'purpose', 'alt' ),
			),
			'cap'         => 'upload_files',
			'callback'    => 'bst_aid_tool_imagegen',
		);
		$tools['image_status']   = array(
			'description' => 'Finish an ImageGen job that returned status "pending".',
			'parameters'  => array( 'type' => 'object', 'properties' => array( 'job_id' => array( 'type' => 'string' ) ), 'required' => array( 'job_id' ) ),
			'cap'         => 'upload_files',
			'callback'    => 'bst_aid_tool_image_status',
		);
		$tools['optimize_image'] = array(
			'description' => 'Optimize a Media Library image: resize to max_width, recompress, convert to WebP (only when the image is not used anywhere yet, so links do not break), and update title, alt, caption, description.',
			'parameters'  => array(
				'type'       => 'object',
				'properties' => array(
					'id'          => array( 'type' => 'integer' ),
					'max_width'   => array( 'type' => 'integer', 'minimum' => 200, 'maximum' => 3000 ),
					'quality'     => array( 'type' => 'integer', 'minimum' => 40, 'maximum' => 95 ),
					'webp'        => array( 'type' => 'boolean' ),
					'alt'         => array( 'type' => 'string' ),
					'title'       => array( 'type' => 'string' ),
					'caption'     => array( 'type' => 'string' ),
					'description' => array( 'type' => 'string' ),
				),
				'required'   => array( 'id' ),
			),
			'cap'         => 'upload_files',
			'callback'    => 'bst_aid_tool_optimize_image',
		);
		$tools['list_media']     = array(
			'description' => 'Find images in the Media Library (id, title, alt, URL, size). Use search to filter.',
			'parameters'  => array( 'type' => 'object', 'properties' => array( 'search' => array( 'type' => 'string' ), 'limit' => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 40 ) ) ),
			'cap'         => 'upload_files',
			'callback'    => 'bst_aid_tool_list_media',
		);
		return $tools;
	}
);

/** Prompt sent to the image model: guidelines, site colors, purpose, safety. */
function bst_aid_image_prompt( $prompt, $purpose ) {
	$g     = bst_aid_guidelines();
	$parts = array( trim( (string) $prompt ) );
	if ( '' !== trim( $g['images'] ) ) {
		$parts[] = 'Follow this image standard: ' . preg_replace( '/\s+/', ' ', $g['images'] );
	}
	$parts[] = 'Website brand colors: primary ' . bst_mod( 'color_primary' ) . ', secondary ' . bst_mod( 'color_secondary' ) . '.';
	$hints   = array(
		'banner'     => 'Wide hero banner with calm space for a headline on one side.',
		'background' => 'Subtle background with low detail so text stays readable.',
		'logo'       => 'Simple, flat, memorable logo mark, centered, plain background, works at small sizes.',
		'icon'       => 'Simple flat icon, centered, plain background.',
		'product'    => 'Product shot, centered, clean background.',
	);
	if ( isset( $hints[ $purpose ] ) ) {
		$parts[] = $hints[ $purpose ];
	}
	if ( 'logo' !== $purpose ) {
		$parts[] = 'No text, letters, watermarks or logos unless the description above asks for them.';
	}
	$parts[] = 'Do not depict real public figures or trademarked brands.';
	return mb_substr( implode( "\n", $parts ), 0, 3900 );
}

/** Add the task's spend for an image. */
function bst_aid_add_image_cost( $usd ) {
	$usd = (float) $usd;
	if ( $usd <= 0 ) {
		return;
	}
	bst_aid_add_spend( $usd );
	$task = (int) ( $GLOBALS['bst_aid_task_id'] ?? 0 );
	if ( $task ) {
		bst_aid_task_set( $task, '_bst_aid_cost', (float) bst_aid_task_get( $task, '_bst_aid_cost', 0 ) + $usd );
	}
}

function bst_aid_tool_imagegen( $a ) {
	$s     = bst_aid_settings();
	$model = (string) $s['image_model'];
	if ( '' === $model ) {
		return array( 'error' => 'No image model is chosen. Ask the user to pick one in AI Designer settings.' );
	}
	$purpose = array_key_exists( (string) ( $a['purpose'] ?? '' ), bst_aid_image_sizes() ) ? $a['purpose'] : 'other';
	$size    = preg_match( '/^\d{3,4}x\d{3,4}$/', (string) ( $a['size'] ?? '' ) ) ? $a['size'] : bst_aid_image_sizes()[ $purpose ];
	$body    = array(
		'model'           => $model,
		'prompt'          => bst_aid_image_prompt( $a['prompt'] ?? '', $purpose ),
		'size'            => $size,
		'n'               => 1,
		'idempotency_key' => substr( md5( wp_json_encode( $a ) . ( $GLOBALS['bst_aid_task_id'] ?? 0 ) . microtime() ), 0, 40 ),
	);
	if ( 0 === strpos( $model, 'gpt-image' ) ) {
		$body['quality'] = in_array( $a['quality'] ?? '', array( 'low', 'medium', 'high' ), true ) ? $a['quality'] : ( in_array( $purpose, array( 'banner', 'logo', 'product' ), true ) ? 'high' : 'medium' );
		if ( ! empty( $a['transparent'] ) ) {
			$body['extra'] = array( 'background' => 'transparent' );
		}
	}
	$refs = array();
	foreach ( array_slice( (array) ( $a['reference_image_ids'] ?? array() ), 0, 4 ) as $rid ) {
		$url = wp_get_attachment_url( (int) $rid );
		if ( $url && 0 === strpos( $url, 'https://' ) ) {
			$refs[] = $url;
		}
	}
	if ( $refs ) {
		$body['image_urls'] = $refs;
	}
	$res = bst_aid_http( 'POST', '/images/generations', $body, true, null, 60 );
	if ( empty( $res['ok'] ) ) {
		return array( 'error' => $res['error'] );
	}
	$job = (string) ( $res['data']['id'] ?? '' );
	if ( '' === $job ) {
		return array( 'error' => 'The image service did not return a job.' );
	}
	// Keep the media details with the job, so image_status can finish it later.
	set_transient( 'bst_aid_job_' . md5( $job ), array( 'args' => $a, 'model' => $model, 'purpose' => $purpose, 'estimate' => (float) ( $res['data']['estimated_cost'] ?? 0 ) ), DAY_IN_SECONDS );
	return bst_aid_wait_image( $job, 35 );
}

function bst_aid_tool_image_status( $a ) {
	return bst_aid_wait_image( (string) ( $a['job_id'] ?? '' ), 40 );
}

/** Poll a job for up to $seconds; on success download it into the Media Library. */
function bst_aid_wait_image( $job, $seconds ) {
	$meta = get_transient( 'bst_aid_job_' . md5( $job ) );
	if ( '' === $job || ! is_array( $meta ) ) {
		return array( 'error' => 'Unknown or expired image job.' );
	}
	$until = time() + $seconds;
	do {
		$r = bst_aid_http( 'GET', '/jobs/' . rawurlencode( $job ), null, true, null, 20 );
		if ( ! empty( $r['ok'] ) ) {
			$status = (string) ( $r['data']['status'] ?? '' );
			if ( 'succeeded' === $status ) {
				break;
			}
			if ( in_array( $status, array( 'failed', 'refunded', 'expired' ), true ) ) {
				delete_transient( 'bst_aid_job_' . md5( $job ) );
				$err = $r['data']['error'] ?? '';
				$err = is_array( $err ) ? (string) ( $err['message'] ?? $err['code'] ?? '' ) : (string) $err;
				return array( 'error' => 'The image could not be created (' . $status . '). ' . ( '' !== $err ? $err : 'Try a different description.' ) );
			}
		}
		sleep( 2 );
	} while ( time() < $until );
	if ( empty( $r['ok'] ) || 'succeeded' !== ( $r['data']['status'] ?? '' ) ) {
		return array( 'status' => 'pending', 'job_id' => $job, 'message' => 'Still creating the image. Call image_status with this job_id in a moment.' );
	}
	delete_transient( 'bst_aid_job_' . md5( $job ) );
	bst_aid_add_image_cost( (float) ( $r['data']['charged_amount'] ?? $meta['estimate'] ) );
	$url = (string) ( $r['data']['output']['url'] ?? '' );
	if ( '' === $url ) {
		return array( 'error' => 'The image service returned no image.' );
	}
	return bst_aid_import_image( $url, $meta['args'], $meta['model'], $meta['purpose'] );
}

/**
 * Download a result file to a temporary path. Kyma-hosted files are public, but if the host
 * answers 401/403 the request is repeated with the API key (only for kymaapi.com).
 *
 * @return string|WP_Error
 */
function bst_aid_download( $url ) {
	$tmp = download_url( $url, 90 );
	if ( ! is_wp_error( $tmp ) ) {
		return $tmp;
	}
	$host = (string) wp_parse_url( $url, PHP_URL_HOST );
	if ( ! preg_match( '/(^|\.)kymaapi\.com$/i', $host ) ) {
		return $tmp;
	}
	$file = wp_tempnam( 'bst-aid-image' );
	$res  = wp_remote_get( $url, array( 'timeout' => 90, 'stream' => true, 'filename' => $file, 'headers' => array( 'Authorization' => 'Bearer ' . bst_aid_key() ) ) );
	if ( is_wp_error( $res ) || 200 !== (int) wp_remote_retrieve_response_code( $res ) ) {
		wp_delete_file( $file ); // Temporary file only.
		return is_wp_error( $res ) ? $res : new WP_Error( 'download', 'HTTP ' . wp_remote_retrieve_response_code( $res ) );
	}
	return $file;
}

/** Download an image URL, convert to WebP, add to the Media Library with full SEO fields. */
function bst_aid_import_image( $url, $a, $model, $purpose ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$tmp = bst_aid_download( $url );
	if ( is_wp_error( $tmp ) ) {
		return array( 'error' => 'Download failed: ' . $tmp->get_error_message() );
	}
	$type = wp_get_image_mime( $tmp );
	if ( ! $type || 'image/svg+xml' === $type ) {
		wp_delete_file( $tmp ); // Temporary download only.
		return array( 'error' => 'The result is not a supported image (SVG is not allowed in the Media Library). Choose a raster image model.' );
	}
	$name = sanitize_file_name( (string) ( $a['filename'] ?? '' ) );
	$name = '' !== $name ? $name : sanitize_title( wp_trim_words( (string) ( $a['alt'] ?? 'ai-image' ), 8, '' ) );
	$name = '' !== $name ? $name : 'ai-image';
	$max  = in_array( $purpose, array( 'logo', 'icon', 'product' ), true ) ? 1200 : 2400;
	$file = $tmp;
	$ext  = str_replace( 'image/', '', $type );
	$ext  = 'jpeg' === $ext ? 'jpg' : $ext;
	$ed   = wp_get_image_editor( $tmp );
	if ( ! is_wp_error( $ed ) ) {
		$size = $ed->get_size();
		if ( ! empty( $size['width'] ) && $size['width'] > $max ) {
			$ed->resize( $max, null );
		}
		$ed->set_quality( 82 );
		if ( $ed->supports_mime_type( 'image/webp' ) ) {
			$saved = $ed->save( $tmp . '.webp', 'image/webp' );
			if ( ! is_wp_error( $saved ) ) {
				wp_delete_file( $tmp ); // Temporary download only.
				$file = $saved['path'];
				$ext  = 'webp';
			}
		}
	}
	$title = sanitize_text_field( bst_aid_clean_plain( (string) ( $a['title'] ?? $a['alt'] ?? '' ) ) );
	$a['alt']     = bst_aid_clean_plain( (string) ( $a['alt'] ?? '' ) );
	$a['caption'] = bst_aid_clean_plain( (string) ( $a['caption'] ?? '' ) );
	$id    = media_handle_sideload( array( 'name' => $name . '.' . $ext, 'tmp_name' => $file ), 0, $title );
	if ( is_wp_error( $id ) ) {
		wp_delete_file( $file ); // Temporary file only.
		return array( 'error' => 'Saving to the Media Library failed: ' . $id->get_error_message() );
	}
	$desc = trim( sanitize_textarea_field( (string) ( $a['description'] ?? '' ) ) );
	$desc = ( '' !== $desc ? $desc . "\n\n" : '' ) . sprintf( /* translators: %s: model id */ __( 'AI-generated image (%s) – BIGSPY AI Designer.', 'bigspy-ai-theme-wp' ), $model );
	wp_update_post( array( 'ID' => $id, 'post_title' => $title, 'post_excerpt' => sanitize_text_field( (string) ( $a['caption'] ?? '' ) ), 'post_content' => $desc ) );
	update_post_meta( $id, '_wp_attachment_image_alt', sanitize_text_field( (string) ( $a['alt'] ?? '' ) ) );
	update_post_meta( $id, '_bst_aid_generated', $model );
	update_post_meta( $id, '_bst_aid_task', (int) ( $GLOBALS['bst_aid_task_id'] ?? 0 ) );
	return bst_aid_media_info( $id ) + array( 'links' => array( array( 'label' => $title, 'edit' => admin_url( 'post.php?post=' . $id . '&action=edit' ), 'view' => wp_get_attachment_url( $id ), 'image' => wp_get_attachment_image_url( $id, 'medium' ) ) ) );
}

function bst_aid_media_info( $id ) {
	$meta = wp_get_attachment_metadata( $id );
	return array(
		'id'     => (int) $id,
		'url'    => wp_get_attachment_url( $id ),
		'large'  => wp_get_attachment_image_url( $id, 'large' ),
		'medium' => wp_get_attachment_image_url( $id, 'medium' ),
		'width'  => (int) ( $meta['width'] ?? 0 ),
		'height' => (int) ( $meta['height'] ?? 0 ),
		'alt'    => (string) get_post_meta( $id, '_wp_attachment_image_alt', true ),
		'title'  => get_the_title( $id ),
	);
}

/** Whether an image is used: content, featured images, product galleries, logo, icon, theme settings. */
function bst_aid_image_in_use( $id ) {
	global $wpdb;
	$id   = (int) $id;
	$url  = (string) wp_get_attachment_url( $id );
	$base = $url ? preg_replace( '/\.[a-z0-9]+$/i', '', wp_basename( $url ) ) : '';
	if ( (int) get_theme_mod( 'custom_logo' ) === $id || (int) get_option( 'site_icon' ) === $id ) {
		return true;
	}
	// phpcs:disable WordPress.DB.DirectDatabaseQuery
	$meta = $wpdb->get_var( $wpdb->prepare( "SELECT meta_id FROM {$wpdb->postmeta} WHERE (meta_key = '_thumbnail_id' AND meta_value = %s) OR (meta_key = '_product_image_gallery' AND FIND_IN_SET(%s, meta_value)) LIMIT 1", (string) $id, (string) $id ) );
	if ( $meta ) {
		return true;
	}
	$like  = array( '%wp-image-' . $id . '%', '%"imageId":' . $id . '%', '%Id":' . $id . ',%', '%Id":' . $id . '}%' );
	$where = implode( ' OR ', array_fill( 0, count( $like ), 'post_content LIKE %s' ) );
	if ( $base ) {
		$where .= ' OR post_content LIKE %s';
		$like[] = '%' . $wpdb->esc_like( $base ) . '%';
	}
	$hit = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type NOT IN ('revision','attachment','bst_aid_task') AND post_status <> 'trash' AND ( $where ) LIMIT 1", $like ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	// phpcs:enable
	if ( $hit ) {
		return true;
	}
	return $base && false !== strpos( wp_json_encode( get_theme_mods() ), $base );
}

function bst_aid_tool_optimize_image( $a ) {
	$id = (int) ( $a['id'] ?? 0 );
	if ( ! wp_attachment_is_image( $id ) || ! current_user_can( 'edit_post', $id ) ) {
		return array( 'error' => 'Not an editable image.' );
	}
	$notes = array();
	$data  = array( 'ID' => $id );
	foreach ( array( 'title' => 'post_title', 'caption' => 'post_excerpt', 'description' => 'post_content' ) as $k => $col ) {
		if ( isset( $a[ $k ] ) ) {
			$data[ $col ] = 'description' === $k ? sanitize_textarea_field( (string) $a[ $k ] ) : sanitize_text_field( (string) $a[ $k ] );
		}
	}
	if ( count( $data ) > 1 ) {
		wp_update_post( $data );
	}
	if ( isset( $a['alt'] ) ) {
		update_post_meta( $id, '_wp_attachment_image_alt', sanitize_text_field( (string) $a['alt'] ) );
	}
	if ( ! empty( $a['max_width'] ) || ! empty( $a['quality'] ) || ! empty( $a['webp'] ) ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$path = get_attached_file( $id );
		$ed   = $path ? wp_get_image_editor( $path ) : new WP_Error( 'file', 'File missing.' );
		if ( is_wp_error( $ed ) ) {
			return array( 'error' => $ed->get_error_message() );
		}
		$size = $ed->get_size();
		if ( ! empty( $a['max_width'] ) && $size['width'] > (int) $a['max_width'] ) {
			$ed->resize( (int) $a['max_width'], null );
		}
		$ed->set_quality( ! empty( $a['quality'] ) ? (int) $a['quality'] : 82 );
		$to_webp = ! empty( $a['webp'] ) && 'image/webp' !== get_post_mime_type( $id );
		if ( $to_webp && bst_aid_image_in_use( $id ) ) {
			$to_webp = false;
			$notes[] = 'Kept the original format because the image is already used; converting would break its links.';
		}
		$target = $to_webp ? preg_replace( '/\.[a-z0-9]+$/i', '.webp', $path ) : $path;
		$saved  = $ed->save( $target, $to_webp ? 'image/webp' : null );
		if ( is_wp_error( $saved ) ) {
			return array( 'error' => $saved->get_error_message() );
		}
		if ( $to_webp ) {
			update_attached_file( $id, $saved['path'] );
			wp_update_post( array( 'ID' => $id, 'post_mime_type' => 'image/webp' ) );
		}
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $saved['path'] ) );
	}
	return bst_aid_media_info( $id ) + array( 'notes' => $notes );
}

function bst_aid_tool_list_media( $a ) {
	$q   = get_posts( array( 'post_type' => 'attachment', 'post_mime_type' => 'image', 'post_status' => 'inherit', 'numberposts' => max( 1, min( 40, (int) ( $a['limit'] ?? 20 ) ) ), 's' => (string) ( $a['search'] ?? '' ) ) );
	$out = array();
	foreach ( $q as $p ) {
		$out[] = bst_aid_media_info( $p->ID );
	}
	return array( 'images' => $out );
}

/* ------------------------------------------------------------------ Unused generated images */

add_action(
	'rest_api_init',
	function () {
		register_rest_route( bst_aid_rest_ns(), '/images/unused', array( 'methods' => 'GET', 'callback' => 'bst_aid_rest_unused_images', 'permission_callback' => 'bst_aid_rest_can_use' ) );
		register_rest_route( bst_aid_rest_ns(), '/images/test', array( 'methods' => 'POST', 'callback' => 'bst_aid_rest_image_test', 'permission_callback' => 'bst_aid_rest_can_manage' ) );
		register_rest_route( bst_aid_rest_ns(), '/images/job', array( 'methods' => 'POST', 'callback' => 'bst_aid_rest_image_job', 'permission_callback' => 'bst_aid_rest_can_manage' ) );
	}
);

/** Settings → Test ImageGen: one real image with the chosen model, into the Media Library. */
function bst_aid_rest_image_test() {
	if ( '' === bst_aid_key() || ! bst_aid_settings()['connected'] ) {
		return new WP_Error( 'bst_aid_ready', __( 'The API is not connected. Connect it in AI Designer settings.', 'bigspy-ai-theme-wp' ), array( 'status' => 400 ) );
	}
	@set_time_limit( 120 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	$r = bst_aid_tool_imagegen(
		array(
			'prompt'   => 'A bright, modern workspace desk with a laptop showing a colorful website design, a small plant and a coffee cup, soft morning light, clean minimal style, photo realistic.',
			'purpose'  => 'card',
			'quality'  => 'low',
			'alt'      => __( 'BIGSPY AI Designer test image: a modern desk with a laptop', 'bigspy-ai-theme-wp' ),
			'title'    => __( 'BIGSPY AI Designer test image', 'bigspy-ai-theme-wp' ),
			'caption'  => __( 'Test image created by ImageGen', 'bigspy-ai-theme-wp' ),
			'filename' => 'bigspy-ai-designer-test',
		)
	);
	return rest_ensure_response( $r );
}

function bst_aid_rest_image_job( WP_REST_Request $request ) {
	@set_time_limit( 90 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	return rest_ensure_response( bst_aid_wait_image( (string) $request->get_param( 'job_id' ), 40 ) );
}

function bst_aid_rest_unused_images() {
	$ids = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'numberposts' => 200, 'fields' => 'ids', 'meta_key' => '_bst_aid_generated' ) ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
	$out = array();
	foreach ( $ids as $id ) {
		if ( ! bst_aid_image_in_use( $id ) ) {
			$out[] = array( 'id' => $id, 'title' => get_the_title( $id ), 'thumb' => wp_get_attachment_image_url( $id, 'thumbnail' ), 'edit' => admin_url( 'post.php?post=' . $id . '&action=edit' ) );
		}
	}
	return rest_ensure_response( array( 'total' => count( $ids ), 'unused' => $out ) );
}
