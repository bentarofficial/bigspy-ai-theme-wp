<?php
/**
 * UX Blocks post type: reusable content for the footer, AI Designer and Flatsome sites.
 * Always loaded; the Flatsome compatibility feature only adds the old shortcodes and styles.
 *
 * @package BigSpyAITheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Keep the "blocks" post type (UX Blocks) so reusable blocks such as the footer stay editable
 * and keep their URLs.
 */
function bst_legacy_register_block_post_type() {
	if ( post_type_exists( 'blocks' ) ) {
		return;
	}
	register_post_type(
		'blocks',
		array(
			'labels'              => array(
				'name'          => __( 'UX Blocks', 'bigspy-ai-theme-wp' ),
				'singular_name' => __( 'UX Block', 'bigspy-ai-theme-wp' ),
				'add_new_item'  => __( 'Add new block', 'bigspy-ai-theme-wp' ),
				'edit_item'     => __( 'Edit block', 'bigspy-ai-theme-wp' ),
			),
			'public'              => true,
			'exclude_from_search' => true,
			'show_in_nav_menus'   => false,
			'show_in_rest'        => true,
			'rest_base'           => 'ux-blocks',
			'menu_icon'           => 'dashicons-screenoptions',
			'supports'            => array( 'title', 'editor', 'revisions' ),
			'rewrite'             => array( 'slug' => 'blocks' ),
		)
	);
}
add_action( 'init', 'bst_legacy_register_block_post_type', 5 );
add_action( 'after_switch_theme', 'flush_rewrite_rules' );
