<?php
/**
 * BIGSPY AI Designer: a chat workspace where a text model designs with BIGSPY blocks,
 * writes content and creates products, and calls ImageGen for pictures.
 *
 * Self-contained: the theme loads this file with one line in functions.php. Deleting the
 * ai-designer folder removes the feature; the theme keeps working. Stored data uses the
 * bst_aid_ prefix and can be removed from Settings.
 *
 * Hard limits: the model can only call the tools registered in includes/tools.php. No tool
 * updates themes/plugins or installs themes, edits files, manages users, changes protected settings,
 * deletes permanently or touches orders, customers and payments.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

define( 'BST_AID_DIR', __DIR__ );
define( 'BST_AID_URI', BST_URI . '/ai-designer' );

require_once BST_AID_DIR . '/includes/settings.php';
require_once BST_AID_DIR . '/includes/client.php';
require_once BST_AID_DIR . '/includes/clean.php';
require_once BST_AID_DIR . '/includes/guidelines.php';
require_once BST_AID_DIR . '/includes/tools.php';
require_once BST_AID_DIR . '/includes/tools-design.php';
require_once BST_AID_DIR . '/includes/tools-images.php';
require_once BST_AID_DIR . '/includes/permissions.php';
require_once BST_AID_DIR . '/includes/tools-site.php';
require_once BST_AID_DIR . '/includes/prompt.php';
require_once BST_AID_DIR . '/includes/tasks.php';
require_once BST_AID_DIR . '/includes/workspace.php';

/** Capability needed to use the AI Designer. */
function bst_aid_cap() {
	return 'edit_pages';
}

/** Admin menu: BIGSPY AI Designer, placed right above the BIGSPY menu (position 1). */
add_action(
	'admin_menu',
	function () {
		$cap = bst_aid_cap();
		add_menu_page( __( 'BIGSPY AI Designer', 'bigspy-ai-theme-wp' ), __( 'BIGSPY AI Designer', 'bigspy-ai-theme-wp' ), $cap, 'bst-ai-designer', 'bst_aid_workspace_page', 'dashicons-art', 0.9 );
		add_submenu_page( 'bst-ai-designer', __( 'Workspace', 'bigspy-ai-theme-wp' ), __( 'Workspace', 'bigspy-ai-theme-wp' ), $cap, 'bst-ai-designer', 'bst_aid_workspace_page' );
		add_submenu_page( 'bst-ai-designer', __( 'Guidelines', 'bigspy-ai-theme-wp' ), __( 'Guidelines', 'bigspy-ai-theme-wp' ), $cap, 'bst-ai-guidelines', 'bst_aid_guidelines_page' );
		add_submenu_page( 'bst-ai-designer', __( 'AI Designer settings', 'bigspy-ai-theme-wp' ), __( 'Settings', 'bigspy-ai-theme-wp' ), 'manage_options', 'bst-ai-settings', 'bst_aid_settings_page' );
		add_submenu_page( 'bst-ai-designer', __( 'Giới thiệu BIGSPY AI Designer', 'bigspy-ai-theme-wp' ), __( 'Giới thiệu', 'bigspy-ai-theme-wp' ), $cap, 'bst-ai-about', 'bst_aid_about_page' );
	},
	9
);

/** Read-only introduction to the AI workspace and its approval flow. */
function bst_aid_about_page() {
	echo '<div class="wrap"><h1>' . esc_html__( 'BIGSPY AI Designer', 'bigspy-ai-theme-wp' ) . '</h1>';
	echo '<p class="description">' . esc_html__( 'Trợ lý thiết kế website tích hợp trong BIGSPY AI Theme WP.', 'bigspy-ai-theme-wp' ) . '</p>';
	echo '<div class="card" style="max-width:760px"><h2>' . esc_html__( 'Có thể làm gì?', 'bigspy-ai-theme-wp' ) . '</h2>';
	echo '<p>' . esc_html__( 'Lên kế hoạch nội dung, thiết kế trang bằng các block BIGSPY, tạo hình ảnh và hỗ trợ SEO ngay trong WordPress.', 'bigspy-ai-theme-wp' ) . '</p>';
	echo '<p>' . esc_html__( 'Các thay đổi lên nội dung đang xuất bản hoặc cài đặt website cần bạn duyệt trong Không gian làm việc trước khi thực hiện.', 'bigspy-ai-theme-wp' ) . '</p>';
	echo '<p><a class="button button-primary" href="' . esc_url( admin_url( 'admin.php?page=bst-ai-designer' ) ) . '">' . esc_html__( 'Mở Không gian làm việc', 'bigspy-ai-theme-wp' ) . '</a> ';
	echo '<a class="button" href="' . esc_url( admin_url( 'admin.php?page=bst-ai-guidelines' ) ) . '">' . esc_html__( 'Xem Tiêu chuẩn', 'bigspy-ai-theme-wp' ) . '</a></p></div></div>';
}

/** REST namespace shared by settings, guidelines and tasks. */
function bst_aid_rest_ns() {
	return 'bst-aid/v1';
}

function bst_aid_rest_can_use() {
	return current_user_can( bst_aid_cap() );
}

function bst_aid_rest_can_manage() {
	return current_user_can( 'manage_options' );
}

/** Shared admin assets for the three AI Designer screens. */
add_action(
	'admin_enqueue_scripts',
	function ( $hook ) {
		if ( false === strpos( (string) $hook, 'bst-ai-' ) && false === strpos( (string) $hook, 'bst-ai-designer' ) ) {
			return;
		}
		$ver = BST_VERSION . '.' . (int) @filemtime( BST_AID_DIR . '/assets/ai-designer.js' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		wp_enqueue_style( 'bst-aid', BST_AID_URI . '/assets/ai-designer.css', array(), $ver );
		wp_enqueue_script( 'bst-aid', BST_AID_URI . '/assets/ai-designer.js', array( 'wp-i18n' ), $ver, true );
		wp_localize_script(
			'bst-aid',
			'bstAid',
			array(
				'rest'        => esc_url_raw( rest_url( bst_aid_rest_ns() ) ),
				'nonce'       => wp_create_nonce( 'wp_rest' ),
				'settingsUrl' => admin_url( 'admin.php?page=bst-ai-settings' ),
				'guidesUrl'   => admin_url( 'admin.php?page=bst-ai-guidelines' ),
				'canManage'   => current_user_can( 'manage_options' ),
			)
		);
	}
);
