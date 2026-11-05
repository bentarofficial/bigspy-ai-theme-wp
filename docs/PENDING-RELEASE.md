# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

- **Yêu cầu/tính năng:** nghiệm thu 1.81.0 phần không cần AI thật (dựng 40 công thức section, chụp 375/768/1024/1440).
- **Đã thay đổi:** `build_sections` gắn ảnh cho Gallery và Logos (trước đó để trống, không báo), báo `without_media` khi thiếu; form trong công thức dùng form native và tự có `formKey` (trước đó cần CF7/WPForms nên không hiện gì); icon địa chỉ `location` → `pin`. Form native: `fields` rỗng dùng bộ trường mặc định thay vì form không có ô nhập. CSS: số Counter và nút Outline chuyển sang trắng trong vùng chữ sáng (Section nền đậm, Banner) — trước đó số trùng màu nền, nút outline khó đọc.
- **Kiểm thử:** Designer 204 đạt (1 test mới), features 82 đạt; 40 trang công thức không tràn ngang, không lỗi JS ở 4 viewport; kiểm lại ảnh 375 sau sửa. Chưa thử AI thật; giao diện chat cần API nên chưa xem.
- **Khi phát hành cần bổ sung:** Có gì mới (sửa lỗi), README Updates.
- **Git:** nhánh `ccr-92450a09-r43hwx`.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
