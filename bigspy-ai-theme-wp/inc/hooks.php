<?php
/**
 * Developer hooks: generic filters for every BIGSPY block (bst/*).
 *
 * Region actions (bst_before_header, bst_after_footer, …) are fired in the
 * templates themselves; see docs/HOOKS.md for the full list.
 * With nothing hooked, output is unchanged.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Output of an action as a string ('' when nothing is hooked).
 *
 * @param string $hook Action name.
 * @param mixed  ...$args Action arguments.
 */
function bst_capture_action( $hook, ...$args ) {
	if ( ! has_action( $hook ) ) {
		return '';
	}
	ob_start();
	do_action( $hook, ...$args );
	return (string) ob_get_clean();
}

/** Whether a parsed block is one of ours. */
function bst_is_theme_block( $block ) {
	return 0 === strpos( (string) ( $block['blockName'] ?? '' ), 'bst/' );
}

/**
 * bst_block_attributes: change a block's attributes before it renders.
 *
 * @param array $block Parsed block.
 */
function bst_filter_block_data( $block ) {
	if ( ! bst_is_theme_block( $block ) || ! has_filter( 'bst_block_attributes' ) ) {
		return $block;
	}
	$attrs = apply_filters( 'bst_block_attributes', (array) ( $block['attrs'] ?? array() ), $block['blockName'], $block );
	if ( is_array( $attrs ) ) {
		$block['attrs'] = $attrs;
	}
	return $block;
}
add_filter( 'render_block_data', 'bst_filter_block_data', 10, 1 );

/**
 * bst_block_classes (extra classes on the outer tag) and bst_block_html (final markup).
 *
 * @param string $content Rendered HTML.
 * @param array  $block   Parsed block.
 */
function bst_filter_block_output( $content, $block ) {
	if ( ! bst_is_theme_block( $block ) ) {
		return $content;
	}
	$name  = $block['blockName'];
	$attrs = (array) ( $block['attrs'] ?? array() );

	if ( has_filter( 'bst_block_classes' ) && class_exists( 'WP_HTML_Tag_Processor' ) ) {
		$classes = array_filter( array_map( 'sanitize_html_class', (array) apply_filters( 'bst_block_classes', array(), $name, $attrs, $block ) ) );
		if ( $classes ) {
			$tags = new WP_HTML_Tag_Processor( $content );
			if ( $tags->next_tag() ) {
				foreach ( $classes as $class ) {
					$tags->add_class( $class );
				}
				$content = $tags->get_updated_html();
			}
		}
	}
	if ( has_filter( 'bst_block_html' ) ) {
		$content = (string) apply_filters( 'bst_block_html', $content, $name, $attrs, $block );
	}
	return $content;
}
add_filter( 'render_block', 'bst_filter_block_output', 20, 2 );
