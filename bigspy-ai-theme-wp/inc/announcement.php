<?php
/**
 * Announcement bar above the header (Customizer → Header → Announcement bar):
 * sale / shipping notice with link, colors, countdown-free and dismissible.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_customizer_sections',
	function ( $sections ) {
		$sections['bst_announcement'] = array( 'title' => __( 'Announcement bar', 'bigspy-ai-theme-wp' ), 'panel' => 'bst_header', 'priority' => 0 );
		return $sections;
	}
);
add_filter(
	'bst_defaults',
	function ( $d ) {
		return $d + array(
			'ann_enable'   => false,
			'ann_text'     => __( 'Free delivery for orders over 300.000đ', 'bigspy-ai-theme-wp' ),
			'ann_link'     => '',
			'ann_link_text' => __( 'Shop now', 'bigspy-ai-theme-wp' ),
			'ann_style'    => 'gradient',
			'ann_bg'       => '',
			'ann_color'    => '#ffffff',
			'ann_dismiss'  => true,
			'ann_mobile'   => true,
			'ann_icon'     => 'gift',
		);
	}
);
add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$s = 'bst_announcement';
		$fields[] = array( 'id' => 'ann_enable', 'section' => $s, 'type' => 'checkbox', 'label' => __( 'Show the announcement bar', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'ann_text', 'section' => $s, 'type' => 'text', 'label' => __( 'Message', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'ann_link', 'section' => $s, 'type' => 'text', 'label' => __( 'Link (optional)', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'ann_link_text', 'section' => $s, 'type' => 'text', 'label' => __( 'Link text', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'ann_icon', 'section' => $s, 'type' => 'select', 'label' => __( 'Icon', 'bigspy-ai-theme-wp' ), 'choices' => array( '' => __( 'None', 'bigspy-ai-theme-wp' ), 'gift' => 'Gift', 'truck' => 'Truck', 'tag' => 'Tag', 'sparkles' => 'Sparkles', 'phone' => 'Phone', 'clock' => 'Clock', 'star' => 'Star' ) );
		$fields[] = array( 'id' => 'ann_style', 'section' => $s, 'type' => 'select', 'label' => __( 'Look', 'bigspy-ai-theme-wp' ), 'choices' => array( 'gradient' => __( 'Brand gradient', 'bigspy-ai-theme-wp' ), 'primary' => __( 'Primary color', 'bigspy-ai-theme-wp' ), 'secondary' => __( 'Secondary color', 'bigspy-ai-theme-wp' ), 'dark' => __( 'Dark', 'bigspy-ai-theme-wp' ), 'custom' => __( 'Custom color (below)', 'bigspy-ai-theme-wp' ) ) );
		$fields[] = array( 'id' => 'ann_bg', 'section' => $s, 'type' => 'color', 'label' => __( 'Custom background', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'ann_color', 'section' => $s, 'type' => 'color', 'label' => __( 'Text color', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'ann_dismiss', 'section' => $s, 'type' => 'checkbox', 'label' => __( 'Visitors can close it (remembered for 7 days)', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'ann_mobile', 'section' => $s, 'type' => 'checkbox', 'label' => __( 'Show on mobile', 'bigspy-ai-theme-wp' ) );
		return $fields;
	}
);

function bst_announcement_bar() {
	if ( ! bst_mod( 'ann_enable' ) || '' === trim( (string) bst_mod( 'ann_text' ) ) ) {
		return;
	}
	$style = bst_choice( bst_mod( 'ann_style' ), array( 'gradient', 'primary', 'secondary', 'dark', 'custom' ), 'gradient' );
	$vars  = bst_style_vars(
		array(
			'--bs-ann-bg'    => 'custom' === $style ? sanitize_hex_color( (string) bst_mod( 'ann_bg' ) ) : '',
			'--bs-ann-color' => sanitize_hex_color( (string) bst_mod( 'ann_color' ) ),
		)
	);
	$key   = 'bstAnn' . substr( md5( (string) bst_mod( 'ann_text' ) ), 0, 8 );
	$link  = trim( (string) bst_mod( 'ann_link' ) );
	$icon  = sanitize_key( (string) bst_mod( 'ann_icon' ) );
	echo '<div class="bs-ann bs-ann--' . esc_attr( $style ) . ( bst_mod( 'ann_mobile' ) ? '' : ' bs-hide-mobile' ) . '" style="' . esc_attr( $vars ) . '" data-bs-ann="' . esc_attr( $key ) . '" role="region" aria-label="' . esc_attr__( 'Announcement', 'bigspy-ai-theme-wp' ) . '">';
	echo '<div class="bs-ann__inner">' . ( $icon ? bst_icon( $icon, 16 ) : '' ) . '<span class="bs-ann__text">' . esc_html( bst_mod( 'ann_text' ) ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	if ( $link ) {
		echo '<a class="bs-ann__link" href="' . esc_url( $link ) . '">' . esc_html( bst_mod( 'ann_link_text' ) ) . bst_icon( 'arrow-right', 14 ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</div>';
	if ( bst_mod( 'ann_dismiss' ) ) {
		echo '<button type="button" class="bs-ann__close" aria-label="' . esc_attr__( 'Close', 'bigspy-ai-theme-wp' ) . '">' . bst_icon( 'close', 14 ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</div>';
	?>
	<script>
	(function () {
		var bar = document.querySelector('[data-bs-ann]');
		if (!bar) { return; }
		var key = bar.getAttribute('data-bs-ann');
		try { if (Number(localStorage.getItem(key)) > Date.now()) { bar.remove(); return; } } catch (e) {}
		var btn = bar.querySelector('.bs-ann__close');
		if (btn) {
			btn.addEventListener('click', function () {
				try { localStorage.setItem(key, String(Date.now() + 7 * 864e5)); } catch (e) {}
				bar.remove();
			});
		}
	})();
	</script>
	<?php
}
add_action( 'wp_body_open', 'bst_announcement_bar', 5 );
