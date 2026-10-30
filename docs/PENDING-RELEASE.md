# Thay đổi chờ phát hành

Theo quy tắc trong [roadmap-vi.md](roadmap-vi.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

### Chuyển động đợt 2: Hover Reveal, Line Draw, Horizontal Scroll, Zoom Hero, chuyển động nâng cao (Claude, 30-10-2026)

- **Yêu cầu:** đợt 2 roadmap "P1 — Chuyển động".
- **Đã thay đổi:**
  - Khối mới `bigspy/hover-reveal` + `-item` (ảnh bay theo con trỏ, điện thoại hiện ảnh nhỏ), `bigspy/line-draw` (6 hình + đường SVG tự nhập, vẽ khi cuộn tới hoặc theo cuộn), `bigspy/horizontal-scroll` + `bigspy/hscroll-panel` (cuộn dọc → trượt ngang bằng wrapper cao + sticky, điện thoại thành hàng vuốt), `bigspy/zoom-hero` (khung ảnh phóng ra toàn màn hình theo cuộn, nội dung hiện dần).
  - "Chuyển động nâng cao" (panel editor + attribute server qua `register_block_type_args`, class qua `render_block`): Section dính khi cuộn, màu nền trang theo section; Button magnetic / mũi tên trượt / vệt sáng định kỳ; Image kéo rèm trái/phải/dưới, mở tròn, Ken Burns; Icon Box icon tự vẽ nét; Counter xáo chữ số (trong `blocks.js`); Gallery ảnh hiện lần lượt. Slider thêm hiệu ứng Mờ + phóng, Lật, Coverflow (CSS `animation-timeline: view()`, có fallback).
  - Runtime mới `assets/js/motion-fx.js` (handle `bst-motion-fx`, chỉ nạp khi dùng); `motion-scroll.js` thêm hscroll, zoom hero. FAMILIES CSS `hover-reveal`, `line-draw`, `horizontal-scroll`, `zoom-hero`; đã chạy lại split. Bản xem trước, mô tả, màu icon, bản dịch vi (80 chuỗi), kiến thức AI.
- **Kiểm thử:** lint PHP; `node --check`; `check-block-previews` (72 khối); render 72 hình xem trước; `playground-check.cjs --welcome` đạt. Playground trang thử (giả lập tắt giảm chuyển động): Hover Reveal bật ảnh nổi khi rê; Line Draw vẽ xong khi cuộn tới, bản theo cuộn đạt offset 0,43 giữa chừng; Horizontal Scroll ở 1280px: wrapper 2454px, track dịch đúng 1:1 (0 → -500 → -1200 → -1654px) rồi nhả; Zoom Hero `--p` 0,77 khi cuộn; nền trang đổi sang màu section (#fde68a); nút magnetic dịch theo chuột; editor chèn 4 khối mới + Slider zoom, panel "Chuyển động nâng cao" hiện, Line Draw xem trước qua server, không lỗi. Chưa xem bằng mắt Coverflow/Lật/Ken Burns/vệt sáng (máy thử luôn bật giảm chuyển động) và chưa thử điện thoại thật.
- **Khi phát hành cần bổ sung:** Có gì mới, README Features/Updates, trang Giới thiệu mục chuyển động.

### Chuyển động đợt 1: cường độ + phong cách, Animated Text, Scroll Story, Stacking Cards (Claude, 30-10-2026)

- **Yêu cầu:** đợt 1 roadmap "P1 — Chuyển động" do owner chốt.
- **Đã thay đổi:**
  - `inc/motion-wave.php`: Customizer > Performance thêm **Cường độ chuyển động** (Nhẹ/Vừa/Mạnh) và **Phong cách chuyển động** (Mặc định/Sang trọng/Năng động/Tối giản), xuất class `bs-motion--*`, `bs-motion-style--*` trên body; CSS hiệu ứng xuất hiện nhân theo `--bs-motion-scale`, `--bs-motion-speed`, `--bs-motion-ease`; Tối giản chỉ mờ dần. Đăng ký script `bst-motion-text`, `bst-motion-scroll` (chỉ nạp khi trang có khối).
  - Khối mới `bigspy/animated-text` (reveal theo cuộn, hiện từng từ, gõ chữ, thay phiên từ, xáo ký tự, gradient chạy, chữ lớn trượt theo cuộn; chữ đầy đủ có sẵn trong HTML + aria-label).
  - Khối mới `bigspy/scroll-story` + `bigspy/story-step` (ảnh sticky đổi theo bước bằng IntersectionObserver, chấm tiến độ, điện thoại hiện ảnh trong từng bước; không chặn cuộn).
  - Khối mới `bigspy/stack-cards` + `bigspy/stack-card` (sticky xếp chồng bằng CSS, JS thêm thu nhỏ/tối thẻ dưới).
  - Heading thêm thuộc tính `textMotion` (reveal/words/scramble), dùng chung runtime.
  - Bản xem trước, mô tả, màu icon (nhóm chuyển động), bản dịch vi, kiến thức AI; FAMILIES CSS mới `animated-text`, `scroll-story`, `stack-cards` và đã chạy lại `split-blocks-css.py`.
- **Kiểm thử:** lint PHP; `node --check`; `check-block-previews` (66 khối); render 66 hình xem trước bằng Node; `playground-check.cjs --welcome` đạt. Playground riêng có trang thử: chế độ giảm chuyển động (môi trường thử bật sẵn) hiện chữ tĩnh đầy đủ; giả lập tắt giảm chuyển động: reveal sáng 14/14 từ khi cuộn, hiện từng từ, gõ chữ, chữ lớn trượt theo cuộn; Scroll Story đổi bước/ảnh/chấm đúng, ảnh sticky; Stacking Cards top 90/114/138px, thẻ trên bị phủ có `--p` 0,925; editor chèn được 5 khối, không lỗi console, Customizer có 2 tùy chọn mới. Chưa xem bằng mắt hiệu ứng gradient chạy (môi trường thử luôn giảm chuyển động) và chưa thử trên điện thoại thật.
- **Khi phát hành cần bổ sung:** Có gì mới, README (Features + Updates), trang Giới thiệu (mục chuyển động: thêm 3 khối và 2 tùy chọn Customizer).

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
