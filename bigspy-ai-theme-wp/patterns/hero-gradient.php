<?php
/**
 * Title: Hero – brand gradient
 * Slug: bigspy-ai-theme-wp/hero-gradient
 * Categories: bigspy-sections
 * Keywords: hero, gradient, banner, color
 * Description: Full-width brand gradient hero with headline, two buttons and a checklist.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Translated, JSON-safe text for block attributes (English source, translations in /languages). */
$bst_t = static function ( $text ) {
	return substr( wp_json_encode( __( $text, 'bigspy-ai-theme-wp' ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ), 1, -1 ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
};
?>
<!-- wp:bst/section {"paddingDesktop": 90, "paddingTablet": 64, "paddingMobile": 48, "bgPreset": "gradient", "minHeight": 560, "contentWidth": "boxed"} -->
<!-- wp:bst/row {"gap": "large", "verticalAlign": "center"} -->
<!-- wp:bst/column {"spanDesktop": 7, "spanTablet": 12, "spanMobile": 12} -->
<!-- wp:bst/text {"html": "\u003cp\u003e\u003cstrong\u003e<?php echo $bst_t( 'NEW · THIS MONTH’S OFFER' ); ?>\u003c/strong\u003e\u003c/p\u003e", "size": 13} /-->
<!-- wp:bst/heading {"text": "<?php echo $bst_t( 'Beautiful websites that sell' ); ?>", "tag": "h1", "balance": true, "size": 52, "sizeTablet": 40, "sizeMobile": 32} /-->
<!-- wp:bst/text {"html": "\u003cp\u003e<?php echo $bst_t( 'Sum up what makes you stand out in one or two sentences.' ); ?>\u003c/p\u003e", "size": 18} /-->
<!-- wp:bst/buttons {"align": "left", "gap": 12, "stackMobile": true} -->
<!-- wp:bst/button {"text": "<?php echo $bst_t( 'Buy now' ); ?>", "url": "#", "style": "primary", "scheme": "white", "size": "lg", "hoverEffect": "lift", "icon": "arrow-right"} /-->
<!-- wp:bst/button {"text": "<?php echo $bst_t( 'Free consultation' ); ?>", "url": "#", "style": "glass", "size": "lg"} /-->
<!-- /wp:bst/buttons -->
<!-- wp:bst/icon-list {"layout": "inline", "iconColor": "#ffffff", "items": [{"icon": "check", "text": "<?php echo $bst_t( '2-hour delivery' ); ?>", "link": ""}, {"icon": "check", "text": "<?php echo $bst_t( '7-day returns' ); ?>", "link": ""}, {"icon": "check", "text": "<?php echo $bst_t( '24/7 support' ); ?>", "link": ""}]} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop": 5, "spanTablet": 12, "spanMobile": 12} -->
<!-- wp:bst/image {"imageUrl": "", "alt": "", "radius": 24, "shadow": true} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->
