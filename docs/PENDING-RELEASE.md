# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

- **AI Designer và block động (G-36):** sửa đọc–sửa–lưu 19 block `bigspy/*` để giữ thuộc tính và nội dung lồng nhau; bổ sung quan hệ cha/con cho Scroll Story, Stacking Cards, Hover Reveal, Horizontal Scroll, Zoom Hero, Video Cards, Bento. Payload native `raw` cũ được yêu cầu đọc lại thay vì âm thầm lưu rỗng. Block Manager dùng namespace trong metadata; filter editor cũng ẩn đúng motion block và luôn nạp trước khi đăng ký. Runner Designer báo từng test và hỗ trợ số worker. Ma trận đối chiếu và backlog ở [THEME-COMPARISON.md](THEME-COMPARISON.md). Kiểm thử: đã tái hiện lỗi trước sửa; Designer 151 mục đạt, filter editor đạt cả 74 block, features-check 82 mục, Auto Detect 67 mục và block previews đều đạt; lint PHP/JS và diff đạt. Chưa thử UI hosting, AI thật hoặc phát hành. Git: `feature/ai-designer`. Khi phát hành: bổ sung ghi chú sửa lỗi AI/block; không đổi số phiên bản trong đợt này.

- **Auto Detect, quét media và Media ATND (G-34):** quét media sâu như plugin Media Cleaner (shortcode Flatsome, WebP, mọi option, postmeta, termmeta); Media Library có tích xanh/X đỏ, cột, bộ lọc, link "Chưa dùng", bulk action; trang Auto detect có ảnh thu nhỏ, chọn tất cả, "Giữ tệp này"; xoá vào thùng rác của theme, kiểm tra lại ngay trước khi xoá, khôi phục được. Thêm module Media ATND dùng kết nối API theme. Test: 67 mục auto-detect đạt trên Playground (AI giả lập), features-check đạt. Chưa thử giao diện trong trình duyệt, chưa thử AI thật. Khi phát hành: bổ sung Có gì mới, README.

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
