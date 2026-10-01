<?php
/**
 * Block pattern categories and the shop patterns.
 *
 * Pattern files in /patterns (service / business sections) are registered by
 * WordPress automatically. Shop patterns are registered here, only while
 * WooCommerce is active, so service websites never see them.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

function bst_register_pattern_categories() {
	register_block_pattern_category( 'bigspy-sections', array( 'label' => __( 'BIGSPY – Sections', 'bigspy-theme' ) ) );
	register_block_pattern_category( 'bigspy-pages', array( 'label' => __( 'BIGSPY – Full pages', 'bigspy-theme' ) ) );

	if ( class_exists( 'WooCommerce' ) ) {
		register_block_pattern_category( 'bigspy-shop', array( 'label' => __( 'BIGSPY – Shop', 'bigspy-theme' ) ) );
		bst_register_shop_patterns();
	}
}
add_action( 'init', 'bst_register_pattern_categories', 9 );

/**
 * URL of a bundled placeholder image for patterns.
 */
function bst_pattern_image( $name ) {
	return esc_url( BST_URI . '/assets/images/' . $name );
}

function bst_register_shop_patterns() {
	$shop = esc_url( wc_get_page_permalink( 'shop' ) );
	$hero = bst_pattern_image( 'hero.svg' );

	$sections = array(
		'shop-hero'          => array(
			__( 'Shop – hero slider', 'bigspy-theme' ),
			'<!-- wp:bst/slider {"align":"full","autoplay":5} -->'
			. '<!-- wp:bst/banner {"imageUrl":"' . $hero . '","heightDesktop":520,"posX":"left","textAlign":"left","contentWidth":55} --><!-- wp:heading {"level":1} --><h1 class="wp-block-heading">New collection 2026</h1><!-- /wp:heading --><!-- wp:paragraph --><p>Up to 50% off this week only. Free shipping on orders over $50.</p><!-- /wp:paragraph --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button {"className":"is-style-secondary"} --><div class="wp-block-button is-style-secondary"><a class="wp-block-button__link wp-element-button" href="' . $shop . '">Shop now</a></div><!-- /wp:button --></div><!-- /wp:buttons --><!-- /wp:bst/banner -->'
			. '<!-- wp:bst/banner {"imageUrl":"' . $hero . '","heightDesktop":520} --><!-- wp:heading {"level":2} --><h2 class="wp-block-heading">Best sellers</h2><!-- /wp:heading --><!-- wp:paragraph --><p>The products our customers love the most.</p><!-- /wp:paragraph --><!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} --><div class="wp-block-buttons"><!-- wp:button {"className":"is-style-white"} --><div class="wp-block-button is-style-white"><a class="wp-block-button__link wp-element-button" href="' . $shop . '">View products</a></div><!-- /wp:button --></div><!-- /wp:buttons --><!-- /wp:bst/banner -->'
			. '<!-- /wp:bst/slider -->',
		),
		'shop-benefits'      => array(
			__( 'Shop – benefits bar', 'bigspy-theme' ),
			'<!-- wp:bst/section {"paddingDesktop":36,"paddingMobile":24,"backgroundColor":"light"} --><!-- wp:bst/row {"gap":"small"} -->'
			. bst_shop_benefit( 'truck', 'Free shipping', 'On orders over $50' )
			. bst_shop_benefit( 'shield', 'Genuine products', '100% authentic guarantee' )
			. bst_shop_benefit( 'gift', 'Easy returns', 'Within 7 days' )
			. bst_shop_benefit( 'support', 'Support 24/7', 'Phone, chat & email' )
			. '<!-- /wp:bst/row --><!-- /wp:bst/section -->',
		),
		'shop-categories'    => array(
			__( 'Shop – product categories', 'bigspy-theme' ),
			'<!-- wp:bst/section {"paddingDesktop":70} --><!-- wp:bst/title {"text":"Shop by category","subtitle":"Categories"} /--><!-- wp:bst/product-categories {"count":6,"columns":6,"columnsTablet":3,"columnsMobile":2,"style":"circle"} /--><!-- /wp:bst/section -->',
		),
		'products-section'   => array(
			__( 'Shop – featured products', 'bigspy-theme' ),
			'<!-- wp:bst/section {"paddingDesktop":70} --><!-- wp:bst/title {"text":"Featured products","subtitle":"Shop","style":"underline-full","align":"left","linkText":"View all","linkUrl":"' . $shop . '"} /--><!-- wp:bst/products {"type":"featured","count":8} /--><!-- /wp:bst/section -->',
		),
		'shop-sale'          => array(
			__( 'Shop – on sale slider', 'bigspy-theme' ),
			'<!-- wp:bst/section {"paddingDesktop":70,"backgroundColor":"light"} --><!-- wp:bst/title {"text":"Hot deals","subtitle":"Sale","style":"underline-full","align":"left","linkText":"View all","linkUrl":"' . $shop . '"} /--><!-- wp:bst/products {"type":"sale","count":10,"layout":"slider","columns":5,"autoplay":5} /--><!-- /wp:bst/section -->',
		),
		'shop-tabs'          => array(
			__( 'Shop – product tabs (new / best selling / sale)', 'bigspy-theme' ),
			'<!-- wp:bst/section {"paddingDesktop":70} --><!-- wp:bst/title {"text":"Our products"} /--><!-- wp:bst/tabs {"style":"pills","align":"center"} -->'
			. '<!-- wp:bst/tab {"title":"New arrivals"} --><!-- wp:bst/products {"type":"latest","count":8} /--><!-- /wp:bst/tab -->'
			. '<!-- wp:bst/tab {"title":"Best sellers"} --><!-- wp:bst/products {"type":"best_selling","count":8} /--><!-- /wp:bst/tab -->'
			. '<!-- wp:bst/tab {"title":"On sale"} --><!-- wp:bst/products {"type":"sale","count":8} /--><!-- /wp:bst/tab -->'
			. '<!-- /wp:bst/tabs --><!-- /wp:bst/section -->',
		),
	);

	foreach ( $sections as $slug => $pattern ) {
		register_block_pattern(
			'bigspy-theme/' . $slug,
			array(
				'title'      => $pattern[0],
				'categories' => array( 'bigspy-shop' ),
				'content'    => $pattern[1],
			)
		);
	}

	register_block_pattern(
		'bigspy-theme/page-shop-home',
		array(
			'title'      => __( 'Homepage – online shop', 'bigspy-theme' ),
			'categories' => array( 'bigspy-pages', 'bigspy-shop' ),
			'blockTypes' => array( 'core/post-content' ),
			'postTypes'  => array( 'page' ),
			'content'    => implode(
				'',
				array(
					$sections['shop-hero'][1],
					$sections['shop-benefits'][1],
					$sections['shop-categories'][1],
					$sections['shop-tabs'][1],
					$sections['shop-sale'][1],
					'<!-- wp:pattern {"slug":"bigspy-theme/testimonials"} /-->',
					'<!-- wp:pattern {"slug":"bigspy-theme/blog"} /-->',
				)
			),
		)
	);
}

/**
 * One column of the benefits bar.
 */
function bst_shop_benefit( $icon, $title, $text ) {
	$attrs = wp_json_encode(
		array(
			'icon'      => $icon,
			'iconStyle' => 'plain',
			'iconSize'  => 30,
			'position'  => 'left',
			'align'     => 'left',
			'title'     => $title,
			'text'      => $text,
			'titleTag'  => 'h4',
		),
		JSON_UNESCAPED_UNICODE
	);
	return '<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} --><!-- wp:bst/icon-box ' . $attrs . ' /--><!-- /wp:bst/column -->';
}
