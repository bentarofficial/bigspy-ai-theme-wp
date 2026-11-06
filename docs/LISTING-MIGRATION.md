# Gộp BIGSPY AI LIST vào BIGSPY AI Theme WP

Mục tiêu: bigspyai.com chạy bằng BIGSPY AI Theme WP, sau đó ngừng theme `theme-bigspy-ai-list` (1.59.1). Tính năng listing trở thành chế độ dùng chung cho người dùng theme. Owner chốt trong chat ngày 06-11-2026. Site đã tạm bật "Discourage search engines" trong lúc chuyển.

## Hai chế độ khi cài theme

Owner chốt 06-11-2026: cài xong, theme chạy như hiện nay (chế độ Website, mặc định). Chế độ **BIGSPY AI List** (site thư mục kiểu bigspyai.com) là tùy chọn: người dùng vào cài đặt theme và bật công tắc "Bật chế độ BIGSPY AI List" thì mới dùng được. Không hỏi chọn chế độ ngay khi cài.

Công tắc đặt trong trang cài đặt của theme, kèm một dòng giới thiệu ở trang About; tắt lại thì về Website mà không mất dữ liệu listing.

**Khối listing khi tắt chế độ:** vẫn hiện trong trình chèn khối nhưng có biểu tượng khoá nhỏ màu đỏ đậm ở góc trên bên phải ô khối (giống khoá trên khối Pro của Elementor). Bấm vào khối bị khoá không chèn được, mà hiện thông báo ngắn "Bật chế độ BIGSPY AI List trong cài đặt theme để dùng khối này", có nút mở trang cài đặt. Khối listing đã có sẵn trong bài cũ thì giữ nguyên nội dung, ngoài trang không hiển thị, trong editor có ghi chú cần bật chế độ.

**Nhóm khối riêng:** khối listing không nằm trong nhóm "BIGSPY AI Builder" mà ở nhóm riêng **"BIGSPY AI List"** (slug `bigspy-list`), đặt ngay bên dưới BIGSPY AI Builder trong trình chèn (owner chốt 06-11-2026). Đăng ký trong `bst_block_category()` ở `inc/blocks.php`, chèn sau mục `bigspy`. Nhóm hiện ở cả hai chế độ: tắt thì các khối trong nhóm mang ổ khoá, bật thì dùng được. Pattern listing cũng gom vào danh mục pattern cùng tên.

**Khi bật chế độ:** khối listing được mở khoá, và toàn bộ khối của BIGSPY AI Theme WP vẫn dùng bình thường. Chế độ List là phần cộng thêm, không thay thế khối nào.

Gợi ý kỹ thuật: đăng ký khối listing ở cả hai chế độ (để bài cũ không báo lỗi "khối không hợp lệ"), khi tắt thì đặt `supports.inserter` qua bộ lọc editor để hiện ô khoá thay vì chèn, và `render.php` trả về rỗng ngoài trang. Chọn Listing thì bật module listing, gợi ý cài WooCommerce nếu chưa có, và cho nhập trang chủ, trang Categories, menu mẫu kiểu thư mục. Chọn Website thì module listing không tải gì. Hai chế độ dùng chung header, footer, block và AI Designer.

## Nghiên cứu: khoá khối và vị thế thị trường (06-11-2026)

### Cách làm khoá khối trong Gutenberg

Gutenberg không có sẵn trạng thái "khối bị khoá" trong trình chèn. Ghép từ các API có sẵn:

| Phần | Cách làm | Ghi chú |
| --- | --- | --- |
| Ổ khoá trên ô khối | Bộ lọc JS `blocks.registerBlockType`: khi chế độ tắt, thay `icon` bằng SVG gốc chồng thêm ổ khoá nhỏ góc trên phải, thêm "(BIGSPY AI List)" vào `description` | Hiện ở trình chèn chính, quick inserter và lệnh `/`. |
| Bấm vào khối khoá | WordPress không cho huỷ thao tác chèn. Chèn vẫn xảy ra, nhưng `edit()` hiện thẻ khoá: biểu tượng khoá, một câu giải thích, nút "Bật BIGSPY AI List" (admin, bật qua REST rồi tải lại editor) và nút "Gỡ khối" | Kadence/Spectra/Essential Blocks cũng dùng thẻ thay thế kiểu này cho khối Pro. Phương án khác: tự gỡ khối ngay và hiện snackbar, nhưng dễ gây khó hiểu. |
| Bài đã có khối listing | Khối vẫn đăng ký nên không lỗi "khối không hợp lệ"; editor hiện thẻ khoá; `render.php` trả về rỗng ngoài trang | Bật lại là hiện như cũ. |
| Pattern | Pattern listing chỉ đăng ký khi bật chế độ | Tránh chèn cả trang thư mục khi đang tắt. |
| AI Designer | Truyền trạng thái chế độ vào prompt; khi tắt, AI không dùng khối listing mà đề nghị người dùng bật | |
| Bảo mật | Bật/tắt chỉ cho `manage_options`, có nonce | |

Kết luận: làm được hoàn toàn bằng API chính thức, không sửa lõi WordPress. Rủi ro chính là giao diện trình chèn thay đổi theo bản WordPress; cần test trên 6.5 và bản mới nhất.

### Thị trường theme/plugin thư mục

- **Theme thư mục trọn gói** (Listeo, MyListing, ListingPro): mạnh về giao diện có sẵn, thường bán trên ThemeForest khoảng 79–99 USD, chủ yếu dựa vào Elementor, khóa người dùng vào một theme chỉ để làm thư mục.
- **Plugin thư mục** (GeoDirectory, Directorist, HivePress, Listora): chạy trên theme bất kỳ nhưng thường phải ghép thêm theme riêng mới đẹp; một số mới đi theo hướng khối Gutenberg.

Khác biệt của BIGSPY AI Theme WP nếu làm tốt:

1. **Một theme, hai chế độ:** site doanh nghiệp/cửa hàng hôm nay, bật thành thư mục ngày mai, không đổi theme, không mất thiết kế.
2. **Thuần khối Gutenberg, miễn phí GPL**, không phụ thuộc Elementor.
3. **Kiếm tiền có sẵn qua WooCommerce:** listing trả phí, Sponsored/AD, affiliate kèm disclosure, schema chuẩn SEO (đã chạy thật trên bigspyai.com).
4. **AI Designer** dựng trang thư mục bằng lời mô tả; về sau có thể đưa trình tạo listing bằng AI thành tính năng.

Còn thiếu so với đối thủ (ứng viên sau G-80, chưa cam kết): người dùng tự gửi listing ở ngoài trang, nhận quyền sở hữu listing (claim), đánh giá của người dùng, bộ lọc tìm kiếm nhiều điều kiện, bản đồ và tìm theo vị trí, so sánh listing.

Nguồn tham khảo (bài tổng hợp, một số do nhà cung cấp viết): gauravtiwari.org/best-directory-wordpress-themes, wbcomdesigns.com/resources/best-wordpress-directory-themes, fs-code.com/blog/best-wordpress-directory-plugins.

## Kết quả kiểm kê (repo private `bentarofficial/theme-bigspy-ai-list`)

**Dữ liệu listing là sản phẩm WooCommerce**, không phải CPT riêng. Đổi theme không mất dữ liệu; chỉ cần BIGSPY AI Theme WP đọc đúng meta và hiển thị.

| Nhóm | Nguồn trong theme cũ | Ghi chú khi chuyển |
| --- | --- | --- |
| Trường ACF (21) | `acf-json/group_bigspy_listing.json`: tool_tagline, pricing_model, rating, starting_price, free_trial, api_available, api_url, company_name, founded_year, key_features, pros, cons, integrations, faq, source_url, ai_generated, ai_model, ai_generated_at, official_url, homepage_url, affiliate_url | Giữ nguyên meta key. Đọc bằng `get_post_meta`, ACF chỉ là tùy chọn (`bigspy_get_field` đã có fallback). |
| Meta riêng | `_bigspy_ad`, `_bigspy_paid_listing`, `_bigspy_sponsor_name`, `_bigspy_listing_type`, `_bigspy_dynamic_attributes`, `_bigspy_affiliate_url`, `_bigspy_internal_links`, pricing_* | Giữ nguyên key; hộp meta "BIGSPY Listing Information" chuyển sang theme mới. |
| Hiển thị | `inc/listing.php` (885 dòng): thẻ listing, giá, rating, facts, thuộc tính động, disclosure affiliate/paid, schema ItemPage/Breadcrumb/Organization (Rank Math) | Phần lõi cần chuyển. |
| Sponsored | `inc/categories-sponsored.php`: cột AD trong Products, bật/tắt AJAX, vòng xoay sponsored, featured trên trang Categories | Chuyển. |
| Trang | `page-categories.php`, `front-page.php`, `home.php`, `search.php`, `woocommerce/*` (archive, card, toolbar, single) | Làm lại bằng template/block của theme mới. |
| Block (18) | listing-grid, category-grid, search, home-seo-divider, elements (sidebar blog…), và các block bố cục chung (section, row, column, heading, text, button, tabs, code, callout, badge, divider) | Chỉ cần listing-grid, category-grid, search, elements; khối chung đã có tương đương trong theme mới. |
| Shortcode | bigspy_categories, bigspy_elements, bigspy_home_sponsor, bigspy_search, bigspy_sponsored_grid, bigspy_home_destination_buttons | Giữ tên shortcode để nội dung cũ không vỡ. |
| Hero trang | `inc/page-heroes.php`, `home-hero-destination-buttons.php` | Chuyển hoặc thay bằng block hero sẵn có. |
| Cài đặt | `inc/customizer.php` (1307 dòng, 15 section) + trang admin "BIGSPY AI Customize" | Chỉ chuyển phần riêng listing (cards, single listing); phần chung dùng Customizer của theme mới. |
| Plugin riêng | list-generator, post-writer, site-insights, popular-analytics | Không đưa vào theme. Giữ là plugin của bigspyai.com; chỉ đảm bảo hook/hàm chúng gọi vẫn tồn tại. |

**Xung đột tên:** theme cũ dùng tiền tố `bigspy_`, theme mới dùng `bst_`. Khi chuyển, đổi sang `bst_listing_*`, và giữ hàm cũ (`bigspy_render_card`, `bigspy_listing_url`, `bigspy_sponsored_refresh_rotation`…) làm lớp tương thích vì plugin riêng có gọi. Cần grep các plugin để có danh sách chính xác.

## Đối chiếu với BIGSPY AI Theme WP (06-11-2026)

**Chỗ đặt công tắc đã có sẵn:** trang tính năng tùy chọn `inc/features.php` (mặc định tắt, file trong `inc/features/` chỉ được nạp khi bật, cài đặt riêng qua `bst_feature_fields`). Chế độ BIGSPY AI List thành tính năng `listing` ở đây: `bst_feature_on( 'listing' )`, mã trong `inc/features/listing.php` và thư mục con. Tắt thì không nạp dòng nào.

**Ổ khoá khối mở rộng từ cái có sẵn:** `inc/block-manager.php` đã ẩn khối khỏi trình chèn mà nội dung cũ vẫn hiển thị. Khối listing dùng cùng danh sách `bst_block_names()`, thêm trạng thái "khoá" (hiện kèm ổ khoá) bên cạnh "ẩn".

| Theme cũ | Theme mới đã có | Việc cần làm |
| --- | --- | --- |
| Header, footer builder, Customizer chung (typography, màu, nút, layout) | header-builder, footer-design, customizer | Không chuyển; cấu hình lại trên theme mới. |
| Sidebar builder theo khu vực (`bigspy_render_sidebar_elements`) | sidebar-options, khối widget-area | Chuyển phần Elements (blog gọn, danh mục chính) thành khối listing. |
| Khối bố cục chung (section, row, column, heading, text, button, tabs, code, callout, badge, divider) | Có tương đương `bst/*` | Không chuyển. Viết bộ đổi nội dung cũ sang khối mới, hoặc giữ khối `bigspy/*` cũ ở dạng chỉ hiển thị. Cần đếm số trang trên bigspyai.com đang dùng. |
| listing-grid, category-grid, search, elements, home-seo-divider | products, products-list, product-categories (chưa có trường listing) | Khối listing mới, có ổ khoá. |
| Thẻ, trang chi tiết listing, giá, rating, facts, thuộc tính động, disclosure | product-design, template product-design, star-rating | Gắn vào template sản phẩm khi bật chế độ. |
| Schema ItemPage, BreadcrumbList, Organization (Rank Math) | Chưa có schema listing | Chuyển nguyên. |
| Sponsored, cột AD, page heroes, nút điểm đến | banner, zoom-hero | Chuyển Sponsored/AD; hero dùng khối có sẵn. |
| Trang Categories, trang chủ, tìm kiếm | patterns, pattern-studio, theme-builder | Làm pattern và template listing. |

### Ràng buộc giữa theme cũ và ba plugin (đo bằng grep)

- Plugin gọi vào theme chỉ 2 hàm: `bigspy_dynamic_attributes`, `bigspy_theme_integration_version`.
- Theme gọi vào plugin: `bigspy_popular_query_args` (Popular), và các hook admin `bigspy_ai_admin_tabs_nav`, `bigspy_ai_admin_tabs_panels`, `bigspy_ai_customize_save`.
- Filter dùng chung: `bigspy_listing_url`, `bigspy_dynamic_listing_attributes`, `bigspy_theme_external_url`.

Ràng buộc mỏng, nên lớp tương thích chỉ cần vài hàm và hook này; các hàm `bigspy_*` khác (237 hàm) không cần giữ tên.

## Lộ trình

1. **G-74 Module listing lõi:** `inc/listing/` chỉ tải khi chọn chế độ Listing và có WooCommerce. Gồm bộ chọn chế độ ở trang About và Customizer. Đọc meta, hộp meta, thẻ listing, giá/rating/facts, disclosure, schema. Lớp tương thích `bigspy_*`.
2. **G-75 Sponsored và cột AD.**
3. **G-76 Block listing:** listing-grid, category-grid, listing search, sidebar elements; giữ shortcode cũ.
4. **G-77 Template:** trang chi tiết listing, lưu trữ danh mục, trang Categories, kết quả tìm kiếm, trang chủ mẫu (pattern) kiểu thư mục.
5. **G-78 Cài đặt và AI Designer:** tùy chọn listing trong Customizer, kiến thức AI Designer, bản dịch.
6. **G-79 Chuyển bigspyai.com (cần owner):** thử trên staging, so URL/slug, schema, breadcrumb, sitemap; rồi chuyển site thật và tắt "Discourage search engines". Sau khi chuyển, dựng trang bigspyai.com/bigspy-ai-theme-wp (tiếng Anh) bằng khối **Theme Showcase** có sẵn từ 1.82.0: phiên bản, tính năng và mục "What is new" lấy từ trang About nên tự cập nhật theo mỗi bản theme, không nhập tay. Nội dung tiếng Anh tham khảo ở `brand/bigspyai-page/page-content-en.html`; mu-plugin changelog GitHub trong cùng thư mục chỉ dùng nếu cần trước khi chuyển.
7. **G-80 Ngừng theme cũ:** sau khi site chạy ổn, lưu trữ repo `theme-bigspy-ai-list`.

Tiêu chí chung: không mất dữ liệu sản phẩm, URL giữ nguyên, site không có WooCommerce hoặc tắt Listing mode không bị ảnh hưởng, các test chính đạt.
