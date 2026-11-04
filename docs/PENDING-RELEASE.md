# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

### Khối Tabs nâng cấp: tiêu đề, tab cuộn có mũi tên, accordion trên điện thoại

- **Yêu cầu/tính năng:** hiển thị sản phẩm hoặc blog dạng tab: tiêu đề H2–H6/P/DIV tùy chọn bên trái, tab bên phải; khi nhiều tab thì cuộn ngang với một mũi tên tự quay đầu ở cuối (hoặc hai mũi tên) hay xuống dòng; mỗi tab chứa bất kỳ khối nào.
- **Đã thay đổi:** `bst/tabs` thêm tiêu đề (thẻ, cỡ, màu), chế độ khi tràn (xuống dòng / cuộn / chỉ cuộn trên điện thoại), kiểu mũi tên (dấu mũ, mũi tên, tròn, vuông), kiểu tab viên thuốc viền và chỉ chữ, căn phải, bo góc, khoảng cách, tab mở đầu tiên, accordion trên điện thoại, mở tab theo `#ten-tab`, nút “Xem tất cả” đổi link theo tab. `bst/tab` thêm tên liên kết và link riêng. Tự cuộn tab đang chọn vào tầm nhìn, ảnh trong tab thêm `loading="lazy"`, slider trong tab ẩn được đo lại khi mở. Thêm mẫu “Cửa hàng – tab sản phẩm có tiêu đề” và “Blog – tab bài viết theo chuyên mục”. Trang cũ giữ nguyên giao diện (mặc định: xuống dòng, không tiêu đề).
- **Kiểm thử:** `php -l`, `node --check`, `check-block-previews.js`; trang thử render PHP thật (hàm WP giả lập) + `blocks.js`/`blocks.css` trong Chromium: mũi tên quay đầu/quay lại, hai mũi tên ẩn hiện, hash, link theo tab, accordion 390px. Chưa kiểm tra trong trình soạn thảo WordPress (Playground chưa có trong môi trường này).
- **Khi phát hành cần bổ sung:** Có gì mới / Giới thiệu nhắc khối Tabs mới.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
