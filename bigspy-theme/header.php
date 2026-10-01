<?php
/**
 * Site header.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'bigspy-theme' ); ?></a>
<div id="page" class="bs-site">
<?php
if ( bst_show_header() ) {
	bst_render_header();
}
?>
<main id="content" class="bs-main">
<?php bst_title_bar(); ?>
