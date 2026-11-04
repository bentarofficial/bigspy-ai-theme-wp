# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

### Ảnh URL trực tiếp và thanh miễn phí vận chuyển (SOURCE-AUDIT P1)

- **Đã thay đổi:** Banner/Image dùng URL (không Media ID) không còn ghi cứng `loading="lazy"`; helper `bst_url_img_loading_attrs()` (`inc/blocks.php`) dùng chung bộ đếm và ngưỡng của WordPress, banner có thể nhận `fetchpriority="high"` một lần. Thanh free shipping (`inc/checkout-extras.php`) đọc `ignore_discounts`, coupon miễn phí ship cho `either`, ẩn với `both` khi chưa có coupon; số nhập tay giữ cách cũ.
- **Kiểm thử:** `php -l`; test cô lập với stub WP/Woo 11/11 đạt (giảm giá, ignore_discounts, either/both/coupon, số nhập tay; eager/lazy/fetchpriority theo ngưỡng). Playground không chạy được trong môi trường cloud (`fetch failed`); chưa thử trên giỏ hàng và trang thật, chưa đo LCP.
- **Khi phát hành cần bổ sung:** dòng Có gì mới; cần chạy Playground/site nháp có WooCommerce.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
