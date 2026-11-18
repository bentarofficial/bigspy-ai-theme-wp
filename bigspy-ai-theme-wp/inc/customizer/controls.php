<?php
/**
 * Custom Customizer controls. Loaded inside customize_register.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * A title / separator between groups of options.
 */
class BST_Heading_Control extends WP_Customize_Control {
	public $type = 'bst-heading';

	public function render_content() {
		echo '<h3 class="bst-customizer-heading">' . esc_html( $this->label ) . '</h3>';
		if ( $this->description ) {
			echo '<p class="description">' . wp_kses_post( $this->description ) . '</p>';
		}
	}
}

/**
 * Drag & drop header builder. The UI is built by customizer-controls.js.
 */
class BST_Header_Builder_Control extends WP_Customize_Control {
	public $type = 'bst-header-builder';

	public function render_content() {
		?>
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php if ( $this->description ) : ?>
			<p class="description"><?php echo wp_kses_post( $this->description ); ?></p>
		<?php endif; ?>
		<input type="hidden" class="bst-hb__value" <?php $this->link(); ?> value="<?php echo esc_attr( wp_json_encode( bst_normalize_header_layout( $this->value() ) ) ); ?>">
		<div class="bst-hb" data-bst-header-builder></div>
		<?php
	}
}

/** One-click, editable layout presets for header and footer settings. */
class BST_Layout_Presets_Control extends WP_Customize_Control {
	public $type = 'bst-layout-presets';
	public $presets = array();
	public $help_url = '';
	public $help_label = '';
	public $help_url_2 = '';
	public $help_label_2 = '';

	public function render_content() {
		echo '<span class="customize-control-title">' . esc_html( $this->label ) . '</span>';
		if ( $this->description ) {
			echo '<p class="description">' . wp_kses_post( $this->description ) . '</p>';
		}
		echo '<div class="bst-layout-presets" data-bst-layout-presets>';
		foreach ( $this->presets as $key => $preset ) {
			$swatches = '';
			foreach ( (array) ( $preset['swatches'] ?? array() ) as $swatch ) {
				$swatches .= '<i style="background:' . esc_attr( sanitize_hex_color( $swatch ) ) . '"></i>';
			}
			echo '<button type="button" class="button bst-layout-presets__button' . ( $swatches ? ' has-swatches' : '' ) . '" data-preset="' . esc_attr( $key ) . '" data-settings="' . esc_attr( wp_json_encode( $preset['settings'] ) ) . '">' . ( $swatches ? '<em class="bst-swatches" aria-hidden="true">' . $swatches . '</em>' : '' ) . '<strong>' . esc_html( $preset['label'] ) . '</strong><span>' . esc_html( $preset['description'] ) . '</span></button>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div><p class="bst-layout-presets__status" role="status" aria-live="polite"></p>';
		if ( $this->help_url && $this->help_label ) {
			echo '<p><a class="button-link" href="' . esc_url( $this->help_url ) . '" target="_blank" rel="noopener">' . esc_html( $this->help_label ) . '</a></p>';
		}
		if ( $this->help_url_2 && $this->help_label_2 ) {
			echo '<p><a class="button-link" href="' . esc_url( $this->help_url_2 ) . '" target="_blank" rel="noopener">' . esc_html( $this->help_label_2 ) . '</a></p>';
		}
	}
}

/** Accessible checkbox rows; recipe code stays out of the controls interface. */
class BST_CSS_Presets_Control extends WP_Customize_Control {
	public $type = 'bst-css-presets';

	public function render_content() {
		$moved   = bst_css_preset_moved_groups();
		$recipes = array_filter( bst_css_presets(), function ( $recipe ) use ( $moved ) {
			return ! in_array( $recipe[0], $moved, true );
		} );
		$enabled = bst_sanitize_css_presets( $this->value() );
		$prefix  = 'bst-css-presets-' . sanitize_html_class( $this->id );
		?>
		<div class="bst-css-presets" data-bst-css-presets>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<p class="description"><?php echo esc_html( $this->description ); ?></p>
			<label class="screen-reader-text" for="<?php echo esc_attr( $prefix . '-search' ); ?>"><?php esc_html_e( 'Tìm tác vụ CSS', 'bigspy-ai-theme-wp' ); ?></label>
			<input id="<?php echo esc_attr( $prefix . '-search' ); ?>" class="bst-css-presets__search" type="search" placeholder="<?php esc_attr_e( 'Tìm tác vụ CSS…', 'bigspy-ai-theme-wp' ); ?>" autocomplete="off">
			<div class="bst-css-presets__toolbar">
				<label><input type="checkbox" class="bst-css-presets__active-only"> <?php esc_html_e( 'Chỉ đang bật', 'bigspy-ai-theme-wp' ); ?></label>
				<button type="button" class="button-link bst-css-presets__clear"><?php esc_html_e( 'Tắt tất cả', 'bigspy-ai-theme-wp' ); ?></button>
				<button type="button" class="button-link bst-css-presets__undo" hidden><?php esc_html_e( 'Hoàn tác', 'bigspy-ai-theme-wp' ); ?></button>
			</div>
			<p class="bst-css-presets__status" role="status" aria-live="polite"><?php esc_html_e( 'Đang bật:', 'bigspy-ai-theme-wp' ); ?> <span data-bst-enabled-count><?php echo (int) count( $enabled ); ?></span> / <?php echo (int) count( $recipes ); ?></p>
			<?php foreach ( array_diff_key( bst_css_preset_groups(), array_flip( $moved ) ) as $group => $title ) : ?>
				<details class="bst-css-presets__group" data-group="<?php echo esc_attr( $group ); ?>">
					<summary><?php echo esc_html( $title ); ?> <span class="bst-css-presets__group-count" aria-hidden="true"></span></summary>
					<?php foreach ( $recipes as $id => $recipe ) : ?>
						<?php if ( $group !== $recipe[0] ) { continue; } ?>
						<div class="bst-css-presets__row" data-preset="<?php echo esc_attr( $id ); ?>" data-exclusive="<?php echo esc_attr( $recipe[3] ); ?>">
							<label title="<?php echo esc_attr( $recipe[4] ); ?>">
								<input type="checkbox" value="<?php echo esc_attr( $id ); ?>" aria-describedby="<?php echo esc_attr( $prefix . '-' . $id ); ?>" <?php checked( in_array( $id, $enabled, true ) ); ?>>
								<span><?php echo esc_html( $recipe[1] ); ?></span>
							</label>
							<span id="<?php echo esc_attr( $prefix . '-' . $id ); ?>" class="screen-reader-text"><?php echo esc_html( $recipe[4] ); ?></span>
						</div>
					<?php endforeach; ?>
				</details>
			<?php endforeach; ?>
			<p class="bst-css-presets__empty" hidden><?php esc_html_e( 'Không có tác vụ phù hợp.', 'bigspy-ai-theme-wp' ); ?></p>
			<p class="description"><?php esc_html_e( 'Mục WooCommerce cần plugin WooCommerce. Mục dịch vụ cần block BIGSPY AI tương ứng. Nếu chưa có thành phần trên trang, tác vụ chưa tạo thay đổi.', 'bigspy-ai-theme-wp' ); ?></p>
		</div>
		<?php
	}
}

/**
 * G-199: one value per device (desktop / tablet / mobile). Linked settings:
 * 'default' = desktop (the original option id, so old values stay as desktop),
 * 'tablet' and 'mobile' = "<id>_tablet" / "<id>_mobile", empty = inherit.
 * The device tabs follow the Customizer's own device buttons.
 */
class BST_Responsive_Control extends WP_Customize_Control {
	public $type = 'bst-responsive';
	public $unit = 'px';

	public function render_content() {
		$devices = bst_responsive_devices();
		echo '<span class="customize-control-title">' . esc_html( $this->label ) . '</span>';
		if ( $this->description ) {
			echo '<span class="description customize-control-description">' . wp_kses_post( $this->description ) . '</span>';
		}
		echo '<div class="bst-resp" data-bst-responsive>';
		echo '<div class="bst-resp__tabs" role="tablist">';
		foreach ( $devices as $key => $device ) {
			echo '<button type="button" role="tab" class="bst-resp__tab" data-device="' . esc_attr( $device['preview'] ) . '" title="' . esc_attr( $device['label'] ) . '" aria-label="' . esc_attr( $device['label'] ) . '"><span class="dashicons ' . esc_attr( $device['icon'] ) . '" aria-hidden="true"></span></button>';
		}
		echo '</div>';
		foreach ( $devices as $key => $device ) {
			$setting_key = 'desktop' === $key ? 'default' : $key;
			if ( ! isset( $this->settings[ $setting_key ] ) ) {
				continue;
			}
			$placeholder = 'desktop' === $key ? '' : __( 'Same as larger screen', 'bigspy-ai-theme-wp' );
			echo '<label class="bst-resp__field" data-device="' . esc_attr( $device['preview'] ) . '"><span class="screen-reader-text">' . esc_html( $this->label . ' – ' . $device['label'] ) . '</span>';
			echo '<input type="number"';
			foreach ( (array) $this->input_attrs as $attr => $val ) {
				echo ' ' . esc_attr( $attr ) . '="' . esc_attr( $val ) . '"';
			}
			echo ' placeholder="' . esc_attr( $placeholder ) . '" value="' . esc_attr( $this->value( $setting_key ) ) . '" ';
			$this->link( $setting_key );
			echo '><em>' . esc_html( $this->unit ) . '</em></label>';
		}
		echo '</div>';
	}
}

/**
 * G-199: four sides (top right bottom left) with a link lock, a unit and
 * optional per-device values. Stored as one JSON string, see bst_sanitize_spacing().
 */
class BST_Spacing_Control extends WP_Customize_Control {
	public $type = 'bst-spacing';
	public $responsive = true;
	public $units = array( 'px', 'em', 'rem', '%' );

	public function render_content() {
		$devices = $this->responsive ? bst_responsive_devices() : array_slice( bst_responsive_devices(), 0, 1, true );
		$sides   = array(
			__( 'Top', 'bigspy-ai-theme-wp' ),
			__( 'Right', 'bigspy-ai-theme-wp' ),
			__( 'Bottom', 'bigspy-ai-theme-wp' ),
			__( 'Left', 'bigspy-ai-theme-wp' ),
		);
		echo '<span class="customize-control-title">' . esc_html( $this->label ) . '</span>';
		if ( $this->description ) {
			echo '<span class="description customize-control-description">' . wp_kses_post( $this->description ) . '</span>';
		}
		echo '<div class="bst-spacing" data-bst-spacing data-units="' . esc_attr( wp_json_encode( array_values( $this->units ) ) ) . '">';
		echo '<input type="hidden" class="bst-spacing__value" value="' . esc_attr( (string) $this->value() ) . '" ';
		$this->link();
		echo '>';
		echo '<div class="bst-resp__tabs" role="tablist">';
		if ( count( $devices ) > 1 ) {
			foreach ( $devices as $device ) {
				echo '<button type="button" role="tab" class="bst-resp__tab" data-device="' . esc_attr( $device['preview'] ) . '" title="' . esc_attr( $device['label'] ) . '" aria-label="' . esc_attr( $device['label'] ) . '"><span class="dashicons ' . esc_attr( $device['icon'] ) . '" aria-hidden="true"></span></button>';
			}
		}
		echo '<select class="bst-spacing__unit" aria-label="' . esc_attr__( 'Unit', 'bigspy-ai-theme-wp' ) . '">';
		foreach ( $this->units as $unit ) {
			echo '<option value="' . esc_attr( $unit ) . '">' . esc_html( $unit ) . '</option>';
		}
		echo '</select></div>';
		foreach ( $devices as $key => $device ) {
			echo '<div class="bst-spacing__row" data-device="' . esc_attr( $device['preview'] ) . '" data-key="' . esc_attr( $key ) . '">';
			foreach ( $sides as $i => $side ) {
				echo '<label><input type="number" step="any" data-side="' . (int) $i . '" placeholder="' . ( 'desktop' === $key ? '' : '–' ) . '"><span>' . esc_html( $side ) . '</span></label>';
			}
			echo '<button type="button" class="bst-spacing__link" aria-pressed="false" title="' . esc_attr__( 'Link all four sides', 'bigspy-ai-theme-wp' ) . '"><span class="dashicons dashicons-admin-links" aria-hidden="true"></span><span class="screen-reader-text">' . esc_html__( 'Link all four sides', 'bigspy-ai-theme-wp' ) . '</span></button>';
			echo '</div>';
		}
		echo '<p class="description bst-spacing__hint">' . esc_html__( 'Empty = theme default. Tablet and mobile follow the larger screen until you type a value.', 'bigspy-ai-theme-wp' ) . '</p>';
		echo '</div>';
	}
}
