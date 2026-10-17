<?php
/**
 * Curated, opt-in CSS recipes. IDs are stored; executable CSS is never imported.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

function bst_css_preset_groups() {
	return array(
		'content'  => __( 'Chữ & nội dung', 'bigspy-ai-theme-wp' ),
		'media'    => __( 'Hình ảnh & banner', 'bigspy-ai-theme-wp' ),
		'buttons'  => __( 'Nút & biểu mẫu', 'bigspy-ai-theme-wp' ),
		'chrome'   => __( 'Đầu trang & chân trang', 'bigspy-ai-theme-wp' ),
		'services' => __( 'Dịch vụ, FAQ & cảm nhận', 'bigspy-ai-theme-wp' ),
		'blog'     => __( 'Blog & bài viết', 'bigspy-ai-theme-wp' ),
		'catalog'  => __( 'Danh sách sản phẩm · WooCommerce', 'bigspy-ai-theme-wp' ),
		'product'  => __( 'Chi tiết & đánh giá · WooCommerce', 'bigspy-ai-theme-wp' ),
		'account'  => __( 'Giỏ hàng & tài khoản · WooCommerce', 'bigspy-ai-theme-wp' ),
		'mobile'   => __( 'Mobile & khả năng truy cập', 'bigspy-ai-theme-wp' ),
	);
}

/**
 * Each recipe is independent unless its exclusive family is shared.
 * Explicit selectors target BIGSPY, core blocks and native WooCommerce markup.
 */
function bst_css_presets() {
	return array(
		'reading_space' => array( 'content', __( 'Giãn dòng nội dung dễ đọc', 'bigspy-ai-theme-wp' ), '.bs-content p,.bs-content li{line-height:1.85}', '', __( 'Đoạn văn và danh sách trong vùng nội dung.', 'bigspy-ai-theme-wp' ) ),
		'heading_balance' => array( 'content', __( 'Cân dòng tiêu đề', 'bigspy-ai-theme-wp' ), '.bs-content :is(h1,h2,h3),.bs-title__text,.bs-entry__title{text-wrap:balance}', '', __( 'Trình duyệt hỗ trợ sẽ cân độ dài các dòng tiêu đề.', 'bigspy-ai-theme-wp' ) ),
		'heading_wrap' => array( 'content', __( 'Ngắt tiêu đề quá dài', 'bigspy-ai-theme-wp' ), '.bs-content :is(h1,h2,h3,h4),.bs-card__title,.bs-product__title{overflow-wrap:anywhere}', '', __( 'Tránh từ hoặc đường dẫn dài tràn khỏi thẻ.', 'bigspy-ai-theme-wp' ) ),
		'article_measure' => array( 'content', __( 'Thu gọn chiều rộng bài viết', 'bigspy-ai-theme-wp' ), '.bs-entry--single .bs-entry__content{max-width:72ch;margin-inline:auto}', '', __( 'Chỉ nội dung bài blog; không thu hẹp trang bán hàng.', 'bigspy-ai-theme-wp' ) ),
		'content_links' => array( 'content', __( 'Gạch chân liên kết trong văn bản', 'bigspy-ai-theme-wp' ), '.bs-content :is(p,li)>a:not(.button):not(.bs-btn){text-decoration:underline;text-underline-offset:.2em}', '', __( 'Không tác động đến nút, menu hay thẻ sản phẩm.', 'bigspy-ai-theme-wp' ) ),
		'quote_card' => array( 'content', __( 'Khung trích dẫn nổi bật', 'bigspy-ai-theme-wp' ), '.bs-content blockquote:not(.bs-testimonial__text){padding:24px;border-inline-start:4px solid var(--bs-primary);background:var(--bs-light);border-radius:12px}', '', __( 'Trích dẫn trong nội dung, không đổi block cảm nhận.', 'bigspy-ai-theme-wp' ) ),
		'table_stripes' => array( 'content', __( 'Bảng có hàng xen kẽ', 'bigspy-ai-theme-wp' ), '.bs-content .wp-block-table tbody tr:nth-child(even){background:var(--bs-light)}.bs-content .wp-block-table td,.bs-content .wp-block-table th{padding:12px;border-color:var(--bs-border)}', '', __( 'Bảng nội dung WordPress; không đổi giỏ hàng.', 'bigspy-ai-theme-wp' ) ),
		'media_round' => array( 'media', __( 'Bo góc ảnh nội dung', 'bigspy-ai-theme-wp' ), '.bs-content .wp-block-image img,.bs-content .bs-image__frame{border-radius:16px}', '', __( 'Block ảnh WordPress và BIGSPY trong nội dung; logo giữ nguyên.', 'bigspy-ai-theme-wp' ) ),
		'media_shadow' => array( 'media', __( 'Đổ bóng nhẹ cho ảnh nội dung', 'bigspy-ai-theme-wp' ), '.bs-content .wp-block-image img,.bs-content .bs-image__frame{box-shadow:0 8px 28px rgba(15,23,42,.12)}', '', __( 'Dùng cho ảnh trong nội dung.', 'bigspy-ai-theme-wp' ) ),
		'caption_center' => array( 'media', __( 'Căn giữa chú thích ảnh', 'bigspy-ai-theme-wp' ), '.bs-content :is(.wp-block-image,.wp-block-gallery,.bs-image) figcaption{text-align:center;font-size:.875em;color:var(--bs-text)}', '', __( 'Chú thích ảnh BIGSPY, ảnh và thư viện ảnh WordPress.', 'bigspy-ai-theme-wp' ) ),
		'banner_round' => array( 'media', __( 'Bo góc banner BIGSPY', 'bigspy-ai-theme-wp' ), '.bs-banner{border-radius:20px;overflow:hidden}', '', __( 'Chỉ block Banner của BIGSPY.', 'bigspy-ai-theme-wp' ) ),
		'banner_text_panel' => array( 'media', __( 'Nền tối giúp chữ banner dễ đọc', 'bigspy-ai-theme-wp' ), '.bs-banner__box{padding:24px;background:rgba(0,0,0,.68);color:#fff;border-radius:16px}.bs-banner__box :is(h1,h2,h3,p){color:inherit}', '', __( 'Tạo nền phía sau vùng chữ của banner BIGSPY.', 'bigspy-ai-theme-wp' ) ),
		'gallery_gap' => array( 'media', __( 'Tăng khoảng cách thư viện ảnh', 'bigspy-ai-theme-wp' ), '.bs-content .wp-block-gallery.has-nested-images{gap:16px}', '', __( 'Áp dụng thư viện ảnh dạng block lồng nhau.', 'bigspy-ai-theme-wp' ) ),
		'button_soft' => array( 'buttons', __( 'Nút bo góc mềm', 'bigspy-ai-theme-wp' ), '.bs-btn,.wp-block-button__link,.wp-element-button,button[type=submit]:not(.bs-search-form__submit),input[type=submit],.woocommerce :is(a,button,input).button.button,.woocommerce #reviews #respond #submit,.woocommerce ul.products li.product .bs-product__actions .button,.woocommerce ul.products li.product .bs-product__quick .button{border-radius:12px}', 'button_shape', __( 'Thay thế lựa chọn nút vuông hoặc nút viên thuốc.', 'bigspy-ai-theme-wp' ) ),
		'button_pill' => array( 'buttons', __( 'Nút hình viên thuốc', 'bigspy-ai-theme-wp' ), '.bs-btn,.wp-block-button__link,.wp-element-button,button[type=submit]:not(.bs-search-form__submit),input[type=submit],.woocommerce :is(a,button,input).button.button,.woocommerce #reviews #respond #submit,.woocommerce ul.products li.product .bs-product__actions .button,.woocommerce ul.products li.product .bs-product__quick .button{border-radius:999px}', 'button_shape', __( 'Thay thế lựa chọn nút vuông hoặc bo góc mềm.', 'bigspy-ai-theme-wp' ) ),
		'button_square' => array( 'buttons', __( 'Nút góc vuông', 'bigspy-ai-theme-wp' ), '.bs-btn,.wp-block-button__link,.wp-element-button,button[type=submit]:not(.bs-search-form__submit),input[type=submit],.woocommerce :is(a,button,input).button.button,.woocommerce #reviews #respond #submit,.woocommerce ul.products li.product .bs-product__actions .button,.woocommerce ul.products li.product .bs-product__quick .button{border-radius:0}', 'button_shape', __( 'Thay thế các kiểu bo góc của nút.', 'bigspy-ai-theme-wp' ) ),
		'button_shadow' => array( 'buttons', __( 'Đổ bóng nhẹ cho nút chính', 'bigspy-ai-theme-wp' ), '.bs-btn:not(.bs-btn--link):not(.bs-btn--outline),.wp-block-button:not(.is-style-outline) .wp-block-button__link{box-shadow:0 4px 14px rgba(15,23,42,.16)}', '', __( 'Không đổi nút dạng liên kết hoặc viền.', 'bigspy-ai-theme-wp' ) ),
		'button_lift' => array( 'buttons', __( 'Nút nhấc nhẹ khi rê chuột', 'bigspy-ai-theme-wp' ), '@media(hover:hover) and (prefers-reduced-motion:no-preference){.bs-btn:not(.bs-btn--link):hover,.wp-block-button__link:hover{transform:translateY(-2px)}}', '', __( 'Chỉ thiết bị có chuột và không yêu cầu giảm chuyển động.', 'bigspy-ai-theme-wp' ) ),
		'form_soft' => array( 'buttons', __( 'Bo góc ô nhập biểu mẫu', 'bigspy-ai-theme-wp' ), '.bs-content :is(input[type=text],input[type=email],input[type=tel],input[type=password],input[type=search],textarea,select),.woocommerce form .input-text{border-radius:10px}', '', __( 'Ô nhập chuẩn và WooCommerce; plugin tự dựng giao diện có thể khác.', 'bigspy-ai-theme-wp' ) ),
		'form_focus' => array( 'buttons', __( 'Làm rõ ô đang nhập', 'bigspy-ai-theme-wp' ), '.bs-content :is(input,textarea,select):focus,.woocommerce form :is(input,textarea,select):focus{outline:2px solid var(--bs-primary);outline-offset:2px}', '', __( 'Thêm viền rõ khi dùng bàn phím hoặc nhập biểu mẫu.', 'bigspy-ai-theme-wp' ) ),
		'header_shadow' => array( 'chrome', __( 'Đổ bóng nhẹ dưới đầu trang', 'bigspy-ai-theme-wp' ), '.bs-header,.bs-header.is-stuck .bs-header__sticky{box-shadow:0 4px 18px rgba(15,23,42,.1)}', '', __( 'Đầu trang BIGSPY, kể cả khi đã bật sticky trong Header.', 'bigspy-ai-theme-wp' ) ),
		'header_divider' => array( 'chrome', __( 'Đường phân cách dưới đầu trang', 'bigspy-ai-theme-wp' ), '.bs-header{border-bottom:1px solid var(--bs-border)}', '', __( 'Thêm viền theo màu đường kẻ của theme.', 'bigspy-ai-theme-wp' ) ),
		'menu_compact' => array( 'chrome', __( 'Thu gọn khoảng cách menu desktop', 'bigspy-ai-theme-wp' ), '@media(min-width:850px){.bs-nav--primary .bs-menu{gap:2px}.bs-nav--primary .bs-menu>li>a{padding-inline:8px}}', '', __( 'Giữ khả năng xuống dòng nếu menu vẫn quá dài.', 'bigspy-ai-theme-wp' ) ),
		'menu_round' => array( 'chrome', __( 'Bo góc menu thả xuống', 'bigspy-ai-theme-wp' ), '.bs-nav .sub-menu{border-radius:12px;box-shadow:0 12px 30px rgba(15,23,42,.14)}', '', __( 'Menu con desktop của BIGSPY.', 'bigspy-ai-theme-wp' ) ),
		'footer_links' => array( 'chrome', __( 'Gạch chân liên kết khi rê chân trang', 'bigspy-ai-theme-wp' ), '.bs-footer a:hover,.bs-footer a:focus-visible{text-decoration:underline;text-underline-offset:4px}', '', __( 'Liên kết chân trang khi rê chuột hoặc dùng bàn phím.', 'bigspy-ai-theme-wp' ) ),
		'footer_spacing' => array( 'chrome', __( 'Tăng khoảng cách liên kết chân trang', 'bigspy-ai-theme-wp' ), '.bs-footer .widget li{margin-bottom:10px}.bs-footer .widget a{line-height:1.8}', '', __( 'Danh sách trong các widget chân trang.', 'bigspy-ai-theme-wp' ) ),
		'service_round' => array( 'services', __( 'Bo góc thẻ dịch vụ', 'bigspy-ai-theme-wp' ), '.bs-icon-box--box-card,.bs-icon-box--box-bordered{border-radius:18px}', '', __( 'Block Icon Box ở chế độ card hoặc bordered.', 'bigspy-ai-theme-wp' ) ),
		'service_shadow' => array( 'services', __( 'Đổ bóng thẻ dịch vụ', 'bigspy-ai-theme-wp' ), '.bs-icon-box--box-card,.bs-icon-box--box-bordered{box-shadow:0 6px 24px rgba(15,23,42,.09)}', '', __( 'Thẻ dịch vụ BIGSPY có nền hoặc viền.', 'bigspy-ai-theme-wp' ) ),
		'service_lift' => array( 'services', __( 'Thẻ dịch vụ nhấc nhẹ khi rê chuột', 'bigspy-ai-theme-wp' ), '@media(hover:hover) and (prefers-reduced-motion:no-preference){.bs-icon-box--box-card:hover,.bs-icon-box--box-bordered:hover{transform:translateY(-4px)}}', '', __( 'Tôn trọng tùy chọn giảm chuyển động của thiết bị.', 'bigspy-ai-theme-wp' ) ),
		'faq_cards' => array( 'services', __( 'FAQ thành từng thẻ riêng', 'bigspy-ai-theme-wp' ), '.bs-accordion .bs-accordion__item{margin-bottom:12px;border:1px solid var(--bs-border);border-radius:12px;background:var(--bs-content-bg)}.bs-accordion .bs-accordion__title{padding:16px 20px}.bs-accordion .bs-accordion__content{padding:0 20px 18px}', '', __( 'Block Accordion của BIGSPY, không thay đổi cơ chế mở/đóng.', 'bigspy-ai-theme-wp' ) ),
		'faq_active' => array( 'services', __( 'Làm nổi câu hỏi đang mở', 'bigspy-ai-theme-wp' ), '.bs-accordion__item[open]>.bs-accordion__title{background:var(--bs-light);border-radius:12px;color:var(--bs-primary)}', '', __( 'Nền sáng theo màu theme cho câu hỏi đang mở.', 'bigspy-ai-theme-wp' ) ),
		'testimonial_card' => array( 'services', __( 'Khung cảm nhận khách hàng', 'bigspy-ai-theme-wp' ), '.bs-testimonial{padding:24px;border:1px solid var(--bs-border);border-radius:18px;background:var(--bs-content-bg)}', '', __( 'Chỉ định dạng nội dung cảm nhận đã có; không tạo đánh giá.', 'bigspy-ai-theme-wp' ) ),
		'pricing_featured' => array( 'services', __( 'Viền nổi gói dịch vụ được chọn', 'bigspy-ai-theme-wp' ), '.bs-pricing-featured{border:2px solid var(--bs-primary);border-radius:18px;box-shadow:0 8px 24px rgba(15,23,42,.1)}', '', __( 'Gói featured trong mẫu bảng giá BIGSPY.', 'bigspy-ai-theme-wp' ) ),
		'blog_round' => array( 'blog', __( 'Bo góc thẻ bài viết', 'bigspy-ai-theme-wp' ), '.bs-card{border-radius:18px;overflow:hidden}', '', __( 'Danh sách bài viết do theme hoặc block Posts xuất ra.', 'bigspy-ai-theme-wp' ) ),
		'blog_shadow' => array( 'blog', __( 'Đổ bóng thẻ bài viết', 'bigspy-ai-theme-wp' ), '.bs-card:not(.bs-card--overlay){box-shadow:0 6px 24px rgba(15,23,42,.09)}', '', __( 'Không đổi kiểu thẻ bài viết có chữ phủ trên ảnh.', 'bigspy-ai-theme-wp' ) ),
		'blog_title_lines' => array( 'blog', __( 'Giới hạn tiêu đề thẻ blog 3 dòng', 'bigspy-ai-theme-wp' ), '.bs-card:not(.bs-card--overlay) .bs-card__title{display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}', '', __( 'Chỉ cắt hiển thị danh sách; tiêu đề đầy đủ vẫn trong trang bài.', 'bigspy-ai-theme-wp' ) ),
		'blog_image_contain' => array( 'blog', __( 'Hiện toàn bộ ảnh thẻ blog', 'bigspy-ai-theme-wp' ), '.bs-card__media img{object-fit:contain;background:var(--bs-light)}', 'blog_image_fit', __( 'Có thể để khoảng trống quanh ảnh; không cắt nội dung ảnh.', 'bigspy-ai-theme-wp' ) ),
		'blog_image_cover' => array( 'blog', __( 'Ảnh blog phủ đầy khung', 'bigspy-ai-theme-wp' ), '.bs-card__media img{object-fit:cover}', 'blog_image_fit', __( 'Có thể cắt mép ảnh để phủ đầy khung.', 'bigspy-ai-theme-wp' ) ),
		'article_thumb_round' => array( 'blog', __( 'Bo góc ảnh đầu bài viết', 'bigspy-ai-theme-wp' ), '.bs-entry__thumb img{border-radius:20px}', '', __( 'Ảnh đại diện ở trang bài viết đơn.', 'bigspy-ai-theme-wp' ) ),
		'product_contain' => array( 'catalog', __( 'Hiện trọn ảnh sản phẩm', 'bigspy-ai-theme-wp' ), '.woocommerce ul.products li.product .bs-product__img,.bs-product__img{object-fit:contain}.bs-product__media{background:var(--bs-content-bg)}', 'product_image_fit', __( 'Phù hợp ảnh bao bì, thiết bị; có thể có khoảng trống quanh ảnh.', 'bigspy-ai-theme-wp' ) ),
		'product_cover' => array( 'catalog', __( 'Ảnh sản phẩm phủ đầy khung', 'bigspy-ai-theme-wp' ), '.woocommerce ul.products li.product .bs-product__img,.bs-product__img{object-fit:cover}', 'product_image_fit', __( 'Phù hợp ảnh lifestyle; có thể cắt mép ảnh.', 'bigspy-ai-theme-wp' ) ),
		'product_round' => array( 'catalog', __( 'Bo góc thẻ sản phẩm', 'bigspy-ai-theme-wp' ), '.bs-product{--bs-card-radius:18px;border-radius:18px}.bs-product__media{border-radius:18px 18px 0 0}', '', __( 'Thẻ sản phẩm BIGSPY trong danh sách WooCommerce.', 'bigspy-ai-theme-wp' ) ),
		'product_shadow' => array( 'catalog', __( 'Đổ bóng nhẹ thẻ sản phẩm', 'bigspy-ai-theme-wp' ), '.bs-product.bs-product{box-shadow:0 6px 22px rgba(15,23,42,.09)}', '', __( 'Thêm bóng cho thẻ mà không đổi bố cục lưới.', 'bigspy-ai-theme-wp' ) ),
		'product_equal_titles' => array( 'catalog', __( 'Căn đều vùng tiêu đề sản phẩm', 'bigspy-ai-theme-wp' ), '.woocommerce ul.products li.product .bs-product__title,.bs-product__title{min-height:2.8em;--bs-product-title-lines:2}', 'product_title_lines', __( 'Dành chỗ cho hai dòng tiêu đề giúp hàng sản phẩm đều hơn.', 'bigspy-ai-theme-wp' ) ),
		'product_titles_three' => array( 'catalog', __( 'Tiêu đề sản phẩm tối đa 3 dòng', 'bigspy-ai-theme-wp' ), '.woocommerce ul.products li.product .bs-product__title,.bs-product__title{min-height:4.2em;--bs-product-title-lines:3}', 'product_title_lines', __( 'Căn đều tiêu đề thẻ BIGSPY; tên đầy đủ vẫn hiển thị trong trang sản phẩm.', 'bigspy-ai-theme-wp' ) ),
		'coupon_brand_button' => array( 'account', __( 'Nút áp dụng mã giảm giá theo màu thương hiệu', 'bigspy-ai-theme-wp' ), '.woocommerce button[name="apply_coupon"]{background:var(--bs-primary);border-color:var(--bs-primary);color:#fff}', '', __( 'Nút coupon WooCommerce classic; giữ nguyên ô nhập và chức năng mã giảm giá.', 'bigspy-ai-theme-wp' ) ),
		'countix_checkout_hide' => array( 'account', __( 'Ẩn đồng hồ Countix trong giỏ hàng và thanh toán', 'bigspy-ai-theme-wp' ), '.woocommerce-cart .woo-sctr-countdown-timer-layout.woo-sctr-layout-1,.woocommerce-checkout .woo-sctr-countdown-timer-layout.woo-sctr-layout-1{display:none!important}', '', __( 'Chỉ có tác dụng khi plugin Countix tạo đồng hồ layout 1; không thay đổi giá hoặc thời hạn ưu đãi.', 'bigspy-ai-theme-wp' ) ),
		'product_price_bold' => array( 'catalog', __( 'Làm nổi giá sản phẩm', 'bigspy-ai-theme-wp' ), '.woocommerce ul.products li.product .bs-product__price,.bs-product__price{font-size:1.15em;font-weight:800}', '', __( 'Chỉ giá trong danh sách; giữ giá cũ/gạch ngang.', 'bigspy-ai-theme-wp' ) ),
		'product_button_solid' => array( 'catalog', __( 'Nút mua nền màu thương hiệu', 'bigspy-ai-theme-wp' ), '.woocommerce ul.products li.product .bs-product__actions .button,.bs-product__actions .button{background:var(--bs-primary);color:#fff;border-color:var(--bs-primary)}', '', __( 'Nút mua bên dưới nội dung thẻ BIGSPY.', 'bigspy-ai-theme-wp' ) ),
		'product_summary_card' => array( 'product', __( 'Nền nhẹ phần thông tin sản phẩm', 'bigspy-ai-theme-wp' ), '.woocommerce div.product div.summary{padding:24px;background:var(--bs-light);border:1px solid var(--bs-border);border-radius:18px;box-sizing:border-box}', '', __( 'Trang chi tiết sản phẩm WooCommerce dạng chuẩn.', 'bigspy-ai-theme-wp' ) ),
		'product_gallery_round' => array( 'product', __( 'Bo góc ảnh chi tiết sản phẩm', 'bigspy-ai-theme-wp' ), '.woocommerce div.product .woocommerce-product-gallery__image{border-radius:16px;overflow:hidden}', '', __( 'Thư viện ảnh sản phẩm chuẩn WooCommerce.', 'bigspy-ai-theme-wp' ) ),
		'product_buy_full' => array( 'product', __( 'Nút mua lớn trong chi tiết', 'bigspy-ai-theme-wp' ), '.woocommerce div.product form.cart .single_add_to_cart_button{width:100%;min-height:48px;margin-top:12px}', '', __( 'Chỉ nút thêm vào giỏ ở trang chi tiết.', 'bigspy-ai-theme-wp' ) ),
		'product_tabs_round' => array( 'product', __( 'Bo góc tab thông tin sản phẩm', 'bigspy-ai-theme-wp' ), '.woocommerce div.product .woocommerce-tabs ul.tabs li{border-radius:10px 10px 0 0}', '', __( 'Các tab mô tả, thông tin và đánh giá chuẩn.', 'bigspy-ai-theme-wp' ) ),
		'review_single_column' => array( 'product', __( 'Đánh giá và form xếp một cột', 'bigspy-ai-theme-wp' ), '.woocommerce #reviews{grid-template-columns:minmax(0,1fr)}.woocommerce #reviews>.clear{display:none}', '', __( 'Ghi đè bố cục hai cột của theme trên desktop.', 'bigspy-ai-theme-wp' ) ),
		'review_avatar_ring' => array( 'product', __( 'Viền nhẹ ảnh đại diện đánh giá', 'bigspy-ai-theme-wp' ), '.woocommerce #reviews .commentlist li img.avatar{border:2px solid var(--bs-border);padding:2px;background:var(--bs-content-bg)}', '', __( 'Không đổi tên, sao hay nội dung của khách hàng.', 'bigspy-ai-theme-wp' ) ),
		'review_stars_primary' => array( 'product', __( 'Sao đánh giá theo màu thương hiệu', 'bigspy-ai-theme-wp' ), '.woocommerce #reviews .star-rating,.woocommerce #reviews p.stars a{color:var(--bs-primary)}', '', __( 'Chỉ đổi màu; không thay đổi điểm đánh giá.', 'bigspy-ai-theme-wp' ) ),
		'cart_totals_card' => array( 'account', __( 'Khung tổng tiền giỏ hàng', 'bigspy-ai-theme-wp' ), '.woocommerce .cart-collaterals .cart_totals,.wc-block-cart .wc-block-cart__sidebar{padding:24px;background:var(--bs-light);border:1px solid var(--bs-border);border-radius:18px;box-sizing:border-box}', '', __( 'Giỏ hàng chuẩn và block Cart; không thay đổi số tiền.', 'bigspy-ai-theme-wp' ) ),
		'cart_checkout_full' => array( 'account', __( 'Nút thanh toán rộng toàn khung', 'bigspy-ai-theme-wp' ), '.woocommerce .wc-proceed-to-checkout .checkout-button{display:block;width:100%;min-height:48px;text-align:center}', '', __( 'Nút đi đến thanh toán của giỏ hàng dạng shortcode.', 'bigspy-ai-theme-wp' ) ),
		'account_nav_round' => array( 'account', __( 'Menu tài khoản bo góc', 'bigspy-ai-theme-wp' ), '.woocommerce-account .woocommerce-MyAccount-navigation li a{border-radius:10px}.woocommerce-account .woocommerce-MyAccount-navigation li.is-active a{background:var(--bs-primary);color:#fff}', '', __( 'Menu tài khoản WooCommerce sau khi đăng nhập.', 'bigspy-ai-theme-wp' ) ),
		'account_orders_stripes' => array( 'account', __( 'Bảng đơn hàng có hàng xen kẽ', 'bigspy-ai-theme-wp' ), '.woocommerce-account .woocommerce-orders-table tbody tr:nth-child(even){background:var(--bs-light)}', '', __( 'Bảng lịch sử đơn hàng trong tài khoản.', 'bigspy-ai-theme-wp' ) ),
		'account_form_flat' => array( 'account', __( 'Form tài khoản nền phẳng', 'bigspy-ai-theme-wp' ), '.woocommerce-account #customer_login>.col-1,.woocommerce-account #customer_login>.col-2,.woocommerce-account .woocommerce-EditAccountForm{background:transparent;border-color:transparent;padding:0}', '', __( 'Bỏ nền thẻ mặc định cho đăng nhập, đăng ký và sửa tài khoản.', 'bigspy-ai-theme-wp' ) ),
		'mobile_form_font' => array( 'mobile', __( 'Ô nhập mobile tối thiểu 16px', 'bigspy-ai-theme-wp' ), '@media(max-width:849px){.bs-content :is(input[type=text],input[type=email],input[type=tel],input[type=password],input[type=search],input[type=number],textarea,select),.woocommerce form .input-text{font-size:16px}}', '', __( 'Giảm hiện tượng tự zoom ô nhập trên trình duyệt mobile.', 'bigspy-ai-theme-wp' ) ),
		'mobile_buttons_full' => array( 'mobile', __( 'Nút nội dung rộng toàn hàng trên mobile', 'bigspy-ai-theme-wp' ), '@media(max-width:549px){.bs-content :is(.wp-block-buttons,.bs-buttons){flex-direction:column;align-items:stretch}.bs-content .wp-block-buttons>.wp-block-button,.bs-content .bs-buttons>.bs-btn{width:100%}.bs-content .wp-block-button__link{display:block;width:100%;text-align:center}}', '', __( 'Nhóm nút WordPress và BIGSPY trong nội dung; không đổi nút header.', 'bigspy-ai-theme-wp' ) ),
		'mobile_menu_targets' => array( 'mobile', __( 'Menu mobile dễ chạm hơn', 'bigspy-ai-theme-wp' ), '@media(max-width:849px){.bs-offcanvas-menu a{min-height:44px;display:flex;align-items:center}}', '', __( 'Tăng vùng chạm của liên kết menu offcanvas BIGSPY.', 'bigspy-ai-theme-wp' ) ),
		'keyboard_focus' => array( 'mobile', __( 'Viền rõ cho điều hướng bàn phím', 'bigspy-ai-theme-wp' ), ':is(.bs-header,.bs-main,.bs-footer) :is(a,button,input,select,textarea,summary):focus-visible{outline:2px solid var(--bs-primary);outline-offset:3px}', '', __( 'Bổ sung dấu hiệu focus, không tắt outline sẵn có.', 'bigspy-ai-theme-wp' ) ),
		'reduce_motion' => array( 'mobile', __( 'Giảm hiệu ứng khi thiết bị yêu cầu', 'bigspy-ai-theme-wp' ), '@media(prefers-reduced-motion:reduce){html{scroll-behavior:auto}.bs-animate{opacity:1!important;transform:none!important}.bs-btn,.bs-card,.bs-icon-box,.bs-product__img,.bs-banner__img,.bs-slider__track{animation:none!important;transition:none!important;scroll-behavior:auto}}', '', __( 'Chỉ khi hệ điều hành bật giảm chuyển động; giữ nguyên slider và menu.', 'bigspy-ai-theme-wp' ) ),
		'mobile_table_scroll' => array( 'mobile', __( 'Bảng nội dung cuộn ngang trên mobile', 'bigspy-ai-theme-wp' ), '@media(max-width:849px){.bs-content .wp-block-table{overflow-x:auto}.bs-content .wp-block-table table{min-width:560px}}', '', __( 'Bảng block WordPress; không áp dụng bảng thanh toán.', 'bigspy-ai-theme-wp' ) ),
	);
}

/** Unknown IDs, malformed inputs and conflicting variants are discarded. Last choice wins. */
function bst_sanitize_css_presets( $value ) {
	if ( ! is_array( $value ) ) {
		return array();
	}
	$recipes = bst_css_presets();
	$enabled = array();
	$families = array();
	foreach ( $value as $id ) {
		if ( ! is_string( $id ) || ! isset( $recipes[ $id ] ) ) {
			continue;
		}
		$family = $recipes[ $id ][3];
		if ( $family && isset( $families[ $family ] ) ) {
			unset( $enabled[ $families[ $family ] ] );
		}
		if ( $family ) {
			$families[ $family ] = $id;
		}
		$enabled[ $id ] = true;
	}
	return array_keys( $enabled );
}

/** Output in catalog order, matching JavaScript preview regardless of click order. */
function bst_css_presets_css() {
	$enabled = bst_sanitize_css_presets( get_theme_mod( 'css_presets', array() ) );
	$css = '';
	foreach ( bst_css_presets() as $id => $recipe ) {
		if ( in_array( $id, $enabled, true ) ) {
			$css .= $recipe[2] . "\n";
		}
	}
	return $css;
}

function bst_css_presets_assets() {
	$css = bst_css_presets_css();
	if ( '' === $css && ! is_customize_preview() ) {
		return;
	}
	$deps = array( 'bst-main' );
	if ( wp_style_is( 'bst-woocommerce', 'enqueued' ) ) {
		$deps[] = 'bst-woocommerce';
	}
	wp_register_style( 'bst-css-presets', false, $deps, BST_VERSION );
	wp_enqueue_style( 'bst-css-presets' );
	// Retain a stable style element for live preview even when every recipe is off.
	wp_add_inline_style( 'bst-css-presets', $css ? $css : '/* BIGSPY CSS presets: none enabled. */' );
}
add_action( 'wp_enqueue_scripts', 'bst_css_presets_assets', 100 );
