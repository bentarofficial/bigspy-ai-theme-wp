<?php
/**
 * G-69 industry presets (inc/form-presets.php), without WordPress.
 * Usage: php tools/tests/form-presets-check.php [out-dir]
 *
 * Every preset: valid schema, valid price rules, every price marked demo, each
 * sample quote equal to its expected total through bst_pricing_quote(), and the
 * same result after the AI Designer cleaning (bst_aid_form_clean_fields/_pricing,
 * as form_config check/save would store it). With out-dir, also writes one HTML
 * page per preset rendered by the real Form block template, plus samples.json,
 * for tools/tests/form-presets-browser.cjs.
 */

require dirname( __DIR__ ) . '/eval/lib/render.php';

define( 'BST_AID_FORM_MAX_FIELDS', 40 );
define( 'BST_AID_FORM_MAX_LINES', 30 );
bst_eval_define( 'remove_accents', 'function remove_accents( $s ) { $t = @iconv( "UTF-8", "ASCII//TRANSLIT//IGNORE", (string) $s ); return false === $t ? (string) $s : $t; }' );
bst_eval_define( 'sanitize_textarea_field', 'function sanitize_textarea_field( $s ) { return trim( strip_tags( (string) $s ) ); }' );
bst_eval_load_forms();
require_once BST_EVAL_THEME . '/inc/form-presets.php';
$missing = bst_eval_load_functions(
	'ai-designer/includes/tools-form.php',
	array( 'bst_aid_form_text', 'bst_aid_form_num', 'bst_aid_form_tag', 'bst_aid_form_ref', 'bst_aid_form_when', 'bst_aid_form_clean_fields', 'bst_aid_form_clean_pricing', 'bst_aid_form_mark_demo', 'bst_aid_form_preset_result' )
);

$pass = 0;
$fail = array();
function bst_t( $ok, $label ) {
	global $pass, $fail;
	if ( $ok ) {
		$pass++;
	} else {
		$fail[] = $label;
	}
}
bst_t( ! $missing, 'AI form functions found: ' . implode( ', ', $missing ) );

$want = array( 'laundry', 'cleaning', 'moving', 'printing', 'curtains', 'lodging', 'spa', 'installment' );
$all  = bst_form_presets();
bst_t( array_keys( $all ) === $want, 'Preset keys: ' . implode( ', ', array_keys( $all ) ) );
$kinds = array();
$out   = $argv[1] ?? '';
$pages = array();

foreach ( $all as $key => $p ) {
	$fields = bst_form_schema_normalize( $p['fields'] );
	bst_t( array_column( $fields, 'id' ) === array_column( $p['fields'], 'id' ), "$key: field ids stable" );
	bst_t( ! bst_form_schema_errors( $fields ), "$key: no condition errors" );
	foreach ( $fields as $f ) {
		bst_t( in_array( $f['type'], bst_native_field_types(), true ), "$key: field type {$f['type']}" );
	}
	$s = bst_pricing_settings( $p['pricing'] );
	bst_t( $s && count( $s['lines'] ) === count( $p['pricing']['lines'] ), "$key: every price line valid" );
	bst_t( false !== stripos( $p['pricing']['note'], 'demo' ), "$key: note says demo" );
	foreach ( $p['pricing']['lines'] as $l ) {
		$kinds[ $l['kind'] ] = true;
		bst_t( false !== stripos( $l['label'], 'demo' ), "$key/{$l['id']}: label says demo" );
		if ( 'formula' === $l['kind'] ) {
			bst_t( '' === bst_pricing_check_expr( $l['expr'], array_merge( array_column( $fields, 'id' ), array_column( $p['pricing']['lines'], 'id' ) ) ), "$key/{$l['id']}: formula valid" );
		}
	}
	bst_t( count( $p['samples'] ) >= 2, "$key: at least two samples" );
	foreach ( $p['samples'] as $i => $smp ) {
		$q = bst_pricing_quote( $s, $fields, $smp['values'] );
		bst_t( 'ok' === $q['status'] && $q['total'] === $smp['total'], "$key sample $i: want {$smp['total']} got {$q['status']} {$q['total']}" );
	}

	// The AI path: form_config cleans fields and pricing the same way before check/save.
	$errors   = array();
	$warnings = array();
	$cf       = bst_aid_form_clean_fields( $p['fields'], array(), $errors );
	$cp       = bst_aid_form_clean_pricing( $p['pricing'], $cf, array(), $errors, $warnings );
	bst_t( ! $errors, "$key: AI cleaning has no errors " . implode( ' | ', $errors ) );
	bst_t( ! $warnings, "$key: AI cleaning has no warnings " . implode( ' | ', $warnings ) );
	bst_t( array_column( $cf, 'id' ) === array_column( $p['fields'], 'id' ), "$key: AI keeps field ids" );
	$cp2 = bst_aid_form_mark_demo( json_decode( wp_json_encode( $cp ), true ) );
	bst_t( array_column( $cp2['lines'], 'label' ) === array_column( $cp['lines'], 'label' ) && $cp2['note'] === $cp['note'], "$key: demo marking leaves preset labels unchanged" );
	$cs = bst_pricing_settings( json_decode( wp_json_encode( $cp ), true ) );
	foreach ( $p['samples'] as $i => $smp ) {
		$q = bst_pricing_quote( $cs, bst_form_schema_normalize( $cf ), $smp['values'] );
		bst_t( 'ok' === $q['status'] && $q['total'] === $smp['total'], "$key sample $i after AI cleaning: got {$q['status']} {$q['total']}" );
	}
	if ( $out ) {
		$markup = bst_eval_tree_to_markup( array( array( 'name' => 'bst/form', 'attrs' => array( 'provider' => 'bigspy', 'formKey' => 'preset-' . $key, 'title' => $p['title'], 'fields' => $p['fields'], 'pricing' => $p['pricing'], 'schemaVersion' => 1 ) ) ) );
		$stats  = array();
		$html   = bst_eval_render_blocks( parse_blocks( $markup ), $stats );
		bst_t( empty( $stats['failed'] ) && false !== strpos( $html, 'data-bs-quote' ), "$key: form renders with its price box" );
		$doc = str_replace( '</body>', '<script src="file://' . BST_EVAL_THEME . '/assets/js/blocks.js"></script></body>', bst_eval_page_html( $html, $key ) );
		file_put_contents( "$out/$key.html", $doc );
		$pages[ $key ] = array( 'fields' => $fields, 'samples' => $p['samples'] );
	}
}
bst_t( 7 === count( $kinds ), 'Presets use all 7 line kinds: ' . implode( ', ', array_keys( $kinds ) ) );

// AI tool output.
$list = bst_aid_form_preset_result( '' );
bst_t( count( $list['presets'] ) === count( $want ), 'form_config preset: list' );
$one = bst_aid_form_preset_result( 'moving' );
bst_t( 'moving' === $one['preset'] && $one['fields'] && $one['pricing']['lines'] && 2 === count( $one['samples'] ) && false !== strpos( $one['note'], 'demo' ), 'form_config preset: one preset with samples and demo note' );
bst_t( isset( bst_aid_form_preset_result( 'nope' )['presets'] ), 'form_config preset: unknown key lists presets' );

if ( $out ) {
	file_put_contents( "$out/samples.json", wp_json_encode( $pages ) );
}
echo "form presets: $pass passed, " . count( $fail ) . " failed\n";
foreach ( $fail as $f ) {
	echo "  FAIL $f\n";
}
exit( $fail ? 1 : 0 );
