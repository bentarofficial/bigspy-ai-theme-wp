# Đối chiếu Flatsome và BIGSPY Theme

Ngày kiểm tra: 05/10/2026  
Flatsome trên site tham chiếu: 3.20.10  
BIGSPY trong repo khi audit ban đầu: 1.8.0; các bản đã phát triển tiếp: 1.9.0–1.14.0

## Cập nhật 07/10/2026 — BIGSPY 1.17.2

Site tham chiếu Flatsome là Home Fresh Mart với Flatsome Child đang kích hoạt; BIGSPY đang chạy trên site TT Orico Store. Source trong repo và site TT Orico hiện ở 1.17.2.

- 1.15.0 bổ sung BIGSPY AI Builder canvas, header/footer/product page design, Buy Now, Quick View, classic archive load more/infinite scroll, Portfolio, countdown và mega menu nhiều cột.
- 1.17.0 bổ sung swatch label/màu/ảnh theo global WooCommerce attribute và term trên trang sản phẩm. Mặc định không bật; WooCommerce select vẫn là nguồn dữ liệu cho tổ hợp biến thể và kiểm tra add-to-cart.
- 1.17.1 bổ sung lọc AJAX tùy chọn cho widget WooCommerce native: danh mục, thuộc tính, đánh giá, giá và sắp xếp; URL/history và fallback điều hướng toàn trang được giữ lại.
- 1.17.2 bổ sung swatch màu/ảnh tùy chọn trên thẻ sản phẩm ở trang shop/danh mục; liên kết mở sản phẩm với giá trị đã chọn. Mặc định tắt.
- Còn thiếu: thư viện ảnh bổ sung riêng theo từng biến thể, lọc AJAX cho plugin bên thứ ba, chuyển đổi trực quan nội dung UX Builder cũ, mega menu có block, hotspot/Lottie/Instagram feed cấu hình nguồn và các công tắc quản lý asset/site loader. Xem [roadmap](roadmap-vi.md).
- Bản vá Countix/jQuery của child theme vẫn không được đưa vào core; cần adapter riêng sau khi xác định phiên bản và markup plugin trên site đích.

## Cập nhật 06/10/2026 — kiểm tra cả theme con

Đã đọc toàn bộ CSS/PHP và kiểm kê thư mục Flatsome Child đang kích hoạt. Xem [đối chiếu cha/con](flatsome-child-audit-vi.md). BIGSPY 1.14.0 thêm ảnh đại diện desktop/mobile, trang 404/bảo trì từ page tự thiết kế, đánh dấu menu theo anchor và ba tác vụ CSS. Các bản vá jQuery/Countix đặc thù chưa được đưa vào mặc định.

## Ghi nhận tại 1.13.0

- Studio thêm nhân bản, di chuyển và sao chép/dán kiểu dáng cho block được chọn; chưa phải canvas builder riêng.
- Shop thêm phân trang AJAX tùy chọn với history và fallback; chưa có lọc AJAX hoặc infinite scroll.
- Giỏ hàng/checkout thêm kiểu Simple/Focused, tổng tiền ghim và tự cập nhật số lượng cho giỏ hàng classic.
- Sản phẩm thêm kiểu tab, tab bổ sung dùng chung và nội dung trước/sau form mua hàng; sửa stacked gallery để tắt FlexSlider.
- Ở mốc 1.13.0, product template builder, Buy Now, Portfolio/feed, công cụ bảo trì và bộ quản lý asset còn thiếu; các bản sau bổ sung một phần như ghi nhận ở đầu tài liệu.

## Phạm vi

Chỉ đọc giao diện và file manager của site Flatsome, không sửa trang, cài đặt hay mã nguồn trên site đó. Đã xem UX Builder, Flatsome Customizer, WooCommerce và Advanced Options; sau đó đối chiếu với mã trong `bigspy-ai-theme-wp/`. Đây là kiểm tra theo các màn hình và nhóm đã quan sát, không phải kiểm định toàn bộ mọi plugin/tính năng có thể cài.

## Đã có ở cả hai hoặc BIGSPY có nền tương đương

| Nhóm | Flatsome | BIGSPY hiện tại |
| --- | --- | --- |
| Header | Header Builder, hàng/khu vực, thiết lập desktop/mobile, preset | Header Builder kéo thả, desktop/mobile riêng, sticky/transparent, nhiều phần tử; 1.11.0 thêm layout mẫu shop/dịch vụ/tối giản và liên kết thẳng tới Logo/Menus |
| Bắt đầu thiết kế | Flatsome Studio và UX Blocks | BIGSPY Studio, thư viện pattern, mẫu đồng bộ WordPress |
| Thành phần nội dung | UX Builder elements và UX Blocks | Gutenberg blocks cho layout, shop, bài viết, FAQ, testimonial, banner, slider… |
| Kiểu dáng | Style, màu, phông, CSS tùy chỉnh | Customizer màu/font, Additional CSS và thư viện CSS tích chọn |
| Shop cơ bản | Lưới sản phẩm, card và tùy chọn WooCommerce | Số cột theo thiết bị, sidebar, kiểu card, hover ảnh, tỉ lệ ảnh, nút giỏ, catalog mode |
| Footer | Cấu hình footer và nhiều vùng hiển thị | Hàng widget, CTA, copyright, social và back-to-top; 1.11.0 thêm mẫu Store/Services/Minimal và lối tắt sang Widgets |
| Tìm kiếm | Live search cho sản phẩm, bài viết và trang | Tìm kiếm gợi ý AJAX; có số lượng kết quả và tìm sản phẩm |
| Responsive | Các điều khiển responsive trong builder | Ẩn theo thiết bị, cột theo kích thước và motion tôn trọng reduced-motion |
| Tùy chỉnh và di chuyển cấu hình | Custom CSS, script và import/export theme options | CSS, head/body/footer scripts, export/import/reset tùy chọn theme |

## Những khoảng trống BIGSPY nên ưu tiên

| Ưu tiên | Flatsome có | BIGSPY hiện thiếu hoặc mới có một phần | Hướng phát triển riêng cho BIGSPY |
| --- | --- | --- | --- |
| **P0 — Trải nghiệm dựng trang** | Canvas xem trang trực tiếp; tìm và thêm element theo nhóm; đổi khung desktop/tablet/mobile; undo/redo; chạy thử animation; Options, Duplicate, Copy options và Save as preset tại phần tử | Gutenberg vẫn là bề mặt chính. BIGSPY Studio giúp chọn mẫu nhưng chưa có canvas chọn/sắp xếp element trực tiếp và thanh thao tác ngữ cảnh thống nhất | Xây “Visual Studio” trên WordPress block editor: chọn trên canvas, thanh thao tác gọn, duplicate, copy/paste kiểu dáng, lưu block/section thành pattern; giữ List View và keyboard làm đường thao tác tương đương |
| **P1 — Trang chi tiết sản phẩm** | Bố cục ảnh rộng tùy chọn; gallery slide/fade, lightbox, zoom, gallery ghim; sticky add-to-cart, Buy Now; nhiều kiểu product tabs; upsell/related slider/grid; chèn nội dung trước/sau nút mua | BIGSPY chủ yếu kế thừa trang sản phẩm chuẩn WooCommerce và có tùy chỉnh card/sidebar. Chưa có builder bố cục sản phẩm hay gallery controls tương đương | BIGSPY 1.9.0 bổ sung stacked gallery, thumbnails trái, sticky summary và thanh mua nhanh mobile. Còn thiếu product template builder, tabs presets, Buy Now và vị trí nội dung tùy chỉnh |
| **P1 — Duyệt shop** | AJAX/PJAX cho trang, phân trang blog/shop, infinite scroll, lọc category/widget và kiểu loader | BIGSPY có phân trang/tải thêm và lọc AJAX cho widget WooCommerce lõi; chưa hỗ trợ bộ lọc riêng của mọi plugin bên thứ ba | Bổ sung AJAX lõi với đồng bộ lịch sử và fallback. Còn thiếu các kiểu loader tùy chọn, kiểm tra tương thích theo plugin phổ biến và adapter riêng cho bộ lọc bên ngoài; PJAX của Flatsome được hãng ghi là thử nghiệm nên cần xử lý tương thích/fallback trước |
| **P1 — Cài đặt giỏ hàng/checkout** | Layout Simple/Focused, sidebar ghim, tự cập nhật số lượng, hiển thị ước tính vận chuyển/ngưỡng miễn phí, vị trí coupon và nội dung bổ sung | BIGSPY để phần lớn hành vi và layout cho WooCommerce mặc định | Thêm lớp trình bày tùy chọn và preview; không tự sửa trường/thứ tự nghiệp vụ đơn hàng nếu người bán chưa bật |
| **P2 — Tìm kiếm** | Gộp sản phẩm/bài viết/trang; độ trễ gợi ý; bố cục hàng/masonry/slider; sắp xếp sản phẩm; tìm SKU và tag | BIGSPY 1.12.0 đã có chọn nguồn dữ liệu, số gợi ý, debounce và SKU/tag tùy chọn | Còn thiếu các kiểu trình bày hàng/masonry/slider và sắp xếp kết quả sản phẩm |
| **P2 — Công cụ site** | Site Loader; trang bảo trì; lựa chọn trang 404; Portfolio post type; Instagram/Google integrations | BIGSPY có trang 404 thiết kế sẵn, Google Maps block, script tùy chỉnh và công cụ chuyển cấu hình; chưa có loader/bảo trì/portfolio/feed tích hợp | Chỉ thêm các công cụ dùng thường xuyên, bật/tắt riêng; portfolio thành post type/plugin tùy mục đích, không ép mọi site phải mang thêm tính năng |
| **P2 — Hiệu năng** | Preload trang, lazy-load, tắt block CSS/MediaElement/jQuery Migrate | BIGSPY hiện có tắt emoji và dựa vào native lazy-load; chưa có bộ công tắc dọn asset theo plugin/theme | Làm màn hình kiểm tra tương thích trước khi tắt asset; không bật mặc định các tùy chọn có thể làm hỏng plugin hoặc nội dung media |

## Lưu ý về những gì đang thấy trên site tham chiếu

- `UX Blocks`, `Portfolio`, wishlist/social login/reviews và một số tích hợp xuất hiện cùng plugin riêng. Cần tách rõ tính năng do Flatsome cung cấp với tính năng do plugin trên site cung cấp.
- Flatsome Site Loader, AJAX content delivery và một số asset-disabling là tùy chọn. AJAX/PJAX có cảnh báo tương thích plugin; đây là ý tưởng để BIGSPY thiết kế có fallback, không phải mã cần sao chép.
- BIGSPY 1.8.0 đã có nút xem thử animation từng block, hiệu ứng scroll/hover và thư viện pattern đồng bộ; các commit gần đây mới xử lý một phần UX Studio chứ chưa tạo full visual canvas.

## Thứ tự phát triển đề xuất

1. Hoàn thiện thao tác ngay trên canvas: chọn, sắp xếp, nhân bản, copy/paste style, lưu thành mẫu, undo/redo và khung xem theo thiết bị.
2. Xây product page builder từ block/preset; làm gallery, sticky add-to-cart và tabs thành tùy chọn có preview.
3. Thêm AJAX shop archive với fallback, kiểm tra tương thích và URL giữ được trạng thái lọc/phân trang.
4. Bổ sung search controls và các tiện ích site nhỏ dựa trên nhu cầu thực.

## Nguồn

- Giao diện quản trị Flatsome 3.20.10 và UX Builder do chủ site mở trong phiên này.
- [Flatsome Studio](https://docs.uxthemes.com/article/246-how-to-use-flatsome-studio), [Flatsome PJAX](https://docs.uxthemes.com/article/430-pjax), [custom product page](https://docs.uxthemes.com/article/245-how-to-create-a-custom-product-page), [Portfolio post type](https://docs.uxthemes.com/article/58-featured-item-custom-postype).
- BIGSPY: `README.md`, `bigspy-ai-theme-wp/inc/`, `bigspy-ai-theme-wp/blocks/`, `bigspy-ai-theme-wp/assets/`.
