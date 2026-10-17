<?php
/**
 * Code / HTML block.
 *
 * "html" mode outputs the author's markup, like the core Custom HTML block:
 * WordPress already strips unsafe markup from block attributes on save for
 * users without the unfiltered_html capability. "snippet" mode displays the
 * code as text with a language label and a copy button.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a    = (array) $attributes;
$code = (string) ( $a['code'] ?? '' );
if ( '' === trim( $code ) ) {
	return;
}

$mode = bst_choice( $a['mode'] ?? 'html', array( 'html', 'snippet' ), 'html' );

if ( 'html' === $mode ) {
	$scope = wp_unique_id( 'bs-code-' );
	$css   = trim( (string) ( $a['css'] ?? '' ) );
	$style = '';
	// Page-level CSS only from authors trusted with unfiltered HTML (administrators / editors).
	$author = (int) get_post_field( 'post_author', get_the_ID() );
	if ( '' !== $css && $author && user_can( $author, 'unfiltered_html' ) ) {
		// "selector" targets this block only, e.g. `selector h2 { color: red; }`.
		$css   = str_replace( 'selector', '.' . $scope, $css );
		$css   = preg_replace( '#</?style[^>]*>#i', '', $css );
		$style = '<style>' . wp_strip_all_tags( $css ) . '</style>';
	}
	printf(
		'<div %1$s>%2$s%3$s</div>',
		get_block_wrapper_attributes( array( 'class' => 'bs-html ' . $scope ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$style, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		do_shortcode( $code ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- see file comment.
	);
	return;
}

$theme      = bst_choice( $a['theme'] ?? 'dark', array( 'dark', 'light' ), 'dark' );
$language   = trim( (string) ( $a['language'] ?? '' ) );
$max_height = bst_block_num( $a, 'maxHeight', 0, 0, 2000 );
$classes    = 'bs-code bs-code--' . $theme . ( ! empty( $a['wrap'] ) ? ' is-wrapped' : '' );
$style      = $max_height ? '--bs-code-max:' . $max_height . 'px' : '';
?>
<figure <?php echo get_block_wrapper_attributes( array( 'class' => $classes, 'style' => $style ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( '' !== $language || ! empty( $a['showCopy'] ) ) : ?>
		<figcaption class="bs-code__bar">
			<span class="bs-code__lang"><?php echo esc_html( $language ); ?></span>
			<?php if ( ! empty( $a['showCopy'] ) ) : ?>
				<button type="button" class="bs-code__copy" data-bs-code-copy data-label-done="<?php esc_attr_e( 'Copied!', 'bigspy-ai-theme-wp' ); ?>"><?php esc_html_e( 'Copy', 'bigspy-ai-theme-wp' ); ?></button>
			<?php endif; ?>
		</figcaption>
	<?php endif; ?>
	<pre class="bs-code__pre"><code><?php echo esc_html( $code ); ?></code></pre>
</figure>
