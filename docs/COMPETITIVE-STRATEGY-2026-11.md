# Đối chiếu tuỳ chọn với đối thủ và chiến lược “theme khó thay thế” — G-140

Ngày 11-11-2026. Bổ sung cho [SOURCE-AUDIT.md](SOURCE-AUDIT.md) (G-53, đọc kiến trúc 34 tệp) và [THEME-COMPARISON.md](THEME-COMPARISON.md) (G-36). Hai tài liệu đó chưa đọc “từng control/widget”; đợt này đo đúng phần đó. Đề xuất chờ owner duyệt, chưa lập trình.

## 1. Cách lấy dữ liệu và giới hạn

- Site đối chiếu do owner chỉ định (Flatsome Child đang chạy; Astra 4.14.1, GeneratePress, Hello, Neve, OceanWP nằm trong thư mục theme; Elementor 4.3.6 bản miễn phí, không có Elementor Pro).
- **Elementor:** mở trình sửa Elementor của một trang mẫu, đọc cấu hình control của 197 widget từ bộ nhớ trình sửa, rồi thoát không lưu.
- **Flatsome, Astra:** đọc tệp nguồn qua trình sửa theme của WordPress bằng yêu cầu GET; không lưu, không đổi cấu hình.
- Chỉ ghi lại **số đếm, tên nhóm và tên tính năng**; không chép mã đối thủ vào repo.
- Giới hạn: Flatsome chỉ đếm được 28 tệp shortcode (lệnh hết thời gian trước khi xong danh sách); số của Flatsome là số lần khai báo `'type'` trong tệp, chưa tính các nhóm tuỳ chọn dùng chung được nạp thêm. Widget Pro của Elementor chỉ thấy tên, không thấy control. Chưa đo tốc độ, chưa dựng cùng một trang trên từng theme. Customizer của Astra/Neve/OceanWP/GeneratePress không đếm được vì không phải theme đang kích hoạt.

## 2. Số liệu

### Tuỳ chọn riêng của từng thành phần

Elementor: số control không tính bản sao theo thiết bị và không tính tab Nâng cao dùng chung. BIGSPY: số thuộc tính trong `block.json`.

| Thành phần | Elementor 4.3.6 | Flatsome 3.20 | BIGSPY 1.90 |
| --- | --- | --- | --- |
| Tabs | 42 (bản lồng khối: 206) | 10 | 8 |
| Accordion | 46 (bản lồng khối: 197) | 6 | 16 |
| Button | 104 | 16 | 32 |
| Divider | 41 | 5 | 10 |
| Icon box | 56 | 12 | 14 |
| Image box | 73 | 5 | 16 |
| Image | 53 | 17 | 24 |
| Testimonial | 53 | — | 8 |
| Counter | 48 | — | 14 |
| Progress | 39 | — | 9 |
| Alert | 40 | — | 9 |
| Icon list | 34 | — | 10 |
| Star rating | 25 | — | 8 |
| Heading | 26 | 10 (title) | 19 |
| Text | 41 | 2 | 16 |
| Video | 54 | — | 10 |
| Social icons | 19 | — | 20 |
| Google Maps | 16 | 20 | 12 |
| Team member | (Pro) | 29 | 16 |

Cách Elementor đạt số lớn: mỗi **phần** của widget có một nhóm ở tab Kiểu. Ví dụ Testimonial có nhóm Nội dung 13, Hình ảnh 5, Tên 13, Chức danh 13; Tabs có 37 control kiểu; bản Tabs lồng khối có Tabs 123, Tiêu đề 28, Icon 6, Nội dung 41. Mỗi nhóm lặp lại cùng một bộ: chữ (font, cỡ, đậm, transform, kiểu, gạch, line-height, letter-spacing, word-spacing), viền chữ, màu theo trạng thái thường/hover/đang chọn, viền, bo góc, padding. Đây đúng là hướng “kit theo phần” của G-129.

### Lớp dùng chung cho mọi thành phần

| | Elementor (tab Nâng cao) | BIGSPY (Design + Typography + chuyển động) |
| --- | --- | --- |
| Tổng số control | 176 | khoảng 60 |
| Nhóm | Bố cục 30, Hiệu ứng động 7, Biến đổi 42, Nền 69, Viền 15, Mặt nạ 9, Ẩn theo thiết bị 2, Thuộc tính tuỳ chỉnh 1, CSS tuỳ chỉnh 1 | margin/padding, width, position, nền một màu, viền một giá trị, bo góc, bóng 5 mức, hover, z-index, chữ, 32 hiệu ứng xuất hiện, 10 hiệu ứng hover |
| Điểm ngắt | 7 mức (thêm mobile lớn, tablet lớn, laptop, màn rộng) | 3 mức |

BIGSPY thiếu so với lớp này: biến đổi (xoay, lệch, lật, dịch) ở cả trạng thái thường lẫn hover, nền gradient/ảnh/video/slideshow, mặt nạ hình, thuộc tính HTML tuỳ chỉnh, CSS riêng cho từng khối.

### Số lượng thành phần

- Elementor miễn phí: khoảng 30 widget thật; khoảng 95 widget hiện tên nhưng khoá sau Pro (Loop, Form, Popup, Theme Builder, WooCommerce, Mega Menu, Flip Box, Hotspot, Lottie, Price Table, Countdown…).
- Flatsome: hơn 28 shortcode trong UX Builder.
- BIGSPY: 81 khối trong `blocks/` cộng 2 khối đăng ký bằng PHP. Nhiều thứ Elementor thu phí thì BIGSPY đã có sẵn: form, popup, flip box, hotspot, lottie, price table, mega menu, countdown, product part, theme builder.

**Kết luận từ số liệu:** về số lượng khối BIGSPY đã vượt bản miễn phí của Elementor và vượt Flatsome. Về độ sâu tuỳ chọn, BIGSPY ngang hoặc hơn Flatsome ở phần lớn thành phần nhưng **kém Elementor 3–6 lần** ở các khối nhỏ. Khoảng cách cần đóng là với Elementor.

### Hướng AI của đối thủ

- **Astra 4.14.1** có thư mục `inc/abilities/`: đăng ký hơn 70 “ability” đọc/ghi (bảng màu, font, header/footer builder, blog, sidebar, hiệu năng…) qua Abilities API của WordPress và khai báo một máy chủ MCP. Nghĩa là bất kỳ AI bên ngoài nào (Claude, ChatGPT…) cũng điều khiển được cài đặt Astra, không cần AI riêng của Astra.
- **Elementor 4** đang bật các thử nghiệm atomic: phần tử mới (`e-tabs`, `e-accordion`, `e-form`, `e-component`…), class dùng chung và biến thiết kế. Hướng đi là hệ thống thiết kế có cấu trúc để cả người và AI cùng dùng.
- **BIGSPY** có AI Designer riêng (58+ công cụ, luật block, duyệt thao tác, hoàn tác) nhưng chưa có Abilities/MCP: AI ngoài chưa điều khiển được theme. Đã grep, không có `wp_register_ability` hay `mcp_adapter` trong mã theme.

## 3. Những gì nên học theo

Học **ý tưởng và cách tổ chức**, không chép mã: theme là GPLv2, Elementor là GPLv3, tài nguyên Flatsome có giấy phép riêng; `roadmap-vi.md` cũng đã cấm sao chép mã, asset, thương hiệu.

| Nguồn | Nên học | Đưa vào |
| --- | --- | --- |
| Elementor | Tab Kiểu chia theo từng phần, mỗi phần cùng một bộ chữ/màu/viền/padding với trạng thái thường/hover/đang chọn | G-129, G-132 → G-138 |
| Elementor | Lớp Nâng cao: biến đổi, nền nhiều loại, mặt nạ, thuộc tính tuỳ chỉnh, CSS theo khối | G-130 (đã bổ sung) |
| Elementor | Thêm điểm ngắt tuỳ chọn (laptop, màn rộng) | G-130, phần đánh giá |
| Elementor | Kiểu icon “trơn / nền đặc / có khung”, viền chữ, word-spacing, đường kẻ dạng hoạ tiết | G-129 `IconPick`, G-132 |
| Elementor | Tabs/Accordion lồng khối với thanh tab chỉnh sâu | G-133 (BIGSPY đã lồng khối sẵn) |
| Elementor 4 | Class dùng chung, biến thiết kế, component có ghi đè | G-39 |
| Elementor Pro | Loop, dynamic tags, điều kiện hiển thị | G-37, G-38 |
| Flatsome | Thư mục preset cho từng phần tử, chọn bằng hình; ít tuỳ chọn nhưng dễ hiểu | G-131 |
| Astra | Abilities API + MCP cho mọi cài đặt; vòng đời bảng màu; command palette | G-141, G-39 |
| Neve | Magic Tags có ngữ cảnh và trả rỗng an toàn | G-37 |
| GeneratePress, Hello | Chỉ nạp script khi tính năng bật; công tắc bỏ từng phần theme | G-41, G-111 |
| OceanWP | Checkout nhiều bước, thanh mua nổi | khảo sát riêng, chưa chốt |

## 4. Những gì BIGSPY có thể làm tốt hơn

1. **Khối gốc WordPress thay vì trình dựng riêng.** Nội dung là block chuẩn: không khoá người dùng, không lồng nhiều lớp `div`, sửa ngay trong trình soạn thảo WordPress. Elementor và UX Builder đều giữ nội dung trong định dạng riêng.
2. **Không khoá tính năng sau bản trả phí.** Form, popup, theme builder, product builder, mega menu, chuyển động đã nằm trong theme.
3. **Tuỳ chọn sâu nhưng có lối tắt.** Elementor có hàng trăm control nhưng người dùng phải tự chỉnh từng cái. BIGSPY nên có cả độ sâu (G-129) lẫn ba lối tắt: chọn kiểu bằng hình, kiểu dựng sẵn một chạm (G-131), và AI chỉnh hộ. Đây là chỗ vượt được chứ không chỉ đuổi kịp.
4. **CSS theo trang.** Chỉ nạp CSS của khối trang đó dùng; cần giữ ưu thế này khi số kiểu tăng (G-108, G-111).
5. **AI có kỷ luật.** Luật block (AI chỉ dựng bằng khối sửa tay được, từ chối khi dưới 90% sửa được), duyệt thao tác, hoàn tác. Chưa đối thủ nào trong nhóm đã đọc có ràng buộc tương đương trong mã.
6. **Thị trường Việt Nam:** tiếng Việt, Zalo, cổng thanh toán nội địa, form báo giá. Cần bù phần bản dịch còn thiếu (G-121).

Chưa có số đo nào chứng minh BIGSPY nhanh hơn hay “mạnh nhất”. Muốn nói điều đó phải có G-145.

## 5. Vì sao theme vẫn khó thay thế khi có AI

AI viết HTML/CSS cho một trang rất nhanh, nên giá trị của theme không còn nằm ở “có sẵn giao diện”. Thứ AI không tự tạo ra và không tự duy trì được là **hệ thống** mà trang chạy trên đó. Đề xuất định vị: *theme là thân máy, AI là người lái thay được*.

| Trụ cột | Vì sao AI không thay được | Việc cần làm |
| --- | --- | --- |
| Hệ thống thiết kế có cấu trúc | AI sinh mã tự do thì mỗi trang một kiểu, người dùng không sửa tay được. Khối + tuỳ chọn + token là “ngôn ngữ” chung để AI viết vào và người sửa tiếp | G-129 → G-139, G-39 |
| Mỗi tuỳ chọn là một năng lực của AI | AI Designer chỉ được đặt thuộc tính của khối. Khối có 8 tuỳ chọn thì AI chỉ làm được 8 việc; có 60 thì AI làm được 60 việc mà vẫn sửa tay được. Mở rộng tuỳ chọn chính là nâng AI | G-129 → G-139 |
| Mở cho mọi AI | Người dùng sẽ có AI riêng (Claude, ChatGPT, Gemini). Theme nào cho AI ngoài điều khiển an toàn thì được chọn; AI tích hợp sẵn chỉ là một lựa chọn | G-141 |
| Kiểm tra chất lượng tự động | AI không tự biết trang vỡ trên di động, tương phản kém, ảnh hero tải chậm. Bộ quy tắc kiểm tra dùng chung cho người và AI là thứ tích luỹ theo thời gian | G-142 |
| Phần chạy thật | Hiệu năng, bảo mật, cập nhật, tương thích WooCommerce/SEO/cache, form, đặt chỗ, listing: mã phải được bảo trì, không sinh một lần là xong | G-107 → G-124, G-57 → G-63 |
| Kho kiểu và mẫu | Preset, pattern, công thức section do người thật tinh chỉnh; AI chọn và điền, không phải nghĩ lại từ đầu | G-131, G-49 đã có |

## 6. Việc mới đề xuất

**G-141 · Abilities API và MCP cho BIGSPY.** Đăng ký ability đọc/ghi cho: cài đặt theme (màu, font, header, footer, shop), danh mục khối kèm kiểu dáng và preset, đọc/sửa trang bằng đúng các công cụ của AI Designer. Dùng lại lớp quyền, luật block, duyệt thao tác và hoàn tác hiện có; không mở đường ghi nào vòng qua chúng. Chỉ đăng ký khi WordPress có `wp_register_ability` (theme hỗ trợ từ 6.5 nên phải kiểm tra hàm tồn tại). Mặc định tắt, bật trong trang Tính năng. Cần xác minh phiên bản WordPress tối thiểu và cách hoạt động của MCP adapter trước khi đặc tả chi tiết.

**G-142 · Kiểm tra thiết kế dùng chung cho người và AI.** Mở rộng Auto Detect: tương phản chữ/nền, thứ tự heading, vùng bấm trên di động, tràn ngang ở 375px, ảnh thiếu alt, ảnh đầu trang chưa ưu tiên tải, quá nhiều font/cỡ chữ lệch token. Hiện trong editor dạng danh sách có nút “Sửa”; AI Designer gọi cùng bộ kiểm tra sau mỗi lần ghi và phải báo kết quả.

**G-143 · Khối còn thiếu so với Elementor.** Kiểm tra trước xem theme đã có dưới dạng khác chưa, rồi mới làm: mục lục, breadcrumb, trích dẫn, kêu gọi hành động, bảng giá dạng thực đơn, chữ theo đường cong, ngăn kéo (off-canvas), danh sách phát video, thanh tiến độ đọc, thông tin bài/hộp tác giả/điều hướng bài, đánh giá, đăng nhập, sơ đồ trang, trang link-in-bio. Khối tìm kiếm đã nằm trong G-134. Mỗi khối mới phải đạt mức tối thiểu ở BLOCK-OPTIONS-SPEC mục 2.

**G-145 · Fixture so sánh với đối thủ.** Dựng cùng một trang (hero, lưới tính năng, tabs, bảng giá, form, lưới bài) trên site đối chiếu bằng BIGSPY, Elementor + Hello, Flatsome, Astra; đo byte, số request, số node DOM, LCP/CLS, và số thao tác để đổi một kiểu. Cần owner đồng ý vì phải đổi theme đang kích hoạt trên site đối chiếu. Chỉ sau mục này mới công bố so sánh.

**Bổ sung vào việc đã có:** G-130 thêm mặt nạ hình, CSS riêng theo khối, thuộc tính HTML tuỳ chỉnh, biến đổi ở trạng thái hover, đánh giá thêm điểm ngắt; BLOCK-OPTIONS-SPEC thêm bảng mốc Elementor làm mức tham chiếu.

## 7. Thứ tự đề xuất

1. G-129 → G-131 (nền tảng tuỳ chọn) rồi G-134, G-133: thu hẹp khoảng cách lớn nhất với Elementor.
2. G-141: nhỏ hơn nhiều so với đợt tuỳ chọn, và là điểm đối thủ (Astra) đã đi trước.
3. Các nhóm khối còn lại (G-132, G-135 → G-138) xen với G-37/G-38/G-39.
4. G-142, G-143.
5. G-145 khi đã có đủ để so.
