<?php
/**
 * More shop, product page and sidebar options (compared with Flatsome, Astra, OceanWP, Neve).
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_defaults',
	function ( $d ) {
		return $d + array(
			'shop_result_count'     => true,
			'shop_ordering'         => true,
			'shop_card_align'       => 'left',
			'shop_show_rating'      => true,
			'shop_empty_rating'     => false,
			'shop_short_desc'       => false,
			'shop_short_desc_words' => 14,
			'shop_title_size'       => 0,
			'shop_card_radius'      => -1,
			'shop_card_padding'     => -1,
			'shop_price_color'      => '',
			'shop_cat_count'        => true,
			'shop_out_of_stock'     => 'badge',
			'badge_sale_text'       => '',
			'badge_out_text'        => '',
			'badge_new_text'        => '',
			'badge_hot_text'        => '',
			'product_image_width'   => 50,
			'product_zoom'          => true,
			'product_lightbox'      => true,
			'product_text_align'    => 'left',
			'product_title_size'    => 0,
			'product_breadcrumb'    => true,
			'product_nav'           => false,
			'product_meta'          => true,
			'product_share'         => false,
			'product_shipping_text' => '',
			'product_tabs_align'    => 'left',
			'product_related'       => true,
			'product_upsells'       => true,
			'related_count'         => 0,
			'related_title'         => '',
			'sidebar_bg'            => '',
			'sidebar_padding'       => -1,
			'sidebar_radius'        => -1,
			'sidebar_title_size'    => 0,
			'sidebar_title_color'   => '',
			'sidebar_link_color'    => '',
		);
	}
);

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$sh = 'bst_shop';
		$pr = 'bst_product';
		$sb = 'bst_sidebar';
		$f  = array();
		$n  = function ( $id, $sec, $label, $min, $max ) {
			return array( 'id' => $id, 'section' => $sec, 'type' => 'number', 'label' => $label, 'input_attrs' => array( 'min' => $min, 'max' => $max ) );
		};
		$c  = function ( $id, $sec, $label ) {
			return array( 'id' => $id, 'section' => $sec, 'type' => 'checkbox', 'label' => $label );
		};

		// Shop page.
		$f[] = array( 'id' => 'bst_g_shop_toolbar', 'section' => $sh, 'type' => 'heading', 'label' => __( 'Toolbar above products', 'bigspy-ai-theme-wp' ) );
		$f[] = $c( 'shop_result_count', $sh, __( 'Show result count', 'bigspy-ai-theme-wp' ) );
		$f[] = $c( 'shop_ordering', $sh, __( 'Show sorting dropdown', 'bigspy-ai-theme-wp' ) );
		$f[] = $c( 'shop_cat_count', $sh, __( 'Show product count on category boxes', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'bst_g_shop_card', 'section' => $sh, 'type' => 'heading', 'label' => __( 'Product cards', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'shop_card_align', 'section' => $sh, 'type' => 'select', 'label' => __( 'Text alignment', 'bigspy-ai-theme-wp' ), 'choices' => array( 'left' => __( 'Left', 'bigspy-ai-theme-wp' ), 'center' => __( 'Center', 'bigspy-ai-theme-wp' ), 'right' => __( 'Right', 'bigspy-ai-theme-wp' ) ) );
		$f[] = $c( 'shop_show_rating', $sh, __( 'Show star rating', 'bigspy-ai-theme-wp' ) );
		$f[] = $c( 'shop_empty_rating', $sh, __( 'Show empty stars when there are no reviews', 'bigspy-ai-theme-wp' ) );
		$f[] = $c( 'shop_short_desc', $sh, __( 'Show short description', 'bigspy-ai-theme-wp' ) );
		$f[] = $n( 'shop_short_desc_words', $sh, __( 'Short description length (words)', 'bigspy-ai-theme-wp' ), 5, 60 );
		$f[] = $n( 'shop_title_size', $sh, __( 'Product name size (px, 0 = default)', 'bigspy-ai-theme-wp' ), 0, 32 );
		$f[] = array( 'id' => 'shop_price_color', 'section' => $sh, 'type' => 'color', 'label' => __( 'Price color', 'bigspy-ai-theme-wp' ) );
		$f[] = $n( 'shop_card_radius', $sh, __( 'Card corner radius (px, -1 = default)', 'bigspy-ai-theme-wp' ), -1, 40 );
		$f[] = $n( 'shop_card_padding', $sh, __( 'Card inner space (px, -1 = default)', 'bigspy-ai-theme-wp' ), -1, 40 );
		$f[] = array( 'id' => 'shop_out_of_stock', 'section' => $sh, 'type' => 'select', 'label' => __( 'Sold-out products', 'bigspy-ai-theme-wp' ), 'choices' => array( 'badge' => __( 'Badge only', 'bigspy-ai-theme-wp' ), 'fade' => __( 'Badge and faded card', 'bigspy-ai-theme-wp' ), 'gray' => __( 'Badge and grayscale image', 'bigspy-ai-theme-wp' ) ) );
		$f[] = array( 'id' => 'bst_g_shop_badges', 'section' => $sh, 'type' => 'heading', 'label' => __( 'Badge texts (empty = default)', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'badge_sale_text', 'section' => $sh, 'type' => 'text', 'label' => __( 'Sale badge (use {percent} for the discount)', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'badge_out_text', 'section' => $sh, 'type' => 'text', 'label' => __( 'Sold-out badge', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'badge_new_text', 'section' => $sh, 'type' => 'text', 'label' => __( 'New badge', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'badge_hot_text', 'section' => $sh, 'type' => 'text', 'label' => __( 'Featured badge', 'bigspy-ai-theme-wp' ) );

		// Product page.
		$f[] = array( 'id' => 'bst_g_product_page', 'section' => $pr, 'type' => 'heading', 'label' => __( 'Product page layout', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'product_image_width', 'section' => $pr, 'type' => 'select', 'label' => __( 'Image column width', 'bigspy-ai-theme-wp' ), 'choices' => array( '40' => '40%', '45' => '45%', '50' => '50%', '55' => '55%', '60' => '60%' ) );
		$f[] = array( 'id' => 'product_text_align', 'section' => $pr, 'type' => 'select', 'label' => __( 'Summary text alignment', 'bigspy-ai-theme-wp' ), 'choices' => array( 'left' => __( 'Left', 'bigspy-ai-theme-wp' ), 'center' => __( 'Center', 'bigspy-ai-theme-wp' ) ) );
		$f[] = $n( 'product_title_size', $pr, __( 'Product name size (px, 0 = default)', 'bigspy-ai-theme-wp' ), 0, 60 );
		$f[] = $c( 'product_zoom', $pr, __( 'Zoom image on hover', 'bigspy-ai-theme-wp' ) );
		$f[] = $c( 'product_lightbox', $pr, __( 'Open images in a lightbox', 'bigspy-ai-theme-wp' ) );
		$f[] = $c( 'product_breadcrumb', $pr, __( 'Show breadcrumbs', 'bigspy-ai-theme-wp' ) );
		$f[] = $c( 'product_nav', $pr, __( 'Previous / next product links', 'bigspy-ai-theme-wp' ) );
		$f[] = $c( 'product_meta', $pr, __( 'Show SKU, categories and tags', 'bigspy-ai-theme-wp' ) );
		$f[] = $c( 'product_share', $pr, __( 'Show share buttons', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'product_shipping_text', 'section' => $pr, 'type' => 'text', 'label' => __( 'Text under the price (e.g. Free shipping over $50)', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'product_tabs_align', 'section' => $pr, 'type' => 'select', 'label' => __( 'Tabs alignment', 'bigspy-ai-theme-wp' ), 'choices' => array( 'left' => __( 'Left', 'bigspy-ai-theme-wp' ), 'center' => __( 'Center', 'bigspy-ai-theme-wp' ) ) );
		$f[] = array( 'id' => 'bst_g_product_more', 'section' => $pr, 'type' => 'heading', 'label' => __( 'Related & upsell products', 'bigspy-ai-theme-wp' ) );
		$f[] = $c( 'product_related', $pr, __( 'Show related products', 'bigspy-ai-theme-wp' ) );
		$f[] = $c( 'product_upsells', $pr, __( 'Show upsell products', 'bigspy-ai-theme-wp' ) );
		$f[] = $n( 'related_count', $pr, __( 'Number of related products (0 = one row)', 'bigspy-ai-theme-wp' ), 0, 24 );
		$f[] = array( 'id' => 'related_title', 'section' => $pr, 'type' => 'text', 'label' => __( 'Related products heading (empty = default)', 'bigspy-ai-theme-wp' ) );

		// Shop and product fields only when WooCommerce runs; sidebar fields always.
		if ( ! class_exists( 'WooCommerce' ) ) {
			$f = array();
		}

		// Sidebar.
		$f[] = array( 'id' => 'bst_g_sidebar_colors', 'section' => $sb, 'type' => 'heading', 'label' => __( 'Colors & spacing', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'sidebar_bg', 'section' => $sb, 'type' => 'color', 'label' => __( 'Widget background', 'bigspy-ai-theme-wp' ) );
		$f[] = $n( 'sidebar_padding', $sb, __( 'Widget inner space (px, -1 = style default)', 'bigspy-ai-theme-wp' ), -1, 60 );
		$f[] = $n( 'sidebar_radius', $sb, __( 'Widget corner radius (px, -1 = style default)', 'bigspy-ai-theme-wp' ), -1, 40 );
		$f[] = $n( 'sidebar_title_size', $sb, __( 'Widget title size (px, 0 = default)', 'bigspy-ai-theme-wp' ), 0, 40 );
		$f[] = array( 'id' => 'sidebar_title_color', 'section' => $sb, 'type' => 'color', 'label' => __( 'Widget title color', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'sidebar_link_color', 'section' => $sb, 'type' => 'color', 'label' => __( 'Widget link color', 'bigspy-ai-theme-wp' ) );

		return array_merge( $fields, $f );
	},
	35
);

/* ---------------------------------------------------------------- Behaviour */
add_action(
	'wp',
	function () {
		if ( ! bst_mod( 'shop_result_count' ) ) {
			remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		}
		if ( ! bst_mod( 'shop_ordering' ) ) {
			remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
		}
		if ( ! bst_mod( 'product_meta' ) ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
		}
		if ( ! bst_mod( 'product_related' ) ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
		}
		if ( ! bst_mod( 'product_upsells' ) ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
		}
	}
);

add_action(
	'after_setup_theme',
	function () {
		if ( ! bst_mod( 'product_zoom' ) ) {
			remove_theme_support( 'wc-product-gallery-zoom' );
		}
		if ( ! bst_mod( 'product_lightbox' ) ) {
			remove_theme_support( 'wc-product-gallery-lightbox' );
		}
	},
	20
);

add_filter(
	'woocommerce_subcategory_count_html',
	function ( $html ) {
		return bst_mod( 'shop_cat_count' ) ? $html : '';
	}
);

add_filter(
	'woocommerce_output_related_products_args',
	function ( $args ) {
		if ( (int) bst_mod( 'related_count' ) > 0 ) {
			$args['posts_per_page'] = (int) bst_mod( 'related_count' );
		}
		return $args;
	},
	20
);

add_filter(
	'woocommerce_product_related_products_heading',
	function ( $heading ) {
		$t = trim( (string) bst_mod( 'related_title' ) );
		return '' !== $t ? $t : $heading;
	}
);

/** Custom badge texts. */
add_filter(
	'bst_product_badges',
	function ( $badges, $product ) {
		$map = array( 'sale' => 'badge_sale_text', 'out' => 'badge_out_text', 'new' => 'badge_new_text', 'hot' => 'badge_hot_text' );
		foreach ( $badges as $i => $badge ) {
			$text = isset( $map[ $badge[0] ] ) ? trim( (string) bst_mod( $map[ $badge[0] ] ) ) : '';
			if ( '' === $text ) {
				continue;
			}
			if ( 'sale' === $badge[0] ) {
				$pct  = function_exists( 'bst_wc_sale_percent' ) ? (int) bst_wc_sale_percent( $product ) : 0;
				$text = str_replace( '{percent}', $pct ? $pct . '%' : '', $text );
			}
			$badges[ $i ][1] = $text;
		}
		return $badges;
	},
	10,
	2
);

/** Text under the price, previous / next links, share buttons. */
add_action(
	'woocommerce_single_product_summary',
	function () {
		$t = trim( (string) bst_mod( 'product_shipping_text' ) );
		if ( '' !== $t ) {
			echo '<p class="bs-product-note">' . esc_html( $t ) . '</p>';
		}
	},
	11
);

add_action(
	'woocommerce_single_product_summary',
	function () {
		if ( ! bst_mod( 'product_nav' ) ) {
			return;
		}
		$prev = get_previous_post( true, '', 'product_cat' );
		$next = get_next_post( true, '', 'product_cat' );
		if ( ! $prev && ! $next ) {
			return;
		}
		echo '<nav class="bs-product-nav" aria-label="' . esc_attr__( 'More products', 'bigspy-ai-theme-wp' ) . '">';
		if ( $prev ) {
			echo '<a href="' . esc_url( get_permalink( $prev ) ) . '" rel="prev" title="' . esc_attr( get_the_title( $prev ) ) . '">' . bst_icon( 'chevron-left', 18 ) . '<span class="screen-reader-text">' . esc_html__( 'Previous', 'bigspy-ai-theme-wp' ) . '</span></a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( $next ) {
			echo '<a href="' . esc_url( get_permalink( $next ) ) . '" rel="next" title="' . esc_attr( get_the_title( $next ) ) . '"><span class="screen-reader-text">' . esc_html__( 'Next', 'bigspy-ai-theme-wp' ) . '</span>' . bst_icon( 'chevron-right', 18 ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</nav>';
	},
	1
);

add_action(
	'woocommerce_single_product_summary',
	function () {
		if ( ! bst_mod( 'product_share' ) ) {
			return;
		}
		$url   = rawurlencode( get_permalink() );
		$title = rawurlencode( get_the_title() );
		$links = array(
			'facebook'  => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
			'x'         => 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title,
			'pinterest' => 'https://pinterest.com/pin/create/button/?url=' . $url . '&description=' . $title,
			'mail'      => 'mailto:?subject=' . $title . '&body=' . $url,
		);
		echo '<div class="bs-share bs-share--product"><span class="bs-share__label">' . esc_html__( 'Share:', 'bigspy-ai-theme-wp' ) . '</span>';
		foreach ( $links as $network => $link ) {
			echo '<a class="bs-share__link bs-share__link--' . esc_attr( $network ) . '" href="' . esc_url( $link, array( 'https', 'mailto' ) ) . '" target="_blank" rel="noopener nofollow" aria-label="' . esc_attr( ucfirst( $network ) ) . '">' . bst_icon( $network, 16 ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div>';
	},
	50
);

/* ---------------------------------------------------------------- Look */
add_filter(
	'body_class',
	function ( $classes ) {
		$classes[] = 'bs-shop-align--' . bst_choice( bst_mod( 'shop_card_align' ), array( 'left', 'center', 'right' ), 'left' );
		$classes[] = 'bs-shop-oos--' . bst_choice( bst_mod( 'shop_out_of_stock' ), array( 'badge', 'fade', 'gray' ), 'badge' );
		if ( 'center' === bst_mod( 'product_text_align' ) ) {
			$classes[] = 'bs-product-summary--center';
		}
		if ( 'center' === bst_mod( 'product_tabs_align' ) ) {
			$classes[] = 'bs-product-tabs--center';
		}
		return $classes;
	}
);

/** CSS from the number / color options. */
function bst_shop_options_css() {
	$r   = array();
	$px  = function ( $key, $min = 1 ) {
		$v = bst_mod( $key );
		return is_numeric( $v ) && (int) $v >= $min ? (int) $v . 'px' : '';
	};
	$col = function ( $key ) {
		return (string) sanitize_hex_color( (string) bst_mod( $key ) );
	};
	if ( $v = $px( 'shop_title_size' ) ) {
		$r[] = ".bs-product__title{font-size:$v}";
	}
	if ( $v = $col( 'shop_price_color' ) ) {
		$r[] = ".bs-product__price{color:$v}";
	}
	if ( '' !== ( $v = $px( 'shop_card_radius', 0 ) ) ) {
		$r[] = "ul.products li.bs-product{border-radius:$v;overflow:hidden}";
	}
	if ( '' !== ( $v = $px( 'shop_card_padding', 0 ) ) ) {
		$r[] = ".bs-product__body{padding:$v}";
	}
	$w = (int) bst_choice( (string) bst_mod( 'product_image_width' ), array( '40', '45', '50', '55', '60' ), '50' );
	if ( 50 !== $w ) {
		$r[] = '@media (min-width:850px){.woocommerce div.product div.images{width:' . $w . '%}.woocommerce div.product div.summary{width:' . ( 96 - $w ) . '%}}';
	}
	if ( $v = $px( 'product_title_size' ) ) {
		$r[] = ".woocommerce div.product .product_title{font-size:$v}";
	}
	$sb = array();
	if ( $v = $col( 'sidebar_bg' ) ) {
		$sb[] = "background:$v";
	}
	if ( '' !== ( $v = $px( 'sidebar_padding', 0 ) ) ) {
		$sb[] = "padding:$v";
	}
	if ( '' !== ( $v = $px( 'sidebar_radius', 0 ) ) ) {
		$sb[] = "border-radius:$v";
	}
	if ( $sb ) {
		$r[] = '.bs-layout__sidebar .widget{' . implode( ';', $sb ) . '}';
	}
	if ( $v = $px( 'sidebar_title_size' ) ) {
		$r[] = ".bs-layout__sidebar .widget-title,.bs-layout__sidebar .widgettitle,.bs-layout__sidebar .wp-block-heading{font-size:$v}";
	}
	if ( $v = $col( 'sidebar_title_color' ) ) {
		$r[] = ".bs-layout__sidebar .widget-title,.bs-layout__sidebar .widgettitle,.bs-layout__sidebar .wp-block-heading{color:$v}";
	}
	if ( $v = $col( 'sidebar_link_color' ) ) {
		$r[] = ".bs-layout__sidebar .widget a{color:$v}";
	}
	return implode( '', $r );
}

add_action(
	'wp_enqueue_scripts',
	function () {
		$css = bst_shop_options_css();
		if ( $css ) {
			wp_add_inline_style( 'bst-blocks', $css );
		}
	},
	30
);
