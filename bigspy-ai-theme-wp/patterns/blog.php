<?php
/**
 * Title: Latest news
 * Slug: bigspy-ai-theme-wp/blog
 * Categories: bigspy-sections
 * Keywords: blog, news, posts, articles
 * Description: Section title with the three latest posts.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:bst/section {"paddingDesktop":90} -->
<!-- wp:bst/title {"text":"Latest news","subtitle":"Blog","style":"underline-full","align":"left","linkText":"View all","linkUrl":"<?php echo esc_url( get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/' ) ); ?>"} /-->

<!-- wp:bst/posts {"count":3,"columns":3,"showExcerpt":true} /-->
<!-- /wp:bst/section -->
