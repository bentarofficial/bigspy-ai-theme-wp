<?php
/**
 * Header and footer code: prints saved snippets on front pages exactly as written.
 * Saving needs the unfiltered_html capability, checked in bst_feature_clean().
 */
defined( 'ABSPATH' ) || exit;

function bst_code_print( $key ) {
	echo bst_feature_get( 'code-inject', $key ); // phpcs:ignore WordPress.Security.EscapeOutput -- trusted admin code by design.
}

add_action(
	'wp_head',
	function () {
		bst_code_print( 'head' );
	},
	99
);
add_action(
	'wp_body_open',
	function () {
		bst_code_print( 'body' );
	}
);
add_action(
	'wp_footer',
	function () {
		bst_code_print( 'footer' );
	},
	99
);

add_filter(
	'bst_feature_fields',
	function ( $fields ) {
		$fields['code-inject'] = array(
			'head'   => array( 'type' => 'code', 'label' => __( 'Before </head>', 'bigspy-ai-theme-wp' ), 'help' => __( 'Only add code from a source you trust; it runs on every page.', 'bigspy-ai-theme-wp' ) ),
			'body'   => array( 'type' => 'code', 'label' => __( 'After <body>', 'bigspy-ai-theme-wp' ) ),
			'footer' => array( 'type' => 'code', 'label' => __( 'Before </body>', 'bigspy-ai-theme-wp' ) ),
		);
		return $fields;
	}
);
