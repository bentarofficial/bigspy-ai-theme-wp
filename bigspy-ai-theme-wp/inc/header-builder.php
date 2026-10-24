<?php
/**
 * Header builder renderer.
 *
 * The layout (which element sits in which row / zone, per device) is stored
 * as JSON in the `header_layout` theme option and edited in the Customizer.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Full-width code banner above the header (promotions, announcements).
 * Saved through bst_sanitize_code: raw only for users allowed unfiltered HTML.
 */
function bst_header_banner() {
	$code = trim( (string) bst_mod( 'header_banner_code' ) );
	if ( ! bst_mod( 'header_banner_on' ) || '' === $code ) {
		return;
	}
	$device = bst_choice( (string) bst_mod( 'header_banner_device' ), array( 'all', 'desktop', 'mobile' ), 'all' );
	echo '<div class="bs-header-banner bs-header-banner--' . esc_attr( $device ) . '">' . do_shortcode( $code ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- admin-authored code.
}

function bst_render_header() {
	$layout  = bst_get_header_layout();
	$classes = array( 'bs-header' );

	if ( bst_header_is_transparent() ) {
		$classes[] = 'is-transparent';
	}
	if ( bst_mod( 'header_sticky' ) ) {
		$classes[] = 'has-sticky';
	}
	if ( bst_mod( 'header_shadow' ) ) {
		$classes[] = 'has-shadow';
	}

	$rows = array( 'top' => '', 'sticky' => '' );
	foreach ( bst_header_rows() as $row ) {
		foreach ( bst_header_devices() as $device ) {
			$html = bst_header_row_html( $device, $row, $layout[ $device ][ $row ] );
			if ( 'top' === $row ) {
				$rows['top'] .= $html;
			} else {
				$rows['sticky'] .= $html;
			}
		}
	}

	echo '<header id="masthead" class="' . esc_attr( implode( ' ', $classes ) ) . '">';
	echo $rows['top']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts.
	echo '<div class="bs-header__sticky">' . $rows['sticky']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	bst_search_panel();
	echo '</div></header>';
}

/**
 * @param string $device desktop|mobile.
 * @param string $row    top|main|bottom.
 * @param array  $zones  zone => element keys.
 * @return string Empty when the row has no visible content.
 */
function bst_header_row_html( $device, $row, $zones ) {
	$zones_html = array();
	$has        = false;

	foreach ( bst_header_zones() as $zone ) {
		$html = '';
		foreach ( (array) $zones[ $zone ] as $element ) {
			$html .= bst_header_element( $element, $device );
		}
		if ( '' !== trim( $html ) ) {
			$has = true;
		}
		$zones_html[ $zone ] = $html;
	}

	if ( ! $has ) {
		return '';
	}

	$inner_class = 'bs-header-row__inner' . ( '' === trim( $zones_html['center'] ) ? ' has-no-center' : ' has-center' );
	$container   = bst_mod( 'header_full_width' ) ? 'bs-container bs-container--fluid' : 'bs-container';

	$out  = '<div class="bs-header-row bs-header-row--' . esc_attr( $row ) . ' bs-header-row--' . esc_attr( $device ) . '">';
	$out .= '<div class="' . esc_attr( $container . ' ' . $inner_class ) . '">';
	foreach ( $zones_html as $zone => $html ) {
		$out .= '<div class="bs-header-zone bs-header-zone--' . esc_attr( $zone ) . '">' . $html . '</div>';
	}
	$out .= '</div></div>';
	return $out;
}

/**
 * Markup for a single header element, or '' if it has nothing to show.
 */
function bst_header_element( $element, $device ) {
	$html = '';

	switch ( $element ) {
		case 'logo':
			$html = bst_logo();
			break;

		case 'nav':
		case 'nav_secondary':
			$location = 'nav' === $element ? 'primary' : 'secondary';
			if ( has_nav_menu( $location ) ) {
				$nav_style  = 'pills' === bst_mod( 'nav_style' ) ? 'pill' : sanitize_html_class( bst_mod( 'nav_style' ) );
				$menu_class = 'bs-menu bs-menu--' . ( 'nav' === $element ? $nav_style : 'plain' );
				$html       = '<nav class="bs-nav bs-nav--' . esc_attr( $location ) . '" aria-label="' . esc_attr( 'nav' === $element ? __( 'Main menu', 'bigspy-ai-theme-wp' ) : __( 'Top bar menu', 'bigspy-ai-theme-wp' ) ) . '">'
					. wp_nav_menu( array( 'theme_location' => $location, 'container' => false, 'menu_class' => $menu_class, 'depth' => 3, 'fallback_cb' => false, 'echo' => false ) )
					. '</nav>';
			} elseif ( 'nav' === $element && current_user_can( 'edit_theme_options' ) ) {
				$html = '<a class="bs-header-hint" href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . esc_html__( 'Assign a menu to “Main menu”', 'bigspy-ai-theme-wp' ) . '</a>';
			}
			break;

		case 'nav_icon':
			$nav_type  = function_exists( 'bst_he' ) ? bst_choice( (string) bst_he( 'nav_icon', 'type' ), array( 'menu', 'menu-short', 'dots', 'grid', 'menu-thin', 'menu-two', 'menu-mid', 'menu-right', 'menu-steps', 'menu-bold', 'dots-v', 'grid-dots', 'list' ), 'menu' ) : 'menu';
			$nav_label = function_exists( 'bst_he' ) ? trim( (string) bst_he( 'nav_icon', 'label' ) ) : '';
			$nav_svg   = array(
				'menu'       => bst_icon( 'menu', 24 ),
				'menu-short' => '<svg class="bs-icon bs-icon--menu" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M3 6h18M3 12h18M3 18h11"/></svg>',
				'dots'       => '<svg class="bs-icon bs-icon--menu" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><circle cx="5" cy="12" r="2" fill="currentColor"/><circle cx="12" cy="12" r="2" fill="currentColor"/><circle cx="19" cy="12" r="2" fill="currentColor"/></svg>',
				'grid'       => '<svg class="bs-icon bs-icon--menu" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2" d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/></svg>',
				'menu-thin' => '<svg class="bs-icon bs-icon--menu" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" d="M3 6h18M3 12h18M3 18h18"/></svg>',
				'menu-two' => '<svg class="bs-icon bs-icon--menu" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M3 9h18M3 15h18"/></svg>',
				'menu-mid' => '<svg class="bs-icon bs-icon--menu" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M3 6h18M3 12h11M3 18h18"/></svg>',
				'menu-right' => '<svg class="bs-icon bs-icon--menu" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M3 6h18M10 12h11M6 18h15"/></svg>',
				'menu-steps' => '<svg class="bs-icon bs-icon--menu" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M3 6h18M3 12h14M3 18h9"/></svg>',
				'menu-bold' => '<svg class="bs-icon bs-icon--menu" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>',
				'dots-v' => '<svg class="bs-icon bs-icon--menu" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="5" r="2" fill="currentColor"/><circle cx="12" cy="12" r="2" fill="currentColor"/><circle cx="12" cy="19" r="2" fill="currentColor"/></svg>',
				'grid-dots' => '<svg class="bs-icon bs-icon--menu" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><g fill="currentColor"><circle cx="5" cy="5" r="2"/><circle cx="12" cy="5" r="2"/><circle cx="19" cy="5" r="2"/><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/><circle cx="5" cy="19" r="2"/><circle cx="12" cy="19" r="2"/><circle cx="19" cy="19" r="2"/></g></svg>',
				'list' => '<svg class="bs-icon bs-icon--menu" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 6h12M9 12h12M9 18h12"/><path d="M4 6h.01M4 12h.01M4 18h.01" stroke-width="3"/></g></svg>',
			);
			$html = '<button type="button" class="bs-icon-btn bs-nav-toggle" data-bs-offcanvas-open aria-controls="bs-offcanvas" aria-expanded="false" aria-label="' . esc_attr__( 'Open menu', 'bigspy-ai-theme-wp' ) . '">' . $nav_svg[ $nav_type ] . ( '' !== $nav_label ? '<span class="bs-he-label">' . esc_html( $nav_label ) . '</span>' : '' ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			break;

		case 'search':
			$html = '<button type="button" class="bs-icon-btn bs-search-toggle" data-bs-search-toggle aria-controls="bs-search-panel" aria-expanded="false" aria-label="' . esc_attr__( 'Search', 'bigspy-ai-theme-wp' ) . '">' . bst_icon( 'search', 20 ) . '</button>';
			break;

		case 'search_form':
			$html = bst_search_form( 'bs-search-form--header' );
			break;

		case 'account':
			$html = bst_account_link();
			break;

		case 'cart':
			$html = function_exists( 'bst_header_cart' ) ? bst_header_cart( $device ) : '';
			break;

		case 'button_1':
		case 'button_2':
			$html = bst_header_button( 'header_' . $element );
			break;

		case 'html_1':
		case 'html_2':
		case 'html_3':
			$html = bst_html_field( 'header_' . $element );
			if ( '' !== $html ) {
				$html = '<div class="bs-header-html">' . $html . '</div>';
			}
			break;

		case 'contact':
			$html = bst_contact_list( 'bs-contact--inline' );
			break;

		case 'social':
			$html = bst_social_icons();
			break;

		case 'divider':
			$html = '<span class="bs-header-divider" aria-hidden="true"></span>';
			break;

		default:
			$html = (string) apply_filters( 'bst_header_element_' . $element, '', $device );
	}

	if ( '' === trim( $html ) ) {
		return '';
	}
	return '<div class="bs-header-el bs-header-el--' . esc_attr( str_replace( '_', '-', $element ) ) . ( function_exists( 'bst_he_classes' ) ? esc_attr( bst_he_classes( $element ) ) : '' ) . '">' . $html . '</div>';
}

function bst_logo() {
	$home  = esc_url( home_url( '/' ) );
	$name  = get_bloginfo( 'name' );
	$logo  = get_theme_mod( 'custom_logo' );
	$inner = '';

	if ( $logo ) {
		$inner = wp_get_attachment_image( $logo, 'full', false, array( 'class' => 'bs-logo__img', 'alt' => $name, 'loading' => false, 'fetchpriority' => 'high' ) );
	}
	if ( ! $inner ) {
		$inner = '<span class="bs-logo__text">' . esc_html( $name ) . '</span>';
	}
	$tagline = function_exists( 'bst_he' ) && bst_he( 'logo', 'tagline' ) ? trim( (string) get_bloginfo( 'description' ) ) : '';
	return '<a class="bs-logo" href="' . $home . '" rel="home">' . $inner . ( '' !== $tagline ? '<span class="bs-logo__tagline">' . esc_html( $tagline ) . '</span>' : '' ) . '</a>';
}

function bst_header_button( $prefix ) {
	$text = trim( (string) bst_mod( $prefix . '_text' ) );
	$url  = trim( (string) bst_mod( $prefix . '_url' ) );
	if ( '' === $text || '' === $url ) {
		return '';
	}
	$style  = bst_choice( bst_mod( $prefix . '_style' ), array( 'primary', 'secondary', 'outline', 'white', 'link', 'gradient', 'soft' ), 'primary' );
	$target = bst_mod( $prefix . '_new_tab' ) ? ' target="_blank" rel="noopener"' : '';
	$key    = str_replace( 'header_', '', $prefix );
	$size   = function_exists( 'bst_he' ) ? bst_choice( (string) bst_he( $key, 'size' ), array( 'sm', 'md', 'lg' ), 'sm' ) : 'sm';
	$icon   = function_exists( 'bst_he' ) ? sanitize_key( (string) bst_he( $key, 'icon' ) ) : '';
	$extra  = in_array( $style, array( 'gradient', 'soft' ), true ) ? ' has-custom-colors' : '';
	return '<a class="bs-btn bs-btn--' . esc_attr( $style ) . ( 'md' !== $size ? ' bs-btn--' . esc_attr( $size ) : '' ) . $extra . '" href="' . esc_url( $url ) . '"' . $target . '>' . ( $icon ? bst_icon( $icon, 'lg' === $size ? 20 : 17 ) : '' ) . '<span>' . esc_html( $text ) . '</span></a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

function bst_account_link() {
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		$url = wc_get_page_permalink( 'myaccount' );
	} else {
		$url = is_user_logged_in() ? admin_url( 'profile.php' ) : wp_login_url();
	}
	$label = is_user_logged_in() ? __( 'My account', 'bigspy-ai-theme-wp' ) : __( 'Login', 'bigspy-ai-theme-wp' );
	if ( function_exists( 'bst_he' ) ) {
		$custom = trim( (string) bst_he( 'account', is_user_logged_in() ? 'label_in' : 'label_out' ) );
		$label  = '' !== $custom ? $custom : $label;
	}
	return '<a class="bs-account-link" href="' . esc_url( $url ) . '">' . bst_icon( 'user', 20 ) . '<span class="bs-account-link__label">' . esc_html( $label ) . '</span></a>';
}

/**
 * Contact info list (phone, email, address, opening hours).
 *
 * @param string $class Extra class.
 */
function bst_contact_list( $class = '' ) {
	$items = array(
		'phone'   => array( bst_mod( 'contact_phone' ), 'tel:' ),
		'mail'    => array( bst_mod( 'contact_email' ), 'mailto:' ),
		'pin'     => array( bst_mod( 'contact_address' ), '' ),
		'clock'   => array( bst_mod( 'contact_hours' ), '' ),
	);
	if ( false !== strpos( $class, 'bs-contact--inline' ) && function_exists( 'bst_he' ) ) {
		$only = (string) bst_he( 'contact', 'items' );
		$keep = array( 'phone' => array( 'phone' ), 'phone_mail' => array( 'phone', 'mail' ), 'phone_pin' => array( 'phone', 'pin' ) );
		if ( isset( $keep[ $only ] ) ) {
			$items = array_intersect_key( $items, array_flip( $keep[ $only ] ) );
		}
	}
	$out = '';
	foreach ( $items as $icon => $item ) {
		$value = trim( (string) $item[0] );
		if ( '' === $value ) {
			continue;
		}
		$label = esc_html( $value );
		if ( $item[1] ) {
			$href  = 'tel:' === $item[1] ? 'tel:' . preg_replace( '/[^0-9+]/', '', $value ) : 'mailto:' . sanitize_email( $value );
			$label = '<a href="' . esc_attr( $href ) . '">' . $label . '</a>';
		}
		$out .= '<li class="bs-contact__item bs-contact__item--' . esc_attr( $icon ) . '">' . bst_icon( $icon, 16 ) . '<span>' . $label . '</span></li>';
	}
	return $out ? '<ul class="bs-contact ' . esc_attr( $class ) . '">' . $out . '</ul>' : '';
}

function bst_social_icons( $class = '' ) {
	$out = '';
	foreach ( bst_social_networks() as $key => $label ) {
		$url = trim( (string) bst_mod( 'social_' . $key ) );
		if ( '' === $url ) {
			continue;
		}
		if ( 'zalo' === $key && preg_match( '/^[0-9 +.]+$/', $url ) ) {
			$url = 'https://zalo.me/' . preg_replace( '/[^0-9]/', '', $url );
		}
		$out .= '<a class="bs-social__link bs-social__link--' . esc_attr( $key ) . '" href="' . esc_url( $url ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr( $label ) . '">' . bst_icon( $key, 18 ) . '</a>';
	}
	return $out ? '<div class="bs-social ' . esc_attr( $class ) . '">' . $out . '</div>' : '';
}

/**
 * Search form used in the header, off-canvas panel and searchform.php.
 */
function bst_search_form( $class = '' ) {
	$products = class_exists( 'WooCommerce' ) && ( bst_mod( 'search_products' ) || 'products' === bst_choice( bst_mod( 'search_scope' ), array( 'all', 'products', 'content' ), 'all' ) );
	$id       = wp_unique_id( 'bs-search-' );
	$hint     = trim( (string) bst_mod( 'search_placeholder' ) );
	if ( '' === $hint ) {
		$hint = $products ? __( 'Search products…', 'bigspy-ai-theme-wp' ) : __( 'Search…', 'bigspy-ai-theme-wp' );
	}
	$out      = '<form role="search" method="get" class="bs-search-form ' . esc_attr( $class ) . '" action="' . esc_url( home_url( '/' ) ) . '">';
	$out     .= '<label class="screen-reader-text" for="' . esc_attr( $id ) . '">' . esc_html__( 'Search for:', 'bigspy-ai-theme-wp' ) . '</label>';
	$out     .= '<input type="search" id="' . esc_attr( $id ) . '" class="bs-search-form__input" name="s" value="' . esc_attr( get_search_query() ) . '" placeholder="' . esc_attr( $hint ) . '">';
	if ( $products ) {
		$out .= '<input type="hidden" name="post_type" value="product">';
	}
	$out .= '<button type="submit" class="bs-search-form__submit" aria-label="' . esc_attr__( 'Submit search', 'bigspy-ai-theme-wp' ) . '">' . bst_icon( 'search', 18 ) . '</button>';
	$out .= '</form>';
	return $out;
}

function bst_search_panel() {
	$layout = bst_get_header_layout();
	$used   = false;
	foreach ( $layout as $rows ) {
		foreach ( $rows as $zones ) {
			foreach ( $zones as $items ) {
				$used = $used || in_array( 'search', $items, true );
			}
		}
	}
	if ( ! $used ) {
		return;
	}
	$style = bst_choice( bst_mod( 'search_style' ), array( 'dropdown', 'lightbox' ), 'dropdown' );
	echo '<div id="bs-search-panel" class="bs-search-panel bs-search-panel--' . esc_attr( $style ) . '" hidden>';
	echo '<div class="bs-container bs-search-panel__inner">';
	echo bst_search_form( 'bs-search-form--large' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '<button type="button" class="bs-icon-btn bs-search-panel__close" data-bs-search-close aria-label="' . esc_attr__( 'Close search', 'bigspy-ai-theme-wp' ) . '">' . bst_icon( 'close', 22 ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '</div></div>';
}

/**
 * Off-canvas panel used by the menu icon element (mainly on mobile).
 */
function bst_render_offcanvas() {
	$side     = bst_choice( bst_mod( 'offcanvas_side' ), array( 'left', 'right' ), 'left' );
	$location = has_nav_menu( 'mobile' ) ? 'mobile' : 'primary';
	?>
	<div id="bs-offcanvas" class="bs-offcanvas bs-offcanvas--<?php echo esc_attr( $side ); ?>" hidden>
		<div class="bs-offcanvas__backdrop" data-bs-offcanvas-close></div>
		<div class="bs-offcanvas__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu', 'bigspy-ai-theme-wp' ); ?>">
			<div class="bs-offcanvas__head">
				<?php echo bst_logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<button type="button" class="bs-icon-btn" data-bs-offcanvas-close aria-label="<?php esc_attr_e( 'Close menu', 'bigspy-ai-theme-wp' ); ?>"><?php echo bst_icon( 'close', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
			</div>
			<div class="bs-offcanvas__body">
				<?php echo bst_search_form( 'bs-search-form--offcanvas' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php
				if ( has_nav_menu( $location ) ) {
					wp_nav_menu(
						array(
							'theme_location' => $location,
							'container'      => 'nav',
							'container_class' => 'bs-offcanvas__nav',
							'container_aria_label' => __( 'Mobile menu', 'bigspy-ai-theme-wp' ),
							'menu_class'     => 'bs-offcanvas-menu',
							'depth'          => 3,
							'fallback_cb'    => false,
						)
					);
				}
				if ( has_nav_menu( 'secondary' ) ) {
					wp_nav_menu( array( 'theme_location' => 'secondary', 'container' => false, 'menu_class' => 'bs-offcanvas-menu bs-offcanvas-menu--secondary', 'depth' => 1, 'fallback_cb' => false ) );
				}
				if ( is_active_sidebar( 'offcanvas' ) ) {
					echo '<div class="bs-offcanvas__widgets">';
					dynamic_sidebar( 'offcanvas' );
					echo '</div>';
				}
				echo bst_contact_list( 'bs-contact--stacked' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo bst_social_icons(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</div>
		</div>
	</div>
	<?php
}
