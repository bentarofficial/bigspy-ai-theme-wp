# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

Đã phát hành ở 1.87.0: G-89 (phần trước bba1655), G-102, G-106, G-109, G-110, G-115, G-116, G-124, G-126. Còn chờ:

- **Yêu cầu/tính năng:** G-89 (owner 10-11): Sponsored có khối riêng, thẻ bấm được cả thẻ, nút mảnh như theme cũ.
- **Đã thay đổi:** khối mới `bigspy/sponsored` (`blocks/sponsored/`, nhóm BIGSPY AI List): số thẻ, số cột máy tính/tablet/điện thoại, kiểu nút (theo cài đặt chung / không nút / viền nhẹ / đặc), chữ nút, nhãn "Sponsored", mô tả, mở tab mới. `inc/listing/sponsored.php`: `bst_render_sponsored()`, `bst_sponsored_slot()` nhận tuỳ chọn, cả thẻ là một liên kết tới đối tác (link phủ toàn thẻ, nút chỉ còn là nhãn trang trí); cài đặt chung mới "Sponsored button" ở trang Features (mặc định: không nút). `assets/css/listing.css`: nút kiểu viền nhẹ giống theme cũ (nền #f6f9fb, viền #dbe7f0, chữ 12px đậm 650), kiểu đặc là tuỳ chọn; nút Visit của thẻ listing cũng đổi sang kiểu nhẹ; lưới Sponsored tự giãn khi chỉ có một thẻ. `assets/js/sponsored.js` cập nhật mọi liên kết khi xoay vòng. Shortcode `[bigspy_sponsored_grid]` và `[bigspy_sponsored_card]` giữ nguyên, nay dùng cùng bộ dựng.
- **Kiểm thử:** features-check đạt (3 test mới), check-block-previews đạt 83 khối, woo-check đạt. Chưa xem khối trong editor và trên bigspyai.com.
- **Khi phát hành cần bổ sung:** Có gì mới: "Khối Sponsored riêng với tuỳ chọn nút, cột, nhãn; cả thẻ Sponsored là liên kết; nút kiểu nhẹ". Lưu ý: thẻ Sponsored mặc định không còn nút "Open site"; muốn có nút, chọn kiểu nút ở Features hoặc trong khối. Kiến thức AI Designer nên nêu khối `bigspy/sponsored` (G-123).
- **Git:** `feature/ai-designer`, xem HANDOFF.
- **Trạng thái:** chờ owner yêu cầu phát hành.

- **Yêu cầu/tính năng:** G-107 (owner 10-11: banner trang chủ bigspyai.com không đẹp bằng site nháp): màu khối Heading bị chế độ chữ của Banner/Section đè; khối Listing search chưa đổi được màu nút.
- **Đã thay đổi:** `assets/css/blocks.css` (`.is-scheme-dark/light` dùng `var(--bs-heading-color, …)` nên màu riêng của Heading được giữ; đã chạy lại `split-blocks-css.py`), `inc/listing/blocks.php` + `assets/css/listing.css` + `blocks/listing-search/block.json` (màu nút, màu chữ nút, màu viền, bo góc, chiều cao), nhãn trong `blocks-listing.js`.
- **Kiểm thử:** woo-check 50 ok (test mới: màu/bo góc/chiều cao ô tìm kiếm, chặn màu sai), features-check 143 ok, previews 83 khối. Đã chèn tạm CSS sửa màu tiêu đề trên bigspyai.com: H1 #003366 và H2 teal hiện đúng.
- **Khi phát hành cần bổ sung:** Có gì mới: "Màu riêng của tiêu đề giữ nguyên trong Banner/Section; ô tìm kiếm listing đổi được màu nút, viền, bo góc, chiều cao". Trang chủ bigspyai.com đã đặt sẵn nút tìm kiếm teal, sẽ hiện sau khi cập nhật.
- **Git:** `feature/ai-designer` c3ab887.
- **Trạng thái:** chờ owner yêu cầu phát hành.

- **Yêu cầu/tính năng:** G-127 (owner 10-11): trang listing chi tiết tràn sang phải trên điện thoại, ảnh cũng tràn.
- **Đã thay đổi:** `assets/css/listing.css`: ở ≤900px lưới `bst-ls-layout--sidebar` dùng `minmax(0,1fr)` thay `1fr` (trước đây ảnh có caption WordPress `width:1908px` và bảng thông số kéo cột tới 1898px trên khổ 375px); `.bst-ls-main/.bst-ls-sidebar{min-width:0}`; ảnh, video, iframe, svg, `.wp-caption`/figure không vượt cột; bảng cuộn ngang trong khung; từ/URL dài tự xuống dòng; hero ≤600px cũng `minmax(0,1fr)`.
- **Kiểm thử:** chèn tạm CSS trên bigspyai.com/higgsfield-ai/ ở 375px: cột nội dung 345px, scrollWidth 375, không còn phần tử nào vượt màn hình (trừ watermark thẻ đã bị cắt trong khung). Chưa thử iPhone/Android thật.
- **Khi phát hành cần bổ sung:** Có gì mới: "Trang listing không còn tràn ngang trên điện thoại; ảnh, caption và bảng tự co theo màn hình".
- **Git:** `feature/ai-designer`, commit bên dưới.
- **Trạng thái:** chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
