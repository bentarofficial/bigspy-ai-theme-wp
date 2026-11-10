# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

Không có (G-89, G-102, G-106, G-109, G-110, G-115, G-116, G-124, G-126 đã phát hành ở 1.87.0).

- **Yêu cầu/tính năng:** G-89 (owner 10-11): Sponsored có khối riêng, thẻ bấm được cả thẻ, nút mảnh như theme cũ.
- **Đã thay đổi:** khối mới `bigspy/sponsored` (`blocks/sponsored/`, nhóm BIGSPY AI List): số thẻ, số cột máy tính/tablet/điện thoại, kiểu nút (theo cài đặt chung / không nút / viền nhẹ / đặc), chữ nút, nhãn "Sponsored", mô tả, mở tab mới. `inc/listing/sponsored.php`: `bst_render_sponsored()`, `bst_sponsored_slot()` nhận tuỳ chọn, cả thẻ là một liên kết tới đối tác (link phủ toàn thẻ, nút chỉ còn là nhãn trang trí); cài đặt chung mới "Sponsored button" ở trang Features (mặc định: không nút). `assets/css/listing.css`: nút kiểu viền nhẹ giống theme cũ (nền #f6f9fb, viền #dbe7f0, chữ 12px đậm 650), kiểu đặc là tuỳ chọn; nút Visit của thẻ listing cũng đổi sang kiểu nhẹ; lưới Sponsored tự giãn khi chỉ có một thẻ. `assets/js/sponsored.js` cập nhật mọi liên kết khi xoay vòng. Shortcode `[bigspy_sponsored_grid]` và `[bigspy_sponsored_card]` giữ nguyên, nay dùng cùng bộ dựng.
- **Kiểm thử:** features-check đạt (3 test mới), check-block-previews đạt 83 khối, woo-check đạt. Chưa xem khối trong editor và trên bigspyai.com.
- **Khi phát hành cần bổ sung:** Có gì mới: "Khối Sponsored riêng với tuỳ chọn nút, cột, nhãn; cả thẻ Sponsored là liên kết; nút kiểu nhẹ". Lưu ý: thẻ Sponsored mặc định không còn nút "Open site"; muốn có nút, chọn kiểu nút ở Features hoặc trong khối. Kiến thức AI Designer nên nêu khối `bigspy/sponsored` (G-123).
- **Git:** `feature/ai-designer`, xem HANDOFF.
- **Trạng thái:** chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
