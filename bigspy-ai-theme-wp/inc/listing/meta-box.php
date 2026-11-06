<?php
/**
 * "BIGSPY Listing Information" box on the product edit screen, with the same meta keys
 * as the old theme. The 21 listing fields get their own box when ACF is not active.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'add_meta_boxes_product',
	function () {
		add_meta_box( 'bigspy_dynamic_listing_information', __( 'BIGSPY Listing Information', 'bigspy-ai-theme-wp' ), 'bst_listing_meta_box', 'product', 'normal', 'high' );
		if ( ! function_exists( 'acf_get_field_group' ) || ! acf_get_field_group( 'group_bigspy_listing' ) ) {
			add_meta_box( 'bst_listing_fields', __( 'Listing fields', 'bigspy-ai-theme-wp' ), 'bst_listing_fields_box', 'product', 'normal', 'default' );
		}
	}
);

function bst_listing_meta_box( $post ) {
	wp_nonce_field( 'bst_listing_save', 'bst_listing_nonce' );
	$row = function ( $label = '', $value = '' ) {
		return '<div class="bst-lrow"><input type="text" class="widefat" name="bst_attr_label[]" value="' . esc_attr( $label ) . '" placeholder="' . esc_attr__( 'Label', 'bigspy-ai-theme-wp' ) . '"><input type="text" class="widefat" name="bst_attr_value[]" value="' . esc_attr( $value ) . '" placeholder="' . esc_attr__( 'Value', 'bigspy-ai-theme-wp' ) . '"><button type="button" class="button-link-delete" data-bst-del aria-label="' . esc_attr__( 'Remove', 'bigspy-ai-theme-wp' ) . '">×</button></div>';
	};
	$attrs = bst_listing_attributes( $post->ID );
	echo '<p><label><input type="checkbox" name="_bigspy_ad" value="1"' . checked( get_post_meta( $post->ID, '_bigspy_ad', true ), '1', false ) . '> <strong>' . esc_html__( 'AD: show in Sponsored slots', 'bigspy-ai-theme-wp' ) . '</strong></label></p>';
	echo '<p><label><input type="checkbox" name="_bigspy_paid_listing" value="1"' . checked( bst_listing_is_paid( $post->ID ), true, false ) . '> <strong>' . esc_html__( 'Paid listing', 'bigspy-ai-theme-wp' ) . '</strong></label></p>';
	echo '<p class="description">' . esc_html__( 'Tick when the company paid for this listing or placement. The listing then shows a "Sponsored" label and a disclosure line.', 'bigspy-ai-theme-wp' ) . '</p>';
	echo '<p><input type="text" class="widefat" name="_bigspy_sponsor_name" value="' . esc_attr( get_post_meta( $post->ID, '_bigspy_sponsor_name', true ) ) . '" placeholder="' . esc_attr__( 'Sponsor name (optional, defaults to the company name)', 'bigspy-ai-theme-wp' ) . '"></p>';
	echo '<p><strong>' . esc_html__( 'Listing type', 'bigspy-ai-theme-wp' ) . '</strong><input type="text" class="widefat" name="_bigspy_listing_type" value="' . esc_attr( get_post_meta( $post->ID, '_bigspy_listing_type', true ) ) . '" placeholder="' . esc_attr__( 'AI Tool, Travel, Product, Service…', 'bigspy-ai-theme-wp' ) . '"></p>';
	echo '<p><strong>' . esc_html__( 'Listing information', 'bigspy-ai-theme-wp' ) . '</strong> <span class="description">' . esc_html__( 'Any label and value. Empty rows are ignored.', 'bigspy-ai-theme-wp' ) . '</span></p>';
	echo '<div data-bst-rows>';
	foreach ( $attrs ? $attrs : array( array( 'label' => '', 'value' => '' ) ) as $attr ) {
		echo $row( $attr['label'], $attr['value'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in $row.
	}
	echo '</div><template data-bst-tpl>' . $row() . '</template>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in $row.
	echo '<p><button type="button" class="button" data-bst-add>+ ' . esc_html__( 'Add information', 'bigspy-ai-theme-wp' ) . '</button></p>';
	echo '<style>.bst-lrow{display:grid;grid-template-columns:1fr 1fr 28px;gap:6px;margin:0 0 6px;align-items:center}.bst-lrow .button-link-delete{font-size:20px;text-decoration:none}</style>';
	echo "<script>(function(){var r=document.querySelector('[data-bst-rows]'),t=document.querySelector('[data-bst-tpl]');document.querySelector('[data-bst-add]').onclick=function(){r.appendChild(t.content.cloneNode(true))};r.onclick=function(e){var b=e.target.closest('[data-bst-del]');if(b){b.parentNode.remove()}}})();</script>";
}

function bst_listing_fields_box( $post ) {
	echo '<table class="form-table" role="presentation">';
	foreach ( bst_listing_fields() as $key => $type ) {
		$value = get_post_meta( $post->ID, $key, true );
		$name  = 'bst_lf[' . $key . ']';
		$label = ucfirst( str_replace( '_', ' ', $key ) );
		if ( 'bool' === $type ) {
			$input = '<input type="checkbox" name="' . esc_attr( $name ) . '" value="1"' . checked( (bool) $value, true, false ) . '>';
		} elseif ( 'textarea' === $type ) {
			$input = '<textarea class="large-text" rows="3" name="' . esc_attr( $name ) . '">' . esc_textarea( is_array( $value ) ? '' : (string) $value ) . '</textarea>';
		} else {
			$input = '<input type="' . ( 'url' === $type ? 'url' : 'text' ) . '" class="regular-text" name="' . esc_attr( $name ) . '" value="' . esc_attr( is_array( $value ) ? '' : (string) $value ) . '">';
		}
		if ( 'faq' === $key ) {
			$input .= '<p class="description">' . esc_html__( 'One per line: Question | Answer', 'bigspy-ai-theme-wp' ) . '</p>';
		}
		echo '<tr><th scope="row">' . esc_html( $label ) . '</th><td>' . $input . '</td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped while building.
	}
	echo '</table>';
}

/** Save both boxes. Values are stored under the old keys, so ACF and the plugins read them too. */
function bst_listing_save( $post_id, $post_data ) {
	$flag = function ( $key ) use ( $post_id, $post_data ) {
		if ( empty( $post_data[ $key ] ) ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, '1' );
		}
	};
	$was_ad = '1' === (string) get_post_meta( $post_id, '_bigspy_ad', true );
	$flag( '_bigspy_ad' );
	$flag( '_bigspy_paid_listing' );
	if ( $was_ad !== ! empty( $post_data['_bigspy_ad'] ) ) {
		do_action( 'bst_listing_ad_changed', $post_id );
	}
	foreach ( array( '_bigspy_sponsor_name', '_bigspy_listing_type' ) as $key ) {
		$text = sanitize_text_field( wp_unslash( $post_data[ $key ] ?? '' ) );
		if ( '' === $text ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $text );
		}
	}
	$labels = array_values( (array) wp_unslash( $post_data['bst_attr_label'] ?? array() ) );
	$values = array_values( (array) wp_unslash( $post_data['bst_attr_value'] ?? array() ) );
	$attrs  = array();
	foreach ( $labels as $i => $label ) {
		$label = sanitize_text_field( (string) $label );
		$value = sanitize_text_field( (string) ( $values[ $i ] ?? '' ) );
		if ( '' !== $label && '' !== $value ) {
			$attrs[] = array( 'label' => $label, 'value' => $value, 'type' => 'text', 'order' => count( $attrs ) );
		}
	}
	if ( $attrs ) {
		update_post_meta( $post_id, '_bigspy_dynamic_attributes', wp_slash( wp_json_encode( $attrs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) );
	} else {
		delete_post_meta( $post_id, '_bigspy_dynamic_attributes' );
	}
	if ( isset( $post_data['bst_lf'] ) && is_array( $post_data['bst_lf'] ) ) {
		$fields = wp_unslash( $post_data['bst_lf'] );
		foreach ( bst_listing_fields() as $key => $type ) {
			$raw = $fields[ $key ] ?? '';
			if ( 'bool' === $type ) {
				$clean = empty( $raw ) ? '' : '1';
			} elseif ( 'url' === $type ) {
				$clean = esc_url_raw( (string) $raw );
			} elseif ( 'textarea' === $type ) {
				$clean = sanitize_textarea_field( (string) $raw );
			} elseif ( 'number' === $type ) {
				$clean = is_numeric( $raw ) ? (string) ( 0 + $raw ) : '';
			} else {
				$clean = sanitize_text_field( (string) $raw );
			}
			if ( '' === $clean ) {
				delete_post_meta( $post_id, $key );
			} else {
				update_post_meta( $post_id, $key, $clean );
			}
		}
	}
}

add_action(
	'save_post_product',
	function ( $post_id ) {
		if ( ! isset( $_POST['bst_listing_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bst_listing_nonce'] ) ), 'bst_listing_save' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		bst_listing_save( $post_id, $_POST ); // phpcs:ignore WordPress.Security.NonceVerification -- checked above; values are cleaned per field.
	},
	20
);
