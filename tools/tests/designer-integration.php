<?php
require '/wordpress/wp-load.php';
wp_set_current_user( 1 );
// Keep the offline test canvas independent of third-party font requests.
foreach ( array( 'font_body', 'font_heading', 'font_nav', 'font_button' ) as $font ) { set_theme_mod( $font, 'system' ); }
$checks = array();
function bst_test( $ok, $label ) {
    global $checks;
    $checks[] = array( 'pass' => (bool) $ok, 'test' => $label );
    if ( ! $ok ) { throw new Exception( 'FAILED: ' . $label ); }
}
$task = wp_insert_post( array( 'post_type' => 'bst_aid_task', 'post_status' => 'private', 'post_title' => 'Integration tests', 'post_author' => 1 ) );
$GLOBALS['bst_aid_task_id'] = $task;
bst_aid_task_set( $task, '_bst_aid_run', array( 'active' => true ) );
function bst_test_run( $name, $args ) { return bst_aid_run_tool( $name, $args ); }
function bst_test_approve( $name, $args ) {
    global $task;
    $GLOBALS['bst_aid_task_id'] = $task;
    $r = bst_aid_run_tool( $name, $args );
    bst_test( isset( $r['approval']['id'] ), 'Approval required: ' . $name );
    $req = new WP_REST_Request( 'POST' );
    $req['id'] = $task; $req->set_param( 'approval_id', $r['approval']['id'] ); $req->set_param( 'decision', 'approve' );
    $response = bst_aid_rest_approval( $req );
    bst_test( ! is_wp_error( $response ), 'Approval executes: ' . $name );
    $result = $response->get_data()['result'];
    bst_test( ! isset( $result['error'] ), 'Approved tool succeeds: ' . $name . ' ' . wp_json_encode( $result ) );
    bst_test( is_wp_error( bst_aid_rest_approval( $req ) ), 'Approval cannot be replayed: ' . $name );
    $GLOBALS['bst_aid_task_id'] = $task;
    return $result;
}
bst_test( ! empty( bst_aid_tool_get_knowledge( array( 'topic' => 'theme' ) )['guide'] ), 'Theme knowledge loads' );
bst_test( isset( bst_aid_run_tool( 'delete_plugin', array() )['error'] ), 'Unknown level 3 tools denied' );
bst_test( isset( bst_aid_tool_site_settings( array( 'values' => array( 'admin_email' => 'no@example.com' ) ) )['error'] ), 'Option whitelist blocks admin email in code' );
$markup = array( array( 'name' => 'bst/heading', 'attrs' => array( 'tag' => 'h2', 'text' => 'Footer links' ) ) );
$ux = bst_test_run( 'ux_block', array( 'mode' => 'create', 'title' => 'Footer test', 'blocks' => $markup ) );
bst_test( ! empty( $ux['id'] ) && 'draft' === get_post_status( $ux['id'] ), 'UX Block created as draft' );
bst_test_approve( 'publish', array( 'id' => $ux['id'] ) );
$old_footer = get_theme_mod( 'footer_block', null );
bst_test_approve( 'set_footer', array( 'source' => 'blocks', 'id' => $ux['id'] ) );
bst_test( (int) bst_mod( 'footer_block' ) === $ux['id'] && bst_footer_block_post()->ID === $ux['id'], 'Published UX Block assigned to live footer' );
$home = bst_test_run( 'create_page', array( 'title' => 'Home test', 'blocks' => array( array( 'name' => 'bst/heading', 'attrs' => array( 'tag' => 'h1', 'text' => 'Home' ) ) ) ) );
bst_test( ! empty( $home['id'] ), 'Draft homepage created' );
bst_test_approve( 'publish', array( 'id' => $home['id'] ) );
$old_front = get_option( 'page_on_front' );
bst_test_approve( 'site_settings', array( 'values' => array( 'show_on_front' => 'page', 'page_on_front' => $home['id'] ) ) );
bst_test( (int) get_option( 'page_on_front' ) === $home['id'], 'Static homepage assigned' );
$live_title = get_the_title( $home['id'] );
$r = bst_test_run( 'update_page', array( 'id' => $home['id'], 'title' => 'Unauthorized', 'confirmed' => true ) );
bst_test( isset( $r['approval'] ) && get_the_title( $home['id'] ) === $live_title, 'Model confirmed:true cannot bypass approval' );
bst_test_approve( 'seo_meta', array( 'id' => $home['id'], 'provider' => 'yoast', 'title' => 'Home SEO', 'description' => 'Description test.' ) );
bst_test( 'Home SEO' === get_post_meta( $home['id'], '_yoast_wpseo_title', true ), 'Yoast SEO saved' );
bst_test_approve( 'set_menu', array( 'name' => 'Integration menu', 'location' => 'primary', 'items' => array( array( 'title' => 'Home', 'page_id' => $home['id'] ) ) ) );
$menus = bst_aid_tool_get_menus();
bst_test( ! empty( get_nav_menu_locations()['primary'] ) && count( $menus['locations'] ) > 0, 'Primary menu and locations available' );
$layout = bst_test_run( 'theme_layout', array( 'mode' => 'create', 'title' => 'Test footer layout', 'location' => 'footer', 'rules' => array( 'front' ), 'blocks' => $markup ) );
bst_test( ! empty( $layout['id'] ) && 'front' === get_post_meta( $layout['id'], '_bst_tb_rules', true ), 'Theme Builder draft and rules saved' );
bst_aid_task_set( $task, '_bst_aid_mode', 'advice' );
$r = bst_test_run( 'create_page', array( 'title' => 'Forbidden', 'blocks' => $markup ) );
bst_test( 'advice_only' === ( $r['error'] ?? '' ), 'Advice mode blocks draft writes' );
$overview = bst_test_run( 'overview', array() );
bst_test( ! empty( $overview['blocks'] ), 'Advice mode permits discovery reads: ' . wp_json_encode( array_intersect_key( $overview, array_flip( array( 'error', 'message' ) ) ) ) );
bst_aid_task_set( $task, '_bst_aid_mode', 'design' );
bst_aid_task_set( $task, '_bst_aid_run', array( 'active' => false ) );
$r = bst_test_run( 'create_page', array( 'title' => 'Stopped', 'blocks' => $markup ) );
bst_test( 'stopped' === ( $r['error'] ?? '' ), 'Stopped task refuses late writes' );
bst_aid_task_set( $task, '_bst_aid_run', array( 'active' => true ) );
// Plugin approval skip must never start installation. Trusted installation is mocked below.
$r = bst_test_run( 'install_plugin', array( 'slug' => 'hello-dolly' ) );
bst_test( isset( $r['approval'] ), 'Plugin install requires approval' );
$req = new WP_REST_Request( 'POST' ); $req['id'] = $task;
$req->set_param( 'approval_id', $r['approval']['id'] ); $req->set_param( 'decision', 'skip' );
$response = bst_aid_rest_approval( $req );
bst_test( ! is_wp_error( $response ) && 'skip' === $response->get_data()['result']['status'], 'Skip plugin approval performs no install' );
$GLOBALS['bst_aid_task_id'] = $task;
// Simulate the directory API, retaining a real local install/activate/undo path.
require_once ABSPATH . 'wp-admin/includes/plugin.php';
wp_mkdir_p( WP_PLUGIN_DIR . '/bst-test-plugin' );
file_put_contents( WP_PLUGIN_DIR . '/bst-test-plugin/plugin.php', "<?php\n/* Plugin Name: BIGSPY test fixture */\n" );
wp_clean_plugins_cache();
add_filter( 'plugins_api', function ( $result, $action, $args ) { return 'bst-test-plugin' === ( $args->slug ?? '' ) ? (object) array( 'download_link' => 'https://downloads.wordpress.org/plugin/bst-test-plugin.zip' ) : $result; }, 10, 3 );
bst_test_approve( 'install_plugin', array( 'slug' => 'bst-test-plugin' ) );
bst_test( is_plugin_active( 'bst-test-plugin/plugin.php' ), 'Approved WordPress.org plugin activated' );
bst_aid_undo_task( $task );
bst_test( get_theme_mod( 'footer_block', null ) === $old_footer && (int) get_option( 'page_on_front' ) === (int) $old_front, 'Undo restores footer and front page' );
bst_test( 'trash' === get_post_status( $home['id'] ) && 'trash' === get_post_status( $ux['id'] ), 'Undo trashes task-created content' );
bst_test( ! is_plugin_active( 'bst-test-plugin/plugin.php' ), 'Undo deactivates task-activated plugin' );

// Exercise the real browser-driven model loop with a deterministic Kyma response.
$run_task = wp_insert_post( array( 'post_type' => 'bst_aid_task', 'post_status' => 'private', 'post_title' => 'Mock Kyma run', 'post_author' => 1 ) );
bst_aid_save_settings( array( 'connected' => true, 'text_model' => 'test-model', 'budget_task' => 2, 'budget_day' => 10 ) );
update_option( 'bst_aid_key', bst_aid_encrypt( 'local-test-fixture-only' ) );
set_transient( 'bst_aid_models', array( 'text' => array( array( 'id' => 'test-model', 'input' => 0, 'output' => 0 ) ), 'image' => array() ), 3600 );
$GLOBALS['bst_test_kyma_mode'] = 'draft';
add_filter( 'pre_http_request', function ( $result, $args, $url ) {
    if ( false === strpos( $url, 'kymaapi.com/' ) ) { return $result; }
    $GLOBALS['bst_test_kyma_calls'] = (int) ( $GLOBALS['bst_test_kyma_calls'] ?? 0 ) + 1;
    $fn = 'draft' === $GLOBALS['bst_test_kyma_mode'] ? 'create_page' : 'site_settings';
    $params = 'create_page' === $fn ? array( 'title' => 'Kyma fixture page', 'blocks' => array( array( 'name' => 'bst/heading', 'attrs' => array( 'text' => 'Mock page', 'tag' => 'h1' ) ) ) ) : array( 'values' => array( 'blogdescription' => 'This requires a real approval card.' ), 'confirmed' => true );
    if ( 'stop' === $GLOBALS['bst_test_kyma_mode'] ) {
        $run = bst_aid_task_get( $GLOBALS['bst_test_run_task'], '_bst_aid_run' ); $run['active'] = false;
        bst_aid_task_set( $GLOBALS['bst_test_run_task'], '_bst_aid_run', $run );
    }
    return array( 'headers' => array(), 'response' => array( 'code' => 200, 'message' => 'OK' ), 'body' => wp_json_encode( array( 'choices' => array( array( 'message' => array( 'role' => 'assistant', 'content' => null, 'tool_calls' => array( array( 'id' => 'call-test', 'type' => 'function', 'function' => array( 'name' => $fn, 'arguments' => wp_json_encode( $params ) ) ) ) ) ) ), 'usage' => array( 'cost' => 0.001 ) ) ) );
}, 10, 3 );
$GLOBALS['bst_test_run_task'] = $run_task;
$request = new WP_REST_Request( 'POST' ); $request['id'] = $run_task;
$request->set_param( 'text', 'Build the test draft.' ); $request->set_param( 'effort', 'high' );
bst_aid_rest_task_send( $request );
$response = bst_aid_rest_task_step( $request )->get_data();
bst_test( 'running' === $response['state'] && count( bst_aid_task_get( $run_task, '_bst_aid_created' ) ) === 1, 'Mock Kyma step creates one draft via dispatcher' );
$GLOBALS['bst_test_kyma_mode'] = 'approval';
$old_tagline = get_option( 'blogdescription' );
$response = bst_aid_rest_task_step( $request )->get_data();
bst_test( 'stopped' === $response['state'] && 'approval' === end( $response['events'] )['kind'] && get_option( 'blogdescription' ) === $old_tagline, 'Mock model cannot bypass level 2 with confirmed true' );
$card = end( $response['events'] )['approval'];
$approve = new WP_REST_Request( 'POST' ); $approve['id'] = $run_task; $approve->set_param( 'approval_id', $card['id'] ); $approve->set_param( 'decision', 'approve' );
$approved = bst_aid_rest_approval( $approve );
bst_test( ! is_wp_error( $approved ) && 'This requires a real approval card.' === get_option( 'blogdescription' ), 'Human endpoint approval applies exact pending arguments' );
$GLOBALS['bst_test_kyma_mode'] = 'stop';
bst_aid_rest_task_send( $request );
$response = bst_aid_rest_task_step( $request )->get_data();
bst_test( 'stopped' === $response['state'] && 'tool-error' === end( $response['events'] )['kind'], 'Stop during mock provider response blocks late tool write' );
bst_aid_undo_task( $run_task );
bst_test( get_option( 'blogdescription' ) === $old_tagline, 'Mock task undo restores original tagline' );

// Validate expiry, argument binding, cross-task and capability boundaries.
$GLOBALS['bst_aid_task_id'] = $task;
bst_aid_task_set( $task, '_bst_aid_run', array( 'active' => true ) );
$r = bst_aid_run_tool( 'site_settings', array( 'values' => array( 'blogname' => 'Do not apply' ) ) );
$pending = bst_aid_task_get( $task, '_bst_aid_approvals' );
$pending[ $r['approval']['id'] ]['expires'] = time() - 1;
bst_aid_task_set( $task, '_bst_aid_approvals', $pending );
$expired = new WP_REST_Request( 'POST' ); $expired['id'] = $task; $expired->set_param( 'approval_id', $r['approval']['id'] ); $expired->set_param( 'decision', 'approve' );
bst_test( is_wp_error( bst_aid_rest_approval( $expired ) ), 'Expired approval rejected' );
$GLOBALS['bst_aid_task_id'] = $task;
bst_test( isset( bst_aid_run_tool( 'install_plugin', array( 'slug' => 'https://example.com/plugin.zip' ) )['error'] ), 'External ZIP rejected before approval' );
bst_aid_task_set( $task, '_bst_aid_image_count', 3 );
bst_test( 2 === bst_aid_tool_policy( 'ImageGen', array() )['level'], 'Fourth image requires approval' );
bst_test( bst_aid_execution_lock( $task ) && ! bst_aid_execution_lock( $task ), 'Atomic task lease excludes a second executor' );
bst_aid_execution_unlock( $task );
$before_budget = bst_aid_settings()['budget_task'];
bst_test_approve( 'budget_extension', array( 'task_usd' => 1, 'day_usd' => 1 ) );
bst_test( abs( bst_aid_effective_budgets( $task )['budget_task'] - $before_budget - 1 ) < .000001 && bst_aid_settings()['budget_task'] === $before_budget, 'Budget extension is task-scoped and leaves global settings unchanged' );
bst_test( is_wp_error( bst_aid_tree_to_markup( array( array( 'name' => 'bigspy/expand-gallery', 'inner' => array() ) ) ) ), 'Gallery minimum count validated by AI builder' );
bst_test( false === strpos( bst_aid_clean_code( '<script>alert(1)</script><p onclick="alert(1)">Safe</p>' ), '<script' ) && false === strpos( bst_aid_clean_code( '<p onclick="alert(1)">Safe</p>' ), 'onclick' ), 'AI Code strips scripts and event handlers for administrators' );
$normalized = bst_aid_normalize_colors( array( 'backgroundColor' => '#0b192c', 'textColor' => '#ffffff' ) );
bst_test( ! isset( $normalized['backgroundColor'] ) && '#0b192c' === $normalized['style']['color']['background'], 'Raw hex colors become inline styles rather than invalid preset classes' );

// Render a page for browser checks, including counts, width variants and both directions/modes.
$html = '';
foreach ( array( 1, 3, 20 ) as $count ) {
    foreach ( array( 'left', 'right' ) as $direction ) {
        foreach ( array( 'loop', 'once' ) as $mode ) {
            $slides = array();
            for ( $i = 0; $i < $count; $i++ ) {
                $slides[] = array( 'name' => 'bigspy/ticker-slide', 'inner' => array( array( 'name' => 'bst/heading', 'attrs' => array( 'text' => 'Slide ' . ( $i + 1 ), 'tag' => 'h3' ) ) ) );
            }
            $built = bst_aid_tree_to_markup( array( array( 'name' => 'bigspy/ticker', 'attrs' => array( 'label' => "$count-$direction-$mode", 'widthDesktop' => '240px', 'widthMobile' => '75%', 'speed' => 500, 'direction' => $direction, 'mode' => $mode ), 'inner' => $slides ) ) );
            bst_test( ! is_wp_error( $built ), 'Ticker schema accepts ' . $count . ' slides' );
            $html .= $built['markup'];
        }
    }
}
foreach ( array( 2, 3, 4, 6 ) as $count ) {
    $panels = array();
    for ( $i = 0; $i < $count; $i++ ) {
        $panels[] = array( 'name' => 'bigspy/expand-panel', 'attrs' => array( 'title' => 'Panel ' . ( $i + 1 ) ), 'inner' => array( array( 'name' => 'bst/text', 'attrs' => array( 'html' => '<p>Panel content for testing.</p>' ) ) ) );
    }
    $built = bst_aid_tree_to_markup( array( array( 'name' => 'bigspy/expand-gallery', 'attrs' => array( 'label' => 'gallery-' . $count ), 'inner' => $panels ) ) );
    bst_test( ! is_wp_error( $built ), 'Gallery schema accepts ' . $count . ' panels' );
    $html .= $built['markup'];
}
$page = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Motion block verification', 'post_content' => wp_slash( $html ), 'page_template' => 'templates/full-width.php' ) );
update_option( 'show_on_front', 'page' ); update_option( 'page_on_front', $page );
update_option( 'bst_test_checks', $checks );
$uploads = wp_upload_dir();
file_put_contents( $uploads['basedir'] . '/bst-verification.json', wp_json_encode( array( 'passed' => count( $checks ), 'checks' => $checks ), JSON_PRETTY_PRINT ) );
echo wp_json_encode( array( 'passed' => count( $checks ), 'page' => $page, 'checks' => $checks ), JSON_PRETTY_PRINT );
