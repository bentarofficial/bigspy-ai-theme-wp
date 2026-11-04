<?php
/**
 * Industry presets for the built-in Form (G-69): fields + price rules built
 * only from the G-58 line kinds, with demo prices. One source for the editor
 * (Pricing tab) and the AI Designer (form_config mode "preset").
 *
 * Every price line label and the note say "demo"; nothing here is a real price.
 * Each preset carries samples with the expected total, checked by
 * tools/tests/form-presets-check.php against inc/form-pricing.php.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * All presets: key => { title, trade, fields, pricing, samples }.
 * Sample values are keyed by field name (as the visitor sends them); total is in currency units.
 *
 * @return array
 */
function bst_form_presets() {
	$demo = ' (demo)';
	$note = __( 'Demo prices: replace them before publishing.', 'bigspy-ai-theme-wp' );
	$name = array( 'id' => 'f_name', 'type' => 'text', 'name' => 'your-name', 'label' => __( 'Name', 'bigspy-ai-theme-wp' ), 'required' => true, 'width' => 'half' );
	$tel  = array( 'id' => 'f_phone', 'type' => 'tel', 'name' => 'your-phone', 'label' => __( 'Phone', 'bigspy-ai-theme-wp' ), 'required' => true, 'width' => 'half' );
	$on   = function ( $field ) {
		return array( 'logic' => 'all', 'rules' => array( array( 'field' => $field, 'op' => 'filled', 'value' => '' ) ) );
	};
	$is   = function ( $field, $value ) {
		return array( 'logic' => 'all', 'rules' => array( array( 'field' => $field, 'op' => 'is', 'value' => $value ) ) );
	};
	$money = function ( $lines, $extra = array() ) use ( $note ) {
		return array_merge( array( 'enabled' => true, 'currency' => 'VND', 'decimals' => 0, 'note' => $note, 'lines' => $lines ), $extra );
	};

	$pickup  = __( 'Pickup and delivery', 'bigspy-ai-theme-wp' );
	$express = __( 'Express', 'bigspy-ai-theme-wp' );
	$basic   = __( 'Basic', 'bigspy-ai-theme-wp' );
	$deep    = __( 'Deep clean', 'bigspy-ai-theme-wp' );
	$t1      = __( '1-ton truck', 'bigspy-ai-theme-wp' );
	$t2      = __( '2-ton truck', 'bigspy-ai-theme-wp' );
	$packing = __( 'Packing service', 'bigspy-ai-theme-wp' );
	$sides   = __( 'Two-sided', 'bigspy-ai-theme-wp' );
	$fabric  = __( 'Fabric', 'bigspy-ai-theme-wp' );
	$black   = __( 'Blackout', 'bigspy-ai-theme-wp' );
	$install = __( 'Installation', 'bigspy-ai-theme-wp' );
	$wday    = __( 'Weekday', 'bigspy-ai-theme-wp' );
	$wend    = __( 'Weekend', 'bigspy-ai-theme-wp' );
	$massage = __( 'Body massage 60 min', 'bigspy-ai-theme-wp' );
	$facial  = __( 'Facial care', 'bigspy-ai-theme-wp' );

	return array(
		'laundry'     => array(
			'title'   => __( 'Laundry', 'bigspy-ai-theme-wp' ),
			'trade'   => __( 'Laundry by weight with pickup and express', 'bigspy-ai-theme-wp' ),
			'fields'  => array(
				$name,
				$tel,
				array( 'id' => 'f_kg', 'type' => 'number', 'name' => 'kg', 'label' => __( 'Weight (kg)', 'bigspy-ai-theme-wp' ), 'required' => true, 'width' => 'half' ),
				array( 'id' => 'f_ship', 'type' => 'checkbox', 'name' => 'pickup', 'label' => $pickup, 'options' => $pickup, 'width' => 'half' ),
				array( 'id' => 'f_fast', 'type' => 'checkbox', 'name' => 'express', 'label' => $express, 'options' => $express, 'width' => 'full' ),
				array( 'id' => 'f_addr', 'type' => 'text', 'name' => 'address', 'label' => __( 'Address', 'bigspy-ai-theme-wp' ), 'required' => true, 'width' => 'full', 'when' => $on( 'f_ship' ) ),
			),
			'pricing' => $money(
				array(
					array( 'id' => 'l_wash', 'kind' => 'qty', 'label' => __( 'Washing', 'bigspy-ai-theme-wp' ) . $demo, 'field' => 'f_kg', 'unit' => 18000 ),
					array( 'id' => 'l_ship', 'kind' => 'fixed', 'label' => $pickup . $demo, 'amount' => 25000, 'when' => $on( 'f_ship' ) ),
					array( 'id' => 'l_fast', 'kind' => 'percent', 'label' => $express . ' +20%' . $demo, 'pct' => 20, 'targets' => array( 'l_wash' ), 'when' => $on( 'f_fast' ) ),
				)
			),
			'samples' => array(
				array( 'values' => array( 'kg' => '3' ), 'total' => 54000 ),
				array( 'values' => array( 'kg' => '3', 'pickup' => $pickup, 'express' => $express, 'address' => 'x' ), 'total' => 89800 ),
			),
		),
		'cleaning'    => array(
			'title'   => __( 'Cleaning', 'bigspy-ai-theme-wp' ),
			'trade'   => __( 'Cleaning priced per m² by package, with a minimum order', 'bigspy-ai-theme-wp' ),
			'fields'  => array(
				$name,
				$tel,
				array( 'id' => 'f_pkg', 'type' => 'select', 'name' => 'package', 'label' => __( 'Package', 'bigspy-ai-theme-wp' ), 'placeholder' => __( 'Choose a package', 'bigspy-ai-theme-wp' ), 'options' => $basic . "\n" . $deep, 'required' => true, 'width' => 'half' ),
				array( 'id' => 'f_m2', 'type' => 'number', 'name' => 'area', 'label' => __( 'Area (m²)', 'bigspy-ai-theme-wp' ), 'required' => true, 'width' => 'half' ),
				array( 'id' => 'f_date', 'type' => 'date', 'name' => 'date', 'label' => __( 'Preferred date', 'bigspy-ai-theme-wp' ), 'width' => 'full' ),
			),
			'pricing' => $money(
				array(
					array( 'id' => 'l_pkg', 'kind' => 'option', 'label' => __( 'Package per m²', 'bigspy-ai-theme-wp' ) . $demo, 'field' => 'f_pkg', 'qtyField' => 'f_m2', 'prices' => array( $basic => 12000, $deep => 20000 ) ),
				),
				array( 'minTotal' => 300000, 'roundStep' => 1000 )
			),
			'samples' => array(
				array( 'values' => array( 'package' => $basic, 'area' => '20' ), 'total' => 300000 ),
				array( 'values' => array( 'package' => $deep, 'area' => '50' ), 'total' => 1000000 ),
			),
		),
		'moving'      => array(
			'title'   => __( 'House moving', 'bigspy-ai-theme-wp' ),
			'trade'   => __( 'Moving: truck type, distance, floors carried by stairs, packing', 'bigspy-ai-theme-wp' ),
			'fields'  => array(
				$name,
				$tel,
				array( 'id' => 'f_truck', 'type' => 'select', 'name' => 'truck', 'label' => __( 'Truck', 'bigspy-ai-theme-wp' ), 'placeholder' => __( 'Choose a truck', 'bigspy-ai-theme-wp' ), 'options' => $t1 . "\n" . $t2, 'required' => true, 'width' => 'half' ),
				array( 'id' => 'f_km', 'type' => 'number', 'name' => 'km', 'label' => __( 'Distance (km)', 'bigspy-ai-theme-wp' ), 'required' => true, 'width' => 'half' ),
				array( 'id' => 'f_floors', 'type' => 'number', 'name' => 'floors', 'label' => __( 'Floors without a lift', 'bigspy-ai-theme-wp' ), 'default' => '0', 'width' => 'half' ),
				array( 'id' => 'f_pack', 'type' => 'checkbox', 'name' => 'packing', 'label' => $packing, 'options' => $packing, 'width' => 'half' ),
			),
			'pricing' => $money(
				array(
					array( 'id' => 'l_truck', 'kind' => 'option', 'label' => __( 'Truck', 'bigspy-ai-theme-wp' ) . $demo, 'field' => 'f_truck', 'prices' => array( $t1 => 600000, $t2 => 900000 ) ),
					array( 'id' => 'l_km', 'kind' => 'qty', 'label' => __( 'Distance', 'bigspy-ai-theme-wp' ) . $demo, 'field' => 'f_km', 'unit' => 15000 ),
					array( 'id' => 'l_floor', 'kind' => 'qty', 'label' => __( 'Carrying by stairs', 'bigspy-ai-theme-wp' ) . $demo, 'field' => 'f_floors', 'unit' => 50000 ),
					array( 'id' => 'l_pack', 'kind' => 'fixed', 'label' => $packing . $demo, 'amount' => 300000, 'when' => $on( 'f_pack' ) ),
				)
			),
			'samples' => array(
				array( 'values' => array( 'truck' => $t2, 'km' => '10', 'floors' => '3' ), 'total' => 1200000 ),
				array( 'values' => array( 'truck' => $t1, 'km' => '4', 'floors' => '0', 'packing' => $packing ), 'total' => 960000 ),
			),
		),
		'printing'    => array(
			'title'   => __( 'Printing', 'bigspy-ai-theme-wp' ),
			'trade'   => __( 'Business cards by quantity tiers, two-sided surcharge', 'bigspy-ai-theme-wp' ),
			'fields'  => array(
				$name,
				$tel,
				array( 'id' => 'f_boxes', 'type' => 'number', 'name' => 'boxes', 'label' => __( 'Boxes', 'bigspy-ai-theme-wp' ), 'required' => true, 'width' => 'half' ),
				array( 'id' => 'f_sides', 'type' => 'checkbox', 'name' => 'two-sided', 'label' => $sides, 'options' => $sides, 'width' => 'half' ),
			),
			'pricing' => $money(
				array(
					array( 'id' => 'l_print', 'kind' => 'tier', 'label' => __( 'Printing', 'bigspy-ai-theme-wp' ) . $demo, 'field' => 'f_boxes', 'mode' => 'all', 'tiers' => array( array( 'from' => 1, 'unit' => 60000 ), array( 'from' => 5, 'unit' => 50000 ), array( 'from' => 20, 'unit' => 42000 ) ) ),
					array( 'id' => 'l_sides', 'kind' => 'percent', 'label' => $sides . ' +30%' . $demo, 'pct' => 30, 'targets' => array( 'l_print' ), 'when' => $on( 'f_sides' ) ),
				)
			),
			'samples' => array(
				array( 'values' => array( 'boxes' => '3' ), 'total' => 180000 ),
				array( 'values' => array( 'boxes' => '6' ), 'total' => 300000 ),
				array( 'values' => array( 'boxes' => '20', 'two-sided' => $sides ), 'total' => 1092000 ),
			),
		),
		'curtains'    => array(
			'title'   => __( 'Curtains', 'bigspy-ai-theme-wp' ),
			'trade'   => __( 'Curtains by width × height and material, minimum area, installation', 'bigspy-ai-theme-wp' ),
			'fields'  => array(
				$name,
				$tel,
				array( 'id' => 'f_mat', 'type' => 'select', 'name' => 'material', 'label' => __( 'Material', 'bigspy-ai-theme-wp' ), 'placeholder' => __( 'Choose a material', 'bigspy-ai-theme-wp' ), 'options' => $fabric . "\n" . $black, 'required' => true, 'width' => 'full' ),
				array( 'id' => 'f_w', 'type' => 'number', 'name' => 'width', 'label' => __( 'Width (m)', 'bigspy-ai-theme-wp' ), 'required' => true, 'width' => 'half' ),
				array( 'id' => 'f_h', 'type' => 'number', 'name' => 'height', 'label' => __( 'Height (m)', 'bigspy-ai-theme-wp' ), 'required' => true, 'width' => 'half' ),
				array( 'id' => 'f_inst', 'type' => 'checkbox', 'name' => 'installation', 'label' => $install, 'options' => $install, 'width' => 'full' ),
			),
			'pricing' => $money(
				array(
					array( 'id' => 'l_fabric', 'kind' => 'area', 'label' => $fabric . ' / m²' . $demo, 'field' => 'f_w', 'field2' => 'f_h', 'unit' => 250000, 'minQty' => 1.5, 'when' => $is( 'f_mat', $fabric ) ),
					array( 'id' => 'l_black', 'kind' => 'area', 'label' => $black . ' / m²' . $demo, 'field' => 'f_w', 'field2' => 'f_h', 'unit' => 350000, 'minQty' => 1.5, 'when' => $is( 'f_mat', $black ) ),
					array( 'id' => 'l_inst', 'kind' => 'fixed', 'label' => $install . $demo, 'amount' => 100000, 'when' => $on( 'f_inst' ) ),
				)
			),
			'samples' => array(
				array( 'values' => array( 'material' => $fabric, 'width' => '2', 'height' => '2.5' ), 'total' => 1250000 ),
				array( 'values' => array( 'material' => $black, 'width' => '1', 'height' => '1', 'installation' => $install ), 'total' => 625000 ),
			),
		),
		'lodging'     => array(
			'title'   => __( 'Homestay / hotel', 'bigspy-ai-theme-wp' ),
			'trade'   => __( 'Rooms: weekday or weekend rate × nights, extra guests per night', 'bigspy-ai-theme-wp' ),
			'fields'  => array(
				$name,
				$tel,
				array( 'id' => 'f_in', 'type' => 'date', 'name' => 'check-in', 'label' => __( 'Check-in date', 'bigspy-ai-theme-wp' ), 'required' => true, 'width' => 'half' ),
				array( 'id' => 'f_day', 'type' => 'radio', 'name' => 'day-type', 'label' => __( 'Days', 'bigspy-ai-theme-wp' ), 'options' => $wday . "\n" . $wend, 'width' => 'half' ),
				array( 'id' => 'f_nights', 'type' => 'number', 'name' => 'nights', 'label' => __( 'Nights', 'bigspy-ai-theme-wp' ), 'required' => true, 'width' => 'half' ),
				array( 'id' => 'f_extra', 'type' => 'number', 'name' => 'extra-guests', 'label' => __( 'Extra guests', 'bigspy-ai-theme-wp' ), 'default' => '0', 'width' => 'half' ),
			),
			'pricing' => $money(
				array(
					array( 'id' => 'l_room', 'kind' => 'option', 'label' => __( 'Room per night', 'bigspy-ai-theme-wp' ) . $demo, 'field' => 'f_day', 'qtyField' => 'f_nights', 'prices' => array( $wday => 800000, $wend => 1000000 ) ),
					array( 'id' => 'l_extra', 'kind' => 'formula', 'label' => __( 'Extra guests per night', 'bigspy-ai-theme-wp' ) . $demo, 'expr' => '{f_extra} * {f_nights} * 150000' ),
				)
			),
			'samples' => array(
				array( 'values' => array( 'day-type' => $wend, 'nights' => '2', 'extra-guests' => '0' ), 'total' => 2000000 ),
				array( 'values' => array( 'day-type' => $wday, 'nights' => '3', 'extra-guests' => '1' ), 'total' => 2850000 ),
			),
		),
		'spa'         => array(
			'title'   => __( 'Spa session packages', 'bigspy-ai-theme-wp' ),
			'trade'   => __( 'Spa: price per session by service, discount for 5 and 10 sessions', 'bigspy-ai-theme-wp' ),
			'fields'  => array(
				$name,
				$tel,
				array( 'id' => 'f_service', 'type' => 'select', 'name' => 'service', 'label' => __( 'Service', 'bigspy-ai-theme-wp' ), 'placeholder' => __( 'Choose a service', 'bigspy-ai-theme-wp' ), 'options' => $massage . "\n" . $facial, 'required' => true, 'width' => 'half' ),
				array( 'id' => 'f_sessions', 'type' => 'number', 'name' => 'sessions', 'label' => __( 'Sessions', 'bigspy-ai-theme-wp' ), 'default' => '1', 'required' => true, 'width' => 'half' ),
				array( 'id' => 'f_date', 'type' => 'date', 'name' => 'date', 'label' => __( 'First visit', 'bigspy-ai-theme-wp' ), 'width' => 'full' ),
			),
			'pricing' => $money(
				array(
					array( 'id' => 'l_service', 'kind' => 'option', 'label' => __( 'Session', 'bigspy-ai-theme-wp' ) . $demo, 'field' => 'f_service', 'qtyField' => 'f_sessions', 'prices' => array( $massage => 350000, $facial => 450000 ) ),
					array(
						'id'      => 'l_pack5',
						'kind'    => 'percent',
						'label'   => __( '5–9 sessions −10%', 'bigspy-ai-theme-wp' ) . $demo,
						'pct'     => -10,
						'targets' => array( 'l_service' ),
						'when'    => array( 'logic' => 'all', 'rules' => array( array( 'field' => 'f_sessions', 'op' => 'gt', 'value' => '4' ), array( 'field' => 'f_sessions', 'op' => 'lt', 'value' => '10' ) ) ),
					),
					array(
						'id'      => 'l_pack10',
						'kind'    => 'percent',
						'label'   => __( '10+ sessions −20%', 'bigspy-ai-theme-wp' ) . $demo,
						'pct'     => -20,
						'targets' => array( 'l_service' ),
						'when'    => array( 'logic' => 'all', 'rules' => array( array( 'field' => 'f_sessions', 'op' => 'gt', 'value' => '9' ) ) ),
					),
				)
			),
			'samples' => array(
				array( 'values' => array( 'service' => $massage, 'sessions' => '1' ), 'total' => 350000 ),
				array( 'values' => array( 'service' => $massage, 'sessions' => '5' ), 'total' => 1575000 ),
				array( 'values' => array( 'service' => $facial, 'sessions' => '10' ), 'total' => 3600000 ),
			),
		),
		'installment' => array(
			'title'   => __( 'Installments', 'bigspy-ai-theme-wp' ),
			'trade'   => __( 'Monthly installment estimate at a flat monthly rate', 'bigspy-ai-theme-wp' ),
			'fields'  => array(
				$name,
				$tel,
				array( 'id' => 'f_amount', 'type' => 'number', 'name' => 'amount', 'label' => __( 'Amount to borrow (VND)', 'bigspy-ai-theme-wp' ), 'required' => true, 'width' => 'half' ),
				array( 'id' => 'f_months', 'type' => 'select', 'name' => 'months', 'label' => __( 'Months', 'bigspy-ai-theme-wp' ), 'options' => "6\n12\n24", 'required' => true, 'width' => 'half' ),
			),
			'pricing' => $money(
				array(
					array( 'id' => 'l_month', 'kind' => 'formula', 'label' => __( 'Monthly payment, flat rate 1.5%/month', 'bigspy-ai-theme-wp' ) . $demo, 'expr' => '{f_amount} / {f_months} + {f_amount} * 0.015' ),
				),
				array( 'roundStep' => 1000, 'note' => $note . ' ' . __( 'Estimate per month at a flat rate on the amount borrowed; the lender confirms the real schedule.', 'bigspy-ai-theme-wp' ) )
			),
			'samples' => array(
				array( 'values' => array( 'amount' => '12000000', 'months' => '12' ), 'total' => 1180000 ),
				array( 'values' => array( 'amount' => '10000000', 'months' => '6' ), 'total' => 1817000 ),
			),
		),
	);
}

/** One preset, or null. */
function bst_form_preset( $key ) {
	$all = bst_form_presets();
	return $all[ sanitize_key( (string) $key ) ] ?? null;
}

/** Editor data: the Pricing tab offers every preset (fields + pricing only). */
function bst_form_presets_editor_data() {
	$out = array();
	foreach ( bst_form_presets() as $key => $p ) {
		$out[] = array( 'key' => $key, 'title' => $p['title'], 'trade' => $p['trade'], 'fields' => $p['fields'], 'pricing' => $p['pricing'] );
	}
	return $out;
}

add_action(
	'enqueue_block_editor_assets',
	function () {
		if ( wp_script_is( 'bst-blocks-editor', 'registered' ) ) {
			wp_add_inline_script( 'bst-blocks-editor', 'window.bstFormPresets=' . wp_json_encode( bst_form_presets_editor_data() ) . ';', 'before' );
		}
	}
);
