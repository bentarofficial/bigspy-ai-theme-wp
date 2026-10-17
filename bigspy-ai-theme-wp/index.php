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
	$bst_layout  = bst_choice( bst_mod( 'blog_layout' ), array( 'grid', 'list' ), 'grid' );
	$bst_columns = 'list' === $bst_layout ? 1 : (int) bst_mod( 'blog_columns' );
	echo '<div class="bs-grid bs-posts bs-posts--' . esc_attr( $bst_layout ) . '" style="--bs-cols:' . (int) $bst_columns . '">';
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/content', get_post_type() );
	}
	echo '</div>';
	bst_pagination();
} else {
	get_template_part( 'template-parts/content', 'none' );
}

bst_layout_close();
get_footer();
