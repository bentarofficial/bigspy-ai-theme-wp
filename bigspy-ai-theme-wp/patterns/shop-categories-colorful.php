<?php
/**
 * Title: Shop – colorful categories
 * Slug: bigspy-ai-theme-wp/shop-categories-colorful
 * Categories: bigspy-sections
 * Keywords: shop, woocommerce, categories, color
 * Description: Mint background with colored category tiles and best sellers.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Translated, JSON-safe text for block attributes (English source, translations in /languages). */
$bst_t = static function ( $text ) {
	return substr( wp_json_encode( __( $text, 'bigspy-ai-theme-wp' ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ), 1, -1 ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
};
?>
<!-- wp:bst/section {"paddingDesktop": 90, "paddingTablet": 64, "paddingMobile": 48, "bgPreset": "mint"} -->
<!-- wp:bst/title {"text": "<?php echo $bst_t( 'Featured categories' ); ?>", "subtitle": "<?php echo $bst_t( 'SHOP' ); ?>"} /-->
<!-- wp:bst/product-categories {"style": "tile", "count": 6, "columns": 6, "columnsTablet": 3, "columnsMobile": 2, "showCount": true, "hover": "lift"} /-->
<!-- wp:bst/spacer {"heightDesktop": 40, "heightTablet": 30, "heightMobile": 24} /-->
<!-- wp:bst/title {"text": "<?php echo $bst_t( 'Best sellers' ); ?>", "linkText": "<?php echo $bst_t( 'View all' ); ?>", "linkUrl": "/shop/"} /-->
<!-- wp:bst/products {"type": "best_selling", "count": 8, "columns": 4, "cardStyle": "colorful", "titleLines": 2} /-->
<!-- /wp:bst/section -->
