<?php
/**
 * G-208: more blog options.
 *
 * Pagination style (pictures), search results layout/columns/sidebar, author
 * page header, share buttons (networks and order, style, position), comment
 * style (avatar, website field) and a skeleton while "Load more" fetches posts.
 * Defaults keep the old look.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$a   = 'bst_blog_archive';
		$s   = 'bst_blog_single';
		$inh = array( 'inherit' => __( 'Same as blog page', 'bigspy-ai-theme-wp' ) );
		$add = array(
			array( 'id' => 'pagination_style', 'section' => $a, 'type' => 'image_radio', 'label' => __( 'Page number style', 'bigspy-ai-theme-wp' ), 'choices' => array(
				'default'   => __( 'Boxed', 'bigspy-ai-theme-wp' ),
				'round'     => __( 'Round', 'bigspy-ai-theme-wp' ),
				'square'    => __( 'Filled squares', 'bigspy-ai-theme-wp' ),
				'underline' => __( 'Underline', 'bigspy-ai-theme-wp' ),
			) ),
			array( 'id' => 'bst_g_search_page', 'section' => $a, 'type' => 'heading', 'label' => __( 'Search results page', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'search_layout', 'section' => $a, 'type' => 'image_radio', 'label' => __( 'Layout', 'bigspy-ai-theme-wp' ), 'choices' => $inh + bst_blog_layout_choices() ),
			array( 'id' => 'search_columns', 'section' => $a, 'type' => 'select', 'label' => __( 'Columns', 'bigspy-ai-theme-wp' ), 'choices' => $inh + array( '1' => '1', '2' => '2', '3' => '3', '4' => '4' ) ),
			array( 'id' => 'search_sidebar', 'section' => $a, 'type' => 'image_radio', 'label' => __( 'Sidebar', 'bigspy-ai-theme-wp' ), 'choices' => $inh + array(
				'right' => __( 'Right sidebar', 'bigspy-ai-theme-wp' ),
				'left'  => __( 'Left sidebar', 'bigspy-ai-theme-wp' ),
				'none'  => __( 'No sidebar', 'bigspy-ai-theme-wp' ),
			) ),
			array( 'id' => 'author_header', 'section' => $a, 'type' => 'checkbox', 'label' => __( 'Author pages: show the author card (photo, bio, post count)', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'bst_g_share', 'section' => $s, 'type' => 'heading', 'label' => __( 'Share buttons', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'share_networks', 'section' => $s, 'type' => 'text', 'label' => __( 'Networks and order', 'bigspy-ai-theme-wp' ), 'description' => __( 'Comma separated: facebook, x, linkedin, pinterest, telegram, whatsapp, zalo, mail, copy. Empty = default.', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'share_style', 'section' => $s, 'type' => 'select', 'label' => __( 'Button style', 'bigspy-ai-theme-wp' ), 'choices' => array(
				'default' => __( 'Outline icons', 'bigspy-ai-theme-wp' ),
				'round'   => __( 'Round icons', 'bigspy-ai-theme-wp' ),
				'brand'   => __( 'Brand colors', 'bigspy-ai-theme-wp' ),
			) ),
			array( 'id' => 'share_position', 'section' => $s, 'type' => 'select', 'label' => __( 'Position', 'bigspy-ai-theme-wp' ), 'choices' => array(
				'bottom' => __( 'Below the post', 'bigspy-ai-theme-wp' ),
				'top'    => __( 'Above the post', 'bigspy-ai-theme-wp' ),
				'both'   => __( 'Above and below', 'bigspy-ai-theme-wp' ),
				'float'  => __( 'Floating at the side (desktop)', 'bigspy-ai-theme-wp' ),
			) ),
			array( 'id' => 'bst_g_comments', 'section' => $s, 'type' => 'heading', 'label' => __( 'Comments', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'comment_style', 'section' => $s, 'type' => 'select', 'label' => __( 'Comment style', 'bigspy-ai-theme-wp' ), 'choices' => array(
				'default' => __( 'Theme default', 'bigspy-ai-theme-wp' ),
				'card'    => __( 'Cards', 'bigspy-ai-theme-wp' ),
				'minimal' => __( 'Minimal lines', 'bigspy-ai-theme-wp' ),
			) ),
			array( 'id' => 'comment_hide_avatar', 'section' => $s, 'type' => 'checkbox', 'label' => __( 'Hide avatars in comments', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'comment_hide_url', 'section' => $s, 'type' => 'checkbox', 'label' => __( 'Remove the Website field from the comment form', 'bigspy-ai-theme-wp' ), 'description' => __( 'Cuts link spam.', 'bigspy-ai-theme-wp' ) ),
		);
		return array_merge( $fields, $add );
	}
);

add_filter(
	'bst_defaults',
	function ( $d ) {
		$d['pagination_style'] = 'default';
		$d['search_layout']    = 'inherit';
		$d['search_columns']   = 'inherit';
		$d['search_sidebar']   = 'inherit';
		$d['share_style']      = 'default';
		$d['share_position']   = 'bottom';
		$d['comment_style']    = 'default';
		return $d;
	}
);

add_filter(
	'bst_layout_pictures',
	function ( $all ) {
		$all['pagination']    = array(
			'default'   => array( array( 8, 14, 10, 10, 0 ), array( 21, 14, 10, 10, 1 ), array( 34, 14, 10, 10, 0 ) ),
			'round'     => array( array( 10, 15, 8, 8, 0 ), array( 24, 15, 8, 8, 1 ), array( 38, 15, 8, 8, 0 ) ),
			'square'    => array( array( 8, 14, 10, 10, 1 ), array( 21, 14, 10, 10, 1 ), array( 34, 14, 10, 10, 1 ) ),
			'underline' => array( array( 8, 24, 10, 2, 0 ), array( 21, 24, 10, 3, 1 ), array( 34, 24, 10, 2, 0 ) ),
		);
		$all['search_blog']   = $all['blog'];
		$all['search_side']   = $all['sidebar'];
		return $all;
	}
);
add_filter(
	'bst_layout_picker_map',
	function ( $map ) {
		$map['pagination_style'] = 'pagination';
		$map['search_layout']    = 'search_blog';
		$map['search_sidebar']   = 'search_side';
		return $map;
	}
);

/* ---------------------------------------------------------------- Search page */

foreach ( array( 'blog_layout' => 'search_layout', 'blog_columns' => 'search_columns', 'blog_sidebar' => 'search_sidebar' ) as $bst_mod_key => $bst_search_key ) {
	add_filter(
		'theme_mod_' . $bst_mod_key,
		function ( $value ) use ( $bst_search_key ) {
			if ( is_admin() || ! is_search() || ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) ) {
				return $value;
			}
			$own = (string) get_theme_mod( $bst_search_key, 'inherit' );
			return ( '' !== $own && 'inherit' !== $own ) ? $own : $value;
		},
		20
	);
}
unset( $bst_mod_key, $bst_search_key );

/* ---------------------------------------------------------------- Author card */

function bst_author_header() {
	if ( ! is_author() || ! get_theme_mod( 'author_header', false ) || is_paged() ) {
		return;
	}
	$user = get_queried_object();
	if ( ! $user instanceof WP_User ) {
		return;
	}
	$bio   = get_the_author_meta( 'description', $user->ID );
	$count = count_user_posts( $user->ID, 'post', true );
	echo '<section class="bs-author-header">' . get_avatar( $user->ID, 96, '', $user->display_name, array( 'class' => 'bs-author-header__avatar' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '<div class="bs-author-header__body"><div class="bs-author-header__name">' . esc_html( $user->display_name ) . '</div>';
	/* translators: %s: number of posts. */
	echo '<div class="bs-meta bs-author-header__count">' . esc_html( sprintf( _n( '%s post', '%s posts', $count, 'bigspy-ai-theme-wp' ), number_format_i18n( $count ) ) ) . '</div>';
	if ( $bio ) {
		echo '<div class="bs-author-header__bio">' . wp_kses_post( wpautop( $bio ) ) . '</div>';
	}
	if ( $user->user_url ) {
		echo '<a class="bs-author-header__url" href="' . esc_url( $user->user_url ) . '" rel="me noopener" target="_blank">' . esc_html( wp_parse_url( $user->user_url, PHP_URL_HOST ) ) . '</a>';
	}
	echo '</div></section>';
}
add_action( 'bst_before_posts_loop', 'bst_author_header', 5 );

/* ---------------------------------------------------------------- Share */

/** Share links in the chosen order: key => URL ('copy' => permalink). */
function bst_share_links() {
	$url   = rawurlencode( get_permalink() );
	$title = rawurlencode( get_the_title() );
	$all   = array(
		'facebook'  => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
		'x'         => 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title,
		'linkedin'  => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url,
		'pinterest' => 'https://pinterest.com/pin/create/button/?url=' . $url . '&description=' . $title,
		'telegram'  => 'https://t.me/share/url?url=' . $url . '&text=' . $title,
		'whatsapp'  => 'https://wa.me/?text=' . $title . '%20' . $url,
		'zalo'      => 'https://sp.zalo.me/share_inline?d=' . rawurlencode( base64_encode( wp_json_encode( array( 'url' => get_permalink() ) ) ) ),
		'mail'      => 'mailto:?subject=' . $title . '&body=' . $url,
		'copy'      => get_permalink(),
	);
	$want = array_filter( array_map( 'sanitize_key', explode( ',', (string) get_theme_mod( 'share_networks', '' ) ) ) );
	if ( ! $want ) {
		$want = array( 'facebook', 'x', 'linkedin', 'pinterest', 'mail', 'copy' );
	}
	$out = array();
	foreach ( $want as $key ) {
		if ( isset( $all[ $key ] ) ) {
			$out[ $key ] = $all[ $key ];
		}
	}
	return $out;
}

function bst_share_position() {
	return bst_choice( get_theme_mod( 'share_position', 'bottom' ), array( 'bottom', 'top', 'both', 'float' ), 'bottom' );
}

/** Share buttons placed above the post content. */
add_filter(
	'the_content',
	function ( $content ) {
		if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() || ! in_array( bst_share_position(), array( 'top', 'both' ), true ) ) {
			return $content;
		}
		ob_start();
		bst_share_buttons( 'top' );
		return ob_get_clean() . $content;
	},
	8
);

/** Floating share bar. */
add_action(
	'wp_footer',
	function () {
		if ( is_singular( 'post' ) && 'float' === bst_share_position() ) {
			bst_share_buttons( 'float' );
		}
	}
);

/* ---------------------------------------------------------------- Comments */

add_filter(
	'comment_form_default_fields',
	function ( $fields ) {
		if ( get_theme_mod( 'comment_hide_url', false ) ) {
			unset( $fields['url'] );
		}
		return $fields;
	}
);

add_filter(
	'body_class',
	function ( $classes ) {
		if ( is_singular() ) {
			$style = bst_choice( get_theme_mod( 'comment_style', 'default' ), array( 'default', 'card', 'minimal' ), 'default' );
			if ( 'default' !== $style ) {
				$classes[] = 'bs-comments--' . $style;
			}
			if ( get_theme_mod( 'comment_hide_avatar', false ) ) {
				$classes[] = 'bs-comments--no-avatar';
			}
		}
		if ( is_home() || is_archive() || is_search() ) {
			$p = bst_choice( get_theme_mod( 'pagination_style', 'default' ), array( 'default', 'round', 'square', 'underline' ), 'default' );
			if ( 'default' !== $p ) {
				$classes[] = 'bs-pager--' . $p;
			}
		}
		return $classes;
	}
);
