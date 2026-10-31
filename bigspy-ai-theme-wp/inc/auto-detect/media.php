<?php
/**
 * Unused media. Any doubt counts as used: a false "used" costs disk space, a false "unused" loses a file.
 * A scan stores used/unused on each attachment so the Media Library can show and filter it.
 * Files only ever go to the trash, where they stay on disk and can be restored from the Auto detect page.
 */
defined( 'ABSPATH' ) || exit;

define( 'BST_AD_MEDIA_META', '_bst_ad_media' );
define( 'BST_AD_MEDIA_KEYS', '/(^|[_\-])ids?$|bg|img|image|gallery|logo|icon|thumb|media|attach|photo|banner|poster|background|avatar|picture/i' );

add_filter(
	'bst_ad_modules',
	function ( $modules ) {
		$modules['media'] = array(
			'label'  => __( 'Unused media', 'bigspy-ai-theme-wp' ),
			'intro'  => __( 'Searches content, blocks, shortcodes, featured images, galleries, widgets, theme settings, plugin settings and all metadata, in every image size and WebP copy. Files hard-coded in templates may still be listed, so check the thumbnails before deleting.', 'bigspy-ai-theme-wp' ),
			'step'   => 'bst_ad_media_step',
			'action' => 'bst_ad_media_action',
		);
		return $modules;
	}
);

/** One key for every size and format of an upload. */
function bst_ad_media_path( $path ) {
	$path = strtolower( rawurldecode( str_replace( '\\/', '/', (string) $path ) ) );
	$path = preg_replace( '/[?#].*$/', '', $path );
	$path = preg_replace( '#^.*?uploads/#', '', $path );
	$path = preg_replace( '/\.(jpe?g|png|gif|bmp|tiff?)\.webp$/', '.$1', $path );
	$path = preg_replace( '/\.[a-z0-9]+$/', '', $path );
	do {
		$before = $path;
		$path   = preg_replace( '/-(\d+x\d+|scaled|rotated|e\d{10,13})$/', '', $path );
	} while ( $path !== $before );
	return $path;
}

function bst_ad_media_add_ids( $list, &$data ) {
	foreach ( preg_split( '/\D+/', (string) $list, -1, PREG_SPLIT_NO_EMPTY ) as $id ) {
		$data['ids'][ (int) $id ] = 1;
	}
}

/** Collect attachment IDs and upload paths referenced by a text. */
function bst_ad_media_collect( $text, &$data ) {
	$text = str_replace( array( '\\/', '&quot;', '&#8221;', '&#8243;', '&#039;' ), array( '/', '"', '"', '"', "'" ), (string) $text );
	if ( preg_match_all( '/(?:wp-image-|attachment_|wp-att-|attachment_id=)(\d+)/', $text, $m ) ) {
		bst_ad_media_add_ids( implode( ',', $m[1] ), $data );
	}
	if ( preg_match_all( '/\w*[iI][dD]s?\w*["\']?\s*[:=]\s*[\[\\\\"\']*([\d,\s"]+)/', $text, $m ) ) {
		bst_ad_media_add_ids( implode( ',', $m[1] ), $data );
	}
	// Shortcode and HTML attributes such as bg="34", img="56", gallery="1,2".
	if ( preg_match_all( '/\b[a-z_\-]*(?:bg|img|images?|gallery|logo|icon|thumbnail|thumb|media|attachment|photo|banner|poster|background)["\']?\s*[=:]\s*["\']?\s*(\d[\d,\s]*)/i', $text, $m ) ) {
		bst_ad_media_add_ids( implode( ',', $m[1] ), $data );
	}
	if ( preg_match_all( '#uploads/[^\s"\'()<>\[\]\\\\,]+?\.[a-z0-9]{2,5}(?![a-z0-9])#i', $text, $m ) ) {
		foreach ( $m[0] as $path ) {
			$data['paths'][ bst_ad_media_path( $path ) ] = 1;
		}
	}
}

/** Walk serialized, JSON or plain values; bare numbers count only under a media-like key. */
function bst_ad_media_walk( $value, $key, &$data, $depth = 0 ) {
	if ( $depth > 25 ) {
		return;
	}
	if ( is_object( $value ) ) {
		$value = (array) $value;
	}
	if ( is_array( $value ) ) {
		foreach ( $value as $k => $item ) {
			bst_ad_media_walk( $item, is_string( $k ) && ! is_numeric( $k ) ? $k : $key, $data, $depth + 1 );
		}
		return;
	}
	if ( is_int( $value ) || ( is_string( $value ) && preg_match( '/^\s*\d+(\s*,\s*\d+)*\s*$/', $value ) ) ) {
		if ( preg_match( BST_AD_MEDIA_KEYS, (string) $key ) ) {
			bst_ad_media_add_ids( $value, $data );
		}
		return;
	}
	if ( ! is_string( $value ) || '' === $value ) {
		return;
	}
	if ( is_serialized( $value ) ) {
		$plain = @unserialize( trim( $value ), array( 'allowed_classes' => false ) ); // phpcs:ignore
		if ( false !== $plain ) {
			bst_ad_media_walk( $plain, $key, $data, $depth + 1 );
			return;
		}
	}
	if ( '{' === $value[0] || '[' === $value[0] ) {
		$json = json_decode( $value, true );
		if ( is_array( $json ) ) {
			bst_ad_media_walk( $json, $key, $data, $depth + 1 );
		}
	}
	bst_ad_media_collect( $value, $data );
}

function bst_ad_media_skip_meta( $key ) {
	return in_array( $key, array( '_wp_attached_file', '_wp_attachment_metadata', '_wp_attachment_backup_sizes', BST_AD_MEDIA_META, '_plmc_status', '_edit_lock', '_edit_last', '_wp_old_slug', '_wp_old_date', '_wp_trash_meta_status', '_wp_trash_meta_time' ), true );
}

function bst_ad_media_step( $state ) {
	global $wpdb;
	$data = wp_parse_args( $state['data'], array( 'ids' => array(), 'paths' => array() ) );
	// phpcs:disable WordPress.DB.DirectDatabaseQuery
	if ( 0 === $state['phase'] ) {
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_content, post_excerpt FROM {$wpdb->posts} WHERE ID > %d AND post_type NOT IN ('attachment','revision') AND post_status <> 'auto-draft' ORDER BY ID ASC LIMIT 100", $state['cursor'] ) );
		foreach ( $rows as $row ) {
			bst_ad_media_collect( $row->post_content . ' ' . $row->post_excerpt, $data );
			$state['cursor'] = (int) $row->ID;
			$state['done']++;
		}
		if ( count( $rows ) < 100 ) {
			$state['phase']  = 1;
			$state['cursor'] = 0;
		}
	} elseif ( 1 === $state['phase'] ) {
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT m.meta_id, m.meta_key, m.meta_value FROM {$wpdb->postmeta} m JOIN {$wpdb->posts} p ON p.ID = m.post_id WHERE m.meta_id > %d AND p.post_type <> 'revision' ORDER BY m.meta_id ASC LIMIT 500", $state['cursor'] ) );
		foreach ( $rows as $row ) {
			$state['cursor'] = (int) $row->meta_id;
			if ( ! bst_ad_media_skip_meta( $row->meta_key ) ) {
				bst_ad_media_walk( $row->meta_value, $row->meta_key, $data );
			}
		}
		if ( count( $rows ) < 500 ) {
			$state['phase']  = 2;
			$state['cursor'] = 0;
		}
	} elseif ( 2 === $state['phase'] ) {
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT meta_id, meta_key, meta_value FROM {$wpdb->termmeta} WHERE meta_id > %d ORDER BY meta_id ASC LIMIT 500", $state['cursor'] ) );
		foreach ( $rows as $row ) {
			$state['cursor'] = (int) $row->meta_id;
			bst_ad_media_walk( $row->meta_value, $row->meta_key, $data );
		}
		if ( count( $rows ) < 500 ) {
			$state['phase']  = 3;
			$state['cursor'] = 0;
		}
	} elseif ( 3 === $state['phase'] ) {
		// Every option, so plugin settings and page builders count. This module's own data must stay out.
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT option_id, option_name, option_value FROM {$wpdb->options} WHERE option_id > %d AND option_name NOT LIKE %s AND option_name NOT LIKE %s AND option_name NOT LIKE %s AND option_name NOT LIKE %s AND option_name NOT IN ('cron','rewrite_rules','recently_edited') ORDER BY option_id ASC LIMIT 200", $state['cursor'], $wpdb->esc_like( '_transient' ) . '%', $wpdb->esc_like( '_site_transient' ) . '%', $wpdb->esc_like( 'plmc_' ) . '%', $wpdb->esc_like( 'bst_ad_' ) . '%' ) );
		foreach ( $rows as $row ) {
			$state['cursor'] = (int) $row->option_id;
			bst_ad_media_walk( $row->option_value, $row->option_name, $data );
		}
		if ( count( $rows ) < 200 ) {
			foreach ( $wpdb->get_results( "SELECT meta_key, meta_value FROM {$wpdb->usermeta} WHERE meta_key LIKE '%avatar%' OR meta_key LIKE '%image%' OR meta_key LIKE '%photo%'" ) as $row ) {
				bst_ad_media_walk( $row->meta_value, $row->meta_key, $data );
			}
			$state['phase']  = 4;
			$state['cursor'] = 0;
			$state['done']   = 0;
			$state['total']  = (int) array_sum( (array) wp_count_attachments() );
		}
	} else {
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT p.ID, m.meta_value AS file FROM {$wpdb->posts} p LEFT JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = '_wp_attached_file' WHERE p.ID > %d AND p.post_type = 'attachment' AND p.post_status <> 'trash' ORDER BY p.ID ASC LIMIT 100", $state['cursor'] ) );
		foreach ( $rows as $row ) {
			$id              = (int) $row->ID;
			$state['cursor'] = $id;
			$used            = ! empty( $data['ids'][ $id ] ) || ( $row->file && ! empty( $data['paths'][ bst_ad_media_path( $row->file ) ] ) );
			update_post_meta( $id, BST_AD_MEDIA_META, $used ? 'used' : 'unused' );
			if ( ! $used ) {
				$state['items'][] = bst_ad_media_item( $id );
			}
			$state['done']++;
		}
		if ( count( $rows ) < 100 ) {
			$state['status'] = 'done';
			$bytes           = array_sum( wp_list_pluck( $state['items'], 'bytes' ) );
			/* translators: 1: file count, 2: total size. */
			$state['note'] = sprintf( __( '%1$d files, %2$s in total.', 'bigspy-ai-theme-wp' ), count( $state['items'] ), size_format( $bytes ) );
		}
	}
	// phpcs:enable
	$state['data'] = $data;
	return $state;
}

function bst_ad_media_thumb( $id ) {
	$url = wp_get_attachment_image_url( $id, 'thumbnail' );
	return $url ? $url : (string) wp_mime_type_icon( $id );
}

function bst_ad_media_item( $id ) {
	$path  = (string) get_attached_file( $id );
	$bytes = $path && file_exists( $path ) ? (int) filesize( $path ) : 0;
	$meta  = wp_get_attachment_metadata( $id );
	foreach ( (array) ( $meta['sizes'] ?? array() ) as $size ) {
		$bytes += (int) ( $size['filesize'] ?? 0 );
	}
	return array(
		'key'      => 'media-' . $id,
		'severity' => 'review',
		'title'    => get_the_title( $id ) . ' (' . size_format( $bytes ) . ')',
		'detail'   => basename( $path ),
		'posts'    => array(),
		'select'   => $id,
		'thumb'    => bst_ad_media_thumb( $id ),
		'edit'     => get_edit_post_link( $id, 'raw' ),
		'ignore'   => true,
		'bytes'    => $bytes,
	);
}

/** Fresh look right before trashing: the scan may be old, so any trace of the file blocks it. */
function bst_ad_media_used_now( $id ) {
	global $wpdb;
	$id   = (int) $id;
	$file = (string) get_post_meta( $id, '_wp_attached_file', true );
	$base = $file ? basename( bst_ad_media_path( $file ) ) : '';
	$like = '' !== $base ? '%' . $wpdb->esc_like( $base ) . '%' : '';
	// phpcs:disable WordPress.DB.DirectDatabaseQuery
	if ( $wpdb->get_var( $wpdb->prepare( "SELECT meta_id FROM {$wpdb->postmeta} WHERE meta_key IN ('_thumbnail_id','_product_image_gallery') AND ( meta_value = %s OR meta_value LIKE %s OR meta_value LIKE %s OR meta_value LIKE %s ) LIMIT 1", (string) $id, $id . ',%', '%,' . $id, '%,' . $id . ',%' ) ) ) {
		return true;
	}
	if ( $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type NOT IN ('attachment','revision') AND post_status <> 'auto-draft' AND post_content LIKE %s LIMIT 1", '%wp-image-' . $id . '%' ) ) ) {
		return true;
	}
	if ( '' === $like ) {
		return true;
	}
	if ( $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type NOT IN ('attachment','revision') AND post_status <> 'auto-draft' AND post_content LIKE %s LIMIT 1", $like ) ) ) {
		return true;
	}
	if ( $wpdb->get_var( $wpdb->prepare( "SELECT m.meta_id FROM {$wpdb->postmeta} m WHERE m.post_id <> %d AND m.meta_value LIKE %s LIMIT 1", $id, $like ) ) ) {
		return true;
	}
	$hit = $wpdb->get_var( $wpdb->prepare( "SELECT option_id FROM {$wpdb->options} WHERE option_value LIKE %s AND option_name NOT LIKE %s AND option_name NOT LIKE %s AND option_name NOT LIKE %s AND option_name NOT LIKE %s LIMIT 1", $like, $wpdb->esc_like( '_transient' ) . '%', $wpdb->esc_like( '_site_transient' ) . '%', $wpdb->esc_like( 'plmc_' ) . '%', $wpdb->esc_like( 'bst_ad_' ) . '%' ) );
	// phpcs:enable
	return (bool) $hit;
}

/** Move one file to the trash if every safety check passes. */
function bst_ad_media_trash( $id ) {
	$id = (int) $id;
	if ( ! EMPTY_TRASH_DAYS || 'attachment' !== get_post_type( $id ) || 'trash' === get_post_status( $id ) || ! current_user_can( 'delete_post', $id ) ) {
		return false;
	}
	$ignored = bst_ad_ignored( 'media' );
	if ( 'unused' !== get_post_meta( $id, BST_AD_MEDIA_META, true ) || ! empty( $ignored[ 'media-' . $id ] ) ) {
		return false;
	}
	if ( bst_ad_media_used_now( $id ) ) {
		update_post_meta( $id, BST_AD_MEDIA_META, 'used' );
		return false;
	}
	return (bool) wp_trash_post( $id );
}

function bst_ad_media_trashed() {
	$ids = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'trash', 'numberposts' => 300, 'orderby' => 'ID', 'order' => 'DESC', 'fields' => 'ids' ) );
	$out = array();
	foreach ( $ids as $id ) {
		$out[] = array( 'id' => $id, 'title' => get_the_title( $id ), 'thumb' => bst_ad_media_thumb( $id ) );
	}
	return $out;
}

add_filter(
	'bst_ad_public_extra',
	function ( $extra, $id ) {
		return 'media' === $id ? array( 'trashed' => bst_ad_media_trashed() ) : $extra;
	},
	10,
	2
);

function bst_ad_media_untrash_status( $status, $id, $previous ) {
	return 'attachment' === get_post_type( $id ) ? $previous : $status;
}

/** Trash files, or restore or purge files already in the trash. Permanent deletion works on trashed files only. */
function bst_ad_media_action( WP_REST_Request $request ) {
	$op = (string) $request->get_param( 'op' );
	if ( ! in_array( $op, array( 'delete', 'restore', 'purge' ), true ) ) {
		return new WP_Error( 'bst_ad_op', 'Unknown action', array( 'status' => 400 ) );
	}
	if ( 'delete' === $op && ! EMPTY_TRASH_DAYS ) {
		return new WP_Error( 'bst_ad_trash', __( 'The WordPress trash is turned off (EMPTY_TRASH_DAYS), so nothing was deleted.', 'bigspy-ai-theme-wp' ), array( 'status' => 409 ) );
	}
	$ids   = wp_parse_id_list( $request->get_param( 'ids' ) );
	$state = bst_ad_state( 'media' );
	$gone  = array();
	foreach ( $ids as $id ) {
		if ( 'delete' === $op ) {
			if ( bst_ad_media_trash( $id ) ) {
				$gone[] = $id;
			} elseif ( 'used' === get_post_meta( $id, BST_AD_MEDIA_META, true ) ) {
				$gone[] = $id; // No longer unused: leaves the list, the file stays.
			}
		} elseif ( 'attachment' === get_post_type( $id ) && 'trash' === get_post_status( $id ) && current_user_can( 'delete_post', $id ) ) {
			if ( 'purge' === $op ) {
				wp_delete_attachment( $id, true );
			} else {
				add_filter( 'wp_untrash_post_status', 'bst_ad_media_untrash_status', 10, 3 );
				wp_untrash_post( $id );
				remove_filter( 'wp_untrash_post_status', 'bst_ad_media_untrash_status', 10 );
				delete_post_meta( $id, BST_AD_MEDIA_META ); // Unknown again until the next scan.
			}
		}
	}
	$state['items'] = array_values(
		array_filter(
			$state['items'],
			function ( $item ) use ( $gone ) {
				return ! in_array( (int) $item['select'], $gone, true );
			}
		)
	);
	bst_ad_save_state( 'media', $state );
	return true;
}

/* ------------------------------------------------------------ Media Library */

// New uploads stay unknown until the next scan, so they can never be trashed unseen.
add_action(
	'add_attachment',
	function ( $id ) {
		delete_post_meta( $id, BST_AD_MEDIA_META );
	}
);

function bst_ad_media_status( $id ) {
	$status = (string) get_post_meta( $id, BST_AD_MEDIA_META, true );
	return in_array( $status, array( 'used', 'unused' ), true ) ? $status : '';
}

add_filter(
	'wp_prepare_attachment_for_js',
	function ( $response, $attachment ) {
		$status = bst_ad_media_status( $attachment->ID );
		if ( $status ) {
			$response['bstUsage'] = $status;
		}
		return $response;
	},
	10,
	2
);

add_filter(
	'manage_media_columns',
	function ( $columns ) {
		$columns['bst_usage'] = __( 'Usage', 'bigspy-ai-theme-wp' );
		return $columns;
	}
);

add_action(
	'manage_media_custom_column',
	function ( $column, $id ) {
		if ( 'bst_usage' === $column ) {
			$status = bst_ad_media_status( $id );
			echo $status ? '<span class="bst-usage bst-usage-' . esc_attr( $status ) . '" data-bst-usage="' . esc_attr( $status ) . '"></span> ' . esc_html( 'used' === $status ? __( 'In use', 'bigspy-ai-theme-wp' ) : __( 'Unused', 'bigspy-ai-theme-wp' ) ) : '—';
		}
	},
	10,
	2
);

function bst_ad_media_filter() {
	$value = isset( $_REQUEST['bst_usage'] ) ? sanitize_key( wp_unslash( $_REQUEST['bst_usage'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return in_array( $value, array( 'used', 'unused' ), true ) ? $value : '';
}

add_action(
	'restrict_manage_posts',
	function ( $type ) {
		if ( 'attachment' === $type ) {
			$value = bst_ad_media_filter();
			echo '<select name="bst_usage"><option value="">' . esc_html__( 'All usage', 'bigspy-ai-theme-wp' ) . '</option>';
			echo '<option value="unused"' . selected( $value, 'unused', false ) . '>' . esc_html__( 'Unused', 'bigspy-ai-theme-wp' ) . '</option>';
			echo '<option value="used"' . selected( $value, 'used', false ) . '>' . esc_html__( 'In use', 'bigspy-ai-theme-wp' ) . '</option></select>';
		}
	}
);

add_filter(
	'views_upload',
	function ( $views ) {
		$views['bst_unused'] = '<a href="' . esc_url( admin_url( 'upload.php?mode=list&bst_usage=unused' ) ) . '"' . ( 'unused' === bst_ad_media_filter() ? ' class="current"' : '' ) . '>' . esc_html__( 'Unused', 'bigspy-ai-theme-wp' ) . '</a>';
		return $views;
	}
);

add_action(
	'pre_get_posts',
	function ( $query ) {
		global $pagenow;
		$value = bst_ad_media_filter();
		if ( $value && is_admin() && $query->is_main_query() && 'upload.php' === $pagenow ) {
			$query->set( 'meta_query', array( array( 'key' => BST_AD_MEDIA_META, 'value' => $value ) ) );
		}
	}
);

add_filter(
	'ajax_query_attachments_args',
	function ( $args ) {
		$value = isset( $_REQUEST['query']['bst_usage'] ) ? sanitize_key( wp_unslash( $_REQUEST['query']['bst_usage'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( in_array( $value, array( 'used', 'unused' ), true ) ) {
			$args['meta_query'] = array( array( 'key' => BST_AD_MEDIA_META, 'value' => $value ) ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		}
		return $args;
	}
);

add_filter(
	'bulk_actions-upload',
	function ( $actions ) {
		$actions['bst_trash_unused'] = __( 'Move unused to trash', 'bigspy-ai-theme-wp' );
		return $actions;
	}
);

add_filter(
	'handle_bulk_actions-upload',
	function ( $redirect, $action, $ids ) {
		if ( 'bst_trash_unused' !== $action ) {
			return $redirect;
		}
		$moved = 0;
		foreach ( $ids as $id ) {
			$moved += bst_ad_media_trash( $id ) ? 1 : 0;
		}
		return add_query_arg( array( 'bst_trashed' => $moved, 'bst_skipped' => count( $ids ) - $moved ), $redirect );
	},
	10,
	3
);

add_action(
	'admin_notices',
	function () {
		if ( isset( $_GET['bst_trashed'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			/* translators: 1: moved files, 2: skipped files. */
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( sprintf( __( '%1$d files moved to trash, %2$d kept because they are in use or not scanned yet. Restore them on the Auto detect page.', 'bigspy-ai-theme-wp' ), absint( $_GET['bst_trashed'] ), absint( $_GET['bst_skipped'] ?? 0 ) ) ) . '</p></div>'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
	}
);

add_action(
	'admin_enqueue_scripts',
	function ( $hook ) {
		if ( 'upload.php' !== $hook ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_script( 'bst-media-usage', BST_URI . '/assets/js/media-usage.js', array( 'jquery', 'media-views' ), BST_VERSION, true );
		wp_localize_script( 'bst-media-usage', 'bstMediaUsage', array( 'initial' => bst_ad_media_filter(), 'all' => __( 'All usage', 'bigspy-ai-theme-wp' ), 'used' => __( 'In use', 'bigspy-ai-theme-wp' ), 'unused' => __( 'Unused', 'bigspy-ai-theme-wp' ) ) );
		wp_register_style( 'bst-media-usage', false, array(), BST_VERSION );
		wp_enqueue_style( 'bst-media-usage' );
		wp_add_inline_style( 'bst-media-usage', '.bst-usage{display:inline-block;width:18px;height:18px;line-height:18px;border-radius:50%;color:#fff;font-size:12px;font-weight:700;text-align:center;vertical-align:middle}.bst-usage-used{background:#1e9e4a}.bst-usage-used:before{content:"\2713"}.bst-usage-unused{background:#d63638}.bst-usage-unused:before{content:"\2715"}.attachment-preview .bst-usage{position:absolute;top:6px;left:6px;z-index:3;box-shadow:0 0 0 2px #fff}.media-icon{position:relative}.media-icon .bst-usage{position:absolute;top:-4px;left:-4px;box-shadow:0 0 0 2px #fff}.column-bst_usage{width:110px}#bst-usage-grid{margin-left:6px}' );
	}
);
