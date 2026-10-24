<?php
/**
 * Reusable layout recipes shared by block controls and the Customizer library.
 * No site-specific selectors or executable user CSS are stored in these options.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** id => array( block name, label, global root selector, root-relative CSS ). */
function bst_layout_style_presets() {
	return array(
		'layout-section-readable' => array(
			'bst/section', __( 'Readable container with side gutters', 'bigspy-ai-theme-wp' ), '.bs-content .bs-section',
			'&>.bs-section__inner{width:100%;max-width:1160px;margin-inline:auto;padding-inline:24px;box-sizing:border-box}@media(max-width:549px){&>.bs-section__inner{padding-inline:20px}}',
		),
		'layout-row-balanced' => array(
			'bst/row', __( 'Balanced row spacing', 'bigspy-ai-theme-wp' ), '.bs-content .bs-row',
			'&{row-gap:28px}@media(max-width:549px){&{row-gap:22px}}',
		),
		'layout-column-icon-stack' => array(
			'bst/column', __( 'Stack icon boxes without stretching', 'bigspy-ai-theme-wp' ), '.bs-content .bs-column',
			'&>.bs-icon-box{height:auto;margin-bottom:24px}&>.bs-icon-box:only-child{height:100%;margin-bottom:0}',
		),
		'layout-icon-balanced' => array(
			'bst/icon-box', __( 'Balanced service card', 'bigspy-ai-theme-wp' ), '.bs-content .bs-icon-box',
			'& .bs-icon-box__title{font-size:20px;line-height:1.35}& .bs-icon-box__text{font-size:15px;line-height:1.7;color:var(--bs-text)}&.bs-icon-box--box-card{padding:28px;border:1px solid var(--bs-border);border-radius:16px;box-shadow:none}@media(max-width:549px){&.bs-icon-box--box-card{padding:24px}}',
		),
		'layout-banner-readable' => array(
			'bst/banner', __( 'Light hero with readable copy', 'bigspy-ai-theme-wp' ), '.bs-content .bs-banner',
			'& .bs-banner__content{padding-inline:max(6vw,24px)}& .bs-banner__bg::after{content:"";position:absolute;inset:0;pointer-events:none;background:linear-gradient(90deg,color-mix(in srgb,var(--bs-light,#eef5fb) 94%,transparent),color-mix(in srgb,var(--bs-light,#eef5fb) 85%,transparent) 35%,transparent 70%)}& .bs-heading{margin:14px 0 22px}& .bs-text{margin-bottom:20px}@media(max-width:849px){& .bs-banner__box{width:65%}}@media(max-width:549px){& .bs-banner__box{width:100%}& .bs-banner__bg::after{background:color-mix(in srgb,var(--bs-light,#eef5fb) 94%,transparent)}& .bs-banner__img{object-position:75% center}}',
		),
		'layout-heading-linebreaks' => array(
			'bst/heading', __( 'Preserve intentional line breaks', 'bigspy-ai-theme-wp' ), '.bs-content .bs-heading',
			'&{white-space:pre-line}',
		),
		'layout-buttons-breathing' => array(
			'bst/buttons', __( 'Space above action buttons', 'bigspy-ai-theme-wp' ), '.bs-content .bs-buttons',
			'&{margin-top:24px}',
		),
		'layout-price-compact' => array(
			'bst/price-table', __( 'Compact readable price card', 'bigspy-ai-theme-wp' ), '.bs-content .bs-price',
			'&{padding:26px;max-width:480px;width:100%;margin-inline:auto;border:1px solid var(--bs-border);border-radius:16px;box-sizing:border-box}& .bs-price__title{font-size:22px;line-height:1.3;margin:0 0 10px}& .bs-price__desc{font-size:15px;line-height:1.6;color:var(--bs-text);margin:0 0 16px}& .bs-price__amount{margin:12px 0 18px;line-height:1.2}& .bs-price__value{font-size:36px;font-weight:700;line-height:1.2}& .bs-price__period{font-size:14px;line-height:1.5;margin-left:8px}& .bs-price__features{gap:0;margin:0 0 20px;padding:0}& .bs-price__features li{font-size:15px;line-height:1.6;padding:5px 0;margin:0;gap:9px;color:var(--bs-text)}& .bs-price__features li span{font-size:15px;line-height:1.6}& .bs-price__features svg{width:15px;height:15px;flex-shrink:0}& .bs-price__btn{font-size:14px;line-height:1.4;min-height:44px;padding:11px 16px;width:100%;box-sizing:border-box}@media(max-width:549px){&{padding:22px;max-width:100%}& .bs-price__value{font-size:32px}& .bs-price__title{font-size:21px}}',
		),
	);
}

/** Add each recipe only to its corresponding block's Style presets panel. */
function bst_add_layout_block_presets( $catalog ) {
	foreach ( bst_layout_style_presets() as $id => $recipe ) {
		$block = $recipe[0];
		if ( ! isset( $catalog[ $block ]['layout-tools'] ) ) {
			$catalog[ $block ]['layout-tools'] = array( __( 'Balanced layout', 'bigspy-ai-theme-wp' ), false, array() );
		}
		// Repeat the local class for specificity over opt-in global library styles.
		$catalog[ $block ]['layout-tools'][2][ $id ] = array( $recipe[1], str_replace( '&', '&&', $recipe[3] ) );
	}
	return $catalog;
}

/** The same recipes, explicitly opt-in across matching blocks on the site. */
function bst_layout_customizer_presets() {
	$recipes = array();
	foreach ( bst_layout_style_presets() as $id => $recipe ) {
		$recipes[ $id ] = array(
			'layout-tools', $recipe[1], str_replace( '&', $recipe[2], $recipe[3] ), '',
			__( 'Site-wide for matching BIGSPY blocks. Use Style presets in a block to apply only there. Uncheck to remove.', 'bigspy-ai-theme-wp' ),
		);
	}
	return $recipes;
}
