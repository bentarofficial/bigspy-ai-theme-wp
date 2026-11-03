<?php
/**
 * Context store: guidelines (business, writing, images) and specialty contexts (sp_<key>).
 * Each box has a version, so the Guidelines page autosaves without overwriting a change the
 * AI made meanwhile, and every AI save can be undone from the chat.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

const BST_AID_CONTEXT_MAX = 6000;

function bst_aid_context_keys() {
	$keys = array( 'business', 'writing', 'images' );
	foreach ( array_keys( bst_aid_specialty_defaults() ) as $sp ) {
		$keys[] = 'sp_' . $sp;
	}
	return $keys;
}

/** Text shown in a box: guidelines as saved, specialties as in use (edited or default). */
function bst_aid_context_get( $key ) {
	if ( 0 === strpos( $key, 'sp_' ) ) {
		return bst_aid_specialty_context( substr( $key, 3 ) );
	}
	return (string) ( bst_aid_guidelines()[ $key ] ?? '' );
}

function bst_aid_context_version( $key ) {
	$meta = (array) get_option( 'bst_aid_context_meta', array() );
	return (int) ( $meta[ $key ]['v'] ?? 0 );
}

/**
 * Save one box. '' on a specialty restores its default.
 *
 * @param string $by 'user' or 'ai'.
 * @return int|WP_Error New version.
 */
function bst_aid_context_set( $key, $text, $by = 'user' ) {
	if ( ! in_array( $key, bst_aid_context_keys(), true ) ) {
		return new WP_Error( 'bst_aid_context', 'Unknown box.', array( 'status' => 400 ) );
	}
	if ( mb_strlen( (string) $text ) > BST_AID_CONTEXT_MAX ) {
		return new WP_Error( 'bst_aid_context', sprintf( 'Too long (%d characters). Rewrite the whole box shorter, under 6,000 characters, merging old and new points instead of appending.', mb_strlen( (string) $text ) ), array( 'status' => 400 ) );
	}
	if ( 0 === strpos( $key, 'sp_' ) ) {
		bst_aid_save_specialty( substr( $key, 3 ), $text );
	} else {
		bst_aid_save_guidelines( array( $key => $text ) );
	}
	$meta         = (array) get_option( 'bst_aid_context_meta', array() );
	$meta[ $key ] = array( 'v' => (int) ( $meta[ $key ]['v'] ?? 0 ) + 1, 'by' => 'ai' === $by ? 'ai' : 'user', 't' => time() );
	update_option( 'bst_aid_context_meta', $meta, false );
	return $meta[ $key ]['v'];
}

add_action(
	'rest_api_init',
	function () {
		$ns = bst_aid_rest_ns();
		register_rest_route( $ns, '/context', array( 'methods' => 'POST', 'callback' => 'bst_aid_rest_context_save', 'permission_callback' => 'bst_aid_rest_can_use' ) );
		register_rest_route( $ns, '/tasks/(?P<id>\d+)/context-undo', array( 'methods' => 'POST', 'callback' => 'bst_aid_rest_context_undo', 'permission_callback' => 'bst_aid_rest_can_use' ) );
	}
);

/** Autosave from the Guidelines page. A stale version gets 409 with the current text. */
function bst_aid_rest_context_save( WP_REST_Request $r ) {
	$key = (string) $r->get_param( 'key' );
	if ( ! in_array( $key, bst_aid_context_keys(), true ) ) {
		return new WP_Error( 'bst_aid_context', 'Unknown box.', array( 'status' => 400 ) );
	}
	if ( (int) $r->get_param( 'version' ) !== bst_aid_context_version( $key ) ) {
		return new WP_Error( 'bst_aid_context_changed', __( 'The AI Designer changed this box meanwhile. Reload to see its version before editing.', 'bigspy-ai-theme-wp' ), array( 'status' => 409, 'text' => bst_aid_context_get( $key ), 'version' => bst_aid_context_version( $key ) ) );
	}
	$v = bst_aid_context_set( $key, sanitize_textarea_field( (string) $r->get_param( 'text' ) ), 'user' );
	if ( is_wp_error( $v ) ) {
		return $v;
	}
	return rest_ensure_response( array( 'version' => $v, 'text' => bst_aid_context_get( $key ) ) );
}

/** Undo one AI save from the chat, unless the box changed again since. */
function bst_aid_rest_context_undo( WP_REST_Request $r ) {
	$task = bst_aid_task( $r['id'] );
	if ( ! $task ) {
		return new WP_Error( 'bst_aid_task', __( 'Task not found.', 'bigspy-ai-theme-wp' ), array( 'status' => 404 ) );
	}
	$undo = (array) bst_aid_task_get( $task->ID, '_bst_aid_context_undo' );
	$id   = (string) $r->get_param( 'undo' );
	if ( empty( $undo[ $id ] ) ) {
		return new WP_Error( 'bst_aid_context', __( 'This change was already undone.', 'bigspy-ai-theme-wp' ), array( 'status' => 409 ) );
	}
	$done = array();
	foreach ( $undo[ $id ] as $key => $u ) {
		if ( bst_aid_context_version( $key ) !== (int) $u['v'] ) {
			return new WP_Error( 'bst_aid_context', __( 'The box was edited again after this change, so it was not undone. Edit it under Guidelines.', 'bigspy-ai-theme-wp' ), array( 'status' => 409 ) );
		}
	}
	foreach ( $undo[ $id ] as $key => $u ) {
		bst_aid_context_set( $key, $u['old'], 'user' );
		$done[] = $key;
	}
	unset( $undo[ $id ] );
	bst_aid_task_set( $task->ID, '_bst_aid_context_undo', $undo );
	$event = bst_aid_task_log( $task->ID, array( 'kind' => 'info', 'text' => __( 'Saved context change undone.', 'bigspy-ai-theme-wp' ) ) );
	return rest_ensure_response( array( 'undone' => $done, 'events' => array( $event ) ) );
}

/** AI tool: save context the user said applies from now on. */
function bst_aid_tool_save_guidelines( $a ) {
	$map = array( 'business' => 'business', 'writing' => 'writing', 'images' => 'images' );
	$set = array();
	foreach ( $map as $param => $key ) {
		if ( isset( $a[ $param ] ) && is_string( $a[ $param ] ) ) {
			$set[ $key ] = $a[ $param ];
		}
	}
	$sp = sanitize_key( (string) ( $a['specialty'] ?? '' ) );
	if ( '' !== $sp && isset( $a['specialty_context'] ) ) {
		if ( ! isset( bst_aid_specialty_defaults()[ $sp ] ) ) {
			return array( 'error' => 'Unknown specialty.' );
		}
		$set[ 'sp_' . $sp ] = (string) $a['specialty_context'];
	}
	if ( ! $set ) {
		return array( 'error' => 'Nothing to save.' );
	}
	foreach ( $set as $key => $text ) {
		if ( mb_strlen( $text ) > BST_AID_CONTEXT_MAX ) {
			return array( 'error' => sprintf( 'The %s box would be %d characters. Rewrite it shorter, under 6,000 characters, merging the old and new points; nothing was saved.', $key, mb_strlen( $text ) ) );
		}
	}
	bst_aid_snapshot( 'option', 'bst_aid_guidelines', get_option( 'bst_aid_guidelines', array() ) );
	bst_aid_snapshot( 'option', 'bst_aid_specialties', get_option( 'bst_aid_specialties', array() ) );
	$undo = array();
	foreach ( $set as $key => $text ) {
		$old          = 0 === strpos( $key, 'sp_' ) ? (string) ( ( (array) get_option( 'bst_aid_specialties', array() ) )[ substr( $key, 3 ) ] ?? '' ) : bst_aid_context_get( $key );
		$v            = bst_aid_context_set( $key, sanitize_textarea_field( $text ), 'ai' );
		$undo[ $key ] = array( 'old' => $old, 'v' => $v );
	}
	$task  = (int) ( $GLOBALS['bst_aid_task_id'] ?? 0 );
	$undo_id = '';
	if ( $task ) {
		$all            = (array) bst_aid_task_get( $task, '_bst_aid_context_undo' );
		$undo_id        = 'u' . wp_generate_password( 8, false, false );
		$all[ $undo_id ] = $undo;
		bst_aid_task_set( $task, '_bst_aid_context_undo', array_slice( $all, -20, null, true ) );
	}
	return array(
		'saved'        => array_keys( $set ),
		'context_undo' => array( 'id' => $undo_id, 'keys' => array_keys( $set ) ),
		'next'         => 'Tell the user in one line what you saved for future tasks; they can undo it with the button in the chat.',
		'links'        => array( array( 'label' => __( 'Guidelines', 'bigspy-ai-theme-wp' ), 'edit' => admin_url( 'admin.php?page=bst-ai-guidelines' ), 'view' => '' ) ),
	);
}
