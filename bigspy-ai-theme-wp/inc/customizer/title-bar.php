<?php
/**
 * G-206: advanced page title bar and breadcrumbs.
 *
 * Title bar: alignment (picked with pictures), minimum height, overlay color and
 * darkness, parallax background, cover image per category/tag (term field) and per
 * page (featured image or no image, in the page layout box).
 * Breadcrumbs: separator, position above/below the title, text and link colors.
 * Empty values keep the old look (centered bar, black 45% overlay, "/" separator).
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$s   = 'bst_title_bar';
		$add = array(
			array( 'id' => 'bst_h_titlebar_adv', 'section' => $s, 'type' => 'heading', 'label' => __( 'Full-width title bar', 'bigspy-ai-theme-wp' ), 'description' => __( 'Categories and tags can have their own cover image (Posts → Categories → Edit). Pages can use their featured image (page layout box).', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'title_bar_align', 'section' => $s, 'type' => 'image_radio', 'label' => __( 'Alignment', 'bigspy-ai-theme-wp' ), 'choices' => array(
				'center' => __( 'Center', 'bigspy-ai-theme-wp' ),
				'left'   => __( 'Left', 'bigspy-ai-theme-wp' ),
				'right'  => __( 'Right', 'bigspy-ai-theme-wp' ),
			) ),
			array( 'id' => 'title_bar_height', 'section' => $s, 'type' => 'number', 'label' => __( 'Minimum height (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 0, 'max' => 800, 'step' => 10, 'placeholder' => '0' ) ),
			array( 'id' => 'title_bar_overlay_color', 'section' => $s, 'type' => 'color', 'label' => __( 'Image overlay color', 'bigspy-ai-theme-wp' ), 'description' => __( 'Only when the bar has an image. Default: black.', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'title_bar_overlay', 'section' => $s, 'type' => 'number', 'label' => __( 'Image overlay strength (%)', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 0, 'max' => 95, 'placeholder' => '45' ) ),
			array( 'id' => 'title_bar_parallax', 'section' => $s, 'type' => 'checkbox', 'label' => __( 'Parallax background image', 'bigspy-ai-theme-wp' ), 'description' => __( 'Desktop only; phones and visitors who prefer less motion get a still image.', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'bst_h_crumbs_adv', 'section' => $s, 'type' => 'heading', 'label' => __( 'Breadcrumb style', 'bigspy-ai-theme-wp' ), 'description' => __( 'The separator applies to the theme and WooCommerce trail; Rank Math and Yoast use the separator set in their own settings.', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'breadcrumb_sep', 'section' => $s, 'type' => 'select', 'label' => __( 'Separator', 'bigspy-ai-theme-wp' ), 'choices' => bst_breadcrumb_sep_choices() ),
			array( 'id' => 'breadcrumb_position', 'section' => $s, 'type' => 'select', 'label' => __( 'Position', 'bigspy-ai-theme-wp' ), 'choices' => array(
				''      => __( 'Automatic (above the simple title, below the title bar heading)', 'bigspy-ai-theme-wp' ),
				'above' => __( 'Above the title', 'bigspy-ai-theme-wp' ),
				'below' => __( 'Below the title', 'bigspy-ai-theme-wp' ),
			) ),
			array( 'id' => 'breadcrumb_color', 'section' => $s, 'type' => 'color', 'label' => __( 'Breadcrumb text color', 'bigspy-ai-theme-wp' ) ),
			array( 'id' => 'breadcrumb_link_color', 'section' => $s, 'type' => 'color', 'label' => __( 'Breadcrumb link color', 'bigspy-ai-theme-wp' ) ),
		);
		// Keep the new options next to the existing title bar fields (before "Show breadcrumbs").
		$at = count( $fields );
		foreach ( $fields as $i => $field ) {
			if ( isset( $field['id'] ) && 'breadcrumbs' === $field['id'] ) {
				$at = $i;
				break;
			}
		}
		array_splice( $fields, $at, 0, array_slice( $add, 0, 6 ) );
		return array_merge( $fields, array_slice( $add, 6 ) );
	}
);

/** Separator choices (value = the character printed). */
function bst_breadcrumb_sep_choices() {
	return array(
		''  => '/',
		'›' => '›',
		'»' => '»',
		'→' => '→',
		'|' => '|',
		'•' => '•',
		'-' => '-',
	);
}

/** The separator character for the theme / WooCommerce trail. */
function bst_breadcrumb_sep() {
	$sep = (string) get_theme_mod( 'breadcrumb_sep', '' );
	return ( '' !== $sep && array_key_exists( $sep, bst_breadcrumb_sep_choices() ) ) ? $sep : '/';
}

/** Whether the breadcrumb prints above the title for the given style ('simple' or 'bar'). */
function bst_breadcrumb_above( $style ) {
	$pos = (string) get_theme_mod( 'breadcrumb_position', '' );
	if ( 'above' === $pos || 'below' === $pos ) {
		return 'above' === $pos;
	}
	return 'simple' === $style;
}

add_filter(
	'bst_layout_pictures',
	function ( $all ) {
		$all['titlealign'] = array(
			'center' => array( array( 2, 2, 56, 36, 0 ), array( 15, 14, 30, 6, 1 ), array( 22, 23, 16, 3, 1 ) ),
			'left'   => array( array( 2, 2, 56, 36, 0 ), array( 6, 14, 30, 6, 1 ), array( 6, 23, 16, 3, 1 ) ),
			'right'  => array( array( 2, 2, 56, 36, 0 ), array( 24, 14, 30, 6, 1 ), array( 38, 23, 16, 3, 1 ) ),
		);
		return $all;
	}
);
add_filter(
	'bst_layout_picker_map',
	function ( $map ) {
		$map['title_bar_align'] = 'titlealign';
		return $map;
	}
);

/** Bar-only options show while the bar style is chosen; breadcrumb style while breadcrumbs are on. */
add_action(
	'customize_register',
	function ( $wp_customize ) {
		$is_bar    = function () {
			return 'bar' === bst_mod( 'title_bar_style' );
		};
		$has_crumb = function () {
			return (bool) bst_mod( 'breadcrumbs' );
		};
		$when      = array(
			'title_bar_align'         => $is_bar,
			'title_bar_height'        => $is_bar,
			'title_bar_overlay_color' => $is_bar,
			'title_bar_overlay'       => $is_bar,
			'title_bar_parallax'      => $is_bar,
			'bst_h_titlebar_adv'      => $is_bar,
			'breadcrumb_sep'          => $has_crumb,
			'breadcrumb_position'     => $has_crumb,
			'breadcrumb_color'        => $has_crumb,
			'breadcrumb_link_color'   => $has_crumb,
			'bst_h_crumbs_adv'        => $has_crumb,
		);
		foreach ( $when as $id => $callback ) {
			$control = $wp_customize->get_control( $id );
			if ( $control ) {
				$control->active_callback = $callback;
			}
		}
	},
	30
);

/**
 * Image for the current title bar: term cover, page featured image, or the Customizer image.
 *
 * @return array{0:string,1:bool} URL ('' = none) and whether it overrides the site-wide image.
 */
function bst_title_bar_image() {
	if ( is_category() || is_tag() || is_tax() ) {
		$cover = (int) get_term_meta( get_queried_object_id(), 'bst_cover_id', true );
		$url   = $cover ? (string) wp_get_attachment_image_url( $cover, 'full' ) : '';
		if ( '' !== $url ) {
			return array( $url, true );
		}
	} elseif ( is_singular() || ( function_exists( 'is_shop' ) && is_shop() ) ) {
		$mode = bst_post_option( 'title_image' );
		if ( 'none' === $mode ) {
			return array( '', true );
		}
		if ( 'featured' === $mode ) {
			$id  = ( function_exists( 'is_shop' ) && is_shop() ) ? wc_get_page_id( 'shop' ) : get_queried_object_id();
			$url = (string) get_the_post_thumbnail_url( $id, 'full' );
			if ( '' !== $url ) {
				return array( $url, true );
			}
		}
	}
	return array( (string) bst_mod( 'title_bar_image' ), false );
}

/** Opening tag of the full-width title bar with its classes and per-view image. */
function bst_title_bar_open() {
	list( $url, $override ) = bst_title_bar_image();
	$classes = array( 'bs-title-bar' );
	if ( '' !== $url ) {
		$classes[] = 'has-image';
		if ( get_theme_mod( 'title_bar_parallax', false ) ) {
			$classes[] = 'is-parallax';
		}
	}
	$align = (string) get_theme_mod( 'title_bar_align', '' );
	if ( 'left' === $align || 'right' === $align ) {
		$classes[] = 'bs-title-bar--' . $align;
	}
	$style = '';
	if ( $override ) {
		$style = '' === $url ? '--bs-titlebar-image:none' : '--bs-titlebar-image:url("' . esc_url_raw( $url ) . '")';
	}
	return '<div class="' . esc_attr( implode( ' ', $classes ) ) . '"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '>';
}

/** Hex + percent to rgba(). */
function bst_title_bar_rgba( $hex, $percent ) {
	$hex = ltrim( (string) sanitize_hex_color( $hex ), '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( 6 !== strlen( $hex ) ) {
		$hex = '000000';
	}
	return 'rgba(' . hexdec( substr( $hex, 0, 2 ) ) . ',' . hexdec( substr( $hex, 2, 2 ) ) . ',' . hexdec( substr( $hex, 4, 2 ) ) . ',' . round( $percent / 100, 2 ) . ')';
}

/** CSS for the options above; '' while none is set. */
function bst_title_bar_css() {
	$vars = array();
	$h    = get_theme_mod( 'title_bar_height', '' );
	if ( is_numeric( $h ) && (int) $h > 0 ) {
		$vars[] = '--bs-titlebar-min-h:' . min( 800, (int) $h ) . 'px';
	}
	$oc = (string) get_theme_mod( 'title_bar_overlay_color', '' );
	$op = get_theme_mod( 'title_bar_overlay', '' );
	if ( sanitize_hex_color( $oc ) || is_numeric( $op ) ) {
		$vars[] = '--bs-titlebar-overlay:' . bst_title_bar_rgba( $oc ? $oc : '#000000', is_numeric( $op ) ? max( 0, min( 95, (int) $op ) ) : 45 );
	}
	$bc = sanitize_hex_color( (string) get_theme_mod( 'breadcrumb_color', '' ) );
	$bl = sanitize_hex_color( (string) get_theme_mod( 'breadcrumb_link_color', '' ) );
	$css = $vars ? ':root{' . implode( ';', $vars ) . '}' : '';
	if ( $bc ) {
		$css .= '.bs-breadcrumbs,.bs-title-bar .bs-breadcrumbs{color:' . $bc . ';opacity:1}';
	}
	if ( $bl ) {
		$css .= '.bs-breadcrumbs a,.bs-title-bar .bs-breadcrumbs a{color:' . $bl . ';opacity:1}';
	}
	return wp_strip_all_tags( $css );
}
add_filter(
	'bst_dynamic_css',
	function ( $css ) {
		return $css . bst_title_bar_css();
	}
);

/* ---------------------------------------------------------------- Cover image field on categories, tags and other public taxonomies */

/** Taxonomies that get the cover field (product categories keep WooCommerce's own thumbnail). */
function bst_cover_taxonomies() {
	$taxes = array_diff( get_taxonomies( array( 'public' => true, 'show_ui' => true ) ), array( 'product_cat', 'post_format' ) );
	return (array) apply_filters( 'bst_cover_taxonomies', array_values( $taxes ) );
}

add_action(
	'admin_init',
	function () {
		foreach ( bst_cover_taxonomies() as $tax ) {
			add_action( $tax . '_add_form_fields', 'bst_cover_field_add' );
			add_action( $tax . '_edit_form_fields', 'bst_cover_field_edit' );
			add_action( 'created_' . $tax, 'bst_cover_field_save' );
			add_action( 'edited_' . $tax, 'bst_cover_field_save' );
		}
	}
);

add_action(
	'admin_enqueue_scripts',
	function ( $hook ) {
		if ( ( 'edit-tags.php' === $hook || 'term.php' === $hook ) && in_array( (string) ( $_GET['taxonomy'] ?? '' ), bst_cover_taxonomies(), true ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wp_enqueue_media();
		}
	}
);

/** Picker markup shared by the add and edit screens. */
function bst_cover_field_input( $id ) {
	$url = $id ? (string) wp_get_attachment_image_url( $id, 'medium' ) : '';
	wp_nonce_field( 'bst_cover', 'bst_cover_nonce' );
	echo '<div class="bst-cover-field">';
	echo '<input type="hidden" name="bst_cover_id" value="' . esc_attr( $id ? $id : '' ) . '">';
	echo '<img src="' . esc_url( $url ) . '" alt="" style="max-width:240px;height:auto;display:' . ( $url ? 'block' : 'none' ) . ';margin-bottom:8px">';
	echo '<button type="button" class="button bst-cover-pick">' . esc_html__( 'Choose image', 'bigspy-ai-theme-wp' ) . '</button> ';
	echo '<button type="button" class="button-link-delete bst-cover-clear"' . ( $url ? '' : ' style="display:none"' ) . '>' . esc_html__( 'Remove', 'bigspy-ai-theme-wp' ) . '</button>';
	echo '<p class="description">' . esc_html__( 'Background of the full-width title bar on this archive (Customizer → Page title & breadcrumbs).', 'bigspy-ai-theme-wp' ) . '</p>';
	echo '</div>';
	?>
	<script>
	( function () {
		document.querySelectorAll( '.bst-cover-field' ).forEach( function ( box ) {
			if ( box.dataset.ready ) { return; }
			box.dataset.ready = '1';
			var input = box.querySelector( 'input' ), img = box.querySelector( 'img' ), clear = box.querySelector( '.bst-cover-clear' ), frame;
			box.querySelector( '.bst-cover-pick' ).addEventListener( 'click', function () {
				if ( ! window.wp || ! wp.media ) { return; }
				frame = frame || wp.media( { library: { type: 'image' }, multiple: false } );
				frame.off( 'select' ).on( 'select', function () {
					var a = frame.state().get( 'selection' ).first().toJSON();
					input.value = a.id;
					img.src = ( a.sizes && a.sizes.medium ? a.sizes.medium.url : a.url );
					img.style.display = 'block';
					clear.style.display = '';
				} );
				frame.open();
			} );
			clear.addEventListener( 'click', function () {
				input.value = ''; img.style.display = 'none'; clear.style.display = 'none';
			} );
		} );
	} )();
	</script>
	<?php
}

function bst_cover_field_add() {
	echo '<div class="form-field"><label>' . esc_html__( 'Cover image', 'bigspy-ai-theme-wp' ) . '</label>';
	bst_cover_field_input( 0 );
	echo '</div>';
}

function bst_cover_field_edit( $term ) {
	echo '<tr class="form-field"><th scope="row"><label>' . esc_html__( 'Cover image', 'bigspy-ai-theme-wp' ) . '</label></th><td>';
	bst_cover_field_input( (int) get_term_meta( $term->term_id, 'bst_cover_id', true ) );
	echo '</td></tr>';
}

function bst_cover_field_save( $term_id ) {
	if ( ! isset( $_POST['bst_cover_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['bst_cover_nonce'] ), 'bst_cover' ) || ! current_user_can( 'manage_categories' ) ) {
		return;
	}
	$id = isset( $_POST['bst_cover_id'] ) ? absint( $_POST['bst_cover_id'] ) : 0;
	if ( $id && wp_attachment_is_image( $id ) ) {
		update_term_meta( $term_id, 'bst_cover_id', $id );
	} else {
		delete_term_meta( $term_id, 'bst_cover_id' );
	}
}
