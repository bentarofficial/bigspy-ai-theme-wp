<?php
/**
 * Site-wide motion intensity and style, plus the scripts of the motion blocks
 * (Animated Text, Scroll Story, Stacking Cards). Scripts load only on pages that use them.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_defaults',
	function ( $d ) {
		return $d + array( 'motion_intensity' => 'normal', 'motion_style' => 'default', 'page_transition' => 'none', 'site_magnetic' => false, 'site_cursor' => false );
	}
);

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$fields[] = array( 'id' => 'motion_intensity', 'section' => 'bst_performance', 'type' => 'select', 'label' => __( 'Motion intensity', 'bigspy-ai-theme-wp' ), 'description' => __( 'Scales every block entrance animation at once: distance and duration. Blocks with their own distance keep it. Visitors who ask their device for less motion always get none.', 'bigspy-ai-theme-wp' ), 'choices' => array( 'subtle' => __( 'Subtle', 'bigspy-ai-theme-wp' ), 'normal' => __( 'Normal', 'bigspy-ai-theme-wp' ), 'strong' => __( 'Strong', 'bigspy-ai-theme-wp' ) ) );
		$fields[] = array( 'id' => 'motion_style', 'section' => 'bst_performance', 'type' => 'select', 'label' => __( 'Motion style', 'bigspy-ai-theme-wp' ), 'description' => __( 'One-click character for all animations. Elegant: slow and smooth. Dynamic: quick with a little bounce. Minimal: fades only, no movement.', 'bigspy-ai-theme-wp' ), 'choices' => array( 'default' => __( 'Theme default', 'bigspy-ai-theme-wp' ), 'elegant' => __( 'Elegant', 'bigspy-ai-theme-wp' ), 'dynamic' => __( 'Dynamic', 'bigspy-ai-theme-wp' ), 'minimal' => __( 'Minimal', 'bigspy-ai-theme-wp' ) ) );
		$fields[] = array( 'id' => 'page_transition', 'section' => 'bst_performance', 'type' => 'select', 'label' => __( 'Page transitions', 'bigspy-ai-theme-wp' ), 'description' => __( 'Smooth change between pages of your site, done by the browser (no script). Browsers without support simply load the next page as usual.', 'bigspy-ai-theme-wp' ), 'choices' => array( 'none' => __( 'Off', 'bigspy-ai-theme-wp' ), 'fade' => __( 'Fade', 'bigspy-ai-theme-wp' ), 'slide' => __( 'Slide up', 'bigspy-ai-theme-wp' ) ) );
		$fields[] = array( 'id' => 'site_magnetic', 'section' => 'bst_performance', 'type' => 'checkbox', 'label' => __( 'Magnetic buttons everywhere', 'bigspy-ai-theme-wp' ), 'description' => __( 'All buttons lean towards the mouse. Computers only.', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'site_cursor', 'section' => 'bst_performance', 'type' => 'checkbox', 'label' => __( 'Custom cursor', 'bigspy-ai-theme-wp' ), 'description' => __( 'A dot with a following ring that grows over links. Computers only; the normal cursor stays usable.', 'bigspy-ai-theme-wp' ) );
		return $fields;
	}
);

add_filter(
	'body_class',
	function ( $classes ) {
		$classes[] = 'bs-motion--' . bst_choice( bst_mod( 'motion_intensity' ), array( 'subtle', 'normal', 'strong' ), 'normal' );
		$classes[] = 'bs-motion-style--' . bst_choice( bst_mod( 'motion_style' ), array( 'default', 'elegant', 'dynamic', 'minimal' ), 'default' );
		if ( bst_mod( 'site_magnetic' ) ) {
			$classes[] = 'bs-site-magnetic';
		}
		if ( bst_mod( 'site_cursor' ) ) {
			$classes[] = 'bs-site-cursor';
		}
		return $classes;
	}
);

/** Registered before the blocks (init 20) so block.json viewScript handles resolve. */
add_action(
	'init',
	function () {
		wp_register_script( 'bst-motion-text', BST_URI . '/assets/js/motion-text.js', array(), bst_asset_version( 'assets/js/motion-text.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
		wp_register_script( 'bst-motion-site', BST_URI . '/assets/js/motion-site.js', array(), bst_asset_version( 'assets/js/motion-site.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
		wp_register_script( 'bst-motion-scroll', BST_URI . '/assets/js/motion-scroll.js', array(), bst_asset_version( 'assets/js/motion-scroll.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	},
	5
);

/** Site-wide options: cursor/magnetic script, and cross-page view transitions (CSS only). */
add_action(
	'wp_enqueue_scripts',
	function () {
		if ( bst_mod( 'site_magnetic' ) || bst_mod( 'site_cursor' ) ) {
			wp_enqueue_script( 'bst-motion-site' );
		}
		$fx = bst_choice( bst_mod( 'page_transition' ), array( 'none', 'fade', 'slide' ), 'none' );
		if ( 'none' !== $fx ) {
			$css = '@view-transition{navigation:auto}@media (prefers-reduced-motion:reduce){@view-transition{navigation:none}}::view-transition-old(root),::view-transition-new(root){animation-duration:.35s}';
			if ( 'slide' === $fx ) {
				$css .= '::view-transition-old(root){animation:.3s ease both bs-vt-out}::view-transition-new(root){animation:.4s cubic-bezier(.2,.7,.2,1) both bs-vt-in}@keyframes bs-vt-out{to{opacity:0;transform:translateY(-24px)}}@keyframes bs-vt-in{from{opacity:0;transform:translateY(24px)}}';
			}
			wp_add_inline_style( 'bst-main', $css );
		}
	},
	20
);

/** Heading "Text motion" reuses the Animated Text runtime; load it only when used. */
add_filter(
	'render_block_bst/heading',
	function ( $html, $block ) {
		$fx = bst_choice( (string) ( $block['attrs']['textMotion'] ?? '' ), array( '', 'reveal', 'words', 'scramble' ), '' );
		if ( '' === $fx || '' === trim( $html ) ) {
			return $html;
		}
		wp_enqueue_script( 'bst-motion-text' );
		$tags = new WP_HTML_Tag_Processor( $html );
		if ( $tags->next_tag() ) {
			$tags->add_class( 'bs-atext bs-atext--' . $fx );
			$tags->set_attribute( 'data-bs-text', wp_json_encode( array( 'effect' => $fx, 'speed' => 1, 'loop' => false ) ) );
			$html = $tags->get_updated_html();
		}
		return $html;
	},
	10,
	2
);

/* ---------------------------------------------------------------- Motion wave 2: advanced motion on existing blocks */

/** Advanced motion attributes per block (registered server-side so previews and REST accept them). */
function bst_mx_attributes() {
	return array(
		'bst/section'  => array( 'mxSticky' => array( 'type' => 'boolean', 'default' => false ), 'mxScrollBg' => array( 'type' => 'string', 'default' => '' ), 'mxBg' => array( 'type' => 'string', 'default' => '' ), 'mxGlass' => array( 'type' => 'boolean', 'default' => false ) ),
		'bst/button'   => array( 'mxButton' => array( 'type' => 'string', 'default' => '' ) ),
		'bst/image'    => array( 'mxReveal' => array( 'type' => 'string', 'default' => '' ) ),
		'bst/icon-box' => array( 'mxDraw' => array( 'type' => 'boolean', 'default' => false ) ),
		'bst/counter'  => array( 'mxScramble' => array( 'type' => 'boolean', 'default' => false ) ),
		'bst/gallery'  => array( 'mxStagger' => array( 'type' => 'boolean', 'default' => false ) ),
	);
}

add_filter(
	'register_block_type_args',
	function ( $args, $name ) {
		$extra = bst_mx_attributes();
		if ( isset( $extra[ $name ] ) ) {
			$args['attributes'] = ( isset( $args['attributes'] ) && is_array( $args['attributes'] ) ? $args['attributes'] : array() ) + $extra[ $name ];
		}
		return $args;
	},
	10,
	2
);

add_action(
	'init',
	function () {
		wp_register_script( 'bst-motion-fx', BST_URI . '/assets/js/motion-fx.js', array(), bst_asset_version( 'assets/js/motion-fx.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	},
	5
);

/** Adds the advanced motion classes to the block's outer tag and loads the small runtime. */
add_filter(
	'render_block',
	function ( $html, $block ) {
		$a    = isset( $block['attrs'] ) && is_array( $block['attrs'] ) ? $block['attrs'] : array();
		$name = (string) ( $block['blockName'] ?? '' );
		if ( '' === trim( $html ) || ! isset( bst_mx_attributes()[ $name ] ) ) {
			return $html;
		}
		$class = array();
		$data  = array();
		switch ( $name ) {
			case 'bst/section':
				if ( ! empty( $a['mxSticky'] ) ) {
					$class[] = 'bs-mx-sticky';
				}
				$fx = bst_choice( (string) ( $a['mxBg'] ?? '' ), array( '', 'grain', 'mesh', 'aurora' ), '' );
				if ( $fx ) {
					$class[] = 'bs-mx-bg-' . $fx;
				}
				if ( ! empty( $a['mxGlass'] ) ) {
					$class[] = 'bs-mx-glass';
				}
				$bg = isset( $a['mxScrollBg'] ) ? sanitize_hex_color( (string) $a['mxScrollBg'] ) : '';
				if ( $bg ) {
					$data['data-bs-scroll-bg'] = $bg;
				}
				break;
			case 'bst/button':
				$fx = bst_choice( (string) ( $a['mxButton'] ?? '' ), array( '', 'magnetic', 'arrow', 'shine' ), '' );
				if ( $fx ) {
					$class[] = 'bs-mx-' . $fx;
				}
				break;
			case 'bst/image':
				$fx = bst_choice( (string) ( $a['mxReveal'] ?? '' ), array( '', 'left', 'right', 'up', 'circle', 'kenburns' ), '' );
				if ( $fx ) {
					$class[] = 'bs-mx-reveal-' . $fx;
				}
				break;
			case 'bst/icon-box':
				if ( ! empty( $a['mxDraw'] ) ) {
					$class[] = 'bs-mx-draw';
				}
				break;
			case 'bst/counter':
				if ( ! empty( $a['mxScramble'] ) ) {
					$class[] = 'bs-mx-scramble';
				}
				break;
			case 'bst/gallery':
				if ( ! empty( $a['mxStagger'] ) ) {
					$class[] = 'bs-mx-stagger';
				}
				break;
		}
		if ( ! $class && ! $data ) {
			return $html;
		}
		$tags = new WP_HTML_Tag_Processor( $html );
		if ( ! $tags->next_tag() ) {
			return $html;
		}
		foreach ( $class as $c ) {
			$tags->add_class( $c );
		}
		foreach ( $data as $k => $v ) {
			$tags->set_attribute( $k, $v );
		}
		// Arrow and sticky are CSS only; everything else needs the runtime (counter scramble runs in blocks.js).
		if ( array_diff( $class, array( 'bs-mx-arrow', 'bs-mx-sticky', 'bs-mx-scramble', 'bs-mx-shine', 'bs-mx-bg-grain', 'bs-mx-bg-mesh', 'bs-mx-bg-aurora', 'bs-mx-glass' ) ) || $data ) {
			wp_enqueue_script( 'bst-motion-fx' );
		}
		return $tags->get_updated_html();
	},
	10,
	2
);
