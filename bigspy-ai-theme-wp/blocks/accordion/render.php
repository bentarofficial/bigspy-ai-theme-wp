<?php
/**
 * Accordion block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a     = (array) $attributes;
$style = bst_choice( $a['style'] ?? 'bordered', array( 'bordered', 'separated', 'minimal', 'shadow', 'solid', 'underline', 'leftbar', 'bignum' ), 'bordered' );
// Which items start open: as set on each item (default), the first, all or none.
$open_mode = bst_choice( $a['openMode'] ?? '', array( '', 'first', 'all', 'none' ), '' );
if ( '' !== $open_mode ) {
	$acc_n   = 0;
	$content = preg_replace_callback(
		'/<details class="bs-accordion__item"( open)?/',
		function () use ( $open_mode, &$acc_n ) {
			++$acc_n;
			$open = 'all' === $open_mode || ( 'first' === $open_mode && 1 === $acc_n );
			return '<details class="bs-accordion__item"' . ( $open ? ' open' : '' );
		},
		(string) $content
	);
}
if ( ! empty( $a['faqSchema'] ) && preg_match_all( '#<summary class="bs-accordion__title">.*?<span class="bs-accordion__text">(.*?)</span>.*?<div class="bs-accordion__content">(.*?)</div>\s*</details>#s', (string) $content, $m, PREG_SET_ORDER ) ) {
	$faq = array();
	foreach ( $m as $item ) {
		$q      = trim( wp_strip_all_tags( $item[1] ) );
		$answer = trim( wp_kses( $item[2], array( 'a' => array( 'href' => true ), 'br' => array(), 'p' => array(), 'strong' => array(), 'b' => array(), 'em' => array(), 'i' => array(), 'ul' => array(), 'ol' => array(), 'li' => array() ) ) );
		if ( '' !== $q && '' !== $answer ) {
			$faq[] = array( '@type' => 'Question', 'name' => $q, 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $answer ) );
		}
	}
	if ( $faq ) {
		echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $faq ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG ) . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
?>
<?php
$acc_class = 'bs-accordion bs-accordion--' . $style
	. ' bs-accordion--icon-' . bst_choice( $a['iconStyle'] ?? 'chevron', array( 'chevron', 'plus', 'circle', 'arrow', 'none' ), 'chevron' )
	. ' bs-accordion--icon-' . bst_choice( $a['iconPosition'] ?? 'right', array( 'right', 'left' ), 'right' )
	. ( ! empty( $a['numbered'] ) ? ' bs-accordion--numbered' : '' )
	. ( 2 === (int) ( $a['columns'] ?? 1 ) ? ' bs-accordion--cols-2' : '' );
$acc_vars = bst_style_vars(
	array(
		'--bs-acc-title-size' => ! empty( $a['titleSize'] ) ? bst_block_num( $a, 'titleSize', 17, 12, 40 ) . 'px' : '',
		'--bs-acc-title'      => bst_block_color( $a, 'titleColor' ),
		'--bs-acc-title-bg'   => bst_block_color( $a, 'titleBg' ),
		'--bs-acc-active'     => bst_block_color( $a, 'activeColor' ),
		'--bs-acc-active-bg'  => bst_block_color( $a, 'activeBg' ),
		'--bs-acc-content-bg' => bst_block_color( $a, 'contentBg' ),
		'--bs-acc-radius'     => isset( $a['radius'] ) && $a['radius'] >= 0 ? bst_block_num( $a, 'radius', 8, 0, 40 ) . 'px' : '',
		'--bs-acc-gap'        => isset( $a['gap'] ) && $a['gap'] >= 0 ? bst_block_num( $a, 'gap', 10, 0, 40 ) . 'px' : '',
		'--bs-acc-icon-c'     => bst_block_color( $a, 'iconColor' ),
		'--bs-acc-icon-size'  => ! empty( $a['iconSize'] ) ? bst_block_num( $a, 'iconSize', 22, 12, 48 ) . 'px' : '',
		'--bs-acc-title-fw'   => ! empty( $a['titleWeight'] ) ? (string) bst_choice( (string) $a['titleWeight'], array( '400', '500', '600', '700', '800' ), '600' ) : '',
		'--bs-acc-title-pad'  => isset( $a['titlePad'] ) && $a['titlePad'] >= 0 ? bst_block_num( $a, 'titlePad', 16, 0, 48 ) . 'px' : '',
		'--bs-acc-title-hover' => bst_block_color( $a, 'titleHover' ),
		'--bs-acc-border-c'   => bst_block_color( $a, 'borderColor' ),
		'--bs-acc-border-w'   => isset( $a['borderWidth'] ) && $a['borderWidth'] >= 0 ? bst_block_num( $a, 'borderWidth', 1, 0, 8 ) . 'px' : '',
		'--bs-acc-content-c'  => bst_block_color( $a, 'contentColor' ),
		'--bs-acc-content-fs' => ! empty( $a['contentSize'] ) ? bst_block_num( $a, 'contentSize', 16, 12, 28 ) . 'px' : '',
	)
);
?>
<?php
$acc_tools = '';
if ( ! empty( $a['search'] ) ) {
	$acc_tools .= '<input type="search" class="bs-accordion__search" placeholder="' . esc_attr__( 'Search questions…', 'bigspy-ai-theme-wp' ) . '" aria-label="' . esc_attr__( 'Search questions', 'bigspy-ai-theme-wp' ) . '">';
}
if ( ! empty( $a['expandAll'] ) ) {
	$acc_tools .= '<button type="button" class="bs-accordion__all" data-open="' . esc_attr__( 'Open all', 'bigspy-ai-theme-wp' ) . '" data-close="' . esc_attr__( 'Close all', 'bigspy-ai-theme-wp' ) . '">' . esc_html__( 'Open all', 'bigspy-ai-theme-wp' ) . '</button>';
}
$acc_tools = '' !== $acc_tools ? '<div class="bs-accordion__tools">' . $acc_tools . '</div>' : '';
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => $acc_class, 'style' => $acc_vars ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-single="<?php echo ! empty( $a['single'] ) ? '1' : '0'; ?>" data-smooth="<?php echo ( ! isset( $a['smooth'] ) || ! empty( $a['smooth'] ) ) ? '1' : '0'; ?>"<?php echo ! empty( $a['deepLink'] ) ? ' data-deeplink="1"' : ''; ?>><?php echo $acc_tools . $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
