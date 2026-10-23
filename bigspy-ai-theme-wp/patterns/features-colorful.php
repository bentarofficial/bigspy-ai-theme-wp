<?php
/**
 * Title: Features – colorful cards
 * Slug: bigspy-ai-theme-wp/features-colorful
 * Categories: bigspy-sections
 * Keywords: features, services, icons, color
 * Description: Soft-gradient section with four colored icon cards.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Translated, JSON-safe text for block attributes (English source, translations in /languages). */
$bst_t = static function ( $text ) {
	return substr( wp_json_encode( __( $text, 'bigspy-ai-theme-wp' ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ), 1, -1 ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
};
?>
<!-- wp:bst/section {"paddingDesktop": 90, "paddingTablet": 64, "paddingMobile": 48, "bgPreset": "gradient-soft"} -->
<!-- wp:bst/title {"text": "<?php echo $bst_t( 'Why customers choose us' ); ?>", "subtitle": "<?php echo $bst_t( 'WHY US' ); ?>"} /-->
<!-- wp:bst/row {"gap": "medium"} -->
<!-- wp:bst/column {"spanDesktop": 3, "spanTablet": 6, "spanMobile": 12} -->
<!-- wp:bst/icon-box {"icon": "truck", "title": "<?php echo $bst_t( 'Fast delivery' ); ?>", "text": "<?php echo $bst_t( 'Same-day delivery in the city.' ); ?>", "iconStyle": "circle", "iconColor": "#ffffff", "iconBg": "#0ea5e9", "boxStyle": "card", "bsDesign": {"br": "16px", "depthHover": 3, "hmove": 6}} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop": 3, "spanTablet": 6, "spanMobile": 12} -->
<!-- wp:bst/icon-box {"icon": "shield", "title": "<?php echo $bst_t( '100% genuine' ); ?>", "text": "<?php echo $bst_t( 'Money back if not as described.' ); ?>", "iconStyle": "circle", "iconColor": "#ffffff", "iconBg": "#16a34a", "boxStyle": "card", "bsDesign": {"br": "16px", "depthHover": 3, "hmove": 6}} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop": 3, "spanTablet": 6, "spanMobile": 12} -->
<!-- wp:bst/icon-box {"icon": "gift", "title": "<?php echo $bst_t( 'Great gifts' ); ?>", "text": "<?php echo $bst_t( 'Special offers for loyal customers.' ); ?>", "iconStyle": "circle", "iconColor": "#ffffff", "iconBg": "#f97316", "boxStyle": "card", "bsDesign": {"br": "16px", "depthHover": 3, "hmove": 6}} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop": 3, "spanTablet": 6, "spanMobile": 12} -->
<!-- wp:bst/icon-box {"icon": "support", "title": "<?php echo $bst_t( 'Caring support' ); ?>", "text": "<?php echo $bst_t( 'Advice by chat and hotline 24/7.' ); ?>", "iconStyle": "circle", "iconColor": "#ffffff", "iconBg": "#db2777", "boxStyle": "card", "bsDesign": {"br": "16px", "depthHover": 3, "hmove": 6}} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->
