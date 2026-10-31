# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

- **Yêu cầu/tính năng:** Horizontal Scroll trượt theo cuộn dọc trên mọi màn hình.
- **Đã thay đổi:** ghim khi panel tràn ngang và vừa chiều cao màn hình, cuộn ngược thì panel trượt ngược; panel vừa đủ thì hiện thẳng hàng; panel quá cao thì giữ hàng vuốt ngang; bỏ nút lật và chấm chỉ số; bù segments.json mà 1.79.1 thiếu CSS mobile.
- **Kiểm thử:** trang thử cục bộ 1159px và 375px; lint, features-check, block previews. Chưa thử cảm ứng thật, chưa thử trên site.
- **Khi phát hành cần bổ sung:** Có gì mới, changelog; dùng tools/release.py.
- **Git:** `feature/ai-designer`.
- **Trạng thái:** đã triển khai; chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
