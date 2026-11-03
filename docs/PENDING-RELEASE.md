# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

- **G-54 Thông báo free shipping:** thanh tiến độ đọc đủ quy tắc của phương thức Free shipping trong WooCommerce: `requires` = min_amount/either/both, `ignore_discounts`, coupon miễn phí vận chuyển; không hiện khi `both` chưa có coupon hoặc phương thức chỉ cần coupon; nhiều package khác quy tắc thì không hiện. File `inc/checkout-extras.php`. **Kiểm thử:** lint đạt, đã đối chiếu dòng-với-dòng `WC_Shipping_Free_Shipping::is_available`; thêm `tools/woo-check.cjs` + `tools/tests/woo-integration.php` (11 case) nhưng chưa chạy được vì Playground hết bộ nhớ (ổ C ~100 MB, cả features-check cũng lỗi). **Khi phát hành:** Có gì mới một dòng.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
