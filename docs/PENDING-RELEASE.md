# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

### BIGSPY AI List: box "BIGSPY FAQ & FAQ Schema"

- **Đã thay đổi:** port từ theme cũ bigspy-ai-list. Khi trường `faq` trống, FAQ được đọc từ mục H2 "FAQ / Frequently asked" trong mô tả (do Generator viết), nên listing cũ vẫn có schema FAQPage. Box mới cùng ID `bigspy-listing-faq-schema` cho sửa FAQ, xem trạng thái và JSON-LD; lưu vào trường `faq` cũ (ACF nếu có). Khi đã lưu, mục FAQ hiển thị riêng và được bỏ khỏi mô tả để không lặp. Box "Listing fields" không còn ô FAQ trùng.
- **Kiểm thử:** features-check 133 ok (thêm 7 test FAQ), woo-check 27 ok (thêm test schema FAQPage). Chưa kiểm tra trên site thật.
- **Khi phát hành cần bổ sung:** Có gì mới + bản dịch vi cho chuỗi mới của box FAQ.
- **Trạng thái:** đã triển khai và kiểm thử; chờ owner yêu cầu phát hành.

### BIGSPY AI List: các phần trang listing từ theme cũ

- **Đã thay đổi:** `inc/listing/single.php` thêm mục lục + đường chia H2 trong mô tả, Key features, Pros & Cons, Integrations, Related resources (link nội bộ `_bigspy_internal_links` của Generator, chỉ cùng site) và ngày cập nhật. Mỗi phần có công tắc trong Features → BIGSPY AI List. Mục lục bài viết tách thành `bst_toc_apply()` dùng chung.
- **Kiểm thử:** woo-check 30 ok (thêm 3 test), features-check 133 ok. Chưa kiểm tra giao diện trên site thật.
- **Khi phát hành cần bổ sung:** Có gì mới + bản dịch vi.
- **Trạng thái:** đã triển khai và kiểm thử; chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
