<?php
require_once '/wordpress/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/template.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
$pass = (int) 'BST_PASS';
function bst_test( $ok, $label ) {
	file_put_contents( '/ft/result.txt', ( $ok ? 'ok   ' : 'FAIL ' ) . $label . "\n", FILE_APPEND );
}
try {
	wp_set_current_user( 1 );
	if ( 1 === $pass ) {
		bst_t_off();
		update_option( 'bst_features', bst_feature_ids() );
	} else {
		foreach ( array( 'on', 'webp', 'smtp', 'related', 'code', 'forms', 'cf7', 'aid', 'hero', 'listing', 'design', 'flatsome', 'accordion' ) as $test ) {
			if ( function_exists( 'bst_t_' . $test ) ) {
				call_user_func( 'bst_t_' . $test );
			}
		}
	}
} catch ( Throwable $e ) {
	bst_test( false, get_class( $e ) . ' ' . $e->getMessage() . ' @' . $e->getLine() );
}

function bst_t_flatsome() {
	bst_test( shortcode_exists( 'ux_text' ) && false !== strpos( do_shortcode( '[ux_text]x[/ux_text]' ), 'x' ), 'flatsome: shortcodes work while on' );
}

function bst_t_accordion() {
	$item = function ( $attrs, $answer = 'A' ) {
		return '<!-- wp:bst/accordion-item ' . wp_json_encode( $attrs ) . ' --><!-- wp:paragraph --><p>' . $answer . '</p><!-- /wp:paragraph --><!-- /wp:bst/accordion-item -->';
	};
	$old = do_blocks( '<!-- wp:bst/accordion --><div>' . $item( array( 'title' => 'Q one', 'open' => true ) ) . $item( array( 'title' => 'Q two' ) ) . '</div><!-- /wp:bst/accordion -->' );
	bst_test( false === strpos( $old, 'bs-accordion__tools' ) && false === strpos( $old, 'data-deeplink' ) && false === strpos( $old, ' id="q-' ) && false === strpos( $old, '--bs-acc-' ) && 1 === substr_count( $old, ' open' ), 'accordion: old content renders as before' );
	$new = do_blocks( '<!-- wp:bst/accordion {"style":"shadow","openMode":"all","deepLink":true,"search":true,"expandAll":true,"columns":2,"iconColor":"#ff0000","titleWeight":700} --><div>' . $item( array( 'title' => 'Q one', 'subtitle' => 'Sub', 'badge' => 'New' ) ) . $item( array( 'title' => 'Q two' ) ) . '</div><!-- /wp:bst/accordion -->' );
	bst_test( false !== strpos( $new, 'bs-accordion--shadow' ) && false !== strpos( $new, 'bs-accordion--cols-2' ) && 2 === substr_count( $new, '<details class="bs-accordion__item" open' ), 'accordion: style, columns, open all' );
	bst_test( false !== strpos( $new, 'id="q-one"' ) && false !== strpos( $new, 'data-deeplink="1"' ) && false !== strpos( $new, 'bs-accordion__search' ) && false !== strpos( $new, 'bs-accordion__all' ), 'accordion: deep link, search, open all button' );
	bst_test( false !== strpos( $new, 'bs-accordion__sub">Sub' ) && false !== strpos( $new, 'bs-accordion__badge">New' ) && false !== strpos( $new, '--bs-acc-icon-c:#ff0000' ) && false !== strpos( $new, '--bs-acc-title-fw:700' ), 'accordion: subtitle, badge, part vars' );
	$first = do_blocks( '<!-- wp:bst/accordion {"openMode":"first"} --><div>' . $item( array( 'title' => 'A1' ) ) . $item( array( 'title' => 'A2', 'open' => true ) ) . '</div><!-- /wp:bst/accordion -->' );
	bst_test( 1 === substr_count( $first, ' open' ) && strpos( $first, ' open' ) < strpos( $first, 'A2' ), 'accordion: open first overrides items' );
}

function bst_t_off() {
	bst_test( array() === (array) get_option( 'bst_features', array() ), 'all features off by default' );
	bst_test( ! function_exists( 'bst_webp_convert' ) && ! function_exists( 'bst_smtp_configure' ) && ! function_exists( 'bst_ir_insert' ) && ! function_exists( 'bst_code_print' ), 'no feature code loaded while off' );
	ob_start();
	bst_features_page();
	$html = ob_get_clean();
	$listed = substr_count( $html, 'name="bst_on[]"' );
	bst_test( 7 === $listed && false === strpos( $html, 'checked' ), 'page lists 7 unticked features (' . $listed . ')' );
	bst_test( ! bst_feature_on( 'flatsome' ) && ! shortcode_exists( 'ux_text' ) && post_type_exists( 'blocks' ) && ! wp_style_is( 'bst-legacy', 'registered' ), 'flatsome: off on a new site, UX Blocks post type kept' );
	delete_option( 'bst_flatsome_checked' );
	$fs = wp_insert_post( array( 'post_title' => 'fs', 'post_status' => 'publish', 'post_type' => 'page', 'post_content' => '[section][ux_text]x[/ux_text][/section]' ) );
	bst_feature_flatsome_migrate();
	bst_test( bst_feature_on( 'flatsome' ), 'flatsome: turns on once when Flatsome content exists' );
	wp_delete_post( $fs, true );
	update_option( 'bst_features', array() );
	bst_test( ! function_exists( 'bst_listing_field' ) && ! function_exists( 'bigspy_dynamic_attributes' ), 'listing: no code loaded while off' );
	bst_test( WP_Block_Type_Registry::get_instance()->is_registered( 'bst/listing-info' ) && '' === render_block( array( 'blockName' => 'bst/listing-info', 'attrs' => array(), 'innerBlocks' => array(), 'innerHTML' => '', 'innerContent' => array() ) ), 'listing: block registered, renders nothing while off' );
	$cats = array_column( get_block_categories( get_post( wp_insert_post( array( 'post_title' => 'c', 'post_status' => 'draft' ) ) ) ), 'slug' );
	bst_test( array( 'bigspy', 'bigspy-list' ) === array_slice( $cats, 0, 2 ), 'listing: BIGSPY AI List category right under BIGSPY AI Builder' );
	bst_features_save( array( 'bst_on' => array( 'webp', 'nope' ) ) );
	bst_test( array( 'webp' ) === get_option( 'bst_features' ), 'save keeps known ids only' );
	wp_set_current_user( 0 );
	bst_test( ! current_user_can( 'manage_options' ), 'visitors lack the save capability' );
	wp_set_current_user( 1 );
	$secret = bst_feature_encrypt( 'p@ss' );
	bst_test( 'p@ss' === bst_feature_decrypt( $secret ) && false === strpos( $secret, 'p@ss' ), 'secret round trip' );
}

function bst_t_on() {
	ob_start();
	bst_features_page();
	$html = ob_get_clean();
	bst_test( substr_count( $html, "checked='checked'" ) >= 5, 'features show as on' );
}

function bst_t_webp() {
	if ( ! bst_webp_supported() ) {
		bst_test( false, 'webp: Playground has no WebP support' );
		return;
	}
	$dir = wp_upload_dir()['path'];
	$img = imagecreatetruecolor( 600, 300 );
	for ( $i = 0; $i < 3000; $i++ ) {
		imagesetpixel( $img, wp_rand( 0, 599 ), wp_rand( 0, 299 ), wp_rand( 0, 0xffffff ) );
	}
	imagejpeg( $img, "$dir/photo.jpg", 95 );
	$r = bst_webp_convert( array( 'file' => "$dir/photo.jpg", 'url' => 'http://x/u/photo.jpg', 'type' => 'image/jpeg' ) );
	bst_test( 'image/webp' === $r['type'] && str_ends_with( $r['file'], 'photo.webp' ) && str_ends_with( $r['url'], '/u/photo.webp' ) && file_exists( $r['file'] ) && ! file_exists( "$dir/photo.jpg" ), 'jpeg becomes webp, original removed' );
	file_put_contents( "$dir/exif.jpg", bst_exif_jpeg( 600, 300 ) );
	$r = bst_webp_convert( array( 'file' => "$dir/exif.jpg", 'url' => 'http://x/exif.jpg', 'type' => 'image/jpeg' ) );
	$s = getimagesize( $r['file'] );
	bst_test( 'image/webp' === $r['type'] && 300 === $s[0] && 600 === $s[1], 'exif orientation applied (' . $s[0] . 'x' . $s[1] . ')' );
	$png = imagecreatetruecolor( 400, 400 );
	imagealphablending( $png, false );
	imagesavealpha( $png, true );
	imagefill( $png, 0, 0, imagecolorallocatealpha( $png, 0, 0, 0, 127 ) );
	for ( $x = 100; $x < 400; $x++ ) {
		for ( $y = 100; $y < 400; $y++ ) {
			imagesetpixel( $png, $x, $y, imagecolorallocatealpha( $png, ( $x + wp_rand( 0, 4 ) ) % 256, ( $y + wp_rand( 0, 4 ) ) % 256, 120, 0 ) );
		}
	}
	imagepng( $png, "$dir/alpha.png" );
	$r = bst_webp_convert( array( 'file' => "$dir/alpha.png", 'url' => 'http://x/alpha.png', 'type' => 'image/png' ) );
	$w = imagecreatefromwebp( $r['file'] );
	$alpha = ( imagecolorat( $w, 2, 2 ) >> 24 ) & 0x7f;
	bst_test( 'image/webp' === $r['type'] && $alpha > 100, 'png keeps transparency (' . $r['type'] . ', alpha ' . $alpha . ', true color ' . (int) imageistruecolor( $w ) . ', ' . _wp_image_editor_choose( array( 'mime_type' => 'image/webp' ) ) . ')' );
	file_put_contents( "$dir/a.gif", 'GIF89a' );
	$r = bst_webp_convert( array( 'file' => "$dir/a.gif", 'url' => 'http://x/a.gif', 'type' => 'image/gif' ) );
	bst_test( 'image/gif' === $r['type'], 'gif untouched' );
	$bad = array( 'file' => "$dir/broken.jpg", 'url' => 'http://x/broken.jpg', 'type' => 'image/jpeg' );
	file_put_contents( $bad['file'], 'not an image' );
	bst_test( bst_webp_convert( $bad ) === $bad && file_exists( $bad['file'] ), 'broken image keeps the original' );
	update_option( 'bst_feature_webp', array( 'quality' => 100 ) );
	$small = imagecreatetruecolor( 8, 8 );
	imagepng( $small, "$dir/tiny.png" );
	$tiny = array( 'file' => "$dir/tiny.png", 'url' => 'http://x/tiny.png', 'type' => 'image/png' );
	$r    = bst_webp_convert( $tiny );
	bst_test( file_exists( $r['file'] ) && count( glob( "$dir/tiny.*" ) ) === 1, 'one file kept for a tiny image (' . $r['type'] . ')' );
	delete_option( 'bst_feature_webp' );
}

function bst_t_smtp() {
	require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
	require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
	require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
	$m = new PHPMailer\PHPMailer\PHPMailer( true );
	bst_smtp_configure( $m );
	bst_test( 'smtp' !== $m->Mailer, 'no host leaves mail unchanged' );
	$pass = "pa'ss\\1";
	bst_features_save(
		array(
			'bst_on'    => bst_feature_ids(),
			'bst_field' => array( 'smtp' => wp_slash( array( 'host' => 'mail.test', 'port' => '465', 'secure' => 'ssl', 'user' => 'u@x.test', 'password' => $pass, 'from' => 'no@x.test', 'from_name' => 'Shop' ) ) ),
		)
	);
	$stored = get_option( 'bst_feature_smtp' );
	bst_test( $pass !== $stored['password'] && false === strpos( $stored['password'], 'ss' ), 'password not stored in plain text' );
	wp_cache_delete( 'alloptions', 'options' );
	bst_test( ! isset( wp_load_alloptions()['bst_feature_smtp'] ), 'settings are not autoloaded' );
	$m = new PHPMailer\PHPMailer\PHPMailer( true );
	bst_smtp_configure( $m );
	bst_test( 'smtp' === $m->Mailer && 'mail.test' === $m->Host && 465 === $m->Port && 'ssl' === $m->SMTPSecure && $m->SMTPAuth && $pass === $m->Password && 'no@x.test' === $m->From && 'Shop' === $m->FromName, 'mailer configured' );
	bst_features_save( array( 'bst_on' => bst_feature_ids(), 'bst_field' => array( 'smtp' => array( 'password' => '', 'host' => 'mail.test', 'secure' => 'ssl', 'user' => 'u@x.test', 'port' => 465 ) ) ) );
	bst_test( $pass === bst_smtp_password(), 'empty password field keeps the saved one' );
	ob_start();
	bst_features_page();
	$html = ob_get_clean();
	bst_test( false === strpos( $html, $stored['password'] ) && false === strpos( $html, "pa'ss" ), 'page never prints the password' );
	bst_test( false !== strpos( $html, 'value="' . BST_FEATURE_PASSWORD_MASK . '"' ), 'saved password shows the mask' );
	bst_features_save( array( 'bst_on' => bst_feature_ids(), 'bst_field' => array( 'smtp' => array( 'password' => BST_FEATURE_PASSWORD_MASK, 'host' => 'mail.test', 'secure' => 'ssl', 'user' => 'u@x.test', 'port' => 465 ) ) ) );
	bst_test( $pass === bst_smtp_password(), 'saving the mask keeps the saved password' );
	bst_test( false !== strpos( $html, 'bst_smtp_test' ), 'test email button shown' );
	bst_test( ! bst_smtp_send_test() && '' !== get_transient( 'bst_smtp_error_1' ), 'failed test keeps the error: ' . get_transient( 'bst_smtp_error_1' ) );
}

function bst_view( $id, $content = null ) {
	global $wp_query, $wp_the_query, $post;
	$wp_query     = new WP_Query( array( 'p' => $id ) );
	$wp_the_query = $wp_query;
	$wp_query->the_post();
	$out = apply_filters( 'the_content', null === $content ? get_post_field( 'post_content', $id ) : $content );
	return $out;
}

function bst_t_related() {
	$cat = wp_insert_term( 'Tin', 'category' )['term_id'];
	$a   = wp_insert_post( array( 'post_title' => 'Bài liên quan', 'post_content' => 'x', 'post_status' => 'publish', 'post_category' => array( $cat ) ) );
	$p   = str_repeat( "<p>Đoạn</p>\n\n", 3 ) . '<blockquote><p>Trích</p><p>Trích 2</p></blockquote>' . "\n\n" . '<div><p>Trong khung</p></div>' . "\n\n" . str_repeat( "<p>Đoạn</p>\n\n", 3 );
	$b   = wp_insert_post( array( 'post_title' => 'Bài chính', 'post_content' => $p, 'post_status' => 'publish', 'post_category' => array( $cat ) ) );
	$out = bst_view( $b );
	bst_test( 1 === substr_count( $out, 'bst-inline-related"' ) && false !== strpos( $out, 'Bài liên quan' ), 'list inserted once' );
	$pos = strpos( $out, '<aside' );
	bst_test( substr_count( substr( $out, 0, $pos ), '<p>Đoạn</p>' ) === 3 && false === strpos( substr( $out, 0, $pos ), 'Trích' ), 'placed after the middle top-level paragraph' );
	bst_test( false === strpos( $out, 'Bài chính</a>' ), 'current post not linked' );
	update_option( 'bst_feature_inline-related', array( 'position' => 'after', 'paragraph' => 1, 'min' => 2, 'count' => 1, 'title' => 'Đọc thêm' ) );
	$out = bst_view( $b );
	bst_test( 1 === substr_count( substr( $out, 0, strpos( $out, '<aside' ) ), '<p>Đoạn</p>' ) && false !== strpos( $out, 'Đọc thêm' ), 'after paragraph setting and title' );
	update_option( 'bst_feature_inline-related', array( 'position' => 'after', 'paragraph' => 50, 'min' => 2 ) );
	$out = trim( bst_view( $b ) );
	bst_test( false !== strpos( $out, '<aside' ) && '</aside>' !== substr( $out, -8 ), 'never placed after the last paragraph' );
	delete_option( 'bst_feature_inline-related' );
	$short = wp_insert_post( array( 'post_title' => 'Ngắn', 'post_content' => '<p>a</p><p>b</p>', 'post_status' => 'publish', 'post_category' => array( $cat ) ) );
	bst_test( false === strpos( bst_view( $short ), 'bst-inline-related' ), 'short post skipped' );
	$group = wp_insert_post( array( 'post_title' => 'Nhóm', 'post_content' => '<div>' . str_repeat( '<p>a</p>', 8 ) . '</div>', 'post_status' => 'publish', 'post_category' => array( $cat ) ) );
	bst_test( false === strpos( bst_view( $group ), 'bst-inline-related' ), 'wrapped content left alone' );
	$page = wp_insert_post( array( 'post_title' => 'Trang', 'post_type' => 'page', 'post_content' => str_repeat( '<p>a</p>', 8 ), 'post_status' => 'publish' ) );
	global $wp_query, $wp_the_query;
	$wp_query     = new WP_Query( array( 'page_id' => $page ) );
	$wp_the_query = $wp_query;
	$wp_query->the_post();
	bst_test( false === strpos( apply_filters( 'the_content', get_post_field( 'post_content', $page ) ), 'bst-inline-related' ), 'pages skipped' );
	wp_reset_postdata();
}

function bst_t_code() {
	$head = '<script>var a = "x" && 1;</script>';
	$post = array( 'bst_on' => bst_feature_ids(), 'bst_field' => array( 'code-inject' => wp_slash( array( 'head' => $head, 'body' => '<noscript>b</noscript>', 'footer' => '<!-- f -->' ) ) ) );
	bst_features_save( $post );
	ob_start();
	do_action( 'wp_head' );
	$h = ob_get_clean();
	ob_start();
	wp_body_open();
	$b = ob_get_clean();
	ob_start();
	do_action( 'wp_footer' );
	$f = ob_get_clean();
	bst_test( false !== strpos( $h, $head ) && false !== strpos( $b, '<noscript>b</noscript>' ) && false !== strpos( $f, '<!-- f -->' ), 'code printed unchanged in all three places' );
	$editor = wp_insert_user( array( 'user_login' => 'mgr', 'user_pass' => wp_generate_password(), 'role' => 'administrator' ) );
	add_filter(
		'map_meta_cap',
		function ( $caps, $cap ) {
			return 'unfiltered_html' === $cap ? array( 'do_not_allow' ) : $caps;
		},
		10,
		2
	);
	wp_set_current_user( $editor );
	$post['bst_field']['code-inject']['head'] = '<script>evil()</script>';
	bst_features_save( $post );
	bst_test( $head === bst_feature_get( 'code-inject', 'head' ), 'admin without unfiltered_html cannot change code' );
	wp_set_current_user( 1 );
}

function bst_t_forms() {
	$s = bst_native_form_settings( array() );
	$e = function ( $f, $v ) use ( $s ) {
		return bst_native_field_error( $f, $v, $s );
	};
	bst_test( '' !== $e( array( 'type' => 'url' ), 'javascript:x' ) && '' === $e( array( 'type' => 'url' ), 'https://a.vn' ), 'url checked' );
	bst_test( '' !== $e( array( 'type' => 'number' ), 'abc' ) && '' !== $e( array( 'type' => 'date' ), '1/2' ) && '' === $e( array( 'type' => 'date' ), '2026-10-30' ), 'number and date checked' );
	$sel = array( 'type' => 'checkbox', 'options' => "A\nB" );
	bst_test( '' === $e( $sel, 'A, B' ) && '' !== $e( $sel, 'A, Z' ), 'choices must be listed' );
	bst_test( '' !== $e( array( 'type' => 'text', 'required' => true ), '' ), 'required still checked' );
	$dir = get_temp_dir();
	file_put_contents( "{$dir}up.tmp", '%PDF-1.4 test' );
	$pdf = array( 'type' => 'file', 'options' => 'pdf' );
	bst_test( 'a.pdf' === bst_native_file_check( $pdf, "{$dir}up.tmp", 'a.pdf', 13 ), 'pdf accepted' );
	bst_test( '' === bst_native_file_check( $pdf, "{$dir}up.tmp", 'a.pdf', 6 * MB_IN_BYTES ), 'over 5 MB refused' );
	file_put_contents( "{$dir}sh.tmp", '<?php echo 1;' );
	bst_test( '' === bst_native_file_check( array( 'type' => 'file' ), "{$dir}sh.tmp", 'a.php', 13 ), 'php refused' );
	bst_test( '' === bst_native_file_check( array( 'type' => 'file' ), "{$dir}sh.tmp", 'a.php.jpg', 13 ), 'php named as jpg refused' );
	bst_test( ! in_array( 'svg', bst_native_file_types( array( 'options' => 'svg,pdf' ) ), true ), 'svg cannot be allowed' );
	$html = bst_native_form_markup( array( 'formKey' => 'k', 'fields' => array( array( 'type' => 'file', 'name' => 'cv', 'options' => 'pdf' ) ) ) );
	bst_test( false !== strpos( $html, 'type="file"' ) && false !== strpos( $html, 'accept=".pdf"' ), 'file input rendered' );
}

function bst_t_cf7() {
	$tpl = "<label> Họ tên\n [text* your-name placeholder \"Tên\"] </label>\n<label> Dịch vụ\n [select svc first_as_label \"Chọn\" \"Giặt\" \"Là\"] </label>\n[file cv filetypes:pdf|doc]\n[acceptance ok] Đồng ý [/acceptance]\n[quiz q \"1+1?|2\"]\n[submit \"Gửi đi\"]";
	$p   = bst_cf7_parse( $tpl );
	$f   = $p['fields'];
	bst_test( 4 === count( $f ) && 'your-name' === $f[0]['name'] && $f[0]['required'] && 'Tên' === $f[0]['placeholder'] && 'Họ tên' === $f[0]['label'], 'text tag parsed' );
	bst_test( 'Chọn' === $f[1]['placeholder'] && "Giặt\nLà" === $f[1]['options'], 'select parsed' );
	bst_test( 'pdf,doc' === $f[2]['options'] && 'Đồng ý' === $f[3]['placeholder'], 'file and acceptance parsed' );
	bst_test( 'Gửi đi' === $p['submit'] && array( 'quiz' ) === $p['skipped'], 'submit read, quiz reported' );
	$d = bst_cf7_parse( "[radio r default:2 \"A\" \"B\"]\n[select s first_as_label default:3 \"Chọn\" \"C\" \"D\"]\n[checkbox c default:1_3 \"E\" \"F\" \"G\"]" )['fields'];
	bst_test( 'B' === $d[0]['default'] && 'D' === $d[1]['default'] && "E\nG" === $d[2]['default'], 'default:N parsed' );
	$html = bst_native_form_markup( array( 'formKey' => 'k', 'fields' => $d ) );
	bst_test( false !== strpos( $html, 'value="B" checked' ) && false === strpos( $html, 'value="A" checked' ) && false !== strpos( $html, 'value="D" selected' ) && false !== strpos( $html, 'value="G" checked' ) && false === strpos( $html, 'value="F" checked' ), 'default option rendered' );
	$_SERVER['REMOTE_ADDR'] = '10.0.0.9';
	bst_test( 'IP 10.0.0.9.' === bst_native_form_tags( 'IP [wpcf7.remote_ip][wpcf7.unknown].', array(), array(), false ), 'wpcf7 dotted tags replaced' );
	$id = wp_insert_post( array( 'post_type' => 'wpcf7_contact_form', 'post_title' => 'Liên hệ', 'post_status' => 'publish' ) );
	update_post_meta( $id, '_form', $tpl );
	update_post_meta( $id, '_hash', 'abc1234def' );
	update_post_meta( $id, '_mail', array( 'recipient' => 'a@x.test', 'subject' => 'S "q" <b>', 'body' => "[your-name]\n--", 'sender' => 'x', 'additional_headers' => '' ) );
	$by = wp_insert_post( array( 'post_title' => 'P1', 'post_status' => 'publish', 'post_content' => "<!-- wp:paragraph --><p>Trên</p><!-- /wp:paragraph -->\n<!-- wp:shortcode -->[contact-form-7 id=\"abc1234\" title=\"x\"]<!-- /wp:shortcode -->" ) );
	$cl = wp_insert_post( array( 'post_title' => 'P2', 'post_status' => 'publish', 'post_content' => "<p>[contact-form-7 id=\"$id\"]</p>" ) );
	$ot = wp_insert_post( array( 'post_title' => 'P3', 'post_status' => 'publish', 'post_content' => '[contact-form-7 id="999999"]' ) );
	bst_test( 2 === bst_cf7_migrate( get_post( $id ) ), 'two posts updated' );
	$a = bst_native_form_find( 'cf7' . $id );
	bst_test( $a && 'a@x.test' === $a['mailTo'] && 'S "q" <b>' === $a['mailSubject'] && "[your-name]\n--" === $a['mailBody'] && 4 === count( $a['fields'] ), 'block found with mail intact' );
	bst_test( false !== strpos( get_post_field( 'post_content', $by ), '<p>Trên</p>' ) && false === strpos( get_post_field( 'post_content', $by ), 'contact-form-7' ), 'other content kept' );
	bst_test( 0 === strpos( get_post_field( 'post_content', $cl ), '<!-- wp:bst/form' ), 'paragraph wrapper removed' );
	bst_test( false !== strpos( get_post_field( 'post_content', $ot ), '999999' ), 'other form untouched' );
	bst_test( false !== strpos( render_block( parse_blocks( get_post_field( 'post_content', $cl ) )[0] ), 'type="file"' ), 'moved form renders' );
	bst_test( 0 === bst_cf7_migrate( get_post( $id ) ), 'second run changes nothing' );
	$act = function ( $form, $meta ) {
		return wp_insert_post( array( 'post_type' => 'wpcf7r_action', 'post_title' => 'R', 'post_status' => 'private', 'meta_input' => $meta + array( 'wpcf7_id' => $form, 'wpcf7_rule_id' => 'default', 'action_type' => 'redirect', 'action_status' => 'on' ) ) );
	};
	$f2 = wp_insert_post( array( 'post_type' => 'wpcf7_contact_form', 'post_title' => 'Khối', 'post_status' => 'publish' ) );
	update_post_meta( $f2, '_form', $tpl );
	$act( $f2, array( 'use_external_url' => 'on', 'external_url' => 'https://x.test/ok', 'open_in_new_tab' => 'on', 'http_build_query' => 'on', 'delay_redirect_seconds' => '3' ) );
	$r = bst_cf7_redirect( $f2 );
	bst_test( 'https://x.test/ok' === $r['redirectUrl'] && 3 === (int) $r['redirectDelay'] && $r['redirectPass'] && $r['redirectNewTab'] && ! $r['partial'], 'redirect action read' );
	$nest = wp_insert_post( array( 'post_title' => 'P4', 'post_status' => 'publish', 'post_content' => "<!-- wp:group -->\n<div class=\"wp-block-group\"><!-- wp:bst/form {\"formId\":$f2,\"successText\":\"Cảm ơn\"} /--></div>\n<!-- /wp:group -->" ) );
	$own  = wp_insert_post( array( 'post_title' => 'P5', 'post_status' => 'publish', 'post_content' => "<!-- wp:bst/form {\"provider\":\"cf7\",\"formId\":$f2,\"redirectUrl\":\"https://x.test/mine\"} /-->" ) );
	$wpf  = wp_insert_post( array( 'post_title' => 'P6', 'post_status' => 'publish', 'post_content' => "<!-- wp:bst/form {\"provider\":\"wpforms\",\"formId\":$f2} /-->" ) );
	bst_test( 2 === bst_cf7_migrate( get_post( $f2 ) ), 'form blocks set to CF7 updated' );
	$b = parse_blocks( get_post_field( 'post_content', $nest ) )[0]['innerBlocks'][0]['attrs'];
	bst_test( 'bigspy' === $b['provider'] && 'cf7' . $f2 === $b['formKey'] && ! isset( $b['formId'] ) && 'Cảm ơn' === $b['successText'] && 4 === count( $b['fields'] ), 'nested block moved, own settings kept' );
	bst_test( 'https://x.test/ok' === $b['redirectUrl'] && $b['redirectNewTab'] && $b['redirectPass'] && 3 === (int) $b['redirectDelay'], 'redirect carried over' );
	bst_test( 'https://x.test/mine' === parse_blocks( get_post_field( 'post_content', $own ) )[0]['attrs']['redirectUrl'], 'block redirect wins' );
	bst_test( false !== strpos( get_post_field( 'post_content', $wpf ), '"wpforms"' ), 'other provider untouched' );
	bst_test( 0 === bst_cf7_migrate( get_post( $f2 ) ), 'second run leaves blocks alone' );
	$f3 = wp_insert_post( array( 'post_type' => 'wpcf7_contact_form', 'post_title' => 'Cũ', 'post_status' => 'publish' ) );
	update_post_meta( $f3, '_wpcf7_redirect_use_external_url', 'on' );
	update_post_meta( $f3, '_wpcf7_redirect_external_url', 'https://x.test/[your-name]' );
	update_post_meta( $f3, '_wpcf7_redirect_delay_redirect', '2000' );
	$r = bst_cf7_redirect( $f3 );
	bst_test( 2 === (int) $r['redirectDelay'] && $r['partial'], 'old plugin settings read, mail tag flagged' );
	$f4 = wp_insert_post( array( 'post_type' => 'wpcf7_contact_form', 'post_title' => 'Tắt', 'post_status' => 'publish' ) );
	$off = $act( $f4, array( 'use_external_url' => 'on', 'external_url' => 'https://x.test/off' ) );
	update_post_meta( $off, 'action_status', 'off' );
	bst_test( array() === bst_cf7_redirect( $f4 ), 'disabled redirect ignored' );
}

function bst_t_aid() {
	$m = bst_aid_tree_to_markup( array( array( 'name' => 'core/html', 'html' => '<div>x</div>' ) ) );
	bst_test( is_wp_error( $m ), 'new core/html refused' );
	$m = bst_aid_tree_to_markup(
		array(
			array( 'name' => 'core/heading', 'html' => 'Tiêu đề', 'attrs' => array( 'level' => 3 ) ),
			array( 'name' => 'core/paragraph', 'html' => 'Đoạn' ),
			array( 'name' => 'core/list', 'html' => '<ul><li>a</li></ul>' ),
		)
	);
	bst_test( ! is_wp_error( $m ) && 3 === substr_count( $m['markup'], 'wp:bst/' ) && false === strpos( $m['markup'], 'wp:html' ) && false !== strpos( $m['markup'], '"tag":"h3"' ), 'core blocks become BIGSPY AI blocks' );
	$old  = '<!-- wp:html --><b>cũ</b><!-- /wp:html -->';
	$page = wp_insert_post( wp_slash( array( 'post_title' => 'Cũ', 'post_type' => 'page', 'post_status' => 'draft', 'post_content' => $old ) ) );
	$raw  = bst_aid_tree_to_markup( array( array( 'name' => 'core/html', 'raw' => $old ) ), $page );
	bst_test( ! is_wp_error( $raw ) && false !== strpos( $raw['markup'], '<b>cũ</b>' ), 'old core/html kept' );
	bst_test( is_wp_error( bst_aid_tree_to_markup( array( array( 'name' => 'core/html', 'raw' => $old ) ) ) ), 'core/html raw without a source refused' );
	$rule = array_keys( bst_block_preset_rules() )[0];
	$p    = bst_aid_tree_to_markup( array( array( 'name' => 'bst/text', 'attrs' => array( 'html' => '<p>a</p>', 'bsPresets' => "$rule fake-id" ) ) ) );
	bst_test( false !== strpos( $p['markup'], $rule ) && false === strpos( $p['markup'], 'fake-id' ), 'presets kept, unknown ids dropped' );
	$code = function ( $n, $size = 10 ) {
		return array_fill( 0, $n, array( 'name' => 'bst/code', 'attrs' => array( 'code' => str_repeat( 'x', $size ) ) ) );
	};
	bst_test( 1 === bst_aid_tool_policy( 'create_page', array( 'blocks' => $code( 8 ) ) )['level'], 'eight code blocks need no card' );
	bst_test( 2 === bst_aid_tool_policy( 'create_page', array( 'blocks' => $code( 9 ) ) )['level'], 'ninth code block needs a card' );
	bst_test( 2 === bst_aid_tool_policy( 'create_page', array( 'blocks' => $code( 1, 7000 ) ) )['level'], 'a code block over 6 KB needs a card' );
	$nest = array( array( 'name' => 'bst/section', 'inner' => $code( 9 ) ) );
	bst_test( 2 === bst_aid_tool_policy( 'update_page', array( 'blocks' => $nest ) )['level'], 'nested code counted' );
	$id = wp_insert_post( array( 'post_title' => 'C', 'post_type' => 'page', 'post_status' => 'draft', 'post_content' => serialize_blocks( bst_aid_build_blocks( $code( 8 ), '', $i ) ) ) );
	bst_test( 2 === bst_aid_tool_policy( 'page_sections', array( 'id' => $id, 'mode' => 'append', 'blocks' => $code( 1 ) ) )['level'], 'existing page code counted' );
	bst_test( 100 === bst_aid_editable_percent( get_post_field( 'post_content', $id ) ), 'code illustrations not scored' );
	bst_test( 90 === bst_aid_editable_percent( str_repeat( '<!-- wp:bst/text /-->', 9 ) . '<!-- wp:html --><b>x</b><!-- /wp:html -->' ), 'score counts blocks' );
	$big = bst_aid_tool_custom_css( array( 'mode' => 'replace', 'css' => 'a{}' . str_repeat( ' ', 1 ) . str_repeat( 'b{color:red}', 300 ) ) );
	bst_test( ! empty( $big['error'] ), 'large AI CSS refused' );
	$ok = bst_aid_tool_custom_css( array( 'mode' => 'replace', 'user_requested' => true, 'css' => str_repeat( 'b{color:red}', 300 ) ) );
	bst_test( ! empty( $ok['saved'] ), 'large CSS allowed when user asked' );
	$opt = bst_aid_tool_style_options( array( 'names' => array_slice( array_keys( bst_block_presets() ), 0, 1 ) ) );
	bst_test( ! empty( $opt['css_library'] ) && ! empty( $opt['blocks'] ), 'style options listed' );
	bst_test( 1 === bst_aid_tool_policy( 'style_options', array() )['level'], 'style options is read only' );
}

/** G-55: load priority on banner/image, for Media IDs and URLs. */
function bst_t_hero() {
	$r = function ( $name, $attrs ) {
		return render_block( array( 'blockName' => $name, 'attrs' => $attrs, 'innerBlocks' => array(), 'innerHTML' => '', 'innerContent' => array() ) );
	};
	$url = 'https://example.com/hero.jpg';
	foreach ( array( 'bst/banner', 'bst/image' ) as $b ) {
		$auto = $r( $b, array( 'imageUrl' => $url ) );
		bst_test( false !== strpos( $auto, 'loading="lazy"' ) && false === strpos( $auto, 'fetchpriority' ), "$b url auto stays lazy" );
		$high = $r( $b, array( 'imageUrl' => $url, 'loadPriority' => 'high' ) );
		bst_test( false !== strpos( $high, 'fetchpriority="high"' ) && false === strpos( $high, 'loading=' ), "$b url high is eager + high" );
		$bad = $r( $b, array( 'imageUrl' => $url, 'loadPriority' => 'x' ) );
		bst_test( false !== strpos( $bad, 'loading="lazy"' ), "$b unknown priority falls back to auto" );
	}
	$dims = $r( 'bst/image', array( 'imageUrl' => $url, 'urlWidth' => 1200, 'urlHeight' => 600 ) );
	bst_test( false !== strpos( $dims, 'width="1200" height="600"' ), 'image url width/height output' );
	$half = $r( 'bst/image', array( 'imageUrl' => $url, 'urlWidth' => 1200 ) );
	bst_test( false === strpos( $half, 'width="' ), 'image url needs both sizes' );

	require_once ABSPATH . 'wp-admin/includes/image.php';
	$dir = wp_upload_dir()['path'];
	$img = imagecreatetruecolor( 1600, 800 );
	imagejpeg( $img, "$dir/hero-g55.jpg", 80 );
	$id = wp_insert_attachment( array( 'post_mime_type' => 'image/jpeg', 'post_title' => 'hero', 'post_status' => 'inherit' ), "$dir/hero-g55.jpg" );
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, "$dir/hero-g55.jpg" ) );
	foreach ( array( 'bst/banner', 'bst/image' ) as $b ) {
		$high = $r( $b, array( 'imageId' => $id, 'loadPriority' => 'high' ) );
		bst_test( false !== strpos( $high, 'fetchpriority="high"' ) && false === strpos( $high, 'loading="lazy"' ), "$b media high" );
		$lazy = $r( $b, array( 'imageId' => $id, 'loadPriority' => 'lazy' ) );
		bst_test( false !== strpos( $lazy, 'loading="lazy"' ) && false === strpos( $lazy, 'fetchpriority="high"' ), "$b media lazy" );
		$auto = $r( $b, array( 'imageId' => $id ) );
		bst_test( ! ( false !== strpos( $auto, 'loading="lazy"' ) && false !== strpos( $auto, 'fetchpriority="high"' ) ), "$b media auto never lazy+high" );
	}
	$mob = $r( 'bst/banner', array( 'imageUrl' => $url, 'imageMobileUrl' => 'https://example.com/m.jpg', 'loadPriority' => 'high' ) );
	bst_test( 1 === substr_count( $mob, 'fetchpriority="high"' ) && false !== strpos( $mob, '<source media="(max-width: 549px)"' ), 'banner mobile source keeps one high img' );
}

/** A JPEG with an EXIF block saying "rotate 90 degrees". */
function bst_exif_jpeg( $w, $h ) {
	$img = imagecreatetruecolor( $w, $h );
	for ( $i = 0; $i < 3000; $i++ ) {
		imagesetpixel( $img, wp_rand( 0, $w - 1 ), wp_rand( 0, $h - 1 ), wp_rand( 0, 0xffffff ) );
	}
	ob_start();
	imagejpeg( $img, null, 95 );
	$jpg  = ob_get_clean();
	$tiff = "MM\x00\x2a\x00\x00\x00\x08\x00\x01\x01\x12\x00\x03\x00\x00\x00\x01\x00\x06\x00\x00\x00\x00\x00\x00";
	$exif = "Exif\x00\x00" . $tiff;
	return substr( $jpg, 0, 2 ) . "\xff\xe1" . pack( 'n', strlen( $exif ) + 2 ) . $exif . substr( $jpg, 2 );
}

function bst_t_listing() {
	if ( ! post_type_exists( 'product' ) ) {
		register_post_type( 'product', array( 'public' => true ) );
	}
	bst_test( function_exists( 'bst_listing_field' ) && '1.6.0' === bigspy_theme_integration_version(), 'listing: module and compat layer loaded' );
	$id = wp_insert_post( array( 'post_type' => 'product', 'post_title' => 'Acme AI', 'post_status' => 'publish' ) );
	update_post_meta( $id, '_bigspy_dynamic_attributes', wp_slash( '[{"label":"Platform","value":"Web","order":1},{"label":"Plan","value":"Free","order":0}]' ) );
	$attrs = bigspy_dynamic_attributes( $id );
	bst_test( 2 === count( $attrs ) && 'Plan' === $attrs[0]['label'], 'listing: dynamic attributes read and sorted' );
	add_filter( 'bigspy_dynamic_listing_attributes', $f = function ( $a ) { $a[] = array( 'label' => 'X', 'value' => 'Y', 'order' => 9 ); return $a; } );
	bst_test( 3 === count( bigspy_dynamic_attributes( $id ) ), 'listing: bigspy_dynamic_listing_attributes filter' );
	remove_filter( 'bigspy_dynamic_listing_attributes', $f );
	update_post_meta( $id, 'pricing_type', 'starting_at' );
	update_post_meta( $id, 'starting_price', '19' );
	update_post_meta( $id, 'price_currency', 'usd' );
	update_post_meta( $id, 'price_unit', 'month' );
	bst_test( 'From USD 19 / month' === bst_listing_price_label( $id ), 'listing: price label' );
	update_post_meta( $id, 'official_url', 'https://acme.test/' );
	bst_test( 'https://acme.test/' === bigspy_listing_url( $id ), 'listing: official url' );
	update_post_meta( $id, 'affiliate_url', 'https://acme.test/?ref=1' );
	bst_test( 'https://acme.test/?ref=1' === bigspy_listing_url( $id ) && 'https://acme.test/?ref=1' === bigspy_theme_external_url( $id ), 'listing: affiliate link wins (bigspy_listing_url filter)' );
	add_filter( 'bigspy_theme_external_url', $g = function () { return 'https://x.test/'; } );
	bst_test( 'https://x.test/' === bigspy_theme_external_url( $id ), 'listing: bigspy_theme_external_url filter' );
	remove_filter( 'bigspy_theme_external_url', $g );
	update_post_meta( $id, '_bigspy_paid_listing', '1' );
	update_post_meta( $id, 'company_name', 'Acme Inc' );
	$d = bst_listing_disclosure( $id );
	bst_test( false !== strpos( $d, 'Acme Inc paid' ) && false !== strpos( $d, 'commission' ), 'listing: paid and affiliate disclosure' );
	update_post_meta( $id, 'rating', '4.6' );
	update_post_meta( $id, 'faq', "What is it? | A tool.
Broken line" );
	bst_test( 1 === count( bst_listing_faq( $id ) ) && 4.6 === bst_listing_rating( $id ), 'listing: faq rows and rating' );
	$html = bst_listing_info_html( $id );
	bst_test( false !== strpos( $html, 'Sponsored' ) && false !== strpos( $html, 'rel="sponsored noopener"' ) && false !== strpos( $html, '<dt>Plan</dt>' ), 'listing: info html' );
	bst_listing_save( $id, array( '_bigspy_ad' => '1', '_bigspy_listing_type' => 'AI Tool', 'bst_attr_label' => array( 'A', '' ), 'bst_attr_value' => array( 'B<script>', 'z' ), 'bst_lf' => array( 'free_trial' => '1', 'official_url' => 'javascript:alert(1)', 'rating' => '4.2' ) ) );
	$saved = json_decode( get_post_meta( $id, '_bigspy_dynamic_attributes', true ), true );
	bst_test( '1' === get_post_meta( $id, '_bigspy_ad', true ) && '' === get_post_meta( $id, '_bigspy_paid_listing', true ) && 'AI Tool' === get_post_meta( $id, '_bigspy_listing_type', true ) && 1 === count( $saved ) && 'B' === $saved[0]['value'], 'listing: meta box save keeps old keys and cleans values' );
	bst_test( '1' === get_post_meta( $id, 'free_trial', true ) && '' === get_post_meta( $id, 'official_url', true ) && '4.2' === get_post_meta( $id, 'rating', true ), 'listing: listing fields saved by type' );
	bst_test( 1 === count( bst_listing_faq( $id ) ), 'listing: listing fields box leaves the FAQ to the FAQ box' );
	$doc = '<p>Intro</p><h2>Frequently Asked Questions</h2><details><summary>Is it free?</summary><p>Yes, a free plan.</p></details><h3>Bad</h3><h2>Verdict</h2><p>Good.</p>';
	$q_id = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'FAQ in content', 'post_content' => $doc ) );
	$rows = bst_listing_faq( $q_id );
	bst_test( 1 === count( $rows ) && 'Is it free?' === $rows[0]['question'] && 'Yes, a free plan.' === $rows[0]['answer'] && ! bst_listing_has_faq_field( $q_id ), 'listing: FAQ read from the description when the field is empty' );
	bst_test( 2 === count( bst_listing_faq_from_content( '<h2>FAQ</h2><h3>One?</h3><p>A.</p><h4>Two?</h4><p>B.</p>' ) ), 'listing: FAQ read from H3-H6 questions' );
	bst_test( '<p>Intro</p><h2>Verdict</h2><p>Good.</p>' === bst_listing_remove_faq_section( $doc ), 'listing: FAQ section removed from the description' );
	ob_start();
	bst_listing_faq_box( get_post( $q_id ) );
	$box = ob_get_clean();
	bst_test( false !== strpos( $box, 'Is it free? | Yes, a free plan.' ) && false !== strpos( $box, 'bigspy_listing_faq_schema_source' ), 'listing: FAQ box prefilled from the description' );
	bst_listing_faq_save( $q_id, "Q1 | A1\nQ2 | A2" );
	bst_test( 2 === count( bst_listing_faq( $q_id ) ) && bst_listing_has_faq_field( $q_id ), 'listing: FAQ box saves the faq field' );
	bst_listing_faq_save( $q_id, '' );
	bst_test( 1 === count( bst_listing_faq( $q_id ) ), 'listing: clearing the FAQ falls back to the description' );
	wp_delete_post( $q_id, true );
	delete_option( 'bst_feature_listing' );
	update_post_meta( $id, 'affiliate_url', 'https://acme.test/?ref=1' ); // the fields box save above cleared it.
	update_option( 'bigspy_categories_promotions', array( 'sponsored_enabled' => true, 'button_text' => 'Go →', 'rotation_version' => 4 ) );
	$cfg = bst_sponsored_config();
	bst_test( $cfg['on'] && 'Go →' === $cfg['button'] && 4 === $cfg['version'], 'sponsored: old theme settings are the defaults' );
	$plain = wp_insert_post( array( 'post_type' => 'product', 'post_title' => 'No partner', 'post_status' => 'publish' ) );
	update_post_meta( $plain, '_bigspy_ad', '1' );
	$items = bst_sponsored_items();
	bst_test( 1 === count( $items ) && $id === $items[0]['id'] && bigspy_listing_is_ad( $id ), 'sponsored: AD listings with a partner URL only' );
	$slot = bst_sponsored_slot( 0, 'single', array( 'buttonStyle' => 'soft' ) );
	bst_test( false !== strpos( $slot, 'rel="sponsored nofollow noopener"' ) && false !== strpos( $slot, 'data-bst-sponsored-pool' ) && false !== strpos( $slot, 'Go →' ), 'sponsored: slot html with pool' );
	bst_test( 1 === substr_count( do_shortcode( '[bigspy_sponsored_grid]' ), 'class="bst-sponsored ' ), 'sponsored: [bigspy_sponsored_grid] shortcode' );
	$plain_slot = bst_sponsored_slot( 0, 'single' );
	bst_test( false !== strpos( $plain_slot, 'bst-sponsored__cover' ) && false === strpos( $plain_slot, 'bst-sponsored__button' ) && false !== strpos( $plain_slot, 'bst-sponsored--btn-none' ), 'sponsored: no button by default, the whole card is the link' );
	$solid_slot = bst_sponsored_slot( 0, 'single', array( 'buttonStyle' => 'solid', 'buttonText' => 'Visit partner', 'showLabel' => false, 'showDescription' => false ) );
	bst_test( false !== strpos( $solid_slot, 'bst-sponsored--btn-solid' ) && false !== strpos( $solid_slot, 'Visit partner' ) && false === strpos( $solid_slot, 'bst-sponsored__label' ) && false === strpos( $solid_slot, 'data-desc' ), 'sponsored: block options set the button, label and description' );
	bst_test( 1 === substr_count( bst_render_sponsored( array( 'count' => 1, 'columnsDesktop' => 2 ) ), 'class="bst-sponsored ' ) && false !== strpos( bst_render_sponsored( array( 'columnsDesktop' => 2 ) ), '--bst-sp-d:2' ), 'sponsored: block renders cards with its own columns' );
	bst_test( 1 === substr_count( do_shortcode( '[bigspy_sponsored_card]' ), 'class="bst-sponsored ' ), 'sponsored: [bigspy_sponsored_card] one card for page heroes' );
	// A Rank Math breadcrumb module that is switched off prints nothing: the theme's own trail must show instead.
	if ( ! function_exists( 'rank_math_the_breadcrumbs' ) ) {
		function rank_math_the_breadcrumbs() {} // phpcs:ignore
	}
	ob_start();
	bst_breadcrumbs();
	$crumbs = ob_get_clean();
	bst_test( false !== strpos( $crumbs, '<nav class="bs-breadcrumbs"' ) && false !== strpos( $crumbs, 'Home' ), 'breadcrumbs: theme trail when the SEO plugin prints nothing' );
	bst_test( false === bst_mod( 'breadcrumbs_pages' ) || ! bst_mod( 'breadcrumbs_pages' ), 'breadcrumbs: pages without an H1 stay as before by default' );
	bst_toggle_listing_ad( $plain );
	$after = get_option( 'bigspy_categories_promotions' );
	bst_test( '' === get_post_meta( $plain, '_bigspy_ad', true ) && 5 === $after['rotation_version'] && 'Go →' === $after['button_text'], 'sponsored: AD toggle refreshes rotation, keeps settings' );
	$cols = apply_filters( 'manage_edit-product_columns', array( 'name' => 'Name', 'featured' => 'F', 'date' => 'D' ) );
	bst_test( array( 'name', 'featured', 'bigspy_ad', 'date' ) === array_keys( $cols ), 'sponsored: AD column after Featured' );
	$hp = "<!-- wp:paragraph --><p>A</p><!-- /wp:paragraph -->

<!-- wp:paragraph --><p>B</p><!-- /wp:paragraph -->

<!-- wp:paragraph --><p>C</p><!-- /wp:paragraph -->

<!-- wp:bigspy/home-seo-divider /-->

<!-- wp:paragraph --><p>D</p><!-- /wp:paragraph -->";
	$w  = bst_home_sidebar_wrap( $hp, 1, 3 );
	bst_test( preg_match( '#<p>A</p>.*bs-home-sb.*<p>B</p>.*bst-home-sidebar.*<p>C</p>#s', $w ) && false === strpos( substr( $w, strpos( $w, 'bst-home-sidebar' ) ), '<p>B</p>' ), 'home sidebar: start after block 1, end before block 3' );
	$w = bst_home_sidebar_wrap( $hp, 0, 0 );
	bst_test( preg_match( '#bs-home-sb.*<p>C</p>.*bst-home-sidebar.*home-seo-divider.*<p>D</p>#s', $w ), 'home sidebar: no end block stops at the old SEO divider' );
	bst_test( $hp === bst_home_sidebar_wrap( $hp, 3, 2 ) && 'home_sidebar' && false === (bool) bst_mod( 'home_sidebar' ), 'home sidebar: off by default, reversed range left alone' );
	bst_test( count( bst_home_sidebar_choices( '-' ) ) >= 1 && is_registered_sidebar( 'sidebar-home' ), 'home sidebar: widget area and block choices' );
	update_option( 'bst_feature_listing', array( 'sponsored_on' => 0 ) );
	bst_test( '' === bst_sponsored_slot( 0 ), 'sponsored: off in Features hides slots' );
	$req = new WP_REST_Request( 'POST', '/bst/v1/feature' );
	$req->set_body_params( array( 'id' => 'listing', 'on' => false ) );
	wp_set_current_user( 0 );
	bst_test( 401 === rest_do_request( $req )->get_status(), 'listing: visitors cannot switch the mode' );
	wp_set_current_user( 1 );
	$res = rest_do_request( $req );
	bst_test( 200 === $res->get_status() && ! bst_feature_on( 'listing' ), 'listing: admin turns the mode off over REST' );
	if ( function_exists( 'bst_panel_settings_page' ) ) {
		ob_start();
		bst_panel_settings_page();
		$settings_html = ob_get_clean();
		bst_test( false !== strpos( $settings_html, 'name="bst_listing_mode"' ) && false === strpos( $settings_html, 'name="bst_listing_mode" value="1" checked' ), 'listing: Settings page shows the BIGSPY AI List switch, off' );
	}
	bst_feature_set( 'listing', true );
}
// G-130: shared Design layer. Old designs print the same CSS; each new key prints its rule.
function bst_t_design() {
	$legacy = '.x{margin-top:10px;background-color:#fff;border-radius:8px;overflow:hidden;box-shadow:0 3px 6px -4px rgba(0,0,0,.16),0 3px 6px rgba(0,0,0,.1);transition:background-color .3s ease,color .3s ease,border-color .3s ease,border-radius .3s ease,box-shadow .3s ease,transform .3s ease}.x:where(.bs-column,.bs-row,.bs-text,.bs-heading,.bs-buttons,.bs-icon-list,.bs-counter,.bs-social,.bs-title,.bs-code,.bs-share,.bs-star-rating,.bs-progress){padding:20px}@media (max-width:549px){.x{font-size:14px;--bs-fs:14px}}@media (hover:hover){.x:hover{transform:translateY(-5px)}}';
	bst_test( $legacy === bst_design_css( array( 'mt' => 10, 'bg' => '#fff', 'br' => 8, 'depth' => 2, 'hmove' => 5, 'fsM' => 14 ), '.x' ), 'design: legacy bsDesign CSS unchanged' );
	$cases = array(
		'border per side'  => array( array( 'bwt' => 3, 'bc' => '#f00' ), '.x{border-style:solid;border-color:#f00;border-width:3px 0 0 0}' ),
		'radius per corner' => array( array( 'rtl' => 20, 'rbr' => 4 ), '.x{border-top-left-radius:20px;border-bottom-right-radius:4px}' ),
		'linear gradient'  => array( array( 'gt' => 'linear', 'g1' => '#f00', 'g2' => '#00f', 'ga' => 90 ), '.x{background-image:linear-gradient(90deg,#f00,#00f)}' ),
		'radial gradient'  => array( array( 'gt' => 'radial', 'g1' => '#f00', 'g2' => '#0f0', 'g3' => '#00f' ), '.x{background-image:radial-gradient(circle,#f00,#0f0,#00f)}' ),
		'image + overlay'  => array( array( 'bgi' => 'https://e.x/a.jpg', 'ovl' => 'rgba(0,0,0,.5)', 'bgs' => 'contain' ), '.x{background-image:linear-gradient(rgba(0,0,0,.5),rgba(0,0,0,.5)),url("https://e.x/a.jpg");background-position:center;background-size:contain;background-repeat:no-repeat}' ),
		'custom shadow'    => array( array( 'depth' => 3, 'shc' => '#000', 'shy' => 4, 'shb' => 10, 'shi' => true ), '.x{box-shadow:inset 0 4px 10px 0 #000}' ),
		'filters'          => array( array( 'op' => 50, 'fbl' => 2, 'fbr' => 120, 'fgr' => 100, 'bdb' => 8 ), '.x{opacity:0.5;filter:blur(2px) brightness(120%) grayscale(100%);-webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px)}' ),
		'transform'        => array( array( 'rot' => 5, 'tx' => 10, 'skx' => 3 ), '.x{transform:translate(10px,0) rotate(5deg) skewX(3deg)}' ),
		'hover transform'  => array( array( 'rot' => 5, 'hrot' => 10, 'tdur' => 600 ), '.x{transition:background-color 600ms ease,color 600ms ease,border-color 600ms ease,border-radius 600ms ease,box-shadow 600ms ease,transform 600ms ease;transform:rotate(5deg)}@media (hover:hover){.x:hover{transform:rotate(5deg) rotate(10deg)}}' ),
		'mask'             => array( array( 'msk' => 'circle' ), '.x{clip-path:circle(50% at 50% 50%)}' ),
		'device colors'    => array( array( 'bgT' => '#eee', 'colorM' => '#111', 'bwT' => 2, 'bcM' => '#0f0', 'brM' => 4 ), '@media (max-width:849px){.x{background-color:#eee;border-width:2px;border-style:solid}}@media (max-width:549px){.x{color:#111;--bs-heading-color:#111;border-color:#0f0;border-radius:4px}}' ),
		'layout'           => array( array( 'mh' => 300, 'mhM' => '50vh', 'ar' => '16/9', 'ord' => -1, 'ordM' => 2, 'asT' => 'center' ), '.x{min-height:300px;aspect-ratio:16/9;order:-1}@media (max-width:849px){.x{align-self:center}}@media (max-width:549px){.x{min-height:50vh;order:2}}' ),
		'sticky'           => array( array( 'st' => 'desktop', 'stt' => 80 ), '.x{position:sticky;top:80px}@media (max-width:849px){.x{position:static}}' ),
		'overflow'         => array( array( 'br' => 10, 'ov' => 'visible' ), '.x{border-radius:10px;overflow:visible}' ),
		'bad url rejected' => array( array( 'bgi' => 'javascript:alert(1)' ), '' ),
	);
	foreach ( $cases as $label => $case ) {
		$got = bst_design_css( $case[0], '.x' );
		bst_test( $case[1] === $got, 'design: ' . $label . ( $case[1] === $got ? '' : ' got ' . $got ) );
	}
	$attrs = bst_design_html_attrs( array( 'hattr' => "data-track=hero\naria-label=\"Intro\"\nonclick=alert(1)\nstyle=x" ) );
	bst_test( array( 'data-track' => 'hero', 'aria-label' => 'Intro' ) === $attrs, 'design: custom attributes only data-* and aria-*' );
	$post = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Design', 'post_author' => 1, 'post_content' => '' ) );
	$allow = function ( $caps, $cap, $user_id ) {
		return 'unfiltered_html' === $cap && 1 === (int) $user_id ? array( 'exist' ) : $caps;
	};
	add_filter( 'map_meta_cap', $allow, 99, 3 ); // An earlier test denies unfiltered_html.
	bst_test( '.x h2{color:red}.x{opacity:.5}' === bst_design_custom_css( array( 'ccss' => 'selector h2{color:red}selector{opacity:.5}' ), '.x', $post ) && '.x{color:red}' === bst_design_custom_css( array( 'ccss' => 'color:red' ), '.x', $post ), 'design: custom CSS scoped to the block' );
	remove_filter( 'map_meta_cap', $allow, 99 );
	$low = wp_insert_user( array( 'user_login' => 'bst_contrib', 'user_pass' => wp_generate_password(), 'role' => 'contributor' ) );
	wp_update_post( array( 'ID' => $post, 'post_author' => $low ) );
	bst_test( '' === bst_design_custom_css( array( 'ccss' => 'color:red' ), '.x', $post ), 'design: custom CSS ignored for authors without unfiltered_html' );
	$html = bst_apply_block_design( '<div class="bs-text">x</div>', array( 'blockName' => 'bst/text', 'attrs' => array( 'bsDesign' => array( 'hattr' => 'data-x=1' ) ) ) );
	bst_test( false !== strpos( $html, 'data-x="1"' ) && false === strpos( $html, 'bsd-' ), 'design: attributes alone add no design class' );
}
// Admin-only files must parse: a syntax error there breaks the whole dashboard.
try {
	$bst_news = include BST_DIR . '/inc/welcome/data.php';
	bst_test( is_array( $bst_news ) && ! empty( $bst_news['news'] ), 'welcome: What is new data loads' );
} catch ( Throwable $e ) {
	bst_test( false, 'welcome: What is new data loads ' . $e->getMessage() );
}
// A page that starts with a full-width block (Banner hero) and no printed title sits under the header.
$bst_flush = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Flush', 'post_content' => '<!-- wp:bst/banner {"align":"full"} /-->' ) );
$bst_plain = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Plain', 'post_content' => '<!-- wp:paragraph --><p>x</p><!-- /wp:paragraph -->' ) );
$GLOBALS['wp_query'] = new WP_Query( array( 'page_id' => $bst_flush ) );
$GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];
$bst_a = bst_starts_full_width();
$GLOBALS['wp_query'] = new WP_Query( array( 'page_id' => $bst_plain ) );
$GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];
bst_test( $bst_a && ! bst_starts_full_width(), 'layout: full-width first block removes the gap under the header' );
wp_reset_query();
// G-151: quick-edit labels. Every focus-map item points at a real Customizer target; no attribute outside the Customizer.
bst_test( '' === bst_header_element_edit_attr( 'logo' ) && false === strpos( bst_header_element( 'logo', 'desktop' ), 'data-bst-edit' ), 'quick-edit: no data-bst-edit outside the Customizer' );
require_once ABSPATH . WPINC . '/class-wp-customize-manager.php';
$GLOBALS['wp_customize'] = new WP_Customize_Manager();
do_action( 'customize_register', $GLOBALS['wp_customize'] );
$bst_raw  = bst_customize_focus_map_raw();
$bst_map  = bst_customize_focus_map();
$bst_miss = array();
foreach ( $bst_raw as $bst_it ) {
	if ( 'url' !== $bst_it['type'] && '' === bst_customize_target_title( $bst_it['type'], $bst_it['id'] ) && ! in_array( $bst_it['id'], array( 'bst_shop', 'bst_product', 'bst_catalog', 'bst_checkout', 'bst_listing_single', 'bst_home_sidebar' ), true ) ) {
		$bst_miss[] = $bst_it['id'];
	}
}
bst_test( ! $bst_miss && count( $bst_map ) >= 15, 'quick-edit: focus map targets exist (' . count( $bst_map ) . ' items' . ( $bst_miss ? '; missing ' . implode( ',', $bst_miss ) : '' ) . ')' );
foreach ( array_keys( bst_he_schema() ) as $bst_k ) {
	bst_test( '' !== bst_customize_target_title( 'section', 'bst_he_' . $bst_k ), 'quick-edit: header element section bst_he_' . $bst_k . ' exists' );
}
unset( $GLOBALS['wp_customize'] );
// G-147: developer hooks. Unhooked output unchanged; one action and filters change output.
$bst_hb   = array( 'blockName' => 'bst/divider', 'attrs' => array(), 'innerBlocks' => array(), 'innerHTML' => '', 'innerContent' => array() );
$bst_base = render_block( $bst_hb );
$bst_logo = bst_header_element( 'logo', 'desktop' );
$bst_cls  = function () { return array( 'g147-x' ); };
$bst_htm  = function ( $html, $name ) { return 'bst/divider' === $name ? $html . '<!--g147-->' : $html; };
$bst_act  = function ( $el ) { if ( 'logo' === $el ) { echo '<i class="g147-a"></i>'; } };
add_filter( 'bst_block_classes', $bst_cls );
add_filter( 'bst_block_html', $bst_htm, 10, 2 );
add_action( 'bst_before_header_element', $bst_act );
$bst_out  = render_block( $bst_hb );
$bst_logo2 = bst_header_element( 'logo', 'desktop' );
remove_filter( 'bst_block_classes', $bst_cls );
remove_filter( 'bst_block_html', $bst_htm, 10 );
remove_action( 'bst_before_header_element', $bst_act );
bst_test( false !== strpos( $bst_out, 'g147-x' ) && false !== strpos( $bst_out, '<!--g147-->' ) && false !== strpos( $bst_logo2, 'g147-a' ), 'hooks: bst_block_classes, bst_block_html and bst_before_header_element change output' );
bst_test( $bst_base === render_block( $bst_hb ) && $bst_logo === bst_header_element( 'logo', 'desktop' ), 'hooks: nothing hooked leaves HTML unchanged' );
$bst_pf = function ( $p ) { $p['divider']['g147'] = array( 'G', false, array() ); return $p; };
add_filter( 'bst_block_presets', $bst_pf );
bst_test( isset( bst_block_presets()['divider']['g147'] ), 'hooks: bst_block_presets filter' );
remove_filter( 'bst_block_presets', $bst_pf );

// G-131: attribute presets (built-in Divider/Button, site presets saved and deleted, AI catalog).
$bst_ap = bst_block_attr_presets();
bst_test( count( $bst_ap['bst/divider'] ?? array() ) >= 4 && count( $bst_ap['bst/button'] ?? array() ) >= 4, 'presets: built-in Divider and Button' );
$bst_ok = true;
foreach ( $bst_ap as $bst_b => $bst_list ) {
	foreach ( $bst_list as $bst_p ) {
		$bst_s = bst_sanitize_preset_attrs( $bst_b, $bst_p[2] );
		if ( $bst_s !== $bst_p[2] ) {
			$bst_ok = 'bad ' . wp_json_encode( array_diff_assoc( array_map( 'wp_json_encode', $bst_p[2] ), array_map( 'wp_json_encode', $bst_s ) ) );
		}
	}
}
bst_test( true === $bst_ok, 'presets: built-in attrs are valid block.json attributes ' . ( true === $bst_ok ? '' : $bst_ok ) );
wp_set_current_user( 1 );
$bst_r = new WP_REST_Request( 'POST', '/bst/v1/block-presets' );
$bst_r->set_body_params( array( 'block' => 'bst/divider', 'label' => 'Mine', 'attrs' => array( 'style' => 'wave', 'thickness' => 3, 'evil' => 'x', 'text' => 7 ) ) );
$bst_res = rest_do_request( $bst_r );
$bst_user = bst_user_block_presets()['bst/divider'] ?? array();
$bst_id = key( $bst_user );
bst_test( 200 === $bst_res->get_status() && 1 === count( $bst_user ) && array( 'style' => 'wave', 'thickness' => 3 ) === $bst_user[ $bst_id ]['attrs'], 'presets: save keeps only valid block.json attributes' );
bst_test( isset( bst_aid_tool_block_catalog( array( 'needs' => bst_aid_block_needs() ) )['blocks'] ) && isset( bst_block_attr_preset_list( 'bst/divider' )[ $bst_id ] ), 'presets: AI catalog lists saved preset' );
$bst_r = new WP_REST_Request( 'DELETE', '/bst/v1/block-presets' );
$bst_r->set_query_params( array( 'block' => 'bst/divider', 'id' => $bst_id ) );
rest_do_request( $bst_r );
bst_test( empty( bst_user_block_presets()['bst/divider'] ), 'presets: delete' );

// G-132-c: Button styles, icon only, badge, per-device width, hover border/shadow.
$bst_btn = function ( $attrs ) {
	return render_block( array( 'blockName' => 'bst/button', 'attrs' => $attrs, 'innerBlocks' => array(), 'innerHTML' => '', 'innerContent' => array() ) );
};
$bst_h = $bst_btn( array( 'text' => 'Go', 'url' => 'https://example.com/', 'style' => 'arrow-circle' ) );
bst_test( false !== strpos( $bst_h, 'bs-btn--arrow-circle' ) && false !== strpos( $bst_h, 'bs-icon' ) && false !== strpos( $bst_h, '<span>Go</span>' ), 'button: arrow-circle adds an icon' );
$bst_h = $bst_btn( array( 'text' => 'Search', 'icon' => 'search', 'iconOnly' => true ) );
bst_test( false !== strpos( $bst_h, 'bs-btn--icon-only' ) && false !== strpos( $bst_h, 'aria-label="Search"' ) && false === strpos( $bst_h, '<span>Search</span>' ), 'button: icon only keeps an aria-label' );
$bst_h = $bst_btn( array( 'text' => 'Buy', 'badge' => '-20%', 'widthMobile' => 'full', 'widthTablet' => 'bad', 'hoverBorder' => '#ff0000', 'hoverShadow' => '#00ff00', 'style' => 'neon' ) );
bst_test( false !== strpos( $bst_h, 'bs-btn__badge' ) && false !== strpos( $bst_h, 'bs-btn--m-full' ) && false === strpos( $bst_h, 'bs-btn--t-' ) && false !== strpos( $bst_h, '--bsb-hbc:#ff0000' ) && false !== strpos( $bst_h, '--bsb-hsh:' ) && false !== strpos( $bst_h, 'bs-btn--neon' ), 'button: badge, per-device width, hover colors' );
$bst_h = $bst_btn( array( 'text' => 'Old', 'url' => '/x' ) );
bst_test( '<a class="bs-btn bs-btn--primary wp-block-bst-button" href="/x"><span>Old</span></a>' === $bst_h, 'button: old attributes render as before' );
