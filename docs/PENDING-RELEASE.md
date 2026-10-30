# Thay đổi chờ phát hành

Theo quy tắc trong [roadmap-vi.md](roadmap-vi.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

### Icon khối có màu theo nhóm (Claude, 30-10-2026)

- **Yêu cầu:** icon các khối BIGSPY trong bảng chèn khối có màu, khác nhau để dễ nhận diện.
- **Đã thay đổi:** `assets/js/block-previews.js` gán `icon.foreground` theo 7 nhóm: bố cục (chàm #4f46e5), chữ (xanh dương #2563eb), media (hồng #db2777), chuyển động (tím #7c3aed), tương tác (cam #ea580c), cửa hàng/bảng giá (xanh lá #16a34a), thẻ nội dung/uy tín (xanh ngọc #0891b2). Khối mới cần thêm vào bảng nhóm `FAMILY`.
- **Kiểm thử:** `node --check`; Playground editor: 60/60 khối `bst/*`, `bigspy/*` có `icon.foreground`, ảnh chụp bảng chèn khối hiện đúng màu.
- **Khi phát hành cần bổ sung:** Có gì mới (icon khối có màu theo nhóm).

Trước đợt này: Chưa có thay đổi sản phẩm mới sau bản 1.75.0. Bản 1.75.0 (29-10-2026) đã gom: bản xem trước và mô tả khối, sửa lỗi AI Designer, tên khối tiếng Việt, nâng cấp khối Sản phẩm + khối Danh sách sản phẩm, tùy chọn giỏ hàng/thanh toán cổ điển, đổi tên Infinity Flow/Spotlight Panels. Kiểm thử phát hành: check-block-previews (61 khối), playground-check --welcome, updater-playground-check, build-theme.py (332 tệp, sha256 c2ef6133…).

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
