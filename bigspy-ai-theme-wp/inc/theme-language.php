<?php
/**
 * Theme language: pick the language of the theme's own texts, separately for the admin
 * (theme panels, Customizer labels) and for the website visitors see, instead of always
 * following the WordPress language. Settings live in BIGSPY → Settings.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Languages the theme ships translations for, plus English (the source language). */
function bst_theme_languages() {
	$names = array(
		'vi'    => 'Tiếng Việt',
		'fr_FR' => 'Français',
		'de_DE' => 'Deutsch',
		'es_ES' => 'Español',
		'ja'    => '日本語',
		'zh_CN' => '简体中文',
		'th'    => 'ไทย',
		'id_ID' => 'Bahasa Indonesia',
	);
	$langs = array( 'en_US' => 'English' );
	foreach ( (array) glob( BST_DIR . '/languages/*.{mo,l10n.php}', GLOB_BRACE ) as $file ) {
		$code = preg_replace( '/\.(mo|l10n\.php)$/', '', basename( (string) $file ) );
		if ( $code && 'index' !== $code ) {
			$langs[ $code ] = $names[ $code ] ?? $code;
		}
	}
	return $langs;
}

add_filter(
	'bst_defaults',
	function ( $d ) {
		return $d + array(
			'theme_lang_admin' => 'auto',
			'theme_lang_front' => 'auto',
		);
	}
);

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$s       = 'bst_theme_settings';
		$choices = array( 'auto' => __( 'Same as WordPress', 'bigspy-ai-theme-wp' ) ) + bst_theme_languages();
		$fields[] = array( 'id' => 'bst_g_theme_lang', 'section' => $s, 'type' => 'heading', 'label' => __( 'Theme language', 'bigspy-ai-theme-wp' ), 'description' => __( 'Language of the theme’s own texts. WordPress and plugins keep their own language (Settings → General and Users → Profile).', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'theme_lang_admin', 'section' => $s, 'type' => 'select', 'label' => __( 'Admin & Customizer', 'bigspy-ai-theme-wp' ), 'choices' => $choices, 'description' => __( '“Same as WordPress” follows each user’s profile language.', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'theme_lang_front', 'section' => $s, 'type' => 'select', 'label' => __( 'Website (what visitors see)', 'bigspy-ai-theme-wp' ), 'choices' => $choices, 'description' => __( 'Buttons and labels printed by the theme, e.g. “Read more”, “Share:”, “Related posts”.', 'bigspy-ai-theme-wp' ) );
		return $fields;
	},
	28
);

/**
 * Locale used when the theme loads its translations.
 */
add_filter(
	'theme_locale',
	function ( $locale, $domain ) {
		if ( 'bigspy-ai-theme-wp' !== $domain ) {
			return $locale;
		}
		$admin  = is_admin() && ! wp_doing_ajax();
		$choice = (string) get_theme_mod( $admin ? 'theme_lang_admin' : 'theme_lang_front', 'auto' );
		return ( 'auto' !== $choice && array_key_exists( $choice, bst_theme_languages() ) ) ? $choice : $locale;
	},
	10,
	2
);

/*
 * WordPress 6.5+ may load translations just in time before the theme asks for them. When a
 * language is forced, unload the domain once so load_theme_textdomain() picks the choice.
 */
add_action(
	'after_setup_theme',
	function () {
		$admin  = is_admin() && ! wp_doing_ajax();
		$choice = (string) get_theme_mod( $admin ? 'theme_lang_admin' : 'theme_lang_front', 'auto' );
		if ( 'auto' !== $choice && is_textdomain_loaded( 'bigspy-ai-theme-wp' ) ) {
			unload_textdomain( 'bigspy-ai-theme-wp', true );
		}
	},
	1
);

/*
 * Forcing English: the source texts are English, so make sure no translation file for the
 * WordPress language gets loaded for the theme domain (just-in-time loading included).
 */
add_filter(
	'override_load_textdomain',
	function ( $override, $domain ) {
		if ( 'bigspy-ai-theme-wp' !== $domain ) {
			return $override;
		}
		$admin = is_admin() && ! wp_doing_ajax();
		return 'en_US' === (string) get_theme_mod( $admin ? 'theme_lang_admin' : 'theme_lang_front', 'auto' ) ? true : $override;
	},
	10,
	2
);
