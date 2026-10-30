<?php
/** Server-enforced tool policy and task-bound, one-use approvals. */
defined( 'ABSPATH' ) || exit;

/** Shared atomic lease for model steps, approval execution and undo. */
function bst_aid_execution_lock( $task ) {
	global $wpdb;
	$key = 'bst_aid_execution_' . (int) $task;
	$old = get_option( $key );
	if ( is_array( $old ) && ( $old['expires'] ?? 0 ) < time() ) {
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name = %s AND option_value = %s", $key, maybe_serialize( $old ) ) );
		wp_cache_delete( $key, 'options' );
	}
	$lease = array( 'token' => wp_generate_uuid4(), 'expires' => time() + 300 );
	if ( ! add_option( $key, $lease, '', false ) ) { return false; }
	$GLOBALS['bst_aid_execution_leases'][ $task ] = $lease;
	return true;
}

function bst_aid_execution_unlock( $task ) {
	global $wpdb;
	$key = 'bst_aid_execution_' . (int) $task;
	$lease = $GLOBALS['bst_aid_execution_leases'][ $task ] ?? null;
	if ( $lease ) {
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name = %s AND option_value = %s", $key, maybe_serialize( $lease ) ) );
		wp_cache_delete( $key, 'options' );
		unset( $GLOBALS['bst_aid_execution_leases'][ $task ] );
	}
}

function bst_aid_execution_busy( $task ) {
	$lease = get_option( 'bst_aid_execution_' . (int) $task );
	return is_array( $lease ) && ( $lease['expires'] ?? 0 ) > time();
}

function bst_aid_tool_policy( $name, $a ) {
	if ( 'site_settings' === $name && empty( $a['values'] ) ) { return array( 'level' => 1, 'group' => 'read', 'write' => false ); }
	$read = array( 'auto_detect_results', 'site_info', 'overview', 'get_knowledge', 'list_content', 'get_post', 'list_blocks', 'style_options', 'block_schema', 'list_patterns', 'get_pattern', 'get_page', 'get_settings', 'get_menus', 'show_palette', 'list_media', 'check_page' );
	if ( in_array( $name, $read, true ) || ( 'header_layout' === $name && 'set' !== ( $a['mode'] ?? 'read' ) ) || ( in_array( $name, array( 'ux_block', 'theme_layout' ), true ) && in_array( $a['mode'] ?? '', array( 'list', 'get' ), true ) ) ) {
		return array( 'level' => 1, 'group' => 'read', 'write' => false );
	}
	$groups = array(
		'create_page' => 'content', 'update_page' => 'content', 'page_sections' => 'content', 'create_post' => 'content', 'update_post' => 'content', 'create_product' => 'content', 'update_product' => 'content', 'ux_block' => 'content', 'theme_layout' => 'content',
		'ImageGen' => 'images', 'image_status' => 'images', 'optimize_image' => 'images', 'save_guidelines' => 'content', 'manual_checklist' => 'content',
		'update_settings' => 'design', 'set_logo' => 'design', 'custom_css' => 'design', 'set_menu' => 'design', 'header_layout' => 'design', 'set_footer' => 'design', 'site_settings' => 'site', 'publish' => 'publish', 'seo_meta' => 'seo', 'install_plugin' => 'plugins', 'budget_extension' => 'budget',
	);
	if ( ! isset( $groups[ $name ] ) ) {
		return array( 'level' => 3, 'group' => 'blocked', 'write' => true );
	}
	$group = $groups[ $name ];
	$level = in_array( $group, array( 'design', 'site', 'publish', 'seo', 'plugins', 'budget' ), true ) ? 2 : 1;
	if ( 'publish' === ( $a['status'] ?? '' ) || ! empty( $a['seo'] ) ) {
		$level = 2;
	}
	if ( in_array( $name, array( 'update_page', 'page_sections', 'update_post', 'update_product', 'ux_block', 'theme_layout', 'optimize_image' ), true ) && ! empty( $a['id'] ) ) {
		$p = get_post( (int) $a['id'] );
		if ( $p && ! in_array( $p->post_status, array( 'draft', 'auto-draft' ), true ) ) {
			$level = 2;
		}
	}
	if ( in_array( $name, array( 'create_page', 'update_page', 'page_sections' ), true ) && bst_aid_code_over_limit( $name, $a ) ) {
		$level = 2;
	}
	$task = (int) ( $GLOBALS['bst_aid_task_id'] ?? 0 );
	if ( 'ImageGen' === $name && $task && (int) bst_aid_task_get( $task, '_bst_aid_image_count', 0 ) >= 3 ) {
		$level = 2;
	}
	// Users may require approval or guidance for a whole group, never weaken a mandatory level.
	$settings = bst_aid_settings();
	$level = max( $level, min( 3, max( 1, (int) ( $settings['permissions'][ $group ] ?? 1 ) ) ) );
	return array( 'level' => $level, 'group' => $group, 'write' => true );
}

function bst_aid_args_hash( $args ) {
	return hash( 'sha256', wp_json_encode( $args ) );
}

function bst_aid_manual_steps( $name ) {
	$steps = array(
		'install_plugin' => __( 'Open Plugins → Add New. Search the exact WordPress.org slug, select Install Now, then Activate.', 'bigspy-ai-theme-wp' ),
		'site_settings' => __( 'Open Settings → Reading for the front page and posts page, Settings → General for title/tagline, or Settings → Media for image sizes. Save Changes.', 'bigspy-ai-theme-wp' ),
		'set_footer' => __( 'Publish the chosen UX Block or page. Open Appearance → Customize → Footer, choose the source, then Publish.', 'bigspy-ai-theme-wp' ),
		'set_menu' => __( 'Open Appearance → Menus, edit the menu and assign its display location. In Customize → Header builder, add the matching menu element.', 'bigspy-ai-theme-wp' ),
		'seo_meta' => __( 'Edit the page or post. Open the Rank Math or Yoast panel, edit the SEO title and description, then save.', 'bigspy-ai-theme-wp' ),
		'publish' => __( 'Open the content editor, review the preview, then click Publish.', 'bigspy-ai-theme-wp' ),
	);
	return $steps[ $name ] ?? __( 'Open the linked content editor or Appearance → Customize, review the proposed values below, apply them and save.', 'bigspy-ai-theme-wp' );
}

function bst_aid_permission_gate( $name, $args ) {
	if ( 'site_settings' === $name && array_diff( array_keys( (array) ( $args['values'] ?? array() ) ), bst_aid_site_options() ) ) {
		return array( 'error' => 'forbidden', 'message' => 'This WordPress option is outside the explicit whitelist.' );
	}
	if ( 'install_plugin' === $name && ! preg_match( '/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', (string) ( $args['slug'] ?? '' ) ) ) {
		return array( 'error' => 'forbidden', 'message' => 'Only exact WordPress.org slugs are allowed; never external ZIPs or URLs.' );
	}
	$policy = bst_aid_tool_policy( $name, $args );
	$task   = (int) ( $GLOBALS['bst_aid_task_id'] ?? 0 );
	if ( $policy['level'] >= 3 ) {
		return array( 'error' => 'forbidden', 'message' => __( 'Guidance only: this action cannot be executed by AI Designer.', 'bigspy-ai-theme-wp' ) );
	}
	if ( ! $policy['write'] ) {
		return null;
	}
	if ( ! $task || ! bst_aid_task( $task ) ) {
		return array( 'error' => 'task_required' );
	}
	if ( in_array( $name, array( 'ImageGen', 'image_status' ), true ) ) {
		$limits = bst_aid_effective_budgets( $task );
		if ( ( $limits['budget_task'] > 0 && (float) bst_aid_task_get( $task, '_bst_aid_cost', 0 ) >= $limits['budget_task'] ) || ( $limits['budget_day'] > 0 && bst_aid_spent_today() >= $limits['budget_day'] ) ) {
			return array( 'error' => 'budget', 'message' => 'Budget exhausted. Request budget_extension approval before another image call.' );
		}
	}
	if ( 'advice' === bst_aid_task_get( $task, '_bst_aid_mode', 'design' ) ) {
		return array( 'error' => 'advice_only', 'message' => __( 'Advice only: write tools are disabled for this task.', 'bigspy-ai-theme-wp' ) );
	}
	$run = bst_aid_task_get( $task, '_bst_aid_run' );
	if ( empty( $run['active'] ) && empty( $GLOBALS['bst_aid_approval'] ) ) {
		return array( 'error' => 'stopped' );
	}
	if ( $policy['level'] < 2 ) {
		return null;
	}
	$approved = $GLOBALS['bst_aid_approval'] ?? array();
	if ( ( $approved['task'] ?? 0 ) === $task && ( $approved['tool'] ?? '' ) === $name && hash_equals( (string) ( $approved['hash'] ?? '' ), bst_aid_args_hash( $args ) ) && ( $approved['expires'] ?? 0 ) > time() ) {
		unset( $GLOBALS['bst_aid_approval'] ); // Exactly one invocation may use this grant.
		return null;
	}
	$id = wp_generate_uuid4();
	$p  = array( 'id' => $id, 'task' => $task, 'tool' => $name, 'args' => $args, 'hash' => bst_aid_args_hash( $args ), 'expires' => time() + 900, 'user' => get_current_user_id() );
	$pending = bst_aid_task_get( $task, '_bst_aid_approvals' );
	foreach ( $pending as $existing ) {
		if ( $existing['tool'] === $name && $existing['hash'] === $p['hash'] && $existing['expires'] > time() ) {
			return array( 'status' => 'approval', 'approval' => bst_aid_approval_card( $existing ) );
		}
	}
	$pending[ $id ] = $p;
	bst_aid_task_set( $task, '_bst_aid_approvals', $pending );
	return array( 'status' => 'approval', 'approval' => bst_aid_approval_card( $p ) );
}

function bst_aid_approval_card( $p ) {
	$post = ! empty( $p['args']['id'] ) ? get_post( (int) $p['args']['id'] ) : null;
	return array(
		'id' => $p['id'], 'tool' => $p['tool'], 'expires' => $p['expires'],
		'scope' => __( 'This action can affect the public site or incur service costs.', 'bigspy-ai-theme-wp' ),
		'cost' => in_array( $p['tool'], array( 'ImageGen', 'image_status' ), true ) ? __( 'Provider-dependent; billed to your API account. Review the selected image model price in Settings.', 'bigspy-ai-theme-wp' ) : __( 'No additional AI call for applying this approval; hosting or plugin charges may apply separately.', 'bigspy-ai-theme-wp' ),
		'undo' => __( 'Task undo restores saved content/settings and deactivates a newly activated plugin. API charges and generated media are retained.', 'bigspy-ai-theme-wp' ),
		'before' => $post ? array( 'title' => $post->post_title, 'status' => $post->post_status, 'content' => $post->post_content ) : bst_aid_approval_before( $p ),
		'after' => $p['args'], 'preview' => $post ? get_preview_post_link( $post->ID ) : '', 'steps' => bst_aid_manual_steps( $p['tool'] ),
	);
}

function bst_aid_approval_before( $p ) {
	$out = array();
	if ( 'site_settings' === $p['tool'] || 'update_settings' === $p['tool'] ) {
		foreach ( array_keys( (array) ( $p['args']['values'] ?? array() ) ) as $key ) {
			$out[ $key ] = 'site_settings' === $p['tool'] ? get_option( $key ) : get_theme_mod( $key );
		}
	} elseif ( 'set_footer' === $p['tool'] ) {
		$out = array( 'footer_block' => bst_mod( 'footer_block' ), 'footer_design_page' => bst_mod( 'footer_design_page' ) );
	} elseif ( 'header_layout' === $p['tool'] ) {
		$out = bst_get_header_layout();
	} elseif ( 'set_menu' === $p['tool'] ) {
		$out = bst_aid_tool_get_menus();
	}
	return $out;
}

add_action( 'rest_api_init', function () {
	register_rest_route( bst_aid_rest_ns(), '/tasks/(?P<id>\d+)/approval', array( 'methods' => 'POST', 'permission_callback' => 'bst_aid_rest_can_use', 'callback' => 'bst_aid_rest_approval' ) );
	register_rest_route( bst_aid_rest_ns(), '/tasks/(?P<id>\d+)/checklist', array( 'methods' => 'POST', 'permission_callback' => 'bst_aid_rest_can_use', 'callback' => function ( WP_REST_Request $r ) {
		if ( ! bst_aid_task( $r['id'] ) ) { return new WP_Error( 'task', 'Task not found.', array( 'status' => 404 ) ); }
		$items = bst_aid_task_get( $r['id'], '_bst_aid_checklist' );
		$i = (int) $r->get_param( 'index' );
		if ( ! isset( $items[ $i ] ) ) { return new WP_Error( 'item', 'Checklist item not found.', array( 'status' => 404 ) ); }
		$items[ $i ]['done'] = (bool) $r->get_param( 'done' );
		bst_aid_task_set( $r['id'], '_bst_aid_checklist', $items );
		return rest_ensure_response( array( 'items' => $items ) );
	} ) );
} );

function bst_aid_rest_approval( WP_REST_Request $r ) {
	$task = (int) $r['id'];
	if ( ! bst_aid_task( $task ) ) {
		return new WP_Error( 'task', 'Task not found.', array( 'status' => 404 ) );
	}
	if ( get_transient( 'bst_aid_lock_' . $task ) ) { return new WP_Error( 'busy', 'Wait for the current step to finish before reviewing.', array( 'status' => 409 ) ); }
	// add_option is atomic across PHP workers, unlike the model or a browser flag.
	if ( ! bst_aid_execution_lock( $task ) ) {
		return new WP_Error( 'busy', 'Approval is already being processed.', array( 'status' => 409 ) );
	}
	try {
		$pending = bst_aid_task_get( $task, '_bst_aid_approvals' );
		$key = (string) $r->get_param( 'approval_id' );
		$p = $pending[ $key ] ?? null;
		$decision = (string) $r->get_param( 'decision' );
		if ( ! $p || $p['expires'] <= time() || $p['user'] !== get_current_user_id() || ! in_array( $decision, array( 'approve', 'manual', 'skip' ), true ) ) {
			return new WP_Error( 'approval', 'Invalid, expired or already used approval.', array( 'status' => 409 ) );
		}
		unset( $pending[ $key ] );
		bst_aid_task_set( $task, '_bst_aid_approvals', $pending ); // Consume before execution.
		$GLOBALS['bst_aid_task_id'] = $task;
		$result = array( 'status' => $decision );
		if ( 'approve' === $decision ) {
			$GLOBALS['bst_aid_approval'] = $p;
			$result = bst_aid_run_tool( $p['tool'], $p['args'] );
		} elseif ( 'manual' === $decision ) {
			$result['instructions'] = bst_aid_manual_steps( $p['tool'] );
		}
		$event = bst_aid_task_log( $task, array( 'kind' => isset( $result['error'] ) ? 'tool-error' : 'info', 'tool' => $p['tool'], 'text' => $p['tool'] . ': ' . $decision, 'approval_id' => $key, 'approved_by' => get_current_user_id(), 'arguments_hash' => $p['hash'], 'result' => $result ) );
		$msgs = bst_aid_task_get( $task, '_bst_aid_messages' );
		$msgs[] = array( 'role' => 'user', 'content' => 'Approval decision for ' . $p['tool'] . ': ' . bst_aid_tool_content( $result ) . '. Do not repeat this action. Continue only with remaining work.' );
		bst_aid_task_set( $task, '_bst_aid_messages', $msgs );
		return rest_ensure_response( array( 'events' => array( $event ), 'result' => $result, 'task' => bst_aid_task_summary( get_post( $task ) ) ) );
	} finally {
		unset( $GLOBALS['bst_aid_approval'], $GLOBALS['bst_aid_task_id'] );
		bst_aid_execution_unlock( $task );
	}
}
