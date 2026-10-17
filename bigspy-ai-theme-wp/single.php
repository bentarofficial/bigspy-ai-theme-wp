<?php
/**
 * Single post and custom post types.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();
bst_layout_open();

while ( have_posts() ) {
	the_post();
	get_template_part( 'template-parts/content', 'single' );
}

bst_layout_close();
get_footer();
