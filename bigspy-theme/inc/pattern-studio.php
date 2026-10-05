<?php
/**
 * BIGSPY's searchable pattern library inside the block editor.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Return only BIGSPY-owned patterns that belong to one of our design groups. */
function bst_pattern_studio_catalog() {
	if ( ! class_exists( 'WP_Block_Patterns_Registry' ) ) {
		return array();
	}

	$allowed_categories = array( 'bigspy-pages', 'bigspy-sections', 'bigspy-shop' );
	$catalog            = array();
	$registry           = WP_Block_Patterns_Registry::get_instance();

	foreach ( $registry->get_all_registered() as $pattern ) {
		$name = isset( $pattern['name'] ) ? (string) $pattern['name'] : '';
		if ( 0 !== strpos( $name, 'bigspy-theme/' ) || empty( $pattern['content'] ) ) {
			continue;
		}

		$categories = array_values( array_intersect( $allowed_categories, (array) ( $pattern['categories'] ?? array() ) ) );
		if ( ! $categories ) {
			continue;
		}

		$catalog[] = array(
			'name'        => $name,
			'title'       => wp_strip_all_tags( (string) ( $pattern['title'] ?? $name ) ),
			'description' => wp_strip_all_tags( (string) ( $pattern['description'] ?? '' ) ),
			'categories'  => $categories,
			'content'     => $pattern['content'],
		);
	}

	return $catalog;
}

/** Load the library only in the post editor and expose the registered catalog to that editor. */
function bst_enqueue_pattern_studio() {
	if ( ! current_user_can( 'edit_pages' ) || ! function_exists( 'get_current_screen' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->base, array( 'post', 'post-new' ), true ) ) {
		return;
	}

	$script = 'bst-pattern-studio';
	wp_register_script(
		$script,
		BST_URI . '/assets/js/pattern-studio.js',
		array( 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data', 'wp-core-data', 'wp-blocks', 'wp-i18n' ),
		bst_asset_version( 'assets/js/pattern-studio.js' ),
		true
	);
	wp_enqueue_script( $script );
	wp_enqueue_style( 'bst-pattern-studio', BST_URI . '/assets/css/pattern-studio.css', array(), bst_asset_version( 'assets/css/pattern-studio.css' ) );
	wp_localize_script( $script, 'bstPatternStudioData', array( 'patterns' => bst_pattern_studio_catalog() ) );
}
add_action( 'enqueue_block_editor_assets', 'bst_enqueue_pattern_studio' );
