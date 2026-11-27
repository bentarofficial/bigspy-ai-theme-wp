<?php
/**
 * G-219-c distance field, no WordPress or network needed: php tools/tests/form-distance-check.php
 * Fake Photon/OSRM answers stand in for the owner's servers.
 */
define( 'ABSPATH', __DIR__ );
define( 'MINUTE_IN_SECONDS', 60 );
define( 'DAY_IN_SECONDS', 86400 );
$GLOBALS['bst_t']     = array();
$GLOBALS['bst_calls'] = array();
$GLOBALS['bst_local'] = false;
function sanitize_key( $k ) {
	return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $k ) );
}
function wp_json_encode( $v ) {
	return json_encode( $v );
}
function wp_parse_url( $u ) {
	return parse_url( $u );
}
function esc_url_raw( $u ) {
	return $u;
}
function untrailingslashit( $u ) {
	return rtrim( $u, '/\\' );
}
function remove_accents( $s ) {
	return strtr( $s, array( 'à' => 'a', 'ạ' => 'a', 'ơ' => 'o', 'ư' => 'u', 'Đ' => 'D', 'đ' => 'd' ) );
}
function add_action() {
}
function apply_filters( $hook, $v ) {
	return 'bst_form_distance_allow_local' === $hook ? $GLOBALS['bst_local'] : $v;
}
function get_transient( $k ) {
	return $GLOBALS['bst_t'][ $k ] ?? false;
}
function set_transient( $k, $v, $ttl ) {
	$GLOBALS['bst_t'][ $k ] = $v;
}
function is_wp_error( $x ) {
	return false;
}
function wp_remote_retrieve_response_code( $r ) {
	return $r['code'];
}
function wp_remote_retrieve_body( $r ) {
	return $r['body'];
}
function bst_fake( $url ) {
	$GLOBALS['bst_calls'][] = $url;
	if ( false !== strpos( $url, 'down.example' ) ) {
		return array( 'code' => 500, 'body' => '' );
	}
	if ( false !== strpos( $url, '/api?' ) ) {
		$q = urldecode( substr( $url, strpos( $url, 'q=' ) + 2 ) );
		$c = array( 'Ha Noi' => array( 105.85, 21.03 ), 'Hai Phong' => array( 106.68, 20.86 ) );
		$hit = $c[ $q ] ?? null;
		return array( 'code' => 200, 'body' => json_encode( array( 'features' => $hit ? array( array( 'geometry' => array( 'coordinates' => $hit ) ) ) : array() ) ) );
	}
	if ( false !== strpos( $url, '/route/v1/driving/105.85,21.03;106.68,20.86' ) ) {
		return array( 'code' => 200, 'body' => json_encode( array( 'code' => 'Ok', 'routes' => array( array( 'distance' => 120345.6 ) ) ) ) );
	}
	return array( 'code' => 200, 'body' => json_encode( array( 'code' => 'NoRoute' ) ) );
}
function wp_safe_remote_get( $url, $args ) {
	return bst_fake( 'safe:' . $url );
}
function wp_remote_get( $url, $args ) {
	return bst_fake( 'local:' . $url );
}
require dirname( __DIR__, 2 ) . '/bigspy-ai-theme-wp/inc/form-distance.php';

$fail = 0;
function t( $ok, $name ) {
	global $fail;
	echo ( $ok ? 'ok   ' : 'FAIL ' ) . $name . "\n";
	$fail += $ok ? 0 : 1;
}

$f = array( 'id' => 'f_km', 'type' => 'distance', 'name' => 'km', 'from' => 'f_a', 'to' => 'f_b', 'geocoder' => 'https://photon.example/', 'router' => 'https://osrm.example' );
t( 120.3 === bst_form_distance_km( $f, 'Ha Noi', 'Hai Phong' ), 'measures driving km, one decimal' );
$n = count( $GLOBALS['bst_calls'] );
t( 3 === $n && 0 === strpos( $GLOBALS['bst_calls'][0], 'safe:https://photon.example/api?limit=1&q=Ha%20Noi' ), 'two geocodes and one route, safe requests' );
t( 120.3 === bst_form_distance_km( $f, ' ha  noi ', 'HAI PHONG' ) && count( $GLOBALS['bst_calls'] ) === $n, 'same pair (case, spaces) comes from the cache' );
t( null === bst_form_distance_km( $f, 'Nowhere', 'Hai Phong' ), 'unknown address cannot be measured' );
t( null === bst_form_distance_km( array_merge( $f, array( 'router' => 'https://down.example' ) ), 'Ha Noi', 'Hai Phong' ), 'router down cannot be measured' );
t( null === bst_form_distance_km( array_merge( $f, array( 'router' => '' ) ), 'Ha Noi', 'Hai Phong' ), 'no router set' );
foreach ( array( 'ftp://osrm.example', 'javascript:alert(1)', 'https://u:p@osrm.example', 'https://osrm.example/?x=1', 'osrm.example' ) as $bad ) {
	t( '' === bst_form_distance_base( $bad ), 'rejects service URL ' . $bad );
}
t( null === bst_form_distance_km( $f, '', 'Hai Phong' ), 'empty address' );
$GLOBALS['bst_t']     = array();
$GLOBALS['bst_local'] = true;
bst_form_distance_km( $f, 'Ha Noi', 'Hai Phong' );
t( 0 === strpos( end( $GLOBALS['bst_calls'] ), 'local:' ), 'local services only through the filter' );

// Submission: the server fills the km itself; what the browser sent is ignored.
$fields = array(
	array( 'id' => 'f_a', 'type' => 'text', 'name' => 'from' ),
	array( 'id' => 'f_b', 'type' => 'text', 'name' => 'to' ),
	$f,
);
$v = bst_form_distance_fill( $fields, array( 'from' => 'Ha Noi', 'to' => 'Hai Phong', 'km' => '1' ) );
t( '120.3' === $v['km'], 'server measures again on submit' );
$v = bst_form_distance_fill( $fields, array( 'from' => 'Nowhere', 'to' => 'Hai Phong', 'km' => '5' ) );
t( '' === $v['km'], 'unmeasured distance is empty, not the browser value' );

exit( $fail ? 1 : 0 );
