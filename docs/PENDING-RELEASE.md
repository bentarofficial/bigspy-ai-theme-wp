# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

- **Yêu cầu/tính năng:** G-102, khối Listing elements có lại đủ tuỳ chọn của khối BIGSPY AI List cũ.
- **Đã thay đổi:** `inc/listing/blocks.php` (View more: chữ, căn lề, trang đích tự động/Listings/Featured/Popular/Categories/Blog/URL riêng; Load more tải tiếp tại chỗ qua REST `bst/v1/listing-elements`; chế độ slider; khoảng cách; nút Visit cạnh tiêu đề hoặc dưới chữ và căn lề; blog dạng thẻ/hàng gọn/tiêu đề đánh số với ảnh, ngày, tác giả, chuyên mục, trích đoạn; lưới danh mục: số cột, viền, bo góc, hover nâng/zoom/bóng/tốc độ, View all), `assets/js/listing-elements.js` mới, CSS tương ứng, 43 thuộc tính mới trong `blocks/listing-elements/block.json`, panel editor chia nhóm và chỉ hiện tuỳ chọn hợp với loại nội dung.
- **Kiểm thử:** woo-check 47 ok (6 test mới: View more, Load more + endpoint, slider, blog titles, style lưới danh mục); features-check 135 ok, chỉ lỗi "What is new" do lỗi cú pháp của 1.86.0 (đã sửa ở 1.86.1); previews đạt. Chưa xem panel editor và slider/Load more trên trình duyệt.
- **Khi phát hành cần bổ sung:** Có gì mới: "Listing elements: View more hoặc Load more, slider, kiểu blog, viền và hover cho lưới danh mục, tuỳ chọn chia nhóm". Lưu ý: View more mặc định bật như khối cũ, trang dựng bằng theme mới sẽ hiện thêm link "View more →".
- **Git:** `feature/ai-designer` 1b77732.
- **Trạng thái:** chờ owner yêu cầu phát hành.

- **Yêu cầu/tính năng:** G-106, Icon List: sửa link của từng dòng ngay trên thanh công cụ; editor giống bản xuất bản.
- **Đã thay đổi:** `assets/js/blocks-editor.js` (dòng đang sửa có nút "Item link" trên thanh công cụ mở ô link của WordPress: xem, sửa, bỏ link; dòng có link có dấu mắt xích khi chọn khối; dấu "+" của dòng không icon chỉ hiện khi đang chọn khối), `assets/css/editor.css`.
- **Kiểm thử:** cú pháp JS đạt; thử trên editor UX Block Footer của bigspyai.com bằng cách nạp tạm khối mới trong trình duyệt (không lưu): nút hiện "Item link: …/about-us/", ô link mở đúng, 4 dấu mắt xích, cột khác không còn "+".
- **Khi phát hành cần bổ sung:** Có gì mới: "Icon List: bấm nút link trên thanh công cụ để xem, sửa hoặc bỏ link của dòng đang chọn".
- **Git:** `feature/ai-designer` 1c7decc.
- **Trạng thái:** chờ owner yêu cầu phát hành.

- **G-116 Block Map:** chỉ nhận iframe từ host Google thật (`bst_map_embed_url()`), tiêu đề iframe mặc định "Map". Kiểm thử: lint đạt; chưa chạy test PHP (máy không có PHP), chưa thêm ca test.
- **G-115 Block Lottie:** tự host `lottie-player.js` 2.0.8 (MIT, 380 KB gốc, 97 KB gzip, sha256 f16bdeb47f643a09ec70a37397bf0134c311413395d164744d12cba7098bce49), bỏ unpkg.com; readme.txt ghi công. Chưa thử hoạt ảnh trên trình duyệt.
- **G-110 Updater:** khai báo `requires`, `requires_php` (đọc header style.css của tag) trong response cập nhật và themes_api; tải lỗi thì để rỗng. Kiểm thử: updater-playground-check 17 đạt. `Tested up to` chưa đổi.
- **G-109 Tìm kiếm trực tiếp:** cache 60 giây cho khách chưa đăng nhập (filter `bst_live_search_cache_ttl`), đếm ký tự bằng `mb_strlen`, huỷ request cũ bằng AbortController. Kiểm thử: lint và `node --check` đạt; chưa chạy test PHP/trình duyệt, chưa thêm ca test.
- **G-124 Cổng lint:** `tools/lint.py` chạy trong release.py và build-theme.py (không đổi sản phẩm).
- **Khi phát hành cần bổ sung:** không cần.

- **Yêu cầu/tính năng:** G-89 (chuyển bigspyai.com sang theme mới): các lỗ hổng tìm thấy khi so site nháp (theme cũ) với site thật.
- **Đã thay đổi:** `inc/template-tags.php` (breadcrumb: khi Rank Math/Yoast tắt mô-đun breadcrumb và không in gì thì dùng chuỗi breadcrumb của theme thay vì để ô trống; trang tắt H1 có thể vẫn hiện breadcrumb, `bst_page_crumbs_only`), tuỳ chọn Customizer "Show breadcrumbs on pages whose H1 is off" (`breadcrumbs_pages`, mặc định tắt), shortcode `[bigspy_sponsored_card]` (một thẻ Sponsored xoay vòng cho hero) và `[bigspy_category_directory]` (đặt thư mục danh mục sau hero của trang Categories), `assets/css/listing.css` (Key features thành ô viền như theme cũ), `inc/features.php` (khi chuyển từ BIGSPY AI List: làm mới rewrite và xoá cache LiteSpeed sau khi chuyển).
- **Kiểm thử:** features-check đạt (2 test mới: thẻ Sponsored, breadcrumb dự phòng); woo-check đạt (1 test mới: shortcode thư mục danh mục). Chưa xem breadcrumb, ô Key features và thẻ Sponsored trên bigspyai.com vì bản 1.86.1 đang cài chưa có các thay đổi này.
- **Khi phát hành cần bổ sung:** Có gì mới: "Breadcrumb có dự phòng khi mô-đun breadcrumb của SEO plugin tắt; tuỳ chọn breadcrumb cho trang tắt H1; shortcode thẻ Sponsored và thư mục danh mục cho hero; Key features dạng ô". Lưu ý khi chuyển từ BIGSPY AI List: nếu site có LiteSpeed, sau khi chuyển nên lưu lại Permalinks và Purge All.
- **Git:** `feature/ai-designer`, xem commit trong HANDOFF.
- **Trạng thái:** chờ owner yêu cầu phát hành.

- **Yêu cầu/tính năng:** G-89 (owner 10-11): Sponsored có khối riêng, thẻ bấm được cả thẻ, nút mảnh như theme cũ.
- **Đã thay đổi:** khối mới `bigspy/sponsored` (`blocks/sponsored/`, nhóm BIGSPY AI List): số thẻ, số cột máy tính/tablet/điện thoại, kiểu nút (theo cài đặt chung / không nút / viền nhẹ / đặc), chữ nút, nhãn "Sponsored", mô tả, mở tab mới. `inc/listing/sponsored.php`: `bst_render_sponsored()`, `bst_sponsored_slot()` nhận tuỳ chọn, cả thẻ là một liên kết tới đối tác (link phủ toàn thẻ, nút chỉ còn là nhãn trang trí); cài đặt chung mới "Sponsored button" ở trang Features (mặc định: không nút). `assets/css/listing.css`: nút kiểu viền nhẹ giống theme cũ (nền #f6f9fb, viền #dbe7f0, chữ 12px đậm 650), kiểu đặc là tuỳ chọn; nút Visit của thẻ listing cũng đổi sang kiểu nhẹ; lưới Sponsored tự giãn khi chỉ có một thẻ. `assets/js/sponsored.js` cập nhật mọi liên kết khi xoay vòng. Shortcode `[bigspy_sponsored_grid]` và `[bigspy_sponsored_card]` giữ nguyên, nay dùng cùng bộ dựng.
- **Kiểm thử:** features-check đạt (3 test mới), check-block-previews đạt 83 khối, woo-check đạt. Chưa xem khối trong editor và trên bigspyai.com.
- **Khi phát hành cần bổ sung:** Có gì mới: "Khối Sponsored riêng với tuỳ chọn nút, cột, nhãn; cả thẻ Sponsored là liên kết; nút kiểu nhẹ". Lưu ý: thẻ Sponsored mặc định không còn nút "Open site"; muốn có nút, chọn kiểu nút ở Features hoặc trong khối. Kiến thức AI Designer nên nêu khối `bigspy/sponsored` (G-123).
- **Git:** `feature/ai-designer`, xem HANDOFF.
- **Trạng thái:** chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

## G-126 Code background (animated) cho Section và Banner

- **Đã thay đổi:** panel mới "Code background (animated)" ở Section và Banner: 6 preset CSS (gradient động, aurora, blobs, lưới phối cảnh, chấm trôi, tia sáng xoay), 3 preset canvas (mạng hạt, đường sóng, bầu trời sao), và "Custom code" (CSS scoped bằng @scope + HTML/SVG động, đã lọc script/handler). Tuỳ chọn: 3 màu (mặc định theo palette), tốc độ, mật độ, độ mờ lớp, ẩn trên mobile. Lớp nằm giữa ảnh nền và overlay. File: inc/code-bg.php, assets/css/code-bg.css, assets/js/code-bg.js (chỉ tải khi dùng), blocks-editor.js, render.php của section/banner.
- **Kiểm thử:** lint sạch; Playground: 10 kiểu render đúng ngoài site và trong editor (canvas chạy trong iframe editor), script/onload trong custom HTML bị loại. check-block-previews báo lỗi có sẵn bigspy/sponsored (không liên quan). Chưa dịch chuỗi vi, chưa thêm vào kiến thức AI Designer.
- **Khi phát hành cần bổ sung:** mục Có gì mới; bản dịch vi; hồ sơ khối AI Designer (thuộc tính codeBg).
- **Trạng thái:** chờ owner yêu cầu phát hành.
