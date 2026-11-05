<?php
/**
 * G-41: render one fixture page as full HTML on a disposable WordPress (CLI, no server).
 * Usage: php render-page.php /path/to/wordpress <text|landing|form> > page.html
 */
$bst_wp_dir             = rtrim( $argv[1] ?? '/wordpress', '/' );
$bst_kind               = $argv[2] ?? 'text';
$_SERVER['HTTP_HOST']   = 't.test';
$_SERVER['SERVER_NAME'] = 't.test';
$_SERVER['REQUEST_URI'] = '/';
define( 'WP_USE_THEMES', false );
require $bst_wp_dir . '/wp-load.php';
$para  = function ( $t ) {
	return '<!-- wp:paragraph --><p>' . $t . '</p><!-- /wp:paragraph -->';
};
$pages = array(
	'text'    => '<!-- wp:heading --><h2>Bài viết</h2><!-- /wp:heading -->' . str_repeat( $para( 'Đoạn văn bản thường, không có block tương tác.' ), 6 ),
	'landing' => '<!-- wp:bst/banner {"imageUrl":"http://t.test/wp-content/themes/bigspy-ai-theme-wp/screenshot.webp"} --><!-- wp:bst/heading {"text":"Tiêu đề","tag":"h1"} /--><!-- wp:bst/buttons --><!-- wp:bst/button {"text":"Mua","url":"#a"} /--><!-- /wp:bst/buttons --><!-- /wp:bst/banner -->'
		. '<!-- wp:bst/tabs --><!-- wp:bst/tab {"title":"A"} -->' . $para( 'A' ) . '<!-- /wp:bst/tab --><!-- wp:bst/tab {"title":"B"} -->' . $para( 'B' ) . '<!-- /wp:bst/tab --><!-- /wp:bst/tabs -->'
		. '<!-- wp:bst/accordion --><!-- wp:bst/accordion-item {"title":"Hỏi"} -->' . $para( 'Đáp' ) . '<!-- /wp:bst/accordion-item --><!-- /wp:bst/accordion -->'
		. '<!-- wp:bst/counter {"number":120} /-->',
	'form'    => '<!-- wp:bst/form {"provider":"bigspy","formKey":"fg41","fields":[{"id":"f_kg","type":"number","name":"kg","label":"Kg"},{"id":"f_t","type":"calc","name":"t","label":"Tổng","formula":"{f_kg} * 18000","isTotal":true},{"id":"f_n","type":"text","name":"ten","label":"Tên","conditions":{"rules":[{"field":"f_kg","op":"not_empty"}]}}]} /-->',
);
$id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'G-41 ' . $bst_kind, 'post_content' => $pages[ $bst_kind ] ) );
update_post_meta( $id, '_wp_page_template', 'page-templates/full-width.php' );
$GLOBALS['wp']->main( array( 'page_id' => $id ) );
$GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];
ob_start();
$tpl = get_page_template();
require $tpl ? $tpl : get_index_template();
$html = ob_get_clean();
if ( function_exists( 'bst_perf_split_on' ) && bst_perf_split_on() ) {
	$html = bst_perf_block_css_buffer( $html ); // Same filter the theme runs on the page output for visitors.
}
wp_delete_post( $id, true );
echo $html;
