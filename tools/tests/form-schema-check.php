<?php
/**
 * G-57 form schema checks. Runs without WordPress: php tools/tests/form-schema-check.php
 */
define( 'ABSPATH', __DIR__ );
function sanitize_key( $k ) {
	return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $k ) );
}
require dirname( __DIR__, 2 ) . '/bigspy-ai-theme-wp/inc/form-schema.php';

$fail = 0;
function t( $ok, $label ) {
	global $fail;
	echo ( $ok ? 'ok   ' : 'FAIL ' ) . $label . "\n";
	$fail += $ok ? 0 : 1;
}
function codes( $errors ) {
	return array_map( function ( $e ) { return $e['code'] . ':' . $e['field']; }, $errors );
}

// Legacy form (1.80 style): no IDs, no version.
$legacy = array(
	array( 'type' => 'text', 'name' => 'your-name', 'label' => 'Họ và tên', 'required' => true ),
	array( 'type' => 'select', 'name' => 'service', 'label' => 'Dịch vụ', 'options' => "Giặt\nỦi" ),
	array( 'type' => 'number', 'name' => 'kg', 'label' => 'Số kg' ),
	array( 'label' => 'no name' ),
);
$n1 = bst_form_schema_normalize( $legacy );
$n2 = bst_form_schema_normalize( $n1 );
t( $n1 === $n2, 'migration repeated gives the same data' );
t( 3 === count( $n1 ) && 'n_your_name' === $n1[0]['id'], 'legacy IDs derived from the name; nameless field dropped' );
t( 'your-name' === $n1[0]['name'] && 'Họ và tên' === $n1[0]['label'] && true === $n1[0]['required'], 'name, label and other keys kept' );
t( array() === bst_form_schema_check( $n1 ), 'legacy schema is valid' );

// Conditions by ID survive rename, mail-tag rename and reorder.
$f = $n1;
$f[2]['conditions'] = array( 'action' => 'show', 'match' => 'all', 'rules' => array( array( 'field' => 'n_service', 'op' => 'is', 'value' => 'Giặt' ) ) );
$f = bst_form_schema_normalize( $f );
$vis = function ( $fields, $vals ) { return bst_form_schema_visibility( $fields, $vals ); };
t( true === $vis( $f, array( 'service' => 'Giặt' ) )['n_kg'], 'show when rule matches' );
t( false === $vis( $f, array( 'service' => 'Ủi' ) )['n_kg'], 'hidden when rule fails' );
$r = $f;
$r[1]['label'] = 'Gói dịch vụ';
$r[1]['name']  = 'goi';
$r             = array( $r[2], $r[0], $r[1] );
$r             = bst_form_schema_normalize( $r );
t( 'n_service' === $r[2]['id'], 'ID stays after label/name change and reorder' );
t( true === $vis( $r, array( 'goi' => 'Giặt' ) )['n_kg'] && array() === bst_form_schema_check( $r ), 'condition still works after rename/reorder' );

// Hide action, any/all, chained hidden fields.
$c = bst_form_schema_normalize( array(
	array( 'type' => 'checkbox', 'name' => 'extras', 'id' => 'f_extras', 'options' => "Giao nhận\nNhanh" ),
	array( 'type' => 'text', 'name' => 'address', 'id' => 'f_addr', 'conditions' => array( 'rules' => array( array( 'field' => 'f_extras', 'op' => 'is', 'value' => 'Giao nhận' ) ) ) ),
	array( 'type' => 'text', 'name' => 'floor', 'id' => 'f_floor', 'conditions' => array( 'rules' => array( array( 'field' => 'f_addr', 'op' => 'not_empty' ) ) ) ),
	array( 'type' => 'number', 'name' => 'qty', 'id' => 'f_qty' ),
	array( 'type' => 'text', 'name' => 'bulk', 'id' => 'f_bulk', 'conditions' => array( 'action' => 'hide', 'match' => 'any', 'rules' => array( array( 'field' => 'f_qty', 'op' => 'lt', 'value' => '5' ), array( 'field' => 'f_qty', 'op' => 'empty' ) ) ) ),
) );
$v = $vis( $c, array( 'extras' => 'Nhanh, Giao nhận', 'address' => '1 Lê Lợi', 'qty' => '10' ) );
t( $v['f_addr'] && $v['f_floor'] && $v['f_bulk'], 'checkbox multi-value, not_empty and hide/any rules' );
$v = $vis( $c, array( 'extras' => 'Nhanh', 'address' => '1 Lê Lợi', 'qty' => '2' ) );
t( ! $v['f_addr'] && ! $v['f_floor'] && ! $v['f_bulk'], 'hidden field counts as empty for its dependants' );

// Dependency validation.
$bad = $c;
$bad[1]['conditions']['rules'][0]['field'] = 'f_floor'; // addr <-> floor loop.
$e = codes( bst_form_schema_check( $bad ) );
t( in_array( 'cycle:f_addr', $e, true ) && in_array( 'cycle:f_floor', $e, true ) && ! in_array( 'cycle:f_qty', $e, true ), 'cycle reported on the looping fields only' );
t( true === $vis( $bad, array() )['f_addr'], 'broken schema shows fields instead of hiding them' );
$bad = $c;
array_splice( $bad, 0, 1 ); // Delete a field that is in use.
t( array( 'missing_ref:f_addr' ) === codes( bst_form_schema_check( $bad ) ), 'deleting a referenced field is reported where it is used' );
t( array( 'f_addr' ) === bst_form_schema_references( $c, 'f_extras' ), 'references list for the delete guard' );
$bad = $c;
$bad[3]['conditions'] = array( 'rules' => array( array( 'field' => 'f_qty', 'op' => 'gt', 'value' => '1' ) ) );
$bad = bst_form_schema_normalize( $bad );
t( in_array( 'self_ref:f_qty', codes( bst_form_schema_check( $bad ) ), true ), 'self reference reported' );
$dup = $c;
$dup[3]['name'] = 'bulk';
t( in_array( 'duplicate_name:f_bulk', codes( bst_form_schema_check( $dup ) ), true ), 'duplicate mail-tag name reported' );
$dup = bst_form_schema_normalize( array( array( 'name' => 'a', 'id' => 'f_x' ), array( 'name' => 'b', 'id' => 'f_x' ) ) );
t( 'f_x' === $dup[0]['id'] && 'f_x_2' === $dup[1]['id'], 'duplicate IDs renamed on normalize' );

// Bad input is cleaned.
$x = bst_form_schema_normalize( array( array( 'name' => 'a', 'id' => '<script>', 'conditions' => array( 'rules' => array( array( 'field' => 'n_b', 'op' => 'eval' ) ) ) ), array( 'name' => 'b', 'conditions' => 'x' ) ) );
t( 'n_a' === $x[0]['id'] && 'is' === $x[0]['conditions']['rules'][0]['op'] && ! isset( $x[1]['conditions'] ), 'invalid ID/operator/conditions cleaned' );
t( false === bst_form_rule_match( array( 'op' => 'gt', 'value' => '1' ), 'abc' ), 'gt on non-number is false' );

echo $fail ? "\n$fail failed\n" : "\nall passed\n";
exit( $fail ? 1 : 0 );
