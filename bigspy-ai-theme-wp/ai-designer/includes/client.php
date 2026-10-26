<?php
/**
 * Kyma API client (OpenAI-compatible). The only outside service the AI Designer calls.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

function bst_aid_api_base() {
	return (string) apply_filters( 'bst_aid_api_base', 'https://kymaapi.com/v1' );
}

/**
 * HTTP request to the API.
 *
 * @return array{ok:bool,status:int,data:array,error:string,code:string,headers:array}
 */
function bst_aid_http( $method, $path, $body = null, $auth = true, $key = null, $timeout = 30 ) {
	$args = array(
		'method'  => $method,
		'timeout' => $timeout,
		'headers' => array( 'Accept' => 'application/json' ),
	);
	if ( $auth ) {
		$key = null === $key ? bst_aid_key() : $key;
		if ( '' === $key ) {
			return array( 'ok' => false, 'status' => 0, 'data' => array(), 'error' => __( 'No API key is stored.', 'bigspy-ai-theme-wp' ), 'code' => 'missing_api_key', 'headers' => array() );
		}
		$args['headers']['Authorization'] = 'Bearer ' . $key;
	}
	if ( null !== $body ) {
		$args['headers']['Content-Type'] = 'application/json';
		$args['body']                    = wp_json_encode( $body );
	}
	$res = wp_remote_request( bst_aid_api_base() . $path, $args );
	if ( is_wp_error( $res ) ) {
		return array( 'ok' => false, 'status' => 0, 'data' => array(), 'error' => $res->get_error_message(), 'code' => 'http_error', 'headers' => array() );
	}
	$status = (int) wp_remote_retrieve_response_code( $res );
	$data   = json_decode( (string) wp_remote_retrieve_body( $res ), true );
	$data   = is_array( $data ) ? $data : array();
	$ok     = $status >= 200 && $status < 300;
	$error  = '';
	$code   = '';
	$raw    = '';
	if ( ! $ok ) {
		$code  = (string) ( $data['error']['code'] ?? '' );
		$error = bst_aid_error_message( $status, $code, (string) ( $data['error']['message'] ?? '' ), $data );
		$raw   = bst_aid_redact( mb_substr( (string) wp_remote_retrieve_body( $res ), 0, 1500 ) );
	}
	$headers = array();
	foreach ( array( 'x-kyma-served-model', 'x-kyma-max-tokens-clamped', 'retry-after' ) as $h ) {
		$v = wp_remote_retrieve_header( $res, $h );
		if ( '' !== $v ) {
			$headers[ $h ] = (string) $v;
		}
	}
	return compact( 'ok', 'status', 'data', 'error', 'code', 'headers', 'raw' );
}

/** A clear message for the user from an API error. */
function bst_aid_error_message( $status, $code, $message, $data ) {
	switch ( $code ) {
		case 'invalid_api_key':
		case 'invalid_credentials':
			return __( 'The API key is invalid or was revoked. Create a new key and connect again.', 'bigspy-ai-theme-wp' );
		case 'insufficient_credits':
			return sprintf( /* translators: 1: needed USD, 2: balance USD */ __( 'Not enough API credit (needs about $%1$s, balance $%2$s). Top up your Kyma account.', 'bigspy-ai-theme-wp' ), number_format_i18n( (float) ( $data['need_usd'] ?? 0 ), 2 ), number_format_i18n( (float) ( $data['have_usd'] ?? 0 ), 2 ) );
		case 'model_requires_paid':
			return __( 'This model needs a paid Kyma account. Add credit once or choose a free model.', 'bigspy-ai-theme-wp' );
		case 'rate_limit_exceeded':
			return __( 'Too many requests right now. Wait a moment and try again.', 'bigspy-ai-theme-wp' );
		case 'model_not_found':
			return __( 'The chosen model is no longer available. Pick another model in Settings.', 'bigspy-ai-theme-wp' );
	}
	if ( $status >= 500 ) {
		return __( 'The model service is temporarily unavailable. Try again in a moment.', 'bigspy-ai-theme-wp' );
	}
	return $message ? $message : sprintf( /* translators: %d: HTTP status */ __( 'API error (HTTP %d).', 'bigspy-ai-theme-wp' ), $status );
}

/**
 * One chat completion with tools.
 *
 * @param array $messages OpenAI-format messages.
 * @param array $tools    OpenAI-format tool definitions.
 * @param array $opts     model, effort (low|medium|high|max).
 */
function bst_aid_chat( $messages, $tools, $opts ) {
	$model = (string) $opts['model'];
	$body  = array(
		'model'               => $model,
		'messages'            => $messages,
		'tools'               => $tools,
		'tool_choice'         => 'auto',
		'parallel_tool_calls' => false,
		'max_tokens'          => 16000, // Room for a full page section tree; Kyma lowers it when the balance is low.
	);
	$meta = bst_aid_text_model_meta( $model );
	if ( $meta && $meta['reasoning'] ) {
		$map                     = array( 'low' => 'low', 'medium' => 'medium', 'high' => 'high', 'max' => 'high' );
		$body['reasoning_effort'] = $map[ $opts['effort'] ] ?? 'medium';
	}
	$res = bst_aid_http( 'POST', '/chat/completions', $body, true, null, 150 );
	if ( empty( $res['ok'] ) && 400 === (int) $res['status'] && ! in_array( $res['code'], array( 'insufficient_credits', 'invalid_api_key', 'model_requires_paid', 'model_not_found' ), true ) ) {
		unset( $body['reasoning_effort'], $body['tool_choice'], $body['parallel_tool_calls'] );
		$body['max_tokens'] = 8192;
		$retry              = bst_aid_http( 'POST', '/chat/completions', $body, true, null, 150 );
		$retry['retried']   = 'plain';
		$retry['first_raw'] = $res['raw'] ?? '';
		return $retry;
	}
	return $res;
}

/** Hide strings that look like secrets before text leaves the site. */
function bst_aid_redact( $text ) {
	$text = preg_replace( '/\b(sk|kyma|ks|pk|rk|ghp|xox[bp])[-_][A-Za-z0-9_\-]{12,}\b/', '[secret hidden]', (string) $text );
	return preg_replace( '/((?:password|passwd|mật khẩu|pass)\s*[:=]\s*)\S+/iu', '$1[hidden]', $text );
}
