# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

G-74–G-78, G-87, G-88, G-90 đã phát hành trong 1.83.0.

### G-91 AI Designer theo chế độ BIGSPY AI List

- **Đã thay đổi:** lời nhắc hệ thống nói rõ: chế độ tắt thì chỉ dùng khối BIGSPY AI Theme WP, user hỏi thư mục thì nhắc bật trong Features; chế độ bật (có WooCommerce) thì thiết kế cả với khối BIGSPY AI List. Khối chỉ hiển thị (inserter false, gồm 14 khối layout cũ) không bao giờ vào danh sách khối của AI, kể cả khi bật chế độ (trước đây lọt vào). Hàm `bst_aid_listing_on()`.
- **Kiểm thử:** playground-check 205 PASS, woo-check thêm 2 mục đạt.
- **Khi phát hành cần bổ sung:** một dòng Có gì mới.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

### G-92 Hồ sơ khối đầy đủ và độ đa dạng khối

- **Đã thay đổi:** rà 69 khối: đều có hồ sơ; điền 99 trường trống (avoid, mobile, pairs) để block_catalog trả đủ thông tin chọn khối. page_plan trả `variety` (số khối nội dung khác nhau, mục tiêu theo số section, lời khuyên: section chỉ có chữ, công thức lặp, 2 section liền nhau cùng khối); prompt và rules của block_catalog yêu cầu làm theo trước khi đưa dàn ý.
- **Kiểm thử:** playground-check đạt, thêm 3 mục (hồ sơ đủ trường, page_plan khuyên khi nghèo khối, không khuyên khi đa dạng). Chưa thử với AI thật.
- **Khi phát hành cần bổ sung:** một dòng Có gì mới.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
