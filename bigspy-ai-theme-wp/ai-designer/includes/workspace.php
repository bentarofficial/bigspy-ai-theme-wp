<?php
/**
 * Workspace screen: task list, chat, effort and log. The UI is built by assets/ai-designer.js.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

function bst_aid_workspace_page() {
	echo '<div class="wrap bst-aid bst-aid--workspace"><h1 class="screen-reader-text">' . esc_html__( 'BIGSPY AI Designer', 'bigspy-ai-theme-wp' ) . '</h1><div id="bst-aid-workspace" class="bst-aid-ws"><p>' . esc_html__( 'Loading…', 'bigspy-ai-theme-wp' ) . '</p></div></div>';
}
