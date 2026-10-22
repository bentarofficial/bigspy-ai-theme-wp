<?php
/**
 * Legacy layout shortcodes.
 *
 * Renders pages that were built with Flatsome's UX Builder shortcodes ([section], [row], [col],
 * [ux_image], [button]…) with BIGSPY markup, so an existing site can switch to this theme without
 * rebuilding its content. Content, images and URLs stay exactly as they are stored in the database;
 * only the presentation changes. A shortcode is skipped when something else already provides it.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shortcode tag => callback.
 */
function bst_legacy_tags() {
	return array(
		'section'        => 'bst_legacy_section',
		'row'            => 'bst_legacy_row',
		'row_inner'      => 'bst_legacy_row',
		'row_inner_1'    => 'bst_legacy_row',
		'row_inner_2'    => 'bst_legacy_row',
		'col'            => 'bst_legacy_col',
		'col_inner'      => 'bst_legacy_col',
		'col_inner_1'    => 'bst_legacy_col',
		'col_inner_2'    => 'bst_legacy_col',
		'ux_text'        => 'bst_legacy_text',
		'ux_html'        => 'bst_legacy_html',
		'ux_image'       => 'bst_legacy_image',
		'button'         => 'bst_legacy_button',
		'gap'            => 'bst_legacy_gap',
		'accordion'      => 'bst_legacy_accordion',
		'accordion-item' => 'bst_legacy_accordion_item',
		'title'          => 'bst_legacy_title',
		'blog_posts'     => 'bst_legacy_blog_posts',
		'team_member'    => 'bst_legacy_team_member',
		'testimonial'    => 'bst_legacy_testimonial',
		'ux_slider'      => 'bst_legacy_slider',
		'block'          => 'bst_legacy_block',
	);
}

function bst_legacy_register_shortcodes() {
	foreach ( bst_legacy_tags() as $tag => $callback ) {
		if ( ! shortcode_exists( $tag ) ) {
			add_shortcode( $tag, $callback );
		}
	}
}
add_action( 'init', 'bst_legacy_register_shortcodes', 20 );

/**
 * Keep the "blocks" post type (UX Blocks) so reusable blocks such as the footer stay editable
 * and keep their URLs.
 */
function bst_legacy_register_block_post_type() {
	if ( post_type_exists( 'blocks' ) ) {
		return;
	}
	register_post_type(
		'blocks',
		array(
			'labels'              => array(
				'name'          => __( 'UX Blocks', 'bigspy-ai-theme-wp' ),
				'singular_name' => __( 'UX Block', 'bigspy-ai-theme-wp' ),
				'add_new_item'  => __( 'Add new block', 'bigspy-ai-theme-wp' ),
				'edit_item'     => __( 'Edit block', 'bigspy-ai-theme-wp' ),
			),
			'public'              => true,
			'exclude_from_search' => true,
			'show_in_nav_menus'   => false,
			'show_in_rest'        => true,
			'rest_base'           => 'ux-blocks',
			'menu_icon'           => 'dashicons-screenoptions',
			'supports'            => array( 'title', 'editor', 'revisions' ),
			'rewrite'             => array( 'slug' => 'blocks' ),
		)
	);
}
add_action( 'init', 'bst_legacy_register_block_post_type', 5 );
add_action( 'after_switch_theme', 'flush_rewrite_rules' );

/* ------------------------------------------------------------------------ Helpers */

/**
 * Render nested content and remove the empty paragraphs wpautop leaves around shortcodes.
 */
function bst_legacy_content( $content ) {
	$content = trim( (string) $content );
	if ( has_blocks( $content ) ) {
		$content = do_blocks( $content );
	}
	$content = preg_replace( '#^(?:\s*</p>)+|(?:<p>\s*)+$#i', '', $content );
	$html    = do_shortcode( shortcode_unautop( $content ) );
	return preg_replace( '#<p>(?:\s|&nbsp;|<br\s*/?>)*</p>#i', '', $html );
}

function bst_legacy_first( $value ) {
	$parts = preg_split( '/\s+/', trim( (string) $value ) );
	return $parts ? $parts[0] : '';
}

/**
 * A CSS length: "30", "30px", "2em", "5%"…
 */
function bst_legacy_length( $value, $default = '' ) {
	$value = bst_legacy_first( $value );
	if ( '' === $value ) {
		return $default;
	}
	if ( is_numeric( $value ) ) {
		return $value . 'px';
	}
	return preg_match( '/^-?[0-9.]+(px|em|rem|%|vh|vw)$/', $value ) ? $value : $default;
}

/**
 * Padding / margin shorthand with up to four lengths.
 */
function bst_legacy_box( $value ) {
	$value = trim( (string) $value );
	return preg_match( '/^(-?[0-9.]+(px|em|rem|%)?\s*){1,4}$/', $value ) ? $value : '';
}

function bst_legacy_color( $value ) {
	$value = trim( (string) $value );
	if ( preg_match( '/^#[0-9a-fA-F]{3,8}$/', $value ) || preg_match( '/^rgba?\([0-9., %]+\)$/', $value ) ) {
		return $value;
	}
	return '';
}

function bst_legacy_span( $value, $default ) {
	$span = (int) round( (float) $value );
	return ( $span >= 1 && $span <= 12 ) ? $span : $default;
}

/**
 * Flatsome visibility option => BIGSPY responsive classes.
 */
function bst_legacy_visibility( $value ) {
	$map = array(
		'hidden'          => 'bs-hide-desktop bs-hide-tablet bs-hide-mobile',
		'hide-for-small'  => 'bs-hide-mobile',
		'show-for-small'  => 'bs-hide-desktop bs-hide-tablet',
		'hide-for-medium' => 'bs-hide-tablet bs-hide-mobile',
		'show-for-medium' => 'bs-hide-desktop',
		'hide-for-large'  => 'bs-hide-desktop',
	);
	return $map[ trim( (string) $value ) ] ?? '';
}

/**
 * Join classes, keeping user classes safe.
 */
function bst_legacy_classes( $classes, $extra = '' ) {
	foreach ( preg_split( '/\s+/', trim( (string) $extra ) ) as $class ) {
		if ( '' !== $class ) {
			$classes[] = sanitize_html_class( $class );
		}
	}
	return implode( ' ', array_filter( array_map( 'trim', $classes ) ) );
}

function bst_legacy_image_url( $value, $size = 'full' ) {
	if ( is_numeric( $value ) ) {
		$url = wp_get_attachment_image_url( (int) $value, $size );
		return $url ? $url : '';
	}
	return esc_url_raw( (string) $value );
}

/* ------------------------------------------------------------------------ Layout */

function bst_legacy_section( $atts, $content = '' ) {
	$a = shortcode_atts(
		array(
			'label'       => '',
			'bg'          => '',
			'bg_color'    => '',
			'bg_overlay'  => '',
			'bg_pos'      => '',
			'bg_size'     => '',
			'padding'     => '30px',
			'padding__md' => '',
			'padding__sm' => '',
			'height'      => '',
			'dark'        => '',
			'class'       => '',
			'visibility'  => '',
		),
		$atts,
		'section'
	);

	$pd = bst_legacy_length( $a['padding'], '30px' );
	$pt = bst_legacy_length( $a['padding__md'], $pd );
	$pm = bst_legacy_length( $a['padding__sm'], $pt );

	$classes = array( 'bs-section', 'bs-section--boxed', 'bs-legacy-section', bst_legacy_visibility( $a['visibility'] ) );
	if ( 'true' === $a['dark'] ) {
		$classes[] = 'is-scheme-light';
	}

	$style = bst_style_vars(
		array(
			'--bs-sec-pd'      => $pd,
			'--bs-sec-pt'      => $pt,
			'--bs-sec-pm'      => $pm,
			'--bs-sec-min'     => bst_legacy_length( $a['height'] ),
			'background-color' => bst_legacy_color( $a['bg_color'] ),
		)
	);

	$out = '<section class="' . esc_attr( bst_legacy_classes( $classes, $a['class'] ) ) . '"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '>';

	$image = $a['bg'] ? bst_legacy_image_url( $a['bg'] ) : '';
	if ( $image ) {
		$size     = in_array( $a['bg_size'], array( 'cover', 'contain', 'auto' ), true ) ? $a['bg_size'] : 'cover';
		$position = preg_match( '/^[0-9.]+% [0-9.]+%$/', $a['bg_pos'] ) ? $a['bg_pos'] : 'center center';
		$out     .= '<div class="bs-section__bg" style="' . esc_attr( 'background-image:url(' . esc_url( $image ) . ');background-size:' . $size . ';background-position:' . $position ) . '" aria-hidden="true"></div>';
	}
	$overlay = bst_legacy_color( $a['bg_overlay'] );
	if ( $overlay ) {
		$out .= '<div class="bs-section__overlay" style="' . esc_attr( 'background:' . $overlay ) . '" aria-hidden="true"></div>';
	}

	$out .= '<div class="bs-section__inner">' . bst_legacy_content( $content ) . '</div></section>';
	return $out;
}

function bst_legacy_row( $atts, $content = '' ) {
	$a = shortcode_atts(
		array(
			'style'      => '',
			'v_align'    => '',
			'h_align'    => '',
			'class'      => '',
			'visibility' => '',
		),
		$atts
	);

	$gaps    = array( 'collapse' => 'none', 'small' => 'small', 'large' => 'large' );
	$valigns = array( 'middle' => 'center', 'bottom' => 'bottom', 'equal' => 'stretch' );
	$justify = array( 'center' => 'center', 'right' => 'end' );

	$classes = array(
		'bs-row',
		'bs-legacy-row',
		'bs-row--gap-' . ( $gaps[ $a['style'] ] ?? 'medium' ),
		'bs-row--valign-' . ( $valigns[ $a['v_align'] ] ?? 'top' ),
		'bs-row--justify-' . ( $justify[ $a['h_align'] ] ?? 'start' ),
		bst_legacy_visibility( $a['visibility'] ),
	);

	// wpautop leaves <br> / <p> between columns; they would take a grid cell.
	$html = preg_replace( '#(^|</div>)(?:\s|<br\s*/?>|</?p>)+(?=<div class="bs-column|$)#i', '$1', bst_legacy_content( $content ) );
	// Drop columns that rendered nothing (e.g. an image that was deleted from the library).
	$html = preg_replace( '#<div class="bs-column[^"]*" style="[^"]*"><div class="col-inner[^"]*"(?: style="[^"]*")?>\s*</div></div>#', '', $html );

	// A row (or its last wrapped line) that does not fill the 12-column grid is centered instead of leaning left.
	$spans = bst_legacy_row_spans( $html );
	if ( $spans && ( 0 !== array_sum( $spans['d'] ) % 12 || 0 !== array_sum( $spans['t'] ) % 12 ) ) {
		$classes[] = 'bs-legacy-row--centered';
	}

	return '<div class="' . esc_attr( bst_legacy_classes( $classes, $a['class'] ) ) . '">' . $html . '</div>';
}

/**
 * Desktop / tablet spans of the direct child columns of a rendered row.
 *
 * @param string $html Row inner HTML.
 * @return array{d:int[],t:int[]}|array
 */
function bst_legacy_row_spans( $html ) {
	if ( ! preg_match_all( '#<div\b[^>]*>|</div>#i', $html, $tags, PREG_SET_ORDER ) ) {
		return array();
	}
	$depth = 0;
	$spans = array( 'd' => array(), 't' => array() );
	foreach ( $tags as $tag ) {
		if ( '</' === substr( $tag[0], 0, 2 ) ) {
			--$depth;
			continue;
		}
		if ( 0 === $depth && preg_match( '#--bs-span-d:(\d+);--bs-span-t:(\d+)#', $tag[0], $m ) ) {
			$spans['d'][] = (int) $m[1];
			$spans['t'][] = (int) $m[2];
		}
		++$depth;
	}
	return $spans['d'] ? $spans : array();
}

function bst_legacy_col( $atts, $content = '' ) {
	$a = shortcode_atts(
		array(
			'span'       => '12',
			'span__md'   => '',
			'span__sm'   => '',
			'align'      => '',
			'bg_color'   => '',
			'depth'      => '',
			'padding'    => '',
			'margin'     => '',
			'color'      => '',
			'class'      => '',
			'visibility' => '',
		),
		$atts
	);

	$desktop = bst_legacy_span( $a['span'], 12 );
	$tablet  = '' !== $a['span__md'] ? bst_legacy_span( $a['span__md'], $desktop ) : $desktop;
	$mobile  = '' !== $a['span__sm'] ? bst_legacy_span( $a['span__sm'], 12 ) : 12;

	$classes = array( 'bs-column', 'bs-legacy-col', bst_legacy_visibility( $a['visibility'] ) );
	if ( in_array( $a['align'], array( 'left', 'center', 'right' ), true ) ) {
		$classes[] = 'has-text-align-' . $a['align'];
	}
	if ( 'light' === $a['color'] ) {
		$classes[] = 'is-scheme-light';
	}

	$inner = array( 'col-inner' );
	$depth = (int) $a['depth'];
	if ( $depth > 0 ) {
		$inner[] = 'box-shadow-' . min( 5, $depth );
	}

	$style = bst_style_vars(
		array(
			'--bs-span-d' => $desktop,
			'--bs-span-t' => $tablet,
			'--bs-span-m' => $mobile,
		)
	);
	$inner_style = bst_style_vars(
		array(
			'background-color' => bst_legacy_color( $a['bg_color'] ),
			'padding'          => bst_legacy_box( $a['padding'] ),
			'margin'           => bst_legacy_box( $a['margin'] ),
		)
	);

	return '<div class="' . esc_attr( bst_legacy_classes( $classes, $a['class'] ) ) . '" style="' . esc_attr( $style ) . '"><div class="' . esc_attr( implode( ' ', $inner ) ) . '"' . ( $inner_style ? ' style="' . esc_attr( $inner_style ) . '"' : '' ) . '>' . bst_legacy_content( $content ) . '</div></div>';
}

function bst_legacy_gap( $atts ) {
	$a = shortcode_atts(
		array(
			'height'     => '30px',
			'height__md' => '',
			'height__sm' => '',
			'class'      => '',
			'visibility' => '',
		),
		$atts
	);
	$d = bst_legacy_length( $a['height'], '30px' );
	$t = bst_legacy_length( $a['height__md'], $d );
	$m = bst_legacy_length( $a['height__sm'], $t );

	$classes = array( 'bs-legacy-gap', bst_legacy_visibility( $a['visibility'] ) );
	return '<div class="' . esc_attr( bst_legacy_classes( $classes, $a['class'] ) ) . '" style="' . esc_attr( '--bs-gap-d:' . $d . ';--bs-gap-t:' . $t . ';--bs-gap-m:' . $m ) . '" aria-hidden="true"></div>';
}

/* ------------------------------------------------------------------------ Content elements */

function bst_legacy_text( $atts, $content = '' ) {
	$a = shortcode_atts(
		array(
			'text_align'  => '',
			'text_color'  => '',
			'font_size'   => '',
			'line_height' => '',
			'class'       => '',
			'visibility'  => '',
		),
		$atts
	);
	$style = bst_style_vars(
		array(
			'text-align'  => in_array( $a['text_align'], array( 'left', 'center', 'right', 'justify' ), true ) ? $a['text_align'] : '',
			'color'       => bst_legacy_color( $a['text_color'] ),
			'font-size'   => is_numeric( $a['font_size'] ) ? (float) $a['font_size'] . 'rem' : '',
			'line-height' => is_numeric( $a['line_height'] ) ? (float) $a['line_height'] : '',
		)
	);
	$classes = array( 'bs-legacy-text', bst_legacy_visibility( $a['visibility'] ) );
	return '<div class="' . esc_attr( bst_legacy_classes( $classes, $a['class'] ) ) . '"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '>' . bst_legacy_content( $content ) . '</div>';
}

/**
 * Raw HTML is output as stored, like in the original builder.
 */
function bst_legacy_html( $atts, $content = '' ) {
	$a    = shortcode_atts( array( 'visibility' => '', 'class' => '' ), $atts );
	$html = bst_legacy_content( $content );
	$vis  = bst_legacy_visibility( $a['visibility'] );
	if ( $vis || '' !== trim( $a['class'] ) ) {
		$html = '<div class="' . esc_attr( bst_legacy_classes( array( 'bs-legacy-html', $vis ), $a['class'] ) ) . '">' . $html . '</div>';
	}
	return $html;
}

function bst_legacy_image( $atts ) {
	$a = shortcode_atts(
		array(
			'id'              => '',
			'org_img'         => '',
			'image_size'      => 'large',
			'width'           => '',
			'link'            => '',
			'target'          => '',
			'rel'             => '',
			'caption'         => '',
			'image_radius'    => '',
			'image_hover'     => '',
			'image_hover_alt' => '',
			'margin'          => '',
			'class'           => '',
			'visibility'      => '',
		),
		$atts
	);

	$img = '';
	if ( is_numeric( $a['id'] ) && (int) $a['id'] > 0 ) {
		$size = sanitize_key( $a['image_size'] );
		$img  = wp_get_attachment_image( (int) $a['id'], $size ? $size : 'large', false, array( 'class' => 'bs-legacy-image__img' ) );
	} elseif ( '' !== $a['id'] || '' !== $a['org_img'] ) {
		$src = '' !== $a['org_img'] ? $a['org_img'] : $a['id'];
		$img = '<img class="bs-legacy-image__img" src="' . esc_url( $src ) . '" alt="" loading="lazy" decoding="async">';
	}
	if ( '' === $img ) {
		return '';
	}

	if ( '' !== $a['link'] ) {
		$target = '_blank' === $a['target'] ? ' target="_blank" rel="noopener"' : ( $a['rel'] ? ' rel="' . esc_attr( $a['rel'] ) . '"' : '' );
		$img    = '<a href="' . esc_url( $a['link'] ) . '"' . $target . '>' . $img . '</a>';
	}

	$classes = array( 'bs-legacy-image', bst_legacy_visibility( $a['visibility'] ) );
	foreach ( array( $a['image_hover'], $a['image_hover_alt'] ) as $hover ) {
		if ( in_array( $hover, array( 'zoom', 'zoom-long', 'glow', 'grayscale', 'fade-in' ), true ) ) {
			$classes[] = 'bs-legacy-image--' . $hover;
		}
	}

	$width = is_numeric( $a['width'] ) ? min( 100, max( 1, (float) $a['width'] ) ) : 0;
	$style = bst_style_vars(
		array(
			'width'         => $width && $width < 100 ? $width . '%' : '',
			'margin'        => bst_legacy_box( $a['margin'] ),
			'--bs-img-radius' => is_numeric( $a['image_radius'] ) ? (float) $a['image_radius'] . '%' : '',
		)
	);

	$caption = '';
	if ( 'true' === $a['caption'] && is_numeric( $a['id'] ) ) {
		$text    = wp_get_attachment_caption( (int) $a['id'] );
		$caption = $text ? '<div class="bs-legacy-image__caption">' . esc_html( $text ) . '</div>' : '';
	}

	return '<div class="' . esc_attr( bst_legacy_classes( $classes, $a['class'] ) ) . '"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '>' . $img . $caption . '</div>';
}

function bst_legacy_button( $atts, $content = '' ) {
	$a = shortcode_atts(
		array(
			'text'        => '',
			'link'        => '',
			'target'      => '',
			'rel'         => '',
			'color'       => 'primary',
			'style'       => '',
			'size'        => '',
			'radius'      => '',
			'expand'      => '',
			'icon'        => '',
			'icon_pos'    => '',
			'letter_case' => '',
			'depth'       => '',
			'class'       => '',
			'visibility'  => '',
		),
		$atts
	);

	$text = '' !== $a['text'] ? $a['text'] : trim( wp_strip_all_tags( (string) $content ) );

	$classes = array( 'bs-btn', 'bs-legacy-btn', bst_legacy_visibility( $a['visibility'] ) );
	$colors  = array( 'secondary', 'success', 'alert', 'white' );
	if ( in_array( $a['color'], $colors, true ) ) {
		$classes[] = 'bs-legacy-btn--' . $a['color'];
	}
	if ( in_array( $a['style'], array( 'outline', 'bevel-outline' ), true ) ) {
		$classes[] = 'bs-btn--outline';
	} elseif ( in_array( $a['style'], array( 'link', 'underline' ), true ) ) {
		$classes[] = 'bs-btn--link';
		$classes[] = 'bs-legacy-btn--' . $a['style'];
	} elseif ( 'shade' === $a['style'] ) {
		$classes[] = 'bs-legacy-btn--shade';
	}
	if ( in_array( $a['size'], array( 'xxsmall', 'xsmall', 'small', 'smaller' ), true ) ) {
		$classes[] = 'bs-btn--sm';
	} elseif ( in_array( $a['size'], array( 'large', 'larger', 'xlarge', 'xxlarge' ), true ) ) {
		$classes[] = 'bs-btn--lg';
	}
	if ( 'true' === $a['expand'] ) {
		$classes[] = 'bs-btn--block';
	}
	$classes[] = 'lowercase' === $a['letter_case'] ? 'bs-legacy-btn--normal-case' : 'bs-legacy-btn--uppercase';
	if ( (int) $a['depth'] > 0 ) {
		$classes[] = 'bs-legacy-btn--depth';
	}

	$icon = '';
	if ( '' !== $a['icon'] ) {
		$icons = array(
			'angle-down'  => 'chevron-down',
			'angle-right' => 'chevron-right',
			'arrow-right' => 'arrow-right',
			'phone'       => 'phone',
			'envelop'     => 'mail',
		);
		foreach ( $icons as $needle => $name ) {
			if ( false !== strpos( $a['icon'], $needle ) ) {
				$icon = bst_icon( $name, 16 );
				break;
			}
		}
	}
	$label = '<span>' . wp_kses_post( $text ) . '</span>';
	$inner = 'left' === $a['icon_pos'] ? $icon . $label : $label . $icon;

	$style = is_numeric( $a['radius'] ) ? 'border-radius:' . (int) $a['radius'] . 'px' : '';
	$attrs = '';
	if ( '_blank' === $a['target'] ) {
		$attrs .= ' target="_blank" rel="noopener"';
	} elseif ( '' !== $a['rel'] ) {
		$attrs .= ' rel="' . esc_attr( $a['rel'] ) . '"';
	}
	$href = '' !== $a['link'] ? esc_url( $a['link'] ) : '#';

	return '<a class="' . esc_attr( bst_legacy_classes( $classes, $a['class'] ) ) . '" href="' . $href . '"' . $attrs . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '>' . $inner . '</a>';
}

function bst_legacy_accordion( $atts, $content = '' ) {
	$a     = shortcode_atts( array( 'title' => '', 'auto_open' => '', 'class' => '' ), $atts );
	$title = '' !== $a['title'] ? '<h3 class="bs-legacy-accordion__title">' . wp_kses_post( $a['title'] ) . '</h3>' : '';
	$html  = bst_legacy_content( $content );
	if ( 'true' === $a['auto_open'] ) {
		$html = preg_replace( '/<details class="bs-accordion__item"/', '<details class="bs-accordion__item" open', $html, 1 );
	}
	return $title . '<div class="' . esc_attr( bst_legacy_classes( array( 'bs-accordion', 'bs-accordion--bordered', 'bs-legacy-accordion' ), $a['class'] ) ) . '" data-single="0">' . $html . '</div>';
}

function bst_legacy_accordion_item( $atts, $content = '' ) {
	$a = shortcode_atts( array( 'title' => '', 'open' => '' ), $atts );
	return '<details class="bs-accordion__item"' . ( 'true' === $a['open'] ? ' open' : '' ) . '><summary class="bs-accordion__title"><span>' . wp_kses_post( $a['title'] ) . '</span>' . bst_icon( 'chevron-down', 18 ) . '</summary><div class="bs-accordion__content">' . bst_legacy_content( $content ) . '</div></details>';
}

function bst_legacy_title( $atts ) {
	$a = shortcode_atts(
		array(
			'text'       => '',
			'tag_name'   => 'h3',
			'style'      => 'normal',
			'color'      => '',
			'link'       => '',
			'link_text'  => '',
			'class'      => '',
			'visibility' => '',
		),
		$atts
	);
	$tag   = in_array( $a['tag_name'], array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div' ), true ) ? $a['tag_name'] : 'h3';
	$color = bst_legacy_color( $a['color'] );
	$style = in_array( $a['style'], array( 'center', 'normal', 'bold', 'bold-center' ), true ) ? $a['style'] : 'normal';

	$link = '';
	if ( '' !== $a['link'] && '' !== $a['link_text'] ) {
		$link = '<a class="bs-legacy-title__link" href="' . esc_url( $a['link'] ) . '">' . wp_kses_post( $a['link_text'] ) . bst_icon( 'chevron-right', 14 ) . '</a>';
	}

	$classes = array( 'bs-legacy-title', 'bs-legacy-title--' . $style, bst_legacy_visibility( $a['visibility'] ) );
	return '<div class="' . esc_attr( bst_legacy_classes( $classes, $a['class'] ) ) . '"><' . $tag . ' class="bs-legacy-title__text"' . ( $color ? ' style="' . esc_attr( 'color:' . $color ) . '"' : '' ) . '>' . wp_kses_post( $a['text'] ) . '</' . $tag . '>' . $link . '</div>';
}

function bst_legacy_blog_posts( $atts ) {
	$a = shortcode_atts(
		array(
			'type'         => 'slider',
			'posts'        => '8',
			'offset'       => '',
			'cat'          => '',
			'ids'          => '',
			'orderby'      => 'date',
			'order'        => 'DESC',
			'columns'      => '4',
			'columns__md'  => '',
			'columns__sm'  => '',
			'excerpt'      => 'visible',
			'auto_slide'   => '',
			'image_height' => '56.25%',
			'class'        => '',
			'visibility'   => '',
		),
		$atts
	);

	$args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => max( 1, min( 1000, (int) $a['posts'] ) ),
		'offset'              => max( 0, (int) $a['offset'] ),
		'orderby'             => in_array( $a['orderby'], array( 'date', 'modified', 'title', 'rand', 'comment_count', 'menu_order' ), true ) ? $a['orderby'] : 'date',
		'order'               => 'ASC' === strtoupper( $a['order'] ) ? 'ASC' : 'DESC',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);
	if ( '' !== $a['ids'] ) {
		$args['post__in'] = array_map( 'absint', explode( ',', $a['ids'] ) );
		$args['orderby']  = 'post__in';
	} elseif ( '' !== $a['cat'] ) {
		$cats = array_filter( array_map( 'trim', explode( ',', $a['cat'] ) ) );
		if ( $cats && ctype_digit( implode( '', $cats ) ) ) {
			$args['cat'] = implode( ',', array_map( 'absint', $cats ) );
		} else {
			$args['category_name'] = implode( ',', array_map( 'sanitize_title', $cats ) );
		}
	}

	$query = new WP_Query( $args );
	if ( ! $query->have_posts() ) {
		return '';
	}

	$ratio = '56.25%' === $a['image_height'] ? '16-9' : ( '75%' === $a['image_height'] ? '4-3' : ( '100%' === $a['image_height'] ? '1-1' : '16-9' ) );
	ob_start();
	while ( $query->have_posts() ) {
		$query->the_post();
		bst_post_card(
			array(
				'style'   => 'shadow',
				'ratio'   => $ratio,
				'meta'    => false,
				'excerpt' => 'false' !== $a['excerpt'],
				'more'    => '',
				'heading' => 'h5',
			)
		);
	}
	wp_reset_postdata();
	$items = ob_get_clean();

	$d = max( 1, min( 6, (int) $a['columns'] ) );
	$t = '' !== $a['columns__md'] ? max( 1, min( 4, (int) $a['columns__md'] ) ) : min( 2, $d );
	$m = '' !== $a['columns__sm'] ? max( 1, min( 2, (int) $a['columns__sm'] ) ) : 1;

	$classes = bst_legacy_classes( array( 'bs-posts-block', 'bs-legacy-posts', bst_legacy_visibility( $a['visibility'] ) ), $a['class'] );

	if ( 'row' === $a['type'] || 'grid' === $a['type'] || 'masonry' === $a['type'] ) {
		// Pick column counts that divide the number of posts, so no row ends with a lone card.
		$count = (int) $query->post_count;
		$d     = bst_legacy_balanced_columns( $d, $count );
		$t     = bst_legacy_balanced_columns( $t, $count );
		$m     = bst_legacy_balanced_columns( $m, $count );
		$html = '<div class="bs-block-grid bs-posts bs-posts--grid" style="' . esc_attr( bst_style_vars( array( '--bs-cols-d' => $d, '--bs-cols-t' => $t, '--bs-cols-m' => $m ) ) ) . '">' . $items . '</div>';
	} else {
		$html = bst_slider_markup(
			$items,
			array(
				'd'        => $d,
				't'        => $t,
				'm'        => $m,
				'gap'      => 24,
				'autoplay' => max( 0, (int) $a['auto_slide'] ),
				'dots'     => true,
				'loop'     => true,
			),
			'bs-slider--cards'
		);
	}
	return '<div class="' . esc_attr( $classes ) . '">' . $html . '</div>';
}

/**
 * Largest column count <= $columns that divides $count (fewer items than columns fill one row).
 */
function bst_legacy_balanced_columns( $columns, $count ) {
	if ( $count < 1 ) {
		return $columns;
	}
	if ( $count <= $columns ) {
		return $count;
	}
	for ( $c = $columns; $c > 1; $c-- ) {
		if ( 0 === $count % $c ) {
			return $c;
		}
	}
	return $columns;
}

function bst_legacy_team_member( $atts, $content = '' ) {
	$a = shortcode_atts(
		array(
			'img'          => '',
			'name'         => '',
			'title'        => '',
			'image_width'  => '',
			'image_radius' => '',
			'class'        => '',
			'visibility'   => '',
		),
		$atts
	);
	$image = is_numeric( $a['img'] ) ? wp_get_attachment_image( (int) $a['img'], 'medium', false, array( 'class' => 'bs-legacy-person__img' ) ) : '';
	$style = bst_style_vars(
		array(
			'width'         => is_numeric( $a['image_width'] ) ? min( 100, (float) $a['image_width'] ) . '%' : '',
			'border-radius' => is_numeric( $a['image_radius'] ) ? (float) $a['image_radius'] . '%' : '',
		)
	);

	$out  = '<div class="' . esc_attr( bst_legacy_classes( array( 'bs-legacy-person', bst_legacy_visibility( $a['visibility'] ) ), $a['class'] ) ) . '">';
	$out .= $image ? '<div class="bs-legacy-person__media"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '>' . $image . '</div>' : '';
	$out .= '<div class="bs-legacy-person__name">' . wp_kses_post( $a['name'] ) . '</div>';
	$out .= '' !== $a['title'] ? '<div class="bs-legacy-person__title">' . wp_kses_post( $a['title'] ) . '</div>' : '';
	$out .= '<div class="bs-legacy-person__text">' . bst_legacy_content( $content ) . '</div></div>';
	return $out;
}

function bst_legacy_testimonial( $atts, $content = '' ) {
	$a = shortcode_atts(
		array(
			'image'   => '',
			'name'    => '',
			'company' => '',
			'stars'   => '5',
			'class'   => '',
		),
		$atts
	);
	$stars = max( 0, min( 5, (int) $a['stars'] ) );
	$out   = '<div class="' . esc_attr( bst_legacy_classes( array( 'bs-legacy-testimonial' ), $a['class'] ) ) . '">';
	if ( is_numeric( $a['image'] ) ) {
		$out .= '<div class="bs-legacy-testimonial__media">' . wp_get_attachment_image( (int) $a['image'], 'thumbnail' ) . '</div>';
	}
	$out .= '<div class="bs-legacy-testimonial__body">';
	if ( $stars ) {
		$out .= '<div class="bs-legacy-testimonial__stars" aria-label="' . esc_attr( $stars . '/5' ) . '">' . str_repeat( '★', $stars ) . '</div>';
	}
	$out .= '<div class="bs-legacy-testimonial__text">' . bst_legacy_content( $content ) . '</div>';
	if ( '' !== $a['name'] || '' !== $a['company'] ) {
		$out .= '<div class="bs-legacy-testimonial__author"><strong>' . wp_kses_post( $a['name'] ) . '</strong>' . ( '' !== $a['company'] ? ' <span>' . wp_kses_post( $a['company'] ) . '</span>' : '' ) . '</div>';
	}
	$out .= '</div></div>';
	return $out;
}

/**
 * Every top-level shortcode inside the slider becomes one slide.
 */
function bst_legacy_slider( $atts, $content = '' ) {
	$a = shortcode_atts(
		array(
			'timer'      => '6000',
			'auto_slide' => 'true',
			'arrows'     => 'true',
			'bullets'    => 'true',
			'class'      => '',
			'visibility' => '',
		),
		$atts
	);

	$items = '';
	if ( preg_match_all( '/' . get_shortcode_regex() . '/', (string) $content, $matches ) ) {
		foreach ( $matches[0] as $slide ) {
			$items .= '<div class="bs-legacy-slide">' . bst_legacy_content( $slide ) . '</div>';
		}
	}
	if ( '' === $items ) {
		$items = '<div class="bs-legacy-slide">' . bst_legacy_content( $content ) . '</div>';
	}

	$html = bst_slider_markup(
		$items,
		array(
			'd'        => 1,
			't'        => 1,
			'm'        => 1,
			'gap'      => 0,
			'autoplay' => 'false' === $a['auto_slide'] ? 0 : max( 1000, (int) $a['timer'] ),
			'arrows'   => 'false' !== $a['arrows'],
			'dots'     => 'false' !== $a['bullets'],
			'loop'     => true,
		),
		'bs-legacy-slider'
	);
	$vis = bst_legacy_visibility( $a['visibility'] );
	return ( $vis || '' !== $a['class'] ) ? '<div class="' . esc_attr( bst_legacy_classes( array( $vis ), $a['class'] ) ) . '">' . $html . '</div>' : $html;
}

/**
 * [block id="123"] or [block id="footer"] – output another block/page's content.
 */
function bst_legacy_block( $atts ) {
	static $depth = 0;
	$a = shortcode_atts( array( 'id' => '' ), $atts );
	if ( '' === $a['id'] || $depth > 3 ) {
		return '';
	}
	$post = is_numeric( $a['id'] ) ? get_post( (int) $a['id'] ) : get_page_by_path( sanitize_title( $a['id'] ), OBJECT, array( 'blocks', 'page' ) );
	if ( ! $post || 'publish' !== $post->post_status ) {
		return '';
	}
	++$depth;
	$html = bst_legacy_content( $post->post_content );
	--$depth;
	return $html;
}

/* ------------------------------------------------------------------------ Page integration */

/**
 * Does this post's content use the legacy builder shortcodes?
 */
function bst_legacy_is_builder_content( $post = null ) {
	$post = get_post( $post );
	return $post && preg_match( '/\[(section|row|col|ux_[a-z_]+|title|blog_posts|gap|accordion|team_member|testimonial|block|tabgroup|text_box|featured_box|message_box|divider)[\s\]]/', $post->post_content );
}

/**
 * Builder pages carry their own heading and layout: no automatic title, no sidebar.
 */
function bst_legacy_show_title( $show ) {
	return ( is_page() && bst_legacy_is_builder_content() ) ? false : $show;
}
add_filter( 'bst_show_title', 'bst_legacy_show_title' );

function bst_legacy_sidebar( $position ) {
	return ( is_page() && bst_legacy_is_builder_content() ) ? 'none' : $position;
}
add_filter( 'bst_sidebar_position', 'bst_legacy_sidebar' );

/**
 * The published UX Block named "footer" (or the one chosen with the `legacy_footer_block` theme mod)
 * is shown at the top of the site footer, as it was with the previous theme.
 */
function bst_legacy_footer_block() {
	if ( function_exists( 'bst_footer_block_post' ) && bst_footer_block_post() ) {
		return; // Chosen in Customizer → Footer widgets → Footer từ UX Block.
	}
	$id = absint( get_theme_mod( 'legacy_footer_block', 0 ) );
	if ( $id ) {
		$post = get_post( $id );
	} else {
		$post = post_type_exists( 'blocks' ) ? get_page_by_path( 'footer', OBJECT, 'blocks' ) : null;
	}
	if ( ! $post || 'publish' !== $post->post_status ) {
		return;
	}
	echo '<div class="bs-legacy-footer">' . bst_legacy_content( $post->post_content ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'bst_footer_start', 'bst_legacy_footer_block' );

function bst_legacy_assets() {
	wp_enqueue_style( 'bst-legacy', BST_URI . '/assets/css/legacy.css', array( 'bst-main' ), bst_asset_version( 'assets/css/legacy.css' ) );
}
add_action( 'wp_enqueue_scripts', 'bst_legacy_assets', 20 );
