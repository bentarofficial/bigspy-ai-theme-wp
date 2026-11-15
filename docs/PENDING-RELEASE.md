# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

Đã phát hành ở 1.96.0: marquee nâng cấp, Floating contact, độ rộng sidebar theo trang, nút Add to trong List View. Đã phát hành ở 1.95.0: S-13-a, dòng hoa hồng affiliate mặc định tắt, G-167 Listing texts, sửa ô tìm Customizer. Đã phát hành ở 1.94.0: G-134-d, khoảng cách khối, banner điện thoại. Đã phát hành ở 1.93.0: Bento/Stacking Cards options, Footer scroll effect. Đã phát hành ở 1.92.0: G-163, G-164, G-134-c. Đã phát hành ở 1.91.0: G-162, G-134-a, G-133-a/b/c, G-132-a/b/c/d, G-129, G-130, G-131, G-141, G-147, G-151, G-94, G-100, G-103, G-113, G-117, G-118, G-119, G-120, G-123. Đang chờ:

- **G-207-a · Khung chung AI Designer:** thanh ứng dụng chung cho Workspace, Guidelines, Settings, About (tab, số dư đã lưu tạm, chuông gom thông báo plugin khác, nút toàn màn hình nhớ trạng thái, Esc để thoát); chiều cao theo cửa sổ thay cho số cứng, ẩn footer wp-admin. Kiểm thử: Playground + mock, 1440/1280/390px, không cuộn cả trang; workspace-browser-check 12/12. Khi phát hành: cập nhật Có gì mới của AI Designer.
- **G-207-b · Bố cục Workspace:** ba trạng thái (màn hình bắt đầu có thẻ gợi ý, chỉ hội thoại, hội thoại + xem trước); dải task thu gọn thành biểu tượng thay vì biến mất, có ô tìm; thanh đầu một hàng với chip trạng thái và menu ⋯ (hoàn tác, đổi tên tại chỗ, chi tiết, xoá); checklist ghim trên ô nhập; ô nhập tự giãn, các lựa chọn thành nút viên; xem trước có chọn trang, nút thiết bị, iframe thu nhỏ theo tỉ lệ, ẩn thanh admin trong khung; dưới 1.024px task thành ngăn kéo và hội thoại/xem trước thành hai tab. Menu trái WordPress chỉ còn mục chính BIGSPY AI Designer (owner yêu cầu), huy hiệu About chuyển sang tab. Kiểm thử: workspace-browser-check 18/18.
- **G-207-c · Bố cục Settings:** hai cột với mục lục dính đánh dấu mục đang xem (dưới 900px thành hàng tab cuộn ngang); dải tóm tắt bốn ô (kết nối, số dư, model, chi tiêu hôm nay/ngân sách); tách thẻ Model, Ngân sách, Quyền công cụ, bỏ số thứ tự tiêu đề; một thanh Lưu dính đáy chỉ hiện khi có thay đổi (Lưu thay đổi / Bỏ thay đổi); vùng xoá dữ liệu thu gọn. Kiểm thử: Playground + mock 1440/390px, lưu ngân sách qua thanh Lưu.
- **G-207-d · Bố cục Guidelines:** hai ngăn: danh sách Tiêu chuẩn và Chuyên môn (số ký tự, chấm báo đã điền/đã sửa so với mặc định) và một trình soạn cao bằng vùng nội dung có đếm ký tự trên 6.000; nhớ mục đang sửa; dưới 900px danh sách thành ô chọn. Form gốc giữ nguyên nên vẫn gửi được khi không có JS. Kiểm thử: tự lưu, tải lại giữ mục, chuyển sang chuyên môn.
- **Sửa lỗi 1.96.0:** khối con `bst/marquee-item` chưa được AI Designer nhận là khối con của `bst/marquee`, làm bài test tích hợp dừng ở “Every top-level block has a catalog profile”. Đã khai báo trong `bst_aid_block_parents()`. Kiểm thử: designer-integration 208/208.
- **G-192-a · Tin nhắn trong Workspace:** câu trả lời AI không còn bong bóng, có avatar, nút Chép và Thử lại; Markdown thêm bảng, khối mã có nút Chép, danh sách lồng, trích dẫn; các dòng công cụ liên tiếp gộp thành “Đã làm N việc” (mở sẵn khi có lỗi); khung xem trước ẩn thanh admin. Kiểm thử: workspace-browser-check 18/18.
- **G-192-b · Hộp thoại, thẻ duyệt, chủ đề tối:** mọi `window.confirm` trong AI Designer thay bằng hộp thoại riêng (nút xoá màu đỏ, Esc để huỷ); thẻ duyệt nổi bật, tên công cụ dễ đọc, nút Đồng ý chính, đếm ngược 15 phút và khoá khi hết hạn; thẻ bảng màu có nút “Dùng bảng này”; chủ đề tối theo hệ thống và nút sáng/tối trên thanh ứng dụng (nhớ lựa chọn). Kiểm thử: Playground + mock, sáng/tối ở Workspace và Settings.
- **G-192-c · Danh sách task và dòng hoạt động:** task nhóm theo Hôm nay / 7 ngày trước / Cũ hơn; dòng công cụ có biểu tượng và màu theo loại việc (đọc, ghi, ảnh, kiểm tra). Kiểm thử: workspace-browser-check 18/18.
- **G-193-a · Tiến trình trực tiếp:** mỗi bước gọi model mà có lệnh công cụ giờ trả `activity` và để công cụ chạy ở bước kế (không gọi model thêm, giới hạn bước tính như cũ); Stop hoặc tin nhắn mới trước khi công cụ chạy thì công cụ không chạy và lịch sử ghi “skipped” để nhà cung cấp không từ chối lượt sau. Workspace có dòng trạng thái sống (“Đang đọc site…”, “Đang dựng trang…”) kèm thời gian và chi phí của lượt chạy, câu trả lời cuối hiện dần (tắt khi giảm chuyển động), xem trước tải lại tại chỗ giữ vị trí cuộn. Sửa kèm: màn hình chào biến mất khi bắt đầu trò chuyện, chi phí ở ô nhập cập nhật trong lúc chạy. Kiểm thử: designer-integration 209/209, workspace-browser-check 19/19.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
