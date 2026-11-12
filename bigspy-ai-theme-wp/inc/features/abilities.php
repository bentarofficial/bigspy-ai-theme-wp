<?php
/**
 * AI abilities (MCP): registers the AI Designer abilities while this feature is on.
 * The abilities live in ai-designer/includes/abilities.php; WordPress older than 6.9 skips them.
 */
defined( 'ABSPATH' ) || exit;

if ( function_exists( 'wp_register_ability' ) ) {
	add_action( 'wp_abilities_api_categories_init', function () {
		if ( function_exists( 'bst_aid_abilities_register_category' ) ) {
			bst_aid_abilities_register_category();
		}
	} );
	add_action( 'wp_abilities_api_init', function () {
		if ( function_exists( 'bst_aid_abilities_register' ) ) {
			bst_aid_abilities_register();
		}
	} );
}
