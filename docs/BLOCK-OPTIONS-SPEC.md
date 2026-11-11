# Đặc tả mở rộng tuỳ chọn cho mọi khối (G-129 → G-139)

Nghiên cứu G-128, ngày 11-11-2026, tại `feature/ai-designer` `5492268` (bản 1.90.0). Chỉ là đề xuất chờ owner duyệt; chưa mục nào được lập trình.

**Yêu cầu của owner:** mọi khối, từ khối chính đến khối phụ nhỏ và ít dùng nhất (tìm kiếm, nút, đường kẻ…), đều phải có thật nhiều tuỳ chọn. Không chỉ tuỳ chọn cơ bản (màu, cỡ, khoảng cách) mà cả **kiểu dáng hiển thị** (bố cục, hình thức) và **phong cách hiển thị** (skin: phẳng, thẻ, viền, kính mờ, gradient…).

**Cách nghiên cứu:** đếm thuộc tính trong 81 `blocks/*/block.json`, đọc danh sách giá trị `bst_choice()` trong `render.php`, đọc lớp dùng chung `inc/block-design.php` và `inc/blocks.php:119-139`. Không đọc `blocks-editor.js` và `blocks.css` từng dòng, không mở editor để xem từng panel. Vì vậy các gợi ý “thêm” bên dưới là danh sách ứng viên: agent lập trình phải grep thuộc tính hiện có của khối trước khi thêm, bỏ mục đã có.

## 1. Hiện trạng

### Đã có cho mọi khối (không thêm lại ở từng khối)

| Lớp | Tệp | Nội dung |
| --- | --- | --- |
| Design (`bsDesign`) | `inc/block-design.php`, `assets/js/block-design.js` | margin/padding theo D/T/M, width, max-width, position + offset, nền, màu chữ, viền (một độ rộng cho 4 cạnh), bo góc (một giá trị), đổ bóng 5 mức + hover, hover nền/chữ/viền/bo góc/dịch lên/phóng to, z-index |
| Typography & alignment | cùng tệp | căn lề, cỡ chữ, line-height, letter-spacing theo D/T/M; font, độ đậm, transform, nghiêng, gạch, bóng chữ |
| Hiển thị và chuyển động | `inc/blocks.php:119-139`, `assets/js/editor-helpers.js` | ẩn theo thiết bị, 32 hiệu ứng xuất hiện (delay, duration, ease, distance, repeat, stagger), 10 hiệu ứng hover, loop, scroll, CSS presets |

Lớp Design chỉ tác động lên **vỏ ngoài** của khối. Phần bên trong (thanh tab, ô nhập, nút trong ô tìm kiếm, icon, tiêu đề thẻ…) chỉ đổi được nếu khối có thuộc tính riêng. Đây là chỗ thiếu chính.

### Số thuộc tính riêng của từng khối

| Nhóm | Khối (số thuộc tính) |
| --- | --- |
| 0–5, gần như không tuỳ chỉnh được | ticker-slide 0, shortcode 1, widget-area 1, tab 2, stack-card 2, hscroll-panel 2, spacer 3, buttons 3, accordion-item 3, story-step 3, row 4, timeline 4, payment-icons 4, horizontal-scroll 4, hover-reveal 4, column 5, bento 5, bento-cell 5, expand-panel 5, hover-reveal-item 5, listing-info 5, products-list 5 |
| 7–10, mỏng | banner-grid 7, share 7, video-cards 7, video-card 7, zoom-hero 7, theme-showcase 7, tabs 8, testimonial 8, star-rating 8, audio 8, lottie 8, line-draw 8, pricing-toggle 8, scroll-story 8, stack-cards 8, code 8, alert 9, table 9, progress 9, logos 9, nav-menu 9, hotspot 9, before-after 9, category-grid 9, divider 10, title 10, icon-list 10, listing-search 10, video 10, video-button 10 |
| 12–20, trung bình | map 12, popup 12, marquee 12, ticker 12, price-table 13, counter 14, icon-box 14, expand-gallery 14, gallery 15, accordion 16, text 16, image-box 16, team-member 16, animated-text 16, product-categories 16, flip-box 17, heading 19, social 20 |
| 24+, đã phong phú | image 24, banner 26, slider 26, sponsored 30, button 32, section 32, posts 35, products 36, listing-grid 39, form 78, listing-elements 91 |

### Số kiểu dáng hiện có (theo `bst_choice`)

tabs 4 · accordion 3 · testimonial 3 · alert 4 · title 5 · divider 5 · price-table 4 · team-member 4 · icon-box 3 khung × 4 kiểu icon · image-box 4 bố cục · gallery 3 · logos 2 · posts 4 bố cục × 7 kiểu thẻ · products 6 kiểu thẻ · button 12. Các khối timeline, progress, counter, star-rating, nav-menu, table, listing-search, hover-reveal, bento, video-cards, banner-grid chỉ có 0–2 kiểu.

### Thiếu sót chung

1. **Kiểu dáng ít.** Phần lớn khối có 2–5 kiểu; người dùng không dựng được hình thức khác nếu không viết CSS.
2. **Không chỉnh được từng phần bên trong:** màu, chữ, viền, bo góc, khoảng cách của nav, thẻ, icon, nút con, nhãn.
3. **Thiếu trạng thái:** hover, active, focus cho phần bên trong.
4. **Thiếu giá trị theo thiết bị** cho cột, chiều cao, cỡ chữ, căn lề ở nhiều khối nhỏ.
5. **Khối con gần như không có tuỳ chọn** (tab, accordion-item, stack-card, hscroll-panel, bento-cell, story-step).
6. **Chọn kiểu bằng chữ**, không có hình xem trước; panel xếp chưa thống nhất (Tabs có tab Cài đặt/Kiểu, Listing search dồn tất cả vào một panel “Settings”).
7. **Chưa có khối tìm kiếm chung** cho site thường: chỉ có `listing-search`, cần chế độ BIGSPY AI List.
8. Lớp Design: viền không theo cạnh, bo góc không theo góc, không gradient/ảnh nền, không bóng tuỳ chỉnh, không opacity; đặt bo góc thì luôn kèm `overflow:hidden` (cắt mất bóng, tooltip, dropdown bên trong).

## 2. Nguyên tắc bắt buộc

1. **Mặc định giữ nguyên hiện trạng.** Thuộc tính mới mặc định rỗng/0/false và khi đó không in CSS. Không đổi tên, không xoá thuộc tính cũ, không đổi mặc định cũ. Đổi cấu trúc dữ liệu (ví dụ danh sách mạng xã hội) phải đọc được dữ liệu cũ. Trang đã xuất bản không được đổi giao diện sau khi cập nhật.
2. **Kiểu dáng là lớp CSS, thông số là biến CSS.** Kiểu: `bs-<khối>--<kiểu>` qua `bst_choice()`. Thông số: biến `--bs-<khối>-<phần>-<thuộc tính>` in bằng `bst_style_vars()` lên thẻ bọc; CSS dùng `var(--…, giá trị mặc định)`. Không rải inline style lên phần tử con, không thêm thẻ `<style>` ngoài lớp Design.
3. **Mọi giá trị qua whitelist:** `bst_choice`, `bst_block_num`, `bst_block_color`; đầu ra escape như hiện nay.
4. **Theo thiết bị:** hậu tố `Tablet`/`Mobile` hoặc D/T/M theo cách khối đó đang dùng; ngưỡng 849px và 549px như lớp Design. Thành phần co theo bề rộng của chính nó thì dùng container query (ghi nhớ của owner 09-11).
5. **Editor giống front end.** Mỗi tuỳ chọn mới phải thấy ngay trong editor.
6. **Panel thống nhất.** Tab Cài đặt: nội dung, nguồn dữ liệu, hành vi. Tab Kiểu: kiểu dáng, rồi từng phần (ví dụ Tabs: Thanh tab → Tab đang chọn → Vùng nội dung). Tuỳ chọn phụ thuộc chỉ hiện khi tuỳ chọn cha bật. Panel phụ đóng sẵn; có dấu `•` khi đã đặt giá trị như panel Design.
7. **Không trùng lớp dùng chung.** Không thêm margin/padding/nền/viền cho vỏ ngoài ở từng khối; chỉ thêm cho phần bên trong.
8. **Mỗi tuỳ chọn phải tạo khác biệt nhìn thấy được.** Không thêm cho đủ số lượng. Tuỳ chọn hiếm dùng đặt trong panel “Nâng cao”.
9. **JS front chỉ nạp khi trang dùng**, không thêm thư viện, tôn trọng giảm chuyển động, dùng được bằng bàn phím.
10. **Chuỗi:** msgid tiếng Anh, thêm tiếng Việt bằng `python tools/add-strings.py`.
11. **AI Designer:** cập nhật `ai-designer/knowledge/blocks.php` và `theme.md` cho kiểu dáng và thuộc tính mới. Luật block không đổi.
12. **Sau mỗi phiên:** `python tools/split-blocks-css.py` (nếu sửa `blocks.css`), `python tools/lint.py`, `node tools/check-block-previews.js`, `node tools/features-check.cjs`; thử khối trong editor và front trên Playground ở 1280/768/375px; ghi `docs/PENDING-RELEASE.md`.

### Mức tối thiểu của một khối sau khi nâng cấp

Áp dụng phần nào có nghĩa với khối đó:

- Kiểu dáng: ít nhất 4–6 kiểu khác nhau rõ rệt, chọn bằng hình thu nhỏ.
- Bố cục và căn chỉnh theo D/T/M; hành vi riêng trên di động.
- Từng phần bên trong: kích thước, khoảng cách, màu nền/chữ/viền, bo góc, bóng.
- Trạng thái hover/active/focus của phần tương tác.
- Chữ của từng phần: cỡ theo thiết bị, độ đậm, màu.
- Icon/ảnh: chọn icon, vị trí, cỡ, màu, hình nền icon.
- Hành vi: kích hoạt, tốc độ, mặc định mở, tự chạy.

## 3. Nền tảng (làm trước)

### G-129 · Bộ điều khiển dùng chung cho editor

Tạo `assets/js/block-kit.js` (nạp trước các tệp editor) và các hàm PHP tương ứng trong `inc/blocks.php` hoặc tệp mới `inc/block-kit.php`. Không tách `blocks-editor.js`; khối nào được nâng cấp thì chuyển sang dùng kit ở phiên của khối đó.

| Kit | Điều khiển | Đầu ra |
| --- | --- | --- |
| `Resp` | một giá trị theo D/T/M, có đơn vị, nút chuyển thiết bị đồng bộ với xem trước của editor | 3 thuộc tính |
| `PartTypo` | cỡ D/T/M, độ đậm, line-height, letter-spacing, transform, màu, màu hover | biến `--bs-<khối>-<phần>-fs/fw/lh/ls/tt/c/ch` |
| `ColorStates` | nền/chữ/viền × thường/hover/active | biến màu |
| `PartBox` | padding, bo góc, viền (rộng/kiểu/màu), bóng (0–5) | biến |
| `IconPick` | thư viện icon của theme, ảnh/SVG tải lên, emoji, số; cỡ, màu, nền, hình (tròn/vuông/bo), vị trí | thuộc tính icon |
| `LinkKit` | URL, tab mới, rel, aria-label; hành động: mở popup theo ID, cuộn đến anchor, gọi điện, tải tệp | thuộc tính link |
| `StylePicker` | lưới hình thu nhỏ (SVG nội tuyến) thay cho hàng nút chữ | một thuộc tính kiểu |
| `Carousel` | cột D/T/M, khoảng cách, tự chạy, tốc độ, mũi tên, chấm, lặp, lộ mép | tái dùng tuỳ chọn và JS của `slider` |
| `CardKit` | kiểu thẻ, padding, bo góc, viền, bóng, hover, tỉ lệ ảnh, hover ảnh | dùng cho posts, products, listing, testimonial, team… |
| `QueryKit` | loại nội dung, taxonomy, gồm/loại trừ ID, sắp xếp, hướng, offset | dùng cho posts, products, categories |

Kèm theo:
- Hàm PHP `bst_part_vars( $a, $prefix, $map )` gom biến cho một phần, để `render.php` không lặp mã.
- `tools/check-block-options.js`: báo thuộc tính có trong `block.json` nhưng không được `render.php` (hoặc tệp render dùng chung) đọc, và thuộc tính không xuất hiện trong JS editor. In cảnh báo, chưa chặn phát hành.
- Nghiệm thu: chuyển khối `divider` sang kit làm mẫu; nội dung cũ render y hệt (so HTML trước/sau).

### G-130 · Mở rộng lớp Design dùng chung

Sửa `inc/block-design.php` và `assets/js/block-design.js`. Mọi khối được hưởng ngay.

- Viền theo từng cạnh; bo góc theo từng góc.
- Nền gradient (2–3 điểm màu, góc, kiểu linear/radial); ảnh nền (vị trí, cỡ, lặp) với lớp phủ màu.
- Bóng tuỳ chỉnh (x, y, blur, spread, màu) bên cạnh 5 mức có sẵn; bóng trong.
- Opacity; bộ lọc (blur, brightness, grayscale); backdrop blur (kính mờ).
- Transform tĩnh: xoay, dịch X/Y, nghiêng.
- Thời gian và kiểu chuyển của hover (hiện cố định 0,3s).
- Màu, viền, bo góc theo D/T/M (hiện chỉ áp cho desktop).
- Chiều cao tối thiểu D/T/M; tỉ lệ khung.
- Bổ sung sau đối chiếu Elementor (G-140): mặt nạ hình (tròn, blob, SVG), CSS riêng cho từng khối (phạm vi tự gắn vào khối, lọc như khối Code), thuộc tính HTML tuỳ chỉnh (`data-*`, `aria-*` theo whitelist), transform ở trạng thái hover; đánh giá thêm điểm ngắt laptop và màn rộng, chỉ làm nếu không phải nhân đôi mọi thuộc tính theo thiết bị.
- Thứ tự hiển thị theo thiết bị (`order`) và tự căn (`align-self`) khi nằm trong Row/Column.
- Dính khi cuộn (sticky) với khoảng cách trên.
- Bo góc không tự kèm `overflow:hidden` cho nội dung mới; thêm tuỳ chọn Overflow (visible/hidden/clip). Nội dung cũ giữ hành vi cũ (xác định bằng cờ phiên bản trong `bsDesign`).
- Nút **Sao chép kiểu / Dán kiểu / Đặt lại** trên thanh công cụ khối: sao chép `bsDesign` và các thuộc tính kiểu của khối cùng loại.
- Kiểm tra: mở rộng test hiện có của Design trong `tools/features-check.cjs`; trang cũ có `bsDesign` in CSS giống hệt trước.

### G-131 · Kiểu dựng sẵn cho từng khối

- Mỗi khối có thể khai báo `blocks/<slug>/presets.json`: danh sách `{ name, label, attrs }`. Panel “Kiểu dựng sẵn” ở đầu tab Kiểu hiện hình thu nhỏ; bấm là áp bộ thuộc tính.
- Khác với `StylePicker` (đổi một thuộc tính kiểu): preset đổi nhiều thuộc tính cùng lúc, ví dụ Tabs “Segmented tối”, Search “Hero bóng đổ”.
- Người dùng lưu kiểu hiện tại thành preset riêng của site (lưu trong option, có xoá). AI Designer đọc được preset qua `block_catalog`.
- Mỗi phiên nâng cấp khối ở mục 4 thêm 4–8 preset cho khối đó.

## 4. Nâng cấp theo nhóm khối

Ký hiệu: **Kiểu** = kiểu dáng/phong cách mới; **Tuỳ chọn** = thông số mới. Mục đánh dấu (kiểm) là chưa xác minh khối đã có hay chưa.

### G-132 · Khối cơ bản nhỏ

| Khối | Kiểu | Tuỳ chọn |
| --- | --- | --- |
| divider | sóng, zigzag, chấm tròn, mờ hai đầu, dày-mỏng, đổ bóng, có icon/chữ ở trái-giữa-phải | độ rộng theo px/% và D/T/M; khoảng cách trên/dưới D/T/M; màu gradient từ–đến (hiện chỉ có công tắc); chữ: cỡ, đậm, màu; icon: cỡ, nền tròn; hiệu ứng tự vẽ khi vào màn hình |
| spacer | — | đơn vị px/vh/rem; giá trị âm để kéo khối chồng lên nhau; hiện vạch trong editor |
| buttons | — | căn và hướng (ngang/dọc) theo D/T/M; khoảng cách D/M; xuống dòng; các nút rộng bằng nhau; chữ ngăn cách (“hoặc”) |
| button | 3D, neon, viền chấm, tô nền trượt khi hover, mũi tên trong vòng tròn | chỉ icon (kèm aria-label); nhãn nhỏ (badge); hành động qua `LinkKit` (popup, cuộn, gọi, tải tệp, sao chép); rộng theo D/T/M; màu viền và bóng khi hover |
| title | hai đường kẻ hai bên, đường đôi, nhãn nền, chấm, gạch gradient, có số thứ tự | dòng nhỏ phía trên (kicker); chữ phụ: cỡ, màu; cỡ theo D/T/M; đường kẻ: dày, dài, màu; icon |
| heading | viền chữ (stroke), đánh dấu kiểu bút dạ/khoanh tròn/khung | kicker; icon trước; đường trang trí trước/sau; bóng chữ tuỳ chỉnh |
| text | kiểu danh sách (dấu tích, mũi tên, số trong vòng tròn), kiểu trích dẫn | kiểu link (gạch chân, hover); khoảng cách và đường kẻ giữa cột; giới hạn số dòng kèm “Xem thêm” |
| star-rating | sao, tim, ngón tay, chấm; dạng số “4,8/5” | nửa điểm; vị trí nhãn; số lượt đánh giá; khoảng cách |
| alert | gradient, kính mờ, thanh ngang toàn chiều rộng, toast góc | icon tuỳ chọn và cỡ; nút hành động; bo góc, độ dày viền; thẻ tiêu đề; nhớ trạng thái đã đóng (số ngày) |
| progress | thanh, vòng tròn, nửa vòng, theo bậc, nét đứt | giá trị tối đa và hậu tố; gradient; vị trí nhãn; màu riêng từng mục; thời gian chạy |
| counter | chữ số cùng icon, thẻ, có đường ngăn, ngang | icon/ảnh; số thập phân; cỡ và màu riêng cho tiền tố/hậu tố; độ đậm số; chạy lại mỗi lần vào màn hình |

### G-133 · Tabs, Accordion, Table, Timeline, Icon List

**tabs + tab** (ví dụ owner nêu; hiện 8 + 2 thuộc tính, 4 kiểu):
- Kiểu: gạch chân dày, đường trên, thư mục (boxed nối liền panel), segmented, nút viền, thẻ, icon trên chữ, dọc trái, dọc phải.
- Vị trí thanh tab: trên/dưới/trái/phải; căn: trái/giữa/phải/dàn đều; bề rộng cột tab dọc.
- Di động: cuộn ngang, chuyển thành accordion, dropdown, xếp dọc.
- Hành vi: tab mở mặc định; liên kết sâu `#tab` và nhớ tab; tự xoay (giây, dừng khi hover); hiệu ứng chuyển (mờ/trượt/phóng/không) và thời gian; thanh tab dính khi cuộn.
- Thanh tab: khoảng cách, padding, bo góc, viền, nền; chữ qua `PartTypo`; màu chữ/nền/viền × thường/hover/đang chọn; màu và độ dày vạch chỉ báo.
- Icon: vị trí trái/phải/trên, cỡ, chỉ icon trên di động.
- Vùng nội dung: nền, viền, padding, bo góc, chiều cao tối thiểu, các panel cao bằng nhau.
- Khối con `tab`: chữ phụ, nhãn (badge), ảnh thay icon, ID anchor, màu riêng, vô hiệu.

**accordion + accordion-item:** kiểu thẻ bóng, nền đặc, gạch dưới, thanh trái, số lớn; mở mặc định (đầu/tất cả/không/chỉ số); nút mở–đóng tất cả; liên kết sâu; icon: màu, cỡ; tiêu đề: đậm, padding, màu hover; viền: màu, dày; chữ nội dung; chia 2 cột; ô lọc câu hỏi. Khối con: icon riêng, chữ phụ, nhãn.

**table:** soạn dạng lưới ô thay cho chuỗi `a | b` (đọc được dữ liệu cũ); kiểu: thường, sọc, viền, thẻ, tối giản, bảng so sánh; nền/chữ tiêu đề; dính hàng tiêu đề và cột đầu; di động: cuộn ngang hoặc xếp thành thẻ; căn và bề rộng từng cột; tô nổi một cột; phím tắt ✓/✗; hover hàng; padding ô; bo góc; sắp xếp và ô tìm (tuỳ chọn, JS chỉ nạp khi bật).

**timeline** (4 thuộc tính): bố cục xen kẽ/trái/phải/ngang/ngang cuộn/gọn; mốc: chấm/số/icon/ảnh/ngày; đường: kiểu, dày, màu, tô dần theo cuộn; thẻ: không/thẻ/viền/có mũi tên; mỗi mục thêm icon, ảnh, link, nút, nhãn; vị trí ngày; chữ và màu từng phần.

**icon-list:** màu và kiểu icon riêng từng dòng (trơn/tròn/vuông); căn icon theo dòng đầu/giữa; chữ phụ và nhãn mỗi dòng; đánh số tự động; cột theo T/M; khoảng cách hàng/cột; kiểu và màu đường ngăn; dấu ngăn ở bố cục ngang; màu link hover.

**code:** không làm ở đây; thuộc G-10.

### G-134 · Tìm kiếm và tiện ích

**listing-search và khối mới `bst/search`** (ví dụ owner nêu; hiện 10 thuộc tính, một panel):
- Tạo `bst/search` cho site thường (bài viết, trang, sản phẩm), dùng điểm cuối tìm kiếm trực tiếp có sẵn ở `inc/header-extras.php`. `listing-search` dùng chung phần giao diện và tuỳ chọn.
- Kiểu: viên thuốc, hộp vuông, gạch chân, nút tách rời, chỉ icon rồi mở rộng, hero lớn có bóng, kèm dropdown danh mục, mở toàn màn hình.
- Icon kính lúp: trái/phải/ẩn. Nút: chữ, icon hoặc cả hai; vị trí trong ô/ngoài ô/xuống dòng trên di động.
- Kích thước: chiều cao D/M, cỡ chữ, padding, bề rộng D/T/M, căn D/M, bo góc riêng cho ô và nút.
- Màu: nền ô, chữ, placeholder, viền, viền khi focus, vòng focus, nút nền/chữ × thường/hover; độ dày viền; bóng thường/focus.
- Gợi ý tức thời: bật/tắt, số kết quả, hiện ảnh/giá/danh mục, số ký tự tối thiểu.
- Phạm vi: loại nội dung, danh mục; trang kết quả đích.
- Từ khoá gợi ý dạng chip dưới ô (nhập tay); placeholder xoay vòng nhiều câu; phím tắt `/`; nhãn ẩn cho trình đọc màn hình.

| Khối | Kiểu | Tuỳ chọn |
| --- | --- | --- |
| nav-menu | gạch chân, viên thuốc, hộp, ngăn bằng vạch, dọc dạng accordion | màu mục đang chọn, nền; chữ; di động: nút mở/ngăn kéo/accordion/cuộn ngang; mở menu con bằng hover hay bấm; số cấp; padding mục; khoảng cách D/M |
| social | chữ kèm icon, lưới, dọc | thêm Telegram, Threads, GitHub, Discord, Shopee, Lazada, Google Maps, website và mục tuỳ chỉnh (icon + URL), chuyển sang danh sách lặp nhưng đọc được thuộc tính cũ; hover (nhấc, đổi màu, xoay); màu nền, viền; cỡ icon tách khỏi cỡ ô; tooltip; căn và khoảng cách theo thiết bị |
| share | viên thuốc có chữ, thanh nổi dọc bên trang, thanh dính đáy trên di động | thêm Telegram, WhatsApp, Pinterest, Email, Messenger, sao chép link, in; vị trí nhãn; màu tuỳ chỉnh; khoảng cách |
| payment-icons | thẻ nền trắng có viền, nền tối | thêm MoMo, ZaloPay, VNPay, Napas, COD, chuyển khoản và ảnh tuỳ chỉnh; khoảng cách; bo góc; xám rồi lên màu khi hover; nhãn phía trước |
| widget-area | thẻ cho từng widget | số cột D/T/M; khoảng cách; ẩn tiêu đề widget; kiểu tiêu đề; đường ngăn |
| shortcode | — | căn, bề rộng tối đa, chữ thay thế khi rỗng, bật/tắt xem trước trong editor (liên quan G-95) |
| map | thẻ có khung thông tin bên cạnh, toàn chiều rộng | chiều cao D/T/M hoặc tỉ lệ; bấm mới tải bản đồ (kèm ảnh giữ chỗ); khung thông tin: vị trí thêm trên/dưới/chồng, màu; kiểu nút chỉ đường; viền, bóng |
| popup | thanh trên/dưới, toàn màn hình, ngăn kéo trái | hiệu ứng mở; kích hoạt thêm: bấm vào anchor/selector, không hoạt động N giây, cuối bài; hiện theo thiết bị, trạng thái đăng nhập, khoảng ngày; tần suất chi tiết; nút đóng: kiểu, vị trí, màu; màu và độ mờ lớp phủ; bề rộng px, padding, bóng, viền; đóng sau khi gửi form |

### G-135 · Thẻ nội dung và media

| Khối | Kiểu | Tuỳ chọn |
| --- | --- | --- |
| icon-box | bóng, nền đặc, gradient, kính mờ, viền trái/trên, số lớn, ngang gọn, viền rồi tô nền khi hover | icon phải/trên-trái/cùng dòng tiêu đề; nguồn icon qua `IconPick`; kiểu icon thêm bo góc, nền nhạt, gradient; chữ tiêu đề và mô tả qua `PartTypo`; nút hoặc link kèm mũi tên; nhãn; hover đảo màu icon; căn theo thiết bị; các hộp cao bằng nhau |
| image-box | chữ hiện khi hover, phủ gradient đáy, ảnh tròn, thẻ lồng, ảnh dưới | màu/độ mờ/vị trí lớp phủ; nút; nhãn; dòng phụ; chữ; padding, viền, bóng; hover thêm mờ dần, chữ trượt lên, xám; tỉ lệ theo thiết bị; xếp dọc trên di động |
| flip-box | lật ngang/dọc, mờ, trượt, phóng, đẩy, khối lập phương | kích hoạt hover/bấm; chiều cao D/M; ảnh nền và lớp phủ cho mặt sau; cỡ và màu icon; chữ; kiểu nút; thời gian; viền, bóng |
| testimonial (8 thuộc tính) | dấu ngoặc lớn, ảnh bên trái, avatar trên giữa, viền, gradient, bong bóng chat, đánh giá ở trên, đường kẻ tối giản | dấu ngoặc: bật/tắt, kiểu, màu, vị trí; avatar: cỡ, hình, viền, vị trí; đánh giá: vị trí, màu, cỡ; logo công ty, nguồn (Google/Facebook), ngày, nhãn đã xác minh; giới hạn số dòng kèm mở rộng; chữ từng phần; nền, viền, bóng, padding |
| team-member | ngang, lật hiện tiểu sử, mạng xã hội hiện khi hover, viền, vòng tròn có viền | danh sách mạng xã hội mở rộng (đọc được thuộc tính cũ); hover ảnh; kiểu, hình, vị trí icon mạng xã hội; nút xem hồ sơ; nhãn; tỉ lệ 4:5 và tròn; chữ, màu |
| price-table | bóng nổi, tối, viền nổi bật, đầu thẻ có màu, gọn, dạng hàng ngang | tính năng dạng danh sách có ✓/✗ và ghi chú (đọc được chuỗi cũ); kiểu ruy băng và màu nhãn; icon/ảnh đầu thẻ; cỡ giá, vị trí đơn vị tiền; kiểu và cỡ nút, link phụ; phóng to thẻ nổi bật; ghi chú dưới nút; màu từng phần |
| pricing-toggle | công tắc, tab, viên thuốc | số cột D/T/M; gói nổi bật; màu nhãn; URL nút riêng cho từng kỳ; dùng chung tuỳ chọn thẻ với price-table |
| hotspot | chú thích sáng/tối/kính mờ; mốc có nhãn luôn hiện | cỡ và màu mốc; vị trí chú thích; nội dung chú thích thêm ảnh, nút, giá; mở sẵn một điểm; di động: danh sách bên dưới |
| before-after | tay nắm tròn/đường/mũi tên/vuông | hướng ngang/dọc; màu, cỡ tay nắm; nhãn: kiểu, vị trí, luôn hiện hay khi hover; di theo chuột hay kéo; tỉ lệ/chiều cao; tự trượt giới thiệu |
| audio | thanh tối giản, gọn, bìa lớn, danh sách phát | màu nền/chữ; tốc độ phát; nút tải; hiện thời gian; hình bìa |
| video | kiểu nút phát (cỡ, màu, hình) | màu lớp phủ; chú thích; phát trong lightbox; thời điểm bắt đầu/kết thúc; thu nhỏ dính góc khi cuộn; bề rộng tối đa và căn; tỉ lệ theo thiết bị; viền, bóng |
| video-button | tròn, bo, viền, kính mờ, chữ kèm icon | chọn icon; vị trí nhãn; kiểu nhịp (vòng/gợn/sáng) và màu; cỡ theo thiết bị |
| lottie | — | kích hoạt: tự chạy/hover/bấm/vào màn hình/theo cuộn; chiều chạy; bề rộng D/T/M; link |
| logos | slider có mũi tên/chấm; hai hàng chạy ngược chiều | cột tablet; link, tooltip, alt từng logo; hover (màu, phóng, nhấc, độ mờ); khung: nền, viền, bo góc, padding; đường kẻ lưới; chiều chạy; mờ hai mép |
| image (kiểm) | mặt nạ hình (tròn, blob, vòm, lục giác), khung polaroid/trình duyệt/điện thoại | kiểu và vị trí chú thích; nhãn; thanh lọc CSS; lớp phủ icon/chữ khi hover; bề rộng D/T/M |

### G-136 · Bố cục và lưới ảnh

| Khối | Kiểu | Tuỳ chọn |
| --- | --- | --- |
| row | mẫu chia cột nhanh (1/2+1/2, 1/3+2/3, 1/4×4…) | khoảng cách px theo D/T/M và khoảng cách hàng; đảo thứ tự cả trên tablet; cột cao bằng nhau; căn thêm dàn đều; đường ngăn giữa cột; ngưỡng xếp dọc; cuộn ngang trên di động |
| column | — | dính khi cuộn; ảnh nền kèm lớp phủ; link toàn cột; thứ tự theo thiết bị; bố cục bên trong (dọc/ngang, khoảng cách, căn) |
| section, banner (kiểm) | — | rà lại: nền video, nền gradient, hiệu ứng Ken Burns, hộp nội dung (nền đặc/kính mờ), viền; chỉ thêm phần chưa có |
| banner-grid | thêm mẫu lưới (1-1, 2-1, 1-3, 3 cột, 2×2, mosaic 5 ô) | chiều cao D/T/M; di động: xếp dọc/slider/2 cột; mỗi ô: tiêu đề, chữ phụ, nút, nhãn, vị trí nội dung, tông chữ, lớp phủ riêng; lớp phủ gradient; hover thêm nhấc, tối, hiện chữ, xám; chữ |
| bento + bento-cell | ô viền, kính mờ, gradient | số cột D/T/M; chiều cao hàng D/M; màu ánh sáng; hover (nhấc, nghiêng, viền sáng); ô: cỡ 2×2 và toàn hàng, lớp phủ và vị trí ảnh nền, link, căn dọc, padding |
| gallery | justified, mosaic, ảnh lớn kèm dải thumbnail, có bộ lọc theo nhóm | chú thích: dưới/chồng/khi hover, chữ; hover thêm lớp phủ icon, chú thích trượt, mờ; lightbox: chú thích, bộ đếm, thumbnail; giới hạn số ảnh kèm “+N”/tải thêm; link tới lightbox/URL/không; viền, bóng; khoảng cách D/M; slider dùng `Carousel` |
| expand-gallery + expand-panel | — | mở bằng hover hay bấm; hướng ngang/dọc; chữ và vị trí tiêu đề; lớp phủ gradient; số thứ tự/icon; link; tự chạy |

### G-137 · Khối chuyển động

| Khối | Kiểu | Tuỳ chọn |
| --- | --- | --- |
| marquee | chữ viền, mục dạng viên thuốc, dải nghiêng, hai hàng ngược chiều | mục là danh sách (chữ/icon/ảnh/link), đọc được chuỗi cũ; dấu ngăn tuỳ chọn và màu; cỡ D/M, font, transform; tốc độ D/M; chiều lên/xuống; mờ hai mép; viền trên/dưới; màu hover |
| ticker | — | mũi tên và kéo; số hàng; tốc độ và khoảng cách theo thiết bị; chiều dọc; bề rộng và màu vùng mờ mép |
| horizontal-scroll + panel | — | kiểu, vị trí, màu mũi tên và chấm; thanh tiến độ; chiều cao panel D/M hoặc tỉ lệ; bo góc, bóng, viền panel; chế độ ghim theo cuộn hoặc vuốt; panel: ảnh nền, lớp phủ, link, căn dọc, padding |
| stack-cards + card | hiệu ứng thu nhỏ/xoay/mờ/nhoè/không | mức thu nhỏ; vị trí dính D/M; padding, viền, mức bóng thẻ; tắt xếp chồng trên di động; thẻ: ảnh nền, gradient, lớp phủ, link |
| scroll-story + step | tiến độ dạng thanh/chấm/số | tỉ lệ bề rộng media (40/50/60); bố cục di động; kiểu làm nổi bước đang xem; khoảng cách bước; bóng, viền media; bước: số, kicker, icon; media là video |
| hover-reveal + item | ảnh theo con trỏ, cố định bên phải, mở ngay trong dòng, làm nền | tỉ lệ, bo góc, độ xoay ảnh; chữ tiêu đề; màu thường/hover/làm mờ mục khác; số thứ tự; icon mũi tên; màu và dày đường ngăn; di động hiện thumbnail |
| video-cards + card | lưới, slider, chữ trong/dưới thẻ | phát khi hover/bấm/vào màn hình; nút tắt tiếng; lớp phủ gradient; chữ; phóng khi hover; nhãn thời lượng; mở lightbox; viền, bóng |
| zoom-hero | hình bắt đầu: khung thụt, tròn, bo góc | chiều cao D/M; bo góc cuối; màu lớp phủ; vị trí nội dung; tâm phóng; nguồn video; biểu tượng gợi ý cuộn; chế độ đơn giản trên di động |
| line-draw | thêm hình có sẵn (mũi tên, vòng tròn, gạch chân, zigzag, dấu tích) | đầu nét, nét đứt, gradient; căn; bề rộng D/M; trễ, lặp, easing; tô nền sau khi vẽ |
| animated-text | kiểu đánh dấu (gạch chân, bút dạ, khoanh tròn, khung) | ký tự và màu con trỏ; thời gian dừng giữa các từ; kích hoạt khi vào màn hình/tải trang/hover; trễ; font, letter-spacing; viền chữ |
| slider (kiểm) | — | rà lại: thời gian và easing chuyển cảnh, chiều dọc, chế độ giữa, thumbnail điều hướng, bộ đếm “1/5”, màu mũi tên và chấm, ẩn hiện mũi tên/chấm theo thiết bị; nút tạm dừng thuộc G-117 |

### G-138 · Khối truy vấn

Dùng `CardKit`, `QueryKit`, `Carousel`. Các khối listing đã nhiều thuộc tính: chủ yếu sắp lại panel, thêm chọn kiểu bằng hình; phần cài đặt chung thuộc G-103.

| Khối | Kiểu | Tuỳ chọn |
| --- | --- | --- |
| posts | masonry, danh sách gọn, lưới chữ chồng ảnh, một bài lớn kèm danh sách, timeline, chỉ tiêu đề | tab lọc theo danh mục; phân trang: số/tải thêm/cuộn vô hạn; truy vấn: loại nội dung, taxonomy, tác giả, bài liên quan, hướng sắp xếp; vị trí và bề rộng ảnh; kiểu, màu, vị trí nhãn danh mục; chữ tiêu đề và mô tả; số thứ tự; tiêu đề khối kèm link “Xem tất cả”; chữ khi rỗng |
| products | danh sách, một sản phẩm lớn kèm lưới | tab lọc (danh mục hoặc mới/bán chạy/giảm giá); nhãn giảm giá: kiểu, vị trí, % hay chữ; nút: kiểu, vị trí (dưới/chồng khi hover/icon); hiện mô tả ngắn, đã bán, tồn kho; chữ tiêu đề, giá; ảnh vừa khung hay cắt, nền ảnh; truy vấn: giảm giá, nổi bật, thuộc tính, loại trừ; tiêu đề khối kèm link. Không thêm wishlist/compare (G-40) |
| products-list (5 thuộc tính) | danh sách, 2 cột, cuộn ngang | ảnh: cỡ, hình, vị trí; hiện đánh giá, giá, danh mục, nút, số thứ tự; đường ngăn; số dòng tiêu đề; sắp xếp; chọn theo ID; link “Xem tất cả” |
| product-categories, category-grid | thẻ, tròn, phủ gradient đáy, chip chỉ chữ, danh sách icon trái, banner | hình ảnh; định dạng số lượng; nút; vị trí chữ; màu lớp phủ; gồm/loại trừ ID; mũi tên và chấm của slider; khoảng cách, bo góc, viền. Nâng category-grid (9 thuộc tính) ngang product-categories; không xoá khối nào |
| listing-info (5 thuộc tính) | thẻ, một dòng, dạng sidebar | chọn từng trường và thứ tự; cỡ và hình logo; nút: chữ, kiểu, màu; đánh giá, nhãn giá, tag; chữ và màu; viền, bóng |
| listing-grid, listing-elements, sponsored | chọn kiểu thẻ bằng hình | chia panel thành tab Cài đặt/Kiểu; không thêm thuộc tính trùng G-103 |
| theme-showcase | — | ưu tiên thấp, chỉ làm nếu owner yêu cầu |

### G-139 · Rà soát cuối và đồng bộ

- `tools/check-block-options.js` không còn cảnh báo; chuyển thành kiểm tra bắt buộc trong `tools/release.py`.
- Mọi khối đã nâng cấp có preset (G-131) và hình xem trước kiểu.
- Cập nhật kiến thức AI, bản dịch tiếng Việt cho chuỗi mới, trang demo từng khối trên phonglammedia.com (theo ghi nhớ “site là nơi trưng bày theme”, cần owner đồng ý trước khi sửa site).
- Đo `blocks.css` và JS editor trước/sau toàn đợt; ghi vào `docs/PENDING-RELEASE.md`. Nếu `blocks.css` tăng trên 25% thì ưu tiên G-111 trước khi làm tiếp.

## 5. Thứ tự và cách chia phiên

1. G-129 → G-130 → G-131 (nền tảng, mỗi mã một phiên).
2. G-134 và G-133 trước, vì chứa hai ví dụ owner nêu (ô tìm kiếm, Tabs).
3. G-132 → G-135 → G-136 → G-137 → G-138.
4. G-139.

Mỗi mã từ G-132 đến G-138 gồm nhiều khối: mỗi phiên chỉ làm 2–4 khối, claim mã con (`G-133-a`, `G-133-b`…), commit theo từng khối. Khối `form`, `button`, `section`, `banner`, `posts`, `products` đã phong phú nên làm sau cùng trong nhóm của mình.

## 6. Rủi ro

- **Dung lượng:** mỗi kiểu mới thêm CSS vào `blocks.css` (đã ~195 KB) và chuỗi vào editor. Per-page CSS giảm tác động ở front, nhưng G-108 (per-page CSS có thể tự tắt) nên xong trước đợt này.
- **Bản dịch:** thêm vài trăm chuỗi editor trong khi `vi.po` đang thiếu khoảng 1.960 chuỗi (G-121).
- **`blocks-editor.js`** (3.464 dòng) không được tách một lần; panel mới viết trong `block-kit.js` hoặc tệp theo nhóm khối.
- **AI Designer:** nhiều thuộc tính hơn làm `block_catalog` dài hơn; chỉ đưa kiểu dáng và thuộc tính chính vào hồ sơ khối.
- **Chưa kiểm chứng:** nghiên cứu này không mở editor; một số tuỳ chọn có thể đã tồn tại dưới tên khác.

## 7. Mốc tham chiếu Elementor (G-140)

Đo ngày 11-11-2026 trên Elementor 4.3.6 miễn phí, số control riêng của widget không tính bản sao theo thiết bị và tab Nâng cao; chi tiết ở [COMPETITIVE-STRATEGY-2026-11.md](COMPETITIVE-STRATEGY-2026-11.md). Dùng làm mức tham chiếu về độ phủ, không phải chỉ tiêu số lượng (nguyên tắc 8 vẫn áp dụng).

| Khối | Elementor | BIGSPY hiện tại |
| --- | --- | --- |
| tabs | 42 | 8 |
| accordion | 46 | 16 |
| button | 104 | 32 |
| divider | 41 | 10 |
| icon-box | 56 | 14 |
| image-box | 73 | 16 |
| image | 53 | 24 |
| testimonial | 53 | 8 |
| counter | 48 | 14 |
| progress | 39 | 9 |
| alert | 40 | 9 |
| icon-list | 34 | 10 |
| star-rating | 25 | 8 |
| text | 41 | 16 |
| video | 54 | 10 |

Elementor đạt các số này bằng cách cho mỗi phần của widget một nhóm ở tab Kiểu với cùng bộ chữ/màu theo trạng thái/viền/padding; G-129 tạo đúng các kit đó để không phải viết lại ở từng khối.
