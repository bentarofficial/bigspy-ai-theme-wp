# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

### G-65 — AI Designer giữ nguyên block không hỗ trợ

- **Yêu cầu/tính năng:** trang có Query Loop, synced pattern (`core/block`), block plugin bên thứ ba hoặc core block ngoài danh sách AI trước đây làm `update_page` từ chối cả trang.
- **Đã thay đổi:** `get_page` đánh dấu các block này `read_only`; AI chỉ được giữ (gửi lại `raw` không đổi), di chuyển hoặc bỏ. Server so dấu vân tay với chính trang đang sửa và ghi bản gốc của trang, không dùng văn bản model gửi; `raw` bị sửa, thiếu, đã mất khỏi trang hoặc chép sang trang khác bị từ chối trước khi ghi. Không mở quyền sửa các block này; quy tắc block/`editable_percent`/`bst/code` giữ nguyên. Cập nhật mô tả tool và prompt.
- **Kiểm thử:** `tools/playground-check.cjs` 216/216 đạt, gồm 8 kiểm tra G-65 (round trip, binding metadata, khoảng trắng, di chuyển/bỏ, raw giả, chép sang trang khác, chỉ đổi tiêu đề, synced pattern không bị sửa). Chạy trên WordPress 6.6-RC3 offline vì môi trường cloud chặn tải 7.1.3; nên chạy lại trên 7.1.3 khi có máy local.
- **Khi phát hành cần bổ sung:** Có gì mới và kiến thức AI Designer: AI giữ nguyên block chỉ đọc.
- **Git:** nhánh `ccr-8b690458-vk9zem`.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
