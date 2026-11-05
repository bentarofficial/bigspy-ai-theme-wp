<?php
/**
 * G-67: render the responsive/a11y fixture page as full HTML on a disposable WordPress (CLI, no server).
 * Usage: php render-fixture.php /path/to/wordpress > fixture.html
 */
$bst_wp_dir = rtrim( $argv[1] ?? '/wordpress', '/' );
$_SERVER['HTTP_HOST']   = 't.test';
$_SERVER['REQUEST_URI'] = '/';
define( 'WP_USE_THEMES', false );
require $bst_wp_dir . '/wp-load.php';
$img   = 'http://t.test/wp-content/themes/bigspy-ai-theme-wp/screenshot.webp';
$col   = function ( $span, $inner ) {
	return '<!-- wp:bst/column {"spanDesktop":' . $span . ',"spanMobile":12} -->' . $inner . '<!-- /wp:bst/column -->';
};
$tab   = function ( $t, $inner ) {
	return '<!-- wp:bst/tab {"title":"' . $t . '"} -->' . $inner . '<!-- /wp:bst/tab -->';
};
$para  = function ( $t ) {
	return '<!-- wp:paragraph --><p>' . $t . '</p><!-- /wp:paragraph -->';
};
$content =
	'<!-- wp:bst/banner {"imageUrl":"' . $img . '"} -->'
	. '<!-- wp:bst/heading {"text":"Giặt sấy giao nhận tận nơi trong ngày","tag":"h1"} /-->'
	. $para( 'Một câu mô tả dài vừa phải để thử xuống dòng trên điện thoại.' )
	. '<!-- wp:bst/buttons --><!-- wp:bst/button {"text":"Nhận báo giá","url":"#quote"} /--><!-- /wp:bst/buttons -->'
	. '<!-- /wp:bst/banner -->'
	. '<!-- wp:bst/section --><!-- wp:bst/row {"reverseMobile":true} -->'
	. $col( 6, $para( 'Cột A <a href="#a">liên kết A</a>' ) ) . $col( 6, $para( 'Cột B <a href="#b">liên kết B</a>' ) )
	. '<!-- /wp:bst/row --><!-- /wp:bst/section -->'
	. '<!-- wp:bst/section --><!-- wp:bst/tabs -->'
	. $tab( 'Giặt', $para( 'Panel giặt <a href="#p1">chi tiết</a>' ) ) . $tab( 'Sấy', $para( 'Panel sấy' ) ) . $tab( 'Ủi', $para( 'Panel ủi' ) )
	. '<!-- /wp:bst/tabs --><!-- wp:bst/tabs {"style":"vertical"} -->'
	. $tab( 'Một', $para( 'Thứ nhất' ) ) . $tab( 'Hai', $para( 'Thứ hai' ) ) . $tab( 'Ba', $para( 'Thứ ba' ) )
	. '<!-- /wp:bst/tabs --><!-- /wp:bst/section -->';
$id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'G-67 fixture', 'post_content' => $content ) );
update_post_meta( $id, '_wp_page_template', 'page-templates/full-width.php' );
$GLOBALS['wp']->main( array( 'page_id' => $id ) );
$GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];
ob_start();
$tpl = get_page_template();
require $tpl ? $tpl : get_index_template();
$html = ob_get_clean();
wp_delete_post( $id, true );
echo $html;
