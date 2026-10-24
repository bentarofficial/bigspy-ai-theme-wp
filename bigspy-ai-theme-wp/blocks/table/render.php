<?php
/**
 * Table block: one row per line, cells separated by |.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a    = (array) $attributes;
$rows = array();
foreach ( explode( "\n", str_replace( "\r", '', (string) ( $a['data'] ?? '' ) ) ) as $line ) {
	if ( '' !== trim( $line ) ) {
		$rows[] = array_map( 'trim', explode( '|', $line ) );
	}
}
if ( ! $rows ) {
	return;
}
$cell = function ( $text, $tag, $scope = '' ) {
	return '<' . $tag . ( $scope ? ' scope="' . $scope . '"' : '' ) . '>' . wp_kses_post( $text ) . '</' . $tag . '>';
};
$html = '';
if ( ! empty( $a['header'] ) ) {
	$head  = array_shift( $rows );
	$html .= '<thead><tr>' . implode( '', array_map( function ( $t ) use ( $cell ) { return $cell( $t, 'th', 'col' ); }, $head ) ) . '</tr></thead>';
}
$html .= '<tbody>';
foreach ( $rows as $row ) {
	$html .= '<tr>';
	foreach ( $row as $i => $t ) {
		$html .= ( 0 === $i && ! empty( $a['firstColumn'] ) ) ? $cell( $t, 'th', 'row' ) : $cell( $t, 'td' );
	}
	$html .= '</tr>';
}
$html .= '</tbody>';
$class = 'bs-table' . ( ! empty( $a['striped'] ) ? ' bs-table--striped' : '' ) . ( ! empty( $a['bordered'] ) ? ' bs-table--bordered' : '' ) . ' has-text-align-' . bst_choice( (string) ( $a['align'] ?? 'left' ), array( 'left', 'center', 'right' ), 'left' );
$cap   = trim( (string) ( $a['caption'] ?? '' ) );
printf(
	'<figure %1$s><div class="bs-table__scroll"><table>%2$s%3$s</table></div></figure>',
	get_block_wrapper_attributes( array( 'class' => $class, 'style' => bst_style_vars( array( '--bs-table-accent' => bst_block_color( $a, 'accent' ), '--bs-table-size' => bst_block_num( $a, 'size', 15, 11, 24 ) . 'px' ) ) ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$cap ? '<caption>' . esc_html( $cap ) . '</caption>' : '',
	$html // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
