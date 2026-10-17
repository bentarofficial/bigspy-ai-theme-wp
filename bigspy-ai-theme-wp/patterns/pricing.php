<?php
/**
 * Title: Pricing – 3 plans
 * Slug: bigspy-ai-theme-wp/pricing
 * Categories: bigspy-sections
 * Keywords: pricing, plans, packages, price table
 * Description: Three pricing cards; the middle one is highlighted.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$bst_plans = array(
	array( 'Basic', '$199', array( 'Up to 5 pages', 'Mobile friendly', 'Basic SEO setup' ), false ),
	array( 'Business', '$499', array( 'Up to 15 pages', 'Blog & news', 'Advanced SEO', 'Contact forms' ), true ),
	array( 'E-commerce', '$899', array( 'Unlimited products', 'Online payment', 'Order management', '1 year support' ), false ),
);
?>
<!-- wp:bst/section {"paddingDesktop":90,"backgroundColor":"light"} -->
<!-- wp:bst/title {"text":"Simple, transparent pricing","subtitle":"Pricing"} /-->

<!-- wp:bst/row {"verticalAlign":"center"} -->
<?php foreach ( $bst_plans as $bst_i => $bst_plan ) : ?>
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":<?php echo 1 === $bst_i ? 12 : 6; ?>,"bsAnimation":"fade-up","bsAnimationDelay":<?php echo (int) $bst_i * 120; ?>} -->
<!-- wp:group {"className":"is-style-card<?php echo $bst_plan[3] ? ' bs-pricing-featured' : ''; ?>","layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-card<?php echo $bst_plan[3] ? ' bs-pricing-featured' : ''; ?>"><!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="wp-block-heading has-text-align-center"><?php echo esc_html( $bst_plan[0] ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","className":"bs-price"} -->
<p class="has-text-align-center bs-price"><?php echo esc_html( $bst_plan[1] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"is-style-check"} -->
<ul class="wp-block-list is-style-check"><?php foreach ( $bst_plan[2] as $bst_feature ) : ?><!-- wp:list-item -->
<li><?php echo esc_html( $bst_feature ); ?></li>
<!-- /wp:list-item --><?php endforeach; ?></ul>
<!-- /wp:list -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"<?php echo $bst_plan[3] ? 'is-style-secondary' : 'is-style-outline'; ?>"} -->
<div class="wp-block-button <?php echo $bst_plan[3] ? 'is-style-secondary' : 'is-style-outline'; ?>"><a class="wp-block-button__link wp-element-button" href="#contact">Choose plan</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
<!-- /wp:bst/column -->
<?php endforeach; ?>
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->
