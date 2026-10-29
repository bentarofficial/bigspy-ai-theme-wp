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

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
