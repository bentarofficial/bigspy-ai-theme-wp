<?php
/**
 * Legacy layout shortcodes, part 2: shop elements, banners, tabs and the
 * remaining content elements of Flatsome's UX Builder.
 *
 * Same rules as compat-flatsome.php: content stays as stored, only the
 * presentation changes, and a tag is skipped when something else provides it.
 * Shop shortcodes render nothing while WooCommerce is inactive.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

function bst_legacy_more_tags() {
	return array(
		// Shop.
		'ux_products'            => 'bst_legacy_products',
		'ux_bestseller_products' => 'bst_legacy_products_bestseller',
		'ux_featured_products'   => 'bst_legacy_products_featured',
		'ux_sale_products'       => 'bst_legacy_products_sale',
		'ux_latest_products'     => 'bst_legacy_products',
		'product_flip'           => 'bst_legacy_products',
		'ux_products_list'       => 'bst_legacy_products_list',
		'ux_product_categories'  => 'bst_legacy_product_categories',
		// Banners.
		'ux_banner'              => 'bst_legacy_banner',
		'text_box'               => 'bst_legacy_text_box',
		'ux_banner_grid'         => 'bst_legacy_banner_grid',
		'col_grid'               => 'bst_legacy_col_grid',
		// Content.
		'tabgroup'               => 'bst_legacy_tabgroup',
		'tabgroup_vertical'      => 'bst_legacy_tabgroup',
		'tab'                    => 'bst_legacy_tab',
		'featured_box'           => 'bst_legacy_featured_box',
		'ux_image_box'           => 'bst_legacy_image_box',
		'ux_stack'               => 'bst_legacy_stack',
		'divider'                => 'bst_legacy_divider',
		'message_box'            => 'bst_legacy_message_box',
		'ux_gallery'             => 'bst_legacy_gallery',
		'ux_video'               => 'bst_legacy_video',
		'search'                 => 'bst_legacy_search',
		'follow'                 => 'bst_legacy_follow',
		'scroll_to'              => 'bst_legacy_scroll_to',
		'price_table'            => 'bst_legacy_price_table',
		'bullet_item'            => 'bst_legacy_bullet_item',
		// Not supported: print nothing instead of raw shortcode text.
		'lightbox'               => '__return_empty_string',
		'ux_countdown'           => '__return_empty_string',
		'ux_instagram_feed'      => '__return_empty_string',
		'ux_hotspot'             => '__return_empty_string',
		'ux_lottie'              => '__return_empty_string',
		'ux_payment_icons'       => '__return_empty_string',
		'ux_sidebar'             => '__return_empty_string',
		'ux_nav'                 => '__return_empty_string',
		'ux_menu'                => '__return_empty_string',
		'ux_pages'               => '__return_empty_string',
		'page_header'            => '__return_empty_string',
		'share'                  => '__return_empty_string',
		'map'                    => '__return_empty_string',
	);
}

function bst_legacy_more_register() {
	foreach ( bst_legacy_more_tags() as $tag => $callback ) {
		if ( ! shortcode_exists( $tag ) ) {
			add_shortcode( $tag, $callback );
		}
	}
}
add_action( 'init', 'bst_legacy_more_register', 21 );

/**
 * Wrap element output with visibility / custom classes when needed.
 */
function bst_legacy_wrap( $html, $a, $base = '' ) {
	if ( '' === $html ) {
		return '';
	}
	$classes = array( $base, bst_legacy_visibility( $a['visibility'] ?? '' ) );
	$class   = bst_legacy_classes( $classes, $a['class'] ?? '' );
	return '' !== $class ? '<div class="' . esc_attr( $class ) . '">' . $html . '</div>' : $html;
}

/**
 * Columns from Flatsome attributes (columns, columns__md, columns__sm).
 */
function bst_legacy_columns( $atts, $d = 4, $t = 3, $m = 2 ) {
	$atts = (array) $atts;
	$desk = isset( $atts['columns'] ) && is_numeric( $atts['columns'] ) ? (int) $atts['columns'] : $d;
	return array(
		'd' => $desk,
		't' => isset( $atts['columns__md'] ) && is_numeric( $atts['columns__md'] ) ? (int) $atts['columns__md'] : min( $t, $desk ),
		'm' => isset( $atts['columns__sm'] ) && is_numeric( $atts['columns__sm'] ) ? (int) $atts['columns__sm'] : min( $m, $desk ),
	);
}

/* ------------------------------------------------------------------------ Shop */

function bst_legacy_products( $atts, $content = '', $tag = '', $force_type = '' ) {
	if ( ! function_exists( 'bst_products_html' ) ) {
		return '';
	}
	$atts = (array) $atts;
	$a    = shortcode_atts(
		array(
			'type'       => 'slider',
			'cat'        => '',
			'tags'       => '',
			'ids'        => '',
			'products'   => '8',
			'offset'     => '',
			'orderby'    => '',
			'order'      => '',
			'show'       => '',
			'auto_slide' => '',
			'relay'      => '',
			'class'      => '',
			'visibility' => '',
		),
		$atts
	);

	$type = $force_type;
	if ( '' === $type ) {
		$shows = array( 'featured' => 'featured', 'onsale' => 'sale' );
		$type  = $shows[ $a['show'] ] ?? 'latest';
	}
	$orders  = array(
		'sales'      => 'popularity',
		'popularity' => 'popularity',
		'rating'     => 'rating',
		'rand'       => 'rand',
		'title'      => 'title',
		'name'       => 'title',
		'menu_order' => 'menu_order',
		'date'       => 'date',
		'price'      => 'DESC' === strtoupper( $a['order'] ) ? 'price-desc' : 'price',
	);
	$cols    = bst_legacy_columns( $atts );
	$is_grid = in_array( $a['type'], array( 'row', 'grid', 'masonry' ), true );

	$html = bst_products_html(
		array(
			'type'      => $type,
			'cats'      => $a['cat'],
			'tags'      => $a['tags'],
			'ids'       => $a['ids'],
			'count'     => $a['products'],
			'offset'    => $a['offset'],
			'orderby'   => $orders[ $a['orderby'] ] ?? '',
			'layout'    => $is_grid ? 'grid' : 'slider',
			'd'         => $cols['d'],
			't'         => $cols['t'],
			'm'         => $cols['m'],
			'autoplay'  => is_numeric( $a['auto_slide'] ) ? (int) $a['auto_slide'] : 0,
			'load_more' => $is_grid && '' !== $a['relay'] && 'pagination' !== $a['relay'],
		)
	);
	return bst_legacy_wrap( $html, $a, 'bs-legacy-products' );
}

function bst_legacy_products_bestseller( $atts ) {
	return bst_legacy_products( $atts, '', '', 'best_selling' );
}

function bst_legacy_products_featured( $atts ) {
	return bst_legacy_products( $atts, '', '', 'featured' );
}

function bst_legacy_products_sale( $atts ) {
	return bst_legacy_products( $atts, '', '', 'sale' );
}

function bst_legacy_products_list( $atts ) {
	if ( ! function_exists( 'bst_products_html' ) ) {
		return '';
	}
	$a = shortcode_atts(
		array(
			'cat'        => '',
			'ids'        => '',
			'products'   => '5',
			'offset'     => '',
			'orderby'    => '',
			'show'       => '',
			'class'      => '',
			'visibility' => '',
		),
		$atts
	);
	$shows  = array( 'featured' => 'featured', 'onsale' => 'sale' );
	$orders = array( 'sales' => 'popularity', 'rating' => 'rating', 'rand' => 'rand', 'title' => 'title', 'date' => 'date' );
	$html   = bst_products_html(
		array(
			'type'    => $shows[ $a['show'] ] ?? 'latest',
			'cats'    => $a['cat'],
			'ids'     => $a['ids'],
			'count'   => $a['products'],
			'offset'  => $a['offset'],
			'orderby' => $orders[ $a['orderby'] ] ?? '',
			'layout'  => 'list',
		)
	);
	return bst_legacy_wrap( $html, $a, 'bs-legacy-products-list' );
}

function bst_legacy_product_categories( $atts ) {
	if ( ! function_exists( 'bst_product_categories_html' ) ) {
		return '';
	}
	$atts = (array) $atts;
	$a    = shortcode_atts(
		array(
			'type'         => 'slider',
			'style'        => 'badge',
			'number'       => '',
			'ids'          => '',
			'parent'       => '',
			'orderby'      => 'menu_order',
			'show_count'   => 'true',
			'image_height' => '',
			'auto_slide'   => '',
			'class'        => '',
			'visibility'   => '',
		),
		$atts
	);
	$cols   = bst_legacy_columns( $atts, 4, 3, 2 );
	$ratios = array( '100%' => '1-1', '75%' => '4-3', '56.25%' => '16-9', '56%' => '16-9', '133%' => '3-4', '125%' => '3-4' );
	$styles = array( 'normal' => 'card', 'default' => 'card', 'bounce' => 'card', 'push' => 'card', 'vertical' => 'card', 'circle' => 'circle' );

	$html = bst_product_categories_html(
		array(
			'count'      => is_numeric( $a['number'] ) ? (int) $a['number'] : 24,
			'ids'        => $a['ids'],
			'parent'     => is_numeric( $a['parent'] ) ? (int) $a['parent'] : 0,
			'top_level'  => '0' === (string) $a['parent'],
			'hide_empty' => true,
			'orderby'    => in_array( $a['orderby'], array( 'name', 'title' ), true ) ? 'name' : ( 'count' === $a['orderby'] ? 'count' : 'menu_order' ),
			'style'      => $styles[ $a['style'] ] ?? 'overlay',
			'ratio'      => $ratios[ $a['image_height'] ] ?? '1-1',
			'show_count' => ! in_array( (string) $a['show_count'], array( 'false', '0', '' ), true ),
			'layout'     => in_array( $a['type'], array( 'row', 'grid', 'masonry' ), true ) ? 'grid' : 'slider',
			'd'          => $cols['d'],
			't'          => $cols['t'],
			'm'          => $cols['m'],
			'autoplay'   => is_numeric( $a['auto_slide'] ) ? (int) $a['auto_slide'] : 0,
		)
	);
	return bst_legacy_wrap( $html, $a, 'bs-legacy-categories' );
}

/* ------------------------------------------------------------------------ Banners */

/**
 * Banner height: px value, or a percentage of the width (Flatsome style).
 *
 * @return string CSS declarations.
 */
function bst_legacy_banner_size( $value, $var ) {
	$value = bst_legacy_first( $value );
	if ( preg_match( '/^([0-9.]+)%$/', $value, $m ) && (float) $m[1] > 0 ) {
		return $var . '-ratio:' . round( 100 / (float) $m[1], 4 );
	}
	$length = bst_legacy_length( $value );
	return $length ? $var . ':' . $length : '';
}

function bst_legacy_banner( $atts, $content = '' ) {
	$a = shortcode_atts(
		array(
			'height'     => '500px',
			'height__md' => '',
			'height__sm' => '',
			'bg'         => '',
			'bg_size'    => 'large',
			'bg_color'   => '',
			'bg_overlay' => '',
			'bg_pos'     => '',
			'link'       => '',
			'target'     => '',
			'hover'      => '',
			'class'      => '',
			'visibility' => '',
		),
		$atts
	);

	$classes = array( 'bs-banner', 'bs-legacy-banner', bst_legacy_visibility( $a['visibility'] ) );
	if ( in_array( $a['hover'], array( 'zoom', 'zoom-fade', 'zoom-long' ), true ) ) {
		$classes[] = 'bs-banner--hover-zoom';
	}

	$style = array_filter(
		array(
			bst_legacy_banner_size( $a['height'], '--bs-lb-d' ),
			bst_legacy_banner_size( $a['height__md'], '--bs-lb-t' ),
			bst_legacy_banner_size( $a['height__sm'], '--bs-lb-m' ),
			bst_legacy_color( $a['bg_color'] ) ? 'background-color:' . bst_legacy_color( $a['bg_color'] ) : '',
		)
	);

	$image = '';
	if ( is_numeric( $a['bg'] ) && (int) $a['bg'] > 0 ) {
		$size     = in_array( $a['bg_size'], array( 'thumbnail', 'medium', 'large', 'original', 'full' ), true ) ? ( 'original' === $a['bg_size'] ? 'full' : $a['bg_size'] ) : 'large';
		$position = preg_match( '/^[0-9.]+% [0-9.]+%$/', $a['bg_pos'] ) ? $a['bg_pos'] : '50% 50%';
		$image    = wp_get_attachment_image( (int) $a['bg'], $size, false, array( 'class' => 'bs-banner__img', 'alt' => '', 'style' => 'object-position:' . $position ) );
	} elseif ( '' !== $a['bg'] ) {
		$image = '<img class="bs-banner__img" src="' . esc_url( $a['bg'] ) . '" alt="" loading="lazy" decoding="async">';
	}

	$out  = '<div class="' . esc_attr( bst_legacy_classes( $classes, $a['class'] ) ) . '"' . ( $style ? ' style="' . esc_attr( implode( ';', $style ) ) . '"' : '' ) . '>';
	$out .= '<div class="bs-banner__bg" aria-hidden="true">' . $image . '</div>';
	$overlay = bst_legacy_color( $a['bg_overlay'] );
	if ( $overlay ) {
		$out .= '<div class="bs-banner__overlay" style="' . esc_attr( 'background:' . $overlay ) . '" aria-hidden="true"></div>';
	}
	if ( '' !== $a['link'] ) {
		$out .= '<a class="bs-banner__link" href="' . esc_url( $a['link'] ) . '"' . ( '_blank' === $a['target'] ? ' target="_blank" rel="noopener"' : '' ) . ' aria-label="' . esc_attr__( 'Open link', 'bigspy-ai-theme-wp' ) . '"></a>';
	}
	$out .= '<div class="bs-legacy-banner__layers">' . bst_legacy_content( $content ) . '</div></div>';
	return $out;
}

function bst_legacy_text_box( $atts, $content = '' ) {
	$a = shortcode_atts(
		array(
			'width'          => '60',
			'width__md'      => '',
			'width__sm'      => '',
			'position_x'     => '50',
			'position_y'     => '50',
			'position_x__sm' => '',
			'position_y__sm' => '',
			'text_align'     => 'center',
			'text_color'     => 'light',
			'bg'             => '',
			'padding'        => '',
			'scale'          => '',
			'scale__sm'      => '',
			'class'          => '',
			'visibility'     => '',
		),
		$atts
	);
	$pct = function ( $value, $default ) {
		return is_numeric( $value ) ? max( 0, min( 100, (float) $value ) ) : $default;
	};
	$width = $pct( $a['width'], 60 );
	$x     = $pct( $a['position_x'], 50 );
	$y     = $pct( $a['position_y'], 50 );

	$classes = array( 'bs-legacy-textbox', 'light' === $a['text_color'] ? 'is-scheme-light' : 'is-scheme-dark', bst_legacy_visibility( $a['visibility'] ) );
	if ( in_array( $a['text_align'], array( 'left', 'center', 'right' ), true ) ) {
		$classes[] = 'has-text-align-' . $a['text_align'];
	}
	$style = bst_style_vars(
		array(
			'--bs-tb-w'   => $width . '%',
			'--bs-tb-wt'  => $pct( $a['width__md'], $width ) . '%',
			'--bs-tb-wm'  => $pct( $a['width__sm'], max( $width, 80 ) ) . '%',
			'--bs-tb-x'   => $x . '%',
			'--bs-tb-y'   => $y . '%',
			'--bs-tb-xm'  => $pct( $a['position_x__sm'], $x ) . '%',
			'--bs-tb-ym'  => $pct( $a['position_y__sm'], $y ) . '%',
			'--bs-tb-s'   => is_numeric( $a['scale'] ) ? max( 50, min( 300, (float) $a['scale'] ) ) / 100 : '',
			'--bs-tb-sm'  => is_numeric( $a['scale__sm'] ) ? max( 50, min( 300, (float) $a['scale__sm'] ) ) / 100 : '',
		)
	);
	$inner = bst_style_vars(
		array(
			'background-color' => bst_legacy_color( $a['bg'] ),
			'padding'          => bst_legacy_box( $a['padding'] ),
		)
	);
	return '<div class="' . esc_attr( bst_legacy_classes( $classes, $a['class'] ) ) . '" style="' . esc_attr( $style ) . '"><div class="bs-legacy-textbox__inner"' . ( $inner ? ' style="' . esc_attr( $inner ) . '"' : '' ) . '>' . bst_legacy_content( $content ) . '</div></div>';
}

function bst_legacy_banner_grid( $atts, $content = '' ) {
	$a = shortcode_atts( array( 'height' => '600', 'spacing' => 'small', 'class' => '', 'visibility' => '' ), $atts );
	$gaps    = array( 'collapse' => '0px', 'xsmall' => '4px', 'small' => '10px', 'normal' => '20px', 'large' => '30px' );
	$style   = '--bs-bg-h:' . max( 200, min( 1400, (int) $a['height'] ) ) . 'px;--bs-bg-gap:' . ( $gaps[ $a['spacing'] ] ?? '10px' );
	$classes = array( 'bs-legacy-banner-grid', bst_legacy_visibility( $a['visibility'] ) );
	$html    = preg_replace( '#(^|</div>)(?:\s|<br\s*/?>|</?p>)+(?=<div class="bs-legacy-col-grid|$)#i', '$1', bst_legacy_content( $content ) );
	return '<div class="' . esc_attr( bst_legacy_classes( $classes, $a['class'] ) ) . '" style="' . esc_attr( $style ) . '">' . $html . '</div>';
}

function bst_legacy_col_grid( $atts, $content = '' ) {
	$a = shortcode_atts( array( 'span' => '12', 'span__md' => '', 'span__sm' => '', 'height' => '1-1', 'class' => '' ), $atts );
	$rows    = array( '1-1' => 6, '1-2' => 3, '1-3' => 2, '2-3' => 4, '1-4' => 2, '3-4' => 5 );
	$desktop = bst_legacy_span( $a['span'], 12 );
	$style   = bst_style_vars(
		array(
			'--bs-span-d' => $desktop,
			'--bs-span-t' => '' !== $a['span__md'] ? bst_legacy_span( $a['span__md'], $desktop ) : $desktop,
			'--bs-span-m' => '' !== $a['span__sm'] ? bst_legacy_span( $a['span__sm'], 12 ) : 12,
			'--bs-rows'   => $rows[ $a['height'] ] ?? 6,
		)
	);
	return '<div class="' . esc_attr( bst_legacy_classes( array( 'bs-legacy-col-grid' ), $a['class'] ) ) . '" style="' . esc_attr( $style ) . '">' . bst_legacy_content( $content ) . '</div>';
}

/* ------------------------------------------------------------------------ Tabs */

/**
 * Titles collected from [tab] shortcodes while their parent [tabgroup] renders.
 *
 * @param string|null $title Push a title, or null to read and reset.
 */
function bst_legacy_tab_titles( $title = null, $reset = false ) {
	static $stack = array();
	if ( $reset ) {
		$titles = $stack;
		$stack  = array();
		return $titles;
	}
	if ( null !== $title ) {
		$stack[] = $title;
	}
	return $stack;
}

function bst_legacy_tab( $atts, $content = '' ) {
	$a = shortcode_atts( array( 'title' => '' ), $atts );
	bst_legacy_tab_titles( $a['title'] );
	return '<div class="bs-tab">' . bst_legacy_content( $content ) . '</div>';
}

function bst_legacy_tabgroup( $atts, $content = '', $tag = '' ) {
	$a = shortcode_atts( array( 'title' => '', 'style' => 'line', 'align' => 'left', 'class' => '', 'visibility' => '' ), $atts );

	$outer  = bst_legacy_tab_titles( null, true ); // Support nested tab groups.
	$panels = bst_legacy_content( $content );
	$titles = bst_legacy_tab_titles( null, true );
	foreach ( $outer as $title ) {
		bst_legacy_tab_titles( $title );
	}

	$styles = array( 'line' => 'underline', 'line-bottom' => 'underline', 'line-grow' => 'underline', 'tabs' => 'boxed', 'outline' => 'pills', 'pills' => 'pills', 'simple' => 'underline' );
	$style  = 'tabgroup_vertical' === $tag ? 'vertical' : ( $styles[ $a['style'] ] ?? 'underline' );
	$align  = 'center' === $a['align'] ? 'center' : 'left';

	$nav = '';
	foreach ( $titles as $i => $title ) {
		$nav .= '<button type="button" class="bs-tabs__tab' . ( 0 === $i ? ' is-active' : '' ) . '" role="tab" aria-selected="' . ( 0 === $i ? 'true' : 'false' ) . '">' . wp_kses_post( $title ) . '</button>';
	}
	$classes = array( 'bs-tabs', 'bs-tabs--' . $style, 'bs-tabs--align-' . $align, 'bs-legacy-tabs', bst_legacy_visibility( $a['visibility'] ) );
	$heading = '' !== $a['title'] ? '<h4 class="bs-legacy-tabs__title">' . wp_kses_post( $a['title'] ) . '</h4>' : '';
	return $heading . '<div class="' . esc_attr( bst_legacy_classes( $classes, $a['class'] ) ) . '"><div class="bs-tabs__nav" role="tablist">' . $nav . '</div><div class="bs-tabs__panels">' . $panels . '</div></div>';
}

/* ------------------------------------------------------------------------ Content elements */

function bst_legacy_featured_box( $atts, $content = '' ) {
	$a = shortcode_atts(
		array(
			'img'         => '',
			'img_width'   => '60',
			'pos'         => 'top',
			'title'       => '',
			'title_small' => '',
			'link'        => '',
			'icon_border' => '',
			'icon_color'  => '',
			'class'       => '',
			'visibility'  => '',
		),
		$atts
	);
	$position = in_array( $a['pos'], array( 'left', 'right' ), true ) ? 'left' : 'top';
	$align    = 'center' === $a['pos'] ? 'center' : ( 'top' === $a['pos'] ? 'left' : 'left' );
	$width    = is_numeric( $a['img_width'] ) ? max( 16, min( 400, (int) $a['img_width'] ) ) : 60;

	$media = '';
	if ( is_numeric( $a['img'] ) && (int) $a['img'] > 0 ) {
		$media = wp_get_attachment_image( (int) $a['img'], 'medium', false, array( 'class' => 'bs-icon-box__img', 'alt' => '' ) );
	} elseif ( '' !== $a['img'] ) {
		$media = '<img class="bs-icon-box__img" src="' . esc_url( $a['img'] ) . '" alt="" loading="lazy">';
	}

	$classes = array( 'bs-icon-box', 'bs-icon-box--' . $position, 'bs-icon-box--align-' . $align, 'bs-icon-box--icon-plain', 'bs-legacy-featured-box', bst_legacy_visibility( $a['visibility'] ) );
	if ( 'right' === $a['pos'] ) {
		$classes[] = 'bs-legacy-featured-box--right';
	}
	$style = '--bs-ib-size:' . ( $width / 2 ) . 'px' . ( bst_legacy_color( $a['icon_color'] ) ? ';--bs-ib-color:' . bst_legacy_color( $a['icon_color'] ) : '' );

	$title = '';
	if ( '' !== $a['title'] || '' !== $a['title_small'] ) {
		$text  = wp_kses_post( $a['title'] ) . ( '' !== $a['title_small'] ? ' <small>' . wp_kses_post( $a['title_small'] ) . '</small>' : '' );
		$title = '<h5 class="bs-icon-box__title">' . ( '' !== $a['link'] ? '<a href="' . esc_url( $a['link'] ) . '">' . $text . '</a>' : $text ) . '</h5>';
	}

	$out  = '<div class="' . esc_attr( bst_legacy_classes( $classes, $a['class'] ) ) . '" style="' . esc_attr( $style ) . '">';
	$out .= $media ? '<div class="bs-icon-box__icon">' . $media . '</div>' : '';
	$out .= '<div class="bs-icon-box__body">' . $title . '<div class="bs-icon-box__text">' . bst_legacy_content( $content ) . '</div></div></div>';
	return $out;
}

function bst_legacy_image_box( $atts, $content = '' ) {
	$a = shortcode_atts(
		array(
			'img'          => '',
			'image_size'   => 'medium_large',
			'image_width'  => '',
			'image_height' => '',
			'image_radius' => '',
			'style'        => '',
			'text_align'   => 'center',
			'link'         => '',
			'target'       => '',
			'class'        => '',
			'visibility'   => '',
		),
		$atts
	);
	$image = is_numeric( $a['img'] ) ? wp_get_attachment_image( (int) $a['img'], sanitize_key( $a['image_size'] ), false, array( 'alt' => '' ) ) : '';
	if ( '' !== $image && '' !== $a['link'] ) {
		$image = '<a href="' . esc_url( $a['link'] ) . '"' . ( '_blank' === $a['target'] ? ' target="_blank" rel="noopener"' : '' ) . '>' . $image . '</a>';
	}
	$media_style = bst_style_vars(
		array(
			'width'         => is_numeric( $a['image_width'] ) ? min( 100, (float) $a['image_width'] ) . '%' : '',
			'aspect-ratio'  => preg_match( '/^([0-9.]+)%$/', $a['image_height'], $m ) && (float) $m[1] > 0 ? round( 100 / (float) $m[1], 4 ) : '',
			'border-radius' => is_numeric( $a['image_radius'] ) ? (float) $a['image_radius'] . '%' : '',
		)
	);
	$classes = array( 'bs-legacy-imagebox', 'bs-legacy-imagebox--' . ( in_array( $a['style'], array( 'overlay', 'shade', 'badge', 'label', 'vertical', 'bounce', 'push' ), true ) ? $a['style'] : 'normal' ), bst_legacy_visibility( $a['visibility'] ) );
	if ( in_array( $a['text_align'], array( 'left', 'center', 'right' ), true ) ) {
		$classes[] = 'has-text-align-' . $a['text_align'];
	}
	return '<div class="' . esc_attr( bst_legacy_classes( $classes, $a['class'] ) ) . '"><div class="bs-legacy-imagebox__media"' . ( $media_style ? ' style="' . esc_attr( $media_style ) . '"' : '' ) . '>' . $image . '</div><div class="bs-legacy-imagebox__text">' . bst_legacy_content( $content ) . '</div></div>';
}

function bst_legacy_stack( $atts, $content = '' ) {
	$a = shortcode_atts( array( 'direction' => 'row', 'distribute' => 'start', 'align' => 'stretch', 'gap' => '1', 'class' => '', 'visibility' => '' ), $atts );
	$justify = array( 'start' => 'flex-start', 'center' => 'center', 'end' => 'flex-end', 'between' => 'space-between', 'around' => 'space-around' );
	$align   = array( 'start' => 'flex-start', 'center' => 'center', 'end' => 'flex-end', 'stretch' => 'stretch', 'baseline' => 'baseline' );
	$style   = bst_style_vars(
		array(
			'flex-direction'  => 'col' === $a['direction'] ? 'column' : 'row',
			'justify-content' => $justify[ $a['distribute'] ] ?? 'flex-start',
			'align-items'     => $align[ $a['align'] ] ?? 'stretch',
			'gap'             => is_numeric( $a['gap'] ) ? max( 0, min( 8, (float) $a['gap'] ) ) * 0.5 . 'rem' : '.5rem',
		)
	);
	return '<div class="' . esc_attr( bst_legacy_classes( array( 'bs-legacy-stack', bst_legacy_visibility( $a['visibility'] ) ), $a['class'] ) ) . '" style="' . esc_attr( $style ) . '">' . bst_legacy_content( $content ) . '</div>';
}

function bst_legacy_divider( $atts ) {
	$a = shortcode_atts( array( 'width' => '30px', 'height' => '3px', 'margin' => '1em', 'align' => 'left', 'color' => '', 'class' => '', 'visibility' => '' ), $atts );
	$style = bst_style_vars(
		array(
			'--bs-divider-width'   => 'full' === $a['width'] ? '100%' : bst_legacy_length( $a['width'], '30px' ),
			'--bs-divider-size'    => bst_legacy_length( $a['height'], '3px' ),
			'--bs-divider-spacing' => bst_legacy_length( $a['margin'], '1em' ),
			'--bs-divider-color'   => bst_legacy_color( $a['color'] ),
		)
	);
	$align = in_array( $a['align'], array( 'left', 'center', 'right' ), true ) ? $a['align'] : 'left';
	return '<hr class="' . esc_attr( bst_legacy_classes( array( 'bs-divider', 'bs-divider--' . $align, 'bs-legacy-divider', bst_legacy_visibility( $a['visibility'] ) ), $a['class'] ) ) . '" style="' . esc_attr( $style ) . '">';
}

function bst_legacy_message_box( $atts, $content = '' ) {
	$a = shortcode_atts( array( 'bg' => '', 'bg_color' => '', 'text_color' => 'light', 'padding' => '15', 'class' => '', 'visibility' => '' ), $atts );
	$style = bst_style_vars(
		array(
			'background-color' => bst_legacy_color( $a['bg_color'] ),
			'background-image' => $a['bg'] && bst_legacy_image_url( $a['bg'] ) ? 'url(' . esc_url( bst_legacy_image_url( $a['bg'] ) ) . ')' : '',
			'padding'          => bst_legacy_length( $a['padding'], '15px' ),
		)
	);
	$classes = array( 'bs-legacy-message', 'dark' === $a['text_color'] ? 'is-scheme-dark' : 'is-scheme-light', bst_legacy_visibility( $a['visibility'] ) );
	return '<div class="' . esc_attr( bst_legacy_classes( $classes, $a['class'] ) ) . '"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '>' . bst_legacy_content( $content ) . '</div>';
}

function bst_legacy_gallery( $atts ) {
	$atts = (array) $atts;
	$a    = shortcode_atts( array( 'ids' => '', 'type' => 'row', 'image_size' => 'medium_large', 'image_height' => '75%', 'lightbox' => 'true', 'auto_slide' => '', 'class' => '', 'visibility' => '' ), $atts );
	$ids  = array_filter( array_map( 'absint', explode( ',', $a['ids'] ) ) );
	if ( ! $ids ) {
		return '';
	}
	$ratios = array( '100%' => '1-1', '75%' => '4-3', '56.25%' => '16-9', '56%' => '16-9', '133%' => '3-4' );
	$ratio  = $ratios[ $a['image_height'] ] ?? '4-3';
	$items  = '';
	foreach ( $ids as $id ) {
		$image = wp_get_attachment_image( $id, sanitize_key( $a['image_size'] ), false, array( 'loading' => 'lazy' ) );
		if ( ! $image ) {
			continue;
		}
		$full   = wp_get_attachment_image_url( $id, 'full' );
		$items .= '<a class="bs-gallery__item bs-ratio bs-ratio--' . esc_attr( $ratio ) . '" href="' . esc_url( $full ) . '"' . ( 'false' !== $a['lightbox'] ? ' data-bs-lightbox' : '' ) . '>' . $image . '</a>';
	}
	$cols = bst_legacy_columns( $atts, 4, 3, 2 );
	if ( 'slider' === $a['type'] ) {
		$html = bst_slider_markup( $items, array( 'd' => $cols['d'], 't' => $cols['t'], 'm' => $cols['m'], 'gap' => 12, 'autoplay' => is_numeric( $a['auto_slide'] ) ? (int) $a['auto_slide'] : 0, 'dots' => false ), 'bs-slider--cards' );
	} else {
		$html = '<div class="bs-block-grid bs-gallery" style="' . esc_attr( bst_style_vars( array( '--bs-cols-d' => $cols['d'], '--bs-cols-t' => $cols['t'], '--bs-cols-m' => $cols['m'] ) ) ) . '">' . $items . '</div>';
	}
	return bst_legacy_wrap( $html, $a, 'bs-legacy-gallery' );
}

function bst_legacy_video( $atts ) {
	$a = shortcode_atts( array( 'url' => '', 'height' => '56.25%', 'class' => '', 'visibility' => '' ), $atts );
	if ( '' === $a['url'] ) {
		return '';
	}
	$embed = wp_oembed_get( esc_url_raw( $a['url'] ) );
	if ( ! $embed ) {
		$embed = '<video src="' . esc_url( $a['url'] ) . '" controls preload="metadata"></video>';
	}
	$ratio = preg_match( '/^([0-9.]+)%$/', $a['height'], $m ) && (float) $m[1] > 0 ? round( 100 / (float) $m[1], 4 ) : 1.7778;
	return '<div class="' . esc_attr( bst_legacy_classes( array( 'bs-video', bst_legacy_visibility( $a['visibility'] ) ), $a['class'] ) ) . '" style="' . esc_attr( 'aspect-ratio:' . $ratio ) . '">' . $embed . '</div>';
}

function bst_legacy_search( $atts ) {
	$a = shortcode_atts( array( 'class' => '', 'visibility' => '' ), $atts );
	return '<div class="' . esc_attr( bst_legacy_classes( array( 'bs-legacy-search', bst_legacy_visibility( $a['visibility'] ) ), $a['class'] ) ) . '">' . bst_search_form( 'bs-search-form--header' ) . '</div>';
}

/**
 * [follow facebook="…" instagram="…"] – explicit URLs, falling back to the theme's social profiles.
 */
function bst_legacy_follow( $atts ) {
	$atts = (array) $atts;
	$out  = '';
	foreach ( array( 'facebook', 'instagram', 'youtube', 'tiktok', 'linkedin', 'pinterest', 'twitter' => 'x', 'x' ) as $key => $network ) {
		$attr = is_string( $key ) ? $key : $network;
		if ( empty( $atts[ $attr ] ) ) {
			continue;
		}
		$out .= '<a class="bs-social__link bs-social__link--' . esc_attr( $network ) . '" href="' . esc_url( $atts[ $attr ] ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr( ucfirst( $network ) ) . '">' . bst_icon( $network, 18 ) . '</a>';
	}
	if ( ! empty( $atts['email'] ) ) {
		$out .= '<a class="bs-social__link" href="' . esc_attr( 'mailto:' . sanitize_email( $atts['email'] ) ) . '" aria-label="Email">' . bst_icon( 'mail', 18 ) . '</a>';
	}
	if ( ! empty( $atts['phone'] ) ) {
		$out .= '<a class="bs-social__link" href="' . esc_attr( 'tel:' . preg_replace( '/[^0-9+]/', '', $atts['phone'] ) ) . '" aria-label="Phone">' . bst_icon( 'phone', 18 ) . '</a>';
	}
	return $out ? '<div class="bs-social bs-legacy-follow">' . $out . '</div>' : bst_social_icons( 'bs-legacy-follow' );
}

function bst_legacy_scroll_to( $atts ) {
	$a = shortcode_atts( array( 'link' => '', 'title' => '' ), $atts );
	$id = sanitize_title( ltrim( $a['link'], '#' ) );
	return $id ? '<span id="' . esc_attr( $id ) . '" class="bs-legacy-anchor"></span>' : '';
}

function bst_legacy_price_table( $atts, $content = '' ) {
	$a = shortcode_atts( array( 'title' => '', 'price' => '', 'description' => '', 'featured' => '', 'button_text' => '', 'button_link' => '', 'class' => '' ), $atts );
	$classes = array( 'wp-block-group', 'is-style-card', 'bs-legacy-price', 'true' === $a['featured'] ? 'bs-pricing-featured' : '' );
	$out     = '<div class="' . esc_attr( bst_legacy_classes( $classes, $a['class'] ) ) . '">';
	$out    .= '' !== $a['title'] ? '<h3 class="has-text-align-center">' . wp_kses_post( $a['title'] ) . '</h3>' : '';
	$out    .= '' !== $a['price'] ? '<p class="has-text-align-center bs-price">' . wp_kses_post( $a['price'] ) . '</p>' : '';
	$out    .= '' !== $a['description'] ? '<p class="has-text-align-center">' . wp_kses_post( $a['description'] ) . '</p>' : '';
	$out    .= '<ul class="is-style-check">' . bst_legacy_content( $content ) . '</ul>';
	if ( '' !== $a['button_text'] && '' !== $a['button_link'] ) {
		$out .= '<p class="has-text-align-center"><a class="bs-btn bs-btn--primary" href="' . esc_url( $a['button_link'] ) . '">' . esc_html( $a['button_text'] ) . '</a></p>';
	}
	return $out . '</div>';
}

function bst_legacy_bullet_item( $atts ) {
	$a = shortcode_atts( array( 'text' => '', 'enabled' => 'true' ), $atts );
	return '<li' . ( 'false' === $a['enabled'] ? ' class="is-disabled"' : '' ) . '>' . wp_kses_post( $a['text'] ) . '</li>';
}
