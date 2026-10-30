<?php
/** Optional site presentation tools. @package BigspyTheme */
defined( 'ABSPATH' ) || exit;

function bst_site_tools_defaults( $defaults ) {
	return $defaults + array( 'custom_404_page' => 0, 'maintenance_enabled' => false, 'maintenance_page' => 0, 'maintenance_message' => '', 'section_navigation' => false );
}
add_filter( 'bst_defaults', 'bst_site_tools_defaults' );

function bst_site_tools_sections( $sections ) {
	$sections['bst_site_tools'] = array( 'title' => __( 'Site tools: 404 & maintenance', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_advanced', 'priority' => 3 );
	return $sections;
}
add_filter( 'bst_customizer_sections', 'bst_site_tools_sections' );

function bst_site_tools_fields( $fields ) {
	$fields[] = array( 'id' => 'section_navigation', 'section' => 'bst_layout_site', 'type' => 'checkbox', 'label' => __( 'Highlight menu links to page sections', 'bigspy-ai-theme-wp' ), 'description' => __( 'Set an HTML anchor on a block, then link to #anchor in your menu. The active section is highlighted as you scroll.', 'bigspy-ai-theme-wp' ) );
	$fields[] = array( 'id' => 'custom_404_page', 'section' => 'bst_site_tools', 'type' => 'dropdown-pages', 'label' => __( 'Page design for 404 errors', 'bigspy-ai-theme-wp' ), 'description' => __( 'Only public, published pages without a password are shown. The response remains 404. Select no page to use the default design.', 'bigspy-ai-theme-wp' ) );
	$fields[] = array( 'id' => 'maintenance_enabled', 'section' => 'bst_site_tools', 'type' => 'checkbox', 'label' => __( 'Show maintenance screen to visitors', 'bigspy-ai-theme-wp' ), 'description' => __( 'Administrators can still view the site. Visitors receive HTTP 503. Login, admin and API endpoints remain available. Purge your page/CDN cache when switching this option.', 'bigspy-ai-theme-wp' ) );
	$fields[] = array( 'id' => 'maintenance_page', 'section' => 'bst_site_tools', 'type' => 'dropdown-pages', 'label' => __( 'Page design for maintenance', 'bigspy-ai-theme-wp' ), 'description' => __( 'Build a published page with blocks. Its content is shown without the site header/footer. Select no page to use the default screen.', 'bigspy-ai-theme-wp' ) );
	$fields[] = array( 'id' => 'maintenance_message', 'section' => 'bst_site_tools', 'type' => 'text', 'label' => __( 'Maintenance message', 'bigspy-ai-theme-wp' ), 'description' => __( 'Used on the default maintenance screen.', 'bigspy-ai-theme-wp' ) );
	return $fields;
}
add_filter( 'bst_customizer_fields', 'bst_site_tools_fields' );

/** Never expose drafts, private pages or password-protected page content. */
function bst_site_design_page( $key ) {
	$id = absint( bst_mod( $key ) );
	if ( ! $id ) {
		return null;
	}
	$page = get_post( $id );
	return $page instanceof WP_Post && 'page' === $page->post_type && 'publish' === $page->post_status && '' === $page->post_password ? $page : null;
}

/** Render in the selected page's post context; keep the original request/query. */
function bst_site_design_content( $page ) {
	global $post;
	$original = $post;
	$post = $page;
	setup_postdata( $post );
	try {
		return apply_filters( 'the_content', $page->post_content );
	} finally {
		$post = $original;
		if ( $post instanceof WP_Post ) {
			setup_postdata( $post );
		} else {
			wp_reset_postdata();
		}
	}
}

function bst_site_tools_assets() {
	if ( is_page() && bst_mod( 'section_navigation' ) ) {
		wp_enqueue_script( 'bst-section-navigation', BST_URI . '/assets/js/section-navigation.js', array(), bst_asset_version( 'assets/js/section-navigation.js' ), true );
		wp_enqueue_style( 'bst-section-navigation', BST_URI . '/assets/css/section-navigation.css', array( 'bst-main' ), bst_asset_version( 'assets/css/section-navigation.css' ) );
	}
}
add_action( 'wp_enqueue_scripts', 'bst_site_tools_assets' );

function bst_maintenance_redirect() {
	if ( ! bst_mod( 'maintenance_enabled' ) || is_admin() || wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || current_user_can( 'manage_options' ) || is_customize_preview() ) {
		return;
	}
	if ( ! defined( 'DONOTCACHEPAGE' ) ) {
		define( 'DONOTCACHEPAGE', true );
	}
	nocache_headers();
	status_header( 503 );
	header( 'Retry-After: 600' );
	add_filter( 'wp_robots', 'bst_maintenance_robots' );
	add_filter( 'pre_get_document_title', 'bst_maintenance_title' );
	get_template_part( 'template-parts/maintenance' );
	exit;
}
add_action( 'template_redirect', 'bst_maintenance_redirect', 1 );

function bst_maintenance_title() {
	return __( 'We will be back soon', 'bigspy-ai-theme-wp' ) . ' — ' . get_bloginfo( 'name' );
}
function bst_maintenance_robots( $robots ) {
	$robots['noindex'] = true;
	$robots['nofollow'] = true;
	unset( $robots['index'], $robots['follow'] );
	return $robots;
}
function bst_maintenance_notice() {
	if ( current_user_can( 'edit_theme_options' ) && bst_mod( 'maintenance_enabled' ) ) {
		$url = add_query_arg( 'autofocus[section]', 'bst_site_tools', admin_url( 'customize.php' ) );
		echo '<div class="notice notice-warning"><p>' . esc_html__( 'BIGSPY AI maintenance mode is enabled. Visitors cannot browse the storefront.', 'bigspy-ai-theme-wp' ) . ' <a href="' . esc_url( $url ) . '">' . esc_html__( 'Manage maintenance mode', 'bigspy-ai-theme-wp' ) . '</a></p></div>';
	}
}
add_action( 'admin_notices', 'bst_maintenance_notice' );
