# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

- **Yêu cầu/tính năng:** Horizontal Scroll xuất bản chiếm quá nhiều diện tích, không khớp editor.
- **Đã thay đổi:** Panel width tính theo % chiều rộng khối (cqw), không còn theo màn hình; editor áp dụng đúng Panel width và Gap; khi ghim, khối cao vừa nội dung và canh giữa màn hình thay vì chiếm 100svh; bỏ lề 4vw khi ghim. Trang có sẵn: thẻ hẹp hơn trên màn hình lớn.
- **Kiểm thử:** mô phỏng CSS và phép đo mới trên trang chủ thật: 1400px và 1920px (46 → thẻ 630-639px, ghim, cao 473px; 31 → 3 thẻ một hàng, không ghim), 375px không tràn ngang; features-check, check-block-previews, phplint đạt. Chưa chạy Playground và chưa thử trong editor thật.
- **Khi phát hành cần bổ sung:** Có gì mới, README Updates; biên dịch lại `vi.mo` nếu cần.
- **Git:** `feature/ai-designer`.
- **Trạng thái:** đã triển khai, chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
