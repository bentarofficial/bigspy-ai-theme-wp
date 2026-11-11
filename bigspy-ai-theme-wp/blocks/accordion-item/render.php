<?php
/**
 * Accordion item.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a   = (array) $attributes;
$tags = array( 'div', 'p', 'h2', 'h3', 'h4', 'h5', 'h6' );
$tag  = bst_choice( (string) ( $block->context['bst/accordionTitleTag'] ?? 'div' ), $tags, 'div' );
// Each item can override the tag chosen on the Accordion.
if ( ! empty( $a['titleTag'] ) ) {
	$tag = bst_choice( (string) $a['titleTag'], $tags, $tag );
}
$sub   = trim( (string) ( $a['subtitle'] ?? '' ) );
$badge = trim( (string) ( $a['badge'] ?? '' ) );
$extra = ( '' !== $sub ? '<span class="bs-accordion__sub">' . esc_html( $sub ) . '</span>' : '' );
$label = ( '' !== $badge ? '<span class="bs-accordion__badge">' . esc_html( $badge ) . '</span>' : '' );
// Deep link: the item gets an id from its title, so /page#question opens it.
$anchor = ! empty( $block->context['bst/accordionDeepLink'] ) ? sanitize_title( wp_strip_all_tags( (string) ( $a['title'] ?? '' ) ) ) : '';
?>
<details class="bs-accordion__item"<?php echo ! empty( $a['open'] ) ? ' open' : ''; ?><?php echo '' !== $anchor ? ' id="' . esc_attr( $anchor ) . '"' : ''; ?>>
	<summary class="bs-accordion__title"><<?php echo esc_html( $tag ); ?> class="bs-accordion__heading"><span class="bs-accordion__text"><?php echo wp_kses_post( $a['title'] ?? '' ); ?></span><?php echo $label . $extra; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?></<?php echo esc_html( $tag ); ?>><span class="bs-accordion__icon" aria-hidden="true"><?php echo bst_icon( 'chevron-down', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span></summary>
	<div class="bs-accordion__content"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
</details>
