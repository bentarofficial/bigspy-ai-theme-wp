<?php
/**
 * Title: Homepage – Education centre
 * Slug: bigspy-ai-theme-wp/page-starter-education
 * Categories: bigspy-pages
 * Keywords: education, school, course, training, english, giao duc
 * Block Types: core/post-content
 * Post Types: page
 * Viewport Width: 1280
 * Description: Education centre homepage with 15 sections: hero, highlights, about, courses, learning path, numbers, method, tuition, gallery, teachers, student reviews, FAQ, news, enrolment and contact.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Translated, JSON-safe text for block attributes (English source, translations in /languages). */
$bst_t = static function ( $text ) {
	return substr( wp_json_encode( __( $text, 'bigspy-ai-theme-wp' ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ), 1, -1 ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
};
?>
<!-- wp:bst/section {"bgImage":"<?php echo esc_url( bst_pattern_image( 'starter/education-hero.svg' ) ); ?>","overlayColor":"#1e1b4b","overlayOpacity":62,"textScheme":"light","paddingDesktop":140,"paddingTablet":100,"paddingMobile":72,"minHeight":88,"minHeightUnit":"vh","align":"full"} -->
<!-- wp:bst/row {"gap":"medium","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":7,"spanTablet":12,"spanMobile":12} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Training centre · Languages · Skills' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"color":"#ffffff"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Learn for real, get really good, move on with confidence' ); ?>","tag":"h1","size":56,"sizeTablet":45,"sizeMobile":36,"bsPresets":"hd-tight2","bsAnimation":"fade-up"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Personal learning paths, experienced teachers and small classes help every student make clear progress after each course – with a written results guarantee.' ); ?>\u003c/p\u003e","size":19,"bsAnimation":"fade-up","bsAnimationDelay":100} /-->
<!-- wp:bst/icon-list {"items":[{"icon":"check","text":"<?php echo $bst_t( '2 free trial lessons' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Max 12 students per class' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Results guarantee' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Unlimited make-up lessons' ); ?>","link":""}],"layout":"grid","columns":2,"iconColor":"#ffffff","iconBg":true,"gap":12} /-->
<!-- wp:bst/buttons {"stackMobile":true} -->
<!-- wp:bst/button {"text":"<?php echo $bst_t( 'Book a free trial' ); ?>","url":"#contact","radius":10} /-->
<!-- wp:bst/button {"text":"<?php echo $bst_t( 'See courses' ); ?>","url":"#services","radius":10,"style":"white"} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":5,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-left"} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/education-about.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Learn for real, get really good, move on with confidence' ); ?>","ratio":"4-3","radius":22,"bsPresets":"img-sh3 img-r24"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e\u003cstrong\u003e<?php echo $bst_t( '96% reach their goal' ); ?>\u003c/strong\u003e\u003cbr\u003e<?php echo $bst_t( 'Out of 5,000+ graduates' ); ?>\u003c/p\u003e","bsPresets":"tx-note","bsDesign":{"mt":"-40","ml":"24","mr":"24","bg":"#ffffff","color":"#1f2937"}} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":64,"paddingTablet":72,"paddingMobile":54,"bsPresets":"sec-white sec-lb"} -->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"lightbulb","title":"<?php echo $bst_t( 'New methods' ); ?>","text":"<?php echo $bst_t( 'Learning through real projects' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#f59e0b","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"users","title":"<?php echo $bst_t( 'Small classes' ); ?>","text":"<?php echo $bst_t( 'Max 12 students' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#6366f1","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"award","title":"<?php echo $bst_t( 'Results guarantee' ); ?>","text":"<?php echo $bst_t( 'Free re-study until you pass' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#10b981","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"clock","title":"<?php echo $bst_t( 'Flexible schedule' ); ?>","text":"<?php echo $bst_t( 'Mornings, evenings and weekends' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#ec4899","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/row {"gap":"large","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":6,"spanTablet":12,"spanMobile":12,"bsAnimation":"fade-right"} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/education-about.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Where every student is treated as an individual' ); ?>","ratio":"1-1","radius":26,"bsPresets":"img-shoff img-r24"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":6,"spanTablet":12,"spanMobile":12,"bsAnimation":"fade-left"} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'About the centre' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker"} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Where every student is treated as an individual' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Founded in 2014, the centre has helped more than 5,000 students – from school pupils to working adults – master languages and skills.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Teachers follow each student closely, send regular progress reports to parents and adjust the path when needed to reach the target.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- wp:bst/icon-list {"items":[{"icon":"check","text":"<?php echo $bst_t( '40+ experienced teachers' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Native-speaker teachers' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Air-conditioned rooms with projectors' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Free library' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Online learning app included' ); ?>","link":""},{"icon":"check","text":"<?php echo $bst_t( 'Monthly progress reports' ); ?>","link":""}],"layout":"grid","columns":2,"iconColor":"#4338ca","iconBg":true,"gap":12} /-->
<!-- wp:bst/buttons {"stackMobile":true} -->
<!-- wp:bst/button {"text":"<?php echo $bst_t( 'Get advice' ); ?>","url":"#contact","radius":10} /-->
<!-- wp:bst/button {"text":"<?php echo $bst_t( 'Call us' ); ?>","url":"tel:0900456789","radius":10,"style":"outline"} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54,"anchor":"services","bsPresets":"sec-soft"} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Courses' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Featured courses' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Choose the course for your goal, with evening and weekend classes for working adults.' ); ?>\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mw":"760","mwc":true}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/education-1.svg' ) ); ?>","alt":"<?php echo $bst_t( 'English conversation' ); ?>","title":"<?php echo $bst_t( 'English conversation' ); ?>","text":"<?php echo $bst_t( 'Natural listening and speaking in 3 months with native teachers.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'See the path →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/education-2.svg' ) ); ?>","alt":"<?php echo $bst_t( 'IELTS preparation' ); ?>","title":"<?php echo $bst_t( 'IELTS preparation' ); ?>","text":"<?php echo $bst_t( 'From 4.5 to 7.0+, with detailed writing feedback every week.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'See the path →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/education-3.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Office skills' ); ?>","title":"<?php echo $bst_t( 'Office skills' ); ?>","text":"<?php echo $bst_t( 'Word, Excel and PowerPoint you can use at work right away.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'See the path →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/education-1.svg' ) ); ?>","alt":"<?php echo $bst_t( 'English for kids' ); ?>","title":"<?php echo $bst_t( 'English for kids' ); ?>","text":"<?php echo $bst_t( 'Learning through games and songs so children love English.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'See the path →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/education-2.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Presentation skills' ); ?>","title":"<?php echo $bst_t( 'Presentation skills' ); ?>","text":"<?php echo $bst_t( 'Speak confidently in public after 12 practical sessions.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'See the path →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/education-3.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Coding basics' ); ?>","title":"<?php echo $bst_t( 'Coding basics' ); ?>","text":"<?php echo $bst_t( 'Python and computational thinking for students.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'See the path →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Learning path' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( '4 steps to your goal' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Every student gets a personal path based on a placement test.' ); ?>\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mw":"760","mwc":true}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-p26 col-eq col-hls col-bdt","bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/heading {"text":"01","tag":"p","size":44,"sizeTablet":36,"sizeMobile":29,"bsDesign":{"color":"#4338ca","fw":"800"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Placement test' ); ?>","tag":"h3","size":21,"sizeTablet":17,"sizeMobile":20} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'A free 30-minute level check.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-p26 col-eq col-hls col-bdt","bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/heading {"text":"02","tag":"p","size":44,"sizeTablet":36,"sizeMobile":29,"bsDesign":{"color":"#4338ca","fw":"800"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Your learning path' ); ?>","tag":"h3","size":21,"sizeTablet":17,"sizeMobile":20} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Teachers design a path for your goal.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-p26 col-eq col-hls col-bdt","bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/heading {"text":"03","tag":"p","size":44,"sizeTablet":36,"sizeMobile":29,"bsDesign":{"color":"#4338ca","fw":"800"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Learn & practise' ); ?>","tag":"h3","size":21,"sizeTablet":17,"sizeMobile":20} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Small classes with practice in every lesson.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-p26 col-eq col-hls col-bdt","bsAnimation":"fade-up","bsAnimationDelay":300} -->
<!-- wp:bst/heading {"text":"04","tag":"p","size":44,"sizeTablet":36,"sizeMobile":29,"bsDesign":{"color":"#4338ca","fw":"800"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Review & certificate' ); ?>","tag":"h3","size":21,"sizeTablet":17,"sizeMobile":20} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Regular tests and a completion certificate.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":76,"paddingMobile":50,"textScheme":"light","style":{"color":{"background":"#4338ca"}}} -->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"5000","suffix":"+","label":"<?php echo $bst_t( 'Students' ); ?>","color":"#ffffff"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Graduated' ); ?>\u003c/p\u003e","size":14,"bsDesign":{"ta":"center","color":"rgba(255,255,255,.8)"}} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"96","suffix":"%","label":"<?php echo $bst_t( 'Reach their goal' ); ?>","color":"#ffffff"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Under our guarantee' ); ?>\u003c/p\u003e","size":14,"bsDesign":{"ta":"center","color":"rgba(255,255,255,.8)"}} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"40","suffix":"+","label":"<?php echo $bst_t( 'Teachers' ); ?>","color":"#ffffff"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Local and international' ); ?>\u003c/p\u003e","size":14,"bsDesign":{"ta":"center","color":"rgba(255,255,255,.8)"}} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"10","suffix":"","label":"<?php echo $bst_t( 'Years teaching' ); ?>","color":"#ffffff"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Since 2014' ); ?>\u003c/p\u003e","size":14,"bsDesign":{"ta":"center","color":"rgba(255,255,255,.8)"}} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Our method' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Learn better every day' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/row {"gap":"medium","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"lightbulb","title":"<?php echo $bst_t( 'Project-based' ); ?>","text":"<?php echo $bst_t( 'Apply what you learn to real situations.' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#f59e0b","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"message","title":"<?php echo $bst_t( 'Speaking practice' ); ?>","text":"<?php echo $bst_t( '70% of class time is practice.' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#6366f1","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"chart","title":"<?php echo $bst_t( 'Progress tracking' ); ?>","text":"<?php echo $bst_t( 'Scores and feedback after every lesson.' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#10b981","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":12,"spanMobile":12,"bsAnimation":"zoom-in"} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/education-2.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Learn better every day' ); ?>","ratio":"1-1","radius":200,"bsPresets":"img-circ img-sh2"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"users","title":"<?php echo $bst_t( 'Interactive classes' ); ?>","text":"<?php echo $bst_t( 'Group work, debates and role-play.' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#ec4899","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"globe","title":"<?php echo $bst_t( 'Learn anywhere' ); ?>","text":"<?php echo $bst_t( 'Watch lessons again in the online app.' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#0ea5e9","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"award","title":"<?php echo $bst_t( 'Certificates' ); ?>","text":"<?php echo $bst_t( 'A certificate when you complete the course.' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#8b5cf6","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54,"bsPresets":"sec-light"} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Tuition' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Clear tuition, plenty of offers' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( '10% off for groups of 3, and 0% instalments by credit card.' ); ?>\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mw":"760","mwc":true}} /-->
<!-- wp:bst/row {"gap":"medium","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/price-table {"title":"<?php echo $bst_t( 'Basic conversation' ); ?>","price":"<?php echo $bst_t( '$159' ); ?>","period":"<?php echo $bst_t( '/ 3 months' ); ?>","description":"<?php echo $bst_t( '24 lessons, twice a week' ); ?>","features":"<?php echo $bst_t( 'Native teachers' ); ?>\n<?php echo $bst_t( 'Exclusive materials' ); ?>\n<?php echo $bst_t( 'Online learning app' ); ?>\n<?php echo $bst_t( '-Results guarantee' ); ?>","buttonText":"<?php echo $bst_t( 'Choose this plan' ); ?>","buttonUrl":"#contact","featured":false,"badge":"<?php echo $bst_t( 'Most chosen' ); ?>","style":"card"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":120} -->
<!-- wp:bst/price-table {"title":"<?php echo $bst_t( 'IELTS guarantee' ); ?>","price":"<?php echo $bst_t( '$399' ); ?>","period":"<?php echo $bst_t( '/ course' ); ?>","description":"<?php echo $bst_t( 'Most popular' ); ?>","features":"<?php echo $bst_t( 'Band score guarantee' ); ?>\n<?php echo $bst_t( 'One-to-one writing feedback' ); ?>\n<?php echo $bst_t( 'Monthly mock tests' ); ?>\n<?php echo $bst_t( 'Free re-study' ); ?>","buttonText":"<?php echo $bst_t( 'Choose this plan' ); ?>","buttonUrl":"#contact","featured":true,"badge":"<?php echo $bst_t( 'Most chosen' ); ?>","style":"card"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":12,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":240} -->
<!-- wp:bst/price-table {"title":"<?php echo $bst_t( 'Kids' ); ?>","price":"<?php echo $bst_t( '$119' ); ?>","period":"<?php echo $bst_t( '/ 3 months' ); ?>","description":"<?php echo $bst_t( 'For ages 5–11' ); ?>","features":"<?php echo $bst_t( 'Learning through games' ); ?>\n<?php echo $bst_t( 'Max 10 children per class' ); ?>\n<?php echo $bst_t( 'Reports for parents' ); ?>\n<?php echo $bst_t( 'Free learning kit' ); ?>","buttonText":"<?php echo $bst_t( 'Choose this plan' ); ?>","buttonUrl":"#contact","featured":false,"badge":"<?php echo $bst_t( 'Most chosen' ); ?>","style":"card"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Activities' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'A day at the centre' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/row {"gap":"small"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/education-1.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/education-2.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/education-3.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/education-about.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- wp:bst/row {"gap":"small"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/education-hero.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/education-3.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/education-1.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/education-2.svg' ) ); ?>","alt":"","ratio":"1-1","radius":14,"bsPresets":"img-hz img-sh1"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54,"bsPresets":"sec-soft"} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Teachers' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Dedicated teachers' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Qualified, with many years of teaching experience.' ); ?>\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mw":"760","mwc":true}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/team-member {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-1.svg' ) ); ?>","name":"<?php echo $bst_t( 'Ms. Sarah Lewis' ); ?>","role":"<?php echo $bst_t( 'Head of IELTS' ); ?>","bio":"<?php echo $bst_t( 'IELTS 8.5, 10 years of exam coaching.' ); ?>","style":"card","facebook":"#","zalo":"#","bsPresets":"tm-r20 tm-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/team-member {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-2.svg' ) ); ?>","name":"Mr. David Brown","role":"<?php echo $bst_t( 'Native teacher' ); ?>","bio":"<?php echo $bst_t( 'TESOL certified, 8 years of teaching.' ); ?>","style":"card","facebook":"#","zalo":"#","bsPresets":"tm-r20 tm-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/team-member {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-3.svg' ) ); ?>","name":"<?php echo $bst_t( 'Ms. Amy Clarke' ); ?>","role":"<?php echo $bst_t( 'Kids teacher' ); ?>","bio":"<?php echo $bst_t( 'Loves children and makes every lesson creative.' ); ?>","style":"card","facebook":"#","zalo":"#","bsPresets":"tm-r20 tm-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":300} -->
<!-- wp:bst/team-member {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-4.svg' ) ); ?>","name":"<?php echo $bst_t( 'Mr. Peter Hall' ); ?>","role":"<?php echo $bst_t( 'IT lecturer' ); ?>","bio":"<?php echo $bst_t( 'Software engineer with 6 years of teaching.' ); ?>","style":"card","facebook":"#","zalo":"#","bsPresets":"tm-r20 tm-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Customer reviews' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'What our students say' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/testimonial {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-1.svg' ) ); ?>","name":"<?php echo $bst_t( 'Jessica' ); ?>","role":"<?php echo $bst_t( 'IELTS 7.0 student' ); ?>","text":"<?php echo $bst_t( 'A clear path and very detailed feedback. I went up 1.5 bands in just 4 months.' ); ?>","bsPresets":"ts-wc ts-r20 ts-sh1 ts-q"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":120} -->
<!-- wp:bst/testimonial {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-2.svg' ) ); ?>","name":"<?php echo $bst_t( 'Michael' ); ?>","role":"<?php echo $bst_t( 'Third-year student' ); ?>","text":"<?php echo $bst_t( 'Small classes mean I speak a lot – now I present with much more confidence.' ); ?>","bsPresets":"ts-wc ts-r20 ts-sh1 ts-q"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":240} -->
<!-- wp:bst/testimonial {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-3.svg' ) ); ?>","name":"<?php echo $bst_t( 'Linda' ); ?>","role":"<?php echo $bst_t( 'Parent' ); ?>","text":"<?php echo $bst_t( 'My child loves going to class, and I get a detailed progress report every week.' ); ?>","bsPresets":"ts-wc ts-r20 ts-sh1 ts-q"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54,"bsPresets":"sec-light"} -->
<!-- wp:bst/row {"gap":"large"} -->
<!-- wp:bst/column {"spanDesktop":5,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'FAQ' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker"} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Frequently asked questions' ); ?>","tag":"h2","size":38,"sizeTablet":31,"sizeMobile":25,"bsPresets":"hd-tight2"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Still have questions? Call us for a free learning-path consultation.' ); ?>\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- wp:bst/icon-box {"icon":"phone","title":"<?php echo $bst_t( 'Hotline' ); ?>","text":"0900 456 789","iconStyle":"square","iconColor":"#ffffff","iconBg":"#4338ca","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"message","title":"<?php echo $bst_t( 'Chat / Messenger' ); ?>","text":"<?php echo $bst_t( 'Reply within 5 minutes in office hours' ); ?>","iconStyle":"square","iconColor":"#ffffff","iconBg":"#4338ca","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":7,"spanTablet":12,"spanMobile":12} -->
<!-- wp:bst/accordion {"bsPresets":"acc-cards acc-abg","titleTag":"h3"} -->
<!-- wp:bst/accordion-item {"title":"<?php echo $bst_t( 'Can I try a lesson first?' ); ?>","open":true} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Yes, you get 2 free trial lessons before you decide to enrol.' ); ?>\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"<?php echo $bst_t( 'What is the timetable?' ); ?>","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Morning and evening classes on weekdays plus weekend classes – choose what fits you.' ); ?>\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"<?php echo $bst_t( 'What if I don’t reach my goal?' ); ?>","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'We guarantee free re-study until you reach the agreed goal.' ); ?>\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"<?php echo $bst_t( 'Can I make up missed lessons?' ); ?>","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Yes, unlimited make-up lessons in classes at the same level.' ); ?>\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"<?php echo $bst_t( 'Can I pay in instalments?' ); ?>","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Yes, 0% instalments by credit card or two payments.' ); ?>\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- /wp:bst/accordion -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'News & tips' ); ?>\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Study corner' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/education-1.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Self-study plan for IELTS 6.5' ); ?>","title":"<?php echo $bst_t( 'Self-study plan for IELTS 6.5' ); ?>","text":"<?php echo $bst_t( 'A 6-month plan with free materials and how to train each skill.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Read more →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/education-2.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Remember vocabulary for longer' ); ?>","title":"<?php echo $bst_t( 'Remember vocabulary for longer' ); ?>","text":"<?php echo $bst_t( 'Spaced repetition to memorise words effectively.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Read more →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/education-3.svg' ) ); ?>","alt":"<?php echo $bst_t( 'Choosing a course for your child' ); ?>","title":"<?php echo $bst_t( 'Choosing a course for your child' ); ?>","text":"<?php echo $bst_t( 'What parents should consider when picking an English class.' ); ?>","link":"#contact","linkText":"<?php echo $bst_t( 'Read more →' ); ?>","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":84,"paddingMobile":56,"anchor":"contact","textScheme":"light","bsPresets":"sec-brand sec-r24"} -->
<!-- wp:bst/row {"gap":"medium","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":8,"spanTablet":12,"spanMobile":12} -->
<!-- wp:bst/heading {"text":"<?php echo $bst_t( 'Start your learning journey today' ); ?>","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e<?php echo $bst_t( 'Book 2 free trial lessons and get a learning-path consultation within 24 hours.' ); ?>\u003c/p\u003e","size":18} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/buttons {"stackMobile":true,"align":"right"} -->
<!-- wp:bst/button {"text":"<?php echo $bst_t( 'Book a free trial' ); ?>","url":"tel:0900456789","radius":10,"style":"white"} /-->
<!-- wp:bst/button {"text":"<?php echo $bst_t( 'Message us' ); ?>","url":"https://zalo.me/0900456789","radius":10,"style":"outline"} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":70,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"pin","title":"<?php echo $bst_t( 'Address' ); ?>","text":"<?php echo $bst_t( '45 College Road, City Centre' ); ?>","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#4338ca","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"clock","title":"<?php echo $bst_t( 'Opening hours' ); ?>","text":"<?php echo $bst_t( '8:00 am – 9:00 pm, Monday – Sunday' ); ?>","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#4338ca","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"phone","title":"<?php echo $bst_t( 'Phone' ); ?>","text":"0900 456 789","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#4338ca","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"mail","title":"<?php echo $bst_t( 'Email' ); ?>","text":"<?php echo $bst_t( 'admissions@academy.edu' ); ?>","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#4338ca","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->
