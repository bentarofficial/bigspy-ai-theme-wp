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
