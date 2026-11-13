# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

Đã phát hành ở 1.94.0: G-134-d, khoảng cách khối, banner điện thoại. Đã phát hành ở 1.93.0: Bento/Stacking Cards options, Footer scroll effect. Đã phát hành ở 1.92.0: G-163, G-164, G-134-c. Đã phát hành ở 1.91.0: G-162, G-134-a, G-133-a/b/c, G-132-a/b/c/d, G-129, G-130, G-131, G-141, G-147, G-151, G-94, G-100, G-103, G-113, G-117, G-118, G-119, G-120, G-123. Đang chờ:

- **S-13-a Editor giống trang xuất bản – Stacking Cards, Flip Box:** Stacking Cards trong editor dùng đúng class/biến CSS của render.php (`bs-stack`, `bs-stack__card`), thẻ dính và chồng khi cuộn canvas, có hiệu ứng thu nhỏ/tối; nút toolbar "Edit cards"/"Preview stack" để mở thẻ ra sửa (bỏ dòng thông báo "Cards are listed here…"). Flip Box: toolbar "Front"/"Back" thay nút lật; CSS thêm ẩn mặt quay đi bằng `visibility` (chống hiện 2 mặt khi trình duyệt/khung không vẽ backface). Ảnh so sánh: `docs-local/S-13-anh/`. Kiểm thử: features-check 285 ok, còn FAIL cũ banner lazy. Chưa xem ở tablet/Gutenberg mobile preview; khối khác của S-13 chưa làm.
- **Dòng hoa hồng affiliate mặc định tắt:** Customizer → Layout → Listing design → "Disclosure line on listings" giờ mặc định "Sponsored line only" (dòng "We may earn a commission…" ẩn); thêm lựa chọn "Sponsored and affiliate lines" (giá trị `all`) để bật lại. Site đã chọn "sponsored"/"none" giữ nguyên; site để mặc định sẽ mất dòng affiliate. Kiểm thử: 4 ca `listing: disclosure` trong features-check đạt; còn FAIL banner có từ trước. Chuỗi vi đã có.
- **G-167 Listing texts (chế độ BIGSPY AI List):** Customizer → Layout → **Listing texts** (`inc/listing/texts.php`). "Call a listing" (Listings/Tools/Products/Services/Places/Companies/Custom số ít + số nhiều) sinh mọi chữ mặc định; mỗi chữ của thanh lọc, thẻ, trang listing, Sponsored, danh mục có một ô ghi đè (trống = mặc định, mặc định hiện làm placeholder; ghi đè thiếu `%s` thì dùng mặc định). Helper `bst_listing_text( $key, ...$args )`, `bst_listing_count_text()`, filter `bst_listing_text`; chữ người dùng nhập được đăng ký với Polylang/WPML. Thanh lọc: bật/tắt ô tìm, danh mục, sắp xếp, số kết quả, Clear filters; chọn kiểu sắp xếp hiện và sắp xếp mặc định (`woocommerce_default_catalog_orderby`); danh mục cấp 1 hoặc cả cấp 2; tắt hết thì không in form. Khối `bigspy/search` (placeholder), `bigspy/elements` (View more, View all categories), `bigspy/sponsored` (badge, nhãn, nút), `bst/listing-sidebar` lấy chữ từ Listing texts khi thuộc tính trống (mặc định trong block.json đổi thành rỗng). AI Designer đọc/ghi qua get_settings/update_settings (đã có duyệt), kiến thức đã cập nhật.
  - **Phải ghi rõ trong changelog khi phát hành:** site đang bật chế độ List được lưu "Tools" một lần khi cập nhật (option `bst_listing_texts_init`) nên thanh lọc vẫn là "Search tools / Tool name or keyword / Find tools"; site mới mặc định "Listings". Chữ sinh từ tên gọi nên vài chỗ trước ghi "listing" nay theo tên gọi: "12 tools found" (trước "12 listings found"), "5 tools" ở danh mục, "Sponsored tool.", "Related tools", placeholder khối tìm "Search tools..." (trước "Search tools, software and services..."). Muốn như cũ: chọn "Listings" hoặc điền ô tương ứng. Khối Sponsored kiểu badge: ô Badge text trống giờ hiện "Sponsored by" (trước là ẩn nhãn).
  - **Kiểm thử:** woo-check 27 ca mới đạt (tên gọi, ghi đè, trống/xoá về mặc định, tắt từng ô, tắt hết, sắp xếp, cấp 2, migration, dịch mẫu). Chưa xem tay trong Customizer và trên trình duyệt. Chưa làm: kiểu nút icon của thanh lọc, ẩn/hiện danh mục rỗng (mục 2.3 phiếu); nút "Visit website" thuộc G-166.
- **Sửa lỗi Customizer bị che phần đầu:** mở một panel/section bất kỳ thì ô tìm kiếm + ô "Quick-edit labels" nằm dưới tiêu đề section và thò ra đè lên control đầu tiên. Giờ `.bst-csearch` ẩn (`is-covered`) khi có panel/section đang mở, hiện lại ở màn hình gốc (`assets/js/customizer-controls.js`, `assets/css/customizer.css`). Kiểm thử: `node --check` đạt; chưa xem tay trong Customizer. Khi phát hành: không cần bổ sung tài liệu.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
