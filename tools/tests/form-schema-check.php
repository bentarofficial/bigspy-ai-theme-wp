<?php
/**
 * G-57 form schema cases, no WordPress needed: php tools/tests/form-schema-check.php
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
function codes( $f ) {
	return array_column( bst_form_schema_validate( $f ), 'code' );
}

// Old form without IDs or conditions.
$old = array(
	array( 'type' => 'text', 'name' => 'your-name', 'label' => 'Name', 'required' => true ),
	array( 'type' => 'select', 'name' => 'service', 'label' => 'Service', 'options' => "Wash\nIron" ),
	array( 'type' => 'number', 'name' => 'kg', 'label' => 'Kg' ),
);
$n1 = bst_form_schema_normalize( $old );
t( 'f_your-name' === $n1[0]['id'] && 'f_kg' === $n1[2]['id'], 'old fields get IDs from their names' );
t( bst_form_schema_normalize( $n1 ) === $n1, 'migration repeated changes nothing' );
t( array_map( function ( $f ) { unset( $f['id'] ); return $f; }, $n1 ) === $old, 'names, labels and options of old fields kept' );
t( array() === codes( $n1 ), 'old form has no problems' );
$st = bst_form_schema_state( $n1, array( 'your-name' => '', 'service' => '', 'kg' => '' ) );
t( $st['f_your-name']['required'] && ! $st['f_kg']['required'] && $st['f_kg']['visible'], 'old required flags unchanged' );

// Conditions by ID survive rename and reorder.
$f       = $n1;
$f[2]['show'] = array( 'rules' => array( array( 'field' => 'f_service', 'op' => 'is', 'value' => 'Wash' ) ) );
$f[0]['requiredWhen'] = array( 'rules' => array( array( 'field' => 'f_kg', 'op' => 'gt', 'value' => '5' ) ) );
$f[0]['required']     = false;
$hidden = bst_form_schema_state( $f, array( 'service' => 'Iron', 'kg' => '9' ) );
$shown  = bst_form_schema_state( $f, array( 'service' => 'Wash', 'kg' => '9' ) );
t( ! $hidden['f_kg']['visible'] && $shown['f_kg']['visible'], 'show rule hides and shows' );
t( ! $hidden['f_your-name']['required'], 'rule on a hidden field reads empty' );
t( $shown['f_your-name']['required'], 'required when rule holds' );
$r                  = $f;
$r[1]['name']       = 'dich-vu';
$r[1]['label']      = 'Dịch vụ';
$r                  = array_reverse( $r );
$st                 = bst_form_schema_state( $r, array( 'dich-vu' => 'Wash', 'kg' => '9' ) );
t( $st['f_kg']['visible'] && $st['f_your-name']['required'] && array() === codes( $r ), 'rename + reorder keep rules' );
t( array( 'f_kg' ) === bst_form_field_usages( $f, 'f_service' ), 'usages list who reads a field' );

// Delete, self reference, loop, bad op, duplicate name.
$d = array_values( array_filter( $f, function ( $x ) { return 'f_service' !== $x['id']; } ) );
t( array( 'missing_ref' ) === codes( $d ), 'deleting a used field is reported' );
$st = bst_form_schema_state( $d, array( 'kg' => '' ) );
t( $st['f_kg']['visible'], 'broken rule keeps field visible on the site' );
$s = $n1;
$s[2]['show'] = array( 'rules' => array( array( 'field' => 'f_kg', 'op' => 'not_empty' ) ) );
t( in_array( 'self_ref', codes( $s ), true ), 'self reference reported' );
$c = $n1;
$c[1]['show'] = array( 'rules' => array( array( 'field' => 'f_kg', 'op' => 'not_empty' ) ) );
$c[2]['show'] = array( 'rules' => array( array( 'field' => 'f_service', 'op' => 'not_empty' ) ) );
$cc = bst_form_schema_validate( $c );
t( 2 === count( array_filter( $cc, function ( $e ) { return 'cycle' === $e['code']; } ) ), 'loop reported on both fields' );
$st = bst_form_schema_state( $c, array() );
t( $st['f_kg']['visible'] && $st['f_service']['visible'], 'loop does not hang and stays visible' );
$b = $n1;
$b[2]['show'] = array( 'rules' => array( array( 'field' => 'f_service', 'op' => 'eval' ) ) );
t( array( 'bad_op' ) === codes( $b ), 'unknown operator reported' );
$dup = array_merge( $old, array( array( 'type' => 'text', 'name' => 'kg', 'label' => 'Kg 2' ) ) );
$nd  = bst_form_schema_normalize( $dup );
t( 'f_kg_2' === $nd[3]['id'] && array( 'duplicate_name' ) === codes( $dup ), 'duplicate names get unique IDs and a warning' );

// Operators and multi-choice values.
t( bst_form_rule_met( array( 'op' => 'is', 'value' => 'b' ), 'a, b' ) && ! bst_form_rule_met( array( 'op' => 'is_not', 'value' => 'b' ), 'a, b' ), 'checkbox values match one choice' );
t( ! bst_form_rule_met( array( 'op' => 'gt', 'value' => '5' ), 'abc' ) && bst_form_rule_met( array( 'op' => 'lt', 'value' => '5' ), '1.5' ), 'number compare' );
t( bst_form_rule_met( array( 'op' => 'contains', 'value' => 'ab' ), 'xABy' ) && ! bst_form_rule_met( array( 'op' => 'contains', 'value' => '' ), 'x' ), 'contains' );
t( '' === bst_form_condition_normalize( array( 'rules' => array() ) ), 'empty condition dropped' );

exit( $fail ? 1 : 0 );
