<?php
/**
 * Comments.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="bs-comments">
	<?php if ( have_comments() ) : ?>
		<h2 class="bs-comments__title">
			<?php
			/* translators: %s: number of comments */
			echo esc_html( sprintf( _n( '%s comment', '%s comments', get_comments_number(), 'bigspy-theme' ), number_format_i18n( get_comments_number() ) ) );
			?>
		</h2>
		<ol class="bs-comment-list">
			<?php wp_list_comments( array( 'style' => 'ol', 'short_ping' => true, 'avatar_size' => 48 ) ); ?>
		</ol>
		<?php the_comments_pagination(); ?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="bs-comments__closed"><?php esc_html_e( 'Comments are closed.', 'bigspy-theme' ); ?></p>
	<?php endif; ?>

	<?php comment_form( array( 'class_submit' => 'bs-btn bs-btn--primary' ) ); ?>
</section>
