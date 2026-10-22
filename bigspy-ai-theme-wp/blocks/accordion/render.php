<?php
/**
 * Accordion block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a     = (array) $attributes;
$style = bst_choice( $a['style'] ?? 'bordered', array( 'bordered', 'separated', 'minimal' ), 'bordered' );
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
	. ( ! empty( $a['numbered'] ) ? ' bs-accordion--numbered' : '' );
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
	)
);
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => $acc_class, 'style' => $acc_vars ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-single="<?php echo ! empty( $a['single'] ) ? '1' : '0'; ?>" data-smooth="<?php echo ( ! isset( $a['smooth'] ) || ! empty( $a['smooth'] ) ) ? '1' : '0'; ?>"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
