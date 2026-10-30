<?php
/**
 * Related links in posts: a short list after a top-level paragraph of a single post.
 * Content without enough top-level paragraphs, such as one wrapped in a group, is left alone.
 * The list uses the same query as Related posts; its title is not a heading, so the table of contents ignores it.
 */
defined( 'ABSPATH' ) || exit;

/** Byte offsets right after each closing p tag that is not nested in another block. */
function bst_ir_paragraph_ends( $content ) {
	$ends  = array();
	$depth = 0;
	preg_match_all( '#<(/?)(p|div|blockquote|table|ul|ol|dl|figure|section|article|aside|details|pre)\b[^>]*>#i', $content, $tags, PREG_SET_ORDER | PREG_OFFSET_CAPTURE );
	foreach ( $tags as $tag ) {
		$closing = '/' === $tag[1][0];
		if ( 'p' === strtolower( $tag[2][0] ) ) {
			if ( $closing && 0 === $depth ) {
				$ends[] = $tag[0][1] + strlen( $tag[0][0] );
			}
			continue;
		}
		$depth = max( 0, $depth + ( $closing ? -1 : 1 ) );
	}
	return $ends;
}

function bst_ir_html() {
	$args                   = bst_related_query_args();
	$args['posts_per_page'] = (int) bst_feature_get( 'inline-related', 'count', 3 );
	$posts                  = get_posts( $args + array( 'suppress_filters' => false ) );
	if ( ! $posts ) {
		return '';
	}
	$title = bst_feature_get( 'inline-related', 'title' ) ?: __( 'Read also', 'bigspy-ai-theme-wp' );
	$items = '';
	foreach ( $posts as $post ) {
		$items .= '<li><a href="' . esc_url( get_permalink( $post ) ) . '">' . esc_html( get_the_title( $post ) ) . '</a></li>';
	}
	return '<aside class="bst-inline-related"><p class="bst-inline-related__title">' . esc_html( $title ) . '</p><ul>' . $items . '</ul></aside>';
}

function bst_ir_insert( $content ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() || is_feed() ) {
		return $content;
	}
	$ends = bst_ir_paragraph_ends( $content );
	$all  = count( $ends );
	if ( $all < 2 || $all < (int) bst_feature_get( 'inline-related', 'min', 6 ) ) {
		return $content;
	}
	$after = 'after' === bst_feature_get( 'inline-related', 'position', 'middle' ) ? (int) bst_feature_get( 'inline-related', 'paragraph', 3 ) : (int) ceil( $all / 2 );
	$html  = bst_ir_html();
	if ( ! $html ) {
		return $content;
	}
	return substr_replace( $content, $html, $ends[ min( $after, $all - 1 ) - 1 ], 0 );
}
add_filter( 'the_content', 'bst_ir_insert', 30 );

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( is_singular( 'post' ) ) {
			wp_add_inline_style( 'bst-main', '.bst-inline-related{margin:1.5em 0;padding:.75em 1em;border-left:3px solid currentColor}.bst-inline-related__title{margin:0 0 .35em;font-weight:700}.bst-inline-related ul{margin:0;padding-left:1.1em}' );
		}
	},
	30
);

add_filter(
	'bst_feature_fields',
	function ( $fields ) {
		$fields['inline-related'] = array(
			'title'     => array( 'type' => 'text', 'label' => __( 'Title (empty = “Read also”)', 'bigspy-ai-theme-wp' ) ),
			'count'     => array( 'type' => 'number', 'label' => __( 'Number of links', 'bigspy-ai-theme-wp' ), 'default' => 3, 'min' => 1, 'max' => 6 ),
			'position'  => array(
				'type'    => 'select',
				'label'   => __( 'Position', 'bigspy-ai-theme-wp' ),
				'default' => 'middle',
				'choices' => array( 'middle' => __( 'Middle of the post', 'bigspy-ai-theme-wp' ), 'after' => __( 'After a paragraph', 'bigspy-ai-theme-wp' ) ),
			),
			'paragraph' => array( 'type' => 'number', 'label' => __( 'Paragraph number', 'bigspy-ai-theme-wp' ), 'default' => 3, 'min' => 1, 'max' => 50 ),
			'min'       => array( 'type' => 'number', 'label' => __( 'Skip posts with fewer paragraphs than', 'bigspy-ai-theme-wp' ), 'default' => 6, 'min' => 2, 'max' => 50 ),
		);
		return $fields;
	}
);
