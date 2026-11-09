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

- **Yêu cầu/tính năng:** G-101, thẻ listing tự thích nghi theo chiều rộng của chính nó, giống theme BIGSPY AI List cũ.
- **Đã thay đổi:** `inc/listing/blocks.php` (thẻ có watermark logo mờ, khối `__brand/__head/__body`, số dòng tiêu đề/mô tả, tag, bật/tắt logo/rating/Featured/watermark; mặc định đọc `bigspy_card_show_*` của theme cũ; Sponsored chèn sau mỗi N hàng hoặc trên đầu lưới), `inc/listing/sponsored.php` (thêm `__in`), `assets/css/listing.css` (container query cho thẻ listing và Sponsored, thay cách sửa theo vị trí của G-105), 10 thuộc tính mới trong `listing-elements`/`listing-grid`, nhãn trong `blocks-listing.js`.
- **Kiểm thử:** woo-check đạt (2 test mới: thẻ ngang có watermark và số dòng; ẩn watermark theo khối); features-check 137 ok, playground-check đạt, previews đạt. Xem thử trên bigspyai.com bằng cách chèn tạm markup/CSS mới trong trình duyệt.
- **Khi phát hành cần bổ sung:** Có gì mới: "Thẻ listing tự co giãn theo chỗ đặt, có watermark logo, tag, chọn số dòng, chèn Sponsored giữa các hàng".
- **Git:** `feature/ai-designer`, e0af524 và commit chỉnh ngưỡng sau đó.
- **Trạng thái:** chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
