<?php
/**
 * Sidebar & widgets options (Flatsome-style): own widget areas for single posts
 * and single products, sticky / style / widget title / mobile behaviour.
 *
 * Positions per view (none / left / right) stay in their own sections.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

function bst_sidebar_extra_areas() {
	$common = array(
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	);
	register_sidebar( array_merge( $common, array( 'name' => __( 'Single post sidebar', 'bigspy-ai-theme-wp' ), 'id' => 'sidebar-single', 'description' => __( 'Shown beside single blog posts. Leave empty to use the Blog sidebar.', 'bigspy-ai-theme-wp' ) ) ) );
	register_sidebar( array_merge( $common, array( 'name' => __( 'Category & archive sidebar', 'bigspy-ai-theme-wp' ), 'id' => 'sidebar-archive', 'description' => __( 'Shown on categories, tags and search. Leave empty to use the Blog sidebar.', 'bigspy-ai-theme-wp' ) ) ) );
	if ( class_exists( 'WooCommerce' ) ) {
		register_sidebar( array_merge( $common, array( 'name' => __( 'Single product sidebar', 'bigspy-ai-theme-wp' ), 'id' => 'sidebar-product', 'description' => __( 'Shown beside single products. Leave empty to use the Shop sidebar.', 'bigspy-ai-theme-wp' ) ) ) );
	}
}
add_action( 'widgets_init', 'bst_sidebar_extra_areas', 20 );

add_filter(
	'bst_defaults',
	function ( $defaults ) {
		return $defaults + array(
			'sidebar_sticky'       => true,
			'sidebar_style'        => 'plain',
			'sidebar_title_style'  => 'plain',
			'sidebar_widget_gap'   => 32,
			'sidebar_gap'          => 40,
			'sidebar_mobile'       => 'below',
			'sidebar_font_size'    => 0,
		);
	}
);

add_filter(
	'bst_customizer_sections',
	function ( $sections ) {
		$sections['bst_sidebar'] = array( 'title' => __( 'Sidebar & widgets', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_layout', 'priority' => 2 );
		return $sections;
	}
);

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$s        = 'bst_sidebar';
		$widgets  = esc_url( admin_url( 'widgets.php' ) );
		$fields[] = array( 'id' => 'bst_g_sidebar_where', 'section' => $s, 'type' => 'heading', 'label' => __( 'Where sidebars show', 'bigspy-ai-theme-wp' ), 'description' => sprintf(
			/* translators: %s: widgets screen URL */
			__( 'Turn the sidebar on/off and pick left or right per view: <b>Blog &amp; archives</b>, <b>Single posts</b>, <b>Shop</b>, <b>Single product</b> (each in its own section of the Customizer), or per post in the editor. Add, remove and reorder widgets in <b>Widgets</b> (in this Customizer, or <a href="%s" target="_blank">Appearance → Widgets</a>). Each view has its own widget area: Blog, Category &amp; archive, Single post, Shop, Single product.', 'bigspy-ai-theme-wp' ),
			$widgets
		) );
		$fields[] = array( 'id' => 'sidebar_width', 'section' => $s, 'type' => 'number', 'label' => __( 'Sidebar width (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 200, 'max' => 480, 'step' => 10 ) );
		$fields[] = array( 'id' => 'bst_g_sidebar_look', 'section' => $s, 'type' => 'heading', 'label' => __( 'Look', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'sidebar_style', 'section' => $s, 'type' => 'select', 'label' => __( 'Sidebar style', 'bigspy-ai-theme-wp' ), 'choices' => array(
			'plain'   => __( 'Plain', 'bigspy-ai-theme-wp' ),
			'divider' => __( 'Divider line next to content', 'bigspy-ai-theme-wp' ),
			'panel'   => __( 'Light panel', 'bigspy-ai-theme-wp' ),
			'boxed'   => __( 'Each widget in a box', 'bigspy-ai-theme-wp' ),
			'shadow'  => __( 'Each widget in a shadow card', 'bigspy-ai-theme-wp' ),
		) );
		$fields[] = array( 'id' => 'sidebar_title_style', 'section' => $s, 'type' => 'select', 'label' => __( 'Widget title style', 'bigspy-ai-theme-wp' ), 'choices' => array(
			'plain'     => __( 'Plain', 'bigspy-ai-theme-wp' ),
			'underline' => __( 'Short brand underline', 'bigspy-ai-theme-wp' ),
			'line'      => __( 'Full line below', 'bigspy-ai-theme-wp' ),
			'bar'       => __( 'Brand bar on the left', 'bigspy-ai-theme-wp' ),
			'filled'    => __( 'Filled brand background', 'bigspy-ai-theme-wp' ),
			'upper'     => __( 'Small uppercase', 'bigspy-ai-theme-wp' ),
		) );
		$fields[] = array( 'id' => 'sidebar_font_size', 'section' => $s, 'type' => 'number', 'label' => __( 'Widget text size (px, 0 = theme)', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 0, 'max' => 22, 'step' => 1 ) );
		$fields[] = array( 'id' => 'sidebar_widget_gap', 'section' => $s, 'type' => 'number', 'label' => __( 'Space between widgets (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 0, 'max' => 80, 'step' => 2 ) );
		$fields[] = array( 'id' => 'sidebar_gap', 'section' => $s, 'type' => 'number', 'label' => __( 'Space between content and sidebar (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 0, 'max' => 120, 'step' => 4 ) );
		$fields[] = array( 'id' => 'bst_g_sidebar_behaviour', 'section' => $s, 'type' => 'heading', 'label' => __( 'Behaviour', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'sidebar_sticky', 'section' => $s, 'type' => 'checkbox', 'label' => __( 'Sticky sidebar (follows while scrolling)', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'sidebar_mobile', 'section' => $s, 'type' => 'select', 'label' => __( 'On phones & tablets', 'bigspy-ai-theme-wp' ), 'choices' => array(
			'below' => __( 'Show below the content', 'bigspy-ai-theme-wp' ),
			'above' => __( 'Show above the content', 'bigspy-ai-theme-wp' ),
			'hide'  => __( 'Hide', 'bigspy-ai-theme-wp' ),
		), 'description' => __( 'The shop sidebar keeps its "Filter" off-canvas button on phones.', 'bigspy-ai-theme-wp' ) );
		// The old width field in Site layout moves here.
		return array_values( array_filter( $fields, function ( $f ) {
			return ! ( 'sidebar_width' === ( $f['id'] ?? '' ) && 'bst_layout_site' === ( $f['section'] ?? '' ) );
		} ) );
	},
	30
);

/** Body classes + CSS vars for the sidebar look. */
add_filter(
	'body_class',
	function ( $classes ) {
		if ( ! bst_mod( 'sidebar_sticky' ) ) {
			$classes[] = 'bs-sidebar-static';
		}
		$style = bst_choice( bst_mod( 'sidebar_style' ), array( 'plain', 'divider', 'panel', 'boxed', 'shadow' ), 'plain' );
		$title = bst_choice( bst_mod( 'sidebar_title_style' ), array( 'plain', 'underline', 'line', 'bar', 'filled', 'upper' ), 'plain' );
		$mob   = bst_choice( bst_mod( 'sidebar_mobile' ), array( 'below', 'above', 'hide' ), 'below' );
		$classes[] = 'bs-sb-' . $style;
		$classes[] = 'bs-sbt-' . $title;
		$classes[] = 'bs-sbm-' . $mob;
		return $classes;
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		$size = (int) bst_mod( 'sidebar_font_size' );
		$css  = ':root{--bs-sb-wgap:' . max( 0, min( 80, (int) bst_mod( 'sidebar_widget_gap' ) ) ) . 'px;--bs-sb-gap:' . max( 0, min( 120, (int) bst_mod( 'sidebar_gap' ) ) ) . 'px' . ( $size ? ';--bs-sb-fs:' . min( 22, $size ) . 'px' : '' ) . '}';
		wp_add_inline_style( 'bst-main', $css );
	},
	30
);
