<?php
/**
 * Legacy BIGSPY AI List layout blocks (bigspy/section, row, column, heading,
 * text, button, button-group, tabs, tab, code, callout, badge, divider,
 * home-seo-divider), carried over from the old theme so content saved with it
 * still renders. They are hidden from the inserter and offer no settings in the
 * editor; new content should use the bst/* blocks.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

function bst_legacy_list_block_names() {
	return array( 'section', 'row', 'column', 'heading', 'text', 'button', 'button-group', 'tabs', 'tab', 'code', 'callout', 'badge', 'divider', 'home-seo-divider' );
}

function bst_register_legacy_list_blocks() {
	wp_register_style( 'bst-legacy-list', BST_URI . '/legacy-list/legacy-list.css', array(), bst_asset_version( 'legacy-list/legacy-list.css' ) );
	wp_register_script(
		'bst-legacy-list-editor',
		BST_URI . '/legacy-list/editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-server-side-render' ),
		bst_asset_version( 'legacy-list/editor.js' ),
		true
	);
	$registry = WP_Block_Type_Registry::get_instance();
	foreach ( bst_legacy_list_block_names() as $block ) {
		if ( $registry->is_registered( 'bigspy/' . $block ) ) {
			continue; // Something else (e.g. the old plugin) already owns it.
		}
		register_block_type( BST_DIR . '/legacy-list/blocks/' . $block );
	}
}
add_action( 'init', 'bst_register_legacy_list_blocks', 21 );
