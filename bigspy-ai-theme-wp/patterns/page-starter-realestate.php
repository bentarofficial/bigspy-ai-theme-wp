<?php
/**
 * Title: Homepage – Real estate
 * Slug: bigspy-ai-theme-wp/page-starter-realestate
 * Categories: bigspy-pages
 * Keywords: real estate, property, apartment, architecture, bat dong san
 * Block Types: core/post-content
 * Post Types: page
 * Viewport Width: 1280
 * Description: Real estate homepage with 15 sections: hero, highlights, about, listings, buying process, numbers, advantages, packages, gallery, consultants, reviews, FAQ, market news, consultation and contact.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Translated, JSON-safe text for block attributes (English source, translations in /languages). */
$bst_t = static function ( $text ) {
	return substr( wp_json_encode( __( $text, 'bigspy-ai-theme-wp' ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ), 1, -1 ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
};
?>
<!-- wp:bst/section {"bgImage":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-hero.svg' ) ); ?>","overlayColor":"#0b1830","overlayOpacity":62,"textScheme":"light","paddingDesktop":140,"paddingTablet":100,"paddingMobile":72,"minHeight":88,"minHeightUnit":"vh","align":"full"} -->
<!-- wp:bst/row {"gap":"medium","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":7,"spanTablet":12,"spanMobile":12} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Real estate · Apartments · Townhouses' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"color":"#ffffff"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Find the home that fits your life' ); ?>","tag":"h1","size":56,"sizeTablet":45,"sizeMobile":36,"bsPresets":"hd-tight2","bsAnimation":"fade-up"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Projects with clear legal status, well-connected locations and consultants who stay with you from choosing a home and negotiating to the mortgage and handover.' ); ?>\u003c/p\u003e","size":19,"bsAnimation":"fade-up","bsAnimationDelay":100} /-->
<!-- wp:bst/icon-list {"items":[{"icon":"check","text":"<?php echo $bst_t( 'Legal checks on every project' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Mortgage support up to 70%' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Free viewings' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Listed prices guaranteed' ); ?>","link":""}],"layout":"grid","columns":2,"iconColor":"#ffffff","iconBg":true,"gap":12} /-->
<!-- wp:bst/buttons {"stackMobile":true} -->
<!-- wp:bst/button {"text":"<?php echo $bst_t( 'Get advice' ); ?>","url":"#contact","radius":10} /-->
<!-- wp:bst/button {"text":"<?php echo $bst_t( 'See projects' ); ?>","url":"#services","radius":10,"style":"white"} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":5,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-left"} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-about.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Find the home that fits your life' ); ?>","ratio":"4-3","radius":22,"bsPresets":"img-sh3 img-r24"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e\u003cstrong\u003e<?php echo $bst_t( '1,200+ deals' ); ?>\u003c/strong\u003e\u003cbr\u003e<?php echo $bst_t( 'Closed in 15 years' ); ?>\u003c/p\u003e","bsPresets":"tx-note","bsDesign":{"mt":"-40","ml":"24","mr":"24","bg":"#ffffff","color":"#1f2937"}} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":64,"paddingTablet":72,"paddingMobile":54,"bsPresets":"sec-white sec-lb"} -->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"shield","title":"<?php echo $bst_t( 'Clear legal status' ); ?>","text":"<?php echo $bst_t( 'Individual title for every unit' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#1d4ed8","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"pin","title":"<?php echo $bst_t( 'Prime locations' ); ?>","text":"<?php echo $bst_t( '15 minutes to the centre' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#b45309","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"chart","title":"<?php echo $bst_t( 'Growth potential' ); ?>","text":"<?php echo $bst_t( '8–12% a year' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#059669","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"card","title":"<?php echo $bst_t( 'Flexible payment' ); ?>","text":"<?php echo $bst_t( 'Instalments up to 30 years' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#7c3aed","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/row {"gap":"large","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":6,"spanTablet":12,"spanMobile":12,"bsAnimation":"fade-right"} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-about.svg' ) ); ?>","alt":"<?php echo $bst_t( 'With you on the road to your new home' ); ?>","ratio":"1-1","radius":26,"bsPresets":"img-shoff img-r24"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":6,"spanTablet":12,"spanMobile":12,"bsAnimation":"fade-left"} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'About us' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker"} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'With you on the road to your new home' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'We are the official agent for many reputable developers. Every project is checked for legal status, zoning and construction progress before we offer it.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Full service: choosing a home, viewings, price negotiation, mortgage support and completing the notary and transfer paperwork.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- wp:bst/icon-list {"items":[{"icon":"check","text":"<?php echo $bst_t( '35+ projects on offer' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( '60 consultants' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( '12 partner banks' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Shuttle to project viewings' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Free paperwork support' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Refund if details are wrong' ); ?>","link":""}],"layout":"grid","columns":2,"iconColor":"#1d4ed8","iconBg":true,"gap":12} /-->
<!-- wp:bst/buttons {"stackMobile":true} -->
<!-- wp:bst/button {"text":"<?php echo $bst_t( 'Book a viewing' ); ?>","url":"#contact","radius":10} /-->
<!-- wp:bst/button {"text":"<?php echo $bst_t( 'Call us' ); ?>","url":"tel:0900345678","radius":10,"style":"outline"} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54,"anchor":"services","bsPresets":"sec-soft"} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Featured projects' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'The best places to live' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Updated every week with the latest prices and offers from developers.' ); ?>\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mw":"760","mwc":true}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-1.svg' ) ); ?>","alt":"<?php echo $bst_t( '2-bedroom city apartment' ); ?>","title":"<?php echo $bst_t( '2-bedroom city apartment' ); ?>","text":"<?php echo $bst_t( '68 m², river view, basic furniture, from $210,000.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Details →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-2.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Terraced townhouse' ); ?>","title":"<?php echo $bst_t( 'Terraced townhouse' ); ?>","text":"<?php echo $bst_t( '5x20 m, four floors, live and do business.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Details →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-3.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Lakeside villa' ); ?>","title":"<?php echo $bst_t( 'Lakeside villa' ); ?>","text":"<?php echo $bst_t( '300 m² plot, private garden, 24/7 security.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Details →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-1.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Studio apartment' ); ?>","title":"<?php echo $bst_t( 'Studio apartment' ); ?>","text":"<?php echo $bst_t( '35 m², ideal for young people and renting out.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Details →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-2.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Commercial shophouse' ); ?>","title":"<?php echo $bst_t( 'Commercial shophouse' ); ?>","text":"<?php echo $bst_t( 'Main-road frontage, ready for business.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Details →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-3.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Residential plots' ); ?>","title":"<?php echo $bst_t( 'Residential plots' ); ?>","text":"<?php echo $bst_t( 'Individual titles, finished infrastructure.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Details →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Buying process' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Buy your home in 4 easy steps' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'A consultant is with you at every step.' ); ?>\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mw":"760","mwc":true}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-p26 col-eq col-hls col-bdt","bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/heading {"text":"01","tag":"p","size":44,"sizeTablet":36,"sizeMobile":29,"bsDesign":{"color":"#1d4ed8","fw":"800"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Your needs' ); ?>","tag":"h3","size":21,"sizeTablet":17,"sizeMobile":20} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Set your budget, area and type of home.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-p26 col-eq col-hls col-bdt","bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/heading {"text":"02","tag":"p","size":44,"sizeTablet":36,"sizeMobile":29,"bsDesign":{"color":"#1d4ed8","fw":"800"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Viewings' ); ?>","tag":"h3","size":21,"sizeTablet":17,"sizeMobile":20} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Visit projects and compare several options.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-p26 col-eq col-hls col-bdt","bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/heading {"text":"03","tag":"p","size":44,"sizeTablet":36,"sizeMobile":29,"bsDesign":{"color":"#1d4ed8","fw":"800"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Negotiation & loan' ); ?>","tag":"h3","size":21,"sizeTablet":17,"sizeMobile":20} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Agree the best price and prepare the loan file.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-p26 col-eq col-hls col-bdt","bsAnimation":"fade-up","bsAnimationDelay":300} -->
<!-- wp:bst/heading {"text":"04","tag":"p","size":44,"sizeTablet":36,"sizeMobile":29,"bsDesign":{"color":"#1d4ed8","fw":"800"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Signing' ); ?>","tag":"h3","size":21,"sizeTablet":17,"sizeMobile":20} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Notary, transfer and handover.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":76,"paddingMobile":50,"textScheme":"light","style":{"color":{"background":"#1d4ed8"}}} -->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"1200","suffix":"+","label":"<?php echo $bst_t( 'Successful deals' ); ?>","color":"#ffffff"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'In 15 years' ); ?>\u003c/p\u003e","size":14,"bsDesign":{"ta":"center","color":"rgba(255,255,255,.8)"}} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"35","suffix":"+","label":"<?php echo $bst_t( 'Projects' ); ?>","color":"#ffffff"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'From trusted developers' ); ?>\u003c/p\u003e","size":14,"bsDesign":{"ta":"center","color":"rgba(255,255,255,.8)"}} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"12","suffix":"","label":"<?php echo $bst_t( 'Partner banks' ); ?>","color":"#ffffff"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Preferential rates' ); ?>\u003c/p\u003e","size":14,"bsDesign":{"ta":"center","color":"rgba(255,255,255,.8)"}} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"60","suffix":"+","label":"<?php echo $bst_t( 'Consultants' ); ?>","color":"#ffffff"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Who know the market' ); ?>\u003c/p\u003e","size":14,"bsDesign":{"ta":"center","color":"rgba(255,255,255,.8)"}} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Why choose us' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Peace of mind from choosing to moving in' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/row {"gap":"medium","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"shield","title":"<?php echo $bst_t( 'Legal checks' ); ?>","text":"<?php echo $bst_t( 'Permits, zoning and progress verified.' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#1d4ed8","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"chart","title":"<?php echo $bst_t( 'Market analysis' ); ?>","text":"<?php echo $bst_t( 'Price and growth reports for every area.' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#059669","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"home","title":"<?php echo $bst_t( 'Homes as described' ); ?>","text":"<?php echo $bst_t( 'Real photos and areas, 100%.' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#b45309","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":12,"spanMobile":12,"bsAnimation":"zoom-in"} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-2.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Peace of mind from choosing to moving in' ); ?>","ratio":"1-1","radius":200,"bsPresets":"img-circ img-sh2"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"card","title":"<?php echo $bst_t( 'Finance support' ); ?>","text":"<?php echo $bst_t( 'Loan packages that fit your income.' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#7c3aed","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"users","title":"<?php echo $bst_t( 'One-to-one advice' ); ?>","text":"<?php echo $bst_t( 'One consultant for the whole journey.' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#db2777","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"support","title":"<?php echo $bst_t( 'After-sales' ); ?>","text":"<?php echo $bst_t( 'Help renting out or reselling later.' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#0d9488","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54,"bsPresets":"sec-light"} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Services' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'A service for every need' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Buyers get free advice. Owners who list with us only pay when the deal closes.' ); ?>\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mw":"760","mwc":true}} /-->
<!-- wp:bst/row {"gap":"medium","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/price-table {"title":"<?php echo $bst_t( 'Buyers' ); ?>","price":"<?php echo $bst_t( 'Free' ); ?>","period":"","description":"<?php echo $bst_t( 'Advice and viewings' ); ?>","features":"<?php echo $bst_t( 'Project advice' ); ?>\n<?php echo $bst_t( 'On-site viewings' ); ?>\n<?php echo $bst_t( 'Negotiation help' ); ?>\n<?php echo $bst_t( '-Mortgage paperwork' ); ?>","buttonText":"<?php echo $bst_t( 'Choose this plan' ); ?>","buttonUrl":"#contact","featured":false,"badge":"<?php echo $bst_t( 'Most chosen' ); ?>","style":"card"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":120} -->
<!-- wp:bst/price-table {"title":"<?php echo $bst_t( 'Full service' ); ?>","price":"<?php echo $bst_t( '0.5%' ); ?>","period":"<?php echo $bst_t( '/ of value' ); ?>","description":"<?php echo $bst_t( 'Most popular' ); ?>","features":"<?php echo $bst_t( 'Everything for buyers' ); ?>\n<?php echo $bst_t( 'Bank loan file' ); ?>\n<?php echo $bst_t( 'Notary and transfer' ); ?>\n<?php echo $bst_t( 'Handover inspection' ); ?>","buttonText":"<?php echo $bst_t( 'Choose this plan' ); ?>","buttonUrl":"#contact","featured":true,"badge":"<?php echo $bst_t( 'Most chosen' ); ?>","style":"card"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":12,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":240} -->
<!-- wp:bst/price-table {"title":"<?php echo $bst_t( 'Sell with us' ); ?>","price":"1%","period":"<?php echo $bst_t( '/ on sale' ); ?>","description":"<?php echo $bst_t( 'For owners' ); ?>","features":"<?php echo $bst_t( 'Free valuation' ); ?>\n<?php echo $bst_t( 'Photos and video' ); ?>\n<?php echo $bst_t( 'Multi-channel ads' ); ?>\n<?php echo $bst_t( 'Qualified buyers' ); ?>","buttonText":"<?php echo $bst_t( 'Choose this plan' ); ?>","buttonUrl":"#contact","featured":false,"badge":"<?php echo $bst_t( 'Most chosen' ); ?>","style":"card"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Project gallery' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Modern living spaces' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/row {"gap":"small"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-1.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-2.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-3.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-about.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- wp:bst/row {"gap":"small"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-hero.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-3.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-1.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-2.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54,"bsPresets":"sec-soft"} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Consultants' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'A caring advisory team' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'They know the market, they are honest and they put your interests first.' ); ?>\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mw":"760","mwc":true}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/team-member {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-1.svg' ) ); ?>","name":"<?php echo $bst_t( 'James Walker' ); ?>","role":"<?php echo $bst_t( 'Sales director' ); ?>","bio":"<?php echo $bst_t( '15 years in the housing market.' ); ?>","style":"card","facebook":"#","zalo":"#","bsPresets":"tm-r20 tm-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/team-member {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-2.svg' ) ); ?>","name":"<?php echo $bst_t( 'Laura Hughes' ); ?>","role":"<?php echo $bst_t( 'Apartment specialist' ); ?>","bio":"<?php echo $bst_t( 'More than 300 apartment deals closed.' ); ?>","style":"card","facebook":"#","zalo":"#","bsPresets":"tm-r20 tm-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/team-member {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-3.svg' ) ); ?>","name":"<?php echo $bst_t( 'Ryan Cooper' ); ?>","role":"<?php echo $bst_t( 'Townhouse specialist' ); ?>","bio":"<?php echo $bst_t( 'Knows city-centre zoning inside out.' ); ?>","style":"card","facebook":"#","zalo":"#","bsPresets":"tm-r20 tm-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":300} -->
<!-- wp:bst/team-member {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-4.svg' ) ); ?>","name":"<?php echo $bst_t( 'Kate Morgan' ); ?>","role":"<?php echo $bst_t( 'Finance advisor' ); ?>","bio":"<?php echo $bst_t( 'Prepares loan files with 12 banks.' ); ?>","style":"card","facebook":"#","zalo":"#","bsPresets":"tm-r20 tm-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Customer reviews' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Families who found their home' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/testimonial {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-1.svg' ) ); ?>","name":"<?php echo $bst_t( 'Tom' ); ?>","role":"<?php echo $bst_t( '2-bedroom apartment' ); ?>","text":"<?php echo $bst_t( 'Clear advice with no pressure. The bank loan was handled from start to finish.' ); ?>","bsPresets":"ts-wc ts-r20 ts-sh1 ts-q"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":120} -->
<!-- wp:bst/testimonial {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-2.svg' ) ); ?>","name":"<?php echo $bst_t( 'Helen' ); ?>","role":"<?php echo $bst_t( 'Townhouse buyer' ); ?>","text":"<?php echo $bst_t( 'We found a great location within budget, and a year later the price has risen nicely.' ); ?>","bsPresets":"ts-wc ts-r20 ts-sh1 ts-q"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":240} -->
<!-- wp:bst/testimonial {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-3.svg' ) ); ?>","name":"<?php echo $bst_t( 'Kevin' ); ?>","role":"<?php echo $bst_t( 'Investor' ); ?>","text":"<?php echo $bst_t( 'Detailed project analysis and transparent legal status. Very trustworthy.' ); ?>","bsPresets":"ts-wc ts-r20 ts-sh1 ts-q"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54,"bsPresets":"sec-light"} -->
<!-- wp:bst/row {"gap":"large"} -->
<!-- wp:bst/column {"spanDesktop":5,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'FAQ' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker"} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Frequently asked questions' ); ?>","tag":"h2","size":38,"sizeTablet":31,"sizeMobile":25,"bsPresets":"hd-tight2"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Need more information? Leave your number and a consultant will call back within 30 minutes.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- wp:bst/icon-box {"icon":"phone","title":"<?php echo $bst_t( 'Hotline' ); ?>","text":"0900 345 678","iconStyle":"square","iconColor":"#ffffff","iconBg":"#1d4ed8","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"message","title":"<?php echo $bst_t( 'Chat / Messenger' ); ?>","text":"<?php echo $bst_t( 'Reply within 5 minutes in office hours' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#1d4ed8","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":7,"spanTablet":12,"spanMobile":12} -->
<!-- wp:bst/accordion {"bsPresets":"acc-cards acc-abg","titleTag":"h3"} -->
<!-- wp:bst/accordion-item {"title":"<?php echo $bst_t( 'Do you help with bank loans?' ); ?>","open":true} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Yes, we work with 12 banks offering preferential rates, simple paperwork and fast disbursement.' ); ?>\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"<?php echo $bst_t( 'Do viewings cost anything?' ); ?>","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'They are completely free, with a shuttle for projects outside the city.' ); ?>\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"<?php echo $bst_t( 'How are projects checked?' ); ?>","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Every project is checked for building permits, detailed zoning and real progress.' ); ?>\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"<?php echo $bst_t( 'Can I list my home for sale?' ); ?>","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Yes, we value it for free and only charge a fee when the sale is done.' ); ?>\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"<?php echo $bst_t( 'What documents do I need to buy?' ); ?>","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'ID, marital status certificate, household registration and proof of income if you take a loan.' ); ?>\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- /wp:bst/accordion -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'News & tips' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Market news' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-1.svg' ) ); ?>","alt":"<?php echo $bst_t( 'The apartment market this quarter' ); ?>","title":"<?php echo $bst_t( 'The apartment market this quarter' ); ?>","text":"<?php echo $bst_t( 'Prices are stable, with new supply mainly in the east and south.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Read more →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-2.svg' ) ); ?>","alt":"<?php echo $bst_t( 'First-time mortgage tips' ); ?>","title":"<?php echo $bst_t( 'First-time mortgage tips' ); ?>","text":"<?php echo $bst_t( 'How to work out what you can repay and choose the right rate.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Read more →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-3.svg' ) ); ?>","alt":"<?php echo $bst_t( '5 checks at handover' ); ?>","title":"<?php echo $bst_t( '5 checks at handover' ); ?>","text":"<?php echo $bst_t( 'A checklist for power, water, walls and doors before you sign.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Read more →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":84,"paddingMobile":56,"anchor":"contact","textScheme":"light","bsPresets":"sec-brand sec-r24"} -->
<!-- wp:bst/row {"gap":"medium","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":8,"spanTablet":12,"spanMobile":12} -->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Looking for a home?' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Leave your number and we will send options that fit your budget within 30 minutes.' ); ?>\u003c/p\u003e","size":18} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/buttons {"stackMobile":true,"align":"right"} -->
<!-- wp:bst/button {"text":"<?php echo $bst_t( 'Get advice' ); ?>","url":"tel:0900345678","radius":10,"style":"white"} /-->
<!-- wp:bst/button {"text":"<?php echo $bst_t( 'Message us' ); ?>","url":"https://zalo.me/0900345678","radius":10,"style":"outline"} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":70,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"pin","title":"<?php echo $bst_t( 'Address' ); ?>","text":"<?php echo $bst_t( 'Floor 12, 68 Park Avenue, City Centre' ); ?>","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#1d4ed8","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"clock","title":"<?php echo $bst_t( 'Opening hours' ); ?>","text":"<?php echo $bst_t( '8:00 am – 8:00 pm, Monday – Sunday' ); ?>","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#1d4ed8","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"phone","title":"<?php echo $bst_t( 'Phone' ); ?>","text":"0900 345 678","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#1d4ed8","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"mail","title":"<?php echo $bst_t( 'Email' ); ?>","text":"<?php echo $bst_t( 'hello@realestate.com' ); ?>","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#1d4ed8","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->
