<?php
/**
 * Default page template.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();
bst_layout_open();
bst_simple_title();

while ( have_posts() ) {
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'bs-entry' ); ?>>
		<div class="bs-entry__content bs-content">
			<?php
			the_content();
			wp_link_pages( array( 'before' => '<nav class="bs-page-links">', 'after' => '</nav>' ) );
			?>
		</div>
	</article>
	<?php
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
}

bst_layout_close();
get_footer();
