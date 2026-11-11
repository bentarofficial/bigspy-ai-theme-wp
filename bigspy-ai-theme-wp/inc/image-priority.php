<?php
/**
 * Automatic image load priority.
 *
 * The first large image a page renders is almost always the hero, which is the LCP element, so it loads
 * at once with fetchpriority="high". Every other image is lazy-loaded. A block's "Load priority" option
 * (Auto / Top of page / Load later) still overrides this for one image.
 *
 * The HTML is built before the page is shown, so the order of images is used instead of their pixel
 * position, the same way WordPress core handles images in post content.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether automatic priority runs on this request: front-end pages only, never the editor, REST
 * previews, feeds or admin screens.
 *
 * @return bool
 */
function bst_image_priority_active() {
	return ! is_admin() && ! wp_is_json_request() && ! is_feed() && ! ( defined( 'REST_REQUEST' ) && REST_REQUEST );
}

/**
 * Marks the page's single high-priority image as taken (also for WordPress core, so it does not add a
 * second one to the featured image or a content image).
 *
 * @param bool|null $set True to take it. Null to read.
 * @return bool Whether it is already taken.
 */
function bst_image_hero_taken( $set = null ) {
	static $taken = false;
	if ( true === $set ) {
		$taken = true;
		if ( function_exists( 'wp_high_priority_element_flag' ) ) {
			wp_high_priority_element_flag( false );
		}
	}
	if ( ! $taken && function_exists( 'wp_high_priority_element_flag' ) && ! wp_high_priority_element_flag() ) {
		return true; // Core already gave fetchpriority="high" to an earlier image.
	}
	return $taken;
}

/**
 * Decides how one image loads.
 *
 * @param string $choice 'auto', 'high' or 'lazy' (the block's Load priority option).
 * @param bool   $large  False for small images (icons, logos, avatars, covers): they never become the hero.
 * @return string 'high' or 'lazy'.
 */
function bst_image_priority( $choice = 'auto', $large = true ) {
	$choice = in_array( $choice, array( 'auto', 'high', 'lazy' ), true ) ? $choice : 'auto';
	if ( 'lazy' === $choice ) {
		return 'lazy';
	}
	if ( 'high' === $choice ) {
		bst_image_hero_taken( true );
		return 'high';
	}
	if ( ! $large || ! bst_image_priority_active() || bst_image_hero_taken() ) {
		return 'lazy';
	}
	bst_image_hero_taken( true );
	return 'high';
}

/**
 * Attributes for wp_get_attachment_image().
 *
 * @param string $prio 'high' or 'lazy'.
 * @return array
 */
function bst_image_load_attrs( $prio ) {
	return 'high' === $prio
		? array( 'loading' => false, 'fetchpriority' => 'high' )
		: array( 'loading' => 'lazy', 'fetchpriority' => false );
}

/**
 * Attribute string for a hand-written <img> tag.
 *
 * @param string $prio 'high' or 'lazy'.
 * @return string
 */
function bst_image_load_html( $prio ) {
	return 'high' === $prio ? 'fetchpriority="high"' : 'loading="lazy"';
}

/**
 * Finds the image of the first block on a page when that block is a hero-style block, so it can be
 * preloaded. Looks through wrapper blocks (section, row, column, slider) at the top of the page.
 *
 * @param array $blocks Parsed blocks.
 * @param int   $depth  Nesting depth.
 * @return array|null { id, url, size } or null.
 */
function bst_image_find_hero( $blocks, $depth = 0 ) {
	foreach ( $blocks as $block ) {
		$name = (string) ( $block['blockName'] ?? '' );
		if ( '' === $name ) {
			if ( '' === trim( wp_strip_all_tags( (string) ( $block['innerHTML'] ?? '' ) ) ) ) {
				continue; // Whitespace between blocks.
			}
			return null;
		}
		$a = (array) ( $block['attrs'] ?? array() );
		if ( 'lazy' === ( $a['loadPriority'] ?? '' ) ) {
			return null;
		}
		if ( 'bst/banner' === $name ) {
			if ( ! empty( $a['imageMobileId'] ) || ! empty( $a['imageMobileUrl'] ) ) {
				return null; // A separate phone image: preloading one of the two would waste a download.
			}
			return array( 'id' => (int) ( $a['imageId'] ?? 0 ), 'url' => (string) ( $a['imageUrl'] ?? '' ), 'size' => 'full', 'sizes' => '100vw' );
		}
		if ( 'bst/zoom-hero' === $name ) {
			// Zoom hero prints a plain full-size <img>, so preload exactly that file (no srcset).
			$url = ! empty( $a['imageId'] ) ? (string) wp_get_attachment_image_url( (int) $a['imageId'], 'full' ) : '';
			return array( 'id' => 0, 'url' => '' !== $url ? $url : (string) ( $a['imageUrl'] ?? '' ), 'size' => 'full', 'sizes' => '' );
		}
		if ( in_array( $name, array( 'bst/section', 'bst/row', 'bst/column', 'bst/slider', 'core/group', 'core/columns', 'core/column' ), true ) && $depth < 4 ) {
			return bst_image_find_hero( (array) ( $block['innerBlocks'] ?? array() ), $depth + 1 );
		}
		return null; // The page starts with something else; the browser finds that image itself.
	}
	return null;
}

/**
 * Preloads the hero image in <head> so the browser starts it before CSS and scripts.
 */
add_action(
	'wp_head',
	function () {
		if ( ! bst_image_priority_active() || ! is_singular() ) {
			return;
		}
		$post = get_queried_object();
		if ( ! $post instanceof WP_Post || post_password_required( $post ) ) {
			return;
		}
		// A single post that shows its featured image above the content: that image is the hero.
		if ( is_single() && has_post_thumbnail( $post ) && bst_mod( 'single_featured_image' ) ) {
			$hero = array( 'id' => (int) get_post_thumbnail_id( $post ), 'url' => '', 'size' => 'large', 'sizes' => '' );
		} else {
			$hero = has_blocks( $post ) ? bst_image_find_hero( parse_blocks( $post->post_content ) ) : null;
		}
		if ( ! $hero ) {
			return;
		}
		$href   = '';
		$srcset = '';
		if ( $hero['id'] > 0 ) {
			$src    = wp_get_attachment_image_src( $hero['id'], $hero['size'] );
			$href   = $src ? $src[0] : '';
			$srcset = (string) wp_get_attachment_image_srcset( $hero['id'], $hero['size'] );
		}
		if ( '' === $href ) {
			$href = $hero['url'];
		}
		if ( '' === $href ) {
			return;
		}
		printf(
			'<link rel="preload" as="image" href="%1$s"%2$s fetchpriority="high">' . "\n",
			esc_url( $href ),
			$srcset ? ' imagesrcset="' . esc_attr( $srcset ) . '" imagesizes="' . esc_attr( $hero['sizes'] ? $hero['sizes'] : (string) wp_get_attachment_image_sizes( $hero['id'], $hero['size'] ) ) . '"' : ''
		);
	},
	2
);
