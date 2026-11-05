# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

### G-57 — Nền schema Form

- **Yêu cầu/tính năng:** Form native có ID trường bất biến, `schemaVersion` 2, điều kiện hiện/ẩn trường và kiểm tra phụ thuộc. Đây là nền cho G-58 (tính giá).
- **Đã thay đổi:** `inc/form-schema.php` (mới): chuẩn hoá ID, kiểm tra vòng lặp/tham chiếu thiếu/trùng tên/tự tham chiếu, tính hiện/ẩn. `form-native.php`: trường ẩn theo điều kiện thì bỏ qua bắt buộc/xác thực, giá trị rỗng, không vào mail/entry. Editor gán ID cho form cũ khi mở, có UI điều kiện, chặn xoá trường đang được tham chiếu và báo lỗi điều kiện. JS phía khách ẩn/hiện, khoá control ẩn. Form cũ giữ `name`/mail-tag; ID suy từ name nên di chuyển lặp lại không đổi dữ liệu.
- **Kiểm thử:** `php tools/tests/form-schema-check.php` 19/19 đạt (đổi nhãn/tên/thứ tự, xoá trường đang dùng, vòng lặp, migration lặp). Đã thử luồng gửi trên server với WP giả lập (form cũ, trường ẩn, gửi giá trị giả cho trường ẩn) và JS phía khách trong Chromium 375px. Chưa thử editor trong Playground vì môi trường không tải được WordPress.
- **Khi phát hành cần bổ sung:** Có gì mới (điều kiện trường Form); kiến thức AI về `id`/`conditions` để ở G-59.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
