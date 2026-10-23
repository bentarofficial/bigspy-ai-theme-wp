<?php
/**
 * Title: Trang chủ – Nhà hàng & cà phê
 * Slug: bigspy-ai-theme-wp/page-starter-restaurant
 * Categories: bigspy-pages
 * Keywords: restaurant, cafe, food, menu, nha hang, ca phe
 * Block Types: core/post-content
 * Post Types: page
 * Viewport Width: 1280
 * Description: Restaurant / café homepage: hero, specials, menu highlights, story, numbers, reviews, FAQ and booking call to action.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:bst/section {"bgImage":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-hero.svg' ) ); ?>","overlayColor":"#1c0d07","overlayOpacity":55,"textScheme":"light","paddingDesktop":150,"paddingTablet":110,"paddingMobile":80,"minHeight":82,"minHeightUnit":"vh","align":"full"} -->
<!-- wp:bst/row {"verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":7,"spanTablet":12,"spanMobile":12} -->
<!-- wp:bst/text {"html":"\u003cp\u003eNhà hàng · Cà phê\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"color":"#ffffff"}} /-->
<!-- wp:bst/heading {"text":"Hương vị tươi ngon cho mọi bữa ăn","tag":"h1","size":58,"sizeTablet":47,"sizeMobile":40,"bsPresets":"hd-tight2","bsAnimation":"fade-up"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eNguyên liệu chọn mỗi sáng, đầu bếp giàu kinh nghiệm và không gian ấm cúng để bạn hẹn hò, gặp gỡ đối tác hay sum họp gia đình.\u003c/p\u003e","size":19,"bsAnimation":"fade-up","bsAnimationDelay":120} /-->
<!-- wp:bst/buttons {"stackMobile":true} -->
<!-- wp:bst/button {"text":"Đặt bàn ngay","url":"#contact","radius":10} /-->
<!-- wp:bst/button {"text":"Xem thực đơn","url":"#services","radius":10,"style":"white"} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":90,"paddingTablet":66,"paddingMobile":50,"bsPresets":"sec-soft"} -->
<!-- wp:bst/text {"html":"\u003cp\u003eVì sao chọn chúng tôi\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"Trải nghiệm ẩm thực trọn vẹn","tag":"h2","size":38,"sizeTablet":31,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eTừ món ăn đến cách phục vụ, mọi thứ đều được chăm chút để bạn muốn quay lại.\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mwc":true,"mw":"720"}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/icon-box {"icon":"star","title":"Món đặc trưng","text":"Công thức riêng được thực khách yêu thích.","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#ea580c","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/icon-box {"icon":"leaf","title":"Nguyên liệu tươi","text":"Rau, thịt, hải sản nhập mỗi sáng.","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#16a34a","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/icon-box {"icon":"clock","title":"Phục vụ nhanh","text":"Lên món trong 15 phút giờ cao điểm.","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#2563eb","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":300} -->
<!-- wp:bst/icon-box {"icon":"truck","title":"Giao tận nơi","text":"Giao nóng hổi trong bán kính 5 km.","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#db2777","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":70,"paddingMobile":54,"anchor":"services"} -->
<!-- wp:bst/text {"html":"\u003cp\u003eThực đơn nổi bật\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"Món ngon được gọi nhiều nhất","tag":"h2","size":38,"sizeTablet":31,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eThực đơn thay đổi theo mùa để giữ trọn vị tươi ngon.\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mwc":true,"mw":"720"}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-sh1 col-eq col-hls col-pm","bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-1.svg' ) ); ?>","alt":"Món chính","ratio":"4-3","radius":14,"bsPresets":"img-hz"} /-->
<!-- wp:bst/heading {"text":"Món chính","tag":"h3","size":22,"sizeTablet":18,"sizeMobile":15} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eBò sốt tiêu đen, cá hồi áp chảo, cơm niêu… đậm đà, đầy đặn.\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- wp:bst/buttons {"stackMobile":true} -->
<!-- wp:bst/button {"text":"Xem món chính →","url":"#contact","radius":10,"style":"link"} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-sh1 col-eq col-hls col-pm","bsAnimation":"fade-up","bsAnimationDelay":120} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-2.svg' ) ); ?>","alt":"Cà phê \u0026 đồ uống","ratio":"4-3","radius":14,"bsPresets":"img-hz"} /-->
<!-- wp:bst/heading {"text":"Cà phê \u0026 đồ uống","tag":"h3","size":22,"sizeTablet":18,"sizeMobile":15} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eCà phê rang xay tại chỗ, trà trái cây và sinh tố mát lành.\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- wp:bst/buttons {"stackMobile":true} -->
<!-- wp:bst/button {"text":"Xem đồ uống →","url":"#contact","radius":10,"style":"link"} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-sh1 col-eq col-hls col-pm","bsAnimation":"fade-up","bsAnimationDelay":240} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-3.svg' ) ); ?>","alt":"Tráng miệng","ratio":"4-3","radius":14,"bsPresets":"img-hz"} /-->
<!-- wp:bst/heading {"text":"Tráng miệng","tag":"h3","size":22,"sizeTablet":18,"sizeMobile":15} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eBánh ngọt làm trong ngày, kem và chè theo mùa.\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- wp:bst/buttons {"stackMobile":true} -->
<!-- wp:bst/button {"text":"Xem tráng miệng →","url":"#contact","radius":10,"style":"link"} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":100,"paddingTablet":70,"paddingMobile":54} -->
<!-- wp:bst/row {"gap":"large","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":6,"spanTablet":12,"spanMobile":12,"bsAnimation":"fade-right"} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/restaurant-about.svg' ) ); ?>","alt":"Từ bếp gia đình đến nhà hàng được yêu thích","ratio":"1-1","radius":24,"bsPresets":"img-hz"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":6,"spanTablet":12,"spanMobile":12,"bsAnimation":"fade-left"} -->
<!-- wp:bst/text {"html":"\u003cp\u003eCâu chuyện của chúng tôi\u003c/p\u003e","bsPresets":"tx-kicker"} /-->
<!-- wp:bst/heading {"text":"Từ bếp gia đình đến nhà hàng được yêu thích","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":28,"bsPresets":"hd-tight2"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eBắt đầu từ một căn bếp nhỏ, chúng tôi giữ nguyên tinh thần nấu bằng cái tâm và nguyên liệu tốt nhất.\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- wp:bst/text {"html":"\u003cul\u003e\u003cli\u003eKhông gian 120 chỗ, có phòng riêng\u003c/li\u003e\u003cli\u003eNhận tiệc sinh nhật, liên hoan công ty\u003c/li\u003e\u003cli\u003eBãi đỗ xe rộng rãi\u003c/li\u003e\u003c/ul\u003e","bsPresets":"tx-airy"} /-->
<!-- wp:bst/buttons {"stackMobile":true} -->
<!-- wp:bst/button {"text":"Đặt tiệc","url":"#contact","radius":10} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":70,"paddingMobile":46,"textScheme":"light","style":{"color":{"background":"#7c2d12"}}} -->
<!-- wp:bst/row -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"12","suffix":"+","label":"Năm kinh nghiệm","color":"#ffffff"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"80","suffix":"+","label":"Món trong thực đơn","color":"#ffffff"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"25","suffix":"k","label":"Thực khách mỗi năm","color":"#ffffff"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"4.9","suffix":"★","label":"Đánh giá trung bình","color":"#ffffff"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":70,"paddingMobile":54,"bsPresets":"sec-light"} -->
<!-- wp:bst/text {"html":"\u003cp\u003eKhách hàng nói gì\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"Được thực khách tin yêu","tag":"h2","size":38,"sizeTablet":31,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mwc":true,"mw":"720"}} /-->
<!-- wp:bst/slider {"slidesDesktop":3,"slidesTablet":2,"slidesMobile":1,"gap":24,"autoplay":6,"dots":true} -->
<!-- wp:bst/testimonial {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-1.svg' ) ); ?>","name":"Minh Anh","role":"Khách quen","text":"Món ăn ngon, trình bày đẹp và nhân viên rất nhiệt tình. Sẽ quay lại cùng gia đình!"} /-->
<!-- wp:bst/testimonial {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-2.svg' ) ); ?>","name":"Thu Hà","role":"Nhân viên văn phòng","text":"Cà phê thơm, không gian yên tĩnh để làm việc buổi chiều."} /-->
<!-- wp:bst/testimonial {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-3.svg' ) ); ?>","name":"Quốc Bảo","role":"Chủ doanh nghiệp","text":"Tổ chức tiệc công ty ở đây rất chuyên nghiệp, món nào cũng hết sạch."} /-->
<!-- wp:bst/testimonial {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-4.svg' ) ); ?>","name":"Lan Phương","role":"Food blogger","text":"Món tráng miệng là điểm cộng lớn, giá hợp lý so với chất lượng."} /-->
<!-- /wp:bst/slider -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":90,"paddingTablet":66,"paddingMobile":50} -->
<!-- wp:bst/text {"html":"\u003cp\u003eHỏi đáp\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"Câu hỏi thường gặp","tag":"h2","size":38,"sizeTablet":31,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mwc":true,"mw":"720"}} /-->
<!-- wp:bst/accordion {"bsPresets":"acc-cards acc-abg acc-wc","titleTag":"h3"} -->
<!-- wp:bst/accordion-item {"title":"Nhà hàng mở cửa lúc mấy giờ?","open":true} -->
<!-- wp:bst/text {"html":"\u003cp\u003eMở cửa từ 7:00 đến 22:00 tất cả các ngày trong tuần.\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"Có nhận đặt bàn trước không?","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003eCó, bạn gọi hotline hoặc nhắn tin để giữ bàn, đặc biệt vào cuối tuần.\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"Có giao hàng không?","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003eCó giao tận nơi trong bán kính 5 km, miễn phí cho đơn từ 300.000đ.\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- /wp:bst/accordion -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":80,"paddingMobile":54,"anchor":"contact","textScheme":"light","style":{"color":{"background":"#c2410c"}}} -->
<!-- wp:bst/row {"verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":8,"spanTablet":12,"spanMobile":12} -->
<!-- wp:bst/heading {"text":"Đặt bàn cho tối nay?","tag":"h2","size":38,"sizeTablet":31,"sizeMobile":26,"bsPresets":"hd-tight2"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eGọi ngay để giữ chỗ đẹp nhất hoặc đặt tiệc cho nhóm của bạn.\u003c/p\u003e","size":18} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":12,"spanMobile":12} -->
<!-- wp:bst/buttons {"stackMobile":true,"align":"right"} -->
<!-- wp:bst/button {"text":"Đặt bàn","url":"tel:0900123456","radius":10,"style":"white"} /-->
<!-- wp:bst/button {"text":"Gọi 0900 123 456","url":"tel:0900123456","radius":10,"style":"outline"} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->
