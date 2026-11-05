<?php
/**
 * G-57/G-58/G-59 integration on a real WordPress (CLI, no web server).
 * Usage: php tools/tests/form-integration.php /path/to/wordpress
 * The WordPress install must have this theme active (a disposable test site).
 */
define( 'DOING_AJAX', true );
$_SERVER['HTTP_HOST']   = 't.test';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REMOTE_ADDR'] = '10.0.0.' . wp_rand_ip();
function wp_rand_ip() {
	return (string) mt_rand( 2, 250 );
}
require rtrim( $argv[1] ?? '/wordpress', '/' ) . '/wp-load.php';

$fail = 0;
function t( $ok, $label ) {
	global $fail;
	echo ( $ok ? 'ok   ' : 'FAIL ' ) . $label . "\n";
	$fail += $ok ? 0 : 1;
}
class BST_Json_Exit extends Exception {}
add_filter( 'wp_die_ajax_handler', function () {
	return function () {
		throw new BST_Json_Exit();
	};
} );
$GLOBALS['bst_mail'] = array();
add_filter( 'pre_wp_mail', function ( $r, $atts ) {
	$GLOBALS['bst_mail'][] = $atts;
	return true;
}, 10, 2 );
function bst_t_submit( $post ) {
	$_POST = $post;
	delete_transient( 'bst_nf_' . md5( $_SERVER['REMOTE_ADDR'] ) );
	ob_start();
	try {
		bst_native_form_submit();
	} catch ( BST_Json_Exit $e ) {
		unset( $e );
	}
	return json_decode( ob_get_clean(), true );
}

wp_set_current_user( 1 );
$legacy = array(
	array( 'type' => 'text', 'name' => 'your-name', 'label' => 'Tên', 'required' => true ),
	array( 'type' => 'number', 'name' => 'so-kg', 'label' => 'Khối lượng (kg)', 'required' => true ),
	array( 'type' => 'checkbox', 'name' => 'giao-nhan', 'label' => 'Giao nhận', 'options' => 'Giao nhận tận nơi' ),
	array( 'type' => 'text', 'name' => 'dia-chi', 'label' => 'Địa chỉ' ),
);
$attrs  = array( 'provider' => 'bigspy', 'formKey' => 'ftest01', 'fields' => $legacy, 'mailBody' => "[all-fields]\n--\n[_quote]", 'mailTo' => 'shop@t.test' );
$page   = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Báo giá', 'post_content' => serialize_blocks( array( array( 'blockName' => 'bst/section', 'attrs' => array(), 'innerBlocks' => array( array( 'blockName' => 'bst/form', 'attrs' => $attrs, 'innerBlocks' => array(), 'innerHTML' => '', 'innerContent' => array() ) ), 'innerHTML' => '', 'innerContent' => array( null ) ) ) ) ) );
t( $page > 0, 'test page with a legacy 1.81 form' );

// Legacy form renders and sends exactly as before.
$html = render_block( array( 'blockName' => 'bst/form', 'attrs' => $attrs, 'innerBlocks' => array(), 'innerHTML' => '', 'innerContent' => array() ) );
t( false !== strpos( $html, 'data-bs-fid="n_so_kg"' ) && false === strpos( $html, 'data-bs-calc=' ), 'legacy form renders with derived ids, no price script data' );
$r = bst_t_submit( array( 'bst_form_key' => 'ftest01', 'bst_form_post' => $page, 'your-name' => 'An', 'so-kg' => '3' ) );
t( ! empty( $r['ok'] ) && empty( $r['quote'] ) && false !== strpos( end( $GLOBALS['bst_mail'] )['message'], 'Khối lượng (kg): 3' ), 'legacy form sends; mail unchanged, no quote' );

// G-59: AI reads the form.
$view = bst_aid_tool_get_form( array( 'id' => $page ) );
t( 1 === count( $view['forms'] ) && 'n_so_kg' === $view['forms'][0]['fields'][1]['id'] && array() === $view['forms'][0]['problems'], 'get_form lists fields with stable ids' );

// AI proposes prices: dry run saves nothing.
$fields   = $view['forms'][0]['fields'];
$fields[2]['optionPrices'] = array( 'Giao nhận tận nơi' => 25000 );
$fields[3]['conditions']   = array( 'rules' => array( array( 'field' => 'n_giao_nhan', 'op' => 'is', 'value' => 'Giao nhận tận nơi' ) ) );
$fields[] = array( 'type' => 'calc', 'name' => 'tien-giat', 'label' => 'Tiền giặt', 'formula' => '{n_so_kg} * 18000' );
$before   = get_post_field( 'post_content', $page );
$call     = array( 'id' => $page, 'key' => 'ftest01', 'fields' => $fields, 'sample' => array( 'so-kg' => '3', 'giao-nhan' => array( 'Giao nhận tận nơi' ) ) );
$dry      = bst_aid_tool_form_config( $call );
t( ! empty( $dry['dry_run'] ) && $before === get_post_field( 'post_content', $page ), 'dry run saves nothing' );
t( ! empty( $dry['diff']['pricing_changed'] ) && in_array( 'optionPrices', $dry['diff']['changed']['n_giao_nhan'] ?? array(), true ) && 1 === count( $dry['diff']['added'] ), 'dry run shows the diff' );
t( array( 'Tiền giặt: 54.000 ₫' ) === $dry['example']['lines'], 'dry run shows a worked example' );
t( 1 === bst_aid_tool_policy( 'form_config', $call )['level'] && false === bst_aid_tool_policy( 'form_config', $call )['write'], 'dry run is a read' );

// Saving price rules always needs approval; a broken schema is refused.
$save = $call + array( 'dry_run' => false );
t( 2 === bst_aid_tool_policy( 'form_config', $save )['level'], 'saving price changes needs approval' );
t( 2 === bst_aid_tool_policy( 'update_page', array( 'id' => $page, 'blocks' => array( array( 'name' => 'bst/form', 'attrs' => array( 'fields' => array( array( 'type' => 'calc', 'formula' => '1' ) ) ) ) ) ) )['level'], 'price formulas through update_page also need approval' );
$bad                 = $save;
$bad['fields'][4]['formula'] = '{n_so_kg} * {f_missing}';
$r                   = bst_aid_tool_form_config( $bad );
t( 'invalid_schema' === ( $r['error'] ?? '' ) && $before === get_post_field( 'post_content', $page ), 'formula with a missing field refused, nothing saved' );
$bad           = $save;
$bad['fields'] = array_values( array_filter( $bad['fields'], function ( $f ) { return 'so-kg' !== ( $f['name'] ?? '' ); } ) );
$r             = bst_aid_tool_form_config( $bad );
t( 'invalid_schema' === ( $r['error'] ?? '' ), 'removing a field the formula uses is refused' );
$bad              = $save;
$bad['fields'][4]['formula'] = 'system(1)';
t( 'invalid_schema' === ( bst_aid_tool_form_config( $bad )['error'] ?? '' ), 'script-like formula refused' );

// Save (as after approval): ids kept, a total added by the AI without ids.
$save['fields'][] = array( 'type' => 'calc', 'name' => 'tong', 'label' => 'Tổng', 'formula' => '{' . 'n_so_kg} * 18000 + {n_giao_nhan}', 'isTotal' => true );
$save['settings'] = array( 'submitText' => 'Gửi yêu cầu báo giá' );
$saved            = bst_aid_tool_form_config( $save );
$after            = bst_aid_tool_get_form( array( 'id' => $page ) )['forms'][0];
t( ! empty( $saved['saved'] ) && 'n_so_kg' === $after['fields'][1]['id'] && 6 === count( $after['fields'] ) && 'Gửi yêu cầu báo giá' === $after['settings']['submitText'], 'saved: ids kept, fields and settings written' );
t( false !== strpos( get_post_field( 'post_content', $page ), '"schemaVersion":2' ) && false !== strpos( get_post_field( 'post_content', $page ), '<!-- wp:bst/section' ), 'other blocks kept, schema version written' );

// The saved form computes on the server (G-58) with conditions (G-57).
$r    = bst_t_submit( array( 'bst_form_key' => 'ftest01', 'bst_form_post' => $page, 'your-name' => 'An', 'so-kg' => '3', 'giao-nhan' => array( 'Giao nhận tận nơi' ), 'dia-chi' => '1 Lê Lợi', 'tong' => '1' ) );
$mail = end( $GLOBALS['bst_mail'] )['message'];
t( ! empty( $r['ok'] ) && 79000.0 === (float) $r['quote']['total'] && false !== strpos( $mail, 'Tổng: 79.000 ₫' ) && false !== strpos( $mail, '1 Lê Lợi' ), 'server total 79.000 ₫, forged total ignored, mail has the quote' );
$r    = bst_t_submit( array( 'bst_form_key' => 'ftest01', 'bst_form_post' => $page, 'your-name' => 'An', 'so-kg' => '2', 'dia-chi' => 'secret' ) );
$mail = end( $GLOBALS['bst_mail'] )['message'];
t( ! empty( $r['ok'] ) && 36000.0 === (float) $r['quote']['total'] && false === strpos( $mail, 'secret' ), 'hidden address dropped, no delivery charge' );
$entry = get_posts( array( 'post_type' => 'bst_form_entry', 'post_status' => 'private', 'numberposts' => 1 ) );
$q     = $entry ? get_post_meta( $entry[0]->ID, '_bst_quote', true ) : null;
t( is_array( $q ) && 36000.0 === (float) $q['total'] && 'estimate' === $q['status'] && 'VND' === $q['currency'], 'entry keeps the quote snapshot' );

// Undo of the AI task restores the page.
$task = wp_insert_post( array( 'post_type' => 'bst_aid_task', 'post_status' => 'private', 'post_title' => 'undo test' ) );
$GLOBALS['bst_aid_task_id'] = $task;
$cur  = get_post_field( 'post_content', $page );
$mini = $save;
$mini['fields'] = array_slice( $save['fields'], 0, 2 );
$r    = bst_aid_tool_form_config( $mini );
t( ! empty( $r['saved'] ) && 2 === count( bst_aid_tool_get_form( array( 'id' => $page ) )['forms'][0]['fields'] ), 'second save inside a task' );
bst_aid_undo_task( $task );
clean_post_cache( $page );
t( $cur === get_post_field( 'post_content', $page ), 'task undo restores the form' );

wp_delete_post( $page, true );
wp_delete_post( $task, true );
foreach ( get_posts( array( 'post_type' => 'bst_form_entry', 'post_status' => 'any', 'numberposts' => -1 ) ) as $e ) {
	wp_delete_post( $e->ID, true );
}
echo $fail ? "\n$fail failed\n" : "\nall passed\n";
exit( $fail ? 1 : 0 );
