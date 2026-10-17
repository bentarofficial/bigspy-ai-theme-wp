<?php
/**
 * Blog posts block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a      = (array) $attributes;
$layout = bst_choice( $a['layout'] ?? 'grid', array( 'grid', 'list', 'slider' ), 'grid' );
$order  = bst_choice( $a['orderBy'] ?? 'date', array( 'date', 'modified', 'comment_count', 'rand', 'title' ), 'date' );

$args = array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => bst_block_num( $a, 'count', 6, 1, 50 ),
	'offset'              => bst_block_num( $a, 'offset', 0, 0, 100 ),
	'orderby'             => $order,
	'order'               => 'title' === $order ? 'ASC' : 'DESC',
	'ignore_sticky_posts' => true,
	'no_found_rows'       => true,
);
if ( ! empty( $a['category'] ) ) {
	$args['cat'] = (int) $a['category'];
}

$query = new WP_Query( $args );
if ( ! $query->have_posts() ) {
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		echo '<p>' . esc_html__( 'No posts found.', 'bigspy-ai-theme-wp' ) . '</p>';
	}
	return;
}

$card_args = array(
	'ratio'   => bst_choice( $a['imageRatio'] ?? '16-9', array( '16-9', '4-3', '3-2', '1-1', 'auto' ), '16-9' ),
	'meta'    => ! empty( $a['showMeta'] ),
	'excerpt' => ! empty( $a['showExcerpt'] ),
	'more'    => (string) ( $a['readMore'] ?? '' ),
	'heading' => 'h3',
);
if ( ! empty( $a['cardStyle'] ) ) {
	$card_args['style'] = bst_choice( $a['cardStyle'], array( 'shadow', 'bordered', 'plain', 'overlay' ), 'shadow' );
}

ob_start();
while ( $query->have_posts() ) {
	$query->the_post();
	bst_post_card( $card_args );
}
wp_reset_postdata();
$items = ob_get_clean();

$d = bst_block_num( $a, 'columns', 3, 1, 6 );
$t = bst_block_num( $a, 'columnsTablet', 2, 1, 4 );
$m = bst_block_num( $a, 'columnsMobile', 1, 1, 2 );

if ( 'slider' === $layout ) {
	$html = bst_slider_markup( $items, array( 'd' => $d, 't' => $t, 'm' => $m, 'gap' => 24, 'autoplay' => bst_block_num( $a, 'autoplay', 0, 0, 30 ) * 1000, 'dots' => true, 'loop' => true ), 'bs-slider--cards' );
} else {
	if ( 'list' === $layout ) {
		$d = 1;
		$t = 1;
		$m = 1;
	}
	$html = '<div class="bs-block-grid bs-posts bs-posts--' . esc_attr( $layout ) . '" style="' . esc_attr( bst_style_vars( array( '--bs-cols-d' => $d, '--bs-cols-t' => $t, '--bs-cols-m' => $m ) ) ) . '">' . $items . '</div>';
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-posts-block' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
