# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

G-91, G-92 đã phát hành trong 1.83.1.

### G-93 Công tắc BIGSPY AI List trong Settings

- **Đã thay đổi:** BIGSPY AI Theme → Settings có mục "BIGSPY AI List mode" (ô bật/tắt, cùng trạng thái với trang Features, chỉ `manage_options`), nhắc cần WooCommerce, liên kết sang Features cho cài đặt chi tiết. 4 chuỗi tiếng Việt.
- **Kiểm thử:** features-check thêm mục hiển thị công tắc; đường lưu dùng `bst_feature_set` sẵn có, chưa bấm thử trên trình duyệt.
- **Khi phát hành cần bổ sung:** một dòng Có gì mới.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
