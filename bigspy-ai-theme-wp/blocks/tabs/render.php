<?php
/**
 * Tabs block. Tab titles come from the inner Tab blocks.
 *
 * Layout: an optional heading on the left, the tab titles on the right (wrapping
 * or scrolling with arrows), an optional "View all" link, then the panels.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a        = (array) $attributes;
$style    = bst_choice( $a['style'] ?? 'underline', array( 'underline', 'pills', 'pills-outline', 'boxed', 'text', 'vertical' ), 'underline' );
$align    = bst_choice( $a['align'] ?? 'left', array( 'left', 'center', 'right' ), 'left' );
$overflow = 'vertical' === $style ? 'wrap' : bst_choice( $a['overflow'] ?? 'wrap', array( 'wrap', 'scroll', 'scroll-mobile' ), 'wrap' );
$arrows   = bst_choice( $a['arrows'] ?? 'toggle', array( 'toggle', 'both' ), 'toggle' );
$arrow_st = bst_choice( $a['arrowStyle'] ?? 'chevron', array( 'chevron', 'arrow', 'circle', 'square' ), 'chevron' );
$mobile   = bst_choice( $a['mobile'] ?? 'tabs', array( 'tabs', 'accordion' ), 'tabs' );
$h_tag    = bst_choice( $a['headingTag'] ?? 'h2', array( 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div' ), 'h2' );
$heading  = trim( (string) ( $a['heading'] ?? '' ) );
$titles   = array();

foreach ( (array) ( $block->parsed_block['innerBlocks'] ?? array() ) as $inner ) {
	if ( 'bst/tab' === ( $inner['blockName'] ?? '' ) ) {
		$titles[] = array( isset( $inner['attrs']['title'] ) ? (string) $inner['attrs']['title'] : __( 'Tab', 'bigspy-ai-theme-wp' ), sanitize_key( $inner['attrs']['icon'] ?? '' ) );
	}
}

$count   = count( $titles );
$default = $count ? max( 0, min( $count - 1, (int) ( $a['defaultTab'] ?? 1 ) - 1 ) ) : 0;

/* Images in hidden tabs load only when needed. */
if ( class_exists( 'WP_HTML_Tag_Processor' ) && false !== stripos( (string) $content, '<img' ) ) {
	$html = new WP_HTML_Tag_Processor( (string) $content );
	while ( $html->next_tag( 'img' ) ) {
		if ( null === $html->get_attribute( 'loading' ) ) {
			$html->set_attribute( 'loading', 'lazy' );
		}
	}
	$content = $html->get_updated_html();
}

$tab_class = "bs-tabs bs-tabs--$style bs-tabs--align-$align bs-tabs--overflow-$overflow bs-tabs--arrows-$arrows bs-tabs--arrow-$arrow_st"
	. ( ! empty( $a['stretch'] ) ? ' bs-tabs--stretch' : '' )
	. ( ( ! isset( $a['fade'] ) || ! empty( $a['fade'] ) ) ? ' bs-tabs--fade' : '' )
	. ( 'accordion' === $mobile ? ' bs-tabs--m-accordion' : '' )
	. ( '' !== $heading ? ' has-heading' : '' );
$tab_vars  = bst_style_vars(
	array(
		'--bs-tabs-size'          => ! empty( $a['navSize'] ) ? bst_block_num( $a, 'navSize', 15, 11, 30 ) . 'px' : '',
		'--bs-tabs-active'        => bst_block_color( $a, 'activeColor' ),
		'--bs-tabs-active-text'   => bst_block_color( $a, 'activeText' ),
		'--bs-tabs-radius'        => isset( $a['radius'] ) && $a['radius'] >= 0 ? bst_block_num( $a, 'radius', 0, 0, 60 ) . 'px' : '',
		'--bs-tabs-gap'           => isset( $a['gap'] ) && $a['gap'] >= 0 ? bst_block_num( $a, 'gap', 4, 0, 60 ) . 'px' : '',
		'--bs-tabs-heading-size'  => ! empty( $a['headingSize'] ) ? bst_block_num( $a, 'headingSize', 24, 12, 80 ) . 'px' : '',
		'--bs-tabs-heading-color' => bst_block_color( $a, 'headingColor' ),
	)
);
$arrow_icon = bst_icon( 'arrow' === $arrow_st ? 'arrow-right' : 'chevron-right', 18 );
$more_text  = trim( (string) ( $a['linkText'] ?? '' ) );
$more_url   = (string) ( $a['linkUrl'] ?? '' );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => $tab_class, 'style' => $tab_vars ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-event="<?php echo 'hover' === ( $a['event'] ?? 'click' ) ? 'hover' : 'click'; ?>" data-default="<?php echo (int) $default; ?>"<?php echo ! empty( $a['hashLink'] ) ? ' data-hash="1"' : ''; ?> data-more="<?php echo esc_url( $more_url ); ?>">
	<div class="bs-tabs__head">
		<?php if ( '' !== $heading ) : ?>
			<<?php echo esc_html( $h_tag ); ?> class="bs-tabs__heading"><?php echo wp_kses_post( $heading ); ?></<?php echo esc_html( $h_tag ); ?>>
		<?php endif; ?>
		<div class="bs-tabs__bar">
			<?php if ( 'both' === $arrows && 'wrap' !== $overflow ) : ?>
				<button type="button" class="bs-tabs__arrow bs-tabs__arrow--prev" hidden aria-label="<?php esc_attr_e( 'Previous tabs', 'bigspy-ai-theme-wp' ); ?>"><?php echo $arrow_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
			<?php endif; ?>
			<div class="bs-tabs__nav" role="tablist"<?php echo '' !== $heading ? ' aria-label="' . esc_attr( wp_strip_all_tags( $heading ) ) . '"' : ''; ?>>
				<?php foreach ( $titles as $i => $title ) : ?>
					<button type="button" class="bs-tabs__tab<?php echo $default === $i ? ' is-active' : ''; ?>" role="tab" aria-selected="<?php echo $default === $i ? 'true' : 'false'; ?>"><?php echo $title[1] ? bst_icon( $title[1], 18 ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo wp_kses_post( $title[0] ); ?></span></button>
				<?php endforeach; ?>
			</div>
			<?php if ( 'wrap' !== $overflow ) : ?>
				<button type="button" class="bs-tabs__arrow bs-tabs__arrow--next" hidden aria-label="<?php esc_attr_e( 'Next tabs', 'bigspy-ai-theme-wp' ); ?>" data-label-back="<?php esc_attr_e( 'Previous tabs', 'bigspy-ai-theme-wp' ); ?>"><?php echo $arrow_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
			<?php endif; ?>
		</div>
		<?php if ( '' !== $more_text ) : ?>
			<a class="bs-tabs__more" href="<?php echo esc_url( $more_url ? $more_url : '#' ); ?>"<?php echo $more_url ? '' : ' hidden'; ?>><span><?php echo esc_html( $more_text ); ?></span><?php echo bst_icon( 'arrow-right', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
		<?php endif; ?>
	</div>
	<div class="bs-tabs__panels"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
</div>
