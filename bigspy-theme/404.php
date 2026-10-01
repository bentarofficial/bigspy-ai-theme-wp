<?php
/**
 * 404 page.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="bs-container bs-404">
	<p class="bs-404__code">404</p>
	<h1 class="bs-404__title"><?php esc_html_e( 'Oops! That page can not be found.', 'bigspy-theme' ); ?></h1>
	<p><?php esc_html_e( 'The page you are looking for may have been moved or deleted. Try searching instead.', 'bigspy-theme' ); ?></p>
	<?php echo bst_search_form( 'bs-search-form--large' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<p><a class="bs-btn bs-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'bigspy-theme' ); ?></a></p>
</div>
<?php
get_footer();
