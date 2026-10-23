# Đối chiếu Flatsome 3.x + Elementor (site darkorange) với BIGSPY 1.46

Nguồn: file manager của darkorange-deer-931853 (themes/flatsome, flatsome-child, plugins/elementor/includes/widgets), đọc ngày 2026-10-23.

## Flatsome – shortcode/element (53) và extension (12)

| Flatsome | BIGSPY | Trạng thái |
|---|---|---|
| ux_banner, ux_slider, sections, row, text_box, gap | Banner, Slider, Section, Row/Column, Spacer | Có |
| ux_image, ux_gallery, ux_video, lightbox | Image, Gallery, Video, Video button | Có |
| featured_box, ux_image_box, team_members, testimonials, price_table | Icon box, Image box, Team, Testimonial, Price table | Có |
| accordion, tabs, titles_dividers, buttons, messages | Accordion, Tabs, Title, Divider, Button(s), Alert | Có |
| blog_posts, ux_products, product_categories, ux_products_list, product_flip | Posts, Products, Product categories | Có (flip = hover ảnh 2 của card) |
| ux_countdown | Countdown | Có |
| share, follow, google_maps, search, ux_html | Share, Social, Map, header search, Code/HTML | Có |
| **ux_hotspot** | **Image hotspots** | **Mới 1.46** |
| **lightbox (popup)** | **Popup** (nút, #link, trễ, cuộn, exit-intent, tần suất) | **Mới 1.46** |
| **ux_logo** | **Logo showcase** (lưới / dải chạy vô tận) | **Mới 1.46** |
| **ux_sidebar** | **Widget area** | **Mới 1.46** |
| **ux_payment_icons** | **Payment icons** (Visa, MoMo, VNPAY, ZaloPay, VietQR, COD…) | **Mới 1.46** |
| shortcode insert | **Shortcode** block có xem trước | **Mới 1.46** |
| ux_banner_grid, ux_stack | Row/Column + Banner, Style presets | Làm được bằng khối hiện có |
| ux_menu / ux_menu_link / ux_nav, page_header, scroll_to | Header builder, Title bar, neo #anchor + Section navigation | Có (khác cách) |
| ux_lottie, ux_instagram_feed | — | Chưa làm (cần thư viện/API ngoài) |
| ext: infinite-scroll, live-search, swatches, quick-view, variation-images | Có trong Shop/Header options | Có |
| **ext: cookie-notice** | **Cookie notice** | **Mới 1.46** |
| **ext: instant-page** | **Instant page prefetch** | **Mới 1.46** |
| ext: lazy-load | WordPress lazy-load native | Có |
| ext: ajax-add-to-cart, cart-refresh | WooCommerce native + mini cart | Có |

## Elementor widgets (36)

Đã có tương đương: heading, text-editor, image, button, divider, spacer, icon, icon-box, image-box, icon-list, counter, progress, testimonial, tabs, accordion/toggle, alert, social-icons, google-maps, video, image-gallery, image-carousel (Slider), star-rating, html, inner-section (Row lồng), menu-anchor (anchor), read-more.
Mới 1.46: shortcode, sidebar (Widget area). Chưa làm: audio (dùng block Audio của WordPress), rating schema riêng.

## Ưu thế BIGSPY hiện có mà Flatsome/Elementor free không có
Style presets trong từng khối, motion/scroll effects, block design (viền, bóng, bo góc, width), header builder với cài đặt từng phần tử, popup + hotspot native block, palette 34 bảng màu, nút "+" thêm khối phía trên, thanh điều hướng slide.

## Gợi ý tiếp theo
1. Lottie (tự host lottie-light, chỉ tải khi có khối).
2. Mega menu nhiều cột trong header builder.
3. Thư viện mẫu (template library) dạng lưới có ảnh xem trước như Flatsome Studio.
4. Banner grid preset (bố cục mosaic 1 lớn + 2–4 nhỏ).
