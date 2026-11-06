<?php
/**
 * Sponsored slots and the AD switch. A listing is advertised when its AD switch is on
 * (_bigspy_ad = 1, set in Products → All Products or the listing box). Each slot shows
 * one sponsor and rotates through the pool on every visit.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_feature_fields',
	function ( $fields ) {
		$old = bst_sponsored_old_config();
		$fields['listing'] = array_merge(
			$fields['listing'] ?? array(),
			array(
				'sponsored_on'     => array( 'type' => 'checkbox', 'label' => __( 'Show Sponsored slots', 'bigspy-ai-theme-wp' ), 'default' => empty( $old['sponsored_enabled'] ) ? 0 : 1, 'help' => __( 'Listings with the AD switch on rotate through the Sponsored slots. Their button opens the partner site directly.', 'bigspy-ai-theme-wp' ) ),
				'sponsored_button' => array( 'type' => 'text', 'label' => __( 'Sponsored button text', 'bigspy-ai-theme-wp' ), 'default' => $old['button_text'] ?? __( 'Open site →', 'bigspy-ai-theme-wp' ) ),
				'sponsored_tab'    => array( 'type' => 'checkbox', 'label' => __( 'Open sponsors in a new tab', 'bigspy-ai-theme-wp' ), 'default' => isset( $old['open_new_tab'] ) && ! $old['open_new_tab'] ? 0 : 1 ),
				'sponsored_jump'   => array( 'type' => 'number', 'label' => __( 'Sponsors to skip on each visit', 'bigspy-ai-theme-wp' ), 'min' => 1, 'max' => 20, 'default' => (int) ( $old['rotation_jump'] ?? 3 ) ),
			)
		);
		return $fields;
	}
);

/** Settings saved by the old theme; used as defaults so a switched site keeps them. */
function bst_sponsored_old_config() {
	$old = get_option( 'bigspy_categories_promotions', array() );
	return is_array( $old ) ? $old : array();
}

function bst_sponsored_config() {
	$old = bst_sponsored_old_config();
	return array(
		'on'      => (bool) bst_feature_get( 'listing', 'sponsored_on', empty( $old['sponsored_enabled'] ) ? 0 : 1 ),
		'button'  => (string) bst_feature_get( 'listing', 'sponsored_button', $old['button_text'] ?? __( 'Open site →', 'bigspy-ai-theme-wp' ) ),
		'tab'     => (bool) bst_feature_get( 'listing', 'sponsored_tab', isset( $old['open_new_tab'] ) && ! $old['open_new_tab'] ? 0 : 1 ),
		'jump'    => max( 1, min( 20, (int) bst_feature_get( 'listing', 'sponsored_jump', $old['rotation_jump'] ?? 3 ) ) ),
		'version' => max( 1, absint( $old['rotation_version'] ?? 1 ) ),
	);
}

function bst_listing_is_ad( $post_id ) {
	return '1' === (string) get_post_meta( $post_id, '_bigspy_ad', true );
}

/** AD listings with a partner URL: the Sponsored button never opens the listing page. */
function bst_sponsored_items() {
	static $items = null;
	if ( null !== $items ) {
		return $items;
	}
	$items = array();
	$ids   = get_posts(
		array(
			'post_type'        => 'product',
			'post_status'      => 'publish',
			'posts_per_page'   => 50,
			'fields'           => 'ids',
			'meta_key'         => '_bigspy_ad', // phpcs:ignore WordPress.DB.SlowDBQuery -- small, indexed set.
			'meta_value'       => '1', // phpcs:ignore WordPress.DB.SlowDBQuery
			'orderby'          => 'title',
			'order'            => 'ASC',
			'no_found_rows'    => true,
			'suppress_filters' => false,
		)
	);
	foreach ( $ids as $post_id ) {
		$url = bst_listing_url( $post_id );
		if ( '' === $url || get_permalink( $post_id ) === $url ) {
			continue;
		}
		$text    = trim( wp_strip_all_tags( (string) get_post_field( 'post_excerpt', $post_id ) ) );
		$text    = '' !== $text ? $text : trim( wp_strip_all_tags( (string) bst_listing_field( 'tool_tagline', $post_id, '' ) ) );
		$items[] = array(
			'id'          => (int) $post_id,
			'name'        => wp_strip_all_tags( get_the_title( $post_id ) ),
			'description' => wp_trim_words( $text, 22, '…' ),
			'url'         => esc_url_raw( $url ),
			'logo'        => (string) get_the_post_thumbnail_url( $post_id, 'thumbnail' ),
		);
	}
	return $items;
}

/** New order for visitors and cached pages after the AD set changes. Option key kept from the old theme. */
function bst_sponsored_refresh_rotation() {
	$stored                     = bst_sponsored_old_config();
	$stored['rotation_version'] = max( 1, absint( $stored['rotation_version'] ?? 1 ) + 1 );
	update_option( 'bigspy_categories_promotions', $stored, false );
	do_action( 'litespeed_purge_all' );
}
add_action( 'bst_listing_ad_changed', 'bst_sponsored_refresh_rotation' );

/**
 * One Sponsored card. $slot is its position in a group of slots, $context groups slots
 * that rotate together (categories, single, shortcode, elements).
 */
function bst_sponsored_slot( $slot = 0, $context = 'elements' ) {
	static $pool = false;
	$config = bst_sponsored_config();
	$items  = bst_sponsored_items();
	if ( ! $config['on'] || ! $items ) {
		return '';
	}
	$context = in_array( $context, array( 'categories', 'single', 'shortcode' ), true ) ? $context : 'elements';
	$item    = $items[ absint( $slot ) % count( $items ) ];
	$rel     = $config['tab'] ? ' target="_blank" rel="sponsored nofollow noopener"' : ' rel="sponsored nofollow"';
	$logo    = '' !== $item['logo'] ? '<img src="' . esc_url( $item['logo'] ) . '" alt="" loading="lazy">' : esc_html( strtoupper( mb_substr( $item['name'], 0, 1 ) ) );
	$out     = '<aside class="bst-sponsored" data-bst-sponsored="' . absint( $slot ) . '" data-context="' . esc_attr( $context ) . '" data-version="' . (int) $config['version'] . '" data-jump="' . (int) $config['jump'] . '" aria-label="' . esc_attr__( 'Sponsored', 'bigspy-ai-theme-wp' ) . '">';
	$out    .= '<span class="bst-sponsored__label">' . esc_html__( 'Sponsored', 'bigspy-ai-theme-wp' ) . '</span>';
	$out    .= '<span class="bst-sponsored__logo" data-logo aria-hidden="true">' . $logo . '</span>';
	$out    .= '<span class="bst-sponsored__text"><strong data-name>' . esc_html( $item['name'] ) . '</strong><span data-desc>' . esc_html( $item['description'] ) . '</span></span>';
	$out    .= '<a class="bst-sponsored__button" data-link href="' . esc_url( $item['url'] ) . '"' . $rel . '>' . esc_html( $config['button'] ) . '</a>';
	if ( ! $pool ) {
		$pool = true;
		$data = array_map(
			function ( $i ) {
				return array_intersect_key( $i, array_flip( array( 'name', 'description', 'url', 'logo' ) ) );
			},
			$items
		);
		$out .= '<script type="application/json" data-bst-sponsored-pool>' . wp_json_encode( $data, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
		wp_enqueue_script( 'bst-sponsored', BST_URI . '/assets/js/sponsored.js', array(), bst_asset_version( 'assets/js/sponsored.js' ), true );
	}
	wp_enqueue_style( 'bst-listing', BST_URI . '/assets/css/listing.css', array(), bst_asset_version( 'assets/css/listing.css' ) );
	return $out . '</aside>';
}

/** [bigspy_sponsored_grid]: up to six rotating Sponsored cards (name kept from the old theme). */
function bst_sponsored_grid() {
	$count = min( 6, count( bst_sponsored_items() ) );
	if ( ! bst_sponsored_config()['on'] || ! $count ) {
		return '';
	}
	$out = '';
	for ( $i = 0; $i < $count; $i++ ) {
		$out .= bst_sponsored_slot( $i, 'shortcode' );
	}
	return '<div class="bst-sponsored-grid">' . $out . '</div>';
}
add_shortcode( 'bigspy_sponsored_grid', 'bst_sponsored_grid' );

// One sponsor under the listing summary on product pages, never the listing itself.
add_action(
	'woocommerce_after_single_product_summary',
	function () {
		if ( ! bst_listing_is_ad( get_the_ID() ) ) {
			echo bst_sponsored_slot( 0, 'single' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped while building.
		}
	},
	11
);

/* AD column in Products → All Products: one click adds or removes a listing from the Sponsored slots. */
add_filter(
	'manage_edit-product_columns',
	function ( $columns ) {
		$out = array();
		foreach ( $columns as $key => $label ) {
			$out[ $key ] = $label;
			if ( 'featured' === $key ) {
				$out['bigspy_ad'] = '<span title="' . esc_attr__( 'Sponsored (AD)', 'bigspy-ai-theme-wp' ) . '">AD</span>';
			}
		}
		$out += array( 'bigspy_ad' => 'AD' );
		return $out;
	},
	25
);

add_action(
	'manage_product_posts_custom_column',
	function ( $column, $post_id ) {
		if ( 'bigspy_ad' !== $column ) {
			return;
		}
		$on    = bst_listing_is_ad( $post_id );
		$class = 'bst-ad' . ( $on ? ' is-on' : '' );
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			echo '<span class="' . esc_attr( $class ) . '">AD</span>';
			return;
		}
		$label = $on ? __( 'Remove from Sponsored', 'bigspy-ai-theme-wp' ) : __( 'Add to Sponsored', 'bigspy-ai-theme-wp' );
		if ( $on && bst_listing_url( $post_id ) === get_permalink( $post_id ) ) {
			$label  = __( 'On, but hidden: this listing has no partner URL', 'bigspy-ai-theme-wp' );
			$class .= ' is-warning';
		}
		$link = wp_nonce_url( admin_url( 'admin-ajax.php?action=bigspy_toggle_ad&product_id=' . absint( $post_id ) ), 'bigspy-toggle-ad_' . absint( $post_id ) );
		echo '<a href="' . esc_url( $link ) . '" class="' . esc_attr( $class ) . '" title="' . esc_attr( $label ) . '" aria-label="' . esc_attr( $label ) . '" aria-pressed="' . ( $on ? 'true' : 'false' ) . '">AD</a>';
	},
	10,
	2
);

add_action(
	'admin_head-edit.php',
	function () {
		if ( 'product' === get_current_screen()->post_type ) {
			echo '<style>.column-bigspy_ad{width:52px;text-align:center}.bst-ad{display:inline-block;min-width:30px;padding:3px 6px;border:1px solid #c3c4c7;border-radius:5px;background:#f6f7f7;color:#a7aaad;font-size:11px;font-weight:700;text-decoration:none}.bst-ad:hover{border-color:#f59e0b;color:#b45309}.bst-ad.is-on{border-color:#f59e0b;background:#fbbf24;color:#1f2937}.bst-ad.is-warning{border-style:dashed;background:#fef3c7}</style>';
		}
	}
);

/** Toggle one listing's AD switch; same action name and nonce as the old theme. */
function bst_toggle_listing_ad( $post_id ) {
	if ( bst_listing_is_ad( $post_id ) ) {
		delete_post_meta( $post_id, '_bigspy_ad' );
	} else {
		update_post_meta( $post_id, '_bigspy_ad', '1' );
	}
	bst_sponsored_refresh_rotation();
}

add_action(
	'wp_ajax_bigspy_toggle_ad',
	function () {
		$post_id = absint( $_GET['product_id'] ?? 0 );
		check_admin_referer( 'bigspy-toggle-ad_' . $post_id );
		if ( ! $post_id || 'product' !== get_post_type( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( esc_html__( 'You are not allowed to change this listing.', 'bigspy-ai-theme-wp' ) );
		}
		bst_toggle_listing_ad( $post_id );
		$back = wp_get_referer();
		wp_safe_redirect( $back ? $back : admin_url( 'edit.php?post_type=product' ) );
		exit;
	}
);

// Names the old plugins and theme parts call.
if ( ! function_exists( 'bigspy_listing_is_ad' ) ) {
	function bigspy_listing_is_ad( $post_id ) {
		return bst_listing_is_ad( $post_id );
	}
}
if ( ! function_exists( 'bigspy_sponsored_refresh_rotation' ) ) {
	function bigspy_sponsored_refresh_rotation() {
		bst_sponsored_refresh_rotation();
	}
}
