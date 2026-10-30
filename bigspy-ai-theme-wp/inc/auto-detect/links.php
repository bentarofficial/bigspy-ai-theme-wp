<?php
/** Broken links in published content: collected first, then checked a few at a time. */
defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_ad_modules',
	function ( $modules ) {
		$modules['links'] = array(
			'label'  => __( 'Broken links', 'bigspy-ai-theme-wp' ),
			'intro'  => __( 'Checks links in published content a few at a time. Sites that block automatic checks are listed for review.', 'bigspy-ai-theme-wp' ),
			'step'   => 'bst_ad_links_step',
			'action' => 'bst_ad_links_action',
		);
		return $modules;
	}
);

/** Absolute http(s) URLs linked from an HTML text, without fragments. */
function bst_ad_links_extract( $html ) {
	$urls = array();
	if ( ! preg_match_all( '#<a\b[^>]*\bhref\s*=\s*(["\'])(.*?)\1#is', $html, $m ) ) {
		return $urls;
	}
	foreach ( $m[2] as $href ) {
		$href = trim( html_entity_decode( $href, ENT_QUOTES, 'UTF-8' ) );
		$href = explode( '#', $href )[0];
		if ( '' === $href || preg_match( '#^(?!https?:)[a-z][a-z0-9+.-]*:#i', $href ) ) {
			continue;
		}
		if ( 0 === strpos( $href, '//' ) ) {
			$href = 'https:' . $href;
		} elseif ( '/' === $href[0] ) {
			$href = home_url( $href );
		}
		if ( preg_match( '#^https?://#i', $href ) ) {
			$urls[ $href ] = 1;
		}
	}
	return array_keys( $urls );
}

/** Returns '', 'error' or 'review' with the reason. */
function bst_ad_links_check( $url ) {
	$args     = array( 'timeout' => 8, 'redirection' => 4, 'user-agent' => 'Mozilla/5.0 (compatible; BIGSPY link check)' );
	$response = wp_safe_remote_head( $url, $args );
	$code     = (int) wp_remote_retrieve_response_code( $response );
	if ( is_wp_error( $response ) || 405 === $code || 403 === $code || 501 === $code ) {
		$response = wp_safe_remote_get( $url, $args + array( 'limit_response_size' => 4096 ) );
		$code     = (int) wp_remote_retrieve_response_code( $response );
	}
	if ( is_wp_error( $response ) ) {
		return array( 'review', $response->get_error_message() );
	}
	if ( 404 === $code || 410 === $code ) {
		return array( 'error', 'HTTP ' . $code );
	}
	return $code >= 400 ? array( 'review', 'HTTP ' . $code ) : array( '', '' );
}

function bst_ad_links_step( $state ) {
	if ( 0 === $state['phase'] ) {
		$types = array_values( array_diff( get_post_types( array( 'public' => true ) ), array( 'attachment' ) ) );
		$ids   = bst_ad_post_ids( $types, $state['cursor'], 50 );
		foreach ( $ids as $id ) {
			foreach ( bst_ad_links_extract( (string) get_post_field( 'post_content', $id ) ) as $url ) {
				$state['data']['urls'][ $url ][] = $id;
			}
		}
		$state['cursor'] += count( $ids );
		if ( count( $ids ) < 50 ) {
			$state['phase']  = 1;
			$state['cursor'] = 0;
			$state['total']  = count( $state['data']['urls'] ?? array() );
		}
		return $state;
	}
	$urls = array_slice( $state['data']['urls'] ?? array(), $state['cursor'], 8, true );
	foreach ( $urls as $url => $ids ) {
		list( $severity, $reason ) = bst_ad_links_check( $url );
		if ( $severity ) {
			$state['items'][] = array( 'key' => 'link-' . md5( $url ), 'severity' => $severity, 'title' => $url, 'detail' => $reason, 'posts' => array_map( 'bst_ad_post_ref', array_slice( $ids, 0, 20 ) ), 'link' => $url, 'ignore' => true );
		}
	}
	$state['cursor'] += count( $urls );
	$state['done']    = $state['cursor'];
	if ( $state['cursor'] >= $state['total'] ) {
		$state['status'] = 'done';
	}
	return $state;
}

/** Swap or unlink an href in post content. Returns the new content. */
function bst_ad_links_edit( $content, $url, $to ) {
	$pattern = '#<a\b[^>]*\bhref\s*=\s*(["\'])(' . preg_quote( $url, '#' ) . '|' . preg_quote( esc_url( $url ), '#' ) . ')(\#[^"\']*)?\1[^>]*>(.*?)</a>#is';
	return preg_replace_callback(
		$pattern,
		function ( $m ) use ( $to ) {
			if ( null === $to ) {
				return $m[4];
			}
			return str_replace( $m[1] . $m[2] . ( $m[3] ?? '' ) . $m[1], $m[1] . esc_url( $to ) . $m[1], $m[0] );
		},
		$content
	);
}

/** Admin-confirmed fix on the posts of a listed link. WordPress keeps a revision of each. */
function bst_ad_links_action( WP_REST_Request $request ) {
	$op    = (string) $request->get_param( 'op' );
	$url   = (string) $request->get_param( 'url' );
	$to    = 'replace' === $op ? esc_url_raw( (string) $request->get_param( 'to' ) ) : null;
	$state = bst_ad_state( 'links' );
	$item  = null;
	foreach ( $state['items'] as $index => $candidate ) {
		if ( $candidate['link'] === $url ) {
			$item = $candidate;
			unset( $state['items'][ $index ] );
		}
	}
	if ( ! $item || ! in_array( $op, array( 'replace', 'remove' ), true ) || ( 'replace' === $op && ! $to ) ) {
		return new WP_Error( 'bst_ad_op', 'Unknown action', array( 'status' => 400 ) );
	}
	foreach ( $item['posts'] as $post ) {
		if ( current_user_can( 'edit_post', $post['id'] ) ) {
			$content = (string) get_post_field( 'post_content', $post['id'] );
			$updated = bst_ad_links_edit( $content, $url, $to );
			if ( $updated !== $content ) {
				wp_update_post( wp_slash( array( 'ID' => $post['id'], 'post_content' => $updated ) ) );
			}
		}
	}
	$state['items'] = array_values( $state['items'] );
	bst_ad_save_state( 'links', $state );
	return true;
}
