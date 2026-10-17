<?php
/**
 * Per-page layout options (header style, sidebar, title, footer…).
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

function bst_layout_meta_fields() {
	return array(
		'header'          => array(
			'label'   => __( 'Header', 'bigspy-ai-theme-wp' ),
			'choices' => array(
				''            => __( 'Default', 'bigspy-ai-theme-wp' ),
				'solid'       => __( 'Solid', 'bigspy-ai-theme-wp' ),
				'transparent' => __( 'Transparent (overlaps first section)', 'bigspy-ai-theme-wp' ),
				'hide'        => __( 'Hidden', 'bigspy-ai-theme-wp' ),
			),
		),
		'sidebar'         => array(
			'label'   => __( 'Sidebar', 'bigspy-ai-theme-wp' ),
			'choices' => array(
				''      => __( 'Default', 'bigspy-ai-theme-wp' ),
				'none'  => __( 'No sidebar', 'bigspy-ai-theme-wp' ),
				'left'  => __( 'Left sidebar', 'bigspy-ai-theme-wp' ),
				'right' => __( 'Right sidebar', 'bigspy-ai-theme-wp' ),
			),
		),
		'content_padding' => array(
			'label'   => __( 'Top / bottom content spacing', 'bigspy-ai-theme-wp' ),
			'choices' => array(
				''     => __( 'Default', 'bigspy-ai-theme-wp' ),
				'none' => __( 'None (content touches header / footer)', 'bigspy-ai-theme-wp' ),
			),
		),
		'hide_title'      => array( 'label' => __( 'Hide page title', 'bigspy-ai-theme-wp' ), 'checkbox' => true ),
		'hide_footer'     => array( 'label' => __( 'Hide footer', 'bigspy-ai-theme-wp' ), 'checkbox' => true ),
	);
}

function bst_layout_post_types() {
	return apply_filters( 'bst_layout_post_types', array( 'page', 'post', 'product' ) );
}

function bst_register_layout_meta() {
	foreach ( bst_layout_post_types() as $type ) {
		foreach ( bst_layout_meta_fields() as $key => $field ) {
			register_post_meta(
				$type,
				'_bst_' . $key,
				array(
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => false,
					'sanitize_callback' => 'sanitize_key',
					'auth_callback'     => function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}
}
add_action( 'init', 'bst_register_layout_meta' );

function bst_add_layout_meta_box() {
	add_meta_box( 'bst-layout', __( 'BIGSPY – Page layout', 'bigspy-ai-theme-wp' ), 'bst_layout_meta_box', bst_layout_post_types(), 'side', 'default' );
}
add_action( 'add_meta_boxes', 'bst_add_layout_meta_box' );

function bst_layout_meta_box( $post ) {
	wp_nonce_field( 'bst_layout_meta', 'bst_layout_nonce' );
	foreach ( bst_layout_meta_fields() as $key => $field ) {
		$value = get_post_meta( $post->ID, '_bst_' . $key, true );
		$id    = 'bst-meta-' . $key;
		echo '<p>';
		if ( ! empty( $field['checkbox'] ) ) {
			echo '<label for="' . esc_attr( $id ) . '"><input type="checkbox" id="' . esc_attr( $id ) . '" name="bst_meta[' . esc_attr( $key ) . ']" value="1" ' . checked( '1', $value, false ) . '> ' . esc_html( $field['label'] ) . '</label>';
		} else {
			echo '<label for="' . esc_attr( $id ) . '"><strong>' . esc_html( $field['label'] ) . '</strong></label><br>';
			echo '<select id="' . esc_attr( $id ) . '" name="bst_meta[' . esc_attr( $key ) . ']" style="width:100%">';
			foreach ( $field['choices'] as $choice => $label ) {
				echo '<option value="' . esc_attr( $choice ) . '" ' . selected( $value, $choice, false ) . '>' . esc_html( $label ) . '</option>';
			}
			echo '</select>';
		}
		echo '</p>';
	}
	echo '<p class="description">' . esc_html__( 'Tip: for landing pages choose the “Full width” or “Blank canvas” template and build with Section blocks.', 'bigspy-ai-theme-wp' ) . '</p>';
}

function bst_save_layout_meta( $post_id ) {
	if ( ! isset( $_POST['bst_layout_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['bst_layout_nonce'] ), 'bst_layout_meta' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$input = isset( $_POST['bst_meta'] ) && is_array( $_POST['bst_meta'] ) ? wp_unslash( $_POST['bst_meta'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per field below.
	foreach ( bst_layout_meta_fields() as $key => $field ) {
		$value = isset( $input[ $key ] ) ? sanitize_key( $input[ $key ] ) : '';
		if ( ! empty( $field['checkbox'] ) ) {
			$value = '1' === $value ? '1' : '';
		} elseif ( ! array_key_exists( $value, $field['choices'] ) ) {
			$value = '';
		}
		if ( '' === $value ) {
			delete_post_meta( $post_id, '_bst_' . $key );
		} else {
			update_post_meta( $post_id, '_bst_' . $key, $value );
		}
	}
}
add_action( 'save_post', 'bst_save_layout_meta' );
