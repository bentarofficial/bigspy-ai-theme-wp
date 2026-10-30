# Roadmap BIGSPY AI Theme WP

Quy tắc phát triển, phát hành và ma trận kiểm tra: xem [DEV-RULES.md](DEV-RULES.md).

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
