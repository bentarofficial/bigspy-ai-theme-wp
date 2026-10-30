<?php
/**
 * Block manager: hide BIGSPY blocks you never use from the inserter
 * (Customizer → Advanced → BIGSPY blocks). Existing content keeps rendering.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_customizer_sections',
	function ( $sections ) {
		$sections['bst_block_manager'] = array(
			'title'       => __( 'BIGSPY AI blocks (show / hide)', 'bigspy-ai-theme-wp' ),
			'panel'       => 'bst_advanced',
			'priority'    => 4,
			'description' => __( 'Tick a block to hide it from the block inserter so the list stays short. Pages that already use it keep working.', 'bigspy-ai-theme-wp' ),
		);
		return $sections;
	}
);

/** name => title of every BIGSPY block (from block.json). */
function bst_block_titles() {
	static $titles = null;
	if ( null === $titles ) {
		$titles = array();
		foreach ( bst_block_names() as $name ) {
			$file = BST_DIR . '/blocks/' . $name . '/block.json';
			$json = is_readable( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : null; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			$titles[ $name ] = is_array( $json ) && ! empty( $json['title'] ) ? $json['title'] : $name;
		}
	}
	return $titles;
}

add_filter(
	'bst_defaults',
	function ( $d ) {
		foreach ( bst_block_names() as $name ) {
			$d += array( 'block_off_' . str_replace( '-', '_', $name ) => false );
		}
		return $d;
	}
);

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$i = 1;
		foreach ( bst_block_titles() as $name => $title ) {
			if ( in_array( $name, array( 'column', 'tab', 'accordion-item', 'button', 'ticker-slide', 'expand-panel' ), true ) ) {
				continue; // Children are managed with their parent block.
			}
			$fields[] = array(
				'id'       => 'block_off_' . str_replace( '-', '_', $name ),
				'section'  => 'bst_block_manager',
				'type'     => 'checkbox',
				/* translators: %s: block title */
				'label'    => sprintf( __( 'Hide "%s"', 'bigspy-ai-theme-wp' ), $title ),
				'priority' => $i++,
			);
		}
		return $fields;
	}
);

/** Block names (bst/…) hidden from the inserter. */
function bst_disabled_blocks() {
	$off = array();
	foreach ( bst_block_names() as $name ) {
		if ( bst_mod( 'block_off_' . str_replace( '-', '_', $name ) ) ) {
			$off[] = ( in_array( $name, array( 'ticker', 'ticker-slide', 'expand-gallery', 'expand-panel' ), true ) ? 'bigspy/' : 'bst/' ) . $name;
		}
	}
	return $off;
}
