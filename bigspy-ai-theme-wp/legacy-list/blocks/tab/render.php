<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$title = sanitize_text_field( $attributes['title'] ?? __( 'Tab title', 'bigspy-ai-theme-wp' ) );
echo '<section class="bigspy-tab-panel" data-bigspy-tab-title="'.esc_attr($title).'">'.$content.'</section>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped