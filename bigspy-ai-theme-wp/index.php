<?php
/**
 * Blog index, archives and search results.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();
bst_layout_open();
bst_simple_title();

if ( have_posts() ) {
	do_action( 'bst_before_posts_loop' );
	bst_blog_wrap_open();
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/content', get_post_type() );
	}
	echo '</div>';
	do_action( 'bst_after_posts_loop' );
	bst_blog_pagination();
} else {
	get_template_part( 'template-parts/content', 'none' );
}

bst_layout_close();
get_footer();
