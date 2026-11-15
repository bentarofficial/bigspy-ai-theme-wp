<?php
/**
 * App shell shared by the four AI Designer screens (G-207): one bar with the product name,
 * tabs for Workspace / Guidelines / Settings / About, the cached balance, a bell that
 * collects other plugins' admin notices and a full-screen switch.
 *
 * The four wp-admin menu items and URLs stay as they are; this only frames them.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Admin page slug => array( label, capability ). Order is the tab order. */
function bst_aid_screens() {
	return array(
		'bst-ai-designer'   => array( __( 'Workspace', 'bigspy-ai-theme-wp' ), bst_aid_cap() ),
		'bst-ai-guidelines' => array( __( 'Guidelines', 'bigspy-ai-theme-wp' ), bst_aid_cap() ),
		'bst-ai-settings'   => array( __( 'Settings', 'bigspy-ai-theme-wp' ), 'manage_options' ),
		'bst-ai-about'      => array( __( 'About', 'bigspy-ai-theme-wp' ), 'edit_theme_options' ),
	);
}

/** True on one of the AI Designer screens. */
function bst_aid_is_screen() {
	$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return is_admin() && isset( bst_aid_screens()[ $page ] );
}

add_filter(
	'admin_body_class',
	function ( $classes ) {
		return bst_aid_is_screen() ? $classes . ' bst-aid-screen' : $classes;
	}
);

/**
 * Opens the shell: wrapper, the bar and the scrollable body. Close it with bst_aid_shell_close().
 *
 * @param string $page  Admin page slug of the current screen.
 * @param string $title Screen title for screen readers (the bar shows the product name instead).
 */
function bst_aid_shell_open( $page, $title ) {
	$kind = str_replace( 'bst-ai-', '', $page );
	/**
	 * Space in px kept free at the bottom of the AI Designer screens, e.g. for a host's floating chat button.
	 *
	 * @param int $px Default 0.
	 */
	$safe = max( 0, (int) apply_filters( 'bst_aid_safe_bottom', 0 ) );
	echo '<div class="wrap bst-aid bst-aid-app bst-aid-app--' . esc_attr( $kind ) . '" style="--aid-safe-bottom:' . (int) $safe . 'px">';
	echo '<h1 class="screen-reader-text">' . esc_html( $title ) . '</h1>';
	echo '<header class="bst-aid-bar">';
	echo '<a class="bst-aid-brand" href="' . esc_url( admin_url( 'admin.php?page=bst-ai-designer' ) ) . '"><span class="bst-aid-brand-mark" aria-hidden="true"></span><span>' . esc_html__( 'BIGSPY AI Designer', 'bigspy-ai-theme-wp' ) . '</span></a>';
	echo '<nav class="bst-aid-tabs" aria-label="' . esc_attr__( 'AI Designer screens', 'bigspy-ai-theme-wp' ) . '">';
	foreach ( bst_aid_screens() as $slug => $s ) {
		if ( ! current_user_can( $s[1] ) ) {
			continue;
		}
		$on    = $slug === $page;
		$badge = '';
		if ( 'bst-ai-about' === $slug && ! $on && function_exists( 'bst_welcome_data' ) ) {
			$news  = bst_welcome_data( 'designer' )['news_version'] ?? '0';
			$badge = version_compare( (string) get_user_meta( get_current_user_id(), 'bst_welcome_seen_designer', true ), $news, '<' ) ? ' <span class="bst-aid-tab-badge" aria-label="' . esc_attr__( 'New', 'bigspy-ai-theme-wp' ) . '">1</span>' : '';
		}
		echo '<a href="' . esc_url( admin_url( 'admin.php?page=' . $slug ) ) . '"' . ( $on ? ' class="is-active" aria-current="page"' : '' ) . '>' . esc_html( $s[0] ) . $badge . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $badge is built above from escaped parts.
	}
	echo '</nav><span class="bst-aid-bar-gap"></span>';
	// Cached value only: opening a screen never calls the provider.
	$balance = get_transient( 'bst_aid_balance' );
	if ( is_array( $balance ) && isset( $balance['balance'] ) ) {
		echo '<span class="bst-aid-bar-balance' . ( ! empty( $balance['low'] ) ? ' is-low' : '' ) . '" title="' . esc_attr__( 'API balance (cached)', 'bigspy-ai-theme-wp' ) . '">$' . esc_html( number_format_i18n( (float) $balance['balance'], 2 ) ) . '</span>';
	}
	echo '<div class="bst-aid-bell">';
	echo '<button type="button" class="bst-aid-bar-btn" aria-expanded="false" aria-controls="bst-aid-notices" title="' . esc_attr__( 'Notices', 'bigspy-ai-theme-wp' ) . '"><span class="dashicons dashicons-bell" aria-hidden="true"></span><span class="screen-reader-text">' . esc_html__( 'Notices', 'bigspy-ai-theme-wp' ) . '</span><span class="bst-aid-bell-count" hidden></span></button>';
	// WordPress moves admin notices right after .wp-header-end, so they land in this panel instead of pushing the layout.
	echo '<div id="bst-aid-notices" class="bst-aid-notices" hidden><p class="bst-aid-notices-empty">' . esc_html__( 'No notices.', 'bigspy-ai-theme-wp' ) . '</p><hr class="wp-header-end"></div>';
	echo '</div>';
	echo '<button type="button" class="bst-aid-bar-btn bst-aid-theme-btn" title="' . esc_attr__( 'Light or dark theme', 'bigspy-ai-theme-wp' ) . '"><span class="dashicons dashicons-lightbulb" aria-hidden="true"></span><span class="screen-reader-text">' . esc_html__( 'Light or dark theme', 'bigspy-ai-theme-wp' ) . '</span></button>';
	echo '<button type="button" class="bst-aid-bar-btn bst-aid-full-btn" aria-pressed="false" title="' . esc_attr__( 'Full screen (Esc to leave)', 'bigspy-ai-theme-wp' ) . '"><span class="dashicons dashicons-editor-expand" aria-hidden="true"></span><span class="screen-reader-text">' . esc_html__( 'Full screen', 'bigspy-ai-theme-wp' ) . '</span></button>';
	echo '</header>';
	echo '<div class="bst-aid-body">';
}

function bst_aid_shell_close() {
	echo '</div></div>';
}

/* Full screen and the chosen theme are applied before the page paints, so they do not flash. */
add_action(
	'admin_head',
	function () {
		if ( bst_aid_is_screen() ) {
			echo "<script>try{var d=document.documentElement;if(localStorage.getItem('bstAidFull')==='1'){d.classList.add('bst-aid-full');}var t=localStorage.getItem('bstAidTheme');if(t==='dark'||t==='light'){d.setAttribute('data-aid-theme',t);}}catch(e){}</script>\n";
		}
	}
);

/*
 * The tabs replace the sub-items in the WordPress menu: only the main "BIGSPY AI Designer" item shows.
 * The sub-pages stay registered, so their URLs, capabilities and menu highlighting keep working.
 */
add_action(
	'admin_head',
	function () {
		echo '<style id="bst-aid-menu">#toplevel_page_bst-ai-designer .wp-submenu{display:none!important}#toplevel_page_bst-ai-designer>a .wp-menu-name .update-plugins{display:none}</style>' . "\n";
	}
);
