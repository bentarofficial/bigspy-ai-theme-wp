# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

- **P1 free shipping khớp điều kiện WooCommerce:** thanh tiến độ đọc `requires` min_amount/either/both, `ignore_discounts`, coupon free shipping và làm tròn theo số lẻ của shop; `both` khi đủ tiền nhưng thiếu coupon sẽ nhắc áp mã. Kiểm thử: 8 case logic bằng stub PHP; chưa thử giỏ thật/nhiều package, chưa hỗ trợ Cart/Checkout blocks. Phát hành: thêm vào Có gì mới.
- **P1 ảnh banner từ URL:** banner dùng URL trực tiếp không còn lazy-load; nhận `fetchpriority="high"` nếu WordPress chưa dùng suất ưu tiên của trang (`bst_url_img_loading()`). Block Image từ URL vẫn lazy. Kiểm thử: `php -l`; chưa đo LCP/CLS trên fixture.
- **G-41 bước 1 — bỏ blocks.js ở trang không cần:** tùy chọn Customizer mới `perf_split_js` (mặc định **tắt**) trong output buffer; chỉ dò marker trong `<body>`, bỏ qua `<style>/<script>`; luôn giữ trên trang WooCommerce; filter `bst_block_js_markers` để plugin thêm marker. Tiết kiệm ~35 KB/1 request trên trang thường. Kiểm thử: 5 case buffer bằng stub PHP; **chưa chạy Playground** (proxy chặn wordpress.org), chưa đo trước/sau trên fixture. main.js chưa tách. Phát hành: thêm vào Có gì mới.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
