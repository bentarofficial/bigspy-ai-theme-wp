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
} catch ( Throwable $e ) {
	bst_test( false, get_class( $e ) . ' ' . $e->getMessage() . ' @' . $e->getLine() );
}
