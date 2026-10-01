<?php
/**
 * Single post content.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'bs-entry bs-entry--single' ); ?>>
	<?php if ( '1' !== bst_post_option( 'hide_title' ) ) : ?>
		<header class="bs-entry__header">
			<?php
			bst_breadcrumbs();
			the_title( '<h1 class="bs-entry__title">', '</h1>' );
			if ( 'post' === get_post_type() && bst_mod( 'blog_show_meta' ) ) {
				bst_post_meta( array( 'category', 'date', 'author', 'comments' ) );
			}
			?>
		</header>
	<?php endif; ?>

	<?php if ( has_post_thumbnail() && bst_mod( 'single_featured_image' ) ) : ?>
		<figure class="bs-entry__thumb"><?php the_post_thumbnail( 'large' ); ?></figure>
	<?php endif; ?>

	<div class="bs-entry__content bs-content">
		<?php
		the_content();
		wp_link_pages( array( 'before' => '<nav class="bs-page-links">', 'after' => '</nav>' ) );
		?>
	</div>

	<?php if ( 'post' === get_post_type() ) : ?>
		<footer class="bs-entry__footer">
			<?php
			the_tags( '<div class="bs-tags">', '', '</div>' );
			bst_share_buttons();
			?>
		</footer>
		<?php
		bst_author_box();
		if ( bst_mod( 'single_nav' ) ) {
			the_post_navigation(
				array(
					'prev_text' => '<span class="bs-post-nav__label">' . esc_html__( 'Previous', 'bigspy-theme' ) . '</span><span class="bs-post-nav__title">%title</span>',
					'next_text' => '<span class="bs-post-nav__label">' . esc_html__( 'Next', 'bigspy-theme' ) . '</span><span class="bs-post-nav__title">%title</span>',
				)
			);
		}
		bst_related_posts();
		?>
	<?php endif; ?>
</article>
<?php
if ( comments_open() || get_comments_number() ) {
	comments_template();
}
