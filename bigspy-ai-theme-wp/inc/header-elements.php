<?php
/**
 * Per-element settings for the header builder (like Flatsome's element options).
 *
 * Every header element gets its own Customizer section (opened by the gear in the
 * builder) with colors, sizes, spacing, icon style and element-specific options.
 * Values are stored as theme mods `he_<element>_<option>` and printed as scoped CSS.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Shared option groups. */
function bst_he_common( $key, $what = array() ) {
	$f   = array();
	$add = function ( $id, $type, $label, $extra = array() ) use ( &$f, $key ) {
		$f[] = array_merge( array( 'id' => 'he_' . $key . '_' . $id, 'type' => $type, 'label' => $label ), $extra );
	};
	if ( in_array( 'icon', $what, true ) ) {
		$add( 'h_icon', 'heading', __( 'Icon', 'bigspy-ai-theme-wp' ) );
		$add( 'icon_style', 'select', __( 'Icon style', 'bigspy-ai-theme-wp' ), array( 'choices' => array( '' => __( 'Plain icon', 'bigspy-ai-theme-wp' ), 'circle' => __( 'Filled circle', 'bigspy-ai-theme-wp' ), 'square' => __( 'Filled square', 'bigspy-ai-theme-wp' ), 'outline' => __( 'Outline circle', 'bigspy-ai-theme-wp' ), 'soft' => __( 'Soft tinted circle', 'bigspy-ai-theme-wp' ) ) ) );
		$add( 'icon_size', 'number', __( 'Icon size (px, 0 = default)', 'bigspy-ai-theme-wp' ), array( 'input_attrs' => array( 'min' => 0, 'max' => 48 ) ) );
		$add( 'icon_bg', 'color', __( 'Icon background (filled styles)', 'bigspy-ai-theme-wp' ) );
	}
	if ( in_array( 'text', $what, true ) ) {
		$add( 'h_text', 'heading', __( 'Text', 'bigspy-ai-theme-wp' ) );
		$add( 'font_size', 'number', __( 'Font size (px, 0 = default)', 'bigspy-ai-theme-wp' ), array( 'input_attrs' => array( 'min' => 0, 'max' => 40 ) ) );
		$add( 'weight', 'select', __( 'Font weight', 'bigspy-ai-theme-wp' ), array( 'choices' => array( '' => __( 'Default', 'bigspy-ai-theme-wp' ), '400' => '400', '500' => '500', '600' => '600', '700' => '700', '800' => '800' ) ) );
		$add( 'transform', 'select', __( 'Letter case', 'bigspy-ai-theme-wp' ), array( 'choices' => array( '' => __( 'Default', 'bigspy-ai-theme-wp' ), 'none' => __( 'As typed', 'bigspy-ai-theme-wp' ), 'uppercase' => __( 'UPPERCASE', 'bigspy-ai-theme-wp' ), 'capitalize' => __( 'Capitalize', 'bigspy-ai-theme-wp' ) ) ) );
	}
	$add( 'h_colors', 'heading', __( 'Colors', 'bigspy-ai-theme-wp' ) );
	$add( 'color', 'color', __( 'Color', 'bigspy-ai-theme-wp' ) );
	$add( 'hover', 'color', __( 'Color on hover', 'bigspy-ai-theme-wp' ) );
	$add( 'h_space', 'heading', __( 'Spacing', 'bigspy-ai-theme-wp' ) );
	$add( 'margin_l', 'number', __( 'Space on the left (px)', 'bigspy-ai-theme-wp' ), array( 'input_attrs' => array( 'min' => -40, 'max' => 120 ) ) );
	$add( 'margin_r', 'number', __( 'Space on the right (px)', 'bigspy-ai-theme-wp' ), array( 'input_attrs' => array( 'min' => -40, 'max' => 120 ) ) );
	$add( 'hide_sticky', 'checkbox', __( 'Hide when the header is sticky (scrolled)', 'bigspy-ai-theme-wp' ) );
	return $f;
}

/** element => [ title, description, fields ]. */
function bst_he_schema() {
	$s = array();
	$o = function ( $key, $id, $type, $label, $extra = array() ) {
		return array_merge( array( 'id' => 'he_' . $key . '_' . $id, 'type' => $type, 'label' => $label ), $extra );
	};

	$s['logo'] = array(
		__( 'Logo', 'bigspy-ai-theme-wp' ),
		__( 'Upload the logo in Site identity. Sizes below override the general logo width.', 'bigspy-ai-theme-wp' ),
		array_merge(
			array(
				array( 'id' => 'logo_width', 'type' => 'number', 'label' => __( 'Max width – desktop (px)', 'bigspy-ai-theme-wp' ) ),
				array( 'id' => 'logo_width_mobile', 'type' => 'number', 'label' => __( 'Max width – mobile (px)', 'bigspy-ai-theme-wp' ) ),
				$o( 'logo', 'height', 'number', __( 'Max height (px, 0 = auto)', 'bigspy-ai-theme-wp' ), array( 'input_attrs' => array( 'min' => 0, 'max' => 300 ) ) ),
				$o( 'logo', 'sticky_width', 'number', __( 'Width when sticky (px, 0 = same)', 'bigspy-ai-theme-wp' ), array( 'input_attrs' => array( 'min' => 0, 'max' => 500 ) ) ),
				$o( 'logo', 'tagline', 'checkbox', __( 'Show the site tagline next to the logo', 'bigspy-ai-theme-wp' ) ),
				$o( 'logo', 'text_size', 'number', __( 'Text logo size (px, when no image)', 'bigspy-ai-theme-wp' ), array( 'input_attrs' => array( 'min' => 0, 'max' => 60 ) ) ),
			),
			bst_he_common( 'logo' )
		),
	);
	$s['nav'] = array(
		__( 'Main menu', 'bigspy-ai-theme-wp' ),
		__( 'Edit menu items in Menus. Style everything else here.', 'bigspy-ai-theme-wp' ),
		array_merge(
			array(
				array( 'id' => 'nav_style', 'type' => 'select', 'label' => __( 'Menu style', 'bigspy-ai-theme-wp' ), 'choices' => array( 'underline' => __( 'Underline', 'bigspy-ai-theme-wp' ), 'plain' => __( 'Plain', 'bigspy-ai-theme-wp' ), 'pills' => __( 'Pills', 'bigspy-ai-theme-wp' ), 'bold' => __( 'Bold', 'bigspy-ai-theme-wp' ) ) ),
				$o( 'nav', 'gap', 'number', __( 'Space between items (px, 0 = default)', 'bigspy-ai-theme-wp' ), array( 'input_attrs' => array( 'min' => 0, 'max' => 80 ) ) ),
				$o( 'nav', 'active', 'color', __( 'Current page / active color', 'bigspy-ai-theme-wp' ) ),
				$o( 'nav', 'indicator', 'color', __( 'Underline / pill color', 'bigspy-ai-theme-wp' ) ),
				$o( 'nav', 'h_drop', 'heading', __( 'Dropdown', 'bigspy-ai-theme-wp' ) ),
				$o( 'nav', 'drop_bg', 'color', __( 'Dropdown background', 'bigspy-ai-theme-wp' ) ),
				$o( 'nav', 'drop_color', 'color', __( 'Dropdown text', 'bigspy-ai-theme-wp' ) ),
				$o( 'nav', 'drop_radius', 'number', __( 'Dropdown corner radius (px)', 'bigspy-ai-theme-wp' ), array( 'input_attrs' => array( 'min' => 0, 'max' => 30 ) ) ),
				array( 'id' => 'nav_size', 'type' => 'number', 'label' => __( 'Font size (px)', 'bigspy-ai-theme-wp' ) ),
				array( 'id' => 'nav_weight', 'type' => 'select', 'label' => __( 'Font weight', 'bigspy-ai-theme-wp' ), 'choices' => array( '400' => '400', '500' => '500', '600' => '600', '700' => '700' ) ),
				array( 'id' => 'nav_transform', 'type' => 'select', 'label' => __( 'Letter case', 'bigspy-ai-theme-wp' ), 'choices' => array( 'none' => __( 'As typed', 'bigspy-ai-theme-wp' ), 'uppercase' => __( 'UPPERCASE', 'bigspy-ai-theme-wp' ) ) ),
			),
			bst_he_common( 'nav' )
		),
	);
	$s['nav_secondary'] = array( __( 'Top bar menu', 'bigspy-ai-theme-wp' ), __( 'Assign a menu to “Top bar menu” in Menus.', 'bigspy-ai-theme-wp' ), array_merge( array( $o( 'nav_secondary', 'gap', 'number', __( 'Space between items (px)', 'bigspy-ai-theme-wp' ) ) ), bst_he_common( 'nav_secondary', array( 'text' ) ) ) );
	$s['nav_icon'] = array(
		__( 'Menu icon (off-canvas)', 'bigspy-ai-theme-wp' ),
		'',
		array_merge(
			array(
				$o( 'nav_icon', 'type', 'select', __( 'Icon', 'bigspy-ai-theme-wp' ), array( 'choices' => array( 'menu' => __( 'Three lines', 'bigspy-ai-theme-wp' ), 'menu-short' => __( 'Lines (short last)', 'bigspy-ai-theme-wp' ), 'dots' => __( 'Dots', 'bigspy-ai-theme-wp' ), 'grid' => __( 'Grid', 'bigspy-ai-theme-wp' ) ) ) ),
				$o( 'nav_icon', 'label', 'text', __( 'Text next to the icon (e.g. Menu)', 'bigspy-ai-theme-wp' ) ),
				array( 'id' => 'offcanvas_side', 'type' => 'radio', 'label' => __( 'Menu opens from', 'bigspy-ai-theme-wp' ), 'choices' => array( 'left' => __( 'Left', 'bigspy-ai-theme-wp' ), 'right' => __( 'Right', 'bigspy-ai-theme-wp' ) ) ),
			),
			bst_he_common( 'nav_icon', array( 'icon' ) )
		),
	);
	$s['search'] = array( __( 'Search icon', 'bigspy-ai-theme-wp' ), '', array_merge( array( array( 'id' => 'search_style', 'type' => 'radio', 'label' => __( 'Search icon opens', 'bigspy-ai-theme-wp' ), 'choices' => array( 'dropdown' => __( 'Dropdown', 'bigspy-ai-theme-wp' ), 'lightbox' => __( 'Full-screen lightbox', 'bigspy-ai-theme-wp' ) ) ) ), bst_he_common( 'search', array( 'icon' ) ) ) );
	$s['search_form'] = array(
		__( 'Search form', 'bigspy-ai-theme-wp' ),
		__( 'Search sources, live suggestions and SKU search: Header → Search.', 'bigspy-ai-theme-wp' ),
		array_merge(
			array(
				$o( 'search_form', 'width', 'number', __( 'Width (px, 0 = auto)', 'bigspy-ai-theme-wp' ), array( 'input_attrs' => array( 'min' => 0, 'max' => 900 ) ) ),
				$o( 'search_form', 'height', 'number', __( 'Height (px, 0 = default)', 'bigspy-ai-theme-wp' ), array( 'input_attrs' => array( 'min' => 0, 'max' => 70 ) ) ),
				$o( 'search_form', 'radius', 'number', __( 'Corner radius (px, -1 = default)', 'bigspy-ai-theme-wp' ), array( 'input_attrs' => array( 'min' => -1, 'max' => 40 ) ) ),
				$o( 'search_form', 'bg', 'color', __( 'Field background', 'bigspy-ai-theme-wp' ) ),
				$o( 'search_form', 'border', 'color', __( 'Field border', 'bigspy-ai-theme-wp' ) ),
				$o( 'search_form', 'button', 'color', __( 'Search button', 'bigspy-ai-theme-wp' ) ),
				array( 'id' => 'search_placeholder', 'type' => 'text', 'label' => __( 'Placeholder text', 'bigspy-ai-theme-wp' ) ),
			),
			bst_he_common( 'search_form' )
		),
	);
	$s['account'] = array(
		__( 'Account', 'bigspy-ai-theme-wp' ),
		'',
		array_merge(
			array(
				$o( 'account', 'show_label', 'select', __( 'Text next to the icon', 'bigspy-ai-theme-wp' ), array( 'choices' => array( '' => __( 'Desktop only', 'bigspy-ai-theme-wp' ), 'always' => __( 'Always', 'bigspy-ai-theme-wp' ), 'never' => __( 'Never (icon only)', 'bigspy-ai-theme-wp' ) ) ) ),
				$o( 'account', 'label_in', 'text', __( 'Text when logged in', 'bigspy-ai-theme-wp' ) ),
				$o( 'account', 'label_out', 'text', __( 'Text when logged out', 'bigspy-ai-theme-wp' ) ),
			),
			bst_he_common( 'account', array( 'icon', 'text' ) )
		),
	);
	$s['cart'] = array(
		__( 'Cart', 'bigspy-ai-theme-wp' ),
		'',
		array_merge(
			array(
				array( 'id' => 'cart_icon', 'type' => 'select', 'label' => __( 'Cart icon', 'bigspy-ai-theme-wp' ), 'choices' => array( 'bag' => __( 'Bag', 'bigspy-ai-theme-wp' ), 'cart' => __( 'Cart', 'bigspy-ai-theme-wp' ) ) ),
				array( 'id' => 'mini_cart', 'type' => 'checkbox', 'label' => __( 'Mini cart dropdown on hover (desktop)', 'bigspy-ai-theme-wp' ) ),
				$o( 'cart', 'total', 'checkbox', __( 'Show the cart total next to the icon', 'bigspy-ai-theme-wp' ) ),
				$o( 'cart', 'label', 'text', __( 'Text next to the icon (e.g. Giỏ hàng)', 'bigspy-ai-theme-wp' ) ),
				$o( 'cart', 'badge_bg', 'color', __( 'Count badge background', 'bigspy-ai-theme-wp' ) ),
				$o( 'cart', 'badge_color', 'color', __( 'Count badge text', 'bigspy-ai-theme-wp' ) ),
			),
			bst_he_common( 'cart', array( 'icon', 'text' ) )
		),
	);
	foreach ( array( 'button_1', 'button_2' ) as $b ) {
		$n         = 'button_1' === $b ? '1' : '2';
		$s[ $b ] = array(
			sprintf( /* translators: %s: 1 or 2 */ __( 'Button %s', 'bigspy-ai-theme-wp' ), $n ),
			'',
			array(
				array( 'id' => 'header_' . $b . '_text', 'type' => 'text', 'label' => __( 'Text', 'bigspy-ai-theme-wp' ) ),
				array( 'id' => 'header_' . $b . '_url', 'type' => 'text', 'label' => __( 'Link (URL, tel:, mailto:)', 'bigspy-ai-theme-wp' ) ),
				array( 'id' => 'header_' . $b . '_style', 'type' => 'select', 'label' => __( 'Style', 'bigspy-ai-theme-wp' ), 'choices' => array( 'primary' => __( 'Primary', 'bigspy-ai-theme-wp' ), 'secondary' => __( 'Secondary', 'bigspy-ai-theme-wp' ), 'outline' => __( 'Outline', 'bigspy-ai-theme-wp' ), 'white' => __( 'White', 'bigspy-ai-theme-wp' ), 'link' => __( 'Text link', 'bigspy-ai-theme-wp' ), 'gradient' => __( 'Gradient', 'bigspy-ai-theme-wp' ), 'soft' => __( 'Soft', 'bigspy-ai-theme-wp' ) ) ),
				array( 'id' => 'header_' . $b . '_new_tab', 'type' => 'checkbox', 'label' => __( 'Open in new tab', 'bigspy-ai-theme-wp' ) ),
				$o( $b, 'icon', 'select', __( 'Icon', 'bigspy-ai-theme-wp' ), array( 'choices' => array( '' => __( 'None', 'bigspy-ai-theme-wp' ), 'phone' => 'Phone', 'zalo' => 'Zalo', 'cart' => 'Cart', 'mail' => 'Mail', 'arrow-right' => 'Arrow', 'message' => 'Message', 'pin' => 'Pin' ) ) ),
				$o( $b, 'size', 'select', __( 'Size', 'bigspy-ai-theme-wp' ), array( 'choices' => array( 'sm' => __( 'Small', 'bigspy-ai-theme-wp' ), 'md' => __( 'Medium', 'bigspy-ai-theme-wp' ), 'lg' => __( 'Large', 'bigspy-ai-theme-wp' ) ) ) ),
				$o( $b, 'radius', 'number', __( 'Corner radius (px, -1 = theme)', 'bigspy-ai-theme-wp' ), array( 'input_attrs' => array( 'min' => -1, 'max' => 50 ) ) ),
				$o( $b, 'pulse', 'checkbox', __( 'Pulse animation (draws attention)', 'bigspy-ai-theme-wp' ) ),
				$o( $b, 'h_colors', 'heading', __( 'Colors', 'bigspy-ai-theme-wp' ) ),
				$o( $b, 'bg', 'color', __( 'Background', 'bigspy-ai-theme-wp' ) ),
				$o( $b, 'color', 'color', __( 'Text', 'bigspy-ai-theme-wp' ) ),
				$o( $b, 'hover_bg', 'color', __( 'Background on hover', 'bigspy-ai-theme-wp' ) ),
				$o( $b, 'hover', 'color', __( 'Text on hover', 'bigspy-ai-theme-wp' ) ),
				$o( $b, 'margin_l', 'number', __( 'Space on the left (px)', 'bigspy-ai-theme-wp' ) ),
				$o( $b, 'margin_r', 'number', __( 'Space on the right (px)', 'bigspy-ai-theme-wp' ) ),
				$o( $b, 'hide_sticky', 'checkbox', __( 'Hide when the header is sticky (scrolled)', 'bigspy-ai-theme-wp' ) ),
			),
		);
	}
	foreach ( array( 'html_1', 'html_2', 'html_3' ) as $h ) {
		$s[ $h ] = array( strtoupper( str_replace( '_', ' ', $h ) ), __( 'Text, HTML or shortcodes.', 'bigspy-ai-theme-wp' ), array_merge( array( array( 'id' => 'header_' . $h, 'type' => 'html', 'label' => __( 'Content', 'bigspy-ai-theme-wp' ) ) ), bst_he_common( $h, array( 'text' ) ) ) );
	}
	$s['contact'] = array(
		__( 'Contact info', 'bigspy-ai-theme-wp' ),
		__( 'Phone, email, address and hours are entered in Contact info & social.', 'bigspy-ai-theme-wp' ),
		array_merge(
			array(
				$o( 'contact', 'items', 'select', __( 'Show', 'bigspy-ai-theme-wp' ), array( 'choices' => array( '' => __( 'Everything filled in', 'bigspy-ai-theme-wp' ), 'phone' => __( 'Phone only', 'bigspy-ai-theme-wp' ), 'phone_mail' => __( 'Phone + email', 'bigspy-ai-theme-wp' ), 'phone_pin' => __( 'Phone + address', 'bigspy-ai-theme-wp' ) ) ) ),
				$o( 'contact', 'icon_color', 'color', __( 'Icon color', 'bigspy-ai-theme-wp' ) ),
				$o( 'contact', 'gap', 'number', __( 'Space between items (px)', 'bigspy-ai-theme-wp' ) ),
			),
			bst_he_common( 'contact', array( 'text' ) )
		),
	);
	$s['social'] = array(
		__( 'Social icons', 'bigspy-ai-theme-wp' ),
		__( 'Profile links are entered in Contact info & social.', 'bigspy-ai-theme-wp' ),
		array_merge(
			array(
				$o( 'social', 'style', 'select', __( 'Style', 'bigspy-ai-theme-wp' ), array( 'choices' => array( '' => __( 'Plain icon', 'bigspy-ai-theme-wp' ), 'circle' => __( 'Filled circle', 'bigspy-ai-theme-wp' ), 'square' => __( 'Filled square', 'bigspy-ai-theme-wp' ), 'outline' => __( 'Outline circle', 'bigspy-ai-theme-wp' ) ) ) ),
				$o( 'social', 'brand', 'checkbox', __( 'Brand colors', 'bigspy-ai-theme-wp' ) ),
				$o( 'social', 'size', 'number', __( 'Icon size (px, 0 = default)', 'bigspy-ai-theme-wp' ) ),
				$o( 'social', 'gap', 'number', __( 'Space between icons (px)', 'bigspy-ai-theme-wp' ) ),
			),
			bst_he_common( 'social' )
		),
	);
	$s['divider'] = array(
		__( 'Divider', 'bigspy-ai-theme-wp' ),
		'',
		array(
			$o( 'divider', 'height', 'number', __( 'Height (px)', 'bigspy-ai-theme-wp' ) ),
			$o( 'divider', 'width', 'number', __( 'Thickness (px)', 'bigspy-ai-theme-wp' ) ),
			$o( 'divider', 'color', 'color', __( 'Color', 'bigspy-ai-theme-wp' ) ),
			$o( 'divider', 'margin_l', 'number', __( 'Space on the left (px)', 'bigspy-ai-theme-wp' ) ),
			$o( 'divider', 'margin_r', 'number', __( 'Space on the right (px)', 'bigspy-ai-theme-wp' ) ),
		),
	);
	return apply_filters( 'bst_header_element_schema', $s );
}

/* ------------------------------------------------------------------ Customizer */

add_filter(
	'bst_customizer_sections',
	function ( $sections ) {
		$i = 20;
		foreach ( bst_he_schema() as $key => $el ) {
			$back = '<button type="button" class="button bst-he-back" data-bst-he-back>← ' . esc_html__( 'Back to Header builder', 'bigspy-ai-theme-wp' ) . '</button>';
			$sections[ 'bst_he_' . $key ] = array(
				'title'       => sprintf( /* translators: %s: element name */ __( 'Header element: %s', 'bigspy-ai-theme-wp' ), $el[0] ),
				'panel'       => 'bst_header',
				'priority'    => $i++,
				'description' => $back . ( $el[1] ? '<p>' . esc_html( $el[1] ) . '</p>' : '' ),
			);
		}
		return $sections;
	}
);

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		// Options that already exist elsewhere move into the element sections (keeping their own
		// definition, so saved values and sanitizing stay the same); select choices are merged.
		$orig = array();
		foreach ( $fields as $i => $field ) {
			if ( isset( $field['id'] ) && 0 !== strpos( $field['id'], 'he_' ) ) {
				$orig[ $field['id'] ] = $i;
			}
		}
		$moved = array();
		foreach ( bst_he_schema() as $key => $el ) {
			$p = 1;
			foreach ( $el[2] as $field ) {
				if ( 0 !== strpos( $field['id'], 'he_' ) && isset( $orig[ $field['id'] ] ) ) {
					$base = $fields[ $orig[ $field['id'] ] ];
					if ( isset( $base['choices'], $field['choices'] ) ) {
						$base['choices'] = $base['choices'] + $field['choices'];
					}
					$field = $base;
					$moved[ $field['id'] ] = true;
				}
				$field['section']  = 'bst_he_' . $key;
				$field['priority'] = $p++;
				$fields[]          = $field;
			}
		}
		foreach ( $orig as $id => $i ) {
			if ( isset( $moved[ $id ] ) ) {
				unset( $fields[ $i ] );
			}
		}
		return array_values( $fields );
	},
	20
);

add_filter(
	'bst_defaults',
	function ( $d ) {
		foreach ( bst_he_schema() as $el ) {
			foreach ( $el[2] as $field ) {
				if ( 0 === strpos( $field['id'], 'he_' ) && ! isset( $d[ $field['id'] ] ) && 'heading' !== $field['type'] ) {
					$d[ $field['id'] ] = in_array( $field['type'], array( 'number' ), true ) ? ( preg_match( '/radius$/', $field['id'] ) ? -1 : 0 ) : ( 'checkbox' === $field['type'] ? false : '' );
				}
			}
		}
		$d['he_button_1_size'] = 'sm';
		$d['he_button_2_size'] = 'sm';
		$d['he_nav_icon_type'] = 'menu';
		return $d;
	}
);

/* ------------------------------------------------------------------ Front end */

function bst_he( $key, $opt ) {
	return bst_mod( 'he_' . $key . '_' . $opt );
}

/** Extra classes on the element wrapper. */
function bst_he_classes( $key ) {
	$c = array();
	$style = (string) bst_he( $key, 'icon_style' );
	if ( in_array( $style, array( 'circle', 'square', 'outline', 'soft' ), true ) ) {
		$c[] = 'bs-he-icon--' . $style;
	}
	if ( bst_he( $key, 'hide_sticky' ) ) {
		$c[] = 'bs-he-hide-sticky';
	}
	if ( 'account' === $key ) {
		$c[] = 'bs-he-label--' . ( bst_choice( (string) bst_he( 'account', 'show_label' ), array( '', 'always', 'never' ), '' ) ?: 'desktop' );
	}
	if ( 'social' === $key ) {
		$soc = (string) bst_he( 'social', 'style' );
		if ( $soc ) {
			$c[] = 'bs-he-social--' . sanitize_html_class( $soc );
		}
		if ( bst_he( 'social', 'brand' ) ) {
			$c[] = 'bs-he-social--brand';
		}
	}
	if ( ( 'button_1' === $key || 'button_2' === $key ) && bst_he( $key, 'pulse' ) ) {
		$c[] = 'bs-he-pulse';
	}
	return $c ? ' ' . implode( ' ', array_map( 'sanitize_html_class', $c ) ) : '';
}

/** Scoped CSS for every element option. */
function bst_he_css() {
	$css = '';
	$px  = function ( $v ) {
		return is_numeric( $v ) && (int) $v ? (int) $v . 'px' : '';
	};
	$col = function ( $v ) {
		return sanitize_hex_color( (string) $v );
	};
	foreach ( array_keys( bst_he_schema() ) as $key ) {
		$sel   = '.bs-header-el--' . str_replace( '_', '-', $key );
		$rules = array();
		$hover = array();
		if ( '' !== ( $c = (string) $col( bst_he( $key, 'color' ) ) ) && 'divider' !== $key ) {
			$rules[] = "$sel, $sel a, $sel .bs-icon-btn, $sel button:not(.bs-btn) { color: $c }";
		}
		if ( '' !== ( $c = (string) $col( bst_he( $key, 'hover' ) ) ) && 'button_1' !== $key && 'button_2' !== $key ) {
			$rules[] = "$sel a:hover, $sel .bs-icon-btn:hover, $sel button:not(.bs-btn):hover { color: $c }";
		}
		$m = array();
		if ( is_numeric( bst_he( $key, 'margin_l' ) ) && (int) bst_he( $key, 'margin_l' ) ) {
			$m[] = 'margin-left:' . (int) bst_he( $key, 'margin_l' ) . 'px';
		}
		if ( is_numeric( bst_he( $key, 'margin_r' ) ) && (int) bst_he( $key, 'margin_r' ) ) {
			$m[] = 'margin-right:' . (int) bst_he( $key, 'margin_r' ) . 'px';
		}
		if ( $m ) {
			$rules[] = "$sel { " . implode( ';', $m ) . ' }';
		}
		if ( $v = $px( bst_he( $key, 'font_size' ) ) ) {
			$rules[] = "$sel, $sel a, $sel span { font-size: $v }";
		}
		if ( $v = (string) bst_he( $key, 'weight' ) ) {
			$rules[] = "$sel, $sel a { font-weight: " . (int) $v . ' }';
		}
		if ( $v = (string) bst_he( $key, 'transform' ) ) {
			$rules[] = "$sel, $sel a { text-transform: " . sanitize_key( $v ) . ' }';
		}
		if ( $v = $px( bst_he( $key, 'icon_size' ) ) ) {
			$rules[] = "$sel svg.bs-icon { width: $v; height: $v }";
		}
		if ( $c = $col( bst_he( $key, 'icon_bg' ) ) ) {
			$rules[] = "$sel { --bs-he-icon-bg: $c }";
		}
		$css .= implode( '', $rules );
	}

	// Element-specific rules.
	$r = array();
	if ( $v = $px( bst_he( 'logo', 'height' ) ) ) {
		$r[] = ".bs-logo__img { max-height: $v; width: auto }";
	}
	if ( $v = $px( bst_he( 'logo', 'sticky_width' ) ) ) {
		$r[] = ".bs-header.is-stuck .bs-logo__img { max-width: $v }";
	}
	if ( $v = $px( bst_he( 'logo', 'text_size' ) ) ) {
		$r[] = ".bs-logo__text { font-size: $v }";
	}
	if ( $v = $px( bst_he( 'nav', 'gap' ) ) ) {
		$r[] = ".bs-nav--primary > .bs-menu { gap: $v }";
	}
	if ( $c = $col( bst_he( 'nav', 'active' ) ) ) {
		$r[] = ".bs-nav--primary .current-menu-item > a, .bs-nav--primary .current-menu-ancestor > a { color: $c }";
	}
	if ( $c = $col( bst_he( 'nav', 'indicator' ) ) ) {
		$r[] = ".bs-nav--primary { --bs-nav-indicator: $c } .bs-nav--primary .bs-menu--underline > li > a::after { background: $c } .bs-nav--primary .bs-menu--pills > li.current-menu-item > a, .bs-nav--primary .bs-menu--pills > li > a:hover { background: $c; color: #fff }";
	}
	if ( $c = $col( bst_he( 'nav', 'drop_bg' ) ) ) {
		$r[] = ".bs-nav--primary .sub-menu { background: $c }";
	}
	if ( $c = $col( bst_he( 'nav', 'drop_color' ) ) ) {
		$r[] = ".bs-nav--primary .sub-menu a { color: $c }";
	}
	if ( is_numeric( bst_he( 'nav', 'drop_radius' ) ) && (int) bst_he( 'nav', 'drop_radius' ) ) {
		$r[] = '.bs-nav--primary .sub-menu { border-radius: ' . (int) bst_he( 'nav', 'drop_radius' ) . 'px }';
	}
	if ( $v = $px( bst_he( 'nav_secondary', 'gap' ) ) ) {
		$r[] = ".bs-nav--secondary > .bs-menu { gap: $v }";
	}
	if ( $v = $px( bst_he( 'search_form', 'width' ) ) ) {
		$r[] = ".bs-header-el--search-form { width: $v; max-width: 100% } .bs-header-el--search-form form { width: 100% }";
	}
	if ( $v = $px( bst_he( 'search_form', 'height' ) ) ) {
		$r[] = ".bs-header-el--search-form input[type=search], .bs-header-el--search-form button { height: $v; min-height: $v }";
	}
	if ( is_numeric( bst_he( 'search_form', 'radius' ) ) && (int) bst_he( 'search_form', 'radius' ) >= 0 ) {
		$rad = (int) bst_he( 'search_form', 'radius' ) . 'px';
		$r[] = ".bs-header-el--search-form form, .bs-header-el--search-form input[type=search] { border-radius: $rad } .bs-header-el--search-form button { border-radius: $rad }";
	}
	if ( $c = $col( bst_he( 'search_form', 'bg' ) ) ) {
		$r[] = ".bs-header-el--search-form input[type=search] { background: $c }";
	}
	if ( $c = $col( bst_he( 'search_form', 'border' ) ) ) {
		$r[] = ".bs-header-el--search-form input[type=search] { border-color: $c }";
	}
	if ( $c = $col( bst_he( 'search_form', 'button' ) ) ) {
		$r[] = ".bs-header-el--search-form button { background: $c; border-color: $c; color: #fff }";
	}
	if ( $c = $col( bst_he( 'cart', 'badge_bg' ) ) ) {
		$r[] = ".bs-cart__count { background: $c }";
	}
	if ( $c = $col( bst_he( 'cart', 'badge_color' ) ) ) {
		$r[] = ".bs-cart__count { color: $c }";
	}
	if ( $c = $col( bst_he( 'contact', 'icon_color' ) ) ) {
		$r[] = ".bs-header-el--contact .bs-icon { color: $c }";
	}
	if ( $v = $px( bst_he( 'contact', 'gap' ) ) ) {
		$r[] = ".bs-header-el--contact .bs-contact { gap: $v }";
	}
	if ( $v = $px( bst_he( 'social', 'size' ) ) ) {
		$r[] = ".bs-header-el--social svg.bs-icon { width: $v; height: $v }";
	}
	if ( $v = $px( bst_he( 'social', 'gap' ) ) ) {
		$r[] = ".bs-header-el--social .bs-social { gap: $v }";
	}
	$dv = array();
	if ( $v = $px( bst_he( 'divider', 'height' ) ) ) {
		$dv[] = "height: $v";
	}
	if ( $v = $px( bst_he( 'divider', 'width' ) ) ) {
		$dv[] = "width: $v";
	}
	if ( $c = $col( bst_he( 'divider', 'color' ) ) ) {
		$dv[] = "background: $c";
	}
	if ( $dv ) {
		$r[] = '.bs-header-divider { ' . implode( ';', $dv ) . ' }';
	}
	foreach ( array( 'button_1', 'button_2' ) as $b ) {
		$sel = '.bs-header-el--' . str_replace( '_', '-', $b ) . ' .bs-btn';
		$bb  = array();
		if ( $c = $col( bst_he( $b, 'bg' ) ) ) {
			$bb[] = "background: $c; border-color: $c";
		}
		if ( $c = $col( bst_he( $b, 'color' ) ) ) {
			$bb[] = "color: $c";
		}
		if ( is_numeric( bst_he( $b, 'radius' ) ) && (int) bst_he( $b, 'radius' ) >= 0 ) {
			$bb[] = 'border-radius: ' . (int) bst_he( $b, 'radius' ) . 'px';
		}
		if ( $bb ) {
			$r[] = "$sel { " . implode( ';', $bb ) . ' }';
		}
		$bh = array();
		if ( $c = $col( bst_he( $b, 'hover_bg' ) ) ) {
			$bh[] = "background: $c; border-color: $c";
		}
		if ( $c = $col( bst_he( $b, 'hover' ) ) ) {
			$bh[] = "color: $c";
		}
		if ( $bh ) {
			$r[] = "$sel:hover { " . implode( ';', $bh ) . ' }';
		}
	}
	$gutter = (int) bst_mod( 'fluid_gutter' );
	if ( $gutter > 0 ) {
		$r[] = '@media (min-width:550px){:root{--bs-gutter-fluid:' . min( 200, $gutter ) . 'px}}';
	}
	return $css . implode( '', $r );
}

add_action(
	'wp_enqueue_scripts',
	function () {
		$css = bst_he_css();
		if ( $css ) {
			wp_add_inline_style( 'bst-main', wp_strip_all_tags( $css ) );
		}
	},
	30
);

/** Customizer: gear opens the element section; "Back" returns to the builder. */
add_action(
	'customize_controls_print_footer_scripts',
	function () {
		?>
		<script>
		(function ($, api) {
			if (!api) { return; }
			api.bind('ready', function () {
				$(document).on('click', '[data-bst-he-back]', function (e) {
					e.preventDefault();
					var s = api.section('bst_header_builder');
					if (s) { s.focus(); }
				});
			});
		})(window.jQuery, window.wp && window.wp.customize);
		</script>
		<?php
	}
);
