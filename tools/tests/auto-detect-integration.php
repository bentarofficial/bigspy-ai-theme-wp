<?php
require_once '/wordpress/wp-load.php';
$GLOBALS['bst_log'] = array();
function bst_test( $ok, $label ) {
	$GLOBALS['bst_log'][] = ( $ok ? 'ok   ' : 'FAIL ' ) . $label;
}
function bst_scan( $id ) {
	bst_ad_start( $id );
	for ( $i = 0; $i < 200 && 'running' === bst_ad_state( $id )['status']; $i++ ) {
		bst_ad_step( $id );
	}
	return bst_ad_visible_items( $id );
}
function bst_post( $title, $content = 'x', $type = 'post', $meta = array() ) {
	return wp_insert_post( array( 'post_title' => $title, 'post_content' => $content, 'post_status' => 'publish', 'post_type' => $type, 'meta_input' => $meta ) );
}
function bst_mock( $fn ) {
	remove_all_filters( 'pre_http_request' );
	add_filter( 'pre_http_request', $fn, 10, 3 );
}
function bst_body( $code, $body = '' ) {
	return array( 'response' => array( 'code' => $code, 'message' => '' ), 'body' => $body, 'headers' => array(), 'cookies' => array() );
}
try {
	wp_set_current_user( 1 );
	$tests = array( 'keywords', 'h1', 'media', 'links', 'security', 'terms', 'designer' );
	foreach ( $tests as $test ) {
		$fn   = 'bst_t_' . $test;
		if ( function_exists( $fn ) ) {
			$fn();
		}
	}
} catch ( Throwable $e ) {
	bst_test( false, get_class( $e ) . ' ' . $e->getMessage() . ' @' . $e->getLine() );
}
file_put_contents( '/adt/result.txt', implode( "\n", $GLOBALS['bst_log'] ) . "\n" );

function bst_t_keywords() {
	$a = bst_post( 'A', 'x', 'post', array( 'rank_math_focus_keyword' => 'giặt là, phụ một' ) );
	$b = bst_post( 'B', 'x', 'post', array( 'rank_math_focus_keyword' => 'giặt là' ) );
	$c = bst_post( 'C', 'x', 'page', array( '_yoast_wpseo_focuskw' => 'Giat  La' ) );
	bst_post( 'D', 'x', 'post', array( 'rank_math_focus_keyword' => 'khác, giặt là' ) );
	wp_insert_post( array( 'post_title' => 'Draft', 'post_status' => 'draft', 'meta_input' => array( 'rank_math_focus_keyword' => 'giặt là' ) ) );
	$items = bst_scan( 'keywords' );
	$keys  = wp_list_pluck( $items, 'severity', 'key' );
	bst_test( 3 === count( $items ), 'keywords: secondary and draft ignored, 3 pairs' );
	bst_test( 'error' === ( $keys[ 'kw-' . $a . '-' . $b ] ?? '' ), 'keywords: exact match is error' );
	bst_test( 'review' === ( $keys[ 'kw-' . $a . '-' . $c ] ?? '' ), 'keywords: variant needs review' );
	$request = new WP_REST_Request( 'POST' );
	$request['module'] = 'keywords';
	$request->set_param( 'key', 'kw-' . $a . '-' . $b );
	bst_ad_rest_ignore( $request );
	bst_test( 2 === count( bst_ad_visible_items( 'keywords' ) ), 'keywords: ignore hides the pair' );
	bst_test( 2 === count( bst_scan( 'keywords' ) ), 'keywords: ignore survives rescan' );
	wp_set_current_user( 0 );
	bst_test( ! bst_ad_can(), 'REST denied to visitors' );
	wp_set_current_user( 1 );
}

function bst_t_h1() {
	$none = bst_post( 'None' );
	$many = bst_post( 'Many' );
	$same = bst_post( 'Same' );
	$gone = bst_post( 'Gone' );
	$map  = array( $none => '<h2>a</h2><!-- <h1>x</h1> -->', $many => '<h1>One</h1><h1 class="a">Two</h1>', $same => '<h1>Một</h1>', $gone => null );
	bst_mock(
		function ( $pre, $args, $url ) use ( $map ) {
			$id = url_to_postid( $url );
			if ( array_key_exists( $id, $map ) && null === $map[ $id ] ) {
				return bst_body( 500 );
			}
			return bst_body( 200, $map[ $id ] ?? '<h1>Một</h1>' );
		}
	);
	$items = wp_list_pluck( bst_scan( 'h1' ), 'severity', 'key' );
	bst_test( 'error' === ( $items[ 'h1-none-' . $none ] ?? '' ), 'h1: missing is error, comment not counted' );
	bst_test( 'review' === ( $items[ 'h1-many-' . $many ] ?? '' ), 'h1: two tags need review' );
	bst_test( isset( $items[ 'h1-same-' . md5( 'một' ) ] ), 'h1: duplicate across pages' );
	bst_test( isset( $items[ 'h1-fetch-' . $gone ] ), 'h1: failed fetch is reported, not called missing' );
	bst_test( ! isset( $items[ 'h1-none-' . $gone ] ), 'h1: failed fetch not counted as missing' );
}

function bst_att( $name ) {
	$id = wp_insert_attachment( array( 'post_title' => $name, 'post_mime_type' => 'image/png', 'post_status' => 'inherit' ), '2026/10/' . $name . '.png' );
	update_post_meta( $id, '_wp_attached_file', '2026/10/' . $name . '.png' );
	return $id;
}
function bst_t_media() {
	$free  = bst_att( 'free' );
	$tag   = bst_att( 'tag' );
	$attr  = bst_att( 'attr' );
	$thumb = bst_att( 'thumb' );
	$url   = bst_att( 'byurl' );
	$mod   = bst_att( 'mod' );
	$gal   = bst_att( 'gal' );
	$post  = bst_post( 'M', '<img class="wp-image-' . $tag . '"><!-- wp:bigspy/image {"imageId":' . $attr . '} /--><img src="https://x.test/wp-content/uploads/2026/10/byurl-300x200.png">' );
	set_post_thumbnail( $post, $thumb );
	update_post_meta( $post, '_product_image_gallery', $gal . ',0' );
	set_theme_mod( 'header_logo_image', $mod );
	$items = bst_scan( 'media' );
	$found = wp_list_pluck( $items, 'select' );
	bst_test( in_array( $free, $found ), 'media: unused listed' );
	foreach ( array( 'tag' => $tag, 'attr' => $attr, 'thumb' => $thumb, 'url' => $url, 'mod' => $mod, 'gallery' => $gal ) as $label => $id ) {
		bst_test( ! in_array( $id, $found ), 'media: used by ' . $label . ' kept' );
	}
	$request = new WP_REST_Request( 'POST' );
	$request->set_param( 'op', 'delete' );
	$request->set_param( 'ids', array( $free, $tag ) );
	bst_ad_media_action( $request );
	bst_test( 'trash' === get_post_status( $free ) && 'inherit' === get_post_field( 'post_status', $tag ), 'media: delete trashes unused only, not a used file' );
	$flat = bst_att( 'flatsome' );
	$opt  = bst_att( 'inoption' );
	$web  = bst_att( 'webpcopy' );
	$term = bst_att( 'cat' );
	$late = bst_att( 'late' );
	$kept = bst_att( 'kept' );
	$old  = bst_att( 'notscanned' );
	bst_post( 'F', '[ux_banner bg="' . $flat . '"] <img src="/wp-content/webp-express/webp-images/uploads/2026/10/webpcopy.png.webp">' );
	update_option( 'some_plugin_opts', array( 'hero' => array( 'image_id' => $opt ) ) );
	$cat = wp_insert_term( 'C' . wp_rand(), 'category' );
	update_term_meta( $cat['term_id'], 'thumbnail_id', $term );
	$ids = wp_list_pluck( bst_scan( 'media' ), 'select' );
	foreach ( array( 'shortcode' => $flat, 'option' => $opt, 'webp' => $web, 'term' => $term ) as $label => $id ) {
		bst_test( ! in_array( $id, $ids ), 'media: used by ' . $label . ' kept' );
		bst_test( 'used' === get_post_meta( $id, BST_AD_MEDIA_META, true ), 'media: ' . $label . ' marked used' );
	}
	bst_test( in_array( $late, $ids ) && 'unused' === get_post_meta( $late, BST_AD_MEDIA_META, true ), 'media: unused marked' );
	$js = wp_prepare_attachment_for_js( $late );
	bst_test( 'unused' === ( $js['bstUsage'] ?? '' ), 'media: library gets the mark' );
	$_REQUEST['query'] = array( 'bst_usage' => 'unused' );
	$args = apply_filters( 'ajax_query_attachments_args', array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => -1, 'fields' => 'ids' ) );
	unset( $_REQUEST['query'] );
	$list = get_posts( $args );
	bst_test( in_array( $late, $list ) && ! in_array( $tag, $list ), 'media: filter shows unused only' );
	bst_post( 'After scan', '<img src="/wp-content/uploads/2026/10/late-300x200.png">' );
	$ignore = new WP_REST_Request( 'POST' );
	$ignore['module'] = 'media';
	$ignore->set_param( 'key', 'media-' . $kept );
	bst_ad_rest_ignore( $ignore );
	$new = bst_att( 'fresh' );
	$request->set_param( 'ids', array( $late, $kept, $new, $old ) );
	bst_ad_media_action( $request );
	bst_test( 'inherit' === get_post_field( 'post_status', $late ) && 'used' === get_post_meta( $late, BST_AD_MEDIA_META, true ), 'media: file used after the scan is refused' );
	bst_test( 'inherit' === get_post_field( 'post_status', $kept ), 'media: kept file is refused' );
	bst_test( 'inherit' === get_post_field( 'post_status', $new ), 'media: file not scanned yet is refused' );
	bst_test( 'trash' === get_post_status( $old ), 'media: unused file trashed' );
	$extra = bst_ad_public_state( 'media' )['extra']['trashed'];
	bst_test( in_array( $old, wp_list_pluck( $extra, 'id' ) ), 'media: trash is listed' );
	$request->set_param( 'op', 'purge' );
	$request->set_param( 'ids', array( $tag ) );
	bst_ad_media_action( $request );
	bst_test( (bool) get_post( $tag ), 'media: purge refuses files outside the trash' );
	$request->set_param( 'op', 'restore' );
	$request->set_param( 'ids', array( $old ) );
	bst_ad_media_action( $request );
	bst_test( 'inherit' === get_post_field( 'post_status', $old ), 'media: restore brings the file back' );
	$request->set_param( 'op', 'purge' );
	$request->set_param( 'ids', array( $free ) );
	bst_ad_media_action( $request );
	bst_test( ! get_post( $free ), 'media: purge deletes a trashed file' );
	bst_t_atnd();
}

function bst_t_atnd() {
	$bare = bst_att( 'bare' );
	update_post_meta( $bare, '_wp_attachment_image_alt', 'Old' );
	bst_test( ! isset( bst_ad_modules()['atnd'] ), 'atnd: not an auto detect module' );
	bst_test( is_wp_error( bst_atnd_generate( $bare, 'sofa', '', 'English', '' ) ), 'atnd: refused without a connection' );
	bst_test( false !== strpos( bst_atnd_panel( $bare ), 'page=bigspy-ai-designer' ), 'atnd: panel asks to connect' );
	$atts = array();
	update_option( 'bst_aid_settings', array_merge( bst_aid_settings(), array( 'connected' => true, 'text_model' => 'test-model' ) ), false );
	update_option( 'bst_aid_key', bst_aid_encrypt( 'test-key-not-real' ), false );
	bst_mock(
		function ( $pre, $args, $url ) use ( &$atts ) {
			if ( ! empty( $args['body'] ) ) {
				$atts[] = $args['body'];
			}
			return bst_body( 200, wp_json_encode( array( 'choices' => array( array( 'message' => array( 'content' => '```json' . "\n" . '{"alt":"Sofa xanh","title":"Sofa","caption":"Cap","description":"Mo ta"}' . "\n```" ) ) ) ) ) );
		}
	);
	bst_test( false !== strpos( bst_atnd_panel( $bare ), 'bst-atnd-go' ), 'atnd: panel shows Generate when connected' );
	require_once ABSPATH . 'wp-admin/includes/media.php';
	bst_test( isset( get_attachment_fields_to_edit( get_post( $bare ) )['bst_atnd'] ), 'atnd: panel is in the media details' );
	bst_test( is_wp_error( bst_atnd_generate( $bare, '', 'desc', 'English', '' ) ) && ! $atts, 'atnd: keyword required, no API call' );
	$saved = bst_atnd_generate( $bare, 'sofa', 'sofa xanh trong phong khach', 'Japanese', 'not-listed' );
	bst_test( ! is_wp_error( $saved ) && 'Sofa xanh' === get_post_meta( $bare, '_wp_attachment_image_alt', true ) && 'Mo ta' === get_post_field( 'post_content', $bare ) && 'Cap' === get_post_field( 'post_excerpt', $bare ), 'atnd: all four fields written' );
	$sent = json_decode( (string) ( $atts[0] ?? '' ), true );
	bst_test( false !== strpos( (string) ( $atts[0] ?? '' ), 'image_url' ) && false !== strpos( (string) ( $atts[0] ?? '' ), 'Japanese' ) && false !== strpos( (string) ( $atts[0] ?? '' ), 'sofa xanh' ), 'atnd: image, language and description sent' );
	bst_test( 'test-model' === ( $sent['model'] ?? '' ), 'atnd: unknown model falls back to the default' );
	bst_test( 'sofa' === get_post_meta( $bare, '_bst_atnd_keyword', true ), 'atnd: keyword remembered' );
}

function bst_t_links() {
	foreach ( get_posts( array( 'post_type' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) ) as $old ) {
		wp_delete_post( $old, true );
	}
	$post = bst_post( 'L', '<p><a href="https://dead.test/a">Dead</a> <a href="https://ok.test/">Ok</a> <a href="https://blocked.test/">B</a> <a href="mailto:a@b.c">M</a> <a href="#x">H</a> <a class="z" href="https://gone.test/p">Gone text</a></p>' );
	bst_mock(
		function ( $pre, $args, $url ) {
			if ( false !== strpos( $url, 'dead.test' ) || false !== strpos( $url, 'gone.test' ) ) {
				return bst_body( 404 );
			}
			return bst_body( false !== strpos( $url, 'blocked' ) ? 403 : 200 );
		}
	);
	$items = wp_list_pluck( bst_scan( 'links' ), 'severity', 'title' );
	bst_test( 3 === count( $items ), 'links: mailto, anchor and live links skipped' );
	bst_test( 'error' === ( $items['https://dead.test/a'] ?? '' ), 'links: 404 is broken' );
	bst_test( 'review' === ( $items['https://blocked.test/'] ?? '' ), 'links: 403 only needs review' );
	$request = new WP_REST_Request( 'POST' );
	$request->set_param( 'op', 'replace' );
	$request->set_param( 'url', 'https://dead.test/a' );
	$request->set_param( 'to', 'https://new.test/a' );
	bst_ad_links_action( $request );
	$request->set_param( 'op', 'remove' );
	$request->set_param( 'url', 'https://gone.test/p' );
	bst_ad_links_action( $request );
	$content = get_post_field( 'post_content', $post );
	bst_test( false !== strpos( $content, 'href="https://new.test/a">Dead' ), 'links: replace keeps text' );
	bst_test( false === strpos( $content, 'gone.test' ) && false !== strpos( $content, 'Gone text' ), 'links: remove keeps text' );
	bst_test( false !== strpos( $content, 'https://ok.test/' ), 'links: other links untouched' );
	bst_test( 1 === count( bst_ad_visible_items( 'links' ) ), 'links: fixed items leave the list' );
}
function bst_t_security() {
	$uploads = wp_get_upload_dir()['basedir'];
	wp_mkdir_p( $uploads . '/2026' );
	file_put_contents( $uploads . '/2026/shell.php', '<?php echo 1;' );
	wp_mkdir_p( WPMU_PLUGIN_DIR );
	file_put_contents( WPMU_PLUGIN_DIR . '/bad.php', '<?php eval(base64_decode($x));' );
	file_put_contents( WPMU_PLUGIN_DIR . '/good.php', '<?php echo strlen("eval");' );
	$changed = 'wp-includes/version.php';
	bst_mock(
		function ( $pre, $args, $url ) use ( $changed ) {
			if ( false !== strpos( $url, 'core/checksums' ) ) {
				return bst_body( 200, wp_json_encode( array( 'checksums' => array( $changed => 'deadbeef', 'index.php' => md5_file( ABSPATH . 'index.php' ), 'wp-gone.php' => 'x' ) ) ) );
			}
			return bst_body( 404 );
		}
	);
	$rogue  = wp_insert_user( array( 'user_login' => 'rogue', 'user_pass' => wp_generate_password(), 'role' => 'administrator' ) );
	$items  = bst_scan( 'security' );
	$titles = wp_list_pluck( $items, 'title' );
	$detail = implode( '|', wp_list_pluck( $items, 'detail' ) );
	bst_test( 'done' === bst_ad_state( 'security' )['status'], 'security: scan finishes' );
	bst_test( in_array( 'WordPress file changed', $titles, true ) && false !== strpos( $detail, $changed ), 'security: changed core file' );
	bst_test( false === strpos( $detail, '|index.php' ) && 0 !== strpos( $detail, 'index.php' ), 'security: intact core file silent' );
	bst_test( false !== strpos( $detail, 'wp-gone.php' ), 'security: missing core file' );
	bst_test( in_array( 'PHP file in uploads', $titles, true ), 'security: PHP in uploads' );
	bst_test( false !== strpos( $detail, 'mu-plugins/bad.php' ), 'security: suspicious code' );
	bst_test( false === strpos( $detail, 'good.php' ), 'security: clean file silent' );
	bst_test( false !== strpos( $detail, 'rogue' ), 'security: new admin' );
	$other = wp_insert_user( array( 'user_login' => 'later', 'user_pass' => wp_generate_password(), 'role' => 'administrator' ) );
	$detail = implode( '|', wp_list_pluck( bst_scan( 'security' ), 'detail' ) );
	bst_test( false !== strpos( $detail, 'later' ) && false === strpos( $detail, 'rogue' ), 'security: baseline flags only newcomers' );
	$changed_count = count( bst_ad_state( 'security' )['items'] );
	bst_test( $changed_count > 0 && file_exists( $uploads . '/2026/shell.php' ), 'security: report only, nothing deleted' );
	require_once ABSPATH . 'wp-admin/includes/user.php';
	wp_delete_user( $rogue );
	wp_delete_user( $other );
}

function bst_t_designer() {
	$tools = bst_aid_tools();
	bst_test( isset( $tools['auto_detect_results'] ), 'designer: tool registered' );
	$r = bst_aid_run_tool( 'auto_detect_results', array( 'check' => 'links' ) );
	bst_test( isset( $r['links']['items'] ) && ! isset( $r['media'] ), 'designer: reads one report ' . substr( wp_json_encode( $r ), 0, 120 ) );
	bst_test( 1 === bst_aid_tool_policy( 'auto_detect_results', array() )['level'], 'designer: read level, no approval card' );
}

function bst_t_terms() {
	register_taxonomy( 'bst_test_tax', 'post', array( 'hierarchical' => true ) );
	add_filter( 'bst_ad_terms_taxonomies', function ( $t ) { $t[] = 'bst_test_tax'; return $t; } );
	$parent = wp_insert_term( 'Empty Parent', 'category' )['term_id'];
	$child  = wp_insert_term( 'Draft Child', 'category', array( 'parent' => $parent ) )['term_id'];
	$used   = wp_insert_term( 'Used Cat', 'category' )['term_id'];
	$menu   = wp_insert_term( 'Menu Cat', 'category' )['term_id'];
	$etag   = wp_insert_term( 'empty-tag', 'post_tag' )['term_id'];
	$xtax   = wp_insert_term( 'Extra Empty', 'bst_test_tax' )['term_id'];
	$draft  = wp_insert_post( array( 'post_title' => 'D', 'post_status' => 'draft', 'post_category' => array( $child ) ) );
	$pub    = bst_post( 'P' );
	wp_set_object_terms( $pub, array( $used ), 'category' );
	wp_set_object_terms( $pub, array( 'busy-tag' ), 'post_tag' );
	$nav = wp_create_nav_menu( 'Test menu' );
	wp_update_nav_menu_item( $nav, 0, array( 'menu-item-object' => 'category', 'menu-item-object-id' => $menu, 'menu-item-type' => 'taxonomy', 'menu-item-status' => 'publish' ) );
	$items = bst_scan( 'terms' );
	$by    = array();
	foreach ( $items as $item ) {
		$by[ $item['key'] ] = $item;
	}
	bst_test( 'done' === bst_ad_state( 'terms' )['status'], 'terms: scan finishes' );
	bst_test( isset( $by[ 'term-' . $parent ], $by[ 'term-' . $child ], $by[ 'term-' . $etag ] ), 'terms: empty category, child and tag listed' );
	bst_test( ! isset( $by[ 'term-' . $used ] ) && ! isset( $by[ 'term-' . (int) get_option( 'default_category' ) ] ), 'terms: used and default category not listed' );
	bst_test( false !== strpos( $by[ 'term-' . $child ]['detail'] ?? '', 'drafts: 1' ), 'terms: draft count shown' );
	bst_test( isset( $by[ 'term-' . $menu ] ) && empty( $by[ 'term-' . $menu ]['select'] ), 'terms: menu term shown, not selectable' );
	bst_test( isset( $by[ 'term-' . $xtax ] ), 'terms: extra taxonomy from filter' );
	$req = new WP_REST_Request( 'POST' );
	$req->set_param( 'op', 'delete' );
	$req->set_param( 'ids', array( $parent, $child, $menu, $used ) );
	bst_ad_terms_action( $req );
	bst_test( ! term_exists( $parent ) && ! term_exists( $child ), 'terms: empty terms deleted' );
	bst_test( term_exists( $menu ) && term_exists( $used ), 'terms: menu and used terms kept on delete' );
	bst_test( 2 === count( bst_ad_terms_log() ), 'terms: deletions logged' );
	$keys = array_keys( bst_ad_terms_log() );
	$req->set_param( 'op', 'restore' );
	$req->set_param( 'ids', $keys );
	bst_ad_terms_action( $req );
	$p2 = get_term_by( 'slug', 'empty-parent', 'category' );
	$c2 = get_term_by( 'slug', 'draft-child', 'category' );
	bst_test( $p2 && $c2 && (int) $c2->parent === (int) $p2->term_id, 'terms: restore keeps hierarchy' );
	bst_test( $c2 && has_term( $c2->term_id, 'category', $draft ), 'terms: restore re-links draft post' );
	bst_test( 0 === count( bst_ad_terms_log() ), 'terms: log cleared after restore' );
}
