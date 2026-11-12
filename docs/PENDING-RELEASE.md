# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

Đã phát hành ở 1.92.0: G-163, G-164, G-134-c. Đã phát hành ở 1.91.0: G-162, G-134-a, G-133-a/b/c, G-132-a/b/c/d, G-129, G-130, G-131, G-141, G-147, G-151, G-94, G-100, G-103, G-113, G-117, G-118, G-119, G-120, G-123. Đang chờ:

- Không có thay đổi đang chờ.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
- **G-134-d Map, Popup, Nav menu:** Map: chiều cao riêng tablet/điện thoại hoặc tỉ lệ khung, chỉ tải bản đồ khi bấm (kèm ảnh giữ chỗ), khung thông tin cạnh bản đồ trái/phải, trên, dưới, màu nền/chữ khung, kiểu nút chỉ đường (viền, liên kết chữ), toàn chiều rộng, viền, bóng. Popup: kích hoạt khi bấm phần tử theo bộ chọn CSS, sau N giây không thao tác, khi cuộn tới cuối bài; chỉ hiện theo thiết bị, trạng thái đăng nhập, khoảng ngày; vị trí mới bảng trái, thanh mỏng trên/dưới, toàn màn hình; hiệu ứng mở (phóng, trượt lên, mờ, không); nút đóng tròn/chữ, bên trái hoặc ngoài góc, màu; màu lớp phủ, bề rộng px, đệm, bóng, viền; tự đóng sau khi gửi form. Nav menu: kiểu ngăn bằng vạch và accordion dọc, màu chữ/nền mục đang mở, độ đậm, kiểu chữ, đệm mục; mở menu con khi bấm; số cấp; điện thoại thêm ngăn kéo trượt và accordion; khoảng cách trên điện thoại. Kiểm thử: 8 ca `map:`/`popup:`/`nav-menu:` trong features-integration đạt, gồm HTML thuộc tính cũ; lint đạt; features-check còn FAIL banner có từ trước. Đã dịch vi, kiến thức AI cập nhật. Chưa thử tay trên trình duyệt (các kích hoạt popup mới, ngăn kéo menu, bản đồ bấm mới tải).
- **Khoảng cách khối và banner trên điện thoại:** nội dung trang (`.bs-content`) giờ cách nhau theo blockGap của theme.json như trong trình soạn thảo, nên khối trên/dưới không còn dính nhau khi xuất bản (độ ưu tiên 0, section toàn chiều rộng liền nhau vẫn sát). Banner dưới 850px dùng chiều cao đặt làm tối thiểu, nội dung xếp chồng không bị cắt. Kiểm thử: chèn CSS trên bigspyai.com/listings và /featured ở 375px và desktop.
