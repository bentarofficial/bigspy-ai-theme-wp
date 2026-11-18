<?php
/**
 * Appearance → BIGSPY AI Theme WP: quick links and theme options import / export / reset.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

function bst_admin_menu() {
	$page = add_theme_page( __( 'BIGSPY AI Studio', 'bigspy-ai-theme-wp' ), __( 'BIGSPY AI Studio', 'bigspy-ai-theme-wp' ), 'edit_theme_options', 'bigspy-ai-theme-wp', 'bst_dashboard_page' );
	add_action(
		'admin_enqueue_scripts',
		static function ( $hook ) use ( $page ) {
			if ( $page === $hook ) {
				wp_enqueue_style( 'bst-admin-studio', BST_URI . '/assets/css/admin-studio.css', array(), bst_asset_version( 'assets/css/admin-studio.css' ) );
			}
		}
	);
}
/** Mini page mockup for a starter card: browser bar, menu, hero, then services or products. */
function bst_studio_mock( $kind ) {
	$shop = 'shop' === $kind;
	$e    = 'esc_html__';
	$nav  = $shop
		? array( $e( 'Home', 'bigspy-ai-theme-wp' ), $e( 'Shop', 'bigspy-ai-theme-wp' ), $e( 'Sale', 'bigspy-ai-theme-wp' ) )
		: array( $e( 'Home', 'bigspy-ai-theme-wp' ), $e( 'Services', 'bigspy-ai-theme-wp' ), $e( 'Contact', 'bigspy-ai-theme-wp' ) );
	$out  = '<div class="bst-mock__bar"><i></i><i></i><i></i><span>' . ( $shop ? 'yourshop.com' : 'yourbusiness.com' ) . '</span></div>';
	$out .= '<div class="bst-mock__nav"><b class="bst-mock__logo">' . ( $shop ? 'SHOP' : 'BRAND' ) . '</b><span>' . implode( '</span><span>', $nav ) . '</span>' . ( $shop ? '<em class="bst-mock__cart">🛒 2</em>' : '<em class="bst-mock__cta">' . $e( 'Call us', 'bigspy-ai-theme-wp' ) . '</em>' ) . '</div>';
	$out .= '<div class="bst-mock__hero"><div class="bst-mock__copy"><small>' . ( $shop ? $e( 'New collection', 'bigspy-ai-theme-wp' ) : $e( 'Trusted local service', 'bigspy-ai-theme-wp' ) ) . '</small><strong>' . ( $shop ? $e( 'Style for every day', 'bigspy-ai-theme-wp' ) : $e( 'Quality service you can count on', 'bigspy-ai-theme-wp' ) ) . '</strong><span class="bst-mock__btns"><u>' . ( $shop ? $e( 'Shop now', 'bigspy-ai-theme-wp' ) : $e( 'Book now', 'bigspy-ai-theme-wp' ) ) . '</u><u class="is-ghost">' . ( $shop ? $e( 'Sale', 'bigspy-ai-theme-wp' ) : $e( 'Our services', 'bigspy-ai-theme-wp' ) ) . '</u></span></div><div class="bst-mock__pic">' . ( $shop ? '👜' : '🏢' ) . '</div></div>';
	if ( $shop ) {
		$items = array( array( '👟', '590.000đ' ), array( '⌚', '1.290.000đ' ), array( '🧥', '890.000đ' ) );
		$out  .= '<div class="bst-mock__grid">';
		foreach ( $items as $it ) {
			$out .= '<div class="bst-mock__card bst-mock__product"><span class="bst-mock__thumb">' . $it[0] . '</span><i></i><b>' . $it[1] . '</b><u>' . $e( 'Add to cart', 'bigspy-ai-theme-wp' ) . '</u></div>';
		}
	} else {
		$items = array( array( '⭐', $e( 'Expert team', 'bigspy-ai-theme-wp' ) ), array( '⚡', $e( 'Fast service', 'bigspy-ai-theme-wp' ) ), array( '💬', $e( 'Free advice', 'bigspy-ai-theme-wp' ) ) );
		$out  .= '<div class="bst-mock__grid">';
		foreach ( $items as $it ) {
			$out .= '<div class="bst-mock__card"><span class="bst-mock__icon">' . $it[0] . '</span><b>' . $it[1] . '</b><i></i></div>';
		}
	}
	return $out . '</div>';
}

// Studio now lives in the top-level BIGSPY menu (inc/admin/theme-panel.php).

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
		wp_die( esc_html__( 'The selected BIGSPY AI starter pattern could not be found.', 'bigspy-ai-theme-wp' ) );
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
		__( 'CSS library · on/off', 'bigspy-ai-theme-wp' ) => bst_customizer_link( 'section', 'custom_css' ),
		__( 'Pages & title', 'bigspy-ai-theme-wp' )        => bst_customizer_link( 'panel', 'bst_layout' ),
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
		'imported'      => array( 'success', __( 'Theme options imported.', 'bigspy-ai-theme-wp' ) ),
		'reset'         => array( 'success', __( 'Theme options reset to defaults.', 'bigspy-ai-theme-wp' ) ),
		'restored'      => array( 'success', __( 'The previous theme options are back.', 'bigspy-ai-theme-wp' ) ),
		'no-backup'     => array( 'error', __( 'There is no saved backup to restore.', 'bigspy-ai-theme-wp' ) ),
		'invalid'       => array( 'error', __( 'The file is not a valid BIGSPY AI Theme WP export.', 'bigspy-ai-theme-wp' ) ),
		'bad-json'      => array( 'error', __( 'The file is damaged or is not a JSON file. Download a new export file and try again.', 'bigspy-ai-theme-wp' ) ),
		'no-file'       => array( 'error', __( 'Choose an export file first.', 'bigspy-ai-theme-wp' ) ),
		'upload-failed' => array( 'error', __( 'The server could not receive the file. Try again, or ask your host to check the upload temp folder.', 'bigspy-ai-theme-wp' ) ),
		/* translators: %s: maximum file size. */
		'too-big'       => array( 'error', sprintf( __( 'The file is larger than this server accepts (%s).', 'bigspy-ai-theme-wp' ), size_format( min( bst_import_max_bytes(), wp_max_upload_size() ) ) ) ),
	);
	if ( 'imported' === $notice ) {
		$count = static function ( $name ) {
			return isset( $_GET[ $name ] ) ? absint( $_GET[ $name ] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		};
		/* translators: 1: number of options applied, 2: number of images copied. */
		$message = sprintf( __( 'Theme options imported: %1$d settings applied, %2$d images copied to the Media Library.', 'bigspy-ai-theme-wp' ), $count( 'bst-applied' ), $count( 'bst-media' ) );
		if ( $count( 'bst-skipped' ) ) {
			/* translators: %d: number of skipped settings. */
			$message .= ' ' . sprintf( __( '%d settings were skipped because this theme version does not have them or your account may not edit code.', 'bigspy-ai-theme-wp' ), $count( 'bst-skipped' ) );
		}
		if ( $count( 'bst-mfail' ) ) {
			/* translators: %d: number of images. */
			$message .= ' ' . sprintf( __( '%d images could not be downloaded from the old website; upload them again in the Customizer.', 'bigspy-ai-theme-wp' ), $count( 'bst-mfail' ) );
		}
		$notices['imported'] = array( $count( 'bst-mfail' ) ? 'warning' : 'success', $message );
	}
	$backup = get_option( 'bst_options_backup' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'BIGSPY AI Studio', 'bigspy-ai-theme-wp' ); ?> <small style="font-size:13px;color:#646970">v<?php echo esc_html( BST_VERSION ); ?></small></h1>

		<?php if ( isset( $notices[ $notice ] ) ) : ?>
			<div class="notice notice-<?php echo esc_attr( $notices[ $notice ][0] ); ?> is-dismissible"><p><?php echo esc_html( $notices[ $notice ][1] ); ?></p></div>
		<?php endif; ?>

		<section class="bst-studio-intro">
			<div>
				<p class="bst-studio-kicker"><?php esc_html_e( 'DESIGN YOUR WEBSITE', 'bigspy-ai-theme-wp' ); ?></p>
				<h2><?php esc_html_e( 'Start with a page. Make it yours.', 'bigspy-ai-theme-wp' ); ?></h2>
				<p><?php esc_html_e( 'Choose a starting point. BIGSPY AI creates an unpublished draft and opens the editor, so your live website stays as it is while you design.', 'bigspy-ai-theme-wp' ); ?></p>
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
						<?php echo bst_studio_mock( $card['kind'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static markup built from escaped strings. ?>
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
				<li><?php esc_html_e( 'Create a page, choose the “Full width” template, then insert ready-made sections from Patterns → “BIGSPY AI – Sections” (or the full “Homepage – business” pattern).', 'bigspy-ai-theme-wp' ); ?></li>
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
					<li><?php esc_html_e( 'Patterns: “BIGSPY AI – Shop” sections and the full “Homepage – online shop”.', 'bigspy-ai-theme-wp' ); ?></li>
					<li><?php esc_html_e( 'Header builder: add the “Cart” and “Account” elements. Shop options: Customizer → WooCommerce → BIGSPY AI.', 'bigspy-ai-theme-wp' ); ?></li>
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
			<p class="description"><?php esc_html_e( 'The file also links the logo, site icon and Customizer images; importing copies them into the new Media Library. Posts, pages, menus, widgets and plugins are not included – use a full backup plugin for those.', 'bigspy-ai-theme-wp' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="bst_export">
				<?php wp_nonce_field( 'bst_export' ); ?>
				<?php submit_button( __( 'Download export file', 'bigspy-ai-theme-wp' ), 'primary', 'submit', false ); ?>
			</form>
		</div>

		<div class="card" style="max-width:none">
			<h2><?php esc_html_e( 'Import theme options', 'bigspy-ai-theme-wp' ); ?></h2>
			<p><?php esc_html_e( 'Upload a file exported from BIGSPY AI Theme WP. Current settings will be overwritten.', 'bigspy-ai-theme-wp' ); ?></p>
			<p class="description">
				<?php
				/* translators: %s: maximum file size. */
				echo esc_html( sprintf( __( 'Current settings are saved first, so you can undo the import. Maximum file size: %s.', 'bigspy-ai-theme-wp' ), size_format( min( bst_import_max_bytes(), wp_max_upload_size() ) ) ) );
				?>
			</p>
			<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="bst_import">
				<?php wp_nonce_field( 'bst_import' ); ?>
				<input type="file" name="bst_file" accept=".json,application/json" required>
				<?php submit_button( __( 'Import', 'bigspy-ai-theme-wp' ), 'secondary', 'submit', false ); ?>
			</form>
			<?php if ( is_array( $backup ) && ! empty( $backup['date'] ) ) : ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:12px" onsubmit="return confirm('<?php echo esc_js( __( 'Restore the theme options saved before the last import or reset?', 'bigspy-ai-theme-wp' ) ); ?>');">
					<input type="hidden" name="action" value="bst_undo">
					<?php wp_nonce_field( 'bst_undo' ); ?>
					<?php submit_button( __( 'Undo last import or reset', 'bigspy-ai-theme-wp' ), 'secondary', 'submit', false ); ?>
					<span class="description">
						<?php
						/* translators: %s: date and time. */
						echo esc_html( sprintf( __( 'Backup saved %s.', 'bigspy-ai-theme-wp' ), wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), (int) $backup['date'] ) ) );
						?>
					</span>
				</form>
			<?php endif; ?>
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

function bst_dashboard_redirect( $notice, $args = array() ) {
	$args['bst-notice'] = $notice;
	wp_safe_redirect( add_query_arg( $args, admin_url( 'admin.php?page=bigspy-ai-theme-wp' ) ) );
	exit;
}

/**
 * Every theme option that export, import, undo and reset handle, with the field that edits it.
 * Built from bst_defaults() plus every Customizer field, so new fields (responsive tablet and
 * mobile values, spacing…) are included automatically.
 *
 * @return array<string, array> key => field definition (empty array when the key has no field).
 */
function bst_transfer_keys() {
	static $keys = null;
	if ( null !== $keys ) {
		return $keys;
	}
	$keys = array_fill_keys( array_keys( bst_defaults() ), array() );
	foreach ( bst_customizer_fields() as $field ) {
		$id   = $field['id'] ?? '';
		$type = $field['type'] ?? '';
		if ( '' === $id || in_array( $type, array( 'heading', 'layout_presets' ), true ) ) {
			continue;
		}
		$keys[ $id ] = $field;
		if ( 'responsive' === $type ) {
			foreach ( array( 'tablet', 'mobile' ) as $device ) {
				$keys[ $id . '_' . $device ] = array( 'id' => $id . '_' . $device, 'type' => 'responsive_device', 'input_attrs' => $field['input_attrs'] ?? array() );
			}
		}
	}
	return $keys;
}

/** Clamp a number to a field's min / max. */
function bst_transfer_clamp( $value, $field ) {
	$attrs = (array) ( $field['input_attrs'] ?? array() );
	if ( isset( $attrs['min'] ) && is_numeric( $attrs['min'] ) ) {
		$value = max( $attrs['min'] + 0, $value );
	}
	if ( isset( $attrs['max'] ) && is_numeric( $attrs['max'] ) ) {
		$value = min( $attrs['max'] + 0, $value );
	}
	return $value;
}

/**
 * Sanitize one imported value the same way the Customizer would.
 *
 * @return mixed Clean value, or null to skip the key.
 */
function bst_sanitize_imported( $key, $value, $default, $field = array() ) {
	if ( 'css_presets' === $key ) {
		return bst_sanitize_css_presets( $value );
	}
	if ( 'header_layout' === $key || 'header_builder' === ( $field['type'] ?? '' ) ) {
		// Empty means "use the default layout"; keep it empty instead of freezing today's default.
		return ( '' === $value || null === $value ) ? '' : wp_json_encode( bst_normalize_header_layout( $value ) );
	}
	if ( 0 === strpos( $key, 'scripts_' ) ) {
		return current_user_can( 'unfiltered_html' ) && is_scalar( $value ) ? (string) $value : null;
	}
	$type = $field['type'] ?? '';
	switch ( $type ) {
		case 'responsive':
		case 'number':
			if ( ! is_numeric( $value ) ) {
				return $default;
			}
			// The default may sit outside min / max on purpose (-1 = inherit).
			return ( $value + 0 ) == $default ? $default : bst_transfer_clamp( $value + 0, $field ); // phpcs:ignore Universal.Operators.StrictComparisons.LooseEqual
		case 'responsive_device':
			return ( is_numeric( $value ) && '' !== $value ) ? bst_transfer_clamp( $value + 0, $field ) : '';
		case 'spacing':
			return bst_sanitize_spacing( $value );
		case 'checkbox':
			return ! empty( $value ) && 'false' !== $value;
		case 'select':
		case 'radio':
		case 'image_radio':
			$choices = (array) ( $field['choices'] ?? array() );
			if ( ! is_scalar( $value ) || ( $choices && ! array_key_exists( (string) $value, $choices ) ) ) {
				return $default;
			}
			// Keep the default's type: numeric choices (columns…) stay numbers.
			return ( is_int( $default ) && is_numeric( $value ) ) ? (int) $value : (string) $value;
		case 'color':
			if ( '' === $value ) {
				return '';
			}
			return is_scalar( $value ) && sanitize_hex_color( (string) $value ) ? sanitize_hex_color( (string) $value ) : $default;
		case 'dropdown-pages':
			$page = absint( $value );
			return ( $page && get_post( $page ) ) ? $page : 0;
		case 'url':
		case 'image':
			return is_scalar( $value ) ? esc_url_raw( (string) $value ) : $default;
		case 'email':
			return is_scalar( $value ) ? sanitize_email( (string) $value ) : $default;
		case 'html':
			return is_scalar( $value ) ? wp_kses_post( (string) $value ) : $default;
		case 'code':
			return current_user_can( 'unfiltered_html' ) && is_scalar( $value ) ? bst_sanitize_code( (string) $value ) : null;
	}
	// Keys without a field: decide from the default's type.
	if ( in_array( $key, array( 'header_banner_desktop', 'header_banner_mobile' ), true ) ) {
		return is_scalar( $value ) ? bst_sanitize_code( (string) $value ) : $default;
	}
	if ( is_bool( $default ) ) {
		return ! empty( $value ) && 'false' !== $value;
	}
	if ( is_int( $default ) || is_float( $default ) ) {
		return is_numeric( $value ) ? $value + 0 : $default;
	}
	if ( is_array( $default ) ) {
		return is_array( $value ) ? map_deep( $value, 'wp_kses_post' ) : $default;
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

/** Option keys edited as code in the Customizer. */
function bst_code_option_keys() {
	$keys = array( 'header_banner_desktop', 'header_banner_mobile' );
	foreach ( bst_customizer_fields() as $field ) {
		if ( in_array( $field['type'] ?? '', array( 'code', 'html' ), true ) && ! empty( $field['id'] ) ) {
			$keys[] = $field['id'];
		}
	}
	return $keys;
}

/** Current state of every theme option, logo, site icon and Additional CSS, for undo. */
function bst_options_snapshot() {
	$mods = get_theme_mods();
	$mods = is_array( $mods ) ? $mods : array();
	$keep = array_intersect_key( $mods, bst_transfer_keys() + array( 'custom_logo' => 1 ) );
	return array(
		'date'      => time(),
		'mods'      => $keep,
		'css'       => wp_get_custom_css(),
		'site_icon' => (int) get_option( 'site_icon' ),
	);
}

/** Save the current state so the next change can be undone. */
function bst_options_save_backup() {
	update_option( 'bst_options_backup', bst_options_snapshot(), false );
}

/** URL of an attachment by id ('' when missing). */
function bst_transfer_media_url( $id ) {
	$url = $id ? wp_get_attachment_url( (int) $id ) : '';
	return $url ? $url : '';
}

/**
 * Bring an image from another site into this Media Library. Images already on this site are reused.
 *
 * @return int Attachment id, or 0 on failure.
 */
function bst_transfer_fetch_image( $url ) {
	$url = esc_url_raw( (string) $url );
	if ( '' === $url || ! wp_http_validate_url( $url ) ) {
		return 0;
	}
	$local = attachment_url_to_postid( $url );
	if ( $local ) {
		return (int) $local;
	}
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$id = media_sideload_image( $url, 0, null, 'id' );
	return is_wp_error( $id ) ? 0 : (int) $id;
}

/** True when a URL points to another website. */
function bst_transfer_is_foreign( $url ) {
	$host = wp_parse_url( (string) $url, PHP_URL_HOST );
	return $host && strtolower( $host ) !== strtolower( (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
}

/** Everything the export file holds. */
function bst_export_options_data() {
	// Export the value each option really has on this site: saved, or its default (which can
	// come from imported Flatsome settings). Fields without a default are exported when saved.
	$defaults = bst_defaults();
	$saved    = get_theme_mods();
	$saved    = is_array( $saved ) ? $saved : array();
	$options  = array();
	foreach ( bst_transfer_keys() as $key => $field ) {
		if ( array_key_exists( $key, $defaults ) ) {
			$value = get_theme_mod( $key, $defaults[ $key ] );
		} elseif ( array_key_exists( $key, $saved ) ) {
			$value = $saved[ $key ];
		} else {
			continue;
		}
		if ( null !== $value ) {
			$options[ $key ] = $value;
		}
	}

	$data = array(
		'theme'   => 'bigspy-ai-theme-wp',
		'format'  => 2,
		'version' => BST_VERSION,
		'site'    => home_url( '/' ),
		'date'    => gmdate( 'c' ),
		'options' => $options,
		'media'   => array(
			'custom_logo' => bst_transfer_media_url( get_theme_mod( 'custom_logo' ) ),
			'site_icon'   => bst_transfer_media_url( get_option( 'site_icon' ) ),
		),
		'css'     => wp_get_custom_css(),
	);
	return $data;
}

function bst_handle_export() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'bigspy-ai-theme-wp' ) );
	}
	check_admin_referer( 'bst_export' );

	$json = wp_json_encode( bst_export_options_data(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE );
	if ( false === $json ) {
		wp_die( esc_html__( 'The export file could not be created.', 'bigspy-ai-theme-wp' ) );
	}
	while ( ob_get_level() ) {
		ob_end_clean();
	}
	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="bigspy-ai-theme-wp-options-' . sanitize_file_name( (string) wp_parse_url( home_url(), PHP_URL_HOST ) ) . '-' . gmdate( 'Y-m-d' ) . '.json"' );
	header( 'Content-Length: ' . strlen( $json ) );
	echo $json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON download.
	exit;
}
add_action( 'admin_post_bst_export', 'bst_handle_export' );

/** Largest accepted import file. */
function bst_import_max_bytes() {
	return 10 * MB_IN_BYTES;
}

/**
 * Apply an export array to this site. Saves a backup first.
 *
 * @return array{applied:int,skipped:int,media:int,media_failed:int}
 */
function bst_import_options_data( $data ) {
	bst_options_save_backup();

	$defaults = bst_defaults();
	$keys     = bst_transfer_keys();
	$result   = array( 'applied' => 0, 'skipped' => 0, 'media' => 0, 'media_failed' => 0 );

	foreach ( $data['options'] as $key => $value ) {
		$key = (string) $key;
		if ( ! array_key_exists( $key, $keys ) ) {
			$result['skipped']++;
			continue;
		}
		$field = $keys[ $key ];
		$clean = bst_sanitize_imported( $key, $value, $defaults[ $key ] ?? '', $field );
		if ( null === $clean ) {
			$result['skipped']++;
			continue;
		}
		// Images that live on the exporting site are copied into this Media Library.
		if ( 'image' === ( $field['type'] ?? '' ) && is_string( $clean ) && bst_transfer_is_foreign( $clean ) ) {
			$id = bst_transfer_fetch_image( $clean );
			if ( $id ) {
				$clean = (string) wp_get_attachment_url( $id );
				$result['media']++;
			} else {
				$result['media_failed']++;
			}
		}
		set_theme_mod( $key, $clean );
		$result['applied']++;
	}

	$media = is_array( $data['media'] ?? null ) ? $data['media'] : array();
	if ( ! empty( $media['custom_logo'] ) && is_string( $media['custom_logo'] ) ) {
		$id = bst_transfer_fetch_image( $media['custom_logo'] );
		if ( $id ) {
			set_theme_mod( 'custom_logo', $id );
			$result['media']++;
		} else {
			$result['media_failed']++;
		}
	}
	if ( ! empty( $media['site_icon'] ) && is_string( $media['site_icon'] ) && current_user_can( 'manage_options' ) ) {
		$id = bst_transfer_fetch_image( $media['site_icon'] );
		if ( $id ) {
			update_option( 'site_icon', $id );
			$result['media']++;
		} else {
			$result['media_failed']++;
		}
	}

	if ( isset( $data['css'] ) && is_string( $data['css'] ) && '' !== trim( $data['css'] ) ) {
		wp_update_custom_css_post( wp_strip_all_tags( $data['css'] ) );
	}

	return $result;
}

/**
 * Read and validate an uploaded export file.
 *
 * @return array|string Data array, or an error notice key.
 */
function bst_import_read_upload( $file ) {
	if ( ! is_array( $file ) || ! isset( $file['error'] ) ) {
		return 'no-file';
	}
	if ( in_array( (int) $file['error'], array( UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE ), true ) ) {
		return 'too-big';
	}
	if ( UPLOAD_ERR_NO_FILE === (int) $file['error'] ) {
		return 'no-file';
	}
	if ( UPLOAD_ERR_OK !== (int) $file['error'] || empty( $file['tmp_name'] ) || ! is_uploaded_file( $file['tmp_name'] ) ) {
		return 'upload-failed';
	}
	if ( (int) $file['size'] > bst_import_max_bytes() ) {
		return 'too-big';
	}
	$raw = (string) file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$raw = preg_replace( '/^\xEF\xBB\xBF/', '', $raw ); // Files saved by some editors start with a BOM.
	$data = json_decode( $raw, true );
	if ( ! is_array( $data ) ) {
		return 'bad-json';
	}
	if ( ! in_array( $data['theme'] ?? '', array( 'bigspy-ai-theme-wp', 'bigspy-theme' ), true ) || ! is_array( $data['options'] ?? null ) ) {
		return 'invalid';
	}
	return $data;
}

function bst_handle_import() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'bigspy-ai-theme-wp' ) );
	}
	check_admin_referer( 'bst_import' );

	// A file larger than post_max_size empties $_FILES and $_POST, so the nonce check above would
	// already have failed; this covers every other case.
	$data = bst_import_read_upload( $_FILES['bst_file'] ?? null ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ! is_array( $data ) ) {
		bst_dashboard_redirect( $data );
	}

	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- copying images can take a while.
	}
	$result = bst_import_options_data( $data );
	bst_dashboard_redirect(
		'imported',
		array(
			'bst-applied' => $result['applied'],
			'bst-skipped' => $result['skipped'],
			'bst-media'   => $result['media'],
			'bst-mfail'   => $result['media_failed'],
		)
	);
}
add_action( 'admin_post_bst_import', 'bst_handle_import' );

/** Restore the state saved before the last import or reset. */
function bst_handle_undo() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'bigspy-ai-theme-wp' ) );
	}
	check_admin_referer( 'bst_undo' );
	$backup = get_option( 'bst_options_backup' );
	if ( ! is_array( $backup ) || ! is_array( $backup['mods'] ?? null ) ) {
		bst_dashboard_redirect( 'no-backup' );
	}
	$current = bst_options_snapshot();
	foreach ( array_keys( bst_transfer_keys() + array( 'custom_logo' => 1 ) ) as $key ) {
		remove_theme_mod( $key );
	}
	foreach ( $backup['mods'] as $key => $value ) {
		set_theme_mod( $key, $value );
	}
	wp_update_custom_css_post( (string) ( $backup['css'] ?? '' ) );
	if ( current_user_can( 'manage_options' ) ) {
		update_option( 'site_icon', (int) ( $backup['site_icon'] ?? 0 ) );
	}
	// Undo can itself be undone.
	update_option( 'bst_options_backup', $current, false );
	bst_dashboard_redirect( 'restored' );
}
add_action( 'admin_post_bst_undo', 'bst_handle_undo' );

function bst_handle_reset() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'bigspy-ai-theme-wp' ) );
	}
	check_admin_referer( 'bst_reset' );
	bst_options_save_backup();
	foreach ( array_keys( bst_transfer_keys() ) as $key ) {
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
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'BIGSPY AI Theme WP is active.', 'bigspy-ai-theme-wp' ) . ' <a href="' . esc_url( admin_url( 'admin.php?page=bigspy-ai-theme-wp' ) ) . '">' . esc_html__( 'Open the quick start guide', 'bigspy-ai-theme-wp' ) . '</a></p></div>';
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
	echo esc_html__( 'BIGSPY AI elements use the WordPress block editor. Classic Editor is currently the default; choose the block editor in Writing settings to edit layouts visually.', 'bigspy-ai-theme-wp' );
	echo ' <a href="' . esc_url( admin_url( 'options-writing.php#classic-editor-options' ) ) . '">' . esc_html__( 'Writing settings', 'bigspy-ai-theme-wp' ) . '</a>';
	echo '</p></div>';
}
add_action( 'admin_notices', 'bst_classic_editor_notice' );
