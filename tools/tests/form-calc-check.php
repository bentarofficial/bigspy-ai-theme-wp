<?php
/**
 * G-58 formula checks: php tools/tests/form-calc-check.php (no WordPress needed).
 * The same fixtures run against the browser twin: node tools/tests/form-calc-check.cjs
 */
define( 'ABSPATH', __DIR__ );
function sanitize_key( $k ) {
	return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $k ) );
}
function wp_list_pluck( $l, $k ) {
	return array_column( $l, $k );
}
require dirname( __DIR__, 2 ) . '/bigspy-ai-theme-wp/inc/form-schema.php';
require dirname( __DIR__, 2 ) . '/bigspy-ai-theme-wp/inc/form-calc.php';
$fail = 0;
foreach ( json_decode( file_get_contents( __DIR__ . '/form-calc-fixtures.json' ), true ) as $c ) {
	$fields = bst_form_schema_normalize( $c['fields'] );
	$seen   = $c['seen'] ?? array_fill_keys( array_column( $fields, 'id' ), true );
	$q      = bst_calc_quote( $fields, $c['values'], $seen, $c['cur'] );
	$ok     = true;
	foreach ( $c['expect'] as $id => $want ) {
		$got = $q['lines'][ $id ] ?? null;
		if ( ! $got || $got[0] !== $want[0] || abs( $got[1] - $want[1] ) > 1e-9 ) {
			$ok = false;
			echo '  ' . $id . ' got ' . json_encode( $got ) . ' want ' . json_encode( $want ) . "\n";
		}
	}
	echo ( $ok ? 'ok   ' : 'FAIL ' ) . $c['name'] . "\n";
	$fail += $ok ? 0 : 1;
}
// Schema: formulas are dependencies (delete guard, loops, missing fields).
$f = bst_form_schema_normalize( array(
	array( 'id' => 'f_a', 'type' => 'calc', 'name' => 'a', 'formula' => '{f_b} + 1' ),
	array( 'id' => 'f_b', 'type' => 'calc', 'name' => 'b', 'formula' => '{f_a} * 2' ),
	array( 'id' => 'f_c', 'type' => 'number', 'name' => 'c' ),
	array( 'id' => 'f_d', 'type' => 'calc', 'name' => 'd', 'formula' => '{f_c} + {f_gone}' ),
) );
$codes = array_map( function ( $e ) { return $e['code'] . ':' . $e['field']; }, bst_form_schema_check( $f ) );
$t     = array(
	'formula loop reported'           => in_array( 'cycle:f_a', $codes, true ) && in_array( 'cycle:f_b', $codes, true ),
	'formula missing field reported'  => in_array( 'missing_ref:f_d', $codes, true ),
	'field used by formula is guarded' => array( 'f_d' ) === bst_form_schema_references( $f, 'f_c' ),
	'formula errors listed'           => array( 'f_d' => 'missing_ref' ) === bst_calc_check( array_slice( $f, 2 ) ) && 'unknown_function' === ( bst_calc_check( array( array( 'id' => 'f_x', 'type' => 'calc', 'formula' => 'exec(1)' ) ) )['f_x'] ?? '' ),
	'money text VND/USD'              => '1.234.567 ₫' === bst_calc_money( 1234567, array( 'code' => 'VND', 'decimals' => 0 ) ) && '1,234.50 USD' === bst_calc_money( 1234.5, array( 'code' => 'USD', 'decimals' => 2 ) ),
	'normalize keeps formula/prices'  => bst_form_schema_normalize( $f ) === $f,
);
foreach ( $t as $label => $ok ) {
	echo ( $ok ? 'ok   ' : 'FAIL ' ) . $label . "\n";
	$fail += $ok ? 0 : 1;
}
echo $fail ? "\n$fail failed\n" : "\nall passed\n";
exit( $fail ? 1 : 0 );
