<?php
/** Unused media. Any doubt counts as used: a false "used" costs disk space, a false "unused" loses a file. */
defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_ad_modules',
	function ( $modules ) {
		$modules['media'] = array(
			'label'  => __( 'Unused media', 'bigspy-ai-theme-wp' ),
			'intro'  => __( 'Searches content, blocks, featured images, galleries, widgets and theme settings. Files used only by plugins or hard-coded in templates may still be listed, so check before deleting.', 'bigspy-ai-theme-wp' ),
			'step'   => 'bst_ad_media_step',
			'action' => 'bst_ad_media_action',
		);
		return $modules;
	}
);

/** Collect attachment IDs and upload paths referenced by a text. */
function bst_ad_media_collect( $text, &$data ) {
	$text = str_replace( '\\/', '/', (string) $text );
	if ( preg_match_all( '/(?:wp-image-|attachment_|wp-att-)(\d+)/', $text, $m ) ) {
		foreach ( $m[1] as $id ) {
			$data['ids'][ (int) $id ] = 1;
		}
	}
	if ( preg_match_all( '/\w*[iI][dD]s?\w*["\']?\s*[:=]\s*[\[\\\\"\']*([\d,\s"]+)/', $text, $m ) ) {
		foreach ( $m[1] as $list ) {
			foreach ( preg_split( '/[^\d]+/', $list, -1, PREG_SPLIT_NO_EMPTY ) as $id ) {
				$data['ids'][ (int) $id ] = 1;
			}
		}
	}
	if ( preg_match_all( '#uploads/([^\s"\'()<>\\\\]+?)(?:-\d+x\d+)?\.([a-z0-9]{2,5})#i', $text, $m, PREG_SET_ORDER ) ) {
		foreach ( $m as $hit ) {
			$data['paths'][ strtolower( $hit[1] . '.' . $hit[2] ) ] = 1;
		}
	}
}

function bst_ad_media_step( $state ) {
	global $wpdb;
	$data = wp_parse_args( $state['data'], array( 'ids' => array(), 'paths' => array() ) );
	if ( 0 === $state['phase'] ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
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
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT m.meta_id, m.meta_key, m.meta_value FROM {$wpdb->postmeta} m JOIN {$wpdb->posts} p ON p.ID = m.post_id WHERE m.meta_id > %d AND p.post_type NOT IN ('attachment','revision') ORDER BY m.meta_id ASC LIMIT 500", $state['cursor'] ) );
		foreach ( $rows as $row ) {
			if ( in_array( $row->meta_key, array( '_thumbnail_id', '_product_image_gallery' ), true ) ) {
				foreach ( wp_parse_id_list( $row->meta_value ) as $id ) {
					$data['ids'][ $id ] = 1;
				}
			} else {
				bst_ad_media_collect( $row->meta_value, $data );
			}
			$state['cursor'] = (int) $row->meta_id;
		}
		if ( count( $rows ) < 500 ) {
			$state['phase']  = 2;
			$state['cursor'] = 0;
		}
	} elseif ( 2 === $state['phase'] ) {
		bst_ad_media_settings( $data );
		$state['phase']  = 3;
		$state['cursor'] = 0;
		$state['done']   = 0;
		$state['total']  = (int) array_sum( (array) wp_count_attachments() );
	} else {
		$ids = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'numberposts' => 100, 'offset' => $state['cursor'], 'orderby' => 'ID', 'order' => 'ASC', 'fields' => 'ids' ) );
		foreach ( $ids as $id ) {
			$file = strtolower( (string) get_post_meta( $id, '_wp_attached_file', true ) );
			if ( empty( $data['ids'][ $id ] ) && empty( $data['paths'][ $file ] ) ) {
				$state['items'][] = bst_ad_media_item( $id );
			}
		}
		$state['cursor'] += count( $ids );
		$state['done']    = $state['cursor'];
		if ( count( $ids ) < 100 ) {
			$state['status'] = 'done';
			$bytes           = array_sum( wp_list_pluck( $state['items'], 'bytes' ) );
			/* translators: 1: file count, 2: total size. */
			$state['note'] = sprintf( __( '%1$d files, %2$s in total.', 'bigspy-ai-theme-wp' ), count( $state['items'] ), size_format( $bytes ) );
		}
	}
	$state['data'] = $data;
	return $state;
}

function bst_ad_media_settings( &$data ) {
	global $wpdb;
	foreach ( array( 'site_icon', 'site_logo', 'woocommerce_placeholder_image' ) as $name ) {
		$data['ids'][ (int) get_option( $name ) ] = 1;
	}
	foreach ( (array) get_theme_mods() as $key => $value ) {
		if ( is_numeric( $value ) && preg_match( '/logo|image|img|icon|bg|background|photo|media|favicon|banner/i', (string) $key ) ) {
			$data['ids'][ (int) $value ] = 1;
		}
		bst_ad_media_collect( is_scalar( $value ) ? (string) $value : wp_json_encode( $value ), $data );
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery
	$rows = $wpdb->get_col( "SELECT option_value FROM {$wpdb->options} WHERE option_name LIKE 'widget\_%'" );
	foreach ( $rows as $value ) {
		bst_ad_media_collect( wp_json_encode( maybe_unserialize( $value ) ), $data );
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery
	foreach ( $wpdb->get_col( "SELECT meta_value FROM {$wpdb->termmeta} WHERE meta_key LIKE '%thumbnail%' OR meta_key LIKE '%image%'" ) as $value ) {
		foreach ( wp_parse_id_list( $value ) as $id ) {
			$data['ids'][ $id ] = 1;
		}
	}
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
		'posts'    => array( bst_ad_post_ref( $id ) ),
		'select'   => $id,
		'bytes'    => $bytes,
	);
}

/** Delete only files the last scan listed, each after a per-file capability check. */
function bst_ad_media_action( WP_REST_Request $request ) {
	if ( 'delete' !== $request->get_param( 'op' ) ) {
		return new WP_Error( 'bst_ad_op', 'Unknown action', array( 'status' => 400 ) );
	}
	$state  = bst_ad_state( 'media' );
	$listed = array_map( 'intval', wp_list_pluck( $state['items'], 'select' ) );
	$ids    = array_intersect( wp_parse_id_list( $request->get_param( 'ids' ) ), $listed );
	$gone   = array();
	foreach ( $ids as $id ) {
		if ( current_user_can( 'delete_post', $id ) && 'attachment' === get_post_type( $id ) && wp_delete_attachment( $id, true ) ) {
			$gone[] = $id;
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
