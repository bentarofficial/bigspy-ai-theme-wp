<?php
/**
 * 404 page.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

if ( bst_tb_for( '404' ) ) {
	add_filter( 'bst_show_title', '__return_false' );
	get_header();
	bst_tb_render( '404', true );
	get_footer();
	return;
}

$bst_error_design = bst_site_design_page( 'custom_404_page' );
if ( $bst_error_design ) {
	$bst_error_content = bst_site_design_content( $bst_error_design );
	add_filter( 'bst_show_title', '__return_false' );
	get_header();
	echo '<div class="bs-full-width bs-content">' . $bst_error_content . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- published page through the_content.
	get_footer();
	return;
}

get_header();
?>
<div class="bs-container bs-404">
	<p class="bs-404__code">404</p>
	<h1 class="bs-404__title"><?php esc_html_e( 'Oops! That page can not be found.', 'bigspy-ai-theme-wp' ); ?></h1>
	<p><?php esc_html_e( 'The page you are looking for may have been moved or deleted. Try searching instead.', 'bigspy-ai-theme-wp' ); ?></p>
	<?php echo bst_search_form( 'bs-search-form--large' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<p><a class="bs-btn bs-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'bigspy-ai-theme-wp' ); ?></a></p>
</div>
<?php
get_footer();
