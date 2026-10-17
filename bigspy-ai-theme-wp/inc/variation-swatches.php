<?php
/** Attribute term swatches for WooCommerce variable products. */
defined( 'ABSPATH' ) || exit;

function bst_swatch_attribute_taxonomy( $attribute ) {
	$attribute = sanitize_title( (string) $attribute );
	if ( 0 !== strpos( $attribute, 'pa_' ) ) { return ''; }
	return taxonomy_exists( $attribute ) ? $attribute : '';
}

function bst_swatch_attribute_type( $taxonomy ) {
	$id = wc_attribute_taxonomy_id_by_name( $taxonomy );
	$type = $id ? get_option( 'bst_swatch_attribute_' . absint( $id ), 'label' ) : 'label';
	return in_array( $type, array( 'label', 'color', 'image' ), true ) ? $type : 'label';
}

/** Render compact global-attribute swatches on shop archive product cards. */
function bst_archive_product_swatches_html( $product ) {
	if ( ! bst_mod( 'product_archive_swatches' ) || ! ( is_shop() || is_product_taxonomy() ) || ! $product || ! $product->is_type( 'variable' ) ) {
		return '';
	}
	$attributes = $product->get_variation_attributes();
	foreach ( $attributes as $key => $values ) {
		$taxonomy = str_replace( 'attribute_', '', (string) $key );
		if ( ! taxonomy_exists( $taxonomy ) || 0 !== strpos( $taxonomy, 'pa_' ) || ! in_array( bst_swatch_attribute_type( $taxonomy ), array( 'color', 'image' ), true ) ) {
			continue;
		}
		$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false, 'slug' => array_map( 'sanitize_title', (array) $values ) ) );
		if ( is_wp_error( $terms ) || ! $terms ) {
			continue;
		}
		$type = bst_swatch_attribute_type( $taxonomy );
		$out  = '<div class="bst-archive-swatches" role="group" aria-label="' . esc_attr( wc_attribute_label( $taxonomy ) ) . '">';
		foreach ( $terms as $term ) {
			$label = (string) ( get_term_meta( $term->term_id, 'bst_swatch_label', true ) ?: $term->name );
			$url   = add_query_arg( 'attribute_' . $taxonomy, $term->slug, get_permalink( $product->get_id() ) );
			$out  .= '<a class="bst-archive-swatch bst-archive-swatch--' . esc_attr( $type ) . '" href="' . esc_url( $url ) . '" aria-label="' . esc_attr( $label ) . '" title="' . esc_attr( $label ) . '">';
			if ( 'color' === $type ) {
				$color = sanitize_hex_color( get_term_meta( $term->term_id, 'bst_swatch_color', true ) );
				if ( $color ) {
					$out .= '<span class="bst-archive-swatch__color" style="--bst-swatch-color:' . esc_attr( $color ) . '"></span>';
				} else {
					$out .= '<span class="bst-archive-swatch__label">' . esc_html( $label ) . '</span>';
				}
			} else {
				$image_id = absint( get_term_meta( $term->term_id, 'bst_swatch_image', true ) );
				if ( $image_id ) {
					$out .= wp_get_attachment_image( $image_id, 'thumbnail', false, array( 'class' => 'bst-archive-swatch__image', 'alt' => '', 'loading' => 'lazy' ) );
				} else {
					$out .= '<span class="bst-archive-swatch__label">' . esc_html( $label ) . '</span>';
				}
			}
			$out .= '</a>';
		}
		return $out . '</div>';
	}
	return '';
}

function bst_swatch_attribute_fields() {
	$attribute_id = isset( $_GET['edit'] ) ? absint( $_GET['edit'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$type = $attribute_id ? get_option( 'bst_swatch_attribute_' . $attribute_id, 'label' ) : 'label';
	?>
	<div class="form-field">
		<label for="bst-swatch-attribute-type"><?php esc_html_e( 'BIGSPY swatch type', 'bigspy-ai-theme-wp' ); ?></label>
		<select name="bst_swatch_attribute_type" id="bst-swatch-attribute-type">
			<option value="label" <?php selected( $type, 'label' ); ?>><?php esc_html_e( 'Label buttons', 'bigspy-ai-theme-wp' ); ?></option>
			<option value="color" <?php selected( $type, 'color' ); ?>><?php esc_html_e( 'Color swatches', 'bigspy-ai-theme-wp' ); ?></option>
			<option value="image" <?php selected( $type, 'image' ); ?>><?php esc_html_e( 'Image swatches', 'bigspy-ai-theme-wp' ); ?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Applies when BIGSPY variation buttons are enabled. Set each color or image in Configure terms.', 'bigspy-ai-theme-wp' ); ?></p>
	</div>
	<?php
}
add_action( 'woocommerce_after_add_attribute_fields', 'bst_swatch_attribute_fields' );
add_action( 'woocommerce_after_edit_attribute_fields', 'bst_swatch_attribute_fields' );

function bst_swatch_save_attribute_type( $attribute_id ) {
	if ( ! current_user_can( 'manage_product_terms' ) || ! isset( $_POST['bst_swatch_attribute_type'] ) ) { return; } // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$type = sanitize_key( wp_unslash( $_POST['bst_swatch_attribute_type'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
	if ( in_array( $type, array( 'label', 'color', 'image' ), true ) ) {
		update_option( 'bst_swatch_attribute_' . absint( $attribute_id ), $type, false );
	}
}
add_action( 'woocommerce_attribute_added', 'bst_swatch_save_attribute_type' );
add_action( 'woocommerce_attribute_updated', 'bst_swatch_save_attribute_type' );

function bst_swatch_term_add_fields( $taxonomy ) {
	if ( ! current_user_can( 'manage_product_terms' ) ) { return; }
	?>
	<div class="form-field bst-swatch-term-field">
		<label for="bst-swatch-color"><?php esc_html_e( 'BIGSPY swatch color', 'bigspy-ai-theme-wp' ); ?></label>
		<input type="text" id="bst-swatch-color" name="bst_swatch_color" value="" class="bst-swatch-color-field" />
		<p class="description"><?php esc_html_e( 'Use a CSS color value, for example #e83e56. Used when this attribute is set to Color swatches.', 'bigspy-ai-theme-wp' ); ?></p>
	</div>
	<div class="form-field bst-swatch-term-field">
		<label><?php esc_html_e( 'BIGSPY swatch image', 'bigspy-ai-theme-wp' ); ?></label>
		<input type="hidden" name="bst_swatch_image" value="" class="bst-swatch-image-id" />
		<button type="button" class="button bst-swatch-image-pick"><?php esc_html_e( 'Choose image', 'bigspy-ai-theme-wp' ); ?></button>
		<button type="button" class="button-link-delete bst-swatch-image-remove" hidden><?php esc_html_e( 'Remove image', 'bigspy-ai-theme-wp' ); ?></button>
		<span class="bst-swatch-image-preview"></span>
		<p class="description"><?php esc_html_e( 'Used when this attribute is set to Image swatches.', 'bigspy-ai-theme-wp' ); ?></p>
	</div>
	<div class="form-field bst-swatch-term-field">
		<label for="bst-swatch-label"><?php esc_html_e( 'BIGSPY swatch label', 'bigspy-ai-theme-wp' ); ?></label>
		<input type="text" id="bst-swatch-label" name="bst_swatch_label" value="" />
		<p class="description"><?php esc_html_e( 'Optional short label shown on the button. Leave empty to use the term name.', 'bigspy-ai-theme-wp' ); ?></p>
	</div>
	<?php
}

function bst_swatch_term_edit_fields( $term, $taxonomy ) {
	if ( ! current_user_can( 'manage_product_terms' ) ) { return; }
	$color = get_term_meta( $term->term_id, 'bst_swatch_color', true );
	$image = absint( get_term_meta( $term->term_id, 'bst_swatch_image', true ) );
	$label = get_term_meta( $term->term_id, 'bst_swatch_label', true );
	?>
	<tr class="form-field bst-swatch-term-field">
		<th scope="row"><label for="bst-swatch-color"><?php esc_html_e( 'BIGSPY swatch color', 'bigspy-ai-theme-wp' ); ?></label></th>
		<td><input type="text" id="bst-swatch-color" name="bst_swatch_color" value="<?php echo esc_attr( $color ); ?>" class="bst-swatch-color-field" /><p class="description"><?php esc_html_e( 'Use a CSS color value, for example #e83e56.', 'bigspy-ai-theme-wp' ); ?></p></td>
	</tr>
	<tr class="form-field bst-swatch-term-field">
		<th scope="row"><label><?php esc_html_e( 'BIGSPY swatch image', 'bigspy-ai-theme-wp' ); ?></label></th>
		<td><input type="hidden" name="bst_swatch_image" value="<?php echo esc_attr( $image ); ?>" class="bst-swatch-image-id" /><button type="button" class="button bst-swatch-image-pick"><?php esc_html_e( 'Choose image', 'bigspy-ai-theme-wp' ); ?></button> <button type="button" class="button-link-delete bst-swatch-image-remove" <?php echo $image ? '' : 'hidden'; ?>><?php esc_html_e( 'Remove image', 'bigspy-ai-theme-wp' ); ?></button><span class="bst-swatch-image-preview"><?php echo $image ? wp_get_attachment_image( $image, array( 48, 48 ) ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span></td>
	</tr>
	<tr class="form-field bst-swatch-term-field">
		<th scope="row"><label for="bst-swatch-label"><?php esc_html_e( 'BIGSPY swatch label', 'bigspy-ai-theme-wp' ); ?></label></th>
		<td><input type="text" id="bst-swatch-label" name="bst_swatch_label" value="<?php echo esc_attr( $label ); ?>" /><p class="description"><?php esc_html_e( 'Optional short label shown on the button.', 'bigspy-ai-theme-wp' ); ?></p></td>
	</tr>
	<?php
}

function bst_swatch_save_term_fields( $term_id ) {
	if ( ! current_user_can( 'manage_product_terms' ) ) { return; }
	if ( isset( $_POST['bst_swatch_color'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$color = sanitize_hex_color( wp_unslash( $_POST['bst_swatch_color'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( $color ) { update_term_meta( $term_id, 'bst_swatch_color', $color ); } else { delete_term_meta( $term_id, 'bst_swatch_color' ); }
	}
	if ( isset( $_POST['bst_swatch_image'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$image = absint( $_POST['bst_swatch_image'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( $image && wp_attachment_is_image( $image ) ) { update_term_meta( $term_id, 'bst_swatch_image', $image ); } else { delete_term_meta( $term_id, 'bst_swatch_image' ); }
	}
	if ( isset( $_POST['bst_swatch_label'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$label = sanitize_text_field( wp_unslash( $_POST['bst_swatch_label'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( '' !== $label ) { update_term_meta( $term_id, 'bst_swatch_label', $label ); } else { delete_term_meta( $term_id, 'bst_swatch_label' ); }
	}
}

add_action( 'admin_init', function () {
	$taxonomies = get_object_taxonomies( 'product', 'names' );
	foreach ( $taxonomies as $taxonomy ) {
		if ( 0 !== strpos( $taxonomy, 'pa_' ) ) { continue; }
		add_action( $taxonomy . '_add_form_fields', 'bst_swatch_term_add_fields' );
		add_action( $taxonomy . '_edit_form_fields', 'bst_swatch_term_edit_fields', 10, 2 );
		add_action( 'created_' . $taxonomy, 'bst_swatch_save_term_fields' );
		add_action( 'edited_' . $taxonomy, 'bst_swatch_save_term_fields' );
	}
	$screen = get_current_screen();
	if ( $screen && 'product_page_product_attributes' === $screen->id ) {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
	}
	if ( $screen && 'edit-tags' === $screen->base && 0 === strpos( (string) $screen->taxonomy, 'pa_' ) ) {
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'bst-swatch-term-admin', BST_URI . '/assets/js/swatch-term-admin.js', array( 'jquery', 'wp-color-picker' ), bst_asset_version( 'assets/js/swatch-term-admin.js' ), true );
		wp_enqueue_style( 'bst-swatch-term-admin', BST_URI . '/assets/css/swatch-term-admin.css', array(), bst_asset_version( 'assets/css/swatch-term-admin.css' ) );
	}
} );

add_filter( 'woocommerce_dropdown_variation_attribute_options_html', function ( $html, $args ) {
	if ( empty( $args['attribute'] ) || empty( $args['product'] ) || ! $args['product'] instanceof WC_Product ) { return $html; }
	$taxonomy = bst_swatch_attribute_taxonomy( $args['attribute'] );
	if ( ! $taxonomy ) { return $html; }
	$type = bst_swatch_attribute_type( $taxonomy );
	$map = array();
	foreach ( (array) ( $args['options'] ?? array() ) as $value ) {
		$term = get_term_by( 'slug', (string) $value, $taxonomy );
		if ( ! $term || is_wp_error( $term ) ) { continue; }
		$image_id = absint( get_term_meta( $term->term_id, 'bst_swatch_image', true ) );
		$image = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : '';
		$map[ (string) $value ] = array(
			'type' => $type,
			'label' => (string) ( get_term_meta( $term->term_id, 'bst_swatch_label', true ) ?: $term->name ),
			'color' => sanitize_hex_color( get_term_meta( $term->term_id, 'bst_swatch_color', true ) ) ?: '',
			'image' => $image ? esc_url_raw( $image ) : '',
		);
	}
	if ( $map ) {
		$html = preg_replace( '/<select\b/', '<select data-bst-swatch-map="' . esc_attr( wp_json_encode( $map ) ) . '"', $html, 1 );
	}
	return $html;
}, 10, 2 );
