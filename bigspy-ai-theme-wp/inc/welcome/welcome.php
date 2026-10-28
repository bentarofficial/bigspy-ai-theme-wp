<?php
/** Shared local-only welcome screens and the product knowledge catalog. */
defined( 'ABSPATH' ) || exit;

function bst_welcome_data( $kind = 'theme' ) {
	$file = 'designer' === $kind ? BST_DIR . '/ai-designer/welcome/data.php' : __DIR__ . '/data.php';
	return is_readable( $file ) ? include $file : array();
}

function bst_welcome_label( $kind ) {
	$data = bst_welcome_data( $kind );
	$label = esc_html__( 'About', 'bigspy-ai-theme-wp' );
	if ( version_compare( (string) get_user_meta( get_current_user_id(), 'bst_welcome_seen_' . $kind, true ), $data['news_version'] ?? '0', '<' ) ) {
		$label .= ' <span class="update-plugins count-1"><span class="plugin-count">1</span></span>';
	}
	return $label;
}

add_action( 'admin_menu', function () {
	global $submenu;
	foreach ( array( 'bigspy-ai-theme-wp' => 'bigspy-about', 'bst-ai-designer' => 'bst-ai-about' ) as $parent => $slug ) {
		foreach ( $submenu[ $parent ] ?? array() as $i => $item ) {
			if ( $slug === $item[2] ) {
				unset( $submenu[ $parent ][ $i ] );
				array_unshift( $submenu[ $parent ], $item );
				break;
			}
		}
	}
}, 99 );

add_action( 'after_switch_theme', function () {
	if ( ! get_option( 'bst_welcome_activated' ) ) {
		update_option( 'bst_welcome_pending', 1, false );
	}
} );
add_action( 'admin_init', function () {
	if ( ! get_option( 'bst_welcome_pending' ) || ! current_user_can( 'edit_theme_options' ) || wp_doing_ajax() || is_network_admin() || ! empty( $_GET['activate-multi'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	update_option( 'bst_welcome_activated', 1, false );
	delete_option( 'bst_welcome_pending' );
	wp_safe_redirect( admin_url( 'admin.php?page=bigspy-about' ) );
	exit;
} );

add_action( 'admin_enqueue_scripts', function () {
	$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( in_array( $page, array( 'bigspy-about', 'bst-ai-about' ), true ) ) {
		wp_enqueue_style( 'bst-welcome', BST_URI . '/inc/welcome/welcome.css', array( 'dashicons' ), BST_VERSION );
		wp_enqueue_script( 'bst-welcome', BST_URI . '/inc/welcome/welcome.js', array(), BST_VERSION, true );
	}
} );

function bst_welcome_screen( $kind = 'theme' ) {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to access this page.', 'bigspy-ai-theme-wp' ), '', array( 'response' => 403 ) );
	}
	$data = bst_welcome_data( $kind );
	update_user_meta( get_current_user_id(), 'bst_welcome_seen_' . $kind, $data['news_version'] );
	echo '<div class="wrap bst-welcome"><header class="bst-welcome__hero"><div><p class="bst-welcome__eyebrow">' . esc_html( 'BIGSPY · ' . BST_VERSION ) . '</p><h1>' . esc_html( $data['title'] ) . '</h1><p>' . esc_html( $data['lead'] ) . '</p></div>';
	if ( 'theme' === $kind ) {
		echo '<img src="' . esc_url( BST_URI . '/screenshot.webp' ) . '" alt="' . esc_attr__( 'BIGSPY theme preview', 'bigspy-ai-theme-wp' ) . '" width="600" height="450">';
	}
	echo '</header>';
	if ( 'theme' === $kind ) {
		bst_welcome_checklist();
		bst_welcome_updates();
	} else {
		$s = bst_aid_settings();
		$balance = get_transient( 'bst_aid_balance' );
		echo '<section class="bst-welcome__status"><h2>' . esc_html__( 'Your AI workspace', 'bigspy-ai-theme-wp' ) . '</h2><p>' . esc_html( bst_aid_ready() ? __( 'Connected', 'bigspy-ai-theme-wp' ) : __( 'Not connected', 'bigspy-ai-theme-wp' ) ) . ' · ' . esc_html( $s['text_model'] ?: __( 'No model selected', 'bigspy-ai-theme-wp' ) ) . '</p><p>' . esc_html( is_array( $balance ) && isset( $balance['balance'] ) ? sprintf( __( 'Last known balance: $%s. Refresh it in Settings.', 'bigspy-ai-theme-wp' ), number_format_i18n( $balance['balance'], 2 ) ) : __( 'Balance has not been checked. Open Settings to refresh it.', 'bigspy-ai-theme-wp' ) ) . '</p><a class="button button-primary" href="' . esc_url( admin_url( 'admin.php?page=bst-ai-designer' ) ) . '">' . esc_html__( 'Open Workspace', 'bigspy-ai-theme-wp' ) . '</a> <a class="button" href="' . esc_url( admin_url( 'admin.php?page=bst-ai-settings' ) ) . '">' . esc_html__( 'Open Settings', 'bigspy-ai-theme-wp' ) . '</a></section>';
	}
	echo '<section class="bst-welcome__news"><h2>' . esc_html__( 'What is new', 'bigspy-ai-theme-wp' ) . '</h2><ul>';
	foreach ( $data['news'] as $news ) { echo '<li>' . esc_html( $news ) . '</li>'; }
	echo '</ul></section><label class="bst-welcome__search">' . esc_html__( 'Search guides', 'bigspy-ai-theme-wp' ) . '<input type="search" data-bst-welcome-search placeholder="' . esc_attr__( 'Try footer, menu, images, or budget…', 'bigspy-ai-theme-wp' ) . '"></label><p class="bst-welcome__results" aria-live="polite" data-bst-welcome-results data-label="' . esc_attr__( 'Guides found: ', 'bigspy-ai-theme-wp' ) . '"></p><div class="bst-welcome__grid">';
	foreach ( $data['features'] as $item ) {
		echo '<details class="bst-welcome__card" data-bst-welcome-card><summary><span class="dashicons dashicons-' . esc_attr( $item['icon'] ) . '" aria-hidden="true"></span><span><strong>' . esc_html( $item['title'] ) . '</strong><small>' . esc_html( $item['summary'] ) . '</small><small class="bst-welcome__badge">' . esc_html( sprintf( __( 'Available since %s', 'bigspy-ai-theme-wp' ), $item['since'] ) ) . '</small></span></summary><div class="bst-welcome__detail"><p>' . esc_html( $item['detail'] ) . '</p><h3>' . esc_html__( 'When to use it', 'bigspy-ai-theme-wp' ) . '</h3><p>' . esc_html( $item['when'] ) . '</p><h3>' . esc_html__( 'Steps', 'bigspy-ai-theme-wp' ) . '</h3><ol>';
		foreach ( $item['steps'] as $step ) { echo '<li>' . esc_html( $step ) . '</li>'; }
		echo '</ol><h3>' . esc_html__( 'Tips and troubleshooting', 'bigspy-ai-theme-wp' ) . '</h3><p>' . esc_html( $item['tips'] ) . '</p>';
		if ( $item['screen'] ) { echo '<p><a class="button" href="' . esc_url( admin_url( $item['screen'] ) ) . '">' . esc_html__( 'Open', 'bigspy-ai-theme-wp' ) . '</a></p>'; }
		foreach ( $item['prompts'] ?? array() as $prompt ) {
			echo '<blockquote>' . esc_html( $prompt ) . '</blockquote><a class="button" href="' . esc_url( add_query_arg( array( 'bst_prompt' => $prompt, 'bst_mode' => $item['task_mode'] ?? 'design' ), admin_url( 'admin.php?page=bst-ai-designer' ) ) ) . '">' . esc_html__( 'Use this prompt', 'bigspy-ai-theme-wp' ) . '</a>';
		}
		echo '</div></details>';
	}
	echo '</div><section class="bst-welcome__roadmap"><h2>' . esc_html__( 'Coming soon', 'bigspy-ai-theme-wp' ) . '</h2><p>' . esc_html( $data['roadmap'] ) . '</p></section><section class="bst-welcome__support"><h2>' . esc_html__( 'Support and documentation', 'bigspy-ai-theme-wp' ) . '</h2><a href="https://phonglammedia.com/giao-dien-wordpress-bigspy-ai-theme-wp/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Product page and Zalo contact', 'bigspy-ai-theme-wp' ) . '</a> · <a href="https://t.me/Phonglamofficial" target="_blank" rel="noopener noreferrer">Telegram @Phonglamofficial</a>';
	if ( 'theme' === $kind && function_exists( 'bst_aid_about_page' ) ) { echo '<p><a class="button" href="' . esc_url( admin_url( 'admin.php?page=bst-ai-about' ) ) . '">' . esc_html__( 'Explore BIGSPY AI Designer', 'bigspy-ai-theme-wp' ) . '</a></p>'; }
	echo '</section></div>';
}

function bst_welcome_checklist() {
	$menus = get_nav_menu_locations();
	$checks = array(
		array( __( 'Set your logo, colors and fonts', 'bigspy-ai-theme-wp' ), has_custom_logo(), 'customize.php' ),
		array( __( 'Build your header', 'bigspy-ai-theme-wp' ), (bool) get_theme_mod( 'header_layout' ) || (bool) get_theme_mod( 'header_design_page' ), 'customize.php?autofocus[section]=bst_header_builder' ),
		array( __( 'Create and assign a static homepage', 'bigspy-ai-theme-wp' ), 'page' === get_option( 'show_on_front' ) && get_post_status( get_option( 'page_on_front' ) ) === 'publish', 'options-reading.php' ),
		array( __( 'Create and assign a reusable footer', 'bigspy-ai-theme-wp' ), (bool) get_theme_mod( 'footer_block' ) || (bool) get_theme_mod( 'footer_design_page' ), 'edit.php?post_type=blocks' ),
		array( __( 'Assign your Main menu', 'bigspy-ai-theme-wp' ), ! empty( $menus['primary'] ), 'nav-menus.php' ),
	);
	echo '<section class="bst-welcome__status"><h2>' . esc_html__( 'Quick start', 'bigspy-ai-theme-wp' ) . '</h2><p>' . esc_html__( 'Checkmarks reflect saved configuration, not a visual design review.', 'bigspy-ai-theme-wp' ) . '</p><ol class="bst-welcome__checklist">';
	foreach ( $checks as $check ) { echo '<li><span aria-label="' . esc_attr( $check[1] ? __( 'Configured', 'bigspy-ai-theme-wp' ) : __( 'Not configured', 'bigspy-ai-theme-wp' ) ) . '">' . ( $check[1] ? '✓' : '○' ) . '</span> <a href="' . esc_url( admin_url( $check[2] ) ) . '">' . esc_html( $check[0] ) . '</a></li>'; }
	echo '</ol></section>';
}

function bst_welcome_updates() {
	$release = bst_update_release();
	$current = wp_get_theme( get_template() )->get( 'Version' );
	echo '<section class="bst-welcome__status"><h2>' . esc_html__( 'Theme updates', 'bigspy-ai-theme-wp' ) . '</h2><p>' . esc_html( sprintf( __( 'Current version: %s', 'bigspy-ai-theme-wp' ), $current ) ) . '</p><p>' . esc_html( $release ? sprintf( __( 'Latest version: %s', 'bigspy-ai-theme-wp' ), $release['version'] ) : __( 'Could not check GitHub for a valid release. Try checking again.', 'bigspy-ai-theme-wp' ) ) . '</p>';
	if ( current_user_can( 'update_themes' ) ) {
		echo '<a class="button" href="' . esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=bst_check_theme_updates' ), 'bst_check_theme_updates' ) ) . '">' . esc_html__( 'Check for updates', 'bigspy-ai-theme-wp' ) . '</a> <a class="button" href="' . esc_url( admin_url( 'update-core.php' ) ) . '">' . esc_html( $release && version_compare( $release['version'], $current, '>' ) ? __( 'Update now', 'bigspy-ai-theme-wp' ) : __( 'Open WordPress updates', 'bigspy-ai-theme-wp' ) ) . '</a>';
	}
	echo '</section>';
}
