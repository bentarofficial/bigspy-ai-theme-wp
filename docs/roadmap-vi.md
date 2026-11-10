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

## Đợt tối ưu sau rà soát mã nguồn 10-11-2026 (G-106 → G-123)

Rà soát ở bản 1.86.1, nhánh `main` tại `c4d154b`. Mỗi mục là một phiên làm việc riêng theo AGENTS.md; làm theo thứ tự ưu tiên, không gộp. **Cách làm chi tiết cho từng mã (tệp, đoạn mã hiện tại, từng bước sửa, ca kiểm thử, tiêu chí nghiệm thu) nằm ở [OPTIMIZATION-SPEC-2026-11.md](OPTIMIZATION-SPEC-2026-11.md); agent lập trình đọc đúng mục của mã đang làm trước khi sửa.** Đây là đề xuất chờ owner duyệt, chưa mục nào được triển khai.

**Phạm vi đã xem:** bootstrap, nạp tài nguyên, `inc/performance.php`, updater, Form native, tìm kiếm trực tiếp, AJAX sản phẩm, Quick View, khóa API và vòng lặp tác vụ của AI Designer, công cụ build/phát hành/test, tài liệu. `php -l` toàn bộ tệp PHP hiện đạt trên PHP 8.3.
**Đợt 2 (cùng ngày)** xem thêm `render.php` của block, script editor, quyền và luật block của AI Designer, lớp tương thích Flatsome, WooCommerce và trợ năng; kết quả ở mục “Đợt rà soát 2”. Các tệp lớn (`blocks-editor.js`, `blocks.css`, `tools-design.php`) chỉ được quét theo mẫu, không đọc từng dòng. Không chạy được bộ test Playground trong môi trường rà soát (xem G-112), nên các mục dưới đây dựa trên đọc mã, chưa tái hiện trên site thật trừ khi ghi rõ.

### P0 — Chặn lỗi phát hành

**G-106 · Cổng cú pháp trước khi đóng gói.** Hai bản liên tiếp (1.85.0 và 1.86.0) làm vỡ toàn bộ trang quản trị vì dấu nháy chưa escape trong `inc/welcome/data.php`; updater đẩy bản lỗi đến mọi site. `tools/release.py` và `tools/build-theme.py` hiện không kiểm tra cú pháp.
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

1. G-106 (cổng cú pháp) — làm trước mọi bản phát hành tiếp theo.
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
