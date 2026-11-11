# Roadmap BIGSPY AI Theme WP

Quy tắc phát triển, phát hành và ma trận kiểm tra: xem [DEV-RULES.md](DEV-RULES.md).

## Phạm vi owner cập nhật 03-11-2026

G-68: [nghiên cứu hành vi người dùng và UX AI](USER-BEHAVIOR-AI-RESEARCH.md), phân biệt khảo sát thị trường với giả thuyết cần kiểm chứng; đề xuất G-69 sau nghiên cứu, không thay thứ tự AI Designer đã chốt.

G-66: nghiên cứu [responsive và khả năng truy cập cho block/AI](RESPONSIVE-AI-RESEARCH.md); đề xuất G-67 kiểm chứng và cải tiến theo fixture, tiếp tục G-41 riêng cho đo tài nguyên.

Nghiên cứu bổ sung G-64 về AI bảo toàn block, dữ liệu động, Query Loop và component: [BLOCK-AI-RESEARCH.md](BLOCK-AI-RESEARCH.md). G-65 là đề xuất tương thích AI; G-37/G-38/G-39 tiếp tục theo backlog, không đổi thứ tự AI Designer đã chốt.

- Ưu tiên **block kéo thả và AI trong theme**. Theme giữ giao diện, thiết kế và hiển thị dữ liệu; Rank Math/SEO chuyên sâu, WooCommerce/đơn hàng/thanh toán, inventory/booking và nghiệp vụ lâu dài thuộc plugin/provider. Điểm hay ngoài phạm vi là đề xuất để owner cân nhắc, không tự gộp vào theme.
- **Vai trò:** Codex nghiên cứu/đặc tả/roadmap; Claude Code lập trình và kiểm thử. Áp dụng từ yêu cầu owner ngày 03-11 cho đến khi owner đổi phân công.
- **Form tính giá và booking:** yêu cầu mới đã được nghiên cứu ở [FORMS-BOOKING-ROADMAP.md](FORMS-BOOKING-ROADMAP.md): Form native báo giá trước khi gửi; block tìm tuyến/chuyến/xe, lọc kết quả và tóm tắt; adapter plugin/provider cho giá/tồn chỗ/đặt thật. Không coi gửi mail thành đặt thành công.
- Các giai đoạn chưa triển khai: G-57 nền Form → G-58 calculator/quote → G-59 AI cấu hình; G-60 khảo sát/chốt backend → G-61 block tìm/hiện kết quả → G-62 adapter booking → G-63 nghiệm thu. G-60 là cổng chọn backend theo loại dịch vụ, không mặc định Woo Bookings đáp ứng bán ghế theo tuyến.
- Đọc [SOURCE-AUDIT.md](SOURCE-AUDIT.md) trước khi mở rộng nền đang có; theo sổ nhận việc và TASKS.md của workspace, không nhận đè việc Claude đang làm. Các ưu tiên shop cũ phía dưới là lịch sử/đề xuất tham khảo, không thay phạm vi mới này.

## Kế hoạch thực thi 11-11-2026: tuỳ chọn khối, cổng AI, hook, Customizer (G-129 → G-151)

**Dành cho agent lập trình: đọc hết mục này là đủ để bắt đầu.** Chỉ mở thêm tài liệu khi phiếu việc ghi rõ. Mọi mục là đề xuất đã được owner yêu cầu ghi vào roadmap; chưa mục nào được lập trình. Số dòng trong mục này đúng tại commit `c9df12e`; nếu lệch thì grep theo tên hàm.

### A. Vì sao làm

- Owner muốn BIGSPY vượt Astra, Flatsome, GeneratePress, Hello Elementor, Neve, OceanWP và Elementor. Mọi khối, kể cả khối phụ nhỏ, phải có thật nhiều tuỳ chọn, gồm cả **kiểu dáng** (bố cục, hình thức) và **phong cách** (skin). AI chỉ là công cụ hỗ trợ; theme phải là thứ khó thay thế.
- Đo trên site đối chiếu (G-140, G-146): BIGSPY có 83 khối, hơn Elementor miễn phí (khoảng 30 widget) và Flatsome (60 phần tử). Nhưng tuỳ chọn riêng của khối nhỏ kém Elementor 3–6 lần (Tabs 8 so với 42, Testimonial 8 so với 53, Divider 10 so với 41); lớp dùng chung khoảng 60 control so với 176. Elementor đạt số đó vì **mỗi phần bên trong widget có một nhóm kiểu riêng** (chữ, màu thường/hover/đang chọn, viền, padding).
- Astra (70 tệp ability) và Neve (13 ability) đã cho AI bên ngoài điều khiển theme qua Abilities API và MCP; BIGSPY chưa có.
- BIGSPY chỉ có 15 `do_action` và 35 `apply_filters`; GeneratePress có 127 và 223.
- Luận điểm sản phẩm: AI Designer chỉ được đặt thuộc tính của khối, nên **mỗi tuỳ chọn mới là một năng lực mới của AI** mà người dùng vẫn sửa tay được.
- Số liệu đầy đủ: [COMPETITIVE-STRATEGY-2026-11.md](COMPETITIVE-STRATEGY-2026-11.md). Danh sách kiểu dáng và tuỳ chọn cho từng khối: [BLOCK-OPTIONS-SPEC.md](BLOCK-OPTIONS-SPEC.md) mục 4.

### B. Bản đồ mã cần biết

Đường dẫn tính từ `bigspy-ai-theme-wp/`.

| Thứ | Ở đâu |
| --- | --- |
| Thuộc tính khối | `blocks/<slug>/block.json` (khối không khai báo thuộc tính trong JS) |
| Đầu ra front | `blocks/<slug>/render.php`; khối listing render ở `inc/listing/blocks.php` |
| Hàm PHP cho render | `bst_choice()` `inc/helpers.php:644`; `bst_block_num()` `inc/blocks.php:307`; `bst_block_color()` `:311`; `bst_style_vars()` `:324`; `bst_icon()` `inc/helpers.php:549` |
| Editor của khối cũ | `assets/js/blocks-editor.js` (không đọc cả tệp): `register()` dòng 34, `styleVars()` 38, `sliderChoice()` 1147; ví dụ Tabs ở dòng 1413–1462 |
| Editor khối khác | `blocks-extra.js`, `blocks-more.js`, `blocks-motion.js`, `blocks-listing.js` |
| Bộ điều khiển dùng chung đã có | `assets/js/editor-helpers.js`, xuất `window.bstEditor` (dòng 174): `Icon`, `IconPicker`, `MediaPicker`, `ColorPanel`, `Select`, `Range`, `Toggle`, `Text`, `binder` |
| Đăng ký script editor | `bst_register_blocks()` `inc/blocks.php:34` |
| Lớp Design (`bsDesign`) | `inc/block-design.php` (`bst_design_rules()` dòng 67, `bst_design_hover_rules()` 197, `bst_design_css()` 228); `assets/js/block-design.js` (panel Design dòng 275, Typography 414, đưa vào tab Kiểu bằng `InspectorControls group: 'styles'` dòng 490, Copy/Paste design dòng 316–326) |
| Thuộc tính chung (ẩn theo thiết bị, hiệu ứng) | `bst_global_block_attributes()` `inc/blocks.php:119` |
| Preset dạng class đã có | `inc/block-presets.php`: `bst_block_presets()` dòng 17 trả `khối => nhóm => (nhãn, loại trừ nhau, tuỳ chọn(id => (nhãn, css)))`; lưu vào thuộc tính `bsPresets`, in class `bsp-<id>`; đã có cho 21 khối |
| CSS khối | `assets/css/blocks.css` (chỉ grep; Tabs bắt đầu dòng 807). Sửa xong chạy `python tools/split-blocks-css.py` |
| JS front | `assets/js/blocks.js` (`initTabs()` dòng 250); khối chuyển động ở `motion-*.js` |
| Tìm kiếm trực tiếp | `bst_live_search_ajax()` `inc/header-extras.php:172`, action `bst_live_search`; tài nguyên ở `bst_live_search_assets()` `:155` |
| Customizer | trường ở `inc/customizer/fields.php`; đăng ký và nạp script ở `inc/customizer/customizer.php` (bảng: dòng 167, khung xem trước: dòng 199); `assets/js/customizer-controls.js` (593 dòng), `assets/js/customizer-preview.js` (19 dòng) |
| Kiến thức AI về khối | `ai-designer/knowledge/blocks.php` (mỗi khối một dòng mảng), `knowledge/theme.md` |
| Công cụ AI | `bst_aid_tools()` `ai-designer/includes/tools.php:15`, `bst_aid_run_tool()` `:156`; quyền ở `permissions.php`: `bst_aid_tool_policy()` `:36`, `bst_aid_permission_gate()` `:102` |
| Auto Detect | `inc/auto-detect/` (h1, keywords, links, media, security); test `tools/tests/auto-detect-integration.php` (mảng `$tests` dòng 26) |

### C. Quy tắc bắt buộc cho mọi phiếu

1. **Nội dung cũ không đổi giao diện.** Thuộc tính mới mặc định rỗng/0/false và khi đó không in CSS, không thêm class. Không đổi tên, không xoá, không đổi mặc định của thuộc tính cũ. Trước khi sửa một khối, lưu HTML render của khối đó với thuộc tính cũ; sau khi sửa so lại phải giống hệt.
2. **Kiểu dáng là class, thông số là biến CSS.** Kiểu: thêm giá trị vào danh sách `bst_choice()` và class `bs-<khối>--<kiểu>`. Thông số: biến `--bs-<khối>-<phần>-<thuộc tính>` in bằng `bst_style_vars()` trên thẻ bọc; trong CSS dùng `var(--…, giá trị hiện tại)`.
3. **Đặt tên thuộc tính:** `<phần><ThuộcTính><TrạngThái><ThiếtBị>`, ví dụ `navBg`, `navBgHover`, `tabColorActive`, `navSizeTablet`, `navSizeMobile`. Ngưỡng thiết bị 849px và 549px.
4. **Mọi giá trị qua** `bst_choice`, `bst_block_num`, `bst_block_color`; đầu ra escape.
5. **Editor phải giống front:** thêm cùng class và biến vào `useBlockProps` của khối.
6. **Bố trí panel:** nội dung, dữ liệu, hành vi ở tab Cài đặt (`InspectorControls` mặc định); kiểu dáng và từng phần ở tab Kiểu (`group: 'styles'`). Panel phụ đóng sẵn. Tuỳ chọn phụ thuộc chỉ hiện khi tuỳ chọn cha bật.
7. **Không thêm lại** margin, padding, nền, viền cho vỏ ngoài: lớp Design đã có.
8. Mỗi tuỳ chọn phải tạo khác biệt nhìn thấy được; không thêm cho đủ số.
9. JS front chỉ nạp khi trang dùng; không thêm thư viện; tôn trọng giảm chuyển động; dùng được bằng bàn phím.
10. Chuỗi giao diện: msgid tiếng Anh, thêm tiếng Việt bằng `python tools/add-strings.py strings.json`.
11. Cập nhật dòng của khối trong `ai-designer/knowledge/blocks.php` và `theme.md` khi thêm kiểu hoặc thuộc tính chính.
12. Không chép mã đối thủ (theme là GPLv2, Elementor là GPLv3, tài nguyên Flatsome có giấy phép riêng).

**Các bước chuẩn để thêm tuỳ chọn cho một khối:**

1. `block.json`: thêm thuộc tính với mặc định trung tính.
2. `render.php`: whitelist, thêm class kiểu và biến CSS.
3. `blocks.css`: thêm rule cho kiểu mới và dùng biến; chạy `python tools/split-blocks-css.py`.
4. Editor: thêm điều khiển (dùng kit của G-129) và cùng class/biến trong `useBlockProps`.
5. JS front nếu có hành vi mới.
6. Chuỗi dịch, kiến thức AI.
7. Kiểm tra: `python tools/lint.py`, `node tools/check-block-previews.js`, `node tools/features-check.cjs`, `node tools/check-block-options.js` (có sau G-129); mở `node tools/playground-page.cjs` và xem khối trong editor lẫn front ở 1280, 768, 375px.
8. Ghi `docs/PENDING-RELEASE.md`, commit theo từng khối, push `feature/ai-designer`.

Trước khi test chạy `. ./env/env.ps1` ở thư mục làm việc. Máy không có PHP: lint bằng `python tools/lint.py`.

### D. Thứ tự và phiếu việc

Thứ tự: **G-129 → G-130 → G-131 → G-133-a (Tabs) → G-134-a (Tìm kiếm) → G-141 → G-147** → các nhóm khối còn lại (G-132, G-133-b, G-134-b, G-135 → G-138) xen với G-142, G-143, G-148, G-149 → G-139 → G-145. **G-151 (nhãn sửa nhanh trong Customizer) độc lập với các phiếu khác, làm được bất cứ lúc nào; đề xuất làm ngay sau G-134-a.** Nên làm G-108 (per-page CSS so hash) trước vì `blocks.css` sẽ tăng. Mỗi phiếu là một phiên; phiếu nhóm khối chia mã con, mỗi phiên 2–4 khối.

#### G-129 · Mở rộng bộ điều khiển dùng chung

- **Mục tiêu:** có sẵn các điều khiển “theo phần” để mỗi khối chỉ cần vài dòng là có nhóm chữ, màu theo trạng thái, khung.
- **Tệp:** `assets/js/editor-helpers.js` (thêm vào `window.bstEditor`, không tạo tệp JS mới trừ khi tệp vượt khoảng 600 dòng, khi đó tạo `assets/js/editor-kit.js` và đăng ký trong `bst_register_blocks()` làm phụ thuộc của các script editor); `inc/blocks.php` (hàm PHP); `tools/check-block-options.js` (mới).
- **Thêm vào `bstEditor`:**
  - `Resp`: một giá trị theo máy tính/tablet/điện thoại, ghi vào ba thuộc tính `key`, `keyTablet`, `keyMobile`; nút chuyển thiết bị gọi `wp.data.dispatch('core/editor').setDeviceType` nếu có.
  - `PartTypo`: cỡ (dùng `Resp`), độ đậm, line-height, letter-spacing, transform, màu, màu hover cho một tiền tố thuộc tính.
  - `ColorStates`: bảng màu nền/chữ/viền với các tab Thường/Hover/Đang chọn, dựng trên `ColorPanel`.
  - `PartBox`: padding, bo góc, độ dày và màu viền, mức bóng 0–5.
  - `StylePicker`: lưới nút có hình SVG nhỏ và nhãn, thay cho `sliderChoice` khi chọn kiểu dáng.
  - `LinkControl`: URL, tab mới, rel, aria-label.
  - `StyleTab`: bọc `InspectorControls` với `group: 'styles'`.
- **PHP:** `bst_part_vars( $a, $block, $part, $keys )` trả mảng biến CSS cho một phần (đọc `<part>Size`, `<part>SizeTablet`, `<part>Color`…), để nối vào `bst_style_vars()`. Biến theo thiết bị in dạng `--…-fs`, `--…-fs-t`, `--…-fs-m`; CSS dùng trong media query.
- **`tools/check-block-options.js`:** với mỗi `blocks/*/block.json`, cảnh báo thuộc tính không xuất hiện trong `render.php` của khối (hoặc `inc/listing/blocks.php`) và không xuất hiện trong các tệp JS editor. Chỉ in cảnh báo, mã thoát 0.
- **Áp mẫu:** chuyển khối `divider` (`blocks/divider/`, 10 thuộc tính) sang dùng kit và thêm các tuỳ chọn của nó ở BLOCK-OPTIONS-SPEC mục G-132.
- **Nghiệm thu:** Divider cũ render giống hệt; các kiểu mới hiện đúng ở editor và front; script kiểm tra chạy được; các test ở mục C đạt.

#### G-130 · Mở rộng lớp Design dùng chung

- **Tệp:** `inc/block-design.php`, `assets/js/block-design.js`; test trong `tools/features-check.cjs`.
- **Thêm khoá vào `bsDesign`** (xử lý trong `bst_design_rules()` và panel dòng 275):
  - viền từng cạnh, bo góc từng góc;
  - nền gradient (2–3 màu, góc, linear/radial), ảnh nền (vị trí, cỡ, lặp) kèm lớp phủ;
  - bóng tuỳ chỉnh (x, y, blur, spread, màu) và bóng trong;
  - opacity, bộ lọc (blur, brightness, grayscale), backdrop blur;
  - transform tĩnh và khi hover (xoay, dịch, nghiêng); thời gian chuyển của hover (hiện cố định 0,3s ở dòng 167);
  - mặt nạ hình (tròn, blob, vòm);
  - màu, viền, bo góc theo tablet/điện thoại;
  - chiều cao tối thiểu theo thiết bị, tỉ lệ khung;
  - `order` và `align-self` theo thiết bị, sticky kèm khoảng cách trên;
  - Overflow (visible/hidden/clip).
- **Chú ý tương thích:** dòng 156 hiện in `border-radius` kèm `overflow:hidden`. Giữ hành vi đó cho `bsDesign` cũ; chỉ khi người dùng chọn Overflow mới in giá trị khác.
- **CSS riêng theo khối và thuộc tính HTML tuỳ chỉnh:** thêm vào panel Nâng cao; CSS được gắn phạm vi vào class `bsd-…` của khối và lọc như khối Code; thuộc tính chỉ nhận `data-*` và `aria-*`.
- **Copy/Paste design** (dòng 316–326) hiện chỉ sao chép `bsDesign`: mở rộng để sao chép thêm thuộc tính kiểu khi hai khối cùng loại; thêm nút Đặt lại.
- **Nghiệm thu:** `bsDesign` cũ sinh CSS giống hệt trước (thêm ca test); mỗi khoá mới có ca test CSS; editor xem trước đúng.

#### G-131 · Kiểu dựng sẵn bằng hình

- **Hiện có:** preset dạng class cho 21 khối trong `inc/block-presets.php`, chọn bằng chữ ở panel “Style presets”.
- **Làm:**
  - thêm loại preset thứ hai “bộ thuộc tính”: `bst_block_attr_presets()` trả `khối => [ id => (nhãn, hình SVG, attrs) ]`; bấm là `setAttributes(attrs)`;
  - panel hiện lưới hình thu nhỏ (dùng `StylePicker`), đặt đầu tab Kiểu;
  - người dùng lưu kiểu hiện tại thành preset của site (option `bst_user_block_presets`, có xoá, giới hạn 50 mỗi khối, chỉ lưu thuộc tính có trong `block.json`);
  - `block_catalog` của AI Designer trả danh sách preset của khối.
- **Phạm vi phiên này:** hạ tầng và preset cho Divider, Button. Các khối khác thêm preset ở phiếu của chính khối đó (4–8 preset mỗi khối).
- **Nghiệm thu:** áp preset rồi hoàn tác được bằng Ctrl+Z; preset người dùng lưu và xoá được; khối không có preset không hiện panel.

#### G-133-a · Tabs (ví dụ owner nêu)

- **Tệp:** `blocks/tabs/`, `blocks/tab/`, editor `blocks-editor.js:1413-1462`, CSS từ `blocks.css:807`, JS `blocks.js:250` (`initTabs`).
- **Hiện có:** `style` (underline, pills, boxed, vertical), `align` (left, center), `event`, `stretch`, `navSize`, `activeColor`, `activeText`, `fade`; khối con có `title`, `icon`.
- **Thêm vào `tabs`:**

| Thuộc tính | Giá trị | Ghi chú |
| --- | --- | --- |
| `style` (thêm giá trị) | `underline-thick`, `line-top`, `folder`, `segmented`, `outline`, `cards`, `icon-top` | giữ 4 giá trị cũ |
| `navPosition` | `''`, `top`, `bottom`, `left`, `right` | rỗng = hành vi cũ (`vertical` vẫn là trái) |
| `align` (thêm) | `right`, `justify` | |
| `sideWidth` | px, 0 = mặc định | bề rộng cột tab khi trái/phải |
| `mobile` | `''`, `scroll`, `accordion`, `dropdown`, `stack` | rỗng = hành vi cũ |
| `defaultTab` | số, 1 | tab mở sẵn |
| `deepLink`, `remember` | boolean | mở theo `#id` của tab; nhớ tab bằng `sessionStorage` |
| `autoplay`, `pauseHover` | giây (0 = tắt), boolean | dừng khi focus và khi giảm chuyển động |
| `transition`, `transitionMs` | `''`, `slide`, `zoom`, `none`; ms | rỗng = theo `fade` cũ |
| `sticky`, `stickyTop` | boolean, px | thanh tab dính |
| `navGap`, `navPadX`, `navPadY`, `navRadius`, `navBg`, `navBorderColor`, `navBorderWidth` | số/màu | thanh tab |
| `tabColor`, `tabColorHover`, `tabBg`, `tabBgHover`, `tabBgActive`, `indicatorColor`, `indicatorSize` | màu/số | `activeColor`, `activeText` cũ giữ nguyên nghĩa |
| `navSizeTablet`, `navSizeMobile`, `navWeight`, `navTransform`, `navLetterSpacing` | | chữ thanh tab |
| `iconPosition`, `iconSize`, `iconOnlyMobile` | `left`/`right`/`top`, px, boolean | |
| `panelBg`, `panelBorderColor`, `panelBorderWidth`, `panelRadius`, `panelPadding`, `panelPaddingMobile`, `panelMinHeight`, `equalHeight` | | vùng nội dung |

- **Thêm vào `tab`:** `subtitle`, `badge`, `badgeColor`, `imageId`/`imageUrl` (thay icon), `anchor`, `color`, `disabled`. `render.php` của Tabs đọc các thuộc tính này từ `innerBlocks` như đang đọc `title` và `icon`.
- **Trợ năng:** giữ `role="tablist"`, phím mũi tên; chế độ accordion trên di động dùng nút có `aria-expanded`; chế độ dropdown dùng `<select>` có nhãn.
- **Preset:** 6 preset (ví dụ Segmented tối, Thư mục, Viên thuốc mềm, Gạch chân dày, Thẻ bóng, Dọc trái).
- **Nghiệm thu:** trang có Tabs cũ không đổi; từng kiểu, từng vị trí và từng chế độ di động chạy ở editor và front; bàn phím dùng được; test ở mục C đạt.

#### G-134-a · Tìm kiếm (ví dụ owner nêu)

- **Hiện có:** chỉ `listing-search` (cần chế độ BIGSPY AI List), render ở `bst_render_listing_search()` `inc/listing/blocks.php:189`, CSS `assets/css/listing.css:89-93` với biến `--bst-ls-radius`, `--bst-ls-h`, `--bst-ls-btn`, `--bst-ls-btn-text`, `--bst-ls-border`; editor trong `blocks-listing.js`. Màu hiện lọc bằng `sanitize_hex_color`.
- **Làm:**
  - tạo `inc/search-block.php` với `bst_render_search_form( $a, $context )` dùng chung; `bst_render_listing_search()` gọi hàm này với `post_type=product`; giữ nguyên tên biến CSS cũ và HTML khi không đặt tuỳ chọn mới;
  - tạo khối mới `bst/search` cho site thường: `python tools/add-block.py search "Search" --prefix bs-search`, thêm vào `bst_block_names()` và hình xem trước trong `block-previews.js`;
  - gợi ý tức thời dùng lại action `bst_live_search`.
- **Thuộc tính cho cả hai khối** (mặc định rỗng = giao diện hiện tại):

| Nhóm | Thuộc tính |
| --- | --- |
| Kiểu | `style`: `''`, `pill`, `boxed`, `underline`, `split`, `icon`, `hero`, `overlay` |
| Icon và nút | `icon`: `''`, `left`, `right`; `buttonMode`: `text`, `icon`, `both`; `buttonPosition`: `inside`, `outside`, `below-mobile`; `buttonRadius` |
| Kích thước | `heightMobile`, `fontSize`, `padX`, `maxWidthTablet`, `maxWidthMobile`, `alignmentMobile` |
| Màu | `inputBg`, `inputColor`, `placeholderColor`, `borderWidth`, `focusColor`, `buttonHoverColor`, `buttonHoverTextColor`, `shadow` (0–5), `shadowFocus` |
| Gợi ý tức thời | `live`, `liveCount`, `liveImage`, `livePrice`, `liveCategory`, `minChars` |
| Phạm vi | `postTypes` (chỉ `bst/search`), `category`, `showCategorySelect` |
| Tiện ích | `chips` (mỗi dòng một từ khoá), `chipsLabel`, `placeholders` (nhiều dòng, xoay vòng), `shortcut` (phím `/`), `ariaLabel` |

- **Nghiệm thu:** `listing-search` cũ render giống hệt; `bst/search` chạy ở site thường; gợi ý tức thời dùng được bằng bàn phím và đóng bằng Escape; kiểu `overlay` giữ focus bên trong; `node tools/woo-check.cjs` đạt nếu chạy được.

#### G-141 · Abilities API và MCP

- **Mục tiêu:** AI bên ngoài điều khiển theme qua đúng lớp quyền, luật block và hoàn tác của AI Designer.
- **Bước 1, xác minh (bắt buộc trước khi viết mã):** đọc tài liệu chính thức của WordPress về Abilities API và MCP adapter: phiên bản WordPress tối thiểu, chữ ký `wp_register_ability()`, `wp_register_ability_category()`, các hook `wp_abilities_api_init`, `wp_abilities_api_categories_init`, `mcp_adapter_init`. Các tên này thấy trong mã Astra 4.14.1, chưa đối chiếu tài liệu.
- **Tệp:** mới `ai-designer/includes/abilities.php`, nạp từ `ai-designer.php`; công tắc trong `inc/features.php` (một tệp trong `inc/features/`), mặc định tắt.
- **Thiết kế:** không viết logic mới; mỗi ability là một lớp bọc quanh công cụ trong `bst_aid_tools()` và gọi `bst_aid_run_tool()`. Theo kiểu gộp của Neve: ít ability, mỗi cái nhận schema. Đợt đầu: `site_info`, `list_content`, `get_post`, `block_catalog`, `section_recipes`, đọc và ghi cài đặt theme, `update_page`/`page_sections`.
- **Quyền:** `permission_callback` dùng đúng capability mà công cụ đang kiểm; công cụ mà `bst_aid_tool_policy()` yêu cầu duyệt thì ability trả lỗi “cần duyệt trong WordPress”, không tự chạy. Luật block trong `tools-design.php` vẫn áp dụng vì đi qua cùng hàm.
- **Điều kiện:** chỉ đăng ký khi `function_exists( 'wp_register_ability' )`; theme vẫn hỗ trợ WordPress 6.5.
- **Nghiệm thu:** thêm ca vào `tools/tests/designer-integration.php`: tắt tính năng thì không đăng ký gì; bật thì ability đọc trả đúng dữ liệu; ability ghi vi phạm luật block bị từ chối; người không đủ quyền bị từ chối.

#### G-147 · Hook cho nhà phát triển

- **Mục tiêu:** child theme và plugin móc được vào theme mà không sửa tệp.
- **Làm:** thêm `do_action` trước/sau header, footer, từng thành phần header, nội dung trang, vòng lặp bài, thẻ bài, thẻ sản phẩm; thêm filter chung trong render của khối: `bst_block_attributes` (trước khi render), `bst_block_classes`, `bst_block_html` (sau khi render, qua `render_block` cho tên bắt đầu bằng `bst/`); filter cho danh sách tuỳ chọn Customizer và danh sách kiểu của khối.
- **Tên:** tiền tố `bst_`, dạng `bst_before_<vùng>`, `bst_after_<vùng>`. Grep hook hiện có trước để không trùng.
- **Tài liệu:** `tools/list-hooks.py` sinh `docs/HOOKS.md` từ mã.
- **Nghiệm thu:** không ai móc vào thì HTML không đổi; test trong `tools/tests/features-integration.php` móc một action và một filter rồi kiểm đầu ra.

#### G-151 · Nhãn sửa nhanh trong Customizer (rê chuột vào phần tử, bấm là tới đúng mục)

- **Yêu cầu của owner (11-11-2026):** trong Customizer, ở bất kỳ trang nào, rê chuột vào một phần tử trên khung xem trước (logo, hàng header, giỏ hàng, sidebar, bố cục blog, footer, dòng bản quyền…) thì hiện nhãn tên phần tử; bấm nhãn là bảng bên trái mở đúng mục cài đặt, không phải lục menu. Menu cũ giữ nguyên, vì có những mục không có phần tử tương ứng trên trang. Các phần tử owner chụp chỉ là ví dụ; phải phủ mọi vùng theme in ra.
- **Flatsome làm thế nào** (xem trên site đối chiếu, Flatsome 3.20.10; chỉ ghi lại cơ chế, không chép mã):
  - Script riêng chỉ nạp trong khung xem trước chứa một bảng 42 mục, mỗi mục gồm: selector CSS, chữ trên nhãn, ID đích, loại đích (`control`, `section` hoặc `panel`), vị trí nhãn.
  - Rê chuột vào phần tử khớp selector thì hiện một nút nhỏ (nền xanh, chữ trắng 10px, mờ 50%, rõ 100% khi rê vào nút) và phần tử được viền mờ.
  - Bấm nút thì gọi `wp.customize.control(id).focus()`, `section(id).focus()` hoặc `panel(id).focus()` của cửa sổ cha.
  - Phần tử lồng nhau hiện nhiều nhãn cùng lúc (ví dụ Logo và Header Main).
  - 42 nhãn của Flatsome: Top Bar, Header Main, Header Bottom, Logo, Transparent Header, Dropdown Style, Header Cart, Header Account, Header Search, Header Social, Header Newsletter, Header Vertical Menu, Button 1, Button 2, Custom Content, Layout, Sidebar Widgets, Blog Layout, Blog Single Post layout, Featured Blog Posts, Catalog Layout, Catalog Sidebar Widgets, Shop Breadcrumbs, Product Layout, Product Gallery, Product Summary, Product Tabs, Product Upsells, Related Products, Product Sidebar Widgets, Share Icons, Cart Layout, Checkout Layout, Portfolio Layout, Payment Icons, Footer 1/2 Options, Footer 1/2 Widgets, Absolute Footer, Cookie Notice.
  - Ngoài ra Flatsome khai báo 66 “partial” của WordPress (selective refresh) để một số vùng tự vẽ lại không cần tải lại trang; đây là việc khác, không thuộc phiếu này.
- **BIGSPY hiện có:** `assets/js/customizer-preview.js` chỉ 19 dòng, cập nhật biến CSS; không có nhãn nào. Bảng bên trái đã có hàm nhảy tới mục: chip của header builder gọi `api.section(id).focus()` (`assets/js/customizer-controls.js:145-153`), ô tìm cài đặt gọi `control.focus()` (`:477`). Script nạp ở `inc/customizer/customizer.php:167` (bảng) và `:199` (khung xem trước).
- **Tệp sửa:** mới `inc/customizer/focus-map.php` (nạp từ `inc/customizer/customizer.php`); `assets/js/customizer-preview.js`; `assets/js/customizer-controls.js`; mới `assets/css/customizer-preview.css` (chỉ nạp ở `customize_preview_init`); `inc/header-builder.php:211`; các nơi in vùng (xem bảng dưới).
- **Thiết kế:**
  1. **Bảng ánh xạ ở PHP:** `bst_customize_focus_map()` trả danh sách `array( 'selector', 'type' => control|section|panel|url, 'id', 'label' )`, có `apply_filters( 'bst_customize_focus_map', … )` để child theme và plugin thêm mục. Nhãn lấy từ tiêu đề section/control đã đăng ký (`$wp_customize->get_section( $id )->title`) để không phải thêm chuỗi dịch; chỉ thêm chuỗi khi không có tiêu đề phù hợp. Mục có ID không tồn tại thì bỏ khỏi bảng.
  2. **Thuộc tính trên phần tử động:** khi `is_customize_preview()`, thêm `data-bst-edit="<type>:<id>"` vào thẻ bọc của phần tử sinh động: từng phần tử header ở `bst_header_element()` (`inc/header-builder.php:211`, đích `section:bst_he_<khoá>`; kiểm tra section đó có tồn tại, không thì dùng section chung như `bst_header_logo`), từng vùng widget (đích `section:sidebar-widgets-<id sidebar>`). Ngoài Customizer không in thuộc tính này.
  3. **Khung xem trước** (`customizer-preview.js`, không dùng jQuery plugin hay thư viện): lắng nghe `mouseover` uỷ quyền trên `document`; tìm phần tử gần nhất khớp bảng hoặc có `data-bst-edit`, cùng các phần tử cha cũng khớp; hiện **một thanh nhãn** bám góc trên trái của phần tử trong cùng, gồm các nút theo thứ tự từ ngoài vào trong (ví dụ `Header chính › Logo`); phần tử được viền bằng `outline` (không đổi kích thước). Thanh nhãn ẩn sau 300ms khi chuột rời cả phần tử lẫn thanh. Bấm nút gửi `wp.customize.preview.send( 'bst-focus', { type, id } )`.
  4. **Bảng bên trái** (`customizer-controls.js`): `api.previewer.bind( 'bst-focus', … )` rồi gọi `api[type](id).focus()`; với `control` thì thêm class nháy sáng như ô tìm kiếm đang làm (`bst-csearch-hit`, dòng 477–480). Loại `url` mở liên kết ở tab mới (dùng cho “Sửa nội dung trang”).
  5. **Chiều ngược lại (điểm làm tốt hơn Flatsome):** khi người dùng mở một section trong bảng, gửi `bst-highlight` sang khung xem trước để cuộn tới và nháy viền phần tử tương ứng. Nếu phần tử không có trên trang đang xem và mục có `preview_url` (trang shop, một sản phẩm, một bài viết, giỏ hàng, thanh toán) thì đổi `api.previewer.previewUrl` sang trang đó.
  6. **Công tắc:** nút “Nhãn sửa nhanh” trong bảng (cạnh ô tìm cài đặt), mặc định bật, nhớ bằng `localStorage`; khi tắt thì khung xem trước không hiện nhãn. Cần vì nhãn có thể che menu thả xuống khi người dùng muốn thử thao tác thật.
  7. **Không cản thao tác:** nhãn không chặn bấm vào liên kết, menu, giỏ hàng trong khung xem trước; `z-index` cao hơn header dính; trên bề rộng xem trước di động vẫn chạy.
  8. **Trợ năng:** nhãn là `<button>` có `aria-label` “Sửa: <tên>”; phím Tab tới được; Escape ẩn thanh nhãn.
- **Bảng ánh xạ tối thiểu** (agent phải grep selector và ID trước khi đưa vào; thêm mọi vùng khác theme in ra):

| Vùng trên trang | Selector hiện có | Đích |
| --- | --- | --- |
| Thanh thông báo | grep trong `inc/announcement.php` | section `bst_announcement` |
| Hàng header trên / chính / dưới | `.bs-header-row--top`, `--main`, `--bottom` | section `bst_header_rows`; nhãn phụ “Bố cục header” tới `bst_header_builder` |
| Header dính | `.bs-header__sticky` | section `bst_header_general` |
| 16 phần tử header (logo, menu chính, menu phụ, icon menu, tìm kiếm, ô tìm, tài khoản, giỏ hàng, nút 1–2, HTML 1–3, liên hệ, mạng xã hội, vạch ngăn) | `.bs-header-el--<khoá>` | section `bst_he_<khoá>`; dự phòng `bst_header_logo`, `bst_header_nav`, `bst_header_search`, `bst_header_buttons`, `bst_header_html`, `bst_contact` |
| Menu (các mục) | thẻ `nav` trong phần tử menu | panel `nav_menus` (nhãn phụ) |
| Mega menu, menu dọc | grep trong `inc/mega-menu.php` | section tương ứng nếu có, không thì `bst_header_nav` |
| Thanh tiêu đề trang, breadcrumb | `.bs-title-bar`, `.bs-breadcrumbs` | section `bst_title_bar` |
| Khung bố cục trang | `.bs-layout__content` | section `bst_layout_site` |
| Sidebar | `.bs-layout__sidebar` | section `bst_sidebar`; nhãn phụ “Widget” tới `sidebar-widgets-<id>` |
| Danh sách bài (blog, lưu trữ, tìm kiếm) | grep class bọc trong `index.php`, `inc/blog-options.php` | section `bst_blog_archive` |
| Bài viết đơn | grep trong `single.php`, `template-parts/content-single.php` | section `bst_blog_single` |
| Nội dung trang dựng bằng khối | vùng nội dung của `page.php` | loại `url`: liên kết sửa trang (`get_edit_post_link`) |
| Trang shop, lưới sản phẩm, bộ lọc | grep trong `inc/shop-options.php`, `woocommerce/` | section `bst_shop` |
| Thẻ sản phẩm, trang sản phẩm (ảnh, tóm tắt, tab, liên quan) | `.bs-product__*`, `.bs-product-part` | section `bst_product` |
| Chế độ catalog | nơi thay nút mua | section `bst_catalog` |
| Giỏ hàng, thanh toán | grep trong `inc/checkout-extras.php` | section `bst_checkout` |
| Dải kêu gọi hành động ở footer | `.bs-footer-cta` | section `bst_footer_cta` |
| Widget footer | `.bs-footer-widgets`, `.bs-footer-widgets__col` | section `bst_footer_widgets`; nhãn phụ tới `sidebar-widgets-<id>` |
| Dòng bản quyền | `.bs-footer-bottom` | section `bst_footer_bottom` |
| Nút liên hệ nổi | `.bs-fc` | section `bst_float` |
| Header/footer dựng bằng Theme Builder | thẻ bọc của layout | loại `url`: liên kết sửa layout đó |
| Vùng của chế độ BIGSPY AI List (thẻ listing, sidebar trang chủ) | grep trong `inc/listing/` | section cài đặt listing nếu có |

- **Kiểm thử:**
  - Thêm ca vào `tools/tests/admin-browser-check.cjs` (đọc cách tệp này mở trang quản trị trước): mở `customize.php`, rê chuột vào `.bs-header-el--logo` trong iframe, thấy nhãn, bấm, kiểm tra section đích đang mở; tắt công tắc thì không còn nhãn.
  - Ca PHP trong `tools/tests/features-integration.php`: mọi mục của `bst_customize_focus_map()` trỏ tới section/control/panel có thật; ngoài Customizer HTML không chứa `data-bst-edit`.
  - Thử tay trên Playground: trang chủ, blog, bài viết, shop, sản phẩm (`--woo`), giỏ hàng; bề rộng xem trước di động; menu thả xuống vẫn mở được khi nhãn bật.
- **Nghiệm thu:** mọi vùng trong bảng có nhãn và nhảy đúng mục; menu cũ của Customizer không đổi; HTML ngoài Customizer giống hệt trước; không có lỗi console; `python tools/lint.py` và `node tools/features-check.cjs` đạt.
- **Ngoài phạm vi:** vẽ lại từng vùng không tải trang (selective refresh) và sửa chữ trực tiếp trên khung xem trước; nếu owner muốn, mở mã riêng sau phiếu này.

#### Các phiếu nhóm khối: G-132, G-133-b, G-134-b, G-135, G-136, G-137, G-138

Danh sách kiểu dáng và tuỳ chọn của từng khối nằm ở BLOCK-OPTIONS-SPEC mục 4, đúng tiểu mục cùng mã. Cách làm cho mỗi khối theo “Các bước chuẩn” ở mục C. Lưu ý riêng:

- **G-132** (divider đã làm ở G-129): spacer, buttons, button, title, heading, text, star-rating, alert, progress, counter.
- **G-133-b:** accordion và accordion-item, table (soạn dạng lưới ô nhưng phải đọc được chuỗi `a | b` cũ), timeline, icon-list.
- **G-134-b:** nav-menu, social và share (chuyển sang danh sách lặp nhưng đọc được các thuộc tính mạng xã hội cũ), payment-icons, widget-area, shortcode, map, popup.
- **G-135:** icon-box, image-box, flip-box, testimonial, team-member, price-table, pricing-toggle, hotspot, before-after, audio, video, video-button, lottie, logos, image.
- **G-136:** row, column, section, banner, banner-grid, bento, gallery, expand-gallery. Section và Banner đã có 26–32 thuộc tính: grep trước, chỉ thêm phần thiếu.
- **G-137:** marquee, ticker, horizontal-scroll, stack-cards, scroll-story, hover-reveal, video-cards, zoom-hero, line-draw, animated-text, slider. Editor ở `blocks-motion.js`.
- **G-138:** posts, products, products-list, product-categories, category-grid, listing-info; ba khối listing lớn chỉ sắp lại panel. Không thêm wishlist/compare (thuộc G-40); cài đặt chung listing thuộc G-103.

Mức tối thiểu sau khi nâng cấp một khối: 4–6 kiểu khác nhau rõ rệt chọn bằng hình; căn chỉnh theo thiết bị; từng phần bên trong có màu (thường/hover), chữ, khoảng cách; 4–8 preset.

#### Các phiếu còn lại

- **G-142 · Kiểm tra thiết kế:** thêm mô-đun `inc/auto-detect/design.php` theo mẫu các mô-đun hiện có: tương phản chữ/nền, thứ tự heading, vùng bấm nhỏ trên di động, ảnh thiếu alt, ảnh đầu trang chưa ưu tiên tải. Đăng ký vào `$tests` của `tools/tests/auto-detect-integration.php`. AI Designer đọc qua công cụ `auto_detect_results` đã có.
- **G-143 · Khối còn thiếu:** grep trước xem theme đã có dạng khác chưa. Ứng viên: mục lục, breadcrumb, trích dẫn, kêu gọi hành động, bảng giá dạng thực đơn, ngăn kéo, danh sách phát video, thanh tiến độ đọc, hộp tác giả, điều hướng bài, nhãn (badge), khối xếp chồng, tải thêm bằng AJAX dùng chung cho lưới. Mỗi khối một phiên, tạo bằng `python tools/add-block.py`.
- **G-148 · Đa ngôn ngữ:** thử Polylang trên Playground; rà chuỗi trong Customizer, header builder, form, popup; thêm `wpml-config.xml`. Chỉ công bố plugin đã thử.
- **G-149 · Đối chiếu tuỳ chọn cấp theme:** phiên nghiên cứu, kết quả là danh sách việc; so tên nhóm Customizer của BIGSPY (`inc/customizer/fields.php`, khoảng 276 tuỳ chọn) với OceanWP, Astra, Flatsome trên site đối chiếu; chú ý trợ năng, hiệu năng, chế độ sáng/tối, thành phần header.
- **G-139 · Rà soát cuối:** `check-block-options.js` thành kiểm tra bắt buộc trong `tools/release.py`; đo `blocks.css` và JS editor trước/sau.
- **G-145 · Fixture so sánh:** cần owner đồng ý vì phải đổi theme đang chạy trên site đối chiếu.

### E. Prompt mẫu cho từng phiên

```text
Đọc AGENTS.md rồi làm G-129 theo mục "Kế hoạch thực thi 11-11-2026" trong repo/docs/roadmap-vi.md (đọc mục B, C và phiếu G-129). Báo ngắn bằng tiếng Việt.
```

Đổi mã việc cho các phiên sau. Với phiếu nhóm khối, ghi rõ khối, ví dụ “G-135-a: icon-box, image-box, flip-box”.

## Đợt tối ưu sau rà soát mã nguồn 10-11-2026 (G-107 → G-124)

Mã G-124 (cổng cú pháp) ban đầu ghi là G-106; đổi vì G-106 đã dùng cho việc Icon List (1c7decc). Sổ việc và trạng thái nằm ở `TASKS.md` thư mục làm việc ngoài repo.

Rà soát ở bản 1.86.1, nhánh `main` tại `c4d154b`. Mỗi mục là một phiên làm việc riêng theo AGENTS.md; làm theo thứ tự ưu tiên, không gộp. **Cách làm chi tiết cho từng mã (tệp, đoạn mã hiện tại, từng bước sửa, ca kiểm thử, tiêu chí nghiệm thu) nằm ở [OPTIMIZATION-SPEC-2026-11.md](OPTIMIZATION-SPEC-2026-11.md); agent lập trình đọc đúng mục của mã đang làm trước khi sửa.** Đây là đề xuất chờ owner duyệt, chưa mục nào được triển khai.

**Phạm vi đã xem:** bootstrap, nạp tài nguyên, `inc/performance.php`, updater, Form native, tìm kiếm trực tiếp, AJAX sản phẩm, Quick View, khóa API và vòng lặp tác vụ của AI Designer, công cụ build/phát hành/test, tài liệu. `php -l` toàn bộ tệp PHP hiện đạt trên PHP 8.3.
**Đợt 2 (cùng ngày)** xem thêm `render.php` của block, script editor, quyền và luật block của AI Designer, lớp tương thích Flatsome, WooCommerce và trợ năng; kết quả ở mục “Đợt rà soát 2”. Các tệp lớn (`blocks-editor.js`, `blocks.css`, `tools-design.php`) chỉ được quét theo mẫu, không đọc từng dòng. Không chạy được bộ test Playground trong môi trường rà soát (xem G-112), nên các mục dưới đây dựa trên đọc mã, chưa tái hiện trên site thật trừ khi ghi rõ.

### P0 — Chặn lỗi phát hành

**G-124 · Cổng cú pháp trước khi đóng gói.** Hai bản liên tiếp (1.85.0 và 1.86.0) làm vỡ toàn bộ trang quản trị vì dấu nháy chưa escape trong `inc/welcome/data.php`; updater đẩy bản lỗi đến mọi site. `tools/release.py` và `tools/build-theme.py` hiện không kiểm tra cú pháp.
- `release.py check()`: chạy `php -l` cho mọi tệp `.php` của theme, `node --check` cho `assets/js/*.js` và `ai-designer/assets/*.js`, `json.loads` cho mọi `block.json`/`theme.json`; có lỗi thì báo `PROBLEM` và không cho `--version`. Thiếu `php` trên máy cũng là `PROBLEM`, không bỏ qua im lặng.
- `build-theme.py`: gọi cùng hàm kiểm tra trước khi ghi ZIP.
- Sửa luôn `release.py:45`: chuỗi đang dò là “Chưa có thay đổi sản phẩm mới” nhưng PENDING-RELEASE.md hiện ghi “Không có thay đổi đang chờ”, nên ghi chú không bao giờ in.
- Nghiệm thu: chèn thử một dấu nháy lỗi vào `data.php` → `python tools/release.py` trả mã khác 0 và nêu tên tệp; gỡ ra → `Ready.`.
- Mở rộng cần owner duyệt riêng: workflow GitHub Actions chạy `php -l` trên PHP 7.4 và 8.3 cho mỗi push (theme khai báo `Requires PHP: 7.4` nhưng hiện chỉ được lint trên 8.3).

### P1 — Bảo mật và độ tin cậy

**G-107 · Form native: siết chống spam** (`inc/form-native.php:288-303`).
- Kiểm tra thời gian điền bị bỏ qua khi request không gửi `bst_form_ts` (`$ts && …`). Bắt buộc có `ts` kèm chữ ký HMAC (`wp_salt`), không gắn với người dùng để vẫn chạy được với cache trang; thiếu hoặc sai chữ ký thì coi là spam.
- Bộ đếm 5 lượt/phút tăng trước khi kiểm tra dữ liệu, nên khách nhập sai vài lần sẽ nhận thông báo spam. Chỉ tính lượt đã qua kiểm tra dữ liệu.
- Giới hạn theo `REMOTE_ADDR`: site sau proxy/CDN có thể thấy mọi khách chung một IP, khi đó cả site chỉ gửi được 5 form/phút. Thêm filter `bst_form_client_ip` và ghi tài liệu; không tự tin `X-Forwarded-For`.
- Mail 2 gửi đến địa chỉ do khách nhập kèm nội dung khách nhập, có thể bị lợi dụng để gửi thư rác cho bên thứ ba. Thêm giới hạn theo từng địa chỉ nhận (đề xuất 3 thư/giờ).
- `redirectPass` đưa mọi giá trị form lên URL (`:361-363`). Bỏ trường textarea/file và cho chọn trường được chuyển.
- Kiểm tra: mở rộng `tools/tests/features-integration.php` cho từng trường hợp trên; form hợp lệ trên trang có cache vẫn gửi được.

**G-108 · Per-page CSS có thể tự tắt sau khi cập nhật** (`inc/performance.php:72-77`). Tính năng chỉ bật khi `filemtime(segments.json) >= filemtime(blocks.css)`. ZIP phát hành xếp `blocks/segments.json` trước `blocks.css` (`build-theme.py` sắp theo đường dẫn) và WordPress giải nén không giữ mtime, nên nếu hai tệp rơi vào hai giây khác nhau thì site âm thầm quay về nạp cả `blocks.css` (~195 KB) cho đến lần cập nhật sau. Rủi ro suy ra từ mã, chưa quan sát trên site thật.
- `tools/split-blocks-css.py` ghi hash của `blocks.css` vào một tệp riêng cạnh `segments.json` (không thêm khóa vào `families.json` vì runtime lặp qua toàn bộ khóa của tệp này).
- Runtime so hash thay cho mtime; lưu kết quả vào transient gắn `BST_VERSION` để không băm tệp ở mỗi request.
- `release.py`: so hash thay cho so thời điểm commit; `build-theme.py` assert hash khớp trong ZIP.
- Thêm một dòng trạng thái trong bảng kiểm tra của Theme panel (`inc/admin/theme-panel.php:366`) cho biết per-page CSS đang hoạt động.

**G-109 · Điểm cuối công khai: giảm tải truy vấn.**
- Tìm kiếm trực tiếp (`inc/header-extras.php:171-292`): mỗi lần gõ gọi `admin-ajax.php` và chạy tới 4 truy vấn, gồm `_sku LIKE` trên postmeta, không cache. Cache kết quả theo từ khóa + phạm vi trong transient ngắn (đề xuất 60 giây); xóa khi lưu bài/sản phẩm là không cần.
- `strlen( $term ) < 2` đếm byte, một ký tự tiếng Việt có dấu đã vượt ngưỡng; đổi sang `mb_strlen`.
- `assets/js/main.js:289-292`: tệp không dùng `AbortController`, nên request cũ vẫn chạy hết khi người dùng gõ tiếp; hủy request trước khi gửi request mới.
- `bst_products_ajax` (`inc/woocommerce-elements.php:271-284`): tùy chọn đã được whitelist; cân nhắc cache ngắn cho các kiểu không ngẫu nhiên.
- Kiểm tra: `node tools/woo-check.cjs` và thao tác tìm kiếm trên Playground.

**G-110 · Updater khai báo yêu cầu hệ thống** (`inc/updater.php:74`). Phản hồi cập nhật thiếu `requires` và `requires_php`, nên WordPress không chặn được bản không tương thích. Đọc hai giá trị từ release (ví dụ từ `readme.txt`/`style.css` trong gói hoặc một dòng quy ước trong ghi chú Release) và trả về trong `response` lẫn `themes_api`. Đồng thời xác minh rồi cập nhật `Tested up to: 6.8` trong `style.css`. Kiểm tra: `node tools/updater-playground-check.cjs`.

### P2 — Hiệu năng front-end

**G-111 · Giảm dung lượng tài nguyên front.** Hiện không có bước nén: `main.css` 57 KB, `woocommerce.css` 43 KB, `blocks.js` 36 KB, `main.js` 17 KB được phát nguyên bản; `blocks.js` nạp trên mọi trang kể cả trang không có block cần JS (`inc/assets.php:31-32`). Nối tiếp G-41 (đo tài nguyên): đo trước, làm sau.
- Bước 1, ít rủi ro: nơi ghi tệp CSS theo trang (`inc/performance.php:136-147`) bỏ chú thích và khoảng trắng thừa trước khi ghi; giữ nguyên thứ tự rule.
- Bước 2: tách phần slider/counter/tabs… trong `blocks.js` thành tệp theo tính năng và enqueue từ `render.php` như `motion-*.js` đang làm. Tách từng phần một, không tách cả tệp trong một phiên.
- Bước 3, cần owner duyệt vì thêm công cụ build: sinh `.min.css`/`.min.js` lúc đóng gói, `SCRIPT_DEBUG` dùng tệp gốc.
- Nghiệm thu: bảng số liệu trước/sau (byte truyền tải, số request) trên trang chủ, trang shop, một trang block; không lệch giao diện ở `tools/tests/browser-check.cjs`.

### P2 — Chất lượng và công cụ

**G-112 · Chạy test được trên mọi máy.** Tám runner trong `tools/` và `tools/tests/` mặc định trỏ đến `C:/Users/DELL/...`; repo không có `package.json`. Agent chạy trên máy khác hoặc trên cloud không chạy được test nào (đợt rà soát này cũng vậy). Đây là mục “Đề xuất chưa triển khai” trong DEV-RULES.md.
- Thêm `package.json` ở gốc repo với `devDependencies` ghim phiên bản (`@wp-playground/cli`, `playwright`); runner `require` theo cách thường, vẫn giữ `BST_PLAYGROUND_MODULES`/`BST_PLAYWRIGHT`/`BST_CHROME` để ghi đè.
- Thêm tùy chọn số worker (mặc định tuần tự) như DEV-RULES đã nêu.
- Xóa hoặc sửa `tools/build-translations.js` (CODEMAP ghi là đã lỗi thời).
- Nghiệm thu: clone mới → `npm ci` → `node tools/playground-check.cjs` chạy không cần sửa đường dẫn.

**G-113 · Chuỗi giao diện viết cứng bằng tiếng Việt.** Các chuỗi sau không qua hàm dịch nên site tiếng Anh vẫn hiện tiếng Việt: `inc/quick-view.php` (nhãn Customizer, “Xem nhanh”, “Xem chi tiết sản phẩm”, thông báo lỗi), `assets/js/quick-view.js` (“Đóng xem nhanh”, “Đang tải sản phẩm…”), `inc/product-options.php:8-11, 44-45`, `inc/admin/dashboard.php:114` (msgid tiếng Việt). Đổi sang msgid tiếng Anh và thêm bản dịch bằng `python tools/add-strings.py`. Rà thêm cùng kiểu trong `inc/customizer/controls.php`, `inc/header-*.php`, `inc/mega-menu.php`, `inc/meta-boxes.php`, `inc/listing/meta-box.php`, `blocks/payment-icons/render.php` (grep ra chữ có dấu, chưa xác nhận từng dòng). Không dịch lại toàn bộ catalog.

**G-114 · Dọn tài liệu điều hướng.** Việc nội bộ, không cần ghi PENDING-RELEASE.
- Mục “Bối cảnh roadmap cũ” vẫn ghi mốc 1.18.1; chuyển phần lịch sử sang một tệp lưu trữ để roadmap chỉ còn việc đang mở.
- `TASKS.md` và `HANDOFF.md` ở gốc repo rỗng (0 byte) trong khi roadmap yêu cầu agent theo `TASKS.md`; hoặc dùng thật, hoặc bỏ tham chiếu.
- Gom `HANDOFF-1.73.md`, `VERIFICATION-1.73*.md`, `VERIFICATION-1.74.md` vào thư mục lưu trữ.
- Các mã G-87 → G-100, G-102 → G-104 không xuất hiện trong tài liệu nào; ghi một bảng tra mã → trạng thái để agent sau không dùng trùng.

### Đợt rà soát 2 — block, editor, AI Designer, Flatsome, WooCommerce, trợ năng

**Không thấy vấn đề:** đầu ra của 80 `render.php` đều qua hàm escape hoặc whitelist; các script editor dùng `apiVersion` 3, không `ServerSideRender`, không API `__experimental`; mọi công cụ ghi của AI Designer kiểm tra quyền theo từng bài, và luật block trong `tools-design.php:427-441` khớp AGENTS.md ở cả ba đường ghi (`create_page`, `update_page`, `page_sections`).

**G-115 · P1 · Block Lottie nạp script từ unpkg.com** (`blocks/lottie/render.php:15`). Trình phát tải từ CDN bên thứ ba, không có SRI: CDN hỏng thì block hỏng, CDN bị chiếm thì mã lạ chạy trên site khách, và khách truy cập bị kết nối ra ngoài trong khi theme đã cam kết tự host font. Đưa trình phát vào `assets/js/vendor/` (ghi rõ giấy phép và phiên bản), vẫn chỉ nạp trên trang có block. Cân nhắc bản nhẹ hơn nếu dung lượng lớn; ghi số liệu vào PENDING-RELEASE.

**G-116 · P1 · Block Map nhận iframe từ host không phải Google** (`blocks/map/render.php:18`). Mẫu `google\.[a-z.]+` khớp cả `https://google.example.com/maps/embed?…`, nên người có quyền viết bài nhưng không có `unfiltered_html` vẫn chèn được iframe tùy ý. Thay bằng danh sách host: `www.google.com`, `google.com`, `maps.google.com` và các tên miền quốc gia của Google theo mẫu chặt (`google\.(com|[a-z]{2}|com?\.[a-z]{2})`, neo bằng `/`). Thêm ca kiểm thử cho host giả. Nhân tiện đặt `title` cho iframe khi chỉ có mã nhúng (hiện rỗng).

**G-117 · P2 · Trợ năng của hộp thoại và slider** (`assets/js/blocks.js`, `main.js`, `quick-view.js`).
- Popup, lightbox, video modal và ngăn menu di động đóng được bằng Escape và trả focus, nhưng không giữ focus bên trong: không có xử lý phím Tab hay `inert`. Viết một hàm giữ focus dùng chung và áp cho cả bốn. Quick View dùng `<dialog>` gốc của trình duyệt nên đã giữ focus, không cần sửa.
- Popup có `role="dialog"` nhưng thiếu tên (`blocks/popup/render.php`); thêm `aria-label` hoặc `aria-labelledby`.
- Nhãn lightbox viết cứng tiếng Anh trong JS (`blocks.js:589-591`), chấm slider chỉ có nhãn “1”, “2” (`:156`); đưa chuỗi dịch qua `wp_localize_script`.
- Slider tự chạy chỉ dừng khi rê chuột hoặc focus (`:194-207`), không có nút dừng và không điều khiển được bằng phím mũi tên. Thêm nút Tạm dừng/Chạy khi `autoplay` bật.
- Kiểm tra bằng bàn phím trên Playground; không cần công cụ mới.

**G-118 · P2 · Ảnh nền Section không responsive** (`blocks/section/render.php:16, 52`). Nền in bằng `background-image:url(…)` với URL gốc dù block đã lưu `bgImageId`: không `srcset`, không ưu tiên tải, nên hero dựng bằng Section tải ảnh cỡ đầy đủ trên di động và LCP chậm. Banner đã làm đúng bằng `<img fetchpriority>`. Khi có `bgImageId` và không dùng chế độ cần CSS (parallax/fixed/lặp), in `wp_get_attachment_image()` với `object-fit`, section đầu trang đặt `fetchpriority="high"`; giữ nền CSS cho các trường hợp còn lại và cho nội dung cũ không có ID. Đo trước/sau cùng G-111.

**G-119 · P2 · Lớp tương thích Flatsome luôn bật.** `functions.php` nạp ba tệp `inc/compat-flatsome*.php` (~1.950 dòng) ở mọi request, và `bst_legacy_register_shortcodes()` chiếm các tên chung `[section]`, `[row]`, `[col]`, `[button]`, `[title]`, `[gap]`, `[accordion]`, `[block]` trên cả site chưa từng dùng Flatsome (`inc/compat-flatsome.php:17-52`). Lớp này còn nạp `legacy.css` (15 KB) trên mọi trang và đăng ký post type `blocks`. Chuyển thành tính năng trong `inc/features.php`. Điều kiện bắt buộc để không làm vỡ site đang chạy: khi nâng cấp, tự bật nếu có bài chứa shortcode Flatsome hoặc có bài kiểu `blocks`; site cài mới thì tắt. `form-migrate.php` và các bộ lọc `bst_legacy_*` phải chạy đúng ở cả hai trạng thái. Kiểm tra bằng `tools/tests/features-integration.php` với cả hai loại site.

**G-120 · P2 · WooCommerce: kiểm chứng trước, sửa sau.**
- `assets/css/woocommerce.css` không có rule `wc-block` nào, trong khi Giỏ hàng và Thanh toán dạng block là mặc định của WooCommerce trên site mới. Dựng hai trang này trên Playground (`--woo`), chụp so với trang shortcode; chỉ thêm CSS theo biến màu/font của theme nếu lệch rõ.
- `woocommerce/content-product.php` ghi `@version 9.4.0`; so với template của bản WooCommerce đang hỗ trợ và cập nhật nếu WooCommerce báo template lỗi thời.

**Ghi chú nhỏ, gộp vào mục liên quan khi làm:**
- Ảnh bìa video mặc định lấy từ `i.ytimg.com` trước khi khách bấm phát (`blocks/video/render.php:33`, `blocks/video-button/render.php:23`); cùng hướng với G-115 nếu owner muốn site không kết nối bên thứ ba trước tương tác.
- `bst_aid_download()` gửi lại request kèm khóa API tới host kymaapi.com (`ai-designer/includes/tools-images.php:293`); đặt `'redirection' => 0` để khóa không thể theo chuyển hướng sang host khác.

### Đợt rà soát 3 — nội dung đã lỗi thời cần cập nhật

Đã kiểm và không cần sửa: danh sách block đăng ký khớp 80 thư mục `blocks/`; `node tools/check-block-previews.js` đạt; `python tools/release.py` báo `Ready.`; `theme.json` phiên bản 3 phù hợp `Requires at least: 6.5`; không thấy hàm WordPress đã bị loại bỏ.

**G-121 · P1 · Bản dịch tiếng Việt thiếu khoảng một nửa.** `.pot` có 3.837 chuỗi, `vi.po` có 2.499; 1.960 chuỗi trong `.pot` không có trong `vi.po` (ví dụ “Map”, “Top”, “Background color”), nên các phần đó hiện tiếng Anh trên site tiếng Việt. Tập trung ở giao diện editor và Customizer, đếm theo tệp (số chuỗi thiếu / tổng, ước lượng bằng regex):

| Tệp | Thiếu |
| --- | --- |
| `assets/js/blocks-editor.js` | 749 / 934 |
| `inc/colorful.php` | 222 / 223 |
| `inc/customizer/fields.php` | 201 / 263 |
| `inc/css-presets.php` | 144 / 144 |
| `assets/js/blocks-more.js` | 119 / 151 |
| `inc/header-elements.php` | 98 / 144 |
| `assets/js/block-design.js` | 88 / 101 |
| `assets/js/blocks-extra.js` | 74 / 104 |
| `inc/floating-contact.php` | 67 / 79 |

- AGENTS.md cấm dịch hàng loạt khi không có nhu cầu cụ thể, nên mục này cần owner duyệt phạm vi trước. Đề xuất làm theo từng tệp, mỗi phiên một tệp, bắt đầu từ `blocks-editor.js` (dùng grep để lấy chuỗi, không đọc cả tệp).
- Thêm `tools/check-strings.py`: liệt kê chuỗi trong mã chưa có trong `vi.po`, theo tệp. `release.py` in con số này dưới dạng ghi chú, không chặn phát hành.
- Dùng `python tools/add-strings.py`; không sửa tay `vi.po`.

**G-122 · P2 · Siêu dữ liệu phát hành lệch phiên bản.**
- `readme.txt:6` ghi `Stable tag: 1.19.1` trong khi theme ở 1.86.1. `release.py` chỉ nâng `style.css` và `functions.php`; thêm `readme.txt` vào bước nâng và vào kiểm tra lệch phiên bản.
- `docs/README-UPDATES.md` chưa có mục 1.86.1 (`readme.txt` đã có). Cho `release.py` cảnh báo khi changelog thiếu mục của phiên bản hiện tại.
- `Tested up to: 6.8` ở `style.css` và `readme.txt`: chạy bộ test trên bản WordPress mới nhất rồi cập nhật (cùng việc với G-110).
- `vi.po` ghi `Project-Id-Version: BIGSPY Theme`; `.pot` ghi ngày tạo 24-10-2026 và không được sinh lại từ mã: `add-strings.py` chỉ nối thêm, nên khoảng 770 chuỗi trong `.pot` không còn trong mã và một số chuỗi trong mã chưa vào `.pot` (ước lượng bằng regex, cần xác nhận khi làm). Sinh lại `.pot` từ mã một lần trong đợt phát hành đã duyệt, rồi đối chiếu với `vi.po`.

**G-123 · P2 · Kiến thức AI và CODEMAP thiếu phần mới.**
- `ai-designer/knowledge/` không nhắc đến `listing-elements` và `listing-search`; `theme.md` không có dòng nào về BIGSPY AI List. Bổ sung mô tả ngắn, chỉ áp dụng khi `bst_aid_listing_on()` đúng.
- Thêm kiểm tra vào `tools/check-block-previews.js` (hoặc script riêng): mọi block trong `blocks/` phải được nêu tên trong thư mục knowledge.
- `docs/CODEMAP.md` chưa có mục cho Listing (`inc/listing/`, `inc/features/listing.php`, `woocommerce/*-listing.php`), WooCommerce (`inc/woocommerce*.php`, `inc/quick-view.php`, `inc/product-options.php`), hiệu năng (`inc/performance.php`) và Form (`inc/form-native.php`, `inc/form-migrate.php`). Thêm mỗi mục một dòng.

### Thứ tự thực hiện đề xuất

1. G-124 (cổng cú pháp) — làm trước mọi bản phát hành tiếp theo.
2. G-116, G-115, G-107 — bảo mật.
3. G-108, G-109, G-110, G-122 — độ tin cậy và siêu dữ liệu phát hành.
4. G-112 — để các mục sau có test chạy được.
5. G-121 — sau khi owner duyệt phạm vi dịch.
6. G-111, G-118, G-117, G-119, G-120 — hiệu năng, trợ năng, tương thích.
7. G-113, G-123, G-114 — chuỗi viết cứng và tài liệu.

### Cần kiểm chứng trước khi lên kế hoạch

- **AI Designer sau proxy:** một bước tác vụ giữ request tới 150–170 giây (`ai-designer/includes/tasks.php:329`, `client.php` timeout 150). Cloudflare và nhiều hosting cắt kết nối sớm hơn trong khi PHP vẫn chạy. Cần thử trên hosting thật xem giao diện có tự nối lại qua trạng thái `busy` hay báo lỗi; chỉ mở mã G mới nếu tái hiện được.
- **Không đề xuất lúc này:** tách `blocks-editor.js` (234 KB) hay `tools-design.php` (1.719 dòng). AGENTS.md đã quy định chỉ tách phần đang sửa.

## Bối cảnh roadmap cũ

**Mốc phát hành:** 1.18.1 · **Đối chiếu:** Flatsome Classic 3.20.11. Trang Home Fresh Mart được quan sát ở bản 3.20.10; UX-Themes công bố Flatsome Site Builder beta ngày 30-09-2026, là sản phẩm block/plugin riêng.
Roadmap này sắp xếp các khoảng trống đã ghi nhận theo tác động đến shop và người làm nội dung. Đây là thứ tự ưu tiên đề xuất, không phải cam kết ngày phát hành.

**Hướng dựng trang:** lấy màn hình **Sửa trang** của WordPress và các khối BIGSPY làm luồng chính. Workspace BIGSPY AI Builder riêng đã được gỡ ở 1.18.0; UX Builder/shortcode Flatsome chỉ cần được giữ để hiển thị nội dung cũ và hỗ trợ chuyển đổi có xem trước. Không cần tái tạo UX Builder thành một editor song song.

## Đã có đến 1.18.1 (ghi nhận lịch sử)

- Sửa lỗi lựa chọn trang thiết kế trống khiến nội dung trang chủ lặp lại trong footer; khôi phục footer widget mặc định và chặn trang tự làm footer của chính nó.

- Các khối BIGSPY và thư viện mẫu dùng trực tiếp trong Sửa trang WordPress; cây cấu trúc, sắp xếp, lưu và revisions dùng editor native. Trang block đã xuất bản vẫn có thể làm thiết kế header/footer/trang sản phẩm.
- Tùy chọn Buy Now, Quick View, Portfolio, countdown, mega menu nhiều cột, load more/infinite scroll cho archive classic.
- Swatch nhãn/màu/ảnh trên trang sản phẩm cho global WooCommerce attributes; mặc định tắt.
- Swatch màu/ảnh tùy chọn trên thẻ sản phẩm trang shop/danh mục; mở trang sản phẩm với giá trị được chọn sẵn.
- Điều hướng AJAX tùy chọn cho widget lọc WooCommerce native: danh mục, thuộc tính, đánh giá, giá và sắp xếp; có history, thông báo trạng thái và tải lại toàn trang khi không thể cập nhật an toàn.
- Gallery ảnh theo biến thể được dùng thông qua WooCommerce Variation Gallery native (từ WooCommerce 10.9, tùy phiên bản/cờ tính năng). Theme giữ nguyên gallery markup chuẩn WooCommerce và hỗ trợ các layout/zoom/lightbox/slider hiện có; không lưu thêm bộ ảnh biến thể riêng.

## Ưu tiên tiếp theo

### P0 — Hoàn thiện trải nghiệm shop

1. **Kiểm tra tương thích gallery biến thể:** xác nhận gallery native trên các phiên bản WooCommerce đang hỗ trợ, gồm đổi variation liên tiếp, reset, bố cục stacked/thumbnail-left, zoom và lightbox. Tính năng tạo/lưu ảnh thuộc WooCommerce; theme chỉ bổ sung adapter nếu tái hiện được lỗi ở gallery markup chuẩn.
2. **Kiểm tra tương thích lọc:** xác nhận widget native với phân trang, load more, lịch sử trình duyệt, bộ nhớ đệm và markup phổ biến. Tài liệu hóa cách plugin lọc bên thứ ba có thể nối sự kiện hoặc dùng điều hướng gốc; không giả định mọi plugin có cùng API.
3. **Tùy chọn checkout còn thiếu:** xem xét hiển thị ngưỡng miễn phí vận chuyển và vị trí coupon/nội dung bổ sung như lựa chọn giao diện, không thay đổi quy tắc tính phí hay thứ tự nghiệp vụ WooCommerce.

### P1 — Dựng trang và nội dung

1. **Hoàn thiện luồng block trong Sửa trang:** làm rõ điểm bắt đầu, giúp tìm/chèn block BIGSPY và mẫu bố cục nhanh, giữ cấu trúc block WordPress để sửa bằng editor chuẩn. Tập trung các cải tiến trong editor WordPress và các khối BIGSPY.
2. **Di chuyển nội dung UX Builder cũ:** công cụ xem trước các shortcode Flatsome nhận diện được, cho chọn phần cần chuyển sang block native, giữ bản gốc để hoàn tác và báo rõ shortcode/plugin không hỗ trợ. Không tự chuyển hoặc xóa nội dung khi chưa có bản xem trước.
3. **Mega menu có block:** bổ sung vùng nội dung tùy chọn cho từng mục menu cấp cao; hỗ trợ điều khiển bằng bàn phím, focus, mobile drawer và fallback về submenu chuẩn.
4. **Tìm kiếm và thư viện mẫu:** bổ sung bố cục tìm kiếm hàng/masonry/slider, tùy chọn sắp xếp sản phẩm và ảnh xem trước nhẹ cho pattern; giữ cấu hình nguồn kết quả và nội dung block native.

### P1 — Chuyển động (owner chốt 30-10-2026)

Chỉ làm các hạng mục phù hợp hoàn toàn với WordPress và hosting chia sẻ: là khối hoặc tùy chọn Customizer, sửa tay được, CSS/API trình duyệt là chính, có dự phòng khi trình duyệt không hỗ trợ (IntersectionObserver), tôn trọng giảm chuyển động, chỉ nạp khi trang dùng.

1. **Đợt 1:** thanh Cường độ chuyển động toàn site (Nhẹ/Vừa/Mạnh) + preset; khối Animated Text (chữ sáng theo cuộn, gõ chữ, từ thay phiên, scramble, gạch chân tự vẽ, chữ gradient, chữ lớn chạy ngang) và áp hiệu ứng chữ cho Heading; Scroll Story; Stacking Cards.
2. **Đợt 2:** Hover Reveal List; SVG Line Draw; Horizontal Scroll (không chặn cuộn); Hero phóng to theo cuộn; Image Reveal; nâng cấp Section (parallax, sticky, đổi màu nền), Button (magnetic, vệt sáng, mũi tên), Image, Icon Box (icon tự vẽ), Slider (hiệu ứng chuyển mới), Counter (xáo chữ số), Gallery (so le).
3. **Đợt 3:** Video Hover Cards; Bento Grid động; nền grain/gradient mesh/kính mờ; chuyển trang mượt (View Transitions); magnetic element và con trỏ tùy biến (mặc định tắt).

**Đã loại (owner quyết định):** Image Sequence/video tua theo cuộn, 3D Product Viewer, Image Distortion, cuộn mượt quán tính (Lenis…), section cuộn toàn màn hình kiểu fullPage.

### P2 — Hiệu năng và tích hợp

1. **Quản lý tài nguyên:** xem xét công tắc tải CSS/media theo nhóm và trạng thái tương thích; mọi mục có giải thích, mặc định an toàn và cách khôi phục. Không tắt jQuery hoặc tài nguyên dùng chung chỉ để đạt một điểm benchmark.
2. **Site loader:** chỉ thêm khi có chế độ giảm chuyển động, không chặn thao tác bàn phím, có timeout/fallback và không che lỗi tải trang.
3. **Khối nội dung bổ sung:** đánh giá hotspot, Lottie và Instagram/feed theo nhu cầu thực tế; cấu hình nguồn rõ ràng, không nhúng token bí mật trong nội dung công khai và không thêm thư viện nếu khối native đáp ứng được.
4. **Adapter Countix:** chỉ làm sau khi chốt phiên bản plugin và markup thực tế. Không đưa bản vá ghi đè hàm jQuery `.html()` hoặc fix riêng child theme vào core.

## Điều kiện đưa tính năng vào bản phát hành

- Dùng API WordPress/WooCommerce và block native; không sao chép mã, asset hoặc thương hiệu Flatsome.
- Tính năng làm thay đổi hành vi shop mặc định tắt hoặc có fallback điều hướng chuẩn.
- Có trạng thái responsive, keyboard/focus và thông báo trợ năng phù hợp.
- Tài liệu nêu rõ plugin/phạm vi tương thích và những gì chưa hỗ trợ.
- Tạo bản xem trước/hoàn tác cho thao tác chuyển đổi nội dung hoặc cấu hình có thể gây mất dữ liệu.

## Phạm vi chưa cam kết

BIGSPY là theme riêng, không phải bản sao đầy đủ của UX Builder. Tương thích mọi shortcode Flatsome, mọi filter plugin và mọi tích hợp mạng xã hội cần adapter hoặc quyền/API riêng; chỉ đưa vào roadmap phát hành sau khi có phạm vi và cách kiểm tra cụ thể.
