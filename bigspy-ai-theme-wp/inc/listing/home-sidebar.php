<?php
/**
 * BIGSPY AI List: optional sidebar on the static front page (G-90).
 *
 * Off by default. In Customizer → Layout → Homepage sidebar the user turns it on and
 * picks the top-level block it starts after and the block it ends before; only the
 * blocks in between sit beside the "Homepage sidebar" widget area, everything else
 * stays full width. Replaces the old theme's Homepage SEO Full-width Divider block:
 * with no end block chosen, a bigspy/home-seo-divider in the content still ends it.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_defaults',
	function ( $defaults ) {
		return $defaults + array(
			'home_sidebar'       => false,
			'home_sidebar_start' => 0,
			'home_sidebar_end'   => 0,
		);
	}
);

add_action(
	'widgets_init',
	function () {
		register_sidebar(
			array(
				'name'          => __( 'Homepage sidebar', 'bigspy-ai-theme-wp' ),
				'id'            => 'sidebar-home',
				'description'   => __( 'Shown beside part of the homepage when it is turned on in Customizer → Layout → Homepage sidebar.', 'bigspy-ai-theme-wp' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);
	},
	20
);

/** Top-level blocks of the front page, skipping whitespace between blocks. */
function bst_home_sidebar_blocks( $content = null ) {
	if ( null === $content ) {
		$page    = (int) get_option( 'page_on_front' );
		$content = ( 'page' === get_option( 'show_on_front' ) && $page ) ? (string) get_post_field( 'post_content', $page ) : '';
	}
	return array_values( array_filter( parse_blocks( $content ), function ( $b ) { return ! empty( $b['blockName'] ); } ) );
}

/** Dropdown choices "N. Block – text" for the front page blocks. */
function bst_home_sidebar_choices( $none ) {
	$choices = array( 0 => $none );
	$types   = WP_Block_Type_Registry::get_instance();
	foreach ( bst_home_sidebar_blocks() as $i => $block ) {
		$type  = $types->get_registered( $block['blockName'] );
		$label = $type && $type->title ? $type->title : $block['blockName'];
		$text  = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( (string) $block['innerHTML'] ) ) );
		if ( '' === $text ) {
			$text = wp_strip_all_tags( (string) ( $block['attrs']['title'] ?? $block['attrs']['heading'] ?? $block['attrs']['text'] ?? '' ) );
		}
		$choices[ $i + 1 ] = ( $i + 1 ) . '. ' . $label . ( '' !== $text ? ' – ' . wp_html_excerpt( $text, 40, '…' ) : '' );
	}
	return $choices;
}

add_filter(
	'bst_customizer_sections',
	function ( $sections ) {
		$sections['bst_home_sidebar'] = array( 'title' => __( 'Homepage sidebar', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_layout', 'priority' => 3 );
		return $sections;
	}
);

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$s = 'bst_home_sidebar';
		if ( ! is_customize_preview() && ! is_admin() ) {
			return $fields;
		}
		$fields[] = array( 'id' => 'bst_g_home_sidebar', 'section' => $s, 'type' => 'heading', 'label' => __( 'Sidebar on part of the homepage', 'bigspy-ai-theme-wp' ), 'description' => __( 'Only for a static homepage (Settings → Reading). Pick the block the sidebar starts after and the block it ends before; blocks outside that range stay full width. Fill the sidebar in Widgets → Homepage sidebar. Block numbers follow the homepage order, so check the choice again after moving blocks.', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'home_sidebar', 'section' => $s, 'type' => 'checkbox', 'label' => __( 'Show a sidebar on the homepage', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'home_sidebar_start', 'section' => $s, 'type' => 'select', 'label' => __( 'Start after block', 'bigspy-ai-theme-wp' ), 'choices' => bst_home_sidebar_choices( __( '— From the first block —', 'bigspy-ai-theme-wp' ) ) );
		$fields[] = array( 'id' => 'home_sidebar_end', 'section' => $s, 'type' => 'select', 'label' => __( 'End before block', 'bigspy-ai-theme-wp' ), 'choices' => bst_home_sidebar_choices( __( '— To the last block (or the Homepage SEO divider) —', 'bigspy-ai-theme-wp' ) ) );
		return $fields;
	}
);

/**
 * Split the front page content into before / beside-sidebar / after. Runs before
 * do_blocks (priority 9); the sidebar is put in after rendering so widget output is
 * not parsed as content.
 */
function bst_home_sidebar_split( $content ) {
	if ( ! bst_mod( 'home_sidebar' ) || ! is_front_page() || ! is_page() || ! in_the_loop() || ! is_main_query() || (int) get_the_ID() !== (int) get_option( 'page_on_front' ) ) {
		return $content;
	}
	return bst_home_sidebar_wrap( $content, (int) bst_mod( 'home_sidebar_start' ), (int) bst_mod( 'home_sidebar_end' ) );
}
add_filter( 'the_content', 'bst_home_sidebar_split', 8 );

/** Wrap blocks after block $start and before block $end (1-based, 0 = none) with the sidebar layout. */
function bst_home_sidebar_wrap( $content, $start, $end ) {
	$blocks = bst_home_sidebar_blocks( $content );
	$count  = count( $blocks );
	if ( ! $count ) {
		return $content;
	}
	$start = max( 0, min( $count, (int) $start ) ); // Blocks [0, $start) come before.
	$end   = (int) $end;                             // Blocks from index $end-1 come after.
	if ( $end < 1 || $end > $count ) {
		$end = $count + 1;
		foreach ( $blocks as $i => $block ) {
			if ( $i >= $start && 'bigspy/home-seo-divider' === $block['blockName'] ) {
				$end = $i + 1;
				break;
			}
		}
	}
	$stop = $end - 1;
	if ( $stop <= $start ) {
		return $content; // Empty or reversed range: nothing to put beside the sidebar.
	}
	$join = function ( $list ) {
		return implode( "\n\n", array_map( 'serialize_block', $list ) );
	};
	return $join( array_slice( $blocks, 0, $start ) )
		. "\n\n<div class=\"bs-container bs-layout bs-layout--right bs-home-sb\"><div class=\"bs-layout__content\">\n\n"
		. $join( array_slice( $blocks, $start, $stop - $start ) )
		. "\n\n</div><!-- bst-home-sidebar --></div>\n\n"
		. $join( array_slice( $blocks, $stop ) );
}

add_filter(
	'the_content',
	function ( $content ) {
		if ( false === strpos( $content, '<!-- bst-home-sidebar -->' ) ) {
			return $content;
		}
		ob_start();
		echo '<aside class="bs-layout__sidebar widget-area" aria-label="' . esc_attr__( 'Homepage sidebar', 'bigspy-ai-theme-wp' ) . '"' . bst_sidebar_edit_attr( 'sidebar-home', 'bst_home_sidebar' ) . '>';
		if ( is_active_sidebar( 'sidebar-home' ) ) {
			dynamic_sidebar( 'sidebar-home' );
		} elseif ( current_user_can( 'edit_theme_options' ) ) {
			echo '<p class="bs-header-hint"><a href="' . esc_url( admin_url( 'widgets.php' ) ) . '">' . esc_html__( 'Add widgets to the Homepage sidebar', 'bigspy-ai-theme-wp' ) . '</a></p>';
		}
		echo '</aside>';
		return str_replace( '<!-- bst-home-sidebar -->', ob_get_clean(), $content );
	},
	99
);

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( bst_mod( 'home_sidebar' ) && is_front_page() ) {
			wp_add_inline_style( 'bst-main', '.bs-home-sb{padding-top:0;padding-bottom:0;margin-block:var(--bs-home-sb-space,40px)}.bs-home-sb .bs-container{max-width:100%;padding-inline:0}.bs-home-sb .alignfull,.bs-home-sb .alignwide{width:auto;max-width:100%;margin-inline:0}' );
		}
	},
	31
);

/**
 * Switching from BIGSPY AI List: WordPress maps widget areas by guesswork, so the old
 * "Listing Sidebar — Homepage" can land in the Blog sidebar. Move each old area into
 * its counterpart here, only where that counterpart is still empty.
 */
function bst_listing_map_old_widgets() {
	$mods = get_option( 'theme_mods_bigspy-ai-list', array() );
	$old  = isset( $mods['sidebars_widgets']['data'] ) && is_array( $mods['sidebars_widgets']['data'] ) ? $mods['sidebars_widgets']['data'] : array();
	if ( ! $old || get_option( 'bst_listing_widgets_mapped' ) ) {
		return;
	}
	$map = array(
		'listing-sidebar'         => 'sidebar-home',
		'secondary-pages-sidebar' => 'sidebar-blog',
	);
	for ( $i = 1; $i <= 4; $i++ ) {
		$map[ 'footer-' . $i ] = 'footer-1-' . $i;
	}
	$now   = wp_get_sidebars_widgets();
	$moved = array();
	foreach ( $map as $from => $to ) {
		if ( empty( $old[ $from ] ) || ! empty( $now[ $to ] ) ) {
			continue;
		}
		$ids = array_values( (array) $old[ $from ] );
		// Take the widgets out of wherever WordPress guessed they belong.
		foreach ( $now as $area => $list ) {
			if ( is_array( $list ) && 'wp_inactive_widgets' !== $area ) {
				$now[ $area ] = array_values( array_diff( $list, $ids ) );
			}
		}
		$now[ $to ] = $ids;
		$moved[]    = $to;
	}
	update_option( 'bst_listing_widgets_mapped', $moved ? $moved : 1, false );
	if ( $moved ) {
		wp_set_sidebars_widgets( $now );
	}
}
add_action( 'after_switch_theme', 'bst_listing_map_old_widgets', 30 );
