# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

Đã phát hành ở 1.91.0: G-162, G-134-a, G-133-a/b/c, G-132-a/b/c/d, G-129, G-130, G-131, G-141, G-147, G-151, G-94, G-100, G-103, G-113, G-117, G-118, G-119, G-120, G-123. Đang chờ:

- **G-163 Dòng công bố trên listing bật/tắt:** Customizer → Layout → Listing design → "Disclosure line on listings": hiện cả dòng tài trợ và affiliate (mặc định, như cũ), chỉ dòng tài trợ, hoặc ẩn (khi site đã có trang Affiliate Disclosure riêng). Nhãn Sponsored trên thẻ không đổi. Kiểm thử: 2 ca mới trong features-check đạt; còn FAIL banner có từ trước. Đã dịch vi.

- **G-164 Sidebar trang listing tuỳ chỉnh được:** vùng widget mới "Listing page sidebar" (Appearance → Widgets, chế độ List). Có widget thì thay phần mặc định; trống thì vẫn hiện nhà tài trợ, danh mục, tài nguyên liên quan như cũ. Khối mới `bst/listing-sidebar` với 3 mục trong trình chèn (Sponsor, Categories, Related resources), đổi tiêu đề được; xem trước trong màn hình Widgets dùng listing mới nhất. Màn hình Widgets có nút "Add them as blocks to edit" chép 3 phần mặc định thành khối. Kiểm thử: 3 ca `listing sidebar:` trong woo-check đạt (53 ok); check-block-previews đạt; đã thêm preview, kiến thức AI, bản dịch vi. Chưa thử tay màn hình Widgets.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
- **G-134-c Social, Share, Payment icons:** Social thêm Telegram, Threads, GitHub, Discord, Shopee, Lazada, Google Maps, website và liên kết tuỳ chỉnh (icon + URL + màu), hiện tên cạnh icon, bố cục lưới/dọc, hiệu ứng rê chuột (nhấc có bóng, đổi màu, xoay, không), cỡ icon tách khỏi cỡ ô, màu nền/viền, tooltip, căn và khoảng cách riêng điện thoại. Share thêm Telegram, Messenger, In; nút có chữ dạng viên thuốc; thanh nổi bên trái (thành thanh dưới đáy trên điện thoại) hoặc thanh đáy chỉ trên điện thoại; vị trí nhãn; màu và khoảng cách. Payment icons thêm NAPAS, kiểu thẻ trắng có viền và nền tối, logo riêng tải lên, khoảng cách, bo góc, xám rồi lên màu khi rê chuột, nhãn phía trước. Icon mới: github, discord, threads, shopee, lazada, link, print. Kiểm thử: 6 ca `social:`/`share:`/`payment-icons:` trong features-integration đạt, gồm HTML thuộc tính cũ; lint đạt; features-check còn FAIL banner có từ trước. Đã dịch vi, kiến thức AI cập nhật. Chưa xem tay trong editor/front.
