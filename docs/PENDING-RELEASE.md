# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

Đã phát hành ở 1.90.0: tự động ưu tiên ảnh hero và preload, danh sách xoay vòng Sponsored, Banner không ảnh trong editor, ẩn thông báo plugin trên trang quản trị theme. Không có thay đổi đang chờ.

- **G-141 Abilities API và MCP:** tính năng tuỳ chọn “AI abilities (MCP)”, mặc định tắt, cần WordPress 6.9+. 10 ability `bigspy/*` (site-info, list-content, get-post, block-catalog, section-recipes, get-settings, update-settings, get-page, update-page, page-sections) bọc công cụ AI Designer qua `bst_aid_run_tool()`; ghi chạy trên tác vụ “External AI (MCP)” của từng người để hoàn tác; việc cần duyệt trả lỗi `bst_approval_required`. Kiểm thử: 6 ca mới trong `designer-integration.php` đạt trên WP 7.1.3.
- **G-129 Bộ điều khiển dùng chung + Divider:** `assets/js/editor-kit.js` thêm vào `bstEditor`: `Resp`, `PartTypo`, `ColorStates`, `PartBox`, `StylePicker`, `LinkControl`, `StyleTab`, `partVars`; PHP `bst_part_vars()`; `tools/check-block-options.js` (chỉ cảnh báo). Divider: kiểu sóng, răng cưa, chấm tròn, dày-mỏng, đổ bóng; độ rộng %/px theo D/T/M, khoảng cách T/M, màu gradient từ–đến, chữ/icon trái-giữa-phải, cỡ/đậm/giãn chữ, cỡ icon và nền tròn, tự vẽ khi cuộn tới; panel kiểu chuyển sang tab Kiểu. HTML Divider cũ giống hệt (7 ca so trên Playground).
- **G-130 Lớp Design mở rộng:** khoá mới trong `bsDesign`: viền từng cạnh, bo từng góc, gradient 2–3 màu (linear/radial, góc), ảnh nền (vị trí, cỡ, lặp) + lớp phủ, bóng tuỳ chỉnh/bóng trong, opacity, blur/brightness/grayscale, backdrop blur, transform tĩnh và khi hover (xoay, dịch, nghiêng), thời gian chuyển hover, mặt nạ hình (tròn, elip, vòm, blob, lục giác, thoi), nền/chữ/viền/bo góc theo tablet/điện thoại, chiều cao tối thiểu, tỉ lệ khung, order, align-self theo thiết bị, sticky (mọi máy/chỉ desktop) + khoảng cách trên, Overflow. Panel mới “Effects & layout” ở tab Kiểu; panel Nâng cao có CSS riêng của khối (`selector`, chỉ in khi tác giả có `unfiltered_html`) và thuộc tính `data-*`/`aria-*`. Copy/Paste design sao chép thêm thuộc tính kiểu khi cùng loại khối. Kiểm thử: 21 ca `design:` trong features-integration đạt; CSS JS cũ/mới giống hệt trên 3000 thiết kế cũ ngẫu nhiên. Chưa xem tay trong editor. Khi phát hành: thêm chuỗi tiếng Việt cho nhãn panel mới.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
