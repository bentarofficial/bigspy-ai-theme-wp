<?php
/**
 * WebP on upload: replaces a new JPEG or PNG with a WebP copy before WordPress creates the attachment.
 * On any failure, or when the WebP is not smaller, the original file stays as it was.
 */
defined( 'ABSPATH' ) || exit;

function bst_webp_supported() {
	return wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) );
}

function bst_webp_convert( $upload ) {
	if ( ! empty( $upload['error'] ) || ! in_array( $upload['type'] ?? '', array( 'image/jpeg', 'image/png' ), true ) || ! bst_webp_supported() ) {
		return $upload;
	}
	$editor = wp_get_image_editor( $upload['file'] );
	if ( is_wp_error( $editor ) ) {
		return $upload;
	}
	// WebP output drops EXIF, so a phone photo must be turned upright first.
	if ( 'image/jpeg' === $upload['type'] ) {
		$editor->maybe_exif_rotate();
	}
	$editor->set_quality( (int) bst_feature_get( 'webp', 'quality', 82 ) );
	$dir   = dirname( $upload['file'] );
	$name  = wp_unique_filename( $dir, pathinfo( $upload['file'], PATHINFO_FILENAME ) . '.webp' );
	$saved = $editor->save( $dir . '/' . $name, 'image/webp' );
	if ( is_wp_error( $saved ) || empty( $saved['path'] ) || ! file_exists( $saved['path'] ) ) {
		return $upload;
	}
	if ( filesize( $saved['path'] ) >= filesize( $upload['file'] ) ) {
		wp_delete_file( $saved['path'] );
		return $upload;
	}
	wp_delete_file( $upload['file'] );
	$upload['file'] = $saved['path'];
	$upload['url']  = trailingslashit( dirname( $upload['url'] ) ) . wp_basename( $saved['path'] );
	$upload['type'] = 'image/webp';
	return $upload;
}
add_filter( 'wp_handle_upload', 'bst_webp_convert' );

add_filter(
	'bst_feature_fields',
	function ( $fields ) {
		$fields['webp'] = array(
			'quality' => array(
				'type'    => 'number',
				'label'   => __( 'WebP quality', 'bigspy-ai-theme-wp' ),
				'default' => 82,
				'min'     => 40,
				'max'     => 100,
				'help'    => bst_webp_supported() ? __( 'Files are kept as they are if WebP would not be smaller.', 'bigspy-ai-theme-wp' ) : __( 'This server cannot create WebP images, so uploads stay unchanged.', 'bigspy-ai-theme-wp' ),
			),
		);
		return $fields;
	}
);
