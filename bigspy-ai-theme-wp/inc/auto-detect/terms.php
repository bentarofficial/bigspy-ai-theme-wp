<?php
/**
 * Empty categories and tags. A term counts as empty when neither it nor any child term has
 * published content. Taxonomies have no trash, so every deletion is logged first (name, slug,
 * parent, description and the draft or trashed posts it held) and can be restored from here.
 * Other features add taxonomies through the bst_ad_terms_taxonomies filter.
 */
defined( 'ABSPATH' ) || exit;

define( 'BST_AD_TERMS_LOG', 'bst_ad_terms_log' );

add_filter(
	'bst_ad_modules',
	function ( $modules ) {
		$modules['terms'] = array(
			'label'  => __( 'Empty categories and tags', 'bigspy-ai-theme-wp' ),
			'intro'  => __( 'Lists categories and tags with no published content, counting child categories too. Default categories and terms used in a menu are never deleted. Deleted terms are logged here and can be restored with their drafts and trashed posts.', 'bigspy-ai-theme-wp' ),
			'step'   => 'bst_ad_terms_step',
			'action' => 'bst_ad_terms_action',
			'i18n'   => array(
				'deleteBtn' => __( 'Delete selected', 'bigspy-ai-theme-wp' ),
				'deleteAsk' => __( 'Delete the selected categories and tags? Terms that gained content or are used in a menu are skipped, and you can restore the rest here.', 'bigspy-ai-theme-wp' ),
				'trash'     => __( 'Deleted terms', 'bigspy-ai-theme-wp' ),
				'purgeBtn'  => __( 'Forget selected', 'bigspy-ai-theme-wp' ),
				'purgeAsk'  => __( 'Remove the selected terms from this log? They can no longer be restored.', 'bigspy-ai-theme-wp' ),
			),
		);
		return $modules;
	}
);

/** Taxonomies to check: post categories and tags, plus whatever other features add. */
function bst_ad_terms_taxonomies() {
	$list = apply_filters( 'bst_ad_terms_taxonomies', array( 'category', 'post_tag' ) );
	return array_values( array_filter( array_unique( (array) $list ), 'taxonomy_exists' ) );
}

/** Terms that must stay: the default category of each taxonomy that has one. */
function bst_ad_terms_protected( $taxonomy ) {
	$ids = array();
	if ( 'category' === $taxonomy ) {
		$ids[] = (int) get_option( 'default_category' );
	}
	if ( 'product_cat' === $taxonomy ) {
		$ids[] = (int) get_option( 'default_product_cat' );
	}
	return array_filter( apply_filters( 'bst_ad_terms_protected', $ids, $taxonomy ) );
}

/** Term IDs of this taxonomy linked from any nav menu. */
function bst_ad_terms_in_menus( $taxonomy ) {
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery
	$ids = $wpdb->get_col( $wpdb->prepare( "SELECT o.meta_value FROM {$wpdb->postmeta} o JOIN {$wpdb->postmeta} t ON t.post_id = o.post_id AND t.meta_key = '_menu_item_object' AND t.meta_value = %s WHERE o.meta_key = '_menu_item_object_id'", $taxonomy ) );
	return array_map( 'intval', $ids );
}

/** Posts per term that are not published (draft, pending, private, future, trash), by term_taxonomy_id. */
function bst_ad_terms_hidden_counts( $taxonomy ) {
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery
	$rows = $wpdb->get_results( $wpdb->prepare( "SELECT tt.term_id, p.post_status, COUNT(*) AS n FROM {$wpdb->term_relationships} r JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = r.term_taxonomy_id JOIN {$wpdb->posts} p ON p.ID = r.object_id WHERE tt.taxonomy = %s AND p.post_status NOT IN ('publish','auto-draft','inherit') GROUP BY tt.term_id, p.post_status", $taxonomy ) );
	$out  = array();
	foreach ( $rows as $row ) {
		$key                         = 'trash' === $row->post_status ? 'trash' : 'draft';
		$out[ (int) $row->term_id ][ $key ] = ( $out[ (int) $row->term_id ][ $key ] ?? 0 ) + (int) $row->n;
	}
	return $out;
}

/** Published objects of a term, its children included. Reads the live count, never a cached one. */
function bst_ad_terms_published( $term_id, $taxonomy ) {
	global $wpdb;
	$ids   = array_merge( array( (int) $term_id ), array_map( 'intval', (array) get_term_children( $term_id, $taxonomy ) ) );
	$place = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQLPlaceholders
	return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT r.object_id) FROM {$wpdb->term_relationships} r JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = r.term_taxonomy_id JOIN {$wpdb->posts} p ON p.ID = r.object_id WHERE tt.taxonomy = %s AND tt.term_id IN ($place) AND p.post_status = 'publish'", array_merge( array( $taxonomy ), $ids ) ) );
}

/** One taxonomy per batch; a site has far fewer terms than posts, so this stays small. */
function bst_ad_terms_step( $state ) {
	$taxes          = bst_ad_terms_taxonomies();
	$state['total'] = count( $taxes );
	$tax            = $taxes[ $state['phase'] ] ?? '';
	if ( ! $tax ) {
		$state['status'] = 'done';
		/* translators: %d: number of empty terms. */
		$state['note'] = sprintf( __( '%d empty categories and tags.', 'bigspy-ai-theme-wp' ), count( $state['items'] ) );
		return $state;
	}
	$label  = get_taxonomy( $tax )->labels->singular_name;
	$terms  = get_terms( array( 'taxonomy' => $tax, 'hide_empty' => false ) );
	$terms  = is_wp_error( $terms ) ? array() : $terms;
	$own    = array();
	$kids   = array();
	$byid   = array();
	foreach ( $terms as $term ) {
		$byid[ $term->term_id ]   = $term;
		$own[ $term->term_id ]    = 0;
		$kids[ $term->parent ][]  = $term->term_id;
	}
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery
	foreach ( $wpdb->get_results( $wpdb->prepare( "SELECT tt.term_id, COUNT(DISTINCT r.object_id) AS n FROM {$wpdb->term_relationships} r JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = r.term_taxonomy_id JOIN {$wpdb->posts} p ON p.ID = r.object_id WHERE tt.taxonomy = %s AND p.post_status = 'publish' GROUP BY tt.term_id", $tax ) ) as $row ) {
		$own[ (int) $row->term_id ] = (int) $row->n;
	}
	$total = function ( $id ) use ( &$total, $own, $kids ) {
		$sum = $own[ $id ] ?? 0;
		foreach ( $kids[ $id ] ?? array() as $child ) {
			$sum += $total( $child );
		}
		return $sum;
	};
	$protected = bst_ad_terms_protected( $tax );
	$menus     = bst_ad_terms_in_menus( $tax );
	$hidden    = bst_ad_terms_hidden_counts( $tax );
	foreach ( $byid as $id => $term ) {
		if ( $total( $id ) > 0 || in_array( $id, $protected, true ) ) {
			continue;
		}
		$detail = array( $label, '/' . $term->slug );
		if ( $term->parent && isset( $byid[ $term->parent ] ) ) {
			/* translators: %s: parent term name. */
			$detail[] = sprintf( __( 'in %s', 'bigspy-ai-theme-wp' ), $byid[ $term->parent ]->name );
		}
		if ( ! empty( $hidden[ $id ]['draft'] ) ) {
			/* translators: %d: number of drafts. */
			$detail[] = sprintf( __( 'drafts: %d', 'bigspy-ai-theme-wp' ), $hidden[ $id ]['draft'] );
		}
		if ( ! empty( $hidden[ $id ]['trash'] ) ) {
			/* translators: %d: number of trashed posts. */
			$detail[] = sprintf( __( 'in trash: %d', 'bigspy-ai-theme-wp' ), $hidden[ $id ]['trash'] );
		}
		$in_menu = in_array( $id, $menus, true );
		if ( $in_menu ) {
			$detail[] = __( 'used in a menu, kept', 'bigspy-ai-theme-wp' );
		}
		$item = array(
			'key'      => 'term-' . $id,
			'severity' => $in_menu ? 'error' : 'review',
			'title'    => html_entity_decode( $term->name, ENT_QUOTES, 'UTF-8' ),
			'detail'   => implode( ' · ', $detail ),
			'posts'    => array(),
			'ignore'   => true,
		);
		if ( ! $in_menu ) {
			$item['select'] = $id;
		}
		$link = get_edit_term_link( $id, $tax );
		if ( $link ) {
			$item['posts'][] = array( 'id' => $id, 'title' => __( 'Edit', 'bigspy-ai-theme-wp' ), 'edit' => $link );
		}
		$state['items'][] = $item;
	}
	++$state['phase'];
	++$state['done'];
	return $state;
}

function bst_ad_terms_log() {
	$log = get_option( BST_AD_TERMS_LOG );
	return is_array( $log ) ? $log : array();
}

/** Delete one term after checking again; the scan may be old. Returns true when deleted. */
function bst_ad_terms_delete( $term_id ) {
	$term = get_term( (int) $term_id );
	if ( ! $term || is_wp_error( $term ) || ! in_array( $term->taxonomy, bst_ad_terms_taxonomies(), true ) ) {
		return false;
	}
	$tax     = $term->taxonomy;
	$ignored = bst_ad_ignored( 'terms' );
	$tax_obj = get_taxonomy( $tax );
	if ( ! current_user_can( $tax_obj->cap->delete_terms ) || ! empty( $ignored[ 'term-' . $term->term_id ] ) ) {
		return false;
	}
	if ( in_array( $term->term_id, bst_ad_terms_protected( $tax ), true ) || in_array( $term->term_id, bst_ad_terms_in_menus( $tax ), true ) || bst_ad_terms_published( $term->term_id, $tax ) > 0 ) {
		return false;
	}
	$objects = get_objects_in_term( $term->term_id, $tax );
	$parent  = $term->parent ? get_term( $term->parent, $tax ) : null;
	$entry   = array(
		'name'        => $term->name,
		'slug'        => $term->slug,
		'taxonomy'    => $tax,
		'description' => $term->description,
		'parent'      => $parent && ! is_wp_error( $parent ) ? $parent->slug : '',
		'objects'     => is_wp_error( $objects ) ? array() : array_map( 'intval', $objects ),
		'meta'        => get_term_meta( $term->term_id ),
		'time'        => time(),
	);
	$deleted = wp_delete_term( $term->term_id, $tax );
	if ( true !== $deleted ) {
		return false;
	}
	$log     = bst_ad_terms_log();
	$key     = $log ? max( array_keys( $log ) ) + 1 : 1;
	$log[ $key ] = $entry;
	update_option( BST_AD_TERMS_LOG, $log, false );
	return true;
}

/** Recreate a logged term with its parent (when it still exists), description, meta and posts. */
function bst_ad_terms_restore( $key ) {
	$log   = bst_ad_terms_log();
	$entry = $log[ $key ] ?? null;
	if ( ! $entry || ! taxonomy_exists( $entry['taxonomy'] ) || ! current_user_can( get_taxonomy( $entry['taxonomy'] )->cap->manage_terms ) ) {
		return false;
	}
	$args   = array( 'slug' => $entry['slug'], 'description' => $entry['description'] );
	$parent = $entry['parent'] ? get_term_by( 'slug', $entry['parent'], $entry['taxonomy'] ) : null;
	if ( $parent ) {
		$args['parent'] = $parent->term_id;
	}
	$made = wp_insert_term( $entry['name'], $entry['taxonomy'], $args );
	if ( is_wp_error( $made ) ) {
		$existing = $made->get_error_data( 'term_exists' );
		if ( ! $existing ) {
			return false;
		}
		$term_id = (int) $existing;
	} else {
		$term_id = (int) $made['term_id'];
		foreach ( (array) $entry['meta'] as $meta_key => $values ) {
			foreach ( (array) $values as $value ) {
				add_term_meta( $term_id, $meta_key, maybe_unserialize( $value ) );
			}
		}
	}
	foreach ( $entry['objects'] as $object_id ) {
		if ( get_post( $object_id ) ) {
			wp_set_object_terms( $object_id, $term_id, $entry['taxonomy'], true );
		}
	}
	unset( $log[ $key ] );
	update_option( BST_AD_TERMS_LOG, $log, false );
	return true;
}

add_filter(
	'bst_ad_public_extra',
	function ( $extra, $id ) {
		if ( 'terms' !== $id ) {
			return $extra;
		}
		$out = array();
		foreach ( array_reverse( bst_ad_terms_log(), true ) as $key => $entry ) {
			$tax   = get_taxonomy( $entry['taxonomy'] );
			$out[] = array( 'id' => (int) $key, 'title' => $entry['name'] . ' (' . ( $tax ? $tax->labels->singular_name : $entry['taxonomy'] ) . ')' );
		}
		return array( 'trashed' => $out );
	},
	10,
	2
);

/** Delete terms, or restore or forget logged ones. */
function bst_ad_terms_action( WP_REST_Request $request ) {
	$op  = (string) $request->get_param( 'op' );
	$ids = wp_parse_id_list( $request->get_param( 'ids' ) );
	if ( ! in_array( $op, array( 'delete', 'restore', 'purge' ), true ) ) {
		return new WP_Error( 'bst_ad_op', 'Unknown action', array( 'status' => 400 ) );
	}
	if ( 'delete' === $op ) {
		// Children first, so a parent is still known when its child is logged.
		usort(
			$ids,
			function ( $a, $b ) {
				$ta = get_term( $a );
				$tb = get_term( $b );
				$da = $ta && ! is_wp_error( $ta ) ? count( get_ancestors( $a, $ta->taxonomy, 'taxonomy' ) ) : 0;
				$db = $tb && ! is_wp_error( $tb ) ? count( get_ancestors( $b, $tb->taxonomy, 'taxonomy' ) ) : 0;
				return $db - $da;
			}
		);
		$gone = array();
		foreach ( $ids as $id ) {
			if ( bst_ad_terms_delete( $id ) || ! term_exists( (int) $id ) ) {
				$gone[] = $id;
			}
		}
		$state          = bst_ad_state( 'terms' );
		$state['items'] = array_values(
			array_filter(
				$state['items'],
				function ( $item ) use ( $gone ) {
					return empty( $item['select'] ) || ! in_array( (int) $item['select'], $gone, true );
				}
			)
		);
		bst_ad_save_state( 'terms', $state );
		return true;
	}
	if ( 'restore' === $op ) {
		// Newest first: a parent is logged after its children, so it comes back before them.
		rsort( $ids );
		foreach ( $ids as $key ) {
			bst_ad_terms_restore( $key );
		}
		return true;
	}
	$log = bst_ad_terms_log();
	foreach ( $ids as $key ) {
		unset( $log[ $key ] );
	}
	update_option( BST_AD_TERMS_LOG, $log, false );
	return true;
}
