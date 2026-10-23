<?php
/**
 * Title: Homepage – Spa & beauty
 * Slug: bigspy-ai-theme-wp/page-starter-spa
 * Categories: bigspy-pages
 * Keywords: spa, beauty, massage, salon, lam dep
 * Block Types: core/post-content
 * Post Types: page
 * Viewport Width: 1280
 * Description: Spa / beauty homepage with 15 sections: hero, highlights, story, treatments, process, numbers, benefits, packages, gallery, therapists, reviews, FAQ, tips, booking and contact.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Translated, JSON-safe text for block attributes (English source, translations in /languages). */
$bst_t = static function ( $text ) {
	return substr( wp_json_encode( __( $text, 'bigspy-ai-theme-wp' ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ), 1, -1 ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
};
?>
<!-- wp:bst/section {"bgImage":"<?php echo esc_url( bst_pattern_image( 'starter/spa-hero.svg' ) ); ?>","overlayColor":"#0f2e25","overlayOpacity":62,"textScheme":"light","paddingDesktop":140,"paddingTablet":100,"paddingMobile":72,"minHeight":88,"minHeightUnit":"vh","align":"full"} -->
<!-- wp:bst/row {"gap":"medium","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":7,"spanTablet":12,"spanMobile":12} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Spa · Skin care · Massage' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"color":"#ffffff"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Relax completely, glow from within' ); ?>","tag":"h1","size":56,"sizeTablet":45,"sizeMobile":36,"bsPresets":"hd-tight2","bsAnimation":"fade-up"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Facial treatments, therapeutic massage and herbal hair care with natural products, caring therapists and a calm space in the heart of the city.' ); ?>\u003c/p\u003e","size":19,"bsAnimation":"fade-up","bsAnimationDelay":100} /-->
<!-- wp:bst/icon-list {"items":[{"icon":"check","text":"<?php echo $bst_t( 'Safe organic products' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Private treatment rooms' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Free skin analysis' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Herbal tea after treatment' ); ?>","link":""}],"layout":"grid","columns":2,"iconColor":"#ffffff","iconBg":true,"gap":12} /-->
<!-- wp:bst/buttons {"stackMobile":true} -->
<!-- wp:bst/button {"text":"<?php echo $bst_t( 'Book an appointment' ); ?>","url":"#contact","radius":10} /-->
<!-- wp:bst/button {"text":"<?php echo $bst_t( 'See treatments' ); ?>","url":"#services","radius":10,"style":"white"} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":5,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-left"} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/spa-about.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Relax completely, glow from within' ); ?>","ratio":"4-3","radius":22,"bsPresets":"img-sh3 img-r24"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e\u003cstrong\u003e<?php echo $bst_t( '15% off your first visit' ); ?>\u003c/strong\u003e\u003cbr\u003e<?php echo $bst_t( 'On every treatment this month' ); ?>\u003c/p\u003e","bsPresets":"tx-note","bsDesign":{"mt":"-40","ml":"24","mr":"24","bg":"#ffffff","color":"#1f2937"}} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":64,"paddingTablet":72,"paddingMobile":54,"bsPresets":"sec-white sec-lb"} -->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"leaf","title":"<?php echo $bst_t( '100% natural' ); ?>","text":"<?php echo $bst_t( 'Organic oils and herbs' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#2f855a","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"sparkles","title":"<?php echo $bst_t( 'Visible results' ); ?>","text":"<?php echo $bst_t( 'Better skin after 3–5 sessions' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#b7791f","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"shield","title":"<?php echo $bst_t( 'Fully sterilised' ); ?>","text":"<?php echo $bst_t( 'Tools sterilised after every guest' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#2b6cb0","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"heart","title":"<?php echo $bst_t( 'Caring therapists' ); ?>","text":"<?php echo $bst_t( 'Trained and certified' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#b83280","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/row {"gap":"large","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":6,"spanTablet":12,"spanMobile":12,"bsAnimation":"fade-right"} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/spa-about.svg' ) ); ?>","alt":"<?php echo $bst_t( 'A green space where you can truly rest' ); ?>","ratio":"1-1","radius":26,"bsPresets":"img-shoff img-r24"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":6,"spanTablet":12,"spanMobile":12,"bsAnimation":"fade-left"} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'About us' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker"} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'A green space where you can truly rest' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'We believe beauty starts with feeling relaxed. Every treatment room is quiet and warm, with soft light and a gentle scent of essential oils.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Before each treatment our therapist analyses your skin and recommends the products that suit your skin and body best.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- wp:bst/icon-list {"items":[{"icon":"check","text":"<?php echo $bst_t( '8 private treatment rooms' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Latest skin analysis device' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Genuine imported products' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Member card with 20% off' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Bridal care packages' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Couple rooms available' ); ?>","link":""}],"layout":"grid","columns":2,"iconColor":"#2f855a","iconBg":true,"gap":12} /-->
<!-- wp:bst/buttons {"stackMobile":true} -->
<!-- wp:bst/button {"text":"<?php echo $bst_t( 'Get a free consultation' ); ?>","url":"#contact","radius":10} /-->
<!-- wp:bst/button {"text":"<?php echo $bst_t( 'Call us' ); ?>","url":"tel:0900234567","radius":10,"style":"outline"} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54,"anchor":"services","bsPresets":"sec-soft"} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Treatments' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Our most-loved treatments' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Every treatment is tailored to your skin and what you want to achieve.' ); ?>\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mw":"760","mwc":true}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/spa-1.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Deep facial care' ); ?>","title":"<?php echo $bst_t( 'Deep facial care' ); ?>","text":"<?php echo $bst_t( 'Deep cleansing, exfoliation, a hydrating mask and recovery.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Details →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/spa-2.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Hot stone massage' ); ?>","title":"<?php echo $bst_t( 'Hot stone massage' ); ?>","text":"<?php echo $bst_t( 'Warm basalt stones and essential oils to release tension and help you sleep.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Details →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/spa-3.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Herbal head spa' ); ?>","title":"<?php echo $bst_t( 'Herbal head spa' ); ?>","text":"<?php echo $bst_t( 'Natural herbs and acupressure for the neck and shoulders.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Details →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/spa-1.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Clinical acne care' ); ?>","title":"<?php echo $bst_t( 'Clinical acne care' ); ?>","text":"<?php echo $bst_t( 'Sterile extraction and soothing nutrient infusion.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Details →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/spa-2.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Herbal brightening bath' ); ?>","title":"<?php echo $bst_t( 'Herbal brightening bath' ); ?>","text":"<?php echo $bst_t( 'Turmeric, colostrum and rice bran for naturally bright skin.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Details →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/spa-3.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Body care' ); ?>","title":"<?php echo $bst_t( 'Body care' ); ?>","text":"<?php echo $bst_t( 'Full-body scrub and a nourishing moisture wrap.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Details →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'How it works' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Your relaxation journey' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'From booking to leaving, every detail is taken care of.' ); ?>\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mw":"760","mwc":true}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-p26 col-eq col-hls col-bdt","bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/heading {"text":"01","tag":"p","size":44,"sizeTablet":36,"sizeMobile":29,"bsDesign":{"color":"#2f855a","fw":"800"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Book' ); ?>","tag":"h3","size":21,"sizeTablet":17,"sizeMobile":20} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Choose a treatment and time by phone or chat.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-p26 col-eq col-hls col-bdt","bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/heading {"text":"02","tag":"p","size":44,"sizeTablet":36,"sizeMobile":29,"bsDesign":{"color":"#2f855a","fw":"800"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Skin analysis' ); ?>","tag":"h3","size":21,"sizeTablet":17,"sizeMobile":20} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Your therapist analyses your skin and suggests a plan.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-p26 col-eq col-hls col-bdt","bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/heading {"text":"03","tag":"p","size":44,"sizeTablet":36,"sizeMobile":29,"bsDesign":{"color":"#2f855a","fw":"800"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Treatment' ); ?>","tag":"h3","size":21,"sizeTablet":17,"sizeMobile":20} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Enjoy your treatment in a quiet private room.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-p26 col-eq col-hls col-bdt","bsAnimation":"fade-up","bsAnimationDelay":300} -->
<!-- wp:bst/heading {"text":"04","tag":"p","size":44,"sizeTablet":36,"sizeMobile":29,"bsDesign":{"color":"#2f855a","fw":"800"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Home care' ); ?>","tag":"h3","size":21,"sizeTablet":17,"sizeMobile":20} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Take home tips to keep your results.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":76,"paddingMobile":50,"textScheme":"light","style":{"color":{"background":"#2f855a"}}} -->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"9","suffix":"+","label":"<?php echo $bst_t( 'Years open' ); ?>","color":"#ffffff"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Since 2015' ); ?>\u003c/p\u003e","size":14,"bsDesign":{"ta":"center","color":"rgba(255,255,255,.8)"}} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"15","suffix":"k","label":"<?php echo $bst_t( 'Clients' ); ?>","color":"#ffffff"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Who keep coming back' ); ?>\u003c/p\u003e","size":14,"bsDesign":{"ta":"center","color":"rgba(255,255,255,.8)"}} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"30","suffix":"+","label":"<?php echo $bst_t( 'Treatments' ); ?>","color":"#ffffff"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'For skin, body and hair' ); ?>\u003c/p\u003e","size":14,"bsDesign":{"ta":"center","color":"rgba(255,255,255,.8)"}} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"98","suffix":"%","label":"<?php echo $bst_t( 'Satisfied' ); ?>","color":"#ffffff"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'In our after-visit survey' ); ?>\u003c/p\u003e","size":14,"bsDesign":{"ta":"center","color":"rgba(255,255,255,.8)"}} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Our promise' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Safe – Effective – Private' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/row {"gap":"medium","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"leaf","title":"<?php echo $bst_t( 'Gentle ingredients' ); ?>","text":"<?php echo $bst_t( 'No corticoids, no harsh chemicals.' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#2f855a","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"droplet","title":"<?php echo $bst_t( 'Deep hydration' ); ?>","text":"<?php echo $bst_t( 'Restores your skin’s natural barrier.' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#3182ce","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"sun","title":"<?php echo $bst_t( 'Skin protection' ); ?>","text":"<?php echo $bst_t( 'How to use sunscreen properly after treatment.' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#d69e2e","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":12,"spanMobile":12,"bsAnimation":"zoom-in"} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/spa-2.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Safe – Effective – Private' ); ?>","ratio":"1-1","radius":200,"bsPresets":"img-circ img-sh2"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"users","title":"<?php echo $bst_t( 'Your own therapist' ); ?>","text":"<?php echo $bst_t( 'One therapist with you for the whole treatment.' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#805ad5","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"clock","title":"<?php echo $bst_t( 'On time' ); ?>","text":"<?php echo $bst_t( 'Never more than 5 minutes’ wait.' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#dd6b20","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"gift","title":"<?php echo $bst_t( 'Gifts' ); ?>","text":"<?php echo $bst_t( 'A free mask with every package.' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#d53f8c","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54,"bsPresets":"sec-light"} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Prices' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Money-saving packages' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Buy a package to save up to 30% and share it with family.' ); ?>\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mw":"760","mwc":true}} /-->
<!-- wp:bst/row {"gap":"medium","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/price-table {"title":"<?php echo $bst_t( 'Relax package' ); ?>","price":"<?php echo $bst_t( '$45' ); ?>","period":"<?php echo $bst_t( '/ session' ); ?>","description":"<?php echo $bst_t( 'Perfect for a first visit' ); ?>","features":"<?php echo $bst_t( '60-minute hot stone massage' ); ?>\n<?php echo $bst_t( 'Herbal head spa' ); ?>\n<?php echo $bst_t( 'Herbal tea' ); ?>\n<?php echo $bst_t( '-Deep skin analysis' ); ?>","buttonText":"<?php echo $bst_t( 'Choose this plan' ); ?>","buttonUrl":"#contact","featured":false,"badge":"<?php echo $bst_t( 'Most chosen' ); ?>","style":"card"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":120} -->
<!-- wp:bst/price-table {"title":"<?php echo $bst_t( '5 facials' ); ?>","price":"<?php echo $bst_t( '$159' ); ?>","period":"<?php echo $bst_t( '/ 5 sessions' ); ?>","description":"<?php echo $bst_t( 'Most popular' ); ?>","features":"<?php echo $bst_t( 'Skin analysis & advice' ); ?>\n<?php echo $bst_t( 'Deep facial care' ); ?>\n<?php echo $bst_t( '24K gold mask' ); ?>\n<?php echo $bst_t( 'One free massage' ); ?>","buttonText":"<?php echo $bst_t( 'Choose this plan' ); ?>","buttonUrl":"#contact","featured":true,"badge":"<?php echo $bst_t( 'Most chosen' ); ?>","style":"card"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":12,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":240} -->
<!-- wp:bst/price-table {"title":"<?php echo $bst_t( 'Bridal package' ); ?>","price":"<?php echo $bst_t( '$319' ); ?>","period":"<?php echo $bst_t( '/ package' ); ?>","description":"<?php echo $bst_t( 'Glow on your big day' ); ?>","features":"<?php echo $bst_t( '8 facial + body sessions' ); ?>\n<?php echo $bst_t( 'Herbal brightening bath' ); ?>\n<?php echo $bst_t( 'Hair care' ); ?>\n<?php echo $bst_t( 'Make-up trial' ); ?>","buttonText":"<?php echo $bst_t( 'Choose this plan' ); ?>","buttonUrl":"#contact","featured":false,"badge":"<?php echo $bst_t( 'Most chosen' ); ?>","style":"card"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Our spa' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'As peaceful as a resort' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/row {"gap":"small"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/spa-1.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/spa-2.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/spa-3.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/spa-about.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- wp:bst/row {"gap":"small"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/spa-hero.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/spa-3.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/spa-1.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/spa-2.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54,"bsPresets":"sec-soft"} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Therapists' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'The hands that care for you' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'A trained team with professional certificates.' ); ?>\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mw":"760","mwc":true}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/team-member {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-1.svg' ) ); ?>","name":"<?php echo $bst_t( 'Grace Miller' ); ?>","role":"<?php echo $bst_t( 'Skin specialist' ); ?>","bio":"<?php echo $bst_t( '10 years of deep skin care experience.' ); ?>","style":"card","facebook":"#","zalo":"#","bsPresets":"tm-r20 tm-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/team-member {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-2.svg' ) ); ?>","name":"<?php echo $bst_t( 'Lily Chen' ); ?>","role":"<?php echo $bst_t( 'Massage therapist' ); ?>","bio":"<?php echo $bst_t( 'Certified in Thai therapeutic massage.' ); ?>","style":"card","facebook":"#","zalo":"#","bsPresets":"tm-r20 tm-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/team-member {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-3.svg' ) ); ?>","name":"<?php echo $bst_t( 'Olivia Brooks' ); ?>","role":"<?php echo $bst_t( 'Hair specialist' ); ?>","bio":"<?php echo $bst_t( 'Expert in herbal head spa rituals.' ); ?>","style":"card","facebook":"#","zalo":"#","bsPresets":"tm-r20 tm-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":300} -->
<!-- wp:bst/team-member {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-4.svg' ) ); ?>","name":"<?php echo $bst_t( 'Mia Turner' ); ?>","role":"<?php echo $bst_t( 'Consultant' ); ?>","bio":"<?php echo $bst_t( 'Always listens to find the right treatment.' ); ?>","style":"card","facebook":"#","zalo":"#","bsPresets":"tm-r20 tm-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Customer reviews' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'What our clients say' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/testimonial {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-1.svg' ) ); ?>","name":"<?php echo $bst_t( 'Rachel' ); ?>","role":"<?php echo $bst_t( 'Loyal client' ); ?>","text":"<?php echo $bst_t( 'My skin improved clearly after a month – the therapist is very gentle and gives great advice.' ); ?>","bsPresets":"ts-wc ts-r20 ts-sh1 ts-q"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":120} -->
<!-- wp:bst/testimonial {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-2.svg' ) ); ?>","name":"<?php echo $bst_t( 'Chloe' ); ?>","role":"<?php echo $bst_t( 'Teacher' ); ?>","text":"<?php echo $bst_t( 'The hot stone massage is amazing, like recharging my energy after a long week.' ); ?>","bsPresets":"ts-wc ts-r20 ts-sh1 ts-q"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":240} -->
<!-- wp:bst/testimonial {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-3.svg' ) ); ?>","name":"<?php echo $bst_t( 'Natalie' ); ?>","role":"<?php echo $bst_t( 'Accountant' ); ?>","text":"<?php echo $bst_t( 'Clean, fragrant and quiet. Booking by chat is very quick.' ); ?>","bsPresets":"ts-wc ts-r20 ts-sh1 ts-q"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54,"bsPresets":"sec-light"} -->
<!-- wp:bst/row {"gap":"large"} -->
<!-- wp:bst/column {"spanDesktop":5,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'FAQ' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker"} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Frequently asked questions' ); ?>","tag":"h2","size":38,"sizeTablet":31,"sizeMobile":25,"bsPresets":"hd-tight2"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Any other questions? Send us a message for a free consultation.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- wp:bst/icon-box {"icon":"phone","title":"<?php echo $bst_t( 'Hotline' ); ?>","text":"0900 234 567","iconStyle":"square","iconColor":"#ffffff","iconBg":"#2f855a","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"message","title":"<?php echo $bst_t( 'Chat / Messenger' ); ?>","text":"<?php echo $bst_t( 'Reply within 5 minutes in office hours' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#2f855a","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":7,"spanTablet":12,"spanMobile":12} -->
<!-- wp:bst/accordion {"bsPresets":"acc-cards acc-abg","titleTag":"h3"} -->
<!-- wp:bst/accordion-item {"title":"<?php echo $bst_t( 'How far ahead should I book?' ); ?>","open":true} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Book one day ahead to get your preferred time. Walk-ins are welcome when a room is free.' ); ?>\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"<?php echo $bst_t( 'Is it suitable for sensitive skin?' ); ?>","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Yes. Your therapist analyses your skin and chooses gentle, non-irritating products.' ); ?>\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"<?php echo $bst_t( 'Do packages expire?' ); ?>","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Packages are valid for 6 months and can be shared with family.' ); ?>\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"<?php echo $bst_t( 'When will I see results?' ); ?>","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Skin is usually smoother and brighter after 3–5 sessions, depending on its condition.' ); ?>\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"<?php echo $bst_t( 'Is there parking?' ); ?>","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Yes, free motorbike and car parking right next to the spa.' ); ?>\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- /wp:bst/accordion -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'News & tips' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Beauty tips' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/spa-1.svg' ) ); ?>","alt":"<?php echo $bst_t( '5 steps for evening skin care' ); ?>","title":"<?php echo $bst_t( '5 steps for evening skin care' ); ?>","text":"<?php echo $bst_t( 'Simple habits that help your skin recover and glow every morning.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Read more →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/spa-2.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Choosing the right sunscreen' ); ?>","title":"<?php echo $bst_t( 'Choosing the right sunscreen' ); ?>","text":"<?php echo $bst_t( 'How to read SPF and PA and pick one for oily or dry skin.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Read more →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/spa-3.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Benefits of regular massage' ); ?>","title":"<?php echo $bst_t( 'Benefits of regular massage' ); ?>","text":"<?php echo $bst_t( 'Less stress, better sleep and healthier circulation.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Read more →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":84,"paddingMobile":56,"anchor":"contact","textScheme":"light","bsPresets":"sec-brand sec-r24"} -->
<!-- wp:bst/row {"gap":"medium","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":8,"spanTablet":12,"spanMobile":12} -->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Take an afternoon for yourself' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Book today and get 15% off your first visit.' ); ?>\u003c/p\u003e","size":18} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/buttons {"stackMobile":true,"align":"right"} -->
<!-- wp:bst/button {"text":"<?php echo $bst_t( 'Book an appointment' ); ?>","url":"tel:0900234567","radius":10,"style":"white"} /-->
<!-- wp:bst/button {"text":"<?php echo $bst_t( 'Message us' ); ?>","url":"https://zalo.me/0900234567","radius":10,"style":"outline"} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":70,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"pin","title":"<?php echo $bst_t( 'Address' ); ?>","text":"<?php echo $bst_t( '12 Garden Avenue, City Centre' ); ?>","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#2f855a","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"clock","title":"<?php echo $bst_t( 'Opening hours' ); ?>","text":"<?php echo $bst_t( '9:00 am – 9:00 pm, every day' ); ?>","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#2f855a","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"phone","title":"<?php echo $bst_t( 'Phone' ); ?>","text":"0900 234 567","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#2f855a","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"mail","title":"<?php echo $bst_t( 'Email' ); ?>","text":"<?php echo $bst_t( 'booking@spa.com' ); ?>","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#2f855a","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->
