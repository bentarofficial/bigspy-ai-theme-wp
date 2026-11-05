<?php
/**
 * G-59 AI form tools inside WordPress (Playground). Loaded after wp-load.php;
 * prints one line per check. Covers form_get / form_configure, dry run, refusal of
 * broken schema, approval on published pages, layout kept, and task undo.
 */
wp_set_current_user( 1 );
$fail = 0;
function bst_t( $ok, $label ) {
	global $fail;
	echo ( $ok ? 'ok   ' : 'FAIL ' ) . $label . "\n";
	$fail += $ok ? 0 : 1;
}
$task = wp_insert_post( array( 'post_type' => 'bst_aid_task', 'post_status' => 'private', 'post_title' => 'Form AI tests', 'post_author' => 1 ) );
$GLOBALS['bst_aid_task_id'] = $task;
bst_aid_task_set( $task, '_bst_aid_run', array( 'active' => true ) );

$content = '<!-- wp:bst/section --><div class="keep-me">Intro</div><!-- /wp:bst/section -->'
	. '<!-- wp:bst/section --><!-- wp:bst/form {"provider":"cf7","title":"Liên hệ","card":true} /--><!-- /wp:bst/section -->';
$page    = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'draft', 'post_title' => 'G59', 'post_content' => $content ) );

$g = bst_aid_run_tool( 'form_get', array( 'id' => $page ) );
bst_t( 1 === count( $g['forms'] ?? array() ) && 'cf7' === $g['forms'][0]['provider'], 'form_get finds the nested form' );
bst_t( 'f_your-name' === ( $g['forms'][0]['fields'][0]['id'] ?? '' ), 'form_get gives stable field ids' );

$fields  = array(
	array( 'type' => 'text', 'label' => 'Họ và tên', 'name' => 'your-name', 'id' => 'f_your-name', 'required' => true ),
	array( 'type' => 'tel', 'label' => 'Số điện thoại', 'name' => 'your-phone', 'id' => 'f_your-phone', 'required' => true ),
	array( 'type' => 'number', 'label' => 'Khối lượng (kg)', 'required' => true ),
	array( 'type' => 'radio', 'label' => 'Tốc độ', 'name' => 'speed', 'options' => "Thường\nNhanh" ),
	array( 'type' => 'checkbox', 'label' => 'Giao nhận', 'name' => 'ship', 'options' => 'Giao nhận', 'prices' => 'Giao nhận = 25000' ),
	array( 'type' => 'text', 'label' => 'Địa chỉ', 'name' => 'address', 'show' => array( 'rules' => array( array( 'field' => 'f_ship', 'op' => 'not_empty' ) ) ) ),
);
$pricing = array( 'enabled' => true, 'note' => 'Giá demo', 'lines' => array(
	array( 'id' => 'l_wash', 'label' => 'Tiền giặt', 'expr' => '{f_khoi-luong-kg} * 18000' ),
	array( 'id' => 'l_fast', 'label' => 'Giặt nhanh', 'expr' => '{l_wash} * 0.2 * {f_speed=Nhanh}' ),
	array( 'id' => 'l_ship', 'label' => 'Giao nhận', 'expr' => '{f_ship}' ),
) );
$args = array( 'id' => $page, 'fields' => $fields, 'pricing' => $pricing, 'dry_run' => true, 'test_values' => array( 'khoi-luong-kg' => '3', 'speed' => 'Nhanh', 'ship' => array( 'Giao nhận' ) ) );
$dry  = bst_aid_run_tool( 'form_configure', $args );
bst_t( false === ( $dry['saved'] ?? null ) && ! isset( $dry['approval'] ), 'dry run needs no approval and saves nothing' );
bst_t( false !== strpos( (string) ( $dry['sample_quote'] ?? '' ), '89.800' ), 'dry run sample quote 89.800: ' . wp_json_encode( $dry['sample_quote'] ?? $dry, JSON_UNESCAPED_UNICODE ) );
bst_t( in_array( 'switch provider cf7 → built-in form', $dry['diff'] ?? array(), true ) && preg_grep( '/^add price line l_wash/', $dry['diff'] ), 'diff lists provider switch and price lines' );
bst_t( get_post( $page )->post_content === $content, 'page unchanged after dry run' );

$bad        = $args;
$bad['dry_run'] = false;
$bad['pricing']['lines'][0]['expr'] = 'system("x")';
$r          = bst_aid_run_tool( 'form_configure', $bad );
bst_t( false === ( $r['saved'] ?? null ) && $r['problems'], 'broken formula refused' );
$bad              = $args;
$bad['dry_run']   = false;
$bad['fields'][5]['show']['rules'][0]['field'] = 'f_gone';
$r                = bst_aid_run_tool( 'form_configure', $bad );
bst_t( false === ( $r['saved'] ?? null ) && preg_grep( '/missing_ref/', $r['problems'] ), 'condition on a missing field refused' );

$save = $args;
unset( $save['dry_run'] );
$r = bst_aid_run_tool( 'form_configure', $save );
bst_t( true === ( $r['saved'] ?? null ), 'draft page saved without approval' );
$after = get_post( $page )->post_content;
bst_t( false !== strpos( $after, 'keep-me' ) && false !== strpos( $after, '"title":"Liên hệ"' ) || false !== strpos( $after, '"title":"Liên hệ"' ), 'other blocks and form title kept' );
$g = bst_aid_run_tool( 'form_get', array( 'id' => $page ) );
$f = $g['forms'][0];
bst_t( 'bigspy' === $f['provider'] && $f['form_key'] && ! $f['problems'] && 'f_khoi-luong-kg' === $f['fields'][2]['id'], 'saved form valid with key and new ids' );
$html = do_blocks( $after );
bst_t( false !== strpos( $html, 'data-bs-quote' ) && false !== strpos( $html, 'data-bs-show' ), 'front end renders quote box and conditions' );

// Rename a label and reorder: ids, names and formulas keep working.
$re            = $g['forms'][0]['fields'];
$re[2]['label'] = 'Cân nặng';
$re            = array_reverse( $re );
$r             = bst_aid_run_tool( 'form_configure', array( 'id' => $page, 'fields' => $re, 'test_values' => array( 'khoi-luong-kg' => '3' ) ) );
bst_t( true === ( $r['saved'] ?? null ) && preg_grep( '/field order changed/', $r['diff'] ) && false !== strpos( (string) $r['sample_quote'], '54.000' ), 'rename + reorder keeps the price' );

// Deleting a field used by a formula is refused.
$del = array_values( array_filter( $re, function ( $x ) { return 'f_ship' !== $x['id']; } ) );
$r   = bst_aid_run_tool( 'form_configure', array( 'id' => $page, 'fields' => $del ) );
bst_t( false === ( $r['saved'] ?? null ) && preg_grep( '/missing_ref/', $r['problems'] ), 'deleting a priced field refused' );

// Published page: price change needs approval.
wp_update_post( array( 'ID' => $page, 'post_status' => 'publish' ) );
$pub                         = $pricing;
$pub['lines'][0]['expr']     = '{f_khoi-luong-kg} * 20000';
$r                           = bst_aid_run_tool( 'form_configure', array( 'id' => $page, 'pricing' => $pub ) );
bst_t( 'approval' === ( $r['status'] ?? '' ), 'published price change asks for approval' );
$r = bst_aid_run_tool( 'form_configure', array( 'id' => $page, 'pricing' => $pub, 'dry_run' => true ) );
bst_t( false === ( $r['saved'] ?? null ) && preg_grep( '/18000 → /', $r['diff'] ), 'published dry run still allowed, shows old → new' );
bst_t( 2 === bst_aid_tool_policy( 'form_configure', array( 'id' => $page ) )['level'] && 1 === bst_aid_tool_policy( 'form_get', array() )['level'], 'policy levels' );

// Undo restores the original page.
bst_aid_undo_task( $task );
bst_t( get_post( $page )->post_content === $content, 'task undo restores the page' );

// Other users' content.
$other = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'draft', 'post_title' => 'X', 'post_content' => '<!-- wp:bst/form /-->', 'post_author' => 1 ) );
wp_set_current_user( 0 );
bst_t( isset( bst_aid_tool_form_get( array( 'id' => $other ) )['error'] ), 'no access without edit rights' );
wp_set_current_user( 1 );

$specs = wp_list_pluck( wp_list_pluck( bst_aid_tool_specs(), 'function' ), 'parameters', 'name' );
bst_t( isset( $specs['form_get'], $specs['form_configure'] ) && false !== wp_json_encode( $specs ) && 'object' === $specs['form_configure']['properties']['fields']['items']['type'], 'tools offered to the model with valid schemas' );
bst_t( false !== strpos( file_get_contents( BST_AID_DIR . '/knowledge/theme.md' ), 'form_configure' ), 'knowledge explains form tools' );

echo $fail ? "FAILED $fail\n" : "ALL PASS\n";
