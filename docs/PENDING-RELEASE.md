# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

### G-57 — Nền schema Form native

- **Yêu cầu/tính năng:** ID trường bất biến, version schema, điều kiện hiện/bắt buộc theo trường khác và kiểm tra phụ thuộc; giữ form/mail-tag cũ.
- **Đã thay đổi:** `inc/form-schema.php` mới (ID `f_<name>` cho trường cũ, idempotent; luật `is/is_not/empty/not_empty/contains/gt/lt`, all/any; báo trùng tên, tham chiếu thiếu, tự tham chiếu, vòng lặp, toán tử lạ). Server tính lại trạng thái khi gửi: trường ẩn không bắt buộc và giá trị bị bỏ (kể cả request cố gửi). Markup render đúng trạng thái ban đầu; `blocks.js` cập nhật ẩn/hiện/bắt buộc khi nhập. Editor: mục “Hiện trường này” và “Chỉ bắt buộc khi…”, cảnh báo lỗi trên từng trường, chặn xoá trường đang được điều kiện dùng. Thuộc tính `formSchema`. Luật lỗi bị bỏ qua trên site (trường vẫn hiện, không mất dữ liệu).
- **Kiểm thử:** `php tools/tests/form-schema-check.php` 21/21 (rename/reorder/ẩn/xoá, migration lặp, vòng lặp). Harness PHP cho submit: 4 case đạt. Chromium chạy markup + `blocks.js`: ẩn/hiện/required đúng, không lỗi JS. **Chưa** chạy Playground/editor thật (wordpress.org bị chặn trong môi trường này); UI editor mới chỉ qua kiểm tra cú pháp.
- **Khi phát hành cần bổ sung:** Có gì mới (điều kiện trường Form), kiến thức AI về `show`/`requiredWhen`.
- **Git:** nhánh `ccr-6e0ee3d0-m48rjp`.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
