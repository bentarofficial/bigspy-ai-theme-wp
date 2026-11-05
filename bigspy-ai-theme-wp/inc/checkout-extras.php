<?php
/**
 * Cart & checkout display options: free-shipping progress, coupon position, a note
 * before payment. Presentation only: shipping rules, fees and order flow stay WooCommerce's.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_defaults',
	function ( $d ) {
		return $d + array( 'free_ship_bar' => false, 'free_ship_amount' => 0, 'checkout_coupon' => 'default', 'checkout_note' => '' );
	}
);

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$fields[] = array( 'id' => 'free_ship_bar', 'section' => 'bst_checkout', 'type' => 'checkbox', 'label' => __( 'Free-shipping progress bar', 'bigspy-ai-theme-wp' ), 'description' => __( 'Shows how much more the customer needs to spend for free shipping, on the cart, checkout and mini cart. Only a message: shipping costs still come from your WooCommerce shipping zones. Works on the classic cart and checkout pages ([woocommerce_cart], [woocommerce_checkout]) and the mini cart, not inside the Cart and Checkout blocks.', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'free_ship_amount', 'section' => 'bst_checkout', 'type' => 'number', 'label' => __( 'Free-shipping amount (0 = read from WooCommerce)', 'bigspy-ai-theme-wp' ), 'description' => __( 'Leave 0 to use the minimum amount of the "Free shipping" method in the customer\'s shipping zone.', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 0, 'step' => 1000 ) );
		$fields[] = array( 'id' => 'checkout_coupon', 'section' => 'bst_checkout', 'type' => 'select', 'label' => __( 'Coupon field on checkout', 'bigspy-ai-theme-wp' ), 'description' => __( 'For the classic checkout page ([woocommerce_checkout]).', 'bigspy-ai-theme-wp' ), 'choices' => array( 'default' => __( 'Top of the page (WooCommerce default)', 'bigspy-ai-theme-wp' ), 'bottom' => __( 'Below the checkout form', 'bigspy-ai-theme-wp' ), 'hide' => __( 'Hide on checkout (coupons stay on the cart page)', 'bigspy-ai-theme-wp' ) ) );
		$fields[] = array( 'id' => 'checkout_note', 'section' => 'bst_checkout', 'type' => 'html', 'label' => __( 'Note before the payment methods', 'bigspy-ai-theme-wp' ), 'description' => __( 'For example delivery times, return policy or a support phone number. Leave empty to show nothing.', 'bigspy-ai-theme-wp' ) );
		return $fields;
	}
);

/**
 * Cart total the way WooCommerce's Free shipping method measures it
 * (WC_Shipping_Free_Shipping::is_available): displayed subtotal, minus the
 * discount tax when prices show tax, minus discounts unless the method
 * ignores them, rounded to the store decimals.
 */
function bst_free_shipping_cart_total( $ignore_discounts = false ) {
	$cart  = WC()->cart;
	$total = (float) $cart->get_displayed_subtotal();
	if ( $cart->display_prices_including_tax() ) {
		$total -= (float) $cart->get_discount_tax();
	}
	if ( ! $ignore_discounts ) {
		$total -= (float) $cart->get_discount_total();
	}
	return round( $total, function_exists( 'wc_get_price_decimals' ) ? wc_get_price_decimals() : 2 );
}

/**
 * Free-shipping state of the current cart, matched to the real WooCommerce rules.
 * Returns array( 'state' => 'done'|'progress'|'none', 'min' => float, 'total' => float ).
 *
 * - done: WooCommerce itself offers Free shipping for every package (is_available()).
 * - progress: an amount is still missing and reaching it alone gives free shipping
 *   (requires min_amount, or either without a coupon).
 * - none: nothing honest to show: no Free shipping method, coupon-only or
 *   "amount AND coupon" without the coupon, or packages in zones without it.
 * A Customizer amount overrides the zone (a manual promise; shown as progress/done).
 */
function bst_free_shipping_state() {
	$none = array( 'state' => 'none', 'min' => 0.0, 'total' => 0.0 );
	if ( ! WC()->cart ) {
		return $none;
	}
	$manual = (float) bst_mod( 'free_ship_amount' );
	if ( $manual > 0 ) {
		$total = bst_free_shipping_cart_total();
		return array( 'state' => $total >= $manual ? 'done' : 'progress', 'min' => $manual, 'total' => $total );
	}
	if ( ! class_exists( 'WC_Shipping_Zones' ) ) {
		return $none;
	}
	$packages = WC()->cart->get_shipping_packages();
	if ( ! $packages ) {
		return $none;
	}
	$all_free = true;
	$min      = 0.0;
	$total    = 0.0;
	foreach ( $packages as $package ) {
		$zone = WC_Shipping_Zones::get_zone_matching_package( $package );
		$free = false;
		$need = 0.0;
		foreach ( $zone->get_shipping_methods( true ) as $method ) {
			if ( 'free_shipping' !== $method->id ) {
				continue;
			}
			if ( $method->is_available( $package ) ) {
				$free = true;
				break;
			}
			$requires = (string) $method->requires;
			$amount   = (float) $method->min_amount;
			// Only an amount the customer can reach by adding products, with no coupon still required.
			if ( $amount > 0 && in_array( $requires, array( 'min_amount', 'either' ), true ) ) {
				$need  = 0.0 === $need ? $amount : min( $need, $amount );
				$total = bst_free_shipping_cart_total( 'yes' === $method->ignore_discounts );
			}
		}
		if ( $free ) {
			continue;
		}
		$all_free = false;
		if ( $need <= 0 ) {
			return $none; // This package can not get free shipping by spending more.
		}
		$min = max( $min, $need );
	}
	if ( $all_free ) {
		return array( 'state' => 'done', 'min' => 0.0, 'total' => 0.0 );
	}
	if ( $total >= $min ) {
		return $none; // Amount reached but WooCommerce still does not offer it (a filter or another rule): promise nothing.
	}
	return array( 'state' => 'progress', 'min' => $min, 'total' => $total );
}

function bst_free_shipping_bar() {
	if ( ! bst_mod( 'free_ship_bar' ) || ! WC()->cart || WC()->cart->is_empty() || ! WC()->cart->needs_shipping() ) {
		return;
	}
	$s = bst_free_shipping_state();
	if ( 'none' === $s['state'] ) {
		return;
	}
	$left = 'done' === $s['state'] ? 0 : max( 0, $s['min'] - $s['total'] );
	$pct  = 'done' === $s['state'] ? 100 : (int) floor( min( 99, $s['total'] / $s['min'] * 100 ) );
	$text = $left > 0
		/* translators: %s: amount still needed, e.g. 120.000₫ */
		? sprintf( __( 'Add %s more to get free shipping.', 'bigspy-ai-theme-wp' ), wc_price( $left ) )
		: __( 'Your order qualifies for free shipping.', 'bigspy-ai-theme-wp' );
	echo '<div class="bs-free-ship' . ( $left > 0 ? '' : ' is-done' ) . '" role="status"><p class="bs-free-ship__text">' . wp_kses_post( $text ) . '</p><div class="bs-free-ship__bar" aria-hidden="true"><span style="width:' . esc_attr( $pct ) . '%"></span></div></div>';
}
add_action( 'woocommerce_before_cart_totals', 'bst_free_shipping_bar' );
add_action( 'woocommerce_review_order_before_payment', 'bst_free_shipping_bar', 5 );
add_action( 'woocommerce_widget_shopping_cart_before_buttons', 'bst_free_shipping_bar' );

/** The note goes inside the refreshed order review, just above the payment methods. */
add_action(
	'woocommerce_review_order_before_payment',
	function () {
		$note = trim( (string) bst_mod( 'checkout_note' ) );
		if ( '' !== $note ) {
			echo '<div class="bs-checkout-note">' . wp_kses_post( wpautop( $note ) ) . '</div>';
		}
	},
	8
);

/** Move or hide the classic checkout coupon form. It stays outside the checkout form (no nested forms). */
add_action(
	'wp',
	function () {
		$where = bst_mod( 'checkout_coupon' );
		if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || 'default' === $where ) {
			return;
		}
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
		if ( 'bottom' === $where ) {
			add_action( 'woocommerce_after_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
		}
	}
);
