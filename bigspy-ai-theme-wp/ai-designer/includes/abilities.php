<?php
/**
 * WordPress Abilities API (WordPress 6.9+) and MCP Adapter bridge for AI Designer.
 *
 * Off by default: the hooks are added by inc/features/abilities.php only while the
 * "AI abilities (MCP)" feature is on. No new logic lives here: every ability wraps one
 * tool from bst_aid_tools() and runs it through bst_aid_run_tool(), so the same
 * capability check, permission gate, block rules and task undo apply.
 *
 * Writes run on one "External AI" task per user, so they can be undone in AI Designer.
 * A call that the permission gate sends for approval is not executed: the ability returns
 * an error asking the user to approve it in WordPress (AI Designer → the external task).
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Ability name (bigspy/<slug>) => tool name. One small set; each takes the tool's schema. */
function bst_aid_ability_map() {
	return apply_filters(
		'bst_aid_ability_map',
		array(
			'site-info'       => 'site_info',
			'list-content'    => 'list_content',
			'get-post'        => 'get_post',
			'block-catalog'   => 'block_catalog',
			'section-recipes' => 'section_recipes',
			'get-settings'    => 'get_settings',
			'update-settings' => 'update_settings',
			'get-page'        => 'get_page',
			'update-page'     => 'update_page',
			'page-sections'   => 'page_sections',
		)
	);
}

function bst_aid_abilities_register_category() {
	if ( ! function_exists( 'wp_register_ability_category' ) ) {
		return;
	}
	wp_register_ability_category(
		'bigspy-ai',
		array(
			'label'       => __( 'BIGSPY AI Designer', 'bigspy-ai-theme-wp' ),
			'description' => __( 'Read the site and edit pages and theme settings with the same rules and undo as AI Designer.', 'bigspy-ai-theme-wp' ),
		)
	);
}

/** JSON Schema of a tool, usable by the Abilities API validator. */
function bst_aid_ability_schema( $params ) {
	$schema = is_array( $params ) ? $params : array( 'type' => 'object' );
	if ( isset( $schema['properties'] ) && ! is_array( $schema['properties'] ) ) {
		$schema['properties'] = (array) $schema['properties'];
	}
	if ( empty( $schema['properties'] ) ) {
		unset( $schema['properties'] );
	}
	$schema['default'] = array();
	return $schema;
}

function bst_aid_abilities_register() {
	if ( ! function_exists( 'wp_register_ability' ) ) {
		return;
	}
	$tools = bst_aid_tools();
	foreach ( bst_aid_ability_map() as $slug => $tool ) {
		if ( ! isset( $tools[ $tool ] ) || ! preg_match( '/^[a-z0-9-]+$/', $slug ) ) {
			continue;
		}
		$t     = $tools[ $tool ];
		$read  = ! bst_aid_tool_policy( $tool, array() )['write'];
		wp_register_ability(
			'bigspy/' . $slug,
			array(
				'label'               => ucfirst( str_replace( '_', ' ', $tool ) ),
				'description'         => (string) $t['description'],
				'category'            => 'bigspy-ai',
				'input_schema'        => bst_aid_ability_schema( $t['parameters'] ?? null ),
				'output_schema'       => array( 'type' => 'object' ),
				'execute_callback'    => function ( $input = null ) use ( $tool ) {
					return bst_aid_ability_execute( $tool, $input );
				},
				'permission_callback' => function () use ( $t ) {
					return current_user_can( $t['cap'] );
				},
				'meta'                => array(
					'show_in_rest' => true,
					'mcp'          => array( 'public' => true ),
					'annotations'  => array(
						'readonly'    => $read,
						'destructive' => false,
						'idempotent'  => $read,
					),
				),
			)
		);
	}
}

/** The user's task for external AI calls; created on first write. */
function bst_aid_ability_task() {
	$user = get_current_user_id();
	$id   = (int) get_user_meta( $user, '_bst_aid_external_task', true );
	$p    = $id ? get_post( $id ) : null;
	if ( $p && 'bst_aid_task' === $p->post_type && (int) $p->post_author === $user ) {
		return $id;
	}
	$id = wp_insert_post( array( 'post_type' => 'bst_aid_task', 'post_status' => 'private', 'post_title' => __( 'External AI (MCP)', 'bigspy-ai-theme-wp' ), 'post_author' => $user ), true );
	if ( is_wp_error( $id ) ) {
		return 0;
	}
	update_user_meta( $user, '_bst_aid_external_task', $id );
	return (int) $id;
}

/** Run one tool for an ability. Errors and pending approvals become WP_Error. */
function bst_aid_ability_execute( $tool, $input ) {
	$args  = is_array( $input ) ? $input : array();
	unset( $args['confirmed'] );
	$write = bst_aid_tool_policy( $tool, $args )['write'];
	$prev  = $GLOBALS['bst_aid_task_id'] ?? null;
	$task  = 0;
	$run   = null;
	if ( $write ) {
		$task = bst_aid_ability_task();
		if ( ! $task ) {
			return new WP_Error( 'bst_task', __( 'Could not create the AI Designer task.', 'bigspy-ai-theme-wp' ) );
		}
		$run = bst_aid_task_get( $task, '_bst_aid_run' );
		bst_aid_task_set( $task, '_bst_aid_run', array_merge( (array) $run, array( 'active' => true ) ) );
		bst_aid_task_log( $task, array( 'kind' => 'info', 'tool' => $tool, 'text' => 'External AI: ' . $tool, 'arguments_hash' => bst_aid_args_hash( $args ) ) );
	}
	$GLOBALS['bst_aid_task_id'] = $task;
	try {
		$result = bst_aid_run_tool( $tool, $args );
	} finally {
		if ( $task ) {
			bst_aid_task_set( $task, '_bst_aid_run', (array) $run );
		}
		$GLOBALS['bst_aid_task_id'] = $prev;
	}
	if ( 'approval' === ( $result['status'] ?? '' ) ) {
		return new WP_Error( 'bst_approval_required', __( 'This change needs approval in WordPress: open AI Designer, task "External AI (MCP)", and approve or skip it.', 'bigspy-ai-theme-wp' ), array( 'status' => 403, 'approval' => $result['approval']['id'] ?? '' ) );
	}
	if ( isset( $result['error'] ) ) {
		$msg = (string) ( $result['message'] ?? $result['error'] );
		return new WP_Error( 'bst_' . sanitize_key( (string) $result['error'] ), $msg, array( 'status' => 400, 'result' => $result ) );
	}
	return wp_is_numeric_array( $result ) ? array( 'items' => $result ) : $result;
}
