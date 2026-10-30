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
		foreach ( array( 'on', 'webp', 'smtp', 'related', 'code' ) as $test ) {
			if ( function_exists( 'bst_t_' . $test ) ) {
				call_user_func( 'bst_t_' . $test );
			}
		}
	}
} catch ( Throwable $e ) {
	bst_test( false, get_class( $e ) . ' ' . $e->getMessage() . ' @' . $e->getLine() );
}

function bst_t_off() {
	bst_test( array() === (array) get_option( 'bst_features', array() ), 'all features off by default' );
	bst_test( ! function_exists( 'bst_webp_convert' ) && ! function_exists( 'bst_smtp_configure' ) && ! function_exists( 'bst_ir_insert' ) && ! function_exists( 'bst_code_print' ), 'no feature code loaded while off' );
	ob_start();
	bst_features_page();
	$html = ob_get_clean();
	bst_test( 4 === substr_count( $html, 'name="bst_on[]"' ) && false === strpos( $html, 'checked' ), 'page lists 4 unticked features' );
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
	bst_test( substr_count( $html, "checked='checked'" ) >= 4, 'features show as on' );
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
