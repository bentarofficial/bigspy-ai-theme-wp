# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

- **Yêu cầu/tính năng:** `G-20`, gộp `G-11`: quy tắc khối bất biến cho AI Designer.
- **Đã thay đổi:** quy tắc cố định đặt sau ngữ cảnh người dùng trong prompt. Máy chủ chỉ nhận `raw` không đổi từ chính bài đó hoặc pattern theme; từ chối ghi khi `editable_percent` dưới 90% và thấp hơn trước; từ chối HTML bố cục trong `bst/text`; trần `bst/code` 4 khối, 10 KB kể cả khi đã duyệt. Áp dụng cả `ux_block`, `theme_layout`. Nội dung cũ vẫn giữ và sửa được nếu không làm tệ hơn.
- **Kiểm thử:** xem `tools/tests/designer-integration.php`; chưa thử với AI thật (`G-07`).
- **Khi phát hành cần bổ sung:** Có gì mới, README Updates, kiến thức AI (`knowledge/theme.md`).
- **Git:** `feature/ai-designer`, xem commit `G-20`.
- **Trạng thái:** đã triển khai; chờ owner yêu cầu phát hành.

- **Yêu cầu/tính năng:** `G-24`: công cụ chuyển CF7 còn sót 2 trường hợp.
- **Đã thay đổi:** `inc/form-migrate.php` chuyển cả khối `bst/form` đang chọn CF7 (kể cả lồng trong khối khác), giữ thiết lập riêng của khối; mang chuyển hướng của Redirection for Contact Form 7 (dạng action và dạng cũ) sang khối. Chuyển hướng có điều kiện, chọn từng trường hoặc có thẻ mail trong URL được báo ở cột "Không chuyển được". Khối đã có `redirectUrl` riêng thì giữ nguyên.
- **Kiểm thử:** nhóm `cf7` trong `tools/tests/features-integration.php` đạt (1 worker, vì máy thiếu RAM); chưa chạy đủ `features-check`, chưa thử với plugin thật trên site.
- **Khi phát hành cần bổ sung:** Có gì mới, README Updates.
- **Git:** `feature/ai-designer`, xem commit `G-24`.
- **Trạng thái:** đã triển khai; chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
