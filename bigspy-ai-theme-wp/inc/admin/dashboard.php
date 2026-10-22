<?php
/**
 * Appearance → BIGSPY AI Theme WP: quick links and theme options import / export / reset.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

function bst_admin_menu() {
	$page = add_theme_page( __( 'BIGSPY Studio', 'bigspy-ai-theme-wp' ), __( 'BIGSPY Studio', 'bigspy-ai-theme-wp' ), 'edit_theme_options', 'bigspy-ai-theme-wp', 'bst_dashboard_page' );
	add_action(
		'admin_enqueue_scripts',
		static function ( $hook ) use ( $page ) {
			if ( $page === $hook ) {
				wp_enqueue_style( 'bst-admin-studio', BST_URI . '/assets/css/admin-studio.css', array(), bst_asset_version( 'assets/css/admin-studio.css' ) );
			}
		}
	);
}
add_action( 'admin_menu', 'bst_admin_menu' );

/** Create a safe, unpublished page from a BIGSPY full-page pattern. */
function bst_create_starter_page() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to create pages.', 'bigspy-ai-theme-wp' ) );
	}
	check_admin_referer( 'bst_create_starter_page' );

	$choice = isset( $_POST['bst_starter'] ) ? sanitize_key( wp_unslash( $_POST['bst_starter'] ) ) : '';
	$starters = array(
		'business-home' => array(
			'pattern' => 'bigspy-ai-theme-wp/page-home',
			'title'   => __( 'Business homepage', 'bigspy-ai-theme-wp' ),
			'woo'     => false,
		),
		'shop-home' => array(
			'pattern' => 'bigspy-ai-theme-wp/page-shop-home',
			'title'   => __( 'Online shop homepage', 'bigspy-ai-theme-wp' ),
			'woo'     => true,
		),
	);
	if ( ! isset( $starters[ $choice ] ) || ( $starters[ $choice ]['woo'] && ! class_exists( 'WooCommerce' ) ) ) {
		wp_die( esc_html__( 'This starter page is not available.', 'bigspy-ai-theme-wp' ) );
	}

	$pattern = WP_Block_Patterns_Registry::get_instance()->get_registered( $starters[ $choice ]['pattern'] );
	if ( ! $pattern || empty( $pattern['content'] ) ) {
		wp_die( esc_html__( 'The selected BIGSPY starter pattern could not be found.', 'bigspy-ai-theme-wp' ) );
	}

	$title = isset( $_POST['bst_title'] ) ? sanitize_text_field( wp_unslash( $_POST['bst_title'] ) ) : '';
	if ( '' === $title ) {
		$title = $starters[ $choice ]['title'];
	}
	$page_id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'draft',
			'post_title'   => $title,
			'post_content' => $pattern['content'],
			'meta_input'   => array( '_wp_page_template' => 'templates/full-width.php' ),
		),
		true
	);
	if ( is_wp_error( $page_id ) ) {
		wp_die( esc_html( $page_id->get_error_message() ) );
	}

	wp_safe_redirect( add_query_arg( array( 'post' => $page_id, 'action' => 'edit', 'bst_starter' => 'created' ), admin_url( 'post.php' ) ) );
	exit;
}
add_action( 'admin_post_bst_create_starter_page', 'bst_create_starter_page' );

function bst_customizer_link( $type, $id ) {
	return admin_url( 'customize.php?autofocus[' . $type . ']=' . $id );
}

function bst_dashboard_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$links = array(
		__( 'Header builder', 'bigspy-ai-theme-wp' )       => bst_customizer_link( 'section', 'bst_header_builder' ),
		__( 'Logo & site identity', 'bigspy-ai-theme-wp' ) => bst_customizer_link( 'section', 'title_tagline' ),
		__( 'Colors', 'bigspy-ai-theme-wp' )               => bst_customizer_link( 'section', 'bst_colors' ),
		__( 'Typography', 'bigspy-ai-theme-wp' )           => bst_customizer_link( 'section', 'bst_typography' ),
		__( 'Thư viện CSS · bật/tắt', 'bigspy-ai-theme-wp' ) => bst_customizer_link( 'section', 'custom_css' ),
		__( 'Layout', 'bigspy-ai-theme-wp' )               => bst_customizer_link( 'panel', 'bst_layout' ),
		__( 'Blog', 'bigspy-ai-theme-wp' )                 => bst_customizer_link( 'panel', 'bst_blog' ),
		__( 'Footer', 'bigspy-ai-theme-wp' )               => bst_customizer_link( 'panel', 'bst_footer' ),
		__( 'Floating contact buttons', 'bigspy-ai-theme-wp' ) => bst_customizer_link( 'section', 'bst_float' ),
		__( 'Menus', 'bigspy-ai-theme-wp' )                => admin_url( 'nav-menus.php' ),
		__( 'Widgets (sidebar & footer)', 'bigspy-ai-theme-wp' ) => admin_url( 'widgets.php' ),
	);
	if ( class_exists( 'WooCommerce' ) ) {
		$links[ __( 'Shop layout', 'bigspy-ai-theme-wp' ) ] = bst_customizer_link( 'section', 'bst_shop' );
	}

	$notice = isset( $_GET['bst-notice'] ) ? sanitize_key( $_GET['bst-notice'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$notices = array(
		'imported' => array( 'success', __( 'Theme options imported.', 'bigspy-ai-theme-wp' ) ),
		'reset'    => array( 'success', __( 'Theme options reset to defaults.', 'bigspy-ai-theme-wp' ) ),
		'invalid'  => array( 'error', __( 'The file is not a valid BIGSPY AI Theme WP export.', 'bigspy-ai-theme-wp' ) ),
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'BIGSPY Studio', 'bigspy-ai-theme-wp' ); ?> <small style="font-size:13px;color:#646970">v<?php echo esc_html( BST_VERSION ); ?></small></h1>

		<?php if ( isset( $notices[ $notice ] ) ) : ?>
			<div class="notice notice-<?php echo esc_attr( $notices[ $notice ][0] ); ?> is-dismissible"><p><?php echo esc_html( $notices[ $notice ][1] ); ?></p></div>
		<?php endif; ?>

		<section class="bst-studio-intro">
			<div>
				<p class="bst-studio-kicker"><?php esc_html_e( 'DESIGN YOUR WEBSITE', 'bigspy-ai-theme-wp' ); ?></p>
				<h2><?php esc_html_e( 'Start with a page. Make it yours.', 'bigspy-ai-theme-wp' ); ?></h2>
				<p><?php esc_html_e( 'Choose a starting point. BIGSPY creates an unpublished draft and opens the editor, so your live website stays as it is while you design.', 'bigspy-ai-theme-wp' ); ?></p>
			</div>
			<a class="button button-secondary" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=page' ) ); ?>"><?php esc_html_e( 'Build from scratch', 'bigspy-ai-theme-wp' ); ?></a>
		</section>

		<div class="bst-studio-grid">
			<?php
			$studio_cards = array(
				'business-home' => array(
					'title'       => __( 'Business or service website', 'bigspy-ai-theme-wp' ),
					'description' => __( 'A complete homepage with an introduction, services, about section, testimonials, pricing, FAQ and contact.', 'bigspy-ai-theme-wp' ),
					'kind'        => 'business',
				),
			);
			if ( class_exists( 'WooCommerce' ) ) {
				$studio_cards['shop-home'] = array(
					'title'       => __( 'Online shop', 'bigspy-ai-theme-wp' ),
					'description' => __( 'A shop homepage with a hero, benefits, categories, product collections and more.', 'bigspy-ai-theme-wp' ),
					'kind'        => 'shop',
				);
			}
			foreach ( $studio_cards as $starter_id => $card ) :
				?>
				<article class="bst-studio-card">
					<div class="bst-studio-preview bst-studio-preview--<?php echo esc_attr( $card['kind'] ); ?>" aria-hidden="true">
						<div class="bst-studio-preview__top"><i></i><i></i><i></i><b></b></div>
						<div class="bst-studio-preview__hero"><span></span><span></span><span></span></div>
						<div class="bst-studio-preview__row"><i></i><i></i><i></i></div>
					</div>
					<div class="bst-studio-card__body">
						<h2><?php echo esc_html( $card['title'] ); ?></h2>
						<p><?php echo esc_html( $card['description'] ); ?></p>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
							<input type="hidden" name="action" value="bst_create_starter_page">
							<input type="hidden" name="bst_starter" value="<?php echo esc_attr( $starter_id ); ?>">
							<?php wp_nonce_field( 'bst_create_starter_page' ); ?>
							<label class="bst-studio-card__label" for="bst-title-<?php echo esc_attr( $starter_id ); ?>"><?php esc_html_e( 'Page name', 'bigspy-ai-theme-wp' ); ?></label>
							<input class="regular-text" id="bst-title-<?php echo esc_attr( $starter_id ); ?>" name="bst_title" type="text" value="<?php echo esc_attr( $card['title'] ); ?>" required>
							<button class="button button-primary" type="submit"><?php esc_html_e( 'Create draft and open editor', 'bigspy-ai-theme-wp' ); ?></button>
						</form>
					</div>
				</article>
			<?php endforeach; ?>
		</div>

		<div class="bst-studio-next">
			<strong><?php esc_html_e( 'Your design stays in draft until you publish it.', 'bigspy-ai-theme-wp' ); ?></strong>
			<span><?php esc_html_e( 'Replace the example text and images, adjust the layout, preview the page, then publish when it is ready.', 'bigspy-ai-theme-wp' ); ?></span>
		</div>

		<div class="card" style="max-width:none">
			<h2><?php esc_html_e( 'Quick start', 'bigspy-ai-theme-wp' ); ?></h2>
			<ol>
				<li><?php esc_html_e( 'Upload your logo and choose colors & fonts in the Customizer.', 'bigspy-ai-theme-wp' ); ?></li>
				<li><?php esc_html_e( 'Arrange the header with the drag & drop Header builder.', 'bigspy-ai-theme-wp' ); ?></li>
				<li><?php esc_html_e( 'Create a page, choose the “Full width” template, then insert ready-made sections from Patterns → “BIGSPY – Sections” (or the full “Homepage – business” pattern).', 'bigspy-ai-theme-wp' ); ?></li>
				<li><?php esc_html_e( 'Every block has “Visibility & animation” settings to hide it per device or animate it on scroll.', 'bigspy-ai-theme-wp' ); ?></li>
			</ol>
			<p>
				<?php foreach ( $links as $label => $url ) : ?>
					<a class="button" style="margin:0 6px 6px 0" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
				<?php endforeach; ?>
			</p>
		</div>

		<div class="card" style="max-width:none">
			<h2><?php esc_html_e( 'Service website or online shop', 'bigspy-ai-theme-wp' ); ?></h2>
			<?php if ( class_exists( 'WooCommerce' ) ) : ?>
				<p><strong style="color:#008a20"><?php esc_html_e( 'WooCommerce is active – shop mode is on.', 'bigspy-ai-theme-wp' ); ?></strong></p>
				<ul style="list-style:disc;padding-left:20px">
					<li><?php esc_html_e( 'Blocks: “Products” (latest, featured, sale, best selling, top rated – grid or slider) and “Product Categories”.', 'bigspy-ai-theme-wp' ); ?></li>
					<li><?php esc_html_e( 'Patterns: “BIGSPY – Shop” sections and the full “Homepage – online shop”.', 'bigspy-ai-theme-wp' ); ?></li>
					<li><?php esc_html_e( 'Header builder: add the “Cart” and “Account” elements. Shop options: Customizer → WooCommerce → BIGSPY.', 'bigspy-ai-theme-wp' ); ?></li>
				</ul>
			<?php else : ?>
				<p><?php esc_html_e( 'The theme works as a service / business website without any plugin.', 'bigspy-ai-theme-wp' ); ?></p>
				<p><?php esc_html_e( 'To build an online shop, install WooCommerce. The “Products” and “Product Categories” blocks, the shop patterns, the header cart and all shop options then appear automatically – nothing else to set up.', 'bigspy-ai-theme-wp' ); ?></p>
				<?php if ( current_user_can( 'install_plugins' ) ) : ?>
					<p><a class="button" href="<?php echo esc_url( admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' ) ); ?>"><?php esc_html_e( 'Install WooCommerce', 'bigspy-ai-theme-wp' ); ?></a></p>
				<?php endif; ?>
			<?php endif; ?>
		</div>

		<div class="card" style="max-width:none">
			<h2><?php esc_html_e( 'Export theme options', 'bigspy-ai-theme-wp' ); ?></h2>
			<p><?php esc_html_e( 'Download all Customizer settings (colors, fonts, header layout, footer, blog, shop, Additional CSS) as a JSON file. Import it on another website to start from the same design.', 'bigspy-ai-theme-wp' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="bst_export">
				<?php wp_nonce_field( 'bst_export' ); ?>
				<?php submit_button( __( 'Download export file', 'bigspy-ai-theme-wp' ), 'primary', 'submit', false ); ?>
			</form>
		</div>

		<div class="card" style="max-width:none">
			<h2><?php esc_html_e( 'Import theme options', 'bigspy-ai-theme-wp' ); ?></h2>
			<p><?php esc_html_e( 'Upload a file exported from BIGSPY AI Theme WP. Current settings will be overwritten.', 'bigspy-ai-theme-wp' ); ?></p>
			<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="bst_import">
				<?php wp_nonce_field( 'bst_import' ); ?>
				<input type="file" name="bst_file" accept=".json,application/json" required>
				<?php submit_button( __( 'Import', 'bigspy-ai-theme-wp' ), 'secondary', 'submit', false ); ?>
			</form>
		</div>

		<div class="card" style="max-width:none">
			<h2><?php esc_html_e( 'Reset', 'bigspy-ai-theme-wp' ); ?></h2>
			<p><?php esc_html_e( 'Restore every theme option to its default value. Menus, widgets, logo and content are not affected.', 'bigspy-ai-theme-wp' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('<?php echo esc_js( __( 'Reset all theme options?', 'bigspy-ai-theme-wp' ) ); ?>');">
				<input type="hidden" name="action" value="bst_reset">
				<?php wp_nonce_field( 'bst_reset' ); ?>
				<?php submit_button( __( 'Reset theme options', 'bigspy-ai-theme-wp' ), 'delete', 'submit', false ); ?>
			</form>
		</div>
	</div>
	<?php
}

function bst_dashboard_redirect( $notice ) {
	wp_safe_redirect( add_query_arg( 'bst-notice', $notice, admin_url( 'themes.php?page=bigspy-ai-theme-wp' ) ) );
	exit;
}

function bst_handle_export() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'bigspy-ai-theme-wp' ) );
	}
	check_admin_referer( 'bst_export' );

	$options = array();
	foreach ( array_keys( bst_defaults() ) as $key ) {
		$value = get_theme_mod( $key, null );
		if ( null !== $value ) {
			$options[ $key ] = $value;
		}
	}

	$data = array(
		'theme'   => 'bigspy-ai-theme-wp',
		'version' => BST_VERSION,
		'site'    => home_url( '/' ),
		'date'    => gmdate( 'c' ),
		'options' => $options,
		'css'     => wp_get_custom_css(),
	);

	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=bigspy-ai-theme-wp-options-' . gmdate( 'Y-m-d' ) . '.json' );
	echo wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
	exit;
}
add_action( 'admin_post_bst_export', 'bst_handle_export' );

/**
 * Sanitize one imported value according to the type of its default.
 */
function bst_sanitize_imported( $key, $value, $default ) {
	if ( 'css_presets' === $key ) {
		return bst_sanitize_css_presets( $value );
	}
	if ( 'header_layout' === $key ) {
		return wp_json_encode( bst_normalize_header_layout( $value ) );
	}
	if ( 0 === strpos( $key, 'scripts_' ) ) {
		return current_user_can( 'unfiltered_html' ) ? (string) $value : null;
	}
	if ( is_bool( $default ) ) {
		return (bool) $value;
	}
	if ( is_int( $default ) || is_float( $default ) ) {
		return is_numeric( $value ) ? $value + 0 : $default;
	}
	if ( ! is_scalar( $value ) ) {
		return $default;
	}
	$value = (string) $value;
	if ( is_string( $default ) && preg_match( '/^#[0-9a-f]{3,6}$/i', $default ) ) {
		return sanitize_hex_color( $value ) ? sanitize_hex_color( $value ) : $default;
	}
	return wp_kses_post( $value );
}

function bst_handle_import() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'bigspy-ai-theme-wp' ) );
	}
	check_admin_referer( 'bst_import' );

	$file = $_FILES['bst_file'] ?? null; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ! $file || ! empty( $file['error'] ) || empty( $file['tmp_name'] ) || ! is_uploaded_file( $file['tmp_name'] ) || $file['size'] > 2 * MB_IN_BYTES ) {
		bst_dashboard_redirect( 'invalid' );
	}

	$data = json_decode( (string) file_get_contents( $file['tmp_name'] ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( ! is_array( $data ) || ! in_array( $data['theme'] ?? '', array( 'bigspy-ai-theme-wp', 'bigspy-theme' ), true ) || ! is_array( $data['options'] ?? null ) ) {
		bst_dashboard_redirect( 'invalid' );
	}

	$defaults = bst_defaults();
	foreach ( $data['options'] as $key => $value ) {
		if ( ! array_key_exists( $key, $defaults ) ) {
			continue;
		}
		$clean = bst_sanitize_imported( $key, $value, $defaults[ $key ] );
		if ( null !== $clean ) {
			set_theme_mod( $key, $clean );
		}
	}

	if ( isset( $data['css'] ) && is_string( $data['css'] ) && '' !== trim( $data['css'] ) ) {
		wp_update_custom_css_post( wp_strip_all_tags( $data['css'] ) );
	}

	bst_dashboard_redirect( 'imported' );
}
add_action( 'admin_post_bst_import', 'bst_handle_import' );

function bst_handle_reset() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'bigspy-ai-theme-wp' ) );
	}
	check_admin_referer( 'bst_reset' );
	foreach ( array_keys( bst_defaults() ) as $key ) {
		remove_theme_mod( $key );
	}
	bst_dashboard_redirect( 'reset' );
}
add_action( 'admin_post_bst_reset', 'bst_handle_reset' );

/**
 * Link to the dashboard right after the theme is activated.
 */
function bst_activation_notice() {
	global $pagenow;
	if ( 'themes.php' === $pagenow && isset( $_GET['activated'] ) && current_user_can( 'edit_theme_options' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'BIGSPY AI Theme WP is active.', 'bigspy-ai-theme-wp' ) . ' <a href="' . esc_url( admin_url( 'themes.php?page=bigspy-ai-theme-wp' ) ) . '">' . esc_html__( 'Open the quick start guide', 'bigspy-ai-theme-wp' ) . '</a></p></div>';
	}
}
add_action( 'admin_notices', 'bst_activation_notice' );

/** Explain why theme elements are unavailable without changing editor preferences. */
function bst_classic_editor_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! class_exists( 'Classic_Editor' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->base, array( 'post', 'edit', 'appearance_page_bigspy-ai-theme-wp' ), true ) ) {
		return;
	}
	if ( 'classic' !== get_option( 'classic-editor-replace', 'classic' ) ) {
		return;
	}
	echo '<div class="notice notice-info"><p>';
	echo esc_html__( 'BIGSPY elements use the WordPress block editor. Classic Editor is currently the default; choose the block editor in Writing settings to edit layouts visually.', 'bigspy-ai-theme-wp' );
	echo ' <a href="' . esc_url( admin_url( 'options-writing.php#classic-editor-options' ) ) . '">' . esc_html__( 'Writing settings', 'bigspy-ai-theme-wp' ) . '</a>';
	echo '</p></div>';
}
add_action( 'admin_notices', 'bst_classic_editor_notice' );
