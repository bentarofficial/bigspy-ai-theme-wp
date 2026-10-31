<?php
/** WordPress and reusable theme content tools. */
defined( 'ABSPATH' ) || exit;

add_filter( 'bst_aid_tools', function ( $tools ) {
	$s = array( 'type' => 'string' );
	$i = array( 'type' => 'integer' );
	$object = array( 'type' => 'object', 'additionalProperties' => true );
	$defs = array(
		'get_knowledge' => array( 'Read concise action guides. Topics: theme, wordpress.', array( 'topic' => array( 'type' => 'string', 'enum' => array( 'theme', 'wordpress' ) ) ), array( 'topic' ), 'edit_pages' ),
		'overview' => array( 'Discover registered blocks, patterns, Customizer fields, menu locations, UX Blocks, layouts, current header/footer and active plugin names.', array(), array(), 'edit_pages' ),
		'ux_block' => array( 'List/get/create/update reusable UX Blocks. Create drafts first; use publish separately before assigning a footer.', array( 'mode' => array( 'type' => 'string', 'enum' => array( 'list', 'get', 'create', 'update' ) ), 'id' => $i, 'title' => $s, 'blocks' => array( 'type' => 'array', 'items' => $object ) ), array( 'mode' ), 'edit_pages' ),
		'theme_layout' => array( 'List/get/create/update Theme Builder layouts. location/rules must match overview. Creates drafts; use publish separately to activate.', array( 'mode' => array( 'type' => 'string', 'enum' => array( 'list', 'get', 'create', 'update' ) ), 'id' => $i, 'title' => $s, 'blocks' => array( 'type' => 'array', 'items' => $object ), 'location' => $s, 'rules' => array( 'type' => 'array', 'items' => $s ), 'exclude' => array( 'type' => 'array', 'items' => $s ), 'ids' => array( 'type' => 'array', 'items' => $i ), 'exclude_ids' => array( 'type' => 'array', 'items' => $i ), 'cats' => array( 'type' => 'array', 'items' => $i ), 'users' => array( 'type' => 'string', 'enum' => array( 'all', 'in', 'out' ) ), 'devices' => array( 'type' => 'string', 'enum' => array( 'all', 'desktop', 'mobile' ) ), 'priority' => $i ), array( 'mode' ), 'edit_theme_options' ),
		'set_footer' => array( 'Assign a published UX Block or public page, or restore widget footer. Approval required.', array( 'source' => array( 'type' => 'string', 'enum' => array( 'blocks', 'page', 'default' ) ), 'id' => $i ), array( 'source' ), 'edit_theme_options' ),
		'site_settings' => array( 'Read or update whitelisted WordPress reading, title/tagline and media-size options. All writes require approval.', array( 'values' => $object ), array(), 'manage_options' ),
		'install_plugin' => array( 'Install and activate an exact WordPress.org plugin slug only, after approval. First offer manual installation instructions and explain the cost.', array( 'slug' => $s ), array( 'slug' ), 'install_plugins' ),
		'seo_meta' => array( 'Update Rank Math or Yoast SEO title, description and optional focus keyword. Requires approval.', array( 'id' => $i, 'provider' => array( 'type' => 'string', 'enum' => array( 'rankmath', 'yoast' ) ), 'title' => $s, 'description' => $s, 'focus_keyword' => $s ), array( 'id', 'provider' ), 'edit_pages' ),
		'publish' => array( 'Publish an existing draft page, post, product, UX Block or Theme Builder layout. Requires approval.', array( 'id' => $i ), array( 'id' ), 'edit_pages' ),
		'check_page' => array( 'Check rendered markup for H1, headings, SEO, alt text, local broken links, large media and likely mobile overflow. Markup checks do not replace visual testing.', array( 'id' => $i ), array( 'id' ), 'edit_pages' ),
		'manual_checklist' => array( 'Save task checklist items the user should do manually. Preserve previous items when updating.', array( 'items' => array( 'type' => 'array', 'maxItems' => 40, 'items' => array( 'type' => 'object', 'properties' => array( 'text' => $s, 'done' => array( 'type' => 'boolean' ) ), 'required' => array( 'text' ) ) ) ), array( 'items' ), 'edit_pages' ),
		'budget_extension' => array( 'Request explicit approval for extra USD for this task. Daily allowance expires at the WordPress UTC spend-day boundary; global settings are never changed.', array( 'task_usd' => array( 'type' => 'number', 'minimum' => 0, 'maximum' => 100 ), 'day_usd' => array( 'type' => 'number', 'minimum' => 0, 'maximum' => 100 ) ), array(), 'manage_options' ),
	);
	foreach ( $defs as $name => $d ) {
		$tools[ $name ] = array( 'description' => $d[0], 'parameters' => array( 'type' => 'object', 'properties' => $d[1] ? $d[1] : new stdClass(), 'required' => $d[2] ), 'cap' => $d[3], 'callback' => 'bst_aid_tool_' . $name );
	}
	foreach ( $tools as $name => &$tool ) {
		$tool['permission'] = bst_aid_tool_policy( $name, array() );
	}
	return $tools;
}, 100 );

function bst_aid_tool_get_knowledge( $a ) {
	$topic = $a['topic'] ?? '';
	if ( ! in_array( $topic, array( 'theme', 'wordpress' ), true ) ) {
		return array( 'error' => 'Unknown topic.' );
	}
	$result = array( 'topic' => $topic, 'guide' => (string) file_get_contents( BST_AID_DIR . '/knowledge/' . $topic . '.md' ) );
	if ( 'theme' === $topic && function_exists( 'bst_welcome_data' ) ) {
		$result['product_guides'] = array(
			'theme' => bst_welcome_data( 'theme' ),
			'designer' => bst_welcome_data( 'designer' ),
		);
	}
	return $result;
}

function bst_aid_effective_budgets( $task ) {
	$s = bst_aid_settings();
	$extra = bst_aid_task_get( $task, '_bst_aid_extra_budget', array() );
	$s['budget_task'] = $s['budget_task'] > 0 ? $s['budget_task'] + (float) ( $extra['task'] ?? 0 ) : 0;
	$s['budget_day'] = $s['budget_day'] > 0 ? $s['budget_day'] + ( ( $extra['day'] ?? '' ) === gmdate( 'Y-m-d' ) ? (float) ( $extra['daily'] ?? 0 ) : 0 ) : 0;
	return $s;
}

function bst_aid_tool_budget_extension( $a ) {
	$task = (int) $GLOBALS['bst_aid_task_id'];
	$t = (float) ( $a['task_usd'] ?? 0 ); $d = (float) ( $a['day_usd'] ?? 0 );
	if ( ! is_finite( $t ) || ! is_finite( $d ) || $t < 0 || $d < 0 || $t > 100 || $d > 100 || $t + $d <= 0 ) { return array( 'error' => 'Specify positive extra USD (at most 100 per allowance).' ); }
	$extra = bst_aid_task_get( $task, '_bst_aid_extra_budget', array() );
	$extra = array( 'task' => (float) ( $extra['task'] ?? 0 ) + $t, 'day' => gmdate( 'Y-m-d' ), 'daily' => ( ( $extra['day'] ?? '' ) === gmdate( 'Y-m-d' ) ? (float) ( $extra['daily'] ?? 0 ) : 0 ) + $d );
	bst_aid_task_set( $task, '_bst_aid_extra_budget', $extra );
	return array( 'approved_extra_usd' => $extra );
}

function bst_aid_tool_overview() {
	$blocks = array();
	foreach ( WP_Block_Type_Registry::get_instance()->get_all_registered() as $name => $type ) {
		$blocks[] = array( 'name' => $name, 'description' => $type->description );
	}
	$fields = array();
	foreach ( bst_customizer_fields() as $f ) {
		$fields[] = array_intersect_key( $f, array_flip( array( 'id', 'label', 'type', 'section' ) ) );
	}
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$plugins = array();
	foreach ( get_plugins() as $file => $data ) {
		if ( is_plugin_active( $file ) ) {
			$plugins[] = wp_strip_all_tags( $data['Name'] );
		}
	}
	return array( 'blocks' => $blocks, 'patterns' => bst_aid_tool_list_patterns(), 'customizer' => $fields, 'menus' => bst_aid_tool_get_menus(), 'ux_blocks' => bst_aid_reusable( array( 'mode' => 'list' ), 'blocks' ), 'layouts' => bst_aid_reusable( array( 'mode' => 'list' ), 'bst_layout' ), 'layout_locations' => bst_tb_locations(), 'layout_rules' => bst_tb_rule_choices(), 'header' => bst_get_header_layout(), 'footer' => array( 'ux_block' => bst_mod( 'footer_block' ), 'page' => bst_mod( 'footer_design_page' ), 'fallback' => 'widgets + copyright; published Theme Builder matches override this' ), 'reading' => array( 'show_on_front' => get_option( 'show_on_front' ), 'page_on_front' => get_option( 'page_on_front' ), 'page_for_posts' => get_option( 'page_for_posts' ) ), 'plugins' => $plugins );
}

function bst_aid_tool_ux_block( $a ) { return bst_aid_reusable( $a, 'blocks' ); }
function bst_aid_tool_theme_layout( $a ) { return bst_aid_reusable( $a, 'bst_layout' ); }

function bst_aid_reusable( $a, $type ) {
	$mode = $a['mode'] ?? 'list';
	if ( 'list' === $mode ) {
		$out = array();
		foreach ( get_posts( array( 'post_type' => $type, 'post_status' => array( 'publish', 'draft', 'private' ), 'numberposts' => 100 ) ) as $p ) {
			if ( current_user_can( 'edit_post', $p->ID ) ) {
				$out[] = array( 'id' => $p->ID, 'title' => $p->post_title, 'status' => $p->post_status );
			}
		}
		return array( 'items' => $out );
	}
	$p = ! empty( $a['id'] ) ? get_post( (int) $a['id'] ) : null;
	if ( 'create' !== $mode && ( ! $p || $p->post_type !== $type || ! current_user_can( 'edit_post', $p->ID ) ) ) {
		return array( 'error' => 'Content not found or not editable.' );
	}
	$keys = array( 'location', 'rules', 'exclude', 'ids', 'exclude_ids', 'cats', 'users', 'devices', 'priority' );
	if ( 'get' === $mode ) {
		$out = array( 'id' => $p->ID, 'title' => $p->post_title, 'status' => $p->post_status, 'blocks' => bst_aid_compact_blocks( parse_blocks( $p->post_content ) ) );
		if ( 'bst_layout' === $type ) {
			foreach ( $keys as $k ) { $out[ $k ] = get_post_meta( $p->ID, '_bst_tb_' . $k, true ); }
		}
		return $out;
	}
	if ( ! in_array( $mode, array( 'create', 'update' ), true ) ) { return array( 'error' => 'Invalid mode.' ); }
	$meta = array();
	if ( 'bst_layout' === $type ) {
		foreach ( array( 'location' => array_keys( bst_tb_locations() ), 'users' => array( 'all', 'in', 'out' ), 'devices' => array( 'all', 'desktop', 'mobile' ) ) as $k => $valid ) {
			if ( isset( $a[ $k ] ) ) {
				if ( ! in_array( $a[ $k ], $valid, true ) ) { return array( 'error' => 'Invalid layout ' . $k ); }
				$meta[ $k ] = $a[ $k ];
			}
		}
		foreach ( array( 'rules', 'exclude' ) as $k ) {
			if ( isset( $a[ $k ] ) ) {
				if ( array_diff( (array) $a[ $k ], array_keys( bst_tb_rule_choices() ) ) ) { return array( 'error' => 'Invalid layout rules.' ); }
				$meta[ $k ] = implode( ',', $a[ $k ] );
			}
		}
		foreach ( array( 'ids', 'exclude_ids', 'cats' ) as $k ) {
			if ( isset( $a[ $k ] ) ) { $meta[ $k ] = implode( ',', array_filter( array_map( 'absint', (array) $a[ $k ] ) ) ); }
		}
		if ( isset( $a['priority'] ) ) { $meta['priority'] = (string) min( 999, absint( $a['priority'] ) ); }
	}
	$data = array( 'post_type' => $type );
	if ( 'create' === $mode ) {
		if ( empty( $a['title'] ) || ! isset( $a['blocks'] ) ) { return array( 'error' => 'title and blocks required.' ); }
		$data['post_status'] = 'draft';
	} else {
		bst_aid_snapshot_post( $p->ID );
		$data['ID'] = $p->ID;
	}
	if ( isset( $a['title'] ) ) { $data['post_title'] = sanitize_text_field( $a['title'] ); }
	if ( isset( $a['blocks'] ) ) {
		$markup = bst_aid_tree_to_markup( $a['blocks'], 'create' === $mode ? 0 : $p->ID );
		if ( is_wp_error( $markup ) ) { return array( 'error' => $markup->get_error_message() ); }
		$data['post_content'] = wp_slash( $markup['markup'] );
	}
	$id = wp_insert_post( $data, true );
	if ( is_wp_error( $id ) ) { return array( 'error' => $id->get_error_message() ); }
	if ( 'create' === $mode ) { bst_aid_track_created( $id ); }
	foreach ( $meta as $k => $v ) { update_post_meta( $id, '_bst_tb_' . $k, $v ); }
	delete_transient( 'bst_tb_layouts' );
	return array( 'id' => $id, 'status' => get_post_status( $id ), 'links' => bst_aid_links( $id, get_the_title( $id ) ) );
}

function bst_aid_tool_set_footer( $a ) {
	$source = $a['source'] ?? '';
	if ( ! in_array( $source, array( 'blocks', 'page', 'default' ), true ) ) { return array( 'error' => 'Invalid source.' ); }
	$id = absint( $a['id'] ?? 0 );
	if ( 'default' !== $source ) {
		$p = get_post( $id );
		if ( ! $p || $source !== $p->post_type || 'publish' !== $p->post_status || $p->post_password || ! current_user_can( 'edit_post', $id ) ) { return array( 'error' => 'Select a public, published source you may edit.' ); }
	}
	foreach ( array( 'footer_block' => 'blocks', 'footer_design_page' => 'page' ) as $key => $type ) {
		bst_aid_snapshot( 'mod', $key, get_theme_mod( $key, null ) );
		set_theme_mod( $key, $source === $type ? $id : 0 );
	}
	return array( 'saved' => true, 'source' => $source, 'id' => $id );
}

function bst_aid_site_options() {
	return array( 'show_on_front', 'page_on_front', 'page_for_posts', 'blogname', 'blogdescription', 'posts_per_page', 'thumbnail_size_w', 'thumbnail_size_h', 'medium_size_w', 'medium_size_h', 'large_size_w', 'large_size_h' );
}

function bst_aid_tool_site_settings( $a ) {
	$values = (array) ( $a['values'] ?? array() );
	if ( array_diff( array_keys( $values ), bst_aid_site_options() ) ) { return array( 'error' => 'Forbidden option: only the explicit reading, title/tagline and media whitelist is allowed.' ); }
	$clean = array();
	foreach ( $values as $k => $v ) {
		if ( in_array( $k, array( 'blogname', 'blogdescription' ), true ) ) { $clean[ $k ] = sanitize_text_field( $v ); }
		elseif ( 'show_on_front' === $k ) {
			if ( ! in_array( $v, array( 'posts', 'page' ), true ) ) { return array( 'error' => 'Invalid front page mode.' ); }
			$clean[ $k ] = $v;
		} else {
			if ( ! is_numeric( $v ) || (int) $v < 0 || ( 'posts_per_page' === $k && (int) $v < 1 ) ) { return array( 'error' => 'Invalid numeric value.' ); }
			$clean[ $k ] = (int) $v;
		}
		if ( in_array( $k, array( 'page_on_front', 'page_for_posts' ), true ) && $v ) {
			$p = get_post( (int) $v );
			if ( ! $p || 'page' !== $p->post_type || 'publish' !== $p->post_status || $p->post_password ) { return array( 'error' => 'Choose a public published page.' ); }
		}
	}
	$front = $clean['page_on_front'] ?? get_option( 'page_on_front' );
	$posts = $clean['page_for_posts'] ?? get_option( 'page_for_posts' );
	if ( ( $clean['show_on_front'] ?? get_option( 'show_on_front' ) ) === 'page' && ( ! $front || ( $posts && (int) $front === (int) $posts ) ) ) { return array( 'error' => 'Static homepage is required and must differ from posts page.' ); }
	foreach ( $clean as $k => $v ) {
		bst_aid_snapshot( 'option', $k, get_option( $k ) );
		update_option( $k, $v );
	}
	$out = array();
	foreach ( bst_aid_site_options() as $key ) { $out[ $key ] = get_option( $key ); }
	return array( 'values' => $out );
}

function bst_aid_tool_publish( $a ) {
	$p = get_post( absint( $a['id'] ?? 0 ) );
	if ( ! $p || ! in_array( $p->post_type, array( 'page', 'post', 'product', 'blocks', 'bst_layout' ), true ) || ! current_user_can( 'edit_post', $p->ID ) ) { return array( 'error' => 'Not editable.' ); }
	$type = get_post_type_object( $p->post_type );
	if ( ! current_user_can( $type->cap->publish_posts ) || 'draft' !== $p->post_status ) { return array( 'error' => 'Only an editable draft may be published.' ); }
	bst_aid_snapshot_post( $p->ID );
	$r = wp_update_post( array( 'ID' => $p->ID, 'post_status' => 'publish' ), true );
	return is_wp_error( $r ) ? array( 'error' => $r->get_error_message() ) : array( 'id' => $p->ID, 'status' => get_post_status( $p->ID ), 'links' => bst_aid_links( $p->ID, $p->post_title ) );
}

function bst_aid_tool_seo_meta( $a ) {
	$p = get_post( absint( $a['id'] ?? 0 ) );
	if ( ! $p || ! in_array( $p->post_type, array( 'page', 'post', 'product' ), true ) || ! current_user_can( 'edit_post', $p->ID ) ) { return array( 'error' => 'Not editable.' ); }
	$provider = $a['provider'] ?? '';
	if ( ! in_array( $provider, array( 'rankmath', 'yoast' ), true ) ) { return array( 'error' => 'Invalid SEO provider.' ); }
	bst_aid_snapshot_post( $p->ID );
	$keys = 'yoast' === $provider ? array( 'title' => '_yoast_wpseo_title', 'description' => '_yoast_wpseo_metadesc', 'focus_keyword' => '_yoast_wpseo_focuskw' ) : array( 'title' => 'rank_math_title', 'description' => 'rank_math_description', 'focus_keyword' => 'rank_math_focus_keyword' );
	foreach ( $keys as $key => $meta ) { if ( isset( $a[ $key ] ) ) { update_post_meta( $p->ID, $meta, sanitize_text_field( $a[ $key ] ) ); } }
	return array( 'saved' => true, 'id' => $p->ID );
}

function bst_aid_tool_install_plugin( $a ) {
	if ( ! current_user_can( 'install_plugins' ) || ! current_user_can( 'activate_plugins' ) || is_multisite() || ( defined( 'DISALLOW_FILE_MODS' ) && DISALLOW_FILE_MODS ) ) { return array( 'error' => 'Plugin installation is unavailable.' ); }
	$slug = (string) ( $a['slug'] ?? '' );
	if ( ! preg_match( '/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $slug ) ) { return array( 'error' => 'Use a WordPress.org slug, never a URL or ZIP.' ); }
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	$info = plugins_api( 'plugin_information', array( 'slug' => $slug, 'fields' => array( 'sections' => false ) ) );
	if ( is_wp_error( $info ) || empty( $info->download_link ) || 'https' !== wp_parse_url( $info->download_link, PHP_URL_SCHEME ) || 'downloads.wordpress.org' !== wp_parse_url( $info->download_link, PHP_URL_HOST ) ) { return array( 'error' => 'A trusted WordPress.org download could not be verified.' ); }
	$file = '';
	foreach ( get_plugins() as $path => $data ) {
		if ( dirname( $path ) === $slug ) { $file = $path; break; }
	}
	if ( ! $file ) {
		$upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
		$ok = $upgrader->install( $info->download_link );
		if ( is_wp_error( $ok ) || ! $ok ) { return array( 'error' => 'Plugin installation failed; use Plugins → Add New.' ); }
		$file = $upgrader->plugin_info();
	}
	if ( ! $file || 0 !== strpos( $file, $slug . '/' ) ) { return array( 'error' => 'Could not identify installed plugin.' ); }
	if ( ! is_plugin_active( $file ) ) {
		bst_aid_snapshot( 'plugin', $file, false );
		$r = activate_plugin( $file );
		if ( is_wp_error( $r ) ) { return array( 'error' => $r->get_error_message() ); }
	}
	return array( 'plugin' => $file, 'active' => is_plugin_active( $file ), 'undo' => 'Deactivate only; installed files and plugin data are retained.' );
}

function bst_aid_tool_manual_checklist( $a ) {
	$task = (int) ( $GLOBALS['bst_aid_task_id'] ?? 0 );
	$items = array();
	foreach ( array_slice( (array) ( $a['items'] ?? array() ), 0, 40 ) as $item ) { $items[] = array( 'text' => sanitize_text_field( $item['text'] ?? '' ), 'done' => ! empty( $item['done'] ) ); }
	bst_aid_task_set( $task, '_bst_aid_checklist', $items );
	return array( 'items' => $items );
}

function bst_aid_tool_check_page( $a ) {
	$p = get_post( absint( $a['id'] ?? 0 ) );
	if ( ! $p || ! in_array( $p->post_type, array( 'page', 'post', 'product' ), true ) || ! current_user_can( 'edit_post', $p->ID ) ) { return array( 'error' => 'Not editable.' ); }
	$html = do_blocks( $p->post_content );
	$h1 = preg_match_all( '/<h1\b/i', $html );
	$issues = array();
	$template = get_post_meta( $p->ID, '_wp_page_template', true );
	$title_h1 = 'page' !== $p->post_type || ! in_array( $template, array( 'templates/full-width.php', 'templates/blank.php' ), true );
	if ( 1 !== $h1 + ( $title_h1 ? 1 : 0 ) ) { $issues[] = 'Expected one H1, including the template title.'; }
	if ( preg_match( '/\*\*|>\s*H[1-6]:/', $html ) ) { $issues[] = 'Possible Markdown or heading label in content.'; }
	if ( preg_match( '/(?:width|min-width)\s*:\s*(?:[4-9]\d{2}|\d{4,})px/i', $html ) ) { $issues[] = 'Review fixed widths on mobile.'; }
	if ( class_exists( 'DOMDocument' ) ) {
		$doc = new DOMDocument();
		$previous = libxml_use_internal_errors( true );
		$doc->loadHTML( '<?xml encoding="utf-8"?>' . $html );
		libxml_clear_errors(); libxml_use_internal_errors( $previous );
		foreach ( $doc->getElementsByTagName( 'img' ) as $img ) { if ( ! $img->hasAttribute( 'alt' ) ) { $issues[] = 'Image missing alt: ' . $img->getAttribute( 'src' ); } }
		foreach ( $doc->getElementsByTagName( 'a' ) as $link ) {
			$url = $link->getAttribute( 'href' );
			if ( '' === $url || '#' === $url ) { $issues[] = 'Empty/placeholder link.'; }
			if ( wp_parse_url( $url, PHP_URL_HOST ) === wp_parse_url( home_url(), PHP_URL_HOST ) && preg_match( '/[?&]p(?:age_id)?=(\d+)/', $url, $m ) && ! get_post( (int) $m[1] ) ) { $issues[] = 'Missing local content: ' . $url; }
		}
	}
	$seo = array();
	foreach ( array( 'rank_math_title', 'rank_math_description', '_yoast_wpseo_title', '_yoast_wpseo_metadesc' ) as $k ) { $seo[ $k ] = get_post_meta( $p->ID, $k, true ); }
	if ( ! array_filter( $seo ) ) { $issues[] = 'No explicit SEO title/description found (plugin defaults may apply).'; }
	$large = array();
	preg_match_all( '/wp-image-(\d+)/', $html, $ids );
	foreach ( array_unique( $ids[1] ) as $id ) { $file = get_attached_file( (int) $id ); if ( $file && is_file( $file ) && filesize( $file ) > 500000 ) { $large[] = array( 'id' => (int) $id, 'bytes' => filesize( $file ) ); } }
	return array( 'h1_in_content' => $h1, 'template_title_h1' => $title_h1, 'issues' => array_values( array_unique( $issues ) ), 'seo' => $seo, 'large_images' => $large, 'limits' => 'Markup checks only. External links, computed responsive styles and image appearance require browser verification.' );
}
