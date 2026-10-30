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
		return $d + array( 'motion_intensity' => 'normal', 'motion_style' => 'default' );
	}
);

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$fields[] = array( 'id' => 'motion_intensity', 'section' => 'bst_performance', 'type' => 'select', 'label' => __( 'Motion intensity', 'bigspy-ai-theme-wp' ), 'description' => __( 'Scales every block entrance animation at once: distance and duration. Blocks with their own distance keep it. Visitors who ask their device for less motion always get none.', 'bigspy-ai-theme-wp' ), 'choices' => array( 'subtle' => __( 'Subtle', 'bigspy-ai-theme-wp' ), 'normal' => __( 'Normal', 'bigspy-ai-theme-wp' ), 'strong' => __( 'Strong', 'bigspy-ai-theme-wp' ) ) );
		$fields[] = array( 'id' => 'motion_style', 'section' => 'bst_performance', 'type' => 'select', 'label' => __( 'Motion style', 'bigspy-ai-theme-wp' ), 'description' => __( 'One-click character for all animations. Elegant: slow and smooth. Dynamic: quick with a little bounce. Minimal: fades only, no movement.', 'bigspy-ai-theme-wp' ), 'choices' => array( 'default' => __( 'Theme default', 'bigspy-ai-theme-wp' ), 'elegant' => __( 'Elegant', 'bigspy-ai-theme-wp' ), 'dynamic' => __( 'Dynamic', 'bigspy-ai-theme-wp' ), 'minimal' => __( 'Minimal', 'bigspy-ai-theme-wp' ) ) );
		return $fields;
	}
);

add_filter(
	'body_class',
	function ( $classes ) {
		$classes[] = 'bs-motion--' . bst_choice( bst_mod( 'motion_intensity' ), array( 'subtle', 'normal', 'strong' ), 'normal' );
		$classes[] = 'bs-motion-style--' . bst_choice( bst_mod( 'motion_style' ), array( 'default', 'elegant', 'dynamic', 'minimal' ), 'default' );
		return $classes;
	}
);

/** Registered before the blocks (init 20) so block.json viewScript handles resolve. */
add_action(
	'init',
	function () {
		wp_register_script( 'bst-motion-text', BST_URI . '/assets/js/motion-text.js', array(), bst_asset_version( 'assets/js/motion-text.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
		wp_register_script( 'bst-motion-scroll', BST_URI . '/assets/js/motion-scroll.js', array(), bst_asset_version( 'assets/js/motion-scroll.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	},
	5
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
