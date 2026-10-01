<?php
/**
 * Title: About – image and text
 * Slug: bigspy-theme/about
 * Categories: bigspy-sections
 * Keywords: about, intro, image, story
 * Description: Two columns: image on the left, story with check list and button on the right.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:bst/section {"paddingDesktop":90,"backgroundColor":"light"} -->
<!-- wp:bst/row {"gap":"large","verticalAlign":"center","reverseMobile":false} -->
<!-- wp:bst/column {"spanDesktop":6,"spanTablet":12,"bsAnimation":"fade-right"} -->
<!-- wp:image {"sizeSlug":"large","className":"is-style-rounded-shadow"} -->
<figure class="wp-block-image size-large is-style-rounded-shadow"><img src="<?php echo bst_pattern_image( 'about.svg' ); ?>" alt=""/></figure>
<!-- /wp:image -->
<!-- /wp:bst/column -->

<!-- wp:bst/column {"spanDesktop":6,"spanTablet":12,"bsAnimation":"fade-left"} -->
<!-- wp:bst/title {"text":"More than 10 years of experience","subtitle":"About us","align":"left"} /-->

<!-- wp:paragraph -->
<p>Tell your story: when you started, what you believe in and what makes your team different. Keep it human and specific.</p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"is-style-check"} -->
<ul class="wp-block-list is-style-check"><!-- wp:list-item -->
<li>Professional, certified team</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Transparent pricing, no hidden fees</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Warranty and long-term support</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#contact">Contact us</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->
