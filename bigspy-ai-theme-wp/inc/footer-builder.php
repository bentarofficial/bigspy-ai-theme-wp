<?php
/**
 * G-205: drag & drop footer builder.
 *
 * Optional mode (Footer → Footer builder → "Footer type"): three rows (top,
 * main, bottom), each 0–6 columns with custom widths; elements are dragged into
 * columns in the Customizer. Widget elements reuse the existing "Footer 1/2 –
 * Column N" widget areas and copyright/bottom HTML reuse the existing fields, so
 * switching to the builder keeps the content. Default "Classic" keeps the old
 * footer; a footer designed with blocks still wins over both.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Rows of the footer builder. */
function bst_fb_rows() {
	return array( 'top', 'main', 'bottom' );
}

/** Highest column count per row. */
function bst_fb_max_cols() {
	return 6;
}

/** Elements that can be placed: key => label. */
function bst_fb_elements() {
	$els = array(
		'logo'        => __( 'Logo', 'bigspy-ai-theme-wp' ),
		'about'       => __( 'Text 1', 'bigspy-ai-theme-wp' ),
		'html_2'      => __( 'Text 2', 'bigspy-ai-theme-wp' ),
		'menu'        => __( 'Footer menu', 'bigspy-ai-theme-wp' ),
		'social'      => __( 'Social icons', 'bigspy-ai-theme-wp' ),
		'contact'     => __( 'Contact info', 'bigspy-ai-theme-wp' ),
		'button'      => __( 'Button', 'bigspy-ai-theme-wp' ),
		'copyright'   => __( 'Copyright', 'bigspy-ai-theme-wp' ),
		'html_bottom' => __( 'Payment / badges HTML', 'bigspy-ai-theme-wp' ),
	);
	for ( $i = 1; $i <= 8; $i++ ) {
		/* translators: %d: widget area number. */
		$els[ 'widget_' . $i ] = sprintf( __( 'Widget area %d', 'bigspy-ai-theme-wp' ), $i );
	}
	return apply_filters( 'bst_fb_elements', $els );
}

/** Default layout: like the classic footer (4 widget columns, then copyright bar). */
function bst_fb_default_layout() {
	$empty = array_fill( 0, bst_fb_max_cols(), array() );
	$main  = $empty;
	$main[0] = array( 'widget_1' );
	$main[1] = array( 'widget_2' );
	$main[2] = array( 'widget_3' );
	$main[3] = array( 'widget_4' );
	$bottom    = $empty;
	$bottom[0] = array( 'menu', 'copyright' );
	$bottom[1] = array( 'social', 'html_bottom' );
	return array( 'top' => $empty, 'main' => $main, 'bottom' => $bottom );
}

/** Default column count per row. */
function bst_fb_default_cols( $row ) {
	$map = array( 'top' => 0, 'main' => 4, 'bottom' => 2 );
	return $map[ $row ] ?? 0;
}

/**
 * Normalise a footer layout (JSON or array): known rows, 6 columns each, known
 * elements only, each element used once.
 *
 * @param mixed $value Raw value.
 * @return array
 */
function bst_fb_normalize_layout( $value ) {
	if ( is_string( $value ) && '' !== $value ) {
		$value = json_decode( $value, true );
	}
	if ( ! is_array( $value ) || empty( $value ) ) {
		return bst_fb_default_layout();
	}
	$allowed = array_keys( bst_fb_elements() );
	$used    = array();
	$clean   = array();
	foreach ( bst_fb_rows() as $row ) {
		for ( $c = 0; $c < bst_fb_max_cols(); $c++ ) {
			$items = $value[ $row ][ $c ] ?? array();
			$clean[ $row ][ $c ] = array();
			foreach ( is_array( $items ) ? $items : array() as $item ) {
				$item = sanitize_key( $item );
				if ( in_array( $item, $allowed, true ) && ! in_array( $item, $used, true ) ) {
					$clean[ $row ][ $c ][] = $item;
					$used[]                = $item;
				}
			}
		}
	}
	return $clean;
}

function bst_fb_sanitize_layout( $value ) {
	return wp_json_encode( bst_fb_normalize_layout( $value ) );
}

/** "2,1,1" → array of fr numbers for $count columns ('' = equal). */
function bst_fb_widths( $raw, $count ) {
	$parts = array_values( array_filter( array_map( 'trim', explode( ',', (string) $raw ) ), 'is_numeric' ) );
	$out   = array();
	for ( $i = 0; $i < $count; $i++ ) {
		$v     = isset( $parts[ $i ] ) ? (float) $parts[ $i ] : 1.0;
		$out[] = max( 0.25, min( 12, $v ) );
	}
	return $out;
}

function bst_fb_enabled() {
	return 'builder' === get_theme_mod( 'footer_type', 'classic' );
}

function bst_fb_cols( $row ) {
	$v = get_theme_mod( 'fb_' . $row . '_cols', bst_fb_default_cols( $row ) );
	return max( 0, min( bst_fb_max_cols(), (int) $v ) );
}

/* ---------------------------------------------------------------- Customizer */

add_filter(
	'bst_customizer_sections',
	function ( $sections ) {
		$sections['bst_footer_builder'] = array(
			'title'       => __( 'Footer builder', 'bigspy-ai-theme-wp' ),
			'panel'       => 'bst_footer',
			'priority'    => 1,
			'description' => __( 'Drag logo, menus, text, widgets, social icons and copyright into up to 3 rows × 6 columns.', 'bigspy-ai-theme-wp' ),
		);
		return $sections;
	}
);

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$s    = 'bst_footer_builder';
		$cols = array( '0' => __( 'Row off', 'bigspy-ai-theme-wp' ) );
		for ( $i = 1; $i <= bst_fb_max_cols(); $i++ ) {
			$cols[ (string) $i ] = (string) $i;
		}
		$names = array(
			'top'    => __( 'Top row', 'bigspy-ai-theme-wp' ),
			'main'   => __( 'Main row', 'bigspy-ai-theme-wp' ),
			'bottom' => __( 'Bottom row', 'bigspy-ai-theme-wp' ),
		);
		$add = array(
			array( 'id' => 'footer_type', 'section' => $s, 'type' => 'radio', 'label' => __( 'Footer type', 'bigspy-ai-theme-wp' ), 'choices' => array(
				'classic' => __( 'Classic (widget rows + copyright bar)', 'bigspy-ai-theme-wp' ),
				'builder' => __( 'Builder (drag & drop below)', 'bigspy-ai-theme-wp' ),
			), 'description' => __( 'A footer designed with blocks (Footer → Footer design) is shown instead of both.', 'bigspy-ai-theme-wp' ) ),
		);
		foreach ( bst_fb_rows() as $row ) {
			$add[] = array( 'id' => 'bst_fb_h_' . $row, 'section' => $s, 'type' => 'heading', 'label' => $names[ $row ] );
			$add[] = array( 'id' => 'fb_' . $row . '_cols', 'section' => $s, 'type' => 'select', 'label' => __( 'Columns', 'bigspy-ai-theme-wp' ), 'choices' => $cols );
			$add[] = array( 'id' => 'fb_' . $row . '_widths', 'section' => $s, 'type' => 'text', 'label' => __( 'Column widths', 'bigspy-ai-theme-wp' ), 'description' => __( 'Relative widths, e.g. 2,1,1,1. Empty = equal.', 'bigspy-ai-theme-wp' ) );
			$add[] = array( 'id' => 'fb_' . $row . '_align', 'section' => $s, 'type' => 'select', 'label' => __( 'Alignment', 'bigspy-ai-theme-wp' ), 'choices' => array(
				'left'   => __( 'Left', 'bigspy-ai-theme-wp' ),
				'center' => __( 'Centered', 'bigspy-ai-theme-wp' ),
				'spread' => __( 'Last column on the right', 'bigspy-ai-theme-wp' ),
			) );
			$add[] = array( 'id' => 'fb_' . $row . '_mobile', 'section' => $s, 'type' => 'select', 'label' => __( 'On mobile', 'bigspy-ai-theme-wp' ), 'choices' => array(
				'stack' => __( 'Stack columns', 'bigspy-ai-theme-wp' ),
				'two'   => __( '2 columns', 'bigspy-ai-theme-wp' ),
				'hide'  => __( 'Hide row', 'bigspy-ai-theme-wp' ),
			) );
			$add[] = array( 'id' => 'fb_' . $row . '_bg', 'section' => $s, 'type' => 'color', 'label' => __( 'Background', 'bigspy-ai-theme-wp' ) );
			$add[] = array( 'id' => 'fb_' . $row . '_color', 'section' => $s, 'type' => 'color', 'label' => __( 'Text color', 'bigspy-ai-theme-wp' ) );
		}
		// G-205-b: show each element on all devices, desktop only or mobile only.
		$add[] = array( 'id' => 'bst_fb_h_devices', 'section' => $s, 'type' => 'heading', 'label' => __( 'Show elements on', 'bigspy-ai-theme-wp' ), 'description' => __( 'Desktop = wider than 849px; mobile and tablet = 849px and narrower.', 'bigspy-ai-theme-wp' ) );
		foreach ( bst_fb_elements() as $key => $label ) {
			$add[] = array( 'id' => 'fb_show_' . $key, 'section' => $s, 'type' => 'select', 'label' => $label, 'choices' => bst_fb_device_choices() );
		}
		$add[] = array( 'id' => 'bst_fb_h_content', 'section' => $s, 'type' => 'heading', 'label' => __( 'Element content', 'bigspy-ai-theme-wp' ), 'description' => __( 'Copyright and Payment / badges use the fields in Footer bottom; widget areas 1–8 are Footer 1/2 columns in Widgets.', 'bigspy-ai-theme-wp' ) );
		$add[] = array( 'id' => 'fb_about', 'section' => $s, 'type' => 'html', 'label' => __( 'Text 1', 'bigspy-ai-theme-wp' ) );
		$add[] = array( 'id' => 'fb_html_2', 'section' => $s, 'type' => 'html', 'label' => __( 'Text 2', 'bigspy-ai-theme-wp' ) );
		$add[] = array( 'id' => 'fb_button_text', 'section' => $s, 'type' => 'text', 'label' => __( 'Button text', 'bigspy-ai-theme-wp' ) );
		$add[] = array( 'id' => 'fb_button_url', 'section' => $s, 'type' => 'text', 'label' => __( 'Button link', 'bigspy-ai-theme-wp' ) );
		$add[] = array( 'id' => 'fb_logo_width', 'section' => $s, 'type' => 'number', 'label' => __( 'Logo width (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 40, 'max' => 400, 'placeholder' => '160' ) );
		return array_merge( $fields, $add );
	}
);

add_action(
	'customize_register',
	function ( $wp_customize ) {
		if ( ! class_exists( 'BST_Footer_Builder_Control' ) ) {
			/** Drag & drop footer builder; UI in footer-builder-controls.js. */
			class BST_Footer_Builder_Control extends WP_Customize_Control {
				public $type = 'bst-footer-builder';

				public function render_content() {
					echo '<span class="customize-control-title">' . esc_html( $this->label ) . '</span>';
					echo '<input type="hidden" ' . $this->get_link() . ' value="' . esc_attr( wp_json_encode( bst_fb_normalize_layout( $this->value() ) ) ) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo '<div class="bst-hb bst-fb" data-bst-footer-builder></div>';
				}
			}
		}
		$wp_customize->add_setting( 'footer_layout', array( 'default' => '', 'transport' => 'refresh', 'sanitize_callback' => 'bst_fb_sanitize_layout' ) );
		$wp_customize->add_control(
			new BST_Footer_Builder_Control(
				$wp_customize,
				'footer_layout',
				array(
					'label'           => __( 'Footer layout', 'bigspy-ai-theme-wp' ),
					'section'         => 'bst_footer_builder',
					'priority'        => 1,
					'active_callback' => 'bst_fb_enabled',
				)
			)
		);
		foreach ( bst_customizer_fields() as $f ) {
			if ( 'bst_footer_builder' === $f['section'] && 'footer_type' !== $f['id'] && ( $c = $wp_customize->get_control( $f['id'] ) ) ) {
				$c->active_callback = 'bst_fb_enabled';
			}
		}
	},
	30
);

add_action(
	'customize_controls_enqueue_scripts',
	function () {
		wp_enqueue_script( 'bst-footer-builder', BST_URI . '/assets/js/footer-builder-controls.js', array( 'customize-controls', 'jquery', 'jquery-ui-sortable' ), bst_asset_version( 'assets/js/footer-builder-controls.js' ), true );
		$i18n = array(
			'available' => __( 'Available elements (drag into a column)', 'bigspy-ai-theme-wp' ),
			'column'    => __( 'Column', 'bigspy-ai-theme-wp' ),
			'off'       => __( 'Row off: choose columns below to use it.', 'bigspy-ai-theme-wp' ),
			'reset'     => __( 'Reset to default layout', 'bigspy-ai-theme-wp' ),
			'confirm'   => __( 'Reset the footer layout?', 'bigspy-ai-theme-wp' ),
			'top'       => __( 'Top row', 'bigspy-ai-theme-wp' ),
			'main'      => __( 'Main row', 'bigspy-ai-theme-wp' ),
			'bottom'    => __( 'Bottom row', 'bigspy-ai-theme-wp' ),
		);
		$cols = array();
		foreach ( bst_fb_rows() as $row ) {
			$cols[ $row ] = bst_fb_default_cols( $row );
		}
		wp_localize_script(
			'bst-footer-builder',
			'bstFooterBuilder',
			array(
				'elements' => bst_fb_elements(),
				'defaults' => bst_fb_default_layout(),
				'cols'     => $cols,
				'max'      => bst_fb_max_cols(),
				'i18n'     => $i18n,
			)
		);
	}
);

/* ---------------------------------------------------------------- Front end */

/** HTML of one element ('' when it has nothing to show). */
function bst_fb_element( $key ) {
	switch ( $key ) {
		case 'logo':
			return bst_logo();
		case 'about':
			$h = bst_html_field( 'fb_about' );
			return $h ? '<div class="bs-fb-text">' . $h . '</div>' : '';
		case 'html_2':
			$h = bst_html_field( 'fb_html_2' );
			return $h ? '<div class="bs-fb-text">' . $h . '</div>' : '';
		case 'menu':
			return has_nav_menu( 'footer' ) ? (string) wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'menu_class' => 'bs-footer-menu', 'depth' => 1, 'echo' => false, 'fallback_cb' => false ) ) : '';
		case 'social':
			return bst_social_icons( 'bs-social--footer' );
		case 'contact':
			return bst_contact_list( 'bs-fb-contact' );
		case 'button':
			$t = trim( (string) get_theme_mod( 'fb_button_text', '' ) );
			$u = trim( (string) get_theme_mod( 'fb_button_url', '' ) );
			return ( '' !== $t && '' !== $u ) ? '<a class="bs-btn bs-btn--primary" href="' . esc_url( $u ) . '">' . esc_html( $t ) . '</a>' : '';
		case 'copyright':
			$h = bst_html_field( 'footer_copyright' );
			return $h ? '<div class="bs-copyright">' . $h . '</div>' : '';
		case 'html_bottom':
			return bst_html_field( 'footer_bottom_html' );
	}
	if ( preg_match( '/^widget_([1-8])$/', $key, $m ) ) {
		$n  = (int) $m[1];
		$id = 'footer-' . ( $n > 4 ? 2 : 1 ) . '-' . ( $n > 4 ? $n - 4 : $n );
		if ( ! is_active_sidebar( $id ) ) {
			return '';
		}
		ob_start();
		dynamic_sidebar( $id );
		return '<div class="bs-fb-widgets"' . bst_sidebar_edit_attr( $id ) . '>' . ob_get_clean() . '</div>';
	}
	return (string) apply_filters( 'bst_fb_element_html', '', $key );
}

/** G-205-b: device choices for one element. */
function bst_fb_device_choices() {
	return array(
		''        => __( 'All devices', 'bigspy-ai-theme-wp' ),
		'desktop' => __( 'Desktop only', 'bigspy-ai-theme-wp' ),
		'mobile'  => __( 'Mobile & tablet only', 'bigspy-ai-theme-wp' ),
	);
}

/** Wrap an element when it shows on one kind of device only. */
function bst_fb_device_wrap( $key, $html ) {
	if ( '' === $html ) {
		return '';
	}
	$show = (string) get_theme_mod( 'fb_show_' . $key, '' );
	if ( 'desktop' !== $show && 'mobile' !== $show ) {
		return $html;
	}
	return '<div class="bs-fb-only-' . $show . '">' . $html . '</div>';
}

/** Print the builder footer rows. */
function bst_fb_render() {
	$layout = bst_fb_normalize_layout( get_theme_mod( 'footer_layout', '' ) );
	foreach ( bst_fb_rows() as $row ) {
		$count = bst_fb_cols( $row );
		if ( ! $count ) {
			continue;
		}
		$cells = array();
		$any   = false;
		for ( $c = 0; $c < $count; $c++ ) {
			$html = '';
			foreach ( $layout[ $row ][ $c ] as $key ) {
				$html .= bst_fb_device_wrap( $key, bst_fb_element( $key ) );
			}
			$any     = $any || '' !== $html;
			$cells[] = '<div class="bs-fb-col">' . $html . '</div>';
		}
		if ( ! $any ) {
			continue;
		}
		$widths = bst_fb_widths( get_theme_mod( 'fb_' . $row . '_widths', '' ), $count );
		$align  = bst_choice( get_theme_mod( 'fb_' . $row . '_align', 'bottom' === $row ? 'spread' : 'left' ), array( 'left', 'center', 'spread' ), 'left' );
		$mobile = bst_choice( get_theme_mod( 'fb_' . $row . '_mobile', 'stack' ), array( 'stack', 'two', 'hide' ), 'stack' );
		$grid   = implode( ' ', array_map( function ( $w ) { return 'minmax(0,' . $w . 'fr)'; }, $widths ) );
		echo '<div class="bs-fb-row bs-fb-row--' . esc_attr( $row ) . ' bs-fb-row--' . esc_attr( $align ) . ' bs-fb-row--m-' . esc_attr( $mobile ) . '" style="--bs-fb-cols:' . esc_attr( $grid ) . ';--bs-fb-n:' . (int) $count . '">';
		echo '<div class="bs-container bs-fb-grid">' . implode( '', $cells ) . '</div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/** Row colors and logo width; '' while none is set. */
function bst_fb_css() {
	if ( ! bst_fb_enabled() ) {
		return '';
	}
	$css = '';
	foreach ( bst_fb_rows() as $row ) {
		$bg = sanitize_hex_color( (string) get_theme_mod( 'fb_' . $row . '_bg', '' ) );
		$co = sanitize_hex_color( (string) get_theme_mod( 'fb_' . $row . '_color', '' ) );
		$d  = ( $bg ? 'background:' . $bg . ';' : '' ) . ( $co ? 'color:' . $co : '' );
		if ( $d ) {
			$css .= '.bs-fb-row--' . $row . '{' . $d . '}';
		}
	}
	$css .= '@media (max-width:849px){.bs-fb-only-desktop{display:none}}@media (min-width:850px){.bs-fb-only-mobile{display:none}}';
	$w = get_theme_mod( 'fb_logo_width', '' );
	if ( '' !== $w && is_numeric( $w ) ) {
		$css .= '.bs-fb-row .bs-logo__img{max-width:' . max( 40, min( 400, (int) $w ) ) . 'px}';
	}
	return wp_strip_all_tags( $css );
}
add_filter(
	'bst_dynamic_css',
	function ( $css ) {
		return $css . bst_fb_css();
	}
);
