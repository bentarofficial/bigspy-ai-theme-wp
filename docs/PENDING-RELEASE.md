# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

Chưa có thay đổi sản phẩm mới sau bản 1.76.0. Bản 1.76.0 (30-10-2026) đã gom: icon khối có màu, chuyển động đợt 1–3, đổi tên BIGSPY AI, sửa CSS ghép theo khối, số nhảy đếm. Kiểm thử phát hành: check-block-previews (76 khối), playground-check --welcome, updater-playground-check, build-theme.py (367 tệp, sha256 3834243a…).

- **Yêu cầu/tính năng:** menu Tự động phát hiện (đợt A): từ khóa chính trùng, thẻ H1, media chưa dùng, link hỏng, quét bảo mật; AI Designer đọc được báo cáo.
- **Đã thay đổi:** `inc/auto-detect.php`, `inc/auto-detect/`, `assets/js/auto-detect.js`, công cụ đọc `auto_detect_results`. Chạy tay theo đợt hoặc nền hằng tuần, tắt được. Chỉ báo cáo; xoá media và sửa/gỡ link chỉ khi admin bấm và xác nhận.
- **Kiểm thử:** `node tools/auto-detect-check.cjs` trên Playground (mạng ngoài được giả lập). Chưa chạy trên site thật.
- **Khi phát hành cần bổ sung:** Giới thiệu, Có gì mới, kiến thức AI.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

- **Yêu cầu/tính năng:** đợt B: trang Tính năng bật/tắt riêng từng mục, mặc định tắt, chỉ nạp khi bật; WebP khi tải ảnh lên; SMTP; liên kết liên quan giữa bài; mã đầu/cuối trang.
- **Đã thay đổi:** `inc/features.php`, `inc/features/`, submenu Tính năng, bản dịch vi. Mật khẩu SMTP mã hóa, có thể đặt `BST_SMTP_PASS`. Lưu ô mã cần quyền `unfiltered_html`.
- **Kiểm thử:** `node tools/features-check.cjs` trên Playground; lưu qua form thật và tải JPEG qua REST bằng trình duyệt. Chỉ thử với GD. Chưa gửi qua máy chủ SMTP thật, chưa chạy trên site thật. Ảnh chuyển WebP mất EXIF/IPTC sau khi đã xoay đúng chiều.
- **Khi phát hành cần bổ sung:** Giới thiệu, Có gì mới, kiến thức AI.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
