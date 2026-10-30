<?php
/** Duplicate focus keywords across published content (Rank Math first keyword, or Yoast). */
defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_ad_modules',
	function ( $modules ) {
		$modules['keywords'] = array(
			'label' => __( 'Duplicate focus keywords', 'bigspy-ai-theme-wp' ),
			'intro' => __( 'Compares the focus keyword of published content. Secondary keywords are not compared.', 'bigspy-ai-theme-wp' ),
			'step'  => 'bst_ad_keywords_step',
		);
		return $modules;
	}
);

/** The focus keyword only: Rank Math keeps a comma list whose first entry is the main one. */
function bst_ad_focus_keyword( $post_id ) {
	$rank = (string) get_post_meta( $post_id, 'rank_math_focus_keyword', true );
	if ( '' !== trim( $rank ) ) {
		return trim( explode( ',', $rank )[0] );
	}
	return trim( (string) get_post_meta( $post_id, '_yoast_wpseo_focuskw', true ) );
}

/** Same keyword apart from case, accents and spacing. */
function bst_ad_fold_keyword( $keyword ) {
	$keyword = str_replace( array( 'đ', 'Đ' ), 'd', $keyword );
	return trim( preg_replace( '/\s+/u', ' ', strtolower( remove_accents( $keyword ) ) ) );
}

function bst_ad_keywords_step( $state ) {
	$types = array_values( array_diff( get_post_types( array( 'public' => true ) ), array( 'attachment' ) ) );
	$ids   = bst_ad_post_ids( $types, $state['cursor'], 200 );
	foreach ( $ids as $id ) {
		$keyword = bst_ad_focus_keyword( $id );
		if ( '' !== $keyword ) {
			$state['data'][ $id ] = $keyword;
		}
	}
	$state['cursor'] += count( $ids );
	$state['done']    = $state['cursor'];
	if ( count( $ids ) < 200 ) {
		$state['items']  = bst_ad_keyword_pairs( $state['data'] );
		$state['status'] = 'done';
	}
	return $state;
}

function bst_ad_keyword_pairs( $keywords ) {
	$groups = array();
	foreach ( $keywords as $id => $keyword ) {
		$groups[ bst_ad_fold_keyword( $keyword ) ][ $id ] = $keyword;
	}
	$items = array();
	foreach ( $groups as $group ) {
		$ids   = array_keys( $group );
		$count = count( $ids );
		for ( $i = 0; $i < $count; $i++ ) {
			for ( $j = $i + 1; $j < $count; $j++ ) {
				$a       = $group[ $ids[ $i ] ];
				$b       = $group[ $ids[ $j ] ];
				$items[] = array(
					'key'      => 'kw-' . min( $ids[ $i ], $ids[ $j ] ) . '-' . max( $ids[ $i ], $ids[ $j ] ),
					'severity' => $a === $b ? 'error' : 'review',
					'title'    => $a === $b ? $a : $a . ' / ' . $b,
					'posts'    => array( bst_ad_post_ref( $ids[ $i ] ), bst_ad_post_ref( $ids[ $j ] ) ),
					'ignore'   => true,
				);
			}
		}
	}
	usort(
		$items,
		function ( $x, $y ) {
			return strcmp( $x['severity'], $y['severity'] );
		}
	);
	return $items;
}
