<?php
/**
 * Template Name: Blank canvas (no header, no footer)
 * Template Post Type: page, post
 *
 * Ideal for landing pages built only with blocks.
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
