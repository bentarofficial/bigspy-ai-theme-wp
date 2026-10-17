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
		$titles[] = isset( $inner['attrs']['title'] ) ? (string) $inner['attrs']['title'] : __( 'Tab', 'bigspy-ai-theme-wp' );
	}
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => "bs-tabs bs-tabs--$style bs-tabs--align-$align" ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="bs-tabs__nav" role="tablist">
		<?php foreach ( $titles as $i => $title ) : ?>
			<button type="button" class="bs-tabs__tab<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tab" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"><?php echo wp_kses_post( $title ); ?></button>
		<?php endforeach; ?>
	</div>
	<div class="bs-tabs__panels"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
</div>
