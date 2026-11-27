<?php
/**
 * Form "Distance (km)" field (G-219-c): driving distance between two address fields of the
 * same form, measured by free, self-hosted OpenStreetMap services the site owner runs:
 * a Photon geocoder (address -> coordinates) and an OSRM router (coordinates -> km).
 * No paid API, no key. The service URLs come only from the saved block, never from the
 * visitor; the browser asks this site, and the server measures again when the form is sent.
 *
 * Requests use wp_safe_remote_get(), which refuses local/private addresses. A service on the
 * same machine needs: add_filter( 'bst_form_distance_allow_local', '__return_true' );
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Base URL of a service (http/https, no query), or ''. */
function bst_form_distance_base( $url ) {
	$url = trim( (string) $url );
	$p   = wp_parse_url( $url );
	if ( ! $p || ! in_array( $p['scheme'] ?? '', array( 'http', 'https' ), true ) || empty( $p['host'] ) || isset( $p['user'] ) || isset( $p['query'] ) ) {
		return '';
	}
	return untrailingslashit( esc_url_raw( $url ) );
}

/** GET a JSON document from a service, or null. */
function bst_form_distance_get( $url ) {
	$args = array( 'timeout' => 6, 'redirection' => 0, 'headers' => array( 'Accept' => 'application/json' ), 'user-agent' => 'BIGSPY-Theme-Form' );
	$res  = apply_filters( 'bst_form_distance_allow_local', false ) ? wp_remote_get( $url, $args ) : wp_safe_remote_get( $url, $args );
	if ( is_wp_error( $res ) || 200 !== (int) wp_remote_retrieve_response_code( $res ) ) {
		return null;
	}
	$data = json_decode( (string) wp_remote_retrieve_body( $res ), true );
	return is_array( $data ) ? $data : null;
}

/** [lon, lat] of an address from a Photon geocoder, or null. */
function bst_form_distance_geocode( $geo, $address ) {
	$data = bst_form_distance_get( $geo . '/api?limit=1&q=' . rawurlencode( $address ) );
	$c    = $data['features'][0]['geometry']['coordinates'] ?? null;
	return is_array( $c ) && 2 === count( $c ) && is_numeric( $c[0] ) && is_numeric( $c[1] ) ? array( (float) $c[0], (float) $c[1] ) : null;
}

/**
 * Driving km between two addresses for a distance field, one decimal, or null when it cannot
 * be measured. Results are cached for 30 days per pair of addresses and services.
 */
function bst_form_distance_km( $f, $from, $to ) {
	$from = trim( (string) $from );
	$to   = trim( (string) $to );
	$geo  = bst_form_distance_base( $f['geocoder'] ?? '' );
	$osrm = bst_form_distance_base( $f['router'] ?? '' );
	if ( '' === $from || '' === $to || '' === $geo || '' === $osrm ) {
		return null;
	}
	$fold  = function ( $s ) {
		return strtolower( preg_replace( '/\s+/', ' ', remove_accents( $s ) ) );
	};
	$cache = 'bst_dist_' . md5( wp_json_encode( array( $geo, $osrm, $fold( $from ), $fold( $to ) ) ) );
	$hit   = get_transient( $cache );
	if ( false !== $hit ) {
		return '' === $hit ? null : (float) $hit;
	}
	$a  = bst_form_distance_geocode( $geo, $from );
	$b  = $a ? bst_form_distance_geocode( $geo, $to ) : null;
	$km = null;
	if ( $a && $b ) {
		$data = bst_form_distance_get( $osrm . '/route/v1/driving/' . $a[0] . ',' . $a[1] . ';' . $b[0] . ',' . $b[1] . '?overview=false' );
		$m    = $data['routes'][0]['distance'] ?? null;
		if ( 'Ok' === ( $data['code'] ?? '' ) && is_numeric( $m ) && $m >= 0 && $m < 1e8 ) {
			$km = round( $m / 1000, 1 );
		}
	}
	// A failure is cached briefly so a broken service is not hammered.
	set_transient( $cache, null === $km ? '' : (string) $km, null === $km ? 10 * MINUTE_IN_SECONDS : 30 * DAY_IN_SECONDS );
	return $km;
}

/** Fill the distance fields of a submission from their two address fields (server side). */
function bst_form_distance_fill( $fields, $values ) {
	$by = array_column( $fields, null, 'id' );
	foreach ( $fields as $f ) {
		if ( 'distance' !== ( $f['type'] ?? '' ) ) {
			continue;
		}
		$a  = $by[ $f['from'] ?? '' ] ?? null;
		$b  = $by[ $f['to'] ?? '' ] ?? null;
		$km = $a && $b ? bst_form_distance_km( $f, $values[ sanitize_key( $a['name'] ) ] ?? '', $values[ sanitize_key( $b['name'] ) ] ?? '' ) : null;
		$values[ sanitize_key( $f['name'] ) ] = null === $km ? '' : (string) $km;
	}
	return $values;
}

/** Browser preview: km for one distance field of a saved form. */
function bst_form_distance_ajax() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- public form; the service URLs come from the saved block and the visitor is rate limited.
	$key   = isset( $_POST['key'] ) ? sanitize_key( wp_unslash( $_POST['key'] ) ) : '';
	$post  = isset( $_POST['post'] ) ? absint( $_POST['post'] ) : 0;
	$field = isset( $_POST['field'] ) ? sanitize_key( wp_unslash( $_POST['field'] ) ) : '';
	$from  = isset( $_POST['from'] ) ? mb_substr( sanitize_text_field( wp_unslash( $_POST['from'] ) ), 0, 200 ) : '';
	$to    = isset( $_POST['to'] ) ? mb_substr( sanitize_text_field( wp_unslash( $_POST['to'] ) ), 0, 200 ) : '';
	// phpcs:enable
	$rate = 'bst_dist_rate_' . md5( (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$hits = (int) get_transient( $rate );
	if ( $hits >= 30 ) {
		wp_send_json( array( 'ok' => false ), 429 );
	}
	set_transient( $rate, $hits + 1, MINUTE_IN_SECONDS );
	$a = bst_native_form_find( $key, $post );
	$f = null;
	foreach ( $a ? bst_form_schema_normalize( bst_native_form_settings( $a )['fields'] ) : array() as $x ) {
		if ( 'distance' === ( $x['type'] ?? '' ) && sanitize_key( $x['id'] ) === $field ) {
			$f = $x;
		}
	}
	if ( ! $f ) {
		wp_send_json( array( 'ok' => false ), 404 );
	}
	$km = bst_form_distance_km( $f, $from, $to );
	wp_send_json( array( 'ok' => null !== $km, 'km' => $km ) );
}
add_action( 'wp_ajax_bst_form_distance', 'bst_form_distance_ajax' );
add_action( 'wp_ajax_nopriv_bst_form_distance', 'bst_form_distance_ajax' );
