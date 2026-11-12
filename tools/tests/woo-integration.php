<?php
/* Free-shipping notice against real WooCommerce zones, coupons and carts (G-54). */
require_once '/wordpress/wp-load.php';
function bst_test( $ok, $label ) {
	file_put_contents( '/ft/result.txt', ( $ok ? 'ok   ' : 'FAIL ' ) . $label . "\n", FILE_APPEND );
}
try {
	wp_set_current_user( 1 );
	update_option( 'woocommerce_calc_taxes', 'no' );
	set_theme_mod( 'free_ship_bar', true );
	set_theme_mod( 'free_ship_amount', 0 );
	WC()->frontend_includes();
	WC()->session  = new WC_Session_Handler();
	WC()->session->init();
	WC()->customer = new WC_Customer( 0, true );
	WC()->customer->set_shipping_country( 'VN' );
	WC()->cart     = new WC_Cart();

	$zone = new WC_Shipping_Zone();
	$zone->set_zone_name( 'VN' );
	$zone->add_location( 'VN', 'country' );
	$zone->save();
	$iid    = $zone->add_shipping_method( 'free_shipping' );
	$set    = function ( $requires, $ignore = 'no' ) use ( $iid ) {
		update_option( 'woocommerce_free_shipping_' . $iid . '_settings', array( 'title' => 'Free', 'requires' => $requires, 'min_amount' => '100', 'ignore_discounts' => $ignore ) );
		WC_Cache_Helper::get_transient_version( 'shipping', true );
	};
	$p = new WC_Product_Simple();
	$p->set_regular_price( '60' );
	$p->save();
	foreach ( array( 'TENOFF' => array( 'fixed_cart', 30, false ), 'SHIPFREE' => array( 'fixed_cart', 0, true ) ) as $code => $c ) {
		$co = new WC_Coupon();
		$co->set_code( $code );
		$co->set_discount_type( $c[0] );
		$co->set_amount( $c[1] );
		$co->set_free_shipping( $c[2] );
		$co->save();
	}
	$bar = function () {
		ob_start();
		bst_free_shipping_bar();
		return ob_get_clean();
	};
	$reset = function ( $qty, $coupons = array() ) use ( $p ) {
		WC()->cart->empty_cart();
		WC()->cart->add_to_cart( $p->get_id(), $qty );
		foreach ( $coupons as $c ) {
			WC()->cart->apply_coupon( $c );
		}
		WC()->cart->calculate_totals();
	};

	$set( 'min_amount' );
	$reset( 1 );
	bst_test( false !== strpos( $bar(), 'more to get free shipping' ), 'min_amount: 60 of 100 shows amount left' );
	$reset( 2, array( 'TENOFF' ) );
	bst_test( false !== strpos( $bar(), 'more to get' ), 'min_amount: 120 minus 30 discount still short' );

	$set( 'min_amount', 'yes' );
	$reset( 2, array( 'TENOFF' ) );
	bst_test( false !== strpos( $bar(), 'qualifies' ), 'ignore_discounts: 120 before discount qualifies' );

	$set( 'either' );
	$reset( 1, array( 'SHIPFREE' ) );
	bst_test( false !== strpos( $bar(), 'qualifies' ), 'either: free-shipping coupon qualifies under the amount' );

	$set( 'both' );
	$reset( 2 );
	bst_test( '' === $bar(), 'both: amount without coupon shows nothing' );
	$reset( 2, array( 'SHIPFREE' ) );
	bst_test( false !== strpos( $bar(), 'qualifies' ), 'both: amount plus coupon qualifies' );
	$reset( 1, array( 'SHIPFREE' ) );
	bst_test( false !== strpos( $bar(), 'more to get' ), 'both: coupon but short shows amount left' );

	$set( 'coupon' );
	$reset( 1 );
	bst_test( '' === $bar(), 'coupon-only method shows no bar' );

	// The bar must agree with WooCommerce's own availability check for each case.
	foreach ( array( array( 'min_amount', 'yes', 2, array( 'TENOFF' ) ), array( 'both', 'no', 2, array( 'SHIPFREE' ) ), array( 'either', 'no', 1, array( 'SHIPFREE' ) ) ) as $c ) {
		$set( $c[0], $c[1] );
		$reset( $c[2], $c[3] );
		$m    = new WC_Shipping_Free_Shipping( $iid );
		$pkgs = WC()->cart->get_shipping_packages();
		bst_test( $m->is_available( reset( $pkgs ) ) === ( false !== strpos( $bar(), 'qualifies' ) ), 'matches WooCommerce is_available: ' . $c[0] );
	}

	set_theme_mod( 'free_ship_amount', 500 );
	$reset( 1 );
	bst_test( abs( bst_free_shipping_min() - 500 ) < 0.01, 'manual amount overrides zone' );

	// BIGSPY AI List mode with WooCommerce: blocks keep the old names and attributes.
	update_option( 'bst_features', array( 'listing' ) );
	require_once get_template_directory() . '/inc/features/listing.php';
	$cat = wp_insert_term( 'Writing AI', 'product_cat' );
	$mk  = function ( $name, $featured = false ) use ( $cat ) {
		$p = new WC_Product_External();
		$p->set_name( $name );
		$p->set_status( 'publish' );
		$p->set_product_url( 'https://' . sanitize_title( $name ) . '.test/?ref=bs' );
		$p->set_featured( $featured );
		$p->set_category_ids( array( $cat['term_id'] ) );
		return $p->save();
	};
	$a1 = $mk( 'Alpha Writer', true );
	$a2 = $mk( 'Beta Writer' );
	update_post_meta( $a1, 'tool_tagline', 'Writes blog posts' );
	update_post_meta( $a1, 'rating', '4.5' );
	$block = function ( $name, $attrs = array() ) {
		return render_block( array( 'blockName' => $name, 'attrs' => $attrs, 'innerBlocks' => array(), 'innerHTML' => '', 'innerContent' => array() ) );
	};
	$grid = $block( 'bigspy/listing-grid', array( 'limit' => 10 ) );
	bst_test( substr_count( $grid, 'class="bst-lcard ' ) >= 2 && false !== strpos( $grid, 'Writes blog posts' ) && false !== strpos( $grid, '4.5' ), 'listing: bigspy/listing-grid renders cards' );
	$logo_id = wp_insert_attachment( array( 'post_mime_type' => 'image/png', 'post_title' => 'alpha-logo', 'post_status' => 'inherit' ), 'alpha-logo.png' );
	update_post_meta( $logo_id, '_wp_attached_file', 'alpha-logo.png' );
	set_post_thumbnail( $a1, $logo_id );
	$h2 = $block( 'bigspy/elements', array( 'elementType' => 'latest', 'layout' => 'horizontal-2', 'showTags' => true, 'descriptionLines' => 3 ) );
	bst_test( str_contains( $h2, 'bst-lcard--horizontal' ) && str_contains( $h2, 'class="bst-lcard__watermark"' ) && str_contains( $h2, '--bst-lc-desc-lines:3' ) && str_contains( $h2, 'class="bst-lcard__in"' ), 'listing: horizontal card with logo watermark and line count' );
	bst_test( ! str_contains( $block( 'bigspy/elements', array( 'elementType' => 'latest', 'showWatermark' => 'hide' ) ), 'bst-lcard__watermark' ), 'listing: watermark can be hidden per block' );
	$vm = $block( 'bigspy/elements', array( 'elementType' => 'latest', 'count' => 1, 'viewMoreText' => 'All tools', 'seeMoreAlignment' => 'center' ) );
	bst_test( str_contains( $vm, 'bst-lelements__more--center' ) && str_contains( $vm, '>All tools</a>' ), 'listing: elements View more link with text and alignment' );
	$lm = $block( 'bigspy/elements', array( 'elementType' => 'latest', 'count' => 1, 'viewMoreBehavior' => 'load' ) );
	$lm_attrs = preg_match( '/data-bst-le-load="([^"]+)"/', $lm, $m ) ? json_decode( html_entity_decode( $m[1] ), true ) : array();
	bst_test( 2 === ( $lm_attrs['startPosition'] ?? 0 ) && str_contains( $lm, 'bst/v1/listing-elements' ), 'listing: elements Load more button carries the next start position' );
	$req = new WP_REST_Request( 'POST', '/bst/v1/listing-elements' );
	$req->set_param( 'attrs', $lm_attrs );
	$next = rest_do_request( $req )->get_data();
	bst_test( 1 === substr_count( $next['html'] ?? '', 'class="bst-lcard ' ) && ! str_contains( $next['html'] ?? '', 'bst-lelements__title' ), 'listing: Load more endpoint returns the next cards' );
	bst_test( str_contains( $block( 'bigspy/elements', array( 'elementType' => 'latest', 'displayMode' => 'slider' ) ), 'data-bst-le-slider' ), 'listing: elements slider mode' );
	$post_id = wp_insert_post( array( 'post_title' => 'Guide post', 'post_status' => 'publish', 'post_type' => 'post' ) );
	$bl = $block( 'bigspy/elements', array( 'elementType' => 'blog', 'blogLayout' => 'titles', 'blogViewMoreText' => 'More guides' ) );
	bst_test( str_contains( $bl, 'bst-lposts--titles' ) && str_contains( $bl, 'bst-lpost__num' ) && str_contains( $bl, 'More guides' ) && str_contains( $bl, 'Guide post' ), 'listing: elements blog titles layout with numbers and View more' );
	$cg = $block( 'bigspy/elements', array( 'elementType' => 'categories', 'categoryBorderRadius' => 4, 'categoryHoverShadow' => 'strong', 'categoryShowViewMore' => true ) );
	bst_test( str_contains( $cg, '--bst-lcat-radius:4px' ) && str_contains( $cg, '0 16px 40px' ) && str_contains( $cg, 'View all categories' ), 'listing: elements category grid style and View all' );
	wp_delete_post( $post_id, true );
	$feat = $block( 'bigspy/listing-grid', array( 'featuredOnly' => true ) );
	bst_test( 1 === substr_count( $feat, 'class="bst-lcard ' ) && false !== strpos( $feat, 'Featured' ), 'listing: featuredOnly filter and badge' );
	bst_test( false !== strpos( $block( 'bigspy/category-grid', array() ), 'Writing AI' ), 'listing: bigspy/category-grid' );
	bst_test( false !== strpos( $block( 'bigspy/search', array( 'buttonText' => 'Go' ) ), 'name="post_type" value="product"' ), 'listing: bigspy/search searches listings' );
	$ls = $block( 'bigspy/search', array( 'buttonColor' => '#008080', 'radius' => 20, 'height' => 56, 'borderColor' => 'red;x' ) );
	bst_test( str_contains( $ls, '--bst-ls-btn:#008080' ) && str_contains( $ls, '--bst-ls-radius:20px' ) && str_contains( $ls, '--bst-ls-h:56px' ) && ! str_contains( $ls, 'red;x' ), 'listing: search button colour, radius and height' );
	$css = (string) file_get_contents( get_template_directory() . '/assets/css/blocks.css' );
	bst_test( str_contains( $css, 'color: var(--bs-heading-color, var(--bs-heading));' ) && str_contains( $css, 'color: var(--bs-heading-color, #fff);' ), 'blocks: Banner/Section text scheme keeps a Heading block colour' );
	$GLOBALS['wp_query']->is_singular = false;
	bst_test( '' === $block( 'bigspy/elements', array( 'elementType' => 'related' ) ) && 1 === substr_count( $block( 'bigspy/elements', array( 'elementType' => 'related', 'relatedContextId' => $a1 ) ), 'class="bst-lcard ' ), 'listing: elements related excludes the listing itself' );
	bst_test( false !== strpos( do_shortcode( '[bigspy_elements type="latest"]' ), 'Beta Writer' ) && false !== strpos( do_shortcode( '[bigspy_categories]' ), 'Writing AI' ) && false !== strpos( do_shortcode( '[bigspy_search]' ), 'bst-lsearch' ), 'listing: old shortcodes render' );
	bst_test( 'https://alpha-writer.test/?ref=bs' === bigspy_listing_url( $a1 ) && bst_listing_has_affiliate( $a1 ), 'listing: external product URL is the listing link' );
	wp_update_post( array( 'ID' => $a2, 'post_content' => '<h2>FAQ</h2><details><summary>Is it free?</summary><p>Yes.</p></details>' ) );
	$faq_schema = bst_listing_faq_schema( $a2 );
	bst_test( 'FAQPage' === ( $faq_schema['@type'] ?? '' ) && 'Is it free?' === ( $faq_schema['mainEntity'][0]['name'] ?? '' ), 'listing: FAQPage schema from the FAQ in the description' );
	update_post_meta( $a1, 'key_features', "Fast drafts\nSEO mode" );
	update_post_meta( $a1, 'pros', 'Cheap' );
	update_post_meta( $a1, 'integrations', "Slack\nZapier" );
	update_post_meta( $a1, '_bigspy_internal_links', array( array( 'label' => 'Writing AI', 'url' => home_url( '/writing/' ) ), array( 'label' => 'Spam', 'url' => 'https://evil.test/' ) ) );
	$sec = bst_listing_sections_html( $a1 );
	bst_test( 2 === substr_count( $sec, '<li>' ) - 1 && false !== strpos( $sec, 'Pros &amp; Cons' ) && false !== strpos( $sec, '<span>Zapier</span>' ) && false === strpos( $sec, 'bst-listing-cons' ), 'listing: key features, pros and integrations sections' );
	$links = bst_listing_internal_links( $a1 );
	bst_test( 1 === count( $links ) && false !== strpos( bst_listing_links_html( $a1 ), 'Writing AI' ), 'listing: related resources keep same-site links only' );
	$toc = bst_toc_apply( bst_listing_h2_dividers( '<h2>One</h2><p>a</p><h2>Two</h2><h3>Sub</h3>' ), 3 );
	bst_test( 1 === substr_count( $toc, 'bst-listing-divider' ) && false !== strpos( $toc, 'class="bs-toc"' ) && false !== strpos( $toc, 'href="#two"' ), 'listing: description TOC and H2 dividers' );
	$nodes = bst_listing_schema_nodes( $a1 );
	bst_test( 'ItemPage' === $nodes['bigspy_listing_webpage']['@type'] && 'Writing AI' === $nodes['bigspy_listing_breadcrumb']['itemListElement'][2]['name'], 'listing: schema breadcrumb through the category' );
	update_post_meta( $a2, '_bigspy_ad', '1' );
	update_option( 'bst_feature_listing', array( 'sponsored_on' => 1 ) );
	bst_test( false !== strpos( do_shortcode( '[bigspy_home_sponsor]' ), 'beta-writer.test' ), 'listing: home sponsor is the first AD listing' );
	// Templates: patterns use registered blocks only; shop loops use listing cards.
	bst_listing_patterns();
	$bad = array();
	foreach ( WP_Block_Patterns_Registry::get_instance()->get_all_registered() as $pat ) {
		if ( in_array( 'bigspy-list', (array) ( $pat['categories'] ?? array() ), true ) ) {
			$walk = function ( $blocks ) use ( &$walk, &$bad ) {
				foreach ( $blocks as $b ) {
					if ( $b['blockName'] && ! WP_Block_Type_Registry::get_instance()->is_registered( $b['blockName'] ) ) {
						$bad[] = $b['blockName'];
					}
					$walk( $b['innerBlocks'] );
				}
			};
			$walk( parse_blocks( $pat['content'] ) );
			$bad[] = 'ok';
		}
	}
	bst_test( 6 === count( array_keys( $bad, 'ok', true ) ) && 6 === count( $bad ), 'listing: 6 patterns, all blocks registered ' . implode( ',', array_diff( $bad, array( 'ok' ) ) ) );
	query_posts( array( 'post_type' => 'product' ) );
	bst_test( str_ends_with( (string) apply_filters( 'wc_get_template_part', 'x', 'content', 'product' ), 'content-listing.php' ), 'listing: shop loop uses listing cards' );
	update_option( 'bst_feature_listing', array( 'sponsored_on' => 1, 'archive_cards' => 0 ) );
	bst_test( 'x' === apply_filters( 'wc_get_template_part', 'x', 'content', 'product' ), 'listing: archive cards can be turned off' );
	if ( ! function_exists( 'bst_aid_block_types' ) ) {
		require_once BST_DIR . '/ai-designer/includes/tools-design.php';
	}
	$aid_types = bst_aid_block_types();
	bst_test( isset( $aid_types['bigspy/listing-grid'], $aid_types['bst/listing-info'] ), 'listing: AI Designer gets BIGSPY AI List blocks when the mode is on' );
	bst_test( ! isset( $aid_types['bigspy/section'] ) && ! isset( $aid_types['bigspy/home-seo-divider'] ), 'listing: AI Designer never gets display-only legacy blocks' );
	wp_reset_query();
	update_option( 'bst_feature_listing', array( 'sponsored_on' => 1 ) );
	update_option( 'theme_mods_bigspy-ai-list', array( 'bigspy_home_destination_buttons' => array( array( 'label' => 'Go', 'url' => 'https://example.com/go/' ) ) ) );
	$sc = do_shortcode( '[bigspy_home_destination_buttons]' );
	bst_test( str_contains( $sc, 'href="https://example.com/go/"' ) && ! str_contains( bst_legacy_hero_tokens( '{{listing_count}} tools' ), '{{' ), 'listing: destination buttons shortcode and counters for Banner heroes' );
	bst_feature_set( 'listing', false );
	do_action( 'after_switch_theme', 'BIGSPY AI List', wp_get_theme() );
	$auto = bst_feature_on( 'listing' );
	bst_feature_set( 'listing', false );
	do_action( 'after_switch_theme', 'BIGSPY AI List', wp_get_theme() );
	bst_test( $auto && ! bst_feature_on( 'listing' ), 'listing: switching from the old theme turns List mode on once' );
	bst_feature_set( 'listing', true );
	$lp = wc_get_products( array( 'limit' => 1, 'return' => 'ids' ) );
	if ( $lp ) {
		$lp = $lp[0];
		$head = bst_listing_header_html( $lp );
		bst_test( str_contains( $head, 'bst-ls-hero__title' ) && str_contains( $head, esc_html( get_the_title( $lp ) ) ), 'listing: directory layout header card' );
		$cat = wp_insert_term( 'Sidebar Cat', 'product_cat' );
		wp_set_object_terms( $lp, array( (int) $cat['term_id'] ), 'product_cat', true );
		$def = bst_listing_sidebar_html( $lp );
		bst_test( str_contains( $def, 'bst-ls-sidebar' ) && str_contains( $def, 'Sidebar Cat' ), 'listing sidebar: default parts while the widget area is empty' );
		bst_test( 3 === bst_listing_sidebar_fill_defaults() && 0 === bst_listing_sidebar_fill_defaults(), 'listing sidebar: default blocks added once' );
		$w = get_option( 'widget_block' );
		$areas = wp_get_sidebars_widgets();
		$id = (int) str_replace( 'block-', '', $areas['sidebar-listing'][1] );
		$w[ $id ]['content'] = '<!-- wp:bst/listing-sidebar {"part":"categories","title":"Topics"} /-->';
		update_option( 'widget_block', $w );
		$areas['sidebar-listing'] = array( $areas['sidebar-listing'][1] );
		wp_set_sidebars_widgets( $areas );
		// List mode was turned on after widgets_init in this run; on a site both happen on the next page load.
		register_sidebar( array( 'id' => 'sidebar-listing', 'name' => 'Listing page sidebar' ) );
		$GLOBALS['wp_widget_factory']->widgets['WP_Widget_Block']->_register();
		$cus = bst_listing_sidebar_html( $lp );
		bst_test( str_contains( $cus, '>Topics<' ) && str_contains( $cus, 'Sidebar Cat' ) && ! str_contains( $cus, 'Related resources' ), 'listing sidebar: widget area replaces the default, custom heading');
		$areas['sidebar-listing'] = array();
		wp_set_sidebars_widgets( $areas );
		$GLOBALS['wp_query']->is_product = true;
		$GLOBALS['wp_query']->is_singular = true;
		$GLOBALS['wp_query']->queried_object = get_post( $lp );
		$GLOBALS['wp_query']->queried_object_id = $lp;
		$GLOBALS['wp_query']->set( 'post_type', 'product' );
		bst_test( str_ends_with( apply_filters( 'template_include', 'x.php' ), 'woocommerce/single-listing.php' ) || ! is_product(), 'listing: product pages use the directory layout' );
		update_option( 'bst_feature_listing', array( 'sponsored_on' => 1, 'single_layout' => 0 ) );
		bst_test( ! str_ends_with( apply_filters( 'template_include', 'x.php' ), 'single-listing.php' ), 'listing: directory layout can be turned off' );
	}
	$dir = bst_category_directory_html();
	bst_test( '' === $dir || ( str_contains( $dir, 'bst-catdir__nav' ) && str_contains( $dir, 'bst-catdir__section' ) ), 'listing: category directory renders navigation and sections' );
	bst_test( 'rand' === bst_category_featured_order(), 'listing: Categories Featured changes order on every load by default' );
	bst_test( do_shortcode( '[bigspy_category_directory]' ) === $dir, 'listing: [bigspy_category_directory] places the directory under a hero' );
	$hs = do_shortcode( '[bigspy_home_sponsor]' );
	bst_test( '' === $hs || ( str_contains( $hs, 'data-bst-sponsored="0"' ) && str_contains( $hs, 'data-context="home"' ) ), 'listing: homepage Sponsored-by brand joins the rotation' );
	$bar = bst_shop_toolbar_html();
	bst_test( str_contains( $bar, 'name="product_cat"' ) && str_contains( $bar, 'name="orderby"' ) && str_contains( $bar, 'name="s"' ), 'listing: shop toolbar has search, category and sort' );
} catch ( Throwable $e ) {
	bst_test( false, get_class( $e ) . ' ' . $e->getMessage() . ' @' . $e->getLine() );
}
