# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

- **Yêu cầu/tính năng:** G-102, khối Listing elements có lại đủ tuỳ chọn của khối BIGSPY AI List cũ.
- **Đã thay đổi:** `inc/listing/blocks.php` (View more: chữ, căn lề, trang đích tự động/Listings/Featured/Popular/Categories/Blog/URL riêng; Load more tải tiếp tại chỗ qua REST `bst/v1/listing-elements`; chế độ slider; khoảng cách; nút Visit cạnh tiêu đề hoặc dưới chữ và căn lề; blog dạng thẻ/hàng gọn/tiêu đề đánh số với ảnh, ngày, tác giả, chuyên mục, trích đoạn; lưới danh mục: số cột, viền, bo góc, hover nâng/zoom/bóng/tốc độ, View all), `assets/js/listing-elements.js` mới, CSS tương ứng, 43 thuộc tính mới trong `blocks/listing-elements/block.json`, panel editor chia nhóm và chỉ hiện tuỳ chọn hợp với loại nội dung.
- **Kiểm thử:** woo-check 47 ok (6 test mới: View more, Load more + endpoint, slider, blog titles, style lưới danh mục); features-check 135 ok, chỉ lỗi "What is new" do lỗi cú pháp của 1.86.0 (đã sửa ở 1.86.1); previews đạt. Chưa xem panel editor và slider/Load more trên trình duyệt.
- **Khi phát hành cần bổ sung:** Có gì mới: "Listing elements: View more hoặc Load more, slider, kiểu blog, viền và hover cho lưới danh mục, tuỳ chọn chia nhóm". Lưu ý: View more mặc định bật như khối cũ, trang dựng bằng theme mới sẽ hiện thêm link "View more →".
- **Git:** `feature/ai-designer` 1b77732.
- **Trạng thái:** chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
