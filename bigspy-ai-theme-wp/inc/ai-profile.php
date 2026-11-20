<?php
/**
 * Public AI profile: tells any AI tool or coding agent that visits the site how to build pages
 * with this theme. Served as /llms.txt, as JSON at /wp-json/bigspy/v1/ai-profile, and linked
 * from <head>. Same source as AI Designer (ai-designer/knowledge), so both stay in step.
 * Turn off with add_filter( 'bst_ai_profile_enabled', '__return_false' ).
 */
defined( 'ABSPATH' ) || exit;

function bst_ai_profile_enabled() {
	return (bool) apply_filters( 'bst_ai_profile_enabled', true );
}

/** Block profiles (name => row) from the AI Designer knowledge, limited to registered blocks. */
function bst_ai_profile_blocks() {
	$file = BST_DIR . '/ai-designer/knowledge/blocks.php';
	$all  = file_exists( $file ) ? (array) include $file : array();
	$reg  = WP_Block_Type_Registry::get_instance();
	$out  = array();
	foreach ( $all as $name => $p ) {
		if ( ! $reg->is_registered( $name ) || ! is_array( $p ) || count( $p ) < 9 ) {
			continue;
		}
		$row = array( 'name' => $name, 'needs' => $p[0], 'use' => $p[1], 'weight' => $p[4], 'attributes' => $p[8] );
		foreach ( array( 'avoid' => 2, 'data' => 3, 'mobile' => 6, 'pairs' => 7 ) as $key => $i ) {
			if ( '' !== $p[ $i ] ) {
				$row[ $key ] = $p[ $i ];
			}
		}
		if ( $p[5] ) {
			$row['pin'] = true;
		}
		$out[] = $row;
	}
	return $out;
}

/** The rules every agent must follow, in priority order. */
function bst_ai_profile_rules() {
	return array(
		'Build every page from this theme\'s blocks (bst/* and bigspy/*) so the owner can edit it visually. Never use core/html (Custom HTML) or core/freeform for layout, sections or styling.',
		'Structure: bst/section > bst/row > bst/column, with bst/heading, bst/text, bst/buttons and bst/image inside. Spacing and backgrounds come from section options, not CSS.',
		'Hero: the first block is bst/banner or bst/slider with "align":"full". The theme then puts it flush under the header on desktop and phones. Exactly one H1 per page.',
		'Prefer dynamic blocks and turn motion on: bst/slider, bst/marquee, bigspy/ticker, bigspy/animated-text, bigspy/stack-cards, bigspy/bento, bigspy/expand-gallery, bigspy/hover-reveal, bigspy/scroll-story, bigspy/horizontal-scroll, bigspy/zoom-hero, bst/counter, bst/before-after.',
		'Any block accepts entrance motion: bsAnimation (see animations), bsAnimationDelay (ms), bsAnimationDuration (ms), bsStagger (ms between children), bsHoverEffect (see hover_effects). Give each section an entrance animation and stagger its cards.',
		'At most 2 blocks with "pin": true per page, never in adjacent sections; one or two "heavy" blocks per page.',
		'Styling order: block options, then Style presets (bsPresets), then Customizer > Additional CSS. bst/code only for small illustrations.',
		'Forms: bst/form, never a shortcode or HTML form.',
		'Use only real facts, prices, reviews and figures from the owner. Never invent them.',
		'Before writing attributes, read the block schema: /wp-json/wp/v2/block-types/<namespace>/<name> (needs an editor login).',
	);
}

function bst_ai_profile_data() {
	$guide = BST_DIR . '/ai-designer/knowledge/theme.md';
	return array(
		'theme'         => 'BIGSPY AI THEME WP',
		'version'       => BST_VERSION,
		'site'          => home_url( '/' ),
		'rules'         => bst_ai_profile_rules(),
		'animations'    => function_exists( 'bst_block_animations' ) ? bst_block_animations() : array(),
		'hover_effects' => function_exists( 'bst_block_hover_effects' ) ? bst_block_hover_effects() : array(),
		'blocks'        => bst_ai_profile_blocks(),
		'guide'         => file_exists( $guide ) ? (string) file_get_contents( $guide ) : '', // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	);
}

function bst_ai_profile_url() {
	return rest_url( 'bigspy/v1/ai-profile' );
}

function bst_ai_profile_text() {
	$d = bst_ai_profile_data();
	$t = '# ' . get_bloginfo( 'name' ) . "\n\n";
	$t .= '> WordPress site built with ' . $d['theme'] . ' ' . $d['version'] . ". AI tools and coding agents: read this before creating or editing pages.\n\n";
	$t .= "## Rules\n\n";
	foreach ( $d['rules'] as $r ) {
		$t .= '- ' . $r . "\n";
	}
	$t .= "\n## Animations\n\nbsAnimation: " . implode( ', ', $d['animations'] ) . "\nbsHoverEffect: " . implode( ', ', $d['hover_effects'] ) . "\n\n";
	$t .= "## Blocks\n\n";
	foreach ( $d['blocks'] as $b ) {
		$t .= '- ' . $b['name'] . ' (' . $b['weight'] . ( ! empty( $b['pin'] ) ? ', pin' : '' ) . '): ' . $b['use'];
		if ( ! empty( $b['avoid'] ) ) {
			$t .= ' Avoid: ' . $b['avoid'];
		}
		$t .= ' Attributes: ' . $b['attributes'] . "\n";
	}
	$t .= "\n## More\n\n- Full profile as JSON (rules, blocks, theme guide): " . bst_ai_profile_url() . "\n";
	return $t;
}

add_action(
	'rest_api_init',
	function () {
		if ( ! bst_ai_profile_enabled() ) {
			return;
		}
		register_rest_route(
			'bigspy/v1',
			'/ai-profile',
			array(
				'methods'             => 'GET',
				'permission_callback' => '__return_true',
				'callback'            => function () {
					return rest_ensure_response( bst_ai_profile_data() );
				},
			)
		);
	}
);

/** /llms.txt without rewrite rules; a real llms.txt file on the server still wins. */
add_action(
	'parse_request',
	function () {
		if ( ! bst_ai_profile_enabled() || empty( $_SERVER['REQUEST_URI'] ) ) {
			return;
		}
		$path = (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$base = rtrim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
		if ( $base . '/llms.txt' !== $path ) {
			return;
		}
		status_header( 200 );
		header( 'Content-Type: text/plain; charset=utf-8' );
		header( 'X-Robots-Tag: noindex' );
		echo bst_ai_profile_text(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		exit;
	},
	0
);

add_action(
	'wp_head',
	function () {
		if ( bst_ai_profile_enabled() ) {
			echo '<link rel="help" type="text/plain" title="AI profile" href="' . esc_url( home_url( '/llms.txt' ) ) . "\" />\n";
		}
	},
	2
);
