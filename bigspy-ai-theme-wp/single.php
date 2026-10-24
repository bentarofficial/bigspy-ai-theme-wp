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
	// A Theme Builder layout for the single post template replaces the default markup.
	if ( 'post' === get_post_type() && bst_tb_render( 'single', true ) ) {
		continue;
	}
	get_template_part( 'template-parts/content', 'single' );
}

bst_layout_close();
get_footer();
