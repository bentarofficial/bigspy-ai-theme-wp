<?php
/** Standalone maintenance screen. @package BigspyTheme */
defined( 'ABSPATH' ) || exit;
$bst_maintenance_page = bst_site_design_page( 'maintenance_page' );
$bst_maintenance_content = $bst_maintenance_page ? bst_site_design_content( $bst_maintenance_page ) : '';
wp_enqueue_style( 'bst-maintenance', BST_URI . '/assets/css/maintenance.css', array( 'bst-main' ), bst_asset_version( 'assets/css/maintenance.css' ) );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'bs-maintenance' ); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'bigspy-ai-theme-wp' ); ?></a>
<main id="content" class="bs-maintenance__content bs-content">
<?php if ( $bst_maintenance_page ) : ?>
	<?php echo $bst_maintenance_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted published page through the_content. ?>
<?php else : ?>
	<section class="bs-maintenance__card">
		<p class="bs-maintenance__brand"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
		<h1><?php esc_html_e( 'We will be back soon', 'bigspy-ai-theme-wp' ); ?></h1>
		<p><?php echo esc_html( bst_mod( 'maintenance_message' ) ?: __( 'Our website is being updated. Please check back soon.', 'bigspy-ai-theme-wp' ) ); ?></p>
	</section>
<?php endif; ?>
</main>
<?php wp_footer(); ?>
</body>
</html>
