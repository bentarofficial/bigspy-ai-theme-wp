<?php
/**
 * Title: Trang chủ – Nhà hàng & cà phê
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
?>
<!-- wp:bst/section {"bgImage":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-hero.svg' ) ); ?>","overlayColor":"#1c0d07","overlayOpacity":62,"textScheme":"light","paddingDesktop":140,"paddingTablet":100,"paddingMobile":72,"minHeight":88,"minHeightUnit":"vh","align":"full"} -->
<!-- wp:bst/row {"gap":"medium","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":7,"spanTablet":12,"spanMobile":12} -->
<!-- wp:bst/text {"html":"\u003cp\u003eNhà hàng · Cà phê · Tiệc\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"color":"#ffffff"}} /-->
<!-- wp:bst/heading {"text":"Hương vị tươi ngon, phục vụ bằng cả trái tim","tag":"h1","size":56,"sizeTablet":45,"sizeMobile":36,"bsPresets":"hd-tight2","bsAnimation":"fade-up"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eNguyên liệu chọn mỗi sáng, đầu bếp hơn 12 năm kinh nghiệm và không gian ấm cúng – nơi lý tưởng để hẹn hò, gặp gỡ đối tác hay sum họp gia đình cuối tuần.\u003c/p\u003e","size":19,"bsAnimation":"fade-up","bsAnimationDelay":100} /-->
<!-- wp:bst/icon-list {"items":[{"icon":"check","text":"Nguyên liệu tươi mỗi ngày","link":""},{"icon":"check","text":"Phòng riêng cho tiệc","link":""},{"icon":"check","text":"Giao tận nơi trong 30 phút","link":""},{"icon":"check","text":"Bãi đỗ xe miễn phí","link":""}],"layout":"grid","columns":2,"iconColor":"#ffffff","iconBg":true,"gap":12} /-->
<!-- wp:bst/buttons {"stackMobile":true} -->
<!-- wp:bst/button {"text":"Đặt bàn ngay","url":"#contact","radius":10} /-->
<!-- wp:bst/button {"text":"Xem thực đơn","url":"#services","radius":10,"style":"white"} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":5,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-left"} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-about.svg' ) ); ?>","alt":"Hương vị tươi ngon, phục vụ bằng cả trái tim","ratio":"4-3","radius":22,"bsPresets":"img-sh3 img-r24"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e\u003cstrong\u003e4.9 ★ trên Google\u003c/strong\u003e\u003cbr\u003eHơn 2.300 đánh giá từ thực khách\u003c/p\u003e","bsPresets":"tx-note","bsDesign":{"mt":"-40","ml":"24","mr":"24","bg":"#ffffff","color":"#1f2937"}} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":64,"paddingTablet":72,"paddingMobile":54,"bsPresets":"sec-white sec-lb"} -->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"clock","title":"Mở cửa cả tuần","text":"07:00 – 22:00 kể cả lễ Tết","iconStyle":"square","iconColor":"#ffffff","iconBg":"#c2410c","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"truck","title":"Giao hàng nhanh","text":"Miễn phí đơn từ 300.000đ","iconStyle":"square","iconColor":"#ffffff","iconBg":"#ea580c","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"users","title":"Tiệc 10–120 khách","text":"Sinh nhật, liên hoan, hội nghị","iconStyle":"square","iconColor":"#ffffff","iconBg":"#b45309","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"award","title":"Top 10 quán ngon","text":"Do cộng đồng ẩm thực bình chọn","iconStyle":"square","iconColor":"#ffffff","iconBg":"#92400e","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/row {"gap":"large","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":6,"spanTablet":12,"spanMobile":12,"bsAnimation":"fade-right"} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-about.svg' ) ); ?>","alt":"Từ căn bếp gia đình đến nhà hàng được yêu thích","ratio":"1-1","radius":26,"bsPresets":"img-shoff img-r24"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":6,"spanTablet":12,"spanMobile":12,"bsAnimation":"fade-left"} -->
<!-- wp:bst/text {"html":"\u003cp\u003eCâu chuyện của chúng tôi\u003c/p\u003e","bsPresets":"tx-kicker"} /-->
<!-- wp:bst/heading {"text":"Từ căn bếp gia đình đến nhà hàng được yêu thích","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eNăm 2012, chúng tôi bắt đầu từ một căn bếp nhỏ với 6 chiếc bàn. Điều giữ chân thực khách không phải món ăn cầu kỳ mà là hương vị quen thuộc, nấu bằng nguyên liệu tốt nhất và sự chu đáo trong từng chi tiết.\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eHôm nay, nhà hàng có hơn 120 chỗ ngồi, 3 phòng riêng và đội ngũ 40 nhân viên – nhưng tinh thần vẫn như ngày đầu: phục vụ mỗi vị khách như người nhà.\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- wp:bst/icon-list {"items":[{"icon":"check","text":"Đầu bếp chính 12 năm kinh nghiệm","link":""},{"icon":"check","text":"Thực đơn 80+ món Á – Âu","link":""},{"icon":"check","text":"Rau sạch từ trang trại riêng","link":""},{"icon":"check","text":"Không dùng bột ngọt công nghiệp","link":""},{"icon":"check","text":"Không gian sân vườn thoáng mát","link":""},{"icon":"check","text":"Nhận đặt tiệc theo yêu cầu","link":""}],"layout":"grid","columns":2,"iconColor":"#c2410c","iconBg":true,"gap":12} /-->
<!-- wp:bst/buttons {"stackMobile":true} -->
<!-- wp:bst/button {"text":"Xem thực đơn đầy đủ","url":"#contact","radius":10} /-->
<!-- wp:bst/button {"text":"Gọi tư vấn","url":"tel:0900123456","radius":10,"style":"outline"} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54,"anchor":"services","bsPresets":"sec-soft"} -->
<!-- wp:bst/text {"html":"\u003cp\u003eThực đơn nổi bật\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"Món ngon được gọi nhiều nhất","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eThực đơn thay đổi theo mùa để luôn giữ trọn vị tươi ngon. Dưới đây là những món thực khách yêu thích nhất.\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mw":"760","mwc":true}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-1.svg' ) ); ?>","alt":"Bò sốt tiêu đen","title":"Bò sốt tiêu đen","text":"Thăn bò Úc mềm, sốt tiêu đen đậm đà, ăn kèm khoai tây nướng bơ tỏi.","link":"#contact","linkText":"Đặt món →","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-2.svg' ) ); ?>","alt":"Cà phê rang xay","title":"Cà phê rang xay","text":"Hạt Arabica Cầu Đất rang mộc mỗi tuần, pha phin hoặc espresso tuỳ thích.","link":"#contact","linkText":"Xem đồ uống →","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-3.svg' ) ); ?>","alt":"Bánh ngọt trong ngày","title":"Bánh ngọt trong ngày","text":"Tiramisu, cheesecake chanh dây và bánh mousse làm mới mỗi sáng.","link":"#contact","linkText":"Xem tráng miệng →","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-1.svg' ) ); ?>","alt":"Cơm niêu cá kho","title":"Cơm niêu cá kho","text":"Cá kho tộ, canh chua và cơm niêu cháy giòn chuẩn vị miền Tây.","link":"#contact","linkText":"Đặt món →","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-3.svg' ) ); ?>","alt":"Lẩu hải sản","title":"Lẩu hải sản","text":"Tôm, mực, nghêu tươi sống cùng nước dùng chua cay đặc biệt.","link":"#contact","linkText":"Đặt món →","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-2.svg' ) ); ?>","alt":"Trà trái cây","title":"Trà trái cây","text":"Trà đào cam sả, trà vải hoa hồng mát lạnh cho ngày nắng.","link":"#contact","linkText":"Xem đồ uống →","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003eĐặt bàn dễ dàng\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"Chỉ 4 bước để có bữa ăn hoàn hảo","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eĐặt bàn qua điện thoại, Zalo hoặc website – nhân viên xác nhận trong vòng 5 phút.\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mw":"760","mwc":true}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-p26 col-eq col-hls col-bdt","bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/heading {"text":"01","tag":"p","size":44,"sizeTablet":36,"sizeMobile":29,"bsDesign":{"color":"#c2410c","fw":"800"}} /-->
<!-- wp:bst/heading {"text":"Chọn thời gian","tag":"h3","size":21,"sizeTablet":17,"sizeMobile":20} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eBáo ngày giờ và số lượng khách qua hotline hoặc Zalo.\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-p26 col-eq col-hls col-bdt","bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/heading {"text":"02","tag":"p","size":44,"sizeTablet":36,"sizeMobile":29,"bsDesign":{"color":"#c2410c","fw":"800"}} /-->
<!-- wp:bst/heading {"text":"Chọn khu vực","tag":"h3","size":21,"sizeTablet":17,"sizeMobile":20} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eSảnh chung, sân vườn hay phòng riêng tuỳ dịp.\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-p26 col-eq col-hls col-bdt","bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/heading {"text":"03","tag":"p","size":44,"sizeTablet":36,"sizeMobile":29,"bsDesign":{"color":"#c2410c","fw":"800"}} /-->
<!-- wp:bst/heading {"text":"Đặt món trước","tag":"h3","size":21,"sizeTablet":17,"sizeMobile":20} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eGọi trước món để không phải chờ lâu khi đến.\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-p26 col-eq col-hls col-bdt","bsAnimation":"fade-up","bsAnimationDelay":300} -->
<!-- wp:bst/heading {"text":"04","tag":"p","size":44,"sizeTablet":36,"sizeMobile":29,"bsDesign":{"color":"#c2410c","fw":"800"}} /-->
<!-- wp:bst/heading {"text":"Thưởng thức","tag":"h3","size":21,"sizeTablet":17,"sizeMobile":20} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eĐến đúng giờ, bàn và món đã sẵn sàng phục vụ.\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":76,"paddingMobile":50,"textScheme":"light","style":{"color":{"background":"#c2410c"}}} -->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"12","suffix":"+","label":"Năm kinh nghiệm","color":"#ffffff"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003ePhục vụ từ năm 2012\u003c/p\u003e","size":14,"bsDesign":{"ta":"center","color":"rgba(255,255,255,.8)"}} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"80","suffix":"+","label":"Món trong thực đơn","color":"#ffffff"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eĐổi mới theo mùa\u003c/p\u003e","size":14,"bsDesign":{"ta":"center","color":"rgba(255,255,255,.8)"}} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"25","suffix":"k","label":"Thực khách mỗi năm","color":"#ffffff"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eVà con số vẫn tăng\u003c/p\u003e","size":14,"bsDesign":{"ta":"center","color":"rgba(255,255,255,.8)"}} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"120","suffix":"","label":"Chỗ ngồi","color":"#ffffff"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eKèm 3 phòng riêng\u003c/p\u003e","size":14,"bsDesign":{"ta":"center","color":"rgba(255,255,255,.8)"}} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003eVì sao thực khách quay lại\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"Trải nghiệm ẩm thực trọn vẹn","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/row {"gap":"medium","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"leaf","title":"Nguyên liệu sạch","text":"Rau củ từ trang trại riêng, thịt cá có nguồn gốc rõ ràng.","iconStyle":"square","iconColor":"#ffffff","iconBg":"#16a34a","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"star","title":"Công thức riêng","text":"Nước sốt và gia vị được bếp trưởng tự pha chế.","iconStyle":"square","iconColor":"#ffffff","iconBg":"#ea580c","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"heart","title":"Phục vụ tận tâm","text":"Nhân viên được đào tạo bài bản, luôn niềm nở.","iconStyle":"square","iconColor":"#ffffff","iconBg":"#db2777","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":12,"spanMobile":12,"bsAnimation":"zoom-in"} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-2.svg' ) ); ?>","alt":"Trải nghiệm ẩm thực trọn vẹn","ratio":"1-1","radius":200,"bsPresets":"img-circ img-sh2"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"clock","title":"Lên món nhanh","text":"Cam kết lên món trong 15 phút giờ cao điểm.","iconStyle":"square","iconColor":"#ffffff","iconBg":"#2563eb","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"shield","title":"An toàn thực phẩm","text":"Bếp đạt chứng nhận vệ sinh an toàn thực phẩm.","iconStyle":"square","iconColor":"#ffffff","iconBg":"#0d9488","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"gift","title":"Ưu đãi thành viên","text":"Tích điểm đổi quà, giảm 10% cho khách thân thiết.","iconStyle":"square","iconColor":"#ffffff","iconBg":"#9333ea","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54,"bsPresets":"sec-light"} -->
<!-- wp:bst/text {"html":"\u003cp\u003eSet menu\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"Thực đơn trọn gói cho mọi dịp","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eGiá đã bao gồm VAT và phục vụ. Có thể thay đổi món theo khẩu vị.\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mw":"760","mwc":true}} /-->
<!-- wp:bst/row {"gap":"medium","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/price-table {"title":"Set gia đình","price":"890.000đ","period":"/ 4 người","description":"Bữa tối ấm cúng cuối tuần","features":"5 món chính + canh\nTráng miệng theo mùa\nNước uống miễn phí\n-Phòng riêng","buttonText":"Chọn gói này","buttonUrl":"#contact","featured":false,"badge":"Được chọn nhiều","style":"card"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":120} -->
<!-- wp:bst/price-table {"title":"Set liên hoan","price":"2.490.000đ","period":"/ 10 người","description":"Lựa chọn phổ biến nhất","features":"8 món chính + lẩu\nBia hoặc nước ngọt\nPhòng riêng có karaoke\nTrang trí theo chủ đề","buttonText":"Chọn gói này","buttonUrl":"#contact","featured":true,"badge":"Được chọn nhiều","style":"card"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":12,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":240} -->
<!-- wp:bst/price-table {"title":"Set hội nghị","price":"350.000đ","period":"/ khách","description":"Từ 30 khách trở lên","features":"Buffet Á – Âu 30 món\nMáy chiếu, âm thanh\nTea-break 2 buổi\nHỗ trợ MC, lễ tân","buttonText":"Chọn gói này","buttonUrl":"#contact","featured":false,"badge":"Được chọn nhiều","style":"card"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003eKhông gian nhà hàng\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"Ấm cúng, sang trọng và gần gũi","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
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
<!-- wp:bst/text {"html":"\u003cp\u003eĐội ngũ\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"Những người đứng sau món ngon","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eĐầu bếp và quản lý giàu kinh nghiệm, luôn chăm chút từng đĩa ăn.\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mw":"760","mwc":true}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/team-member {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-1.svg' ) ); ?>","name":"Trần Minh Đức","role":"Bếp trưởng","bio":"15 năm kinh nghiệm tại các khách sạn 5 sao.","style":"card","facebook":"#","zalo":"#","bsPresets":"tm-r20 tm-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/team-member {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-2.svg' ) ); ?>","name":"Lê Thu Hằng","role":"Bếp bánh","bio":"Tốt nghiệp chuyên ngành làm bánh tại Pháp.","style":"card","facebook":"#","zalo":"#","bsPresets":"tm-r20 tm-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/team-member {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-3.svg' ) ); ?>","name":"Phạm Quang Huy","role":"Pha chế","bio":"Barista đạt giải cuộc thi cà phê toàn quốc.","style":"card","facebook":"#","zalo":"#","bsPresets":"tm-r20 tm-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":300} -->
<!-- wp:bst/team-member {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-4.svg' ) ); ?>","name":"Nguyễn Thanh Mai","role":"Quản lý","bio":"Luôn lắng nghe để mỗi bữa ăn trọn vẹn hơn.","style":"card","facebook":"#","zalo":"#","bsPresets":"tm-r20 tm-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003eCảm nhận khách hàng\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"Thực khách nói gì về chúng tôi","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/testimonial {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-1.svg' ) ); ?>","name":"Minh Anh","role":"Khách quen 3 năm","text":"Món ăn ngon, trình bày đẹp và nhân viên rất nhiệt tình. Cuối tuần nào gia đình mình cũng ghé.","bsPresets":"ts-wc ts-r20 ts-sh1 ts-q"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":120} -->
<!-- wp:bst/testimonial {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-2.svg' ) ); ?>","name":"Thu Hà","role":"Nhân viên văn phòng","text":"Cà phê thơm, không gian yên tĩnh. Mình hay mang laptop đến làm việc buổi chiều.","bsPresets":"ts-wc ts-r20 ts-sh1 ts-q"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":240} -->
<!-- wp:bst/testimonial {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-3.svg' ) ); ?>","name":"Quốc Bảo","role":"Giám đốc công ty","text":"Tổ chức tiệc tất niên 80 người rất chuyên nghiệp, món nào cũng hết sạch.","bsPresets":"ts-wc ts-r20 ts-sh1 ts-q"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54,"bsPresets":"sec-light"} -->
<!-- wp:bst/row {"gap":"large"} -->
<!-- wp:bst/column {"spanDesktop":5,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/text {"html":"\u003cp\u003eHỏi đáp\u003c/p\u003e","bsPresets":"tx-kicker"} /-->
<!-- wp:bst/heading {"text":"Câu hỏi thường gặp","tag":"h2","size":38,"sizeTablet":31,"sizeMobile":25,"bsPresets":"hd-tight2"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eKhông tìm thấy câu trả lời? Gọi hotline hoặc nhắn Zalo, chúng tôi luôn sẵn lòng hỗ trợ.\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- wp:bst/icon-box {"icon":"phone","title":"Hotline","text":"0900 123 456","iconStyle":"square","iconColor":"#ffffff","iconBg":"#c2410c","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"message","title":"Zalo / Messenger","text":"Phản hồi trong 5 phút giờ làm việc","iconStyle":"square","iconColor":"#ffffff","iconBg":"#c2410c","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":7,"spanTablet":12,"spanMobile":12} -->
<!-- wp:bst/accordion {"bsPresets":"acc-cards acc-abg","titleTag":"h3"} -->
<!-- wp:bst/accordion-item {"title":"Nhà hàng mở cửa lúc mấy giờ?","open":true} -->
<!-- wp:bst/text {"html":"\u003cp\u003eMở cửa từ 07:00 đến 22:00 tất cả các ngày trong tuần, kể cả ngày lễ.\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"Có cần đặt bàn trước không?","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003eNên đặt trước vào tối thứ Sáu đến Chủ nhật để có chỗ đẹp. Ngày thường bạn có thể đến trực tiếp.\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"Có giao hàng tận nơi không?","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003eCó, giao trong bán kính 5 km, miễn phí cho đơn từ 300.000đ.\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"Nhà hàng có món chay không?","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003eCó thực đơn chay hơn 15 món, bạn báo trước để bếp chuẩn bị riêng.\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"Có xuất hoá đơn VAT không?","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003eCó xuất hoá đơn điện tử cho cá nhân và doanh nghiệp.\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- /wp:bst/accordion -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003eTin tức \u0026 chia sẻ\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"Câu chuyện từ căn bếp","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-1.svg' ) ); ?>","alt":"Bí quyết chọn thịt bò ngon","title":"Bí quyết chọn thịt bò ngon","text":"Cách nhận biết thớ thịt, màu sắc và độ tươi để món bò luôn mềm ngọt.","link":"#contact","linkText":"Đọc tiếp →","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-2.svg' ) ); ?>","alt":"Pha cà phê phin đúng chuẩn","title":"Pha cà phê phin đúng chuẩn","text":"Tỷ lệ cà phê, nhiệt độ nước và thời gian ủ để có ly cà phê thơm đậm.","link":"#contact","linkText":"Đọc tiếp →","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-3.svg' ) ); ?>","alt":"Thực đơn mùa thu mới","title":"Thực đơn mùa thu mới","text":"Giới thiệu 8 món mới lấy cảm hứng từ nông sản mùa thu.","link":"#contact","linkText":"Đọc tiếp →","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":84,"paddingMobile":56,"anchor":"contact","textScheme":"light","bsPresets":"sec-brand sec-r24"} -->
<!-- wp:bst/row {"gap":"medium","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":8,"spanTablet":12,"spanMobile":12} -->
<!-- wp:bst/heading {"text":"Đặt bàn cho tối nay?","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eGọi ngay để giữ chỗ đẹp nhất, đặt tiệc cho nhóm hoặc gọi món mang về.\u003c/p\u003e","size":18} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/buttons {"stackMobile":true,"align":"right"} -->
<!-- wp:bst/button {"text":"Đặt bàn ngay","url":"tel:0900123456","radius":10,"style":"white"} /-->
<!-- wp:bst/button {"text":"Nhắn Zalo","url":"https://zalo.me/0900123456","radius":10,"style":"outline"} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":70,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"pin","title":"Địa chỉ","text":"25 Lê Lợi, Quận 1, TP.HCM","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#c2410c","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"clock","title":"Giờ mở cửa","text":"07:00 – 22:00 tất cả các ngày","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#c2410c","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"phone","title":"Điện thoại","text":"0900 123 456","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#c2410c","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"mail","title":"Email","text":"datban@nhahang.vn","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#c2410c","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->
