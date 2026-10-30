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
	return array( 'section', 'row', 'column', 'banner', 'slider', 'icon-box', 'title', 'tabs', 'tab', 'accordion', 'accordion-item', 'posts', 'testimonial', 'counter', 'map', 'heading', 'text', 'image', 'gallery', 'video', 'social', 'form', 'team-member', 'price-table', 'video-button', 'share', 'icon-list', 'image-box', 'alert', 'progress', 'star-rating', 'buttons', 'button', 'divider', 'spacer', 'code', 'products', 'products-list', 'product-categories', 'hotspot', 'popup', 'logos', 'widget-area', 'shortcode', 'payment-icons', 'marquee', 'before-after', 'flip-box', 'timeline', 'table', 'pricing-toggle', 'lottie', 'audio', 'nav-menu', 'banner-grid', 'ticker', 'ticker-slide', 'expand-gallery', 'expand-panel', 'animated-text', 'scroll-story', 'story-step', 'stack-cards', 'stack-card' );
}

function bst_register_blocks() {
	wp_register_script(
		'bst-block-design',
		BST_URI . '/assets/js/block-design.js',
		array( 'bst-editor-helpers', 'wp-hooks', 'wp-compose', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-data', 'wp-i18n' ),
		bst_asset_version( 'assets/js/block-design.js' ),
		true
	);
	wp_register_script(
		'bst-blocks-editor',
		BST_URI . '/assets/js/blocks-editor.js',
		array( 'bst-editor-helpers', 'bst-block-design', 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-server-side-render', 'wp-data', 'wp-api-fetch' ),
		bst_asset_version( 'assets/js/blocks-editor.js' ),
		true
	);

	wp_register_script(
		'bst-blocks-extra',
		BST_URI . '/assets/js/blocks-extra.js',
		array( 'bst-editor-helpers', 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-server-side-render', 'wp-data' ),
		bst_asset_version( 'assets/js/blocks-extra.js' ),
		true
	);
	wp_register_script(
		'bst-blocks-more',
		BST_URI . '/assets/js/blocks-more.js',
		array( 'bst-editor-helpers', 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-server-side-render', 'wp-data', 'wp-core-data' ),
		bst_asset_version( 'assets/js/blocks-more.js' ),
		true
	);
	wp_register_style( 'bst-motion-editor', BST_URI . '/assets/css/blocks-motion-editor.css', array(), bst_asset_version( 'assets/css/blocks-motion-editor.css' ) );
	wp_register_script( 'bst-motion-editor', BST_URI . '/assets/js/blocks-motion.js', array( 'bst-block-previews', 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data', 'wp-api-fetch' ), bst_asset_version( 'assets/js/blocks-motion.js' ), true );
	$areas = array();
	foreach ( (array) ( $GLOBALS['wp_registered_sidebars'] ?? array() ) as $sidebar ) {
		$areas[] = array( 'id' => $sidebar['id'], 'name' => $sidebar['name'] );
	}
	wp_add_inline_script( 'bst-blocks-extra', 'window.bstWidgetAreas=' . wp_json_encode( $areas ) . ';', 'before' );

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
		'bsPresets'        => array( 'type' => 'string', 'default' => '' ),
		'bsEase'           => array( 'type' => 'string', 'default' => '' ),
		'bsDistance'       => array( 'type' => 'number', 'default' => 0 ),
		'bsRepeat'         => array( 'type' => 'boolean', 'default' => false ),
		'bsStagger'        => array( 'type' => 'number', 'default' => 0 ),
		'bsLoop'           => array( 'type' => 'string', 'default' => '' ),
		'bsScroll'         => array( 'type' => 'string', 'default' => '' ),
		'bsScrollStrength' => array( 'type' => 'number', 'default' => 5 ),
	);
	return $args;
}
add_filter( 'register_block_type_args', 'bst_global_block_attributes' );

/** Inserter-only flag (see block-previews.js), declared so server-side renders accept it. */
add_filter(
	'register_block_type_args',
	function ( $args, $name ) {
		if ( preg_match( '#^(bst|bigspy)/#', (string) $name ) ) {
			$args['attributes'] = isset( $args['attributes'] ) && is_array( $args['attributes'] ) ? $args['attributes'] : array();
			$args['attributes'] += array( 'bstPreview' => array( 'type' => 'boolean', 'default' => false ) );
		}
		return $args;
	},
	10,
	2
);

function bst_block_animations() {
	return array( 'fade-in', 'fade-up', 'fade-down', 'fade-left', 'fade-right', 'slide-up', 'slide-down', 'slide-left', 'slide-right', 'zoom-in', 'zoom-out', 'zoom-in-up', 'zoom-in-down', 'bounce-in', 'bounce-up', 'bounce-down', 'bounce-left', 'bounce-right', 'flip-up', 'flip-x', 'flip-y', 'rotate-in', 'roll-in', 'swing-in', 'reveal-up', 'reveal-left', 'reveal-right', 'reveal-center', 'blur-in', 'skew-in', 'light-speed', 'pop' );
}

function bst_block_hover_effects() {
	return array( 'lift', 'zoom', 'glow', 'grayscale', 'tilt', 'shine', 'border-glow', 'bounce', 'rotate', 'shrink' );
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
	$motion = array();
	$loop   = isset( $attrs['bsLoop'] ) ? (string) $attrs['bsLoop'] : '';
	if ( in_array( $loop, array( 'float', 'pulse', 'heartbeat', 'swing', 'wobble', 'spin', 'glow', 'shake' ), true ) ) {
		$classes[] = 'bs-loop';
		$classes[] = 'bs-loop--' . $loop;
	}
	$scroll = isset( $attrs['bsScroll'] ) ? (string) $attrs['bsScroll'] : '';
	if ( in_array( $scroll, array( 'up', 'down', 'left', 'right', 'zoom', 'rotate', 'fade' ), true ) ) {
		$motion['data-bs-scroll'] = $scroll;
		$motion['data-bs-scroll-strength'] = (string) max( 1, min( 10, (int) ( $attrs['bsScrollStrength'] ?? 5 ) ) );
	}
	if ( $animation ) {
		$ease = isset( $attrs['bsEase'] ) ? (string) $attrs['bsEase'] : '';
		if ( in_array( $ease, array( 'spring', 'soft', 'snappy', 'linear' ), true ) ) {
			$classes[] = 'bs-ease--' . $ease;
		}
		if ( ! empty( $attrs['bsRepeat'] ) ) {
			$motion['data-bs-repeat'] = '1';
		}
		if ( ! empty( $attrs['bsStagger'] ) ) {
			$motion['data-bs-stagger'] = (string) max( 40, min( 600, (int) $attrs['bsStagger'] ) );
		}
	}
	if ( ! $classes && ! $motion ) {
		return $content;
	}
	if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return $content;
	}

	$tags = new WP_HTML_Tag_Processor( $content );
	if ( ! $tags->next_tag() ) {
		return $content;
	}
	foreach ( $classes as $class ) {
		$tags->add_class( $class );
	}
	foreach ( $motion as $name => $value ) {
		$tags->set_attribute( $name, $value );
	}
	if ( $animation && ! empty( $attrs['bsDistance'] ) ) {
		$style = (string) $tags->get_attribute( 'style' );
		$tags->set_attribute( 'style', rtrim( $style, '; ' ) . ( $style ? ';' : '' ) . '--bs-dist:' . max( 5, min( 300, (int) $attrs['bsDistance'] ) ) . 'px' );
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
 * HTML anchor (Advanced → HTML anchor) on dynamic BIGSPY blocks: core only saves it
 * into static markup, so print it as the id of the block's first tag.
 */
function bst_apply_block_anchor( $content, $block ) {
	$anchor = isset( $block['attrs']['anchor'] ) ? sanitize_title( (string) $block['attrs']['anchor'] ) : '';
	if ( '' === $anchor || 0 !== strpos( (string) ( $block['blockName'] ?? '' ), 'bst/' ) || ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return $content;
	}
	$tags = new WP_HTML_Tag_Processor( $content );
	if ( ! $tags->next_tag() || $tags->get_attribute( 'id' ) ) {
		return $content;
	}
	$tags->set_attribute( 'id', $anchor );
	return $tags->get_updated_html();
}
add_filter( 'render_block', 'bst_apply_block_anchor', 10, 2 );

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
			'fade'        => false,
			'peek'        => 0,
			'progress'    => false,
			'pause'       => true,
		)
	);
	$style = bst_style_vars(
		array(
			'--bs-slides-d' => (int) $opts['d'],
			'--bs-slides-t' => (int) $opts['t'],
			'--bs-slides-m' => (int) $opts['m'],
			'--bs-slide-gap' => (int) $opts['gap'] . 'px',
			'--bs-peek'      => $opts['peek'] ? round( (int) $opts['peek'] / 100, 2 ) : '',
			'--bs-peek-on'   => $opts['peek'] ? 1 : '',
		)
	);
	$tag  = in_array( $opts['track_tag'], array( 'div', 'ul' ), true ) ? $opts['track_tag'] : 'div';
	$class .= $opts['fade'] ? ' bs-slider--fade' : '';
	$out    = '<div class="bs-slider ' . esc_attr( trim( $class ) ) . '" data-bs-slider data-autoplay="' . (int) $opts['autoplay'] . '" data-loop="' . ( $opts['loop'] || $opts['fade'] ? '1' : '0' ) . '"' . ( $opts['fade'] ? ' data-fade="1"' : '' ) . ( $opts['pause'] ? '' : ' data-pause="0"' ) . ' style="' . esc_attr( $style ) . '">';
	$out .= '<' . $tag . ' class="bs-slider__track ' . esc_attr( $opts['track_class'] ) . '" data-bs-slider-track>' . $items . '</' . $tag . '>';
	if ( $opts['arrows'] ) {
		$out .= '<button type="button" class="bs-slider__arrow bs-slider__arrow--prev" data-bs-slider-prev aria-label="' . esc_attr__( 'Previous', 'bigspy-ai-theme-wp' ) . '">' . bst_icon( 'chevron-left', 22 ) . '</button>';
		$out .= '<button type="button" class="bs-slider__arrow bs-slider__arrow--next" data-bs-slider-next aria-label="' . esc_attr__( 'Next', 'bigspy-ai-theme-wp' ) . '">' . bst_icon( 'chevron-right', 22 ) . '</button>';
	}
	if ( $opts['dots'] ) {
		$out .= '<div class="bs-slider__dots" data-bs-slider-dots></div>';
	}
	// Timer shown while autoplay runs. true = the classic bottom bar.
	$timer = true === $opts['progress'] ? 'bar' : bst_choice( (string) $opts['progress'], array( 'off', 'bar', 'bar-top', 'bar-thick', 'line-left', 'circle', 'dot', 'number' ), 'off' );
	if ( $opts['autoplay'] && 'off' !== $timer ) {
		if ( 'circle' === $timer ) {
			$out .= '<div class="bs-slider__timer bs-slider__timer--circle" aria-hidden="true"><svg viewBox="0 0 36 36"><circle class="bs-slider__ring-bg" cx="18" cy="18" r="15.9"/><circle class="bs-slider__ring" cx="18" cy="18" r="15.9" pathLength="100" data-bs-slider-progress/></svg></div>';
		} elseif ( 'number' === $timer ) {
			$out .= '<div class="bs-slider__timer bs-slider__timer--number" aria-hidden="true"><b data-bs-slider-count>01</b><span class="bs-slider__timer-line"><span data-bs-slider-progress></span></span><i data-bs-slider-total>01</i></div>';
		} elseif ( 'dot' === $timer ) {
			$out .= '<div class="bs-slider__timer bs-slider__timer--dot" hidden data-bs-slider-dot-timer></div>';
		} else {
			$out .= '<div class="bs-slider__progress bs-slider__progress--' . esc_attr( $timer ) . '" aria-hidden="true"><span data-bs-slider-progress></span></div>';
		}
	}
	$out .= '</div>';
	return $out;
}

/*
 * Text colors in BIGSPY rich text are often rgb() / hsl() (pasted codes, the Text block's
 * color picker). WordPress kses drops any CSS value with parentheses, so the color showed in
 * the editor but not on the site. Allow numeric rgb(a) / hsl(a) for color and background-color.
 */
add_filter(
	'safecss_filter_attr_allow_css',
	function ( $allow, $css ) {
		if ( ! $allow && preg_match( '/^\s*(background-color|color)\s*:\s*(rgba?|hsla?)\(\s*[0-9.,%\s\/]+(deg)?[0-9.,%\s\/]*\)\s*$/i', (string) $css ) ) {
			return true;
		}
		return $allow;
	},
	10,
	2
);
