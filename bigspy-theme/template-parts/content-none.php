<?php
/**
 * Nothing found.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="bs-none">
	<p>
		<?php
		if ( is_search() ) {
			esc_html_e( 'Nothing matched your search. Please try different keywords.', 'bigspy-theme' );
		} else {
			esc_html_e( 'There is nothing here yet.', 'bigspy-theme' );
		}
		?>
	</p>
	<?php echo bst_search_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
