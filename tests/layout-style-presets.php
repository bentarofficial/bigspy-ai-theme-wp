<?php
/** Contract tests for the shared, opt-in catalog. Run with PHP 7.4+. */
define( 'ABSPATH', __DIR__ );
function __( $text, $domain = '' ) { return $text; }
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function get_theme_mod( $key, $default = null ) { return $GLOBALS['test_mods'][ $key ] ?? $default; }
require __DIR__ . '/../bigspy-ai-theme-wp/inc/layout-style-presets.php';
require __DIR__ . '/../bigspy-ai-theme-wp/inc/block-presets.php';
require __DIR__ . '/../bigspy-ai-theme-wp/inc/css-presets.php';
function check( $condition, $message ) {
	if ( ! $condition ) { throw new RuntimeException( $message ); }
}
$shared = bst_layout_style_presets();
$blocks = bst_block_presets();
$global = bst_css_presets();
check( count( $shared ) === 8, 'Eight recipes are available.' );
check( '' === bst_css_presets_css(), 'Global options default off.' );
check( isset( bst_css_preset_groups()['layout-tools'] ), 'Customizer group exists.' );
check( ! in_array( 'layout-tools', bst_css_preset_moved_groups(), true ), 'Group stays visible in Customize.' );
foreach ( $shared as $id => $recipe ) {
	check( isset( $blocks[ $recipe[0] ]['layout-tools'][2][ $id ] ), 'Recipe is available in its matching block.' );
	check( isset( $global[ $id ] ), 'Recipe is available in Customize.' );
	check( $global[ $id ][2] === str_replace( '&', $recipe[2], $recipe[3] ), 'Global CSS matches shared source.' );
	check( strpos( bst_block_preset_rules()[ $id ], '&' ) === false, 'Root placeholder is expanded.' );
	check( strpos( bst_block_preset_rules()[ $id ], '.bsp-' . $id . '.bsp-' . $id ) !== false, 'Local specificity exceeds global root.' );
	check( strpos( $recipe[3], 'plm-' ) === false && strpos( $recipe[3], '1821' ) === false, 'No site-specific selectors.' );
	$GLOBALS['test_mods']['css_presets'] = array( $id );
	check( trim( bst_css_presets_css() ) === $global[ $id ][2], 'Only selected global CSS is emitted.' );
}
$GLOBALS['test_mods']['css_presets'] = array();
check( '' === bst_css_presets_css(), 'Unchecking restores empty global CSS.' );
check( array( 'layout-price-compact' ) === bst_sanitize_css_presets( array( 'layout-price-compact', 'untrusted-css', '<style>', 'layout-price-compact' ) ), 'Unknown and duplicate IDs are removed.' );
check( array() === bst_sanitize_css_presets( 'body{display:none}' ), 'Raw CSS cannot be imported.' );
check( count( bst_block_presets()['bst/banner']['shade'][2] ) >= 3, 'Existing presets are retained.' );
echo "PASS: shared catalog, matching block panels, opt-in global CSS, reset and import allowlist.\n";
