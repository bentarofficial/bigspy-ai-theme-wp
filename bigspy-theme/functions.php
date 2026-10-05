<?php
/**
 * BIGSPY Theme bootstrap.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

define( 'BST_VERSION', '1.2.5' );
define( 'BST_DIR', get_template_directory() );
define( 'BST_URI', get_template_directory_uri() );

$bst_includes = array(
	'inc/helpers.php',
	'inc/setup.php',
	'inc/fonts.php',
	'inc/dynamic-css.php',
	'inc/assets.php',
	'inc/template-tags.php',
	'inc/header-builder.php',
	'inc/header-extras.php',
	'inc/footer.php',
	'inc/meta-boxes.php',
	'inc/customizer/customizer.php',
	'inc/blocks.php',
	'inc/patterns.php',
	'inc/compat-flatsome.php',
	'inc/compat-flatsome-elements.php',
	'inc/compat-flatsome-settings.php',
	'inc/admin/dashboard.php',
);

foreach ( $bst_includes as $bst_file ) {
	require_once BST_DIR . '/' . $bst_file;
}

if ( class_exists( 'WooCommerce' ) ) {
	require_once BST_DIR . '/inc/woocommerce.php';
}
