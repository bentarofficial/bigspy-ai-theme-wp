# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

Đã phát hành ở 1.96.0: marquee nâng cấp, Floating contact, độ rộng sidebar theo trang, nút Add to trong List View. Đã phát hành ở 1.95.0: S-13-a, dòng hoa hồng affiliate mặc định tắt, G-167 Listing texts, sửa ô tìm Customizer. Đã phát hành ở 1.94.0: G-134-d, khoảng cách khối, banner điện thoại. Đã phát hành ở 1.93.0: Bento/Stacking Cards options, Footer scroll effect. Đã phát hành ở 1.92.0: G-163, G-164, G-134-c. Đã phát hành ở 1.91.0: G-162, G-134-a, G-133-a/b/c, G-132-a/b/c/d, G-129, G-130, G-131, G-141, G-147, G-151, G-94, G-100, G-103, G-113, G-117, G-118, G-119, G-120, G-123. Đang chờ:

- **Tính năng tùy chọn "AI search (AEO and GEO)"** (Features, mặc định tắt).
  - **Đã thay đổi:** `inc/features/ai-search.php` phục vụ `/llms.txt` (trang chính, danh mục, listing/sản phẩm và bài viết mới nhất kèm mô tả; cache 1 giờ, xóa khi lưu nội dung); khi không có plugin SEO thì in meta description, Open Graph, Twitter card và schema Organization (kèm sameAs), WebSite, Article (tác giả Person), BreadcrumbList. Có plugin SEO thì không in gì.
  - **Kiểm thử:** `node tools/features-check.cjs` với WP 6.5 cục bộ: 8 kiểm thử ai-search đạt. `social`/`payment-icons: old attributes render as before` FAIL sẵn từ trước trên bản WP 6.5 này, không liên quan.
  - **Khi phát hành cần bổ sung:** Có gì mới, README Updates, kiến thức AI (`ai-designer/knowledge/theme.md`).
  - **Trạng thái:** đã triển khai và kiểm thử; chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
