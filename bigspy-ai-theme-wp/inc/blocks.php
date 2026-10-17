<?php
/**
 * Theme blocks, block styles, editor palette and the responsive / animation
 * options added to every block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

function bst_block_names() {
	// Shop blocks are always registered so pages keep working if WooCommerce is
	// deactivated later; they only appear in the inserter while WooCommerce is active
	// (see blocks-editor.js) and render nothing without it.
	return array( 'section', 'row', 'column', 'banner', 'slider', 'icon-box', 'title', 'tabs', 'tab', 'accordion', 'accordion-item', 'posts', 'testimonial', 'counter', 'map', 'heading', 'text', 'image', 'buttons', 'button', 'divider', 'spacer', 'code', 'products', 'product-categories' );
}

function bst_register_blocks() {
	wp_register_script(
		'bst-blocks-editor',
		BST_URI . '/assets/js/blocks-editor.js',
		array( 'bst-editor-helpers', 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-server-side-render', 'wp-data' ),
		bst_asset_version( 'assets/js/blocks-editor.js' ),
		true
	);

	foreach ( bst_block_names() as $block ) {
		register_block_type( BST_DIR . '/blocks/' . $block );
	}
}
add_action( 'init', 'bst_register_blocks', 20 );

function bst_block_category( $categories ) {
	array_unshift( $categories, array( 'slug' => 'bigspy', 'title' => __( 'BIGSPY AI Builder', 'bigspy-ai-theme-wp' ), 'icon' => null ) );
	return $categories;
}
add_filter( 'block_categories_all', 'bst_block_category' );

/**
 * Extra button styles for core/button.
 */
function bst_block_styles() {
	register_block_style( 'core/button', array( 'name' => 'secondary', 'label' => __( 'Secondary', 'bigspy-ai-theme-wp' ) ) );
	register_block_style( 'core/button', array( 'name' => 'white', 'label' => __( 'White', 'bigspy-ai-theme-wp' ) ) );
	register_block_style( 'core/button', array( 'name' => 'pill', 'label' => __( 'Pill', 'bigspy-ai-theme-wp' ) ) );
	register_block_style( 'core/image', array( 'name' => 'rounded-shadow', 'label' => __( 'Rounded + shadow', 'bigspy-ai-theme-wp' ) ) );
	register_block_style( 'core/group', array( 'name' => 'card', 'label' => __( 'Card', 'bigspy-ai-theme-wp' ) ) );
	register_block_style( 'core/list', array( 'name' => 'check', 'label' => __( 'Check marks', 'bigspy-ai-theme-wp' ) ) );
}
add_action( 'init', 'bst_block_styles' );

/**
 * Register the global responsive / animation attributes on every block so
 * server-rendered blocks accept them in the editor.
 */
function bst_global_block_attributes( $args ) {
	$args['attributes'] = isset( $args['attributes'] ) && is_array( $args['attributes'] ) ? $args['attributes'] : array();
	$args['attributes'] += array(
		'bsHideDesktop'    => array( 'type' => 'boolean', 'default' => false ),
		'bsHideTablet'     => array( 'type' => 'boolean', 'default' => false ),
		'bsHideMobile'     => array( 'type' => 'boolean', 'default' => false ),
		'bsAnimation'      => array( 'type' => 'string', 'default' => '' ),
		'bsAnimationDelay' => array( 'type' => 'number', 'default' => 0 ),
		'bsAnimationDuration' => array( 'type' => 'number', 'default' => 700 ),
		'bsHoverEffect'    => array( 'type' => 'string', 'default' => '' ),
	);
	return $args;
}
add_filter( 'register_block_type_args', 'bst_global_block_attributes' );

function bst_block_animations() {
	return array( 'fade-in', 'fade-up', 'fade-down', 'fade-left', 'fade-right', 'zoom-in', 'zoom-out', 'flip-up', 'blur-in', 'rotate-in', 'bounce-in' );
}

function bst_block_hover_effects() {
	return array( 'lift', 'zoom', 'glow', 'grayscale' );
}

/**
 * Add visibility / animation classes to the first element of a block.
 */
function bst_apply_global_block_attributes( $content, $block ) {
	$attrs = $block['attrs'] ?? array();
	// Never hide server-side previews inside the editor.
	if ( '' === trim( (string) $content ) || empty( $attrs ) || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return $content;
	}

	$classes = array();
	foreach ( array( 'Desktop', 'Tablet', 'Mobile' ) as $device ) {
		if ( ! empty( $attrs[ 'bsHide' . $device ] ) ) {
			$classes[] = 'bs-hide-' . strtolower( $device );
		}
	}
	$animation = isset( $attrs['bsAnimation'] ) ? (string) $attrs['bsAnimation'] : '';
	if ( in_array( $animation, bst_block_animations(), true ) ) {
		$classes[] = 'bs-animate';
		$classes[] = 'bs-animate--' . $animation;
	}
	$hover = isset( $attrs['bsHoverEffect'] ) ? (string) $attrs['bsHoverEffect'] : '';
	if ( in_array( $hover, bst_block_hover_effects(), true ) ) {
		$classes[] = 'bs-hover';
		$classes[] = 'bs-hover--' . $hover;
	}
	if ( ! $classes || ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return $content;
	}

	$tags = new WP_HTML_Tag_Processor( $content );
	if ( ! $tags->next_tag() ) {
		return $content;
	}
	foreach ( $classes as $class ) {
		$tags->add_class( $class );
	}
	$delay = isset( $attrs['bsAnimationDelay'] ) ? absint( $attrs['bsAnimationDelay'] ) : 0;
	if ( $animation && $delay ) {
		$style = (string) $tags->get_attribute( 'style' );
		$tags->set_attribute( 'style', rtrim( $style, '; ' ) . ( $style ? ';' : '' ) . '--bs-delay:' . min( 3000, $delay ) . 'ms' );
	}
	if ( $animation ) {
		$style = (string) $tags->get_attribute( 'style' );
		$duration = isset( $attrs['bsAnimationDuration'] ) ? absint( $attrs['bsAnimationDuration'] ) : 700;
		$tags->set_attribute( 'style', rtrim( $style, '; ' ) . ( $style ? ';' : '' ) . '--bs-duration:' . min( 2500, max( 300, $duration ) ) . 'ms' );
	}
	return $tags->get_updated_html();
}
add_filter( 'render_block', 'bst_apply_global_block_attributes', 10, 2 );

/**
 * Feed Customizer colors and container width into theme.json so the editor
 * palette and layout always match the site.
 */
function bst_theme_json_data( $theme_json ) {
	// Theme JSON can be resolved before `init`; only translate the names once that is safe.
	$t = did_action( 'init' ) ? '__' : static function ( $text ) {
		return $text;
	};

	$palette = array(
		array( 'slug' => 'primary', 'name' => $t( 'Primary', 'bigspy-ai-theme-wp' ), 'color' => bst_mod( 'color_primary' ) ),
		array( 'slug' => 'secondary', 'name' => $t( 'Secondary', 'bigspy-ai-theme-wp' ), 'color' => bst_mod( 'color_secondary' ) ),
		array( 'slug' => 'success', 'name' => $t( 'Success', 'bigspy-ai-theme-wp' ), 'color' => bst_mod( 'color_success' ) ),
		array( 'slug' => 'alert', 'name' => $t( 'Alert', 'bigspy-ai-theme-wp' ), 'color' => bst_mod( 'color_alert' ) ),
		array( 'slug' => 'heading', 'name' => $t( 'Heading', 'bigspy-ai-theme-wp' ), 'color' => bst_mod( 'color_heading' ) ),
		array( 'slug' => 'text', 'name' => $t( 'Text', 'bigspy-ai-theme-wp' ), 'color' => bst_mod( 'color_text' ) ),
		array( 'slug' => 'light', 'name' => $t( 'Light background', 'bigspy-ai-theme-wp' ), 'color' => bst_mod( 'color_light' ) ),
		array( 'slug' => 'border', 'name' => $t( 'Border', 'bigspy-ai-theme-wp' ), 'color' => bst_mod( 'color_border' ) ),
		array( 'slug' => 'white', 'name' => $t( 'White', 'bigspy-ai-theme-wp' ), 'color' => '#ffffff' ),
		array( 'slug' => 'black', 'name' => $t( 'Black', 'bigspy-ai-theme-wp' ), 'color' => '#000000' ),
	);

	return $theme_json->update_with(
		array(
			'version'  => 3,
			'settings' => array(
				'color'  => array( 'palette' => $palette ),
				'layout' => array(
					'contentSize' => (int) bst_mod( 'container_width' ) . 'px',
					'wideSize'    => ( (int) bst_mod( 'container_width' ) + 200 ) . 'px',
				),
			),
		)
	);
}
add_filter( 'wp_theme_json_data_theme', 'bst_theme_json_data' );

/**
 * Shared helpers for block render templates.
 */
function bst_block_num( $attrs, $key, $default, $min, $max ) {
	return bst_clamp( $attrs[ $key ] ?? $default, $min, $max, $default );
}

function bst_block_color( $attrs, $key ) {
	$value = isset( $attrs[ $key ] ) ? (string) $attrs[ $key ] : '';
	if ( preg_match( '/^#[0-9a-fA-F]{3,8}$/', $value ) || preg_match( '/^var\(--[a-z0-9-]+\)$/', $value ) || preg_match( '/^rgba?\([0-9., %]+\)$/', $value ) ) {
		return $value;
	}
	return '';
}

/**
 * Build a style attribute value from custom properties, skipping empty values.
 *
 * @param array $vars name => value.
 */
function bst_style_vars( $vars ) {
	$out = array();
	foreach ( $vars as $name => $value ) {
		if ( '' !== $value && null !== $value && false !== $value ) {
			$out[] = $name . ':' . $value;
		}
	}
	return implode( ';', $out );
}

/**
 * Slider wrapper markup shared by the Slider, Posts and Products blocks.
 *
 * @param string $items  Slide markup (direct children of the track).
 * @param array  $opts   perView d/t/m, gap, autoplay (ms), arrows, dots, loop, track_tag, track_class.
 * @param string $class  Extra class on the root.
 * @return string
 */
function bst_slider_markup( $items, $opts, $class = '' ) {
	$opts = wp_parse_args(
		$opts,
		array(
			'd'           => 1,
			't'           => 1,
			'm'           => 1,
			'gap'         => 20,
			'autoplay'    => 0,
			'arrows'      => true,
			'dots'        => true,
			'loop'        => true,
			'track_tag'   => 'div',
			'track_class' => '',
		)
	);
	$style = bst_style_vars(
		array(
			'--bs-slides-d' => (int) $opts['d'],
			'--bs-slides-t' => (int) $opts['t'],
			'--bs-slides-m' => (int) $opts['m'],
			'--bs-slide-gap' => (int) $opts['gap'] . 'px',
		)
	);
	$tag  = in_array( $opts['track_tag'], array( 'div', 'ul' ), true ) ? $opts['track_tag'] : 'div';
	$out  = '<div class="bs-slider ' . esc_attr( $class ) . '" data-bs-slider data-autoplay="' . (int) $opts['autoplay'] . '" data-loop="' . ( $opts['loop'] ? '1' : '0' ) . '" style="' . esc_attr( $style ) . '">';
	$out .= '<' . $tag . ' class="bs-slider__track ' . esc_attr( $opts['track_class'] ) . '" data-bs-slider-track>' . $items . '</' . $tag . '>';
	if ( $opts['arrows'] ) {
		$out .= '<button type="button" class="bs-slider__arrow bs-slider__arrow--prev" data-bs-slider-prev aria-label="' . esc_attr__( 'Previous', 'bigspy-ai-theme-wp' ) . '">' . bst_icon( 'chevron-left', 22 ) . '</button>';
		$out .= '<button type="button" class="bs-slider__arrow bs-slider__arrow--next" data-bs-slider-next aria-label="' . esc_attr__( 'Next', 'bigspy-ai-theme-wp' ) . '">' . bst_icon( 'chevron-right', 22 ) . '</button>';
	}
	if ( $opts['dots'] ) {
		$out .= '<div class="bs-slider__dots" data-bs-slider-dots></div>';
	}
	$out .= '</div>';
	return $out;
}
