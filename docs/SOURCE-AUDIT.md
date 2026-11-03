# Đối soát mã nguồn theme và Elementor — G-53

Ngày đọc: 03-11-2026. Nguồn: File Manager trên site đối chiếu do owner chỉ định. Đối chiếu BIGSPY 1.80.3, nhánh `feature/ai-designer`, bắt đầu tại `96c684b`; kiểm tra lại các nhánh liên quan khi HEAD tiến tới `c8697ac` trong phiên.

## Phạm vi thực hiện

Đã mở **34 file nguồn** của sáu theme gốc và Elementor: đọc metadata/điểm khởi tạo, rồi lần theo các nhánh triển khai được ghi bên dưới. Với file lớn, đọc các hàm và điều kiện liên quan, không nhận là đã đọc từng dòng. Ngoài ra đã xem danh sách thư mục/module và tìm, đọc phần tương ứng trong mã BIGSPY.

Phần lớn file được mở bằng `Edit file`, đọc nội dung textarea rồi bấm **Cancel**; hai file Astra được tải qua nút Download. Không Save, đổi cấu hình, kích hoạt theme/plugin hay sửa nội dung trên hosting. Không đưa bản sao mã đối thủ hoặc thông tin tài khoản lên Git.

Đây là rà soát nguồn có phạm vi xác định, **không phải kiểm toán toàn bộ mọi file/asset**. Có mã không đồng nghĩa tính năng đang bật hoặc đã chạy đúng trên site. Chưa kiểm tra checksum với bản phát hành của từng hãng, chưa đo tốc độ và chưa chạy thử các luồng đối thủ.

| Sản phẩm | Phiên bản đọc từ mã | Điểm phân biệt quan trọng |
| --- | --- | --- |
| Astra | 4.14.1 | Có palette và command palette trong nguồn theme; addon Pro không nằm trong phạm vi đã đọc. |
| Flatsome | 3.20.10 | Bản Classic dùng UX Builder/shortcode; không phải Flatsome Site Builder 4 được nhắc trong tài liệu trước. |
| GeneratePress | 3.6.1 | Nguồn theme cơ bản; không gán khả năng GP Premium/GenerateBlocks vào bản này. |
| Hello Elementor | 3.5.1 | Theme nền với các công tắc lọc/bỏ thành phần và stylesheet. |
| Neve | 4.2.17 | Có header/footer engine và Magic Tags; không mặc định mọi nhánh Pro đã có. |
| OceanWP | 4.2.8 | WooCommerce config có nhiều nhánh thực; một số tích hợp vẫn phụ thuộc plugin khác. |
| Elementor | 4.3.5 | Có source component/global classes/atomic, nhưng có cổng experiment và nhánh chưa hỗ trợ import/export. |

Trong danh sách thư mục `wp-content/plugins`, lọc các tên liên quan Elementor/Astra/GP Premium/Neve/Ocean chỉ thấy `elementor`. Đây là bằng chứng về tên thư mục đã thấy, không chứng minh không thể có code liên quan dưới tên khác hoặc trong mu-plugin.

## Các đính chính bắt buộc đối với đánh giá G-36

1. **Số block:** BIGSPY có 74 bộ metadata trong `blocks/`, cộng `bst/countdown` (`inc/countdown.php:5`) và `bst/product-part` (`inc/product-design.php:23`) đăng ký bằng PHP: **76 loại block trong nguồn**. Số 74 trước chỉ đếm thư mục; kiểm thử filter 74 loại của G-36 cũng chỉ bao phủ nhóm đó.
2. **Product Builder đã có nền tảng:** `bst_product_design_page()`, `bst_product_part_render()` và `templates/product-design.php` đã dựng template sản phẩm từ block, có title/gallery/price/cart/tabs/related/upsell và preview theo ID. G-40 phải mở rộng nền hiện có, không xây lại vì tưởng chưa có.
3. **Dữ liệu động đã có một phần:** shortcode single-post trong `inc/theme-builder.php:330` trở đi và Product Part đã lấy dữ liệu runtime. G-37 là thống nhất nguồn, kiểu dữ liệu, context và UI binding, không phải thêm toàn bộ từ số không.
4. **Tìm settings đã có:** `assets/js/customizer-controls.js:423` đã tìm và focus vào control. Command palette Astra là một cách truy cập bổ sung từ admin; không được ghi BIGSPY thiếu tìm kiếm Customizer.
5. **Free shipping đã đọc vùng giao hàng:** `bst_free_shipping_min()` có `WC_Shipping_Zones::get_zone_matching_package()`, ngoài mức nhập tay. Khoảng trống cụ thể là `ignore_discounts`, coupon miễn phí vận chuyển và yêu cầu kết hợp; không phải thiếu toàn bộ tính năng tự đọc zone.
6. **Không dùng tài liệu bản mới thay cho bản đang cài:** phần Flatsome 4/MCP, Elementor Pro Loop/Grid/Theme Builder và GP Premium trong báo cáo cũ chỉ là tham khảo sản phẩm khác/gói khác, không phải kết quả đọc mã các bản trên hosting.
7. **Elementor có source chưa phải tất cả đều dùng được:** components phụ thuộc experiment Atomic; `IS_IMPORT_EXPORT_SUPPORTED = false` trong file đã đọc. Module dynamic-tags cơ bản trả `[]` trong `get_tag_classes_names()`. Phải lần provider và điều kiện, không kết luận chỉ từ tên folder.

## Phát hiện có bằng chứng và phần đáng nâng cấp

### 1. Màu và class dùng chung cần có vòng đời dữ liệu

**Nguồn:** A03, E04, E05. Astra `get_custom_colors()` và `generate_global_palette_style()` giữ vị trí màu bổ sung, có trạng thái retired và không phát CSS cho slot đã bỏ. Elementor global classes có context preview, danh sách usage, thứ tự class và kiểm tra nhãn trùng trong API.

**BIGSPY:** `bst_css_var_map()` (`inc/dynamic-css.php:14`) đã có biến màu, font, kích thước có tên cố định. `inc/colorful.php` có thư viện palette; `block-presets.php`/`css-presets.php` có recipe; `block-design.js` đã copy/paste thiết kế. Những phần này đã có và cần giữ.

**Khoảng trống đã tìm trong nhóm mã liên quan:** chưa thấy kho token do người dùng đặt tên với danh sách nơi dùng, trạng thái preview/publish và thao tác đổi/xoá có ánh xạ tham chiếu. Recipe và copy thiết kế là các khả năng khác với liên kết đồng bộ.

**Hướng tốt hơn:** mở rộng token có ID ổn định trên nền CSS variables hiện tại; xem trước danh sách nơi ảnh hưởng; khi xoá phải chọn thay thế/giữ fallback và hoàn tác được. Cho AI dùng đúng kho đó. Không chép nguyên hệ lưu trữ Elementor sang theme.

### 2. Dữ liệu động cần context rõ ràng, tận dụng nền đã có

**Nguồn:** N04. Neve `Magic_Tags::do_magic_tags()` nhận các tag đã cấu hình; các hàm cung cấp title/excerpt/archive/site/author/product, trả rỗng khi context không phù hợp và lọc HTML đầu ra. E06 chỉ là framework dynamic-tags; danh sách provider cơ bản của nó rỗng.

**BIGSPY:** đã có shortcode single-post và Product Part. AI `bst_aid_core_blocks()` chỉ cho một nhóm core block, **chưa có `core/query`, `core/post-template`, `core/block`**. Không được suy ra WordPress thiếu Query Loop hoặc synced patterns; vấn đề là độ phủ trong giao diện/công cụ và schema BIGSPY.

**Hướng tốt hơn:** nguồn dữ liệu phân kiểu text/URL/image/number, context post/product/term; cho xem preview và fallback khi thiếu dữ liệu; meta theo allowlist/quyền. Nghiên cứu dùng cơ chế WordPress sẵn có trước khi dựng engine riêng. Với AI, giữ nguyên block đã hỗ trợ trong trang hoặc trả lỗi rõ; mở thêm loại core phải giữ kiểm tra nguồn raw và quy tắc block hiện hành.

### 3. Thông báo miễn phí vận chuyển cần khớp điều kiện thật

**Nguồn:** F04, `Shipping::free_shipping()` dòng 88–190. Có đọc phương thức của zone, `ignore_discounts`, coupon free shipping, điều chỉnh chiết khấu và nhánh WPML đa tiền tệ.

**BIGSPY:** `inc/checkout-extras.php:33–73` đã đọc zone; chỉ chọn `requires` trong `min_amount/either`. Giá trị để vẽ thanh luôn trừ discount và không có nhánh riêng cho `ignore_discounts`/coupon free shipping. Đây là chênh lệch xác định từ mã; chưa dựng lại trên giỏ hàng thật.

**Hướng tốt hơn:** đối chiếu với điều kiện tính phí của WooCommerce đang chạy, không chỉ sao chép Flatsome. Test min amount, either/both/coupon, trước/sau discount, giá gồm thuế, nhiều package và đổi địa chỉ; chỉ báo “đủ điều kiện” khi thực sự phù hợp. Giữ lựa chọn nhập tay có giải thích và tương thích Cart/Checkout blocks khi mở rộng. Cả hai đoạn mã đã đọc đều bắt đầu từ package đầu tiên, nên nhiều package là việc phải kiểm chứng ở cả hai.

### 4. Ưu tiên ảnh đầu trang, không lazy-load đồng loạt

**Nguồn:** E08. Module image-loading có ngưỡng ảnh, xử lý loading/fetchpriority đã được đặt, nhận kích thước và xét vùng đầu nội dung. Nó còn can thiệp logic WordPress; không mặc định áp nguyên cơ chế đó cho BIGSPY.

**BIGSPY:** ảnh có attachment ID dùng `wp_get_attachment_image()` và hưởng cơ chế WordPress. Nhánh URL trực tiếp trong `blocks/banner/render.php:50` và `blocks/image/render.php:28` lại ghi cứng `loading="lazy"`; chưa có phân biệt ảnh hero trong các nhánh đó.

**Hướng tốt hơn:** điều khiển ưu tiên ảnh hero/ảnh ngoài thư viện, giữ width/height hoặc tỉ lệ để tránh nhảy bố cục, không vừa lazy vừa high priority. Dựa vào WordPress cho Media ID; tránh có hai bộ tối ưu tranh quyền trên cùng ảnh. Đo LCP/CLS trên fixture có ảnh ID, URL ngoài, mobile và slider trước/sau.

### 5. Tải script theo chức năng, không coi mọi mã đối thủ đều tối ưu

**Nguồn:** G03, H01, H03, O01. GeneratePress chỉ nạp menu khi có menu, search/back-to-top theo option; Hello tách reset/theme/header-footer CSS và có filter tắt. OceanWP chia script theo menu/search/filter nhưng vẫn có nhánh enqueue slider chung. GeneratePress cũng có nhánh font icon mặc định: không kết luận mọi đối thủ đều nhẹ trong mọi cấu hình.

**BIGSPY:** `inc/performance.php` đã chia CSS theo HTML thực tế, cache CSS sinh ra và host font local. `inc/assets.php:30–31` vẫn enqueue `main.js` và `blocks.js` chung. `inc/woocommerce.php:79–85` đã nạp script shop/product theo điều kiện. `bst_icon()` (`inc/helpers.php:548`) đã in SVG riêng cho icon cần dùng.

**Hướng tốt hơn:** giữ nền tốt hiện tại, phân tích các nhóm chức năng trong JS chung trước khi tách; lập fixture trang trắng, nội dung, form/popup, motion và shop. Đếm byte/request và đo hành vi; không chỉ lấy số file làm chỉ tiêu. Không thêm cơ chế buffering/cache HTML mới khi chưa chứng minh an toàn cho nội dung động và giỏ hàng.

### 6. Checkout và component: cần đặc tả, chưa có cơ sở làm tất cả ngay

**Nguồn:** O03 có nhánh multi-step checkout, distraction-free và floating bar; wishlist icon có nhánh phụ thuộc YITH/TI. F05 có mua ngay và redirect theo cart/checkout. E02/E03 có component override, khoá chỉnh sửa, kiểm tra tham chiếu vòng; import/export đang bị chặn trong nguồn đã đọc.

**BIGSPY:** đã có mua ngay (`inc/product-options.php`), thanh mua mobile và sticky summary (`inc/woocommerce.php`), Product Part và bố cục cart/checkout. Chưa thấy nhánh checkout nhiều bước tương đương trong nhóm đã rà. Không được coi mọi tính năng trên là thiếu toàn bộ, hoặc coi icon wishlist là hệ wishlist độc lập.

**Hướng tốt hơn:** dùng lại Product Part và các luồng WooCommerce; xác định vấn đề người dùng trước khi thêm nhiều bước checkout. Với component đồng bộ, đánh giá synced patterns WordPress trước, sau đó mới đặc tả override, khoá, chống tham chiếu vòng và import có dependency map. Chưa có kết luận sản phẩm BIGSPY tốt hơn về chuyển đổi bán hàng hay editor UX khi chưa thử thực tế.

## Ưu tiên sau khi đọc nguồn

| Ưu tiên | Việc có căn cứ | Điều kiện hoàn thành |
| --- | --- | --- |
| P1 | Đúng thông báo free shipping | Bộ case WooCommerce ở mục 3 đạt; chưa được gọi là lỗi đã tái hiện trên hosting. |
| P1 | Tải ảnh hero/URL trực tiếp đúng | Test thuộc tính và số đo LCP/CLS, không phá ảnh Media ID/slider. |
| P1 | Đo và giảm script không dùng — G-41 | Có fixture/số đo trước, giữ chức năng và reduced-motion. |
| P2 | Binding dữ liệu + độ phủ AI — tinh chỉnh G-37 | Tận dụng shortcode/Product Part/core; test context/fallback/quyền và trang có core block. |
| P2 | Token/class có tham chiếu — tinh chỉnh G-39 | Preview, usage, giữ ID, thay thế khi xoá và undo. |
| P3 | Loop/archive — G-38; mở rộng Woo Builder — G-40 | Đi sau nền context; mở rộng template sản phẩm hiện có, không xây trùng. |
| Cần khảo sát tiếp | Checkout nhiều bước, wishlist, popup campaign, component export | Đọc thêm triển khai, thử UI và xác định nhu cầu trước khi chốt đặc tả. |

Đây là thứ tự đề xuất từ nguồn, không tự thay thứ tự AI Designer owner đang giao cho agent khác trong TASKS.md. Không có số đo để tuyên bố BIGSPY đứng số 1 hoặc vượt toàn bộ đối thủ.

## Danh mục 34 file thực sự đã mở

Đường dẫn theme tính từ `wp-content/themes/<theme>/`, Elementor từ `wp-content/plugins/elementor/`. File CSS ở đây chỉ đọc header phiên bản; các file PHP đọc nhánh/hàm nêu trong cột cuối, không nhận là mọi dòng đều được phân tích.

| ID | Nguồn | File | Nhánh đã đọc |
| --- | --- | --- | --- |
| A01 | Astra | `functions.php` | Version, require chain, builder, compatibility và abilities bootstrap. |
| A02 | Astra | `style.css` | Header 4.14.1. |
| A03 | Astra | `inc/class-astra-global-palette.php` | `get_custom_colors`, retired slot, CSS variable, format/editor palette. |
| A04 | Astra | `inc/core/class-astra-command-palette.php` | Script registration, customizer panel commands và điều kiện phiên bản WP. |
| F01 | Flatsome | `functions.php` | Chuyển tới init, khởi tạo theme. |
| F02 | Flatsome | `style.css` | Header 3.20.10. |
| F03 | Flatsome | `inc/init.php` | Loader shortcode/builder, điều kiện WooCommerce và extensions. |
| F04 | Flatsome | `inc/woocommerce/class-shipping.php` | `free_shipping`, zone/coupon/discount và fragments. |
| F05 | Flatsome | `inc/woocommerce/class-buy-now.php` | Điều kiện sản phẩm, add-to-cart và redirect. |
| F06 | Flatsome | `inc/builder/builder.php` | UX Builder setup hook. |
| F07 | Flatsome | `inc/builder/shortcodes.php` | Catalog, Woo/CF7 gates và custom-product layout. |
| F08 | Flatsome | `inc/builder/core/ux-builder.php` | Runtime loader, editor gate, helpers và application. |
| G01 | GeneratePress | `functions.php` | Version 3.6.1, setup và require chain. |
| G02 | GeneratePress | `style.css` | Header. |
| G03 | GeneratePress | `inc/general.php` | `generate_scripts`, conditional enqueue và CSS cache switch. |
| G04 | GeneratePress | `inc/class-typography.php` | Selector, font manager, CSS tablet/mobile và editor style. |
| H01 | Hello | `functions.php` | Version 3.5.1, theme support, styles, locations và filters. |
| H02 | Hello | `includes/settings-functions.php` | Đường tới AdminHome Settings_Controller. |
| H03 | Hello | `modules/admin-home/components/settings-controller.php` | Các setting bật/tắt thành phần, apply callbacks, đăng ký option. |
| N01 | Neve | `functions.php` | Version 4.2.17, bootstrap và HFG loader. |
| N02 | Neve | `header-footer-grid/loader.php` | Load template/migration và render hooks. |
| N03 | Neve | `header-footer-grid/Main.php` | Builder registry, templates và gom styles. |
| N04 | Neve | `header-footer-grid/Core/Magic_Tags.php` | Parse/tag dispatch, single/archive/author/product và lọc đầu ra. |
| O01 | OceanWP | `functions.php` | Loader, performance conditions, menu/search/slider enqueue. |
| O02 | OceanWP | `style.css` | Header 4.2.8. |
| O03 | OceanWP | `inc/woocommerce/woocommerce-config.php` | Quick view, floating bar, filters, multi-step/distraction-free, wishlist dependencies. |
| E01 | Elementor | `elementor.php` | Version 4.3.5, requirement và plugin loader. |
| E02 | Elementor | `modules/components/module.php` | Atomic experiment, override hook, variants và import/export gate. |
| E03 | Elementor | `modules/components/circular-dependency-validator.php` | Direct/transitive references và giới hạn duyệt. |
| E04 | Elementor | `modules/global-classes/module.php` | Atomic gate, REST/usage/style và import hooks. |
| E05 | Elementor | `modules/global-classes/global-classes-rest-api.php` | Preview context, usage, capability, duplicate labels và apply changes. |
| E06 | Elementor | `modules/dynamic-tags/module.php` | Category/provider contract, danh sách base rỗng. |
| E07 | Elementor | `modules/element-cache/module.php` | Cache unique ID, control/TTL và shortcode entry; chưa lần toàn bộ render cache. |
| E08 | Elementor | `modules/image-loading-optimization/module.php` | Kích thước, loading/fetchpriority, ngưỡng ảnh và hooks. |

## Phần chưa đủ bằng chứng

- Toàn bộ JS bundle/canvas UX Builder và Elementor, từng control/widget, responsive behaviour, nội dung template cloud và tích hợp dịch vụ ngoài.
- Toàn bộ Astra/Neve/OceanWP/GeneratePress module, hook extensions, nguồn các addon Pro và Elementor Pro không có trong các folder đã thấy.
- Trạng thái experiment thực bật trên site, hiệu quả cache runtime, field validation thực tế, khả năng truy cập và tỷ lệ chuyển đổi checkout.
- Không đổi thứ tự triển khai chỉ từ tên feature. Trước mỗi hạng mục mới, đọc thêm nhánh liên quan còn thiếu và viết case nghiệm thu. Rà soát G-53 chỉ sửa tài liệu/phạm vi kết luận; không sửa runtime theme.
