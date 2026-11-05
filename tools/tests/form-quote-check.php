<?php
/**
 * G-58 quote cases, no WordPress needed: php tools/tests/form-quote-check.php
 */
define( 'ABSPATH', __DIR__ );
function sanitize_key( $k ) {
	return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $k ) );
}
function wp_json_encode( $v ) {
	return json_encode( $v );
}
function __( $s ) {
	return $s;
}
function esc_html( $s ) {
	return htmlspecialchars( (string) $s );
}
$theme = dirname( __DIR__, 2 ) . '/bigspy-ai-theme-wp/inc/';
preg_match( '/function bst_native_field_options\(.*?\n}\n/s', file_get_contents( $theme . 'form-native.php' ), $m );
eval( $m[0] );
require $theme . 'form-schema.php';
require $theme . 'form-quote.php';

$fail = 0;
function t( $ok, $label ) {
	global $fail;
	echo ( $ok ? 'ok   ' : 'FAIL ' ) . $label . "\n";
	$fail += $ok ? 0 : 1;
}

// Case 1: laundry demo prices.
$fields  = array(
	array( 'id' => 'f_kg', 'type' => 'number', 'name' => 'kg', 'label' => 'Kg', 'required' => true ),
	array( 'id' => 'f_ship', 'type' => 'checkbox', 'name' => 'ship', 'label' => 'Delivery', 'options' => 'Giao nhận', 'prices' => 'Giao nhận = 25000' ),
	array( 'id' => 'f_speed', 'type' => 'radio', 'name' => 'speed', 'label' => 'Speed', 'options' => "Thường\nNhanh" ),
	array( 'id' => 'f_area', 'type' => 'select', 'name' => 'area', 'label' => 'Area', 'options' => "Nội thành\nNgoại thành", 'show' => array( 'rules' => array( array( 'field' => 'f_ship', 'op' => 'not_empty' ) ) ) ),
);
$pricing = array(
	'enabled' => true,
	'lines'   => array(
		array( 'id' => 'l_wash', 'label' => 'Giặt', 'expr' => '{f_kg} * 18000' ),
		array( 'id' => 'l_ship', 'label' => 'Giao nhận', 'expr' => '{f_ship}' ),
		array( 'id' => 'l_fast', 'label' => 'Giặt nhanh', 'expr' => '{l_wash} * 0.2 * {f_speed=Nhanh}' ),
	),
);
$q = bst_quote_compute( $pricing, $fields, array( 'kg' => '3', 'ship' => 'Giao nhận', 'speed' => 'Thường' ) );
t( 'ok' === $q['status'] && 79000.0 === $q['total'], '3 kg + delivery = 79.000' );
$q = bst_quote_compute( $pricing, $fields, array( 'kg' => '3', 'ship' => 'Giao nhận', 'speed' => 'Nhanh' ) );
t( 89800.0 === $q['total'] && 10800.0 === $q['lines'][2]['amount'], 'express +20% of washing only = 89.800' );
t( "Giặt: 54.000 ₫\nGiao nhận: 25.000 ₫\nGiặt nhanh: 10.800 ₫\nEstimated total: 89.800 ₫" === bst_quote_text( $q ), 'text table in VND' );

// Case 2: locale numbers and currencies with decimals.
t( 1.5 === bst_quote_number( '1,5' ) && 1.5 === bst_quote_number( '1.5' ) && 1234.5 === bst_quote_number( '1.234,5' ) && 1234.5 === bst_quote_number( '1,234.5' ) && null === bst_quote_number( '1e3' ), 'locale numbers' );
$q = bst_quote_compute( $pricing, $fields, array( 'kg' => '1,5', 'speed' => 'Thường' ) );
t( 27000.0 === $q['total'], '1,5 kg = 27.000' );
$usd = array( 'enabled' => true, 'decimals' => 2, 'symbol' => '$', 'lines' => array( array( 'id' => 'l_a', 'label' => 'A', 'expr' => '{f_kg} * 1.005' ) ) );
$q   = bst_quote_compute( $usd, $fields, array( 'kg' => '1' ) );
t( 1.01 === $q['total'] && '1,01 $' === bst_quote_money( $q['total'], $q ), '2 decimals round half up without float drift' );

// Case 4: hidden / unchosen options do not charge; missing required input is not a free total.
$q = bst_quote_compute( $pricing, $fields, array( 'kg' => '2', 'speed' => 'Thường' ) );
t( 36000.0 === $q['total'], 'unticked delivery charges nothing' );
$q = bst_quote_compute( $pricing, $fields, array( 'kg' => '', 'speed' => 'Thường' ) );
t( 'incomplete' === $q['status'] && 0 === $q['total'], 'missing kg is incomplete, not 0' );
$hid           = $fields;
$hid[1]['show'] = array( 'rules' => array( array( 'field' => 'f_speed', 'op' => 'is', 'value' => 'Nhanh' ) ) );
$q             = bst_quote_compute( $pricing, $hid, array( 'kg' => '1', 'ship' => 'Giao nhận', 'speed' => 'Thường' ) );
t( 18000.0 === $q['total'], 'hidden field charges nothing even if sent' );

$q = bst_quote_compute( $pricing, $hid, array( 'kg' => '0', 'speed' => 'Thường' ) );
t( 'incomplete' === $q['status'], 'nothing chargeable is not a free total' );

// Case 5: tampered / bad input and formulas.
$q = bst_quote_compute( $pricing, $fields, array( 'kg' => '-3' ) );
t( 'error' === $q['status'], 'negative quantity needs confirmation' );
$q = bst_quote_compute( $pricing, $fields, array( 'kg' => '99999999999' ) );
t( 'error' === $q['status'], 'too large needs confirmation' );
$q = bst_quote_compute( $pricing, $fields, array( 'kg' => '3', 'ship' => 'Free ship = 0', 'speed' => 'Thường' ) );
t( 54000.0 === $q['total'], 'unknown option adds nothing' );
$div = array( 'enabled' => true, 'lines' => array( array( 'id' => 'l_a', 'label' => 'A', 'expr' => '100 / ({f_kg} - 3)' ) ) );
t( 'error' === bst_quote_compute( $div, $fields, array( 'kg' => '3' ) )['status'], 'division by zero needs confirmation' );
foreach ( array( 'system("x")', '{f_kg} ; 1', '`ls`', '1 +', 'max()', 'if(1,2)', '$x', '{f_kg}.constructor' ) as $bad ) {
	$p = array( 'enabled' => true, 'lines' => array( array( 'id' => 'l_a', 'label' => 'A', 'expr' => $bad ) ) );
	t( 'syntax' === ( bst_quote_validate( $p, $fields )[0]['code'] ?? '' ) && 'error' === bst_quote_compute( $p, $fields, array( 'kg' => '1' ) )['status'], 'rejects ' . $bad );
}
$loop = array( 'enabled' => true, 'lines' => array( array( 'id' => 'l_a', 'label' => 'A', 'expr' => '{l_b} + 1' ), array( 'id' => 'l_b', 'label' => 'B', 'expr' => '{l_a}' ) ) );
t( 2 === count( bst_quote_validate( $loop, $fields ) ), 'line loop reported' );
$self = array( 'enabled' => true, 'lines' => array( array( 'id' => 'l_a', 'label' => 'A', 'expr' => '{l_a} + 1' ) ) );
t( 'cycle' === bst_quote_validate( $self, $fields )[0]['code'], 'line self reference reported' );
$miss = array( 'enabled' => true, 'lines' => array( array( 'id' => 'l_a', 'label' => 'A', 'expr' => '{f_gone} + {f_speed=Bay}' ) ) );
t( array( 'missing_ref', 'bad_option' ) === array_column( bst_quote_validate( $miss, $fields ), 'code' ), 'deleted field and unknown option reported' );
$neg = array( 'enabled' => true, 'lines' => array( array( 'id' => 'l_a', 'label' => 'A', 'expr' => '0 - 5' ) ) );
t( 'error' === bst_quote_compute( $neg, $fields, array() )['status'], 'negative total needs confirmation' );

// Functions, comparisons, minimum charge.
$fn = array( 'enabled' => true, 'lines' => array( array( 'id' => 'l_a', 'label' => 'A', 'expr' => 'max({f_kg} * 15000, 300000) + if({f_kg} >= 10, -min(50000, 20000), 0) + round(1234.5, -2) + ceil(1.2) + floor(1.8)' ) ) );
t( 300000 + 1200 + 2 + 1 === (int) bst_quote_compute( $fn, $fields, array( 'kg' => '5' ) )['total'], 'max/min/if/round/ceil/floor' );
t( 300000 - 20000 + 1200 + 3 === (int) bst_quote_compute( $fn, $fields, array( 'kg' => '10' ) )['total'], 'if branch' );

// Revision changes with any price and is stable otherwise; off by default.
$r1 = bst_quote_revision( $pricing, $fields );
$f2 = $fields;
$f2[1]['prices'] = 'Giao nhận = 30000';
t( $r1 === bst_quote_revision( $pricing, $fields ) && $r1 !== bst_quote_revision( $pricing, $f2 ), 'revision follows prices' );
t( 'off' === bst_quote_compute( array(), $fields, array() )['status'] && '' === bst_quote_text( bst_quote_compute( array(), $fields, array() ) ), 'old forms have no quote' );

exit( $fail ? 1 : 0 );
