<?php
/**
 * Customizer registration: panels, sections and all theme options.
 *
 * Fields are declared as data in fields.php and registered in a loop so every
 * option has a consistent sanitizer and default.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/sanitize.php';
require_once __DIR__ . '/fields.php';

function bst_customize_register( $wp_customize ) {
	require_once __DIR__ . '/controls.php';

	$panels = array(
		'bst_header'   => array( __( 'Header', 'bigspy-theme' ), 21 ),
		'bst_style'    => array( __( 'Style: colors & typography', 'bigspy-theme' ), 22 ),
		'bst_layout'   => array( __( 'Layout', 'bigspy-theme' ), 23 ),
		'bst_blog'     => array( __( 'Blog', 'bigspy-theme' ), 24 ),
		'bst_footer'   => array( __( 'Footer', 'bigspy-theme' ), 26 ),
		'bst_advanced' => array( __( 'Advanced', 'bigspy-theme' ), 200 ),
	);
	foreach ( $panels as $id => $panel ) {
		$wp_customize->add_panel( $id, array( 'title' => $panel[0], 'priority' => $panel[1] ) );
	}

	foreach ( bst_customizer_sections() as $id => $section ) {
		$wp_customize->add_section(
			$id,
			array(
				'title'       => $section['title'],
				'panel'       => $section['panel'] ?? '',
				'priority'    => $section['priority'] ?? 10,
				'description' => $section['description'] ?? '',
			)
		);
	}

	$defaults = bst_defaults();
	$css_vars = bst_css_var_map();

	foreach ( bst_customizer_fields() as $field ) {
		$id   = $field['id'];
		$type = $field['type'];

		if ( 'heading' === $type ) {
			$wp_customize->add_setting( $id, array( 'sanitize_callback' => '__return_empty_string' ) );
			$wp_customize->add_control( new BST_Heading_Control( $wp_customize, $id, array( 'section' => $field['section'], 'label' => $field['label'], 'description' => $field['description'] ?? '', 'priority' => $field['priority'] ?? 10 ) ) );
			continue;
		}

		if ( 'code' === $type && ! current_user_can( 'unfiltered_html' ) ) {
			continue;
		}

		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $defaults[ $id ] ?? '',
				'transport'         => $field['transport'] ?? ( isset( $css_vars[ $id ] ) ? 'postMessage' : 'refresh' ),
				'sanitize_callback' => bst_sanitize_callback_for( $type ),
			)
		);

		$args = array(
			'section'     => $field['section'],
			'label'       => $field['label'] ?? '',
			'description' => $field['description'] ?? '',
			'priority'    => $field['priority'] ?? 10,
		);

		switch ( $type ) {
			case 'color':
				$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, $args ) );
				break;
			case 'image':
				$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $id, $args ) );
				break;
			case 'header_builder':
				$wp_customize->add_control( new BST_Header_Builder_Control( $wp_customize, $id, $args ) );
				break;
			case 'number':
				$args['type']        = 'number';
				$args['input_attrs'] = $field['input_attrs'] ?? array();
				$wp_customize->add_control( $id, $args );
				break;
			case 'html':
			case 'code':
				$args['type']        = 'textarea';
				$args['input_attrs'] = array( 'rows' => 'code' === $type ? 8 : 4, 'style' => 'code' === $type ? 'font-family:monospace' : '' );
				$wp_customize->add_control( $id, $args );
				break;
			default:
				$args['type'] = $type;
				if ( isset( $field['choices'] ) ) {
					$args['choices'] = $field['choices'];
				}
				if ( isset( $field['input_attrs'] ) ) {
					$args['input_attrs'] = $field['input_attrs'];
				}
				$wp_customize->add_control( $id, $args );
		}
	}

	// Move the site identity + menus closer to the header options.
	$logo = $wp_customize->get_section( 'title_tagline' );
	if ( $logo ) {
		$logo->priority = 20;
	}
}
add_action( 'customize_register', 'bst_customize_register' );

function bst_customize_controls_assets() {
	wp_enqueue_style( 'bst-customizer', BST_URI . '/assets/css/customizer.css', array(), bst_asset_version( 'assets/css/customizer.css' ) );
	wp_enqueue_script( 'bst-customizer', BST_URI . '/assets/js/customizer-controls.js', array( 'customize-controls', 'jquery', 'jquery-ui-sortable' ), bst_asset_version( 'assets/js/customizer-controls.js' ), true );
	wp_localize_script(
		'bst-customizer',
		'bstCustomizer',
		array(
			'elements' => bst_header_elements(),
			'defaults' => bst_default_header_layout(),
			'i18n'     => array(
				'desktop'   => __( 'Desktop', 'bigspy-theme' ),
				'mobile'    => __( 'Mobile', 'bigspy-theme' ),
				'top'       => __( 'Top bar', 'bigspy-theme' ),
				'main'      => __( 'Main header', 'bigspy-theme' ),
				'bottom'    => __( 'Bottom header', 'bigspy-theme' ),
				'left'      => __( 'Left', 'bigspy-theme' ),
				'center'    => __( 'Center', 'bigspy-theme' ),
				'right'     => __( 'Right', 'bigspy-theme' ),
				'available' => __( 'Available elements (drag into a row)', 'bigspy-theme' ),
				'reset'     => __( 'Reset to default layout', 'bigspy-theme' ),
				'confirm'   => __( 'Reset the header layout for this device?', 'bigspy-theme' ),
			),
		)
	);
}
add_action( 'customize_controls_enqueue_scripts', 'bst_customize_controls_assets' );

function bst_customize_preview_assets() {
	wp_enqueue_script( 'bst-customizer-preview', BST_URI . '/assets/js/customizer-preview.js', array( 'customize-preview' ), bst_asset_version( 'assets/js/customizer-preview.js' ), true );
	wp_localize_script( 'bst-customizer-preview', 'bstPreviewVars', bst_css_var_map() );
}
add_action( 'customize_preview_init', 'bst_customize_preview_assets' );
