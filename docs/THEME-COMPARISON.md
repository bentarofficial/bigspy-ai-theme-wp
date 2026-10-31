# BIGSPY AI Theme WP — đối soát và hướng phát triển

Ngày rà: 31-10-2026. Task G-36. Mã nền: `feature/ai-designer`, `b47a859`, phiên bản khai báo 1.79.1; có thay đổi chưa phát hành. Đây là đánh giá sản phẩm theme, không phải thiết kế website.

## Phạm vi và cách đọc

- BIGSPY: kiểm tra cấu trúc block, bộ dựng, dữ liệu AI, tuỳ chọn WooCommerce, biểu mẫu và cách nạp tài nguyên trong mã nguồn.
- Đối chiếu: tài liệu chính thức Astra, Flatsome, GeneratePress, Hello Elementor, Neve, OceanWP, Elementor. Tính năng trả phí hoặc cần extension được tính là hướng tham khảo, không mặc định có trong bản miễn phí.
- Site đối chiếu đã thấy sáu thư mục theme; Flatsome Child đang hoạt động, Elementor 4.3.5 đang bật, chưa thấy Elementor Pro trong danh sách plugin. Chưa kiểm toán từng file hay xác nhận phiên bản của sáu theme trên hosting.
- Site nháp do owner cung cấp để nghiệm thu theme: `wheat-spider-420567.hostingersite.com`. Không lưu thông tin đăng nhập trong tài liệu.
- “Có mã” chưa chứng minh ngang bằng về giao diện, khả năng truy cập, độ ổn định hoặc tốc độ. Các mục chưa đo giữ nguyên trạng thái chưa đo.

## Những gì BIGSPY đã có

Đếm trực tiếp từ `blocks/*/block.json`: **74 block**, gồm 55 `bst/*` và 19 `bigspy/*`; số 73 trong trao đổi trước là sai. Có 27 file pattern; màn hình tạo trang nhanh chỉ có hai lựa chọn business/shop, khác với số pattern.

| Nhóm | Bằng chứng trong `bigspy-ai-theme-wp/` | Đánh giá và phần cần làm tốt hơn |
| --- | --- | --- |
| Header/footer và mobile | `inc/header-builder.php`, `header-design.php`, `footer-design.php` | Đã có nền tảng như Astra/Neve/Flatsome. Nghiệm thu thao tác kéo thả, bàn phím và độ thống nhất giữa các màn hình trước khi mở thêm tuỳ chọn. |
| Mega menu, menu dọc, tìm kiếm tức thời | `inc/mega-menu.php`, `header-extras.php` | Đã có; live search có cả nhánh tìm SKU. Cần đo và thử với catalogue lớn, sản phẩm ẩn/hết hàng, bàn phím và mobile. |
| Màu, font và thiết kế chung | `inc/dynamic-css.php`, `block-design.php`, `block-presets.php`, `css-presets.php` | Đã có global settings và recipe. Chưa thấy hệ thống quản lý token spacing/type và component có thuộc tính ghi đè thống nhất như hướng Elementor hiện tại. |
| Bố cục và chỉnh responsive | `blocks/section`, `row`, `column`; `inc/block-design.php` | Đã có điều khiển theo thiết bị. Cần chuẩn hoá giao diện, kế thừa giá trị và cách báo thuộc tính đang ghi đè. |
| Theme Builder có điều kiện | `inc/theme-builder.php` | Có header/footer, trước/sau nội dung, single post, 404; include/exclude, IDs, category, đăng nhập và thiết bị. Chưa có vị trí thay archive/search/product/cart/checkout trong danh sách location. |
| Bài viết và danh sách sản phẩm | `blocks/posts`, `products`, `products-list`; `inc/woocommerce-elements.php` | Có grid/list/slider và tuỳ chọn truy vấn. Chưa thấy loop item do người dùng tự thiết kế rồi gắn dữ liệu động như Elementor Loop Grid / GeneratePress Loop Template. |
| Motion và minh hoạ | `assets/js/blocks-motion.js`, `blocks/*/block.json` | Nhiều block có thể chỉnh trực quan. G-36 phát hiện AI chưa xử lý đúng namespace và quan hệ con của các block mới; đã bổ sung trong đợt này. |
| Popup | `blocks/popup/block.json`, `render.php` | Có button/link/delay/scroll/exit và tần suất. Không được coi là còn thiếu popup; cần bổ sung quản lý chiến dịch và báo xung đột điều kiện nếu phát triển tiếp. |
| WooCommerce | `inc/shop-options.php`, `product-options.php`, `quick-view.php`, `variation-swatches.php`, `commerce-customization.php`, `checkout-extras.php` | Có quick view, swatch, bố cục, AJAX shop và tiện ích giỏ hàng. Cần mở rộng thiết kế template và kiểm thử tương thích cả checkout cổ điển lẫn Woo blocks. |
| Wishlist/so sánh sản phẩm | Chưa thấy module riêng trong nhóm `inc/` đã rà | Ứng viên bổ sung dạng module bật/tắt, có dữ liệu dùng lại được. Cần quy định hành vi khách chưa đăng nhập, lưu trữ và giới hạn trước khi triển khai. |
| Form và gửi mail | `inc/form-native.php`, `form-migrate.php`, `features/smtp.php` | Có 12 loại trường, file, xác thực, chống spam, lưu submission và mail. Điều kiện trường, form nhiều bước và hành động tích hợp cần đặc tả riêng; dữ liệu tệp khi mail lỗi vẫn là việc cần xử lý. |
| Hiệu năng | `inc/performance.php`, `assets.php` | Có CSS theo nhóm block, font local và CSS sinh ra có cache. `main.js` và `blocks.js` vẫn được enqueue chung; chưa có benchmark so với theme khác. |
| Thư viện tái sử dụng | `inc/pattern-studio.php`, `patterns/`, `inc/admin/dashboard.php` | Có thư viện pattern trong editor. Cần component đồng bộ và kiểm tra tác động khi thay đổi; kho site mẫu là hạng mục sau, không thay thế việc nâng engine theme. |
| AI, quyền và hoàn tác | `ai-designer/includes/tools-design.php`, `permissions.php`, `tasks.php` | Có schema, snapshot/undo, duyệt thao tác và quy tắc nội dung sửa được bằng block. Cần hợp đồng AI bao phủ đầy đủ mọi block đã đăng ký. |
| Công cụ tích hợp | `inc/features/`, `inc/auto-detect/`, `inc/updater.php` | Có WebP, SMTP, mã theo dõi, phát hiện vấn đề và updater. All-in-one nên có trạng thái module rõ ràng và chỉ nạp chức năng đang dùng. |

## Đối thủ đã tiến tới đâu

- **Astra:** header/footer builder đã có trong theme; tuỳ chọn nâng cao thuộc Pro. Học cách nhóm và giải thích điều khiển, không chỉ đếm số tuỳ chọn. [Tài liệu](https://wpastra.com/docs/faqs-astra-header-footer-builder/).
- **Neve:** tài liệu có header/footer, thiết lập theo mobile, global styles và nhiều module Pro. Học tính nhất quán khi chuyển giữa thiết bị. [Tài liệu](https://docs.themeisle.com/neve/neve-doc).
- **GeneratePress:** Block Elements thuộc GP Premium, gồm template nội dung và loop/archive cùng display rules. BIGSPY cần mở rộng phạm vi template và cách chọn dữ liệu. [Tài liệu](https://learn.generatepress.com/classic-theme/block-elements/).
- **Flatsome:** Classic dùng UX Builder/shortcode; tài liệu hiện tại còn giới thiệu Site Builder dùng WordPress blocks và MCP. Không thể coi “block native + AI” là lợi thế độc quyền của BIGSPY. Chưa xác nhận Site Builder mới có trên site đối chiếu. [So sánh chính thức](https://flatsome.com/classic-vs-site-builder), [Header Designer Classic](https://demos.flatsome.com/features/header-designer/).
- **Hello Elementor:** nền tối giản cho Elementor, có hook kiểm soát các phần theme và style. Học cách giữ phần mặc định nhẹ và đo tài nguyên thực tải. [Tài liệu](https://developers.elementor.com/docs/hello-elementor-theme/).
- **OceanWP:** nhiều tuỳ chỉnh cửa hàng và trang; một số cần Ocean Extra hoặc extension trả phí. BIGSPY đã có nhiều phần tương ứng, cần nghiệm thu độ sâu và tính đồng nhất. [Customizer](https://docs.oceanwp.org/article/895-oceanwp-customizer-introduction), [WooCommerce](https://docs.oceanwp.org/article/436-ecommerce-features-in-depth).
- **Elementor:** Loop Grid cho phép thiết kế thẻ dữ liệu động; MCP hiện hỗ trợ global classes, variables và reusable components. BIGSPY cần đạt mức dữ liệu và hệ thống thiết kế tương tự bằng block trực quan của mình. [Loop Grid](https://elementor.com/help/how-do-i-build-a-loop-grid/), [MCP](https://elementor.com/help/how-to-build-and-edit-your-site-using-elementor-mcp/).

## Những điểm BIGSPY có thể làm tốt hơn — mục tiêu cần chứng minh

| Mục tiêu | Tiêu chí nghiệm thu |
| --- | --- |
| AI sửa đúng thành phần, giữ nguyên phần còn lại | Mọi family block qua đọc → sửa con → lưu không mất attrs/inner; từ chối cấu trúc sai; undo phục hồi nội dung. G-36 bắt đầu thực hiện. |
| Một hệ thống thiết kế cho cả người và AI | Màu/font/spacing dùng token chung; sửa token cập nhật đúng mọi nơi; cho xem phần bị ảnh hưởng và hoàn tác. Không tạo bản CSS lặp ở từng block. |
| Dựng template động dễ hiểu | Chọn nguồn dữ liệu trong giao diện; preview dữ liệu mẫu; dùng lại loop item; archive, product và nội dung rỗng có fallback đúng. |
| All-in-one nhưng không nạp thừa | Module tắt không enqueue tài nguyên frontend của nó; có bảng byte/request theo fixture. Giữ cùng nội dung, ảnh, hosting và cache khi so với đối thủ. |
| Dễ dùng bằng tiếng Việt, dùng được với bàn phím | Kiểm tra menu, dialog, popup, form và editor bằng bàn phím; focus đúng; lỗi form rõ; reduced-motion có nội dung đầy đủ. |
| Tương thích và nâng cấp đáng tin | Fixture WooCommerce cổ điển/blocks, shortcode Flatsome đang hỗ trợ, plugin SEO, site cũ; cập nhật giữ dữ liệu và layout. Không tuyên bố hỗ trợ mọi plugin khi chưa thử. |

Mục tiêu “số 1” phải được chứng minh qua các fixture và tác vụ người dùng như trên. Đợt này chưa tạo số đo chứng minh BIGSPY nhanh hơn hoặc tốt hơn mọi đối thủ.

## Thứ tự phát triển

1. **G-36 — tính toàn vẹn AI/block:** sửa xuất dữ liệu `bigspy/*`, quan hệ container/con, trạng thái block bị ẩn; test từng family và báo kết quả test thực tế.
2. **G-37 — nền dữ liệu động:** binding cho title, ảnh, excerpt, meta được cho phép; UI chọn nguồn, preview và fallback; kiểm soát dữ liệu theo quyền.
3. **G-38 — Loop Builder và archive/search:** thiết kế loop item bằng block, truy vấn và phân trang; reuse cùng binding của G-37.
4. **G-39 — hệ thống token/component:** token màu/font/spacing, component đồng bộ, ghi đè và hoàn tác; AI dùng cùng schema với editor.
5. **G-40 — WooCommerce Builder:** template product/shop dùng nền G-37/G-38; nghiệm thu với biến thể, giỏ hàng và hai kiểu checkout. Wishlist/compare cần module riêng sau đặc tả.
6. **G-41 — tải tài nguyên theo module:** đo fixture trước/sau, giảm JS dùng chung theo chức năng, giữ frontend/editor hoạt động và không nhấp nháy layout.
7. **G-42 — form bền vững và dễ truy cập:** lưu tệp có quyền truy cập phù hợp khi gửi mail lỗi; thử gửi lại; sau đó mới thêm điều kiện/nhiều bước.

Các mục G-37–G-42 là backlog phát triển đã được owner yêu cầu theo định hướng theme, chưa phải tính năng đã triển khai. Đóng gói/phát hành theo quy trình duyệt riêng. WooCommerce, SEO, cache, migration và plugin nghiệp vụ tiếp tục có ranh giới tích hợp theo TASKS.md hiện hành.

## Kết quả triển khai G-36

- `bst_aid_compact_blocks()` trả `attrs`/`inner` cho cả `bst/*` lẫn `bigspy/*`; giữ cơ chế raw có kiểm tra nguồn cho core.
- Builder nhận các container motion mới và bắt buộc đúng parent/child; từ chối payload raw cũ cho native block để AI đọc lại thay vì lưu nội dung rỗng.
- Block Manager lấy namespace từ metadata, sửa trường hợp block motion mới bị gắn sai thành `bst/*` khi ẩn.
- Runner Designer hỗ trợ giới hạn worker và đọc báo cáo kiểm thử; không chỉ coi trạng thái READY là đạt.
- Test trước sửa đã thất bại ở `Native block exposes editable attributes and children: bigspy/ticker`, tái hiện vấn đề hợp đồng dữ liệu.
- Sau sửa: Designer 151 mục, features-check 82 mục, Auto Detect 67 mục đạt; filter JavaScript thử cả 74 block; block previews, lint PHP/JS và diff đạt. Bằng chứng/commit cuối đợt ghi trong task G-36 và PENDING-RELEASE.md; chưa phát hành, chưa thử AI thật và chưa nghiệm thu UI hosting.
