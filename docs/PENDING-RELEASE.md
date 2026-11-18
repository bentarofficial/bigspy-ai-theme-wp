# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

Đã phát hành ở 1.97.0: G-184 nhãn chỉnh nhanh trang listing, G-185 nguồn Popular cho cả site. Đã phát hành ở 1.96.1: giao diện mềm cho Floating contact. Đã phát hành ở 1.96.0: marquee nâng cấp, Floating contact, độ rộng sidebar theo trang, nút Add to trong List View. Đã phát hành ở 1.95.0: S-13-a, dòng hoa hồng affiliate mặc định tắt, G-167 Listing texts, sửa ô tìm Customizer. Đã phát hành ở 1.94.0: G-134-d, khoảng cách khối, banner điện thoại. Đã phát hành ở 1.93.0: Bento/Stacking Cards options, Footer scroll effect. Đã phát hành ở 1.92.0: G-163, G-164, G-134-c. Đã phát hành ở 1.91.0: G-162, G-134-a, G-133-a/b/c, G-132-a/b/c/d, G-129, G-130, G-131, G-141, G-147, G-151, G-94, G-100, G-103, G-113, G-117, G-118, G-119, G-120, G-123. Đang chờ:

- **G-197 Blog layouts (Customizer):** chọn bố cục bằng hình minh hoạ (posts layout, sidebar blog/danh mục/bài/trang, title layout, featured); mục mới **Featured posts** (tắt, 1 bài lớn, 1+2, 1+4, hàng 3, slider có mũi tên + tự chạy; nguồn: bài ghim, mới nhất, danh mục, tag, nhiều bình luận; chiều cao, excerpt, không lặp bài trong danh sách); mục mới **Category & tag pages** (layout/cột/sidebar riêng, mặc định "Giống trang blog", mô tả danh mục trên/dưới/ẩn, bật Featured cho danh mục); **ghi đè theo từng danh mục/tag** trong màn hình sửa danh mục; title layout thêm "Tiêu đề cạnh ảnh" và "Banner tràn ngang"; tuỳ chọn phụ tự ẩn khi không liên quan. Kiểm thử: lint PHP/JS; Playground WP 6.8 (control, sanitize, trang blog/danh mục/bài, loại trừ bài nổi bật), xem bằng trình duyệt desktop + mobile và Customizer (đổi style xem trước ngay). Chưa: `product_cat`/danh mục listing (việc sau), chưa test trên site thật. Khi phát hành: thêm vào Có gì mới/Giới thiệu và chuỗi dịch vi. Nhánh `feature/g-197-blog-customizer`, chưa push.

- **G-199 Control Responsive và Spacing dùng chung (Customizer):** loại trường `responsive` (giá trị desktop giữ id cũ, thêm `<id>_tablet`, `<id>_mobile`, để trống = theo màn lớn hơn; tab thiết bị đồng bộ nút thiết bị Customizer) và `spacing` (4 cạnh, khoá liên kết, đơn vị px/em/rem/%, theo thiết bị, lưu JSON). CSS biến in theo breakpoint 849px/549px của theme; xem trước tức thì. Đã chuyển H1–H4 sang Responsive; thêm **Title bar padding** (mặc định giữ 48px 0). AI Designer vẫn sửa được H1–H4 (desktop). Kiểm thử: lint PHP/JS, features-check 4 test G-199 đạt. Chưa xem UI Customizer trên trình duyệt. playground-check báo lỗi cũ `bst/marquee-item` thiếu catalog profile (không do đợt này). Khi phát hành: chuỗi dịch vi, Có gì mới. Nhánh `feature/g-197-blog-customizer`, chưa push.

- **G-201 Design tokens (Style → Global style):** bo góc thẻ, bóng đổ 3 mức (`--bs-shadow-sm/md/lg` luôn có để khối và CSS riêng dùng), khoảng cách section theo thiết bị. Áp cho thẻ blog `.bs-card`, thẻ sản phẩm, thẻ listing, widget sidebar dạng box; khối Section để padding mặc định (80/60/40) sẽ đọc token. Để trống = giữ giao diện cũ; Shop card radius và Listing design radius/shadow vẫn ghi đè. Độ rộng nội dung đọc không thêm vì đã có `single_width`. Kiểm thử: lint, features-check 4 test G-201 đạt. Chưa xem trên trình duyệt; editor chưa đọc token khoảng cách section. Nhánh `feature/g-197-blog-customizer`, chưa push.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
