<?php
/**
 * Title: Call to action – sunrise
 * Slug: bigspy-ai-theme-wp/cta-sunrise
 * Categories: bigspy-sections
 * Keywords: cta, banner, gradient, wave
 * Description: Sunrise gradient band with wave dividers, headline and pulsing button.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Translated, JSON-safe text for block attributes (English source, translations in /languages). */
$bst_t = static function ( $text ) {
	return substr( wp_json_encode( __( $text, 'bigspy-ai-theme-wp' ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ), 1, -1 ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
};
?>
<!-- wp:bst/section {"paddingDesktop": 90, "paddingTablet": 64, "paddingMobile": 48, "bgPreset": "sunrise", "dividerTop": "wave", "dividerTopHeight": 60, "dividerBottom": "wave", "dividerBottomHeight": 60, "dividerBottomFlip": true} -->
<!-- wp:bst/row {"gap": "large", "verticalAlign": "center"} -->
<!-- wp:bst/column {"spanDesktop": 8, "spanTablet": 12, "spanMobile": 12} -->
<!-- wp:bst/heading {"text": "<?php echo $bst_t( 'Ready to start? Get 20% off today' ); ?>", "tag": "h2", "balance": true, "size": 36, "sizeMobile": 26} /-->
<!-- wp:bst/text {"html": "\u003cp\u003e<?php echo $bst_t( 'Sign up this week for a free consultation.' ); ?>\u003c/p\u003e"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop": 4, "spanTablet": 12, "spanMobile": 12} -->
<!-- wp:bst/buttons {"align": "right", "gap": 12, "stackMobile": true} -->
<!-- wp:bst/button {"text": "<?php echo $bst_t( 'Call now' ); ?>", "url": "#", "subtext": "0944 336 123", "style": "primary", "scheme": "white", "size": "lg", "icon": "phone", "iconPosition": "before", "hoverEffect": "pulse"} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->
