# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

- **G-67 (phần Tabs):** Tabs dọc khai báo `aria-orientation`; panel không có phần tử focus được có `tabindex=0`; hiệu ứng fade tắt khi `prefers-reduced-motion: reduce`; khởi tạo lại (`bstInitBlocks`) không gắn trùng sự kiện. Kiểm thử: Playwright (phím End/Home/mũi tên, reduced motion bật/tắt). Chưa thử screen reader/editor/Playground. Khi phát hành: ghi một dòng Có gì mới.
- **G-67 (phần Row):** AI Designer cảnh báo (không chặn, không tự sửa) khi `bst/row` bật `reverseMobile` mà cả cột đầu và cột sau đều có link/nút, vì thứ tự Tab khác thứ tự nhìn trên điện thoại; cảnh báo ghi đường dẫn block và nói rõ chỉ suy từ dữ liệu block. Kiểm thử: `php -l` và unit nhanh cho hàm phát hiện; chưa chạy trên site thật. Khi phát hành: không cần mục riêng.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
