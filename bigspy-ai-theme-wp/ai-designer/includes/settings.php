<?php
/**
 * AI Designer settings: API key (encrypted, server only), connection state, models, budgets.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Settings with defaults. */
function bst_aid_settings() {
	$s = get_option( 'bst_aid_settings', array() );
	return wp_parse_args(
		is_array( $s ) ? $s : array(),
		array(
			'connected'      => false,
			'text_model'     => '',
			'image_model'    => '',
			'budget_task'    => 2.0,  // USD per task, 0 = no limit.
			'budget_day'     => 10.0, // USD per day, 0 = no limit.
			'allow_fallback' => true,
		)
	);
}

function bst_aid_save_settings( $changes ) {
	update_option( 'bst_aid_settings', array_merge( bst_aid_settings(), $changes ), false );
}

/* ------------------------------------------------------------------ Key storage */

function bst_aid_secret() {
	return hash( 'sha256', wp_salt( 'auth' ) . 'bst-ai-designer', true );
}

function bst_aid_encrypt( $plain ) {
	if ( function_exists( 'openssl_encrypt' ) ) {
		$iv  = random_bytes( 16 );
		$enc = openssl_encrypt( $plain, 'aes-256-cbc', bst_aid_secret(), OPENSSL_RAW_DATA, $iv );
		if ( false !== $enc ) {
			return 'v1:' . base64_encode( $iv . $enc ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		}
	}
	return 'v0:' . base64_encode( $plain ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
}

function bst_aid_decrypt( $stored ) {
	$stored = (string) $stored;
	if ( 0 === strpos( $stored, 'v1:' ) && function_exists( 'openssl_decrypt' ) ) {
		$raw = base64_decode( substr( $stored, 3 ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		$out = openssl_decrypt( substr( $raw, 16 ), 'aes-256-cbc', bst_aid_secret(), OPENSSL_RAW_DATA, substr( $raw, 0, 16 ) );
		return false === $out ? '' : $out;
	}
	if ( 0 === strpos( $stored, 'v0:' ) ) {
		return (string) base64_decode( substr( $stored, 3 ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
	}
	return '';
}

/** The API key, or '' when none is stored. Never sent to the browser. */
function bst_aid_key() {
	return bst_aid_decrypt( get_option( 'bst_aid_key', '' ) );
}

/** Masked key for display: kyma-••••••1234. */
function bst_aid_key_masked() {
	$key = bst_aid_key();
	if ( '' === $key ) {
		return '';
	}
	$prefix = preg_match( '/^([a-z]+-)/i', $key, $m ) ? $m[1] : '';
	return $prefix . '••••••' . substr( $key, -4 );
}

/** Connected and usable: a key is stored and the user has not disconnected. */
function bst_aid_ready() {
	$s = bst_aid_settings();
	return $s['connected'] && '' !== bst_aid_key() && '' !== $s['text_model'];
}

/* ------------------------------------------------------------------ Models */

/** Models from Kyma (public list), cached for 6 hours: array( 'text' => [...], 'image' => [...] ). */
function bst_aid_models( $refresh = false ) {
	$cached = get_transient( 'bst_aid_models' );
	if ( ! $refresh && is_array( $cached ) ) {
		return $cached;
	}
	$out = array( 'text' => array(), 'image' => array() );
	$text = bst_aid_http( 'GET', '/models?type=language&tools=true', null, false );
	foreach ( (array) ( $text['data']['data'] ?? array() ) as $m ) {
		if ( empty( $m['id'] ) || empty( $m['supports_tools'] ) ) {
			continue;
		}
		$out['text'][] = array(
			'id'          => (string) $m['id'],
			'name'        => (string) ( $m['name'] ?? $m['id'] ),
			'reasoning'   => ! empty( $m['supports_reasoning'] ),
			'context'     => (int) ( $m['context_window'] ?? 0 ),
			'input'       => (float) ( $m['pricing']['input'] ?? 0 ),
			'output'      => (float) ( $m['pricing']['output'] ?? 0 ),
			'quality'     => (string) ( $m['quality_tier'] ?? '' ),
			'free'        => ! empty( $m['free_tier'] ),
		);
	}
	// Tiers from the live data, not a fixed list: strong + cheap first, then the top models.
	foreach ( $out['text'] as $i => $m ) {
		$good = in_array( $m['quality'], array( 'frontier-open', 'strong' ), true );
		// Low-cost picks only from OpenAI and Anthropic: their models are trained for agentic tool use.
		$agentic = (bool) preg_match( '/^(gpt-|claude-)/i', $m['id'] ) && false === stripos( $m['id'], 'gpt-oss' ); // Open-weight gpt-oss runs on third-party hosts: not a recommended pick.
		$out['text'][ $i ]['tier'] = ( $good && $agentic && $m['input'] > 0 && $m['input'] <= 0.6 ) ? 'value' : ( ( 'frontier-open' === $m['quality'] && $m['input'] >= 2 && ( $agentic || preg_match( '/^gemini-([4-9]|\d{2,})/i', $m['id'] ) ) ) ? 'premium' : 'all' ); // Gemini only from generation 4 (Argon) up.
	}
	usort( $out['text'], function ( $a, $b ) {
		$rank = array( 'frontier-open' => 0, 'strong' => 1 );
		return array( $rank[ $a['quality'] ] ?? 2, $a['input'] ) <=> array( $rank[ $b['quality'] ] ?? 2, $b['input'] );
	} );
	$image   = bst_aid_http( 'GET', '/models?type=image', null, false );
	$svg_ok  = in_array( 'image/svg+xml', get_allowed_mime_types(), true );
	foreach ( (array) ( $image['data']['data'] ?? array() ) as $m ) {
		if ( empty( $m['id'] ) ) {
			continue;
		}
		// SVG results cannot be saved to the Media Library unless the site allows SVG uploads.
		if ( ! $svg_ok && false !== stripos( (string) $m['id'], 'vector' ) ) {
			continue;
		}
		$out['image'][] = array(
			'id'          => (string) $m['id'],
			'name'        => (string) ( $m['name'] ?? $m['id'] ),
			'free'        => ! empty( $m['free_tier'] ),
			'price'       => (float) ( $m['pricing']['per_image_usd'] ?? $m['pricing']['per_image'] ?? 0 ),
			'recommended' => in_array( (string) $m['id'], array( 'gpt-image-2', 'gpt-image-2.5-flare' ), true ),
		);
	}
	if ( $out['text'] ) {
		set_transient( 'bst_aid_models', $out, 6 * HOUR_IN_SECONDS );
	}
	return $out;
}

/** Meta of one text model (reasoning support…), or null. */
function bst_aid_text_model_meta( $id ) {
	foreach ( bst_aid_models()['text'] as $m ) {
		if ( $m['id'] === $id ) {
			return $m;
		}
	}
	return null;
}

/* ------------------------------------------------------------------ REST */

add_action(
	'rest_api_init',
	function () {
		$ns = bst_aid_rest_ns();
		register_rest_route( $ns, '/settings', array(
			array( 'methods' => 'GET', 'callback' => 'bst_aid_rest_settings', 'permission_callback' => 'bst_aid_rest_can_use' ),
			array( 'methods' => 'POST', 'callback' => 'bst_aid_rest_settings_save', 'permission_callback' => 'bst_aid_rest_can_manage' ),
		) );
		register_rest_route( $ns, '/connect', array( 'methods' => 'POST', 'callback' => 'bst_aid_rest_connect', 'permission_callback' => 'bst_aid_rest_can_manage' ) );
		register_rest_route( $ns, '/disconnect', array( 'methods' => 'POST', 'callback' => 'bst_aid_rest_disconnect', 'permission_callback' => 'bst_aid_rest_can_manage' ) );
		register_rest_route( $ns, '/reconnect', array( 'methods' => 'POST', 'callback' => 'bst_aid_rest_reconnect', 'permission_callback' => 'bst_aid_rest_can_manage' ) );
		register_rest_route( $ns, '/key/delete', array( 'methods' => 'POST', 'callback' => 'bst_aid_rest_key_delete', 'permission_callback' => 'bst_aid_rest_can_manage' ) );
		register_rest_route( $ns, '/data/delete', array( 'methods' => 'POST', 'callback' => 'bst_aid_rest_data_delete', 'permission_callback' => 'bst_aid_rest_can_manage' ) );
	}
);

/** Public view of the settings (no key). */
function bst_aid_rest_settings( $request = null ) {
	$s       = bst_aid_settings();
	$has_key = '' !== bst_aid_key();
	$data    = array(
		'hasKey'        => $has_key,
		'keyMasked'     => bst_aid_key_masked(),
		'connected'     => $has_key && $s['connected'],
		'ready'         => bst_aid_ready(),
		'textModel'     => $s['text_model'],
		'imageModel'    => $s['image_model'],
		'budgetTask'    => (float) $s['budget_task'],
		'budgetDay'     => (float) $s['budget_day'],
		'allowFallback' => (bool) $s['allow_fallback'],
		'spentToday'    => bst_aid_spent_today(),
	);
	// The workspace asks with lite=1: no calls to the API (models, balance) on every page load.
	$lite = $request instanceof WP_REST_Request && $request->get_param( 'lite' );
	if ( $has_key && $s['connected'] && ! $lite ) {
		$models          = bst_aid_models( $request instanceof WP_REST_Request && $request->get_param( 'refresh' ) );
		$data['models']  = $models;
		$data['balance'] = bst_aid_balance();
	}
	return rest_ensure_response( $data );
}

/** Balance from Kyma (cached 60 s): array( balance, low ) or null. */
function bst_aid_balance( $refresh = false ) {
	$cached = get_transient( 'bst_aid_balance' );
	if ( ! $refresh && is_array( $cached ) ) {
		return $cached;
	}
	$r = bst_aid_http( 'GET', '/credits/balance' );
	if ( empty( $r['ok'] ) || empty( $r['data']['read_ok'] ) ) {
		return null;
	}
	$out = array( 'balance' => (float) $r['data']['balance'], 'low' => ! empty( $r['data']['low_balance'] ) );
	set_transient( 'bst_aid_balance', $out, MINUTE_IN_SECONDS );
	return $out;
}

function bst_aid_rest_connect( WP_REST_Request $request ) {
	$key = trim( (string) $request->get_param( 'key' ) );
	if ( '' === $key || strlen( $key ) > 300 || preg_match( '/\s/', $key ) ) {
		return new WP_Error( 'bst_aid_key', __( 'Enter a valid API key.', 'bigspy-ai-theme-wp' ), array( 'status' => 400 ) );
	}
	// Check the key before storing it: the balance endpoint needs a valid key.
	$r = bst_aid_http( 'GET', '/credits/balance', null, true, $key );
	if ( empty( $r['ok'] ) ) {
		return new WP_Error( 'bst_aid_key', $r['error'] ? $r['error'] : __( 'The API key was not accepted.', 'bigspy-ai-theme-wp' ), array( 'status' => 400 ) );
	}
	update_option( 'bst_aid_key', bst_aid_encrypt( $key ), false );
	bst_aid_save_settings( array( 'connected' => true ) );
	delete_transient( 'bst_aid_balance' );
	bst_aid_models( true );
	return bst_aid_rest_settings();
}

function bst_aid_rest_disconnect() {
	bst_aid_save_settings( array( 'connected' => false ) );
	return bst_aid_rest_settings();
}

function bst_aid_rest_reconnect() {
	if ( '' === bst_aid_key() ) {
		return new WP_Error( 'bst_aid_key', __( 'No API key is stored.', 'bigspy-ai-theme-wp' ), array( 'status' => 400 ) );
	}
	bst_aid_save_settings( array( 'connected' => true ) );
	return bst_aid_rest_settings();
}

function bst_aid_rest_key_delete() {
	delete_option( 'bst_aid_key' );
	delete_transient( 'bst_aid_balance' );
	bst_aid_save_settings( array( 'connected' => false ) );
	return bst_aid_rest_settings();
}

function bst_aid_rest_settings_save( WP_REST_Request $request ) {
	$models  = bst_aid_models();
	$text    = (string) $request->get_param( 'textModel' );
	$image   = (string) $request->get_param( 'imageModel' );
	$changes = array();
	if ( '' === $text || in_array( $text, wp_list_pluck( $models['text'], 'id' ), true ) ) {
		$changes['text_model'] = $text;
	}
	if ( '' === $image || in_array( $image, wp_list_pluck( $models['image'], 'id' ), true ) ) {
		$changes['image_model'] = $image;
	}
	foreach ( array( 'budgetTask' => 'budget_task', 'budgetDay' => 'budget_day' ) as $param => $key ) {
		if ( null !== $request->get_param( $param ) ) {
			$changes[ $key ] = max( 0, min( 1000, round( (float) $request->get_param( $param ), 2 ) ) );
		}
	}
	if ( null !== $request->get_param( 'allowFallback' ) ) {
		$changes['allow_fallback'] = (bool) $request->get_param( 'allowFallback' );
	}
	bst_aid_save_settings( $changes );
	return bst_aid_rest_settings();
}

/** Remove everything the AI Designer stored: key, settings, guidelines, tasks. */
function bst_aid_rest_data_delete() {
	foreach ( array( 'bst_aid_key', 'bst_aid_settings', 'bst_aid_guidelines', 'bst_aid_spend' ) as $opt ) {
		delete_option( $opt );
	}
	delete_transient( 'bst_aid_models' );
	delete_transient( 'bst_aid_balance' );
	$ids = get_posts( array( 'post_type' => 'bst_aid_task', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) );
	foreach ( $ids as $id ) {
		wp_delete_post( $id, true ); // Tasks are the tool's own data, not site content.
	}
	return bst_aid_rest_settings();
}

/* ------------------------------------------------------------------ Spending */

/** Add a cost (USD) to today's total. */
function bst_aid_add_spend( $usd ) {
	$spend = get_option( 'bst_aid_spend', array() );
	$day   = gmdate( 'Y-m-d' );
	$spend = is_array( $spend ) && ( $spend['day'] ?? '' ) === $day ? $spend : array( 'day' => $day, 'usd' => 0 );
	$spend['usd'] += (float) $usd;
	update_option( 'bst_aid_spend', $spend, false );
}

function bst_aid_spent_today() {
	$spend = get_option( 'bst_aid_spend', array() );
	return is_array( $spend ) && ( $spend['day'] ?? '' ) === gmdate( 'Y-m-d' ) ? round( (float) $spend['usd'], 4 ) : 0.0;
}

/* ------------------------------------------------------------------ Page */

function bst_aid_settings_page() {
	echo '<div class="wrap bst-aid"><h1>' . esc_html__( 'BIGSPY AI Designer – Settings', 'bigspy-ai-theme-wp' ) . '</h1><div id="bst-aid-settings" class="bst-aid-settings"><p>' . esc_html__( 'Loading…', 'bigspy-ai-theme-wp' ) . '</p></div></div>';
}
