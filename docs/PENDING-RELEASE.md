# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

- **Yêu cầu/tính năng:** G-105, khối listing trong editor hiển thị như trang đã xuất bản; thẻ Sponsored trong lưới/sidebar không bị ép hẹp.
- **Đã thay đổi:** `inc/listing/display.php` nạp `listing.css` qua `enqueue_block_assets` khi ở admin (vào iframe editor); `assets/css/listing.css` xếp dọc thẻ Sponsored trong `.bst-sponsored-grid` và sidebar, mô tả giới hạn 3 dòng, lưới tối thiểu 300px.
- **Kiểm thử:** lint PHP đạt; thử CSS bằng cách chèn tạm trên bigspyai.com (trang chủ và editor trang Home) đúng như mong đợi. Chưa chạy features-check.
- **Khi phát hành cần bổ sung:** Có gì mới: "Khối listing nhìn giống nhau khi sửa và khi xem; thẻ Sponsored gọn hơn".
- **Git:** `feature/ai-designer`, commit bên dưới.
- **Trạng thái:** chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
