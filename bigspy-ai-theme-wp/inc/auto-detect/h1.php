<?php
/** H1 tags counted on the rendered page, so theme and block output are included. */
defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_ad_modules',
	function ( $modules ) {
		$modules['h1'] = array(
			'label' => __( 'H1 tags', 'bigspy-ai-theme-wp' ),
			'intro' => __( 'Opens each published page, post and product as a visitor and counts its H1 tags.', 'bigspy-ai-theme-wp' ),
			'step'  => 'bst_ad_h1_step',
		);
		return $modules;
	}
);

/** Visible H1 texts of an HTML document. */
function bst_ad_h1_texts( $html ) {
	$html = preg_replace( '#<(script|style|template|noscript)\b.*?</\1>|<!--.*?-->#is', '', $html );
	preg_match_all( '#<h1\b[^>]*>(.*?)</h1>#is', $html, $found );
	return array_map(
		function ( $text ) {
			return trim( preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( $text ), ENT_QUOTES, 'UTF-8' ) ) );
		},
		$found[1]
	);
}

function bst_ad_h1_step( $state ) {
	$types = array_values( array_intersect( array( 'page', 'post', 'product' ), get_post_types() ) );
	if ( ! $state['total'] ) {
		foreach ( $types as $type ) {
			$state['total'] += (int) wp_count_posts( $type )->publish;
		}
	}
	$ids = bst_ad_post_ids( $types, $state['cursor'], 5 );
	foreach ( $ids as $id ) {
		$response = wp_remote_get( get_permalink( $id ), array( 'timeout' => 15, 'sslverify' => false, 'redirection' => 3 ) );
		$code     = (int) wp_remote_retrieve_response_code( $response );
		if ( is_wp_error( $response ) || 200 !== $code ) {
			$state['items'][] = array(
				'key'      => 'h1-fetch-' . $id,
				'severity' => 'review',
				'title'    => __( 'Page could not be opened', 'bigspy-ai-theme-wp' ),
				'detail'   => is_wp_error( $response ) ? $response->get_error_message() : 'HTTP ' . $code,
				'posts'    => array( bst_ad_post_ref( $id ) ),
			);
			continue;
		}
		$state['data'][ $id ] = bst_ad_h1_texts( wp_remote_retrieve_body( $response ) );
	}
	$state['cursor'] += count( $ids );
	$state['done']    = $state['cursor'];
	if ( count( $ids ) < 5 ) {
		$state['items']  = array_merge( $state['items'], bst_ad_h1_items( $state['data'] ) );
		$state['status'] = 'done';
	}
	return $state;
}

function bst_ad_h1_items( $pages ) {
	$items = array();
	$same  = array();
	foreach ( $pages as $id => $texts ) {
		$count = count( $texts );
		if ( 0 === $count ) {
			$items[] = array( 'key' => 'h1-none-' . $id, 'severity' => 'error', 'title' => __( 'No H1 tag', 'bigspy-ai-theme-wp' ), 'posts' => array( bst_ad_post_ref( $id ) ) );
		} elseif ( $count > 1 ) {
			/* translators: %d: number of H1 tags. */
			$items[] = array( 'key' => 'h1-many-' . $id, 'severity' => 'review', 'title' => sprintf( __( '%d H1 tags', 'bigspy-ai-theme-wp' ), $count ), 'detail' => implode( ' | ', $texts ), 'posts' => array( bst_ad_post_ref( $id ) ) );
		}
		foreach ( array_unique( $texts ) as $text ) {
			if ( '' !== $text ) {
				$same[ mb_strtolower( $text ) ][] = $id;
			}
		}
	}
	foreach ( $same as $text => $ids ) {
		if ( count( $ids ) > 1 ) {
			$items[] = array( 'key' => 'h1-same-' . md5( $text ), 'severity' => 'review', 'title' => __( 'Same H1 on several pages', 'bigspy-ai-theme-wp' ), 'detail' => $text, 'posts' => array_map( 'bst_ad_post_ref', $ids ) );
		}
	}
	return $items;
}
