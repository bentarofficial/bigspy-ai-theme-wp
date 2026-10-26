<?php
/** Sandboxed editor preview uses the same renderer and runtime as the front end. */
defined( 'ABSPATH' ) || exit;
add_action( 'rest_api_init', function () {
	register_rest_route( 'bst-motion/v1', '/preview', array(
		'methods' => 'POST', 'permission_callback' => function () { return current_user_can( 'edit_pages' ); },
		'callback' => function ( WP_REST_Request $r ) {
			$content = (string) $r->get_param( 'content' );
			if ( strlen( $content ) > 200000 ) { return new WP_Error( 'size', 'Preview too large.', array( 'status' => 400 ) ); }
			if ( ! current_user_can( 'unfiltered_html' ) ) { $content = wp_kses_post( $content ); }
			// Only the expected top-level block is accepted in the sandboxed preview.
			$blocks = array_values( array_filter( parse_blocks( $content ), function ( $b ) { return ! empty( $b['blockName'] ); } ) );
			if ( 1 !== count( $blocks ) || 'bigspy/ticker' !== $blocks[0]['blockName'] ) { return new WP_Error( 'block', 'Expected a ticker.', array( 'status' => 400 ) ); }
			$html = render_block( $blocks[0] );
			return rest_ensure_response( array( 'html' => '<!doctype html><html><head><meta name="viewport" content="width=device-width"><link rel="stylesheet" href="' . esc_url( BST_URI . '/assets/css/blocks.css' ) . '"></head><body>' . $html . '<script src="' . esc_url( BST_URI . '/blocks/ticker/view.js' ) . '"></script></body></html>' ) );
		},
	) );
} );
add_action( 'enqueue_block_editor_assets', function () {
	wp_enqueue_style( 'bst-motion-editor', BST_URI . '/assets/css/blocks-motion-editor.css', array(), BST_VERSION );
} );
