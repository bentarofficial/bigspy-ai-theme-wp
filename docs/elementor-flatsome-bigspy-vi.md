# Elementor + Flatsome → BIGSPY: nên học gì

Ngày: 22/10/2026 · Nguồn: site Home Fresh Mart (chỉ đọc qua WP File Manager) — Elementor **4.3.5 bản miễn phí** (không có Pro), Flatsome 3.20.x; BIGSPY 1.22.0 trong repo.

## Kết luận ngắn

- **Flatsome mạnh ở phần tử dựng sẵn cho shop/landing** (banner + text box, slider, lightbox, ux_products, hotspot, team, price table…) và ở **option sâu cho từng phần tử**.
- **Elementor mạnh ở hệ thống thiết kế dùng chung**: tab Advanced giống nhau cho mọi widget (vị trí tuyệt đối, mask, transform, hover), **Global colors/typography/classes/variables**, **Interactions** (kích hoạt khi xuất hiện/tải/hover/click), Theme Style cho nút/form/ảnh, thư viện shape divider phong phú, Element Manager tắt widget không dùng, Element Cache.
- BIGSPY nên lấy **danh mục phần tử** của Flatsome và **kiến trúc thiết kế dùng chung** của Elementor, nhưng giữ lợi thế riêng: block editor gốc của WordPress (không khoá nội dung vào shortcode/JSON builder), ít CSS/JS, render bằng PHP.

## 1. Phần tử (widget/element)

| Phần tử | Elementor (free) | Flatsome | BIGSPY 1.22 | Nên làm |
| --- | --- | --- | --- | --- |
| Section / Container | Container flex + grid, Inner section | Section, Row, Col, col_grid | Section, Row, Column | Thêm **chế độ Grid** cho Row (đặt ô theo cột/hàng như Elementor) |
| Heading / Title | Heading | Title (nhiều style, icon, link) | Heading, Title | Đủ; thêm icon cho Title |
| Text | Text editor | Text | Text | Đủ |
| Button | Button | Button (nhiều style, depth) | Button (1.21) | Đủ |
| Image | Image | ux_image (lightbox, overlay, parallax) | Image (1.21) | Thêm **parallax** |
| Image box / Icon box | Image box, Icon box, Icon | featured_box, ux_image_box | Icon box | Thêm biến thể **Image box** (ảnh trên/trái + chữ + link) |
| Icon list | Icon list | (text) | — | **Thêm Icon list** — rất hay dùng cho dịch vụ/ưu điểm |
| Gallery / Carousel | Image gallery, Image carousel | ux_gallery, ux_slider | Gallery (1.21), Slider | Đủ |
| Banner | — | ux_banner + text_box, banner_grid | Banner | Thêm **Banner grid** |
| Video | Video | ux_video, video_button | Video (1.21) | Thêm **nút video popup** |
| Tabs / Accordion | **Nested** tabs/accordion (chứa mọi phần tử) | tabgroup, accordion | Tabs, Accordion (đã chứa block con) | Thêm tabs dọc, mở bằng hover |
| Counter / Progress | Counter, Progress bar | — | Counter | **Thêm Progress bar** |
| Testimonial / Rating | Testimonial, Star rating, Rating | testimonial | Testimonial | Thêm **Star rating** độc lập |
| Alert | Alert | message_box | — | **Thêm Message box/Alert** |
| Map | Google Maps | map (lat/long, hộp nội dung, màu) | Map | Thêm lat/long + hộp nội dung |
| Social | Social icons | follow, share | Social follow (1.21) | Thêm **Share** |
| Team / Price table | (Pro) | team_member, price_table | — | **Thêm cả hai** |
| Hotspot / Lottie | (Pro) | ux_hotspot, ux_lottie | — | Hotspot ảnh sản phẩm: P2 |
| Menu anchor / Scroll to | Menu anchor | scroll_to | (anchor của block) | Đủ |
| Divider / Spacer | Divider (có icon/chữ giữa), Spacer | divider, gap | Divider, Spacer | Divider có chữ/icon ở giữa |
| Products / Categories / Posts | (Pro/Woo) | ux_products, categories, blog_posts | Products, Categories, Posts | Đủ |
| Countdown | (Pro) | ux_countdown | Countdown | Đủ |
| Floating buttons | **Floating buttons + floating bars** | (plugin ngoài) | Floating contact (1.22) | Thêm **Floating bar** (thanh thông báo/khuyến mãi trên đầu) |
| Link in bio | Link in bio page | — | — | P2: mẫu trang link-in-bio |
| Form | (Pro / atomic form) | contactform7 | — | Dùng CF7/WPForms; không tự viết form |

## 2. Tính năng dùng chung — đây là phần đáng học nhất từ Elementor

| Tính năng | Elementor | Flatsome | BIGSPY | Đề xuất |
| --- | --- | --- | --- | --- |
| Margin/padding theo thiết bị | Có | Có | **Có (Design, 1.21)** | — |
| Nền, viền, bo góc, bóng | Có, kèm **trạng thái hover** riêng | Có | Có (Design); hover chỉ có bóng | Thêm **màu nền/viền/bo góc khi hover** |
| Vị trí tuyệt đối/cố định + toạ độ, z-index | Có (`_position`, `_offset_x/y`) | position_x/y trong banner | Chỉ z-index | **Thêm Position: absolute/fixed + offset** theo thiết bị — để đặt badge, hình trang trí |
| Độ rộng phần tử (auto/full/custom) | Có | width | max-width | Thêm width auto/inline |
| Mask hình (tròn, blob, SVG) | Có | masks | — | P1: mask cho Image/Section |
| Transform (xoay, scale, dịch) + hover | Có | rotate/scale ở text_box | — | P1 |
| Animation khi cuộn | Có | animate | Có (11 hiệu ứng) | — |
| **Interactions** (trigger viewport/load/hover/click, lặp, easing) | Có (4.x) | — | — | P1: mở rộng animation hiện có thêm trigger hover/click, lặp |
| Ẩn theo thiết bị | Có | visibility | Có | — |
| ID + class CSS | Có | class | Có (anchor + class của WP) | — |
| **Global colors / typography** | Có (Kit) | Có (Customizer) | Màu + font trong Customizer | Đưa vào **bảng màu và kiểu chữ của block editor** (đã có theme.json một phần) |
| **Global classes / Variables** (4.x) | Có | — | Thư viện CSS preset | P1: **lưu kiểu Design thành preset** dùng lại ("Save as preset" của Flatsome = Global class của Elementor) |
| Theme Style: nút, form, ảnh | Có | Có | Nút trong Customizer | Thêm style form field + ảnh mặc định |
| Lightbox toàn site | Settings lightbox | Có | Có (1.21, nhẹ) | Thêm tùy chọn màu nền, tắt/bật toàn site |
| Shape dividers | 31 kiểu (kèm bản "negative") | dividers | 9 kiểu (1.21) | Thêm clouds, drops, book, pyramids, split, waves-pattern, opacity-fan và **chế độ negative** |
| Page transitions | Có | — | — | P2 (nặng, ít giá trị SEO) |
| Element Manager (tắt widget không dùng) | Có | — | — | P1: **tắt khối BIGSPY không dùng** để inserter gọn |
| Element cache | Có | — | Render PHP trực tiếp | Không cần ở quy mô hiện tại |
| Favorites widget | Có | — | — | Dùng tính năng có sẵn của block editor |
| Copy/paste style, history, notes | Có | Copy options | Studio có copy style | Đủ / dùng undo của WP |

## 3. Không nên sao chép

- Dữ liệu trang khoá vào builder (Elementor JSON, shortcode `[ux_*]` của Flatsome) → BIGSPY giữ block markup chuẩn WordPress.
- Tải CSS/JS cho mọi widget trên mọi trang. BIGSPY chỉ in CSS Design khi có dùng, nên tiếp tục tách asset theo khối.
- Mỗi widget một bộ control khác nhau. Gom về bảng Design/Interactions dùng chung như đã bắt đầu ở 1.21.

## 4. Trải nghiệm kéo thả và dễ dùng (yêu cầu 22/10)

Nguyên tắc: thao tác trực tiếp trên khung soạn thảo trước, thanh bên chỉ để tinh chỉnh; mọi tùy chọn theo thiết bị đi theo chế độ xem trước Desktop/Tablet/Mobile của editor.

Đã làm trong 1.23.0:
- Kéo mép phải Column để đổi độ rộng (bám 12 cột, hiện nhãn "6/12"); đang xem trước Tablet/Mobile thì chỉnh độ rộng thiết bị đó.
- Kéo mép dưới Spacer để đổi chiều cao (theo thiết bị đang xem).
- Bảng Design nằm ở tab **Styles** của thanh bên (giống Content/Style/Advanced của Elementor); tab thiết bị trong bảng đồng bộ với chế độ xem trước của editor.
- Icon list sửa chữ ngay trên canvas, bấm icon để đổi, Enter thêm dòng, nút thêm/xóa; Star rating bấm vào sao để chấm (nửa sao); Image box/Message box/Progress gõ chữ trực tiếp.
- Ảnh xem trước (example) cho các khối mới trong inserter; ẩn khối không dùng tại Customizer → Advanced → BIGSPY blocks.

Nên làm tiếp: kéo padding/margin trực tiếp trên canvas (tay kéo cạnh như Elementor), nhân bản/di chuyển nhanh bằng thanh nổi, thư viện section dựng sẵn có ảnh xem trước theo ngành, copy/paste style giữa các khối.

## 5. Tình trạng tới 1.29.0 (22/10/2026)

Đã làm: Design (spacing responsive, nền, viền, bóng, hover, position, width, copy/paste), Image/Button/Text/Section nâng cao, Gallery, Video, Video popup, Social, Share, Icon list, Image box, Message box, Progress, Star rating, Form (CF7/WPForms, 8 mẫu, thanh công cụ trên khối), Team member, Price table, Slider (fade, peek, progress, mũi tên/chấm), Accordion (icon, số, màu, mượt), Tabs (icon, hover, stretch, màu), Posts (magazine, 8 kiểu thẻ, badge), Products/Categories nhiều kiểu, Map (tọa độ, hộp thông tin, trắng đen), Divider có chữ/icon, 15 shape divider, parallax, 14 nền Section, 10 bộ màu, thanh thông báo, nút liên hệ nổi, ẩn khối không dùng, kéo cột/spacer.

Còn lại: Interactions theo click/hover cho mọi khối, mask hình, Row dạng Grid tự do, Hotspot, Lottie, Banner grid, mẫu trang link-in-bio, thư viện section theo ngành có ảnh xem trước.

## 6. Lộ trình đề xuất

**P0** — 1.23.0 đã làm mục 1, 2, 4; mục 3 (preset Design) còn lại.

**P0 (làm tiếp ngay, dùng hằng ngày)**
1. Design: thêm trạng thái hover (nền, chữ, viền, bo góc, dịch/scale) và **Position absolute/fixed + offset** theo thiết bị.
2. Khối mới: **Icon list**, **Image box**, **Message box/Alert**, **Progress bar**, **Star rating**.
3. **Lưu kiểu Design thành preset** và áp cho khối khác (Global class).
4. **Element Manager**: Customizer/trang Dashboard bật/tắt từng khối BIGSPY.

**P1**
5. Interactions: trigger hover/click/load, lặp, easing; parallax cho Image/Section.
6. Mask và transform; shape divider bổ sung + negative.
7. Row chế độ Grid; Tabs dọc/hover; Map lat/long + hộp nội dung; Divider có chữ/icon.
8. Team member, Price table, Banner grid, nút video popup, Share; Floating bar (thanh thông báo).

**P2**
9. Hotspot, Lottie, mẫu Link in bio, page transitions.

Mỗi mục phải có kiểm tra trong WordPress thật (editor + frontend desktop/mobile) trước khi coi là xong.
