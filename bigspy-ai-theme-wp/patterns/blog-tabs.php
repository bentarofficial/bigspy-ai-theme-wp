<?php
/**
 * Title: Blog – post tabs by category
 * Slug: bigspy-ai-theme-wp/blog-tabs
 * Categories: bigspy-sections
 * Keywords: blog, news, posts, tabs, categories
 * Description: A heading with scrolling tabs: latest posts, then one tab per category.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$bst_blog_url = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/' );
$bst_cats     = get_categories(
	array(
		'parent'     => 0,
		'hide_empty' => true,
		'number'     => 5,
		'orderby'    => 'count',
		'order'      => 'DESC',
	)
);
?>
<!-- wp:bst/section {"paddingDesktop":90} -->
<!-- wp:bst/tabs <?php echo serialize_block_attributes( array( 'heading' => 'Latest news', 'style' => 'text', 'overflow' => 'scroll', 'linkText' => 'View all', 'linkUrl' => $bst_blog_url ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> -->
<!-- wp:bst/tab {"title":"All"} -->
<!-- wp:bst/posts {"count":3,"columns":3,"showExcerpt":true} /-->
<!-- /wp:bst/tab -->
<?php foreach ( $bst_cats as $bst_cat ) : ?>
<!-- wp:bst/tab <?php echo serialize_block_attributes( array( 'title' => $bst_cat->name, 'link' => get_category_link( $bst_cat ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> -->
<!-- wp:bst/posts {"count":3,"columns":3,"showExcerpt":true,"category":<?php echo (int) $bst_cat->term_id; ?>} /-->
<!-- /wp:bst/tab -->
<?php endforeach; ?>
<!-- /wp:bst/tabs -->
<!-- /wp:bst/section -->
