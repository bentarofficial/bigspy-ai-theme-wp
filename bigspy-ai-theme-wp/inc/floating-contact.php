<?php
/**
 * Floating contact buttons (Customizer → Footer → Floating contact buttons).
 *
 * Desktop and mobile each pick a style: stacked circles, pills with labels,
 * one "all in one" toggle, a bottom bar (mobile) or hidden. Position, offsets,
 * size, gap, effect, labels, phone number bar, appear-after-scroll and per-button
 * label/color/order are all configurable. Settings saved by older versions
 * (float_phone, float_zalo, float_mobile_bar…) keep working.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Button keys in default order => [ icon, default label, default color ]. */
function bst_float_buttons() {
	return array(
		'phone'     => array( 'phone', __( 'Call now', 'bigspy-ai-theme-wp' ), '#e53935' ),
		'phone2'    => array( 'phone', __( 'Hotline 2', 'bigspy-ai-theme-wp' ), '#f57c00' ),
		'zalo'      => array( 'zalo', 'Zalo', '#0068ff' ),
		'messenger' => array( 'messenger', 'Messenger', '#0084ff' ),
		'whatsapp'  => array( 'whatsapp', 'WhatsApp', '#25d366' ),
		'telegram'  => array( 'telegram', 'Telegram', '#229ed9' ),
		'viber'     => array( 'viber', 'Viber', '#7360f2' ),
		'facebook'  => array( 'facebook', 'Facebook', '#1877f2' ),
		'tiktok'    => array( 'tiktok', 'TikTok', '#111111' ),
		'youtube'   => array( 'youtube', 'YouTube', '#ff0000' ),
		'instagram' => array( 'instagram', 'Instagram', '#e4405f' ),
		'map'       => array( 'pin', __( 'Directions', 'bigspy-ai-theme-wp' ), '#16a34a' ),
		'email'     => array( 'mail', 'Email', '#64748b' ),
		'custom'    => array( 'message', __( 'Contact', 'bigspy-ai-theme-wp' ), '#0a4d8c' ),
	);
}

function bst_float_styles( $mobile = false ) {
	$styles = array(
		'stack' => __( 'Round icons, stacked', 'bigspy-ai-theme-wp' ),
		'pill'  => __( 'Buttons with text', 'bigspy-ai-theme-wp' ),
		'aio'   => __( 'All in one (one button opens the list)', 'bigspy-ai-theme-wp' ),
	);
	if ( $mobile ) {
		$styles = array( 'bar' => __( 'Bottom bar (full width)', 'bigspy-ai-theme-wp' ) ) + $styles + array( 'hide' => __( 'Hidden', 'bigspy-ai-theme-wp' ) );
	} else {
		$styles['hide'] = __( 'Hidden', 'bigspy-ai-theme-wp' );
	}
	return $styles;
}

add_filter(
	'bst_defaults',
	function ( $d ) {
		$add = array(
			'float_desktop_style'   => 'stack',
			'float_mobile_style'    => get_theme_mod( 'float_mobile_bar' ) ? 'bar' : 'stack',
			'float_offset_x'        => 20,
			'float_offset_y'        => 24,
			'float_offset_y_mobile' => 16,
			'float_size'            => 54,
			'float_gap'             => 12,
			'float_effect'          => 'ring',
			'float_effect_target'   => 'first',
			'float_labels'          => 'hover',
			'float_phone_bar'       => false,
			'float_show_after'      => 0,
			'float_bar_style'       => 'solid',
			'float_bar_labels'      => true,
			'float_aio_label'       => __( 'Contact', 'bigspy-ai-theme-wp' ),
			'float_aio_color'       => '#0a4d8c',
			'float_order'           => '',
		);
		foreach ( array_keys( bst_float_buttons() ) as $key ) {
			$add[ 'float_' . $key ]            = $d[ 'float_' . $key ] ?? '';
			$add[ 'float_label_' . $key ]      = '';
			$add[ 'float_color_' . $key ]      = '';
		}
		return $d + $add;
	}
);

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$s = 'bst_float';
		$p = 1;
		$f = function ( $field ) use ( &$fields, &$p, $s ) {
			$field['section']  = $s;
			$field['priority'] = $p++;
			$fields[]          = $field;
		};
		$f( array( 'id' => 'float_enable', 'type' => 'checkbox', 'label' => __( 'Enable floating contact buttons', 'bigspy-ai-theme-wp' ) ) );

		$f( array( 'id' => 'float_h_display', 'type' => 'heading', 'label' => __( 'Display', 'bigspy-ai-theme-wp' ) ) );
		$f( array( 'id' => 'float_desktop_style', 'type' => 'select', 'label' => __( 'Style on desktop & tablet', 'bigspy-ai-theme-wp' ), 'choices' => bst_float_styles() ) );
		$f( array( 'id' => 'float_mobile_style', 'type' => 'select', 'label' => __( 'Style on mobile', 'bigspy-ai-theme-wp' ), 'choices' => bst_float_styles( true ) ) );
		$f( array( 'id' => 'float_position', 'type' => 'radio', 'label' => __( 'Side', 'bigspy-ai-theme-wp' ), 'choices' => array( 'left' => __( 'Left', 'bigspy-ai-theme-wp' ), 'right' => __( 'Right', 'bigspy-ai-theme-wp' ) ) ) );
		$f( array( 'id' => 'float_offset_x', 'type' => 'number', 'label' => __( 'Distance from the side (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 0, 'max' => 400 ) ) );
		$f( array( 'id' => 'float_offset_y', 'type' => 'number', 'label' => __( 'Distance from the bottom – desktop (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 0, 'max' => 800 ) ) );
		$f( array( 'id' => 'float_offset_y_mobile', 'type' => 'number', 'label' => __( 'Distance from the bottom – mobile (px)', 'bigspy-ai-theme-wp' ), 'description' => __( 'Not used by the bottom bar.', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 0, 'max' => 600 ) ) );
		$f( array( 'id' => 'float_size', 'type' => 'number', 'label' => __( 'Button size (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 36, 'max' => 90 ) ) );
		$f( array( 'id' => 'float_gap', 'type' => 'number', 'label' => __( 'Space between buttons (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 0, 'max' => 40 ) ) );
		$f( array( 'id' => 'float_labels', 'type' => 'select', 'label' => __( 'Text next to round icons', 'bigspy-ai-theme-wp' ), 'choices' => array( 'hover' => __( 'Show on hover', 'bigspy-ai-theme-wp' ), 'always' => __( 'Always show', 'bigspy-ai-theme-wp' ), 'never' => __( 'Never', 'bigspy-ai-theme-wp' ) ) ) );
		$f( array( 'id' => 'float_phone_bar', 'type' => 'checkbox', 'label' => __( 'Show the phone number next to the call button (desktop)', 'bigspy-ai-theme-wp' ) ) );
		$f( array( 'id' => 'float_show_after', 'type' => 'number', 'label' => __( 'Appear after scrolling (px, 0 = always)', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 0, 'max' => 3000 ) ) );

		$f( array( 'id' => 'float_h_effect', 'type' => 'heading', 'label' => __( 'Effect', 'bigspy-ai-theme-wp' ) ) );
		$f( array( 'id' => 'float_effect', 'type' => 'select', 'label' => __( 'Attention effect', 'bigspy-ai-theme-wp' ), 'choices' => array( 'ring' => __( 'Ringing + ripple waves', 'bigspy-ai-theme-wp' ), 'pulse' => __( 'Pulse waves', 'bigspy-ai-theme-wp' ), 'shake' => __( 'Shake', 'bigspy-ai-theme-wp' ), 'bounce' => __( 'Bounce', 'bigspy-ai-theme-wp' ), 'none' => __( 'None', 'bigspy-ai-theme-wp' ) ) ) );
		$f( array( 'id' => 'float_effect_target', 'type' => 'radio', 'label' => __( 'Apply effect to', 'bigspy-ai-theme-wp' ), 'choices' => array( 'first' => __( 'First button only', 'bigspy-ai-theme-wp' ), 'all' => __( 'All buttons', 'bigspy-ai-theme-wp' ) ) ) );

		$f( array( 'id' => 'float_h_bar', 'type' => 'heading', 'label' => __( 'Mobile bottom bar', 'bigspy-ai-theme-wp' ) ) );
		$f( array( 'id' => 'float_bar_style', 'type' => 'select', 'label' => __( 'Bar look', 'bigspy-ai-theme-wp' ), 'choices' => array( 'solid' => __( 'Each button in its own color', 'bigspy-ai-theme-wp' ), 'light' => __( 'White bar, colored icons', 'bigspy-ai-theme-wp' ), 'dark' => __( 'Dark bar, colored icons', 'bigspy-ai-theme-wp' ) ) ) );
		$f( array( 'id' => 'float_bar_labels', 'type' => 'checkbox', 'label' => __( 'Show text under icons', 'bigspy-ai-theme-wp' ) ) );

		$f( array( 'id' => 'float_h_aio', 'type' => 'heading', 'label' => __( 'All in one button', 'bigspy-ai-theme-wp' ) ) );
		$f( array( 'id' => 'float_aio_label', 'type' => 'text', 'label' => __( 'Text', 'bigspy-ai-theme-wp' ) ) );
		$f( array( 'id' => 'float_aio_color', 'type' => 'color', 'label' => __( 'Color', 'bigspy-ai-theme-wp' ) ) );

		return $fields;
	}
);

/** Placeholder hint shown in the editor for each button type. */
function bst_float_hints() {
	return array(
		'phone'     => __( 'Phone number', 'bigspy-ai-theme-wp' ),
		'phone2'    => __( 'Second phone number', 'bigspy-ai-theme-wp' ),
		'zalo'      => __( 'Zalo phone number or zalo.me link', 'bigspy-ai-theme-wp' ),
		'messenger' => __( 'Page username or m.me link', 'bigspy-ai-theme-wp' ),
		'whatsapp'  => __( 'Number with country code', 'bigspy-ai-theme-wp' ),
		'telegram'  => __( 'Username or t.me link', 'bigspy-ai-theme-wp' ),
		'viber'     => __( 'Number with country code', 'bigspy-ai-theme-wp' ),
		'facebook'  => __( 'Facebook page link', 'bigspy-ai-theme-wp' ),
		'tiktok'    => __( 'TikTok link', 'bigspy-ai-theme-wp' ),
		'youtube'   => __( 'YouTube link', 'bigspy-ai-theme-wp' ),
		'instagram' => __( 'Instagram link', 'bigspy-ai-theme-wp' ),
		'map'       => __( 'Google Maps link or address', 'bigspy-ai-theme-wp' ),
		'email'     => __( 'Email address', 'bigspy-ai-theme-wp' ),
		'custom'    => __( 'Any link (contact page, form, booking…)', 'bigspy-ai-theme-wp' ),
	);
}

/**
 * Button list as saved by the Customizer list control (JSON in `float_items`):
 * [ { k: type, id, on, v: value, l: label, s: second line, c: bg, ic: icon color, dev, fx, nt } ].
 * Sites saved before 1.36 are converted from the old per-button settings.
 */
function bst_float_config() {
	$raw  = json_decode( (string) get_theme_mod( 'float_items', '' ), true );
	$list = array();
	if ( is_array( $raw ) && $raw ) {
		foreach ( $raw as $row ) {
			if ( is_array( $row ) && isset( $row['k'] ) && isset( bst_float_buttons()[ $row['k'] ] ) ) {
				$list[] = $row;
			}
		}
		return $list;
	}
	$order = array_filter( array_map( 'sanitize_key', explode( ',', (string) bst_mod( 'float_order' ) ) ) );
	$keys  = array_unique( array_merge( array_values( array_intersect( $order, array_keys( bst_float_buttons() ) ) ), array_keys( bst_float_buttons() ) ) );
	foreach ( $keys as $key ) {
		$v      = trim( (string) bst_mod( 'float_' . $key ) );
		$list[] = array(
			'k'  => $key,
			'id' => $key,
			'on' => '' !== $v,
			'v'  => $v,
			'l'  => trim( (string) bst_mod( 'float_label_' . $key ) ),
			'c'  => (string) bst_mod( 'float_color_' . $key ),
		);
	}
	return $list;
}

function bst_float_sanitize_items( $value ) {
	$raw = json_decode( (string) $value, true );
	if ( ! is_array( $raw ) ) {
		return '';
	}
	$out = array();
	foreach ( array_slice( $raw, 0, 40 ) as $row ) {
		if ( ! is_array( $row ) || ! isset( $row['k'] ) || ! isset( bst_float_buttons()[ $row['k'] ] ) ) {
			continue;
		}
		$out[] = array(
			'k'   => sanitize_key( $row['k'] ),
			'id'  => sanitize_key( $row['id'] ?? $row['k'] ),
			'on'  => ! empty( $row['on'] ),
			'v'   => sanitize_text_field( (string) ( $row['v'] ?? '' ) ),
			'l'   => sanitize_text_field( (string) ( $row['l'] ?? '' ) ),
			's'   => sanitize_text_field( (string) ( $row['s'] ?? '' ) ),
			'c'   => (string) sanitize_hex_color( (string) ( $row['c'] ?? '' ) ),
			'ic'  => (string) sanitize_hex_color( (string) ( $row['ic'] ?? '' ) ),
			'dev' => bst_choice( (string) ( $row['dev'] ?? '' ), array( '', 'desktop', 'mobile' ), '' ),
			'fx'  => bst_choice( (string) ( $row['fx'] ?? '' ), array( '', 'none', 'ring', 'pulse', 'shake', 'bounce' ), '' ),
			'nt'  => ! empty( $row['nt'] ),
		);
	}
	return wp_json_encode( $out );
}

/** Link for one button value. Returns [ url, external ]. */
function bst_float_url( $key, $value ) {
	switch ( $key ) {
		case 'phone':
		case 'phone2':
			$num = preg_replace( '/[^0-9+]/', '', $value );
			return array( $num ? 'tel:' . $num : '', false );
		case 'zalo':
			return array( preg_match( '/^[0-9 +.]+$/', $value ) ? 'https://zalo.me/' . preg_replace( '/[^0-9]/', '', $value ) : $value, true );
		case 'messenger':
			return array( preg_match( '#^https?://#', $value ) ? $value : 'https://m.me/' . rawurlencode( ltrim( $value, '@' ) ), true );
		case 'whatsapp':
			return array( 'https://wa.me/' . preg_replace( '/[^0-9]/', '', $value ), true );
		case 'telegram':
			return array( preg_match( '#^https?://#', $value ) ? $value : 'https://t.me/' . rawurlencode( ltrim( $value, '@' ) ), true );
		case 'viber':
			return array( 'viber://chat?number=' . rawurlencode( preg_replace( '/[^0-9+]/', '', $value ) ), false );
		case 'map':
			return array( preg_match( '#^https?://#', $value ) ? $value : 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $value ), true );
		case 'email':
			$email = sanitize_email( $value );
			return array( $email ? 'mailto:' . $email : '', false );
		default:
			return array( $value, (bool) preg_match( '#^https?://#', $value ) && false === strpos( $value, home_url() ) );
	}
}

/** Enabled buttons in display order: [ key, icon, url, label, color, external, sub, extra ]. */
function bst_float_items() {
	$items   = array();
	$buttons = bst_float_buttons();
	foreach ( bst_float_config() as $row ) {
		$key   = $row['k'];
		$value = trim( (string) ( $row['v'] ?? '' ) );
		if ( empty( $row['on'] ) || '' === $value ) {
			continue;
		}
		list( $url, $external ) = bst_float_url( $key, $value );
		if ( ! $url ) {
			continue;
		}
		$btn     = $buttons[ $key ];
		$label   = trim( (string) ( $row['l'] ?? '' ) );
		$color   = sanitize_hex_color( (string) ( $row['c'] ?? '' ) );
		$sub     = trim( (string) ( $row['s'] ?? '' ) );
		if ( '' === $sub && ( 'phone' === $key || 'phone2' === $key ) ) {
			$sub = $value;
		}
		$items[] = array(
			$key,
			$btn[0],
			$url,
			'' !== $label ? $label : $btn[1],
			$color ? $color : $btn[2],
			$external || ! empty( $row['nt'] ),
			$sub,
			array(
				'ic'  => sanitize_hex_color( (string) ( $row['ic'] ?? '' ) ),
				'dev' => (string) ( $row['dev'] ?? '' ),
				'fx'  => (string) ( $row['fx'] ?? '' ),
			),
		);
	}
	return $items;
}

/* ------------------------------------------------------------------ Customizer list control */

add_action(
	'customize_register',
	function ( $wp_customize ) {
		if ( ! class_exists( 'BST_Float_Items_Control' ) ) {
			/** Sortable, collapsible list of floating contact buttons. */
			class BST_Float_Items_Control extends WP_Customize_Control {
				public $type = 'bst-float-items';
				public function render_content() {
					echo '<span class="customize-control-title">' . esc_html( $this->label ) . '</span>';
					if ( $this->description ) {
						echo '<p class="description">' . esc_html( $this->description ) . '</p>';
					}
					echo '<div class="bst-fi" data-bst-float-items></div>';
					echo '<input type="hidden" class="bst-fi__value" ' . $this->get_link() . ' value="' . esc_attr( (string) $this->value() ) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}
		}
		$wp_customize->add_setting(
			'float_items',
			array(
				'default'           => '',
				'transport'         => 'refresh',
				'sanitize_callback' => 'bst_float_sanitize_items',
			)
		);
		$wp_customize->add_control(
			new BST_Float_Items_Control(
				$wp_customize,
				'float_items',
				array(
					'section'     => 'bst_float',
					'priority'    => 200,
					'label'       => __( 'Buttons', 'bigspy-ai-theme-wp' ),
					'description' => __( 'Switch buttons on/off, click a button to edit it, drag ☰ to change the order.', 'bigspy-ai-theme-wp' ),
				)
			)
		);
	},
	30
);

add_action(
	'customize_controls_enqueue_scripts',
	function () {
		$meta = array();
		foreach ( bst_float_buttons() as $key => $btn ) {
			$meta[ $key ] = array(
				'label' => $btn[1],
				'color' => $btn[2],
				'icon'  => bst_icon( $btn[0], 18 ),
				'hint'  => bst_float_hints()[ $key ] ?? '',
			);
		}
		wp_add_inline_script(
			'bst-customizer',
			'window.bstFloatItems = ' . wp_json_encode(
				array(
					'meta'  => $meta,
					'items' => bst_float_config(),
					'i18n'  => array(
						'value'  => __( 'Link / number', 'bigspy-ai-theme-wp' ),
						'label'  => __( 'Text', 'bigspy-ai-theme-wp' ),
						'sub'    => __( 'Second line (e.g. phone number, “Free consultation”)', 'bigspy-ai-theme-wp' ),
						'bg'     => __( 'Button color', 'bigspy-ai-theme-wp' ),
						'ic'     => __( 'Icon color', 'bigspy-ai-theme-wp' ),
						'dev'    => __( 'Show on', 'bigspy-ai-theme-wp' ),
						'all'    => __( 'All devices', 'bigspy-ai-theme-wp' ),
						'desk'   => __( 'Desktop & tablet only', 'bigspy-ai-theme-wp' ),
						'mob'    => __( 'Mobile only', 'bigspy-ai-theme-wp' ),
						'fx'     => __( 'Effect for this button', 'bigspy-ai-theme-wp' ),
						'fxDef'  => __( 'Same as the general setting', 'bigspy-ai-theme-wp' ),
						'none'   => __( 'None', 'bigspy-ai-theme-wp' ),
						'ring'   => __( 'Ringing + waves', 'bigspy-ai-theme-wp' ),
						'pulse'  => __( 'Pulse waves', 'bigspy-ai-theme-wp' ),
						'shake'  => __( 'Shake', 'bigspy-ai-theme-wp' ),
						'bounce' => __( 'Bounce', 'bigspy-ai-theme-wp' ),
						'nt'     => __( 'Open in a new tab', 'bigspy-ai-theme-wp' ),
						'reset'  => __( 'Default', 'bigspy-ai-theme-wp' ),
						'add'    => __( '+ Add a custom link button', 'bigspy-ai-theme-wp' ),
						'remove' => __( 'Remove this button', 'bigspy-ai-theme-wp' ),
						'empty'  => __( 'not set', 'bigspy-ai-theme-wp' ),
					),
				)
			) . ';',
			'before'
		);
	},
	20
);

function bst_float_mobile_style() {
	return bst_choice( bst_mod( 'float_mobile_style' ), array_keys( bst_float_styles( true ) ), 'stack' );
}

function bst_floating_contact() {
	if ( ! bst_mod( 'float_enable' ) || is_page_template( 'templates/blank.php' ) ) {
		return;
	}
	$items = bst_float_items();
	if ( ! $items ) {
		return;
	}
	$desktop = bst_choice( bst_mod( 'float_desktop_style' ), array_keys( bst_float_styles() ), 'stack' );
	$mobile  = bst_float_mobile_style();
	$side    = bst_choice( bst_mod( 'float_position' ), array( 'left', 'right' ), 'right' );
	$effect  = bst_choice( bst_mod( 'float_effect' ), array( 'ring', 'pulse', 'shake', 'bounce', 'none' ), 'ring' );
	$labels  = bst_choice( bst_mod( 'float_labels' ), array( 'hover', 'always', 'never' ), 'hover' );
	$after   = absint( bst_mod( 'float_show_after' ) );

	$classes = array(
		'bs-fc',
		'bs-fc--' . $side,
		'bs-fc--d-' . $desktop,
		'bs-fc--m-' . $mobile,
		'bs-fc--fx-' . $effect,
		'bs-fc--fx-' . bst_choice( bst_mod( 'float_effect_target' ), array( 'first', 'all' ), 'first' ),
		'bs-fc--labels-' . $labels,
		'bs-fc--bar-' . bst_choice( bst_mod( 'float_bar_style' ), array( 'solid', 'light', 'dark' ), 'solid' ),
	);
	if ( ! bst_mod( 'float_bar_labels' ) ) {
		$classes[] = 'bs-fc--bar-nolabels';
	}
	if ( bst_mod( 'float_phone_bar' ) ) {
		$classes[] = 'has-phone-bar';
	}
	if ( $after ) {
		$classes[] = 'is-waiting';
	}
	$style = bst_style_vars(
		array(
			'--fc-x'     => bst_clamp( bst_mod( 'float_offset_x' ), 0, 400, 20 ) . 'px',
			'--fc-y'     => bst_clamp( bst_mod( 'float_offset_y' ), 0, 800, 24 ) . 'px',
			'--fc-y-m'   => bst_clamp( bst_mod( 'float_offset_y_mobile' ), 0, 600, 16 ) . 'px',
			'--fc-size'  => bst_clamp( bst_mod( 'float_size' ), 36, 90, 54 ) . 'px',
			'--fc-gap'   => bst_clamp( bst_mod( 'float_gap' ), 0, 40, 12 ) . 'px',
			'--fc-count' => count( $items ),
			'--fc-aio'   => sanitize_hex_color( (string) bst_mod( 'float_aio_color' ) ),
		)
	);

	$links = '';
	foreach ( $items as $i => $item ) {
		list( $key, $icon, $url, $label, $color, $external, $sub ) = $item;
		$extra = $item[7] ?? array();
		$more  = ( ! empty( $extra['dev'] ) ? ' bs-fc__btn--only-' . sanitize_html_class( $extra['dev'] ) : '' ) . ( ! empty( $extra['fx'] ) ? ' bs-fc__btn--fx-' . sanitize_html_class( $extra['fx'] ) : '' ) . ( $sub ? ' has-sub' : '' );
		$color = $color . ( ! empty( $extra['ic'] ) ? ';--fc-ic:' . $extra['ic'] : '' );
		$links .= sprintf(
			'<a class="bs-fc__btn bs-fc__btn--%1$s%2$s" href="%3$s"%4$s aria-label="%5$s" style="--fc-c:%6$s"><span class="bs-fc__icon">%7$s</span><span class="bs-fc__label">%8$s</span>%9$s</a>',
			esc_attr( $key ),
			( 0 === $i ? ' is-first' : '' ) . esc_attr( $more ),
			esc_url( $url, array( 'http', 'https', 'tel', 'mailto', 'viber' ) ),
			$external ? ' target="_blank" rel="noopener"' : '',
			esc_attr( $label ),
			esc_attr( $color ),
			bst_icon( $icon, 24 ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_html( $label ),
			$sub ? '<span class="bs-fc__phone">' . esc_html( $sub ) . '</span>' : ''
		);
	}

	$aio_label = trim( (string) bst_mod( 'float_aio_label' ) );
	$aio_label = '' !== $aio_label ? $aio_label : __( 'Contact', 'bigspy-ai-theme-wp' );
	$toggle    = '<button type="button" class="bs-fc__toggle" aria-expanded="false" aria-label="' . esc_attr( $aio_label ) . '"><span class="bs-fc__toggle-open">' . bst_icon( 'message', 26 ) . '</span><span class="bs-fc__toggle-close">' . bst_icon( 'close', 24 ) . '</span><span class="bs-fc__toggle-label">' . esc_html( $aio_label ) . '</span></button>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	printf(
		'<div class="%1$s" style="%2$s" data-bs-fc%3$s role="complementary" aria-label="%4$s"><div class="bs-fc__list">%5$s</div>%6$s</div>',
		esc_attr( implode( ' ', $classes ) ),
		esc_attr( $style ),
		$after ? ' data-show-after="' . (int) $after . '"' : '',
		esc_attr__( 'Quick contact', 'bigspy-ai-theme-wp' ),
		$links, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		( 'aio' === $desktop || 'aio' === $mobile ) ? $toggle : '' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	);
	?>
	<script>
	(function () {
		var fc = document.querySelector('[data-bs-fc]');
		if (!fc) { return; }
		var t = fc.querySelector('.bs-fc__toggle');
		if (t) {
			t.addEventListener('click', function () {
				var open = fc.classList.toggle('is-open');
				t.setAttribute('aria-expanded', open ? 'true' : 'false');
			});
			document.addEventListener('keydown', function (e) {
				if (e.key === 'Escape' && fc.classList.contains('is-open')) { fc.classList.remove('is-open'); t.setAttribute('aria-expanded', 'false'); t.focus(); }
			});
		}
		var after = parseInt(fc.getAttribute('data-show-after') || '0', 10);
		if (after) {
			var check = function () { fc.classList.toggle('is-waiting', window.scrollY < after); };
			window.addEventListener('scroll', check, { passive: true });
			check();
		}
	})();
	</script>
	<?php
}

function bst_float_assets() {
	if ( bst_mod( 'float_enable' ) ) {
		wp_enqueue_style( 'bst-float-contact', BST_URI . '/assets/css/float-contact.css', array( 'bst-main' ), bst_asset_version( 'assets/css/float-contact.css' ) );
	}
}
add_action( 'wp_enqueue_scripts', 'bst_float_assets', 20 );
