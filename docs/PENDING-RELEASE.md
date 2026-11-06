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

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
