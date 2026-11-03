# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

- **Yêu cầu/tính năng:** G-45, nguyên tắc thiết kế mặc định cho AI Designer.
- **Đã thay đổi:** `prompt.php` thêm mục banner đầu trang, dùng đa dạng khối, khối động, `bst/code` chỉ để minh hoạ, hiệu ứng khối/chữ/hover/cuộn và chất lượng thiết kế.
- **Kiểm thử:** lint và bộ Designer; chưa thử với AI thật.
- **Khi phát hành cần bổ sung:** Có gì mới, README Updates.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

- **Yêu cầu/tính năng:** G-46 (làm thay, G-46-T), hồ sơ block và công cụ `block_catalog`.
- **Đã thay đổi:** `ai-designer/knowledge/blocks.php` có hồ sơ 63 block cấp trên (nhóm nhu cầu, dùng/tránh, dữ liệu cần, độ nặng, ghim cuộn, mobile, khối đi cùng, thuộc tính tối thiểu); công cụ đọc `block_catalog` trả nhóm hoặc hồ sơ theo nhu cầu; prompt và `knowledge/theme.md` chuyển sang dùng nó. `list_blocks` giữ nguyên.
- **Kiểm thử:** lint, bộ Designer 157 đạt (6 test mới); chưa thử với AI thật.
- **Khi phát hành cần bổ sung:** Có gì mới, README Updates.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

- **Yêu cầu/tính năng:** G-47, đo task AI Designer.
- **Đã thay đổi:** `tasks.php` ghi mỗi lượt chạy (mỗi tin nhắn) vào `_bst_aid_metrics`: thời gian, số lần gọi model và thời gian chờ model, số lần gọi công cụ theo tên và thời gian, token vào/ra, chi phí, model, effort, trạng thái kết thúc; giữ 20 lượt. Tóm tắt task trả `metrics` (lượt cuối + tổng); đầu khung chat hiện dòng "Lượt cuối", di chuột xem tổng.
- **Kiểm thử:** lint, bộ Designer 159 đạt (2 test mới, Kyma giả lập); chưa thử AI thật và UI trình duyệt.
- **Khi phát hành cần bổ sung:** Có gì mới, README Updates.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

- **Yêu cầu/tính năng:** G-48, minh hoạ bằng code.
- **Đã thay đổi:** `bst/code` không duyệt tới 8 khối, 6 KB mỗi khối; trên mức đó cần thẻ duyệt; trần 12 khối, 40 KB mỗi trang kể cả khi duyệt (tính cả `css`). `bst/code` không tính vào `editable_percent`, nhưng số khối code không được vượt số khối khác. Bộ lọc SVG thêm text, ellipse, polyline, gradient và thuộc tính nét; vẫn bỏ script, on*, use, image, foreignObject. Prompt hướng dẫn minh hoạ SVG/HTML/CSS khi người dùng không yêu cầu ảnh, dùng biến màu theme, vừa 375px, không JS, không bịa số liệu. AGENTS.md cập nhật quy tắc block.
- **Kiểm thử:** lint, Designer 164 đạt, features đạt; chưa thử AI thật và hiển thị trên trình duyệt.
- **Khi phát hành cần bổ sung:** Có gì mới, README Updates (giới hạn Code mới).
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

- **Yêu cầu/tính năng:** G-49, công thức section.
- **Đã thay đổi:** `ai-designer/knowledge/sections.php` có 42 công thức section (hero, tính năng, quy trình, bằng chứng, giá, FAQ, CTA, liên hệ, media, blog, shop), mỗi công thức là cây block có sẵn hiệu ứng và chỗ trống `{{...}}`. Công cụ đọc `section_recipes`: liệt kê theo nhu cầu hoặc trả cây và danh sách chỗ trống; tự ẩn công thức cần block đang tắt (vd. Woo). Prompt cho AI bắt đầu mỗi section từ công thức khi phù hợp.
- **Kiểm thử:** lint, Designer 169 đạt (mọi công thức dựng được, không cảnh báo thuộc tính); chưa thử AI thật và chưa xem hiển thị từng công thức.
- **Khi phát hành cần bổ sung:** Có gì mới, README Updates.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

- **Yêu cầu/tính năng:** G-50, dựng nhanh sau khi duyệt.
- **Đã thay đổi:** công cụ `page_plan` hiện sơ đồ trang thành thẻ trong chat (lưu `_bst_aid_plan`), người dùng trả lời OK một lần. Công cụ `build_sections` dựng tới 8 section mỗi lần gọi từ công thức: máy chủ điền chỗ trống (giá trị lặp theo mảng), gắn ảnh theo ID, bỏ ảnh thiếu, báo chỗ trống chưa điền; không có id thì tạo trang nháp, có id thì nối thêm. Prompt: duyệt sơ đồ một lần rồi dựng cả trang không hỏi giữa chừng.
- **Kiểm thử:** lint, Designer 174 đạt; chưa thử AI thật và thẻ sơ đồ trên trình duyệt.
- **Khi phát hành cần bổ sung:** Có gì mới, README Updates.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

- **Yêu cầu/tính năng:** G-51, model theo việc.
- **Đã thay đổi:** Cài đặt AI Designer thêm "Model nhanh" (không bắt buộc). Lượt gửi với Effort Thấp (tra cứu, sửa nhỏ) dùng model nhanh; mức khác dùng model văn bản để viết và thiết kế. Model nhanh lỗi thì lượt chuyển hẳn sang model chính và báo trong chat. Chi phí ước tính và số đo task ghi đúng model đã dùng. Chọn model ở thanh chat vẫn là G-21.
- **Kiểm thử:** lint, Designer 177 đạt (Kyma giả lập); chưa thử AI thật và giao diện cài đặt.
- **Khi phát hành cần bổ sung:** Có gì mới, README Updates, hướng dẫn cài đặt AI Designer.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

- **Yêu cầu/tính năng:** G-21, chọn model ở thanh chat.
- **Đã thay đổi:** thanh chat có ô Model bên trái Effort: "Tự động (Cài đặt)" hoặc một model từ danh sách đã cache (không gọi API khi mở trang). Model gửi theo từng tin nhắn, máy chủ kiểm tra, lưu vào `_bst_aid_run`, chi phí và số đo theo model đã dùng; model chọn bị lỗi thì chuyển model chính. Cài đặt thêm danh sách model người dùng được chọn (để trống = tất cả).
- **Kiểm thử:** lint, Designer 180 đạt (Kyma giả lập); chưa thử AI thật và chưa xem giao diện trên trình duyệt.
- **Khi phát hành cần bổ sung:** Có gì mới, README Updates, hướng dẫn AI Designer.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

- **Yêu cầu/tính năng:** G-44, đính kèm file ở thanh chat AI Designer.
- **Đã thay đổi:** nút ghim mở Thư viện Media (chọn nhiều), kéo thả hoặc dán tệp vào ô tin nhắn, thẻ xem trước gỡ được. Ảnh/video tải lên vào Media với dấu `_bst_aid_upload` + ID task; tài liệu .txt/.md/.csv (tối đa 1 MB) chỉ lưu trong dữ liệu task, không công khai, tự bỏ sau 7 ngày. Tin nhắn mang ID file, máy chủ kiểm tra quyền và loại; AI nhận ID, kích thước, alt, URL và văn bản tài liệu (tối đa 12.000 ký tự mỗi tài liệu) dưới dạng dữ liệu, không phải lệnh. Danh sách ảnh AI chưa dùng gồm cả tệp tải lên từ chat. Chưa gửi ảnh cho model (chưa xác minh Kyma hỗ trợ) và chưa đọc PDF/Word: xem G-53.
- **Kiểm thử:** lint, Designer 187 đạt; chưa thử nút ghim, kéo thả, dán trên trình duyệt và AI thật.
- **Khi phát hành cần bổ sung:** Có gì mới, README Updates, hướng dẫn AI Designer.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
