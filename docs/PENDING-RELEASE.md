# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

Đã phát hành ở 1.99.1: sửa tràn khung editor Mobile/Tablet, thanh kéo cột ở mép, con trỏ bàn tay. Đã phát hành ở 1.99.0: marquee chạy khi giảm chuyển động, Stacking Cards Lưới gọn, sửa khoảng trống khối Custom HTML đầu trang, hồ sơ AI công khai. Đã phát hành ở 1.98.0: mã tracking đầu <head>, G-197, G-199, G-200, G-201, G-202, G-204, G-204-b, G-205, G-205-b, G-206, G-209, G-214, export/import theme options. Đã phát hành ở 1.97.0: G-184 nhãn chỉnh nhanh trang listing, G-185 nguồn Popular cho cả site. Đã phát hành ở 1.96.1: giao diện mềm cho Floating contact. Đã phát hành ở 1.96.0: marquee nâng cấp, Floating contact, độ rộng sidebar theo trang, nút Add to trong List View. Đã phát hành ở 1.95.0: S-13-a, dòng hoa hồng affiliate mặc định tắt, G-167 Listing texts, sửa ô tìm Customizer. Đã phát hành ở 1.94.0: G-134-d, khoảng cách khối, banner điện thoại. Đã phát hành ở 1.93.0: Bento/Stacking Cards options, Footer scroll effect. Đã phát hành ở 1.92.0: G-163, G-164, G-134-c. Đã phát hành ở 1.91.0: G-162, G-134-a, G-133-a/b/c, G-132-a/b/c/d, G-129, G-130, G-131, G-141, G-147, G-151, G-94, G-100, G-103, G-113, G-117, G-118, G-119, G-120, G-123. Đang chờ:

- **Floating contact mẫu B + logo chuẩn:** nút màu phẳng, viền trắng mờ, đổ bóng nhẹ (bỏ quầng phập phồng); logo chuẩn Simple Icons cho Zalo, Messenger, WhatsApp, Telegram, Viber, TikTok, YouTube, Instagram; icon mới phone-solid (MDI phone-in-talk), gmaps, gmail. Kiểm thử: lint PHP, xem trên Playground Customizer. Khi phát hành: thêm Có gì mới/changelog.
- **Khối Form sắp xếp lại + trường mới:** tab Cài đặt (Form, Trường & email, Sau khi gửi, Tiêu đề & mô tả) và tab Giao diện (Mẫu có sẵn, Ô nhập, Màu ô nhập, Nút gửi, Màu nút, Bố cục/khung/ảnh, Màu khung & chữ); nút Add field là menu chọn kiểu; trường Giờ và Ngày + giờ; mục Sau khi gửi chọn lời cảm ơn hoặc trang cảm ơn, tìm trang, nút Tạo trang cảm ơn; Global style thêm Form field corner radius (block radius mặc định -1, ô nhập theme mặc định 10px như cũ); dịch tiếng Việt. Kiểm thử: node --check, phplint, features-check, Playground editor (menu, trường Giờ render input time, tạo trang cảm ơn). Khi phát hành: thêm Có gì mới/changelog.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
