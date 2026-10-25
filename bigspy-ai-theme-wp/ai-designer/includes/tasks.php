<?php
/**
 * Tasks: chat history, step-by-step tool loop, budgets and log.
 *
 * Each user message starts a run. The browser calls /step repeatedly; every step makes one
 * model call and runs the tools it asked for, so each request stays within PHP time limits
 * and the history is saved after every step.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		register_post_type(
			'bst_aid_task',
			array(
				'label'           => __( 'AI Designer tasks', 'bigspy-ai-theme-wp' ),
				'public'          => false,
				'show_ui'         => false,
				'show_in_rest'    => false,
				'supports'        => array( 'title', 'author' ),
				'capability_type' => 'page',
				'map_meta_cap'    => true,
			)
		);
	}
);

/** Steps (model calls) allowed per user message, by effort. */
function bst_aid_effort_steps( $effort ) {
	$map = array( 'low' => 6, 'medium' => 12, 'high' => 20, 'max' => 30 );
	return $map[ $effort ] ?? 12;
}

function bst_aid_task_get( $id, $key, $default = array() ) {
	$v = get_post_meta( $id, $key, true );
	return '' === $v || null === $v ? $default : $v;
}

/** Arrays are slashed before saving so backslashes in JSON survive update_post_meta(). */
function bst_aid_task_set( $id, $key, $value ) {
	update_post_meta( $id, $key, wp_slash( $value ) );
}

function bst_aid_task_log( $id, $event ) {
	$log   = bst_aid_task_get( $id, '_bst_aid_log' );
	$event = array_merge( array( 't' => time() ), $event );
	$log[] = $event;
	bst_aid_task_set( $id, '_bst_aid_log', $log );
	return $event;
}

/** The task when the current user may use it, else null. */
function bst_aid_task( $id ) {
	$p = get_post( (int) $id );
	if ( ! $p || 'bst_aid_task' !== $p->post_type ) {
		return null;
	}
	if ( (int) $p->post_author !== get_current_user_id() && ! current_user_can( 'manage_options' ) ) {
		return null;
	}
	return $p;
}

function bst_aid_task_summary( $p ) {
	return array(
		'id'       => $p->ID,
		'title'    => $p->post_title,
		'modified' => mysql_to_rfc3339( $p->post_modified_gmt ),
		'cost'     => round( (float) bst_aid_task_get( $p->ID, '_bst_aid_cost', 0 ), 4 ),
		'running'  => ! empty( bst_aid_task_get( $p->ID, '_bst_aid_run' )['active'] ),
		'canUndo'  => (bool) ( bst_aid_task_get( $p->ID, '_bst_aid_snapshots' ) || bst_aid_task_get( $p->ID, '_bst_aid_created' ) ),
	);
}

/* ------------------------------------------------------------------ REST */

add_action(
	'rest_api_init',
	function () {
		$ns = bst_aid_rest_ns();
		$id = '/tasks/(?P<id>\d+)';
		register_rest_route( $ns, '/tasks', array(
			array( 'methods' => 'GET', 'callback' => 'bst_aid_rest_tasks', 'permission_callback' => 'bst_aid_rest_can_use' ),
			array( 'methods' => 'POST', 'callback' => 'bst_aid_rest_task_create', 'permission_callback' => 'bst_aid_rest_can_use' ),
		) );
		register_rest_route( $ns, $id, array(
			array( 'methods' => 'GET', 'callback' => 'bst_aid_rest_task_read', 'permission_callback' => 'bst_aid_rest_can_use' ),
			array( 'methods' => 'POST', 'callback' => 'bst_aid_rest_task_rename', 'permission_callback' => 'bst_aid_rest_can_use' ),
			array( 'methods' => 'DELETE', 'callback' => 'bst_aid_rest_task_delete', 'permission_callback' => 'bst_aid_rest_can_use' ),
		) );
		register_rest_route( $ns, $id . '/send', array( 'methods' => 'POST', 'callback' => 'bst_aid_rest_task_send', 'permission_callback' => 'bst_aid_rest_can_use' ) );
		register_rest_route( $ns, $id . '/step', array( 'methods' => 'POST', 'callback' => 'bst_aid_rest_task_step', 'permission_callback' => 'bst_aid_rest_can_use' ) );
		register_rest_route( $ns, $id . '/stop', array( 'methods' => 'POST', 'callback' => 'bst_aid_rest_task_stop', 'permission_callback' => 'bst_aid_rest_can_use' ) );
		register_rest_route( $ns, $id . '/undo', array( 'methods' => 'POST', 'callback' => 'bst_aid_rest_task_undo', 'permission_callback' => 'bst_aid_rest_can_use' ) );
	}
);

function bst_aid_rest_tasks() {
	$args = array( 'post_type' => 'bst_aid_task', 'post_status' => 'private', 'numberposts' => 100, 'orderby' => 'modified', 'order' => 'DESC' );
	if ( ! current_user_can( 'manage_options' ) ) {
		$args['author'] = get_current_user_id();
	}
	return rest_ensure_response( array_map( 'bst_aid_task_summary', get_posts( $args ) ) );
}

function bst_aid_rest_task_create( WP_REST_Request $r ) {
	$title = sanitize_text_field( (string) $r->get_param( 'title' ) );
	$id    = wp_insert_post( array( 'post_type' => 'bst_aid_task', 'post_status' => 'private', 'post_title' => $title ? $title : __( 'New task', 'bigspy-ai-theme-wp' ), 'post_author' => get_current_user_id() ), true );
	if ( is_wp_error( $id ) ) {
		return $id;
	}
	return rest_ensure_response( bst_aid_task_summary( get_post( $id ) ) );
}

function bst_aid_rest_task_read( WP_REST_Request $r ) {
	$p = bst_aid_task( $r['id'] );
	if ( ! $p ) {
		return new WP_Error( 'bst_aid_task', __( 'Task not found.', 'bigspy-ai-theme-wp' ), array( 'status' => 404 ) );
	}
	return rest_ensure_response( array( 'task' => bst_aid_task_summary( $p ), 'log' => bst_aid_task_get( $p->ID, '_bst_aid_log' ), 'effort' => (string) ( bst_aid_task_get( $p->ID, '_bst_aid_run' )['effort'] ?? 'medium' ) ) );
}

/** Undo everything the task changed: settings, CSS, logo, menus, header, edited pages; trash what it created. */
function bst_aid_rest_task_undo( WP_REST_Request $r ) {
	$p = bst_aid_task( $r['id'] );
	if ( ! $p ) {
		return new WP_Error( 'bst_aid_task', __( 'Task not found.', 'bigspy-ai-theme-wp' ), array( 'status' => 404 ) );
	}
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return new WP_Error( 'bst_aid_cap', __( 'You are not allowed to do this.', 'bigspy-ai-theme-wp' ), array( 'status' => 403 ) );
	}
	$done  = function_exists( 'bst_aid_undo_task' ) ? bst_aid_undo_task( $p->ID ) : array();
	$event = bst_aid_task_log( $p->ID, array( 'kind' => 'info', 'text' => $done ? sprintf( /* translators: %d: number of changes */ __( 'Undone: %d changes restored. Created pages, posts and products were moved to the trash; generated images stay in the Media Library.', 'bigspy-ai-theme-wp' ), count( $done ) ) : __( 'Nothing to undo in this task.', 'bigspy-ai-theme-wp' ) ) );
	return rest_ensure_response( array( 'events' => array( $event ), 'task' => bst_aid_task_summary( get_post( $p->ID ) ) ) );
}

function bst_aid_rest_task_rename( WP_REST_Request $r ) {
	$p = bst_aid_task( $r['id'] );
	if ( ! $p ) {
		return new WP_Error( 'bst_aid_task', __( 'Task not found.', 'bigspy-ai-theme-wp' ), array( 'status' => 404 ) );
	}
	wp_update_post( array( 'ID' => $p->ID, 'post_title' => sanitize_text_field( (string) $r->get_param( 'title' ) ) ) );
	return rest_ensure_response( bst_aid_task_summary( get_post( $p->ID ) ) );
}

function bst_aid_rest_task_delete( WP_REST_Request $r ) {
	$p = bst_aid_task( $r['id'] );
	if ( ! $p ) {
		return new WP_Error( 'bst_aid_task', __( 'Task not found.', 'bigspy-ai-theme-wp' ), array( 'status' => 404 ) );
	}
	wp_delete_post( $p->ID, true ); // The chat history only; pages and posts it made stay.
	return rest_ensure_response( array( 'deleted' => true ) );
}

function bst_aid_rest_task_send( WP_REST_Request $r ) {
	$p = bst_aid_task( $r['id'] );
	if ( ! $p ) {
		return new WP_Error( 'bst_aid_task', __( 'Task not found.', 'bigspy-ai-theme-wp' ), array( 'status' => 404 ) );
	}
	if ( ! bst_aid_ready() ) {
		return new WP_Error( 'bst_aid_ready', __( 'The API is not connected. Connect it in AI Designer settings.', 'bigspy-ai-theme-wp' ), array( 'status' => 400 ) );
	}
	$text = trim( (string) $r->get_param( 'text' ) );
	if ( '' === $text ) {
		return new WP_Error( 'bst_aid_text', __( 'Write a message first.', 'bigspy-ai-theme-wp' ), array( 'status' => 400 ) );
	}
	$text   = mb_substr( $text, 0, 20000 );
	$effort = in_array( $r->get_param( 'effort' ), array( 'low', 'medium', 'high', 'max' ), true ) ? $r->get_param( 'effort' ) : 'medium';
	$msgs   = bst_aid_task_get( $p->ID, '_bst_aid_messages' );
	$msgs[] = array( 'role' => 'user', 'content' => bst_aid_redact( $text ) );
	bst_aid_task_set( $p->ID, '_bst_aid_messages', $msgs );
	bst_aid_task_set( $p->ID, '_bst_aid_run', array( 'active' => true, 'effort' => $effort, 'steps_left' => bst_aid_effort_steps( $effort ) ) );
	$event = bst_aid_task_log( $p->ID, array( 'kind' => 'user', 'text' => $text ) );
	if ( 1 === count( $msgs ) ) {
		wp_update_post( array( 'ID' => $p->ID, 'post_title' => wp_trim_words( wp_strip_all_tags( $text ), 9, '…' ) ) );
	}
	return rest_ensure_response( array( 'state' => 'running', 'events' => array( $event ), 'task' => bst_aid_task_summary( get_post( $p->ID ) ) ) );
}

function bst_aid_rest_task_stop( WP_REST_Request $r ) {
	$p = bst_aid_task( $r['id'] );
	if ( ! $p ) {
		return new WP_Error( 'bst_aid_task', __( 'Task not found.', 'bigspy-ai-theme-wp' ), array( 'status' => 404 ) );
	}
	$run           = bst_aid_task_get( $p->ID, '_bst_aid_run' );
	$run['active'] = false;
	bst_aid_task_set( $p->ID, '_bst_aid_run', $run );
	$event = bst_aid_task_log( $p->ID, array( 'kind' => 'info', 'text' => __( 'Stopped.', 'bigspy-ai-theme-wp' ) ) );
	return rest_ensure_response( array( 'state' => 'stopped', 'events' => array( $event ) ) );
}

/** End the run with an event. */
function bst_aid_finish( $id, $state, $events, $event = null ) {
	$run           = bst_aid_task_get( $id, '_bst_aid_run' );
	$run['active'] = false;
	bst_aid_task_set( $id, '_bst_aid_run', $run );
	if ( $event ) {
		$events[] = bst_aid_task_log( $id, $event );
	}
	delete_transient( 'bst_aid_lock_' . $id );
	return rest_ensure_response( array( 'state' => $state, 'events' => $events, 'task' => bst_aid_task_summary( get_post( $id ) ) ) );
}

/** History sent to the model: the latest messages, starting at a user message. */
function bst_aid_history( $msgs ) {
	$max = 60;
	if ( count( $msgs ) > $max ) {
		$msgs = array_slice( $msgs, -$max );
		while ( $msgs && 'user' !== ( $msgs[0]['role'] ?? '' ) ) {
			array_shift( $msgs );
		}
	}
	return $msgs;
}

function bst_aid_rest_task_step( WP_REST_Request $r ) {
	$p = bst_aid_task( $r['id'] );
	if ( ! $p ) {
		return new WP_Error( 'bst_aid_task', __( 'Task not found.', 'bigspy-ai-theme-wp' ), array( 'status' => 404 ) );
	}
	$id  = $p->ID;
	$run = bst_aid_task_get( $id, '_bst_aid_run' );
	if ( empty( $run['active'] ) ) {
		return rest_ensure_response( array( 'state' => 'done', 'events' => array(), 'task' => bst_aid_task_summary( $p ) ) );
	}
	if ( get_transient( 'bst_aid_lock_' . $id ) ) {
		return rest_ensure_response( array( 'state' => 'busy', 'events' => array() ) );
	}
	set_transient( 'bst_aid_lock_' . $id, 1, 180 );
	@set_time_limit( 170 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

	$s      = bst_aid_settings();
	$events = array();
	if ( ! bst_aid_ready() ) {
		return bst_aid_finish( $id, 'error', $events, array( 'kind' => 'error', 'text' => __( 'The API is not connected. Connect it in AI Designer settings.', 'bigspy-ai-theme-wp' ) ) );
	}
	$cost = (float) bst_aid_task_get( $id, '_bst_aid_cost', 0 );
	if ( $s['budget_task'] > 0 && $cost >= $s['budget_task'] ) {
		return bst_aid_finish( $id, 'error', $events, array( 'kind' => 'error', 'text' => sprintf( /* translators: %s: USD */ __( 'This task reached its budget of $%s. Raise the limit in Settings to continue.', 'bigspy-ai-theme-wp' ), number_format_i18n( $s['budget_task'], 2 ) ) ) );
	}
	if ( $s['budget_day'] > 0 && bst_aid_spent_today() >= $s['budget_day'] ) {
		return bst_aid_finish( $id, 'error', $events, array( 'kind' => 'error', 'text' => sprintf( /* translators: %s: USD */ __( 'Today\'s budget of $%s is used up. Raise the limit in Settings or continue tomorrow.', 'bigspy-ai-theme-wp' ), number_format_i18n( $s['budget_day'], 2 ) ) ) );
	}
	if ( (int) ( $run['steps_left'] ?? 0 ) <= 0 ) {
		return bst_aid_finish( $id, 'done', $events, array( 'kind' => 'info', 'text' => __( 'Paused after the step limit for this effort. Reply "continue" to keep going, or raise the effort.', 'bigspy-ai-theme-wp' ) ) );
	}

	$msgs     = bst_aid_task_get( $id, '_bst_aid_messages' );
	$messages = array_merge( array( array( 'role' => 'system', 'content' => bst_aid_system_prompt() ) ), bst_aid_history( $msgs ) );
	$res      = bst_aid_chat( $messages, bst_aid_tool_specs(), array( 'model' => $s['text_model'], 'effort' => $run['effort'] ?? 'medium' ) );
	if ( empty( $res['ok'] ) ) {
		return bst_aid_finish( $id, 'error', $events, array( 'kind' => 'error', 'text' => $res['error'] ) );
	}

	// Cost: Kyma reports it in usage; otherwise estimate from the model price (per million tokens).
	$usage = (array) ( $res['data']['usage'] ?? array() );
	$step  = isset( $usage['cost'] ) ? (float) $usage['cost'] : 0.0;
	if ( ! isset( $usage['cost'] ) ) {
		$meta = bst_aid_text_model_meta( $s['text_model'] );
		if ( $meta ) {
			$step = ( (int) ( $usage['prompt_tokens'] ?? 0 ) * $meta['input'] + (int) ( $usage['completion_tokens'] ?? 0 ) * $meta['output'] ) / 1000000;
		}
	}
	bst_aid_task_set( $id, '_bst_aid_cost', $cost + $step );
	bst_aid_add_spend( $step );

	$served = (string) ( $res['headers']['x-kyma-served-model'] ?? '' );
	if ( '' !== $served && $served !== $s['text_model'] ) {
		$events[] = bst_aid_task_log( $id, array( 'kind' => 'info', 'text' => sprintf( /* translators: %s: model id */ __( 'Answered by the fallback model %s.', 'bigspy-ai-theme-wp' ), $served ) ) );
	}

	$msg   = (array) ( $res['data']['choices'][0]['message'] ?? array() );
	$calls = array_values( array_filter( (array) ( $msg['tool_calls'] ?? array() ), function ( $c ) { return ! empty( $c['function']['name'] ); } ) );
	$text  = trim( (string) ( $msg['content'] ?? '' ) );
	$saved = array( 'role' => 'assistant', 'content' => '' !== $text ? $text : null );
	if ( $calls ) {
		$saved['tool_calls'] = $calls;
	}
	$msgs[] = $saved;
	if ( '' !== $text ) {
		$events[] = bst_aid_task_log( $id, array( 'kind' => 'assistant', 'text' => $text, 'cost' => round( $step, 5 ) ) );
	}

	if ( ! $calls ) {
		bst_aid_task_set( $id, '_bst_aid_messages', $msgs );
		return bst_aid_finish( $id, 'done', $events );
	}

	$GLOBALS['bst_aid_task_id'] = $id; // Tools record snapshots and spending on this task.
	foreach ( $calls as $call ) {
		$name   = (string) $call['function']['name'];
		$args   = json_decode( (string) ( $call['function']['arguments'] ?? '{}' ), true );
		$result = bst_aid_run_tool( $name, is_array( $args ) ? $args : array() );
		$links  = $result['links'] ?? array();
		unset( $result['links'] );
		$event = array(
			'kind'  => isset( $result['error'] ) && 'needs_confirmation' !== $result['error'] ? 'tool-error' : 'tool',
			'tool'  => $name,
			'text'  => isset( $result['error'] ) ? (string) ( $result['message'] ?? $result['error'] ) : ( 'pending' === ( $result['status'] ?? '' ) ? (string) $result['message'] : '' ),
			'links' => $links,
		);
		if ( 'show_palette' === $name && ! empty( $result['palettes'] ) ) {
			$event['palettes'] = $result['palettes'];
		}
		if ( ! empty( $result['preview'] ) ) {
			$event['preview'] = $result['preview'];
		}
		$events[] = bst_aid_task_log( $id, $event );
		$msgs[] = array( 'role' => 'tool', 'tool_call_id' => (string) ( $call['id'] ?? '' ), 'content' => mb_substr( wp_json_encode( $result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ), 0, 12000 ) );
	}
	bst_aid_task_set( $id, '_bst_aid_messages', $msgs );
	$run['steps_left'] = (int) $run['steps_left'] - 1;
	bst_aid_task_set( $id, '_bst_aid_run', $run );
	delete_transient( 'bst_aid_lock_' . $id );
	return rest_ensure_response( array( 'state' => 'running', 'events' => $events, 'task' => bst_aid_task_summary( get_post( $id ) ) ) );
}
