<?php
/**
 * Blog posts block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a      = (array) $attributes;
$layout = bst_choice( $a['layout'] ?? 'grid', array( 'grid', 'list', 'slider', 'magazine' ), 'grid' );
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
if ( ! empty( $a['tag'] ) ) {
	$args['tag_id'] = (int) $a['tag'];
}
$ids = array_filter( array_map( 'absint', explode( ',', (string) ( $a['ids'] ?? '' ) ) ) );
if ( $ids ) {
	$args['post__in']       = $ids;
	$args['orderby']        = 'post__in';
	$args['posts_per_page'] = count( $ids );
	unset( $args['offset'] );
}
if ( ( ! isset( $a['excludeCurrent'] ) || ! empty( $a['excludeCurrent'] ) ) && is_singular( 'post' ) ) {
	$args['post__not_in'] = array( get_queried_object_id() );
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
	'show'    => ! empty( $a['showMeta'] ) ? array_keys( array_filter( array( 'category' => ( $a['showCategory'] ?? true ) && empty( $a['catBadge'] ), 'date' => ( $a['showDate'] ?? true ) && empty( $a['dateBadge'] ), 'author' => ! empty( $a['showAuthor'] ), 'readtime' => ! empty( $a['showReadTime'] ) ) ) ) : array(),
	'date_badge'  => ! empty( $a['dateBadge'] ),
	'cat_badge'   => ! empty( $a['catBadge'] ),
	'excerpt_len' => ! empty( $a['excerptLength'] ) ? bst_block_num( $a, 'excerptLength', 20, 5, 80 ) : 0,
	'more_style'  => bst_choice( $a['moreStyle'] ?? 'link', array( 'link', 'button', 'none' ), 'link' ),
	'excerpt' => ! empty( $a['showExcerpt'] ),
	'more'    => (string) ( $a['readMore'] ?? '' ),
	'heading' => 'h3',
);
if ( ! empty( $a['cardStyle'] ) ) {
	$card_args['style'] = bst_choice( $a['cardStyle'], array( 'shadow', 'bordered', 'plain', 'overlay', 'minimal', 'soft', 'gradient' ), 'shadow' );
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
	$html = bst_slider_markup( $items, array( 'd' => $d, 't' => $t, 'm' => $m, 'gap' => 24, 'autoplay' => bst_block_num( $a, 'autoplay', 0, 0, 30 ) * 1000, 'dots' => ! isset( $a['dots'] ) || ! empty( $a['dots'] ), 'arrows' => ! isset( $a['arrows'] ) || ! empty( $a['arrows'] ), 'loop' => true, 'peek' => bst_block_num( $a, 'peek', 0, 0, 40 ) ), 'bs-slider--cards bs-slider--arrows-dark' );
} else {
	if ( 'list' === $layout ) {
		$d = 1;
		$t = 1;
		$m = 1;
	}
	$html = '<div class="bs-block-grid bs-posts bs-posts--' . esc_attr( $layout ) . '" style="' . esc_attr( bst_style_vars( array( '--bs-cols-d' => $d, '--bs-cols-t' => $t, '--bs-cols-m' => $m ) ) ) . '">' . $items . '</div>';
}
?>
<?php
$wrap_class = 'bs-posts-block bs-posts-hover--' . bst_choice( $a['imageHover'] ?? 'zoom', array( 'zoom', 'none', 'grayscale', 'lift' ), 'zoom' )
	. ( 'center' === ( $a['align'] ?? '' ) ? ' bs-posts--center' : '' ) . ( ! empty( $a['titleLines'] ) ? ' bs-posts--clamp' : '' );
$wrap_vars  = bst_style_vars(
	array(
		'--bs-post-title'   => ! empty( $a['titleSize'] ) ? bst_block_num( $a, 'titleSize', 20, 12, 48 ) . 'px' : '',
		'--bs-post-lines'   => ! empty( $a['titleLines'] ) ? bst_block_num( $a, 'titleLines', 2, 1, 4 ) : '',
		'--bs-post-gap'     => isset( $a['gap'] ) && $a['gap'] >= 0 ? bst_block_num( $a, 'gap', 24, 0, 60 ) . 'px' : '',
		'--bs-post-accent'  => bst_block_color( $a, 'accent' ),
		'--bs-post-bg'      => bst_block_color( $a, 'cardBg' ),
	)
);
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => $wrap_class, 'style' => $wrap_vars ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
