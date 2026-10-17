<?php
/**
 * Title: Testimonials slider
 * Slug: bigspy-ai-theme-wp/testimonials
 * Categories: bigspy-sections
 * Keywords: testimonial, review, feedback, slider
 * Description: Section title with a slider of customer reviews.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;
$bst_avatar = bst_pattern_image( 'avatar.svg' );
?>
<!-- wp:bst/section {"paddingDesktop":90} -->
<!-- wp:bst/title {"text":"What our customers say","subtitle":"Testimonials"} /-->

<!-- wp:bst/slider {"slidesDesktop":3,"slidesTablet":2,"slidesMobile":1,"gap":24,"autoplay":6} -->
<!-- wp:bst/testimonial {"imageUrl":"<?php echo $bst_avatar; // phpcs:ignore ?>","name":"John Carter","role":"CEO, Northwind Co.","text":"Great service and a very professional team. Our new website brought us more customers from the first month."} /-->

<!-- wp:bst/testimonial {"imageUrl":"<?php echo $bst_avatar; // phpcs:ignore ?>","name":"Emily Stone","role":"Shop owner","text":"Fast, responsive and easy to manage. I can update products and posts myself without any technical knowledge."} /-->

<!-- wp:bst/testimonial {"imageUrl":"<?php echo $bst_avatar; // phpcs:ignore ?>","name":"Michael Lee","role":"Marketing manager","text":"The design looks beautiful on mobile and the site loads very quickly. Highly recommended!"} /-->

<!-- wp:bst/testimonial {"imageUrl":"<?php echo $bst_avatar; // phpcs:ignore ?>","name":"Sarah Miller","role":"Founder, Bright Studio","text":"Clear process, on-time delivery and great support after launch. We will definitely work together again."} /-->
<!-- /wp:bst/slider -->
<!-- /wp:bst/section -->
