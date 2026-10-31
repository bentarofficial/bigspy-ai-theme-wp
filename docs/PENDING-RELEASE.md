# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

- **Yêu cầu/tính năng:** Horizontal Scroll trên máy tính hiện đủ 3 thẻ, từ 4 thẻ mới trượt; section không bị co thành khung trắng khi ghim.
- **Đã thay đổi:** từ 1024px, thẻ rộng tối đa 1/3 khối; section full width giữ nguyên chiều rộng khi ghim.
- **Kiểm thử:** đo trên trang chủ thật 1920px bằng CSS chèn: thẻ 435px, không tràn, không ghim. Chưa thử khối 4 thẻ.
- **Khi phát hành cần bổ sung:** Có gì mới, README Updates.
- **Git:** `feature/ai-designer`.
- **Trạng thái:** đã commit và push; chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
