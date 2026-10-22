<?php
/**
 * Accordion item.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a   = (array) $attributes;
$tag = bst_choice( (string) ( $block->context['bst/accordionTitleTag'] ?? 'div' ), array( 'div', 'h2', 'h3', 'h4' ), 'div' );
?>
<details class="bs-accordion__item"<?php echo ! empty( $a['open'] ) ? ' open' : ''; ?>>
	<summary class="bs-accordion__title"><<?php echo esc_html( $tag ); ?> class="bs-accordion__heading"><span class="bs-accordion__text"><?php echo wp_kses_post( $a['title'] ?? '' ); ?></span></<?php echo esc_html( $tag ); ?>><span class="bs-accordion__icon" aria-hidden="true"><?php echo bst_icon( 'chevron-down', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span></summary>
	<div class="bs-accordion__content"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
</details>
