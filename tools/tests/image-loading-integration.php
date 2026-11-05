<?php
/**
 * URL images in Banner/Image follow WordPress loading optimization (SOURCE-AUDIT P1).
 * Usage: php tools/tests/image-loading-integration.php /path/to/wordpress (disposable site, theme active).
 */
$_SERVER['HTTP_HOST']   = 't.test';
$_SERVER['REQUEST_URI'] = '/';
require rtrim( $argv[1] ?? '/wordpress', '/' ) . '/wp-load.php';
$fail = 0;
function t( $ok, $label ) {
	global $fail;
	echo ( $ok ? 'ok   ' : 'FAIL ' ) . $label . "\n";
	$fail += $ok ? 0 : 1;
}
$img     = function ( $n ) {
	return '<!-- wp:bst/image {"imageUrl":"https://cdn.t.test/' . $n . '.jpg","alt":"' . $n . '"} /-->';
};
$content = '<!-- wp:bst/banner {"imageUrl":"https://cdn.t.test/hero.jpg"} /-->' . $img( 'a' ) . $img( 'b' ) . $img( 'c' ) . $img( 'd' );
$id      = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Loading', 'post_content' => $content ) );
global $wp_query;
$wp_query                = new WP_Query( array( 'page_id' => $id ) );
$GLOBALS['wp_the_query'] = $wp_query;
$html                    = '';
while ( have_posts() ) {
	the_post();
	ob_start();
	the_content();
	$html = ob_get_clean();
}
preg_match_all( '/<img[^>]*src="https:\/\/cdn\.t\.test\/(\w+)\.jpg"[^>]*>/', $html, $m );
$tags = array_combine( $m[1], $m[0] );
t( 5 === count( $tags ), 'five URL images rendered' );
t( isset( $tags['hero'] ) && false === strpos( $tags['hero'], 'loading="lazy"' ) && false !== strpos( $tags['hero'], 'fetchpriority="high"' ), 'hero banner image: not lazy, fetchpriority high' );
t( isset( $tags['a'] ) && false === strpos( $tags['a'], 'loading="lazy"' ) && false === strpos( $tags['a'], 'fetchpriority' ), 'next images above the fold: not lazy' );
t( isset( $tags['d'] ) && false !== strpos( $tags['d'], 'loading="lazy"' ), 'later images: lazy' );
t( false === strpos( implode( '', $tags ), 'width=' ), 'no invented width/height printed' );
t( false !== strpos( $tags['hero'] ?? '', 'decoding="async"' ), 'decoding async kept' );
wp_delete_post( $id, true );
echo $fail ? "\n$fail failed\n" : "\nall passed\n";
exit( $fail ? 1 : 0 );
