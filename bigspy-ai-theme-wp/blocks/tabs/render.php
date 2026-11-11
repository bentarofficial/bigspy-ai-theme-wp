<?php
/**
 * Tabs block. Tab titles come from the inner Tab blocks.
 *
 * Options added in G-133-a are all off by default: old content renders the same markup.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a      = (array) $attributes;
$style  = bst_choice( $a['style'] ?? 'underline', array( 'underline', 'pills', 'boxed', 'vertical', 'underline-thick', 'line-top', 'folder', 'segmented', 'outline', 'cards', 'icon-top' ), 'underline' );
$align  = bst_choice( $a['align'] ?? 'left', array( 'left', 'center', 'right', 'justify' ), 'left' );
$titles = array();

foreach ( (array) ( $block->parsed_block['innerBlocks'] ?? array() ) as $inner ) {
	if ( 'bst/tab' === ( $inner['blockName'] ?? '' ) ) {
		$ia       = (array) ( $inner['attrs'] ?? array() );
		$titles[] = array(
			isset( $ia['title'] ) ? (string) $ia['title'] : __( 'Tab', 'bigspy-ai-theme-wp' ),
			sanitize_key( $ia['icon'] ?? '' ),
			array(
				'subtitle' => (string) ( $ia['subtitle'] ?? '' ),
				'badge'    => (string) ( $ia['badge'] ?? '' ),
				'badge_c'  => bst_block_color( $ia, 'badgeColor' ),
				'image'    => ! empty( $ia['imageId'] ) ? (int) $ia['imageId'] : 0,
				'image_u'  => (string) ( $ia['imageUrl'] ?? '' ),
				'color'    => bst_block_color( $ia, 'color' ),
				'disabled' => ! empty( $ia['disabled'] ),
			),
		);
	}
}

$nav_pos  = bst_choice( $a['navPosition'] ?? '', array( '', 'top', 'bottom', 'left', 'right' ), '' );
$mobile   = bst_choice( $a['mobile'] ?? '', array( '', 'scroll', 'accordion', 'dropdown', 'stack' ), '' );
$trans    = bst_choice( $a['transition'] ?? '', array( '', 'slide', 'zoom', 'none' ), '' );
$icon_pos = bst_choice( $a['iconPosition'] ?? '', array( '', 'left', 'right', 'top' ), '' );

/* First tab open: defaultTab (1-based), skipping disabled tabs. */
$active = max( 0, min( count( $titles ) - 1, (int) bst_block_num( $a, 'defaultTab', 1, 1, 50 ) - 1 ) );
if ( $titles && $titles[ $active ][2]['disabled'] ) {
	foreach ( $titles as $i => $t ) {
		if ( ! $t[2]['disabled'] ) {
			$active = $i;
			break;
		}
	}
}

$px  = function ( $key, $min, $max, $unset = -1 ) use ( $a ) {
	if ( ! isset( $a[ $key ] ) || ! is_numeric( $a[ $key ] ) || (float) $a[ $key ] === (float) $unset ) {
		return '';
	}
	return bst_clamp( $a[ $key ], $min, $max, 0 ) . 'px';
};
$pos = function ( $key, $max ) use ( $a ) {
	return ! empty( $a[ $key ] ) && is_numeric( $a[ $key ] ) && $a[ $key ] > 0 ? bst_clamp( $a[ $key ], 0, $max, 0 ) . 'px' : '';
};

$tab_class = "bs-tabs bs-tabs--$style bs-tabs--align-$align" . ( ! empty( $a['stretch'] ) ? ' bs-tabs--stretch' : '' ) . ( ( '' === $trans && ( ! isset( $a['fade'] ) || ! empty( $a['fade'] ) ) ) ? ' bs-tabs--fade' : '' );
$tab_class .= ( $nav_pos ? " bs-tabs--nav-$nav_pos" : '' ) . ( $mobile ? " bs-tabs--m-$mobile" : '' ) . ( $trans ? " bs-tabs--tr-$trans" : '' );
$tab_class .= ( $icon_pos && 'left' !== $icon_pos ? " bs-tabs--icon-$icon_pos" : '' ) . ( ! empty( $a['iconOnlyMobile'] ) ? ' bs-tabs--icon-only-m' : '' );
$tab_class .= ( ! empty( $a['sticky'] ) ? ' bs-tabs--sticky' : '' ) . ( ! empty( $a['equalHeight'] ) ? ' bs-tabs--equal' : '' );

$tab_vars = bst_style_vars(
	array(
		'--bs-tabs-size'        => ! empty( $a['navSize'] ) ? bst_block_num( $a, 'navSize', 15, 11, 30 ) . 'px' : '',
		'--bs-tabs-active'      => bst_block_color( $a, 'activeColor' ),
		'--bs-tabs-active-text' => bst_block_color( $a, 'activeText' ),
		'--bs-tabs-fst'         => $pos( 'navSizeTablet', 60 ),
		'--bs-tabs-fsm'         => $pos( 'navSizeMobile', 60 ),
		'--bs-tabs-fw'          => bst_choice( (string) ( $a['navWeight'] ?? '' ), array( '', '300', '400', '500', '600', '700', '800', '900' ), '' ),
		'--bs-tabs-tt'          => bst_choice( (string) ( $a['navTransform'] ?? '' ), array( '', 'none', 'uppercase', 'lowercase', 'capitalize' ), '' ),
		'--bs-tabs-ls'          => ! empty( $a['navLetterSpacing'] ) && is_numeric( $a['navLetterSpacing'] ) ? bst_clamp( $a['navLetterSpacing'], -5, 20, 0 ) . 'px' : '',
		'--bs-tabs-side'        => $pos( 'sideWidth', 600 ),
		'--bs-tabs-stop'        => ! empty( $a['sticky'] ) ? ( $pos( 'stickyTop', 400 ) ? $pos( 'stickyTop', 400 ) : '' ) : '',
		'--bs-tabs-ms'          => $pos( 'transitionMs', 3000 ) ? (int) $a['transitionMs'] . 'ms' : '',
		'--bs-tabs-gap'         => $px( 'navGap', 0, 100 ),
		'--bs-tabs-padx'        => $px( 'navPadX', 0, 100 ),
		'--bs-tabs-pady'        => $px( 'navPadY', 0, 60 ),
		'--bs-tabs-rad'         => $px( 'navRadius', 0, 999 ),
		'--bs-tabs-navbg'       => bst_block_color( $a, 'navBg' ),
		'--bs-tabs-navbc'       => bst_block_color( $a, 'navBorderColor' ),
		'--bs-tabs-navbw'       => $px( 'navBorderWidth', 0, 20 ),
		'--bs-tabs-tcol'        => bst_block_color( $a, 'tabColor' ),
		'--bs-tabs-hcol'        => bst_block_color( $a, 'tabColorHover' ),
		'--bs-tabs-tbg'         => bst_block_color( $a, 'tabBg' ),
		'--bs-tabs-hbg'         => bst_block_color( $a, 'tabBgHover' ),
		'--bs-tabs-abg'         => bst_block_color( $a, 'tabBgActive' ),
		'--bs-tabs-indc'        => bst_block_color( $a, 'indicatorColor' ),
		'--bs-tabs-indw'        => $pos( 'indicatorSize', 20 ),
		'--bs-tabs-isz'         => $pos( 'iconSize', 120 ),
		'--bs-tabs-pbg'         => bst_block_color( $a, 'panelBg' ),
		'--bs-tabs-pbc'         => bst_block_color( $a, 'panelBorderColor' ),
		'--bs-tabs-pbw'         => $px( 'panelBorderWidth', 0, 20 ),
		'--bs-tabs-prad'        => $px( 'panelRadius', 0, 200 ),
		'--bs-tabs-ppad'        => $px( 'panelPadding', 0, 200 ),
		'--bs-tabs-mpad'        => $px( 'panelPaddingMobile', 0, 200 ),
		'--bs-tabs-pminh'       => $pos( 'panelMinHeight', 2000 ),
	)
);

$data = '';
foreach (
	array(
		'default'  => $active ? $active : '',
		'deeplink' => ! empty( $a['deepLink'] ) ? '1' : '',
		'remember' => ! empty( $a['remember'] ) ? '1' : '',
		'autoplay' => ! empty( $a['autoplay'] ) && is_numeric( $a['autoplay'] ) && $a['autoplay'] > 0 ? (int) bst_clamp( $a['autoplay'], 1, 60, 5 ) : '',
		'pause'    => ! empty( $a['autoplay'] ) && isset( $a['pauseHover'] ) && ! $a['pauseHover'] ? '0' : '',
	) as $key => $value
) {
	if ( '' !== $value ) {
		$data .= ' data-' . $key . '="' . esc_attr( (string) $value ) . '"';
	}
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => $tab_class, 'style' => $tab_vars ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-event="<?php echo 'hover' === ( $a['event'] ?? 'click' ) ? 'hover' : 'click'; ?>"<?php echo $data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( 'dropdown' === $mobile && $titles ) : ?>
	<select class="bs-tabs__select" aria-label="<?php esc_attr_e( 'Choose a tab', 'bigspy-ai-theme-wp' ); ?>">
		<?php foreach ( $titles as $i => $title ) : ?>
		<option value="<?php echo (int) $i; ?>"<?php selected( $i, $active ); ?><?php disabled( $title[2]['disabled'] ); ?>><?php echo esc_html( wp_strip_all_tags( $title[0] ) ); ?></option>
		<?php endforeach; ?>
	</select>
	<?php endif; ?>
	<div class="bs-tabs__nav" role="tablist">
		<?php
		foreach ( $titles as $i => $title ) :
			$x     = $title[2];
			$on    = $i === $active;
			$media = '';
			if ( $x['image'] ) {
				$media = wp_get_attachment_image( $x['image'], 'thumbnail', false, array( 'class' => 'bs-tabs__img', 'alt' => '', 'loading' => 'lazy' ) );
			}
			if ( ! $media && $x['image_u'] ) {
				$media = '<img class="bs-tabs__img" src="' . esc_url( $x['image_u'] ) . '" alt="" loading="lazy">';
			}
			if ( ! $media && $title[1] ) {
				$media = bst_icon( $title[1], 18 );
			}
			$extra = ( $x['color'] ? ' style="--bs-tab-c:' . esc_attr( $x['color'] ) . '"' : '' ) . ( $x['disabled'] ? ' disabled aria-disabled="true"' : '' );
			?>
			<button type="button" class="bs-tabs__tab<?php echo $on ? ' is-active' : ''; ?>" role="tab" aria-selected="<?php echo $on ? 'true' : 'false'; ?>"<?php echo $extra; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo wp_kses_post( $title[0] ); ?></span><?php echo '' !== $x['subtitle'] ? '<small class="bs-tabs__sub">' . esc_html( $x['subtitle'] ) . '</small>' : ''; ?><?php echo '' !== $x['badge'] ? '<span class="bs-tabs__badge"' . ( $x['badge_c'] ? ' style="background:' . esc_attr( $x['badge_c'] ) . '"' : '' ) . '>' . esc_html( $x['badge'] ) . '</span>' : ''; ?></button>
		<?php endforeach; ?>
	</div>
	<div class="bs-tabs__panels"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
</div>
