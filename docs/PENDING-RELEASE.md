# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

- **Yêu cầu/tính năng:** G-45, nguyên tắc thiết kế mặc định cho AI Designer.
- **Đã thay đổi:** `prompt.php` thêm mục banner đầu trang, dùng đa dạng khối, khối động, `bst/code` chỉ để minh hoạ, hiệu ứng khối/chữ/hover/cuộn và chất lượng thiết kế.
- **Kiểm thử:** lint và bộ Designer; chưa thử với AI thật.
- **Khi phát hành cần bổ sung:** Có gì mới, README Updates.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

- **Yêu cầu/tính năng:** G-46 (làm thay, G-46-T), hồ sơ block và công cụ `block_catalog`.
- **Đã thay đổi:** `ai-designer/knowledge/blocks.php` có hồ sơ 62 block cấp trên (nhóm nhu cầu, dùng/tránh, dữ liệu cần, độ nặng, ghim cuộn, mobile, khối đi cùng, thuộc tính tối thiểu); công cụ đọc `block_catalog` trả nhóm hoặc hồ sơ theo nhu cầu; prompt và `knowledge/theme.md` chuyển sang dùng nó. `list_blocks` giữ nguyên.
- **Kiểm thử:** lint, bộ Designer 157 đạt (6 test mới); chưa thử với AI thật.
- **Khi phát hành cần bổ sung:** Có gì mới, README Updates.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
