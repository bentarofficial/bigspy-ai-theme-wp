<?php
/**
 * Free-shipping bar cases (SOURCE-AUDIT §3). No WordPress needed:
 * php tools/tests/free-shipping-check.php
 *
 * The stub method copies the decision of WooCommerce's
 * WC_Shipping_Free_Shipping::is_available() (requires, ignore_discounts,
 * coupon, discount tax), so the bar is checked against the same rules.
 */
define( 'ABSPATH', __DIR__ );
$GLOBALS['mods'] = array();
function bst_mod( $k ) {
	return $GLOBALS['mods'][ $k ] ?? 0;
}
function add_filter() {}
function add_action() {}
function __( $s ) {
	return $s;
}
function wc_price( $v ) {
	return number_format( $v, 0, ',', '.' ) . '₫';
}
function wc_get_price_decimals() {
	return 0;
}
function wp_kses_post( $s ) {
	return $s;
}
function esc_attr( $s ) {
	return $s;
}

class Cart {
	public $subtotal = 0, $discount = 0, $discount_tax = 0, $incl_tax = false, $coupon_free = false, $packages = array( array( 'zone' => 'vn' ) );
	function get_displayed_subtotal() { return $this->subtotal; }
	function get_discount_total() { return $this->discount; }
	function get_discount_tax() { return $this->discount_tax; }
	function display_prices_including_tax() { return $this->incl_tax; }
	function get_shipping_packages() { return $this->packages; }
	function is_empty() { return false; }
	function needs_shipping() { return true; }
}
class FreeMethod {
	public $id = 'free_shipping', $requires, $min_amount, $ignore_discounts;
	function __construct( $requires, $min, $ignore = 'no' ) {
		$this->requires = $requires; $this->min_amount = $min; $this->ignore_discounts = $ignore;
	}
	/* Same decision as WooCommerce 9/10 WC_Shipping_Free_Shipping::is_available(). */
	function is_available( $package ) {
		$cart = WC()->cart;
		$coupon = in_array( $this->requires, array( 'coupon', 'either', 'both' ), true ) && $cart->coupon_free;
		$met    = false;
		if ( in_array( $this->requires, array( 'min_amount', 'either', 'both' ), true ) ) {
			$total = $cart->get_displayed_subtotal();
			if ( $cart->display_prices_including_tax() ) {
				$total -= $cart->get_discount_tax();
			}
			if ( 'no' === $this->ignore_discounts ) {
				$total -= $cart->get_discount_total();
			}
			$met = round( $total, 0 ) >= $this->min_amount;
		}
		switch ( $this->requires ) {
			case 'min_amount': return $met;
			case 'coupon': return $coupon;
			case 'both': return $met && $coupon;
			case 'either': return $met || $coupon;
			default: return true;
		}
	}
}
class Zone {
	public $methods;
	function __construct( $m ) { $this->methods = $m; }
	function get_shipping_methods() { return $this->methods; }
}
class WC_Shipping_Zones {
	static $zones = array();
	static function get_zone_matching_package( $p ) { return self::$zones[ $p['zone'] ] ?? new Zone( array() ); }
}
$GLOBALS['wc'] = (object) array( 'cart' => null );
function WC() {
	return $GLOBALS['wc'];
}
require dirname( __DIR__, 2 ) . '/bigspy-ai-theme-wp/inc/checkout-extras.php';

$fail = 0;
function check( $label, $cart, $zones, $want_state, $want_text = null, $mods = array() ) {
	global $fail;
	$GLOBALS['wc']->cart     = $cart;
	WC_Shipping_Zones::$zones = $zones;
	$GLOBALS['mods']         = $mods + array( 'free_ship_bar' => 1 );
	$s = bst_free_shipping_state();
	ob_start();
	bst_free_shipping_bar();
	$html = ob_get_clean();
	$ok   = $s['state'] === $want_state && ( null === $want_text || false !== strpos( $html, $want_text ) ) && ( 'none' !== $want_state || '' === $html );
	echo ( $ok ? 'ok   ' : 'FAIL ' ) . $label . ( $ok ? '' : ' → ' . json_encode( $s ) . ' ' . strip_tags( $html ) ) . "\n";
	$fail += $ok ? 0 : 1;
}
function cart( $sub, $props = array() ) {
	$c = new Cart();
	$c->subtotal = $sub;
	foreach ( $props as $k => $v ) {
		$c->$k = $v;
	}
	return $c;
}
$vn = function ( $requires, $min, $ignore = 'no' ) {
	return array( 'vn' => new Zone( array( (object) array( 'id' => 'flat_rate' ), new FreeMethod( $requires, $min, $ignore ) ) ) );
};
// The flat-rate stub has no is_available; the code must skip non-free methods first.
check( 'min amount: progress shows the missing amount', cart( 300000 ), $vn( 'min_amount', 500000 ), 'progress', 'Add 200.000₫ more' );
check( 'min amount reached: qualifies', cart( 500000 ), $vn( 'min_amount', 500000 ), 'done', 'qualifies' );
check( 'discount counts (ignore_discounts no): not yet', cart( 520000, array( 'discount' => 50000 ) ), $vn( 'min_amount', 500000 ), 'progress', 'Add 30.000₫ more' );
check( 'ignore_discounts yes: before-discount total qualifies', cart( 520000, array( 'discount' => 50000 ) ), $vn( 'min_amount', 500000, 'yes' ), 'done' );
check( 'ignore_discounts yes: progress uses the before-discount total', cart( 450000, array( 'discount' => 50000 ) ), $vn( 'min_amount', 500000, 'yes' ), 'progress', 'Add 50.000₫ more' );
check( 'prices incl. tax: discount tax removed like WooCommerce', cart( 505000, array( 'incl_tax' => true, 'discount_tax' => 10000 ) ), $vn( 'min_amount', 500000, 'yes' ), 'progress', 'Add 5.000₫ more' );
check( 'either + free-shipping coupon: qualifies below the amount', cart( 100000, array( 'coupon_free' => true ) ), $vn( 'either', 500000 ), 'done' );
check( 'either without coupon: progress', cart( 100000 ), $vn( 'either', 500000 ), 'progress', 'Add 400.000₫ more' );
check( 'both without coupon: no promise', cart( 600000 ), $vn( 'both', 500000 ), 'none' );
check( 'both with coupon, amount missing: no promise of "add X"', cart( 100000, array( 'coupon_free' => true ) ), $vn( 'both', 500000 ), 'none' );
check( 'both with coupon and amount: qualifies', cart( 600000, array( 'coupon_free' => true ) ), $vn( 'both', 500000 ), 'done' );
check( 'coupon only, no coupon: nothing', cart( 900000 ), $vn( 'coupon', 0 ), 'none' );
check( 'coupon only, with coupon: qualifies', cart( 10, array( 'coupon_free' => true ) ), $vn( 'coupon', 0 ), 'done' );
check( 'zone without free shipping: nothing', cart( 900000 ), array( 'vn' => new Zone( array( (object) array( 'id' => 'flat_rate' ) ) ) ), 'none' );
check( 'two packages, one zone without free shipping: nothing', cart( 300000, array( 'packages' => array( array( 'zone' => 'vn' ), array( 'zone' => 'xx' ) ) ) ), $vn( 'min_amount', 500000 ), 'none' );
check( 'two packages, both free: qualifies', cart( 600000, array( 'packages' => array( array( 'zone' => 'vn' ), array( 'zone' => 'vn' ) ) ) ), $vn( 'min_amount', 500000 ), 'done' );
check( 'manual Customizer amount still works', cart( 300000 ), array(), 'progress', 'Add 100.000₫ more', array( 'free_ship_amount' => 400000 ) );
check( 'bar never reaches 100% before free shipping', cart( 499999 ), $vn( 'min_amount', 500000 ), 'progress', 'width:99%' );
echo $fail ? "\n$fail failed\n" : "\nall passed\n";
exit( $fail ? 1 : 0 );
