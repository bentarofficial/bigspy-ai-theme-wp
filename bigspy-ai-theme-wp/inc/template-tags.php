<?php
/**
 * Template helpers: layout wrappers, title bar, breadcrumbs, post parts.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Open the content + sidebar layout.
 */
function bst_layout_open() {
	$sidebar = bst_sidebar_position();
	echo '<div class="bs-container bs-layout bs-layout--' . esc_attr( $sidebar ) . '"><div class="bs-layout__content">';
}

function bst_layout_close() {
	echo '</div>';
	$sidebar = bst_sidebar_position();
	if ( 'none' !== $sidebar ) {
		$id = bst_sidebar_id();
		$is_shop_sidebar = function_exists( 'is_woocommerce' ) && ! is_product() && ( is_shop() || is_product_taxonomy() );
		$sidebar_id      = $is_shop_sidebar ? 'bs-shop-sidebar' : 'bs-layout-sidebar';
		$sidebar_class   = 'bs-layout__sidebar widget-area' . ( $is_shop_sidebar ? ' bs-shop-sidebar' : '' );
		echo '<aside id="' . esc_attr( $sidebar_id ) . '" class="' . esc_attr( $sidebar_class ) . '" aria-label="' . esc_attr( $is_shop_sidebar ? __( 'Product filters and shop widgets', 'bigspy-ai-theme-wp' ) : __( 'Sidebar', 'bigspy-ai-theme-wp' ) ) . '"' . ( $is_shop_sidebar ? ' tabindex="-1"' : '' ) . '>';
		if ( is_active_sidebar( $id ) ) {
			dynamic_sidebar( $id );
		} elseif ( current_user_can( 'edit_theme_options' ) ) {
			echo '<p class="bs-header-hint"><a href="' . esc_url( admin_url( 'widgets.php' ) ) . '">' . esc_html__( 'Add widgets to this sidebar', 'bigspy-ai-theme-wp' ) . '</a></p>';
		}
		echo '</aside>';
	}
	echo '</div>';
}

/**
 * Title of the current view.
 */
function bst_get_title() {
	if ( function_exists( 'is_shop' ) && is_shop() ) {
		return woocommerce_page_title( false );
	}
	if ( is_home() ) {
		$page = (int) get_option( 'page_for_posts' );
		return $page ? get_the_title( $page ) : __( 'Blog', 'bigspy-ai-theme-wp' );
	}
	if ( is_search() ) {
		/* translators: %s: search query */
		return sprintf( __( 'Search results for “%s”', 'bigspy-ai-theme-wp' ), get_search_query() );
	}
	if ( is_404() ) {
		return __( 'Page not found', 'bigspy-ai-theme-wp' );
	}
	if ( is_archive() ) {
		return get_the_archive_title();
	}
	return get_the_title();
}

add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );

/**
 * Whether the current view should print its title.
 */
function bst_show_title() {
	if ( is_front_page() && ! is_home() ) {
		$show = false;
	} elseif ( is_page_template( array( 'templates/full-width.php', 'templates/blank.php' ) ) ) {
		$show = false;
	} elseif ( '1' === bst_post_option( 'hide_title' ) ) {
		$show = false;
	} else {
		$show = 'none' !== bst_mod( 'title_bar_style' );
	}
	return (bool) apply_filters( 'bst_show_title', $show );
}

/**
 * Full-width title bar printed above the layout ("bar" style).
 */
function bst_title_bar() {
	if ( 'bar' !== bst_mod( 'title_bar_style' ) || ! bst_show_title() || ( is_singular( 'post' ) ) || ( function_exists( 'is_product' ) && is_product() ) ) {
		return;
	}
	echo '<div class="bs-title-bar' . ( bst_mod( 'title_bar_image' ) ? ' has-image' : '' ) . '"><div class="bs-container bs-title-bar__inner">';
	echo '<h1 class="bs-title-bar__title">' . wp_kses_post( bst_get_title() ) . '</h1>';
	bst_archive_description();
	bst_breadcrumbs();
	echo '</div></div>';
}

/**
 * Simple in-content title ("simple" style).
 */
function bst_simple_title() {
	if ( 'simple' !== bst_mod( 'title_bar_style' ) || ! bst_show_title() ) {
		return;
	}
	echo '<header class="bs-page-header">';
	bst_breadcrumbs();
	echo '<h1 class="bs-page-title">' . wp_kses_post( bst_get_title() ) . '</h1>';
	bst_archive_description();
	echo '</header>';
}

function bst_archive_description() {
	if ( is_archive() && ! ( function_exists( 'is_shop' ) && is_shop() ) ) {
		$description = get_the_archive_description();
		if ( $description ) {
			echo '<div class="bs-archive-description">' . wp_kses_post( $description ) . '</div>';
		}
	}
}

/**
 * Breadcrumbs: uses Rank Math / Yoast / WooCommerce when available.
 */
function bst_breadcrumbs() {
	if ( ! bst_mod( 'breadcrumbs' ) || is_front_page() ) {
		return;
	}
	if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
		echo '<div class="bs-breadcrumbs">';
		rank_math_the_breadcrumbs();
		echo '</div>';
		return;
	}
	if ( function_exists( 'yoast_breadcrumb' ) ) {
		yoast_breadcrumb( '<div class="bs-breadcrumbs">', '</div>' );
		return;
	}
	if ( function_exists( 'woocommerce_breadcrumb' ) && function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
		woocommerce_breadcrumb( array( 'wrap_before' => '<nav class="bs-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'bigspy-ai-theme-wp' ) . '">', 'wrap_after' => '</nav>', 'delimiter' => '<span class="bs-breadcrumbs__sep">/</span>' ) );
		return;
	}

	$crumbs   = array( array( home_url( '/' ), __( 'Home', 'bigspy-ai-theme-wp' ) ) );
	$blog_url = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : '';

	if ( is_singular( 'post' ) ) {
		if ( $blog_url ) {
			$crumbs[] = array( $blog_url, get_the_title( get_option( 'page_for_posts' ) ) );
		}
		$cats = get_the_category();
		if ( $cats ) {
			$crumbs[] = array( get_category_link( $cats[0] ), $cats[0]->name );
		}
		$crumbs[] = array( '', get_the_title() );
	} elseif ( is_page() ) {
		foreach ( array_reverse( get_post_ancestors( get_queried_object_id() ) ) as $ancestor ) {
			$crumbs[] = array( get_permalink( $ancestor ), get_the_title( $ancestor ) );
		}
		$crumbs[] = array( '', get_the_title() );
	} elseif ( is_singular() ) {
		$type = get_post_type_object( get_post_type() );
		if ( $type && $type->has_archive ) {
			$crumbs[] = array( get_post_type_archive_link( $type->name ), $type->labels->name );
		}
		$crumbs[] = array( '', get_the_title() );
	} else {
		if ( ( is_category() || is_tag() || is_author() || is_date() ) && $blog_url ) {
			$crumbs[] = array( $blog_url, get_the_title( get_option( 'page_for_posts' ) ) );
		}
		$crumbs[] = array( '', wp_strip_all_tags( bst_get_title() ) );
	}

	$items = array();
	foreach ( $crumbs as $crumb ) {
		$items[] = $crumb[0] ? '<a href="' . esc_url( $crumb[0] ) . '">' . esc_html( $crumb[1] ) . '</a>' : '<span aria-current="page">' . esc_html( $crumb[1] ) . '</span>';
	}
	echo '<nav class="bs-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'bigspy-ai-theme-wp' ) . '">' . implode( '<span class="bs-breadcrumbs__sep">/</span>', $items ) . '</nav>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Post meta line: category, date, author, comments.
 */
function bst_post_meta( $parts = array( 'date', 'author', 'comments' ) ) {
	$out = array();
	foreach ( $parts as $part ) {
		switch ( $part ) {
			case 'date':
				$out[] = '<time datetime="' . esc_attr( get_the_date( DATE_W3C ) ) . '">' . esc_html( get_the_date() ) . '</time>';
				break;
			case 'author':
				$out[] = '<span class="bs-meta__author">' . esc_html( get_the_author() ) . '</span>';
				break;
			case 'category':
				$cats = get_the_category();
				if ( $cats ) {
					$out[] = '<a class="bs-meta__cat" href="' . esc_url( get_category_link( $cats[0] ) ) . '">' . esc_html( $cats[0]->name ) . '</a>';
				}
				break;
			case 'comments':
				if ( comments_open() && get_comments_number() ) {
					/* translators: %s: comment count */
					$out[] = '<span>' . esc_html( sprintf( _n( '%s comment', '%s comments', get_comments_number(), 'bigspy-ai-theme-wp' ), number_format_i18n( get_comments_number() ) ) ) . '</span>';
				}
				break;
		}
	}
	if ( $out ) {
		echo '<div class="bs-meta">' . implode( '<span class="bs-meta__sep" aria-hidden="true">•</span>', $out ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Post card used by the blog archive, related posts and the Posts block.
 *
 * @param array $args Display options.
 */
function bst_post_card( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'style'   => bst_mod( 'blog_card_style' ),
			'ratio'   => bst_mod( 'blog_image_ratio' ),
			'meta'    => bst_mod( 'blog_show_meta' ),
			'excerpt' => bst_mod( 'blog_show_excerpt' ),
			'more'    => bst_mod( 'blog_read_more' ),
			'heading' => 'h2',
		)
	);
	// The untouched default label follows the site language (the Posts block passes it as its own default).
	if ( 'Read more' === $args['more'] ) {
		$args['more'] = __( 'Read more', 'bigspy-ai-theme-wp' );
	}
	$heading = bst_choice( $args['heading'], array( 'h2', 'h3', 'h4', 'h5' ), 'h2' );
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'bs-card bs-card--' . sanitize_html_class( $args['style'] ) ); ?>>
		<?php if ( has_post_thumbnail() ) : ?>
			<a class="bs-card__media bs-ratio bs-ratio--<?php echo esc_attr( $args['ratio'] ); ?>" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
				<?php the_post_thumbnail( 'bst-card', array( 'alt' => '' ) ); ?>
			</a>
		<?php endif; ?>
		<div class="bs-card__body">
			<?php
			if ( $args['meta'] ) {
				bst_post_meta( array( 'category', 'date' ) );
			}
			printf( '<%1$s class="bs-card__title"><a href="%2$s">%3$s</a></%1$s>', esc_html( $heading ), esc_url( get_permalink() ), esc_html( get_the_title() ) );
			if ( $args['excerpt'] ) {
				echo '<div class="bs-card__excerpt">' . wp_kses_post( wpautop( get_the_excerpt() ) ) . '</div>';
			}
			if ( $args['more'] ) {
				echo '<a class="bs-card__more" href="' . esc_url( get_permalink() ) . '">' . esc_html( $args['more'] ) . bst_icon( 'arrow-right', 16 ) . '<span class="screen-reader-text"> ' . esc_html( get_the_title() ) . '</span></a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</div>
	</article>
	<?php
}

function bst_pagination() {
	the_posts_pagination(
		array(
			'mid_size'  => 1,
			'prev_text' => bst_icon( 'chevron-left', 18 ) . '<span class="screen-reader-text">' . __( 'Previous', 'bigspy-ai-theme-wp' ) . '</span>',
			'next_text' => '<span class="screen-reader-text">' . __( 'Next', 'bigspy-ai-theme-wp' ) . '</span>' . bst_icon( 'chevron-right', 18 ),
			'class'     => 'bs-pagination',
		)
	);
}

function bst_share_buttons() {
	if ( ! bst_mod( 'single_share' ) ) {
		return;
	}
	$url   = rawurlencode( get_permalink() );
	$title = rawurlencode( get_the_title() );
	$links = array(
		'facebook'  => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
		'x'         => 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title,
		'linkedin'  => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url,
		'pinterest' => 'https://pinterest.com/pin/create/button/?url=' . $url . '&description=' . $title,
		'mail'      => 'mailto:?subject=' . $title . '&body=' . $url,
	);
	echo '<div class="bs-share"><span class="bs-share__label">' . esc_html__( 'Share:', 'bigspy-ai-theme-wp' ) . '</span>';
	foreach ( $links as $network => $link ) {
		echo '<a class="bs-share__link bs-share__link--' . esc_attr( $network ) . '" href="' . esc_url( $link, array( 'https', 'mailto' ) ) . '" target="_blank" rel="noopener nofollow" aria-label="' . esc_attr( ucfirst( $network ) ) . '">' . bst_icon( $network, 16 ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '<button type="button" class="bs-share__link bs-share__copy" data-bs-copy="' . esc_attr( get_permalink() ) . '" aria-label="' . esc_attr__( 'Copy link', 'bigspy-ai-theme-wp' ) . '">' . bst_icon( 'check', 16 ) . '<span>' . esc_html__( 'Copy link', 'bigspy-ai-theme-wp' ) . '</span></button>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '</div>';
}

function bst_author_box() {
	if ( ! bst_mod( 'single_author_box' ) ) {
		return;
	}
	$bio = get_the_author_meta( 'description' );
	if ( ! $bio ) {
		return;
	}
	?>
	<section class="bs-author-box">
		<?php echo get_avatar( get_the_author_meta( 'ID' ), 80 ); ?>
		<div>
			<h2 class="bs-author-box__name"><?php the_author_posts_link(); ?></h2>
			<p><?php echo wp_kses_post( $bio ); ?></p>
		</div>
	</section>
	<?php
}

function bst_related_posts() {
	if ( ! bst_mod( 'single_related' ) ) {
		return;
	}
	$query = new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => 3,
			'post__not_in'        => array( get_the_ID() ),
			'category__in'        => wp_get_post_categories( get_the_ID() ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	if ( ! $query->have_posts() ) {
		return;
	}
	echo '<section class="bs-related"><h2 class="bs-related__title">' . esc_html__( 'Related posts', 'bigspy-ai-theme-wp' ) . '</h2><div class="bs-grid" style="--bs-cols:3">';
	while ( $query->have_posts() ) {
		$query->the_post();
		bst_post_card( array( 'excerpt' => false, 'more' => '', 'heading' => 'h3' ) );
	}
	echo '</div></section>';
	wp_reset_postdata();
}
