<?php
/**
 * Single post content.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$bst_is_post = 'post' === get_post_type();
$bst_thumb   = has_post_thumbnail() && bst_mod( 'single_featured_image' );
$bst_layout  = $bst_is_post ? bst_choice( bst_mod( 'single_layout' ), array( 'standard', 'image-first', 'overlay', 'centered' ), 'standard' ) : 'standard';
if ( 'overlay' === $bst_layout && ( ! $bst_thumb || '1' === bst_post_option( 'hide_title' ) ) ) {
	$bst_layout = 'image-first';
}
$bst_ratio   = bst_choice( bst_mod( 'single_image_ratio' ), array( 'auto', '16-9', '21-9', '4-3' ), 'auto' );
$bst_width   = $bst_is_post ? (int) bst_mod( 'single_width' ) : 0;
$bst_classes = 'bs-entry bs-entry--single bs-entry--' . $bst_layout . ( $bst_is_post && bst_mod( 'single_dropcap' ) ? ' has-dropcap' : '' ) . ( $bst_width > 0 ? ' has-width' : '' );
$bst_figure  = $bst_thumb ? '<figure class="bs-entry__thumb' . ( 'auto' !== $bst_ratio ? ' bs-ratio bs-ratio--' . $bst_ratio : '' ) . '">' . get_the_post_thumbnail( null, 'large' ) . '</figure>' : '';
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $bst_classes ); ?><?php echo $bst_width > 0 ? ' style="--bs-single-w:' . (int) $bst_width . 'px"' : ''; ?>>
	<?php
	if ( 'overlay' === $bst_layout ) {
		echo '<div class="bs-entry__hero">';
	}
	if ( 'image-first' === $bst_layout || 'overlay' === $bst_layout ) {
		echo $bst_figure; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core thumbnail markup.
	}
	?>
	<?php if ( '1' !== bst_post_option( 'hide_title' ) ) : ?>
		<header class="bs-entry__header">
			<?php
			bst_breadcrumbs();
			the_title( '<h1 class="bs-entry__title">', '</h1>' );
			if ( $bst_is_post && bst_mod( 'blog_show_meta' ) ) {
				bst_post_meta( bst_blog_meta_selected( 'single' ) );
			}
			?>
		</header>
	<?php endif; ?>
	<?php
	if ( 'overlay' === $bst_layout ) {
		echo '</div>';
	} elseif ( 'image-first' !== $bst_layout ) {
		echo $bst_figure; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core thumbnail markup.
	}
	?>

	<div class="bs-entry__content bs-content">
		<?php
		the_content();
		wp_link_pages( array( 'before' => '<nav class="bs-page-links">', 'after' => '</nav>' ) );
		?>
	</div>

	<?php if ( $bst_is_post ) : ?>
		<footer class="bs-entry__footer">
			<?php
			if ( bst_mod( 'single_tags' ) ) {
				the_tags( '<div class="bs-tags">', '', '</div>' );
			}
			bst_share_buttons();
			?>
		</footer>
		<?php
		bst_author_box();
		if ( bst_mod( 'single_nav' ) ) {
			the_post_navigation(
				array(
					'prev_text' => '<span class="bs-post-nav__label">' . esc_html__( 'Previous', 'bigspy-ai-theme-wp' ) . '</span><span class="bs-post-nav__title">%title</span>',
					'next_text' => '<span class="bs-post-nav__label">' . esc_html__( 'Next', 'bigspy-ai-theme-wp' ) . '</span><span class="bs-post-nav__title">%title</span>',
				)
			);
		}
		bst_related_posts();
		?>
	<?php endif; ?>
</article>
<?php
if ( ( comments_open() || get_comments_number() ) && ( ! $bst_is_post || bst_mod( 'single_comments' ) ) ) {
	comments_template();
}
