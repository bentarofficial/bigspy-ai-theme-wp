<?php
/**
 * Front-end and editor assets.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

function bst_asset_version( $relative ) {
	$file = BST_DIR . '/' . $relative;
	return file_exists( $file ) ? BST_VERSION . '.' . filemtime( $file ) : BST_VERSION;
}

/**
 * Shared by front end and block editor: fonts, CSS variables and block styles.
 */
function bst_block_assets() {
	$fonts = bst_google_fonts_url();
	if ( $fonts ) {
		wp_enqueue_style( 'bst-fonts', $fonts, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	}
	wp_enqueue_style( 'bst-blocks', BST_URI . '/assets/css/blocks.css', array(), bst_asset_version( 'assets/css/blocks.css' ) );
	wp_add_inline_style( 'bst-blocks', bst_dynamic_css() );
}
add_action( 'enqueue_block_assets', 'bst_block_assets' );

function bst_front_assets() {
	wp_enqueue_style( 'bst-main', BST_URI . '/assets/css/main.css', array( 'bst-blocks' ), bst_asset_version( 'assets/css/main.css' ) );

	wp_enqueue_script( 'bst-main', BST_URI . '/assets/js/main.js', array(), bst_asset_version( 'assets/js/main.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_enqueue_script( 'bst-blocks', BST_URI . '/assets/js/blocks.js', array(), bst_asset_version( 'assets/js/blocks.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'bst_front_assets' );

/**
 * Editor-only UI helpers shared by every theme block.
 */
function bst_register_editor_helpers() {
	wp_register_script(
		'bst-editor-helpers',
		BST_URI . '/assets/js/editor-helpers.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-hooks', 'wp-compose', 'wp-data' ),
		bst_asset_version( 'assets/js/editor-helpers.js' ),
		true
	);
	wp_localize_script(
		'bst-editor-helpers',
		'bstEditor',
		array(
			'icons'    => array_intersect_key( bst_icon_paths(), array_flip( array_merge( bst_block_icon_names(), array( 'facebook', 'instagram', 'youtube', 'tiktok', 'zalo', 'x', 'linkedin', 'pinterest', 'messenger', 'whatsapp', 'telegram', 'viber', 'close', 'chevron-down', 'arrow-up' ) ) ) ),
			'hasWoo'   => class_exists( 'WooCommerce' ),
			'disabledBlocks' => function_exists( 'bst_disabled_blocks' ) ? bst_disabled_blocks() : array(),
		)
	);
}
add_action( 'init', 'bst_register_editor_helpers', 5 );

function bst_editor_assets() {
	// Responsive visibility + animation controls for every block.
	wp_enqueue_script( 'bst-editor-helpers' );
	// Type or paste a color / gradient code under every picker.
	// Let Rank Math / Yoast read the text inside BIGSPY blocks.
	wp_enqueue_script( 'bst-seo-content', BST_URI . '/assets/js/seo-content.js', array( 'wp-hooks', 'wp-data' ), bst_asset_version( 'assets/js/seo-content.js' ), true );
	wp_enqueue_script( 'bst-color-input', BST_URI . '/assets/js/color-input.js', array( 'wp-i18n' ), bst_asset_version( 'assets/js/color-input.js' ), true );
	wp_enqueue_style( 'bst-editor-ui', BST_URI . '/assets/css/editor-ui.css', array(), bst_asset_version( 'assets/css/editor-ui.css' ) );
	wp_add_inline_style( 'bst-editor-ui', '.bst-btn-styles,.bst-bg-presets,.bst-form-templates{--bst-primary:' . sanitize_hex_color( (string) bst_mod( 'color_primary' ) ) . ';--bst-secondary:' . sanitize_hex_color( (string) bst_mod( 'color_secondary' ) ) . '}' );
}
add_action( 'enqueue_block_editor_assets', 'bst_editor_assets' );
