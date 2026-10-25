<?php
/**
 * BIGSPY AI Theme WP bootstrap.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

define( 'BST_VERSION', '1.72.1' );
define( 'BST_DIR', get_template_directory() );
define( 'BST_URI', get_template_directory_uri() );

$bst_includes = array(
	'inc/helpers.php',
	'inc/css-presets.php',
	'inc/setup.php',
	'inc/fonts.php',
	'inc/dynamic-css.php',
	'inc/assets.php',
	'inc/performance.php',
	'inc/template-tags.php',
	'inc/header-builder.php',
	'inc/header-design.php',
	'inc/header-elements.php',
	'inc/header-extras.php',
	'inc/mega-menu.php',
	'inc/footer.php',
	'inc/footer-design.php',
	'inc/floating-contact.php',
	'inc/meta-boxes.php',
	'inc/site-tools.php',
	'inc/portfolio.php',
	'inc/customizer/customizer.php',
	'inc/customizer/groups.php',
	'inc/blocks.php',
	'inc/block-design.php',
	'inc/block-presets.php',
	'inc/sidebar-options.php',
	'inc/blog-options.php',
	'inc/shop-options.php',
	'inc/author-options.php',
	'inc/theme-builder.php',
	'inc/theme-language.php',
	'inc/header-fit.php',
	'inc/site-extras.php',
	'inc/form-native.php',
	'inc/block-shapes.php',
	'inc/block-manager.php',
	'inc/colorful.php',
	'inc/forms.php',
	'inc/announcement.php',
	'inc/countdown.php',
	'inc/patterns.php',
	'inc/pattern-studio.php',

	'inc/compat-flatsome.php',
	'inc/compat-flatsome-elements.php',
	'inc/compat-flatsome-settings.php',
	'inc/admin/dashboard.php',
	'inc/admin/theme-panel.php',
);

foreach ( $bst_includes as $bst_file ) {
	require_once BST_DIR . '/' . $bst_file;
}

if ( class_exists( 'WooCommerce' ) ) {
	require_once BST_DIR . '/inc/woocommerce.php';
}

// BIGSPY AI Designer: optional, self-contained. Delete the ai-designer folder to remove it.
if ( file_exists( BST_DIR . '/ai-designer/ai-designer.php' ) ) {
	require_once BST_DIR . '/ai-designer/ai-designer.php';
}
