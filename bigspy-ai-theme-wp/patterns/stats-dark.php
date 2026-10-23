<?php
/**
 * Title: Stats – dark glow
 * Slug: bigspy-ai-theme-wp/stats-dark
 * Categories: bigspy-sections
 * Keywords: stats, counter, numbers, dark
 * Description: Dark glowing section with four counters and a star rating.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Translated, JSON-safe text for block attributes (English source, translations in /languages). */
$bst_t = static function ( $text ) {
	return substr( wp_json_encode( __( $text, 'bigspy-ai-theme-wp' ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ), 1, -1 ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
};
?>
<!-- wp:bst/section {"paddingDesktop": 90, "paddingTablet": 64, "paddingMobile": 48, "bgPreset": "dark"} -->
<!-- wp:bst/title {"text": "<?php echo $bst_t( 'The numbers say it all' ); ?>", "subtitle": "<?php echo $bst_t( 'ACHIEVEMENTS' ); ?>", "color": "#ffffff"} /-->
<!-- wp:bst/row {"gap": "medium"} -->
<!-- wp:bst/column {"spanDesktop": 3, "spanTablet": 6, "spanMobile": 12} -->
<!-- wp:bst/counter {"number": "10", "suffix": "+", "label": "<?php echo $bst_t( 'Years of experience' ); ?>", "color": "#facc15"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop": 3, "spanTablet": 6, "spanMobile": 12} -->
<!-- wp:bst/counter {"number": "25000", "suffix": "+", "label": "<?php echo $bst_t( 'Customers' ); ?>", "color": "#facc15"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop": 3, "spanTablet": 6, "spanMobile": 12} -->
<!-- wp:bst/counter {"number": "3", "suffix": "", "label": "<?php echo $bst_t( 'Locations' ); ?>", "color": "#facc15"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop": 3, "spanTablet": 6, "spanMobile": 12} -->
<!-- wp:bst/counter {"number": "98", "suffix": "%", "label": "<?php echo $bst_t( 'Satisfied' ); ?>", "color": "#facc15"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- wp:bst/star-rating {"rating": 4.9, "label": "<?php echo $bst_t( '4.9/5 from more than 1,200 reviews' ); ?>", "align": "center", "emptyColor": "#475569"} /-->
<!-- /wp:bst/section -->
