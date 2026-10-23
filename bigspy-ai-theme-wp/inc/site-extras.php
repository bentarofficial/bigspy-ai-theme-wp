<?php
/**
 * Site extras (Flatsome extensions parity): cookie notice, instant page
 * prefetch and a reading progress bar on posts.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_defaults',
	function ( $defaults ) {
		return $defaults + array(
			'cookie_notice'        => false,
			'cookie_text'          => 'Website dùng cookie để ghi nhớ lựa chọn của bạn và cải thiện trải nghiệm.',
			'cookie_button'        => 'Đồng ý',
			'cookie_link'          => '',
			'cookie_link_text'     => 'Chính sách bảo mật',
			'cookie_position'      => 'bottom',
			'instant_page'         => false,
			'reading_progress'     => false,
		);
	}
);

add_filter(
	'bst_customizer_sections',
	function ( $sections ) {
		$sections['bst_site_extras'] = array( 'title' => __( 'Cookie notice, speed & reading', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_advanced', 'priority' => 4 );
		return $sections;
	}
);

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$s        = 'bst_site_extras';
		$fields[] = array( 'id' => 'bst_g_cookie', 'section' => $s, 'type' => 'heading', 'label' => __( 'Cookie notice', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'cookie_notice', 'section' => $s, 'type' => 'checkbox', 'label' => __( 'Show a cookie notice until the visitor accepts', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'cookie_text', 'section' => $s, 'type' => 'text', 'label' => __( 'Message', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'cookie_button', 'section' => $s, 'type' => 'text', 'label' => __( 'Button text', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'cookie_link', 'section' => $s, 'type' => 'text', 'label' => __( 'Policy page URL (optional)', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'cookie_link_text', 'section' => $s, 'type' => 'text', 'label' => __( 'Policy link text', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'cookie_position', 'section' => $s, 'type' => 'select', 'label' => __( 'Position', 'bigspy-ai-theme-wp' ), 'choices' => array( 'bottom' => __( 'Bottom, centered', 'bigspy-ai-theme-wp' ), 'left' => __( 'Bottom left box', 'bigspy-ai-theme-wp' ) ) );
		$fields[] = array( 'id' => 'bst_g_speed', 'section' => $s, 'type' => 'heading', 'label' => __( 'Speed & reading', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'instant_page', 'section' => $s, 'type' => 'checkbox', 'label' => __( 'Instant page: preload a page when the visitor hovers or touches its link', 'bigspy-ai-theme-wp' ), 'description' => __( 'Pages open almost instantly. Skipped on data-saver connections, admin, cart and file links.', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'reading_progress', 'section' => $s, 'type' => 'checkbox', 'label' => __( 'Reading progress bar on blog posts', 'bigspy-ai-theme-wp' ) );
		return $fields;
	}
);

add_filter(
	'body_class',
	function ( $classes ) {
		if ( bst_mod( 'instant_page' ) ) {
			$classes[] = 'bs-instant-page';
		}
		return $classes;
	}
);

add_action(
	'wp_footer',
	function () {
		if ( bst_mod( 'reading_progress' ) && is_singular( 'post' ) ) {
			echo '<div class="bs-reading-progress" aria-hidden="true"></div>';
		}
		if ( bst_mod( 'cookie_notice' ) ) {
			$link = esc_url( bst_mod( 'cookie_link' ) );
			printf(
				'<div class="bs-cookie bs-cookie--%1$s" role="region" aria-label="%2$s" hidden><p>%3$s%4$s</p><button type="button" class="bs-btn bs-btn--sm" data-bs-cookie-ok>%5$s</button></div>',
				esc_attr( bst_choice( bst_mod( 'cookie_position' ), array( 'bottom', 'left' ), 'bottom' ) ),
				esc_attr__( 'Cookie notice', 'bigspy-ai-theme-wp' ),
				esc_html( bst_mod( 'cookie_text' ) ),
				$link ? ' <a href="' . $link . '">' . esc_html( bst_mod( 'cookie_link_text' ) ) . '</a>' : '', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				esc_html( bst_mod( 'cookie_button' ) )
			);
		}
	},
	20
);
