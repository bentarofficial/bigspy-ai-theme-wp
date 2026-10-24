<?php
/**
 * Theme Builder: layouts built with blocks (like Elementor Pro Theme Builder, Astra / Neve
 * Custom Layouts) that replace or extend a part of the site where their display rules match.
 *
 * Locations: before / instead of / after the header, before / after the content, before /
 * instead of the footer, the single post template and the 404 page.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

function bst_tb_locations() {
	return array(
		'before_header'  => __( 'Before the header', 'bigspy-ai-theme-wp' ),
		'header'         => __( 'Header (replaces the theme header)', 'bigspy-ai-theme-wp' ),
		'after_header'   => __( 'After the header', 'bigspy-ai-theme-wp' ),
		'before_content' => __( 'Before the page content', 'bigspy-ai-theme-wp' ),
		'after_content'  => __( 'After the page content', 'bigspy-ai-theme-wp' ),
		'before_footer'  => __( 'Before the footer', 'bigspy-ai-theme-wp' ),
		'footer'         => __( 'Footer (replaces the theme footer)', 'bigspy-ai-theme-wp' ),
		'single'         => __( 'Single post template', 'bigspy-ai-theme-wp' ),
		'404'            => __( '404 page', 'bigspy-ai-theme-wp' ),
	);
}

function bst_tb_rule_choices() {
	$r = array(
		'entire'   => __( 'Entire site', 'bigspy-ai-theme-wp' ),
		'front'    => __( 'Front page', 'bigspy-ai-theme-wp' ),
		'blog'     => __( 'Blog posts page', 'bigspy-ai-theme-wp' ),
		'pages'    => __( 'All pages', 'bigspy-ai-theme-wp' ),
		'posts'    => __( 'All posts', 'bigspy-ai-theme-wp' ),
		'archives' => __( 'All archives (category, tag, author, date)', 'bigspy-ai-theme-wp' ),
		'search'   => __( 'Search results', 'bigspy-ai-theme-wp' ),
		'404'      => __( '404 page', 'bigspy-ai-theme-wp' ),
	);
	if ( class_exists( 'WooCommerce' ) ) {
		$r['shop']     = __( 'Shop & product categories', 'bigspy-ai-theme-wp' );
		$r['products'] = __( 'All products', 'bigspy-ai-theme-wp' );
	}
	return $r;
}

/* ---------------------------------------------------------------- Post type */
add_action(
	'init',
	function () {
		register_post_type(
			'bst_layout',
			array(
				'labels'              => array(
					'name'          => __( 'Theme Builder', 'bigspy-ai-theme-wp' ),
					'singular_name' => __( 'Layout', 'bigspy-ai-theme-wp' ),
					'add_new'       => __( 'Add layout', 'bigspy-ai-theme-wp' ),
					'add_new_item'  => __( 'Add new layout', 'bigspy-ai-theme-wp' ),
					'edit_item'     => __( 'Edit layout', 'bigspy-ai-theme-wp' ),
					'all_items'     => __( 'Theme Builder', 'bigspy-ai-theme-wp' ),
				),
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => 'bigspy-ai-theme-wp',
				'show_in_rest'        => true,
				'exclude_from_search' => true,
				'capability_type'     => 'page',
				'map_meta_cap'        => true,
				'supports'            => array( 'title', 'editor', 'revisions' ),
			)
		);
		foreach ( array( 'location', 'rules', 'exclude', 'ids', 'exclude_ids', 'cats', 'users', 'devices', 'priority' ) as $key ) {
			register_post_meta( 'bst_layout', '_bst_tb_' . $key, array( 'type' => 'string', 'single' => true, 'show_in_rest' => false, 'auth_callback' => function () { return current_user_can( 'edit_theme_options' ); } ) );
		}
	}
);

/* ---------------------------------------------------------------- Settings box */
add_action(
	'add_meta_boxes_bst_layout',
	function () {
		add_meta_box( 'bst-tb-rules', __( 'Where this layout shows', 'bigspy-ai-theme-wp' ), 'bst_tb_meta_box', 'bst_layout', 'side', 'high' );
	}
);

function bst_tb_get( $post_id, $key, $default = '' ) {
	$v = get_post_meta( $post_id, '_bst_tb_' . $key, true );
	return '' === $v ? $default : $v;
}

function bst_tb_meta_box( $post ) {
	wp_nonce_field( 'bst_tb_save', 'bst_tb_nonce' );
	$loc   = bst_tb_get( $post->ID, 'location', 'after_header' );
	$rules = array_filter( explode( ',', (string) bst_tb_get( $post->ID, 'rules', 'entire' ) ) );
	$excl  = array_filter( explode( ',', (string) bst_tb_get( $post->ID, 'exclude' ) ) );
	$cats  = array_filter( array_map( 'absint', explode( ',', (string) bst_tb_get( $post->ID, 'cats' ) ) ) );
	echo '<p><label for="bst-tb-location"><strong>' . esc_html__( 'Location', 'bigspy-ai-theme-wp' ) . '</strong></label><br><select id="bst-tb-location" name="bst_tb[location]" style="width:100%">';
	foreach ( bst_tb_locations() as $k => $label ) {
		echo '<option value="' . esc_attr( $k ) . '" ' . selected( $loc, $k, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select></p>';
	echo '<p class="description">' . esc_html__( 'For the single post template, add a Shortcode block with [bst_post_title], [bst_featured_image], [bst_post_meta], [bst_post_content], [bst_author_box], [bst_related_posts] or [bst_post_comments].', 'bigspy-ai-theme-wp' ) . '</p>';
	$boxes = function ( $name, $checked ) {
		foreach ( bst_tb_rule_choices() as $k => $label ) {
			echo '<label style="display:block"><input type="checkbox" name="bst_tb[' . esc_attr( $name ) . '][]" value="' . esc_attr( $k ) . '" ' . checked( in_array( $k, $checked, true ), true, false ) . '> ' . esc_html( $label ) . '</label>';
		}
	};
	echo '<p><strong>' . esc_html__( 'Show on', 'bigspy-ai-theme-wp' ) . '</strong></p>';
	$boxes( 'rules', $rules );
	echo '<p><label>' . esc_html__( 'Also on these post / page IDs (comma separated)', 'bigspy-ai-theme-wp' ) . '<input type="text" name="bst_tb[ids]" value="' . esc_attr( (string) bst_tb_get( $post->ID, 'ids' ) ) . '" style="width:100%"></label></p>';
	$terms = get_categories( array( 'hide_empty' => false ) );
	if ( $terms ) {
		echo '<p><strong>' . esc_html__( 'Posts in these categories (and their archives)', 'bigspy-ai-theme-wp' ) . '</strong></p><div style="max-height:140px;overflow:auto;border:1px solid #dcdcde;padding:6px">';
		foreach ( $terms as $t ) {
			echo '<label style="display:block"><input type="checkbox" name="bst_tb[cats][]" value="' . (int) $t->term_id . '" ' . checked( in_array( (int) $t->term_id, $cats, true ), true, false ) . '> ' . esc_html( $t->name ) . '</label>';
		}
		echo '</div>';
	}
	echo '<details style="margin-top:10px"><summary><strong>' . esc_html__( 'Do not show on', 'bigspy-ai-theme-wp' ) . '</strong></summary>';
	$boxes( 'exclude', $excl );
	echo '<p><label>' . esc_html__( 'Post / page IDs to exclude', 'bigspy-ai-theme-wp' ) . '<input type="text" name="bst_tb[exclude_ids]" value="' . esc_attr( (string) bst_tb_get( $post->ID, 'exclude_ids' ) ) . '" style="width:100%"></label></p></details>';
	$sel = function ( $name, $value, $choices ) {
		echo '<select name="bst_tb[' . esc_attr( $name ) . ']" style="width:100%">';
		foreach ( $choices as $k => $label ) {
			echo '<option value="' . esc_attr( $k ) . '" ' . selected( $value, $k, false ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select>';
	};
	echo '<p><strong>' . esc_html__( 'Visitors', 'bigspy-ai-theme-wp' ) . '</strong><br>';
	$sel( 'users', bst_tb_get( $post->ID, 'users', 'all' ), array( 'all' => __( 'Everyone', 'bigspy-ai-theme-wp' ), 'in' => __( 'Logged-in users only', 'bigspy-ai-theme-wp' ), 'out' => __( 'Logged-out visitors only', 'bigspy-ai-theme-wp' ) ) );
	echo '</p><p><strong>' . esc_html__( 'Devices', 'bigspy-ai-theme-wp' ) . '</strong><br>';
	$sel( 'devices', bst_tb_get( $post->ID, 'devices', 'all' ), array( 'all' => __( 'Desktop and mobile', 'bigspy-ai-theme-wp' ), 'desktop' => __( 'Desktop only', 'bigspy-ai-theme-wp' ), 'mobile' => __( 'Mobile only', 'bigspy-ai-theme-wp' ) ) );
	echo '</p><p><label><strong>' . esc_html__( 'Priority', 'bigspy-ai-theme-wp' ) . '</strong> <input type="number" name="bst_tb[priority]" value="' . esc_attr( (string) bst_tb_get( $post->ID, 'priority', '10' ) ) . '" min="0" max="999" style="width:80px"></label><br><span class="description">' . esc_html__( 'Lower numbers run first. When two layouts replace the same part, the lowest number wins.', 'bigspy-ai-theme-wp' ) . '</span></p>';
}

add_action(
	'save_post_bst_layout',
	function ( $post_id ) {
		if ( ! isset( $_POST['bst_tb_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['bst_tb_nonce'] ) ), 'bst_tb_save' ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$in    = isset( $_POST['bst_tb'] ) && is_array( $_POST['bst_tb'] ) ? wp_unslash( $_POST['bst_tb'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below.
		$valid = array_keys( bst_tb_rule_choices() );
		$list  = function ( $v ) use ( $valid ) {
			return implode( ',', array_intersect( array_map( 'sanitize_key', (array) $v ), $valid ) );
		};
		$ids   = function ( $v ) {
			return implode( ',', array_filter( array_map( 'absint', explode( ',', (string) $v ) ) ) );
		};
		$data = array(
			'location'    => array_key_exists( (string) ( $in['location'] ?? '' ), bst_tb_locations() ) ? (string) $in['location'] : 'after_header',
			'rules'       => $list( $in['rules'] ?? array() ),
			'exclude'     => $list( $in['exclude'] ?? array() ),
			'ids'         => $ids( $in['ids'] ?? '' ),
			'exclude_ids' => $ids( $in['exclude_ids'] ?? '' ),
			'cats'        => implode( ',', array_filter( array_map( 'absint', (array) ( $in['cats'] ?? array() ) ) ) ),
			'users'       => in_array( $in['users'] ?? '', array( 'all', 'in', 'out' ), true ) ? $in['users'] : 'all',
			'devices'     => in_array( $in['devices'] ?? '', array( 'all', 'desktop', 'mobile' ), true ) ? $in['devices'] : 'all',
			'priority'    => (string) min( 999, absint( $in['priority'] ?? 10 ) ),
		);
		foreach ( $data as $key => $value ) {
			update_post_meta( $post_id, '_bst_tb_' . $key, $value );
		}
		delete_transient( 'bst_tb_layouts' );
	}
);
add_action( 'deleted_post', function () { delete_transient( 'bst_tb_layouts' ); } );
add_action( 'transition_post_status', function ( $new, $old, $post ) { if ( 'bst_layout' === $post->post_type ) { delete_transient( 'bst_tb_layouts' ); } }, 10, 3 );

/* Admin list: show location and rules. */
add_filter(
	'manage_bst_layout_posts_columns',
	function ( $cols ) {
		return array_slice( $cols, 0, 2, true ) + array( 'bst_tb_location' => __( 'Location', 'bigspy-ai-theme-wp' ), 'bst_tb_rules' => __( 'Show on', 'bigspy-ai-theme-wp' ) ) + array_slice( $cols, 2, null, true );
	}
);
add_action(
	'manage_bst_layout_posts_custom_column',
	function ( $col, $post_id ) {
		if ( 'bst_tb_location' === $col ) {
			$locs = bst_tb_locations();
			echo esc_html( $locs[ bst_tb_get( $post_id, 'location', 'after_header' ) ] ?? '' );
		} elseif ( 'bst_tb_rules' === $col ) {
			$names = bst_tb_rule_choices();
			$out   = array();
			foreach ( array_filter( explode( ',', (string) bst_tb_get( $post_id, 'rules' ) ) ) as $r ) {
				$out[] = $names[ $r ] ?? $r;
			}
			if ( bst_tb_get( $post_id, 'ids' ) ) {
				$out[] = 'ID ' . bst_tb_get( $post_id, 'ids' );
			}
			if ( bst_tb_get( $post_id, 'cats' ) ) {
				$out[] = __( 'Selected categories', 'bigspy-ai-theme-wp' );
			}
			echo esc_html( $out ? implode( ', ', $out ) : '—' );
		}
	},
	10,
	2
);

/* ---------------------------------------------------------------- Matching */
function bst_tb_all_layouts() {
	$layouts = get_transient( 'bst_tb_layouts' );
	if ( false === $layouts ) {
		$layouts = array();
		$posts   = get_posts( array( 'post_type' => 'bst_layout', 'post_status' => 'publish', 'numberposts' => 200, 'no_found_rows' => true ) );
		foreach ( $posts as $p ) {
			$m = array();
			foreach ( array( 'location', 'rules', 'exclude', 'ids', 'exclude_ids', 'cats', 'users', 'devices', 'priority' ) as $k ) {
				$m[ $k ] = (string) get_post_meta( $p->ID, '_bst_tb_' . $k, true );
			}
			$m['id']   = $p->ID;
			$layouts[] = $m;
		}
		usort( $layouts, function ( $a, $b ) { return (int) $a['priority'] - (int) $b['priority']; } );
		set_transient( 'bst_tb_layouts', $layouts, DAY_IN_SECONDS );
	}
	return $layouts;
}

function bst_tb_rule_hit( $rule ) {
	switch ( $rule ) {
		case 'entire':
			return true;
		case 'front':
			return is_front_page();
		case 'blog':
			return is_home();
		case 'pages':
			return is_page();
		case 'posts':
			return is_singular( 'post' );
		case 'archives':
			return is_archive() && ! ( function_exists( 'is_woocommerce' ) && is_woocommerce() );
		case 'search':
			return is_search();
		case '404':
			return is_404();
		case 'shop':
			return function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() );
		case 'products':
			return function_exists( 'is_product' ) && is_product();
	}
	return false;
}

function bst_tb_matches( $m ) {
	if ( 'in' === $m['users'] && ! is_user_logged_in() ) {
		return false;
	}
	if ( 'out' === $m['users'] && is_user_logged_in() ) {
		return false;
	}
	$id   = is_singular() ? get_queried_object_id() : 0;
	$excl = array_filter( explode( ',', $m['exclude'] ) );
	foreach ( $excl as $r ) {
		if ( bst_tb_rule_hit( $r ) ) {
			return false;
		}
	}
	if ( $id && in_array( $id, array_map( 'absint', explode( ',', $m['exclude_ids'] ) ), true ) ) {
		return false;
	}
	foreach ( array_filter( explode( ',', $m['rules'] ) ) as $r ) {
		if ( bst_tb_rule_hit( $r ) ) {
			return true;
		}
	}
	if ( $id && in_array( $id, array_map( 'absint', explode( ',', $m['ids'] ) ), true ) ) {
		return true;
	}
	$cats = array_filter( array_map( 'absint', explode( ',', $m['cats'] ) ) );
	if ( $cats && ( ( is_singular( 'post' ) && has_category( $cats, $id ) ) || is_category( $cats ) ) ) {
		return true;
	}
	return false;
}

/** Matching layouts for a location (lowest priority first). */
function bst_tb_for( $location ) {
	if ( is_admin() ) {
		return array();
	}
	$out = array();
	foreach ( bst_tb_all_layouts() as $m ) {
		if ( $m['location'] === $location && bst_tb_matches( $m ) ) {
			$out[] = $m;
		}
	}
	return $out;
}

function bst_tb_html( $m ) {
	static $depth = 0;
	$post = get_post( (int) $m['id'] );
	if ( ! $post || $depth > 2 ) {
		return '';
	}
	++$depth;
	$html = do_shortcode( do_blocks( $post->post_content ) );
	--$depth;
	$class = 'bs-tb bs-tb--' . sanitize_html_class( $m['location'] ) . ( 'desktop' === $m['devices'] ? ' bs-hide-mobile' : '' ) . ( 'mobile' === $m['devices'] ? ' bs-hide-desktop' : '' );
	return '<div class="' . esc_attr( $class ) . '" data-layout="' . (int) $post->ID . '">' . $html . '</div>';
}

/**
 * Print the layouts for a location. For replacing locations only the first match prints.
 *
 * @return bool True when something was printed.
 */
function bst_tb_render( $location, $single = false ) {
	$layouts = bst_tb_for( $location );
	if ( ! $layouts ) {
		return false;
	}
	if ( $single ) {
		$layouts = array( $layouts[0] );
	}
	foreach ( $layouts as $m ) {
		echo bst_tb_html( $m ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block content rendered by core.
	}
	return true;
}

/* ---------------------------------------------------------------- Single-post shortcodes */
add_action(
	'init',
	function () {
		$codes = array(
			'bst_post_title'     => function ( $atts ) {
				$atts = shortcode_atts( array( 'tag' => 'h1' ), $atts );
				$tag  = in_array( $atts['tag'], array( 'h1', 'h2', 'h3', 'p', 'div' ), true ) ? $atts['tag'] : 'h1';
				return '<' . $tag . ' class="bs-entry__title">' . esc_html( get_the_title() ) . '</' . $tag . '>';
			},
			'bst_post_content'   => function () {
				static $busy = false;
				if ( $busy ) {
					return '';
				}
				$busy = true;
				$html = apply_filters( 'the_content', get_the_content() ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- core hook.
				$busy = false;
				return '<div class="bs-entry__content bs-content">' . $html . '</div>';
			},
			'bst_post_excerpt'   => function () {
				return '<div class="bs-entry__excerpt">' . wp_kses_post( wpautop( get_the_excerpt() ) ) . '</div>';
			},
			'bst_featured_image' => function ( $atts ) {
				$atts = shortcode_atts( array( 'size' => 'large' ), $atts );
				return has_post_thumbnail() ? '<figure class="bs-entry__thumb">' . get_the_post_thumbnail( null, sanitize_key( $atts['size'] ) ) . '</figure>' : '';
			},
			'bst_post_meta'      => function () {
				ob_start();
				bst_post_meta( function_exists( 'bst_blog_meta_selected' ) ? bst_blog_meta_selected( 'single' ) : array( 'category', 'date', 'author' ) );
				return ob_get_clean();
			},
			'bst_breadcrumbs'    => function () {
				ob_start();
				bst_breadcrumbs();
				return ob_get_clean();
			},
			'bst_author_box'     => function () {
				ob_start();
				bst_author_box();
				return ob_get_clean();
			},
			'bst_related_posts'  => function () {
				ob_start();
				bst_related_posts();
				return ob_get_clean();
			},
			'bst_post_comments'  => function () {
				if ( ! comments_open() && ! get_comments_number() ) {
					return '';
				}
				ob_start();
				comments_template();
				return ob_get_clean();
			},
		);
		foreach ( $codes as $tag => $cb ) {
			add_shortcode( $tag, $cb );
		}
	}
);
