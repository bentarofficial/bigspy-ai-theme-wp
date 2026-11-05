<?php
/**
 * G-67: check_page block warnings on a disposable WordPress (CLI).
 * Usage: php check-page-integration.php /path/to/wordpress
 */
$bst_wp_dir             = rtrim( $argv[1] ?? '/wordpress', '/' );
$_SERVER['HTTP_HOST']   = 't.test';
$_SERVER['REQUEST_URI'] = '/';
require $bst_wp_dir . '/wp-load.php';
wp_set_current_user( 1 );
$fail = 0;
function t( $ok, $label ) {
	global $fail;
	echo ( $ok ? 'ok   ' : 'FAIL ' ) . $label . "\n";
	$fail += $ok ? 0 : 1;
}
$p   = '<!-- wp:paragraph --><p>x <a href="/a">a</a></p><!-- /wp:paragraph -->';
$col = '<!-- wp:bst/column -->' . $p . '<!-- /wp:bst/column -->';
$c   = '<!-- wp:bst/section --><!-- wp:bst/row {"reverseMobile":true} -->' . $col . $col . '<!-- /wp:bst/row --><!-- /wp:bst/section -->'
	. '<!-- wp:bst/tabs --><!-- wp:bst/tab {"title":"A"} -->' . $p . '<!-- /wp:bst/tab --><!-- wp:bst/tab {"title":"A"} --><!-- /wp:bst/tab --><!-- /wp:bst/tabs -->'
	. '<!-- wp:bst/banner {"imageUrl":"http://t.test/x.webp","overlayOpacity":5} --><!-- wp:bst/buttons --><!-- wp:bst/button {"text":"Go"} /--><!-- /wp:bst/buttons --><!-- /wp:bst/banner -->'
	. '<!-- wp:bst/row --><!-- wp:bst/column --><!-- wp:bst/image {"imageUrl":"http://t.test/y.webp"} /--><!-- /wp:bst/column --><!-- wp:bst/column --><!-- /wp:bst/column --><!-- /wp:bst/row -->';
$id  = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'draft', 'post_title' => 'G-67 check', 'post_content' => $c ) );
$r   = bst_aid_tool_check_page( array( 'id' => $id ) );
$w   = array();
foreach ( $r['blocks'] as $x ) {
	$w[] = $x['path'] . ' ' . $x['block'] . ' ' . $x['kind'] . ' ' . $x['viewport'];
}
echo implode( "\n", array_map( function ( $s ) { return '     ' . $s; }, $w ) ), "\n";
t( in_array( '0.0 bst/row structure mobile', $w, true ), 'reverse-on-mobile with links reported with its path' );
t( in_array( '1 bst/tabs structure all', $w, true ) && 2 === count( array_filter( $w, function ( $s ) { return 0 === strpos( $s, '1 bst/tabs' ); } ) ), 'repeated tab title and empty panel reported' );
t( in_array( '2 bst/banner suggestion all', $w, true ), 'banner text on a light overlay is a suggestion, not a measured fail' );
t( in_array( '2.0.0 bst/button structure all', $w, true ), 'button without link reported' );
t( ! preg_grep( '/^3 /', $w ), 'plain row without reverse gives no warning' );
t( false !== strpos( $r['limits'], 'contrast' ), 'limits say contrast is not measured' );
t( $c === get_post_field( 'post_content', $id ), 'check_page changes nothing' );
wp_delete_post( $id, true );
echo $fail ? "\n$fail failed\n" : "\nall passed\n";
exit( $fail ? 1 : 0 );
