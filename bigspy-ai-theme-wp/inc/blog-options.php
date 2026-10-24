<?php
/**
 * Blog archive and single post options: layouts, meta, pagination, table of contents…
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Meta parts a post can show, in display order. */
function bst_blog_meta_parts() {
	return array(
		'category' => __( 'Category', 'bigspy-ai-theme-wp' ),
		'date'     => __( 'Date', 'bigspy-ai-theme-wp' ),
		'author'   => __( 'Author', 'bigspy-ai-theme-wp' ),
		'readtime' => __( 'Reading time', 'bigspy-ai-theme-wp' ),
		'comments' => __( 'Comments', 'bigspy-ai-theme-wp' ),
	);
}

add_filter(
	'bst_defaults',
	function ( $d ) {
		return array_merge(
			$d,
			array(
				'blog_text_align'       => 'left',
				'blog_hover'            => 'zoom',
				'blog_gap'              => 0,
				'blog_radius'           => -1,
				'blog_title_size'       => 0,
				'blog_title_lines'      => 0,
				'blog_cat_badge'        => false,
				'blog_date_badge'       => false,
				'blog_more_style'       => 'link',
				'blog_meta_category'    => true,
				'blog_meta_date'        => true,
				'blog_meta_author'      => false,
				'blog_meta_readtime'    => false,
				'blog_meta_comments'    => false,
				'blog_pagination'       => 'numbers',
				'single_layout'         => 'standard',
				'single_width'          => 0,
				'single_image_ratio'    => 'auto',
				'single_meta_category'  => true,
				'single_meta_date'      => true,
				'single_meta_author'    => true,
				'single_meta_readtime'  => false,
				'single_meta_comments'  => true,
				'single_toc'            => false,
				'single_toc_min'        => 3,
				'single_dropcap'        => false,
				'single_tags'           => true,
				'single_comments'       => true,
				'single_related_count'  => 3,
				'single_related_cols'   => 3,
				'single_related_by'     => 'category',
				'single_related_title'  => '',
			)
		);
	}
);

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$a     = 'bst_blog_archive';
		$s     = 'bst_blog_single';
		$f     = array();
		$parts = bst_blog_meta_parts();

		$f[] = array( 'id' => 'bst_g_blog_look', 'section' => $a, 'type' => 'heading', 'label' => __( 'Card details', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'blog_text_align', 'section' => $a, 'type' => 'select', 'label' => __( 'Text alignment', 'bigspy-ai-theme-wp' ), 'choices' => array( 'left' => __( 'Left', 'bigspy-ai-theme-wp' ), 'center' => __( 'Center', 'bigspy-ai-theme-wp' ) ) );
		$f[] = array( 'id' => 'blog_hover', 'section' => $a, 'type' => 'select', 'label' => __( 'Hover effect', 'bigspy-ai-theme-wp' ), 'choices' => array( 'zoom' => __( 'Zoom image', 'bigspy-ai-theme-wp' ), 'lift' => __( 'Lift card', 'bigspy-ai-theme-wp' ), 'grayscale' => __( 'Grayscale to color', 'bigspy-ai-theme-wp' ), 'none' => __( 'None', 'bigspy-ai-theme-wp' ) ) );
		$f[] = array( 'id' => 'blog_gap', 'section' => $a, 'type' => 'number', 'label' => __( 'Space between posts (px, 0 = default)', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 0, 'max' => 80 ) );
		$f[] = array( 'id' => 'blog_radius', 'section' => $a, 'type' => 'number', 'label' => __( 'Card corner radius (px, -1 = default)', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => -1, 'max' => 40 ) );
		$f[] = array( 'id' => 'blog_title_size', 'section' => $a, 'type' => 'number', 'label' => __( 'Title size (px, 0 = default)', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 0, 'max' => 48 ) );
		$f[] = array( 'id' => 'blog_title_lines', 'section' => $a, 'type' => 'select', 'label' => __( 'Title lines', 'bigspy-ai-theme-wp' ), 'choices' => array( '0' => __( 'All', 'bigspy-ai-theme-wp' ), '1' => '1', '2' => '2', '3' => '3' ) );
		$f[] = array( 'id' => 'blog_cat_badge', 'section' => $a, 'type' => 'checkbox', 'label' => __( 'Category badge on the image', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'blog_date_badge', 'section' => $a, 'type' => 'checkbox', 'label' => __( 'Date badge on the image', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'blog_more_style', 'section' => $a, 'type' => 'select', 'label' => __( '“Read more” style', 'bigspy-ai-theme-wp' ), 'choices' => array( 'link' => __( 'Link with arrow', 'bigspy-ai-theme-wp' ), 'button' => __( 'Button', 'bigspy-ai-theme-wp' ), 'none' => __( 'Hidden', 'bigspy-ai-theme-wp' ) ) );

		$f[] = array( 'id' => 'bst_g_blog_meta', 'section' => $a, 'type' => 'heading', 'label' => __( 'Meta shown on cards', 'bigspy-ai-theme-wp' ) );
		foreach ( $parts as $key => $label ) {
			$f[] = array( 'id' => 'blog_meta_' . $key, 'section' => $a, 'type' => 'checkbox', 'label' => $label );
		}

		$f[] = array( 'id' => 'bst_g_blog_pages', 'section' => $a, 'type' => 'heading', 'label' => __( 'Pagination', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'blog_pagination', 'section' => $a, 'type' => 'select', 'label' => __( 'More posts', 'bigspy-ai-theme-wp' ), 'choices' => array( 'numbers' => __( 'Page numbers', 'bigspy-ai-theme-wp' ), 'prevnext' => __( 'Older / newer links', 'bigspy-ai-theme-wp' ), 'more' => __( 'Load more button', 'bigspy-ai-theme-wp' ), 'infinite' => __( 'Load while scrolling', 'bigspy-ai-theme-wp' ) ) );

		$f[] = array( 'id' => 'bst_g_single_look', 'section' => $s, 'type' => 'heading', 'label' => __( 'Post header', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'single_layout', 'section' => $s, 'type' => 'select', 'label' => __( 'Header style', 'bigspy-ai-theme-wp' ), 'choices' => array( 'standard' => __( 'Title, then image', 'bigspy-ai-theme-wp' ), 'image-first' => __( 'Image, then title', 'bigspy-ai-theme-wp' ), 'overlay' => __( 'Title over the image', 'bigspy-ai-theme-wp' ), 'centered' => __( 'Centered title, then image', 'bigspy-ai-theme-wp' ) ) );
		$f[] = array( 'id' => 'single_image_ratio', 'section' => $s, 'type' => 'select', 'label' => __( 'Featured image shape', 'bigspy-ai-theme-wp' ), 'choices' => array( 'auto' => __( 'Original', 'bigspy-ai-theme-wp' ), '16-9' => '16:9', '21-9' => '21:9', '4-3' => '4:3' ) );
		$f[] = array( 'id' => 'single_width', 'section' => $s, 'type' => 'number', 'label' => __( 'Content width (px, 0 = full)', 'bigspy-ai-theme-wp' ), 'description' => __( 'About 720–800px is easiest to read.', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 0, 'max' => 1400 ) );
		foreach ( $parts as $key => $label ) {
			/* translators: %s: meta part, e.g. Date */
			$f[] = array( 'id' => 'single_meta_' . $key, 'section' => $s, 'type' => 'checkbox', 'label' => sprintf( __( 'Show %s', 'bigspy-ai-theme-wp' ), strtolower( $label ) ) );
		}

		$f[] = array( 'id' => 'bst_g_single_body', 'section' => $s, 'type' => 'heading', 'label' => __( 'Content', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'single_toc', 'section' => $s, 'type' => 'checkbox', 'label' => __( 'Table of contents (from H2 / H3)', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'single_toc_min', 'section' => $s, 'type' => 'number', 'label' => __( 'Show it from this many headings', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 1, 'max' => 20 ) );
		$f[] = array( 'id' => 'single_dropcap', 'section' => $s, 'type' => 'checkbox', 'label' => __( 'Large first letter (drop cap)', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'single_tags', 'section' => $s, 'type' => 'checkbox', 'label' => __( 'Show tags', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'single_comments', 'section' => $s, 'type' => 'checkbox', 'label' => __( 'Show comments', 'bigspy-ai-theme-wp' ) );

		$f[] = array( 'id' => 'bst_g_single_related', 'section' => $s, 'type' => 'heading', 'label' => __( 'Related posts', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'single_related_title', 'section' => $s, 'type' => 'text', 'label' => __( 'Heading (empty = “Related posts”)', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'single_related_by', 'section' => $s, 'type' => 'select', 'label' => __( 'Pick related posts by', 'bigspy-ai-theme-wp' ), 'choices' => array( 'category' => __( 'Same category', 'bigspy-ai-theme-wp' ), 'tag' => __( 'Same tags', 'bigspy-ai-theme-wp' ), 'latest' => __( 'Latest posts', 'bigspy-ai-theme-wp' ) ) );
		$f[] = array( 'id' => 'single_related_count', 'section' => $s, 'type' => 'number', 'label' => __( 'Number of posts', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 1, 'max' => 12 ) );
		$f[] = array( 'id' => 'single_related_cols', 'section' => $s, 'type' => 'select', 'label' => __( 'Columns', 'bigspy-ai-theme-wp' ), 'choices' => array( '2' => '2', '3' => '3', '4' => '4' ) );

		return array_merge( $fields, $f );
	},
	20
);

/** Meta parts switched on for 'blog' or 'single'. */
function bst_blog_meta_selected( $where ) {
	$out = array();
	foreach ( array_keys( bst_blog_meta_parts() ) as $key ) {
		if ( bst_mod( $where . '_meta_' . $key ) ) {
			$out[] = $key;
		}
	}
	return $out;
}

/** Card options for the blog archive. */
function bst_blog_card_args() {
	$layout = bst_blog_layout();
	$args   = array(
		'show'       => bst_mod( 'blog_show_meta' ) ? bst_blog_meta_selected( 'blog' ) : array(),
		'cat_badge'  => (bool) bst_mod( 'blog_cat_badge' ),
		'date_badge' => (bool) bst_mod( 'blog_date_badge' ),
		'more_style' => bst_choice( bst_mod( 'blog_more_style' ), array( 'link', 'button', 'none' ), 'link' ),
	);
	if ( 'overlay' === $layout ) {
		$args['style'] = 'overlay';
	}
	if ( 'minimal' === $layout ) {
		$args['image'] = false;
	}
	return $args;
}

function bst_blog_layout() {
	return bst_choice( bst_mod( 'blog_layout' ), array( 'grid', 'list', 'masonry', 'featured', 'zigzag', 'classic', 'overlay', 'minimal' ), 'grid' );
}

/** Opening tag of the archive post list. */
function bst_blog_wrap_open() {
	$layout = bst_blog_layout();
	$cols   = in_array( $layout, array( 'list', 'zigzag', 'classic', 'minimal' ), true ) ? 1 : max( 1, (int) bst_mod( 'blog_columns' ) );
	$class  = array( 'bs-grid', 'bs-posts', 'bs-posts--' . $layout, 'bs-posts-hover--' . bst_choice( bst_mod( 'blog_hover' ), array( 'zoom', 'lift', 'grayscale', 'none' ), 'zoom' ) );
	if ( 'list' !== $layout && 'zigzag' !== $layout ) {
		$class[] = 'bs-posts--lay-' . $layout;
	}
	if ( 'zigzag' === $layout ) {
		$class[] = 'bs-posts--list';
	}
	if ( 'center' === bst_mod( 'blog_text_align' ) ) {
		$class[] = 'bs-posts--center';
	}
	$style = '--bs-cols:' . $cols;
	if ( (int) bst_mod( 'blog_gap' ) > 0 ) {
		$style .= ';gap:' . (int) bst_mod( 'blog_gap' ) . 'px;--bs-blog-gap:' . (int) bst_mod( 'blog_gap' ) . 'px';
	}
	if ( (int) bst_mod( 'blog_radius' ) >= 0 ) {
		$style .= ';--bs-blog-r:' . (int) bst_mod( 'blog_radius' ) . 'px';
	}
	if ( (int) bst_mod( 'blog_title_size' ) > 0 ) {
		$style .= ';--bs-blog-title:' . (int) bst_mod( 'blog_title_size' ) . 'px';
	}
	if ( (int) bst_mod( 'blog_title_lines' ) > 0 ) {
		$class[] = 'bs-posts--clamp';
		$style  .= ';--bs-post-lines:' . (int) bst_mod( 'blog_title_lines' );
	}
	echo '<div class="' . esc_attr( implode( ' ', $class ) ) . '" style="' . esc_attr( $style ) . '" data-bst-posts>';
}

/** Archive pagination in the chosen style. */
function bst_blog_pagination() {
	$mode = bst_choice( bst_mod( 'blog_pagination' ), array( 'numbers', 'prevnext', 'more', 'infinite' ), 'numbers' );
	if ( 'prevnext' === $mode ) {
		the_posts_navigation(
			array(
				'prev_text' => __( '← Older posts', 'bigspy-ai-theme-wp' ),
				'next_text' => __( 'Newer posts →', 'bigspy-ai-theme-wp' ),
				'class'     => 'bs-pagination bs-pagination--prevnext',
			)
		);
		return;
	}
	bst_pagination();
	if ( 'more' === $mode || 'infinite' === $mode ) {
		$next = get_next_posts_page_link();
		if ( $next && get_next_posts_link() ) {
			echo '<div class="bs-load-more-wrap"><button type="button" class="bs-btn bs-load-more" data-bst-more="' . esc_url( $next ) . '" data-mode="' . esc_attr( $mode ) . '" data-loading="' . esc_attr__( 'Loading…', 'bigspy-ai-theme-wp' ) . '">' . esc_html__( 'Load more posts', 'bigspy-ai-theme-wp' ) . '</button></div>';
		}
	}
}

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( ( is_home() || is_archive() || is_search() ) && in_array( bst_mod( 'blog_pagination' ), array( 'more', 'infinite' ), true ) && ! ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) ) {
			wp_enqueue_script( 'bst-blog-more', BST_URI . '/assets/js/blog-more.js', array(), bst_asset_version( 'assets/js/blog-more.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
		}
	}
);

/** Related posts query arguments from the options. */
function bst_related_query_args() {
	$args = array(
		'post_type'           => 'post',
		'posts_per_page'      => max( 1, min( 12, (int) bst_mod( 'single_related_count' ) ) ),
		'post__not_in'        => array( get_the_ID() ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);
	$by = bst_choice( bst_mod( 'single_related_by' ), array( 'category', 'tag', 'latest' ), 'category' );
	if ( 'category' === $by ) {
		$args['category__in'] = wp_get_post_categories( get_the_ID() );
	} elseif ( 'tag' === $by ) {
		$tags = wp_get_post_tags( get_the_ID(), array( 'fields' => 'ids' ) );
		if ( $tags ) {
			$args['tag__in'] = $tags;
		} else {
			$args['category__in'] = wp_get_post_categories( get_the_ID() );
		}
	}
	return $args;
}

/** Table of contents: ids on H2/H3 and a list at the top of the post. */
add_filter(
	'the_content',
	function ( $content ) {
		if ( ! bst_mod( 'single_toc' ) || ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}
		$items = array();
		$used  = array();
		$content = preg_replace_callback(
			'#<h([23])([^>]*)>(.*?)</h\1>#is',
			function ( $m ) use ( &$items, &$used ) {
				$text = trim( wp_strip_all_tags( $m[3] ) );
				if ( '' === $text ) {
					return $m[0];
				}
				if ( preg_match( '/\sid=["\']([^"\']+)["\']/', $m[2], $id ) ) {
					$slug = $id[1];
					$attr = $m[2];
				} else {
					$slug = sanitize_title( $text );
					$slug = '' === $slug ? 'section' : $slug;
					$base = $slug;
					$n    = 2;
					while ( isset( $used[ $slug ] ) ) {
						$slug = $base . '-' . $n++;
					}
					$attr = $m[2] . ' id="' . esc_attr( $slug ) . '"';
				}
				$used[ $slug ] = true;
				$items[]       = array( (int) $m[1], $slug, $text );
				return '<h' . $m[1] . $attr . '>' . $m[3] . '</h' . $m[1] . '>';
			},
			$content
		);
		if ( count( $items ) < max( 1, (int) bst_mod( 'single_toc_min' ) ) ) {
			return $content;
		}
		$list = '';
		foreach ( $items as $item ) {
			$list .= '<li class="bs-toc__item bs-toc__item--h' . $item[0] . '"><a href="#' . esc_attr( $item[1] ) . '">' . esc_html( $item[2] ) . '</a></li>';
		}
		$toc = '<details class="bs-toc" open><summary class="bs-toc__title">' . esc_html__( 'Contents', 'bigspy-ai-theme-wp' ) . '</summary><ol class="bs-toc__list">' . $list . '</ol></details>';
		return $toc . $content;
	},
	20
);
