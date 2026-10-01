<?php
/**
 * Title: Hero – full screen with background
 * Slug: bigspy-theme/hero
 * Categories: bigspy-sections
 * Keywords: hero, banner, header, intro
 * Description: Full-height intro section with background image, overlay, title and two buttons.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:bst/section {"bgImage":"<?php echo bst_pattern_image( 'hero.svg' ); ?>","overlayColor":"#0b1220","overlayOpacity":35,"textScheme":"light","paddingDesktop":140,"paddingTablet":100,"paddingMobile":80,"minHeight":85,"minHeightUnit":"vh"} -->
<!-- wp:bst/row {"verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":7,"spanTablet":12,"bsAnimation":"fade-up"} -->
<!-- wp:paragraph {"className":"bs-eyebrow"} -->
<p class="bs-eyebrow">Welcome to our company</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">We build websites that grow your business</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Short, benefit-driven introduction. Tell visitors what you do, who it is for and why they should choose you – in two sentences.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-secondary"} -->
<div class="wp-block-button is-style-secondary"><a class="wp-block-button__link wp-element-button" href="#contact">Get a free quote</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-white"} -->
<div class="wp-block-button is-style-white"><a class="wp-block-button__link wp-element-button" href="#services">Our services</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->
