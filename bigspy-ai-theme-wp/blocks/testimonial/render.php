<?php
/**
 * Testimonial block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a      = (array) $attributes;
$style  = bst_choice( $a['style'] ?? 'card', array( 'card', 'bubble', 'minimal' ), 'card' );
$align  = bst_choice( $a['align'] ?? 'left', array( 'left', 'center' ), 'left' );
$rating = (int) bst_block_num( $a, 'rating', 5, 0, 5 );

$image = '';
if ( ! empty( $a['imageId'] ) ) {
	$image = wp_get_attachment_image( (int) $a['imageId'], 'thumbnail', false, array( 'class' => 'bs-testimonial__img', 'alt' => wp_strip_all_tags( $a['name'] ?? '' ) ) );
} elseif ( ! empty( $a['imageUrl'] ) ) {
	$image = '<img class="bs-testimonial__img" src="' . esc_url( $a['imageUrl'] ) . '" alt="' . esc_attr( wp_strip_all_tags( $a['name'] ?? '' ) ) . '" loading="lazy">';
}
?>
<figure <?php echo get_block_wrapper_attributes( array( 'class' => "bs-testimonial bs-testimonial--$style bs-testimonial--align-$align" ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $rating ) : ?>
		<div class="bs-testimonial__rating" role="img" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: rating */ __( 'Rated %d out of 5', 'bigspy-ai-theme-wp' ), $rating ) ); ?>">
			<?php
			for ( $i = 1; $i <= 5; $i++ ) {
				echo '<span class="' . ( $i <= $rating ? 'is-on' : 'is-off' ) . '">' . bst_icon( 'star', 16 ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</div>
	<?php endif; ?>
	<blockquote class="bs-testimonial__text"><?php echo wp_kses_post( wpautop( $a['text'] ?? '' ) ); ?></blockquote>
	<figcaption class="bs-testimonial__author">
		<?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<span>
			<strong class="bs-testimonial__name"><?php echo wp_kses_post( $a['name'] ?? '' ); ?></strong>
			<?php if ( ! empty( $a['role'] ) ) : ?>
				<span class="bs-testimonial__role"><?php echo wp_kses_post( $a['role'] ); ?></span>
			<?php endif; ?>
		</span>
	</figcaption>
</figure>
