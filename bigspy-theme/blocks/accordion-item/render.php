<?php
/**
 * Accordion item block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a = (array) $attributes;
?>
<details class="bs-accordion__item"<?php echo ! empty( $a['open'] ) ? ' open' : ''; ?>>
	<summary class="bs-accordion__title"><span><?php echo wp_kses_post( $a['title'] ?? '' ); ?></span><?php echo bst_icon( 'chevron-down', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></summary>
	<div class="bs-accordion__content"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
</details>
