<?php
/**
 * Tabs block. Tab titles come from the inner Tab blocks.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a      = (array) $attributes;
$style  = bst_choice( $a['style'] ?? 'underline', array( 'underline', 'pills', 'boxed', 'vertical' ), 'underline' );
$align  = bst_choice( $a['align'] ?? 'left', array( 'left', 'center' ), 'left' );
$titles = array();

foreach ( (array) ( $block->parsed_block['innerBlocks'] ?? array() ) as $inner ) {
	if ( 'bst/tab' === ( $inner['blockName'] ?? '' ) ) {
		$titles[] = array( isset( $inner['attrs']['title'] ) ? (string) $inner['attrs']['title'] : __( 'Tab', 'bigspy-ai-theme-wp' ), sanitize_key( $inner['attrs']['icon'] ?? '' ) );
	}
}
?>
<?php
$tab_class = "bs-tabs bs-tabs--$style bs-tabs--align-$align" . ( ! empty( $a['stretch'] ) ? ' bs-tabs--stretch' : '' ) . ( ( ! isset( $a['fade'] ) || ! empty( $a['fade'] ) ) ? ' bs-tabs--fade' : '' );
$tab_vars  = bst_style_vars(
	array(
		'--bs-tabs-size'   => ! empty( $a['navSize'] ) ? bst_block_num( $a, 'navSize', 15, 11, 30 ) . 'px' : '',
		'--bs-tabs-active' => bst_block_color( $a, 'activeColor' ),
		'--bs-tabs-active-text' => bst_block_color( $a, 'activeText' ),
	)
);
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => $tab_class, 'style' => $tab_vars ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-event="<?php echo 'hover' === ( $a['event'] ?? 'click' ) ? 'hover' : 'click'; ?>">
	<div class="bs-tabs__nav" role="tablist">
		<?php foreach ( $titles as $i => $title ) : ?>
			<button type="button" class="bs-tabs__tab<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tab" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"><?php echo $title[1] ? bst_icon( $title[1], 18 ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo wp_kses_post( $title[0] ); ?></span></button>
		<?php endforeach; ?>
	</div>
	<div class="bs-tabs__panels"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
</div>
