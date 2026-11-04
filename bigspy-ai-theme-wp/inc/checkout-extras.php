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
 * Free-shipping rule for the current cart: the Customizer amount, else the
 * "Free shipping" method of the zone that matches the cart, read the way
 * WooCommerce checks it (requires, ignore_discounts). Null when there is none.
 *
 * @return array|null { min: float, ignore_discounts: bool, requires: string }
 */
function bst_free_shipping_rule() {
	$manual = (float) bst_mod( 'free_ship_amount' );
	if ( $manual > 0 ) {
		return array( 'min' => $manual, 'ignore_discounts' => false, 'requires' => 'min_amount' );
	}
	if ( ! WC()->cart || ! class_exists( 'WC_Shipping_Zones' ) ) {
		return null;
	}
	$packages = WC()->cart->get_shipping_packages();
	if ( ! $packages ) {
		return null;
	}
	$zone = WC_Shipping_Zones::get_zone_matching_package( reset( $packages ) );
	foreach ( $zone->get_shipping_methods( true ) as $method ) {
		if ( 'free_shipping' === $method->id && in_array( $method->requires, array( 'min_amount', 'either', 'both' ), true ) && (float) $method->min_amount > 0 ) {
			return array(
				'min'              => (float) $method->min_amount,
				'ignore_discounts' => 'yes' === $method->get_option( 'ignore_discounts', 'no' ),
				'requires'         => (string) $method->requires,
			);
		}
	}
	return null;
}

/** Amount needed for free shipping, 0 when there is none. */
function bst_free_shipping_min() {
	$rule = bst_free_shipping_rule();
	return $rule ? $rule['min'] : 0.0;
}

/** Whether an applied coupon grants free shipping. */
function bst_cart_has_free_shipping_coupon() {
	foreach ( WC()->cart->get_coupons() as $coupon ) {
		if ( $coupon->is_valid() && $coupon->get_free_shipping() ) {
			return true;
		}
	}
	return false;
}

function bst_free_shipping_bar() {
	if ( ! bst_mod( 'free_ship_bar' ) || ! WC()->cart || WC()->cart->is_empty() || ! WC()->cart->needs_shipping() ) {
		return;
	}
	$rule = bst_free_shipping_rule();
	if ( ! $rule ) {
		return;
	}
	$coupon = bst_cart_has_free_shipping_coupon();
	if ( 'both' === $rule['requires'] && ! $coupon ) {
		return; // The amount alone never qualifies; a progress bar would mislead.
	}
	$min   = $rule['min'];
	$total = (float) WC()->cart->get_displayed_subtotal();
	if ( ! $rule['ignore_discounts'] ) {
		$total -= (float) WC()->cart->get_discount_total();
		if ( WC()->cart->display_prices_including_tax() ) {
			$total -= (float) WC()->cart->get_discount_tax();
		}
	}
	$total = round( $total, wc_get_price_decimals() );
	$left  = ( 'either' === $rule['requires'] && $coupon ) ? 0 : max( 0, $min - $total );
	$pct   = $left > 0 ? (int) round( min( 100, max( 0, $total ) / $min * 100 ) ) : 100;
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
