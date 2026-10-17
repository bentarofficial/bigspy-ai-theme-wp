<?php
/**
 * Template Name: Full width (no title, no container)
 * Template Post Type: page, post, product
 *
 * Content spans the whole screen. Build the page with Section blocks.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) {
	the_post();
	echo '<div class="bs-full-width bs-content">';
	the_content();
	echo '</div>';
}
get_footer();
