<?php
/**
 * Top-level "BIGSPY AI Theme" admin menu: Studio, Theme Options, quick links,
 * System status and Help. Theme Options edit the same theme mods as the Customizer, so a value
 * changed in one place shows up in the other.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------- New options: site tweaks */
add_filter(
	'bst_defaults',
	function ( $d ) {
		return $d + array(
			'tweak_xmlrpc'      => false,
			'tweak_wp_version'  => true,
			'tweak_embeds'      => false,
			'tweak_dashicons'   => false,
			'tweak_self_ping'   => true,
			'site_loader'       => false,
			'site_loader_color' => '',
		);
	}
);

add_filter(
	'bst_customizer_sections',
	function ( $sections ) {
		$sections['bst_tweaks'] = array( 'title' => __( 'WordPress tweaks & site loader', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_advanced', 'priority' => 5 );
		return $sections;
	}
);

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$s = 'bst_tweaks';
		$c = function ( $id, $label, $desc = '' ) use ( $s ) {
			return array( 'id' => $id, 'section' => $s, 'type' => 'checkbox', 'label' => $label, 'description' => $desc );
		};
		$fields[] = array( 'id' => 'bst_g_tweaks', 'section' => $s, 'type' => 'heading', 'label' => __( 'Clean up WordPress', 'bigspy-ai-theme-wp' ) );
		$fields[] = $c( 'tweak_xmlrpc', __( 'Disable XML-RPC', 'bigspy-ai-theme-wp' ), __( 'Blocks a common brute-force target. Leave off if you use the WordPress mobile app or Jetpack.', 'bigspy-ai-theme-wp' ) );
		$fields[] = $c( 'tweak_wp_version', __( 'Hide the WordPress version from the page source', 'bigspy-ai-theme-wp' ) );
		$fields[] = $c( 'tweak_embeds', __( 'Disable oEmbed scripts', 'bigspy-ai-theme-wp' ), __( 'Removes wp-embed. Pasted YouTube links still work through blocks.', 'bigspy-ai-theme-wp' ) );
		$fields[] = $c( 'tweak_dashicons', __( 'Do not load Dashicons for visitors who are not logged in', 'bigspy-ai-theme-wp' ) );
		$fields[] = $c( 'tweak_self_ping', __( 'Stop self-pingbacks (links to your own posts)', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'bst_g_loader', 'section' => $s, 'type' => 'heading', 'label' => __( 'Site loader', 'bigspy-ai-theme-wp' ) );
		$fields[] = $c( 'site_loader', __( 'Show a loading screen until the page is ready', 'bigspy-ai-theme-wp' ), __( 'Hidden automatically after at most 3 seconds.', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'site_loader_color', 'section' => $s, 'type' => 'color', 'label' => __( 'Loader color (empty = primary color)', 'bigspy-ai-theme-wp' ) );
		return $fields;
	},
	26
);

add_action(
	'init',
	function () {
		if ( bst_mod( 'tweak_xmlrpc' ) ) {
			add_filter( 'xmlrpc_enabled', '__return_false' );
			remove_action( 'wp_head', 'rsd_link' );
		}
		if ( bst_mod( 'tweak_wp_version' ) ) {
			remove_action( 'wp_head', 'wp_generator' );
			add_filter( 'the_generator', '__return_empty_string' );
		}
		if ( bst_mod( 'tweak_embeds' ) ) {
			remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
			remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		}
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( bst_mod( 'tweak_dashicons' ) && ! is_user_logged_in() ) {
			wp_deregister_style( 'dashicons' );
		}
		if ( bst_mod( 'tweak_embeds' ) ) {
			wp_deregister_script( 'wp-embed' );
		}
	},
	100
);

add_action(
	'pre_ping',
	function ( &$links ) {
		if ( ! bst_mod( 'tweak_self_ping' ) ) {
			return;
		}
		$home = home_url();
		foreach ( $links as $i => $link ) {
			if ( 0 === strpos( $link, $home ) ) {
				unset( $links[ $i ] );
			}
		}
	}
);

/* Site loader: inline so it shows before any stylesheet loads. */
add_action(
	'wp_body_open',
	function () {
		if ( ! bst_mod( 'site_loader' ) || is_customize_preview() ) {
			return;
		}
		$color = sanitize_hex_color( (string) bst_mod( 'site_loader_color' ) );
		$color = $color ? $color : 'var(--bs-primary,#2563eb)';
		echo '<div id="bs-site-loader" aria-hidden="true" style="position:fixed;inset:0;z-index:100000;display:grid;place-items:center;background:#fff;transition:opacity .35s"><span style="width:44px;height:44px;border:4px solid rgba(0,0,0,.08);border-top-color:' . esc_attr( $color ) . ';border-radius:50%;animation:bs-loader-spin .8s linear infinite"></span></div>';
		echo '<style>@keyframes bs-loader-spin{to{transform:rotate(360deg)}}</style>';
		echo '<script>(function(){var l=document.getElementById("bs-site-loader");function h(){if(!l)return;l.style.opacity="0";setTimeout(function(){l&&l.remove();l=null},400)}window.addEventListener("load",h);setTimeout(h,3000)})();</script>';
	},
	1
);

/* ---------------------------------------------------------------- Menu */
function bst_panel_icon() {
	$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill="black" d="M4 2h7.5a4 4 0 0 1 2.6 7A4.3 4.3 0 0 1 12 18H4zm3 3v3.5h4a1.75 1.75 0 0 0 0-3.5zm0 6.3V15h4.6a1.85 1.85 0 0 0 0-3.7z"/></svg>';
	return 'data:image/svg+xml;base64,' . base64_encode( $svg ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- menu icon.
}

function bst_panel_tabs() {
	return array(
		'scripts'  => array( __( 'Scripts & code', 'bigspy-ai-theme-wp' ), array( 'bst_scripts' ) ),
		'speed'    => array( __( 'Performance', 'bigspy-ai-theme-wp' ), array( 'bst_performance', 'bst_tweaks' ) ),
		'tools'    => array( __( '404 & maintenance', 'bigspy-ai-theme-wp' ), array( 'bst_site_tools' ) ),
		'extras'   => array( __( 'Cookie notice & reading', 'bigspy-ai-theme-wp' ), array( 'bst_site_extras' ) ),
		// Shown on BIGSPY → Settings, not as a Theme Options tab.
		'settings' => array( __( 'Settings', 'bigspy-ai-theme-wp' ), array( 'bst_theme_settings' ) ),
	);
}

add_action(
	'admin_menu',
	function () {
		$cap  = 'edit_theme_options';
		$main = 'bigspy-ai-theme-wp';
		add_menu_page( __( 'BIGSPY AI Studio', 'bigspy-ai-theme-wp' ), __( 'BIGSPY AI Theme', 'bigspy-ai-theme-wp' ), $cap, $main, 'bst_dashboard_page', bst_panel_icon(), 1 );
		add_submenu_page( $main, __( 'BIGSPY AI Studio', 'bigspy-ai-theme-wp' ), __( 'Studio', 'bigspy-ai-theme-wp' ), $cap, $main, 'bst_dashboard_page' );
		add_submenu_page( $main, __( 'Theme Options', 'bigspy-ai-theme-wp' ), __( 'Theme Options', 'bigspy-ai-theme-wp' ), $cap, 'bigspy-options', 'bst_panel_options_page' );
		add_submenu_page( $main, __( 'Customize', 'bigspy-ai-theme-wp' ), __( 'Customize', 'bigspy-ai-theme-wp' ), $cap, 'customize.php' );
		add_submenu_page( $main, __( 'Header builder', 'bigspy-ai-theme-wp' ), __( 'Header builder', 'bigspy-ai-theme-wp' ), $cap, 'customize.php?autofocus[section]=bst_header_builder' );
		add_submenu_page( $main, __( 'Additional CSS', 'bigspy-ai-theme-wp' ), __( 'Custom CSS', 'bigspy-ai-theme-wp' ), $cap, 'customize.php?autofocus[section]=custom_css' );
		add_submenu_page( $main, __( 'Floating contact buttons', 'bigspy-ai-theme-wp' ), __( 'Floating contact buttons', 'bigspy-ai-theme-wp' ), $cap, 'customize.php?autofocus[section]=bst_float' );
		add_submenu_page( $main, __( 'UX Blocks', 'bigspy-ai-theme-wp' ), __( 'UX Blocks', 'bigspy-ai-theme-wp' ), $cap, 'edit.php?post_type=blocks' );
		add_submenu_page( $main, __( 'Settings', 'bigspy-ai-theme-wp' ), __( 'Settings', 'bigspy-ai-theme-wp' ), $cap, 'bigspy-settings', 'bst_panel_settings_page' );
		add_submenu_page( $main, __( 'System status', 'bigspy-ai-theme-wp' ), __( 'System status', 'bigspy-ai-theme-wp' ), $cap, 'bigspy-status', 'bst_panel_status_page' );
		add_submenu_page( $main, __( 'Help & guides', 'bigspy-ai-theme-wp' ), __( 'Help & guides', 'bigspy-ai-theme-wp' ), $cap, 'bigspy-help', 'bst_panel_help_page' );
		add_submenu_page( $main, __( 'About BIGSPY AI Theme', 'bigspy-ai-theme-wp' ), bst_welcome_label( 'theme' ), $cap, 'bigspy-about', 'bst_panel_about_page' );
		if ( ! post_type_exists( 'blocks' ) ) {
			remove_submenu_page( $main, 'edit.php?post_type=blocks' );
		}
	},
	9
);

add_action(
	'admin_enqueue_scripts',
	function ( $hook ) {
		if ( false !== strpos( (string) $hook, 'bigspy' ) ) {
			wp_enqueue_style( 'bst-admin-studio', BST_URI . '/assets/css/admin-studio.css', array(), bst_asset_version( 'assets/css/admin-studio.css' ) );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_add_inline_script( 'wp-color-picker', 'jQuery(function($){$(".bst-panel-color").wpColorPicker();});' );
		}
	}
);

/* ---------------------------------------------------------------- Theme Options */
function bst_panel_fields_for( $sections ) {
	return array_values(
		array_filter(
			bst_customizer_fields(),
			function ( $f ) use ( $sections ) {
				return in_array( $f['section'] ?? '', $sections, true );
			}
		)
	);
}

function bst_panel_sanitize( $field, $raw ) {
	$type = $field['type'] ?? 'text';
	$raw  = is_string( $raw ) ? wp_unslash( $raw ) : $raw;
	switch ( $type ) {
		case 'checkbox':
			return ! empty( $raw );
		case 'number':
			return is_numeric( $raw ) ? $raw + 0 : ( bst_defaults()[ $field['id'] ] ?? 0 );
		case 'select':
		case 'radio':
			$choices = (array) ( $field['choices'] ?? array() );
			return array_key_exists( (string) $raw, $choices ) ? (string) $raw : ( bst_defaults()[ $field['id'] ] ?? '' );
		case 'color':
			return (string) sanitize_hex_color( (string) $raw );
		case 'dropdown-pages':
			return absint( $raw );
		case 'url':
		case 'image':
			return esc_url_raw( (string) $raw );
		case 'html':
			return wp_kses_post( (string) $raw );
		case 'code':
			return bst_sanitize_code( (string) $raw );
		default:
			return sanitize_text_field( (string) $raw );
	}
}

add_action(
	'admin_post_bst_panel_save',
	function () {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to change theme options.', 'bigspy-ai-theme-wp' ) );
		}
		check_admin_referer( 'bst_panel_save' );
		$tabs = bst_panel_tabs();
		$tab  = isset( $_POST['tab'] ) ? sanitize_key( wp_unslash( $_POST['tab'] ) ) : '';
		if ( ! isset( $tabs[ $tab ] ) ) {
			wp_die( esc_html__( 'Unknown options tab.', 'bigspy-ai-theme-wp' ) );
		}
		$input = isset( $_POST['bst'] ) && is_array( $_POST['bst'] ) ? $_POST['bst'] : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per field.
		foreach ( bst_panel_fields_for( $tabs[ $tab ][1] ) as $field ) {
			if ( 'heading' === ( $field['type'] ?? '' ) || empty( $field['id'] ) ) {
				continue;
			}
			set_theme_mod( $field['id'], bst_panel_sanitize( $field, $input[ $field['id'] ] ?? '' ) );
		}
		$page = 'settings' === $tab ? 'bigspy-settings' : 'bigspy-options';
		wp_safe_redirect( add_query_arg( array( 'page' => $page, 'tab' => $tab, 'saved' => 1 ), admin_url( 'admin.php' ) ) );
		exit;
	}
);

function bst_panel_field_html( $field ) {
	$id    = $field['id'];
	$type  = $field['type'] ?? 'text';
	$name  = 'bst[' . $id . ']';
	$value = bst_mod( $id );
	$label = $field['label'] ?? $id;
	$desc  = $field['description'] ?? '';
	if ( 'heading' === $type ) {
		echo '<tr><th colspan="2"><h2 class="bst-panel__group">' . esc_html( $label ) . '</h2>' . ( $desc ? '<p class="description">' . wp_kses_post( $desc ) . '</p>' : '' ) . '</th></tr>';
		return;
	}
	echo '<tr><th scope="row"><label for="bst-' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label></th><td>';
	switch ( $type ) {
		case 'checkbox':
			echo '<label><input type="checkbox" id="bst-' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="1" ' . checked( (bool) $value, true, false ) . '> ' . esc_html__( 'On', 'bigspy-ai-theme-wp' ) . '</label>';
			break;
		case 'select':
		case 'radio':
			echo '<select id="bst-' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '">';
			foreach ( (array) ( $field['choices'] ?? array() ) as $k => $v ) {
				echo '<option value="' . esc_attr( $k ) . '" ' . selected( (string) $value, (string) $k, false ) . '>' . esc_html( $v ) . '</option>';
			}
			echo '</select>';
			break;
		case 'code':
		case 'html':
			echo '<textarea id="bst-' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" rows="7" class="large-text code" spellcheck="false">' . esc_textarea( (string) $value ) . '</textarea>';
			break;
		case 'number':
			echo '<input type="number" id="bst-' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $value ) . '" class="small-text">';
			break;
		case 'color':
			echo '<input type="text" id="bst-' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $value ) . '" class="bst-panel-color">';
			break;
		case 'dropdown-pages':
			wp_dropdown_pages( array( 'name' => $name, 'id' => 'bst-' . $id, 'selected' => (int) $value, 'show_option_none' => __( '— None —', 'bigspy-ai-theme-wp' ), 'option_none_value' => 0 ) ); // phpcs:ignore
			break;
		default:
			echo '<input type="' . ( in_array( $type, array( 'url', 'image' ), true ) ? 'url' : 'text' ) . '" id="bst-' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $value ) . '" class="regular-text">';
	}
	if ( $desc ) {
		echo '<p class="description">' . wp_kses_post( $desc ) . '</p>';
	}
	echo '</td></tr>';
}

function bst_panel_header( $title, $lead ) {
	echo '<div class="wrap bst-panel"><h1>' . esc_html( $title ) . '</h1><p class="bst-panel__lead">' . esc_html( $lead ) . '</p>';
}

/** Read-only overview of the theme and its update source. */
function bst_panel_about_page() {
	bst_welcome_screen( 'theme' );
}

function bst_panel_options_page() {
	$tabs = bst_panel_tabs();
	$tab  = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'scripts'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$tab  = ( isset( $tabs[ $tab ] ) && 'settings' !== $tab ) ? $tab : 'scripts';
	bst_panel_header( __( 'Theme Options', 'bigspy-ai-theme-wp' ), __( 'Site-wide settings that do not need a live preview. They are the same settings as in Appearance → Customize → Advanced.', 'bigspy-ai-theme-wp' ) );
	if ( ! empty( $_GET['saved'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings saved.', 'bigspy-ai-theme-wp' ) . '</p></div>';
	}
	echo '<nav class="nav-tab-wrapper">';
	unset( $tabs['settings'] );
	foreach ( $tabs as $key => $t ) {
		echo '<a class="nav-tab' . ( $key === $tab ? ' nav-tab-active' : '' ) . '" href="' . esc_url( add_query_arg( array( 'page' => 'bigspy-options', 'tab' => $key ), admin_url( 'admin.php' ) ) ) . '">' . esc_html( $t[0] ) . '</a>';
	}
	echo '</nav><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	wp_nonce_field( 'bst_panel_save' );
	echo '<input type="hidden" name="action" value="bst_panel_save"><input type="hidden" name="tab" value="' . esc_attr( $tab ) . '"><table class="form-table" role="presentation"><tbody>';
	foreach ( bst_panel_fields_for( $tabs[ $tab ][1] ) as $field ) {
		bst_panel_field_html( $field );
	}
	echo '</tbody></table>';
	submit_button( __( 'Save changes', 'bigspy-ai-theme-wp' ) );
	echo '</form></div>';
}

/* ---------------------------------------------------------------- Settings */
function bst_panel_settings_page() {
	bst_panel_header( __( 'BIGSPY AI settings', 'bigspy-ai-theme-wp' ), __( 'Important theme-wide settings.', 'bigspy-ai-theme-wp' ) );
	if ( ! empty( $_GET['saved'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings saved.', 'bigspy-ai-theme-wp' ) . '</p></div>';
	}
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	wp_nonce_field( 'bst_panel_save' );
	echo '<input type="hidden" name="action" value="bst_panel_save"><input type="hidden" name="tab" value="settings"><table class="form-table" role="presentation"><tbody>';
	foreach ( bst_panel_fields_for( array( 'bst_theme_settings' ) ) as $field ) {
		bst_panel_field_html( $field );
	}
	echo '</tbody></table>';
	submit_button( __( 'Save changes', 'bigspy-ai-theme-wp' ) );
	echo '</form></div>';
}

/* ---------------------------------------------------------------- System status */
function bst_panel_status_page() {
	global $wpdb;
	$theme   = wp_get_theme( get_template() );
	$memory  = wp_convert_hr_to_bytes( WP_MEMORY_LIMIT );
	$plugins = (array) get_option( 'active_plugins', array() );
	$rows    = array(
		__( 'Theme', 'bigspy-ai-theme-wp' )              => array( $theme->get( 'Name' ) . ' ' . $theme->get( 'Version' ), true ),
		__( 'Child theme', 'bigspy-ai-theme-wp' )        => array( is_child_theme() ? wp_get_theme()->get( 'Name' ) : __( 'Not used', 'bigspy-ai-theme-wp' ), true ),
		__( 'WordPress', 'bigspy-ai-theme-wp' )          => array( get_bloginfo( 'version' ), version_compare( get_bloginfo( 'version' ), '6.5', '>=' ) ),
		__( 'PHP', 'bigspy-ai-theme-wp' )                => array( PHP_VERSION, version_compare( PHP_VERSION, '7.4', '>=' ) ),
		__( 'Database', 'bigspy-ai-theme-wp' )           => array( $wpdb->db_server_info(), true ),
		__( 'WordPress memory limit', 'bigspy-ai-theme-wp' ) => array( size_format( $memory ), $memory >= 128 * MB_IN_BYTES ),
		__( 'Max upload size', 'bigspy-ai-theme-wp' )    => array( size_format( wp_max_upload_size() ), wp_max_upload_size() >= 8 * MB_IN_BYTES ),
		__( 'Max execution time', 'bigspy-ai-theme-wp' ) => array( (int) ini_get( 'max_execution_time' ) . 's', 0 === (int) ini_get( 'max_execution_time' ) || (int) ini_get( 'max_execution_time' ) >= 60 ),
		__( 'Site language', 'bigspy-ai-theme-wp' )      => array( get_locale(), true ),
		__( 'Timezone', 'bigspy-ai-theme-wp' )           => array( wp_timezone_string(), 'UTC' !== wp_timezone_string() ),
		__( 'HTTPS', 'bigspy-ai-theme-wp' )              => array( is_ssl() ? __( 'Yes', 'bigspy-ai-theme-wp' ) : __( 'No', 'bigspy-ai-theme-wp' ), is_ssl() ),
		__( 'WooCommerce', 'bigspy-ai-theme-wp' )        => array( class_exists( 'WooCommerce' ) ? WC()->version : __( 'Not active', 'bigspy-ai-theme-wp' ), true ),
		__( 'Active plugins', 'bigspy-ai-theme-wp' )     => array( (string) count( $plugins ), true ),
		__( 'Debug mode', 'bigspy-ai-theme-wp' )         => array( WP_DEBUG ? __( 'On', 'bigspy-ai-theme-wp' ) : __( 'Off', 'bigspy-ai-theme-wp' ), ! WP_DEBUG ),
	);
	bst_panel_header( __( 'System status', 'bigspy-ai-theme-wp' ), __( 'A quick health check of the server and WordPress setup. Items marked with ! are below the recommended value.', 'bigspy-ai-theme-wp' ) );
	echo '<table class="widefat striped bst-panel__status"><tbody>';
	foreach ( $rows as $label => $r ) {
		echo '<tr><td style="width:260px"><strong>' . esc_html( $label ) . '</strong></td><td>' . ( $r[1] ? '<span style="color:#15803d">✔</span> ' : '<span style="color:#b45309;font-weight:700">!</span> ' ) . esc_html( (string) $r[0] ) . '</td></tr>';
	}
	echo '</tbody></table>';
	echo '<h2>' . esc_html__( 'Active plugins', 'bigspy-ai-theme-wp' ) . '</h2><ul style="columns:2;max-width:900px">';
	if ( ! function_exists( 'get_plugin_data' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	foreach ( $plugins as $file ) {
		$path = WP_PLUGIN_DIR . '/' . $file;
		if ( is_readable( $path ) ) {
			$data = get_plugin_data( $path, false, false );
			echo '<li>' . esc_html( $data['Name'] . ' ' . $data['Version'] ) . '</li>';
		}
	}
	echo '</ul></div>';
}

/* ---------------------------------------------------------------- Help */
function bst_panel_help_page() {
	$c     = admin_url( 'customize.php' );
	$links = array(
		array( __( 'Header & menu', 'bigspy-ai-theme-wp' ), __( 'Drag elements in the header builder, set the logo, menus and buttons.', 'bigspy-ai-theme-wp' ), add_query_arg( 'autofocus[panel]', 'bst_header', $c ) ),
		array( __( 'Colors & fonts', 'bigspy-ai-theme-wp' ), __( 'Brand colors, palettes, typography and button styles.', 'bigspy-ai-theme-wp' ), add_query_arg( 'autofocus[panel]', 'bst_style', $c ) ),
		array( __( 'Blog & single post', 'bigspy-ai-theme-wp' ), __( 'Archive layouts, cards, table of contents, author box, related posts.', 'bigspy-ai-theme-wp' ), add_query_arg( 'autofocus[panel]', 'bst_blog', $c ) ),
		array( __( 'Footer', 'bigspy-ai-theme-wp' ), __( 'Footer columns, a footer built with blocks, copyright and floating contact buttons.', 'bigspy-ai-theme-wp' ), add_query_arg( 'autofocus[panel]', 'bst_footer', $c ) ),
		array( __( 'Build pages with blocks', 'bigspy-ai-theme-wp' ), __( 'Add a page and insert BIGSPY AI blocks or a starter page pattern.', 'bigspy-ai-theme-wp' ), admin_url( 'post-new.php?post_type=page' ) ),
		array( __( 'Scripts, speed & maintenance', 'bigspy-ai-theme-wp' ), __( 'Analytics and pixel code, performance tweaks, 404 and maintenance pages.', 'bigspy-ai-theme-wp' ), admin_url( 'admin.php?page=bigspy-options' ) ),
	);
	bst_panel_header( __( 'Help & guides', 'bigspy-ai-theme-wp' ), __( 'Where to find each part of the theme.', 'bigspy-ai-theme-wp' ) );
	echo '<div class="bst-panel__cards" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px;max-width:1100px;margin-top:16px">';
	foreach ( $links as $l ) {
		echo '<a href="' . esc_url( $l[2] ) . '" style="display:block;padding:18px 20px;background:#fff;border:1px solid #dcdcde;border-radius:10px;text-decoration:none;color:inherit"><strong style="font-size:15px;color:#1d2327">' . esc_html( $l[0] ) . '</strong><p style="margin:6px 0 0;color:#50575e">' . esc_html( $l[1] ) . '</p></a>';
	}
	echo '</div></div>';
}
