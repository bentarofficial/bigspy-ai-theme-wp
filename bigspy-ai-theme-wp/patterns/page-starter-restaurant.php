<?php
/**
 * Title: Homepage – Restaurant & café
 * Slug: bigspy-ai-theme-wp/page-starter-restaurant
 * Categories: bigspy-pages
 * Keywords: restaurant, cafe, food, menu, nha hang, ca phe
 * Block Types: core/post-content
 * Post Types: page
 * Viewport Width: 1280
 * Description: Restaurant / café homepage with 15 sections: hero, highlights, story, menu, process, numbers, signature dishes, set menus, gallery, chefs, reviews, FAQ, news, booking and contact.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Translated, JSON-safe text for block attributes (English source, translations in /languages). */
$bst_t = static function ( $text ) {
	return substr( wp_json_encode( __( $text, 'bigspy-ai-theme-wp' ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ), 1, -1 ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
};
?>
<!-- wp:bst/section {"bgImage":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-hero.svg' ) ); ?>","overlayColor":"#1c0d07","overlayOpacity":62,"textScheme":"light","paddingDesktop":140,"paddingTablet":100,"paddingMobile":72,"minHeight":88,"minHeightUnit":"vh","align":"full"} -->
<!-- wp:bst/row {"gap":"medium","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":7,"spanTablet":12,"spanMobile":12} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Restaurant · Café · Events' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"color":"#ffffff"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Fresh flavours, served with heart' ); ?>","tag":"h1","size":56,"sizeTablet":45,"sizeMobile":36,"bsPresets":"hd-tight2","bsAnimation":"fade-up"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Ingredients chosen every morning, chefs with over 12 years of experience and a warm space – the perfect place for a date, a business lunch or a family weekend.' ); ?>\u003c/p\u003e","size":19,"bsAnimation":"fade-up","bsAnimationDelay":100} /-->
<!-- wp:bst/icon-list {"items":[{"icon":"check","text":"<?php echo $bst_t( 'Fresh ingredients every day' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Private rooms for parties' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Delivery within 30 minutes' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Free parking' ); ?>","link":""}],"layout":"grid","columns":2,"iconColor":"#ffffff","iconBg":true,"gap":12} /-->
<!-- wp:bst/buttons {"stackMobile":true} -->
<!-- wp:bst/button {"text":"<?php echo $bst_t( 'Book a table' ); ?>","url":"#contact","radius":10} /-->
<!-- wp:bst/button {"text":"<?php echo $bst_t( 'See the menu' ); ?>","url":"#services","radius":10,"style":"white"} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":5,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-left"} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-about.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Fresh flavours, served with heart' ); ?>","ratio":"4-3","radius":22,"bsPresets":"img-sh3 img-r24"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e\u003cstrong\u003e<?php echo $bst_t( '4.9 ★ on Google' ); ?>\u003c/strong\u003e\u003cbr\u003e<?php echo $bst_t( 'More than 2,300 guest reviews' ); ?>\u003c/p\u003e","bsPresets":"tx-note","bsDesign":{"mt":"-40","ml":"24","mr":"24","bg":"#ffffff","color":"#1f2937"}} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":64,"paddingTablet":72,"paddingMobile":54,"bsPresets":"sec-white sec-lb"} -->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"clock","title":"<?php echo $bst_t( 'Open every day' ); ?>","text":"<?php echo $bst_t( '7:00 am – 10:00 pm, holidays too' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#c2410c","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"truck","title":"<?php echo $bst_t( 'Fast delivery' ); ?>","text":"<?php echo $bst_t( 'Free on orders over $30' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#ea580c","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"users","title":"<?php echo $bst_t( 'Parties of 10–120' ); ?>","text":"<?php echo $bst_t( 'Birthdays, team dinners, events' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#b45309","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"award","title":"<?php echo $bst_t( 'Top 10 restaurants' ); ?>","text":"<?php echo $bst_t( 'Voted by the local food community' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#92400e","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/row {"gap":"large","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":6,"spanTablet":12,"spanMobile":12,"bsAnimation":"fade-right"} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-about.svg' ) ); ?>","alt":"<?php echo $bst_t( 'From a family kitchen to a much-loved restaurant' ); ?>","ratio":"1-1","radius":26,"bsPresets":"img-shoff img-r24"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":6,"spanTablet":12,"spanMobile":12,"bsAnimation":"fade-left"} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Our story' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker"} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'From a family kitchen to a much-loved restaurant' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'In 2012 we started in a small kitchen with six tables. What kept guests coming back was not fancy food but familiar flavours, cooked with the best ingredients and care in every detail.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Today the restaurant seats over 120 guests, has 3 private rooms and a team of 40 – but the spirit is the same as on day one: we serve every guest like family.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- wp:bst/icon-list {"items":[{"icon":"check","text":"<?php echo $bst_t( 'Head chef with 12 years of experience' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( '80+ Asian and European dishes' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Vegetables from our own farm' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'No industrial MSG' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Airy garden seating' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Custom party menus' ); ?>","link":""}],"layout":"grid","columns":2,"iconColor":"#c2410c","iconBg":true,"gap":12} /-->
<!-- wp:bst/buttons {"stackMobile":true} -->
<!-- wp:bst/button {"text":"<?php echo $bst_t( 'See the full menu' ); ?>","url":"#contact","radius":10} /-->
<!-- wp:bst/button {"text":"<?php echo $bst_t( 'Call us' ); ?>","url":"tel:0900123456","radius":10,"style":"outline"} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54,"anchor":"services","bsPresets":"sec-soft"} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Menu highlights' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Our most-ordered dishes' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'The menu changes with the seasons to keep every flavour fresh. Here are the dishes our guests love most.' ); ?>\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mw":"760","mwc":true}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-1.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Black pepper beef' ); ?>","title":"<?php echo $bst_t( 'Black pepper beef' ); ?>","text":"<?php echo $bst_t( 'Tender Australian tenderloin, rich black pepper sauce and garlic butter potatoes.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Order →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-2.svg' ) ); ?>","alt":"<?php echo $bst_t( 'House-roasted coffee' ); ?>","title":"<?php echo $bst_t( 'House-roasted coffee' ); ?>","text":"<?php echo $bst_t( 'Arabica beans roasted every week, brewed as filter or espresso.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'See drinks →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-3.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Cakes baked today' ); ?>","title":"<?php echo $bst_t( 'Cakes baked today' ); ?>","text":"<?php echo $bst_t( 'Tiramisu, passion fruit cheesecake and mousse, made fresh every morning.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'See desserts →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-1.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Clay pot fish' ); ?>","title":"<?php echo $bst_t( 'Clay pot fish' ); ?>","text":"<?php echo $bst_t( 'Caramelised fish, sour soup and crispy clay pot rice.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Order →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-3.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Seafood hot pot' ); ?>","title":"<?php echo $bst_t( 'Seafood hot pot' ); ?>","text":"<?php echo $bst_t( 'Live shrimp, squid and clams in our special sweet-and-sour broth.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Order →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-2.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Fruit teas' ); ?>","title":"<?php echo $bst_t( 'Fruit teas' ); ?>","text":"<?php echo $bst_t( 'Peach lemongrass and lychee rose teas, served iced on sunny days.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'See drinks →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Easy booking' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Four steps to a perfect meal' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Book by phone, chat or the website – our team confirms within 5 minutes.' ); ?>\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mw":"760","mwc":true}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-p26 col-eq col-hls col-bdt","bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/heading {"text":"01","tag":"p","size":44,"sizeTablet":36,"sizeMobile":29,"bsDesign":{"color":"#c2410c","fw":"800"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Pick a time' ); ?>","tag":"h3","size":21,"sizeTablet":17,"sizeMobile":20} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Tell us the date, time and number of guests by phone or chat.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-p26 col-eq col-hls col-bdt","bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/heading {"text":"02","tag":"p","size":44,"sizeTablet":36,"sizeMobile":29,"bsDesign":{"color":"#c2410c","fw":"800"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Choose your area' ); ?>","tag":"h3","size":21,"sizeTablet":17,"sizeMobile":20} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Main hall, garden or a private room – whatever suits the occasion.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-p26 col-eq col-hls col-bdt","bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/heading {"text":"03","tag":"p","size":44,"sizeTablet":36,"sizeMobile":29,"bsDesign":{"color":"#c2410c","fw":"800"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Pre-order dishes' ); ?>","tag":"h3","size":21,"sizeTablet":17,"sizeMobile":20} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Order ahead so nothing keeps you waiting when you arrive.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-p26 col-eq col-hls col-bdt","bsAnimation":"fade-up","bsAnimationDelay":300} -->
<!-- wp:bst/heading {"text":"04","tag":"p","size":44,"sizeTablet":36,"sizeMobile":29,"bsDesign":{"color":"#c2410c","fw":"800"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Enjoy' ); ?>","tag":"h3","size":21,"sizeTablet":17,"sizeMobile":20} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Arrive on time – your table and dishes are ready.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":76,"paddingMobile":50,"textScheme":"light","style":{"color":{"background":"#c2410c"}}} -->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"12","suffix":"+","label":"<?php echo $bst_t( 'Years of experience' ); ?>","color":"#ffffff"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Serving since 2012' ); ?>\u003c/p\u003e","size":14,"bsDesign":{"ta":"center","color":"rgba(255,255,255,.8)"}} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"80","suffix":"+","label":"<?php echo $bst_t( 'Dishes on the menu' ); ?>","color":"#ffffff"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Refreshed every season' ); ?>\u003c/p\u003e","size":14,"bsDesign":{"ta":"center","color":"rgba(255,255,255,.8)"}} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"25","suffix":"k","label":"<?php echo $bst_t( 'Guests a year' ); ?>","color":"#ffffff"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'And still growing' ); ?>\u003c/p\u003e","size":14,"bsDesign":{"ta":"center","color":"rgba(255,255,255,.8)"}} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"120","suffix":"","label":"<?php echo $bst_t( 'Seats' ); ?>","color":"#ffffff"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Plus 3 private rooms' ); ?>\u003c/p\u003e","size":14,"bsDesign":{"ta":"center","color":"rgba(255,255,255,.8)"}} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Why guests come back' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'A complete dining experience' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/row {"gap":"medium","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"leaf","title":"<?php echo $bst_t( 'Clean ingredients' ); ?>","text":"<?php echo $bst_t( 'Farm vegetables, meat and fish with clear origins.' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#16a34a","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"star","title":"<?php echo $bst_t( 'Our own recipes' ); ?>","text":"<?php echo $bst_t( 'Sauces and spice blends made by the head chef.' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#ea580c","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"heart","title":"<?php echo $bst_t( 'Caring service' ); ?>","text":"<?php echo $bst_t( 'A trained team that always welcomes you with a smile.' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#db2777","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":12,"spanMobile":12,"bsAnimation":"zoom-in"} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-2.svg' ) ); ?>","alt":"<?php echo $bst_t( 'A complete dining experience' ); ?>","ratio":"1-1","radius":200,"bsPresets":"img-circ img-sh2"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"clock","title":"<?php echo $bst_t( 'Quick to the table' ); ?>","text":"<?php echo $bst_t( 'Dishes served within 15 minutes at peak hours.' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#2563eb","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"shield","title":"<?php echo $bst_t( 'Food safety' ); ?>","text":"<?php echo $bst_t( 'A kitchen certified for hygiene and food safety.' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#0d9488","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"gift","title":"<?php echo $bst_t( 'Member rewards' ); ?>","text":"<?php echo $bst_t( 'Collect points and enjoy 10% off as a regular.' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#9333ea","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54,"bsPresets":"sec-light"} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Set menus' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Set menus for every occasion' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Prices include tax and service. Dishes can be swapped to suit your taste.' ); ?>\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mw":"760","mwc":true}} /-->
<!-- wp:bst/row {"gap":"medium","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/price-table {"title":"<?php echo $bst_t( 'Family set' ); ?>","price":"<?php echo $bst_t( '$59' ); ?>","period":"<?php echo $bst_t( '/ 4 guests' ); ?>","description":"<?php echo $bst_t( 'A cosy weekend dinner' ); ?>","features":"<?php echo $bst_t( '5 mains + soup' ); ?>\n<?php echo $bst_t( 'Seasonal dessert' ); ?>\n<?php echo $bst_t( 'Free soft drinks' ); ?>\n<?php echo $bst_t( '-Private room' ); ?>","buttonText":"<?php echo $bst_t( 'Choose this plan' ); ?>","buttonUrl":"#contact","featured":false,"badge":"<?php echo $bst_t( 'Most chosen' ); ?>","style":"card"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":120} -->
<!-- wp:bst/price-table {"title":"<?php echo $bst_t( 'Party set' ); ?>","price":"<?php echo $bst_t( '$149' ); ?>","period":"<?php echo $bst_t( '/ 10 guests' ); ?>","description":"<?php echo $bst_t( 'Most popular choice' ); ?>","features":"<?php echo $bst_t( '8 mains + hot pot' ); ?>\n<?php echo $bst_t( 'Beer or soft drinks' ); ?>\n<?php echo $bst_t( 'Private room with karaoke' ); ?>\n<?php echo $bst_t( 'Themed decoration' ); ?>","buttonText":"<?php echo $bst_t( 'Choose this plan' ); ?>","buttonUrl":"#contact","featured":true,"badge":"<?php echo $bst_t( 'Most chosen' ); ?>","style":"card"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":12,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":240} -->
<!-- wp:bst/price-table {"title":"<?php echo $bst_t( 'Conference set' ); ?>","price":"<?php echo $bst_t( '$19' ); ?>","period":"<?php echo $bst_t( '/ guest' ); ?>","description":"<?php echo $bst_t( 'From 30 guests' ); ?>","features":"<?php echo $bst_t( '30-dish Asian & European buffet' ); ?>\n<?php echo $bst_t( 'Projector and sound' ); ?>\n<?php echo $bst_t( 'Two tea breaks' ); ?>\n<?php echo $bst_t( 'MC and reception help' ); ?>","buttonText":"<?php echo $bst_t( 'Choose this plan' ); ?>","buttonUrl":"#contact","featured":false,"badge":"<?php echo $bst_t( 'Most chosen' ); ?>","style":"card"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Our space' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Warm, elegant and welcoming' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/row {"gap":"small"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-1.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-2.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-3.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-about.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- wp:bst/row {"gap":"small"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-hero.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-3.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-1.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-2.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54,"bsPresets":"sec-soft"} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Our team' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'The people behind the food' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Experienced chefs and managers who care about every plate.' ); ?>\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mw":"760","mwc":true}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/team-member {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-1.svg' ) ); ?>","name":"<?php echo $bst_t( 'Daniel Carter' ); ?>","role":"<?php echo $bst_t( 'Head chef' ); ?>","bio":"<?php echo $bst_t( '15 years in five-star hotel kitchens.' ); ?>","style":"card","facebook":"#","zalo":"#","bsPresets":"tm-r20 tm-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/team-member {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-2.svg' ) ); ?>","name":"<?php echo $bst_t( 'Sophie Bennett' ); ?>","role":"<?php echo $bst_t( 'Pastry chef' ); ?>","bio":"<?php echo $bst_t( 'Trained in pastry in France.' ); ?>","style":"card","facebook":"#","zalo":"#","bsPresets":"tm-r20 tm-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/team-member {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-3.svg' ) ); ?>","name":"<?php echo $bst_t( 'Lucas Moreno' ); ?>","role":"<?php echo $bst_t( 'Barista' ); ?>","bio":"<?php echo $bst_t( 'Award-winning national coffee champion.' ); ?>","style":"card","facebook":"#","zalo":"#","bsPresets":"tm-r20 tm-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":300} -->
<!-- wp:bst/team-member {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-4.svg' ) ); ?>","name":"<?php echo $bst_t( 'Emma Walsh' ); ?>","role":"<?php echo $bst_t( 'Manager' ); ?>","bio":"<?php echo $bst_t( 'Always listening to make every meal better.' ); ?>","style":"card","facebook":"#","zalo":"#","bsPresets":"tm-r20 tm-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Customer reviews' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'What our guests say' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/testimonial {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-1.svg' ) ); ?>","name":"<?php echo $bst_t( 'Anna' ); ?>","role":"<?php echo $bst_t( 'Regular for 3 years' ); ?>","text":"<?php echo $bst_t( 'Delicious food, beautiful plating and a really friendly team. Our family comes every weekend.' ); ?>","bsPresets":"ts-wc ts-r20 ts-sh1 ts-q"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":120} -->
<!-- wp:bst/testimonial {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-2.svg' ) ); ?>","name":"<?php echo $bst_t( 'Hannah' ); ?>","role":"<?php echo $bst_t( 'Office worker' ); ?>","text":"<?php echo $bst_t( 'Fragrant coffee and a quiet space. I often bring my laptop and work here in the afternoon.' ); ?>","bsPresets":"ts-wc ts-r20 ts-sh1 ts-q"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":240} -->
<!-- wp:bst/testimonial {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-3.svg' ) ); ?>","name":"<?php echo $bst_t( 'Mark' ); ?>","role":"<?php echo $bst_t( 'Company director' ); ?>","text":"<?php echo $bst_t( 'Our year-end party for 80 people was perfectly organised and every dish was finished.' ); ?>","bsPresets":"ts-wc ts-r20 ts-sh1 ts-q"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54,"bsPresets":"sec-light"} -->
<!-- wp:bst/row {"gap":"large"} -->
<!-- wp:bst/column {"spanDesktop":5,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'FAQ' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker"} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Frequently asked questions' ); ?>","tag":"h2","size":38,"sizeTablet":31,"sizeMobile":25,"bsPresets":"hd-tight2"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Can’t find your answer? Call or message us – we are always happy to help.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- wp:bst/icon-box {"icon":"phone","title":"<?php echo $bst_t( 'Hotline' ); ?>","text":"0900 123 456","iconStyle":"square","iconColor":"#ffffff","iconBg":"#c2410c","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"message","title":"<?php echo $bst_t( 'Chat / Messenger' ); ?>","text":"<?php echo $bst_t( 'Reply within 5 minutes in office hours' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#c2410c","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":7,"spanTablet":12,"spanMobile":12} -->
<!-- wp:bst/accordion {"bsPresets":"acc-cards acc-abg","titleTag":"h3"} -->
<!-- wp:bst/accordion-item {"title":"<?php echo $bst_t( 'What are your opening hours?' ); ?>","open":true} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'We are open from 7:00 am to 10:00 pm every day, including holidays.' ); ?>\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"<?php echo $bst_t( 'Do I need to book a table?' ); ?>","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Booking is recommended from Friday to Sunday evening. On weekdays you can just walk in.' ); ?>\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"<?php echo $bst_t( 'Do you deliver?' ); ?>","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Yes, within 5 km, free for orders over $30.' ); ?>\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"<?php echo $bst_t( 'Do you have vegetarian dishes?' ); ?>","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'We have a vegetarian menu of more than 15 dishes – let us know in advance.' ); ?>\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"<?php echo $bst_t( 'Can I get an invoice?' ); ?>","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Yes, we issue electronic invoices for individuals and companies.' ); ?>\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- /wp:bst/accordion -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'News & tips' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Stories from the kitchen' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-1.svg' ) ); ?>","alt":"<?php echo $bst_t( 'How to choose great beef' ); ?>","title":"<?php echo $bst_t( 'How to choose great beef' ); ?>","text":"<?php echo $bst_t( 'How to read the grain, colour and freshness so beef stays tender and sweet.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Read more →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-2.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Brewing the perfect filter coffee' ); ?>","title":"<?php echo $bst_t( 'Brewing the perfect filter coffee' ); ?>","text":"<?php echo $bst_t( 'Coffee ratio, water temperature and brewing time for a rich cup.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Read more →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-3.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Our new autumn menu' ); ?>","title":"<?php echo $bst_t( 'Our new autumn menu' ); ?>","text":"<?php echo $bst_t( 'Eight new dishes inspired by autumn produce.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Read more →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":84,"paddingMobile":56,"anchor":"contact","textScheme":"light","bsPresets":"sec-brand sec-r24"} -->
<!-- wp:bst/row {"gap":"medium","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":8,"spanTablet":12,"spanMobile":12} -->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Book a table for tonight?' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Call now to get the best seats, book a party or order takeaway.' ); ?>\u003c/p\u003e","size":18} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/buttons {"stackMobile":true,"align":"right"} -->
<!-- wp:bst/button {"text":"<?php echo $bst_t( 'Book a table' ); ?>","url":"tel:0900123456","radius":10,"style":"white"} /-->
<!-- wp:bst/button {"text":"<?php echo $bst_t( 'Message us' ); ?>","url":"https://zalo.me/0900123456","radius":10,"style":"outline"} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":70,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"pin","title":"<?php echo $bst_t( 'Address' ); ?>","text":"<?php echo $bst_t( '25 Main Street, City Centre' ); ?>","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#c2410c","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"clock","title":"<?php echo $bst_t( 'Opening hours' ); ?>","text":"<?php echo $bst_t( '7:00 am – 10:00 pm, every day' ); ?>","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#c2410c","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"phone","title":"<?php echo $bst_t( 'Phone' ); ?>","text":"0900 123 456","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#c2410c","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"mail","title":"<?php echo $bst_t( 'Email' ); ?>","text":"<?php echo $bst_t( 'booking@restaurant.com' ); ?>","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#c2410c","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->
