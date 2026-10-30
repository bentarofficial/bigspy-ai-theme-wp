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

- **Yêu cầu/tính năng:** đợt C: form có sẵn thay Contact Form 7; trường tải tệp; kiểm tra url/số/ngày/lựa chọn phía máy chủ; trang Chuyển form CF7.
- **Đã thay đổi:** `inc/form-native.php`, `inc/form-migrate.php`, loại trường trong trình sửa khối, bản dịch vi. Tệp tối đa 5 MB, chỉ đính kèm mail cho admin, không lưu trên site. Chuyển form không tự tắt CF7. Không chuyển được: quiz, captcha, trường ẩn (trang hiện rõ).
- **Kiểm thử:** `node tools/features-check.cjs` trên Playground, dữ liệu CF7 giả lập. Chưa thử với plugin CF7 thật, chưa gửi tệp qua HTTP thật, chưa bấm nút Chuyển trên trình duyệt.
- **Khi phát hành cần bổ sung:** Giới thiệu, Có gì mới, kiến thức AI.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

- **Yêu cầu/tính năng:** siết AI Designer: không tạo `core/html` mới, khối lõi tự thành khối BIGSPY AI; `bst/code` quá 2 khối hoặc 3 KB mỗi trang cần thẻ duyệt; báo tỉ lệ khối sửa được (mốc 90%, chỉ báo); CSS AI tự thêm tối đa 2 KB; công cụ `style_options` và thứ tự ưu tiên trong prompt.
- **Đã thay đổi:** `ai-designer/includes/tools-design.php`, `permissions.php`, `prompt.php`.
- **Kiểm thử:** `node tools/features-check.cjs`, `node tools/playground-check.cjs`. Chưa chạy với AI thật. Giới hạn: AI gửi `raw` core/html vẫn được giữ để không vỡ trang cũ; `user_requested` do AI tự khai, nhưng custom_css vẫn qua thẻ duyệt.
- **Khi phát hành cần bổ sung:** Có gì mới, kiến thức AI.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
