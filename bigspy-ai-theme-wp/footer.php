<?php
/**
 * Site footer.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;
bst_tb_render( 'after_content' );
?>
</main>
<?php
if ( bst_show_footer() ) {
	bst_tb_render( 'before_footer' );
	if ( ! bst_tb_render( 'footer', true ) ) {
		bst_render_footer();
	}
}
?>
</div><!-- #page -->
<?php
if ( bst_show_header() ) {
	bst_render_offcanvas();
}
bst_floating_contact();
bst_back_to_top();
wp_footer();
?>
</body>
</html>
