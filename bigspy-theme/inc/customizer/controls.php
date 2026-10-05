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

/** Accessible checkbox rows; recipe code stays out of the controls interface. */
class BST_CSS_Presets_Control extends WP_Customize_Control {
	public $type = 'bst-css-presets';

	public function render_content() {
		$recipes = bst_css_presets();
		$enabled = bst_sanitize_css_presets( $this->value() );
		$prefix  = 'bst-css-presets-' . sanitize_html_class( $this->id );
		?>
		<div class="bst-css-presets" data-bst-css-presets>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<p class="description"><?php echo esc_html( $this->description ); ?></p>
			<label class="screen-reader-text" for="<?php echo esc_attr( $prefix . '-search' ); ?>"><?php esc_html_e( 'Tìm tác vụ CSS', 'bigspy-theme' ); ?></label>
			<input id="<?php echo esc_attr( $prefix . '-search' ); ?>" class="bst-css-presets__search" type="search" placeholder="<?php esc_attr_e( 'Tìm tác vụ CSS…', 'bigspy-theme' ); ?>" autocomplete="off">
			<div class="bst-css-presets__toolbar">
				<label><input type="checkbox" class="bst-css-presets__active-only"> <?php esc_html_e( 'Chỉ đang bật', 'bigspy-theme' ); ?></label>
				<button type="button" class="button-link bst-css-presets__clear"><?php esc_html_e( 'Tắt tất cả', 'bigspy-theme' ); ?></button>
				<button type="button" class="button-link bst-css-presets__undo" hidden><?php esc_html_e( 'Hoàn tác', 'bigspy-theme' ); ?></button>
			</div>
			<p class="bst-css-presets__status" role="status" aria-live="polite"><?php esc_html_e( 'Đang bật:', 'bigspy-theme' ); ?> <span data-bst-enabled-count><?php echo (int) count( $enabled ); ?></span> / <?php echo (int) count( $recipes ); ?></p>
			<?php foreach ( bst_css_preset_groups() as $group => $title ) : ?>
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
			<p class="bst-css-presets__empty" hidden><?php esc_html_e( 'Không có tác vụ phù hợp.', 'bigspy-theme' ); ?></p>
			<p class="description"><?php esc_html_e( 'Mục WooCommerce cần plugin WooCommerce. Mục dịch vụ cần block BIGSPY tương ứng. Nếu chưa có thành phần trên trang, tác vụ chưa tạo thay đổi.', 'bigspy-theme' ); ?></p>
		</div>
		<?php
	}
}
