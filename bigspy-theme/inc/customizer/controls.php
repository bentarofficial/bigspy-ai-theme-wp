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
