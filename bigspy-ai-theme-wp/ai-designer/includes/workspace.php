<?php
/**
 * Workspace screen: task list, chat, effort and log. The UI is built by assets/ai-designer.js.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

function bst_aid_workspace_page() {
	bst_aid_shell_open( 'bst-ai-designer', __( 'BIGSPY AI Designer', 'bigspy-ai-theme-wp' ) );
	echo '<div id="bst-aid-workspace" class="bst-aid-ws"><p>' . esc_html__( 'Loading…', 'bigspy-ai-theme-wp' ) . '</p></div>';
	bst_aid_shell_close();
}
