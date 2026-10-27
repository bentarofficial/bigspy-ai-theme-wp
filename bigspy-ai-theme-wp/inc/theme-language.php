<?php
/**
 * Theme language: pick the language of the theme's own texts, separately for the admin
 * (theme panels, Customizer labels) and for the website visitors see, instead of always
 * following the WordPress language. Settings live in BIGSPY → Settings.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Native name of a locale, from the WordPress translation list when available. */
function bst_theme_language_name( $code ) {
	static $list = null;
	if ( null === $list ) {
		$list = array();
		if ( ! function_exists( 'wp_get_available_translations' ) && file_exists( ABSPATH . 'wp-admin/includes/translation-install.php' ) ) {
			require_once ABSPATH . 'wp-admin/includes/translation-install.php';
		}
		if ( function_exists( 'wp_get_available_translations' ) && is_admin() ) {
			foreach ( (array) wp_get_available_translations() as $t ) {
				$list[ $t['language'] ] = $t['native_name'];
			}
		}
	}
	return $list[ $code ] ?? $code;
}

/** Languages the theme has translations for (bundled or installed), plus English (the source language). */
function bst_theme_languages( $with_names = true ) {
	$names = array(
		'vi'    => 'Tiếng Việt',
	);
	$langs = array( 'en_US' => 'English' );
	// Bundled translations, plus ones added later by WordPress.org language packs or Loco Translate.
	$dirs = array(
		BST_DIR . '/languages/'                                  => '',
		WP_LANG_DIR . '/themes/'                                 => 'bigspy-ai-theme-wp-',
		WP_LANG_DIR . '/loco/themes/'                            => 'bigspy-ai-theme-wp-',
	);
	foreach ( $dirs as $dir => $prefix ) {
		$files = array_merge( (array) glob( $dir . $prefix . '*.mo' ), (array) glob( $dir . $prefix . '*.l10n.php' ) );
		foreach ( $files as $file ) {
			$code = preg_replace( '/\.(mo|l10n\.php)$/', '', substr( basename( (string) $file ), strlen( $prefix ) ) );
			if ( $code && 'index' !== $code && preg_match( '/^[a-z]{2,3}(_[A-Z]{2})?(_[a-z]+)?$/', $code ) ) {
				$langs[ $code ] = $with_names ? ( $names[ $code ] ?? bst_theme_language_name( $code ) ) : $code;
			}
		}
	}
	return $langs;
}

add_filter(
	'bst_defaults',
	function ( $d ) {
		return $d + array(
			'theme_lang_admin' => 'en_US',
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
		$admin  = bst_theme_lang_is_admin_request();
		$choice = (string) get_theme_mod( $admin ? 'theme_lang_admin' : 'theme_lang_front', $admin ? 'en_US' : 'auto' );
		return ( 'auto' !== $choice && array_key_exists( $choice, bst_theme_languages( false ) ) ) ? $choice : $locale;
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
		$admin  = bst_theme_lang_is_admin_request();
		$choice = (string) get_theme_mod( $admin ? 'theme_lang_admin' : 'theme_lang_front', $admin ? 'en_US' : 'auto' );
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
		$admin = bst_theme_lang_is_admin_request();
		return 'en_US' === (string) get_theme_mod( $admin ? 'theme_lang_admin' : 'theme_lang_front', $admin ? 'en_US' : 'auto' ) ? true : $override;
	},
	10,
	2
);

/**
 * Whether this request shows the admin side: wp-admin pages, plus the REST calls the block
 * editor makes for block previews (context=edit), so previews match the editor language.
 */
function bst_theme_lang_is_admin_request() {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return true;
	}
	$uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$is_rest = false !== strpos( $uri, '/wp-json/' ) || isset( $_GET['rest_route'] ); // phpcs:ignore WordPress.Security.NonceVerification
	return $is_rest && isset( $_GET['context'] ) && 'edit' === $_GET['context']; // phpcs:ignore WordPress.Security.NonceVerification
}

/*
 * Editor and Customizer scripts translate with wp.i18n. Hand them the theme strings already
 * loaded in PHP, so they follow the chosen admin language without separate JSON files.
 */
function bst_theme_lang_script_data() {
	$data = array( '' => array( 'domain' => 'bigspy-ai-theme-wp' ) );
	// Since WordPress 6.5 translations loaded from .l10n.php expose no entries, so read the
	// file of the language the theme uses (bundled, language pack or Loco) directly.
	$locale = (string) apply_filters( 'theme_locale', determine_locale(), 'bigspy-ai-theme-wp' );
	foreach ( array( WP_LANG_DIR . '/loco/themes/bigspy-ai-theme-wp-' . $locale, WP_LANG_DIR . '/themes/bigspy-ai-theme-wp-' . $locale, BST_DIR . '/languages/' . $locale ) as $base ) {
		if ( ! is_readable( $base . '.l10n.php' ) ) {
			continue;
		}
		$file = include $base . '.l10n.php';
		if ( ! empty( $file['plural-forms'] ) ) {
			$data['']['plural_forms'] = $file['plural-forms'];
		}
		foreach ( (array) ( $file['messages'] ?? array() ) as $key => $value ) {
			if ( '' !== (string) $key && '' !== (string) $value ) {
				$data[ (string) $key ] = explode( "\0", (string) $value ); // Plural forms are joined with NUL.
			}
		}
		break;
	}
	// Older WordPress: entries from the loaded MO file.
	if ( count( $data ) < 2 ) {
		$translations = get_translations_for_domain( 'bigspy-ai-theme-wp' );
		foreach ( (array) ( $translations->entries ?? array() ) as $entry ) {
			if ( ! empty( $entry->translations ) && '' !== (string) $entry->translations[0] ) {
				$data[ ( $entry->context ? $entry->context . "\4" : '' ) . $entry->singular ] = array_values( $entry->translations );
			}
		}
	}
	if ( count( $data ) < 2 ) {
		return;
	}
	wp_add_inline_script( 'wp-i18n', 'wp.i18n.setLocaleData(' . wp_json_encode( $data ) . ',"bigspy-ai-theme-wp");', 'after' );
}

add_action(
	'admin_enqueue_scripts',
	function () {
		static $done = false;
		if ( ! $done ) {
			$done = true;
			bst_theme_lang_script_data();
		}
	},
	1
);
add_action(
	'customize_controls_enqueue_scripts',
	function () {
		bst_theme_lang_script_data();
	},
	1
);
