<?php
/**
 * Footer, floating contact buttons and back-to-top.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

function bst_render_footer() {
	bst_footer_cta();
	echo '<footer id="colophon" class="bs-footer">';
	do_action( 'bst_footer_start' );
	if ( ! bst_footer_design() ) {
		bst_footer_widgets_row( 1 );
		bst_footer_widgets_row( 2 );
	}
	bst_footer_bottom();
	echo '</footer>';
}

/**
 * Call-to-action band above the footer.
 */
function bst_footer_cta() {
	if ( ! bst_mod( 'footer_cta_enable' ) || is_page_template( 'templates/blank.php' ) ) {
		return;
	}

	$hidden = array_filter( array_map( 'trim', explode( ',', (string) bst_mod( 'footer_cta_hide_pages' ) ) ) );
	if ( $hidden && is_page() ) {
		$page = get_queried_object();
		if ( in_array( (string) $page->ID, $hidden, true ) || in_array( $page->post_name, $hidden, true ) ) {
			return;
		}
	}

	$title = trim( (string) bst_mod( 'footer_cta_title' ) );
	$text  = bst_html_field( 'footer_cta_text' );
	if ( '' === $title && '' === $text ) {
		return;
	}

	$buttons = '';
	foreach ( array( array( 'footer_cta_button', 'footer_cta_url', 'white' ), array( 'footer_cta_button_2', 'footer_cta_url_2', 'ghost' ) ) as $btn ) {
		$label = trim( (string) bst_mod( $btn[0] ) );
		$url   = trim( (string) bst_mod( $btn[1] ) );
		if ( '' !== $label && '' !== $url ) {
			$buttons .= '<a class="bs-btn bs-btn--' . $btn[2] . ' bs-btn--lg" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
		}
	}
	?>
	<section class="bs-footer-cta" aria-label="<?php esc_attr_e( 'Call to action', 'bigspy-ai-theme-wp' ); ?>">
		<div class="bs-container bs-footer-cta__inner">
			<div class="bs-footer-cta__text">
				<?php if ( $title ) : ?>
					<h2 class="bs-footer-cta__title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>
				<?php if ( $text ) : ?>
					<div class="bs-footer-cta__desc"><?php echo $text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<?php endif; ?>
			</div>
			<?php if ( $buttons ) : ?>
				<div class="bs-footer-cta__actions"><?php echo $buttons; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

/**
 * @param int $row 1 or 2.
 */
function bst_footer_widgets_row( $row ) {
	$columns = (int) bst_mod( 'footer_' . $row . '_columns' );
	if ( $columns < 1 ) {
		return;
	}

	$active = false;
	for ( $i = 1; $i <= $columns; $i++ ) {
		$active = $active || is_active_sidebar( 'footer-' . $row . '-' . $i );
	}
	if ( ! $active ) {
		return;
	}

	echo '<div class="bs-footer-widgets bs-footer-widgets--' . (int) $row . '">';
	echo '<div class="bs-container bs-footer-widgets__grid" style="--bs-footer-cols:' . (int) $columns . '">';
	for ( $i = 1; $i <= $columns; $i++ ) {
		echo '<div class="bs-footer-widgets__col">';
		dynamic_sidebar( 'footer-' . $row . '-' . $i );
		echo '</div>';
	}
	echo '</div></div>';
}

function bst_footer_bottom() {
	if ( ! bst_mod( 'footer_bottom_enable' ) ) {
		return;
	}
	$layout    = bst_choice( bst_mod( 'footer_bottom_layout' ), array( 'split', 'center' ), 'split' );
	$copyright = bst_html_field( 'footer_copyright' );
	$extra     = bst_html_field( 'footer_bottom_html' );
	$menu      = has_nav_menu( 'footer' ) ? wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'menu_class' => 'bs-footer-menu', 'depth' => 1, 'echo' => false, 'fallback_cb' => false ) ) : '';
	$social    = bst_mod( 'footer_social' ) ? bst_social_icons( 'bs-social--footer' ) : '';

	echo '<div class="bs-footer-bottom bs-footer-bottom--' . esc_attr( $layout ) . '"><div class="bs-container bs-footer-bottom__inner">';
	echo '<div class="bs-footer-bottom__left">';
	if ( $menu ) {
		echo $menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	if ( $copyright ) {
		echo '<div class="bs-copyright">' . $copyright . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</div><div class="bs-footer-bottom__right">';
	echo $social . $extra; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '</div></div></div>';
}

/**
 * Floating call / Zalo / Messenger buttons – a common request for Vietnamese business sites.
 */
function bst_back_to_top() {
	if ( ! bst_mod( 'back_to_top' ) ) {
		return;
	}
	echo '<a href="#page" class="bs-back-to-top" data-bs-back-to-top aria-label="' . esc_attr__( 'Back to top', 'bigspy-ai-theme-wp' ) . '">' . bst_icon( 'arrow-up', 20 ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
