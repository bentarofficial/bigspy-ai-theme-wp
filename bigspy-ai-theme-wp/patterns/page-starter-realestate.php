<?php
/**
 * Title: Trang chủ – Bất động sản
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
?>
<!-- wp:bst/section {"bgImage":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-hero.svg' ) ); ?>","overlayColor":"#0b1830","overlayOpacity":62,"textScheme":"light","paddingDesktop":140,"paddingTablet":100,"paddingMobile":72,"minHeight":88,"minHeightUnit":"vh","align":"full"} -->
<!-- wp:bst/row {"gap":"medium","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":7,"spanTablet":12,"spanMobile":12} -->
<!-- wp:bst/text {"html":"\u003cp\u003eBất động sản · Căn hộ · Nhà phố\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"color":"#ffffff"}} /-->
<!-- wp:bst/heading {"text":"Tìm ngôi nhà phù hợp với cuộc sống của bạn","tag":"h1","size":56,"sizeTablet":45,"sizeMobile":36,"bsPresets":"hd-tight2","bsAnimation":"fade-up"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eDự án pháp lý minh bạch, vị trí kết nối thuận tiện và đội ngũ chuyên viên đồng hành từ lúc chọn nhà, đàm phán giá, vay vốn đến khi nhận bàn giao.\u003c/p\u003e","size":19,"bsAnimation":"fade-up","bsAnimationDelay":100} /-->
<!-- wp:bst/icon-list {"items":[{"icon":"check","text":"Pháp lý kiểm tra kỹ","link":""},{"icon":"check","text":"Hỗ trợ vay đến 70%","link":""},{"icon":"check","text":"Dẫn xem nhà miễn phí","link":""},{"icon":"check","text":"Cam kết đúng giá niêm yết","link":""}],"layout":"grid","columns":2,"iconColor":"#ffffff","iconBg":true,"gap":12} /-->
<!-- wp:bst/buttons {"stackMobile":true} -->
<!-- wp:bst/button {"text":"Nhận tư vấn","url":"#contact","radius":10} /-->
<!-- wp:bst/button {"text":"Xem dự án","url":"#services","radius":10,"style":"white"} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":5,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-left"} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-about.svg' ) ); ?>","alt":"Tìm ngôi nhà phù hợp với cuộc sống của bạn","ratio":"4-3","radius":22,"bsPresets":"img-sh3 img-r24"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003e\u003cstrong\u003e1.200+ giao dịch\u003c/strong\u003e\u003cbr\u003eThành công trong 15 năm hoạt động\u003c/p\u003e","bsPresets":"tx-note","bsDesign":{"mt":"-40","ml":"24","mr":"24","bg":"#ffffff","color":"#1f2937"}} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":64,"paddingTablet":72,"paddingMobile":54,"bsPresets":"sec-white sec-lb"} -->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"shield","title":"Pháp lý rõ ràng","text":"Sổ hồng riêng từng căn","iconStyle":"square","iconColor":"#ffffff","iconBg":"#1d4ed8","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"pin","title":"Vị trí đắc địa","text":"15 phút tới trung tâm","iconStyle":"square","iconColor":"#ffffff","iconBg":"#b45309","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"chart","title":"Tiềm năng tăng giá","text":"8–12% mỗi năm","iconStyle":"square","iconColor":"#ffffff","iconBg":"#059669","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"card","title":"Thanh toán linh hoạt","text":"Trả góp đến 30 năm","iconStyle":"square","iconColor":"#ffffff","iconBg":"#7c3aed","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/row {"gap":"large","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":6,"spanTablet":12,"spanMobile":12,"bsAnimation":"fade-right"} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-about.svg' ) ); ?>","alt":"Đồng hành cùng bạn trên hành trình an cư","ratio":"1-1","radius":26,"bsPresets":"img-shoff img-r24"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":6,"spanTablet":12,"spanMobile":12,"bsAnimation":"fade-left"} -->
<!-- wp:bst/text {"html":"\u003cp\u003eVề chúng tôi\u003c/p\u003e","bsPresets":"tx-kicker"} /-->
<!-- wp:bst/heading {"text":"Đồng hành cùng bạn trên hành trình an cư","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eChúng tôi là đơn vị phân phối chính thức của nhiều chủ đầu tư uy tín. Mỗi dự án đều được thẩm định về pháp lý, quy hoạch và tiến độ trước khi giới thiệu tới khách hàng.\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eDịch vụ trọn gói: tư vấn chọn nhà, dẫn xem thực tế, đàm phán giá, hỗ trợ vay ngân hàng và hoàn tất thủ tục công chứng, sang tên.\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- wp:bst/icon-list {"items":[{"icon":"check","text":"Phân phối 35+ dự án","link":""},{"icon":"check","text":"60 chuyên viên tư vấn","link":""},{"icon":"check","text":"Liên kết 12 ngân hàng","link":""},{"icon":"check","text":"Xe đưa đón xem dự án","link":""},{"icon":"check","text":"Hỗ trợ thủ tục miễn phí","link":""},{"icon":"check","text":"Cam kết hoàn tiền nếu sai thông tin","link":""}],"layout":"grid","columns":2,"iconColor":"#1d4ed8","iconBg":true,"gap":12} /-->
<!-- wp:bst/buttons {"stackMobile":true} -->
<!-- wp:bst/button {"text":"Đặt lịch xem nhà","url":"#contact","radius":10} /-->
<!-- wp:bst/button {"text":"Gọi tư vấn","url":"tel:0900345678","radius":10,"style":"outline"} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54,"anchor":"services","bsPresets":"sec-soft"} -->
<!-- wp:bst/text {"html":"\u003cp\u003eDự án nổi bật\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"Lựa chọn đáng sống nhất","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eDanh sách được cập nhật mỗi tuần với giá bán và chính sách mới nhất từ chủ đầu tư.\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mw":"760","mwc":true}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-1.svg' ) ); ?>","alt":"Căn hộ 2PN trung tâm","title":"Căn hộ 2PN trung tâm","text":"68m², view sông, nội thất cơ bản, giá từ 3,2 tỷ.","link":"#contact","linkText":"Xem chi tiết →","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-2.svg' ) ); ?>","alt":"Nhà phố liền kề","title":"Nhà phố liền kề","text":"5x20m, 1 trệt 3 lầu, vừa ở vừa kinh doanh.","link":"#contact","linkText":"Xem chi tiết →","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-3.svg' ) ); ?>","alt":"Biệt thự ven hồ","title":"Biệt thự ven hồ","text":"300m² đất, sân vườn riêng, an ninh 24/7.","link":"#contact","linkText":"Xem chi tiết →","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-1.svg' ) ); ?>","alt":"Căn hộ studio","title":"Căn hộ studio","text":"35m², phù hợp người trẻ và cho thuê.","link":"#contact","linkText":"Xem chi tiết →","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-2.svg' ) ); ?>","alt":"Shophouse thương mại","title":"Shophouse thương mại","text":"Mặt tiền đường lớn, khai thác kinh doanh ngay.","link":"#contact","linkText":"Xem chi tiết →","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-3.svg' ) ); ?>","alt":"Đất nền khu đô thị","title":"Đất nền khu đô thị","text":"Sổ đỏ từng nền, hạ tầng hoàn thiện.","link":"#contact","linkText":"Xem chi tiết →","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003eQuy trình mua nhà\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"Mua nhà dễ dàng trong 4 bước","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eChuyên viên đồng hành ở mọi bước để bạn yên tâm.\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mw":"760","mwc":true}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-p26 col-eq col-hls col-bdt","bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/heading {"text":"01","tag":"p","size":44,"sizeTablet":36,"sizeMobile":29,"bsDesign":{"color":"#1d4ed8","fw":"800"}} /-->
<!-- wp:bst/heading {"text":"Tư vấn nhu cầu","tag":"h3","size":21,"sizeTablet":17,"sizeMobile":20} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eXác định ngân sách, vị trí và loại hình phù hợp.\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-p26 col-eq col-hls col-bdt","bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/heading {"text":"02","tag":"p","size":44,"sizeTablet":36,"sizeMobile":29,"bsDesign":{"color":"#1d4ed8","fw":"800"}} /-->
<!-- wp:bst/heading {"text":"Xem nhà thực tế","tag":"h3","size":21,"sizeTablet":17,"sizeMobile":20} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eDẫn xem dự án, so sánh nhiều lựa chọn.\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-p26 col-eq col-hls col-bdt","bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/heading {"text":"03","tag":"p","size":44,"sizeTablet":36,"sizeMobile":29,"bsDesign":{"color":"#1d4ed8","fw":"800"}} /-->
<!-- wp:bst/heading {"text":"Đàm phán \u0026 vay vốn","tag":"h3","size":21,"sizeTablet":17,"sizeMobile":20} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eThương lượng giá tốt, chuẩn bị hồ sơ vay.\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsPresets":"col-wc col-r20 col-p26 col-eq col-hls col-bdt","bsAnimation":"fade-up","bsAnimationDelay":300} -->
<!-- wp:bst/heading {"text":"04","tag":"p","size":44,"sizeTablet":36,"sizeMobile":29,"bsDesign":{"color":"#1d4ed8","fw":"800"}} /-->
<!-- wp:bst/heading {"text":"Ký hợp đồng","tag":"h3","size":21,"sizeTablet":17,"sizeMobile":20} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eCông chứng, sang tên và nhận bàn giao.\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":76,"paddingMobile":50,"textScheme":"light","style":{"color":{"background":"#1d4ed8"}}} -->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"1200","suffix":"+","label":"Giao dịch thành công","color":"#ffffff"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eTrong 15 năm\u003c/p\u003e","size":14,"bsDesign":{"ta":"center","color":"rgba(255,255,255,.8)"}} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"35","suffix":"+","label":"Dự án phân phối","color":"#ffffff"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eTừ chủ đầu tư uy tín\u003c/p\u003e","size":14,"bsDesign":{"ta":"center","color":"rgba(255,255,255,.8)"}} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"12","suffix":"","label":"Ngân hàng liên kết","color":"#ffffff"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eLãi suất ưu đãi\u003c/p\u003e","size":14,"bsDesign":{"ta":"center","color":"rgba(255,255,255,.8)"}} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/counter {"number":"60","suffix":"+","label":"Chuyên viên","color":"#ffffff"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eAm hiểu thị trường\u003c/p\u003e","size":14,"bsDesign":{"ta":"center","color":"rgba(255,255,255,.8)"}} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003eVì sao chọn chúng tôi\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"An tâm từ lúc chọn đến khi ở","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/row {"gap":"medium","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"shield","title":"Thẩm định pháp lý","text":"Kiểm tra giấy phép, quy hoạch, tiến độ.","iconStyle":"square","iconColor":"#ffffff","iconBg":"#1d4ed8","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"chart","title":"Phân tích thị trường","text":"Báo cáo giá và tiềm năng từng khu vực.","iconStyle":"square","iconColor":"#ffffff","iconBg":"#059669","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"home","title":"Nhà đúng mô tả","text":"Hình ảnh, diện tích thực tế 100%.","iconStyle":"square","iconColor":"#ffffff","iconBg":"#b45309","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":12,"spanMobile":12,"bsAnimation":"zoom-in"} -->
<!-- wp:bst/image {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-2.svg' ) ); ?>","alt":"An tâm từ lúc chọn đến khi ở","ratio":"1-1","radius":200,"bsPresets":"img-circ img-sh2"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"card","title":"Hỗ trợ tài chính","text":"Tư vấn gói vay phù hợp thu nhập.","iconStyle":"square","iconColor":"#ffffff","iconBg":"#7c3aed","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"users","title":"Tư vấn 1-1","text":"Một chuyên viên theo bạn suốt quá trình.","iconStyle":"square","iconColor":"#ffffff","iconBg":"#db2777","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"support","title":"Hậu mãi","text":"Hỗ trợ cho thuê, bán lại khi cần.","iconStyle":"square","iconColor":"#ffffff","iconBg":"#0d9488","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54,"bsPresets":"sec-light"} -->
<!-- wp:bst/text {"html":"\u003cp\u003eGói dịch vụ\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"Dịch vụ phù hợp mọi nhu cầu","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eNgười mua nhà được tư vấn miễn phí. Chủ nhà ký gửi chỉ trả phí khi giao dịch thành công.\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mw":"760","mwc":true}} /-->
<!-- wp:bst/row {"gap":"medium","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/price-table {"title":"Người mua","price":"Miễn phí","period":"","description":"Tư vấn và dẫn xem nhà","features":"Tư vấn chọn dự án\nDẫn xem thực tế\nHỗ trợ đàm phán\n-Thủ tục vay vốn","buttonText":"Chọn gói này","buttonUrl":"#contact","featured":false,"badge":"Được chọn nhiều","style":"card"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":120} -->
<!-- wp:bst/price-table {"title":"Trọn gói","price":"0,5%","period":"/ giá trị","description":"Được chọn nhiều nhất","features":"Tất cả quyền lợi người mua\nHồ sơ vay ngân hàng\nCông chứng, sang tên\nKiểm tra bàn giao","buttonText":"Chọn gói này","buttonUrl":"#contact","featured":true,"badge":"Được chọn nhiều","style":"card"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":12,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":240} -->
<!-- wp:bst/price-table {"title":"Ký gửi bán","price":"1%","period":"/ khi bán được","description":"Dành cho chủ nhà","features":"Định giá miễn phí\nChụp ảnh, quay video\nQuảng cáo đa kênh\nLọc khách tiềm năng","buttonText":"Chọn gói này","buttonUrl":"#contact","featured":false,"badge":"Được chọn nhiều","style":"card"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003eHình ảnh dự án\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"Không gian sống hiện đại","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
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
<!-- wp:bst/text {"html":"\u003cp\u003eChuyên viên\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"Đội ngũ tư vấn tận tâm","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eAm hiểu thị trường, trung thực và luôn đặt quyền lợi khách hàng lên đầu.\u003c/p\u003e","bsPresets":"tx-intro","bsDesign":{"ta":"center","mw":"760","mwc":true}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/team-member {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-1.svg' ) ); ?>","name":"Nguyễn Hoàng Nam","role":"Giám đốc kinh doanh","bio":"15 năm kinh nghiệm thị trường nhà ở.","style":"card","facebook":"#","zalo":"#","bsPresets":"tm-r20 tm-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":100} -->
<!-- wp:bst/team-member {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-2.svg' ) ); ?>","name":"Trần Bảo Ngọc","role":"Chuyên viên căn hộ","bio":"Hơn 300 giao dịch căn hộ thành công.","style":"card","facebook":"#","zalo":"#","bsPresets":"tm-r20 tm-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":200} -->
<!-- wp:bst/team-member {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-3.svg' ) ); ?>","name":"Lê Đức Anh","role":"Chuyên viên nhà phố","bio":"Am hiểu quy hoạch các quận trung tâm.","style":"card","facebook":"#","zalo":"#","bsPresets":"tm-r20 tm-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":300} -->
<!-- wp:bst/team-member {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-4.svg' ) ); ?>","name":"Phạm Thu Thảo","role":"Tư vấn tài chính","bio":"Hỗ trợ hồ sơ vay với 12 ngân hàng.","style":"card","facebook":"#","zalo":"#","bsPresets":"tm-r20 tm-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003eCảm nhận khách hàng\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"Những gia đình đã tìm được nhà","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":0} -->
<!-- wp:bst/testimonial {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-1.svg' ) ); ?>","name":"Anh Tuấn","role":"Mua căn hộ 2PN","text":"Tư vấn rõ ràng, không ép mua. Thủ tục vay ngân hàng được hỗ trợ tận tình từ đầu đến cuối.","bsPresets":"ts-wc ts-r20 ts-sh1 ts-q"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":120} -->
<!-- wp:bst/testimonial {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-2.svg' ) ); ?>","name":"Chị Hạnh","role":"Mua nhà phố","text":"Chọn được vị trí đẹp đúng ngân sách, sau một năm giá đã tăng khá tốt.","bsPresets":"ts-wc ts-r20 ts-sh1 ts-q"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12,"bsAnimation":"fade-up","bsAnimationDelay":240} -->
<!-- wp:bst/testimonial {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/avatar-3.svg' ) ); ?>","name":"Anh Khoa","role":"Nhà đầu tư","text":"Phân tích dự án chi tiết, minh bạch về pháp lý. Rất đáng tin cậy.","bsPresets":"ts-wc ts-r20 ts-sh1 ts-q"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54,"bsPresets":"sec-light"} -->
<!-- wp:bst/row {"gap":"large"} -->
<!-- wp:bst/column {"spanDesktop":5,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/text {"html":"\u003cp\u003eHỏi đáp\u003c/p\u003e","bsPresets":"tx-kicker"} /-->
<!-- wp:bst/heading {"text":"Câu hỏi thường gặp","tag":"h2","size":38,"sizeTablet":31,"sizeMobile":25,"bsPresets":"hd-tight2"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eCần thêm thông tin? Để lại số điện thoại, chuyên viên sẽ gọi lại trong 30 phút.\u003c/p\u003e","bsPresets":"tx-muted"} /-->
<!-- wp:bst/icon-box {"icon":"phone","title":"Hotline","text":"0900 345 678","iconStyle":"square","iconColor":"#ffffff","iconBg":"#1d4ed8","position":"left","align":"left","titleTag":"h4"} /-->
<!-- wp:bst/icon-box {"icon":"message","title":"Zalo / Messenger","text":"Phản hồi trong 5 phút giờ làm việc","iconStyle":"square","iconColor":"#ffffff","iconBg":"#1d4ed8","position":"left","align":"left","titleTag":"h4"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":7,"spanTablet":12,"spanMobile":12} -->
<!-- wp:bst/accordion {"bsPresets":"acc-cards acc-abg","titleTag":"h3"} -->
<!-- wp:bst/accordion-item {"title":"Có hỗ trợ vay ngân hàng không?","open":true} -->
<!-- wp:bst/text {"html":"\u003cp\u003eCó, chúng tôi liên kết 12 ngân hàng với lãi suất ưu đãi, hồ sơ đơn giản, giải ngân nhanh.\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"Xem nhà có mất phí không?","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003eHoàn toàn miễn phí, có xe đưa đón khi xem dự án ngoại thành.\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"Pháp lý dự án được kiểm tra thế nào?","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003eMỗi dự án đều được thẩm định giấy phép xây dựng, quy hoạch 1/500 và tiến độ thực tế.\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"Tôi có thể ký gửi nhà để bán không?","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003eCó, chúng tôi định giá miễn phí và chỉ thu phí khi giao dịch thành công.\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- wp:bst/accordion-item {"title":"Mua nhà cần chuẩn bị giấy tờ gì?","open":false} -->
<!-- wp:bst/text {"html":"\u003cp\u003eCCCD, giấy xác nhận tình trạng hôn nhân, sổ hộ khẩu và chứng minh thu nhập nếu vay vốn.\u003c/p\u003e"} /-->
<!-- /wp:bst/accordion-item -->
<!-- /wp:bst/accordion -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":96,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/text {"html":"\u003cp\u003eTin tức \u0026 chia sẻ\u003c/p\u003e","bsPresets":"tx-kicker","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/heading {"text":"Tin thị trường","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2","bsDesign":{"ta":"center"}} /-->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-1.svg' ) ); ?>","alt":"Thị trường căn hộ quý này","title":"Thị trường căn hộ quý này","text":"Giá bán ổn định, nguồn cung mới tập trung ở khu Đông và khu Nam.","link":"#contact","linkText":"Đọc tiếp →","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-2.svg' ) ); ?>","alt":"Kinh nghiệm vay mua nhà lần đầu","title":"Kinh nghiệm vay mua nhà lần đầu","text":"Cách tính khả năng trả nợ và chọn gói lãi suất phù hợp.","link":"#contact","linkText":"Đọc tiếp →","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/image-box {"imageUrl":"<?php echo esc_url( bst_pattern_image( 'starter/realestate-3.svg' ) ); ?>","alt":"5 điều cần kiểm tra khi nhận nhà","title":"5 điều cần kiểm tra khi nhận nhà","text":"Danh sách kiểm tra điện nước, tường, cửa trước khi ký biên bản.","link":"#contact","linkText":"Đọc tiếp →","ratio":"4-3","radius":16,"hover":"zoom","card":true,"bsPresets":"imb-r20 imb-sh1 imb-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":84,"paddingMobile":56,"anchor":"contact","textScheme":"light","bsPresets":"sec-brand sec-r24"} -->
<!-- wp:bst/row {"gap":"medium","verticalAlign":"center"} -->
<!-- wp:bst/column {"spanDesktop":8,"spanTablet":12,"spanMobile":12} -->
<!-- wp:bst/heading {"text":"Bạn đang tìm nhà?","tag":"h2","size":40,"sizeTablet":32,"sizeMobile":26,"bsPresets":"hd-tight2"} /-->
<!-- wp:bst/text {"html":"\u003cp\u003eĐể lại số điện thoại, chuyên viên sẽ gửi danh sách phù hợp ngân sách trong 30 phút.\u003c/p\u003e","size":18} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":4,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/buttons {"stackMobile":true,"align":"right"} -->
<!-- wp:bst/button {"text":"Nhận tư vấn","url":"tel:0900345678","radius":10,"style":"white"} /-->
<!-- wp:bst/button {"text":"Nhắn Zalo","url":"https://zalo.me/0900345678","radius":10,"style":"outline"} /-->
<!-- /wp:bst/buttons -->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->

<!-- wp:bst/section {"paddingDesktop":70,"paddingTablet":72,"paddingMobile":54} -->
<!-- wp:bst/row {"gap":"medium"} -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"pin","title":"Địa chỉ","text":"Tầng 12, 68 Lê Duẩn, Quận 1, TP.HCM","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#1d4ed8","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"clock","title":"Giờ mở cửa","text":"08:00 – 20:00, thứ Hai – Chủ nhật","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#1d4ed8","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"phone","title":"Điện thoại","text":"0900 345 678","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#1d4ed8","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- wp:bst/column {"spanDesktop":3,"spanTablet":6,"spanMobile":12} -->
<!-- wp:bst/icon-box {"icon":"mail","title":"Email","text":"tuvan@batdongsan.vn","iconStyle":"circle","iconColor":"#ffffff","iconBg":"#1d4ed8","boxStyle":"card","bsPresets":"ib-r20 ib-hls"} /-->
<!-- /wp:bst/column -->
<!-- /wp:bst/row -->
<!-- /wp:bst/section -->
