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
$marks   = ! empty( $a['marks'] );
$hl      = max( 0, (int) ( $a['highlightCol'] ?? 0 ) );
$cards   = 'cards' === ( $a['mobile'] ?? 'scroll' ) && ! empty( $a['header'] );
$aligns  = array_map( 'trim', explode( ',', (string) ( $a['colAlign'] ?? '' ) ) );
$widths  = array_map( 'trim', explode( ',', (string) ( $a['colWidth'] ?? '' ) ) );
$labels  = array();
$colattr = function ( $i ) use ( $aligns, $hl ) {
	$cls = ( $hl && $hl === $i + 1 ) ? 'is-hl' : '';
	$al  = isset( $aligns[ $i ] ) ? bst_choice( $aligns[ $i ], array( '', 'left', 'center', 'right' ), '' ) : '';
	return ( $cls ? ' class="' . $cls . '"' : '' ) . ( $al ? ' style="text-align:' . $al . '"' : '' );
};
$value = function ( $text ) use ( $marks ) {
	$html = wp_kses_post( $text );
	if ( $marks ) {
		$key = strtolower( trim( wp_strip_all_tags( $text ) ) );
		if ( in_array( $key, array( 'yes', 'y', 'v', '[v]', '✓', '✔', 'có', 'co' ), true ) ) {
			$html = '<span class="bs-table__mark is-yes" role="img" aria-label="' . esc_attr__( 'Yes', 'bigspy-ai-theme-wp' ) . '">✓</span>';
		} elseif ( in_array( $key, array( 'no', 'n', 'x', '[x]', '✗', '✕', 'không', 'khong' ), true ) ) {
			$html = '<span class="bs-table__mark is-no" role="img" aria-label="' . esc_attr__( 'No', 'bigspy-ai-theme-wp' ) . '">✗</span>';
		}
	}
	return $html;
};
$advanced = $marks || $hl || $cards || array_filter( $aligns ) || array_filter( $widths );
$cell     = function ( $text, $tag, $scope = '', $i = -1 ) use ( $value, $colattr, $cards, $advanced, &$labels ) {
	$extra = ( $advanced && $i >= 0 ) ? $colattr( $i ) : '';
	if ( $cards && 'td' === $tag && isset( $labels[ $i ] ) && '' !== $labels[ $i ] ) {
		$extra .= ' data-label="' . esc_attr( $labels[ $i ] ) . '"';
	}
	return '<' . $tag . ( $scope ? ' scope="' . $scope . '"' : '' ) . $extra . '>' . $value( $text ) . '</' . $tag . '>';
};
$html = '';
if ( array_filter( $widths ) ) {
	$html .= '<colgroup>';
	foreach ( $widths as $w ) {
		$w     = preg_match( '/^\d+(\.\d+)?(px|%|em|rem)?$/', $w ) ? ( is_numeric( $w ) ? $w . 'px' : $w ) : '';
		$html .= '<col' . ( $w ? ' style="width:' . esc_attr( $w ) . '"' : '' ) . '>';
	}
	$html .= '</colgroup>';
}
if ( ! empty( $a['header'] ) ) {
	$head   = array_shift( $rows );
	$labels = array_map( 'wp_strip_all_tags', $head );
	$html  .= '<thead><tr>';
	foreach ( $head as $i => $t ) {
		$html .= $cell( $t, 'th', 'col', $i );
	}
	$html .= '</tr></thead>';
}
$html .= '<tbody>';
foreach ( $rows as $row ) {
	$html .= '<tr>';
	foreach ( $row as $i => $t ) {
		$html .= ( 0 === $i && ! empty( $a['firstColumn'] ) ) ? $cell( $t, 'th', 'row', $i ) : $cell( $t, 'td', '', $i );
	}
	$html .= '</tr>';
}
$html  .= '</tbody>';
$tstyle = bst_choice( (string) ( $a['tableStyle'] ?? '' ), array( '', 'minimal', 'card', 'compare' ), '' );
$class  = 'bs-table' . ( ! empty( $a['striped'] ) ? ' bs-table--striped' : '' ) . ( ! empty( $a['bordered'] ) ? ' bs-table--bordered' : '' ) . ' has-text-align-' . bst_choice( (string) ( $a['align'] ?? 'left' ), array( 'left', 'center', 'right' ), 'left' );
$class .= ( $tstyle ? ' bs-table--' . $tstyle : '' ) . ( ! empty( $a['stickyHead'] ) ? ' is-sticky-head' : '' ) . ( ! empty( $a['stickyCol'] ) ? ' is-sticky-col' : '' ) . ( $cards ? ' bs-table--m-cards' : '' ) . ( ! empty( $a['hoverRow'] ) ? ' has-hover' : '' );
$wrap   = array(
	'class' => $class,
	'style' => bst_style_vars(
		array(
			'--bs-table-accent' => bst_block_color( $a, 'accent' ),
			'--bs-table-size'   => bst_block_num( $a, 'size', 15, 11, 24 ) . 'px',
			'--bs-table-hbg'    => bst_block_color( $a, 'headBg' ),
			'--bs-table-hc'     => bst_block_color( $a, 'headColor' ),
			'--bs-table-pad'    => ! empty( $a['cellPad'] ) ? bst_block_num( $a, 'cellPad', 12, 2, 40 ) . 'px' : '',
			'--bs-table-radius' => isset( $a['radius'] ) && $a['radius'] >= 0 ? min( 40, (int) $a['radius'] ) . 'px' : '',
		)
	),
);
if ( ! empty( $a['sortable'] ) || ! empty( $a['search'] ) ) {
	$wrap['data-bs-table'] = trim( ( ! empty( $a['sortable'] ) ? 'sort ' : '' ) . ( ! empty( $a['search'] ) ? 'search' : '' ) );
	wp_enqueue_script( 'bst-table', BST_URI . '/assets/js/table.js', array(), bst_asset_version( 'assets/js/table.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_localize_script( 'bst-table', 'bstTableL10n', array( 'search' => __( 'Search the table', 'bigspy-ai-theme-wp' ), 'sort' => __( 'Sort by this column', 'bigspy-ai-theme-wp' ) ) );
}
$cap   = trim( (string) ( $a['caption'] ?? '' ) );
printf(
	'<figure %1$s><div class="bs-table__scroll"><table>%2$s%3$s</table></div></figure>',
	get_block_wrapper_attributes( $wrap ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$cap ? '<caption>' . esc_html( $cap ) . '</caption>' : '',
	$html // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
