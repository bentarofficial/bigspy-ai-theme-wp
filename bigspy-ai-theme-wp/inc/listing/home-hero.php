<?php
/**
 * Homepage hero from the old BIGSPY AI List theme. After switching themes the hero
 * keeps working: its editor content, background and destination buttons are read
 * from the old theme's saved settings, so nothing needs to be entered again.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_feature_fields',
	function ( $fields ) {
		$fields['listing']['home_hero'] = array( 'type' => 'checkbox', 'label' => __( 'Homepage hero from the BIGSPY AI List theme (content, background and buttons saved in that theme)', 'bigspy-ai-theme-wp' ), 'default' => 1 );
		return $fields;
	}
);

/** A setting saved by the old BIGSPY AI List theme. */
function bst_legacy_list_mod( $key, $default = null ) {
	$mods = get_option( 'theme_mods_bigspy-ai-list', array() );
	$mods = is_array( $mods ) ? $mods : array();
	return array_key_exists( $key, $mods ) ? $mods[ $key ] : $default;
}

/** Destination buttons: saved list, or the five directory pages. */
function bst_legacy_home_destinations() {
	$saved = bst_legacy_list_mod( 'bigspy_home_destination_buttons', null );
	if ( ! is_array( $saved ) ) {
		$shop  = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/listings/' );
		$saved = array(
			array( 'label' => __( 'All Listings', 'bigspy-ai-theme-wp' ), 'url' => $shop ),
			array( 'label' => __( 'Categories', 'bigspy-ai-theme-wp' ), 'url' => home_url( '/categories/' ) ),
			array( 'label' => __( 'Featured', 'bigspy-ai-theme-wp' ), 'url' => home_url( '/featured/' ) ),
			array( 'label' => __( 'Popular', 'bigspy-ai-theme-wp' ), 'url' => home_url( '/popular/' ) ),
			array( 'label' => __( 'Blog', 'bigspy-ai-theme-wp' ), 'url' => home_url( '/blog/' ) ),
		);
	}
	$html = '';
	foreach ( $saved as $item ) {
		$label = is_array( $item ) ? sanitize_text_field( $item['label'] ?? '' ) : '';
		$url   = is_array( $item ) ? esc_url( $item['url'] ?? '', array( 'http', 'https' ) ) : '';
		if ( '' !== $label && '' !== $url ) {
			$html .= '<a class="bigspy-home-hero-destinations__button" href="' . $url . '">' . esc_html( $label ) . '</a>';
		}
	}
	return '' === $html ? '' : '<nav class="bigspy-home-hero-destinations" aria-label="' . esc_attr( sprintf( /* translators: %s: site name */ __( 'Explore %s', 'bigspy-ai-theme-wp' ), get_bloginfo( 'name' ) ) ) . '">' . $html . '</nav>';
}
if ( ! shortcode_exists( 'bigspy_home_destination_buttons' ) ) {
	add_shortcode( 'bigspy_home_destination_buttons', 'bst_legacy_home_destinations' );
}

/** {{listing_count}} and the other counters the old hero editor offered. */
function bst_legacy_hero_tokens( $content ) {
	if ( false === strpos( $content, '{{' ) ) {
		return $content;
	}
	$sponsored = function_exists( 'bst_sponsored_items' ) ? count( bst_sponsored_items() ) : 0;
	$terms     = taxonomy_exists( 'product_cat' ) ? (int) wp_count_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) ) : 0;
	$users     = count_users();
	$current   = ( is_tax() || is_category() ) && isset( get_queried_object()->count ) ? (int) get_queried_object()->count : 0;
	return strtr(
		$content,
		array(
			'{{listing_count}}'          => number_format_i18n( (int) ( wp_count_posts( 'product' )->publish ?? 0 ) ),
			'{{category_count}}'         => number_format_i18n( $terms ),
			'{{sponsored_count}}'        => number_format_i18n( $sponsored ),
			'{{post_count}}'             => number_format_i18n( (int) ( wp_count_posts( 'post' )->publish ?? 0 ) ),
			'{{user_count}}'             => number_format_i18n( (int) ( $users['total_users'] ?? 0 ) ),
			'{{current_category_count}}' => number_format_i18n( $current ),
		)
	);
}

/** Hero HTML, or '' when the old theme left none or it is turned off. */
function bst_legacy_home_hero_html() {
	if ( ! bst_feature_get( 'listing', 'home_hero', 1 ) || false === get_option( 'theme_mods_bigspy-ai-list', false ) || ! bst_legacy_list_mod( 'bigspy_home_show_hero', true ) ) {
		return '';
	}
	$content = (string) bst_legacy_list_mod( 'bigspy_hero_visual_content', '' );
	if ( '' === trim( $content ) ) {
		if ( bst_legacy_list_mod( 'bigspy_hero_visual_initialized', false ) ) {
			return '';
		}
		$content = '<p class="bigspy-eyebrow">' . esc_html( bst_legacy_list_mod( 'bigspy_hero_eyebrow', 'BIGSPY AI LIST' ) ) . '</p><h1>' . esc_html( bst_legacy_list_mod( 'bigspy_hero_title', __( 'Discover the best tools, software and services.', 'bigspy-ai-theme-wp' ) ) ) . '</h1><p>' . esc_html( bst_legacy_list_mod( 'bigspy_hero_description', '' ) ) . '</p><p>[bigspy_search]</p>';
	}
	$allowed = wp_kses_allowed_html( 'post' );
	foreach ( array( 'h1', 'h2', 'h3', 'h4', 'p', 'span', 'div', 'a', 'strong', 'em', 'img', 'nav' ) as $tag ) {
		$allowed[ $tag ]          = $allowed[ $tag ] ?? array();
		$allowed[ $tag ]['class'] = true;
		$allowed[ $tag ]['style'] = true;
	}
	$content = wp_kses( $content, $allowed );
	// The "Sponsored by" brand link becomes the first Sponsored listing, as in the old theme.
	$content = preg_replace( '/<a\b[^>]*class=["\'][^"\']*\bbigspy-sponsor-brand\b[^"\']*["\'][^>]*>.*?<\/a>/is', '[bigspy_home_sponsor]', $content );
	$content = do_shortcode( bst_legacy_hero_tokens( $content ) );

	$num   = function ( $key, $def, $min, $max ) {
		return max( $min, min( $max, (int) bst_legacy_list_mod( $key, $def ) ) );
	};
	$align = bst_legacy_list_mod( 'bigspy_hero_align', 'center' );
	$align = in_array( $align, array( 'left', 'center', 'right' ), true ) ? $align : 'center';
	$style = '--bst-lh-align:' . $align . ';--bst-lh-pad:' . $num( 'bigspy_hero_padding', 48, 0, 220 ) . 'px;--bst-lh-max:' . $num( 'bigspy_hero_title_max_width', 1000, 400, 1600 ) . 'px;--bst-lh-blur:' . $num( 'bigspy_hero_background_blur', 0, 0, 40 ) . 'px;';
	$over  = sanitize_hex_color( (string) bst_legacy_list_mod( 'bigspy_hero_background_overlay', '#000000' ) );
	$style .= '--bst-lh-overlay:' . ( $over ? $over : '#000000' ) . ';--bst-lh-overlay-o:' . ( $num( 'bigspy_hero_background_overlay_opacity', 0, 0, 100 ) / 100 ) . ';';
	$bg     = wp_get_attachment_image_url( absint( bst_legacy_list_mod( 'bigspy_hero_background_image_id', 0 ) ), 'full' );
	if ( $bg ) {
		$style .= '--bst-lh-bg:url("' . esc_url( $bg ) . '");';
	}
	return '<section class="bst-legacy-hero bigspy-home-hero-visual" style="' . esc_attr( $style ) . '"><div class="bst-legacy-hero__bg" aria-hidden="true"></div><div class="bst-legacy-hero__overlay" aria-hidden="true"></div><div class="bst-legacy-hero__content">' . $content . '</div></section>';
}

add_action(
	'bst_before_layout',
	function () {
		if ( is_front_page() && is_page() ) {
			echo bst_legacy_home_hero_html(); // phpcs:ignore WordPress.Security.EscapeOutput -- sanitized while building.
		}
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( ! is_front_page() ) {
			return;
		}
		wp_add_inline_style(
			'bst-main',
			'.bst-legacy-hero{position:relative;overflow:hidden;padding:var(--bst-lh-pad) 16px;background:#f8fafc;text-align:var(--bst-lh-align)}'
			. '.bst-legacy-hero__bg{position:absolute;inset:calc(var(--bst-lh-blur) * -2);background:var(--bst-lh-bg,none) center/cover no-repeat;filter:blur(var(--bst-lh-blur))}'
			. '.bst-legacy-hero__overlay{position:absolute;inset:0;background:var(--bst-lh-overlay);opacity:var(--bst-lh-overlay-o)}'
			. '.bst-legacy-hero__content{position:relative;max-width:var(--bst-lh-max);margin:0 auto}'
			. '.bst-legacy-hero h1{margin:0 0 14px;color:#036;font-size:clamp(34px,5vw,50px);font-weight:800;line-height:1.05}'
			. '.bst-legacy-hero h2{margin:0 auto 16px;font-size:clamp(22px,3vw,31px);font-weight:600;line-height:1.25}'
			. '.bst-legacy-hero .bigspy-sponsor-label{display:inline-flex;align-items:center;gap:8px;padding:7px 9px 7px 17px;border:1px solid rgba(183,130,61,.25);border-radius:999px;background:#fffaf3;color:#64748b;font-size:13px}'
			. '.bst-legacy-hero .bst-home-sponsor,.bst-legacy-hero .bigspy-sponsor-brand{padding:4px 12px;border-radius:999px;background:#f4e7d4;color:#80551f;font-weight:700;text-decoration:none}'
			. '.bst-legacy-hero form{max-width:760px;margin-inline:auto}'
			. '.bigspy-home-hero-destinations{display:flex;flex-wrap:wrap;justify-content:center;gap:10px;margin-top:24px}'
			. '.bigspy-home-hero-destinations__button{padding:5px 14px;border:1px solid rgba(0,51,102,.2);border-radius:999px;background:rgba(255,255,255,.9);color:#036;font-size:12.5px;font-weight:700;text-decoration:none}'
			. '.bigspy-home-hero-destinations__button:hover{border-color:#008080;color:#008080}'
		);
	},
	31
);
