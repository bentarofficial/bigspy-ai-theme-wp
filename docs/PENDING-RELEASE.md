# Thay đổi chờ phát hành

Theo quy tắc trong [roadmap-vi.md](roadmap-vi.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

### Bản xem trước trong bảng chèn khối và mô tả khối chi tiết (Claude, 29-10-2026)

- **Yêu cầu/tính năng:** mọi khối BIGSPY có bản xem trước khi rê chuột trong bảng chèn khối, vẽ bằng HTML + CSS inline (khung trình duyệt giả, không dùng ảnh); mô tả khối viết lại chi tiết (làm gì, dùng khi nào, tùy chọn nổi bật).
- **Đã thay đổi:** `assets/js/block-previews.js` (filter `blocks.registerBlockType` gắn `example` + thuộc tính `bstPreview`, nạp trước mọi script khối); `inc/blocks.php` khai báo `bstPreview` phía server cho `bst/*`, `bigspy/*`; mô tả mới trong 58 `block.json` + `bst/countdown`, `bst/product-part`; bản dịch vi với ngữ cảnh `block description`; `tools/compile-po.py` hỗ trợ `msgctxt`; script kiểm tra `node tools/check-block-previews.js` (bắt buộc chạy trước phát hành, khối mới phải có preview và mô tả).
- **Kiểm thử:** `node tools/check-block-previews.js` OK (60 khối); render thử 60 hình vẽ bằng Node: 0 lỗi; lint PHP (php-parser) 5 tệp sửa OK; Playground (`node tools/playground-check.cjs --serve`, designer-integration chạy kèm): 59 khối `bst/*`, `bigspy/*` đều có `example.bstPreview`, bảng chèn khối hiện hình xem trước (Expanding gallery, Price table) và mô tả mới; chèn bst/text, bst/posts (render server), bigspy/ticker vẫn chạy, nội dung lưu không có `bstPreview`; `vi.mo` trả đúng bản dịch có ngữ cảnh `block description`. Chưa xem bằng mắt giao diện tiếng Việt trong editor.
- **Lỗi ngoài phạm vi phát hiện khi test:** `assets/js/editor-helpers.js` dòng ~486 (cuộn xem trước pattern) dùng biến `inner` chưa khai báo, gây `ReferenceError` khi rê chuột qua pattern; chưa sửa.
- **Khi phát hành cần bổ sung:** Có gì mới (bản xem trước khối, mô tả chi tiết); ghi chú quy tắc "khối mới phải có preview" cho agent. Tiêu đề khối (block title) vẫn chưa có bản dịch tiếng Việt, nằm ngoài phạm vi đợt này.

### Sửa lỗi AI Designer, tên khối tiếng Việt, nâng cấp khối Sản phẩm, tùy chọn giỏ hàng/thanh toán (Claude, 29-10-2026)

- **Yêu cầu/tính năng:** owner yêu cầu làm 4 nhóm việc: lỗi AI Designer trong `docs/AI-DESIGNER-DRAFT-SITE-AUDIT-2026-10-26.md`, lỗi rê chuột qua pattern, nâng cấp khối Sản phẩm (Prompt 5), mục P0 roadmap về ngưỡng miễn phí vận chuyển/vị trí coupon.
- **Đã thay đổi:**
  - AI Designer: Effort không còn bị đưa về "medium" khi mở lại task chưa chạy (`/tasks/{id}` trả effort rỗng); nút Dừng tắt trạng thái "đang làm" ngay; kết quả tool có `status` blocked/failed/error/rejected hiện là cảnh báo; ImageGen thử lại 1 lần sau 5 giây khi dịch vụ tạm lỗi (429/5xx/"tạm thời gián đoạn") và báo rõ "Image NOT created" để model không coi ảnh lỗi là đã tạo; prompt bắt buộc mọi chữ người dùng đọc theo ngôn ngữ của họ và chỉ báo kết quả tool đã xác nhận. Hai mục audit đã được 1.73 xử lý từ trước (chặn ghi muộn sau Dừng ở tầng quyền, chuẩn hóa mã màu hex sang style.color) nên giữ nguyên.
  - `editor-helpers.js`: khai báo biến `inner` cho hiệu ứng cuộn xem trước pattern (hết `ReferenceError`).
  - Tên khối (block title) có bản dịch vi theo ngữ cảnh `block title`; Countdown, Product Part dùng `__()`.
  - Khối Sản phẩm: bố cục danh sách gọn; tỉ lệ ảnh, bo góc ảnh, hiệu ứng rê chuột (zoom, ảnh thứ hai, mờ, đen trắng sang màu, nâng thẻ) theo từng khối; độ đổ bóng thường/khi rê (class, vì style attribute bỏ giá trị có ngoặc); cỡ chữ XS–XL; thẻ cao bằng nhau; cuộn vô hạn; sản phẩm hết hàng: giữ thứ tự / đưa xuống cuối / ẩn (áp cả cho nút tải thêm). Khối mới **Danh sách sản phẩm** (`bst/products-list`) cho sidebar/footer.
  - `inc/checkout-extras.php`: thanh tiến độ miễn phí vận chuyển (giỏ hàng, thanh toán, giỏ mini; ngưỡng tự đọc từ phương thức Free shipping của vùng giao hàng hoặc nhập tay), vị trí ô coupon ở trang thanh toán (mặc định/dưới form/ẩn, không lồng form), ghi chú trước phần thanh toán. Chỉ áp dụng trang giỏ hàng/thanh toán cổ điển (shortcode), không áp dụng khối Cart/Checkout.
- **Kiểm thử:** lint php-parser các tệp PHP sửa; `node --check` các JS sửa; `node tools/check-block-previews.js` OK (61 khối); `node tools/playground-check.cjs` (designer-integration) đạt toàn bộ sau khi bỏ một nhánh Dừng trùng với tầng quyền; Playground riêng có WooCommerce 10.x + dữ liệu mẫu: trang có khối Sản phẩm (hết hàng xuống cuối, tỉ lệ 3:4, đổ bóng, cỡ chữ L), dạng danh sách ẩn hàng hết, khối Danh sách sản phẩm; giỏ hàng cổ điển hiện "Mua thêm 50.000đ…", trang thanh toán hiện thanh tiến độ + ghi chú, form coupon nằm sau form thanh toán, không lồng; 4 trang không có Warning/Notice/Fatal. Chưa mở bảng tùy chọn mới của khối Sản phẩm trong editor bằng mắt (Playground WooCommerce không đăng nhập được do cookie dùng chung với instance khác); cuộn vô hạn chưa thử với đủ sản phẩm để có trang 2.
- **Khi phát hành cần bổ sung:** Có gì mới (khối Danh sách sản phẩm, tùy chọn mới khối Sản phẩm, thanh miễn phí vận chuyển, vị trí coupon, ghi chú thanh toán, sửa lỗi AI Designer); kiến thức AI (`knowledge/theme.md`) về các tùy chọn và khối mới; README Features.

### Đổi tên khối: Infinity Flow và Spotlight Panels (Claude, 29-10-2026)

- **Yêu cầu:** owner chốt tên riêng, không trùng khối khác: Continuous ticker → **Infinity Flow**, Expanding gallery → **Spotlight Panels** (khối con: Infinity Flow slide, Spotlight panel). Chỉ đổi tên hiển thị; mã khối `bigspy/ticker`, `bigspy/expand-gallery` giữ nguyên nên nội dung cũ không ảnh hưởng.
- **Đã thay đổi:** title + keywords tìm kiếm (Anh/Việt) trong block.json, mô tả khối con và Testimonial, bảng tùy chọn editor, aria-label mặc định, trang Giới thiệu (mục Chuyển động), kiến thức AI `knowledge/theme.md`, tiêu đề/mô tả pattern, bản dịch vi.
- **Kiểm thử:** `check-block-previews` OK; lint PHP; `vi.mo` trả đúng tên mới; `playground-check.cjs --welcome` (designer + welcome) đạt.
- **Khi phát hành cần bổ sung:** README dòng mô tả 2 khối (mục 1.73) đổi sang tên mới; Có gì mới nhắc tên mới. Mục lịch sử "1.73.0: …continuous ticker and expanding gallery" trong Có gì mới giữ nguyên vì là ghi chép lịch sử.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
