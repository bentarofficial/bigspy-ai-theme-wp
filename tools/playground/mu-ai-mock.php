<?php
/**
 * Playground-only AI provider mock. tools/playground-page.cjs mounts this file with
 * --mock-ai; it is outside the theme folder and never ships in a ZIP.
 *
 * Connects the AI Designer with a fixture key and answers every Kyma request locally
 * with a short scripted run (read site, palettes, page plan, draft page, summary), so
 * the Workspace can be opened and exercised without a real key or provider charges.
 */
add_action(
	'init',
	function () {
		$host = wp_parse_url( home_url(), PHP_URL_HOST );
		if ( ! function_exists( 'bst_aid_save_settings' ) || ! in_array( $host, array( '127.0.0.1', 'localhost' ), true ) ) {
			return;
		}
		if ( ! get_option( 'bst_aid_mock_ready' ) ) {
			bst_aid_save_settings( array( 'connected' => true, 'text_model' => 'mock-model', 'budget_task' => 2, 'budget_day' => 10 ) );
			update_option( 'bst_aid_key', bst_aid_encrypt( 'local-test-fixture-only' ) );
			update_option( 'bst_aid_mock_ready', 1 );
		}
		set_transient( 'bst_aid_models', array( 'text' => array( array( 'id' => 'mock-model', 'name' => 'Mock model', 'input' => 0, 'output' => 0 ) ), 'image' => array() ), 3600 );
	},
	20
);

add_filter(
	'pre_http_request',
	function ( $result, $args, $url ) {
		if ( false === strpos( $url, 'kymaapi.com/' ) ) {
			return $result;
		}
		$ok = function ( $data ) {
			return array( 'headers' => array(), 'response' => array( 'code' => 200, 'message' => 'OK' ), 'body' => wp_json_encode( $data ) );
		};
		if ( false !== strpos( $url, '/credits/balance' ) ) {
			return $ok( array( 'balance' => 12.5 ) );
		}
		if ( false === strpos( $url, '/chat/completions' ) ) {
			return $ok( array( 'data' => array() ) );
		}
		$body  = json_decode( (string) ( $args['body'] ?? '' ), true );
		$msgs  = (array) ( $body['messages'] ?? array() );
		// Tool calls the model already made since the last user message pick the next scripted reply.
		$called = array();
		$ask    = '';
		foreach ( $msgs as $m ) {
			if ( 'user' === ( $m['role'] ?? '' ) ) {
				$called = array();
				$ask    = strtolower( (string) ( is_string( $m['content'] ?? null ) ? $m['content'] : '' ) );
			}
			foreach ( (array) ( $m['tool_calls'] ?? array() ) as $c ) {
				$called[] = (string) ( $c['function']['name'] ?? '' );
			}
		}
		$turns = count( $called );
		if ( defined( 'BST_AI_MOCK_LOG' ) ) {
			error_log( 'mock ' . wp_json_encode( array( 'model' => $body['model'] ?? '', 'called' => $called ) ) . "\n", 3, BST_AI_MOCK_LOG );
		}
		$call = function ( $name, $params ) use ( $ok, $turns ) {
			return $ok(
				array(
					'choices' => array( array( 'message' => array( 'role' => 'assistant', 'content' => null, 'tool_calls' => array( array( 'id' => 'mock-' . $turns, 'type' => 'function', 'function' => array( 'name' => $name, 'arguments' => wp_json_encode( $params ) ) ) ) ) ) ),
					'usage'   => array( 'cost' => 0.002 ),
				)
			);
		};
		usleep( 600000 ); // A little latency so progress states are visible.
		$says = function ( $text, $cost = 0.003 ) use ( $ok ) {
			return $ok( array( 'choices' => array( array( 'message' => array( 'role' => 'assistant', 'content' => $text ) ) ), 'usage' => array( 'cost' => $cost ) ) );
		};
		// Scenarios picked by a keyword in the user's message.
		if ( false !== strpos( $ask, 'error' ) ) {
			return array( 'headers' => array(), 'response' => array( 'code' => 500, 'message' => 'Error' ), 'body' => '{"error":{"message":"Mock provider error"}}' );
		}
		if ( false !== strpos( $ask, 'approval' ) ) {
			return $turns ? $says( 'The tagline change is waiting for your approval.' ) : $call( 'site_settings', array( 'values' => array( 'blogdescription' => 'Tagline written by the mock AI' ) ) );
		}
		if ( false !== strpos( $ask, 'budget' ) ) {
			return $ok( array( 'choices' => array( array( 'message' => array( 'role' => 'assistant', 'content' => null, 'tool_calls' => array( array( 'id' => 'mock-b' . $turns, 'type' => 'function', 'function' => array( 'name' => 'site_info', 'arguments' => '{}' ) ) ) ) ) ), 'usage' => array( 'cost' => 1.5 ) ) );
		}
		if ( false !== strpos( $ask, 'long' ) ) {
			return $says( "## A long answer\n\nThis reply checks how the chat shows **long** content.\n\n| Section | Purpose | Blocks |\n| --- | --- | --- |\n| Hero | First impression | banner, button |\n| Services | What you offer | icon-box ×3 |\n| Contact | Next step | form, map |\n\n1. First step\n   - nested point\n   - another point\n2. Second step\n\n```css\n.hero { min-height: 80vh; }\n```\n\n" . str_repeat( 'A sentence that makes the answer longer so the chat has to scroll. ', 40 ) );
		}
		switch ( $turns ) {
			case 0:
				return $call( 'site_info', new stdClass() );
			case 1:
				return $call(
					'show_palette',
					array(
						'palettes' => array(
							array( 'name' => 'Ocean', 'colors' => array( 'primary' => '#2563eb', 'secondary' => '#06b6d4', 'heading' => '#0f172a', 'text' => '#334155', 'background' => '#f8fafc' ), 'fonts' => array( 'heading' => 'Sora', 'body' => 'Inter' ) ),
							array( 'name' => 'Sunset', 'colors' => array( 'primary' => '#ea580c', 'secondary' => '#f59e0b', 'heading' => '#1c1917', 'text' => '#44403c', 'background' => '#fffbeb' ), 'fonts' => array( 'heading' => 'Playfair Display', 'body' => 'Inter' ) ),
						),
					)
				);
			case 2:
				return $call(
					'page_plan',
					array(
						'page'     => 'Home',
						'sections' => array(
							array( 'title' => 'Hero', 'purpose' => 'Say what the company does and offer one action' ),
							array( 'title' => 'Services', 'purpose' => 'Three services with icons' ),
							array( 'title' => 'Testimonials', 'purpose' => 'Build trust with customer quotes' ),
							array( 'title' => 'Contact', 'purpose' => 'Form and map' ),
						),
					)
				);
			case 3:
				return $call(
					'create_page',
					array(
						'title'  => 'Mock home page',
						'blocks' => array(
							array( 'name' => 'bst/heading', 'attrs' => array( 'text' => 'A page built by the mock AI', 'tag' => 'h1' ) ),
							array( 'name' => 'bst/text', 'attrs' => array( 'html' => '<p>This draft exists so the Workspace preview has something to show.</p>' ) ),
						),
					)
				);
		}
		return $ok(
			array(
				'choices' => array( array( 'message' => array( 'role' => 'assistant', 'content' => "## Done\n\nI read the site, proposed **two palettes**, planned the page and saved a draft.\n\n- Review the draft in the preview\n- Tell me which palette you prefer\n- Say `publish` when it is ready" ) ) ),
				'usage'   => array( 'cost' => 0.003 ),
			)
		);
	},
	10,
	3
);

/* ?bst_mock_notices=1 adds three admin notices, to check that the AI Designer bell collects them. */
add_action(
	'admin_notices',
	function () {
		if ( empty( $_GET['bst_mock_notices'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}
		foreach ( array( 'warning', 'error', 'info' ) as $i => $type ) {
			echo '<div class="notice notice-' . esc_attr( $type ) . ' is-dismissible"><p>Mock plugin notice ' . (int) ( $i + 1 ) . ': this would normally push the layout down.</p></div>';
		}
	}
);
