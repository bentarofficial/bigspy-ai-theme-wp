# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

### G-74 Chế độ BIGSPY AI List và module listing lõi

- **Yêu cầu/tính năng:** tính năng tùy chọn `listing` (Features, mặc định tắt) biến sản phẩm WooCommerce thành listing thư mục; bước đầu gộp theme BIGSPY AI LIST (docs/LISTING-MIGRATION.md).
- **Đã thay đổi:** `inc/features/listing.php` + `inc/listing/` (core, compat, meta-box, display, schema) chỉ nạp khi bật; đọc 21 trường ACF và các key `_bigspy_*` giữ nguyên, ACF tùy chọn; hộp meta "BIGSPY Listing Information" và hộp "Listing fields" khi không có ACF; tóm tắt listing + FAQ trên trang sản phẩm; schema ItemPage/Breadcrumb/Organization/FAQPage (gộp vào Rank Math hoặc tự in); lớp tương thích `bigspy_dynamic_attributes`, `bigspy_theme_integration_version` (1.6.0), `bigspy_get_field`, `bigspy_listing_url`, `bigspy_theme_external_url` và filter `bigspy_listing_url`, `bigspy_dynamic_listing_attributes`, `bigspy_theme_external_url`. Nhóm khối "BIGSPY AI List" (`bigspy-list`) ngay dưới BIGSPY AI Builder, khối đầu tiên `bst/listing-info`; khi tắt chế độ: ổ khoá đỏ góc trên phải icon, chèn vào ra thẻ khoá có nút bật chế độ (REST `bst/v1/feature`, chỉ `manage_options`), ngoài trang không hiển thị. Hồ sơ AI Designer cho `bst/listing-info` và `bigspy/theme-showcase` (thiếu từ G-72, làm playground-check đỏ).
- **Kiểm thử:** features-check đạt trên WP 7.1.3 và 6.5 (21 mục listing mới); playground-check 203 PASS; check-block-previews 78 khối; editor 7.1.3: nhóm khối đúng thứ tự, icon khoá, thẻ khoá, nút bật chế độ hoạt động. Chưa chạy với WooCommerce thật (display/schema trên trang sản phẩm), chưa thử editor trên 6.5, chưa thử với ACF và Rank Math, chưa có bản dịch (G-78).
- **Khi phát hành cần bổ sung:** Có gì mới, About (một dòng về chế độ), kiến thức AI Designer về chế độ listing, bản dịch.
- **Git:** nhánh `feature/ai-designer`, xem commit G-74.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

### G-75 Sponsored và cột AD (BIGSPY AI List)

- **Yêu cầu/tính năng:** chuyển Sponsored và cột AD của theme cũ (`inc/categories-sponsored.php`).
- **Đã thay đổi:** `inc/listing/sponsored.php`, `assets/js/sponsored.js`, CSS trong `listing.css`. Cột AD trong Products bật/tắt một chạm (giữ action `bigspy_toggle_ad` và nonce cũ); listing AD có URL đối tác vào ô Sponsored, xoay vòng theo lượt xem (localStorage, bước nhảy, phiên bản xoay đổi khi tập AD đổi, xoá cache LiteSpeed); một ô Sponsored dưới tóm tắt trang sản phẩm; shortcode `[bigspy_sponsored_grid]`; cài đặt trong Features (bật ô, chữ nút, tab mới, bước nhảy), mặc định lấy từ option cũ `bigspy_categories_promotions`; giữ `bigspy_listing_is_ad`, `bigspy_sponsored_refresh_rotation`. Featured trên trang Categories để G-77.
- **Kiểm thử:** features-check đạt trên WP 7.1.3 và 6.5 (7 mục sponsored mới); check-block-previews đạt. Chưa thử trên trình duyệt với WooCommerce thật (cột AD, xoay vòng).
- **Khi phát hành cần bổ sung:** gộp với G-74.
- **Git:** nhánh `feature/ai-designer`, commit G-75.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

### G-76 Block listing và shortcode cũ (BIGSPY AI List)

- **Đã thay đổi:** 4 khối trong nhóm BIGSPY AI List, giữ đúng tên và thuộc tính của theme cũ để trang cũ hiển thị không cần sửa: `bigspy/listing-grid`, `bigspy/category-grid`, `bigspy/search`, `bigspy/elements` (Featured/Latest/Popular/Related/All, danh mục, bài blog, kèm ô Sponsored). Thẻ listing `bst_listing_card` (Sponsored/Featured, rating, giá). Shortcode cũ `bigspy_search`, `bigspy_elements`, `bigspy_categories`, `bigspy_home_sponsor`. Khoá khối khi tắt chế độ như `bst/listing-info`. Hồ sơ AI Designer cho 4 khối. Sửa: ô Sponsored không đọc danh sách AD khi đang tắt. Bỏ qua: slider, "load more", tuỳ chỉnh viền/hover chi tiết của Elements cũ; `bigspy_home_destination_buttons` để G-77.
- **Kiểm thử:** woo-check WP 7.1.3 21 mục (10 mục listing mới với WooCommerce 11.2.1); features-check 7.1.3 và 6.5; playground-check 203 PASS; check-block-previews 82 khối. WooCommerce 11.2 cần WP ≥ 7.0 nên chưa thử listing với Woo trên 6.5; chưa xem giao diện thẻ trên trình duyệt.
- **Git:** nhánh `feature/ai-designer`, commit G-76.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

### G-77 Template listing (BIGSPY AI List)

- **Đã thay đổi:** `inc/listing/templates.php`, `woocommerce/content-listing.php`. Khi bật chế độ: trang Shop, danh mục, thẻ và tìm kiếm sản phẩm dùng thẻ listing (tắt được trong Features); trang listing thay "sản phẩm liên quan" bằng "Related listings" (tắt được). Nhóm pattern "BIGSPY AI List": search hero, danh mục, featured, latest, trang chủ thư mục, trang Categories. Trang chi tiết listing đã có tóm tắt, FAQ, Sponsored, schema từ G-74/G-75. Chưa làm: nút điểm đến trang chủ (`bigspy_home_destination_buttons`), hero riêng từng trang của theme cũ (dùng khối banner sẵn có).
- **Kiểm thử:** woo-check WP 7.1.3 24 mục (pattern chỉ dùng khối đã đăng ký, vòng lặp shop đổi sang thẻ listing, tắt được). Chưa xem trang trên trình duyệt.
- **Git:** nhánh `feature/ai-designer`, commit G-77.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

### G-78 Cài đặt, AI Designer, bản dịch cho listing

- **Đã thay đổi:** cài đặt listing nằm trong trang Features (Sponsored, thẻ listing ở trang lưu trữ, listing liên quan); trang About có một dòng về chế độ BIGSPY AI List kèm liên kết Features. AI Designer bỏ các khối nhóm BIGSPY AI List khi chế độ tắt hoặc thiếu WooCommerce (hồ sơ khối đã có từ G-74/G-76). Thêm 117 chuỗi tiếng Việt (vi.po/.mo/.l10n.php, pot). Chưa dịch: nhãn tự sinh trong bảng thuộc tính khối (Tagline, Show…), chuỗi số nhiều "%s listing".
- **Kiểm thử:** features-check 121 ok, playground-check 204 PASS (thêm kiểm tra AI Designer bỏ khối listing khi tắt), woo-check 24 ok.
- **Khi phát hành cần bổ sung:** Có gì mới và README: chế độ BIGSPY AI List (G-74–G-78).
- **Git:** nhánh `feature/ai-designer`, commit G-78.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

- G-88: Re-register the 14 legacy BIGSPY AI List layout blocks (`bigspy/section`, `row`, `column`, `heading`, `text`, `button`, `button-group`, `tabs`, `tab`, `code`, `callout`, `badge`, `divider`, `home-seo-divider`) display-only and hidden from the inserter (`legacy-list/`, `inc/legacy-list-blocks.php`), so old bigspyai.com content keeps rendering. Styles extracted from the old main.css (~18 KB, loaded only when a block is on the page).

### G-87 Quyền cho tool của plugin trong AI Designer

- **Yêu cầu/tính năng:** cho BIGSPY AI List Suite thêm tool vào AI Designer mà không nới quyền.
- **Đã thay đổi:** `ai-designer/includes/permissions.php`: filter `bst_aid_extra_tool_groups` để plugin khai báo tool mới là `read` hoặc `content`; nhóm khác bị bỏ qua, tên tool lõi không khai báo lại được, tool không khai báo vẫn bị chặn (level 3). Tool `content` theo đúng luật phê duyệt và mức quyền nhóm Nội dung như tool lõi.
- **Kiểm thử:** playground-check 204 PASS, features-check đạt; Playground với List Suite: tool đọc chạy không cần task, tool ghi đòi task, `publish`/tool lạ vẫn bị chặn dù plugin khai báo sai.
- **Khi phát hành cần bổ sung:** một dòng trong Có gì mới cùng List Suite.
- **Git:** nhánh `feature/ai-designer`, commit G-87.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

- G-90: BIGSPY AI List mode adds Customizer → Layout → Homepage sidebar (off by default): turn it on, pick "Start after block" and "End before block" from the homepage's top-level blocks; only that range sits beside the new "Homepage sidebar" widget area, the rest stays full width. Replaces the old Homepage SEO Full-width Divider; with no end block chosen, an existing `bigspy/home-seo-divider` still ends the range.
