<?php
/**
 * Site footer.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;
?>
</main>
<?php
if ( bst_show_footer() ) {
	bst_render_footer();
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
