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
	$feat = $block( 'bigspy/listing-grid', array( 'featuredOnly' => true ) );
	bst_test( 1 === substr_count( $feat, 'class="bst-lcard ' ) && false !== strpos( $feat, 'Featured' ), 'listing: featuredOnly filter and badge' );
	bst_test( false !== strpos( $block( 'bigspy/category-grid', array() ), 'Writing AI' ), 'listing: bigspy/category-grid' );
	bst_test( false !== strpos( $block( 'bigspy/search', array( 'buttonText' => 'Go' ) ), 'name="post_type" value="product"' ), 'listing: bigspy/search searches listings' );
	$GLOBALS['wp_query']->is_singular = false;
	bst_test( '' === $block( 'bigspy/elements', array( 'elementType' => 'related' ) ) && 1 === substr_count( $block( 'bigspy/elements', array( 'elementType' => 'related', 'relatedContextId' => $a1 ) ), 'class="bst-lcard ' ), 'listing: elements related excludes the listing itself' );
	bst_test( false !== strpos( do_shortcode( '[bigspy_elements type="latest"]' ), 'Beta Writer' ) && false !== strpos( do_shortcode( '[bigspy_categories]' ), 'Writing AI' ) && false !== strpos( do_shortcode( '[bigspy_search]' ), 'bst-lsearch' ), 'listing: old shortcodes render' );
	bst_test( 'https://alpha-writer.test/?ref=bs' === bigspy_listing_url( $a1 ) && bst_listing_has_affiliate( $a1 ), 'listing: external product URL is the listing link' );
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
} catch ( Throwable $e ) {
	bst_test( false, get_class( $e ) . ' ' . $e->getMessage() . ' @' . $e->getLine() );
}
