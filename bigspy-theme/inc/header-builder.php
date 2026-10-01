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
				$menu_class = 'bs-menu bs-menu--' . ( 'nav' === $element ? sanitize_html_class( bst_mod( 'nav_style' ) ) : 'plain' );
				$html       = '<nav class="bs-nav bs-nav--' . esc_attr( $location ) . '" aria-label="' . esc_attr( 'nav' === $element ? __( 'Main menu', 'bigspy-theme' ) : __( 'Top bar menu', 'bigspy-theme' ) ) . '">'
					. wp_nav_menu( array( 'theme_location' => $location, 'container' => false, 'menu_class' => $menu_class, 'depth' => 3, 'fallback_cb' => false, 'echo' => false ) )
					. '</nav>';
			} elseif ( 'nav' === $element && current_user_can( 'edit_theme_options' ) ) {
				$html = '<a class="bs-header-hint" href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . esc_html__( 'Assign a menu to “Main menu”', 'bigspy-theme' ) . '</a>';
			}
			break;

		case 'nav_icon':
			$html = '<button type="button" class="bs-icon-btn bs-nav-toggle" data-bs-offcanvas-open aria-controls="bs-offcanvas" aria-expanded="false" aria-label="' . esc_attr__( 'Open menu', 'bigspy-theme' ) . '">' . bst_icon( 'menu', 24 ) . '</button>';
			break;

		case 'search':
			$html = '<button type="button" class="bs-icon-btn bs-search-toggle" data-bs-search-toggle aria-controls="bs-search-panel" aria-expanded="false" aria-label="' . esc_attr__( 'Search', 'bigspy-theme' ) . '">' . bst_icon( 'search', 20 ) . '</button>';
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
	return '<div class="bs-header-el bs-header-el--' . esc_attr( str_replace( '_', '-', $element ) ) . '">' . $html . '</div>';
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
	return '<a class="bs-logo" href="' . $home . '" rel="home">' . $inner . '</a>';
}

function bst_header_button( $prefix ) {
	$text = trim( (string) bst_mod( $prefix . '_text' ) );
	$url  = trim( (string) bst_mod( $prefix . '_url' ) );
	if ( '' === $text || '' === $url ) {
		return '';
	}
	$style  = bst_choice( bst_mod( $prefix . '_style' ), array( 'primary', 'secondary', 'outline', 'white', 'link' ), 'primary' );
	$target = bst_mod( $prefix . '_new_tab' ) ? ' target="_blank" rel="noopener"' : '';
	return '<a class="bs-btn bs-btn--' . esc_attr( $style ) . ' bs-btn--sm" href="' . esc_url( $url ) . '"' . $target . '>' . esc_html( $text ) . '</a>';
}

function bst_account_link() {
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		$url = wc_get_page_permalink( 'myaccount' );
	} else {
		$url = is_user_logged_in() ? admin_url( 'profile.php' ) : wp_login_url();
	}
	$label = is_user_logged_in() ? __( 'My account', 'bigspy-theme' ) : __( 'Login', 'bigspy-theme' );
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
	$products = bst_mod( 'search_products' ) && class_exists( 'WooCommerce' );
	$id       = wp_unique_id( 'bs-search-' );
	$out      = '<form role="search" method="get" class="bs-search-form ' . esc_attr( $class ) . '" action="' . esc_url( home_url( '/' ) ) . '">';
	$out     .= '<label class="screen-reader-text" for="' . esc_attr( $id ) . '">' . esc_html__( 'Search for:', 'bigspy-theme' ) . '</label>';
	$out     .= '<input type="search" id="' . esc_attr( $id ) . '" class="bs-search-form__input" name="s" value="' . esc_attr( get_search_query() ) . '" placeholder="' . esc_attr( $products ? __( 'Search products…', 'bigspy-theme' ) : __( 'Search…', 'bigspy-theme' ) ) . '">';
	if ( $products ) {
		$out .= '<input type="hidden" name="post_type" value="product">';
	}
	$out .= '<button type="submit" class="bs-search-form__submit" aria-label="' . esc_attr__( 'Submit search', 'bigspy-theme' ) . '">' . bst_icon( 'search', 18 ) . '</button>';
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
	echo '<button type="button" class="bs-icon-btn bs-search-panel__close" data-bs-search-close aria-label="' . esc_attr__( 'Close search', 'bigspy-theme' ) . '">' . bst_icon( 'close', 22 ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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
		<div class="bs-offcanvas__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu', 'bigspy-theme' ); ?>">
			<div class="bs-offcanvas__head">
				<?php echo bst_logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<button type="button" class="bs-icon-btn" data-bs-offcanvas-close aria-label="<?php esc_attr_e( 'Close menu', 'bigspy-theme' ); ?>"><?php echo bst_icon( 'close', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
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
							'container_aria_label' => __( 'Mobile menu', 'bigspy-theme' ),
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
