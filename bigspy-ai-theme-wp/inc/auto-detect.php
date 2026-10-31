<?php
/**
 * Auto detect: report-only site checks that run in small batches.
 * A scan advances from the admin page or from WP-Cron; each module file adds one check
 * through the bst_ad_modules filter. Nothing is changed unless an admin presses an action.
 */
defined( 'ABSPATH' ) || exit;

define( 'BST_AD_NS', 'bst-detect/v1' );
define( 'BST_AD_MAX_ITEMS', 1000 );

foreach ( array( 'keywords', 'h1', 'media', 'atnd', 'links', 'security' ) as $bst_ad_file ) {
	require_once __DIR__ . '/auto-detect/' . $bst_ad_file . '.php';
}

/** Registered checks: id => array( label, intro, step callback, optional action callback ). */
function bst_ad_modules() {
	return apply_filters( 'bst_ad_modules', array() );
}

function bst_ad_enabled() {
	return (bool) get_option( 'bst_ad_enabled', 1 );
}

function bst_ad_state( $id ) {
	$state = get_option( 'bst_ad_state_' . $id );
	return is_array( $state ) ? $state : array( 'status' => 'idle', 'items' => array(), 'finished' => 0 );
}

function bst_ad_save_state( $id, $state ) {
	update_option( 'bst_ad_state_' . $id, $state, false );
}

function bst_ad_ignored( $id ) {
	$list = get_option( 'bst_ad_ignored_' . $id );
	return is_array( $list ) ? $list : array();
}

/** Start from a clean scan; ignored pairs are kept separately. */
function bst_ad_start( $id ) {
	bst_ad_save_state( $id, array( 'status' => 'running', 'items' => array(), 'finished' => 0, 'phase' => 0, 'cursor' => 0, 'data' => array(), 'done' => 0, 'total' => 0 ) );
	bst_ad_schedule();
}

/** Run one batch. A short lock keeps cron and the page from running the same batch twice. */
function bst_ad_step( $id ) {
	$modules = bst_ad_modules();
	$state   = bst_ad_state( $id );
	if ( empty( $modules[ $id ] ) || 'running' !== $state['status'] ) {
		return $state;
	}
	$lock = 'bst_ad_lock_' . $id;
	if ( get_transient( $lock ) ) {
		return $state;
	}
	set_transient( $lock, 1, 120 );
	$state = call_user_func( $modules[ $id ]['step'], $state );
	if ( 'running' !== $state['status'] ) {
		$state['finished'] = time();
		unset( $state['data'] );
		$state['items'] = array_slice( $state['items'], 0, BST_AD_MAX_ITEMS );
	}
	bst_ad_save_state( $id, $state );
	delete_transient( $lock );
	return $state;
}

function bst_ad_schedule() {
	if ( bst_ad_enabled() && ! wp_next_scheduled( 'bst_ad_tick' ) ) {
		wp_schedule_single_event( time() + 60, 'bst_ad_tick' );
	}
}

// Cron: one batch of the first running check, then wait for the next tick.
add_action(
	'bst_ad_tick',
	function () {
		if ( ! bst_ad_enabled() ) {
			return;
		}
		foreach ( array_keys( bst_ad_modules() ) as $id ) {
			if ( 'running' === bst_ad_state( $id )['status'] ) {
				bst_ad_step( $id );
				if ( 'running' === bst_ad_state( $id )['status'] ) {
					bst_ad_schedule();
					return;
				}
			}
		}
		foreach ( array_keys( bst_ad_modules() ) as $id ) {
			if ( 'running' === bst_ad_state( $id )['status'] ) {
				bst_ad_schedule();
				return;
			}
		}
	}
);

// Weekly full run while enabled.
add_action(
	'bst_ad_weekly',
	function () {
		if ( bst_ad_enabled() ) {
			foreach ( array_keys( bst_ad_modules() ) as $id ) {
				bst_ad_start( $id );
			}
		}
	}
);

add_action(
	'admin_init',
	function () {
		$next = wp_next_scheduled( 'bst_ad_weekly' );
		if ( bst_ad_enabled() && ! $next ) {
			wp_schedule_event( time() + DAY_IN_SECONDS, 'weekly', 'bst_ad_weekly' );
		} elseif ( ! bst_ad_enabled() && $next ) {
			wp_clear_scheduled_hook( 'bst_ad_weekly' );
			wp_clear_scheduled_hook( 'bst_ad_tick' );
		}
	}
);

/** Items without ignored ones; the shape a module stores is what the page shows. */
function bst_ad_visible_items( $id ) {
	$ignored = bst_ad_ignored( $id );
	$out     = array();
	foreach ( bst_ad_state( $id )['items'] as $item ) {
		if ( empty( $ignored[ $item['key'] ] ) ) {
			$out[] = $item;
		}
	}
	return $out;
}

/** Published post IDs of the given types, a page at a time. */
function bst_ad_post_ids( $types, $offset, $limit ) {
	return get_posts( array( 'post_type' => $types, 'post_status' => 'publish', 'numberposts' => $limit, 'offset' => $offset, 'orderby' => 'ID', 'order' => 'ASC', 'fields' => 'ids', 'no_found_rows' => true, 'suppress_filters' => true ) );
}

function bst_ad_post_ref( $id ) {
	return array( 'id' => (int) $id, 'title' => get_the_title( $id ), 'edit' => get_edit_post_link( $id, 'raw' ), 'url' => get_permalink( $id ) );
}

function bst_ad_can() {
	return current_user_can( 'manage_options' );
}

add_action(
	'rest_api_init',
	function () {
		$id = '/(?P<module>[a-z0-9]+)';
		register_rest_route( BST_AD_NS, '/state', array( 'methods' => 'GET', 'callback' => 'bst_ad_rest_state', 'permission_callback' => 'bst_ad_can' ) );
		register_rest_route( BST_AD_NS, '/enabled', array( 'methods' => 'POST', 'callback' => 'bst_ad_rest_enabled', 'permission_callback' => 'bst_ad_can' ) );
		register_rest_route( BST_AD_NS, $id . '/start', array( 'methods' => 'POST', 'callback' => 'bst_ad_rest_start', 'permission_callback' => 'bst_ad_can' ) );
		register_rest_route( BST_AD_NS, $id . '/step', array( 'methods' => 'POST', 'callback' => 'bst_ad_rest_step', 'permission_callback' => 'bst_ad_can' ) );
		register_rest_route( BST_AD_NS, $id . '/ignore', array( 'methods' => 'POST', 'callback' => 'bst_ad_rest_ignore', 'permission_callback' => 'bst_ad_can' ) );
		register_rest_route( BST_AD_NS, $id . '/action', array( 'methods' => 'POST', 'callback' => 'bst_ad_rest_action', 'permission_callback' => 'bst_ad_can' ) );
	}
);

function bst_ad_public_state( $id ) {
	$state = bst_ad_state( $id );
	return array( 'status' => $state['status'], 'finished' => (int) $state['finished'], 'done' => (int) ( $state['done'] ?? 0 ), 'total' => (int) ( $state['total'] ?? 0 ), 'items' => bst_ad_visible_items( $id ), 'note' => (string) ( $state['note'] ?? '' ), 'extra' => apply_filters( 'bst_ad_public_extra', array(), $id ) );
}

function bst_ad_rest_state() {
	$out = array( 'enabled' => bst_ad_enabled(), 'modules' => array() );
	foreach ( array_keys( bst_ad_modules() ) as $id ) {
		$out['modules'][ $id ] = bst_ad_public_state( $id );
	}
	return rest_ensure_response( $out );
}

function bst_ad_rest_enabled( WP_REST_Request $request ) {
	update_option( 'bst_ad_enabled', $request->get_param( 'enabled' ) ? 1 : 0 );
	return bst_ad_rest_state();
}

function bst_ad_known_module( WP_REST_Request $request ) {
	$id = (string) $request['module'];
	return isset( bst_ad_modules()[ $id ] ) ? $id : '';
}

function bst_ad_rest_start( WP_REST_Request $request ) {
	$id = bst_ad_known_module( $request );
	if ( ! $id ) {
		return new WP_Error( 'bst_ad_module', 'Unknown check', array( 'status' => 404 ) );
	}
	bst_ad_start( $id );
	return rest_ensure_response( bst_ad_public_state( $id ) );
}

function bst_ad_rest_step( WP_REST_Request $request ) {
	$id = bst_ad_known_module( $request );
	if ( ! $id ) {
		return new WP_Error( 'bst_ad_module', 'Unknown check', array( 'status' => 404 ) );
	}
	bst_ad_step( $id );
	return rest_ensure_response( bst_ad_public_state( $id ) );
}

function bst_ad_rest_ignore( WP_REST_Request $request ) {
	$id  = bst_ad_known_module( $request );
	$key = sanitize_text_field( (string) $request->get_param( 'key' ) );
	if ( ! $id || '' === $key ) {
		return new WP_Error( 'bst_ad_module', 'Unknown item', array( 'status' => 400 ) );
	}
	$list         = bst_ad_ignored( $id );
	$list[ $key ] = time();
	update_option( 'bst_ad_ignored_' . $id, $list, false );
	return rest_ensure_response( bst_ad_public_state( $id ) );
}

function bst_ad_rest_action( WP_REST_Request $request ) {
	$id      = bst_ad_known_module( $request );
	$modules = bst_ad_modules();
	if ( ! $id || empty( $modules[ $id ]['action'] ) ) {
		return new WP_Error( 'bst_ad_module', 'No action', array( 'status' => 400 ) );
	}
	$result = call_user_func( $modules[ $id ]['action'], $request );
	return is_wp_error( $result ) ? $result : rest_ensure_response( bst_ad_public_state( $id ) );
}

// AI Designer reads the latest reports; any fix still goes through its approval cards.
add_filter(
	'bst_aid_tools',
	function ( $tools ) {
		$tools['auto_detect_results'] = array(
			'description' => 'Read the latest Auto detect reports: duplicate focus keywords, H1 tags, unused media, images missing alt text, broken links, security. Read-only. Use it to propose fixes.',
			'parameters'  => array( 'type' => 'object', 'properties' => array( 'check' => array( 'type' => 'string', 'enum' => array_keys( bst_ad_modules() ) ) ), 'required' => array() ),
			'cap'         => 'manage_options',
			'callback'    => 'bst_aid_tool_auto_detect_results',
		);
		return $tools;
	}
);

function bst_aid_tool_auto_detect_results( $a ) {
	$out = array();
	foreach ( array_keys( bst_ad_modules() ) as $id ) {
		if ( empty( $a['check'] ) || $a['check'] === $id ) {
			$state      = bst_ad_state( $id );
			$out[ $id ] = array( 'status' => $state['status'], 'finished' => $state['finished'] ? gmdate( 'c', $state['finished'] ) : null, 'items' => array_slice( bst_ad_visible_items( $id ), 0, 100 ) );
		}
	}
	return $out;
}

add_action(
	'admin_menu',
	function () {
		add_submenu_page( 'bigspy-ai-theme-wp', __( 'Auto detect', 'bigspy-ai-theme-wp' ), __( 'Auto detect', 'bigspy-ai-theme-wp' ), 'manage_options', 'bigspy-auto-detect', 'bst_ad_page' );
	},
	20
);

function bst_ad_page() {
	$modules = array();
	foreach ( bst_ad_modules() as $id => $module ) {
		$modules[ $id ] = array( 'label' => $module['label'], 'intro' => $module['intro'] );
	}
	wp_enqueue_script( 'bst-auto-detect', BST_URI . '/assets/js/auto-detect.js', array( 'wp-api-fetch' ), BST_VERSION, true );
	wp_localize_script(
		'bst-auto-detect',
		'bstAutoDetect',
		array(
			'ns'      => BST_AD_NS,
			'modules' => $modules,
			'i18n'    => array(
				'scan'       => __( 'Scan now', 'bigspy-ai-theme-wp' ),
				'scanning'   => __( 'Scanning…', 'bigspy-ai-theme-wp' ),
				'never'      => __( 'Not scanned yet.', 'bigspy-ai-theme-wp' ),
				'last'       => __( 'Last scan:', 'bigspy-ai-theme-wp' ),
				'clean'      => __( 'No problems found.', 'bigspy-ai-theme-wp' ),
				'ignore'     => __( 'Ignore this pair', 'bigspy-ai-theme-wp' ),
				'enable'     => __( 'Run automatically every week, in small background batches', 'bigspy-ai-theme-wp' ),
				'error'      => __( 'Needs fixing', 'bigspy-ai-theme-wp' ),
				'review'     => __( 'Needs review', 'bigspy-ai-theme-wp' ),
				'deleteBtn'  => __( 'Move selected to trash', 'bigspy-ai-theme-wp' ),
				'deleteAsk'  => __( 'Move the selected files to the trash? Files still in use are skipped, and you can restore the rest here.', 'bigspy-ai-theme-wp' ),
				'selectAll'  => __( 'Select all', 'bigspy-ai-theme-wp' ),
				'keep'       => __( 'Keep this file', 'bigspy-ai-theme-wp' ),
				'trash'      => __( 'Trash', 'bigspy-ai-theme-wp' ),
				'restoreBtn' => __( 'Restore selected', 'bigspy-ai-theme-wp' ),
				'purgeBtn'   => __( 'Delete selected permanently', 'bigspy-ai-theme-wp' ),
				'purgeAsk'   => __( 'Permanently delete the selected files? This cannot be undone.', 'bigspy-ai-theme-wp' ),
				'generate'   => __( 'Generate for selected', 'bigspy-ai-theme-wp' ),
				'connect'    => __( 'Connect the API to use this tool', 'bigspy-ai-theme-wp' ),
				'replaceBtn' => __( 'Replace link', 'bigspy-ai-theme-wp' ),
				'replaceAsk' => __( 'New URL:', 'bigspy-ai-theme-wp' ),
				'removeBtn'  => __( 'Remove link', 'bigspy-ai-theme-wp' ),
				'removeAsk'  => __( 'Remove this link and keep its text in every listed post?', 'bigspy-ai-theme-wp' ),
				'failed'     => __( 'Request failed.', 'bigspy-ai-theme-wp' ),
			),
		)
	);
	wp_set_script_translations( 'bst-auto-detect', 'bigspy-ai-theme-wp' );
	echo '<div class="wrap"><h1>' . esc_html__( 'Auto detect', 'bigspy-ai-theme-wp' ) . '</h1>';
	echo '<p>' . esc_html__( 'These checks only report. Nothing is changed or deleted unless you press an action and confirm it.', 'bigspy-ai-theme-wp' ) . '</p>';
	echo '<div id="bst-auto-detect"></div></div>';
}
