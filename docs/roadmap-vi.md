# Roadmap BIGSPY AI Theme WP

**Mốc hiện tại:** 1.17.2 · **Đối chiếu tham chiếu:** Flatsome 3.20.10 trên Home Fresh Mart.
Roadmap này sắp xếp các khoảng trống đã ghi nhận theo tác động đến shop và người làm nội dung. Đây là thứ tự ưu tiên đề xuất, không phải cam kết ngày phát hành.

## Đã có trong 1.17.x

- BIGSPY AI Builder dùng block WordPress, có canvas, danh sách cấu trúc, sắp xếp và thiết kế header/footer/trang sản phẩm.
- Tùy chọn Buy Now, Quick View, Portfolio, countdown, mega menu nhiều cột, load more/infinite scroll cho archive classic.
- Swatch nhãn/màu/ảnh trên trang sản phẩm cho global WooCommerce attributes; mặc định tắt.
- Swatch màu/ảnh tùy chọn trên thẻ sản phẩm trang shop/danh mục; mở trang sản phẩm với giá trị được chọn sẵn.
- Điều hướng AJAX tùy chọn cho widget lọc WooCommerce native: danh mục, thuộc tính, đánh giá, giá và sắp xếp; có history, thông báo trạng thái và tải lại toàn trang khi không thể cập nhật an toàn.

## Ưu tiên tiếp theo

### P0 — Hoàn thiện trải nghiệm shop

1. **Ảnh bổ sung theo biến thể:** cho phép chọn nhiều ảnh cho từng variation trong màn hình sửa sản phẩm; khi khách chọn biến thể, cập nhật gallery và phục hồi gallery gốc khi reset. Tương thích gallery WooCommerce, zoom/lightbox và ảnh đại diện variation.
2. **Kiểm tra tương thích lọc:** xác nhận widget native với phân trang, load more, lịch sử trình duyệt, bộ nhớ đệm và markup phổ biến. Tài liệu hóa cách plugin lọc bên thứ ba có thể nối sự kiện hoặc dùng điều hướng gốc; không giả định mọi plugin có cùng API.
3. **Tùy chọn checkout còn thiếu:** xem xét hiển thị ngưỡng miễn phí vận chuyển và vị trí coupon/nội dung bổ sung như lựa chọn giao diện, không thay đổi quy tắc tính phí hay thứ tự nghiệp vụ WooCommerce.

### P1 — Dựng trang và nội dung

1. **Di chuyển nội dung UX Builder cũ:** công cụ xem trước các shortcode Flatsome nhận diện được, cho chọn phần cần chuyển sang block native, giữ bản gốc để hoàn tác và báo rõ shortcode/plugin không hỗ trợ. Không tự chuyển hoặc xóa nội dung khi chưa có bản xem trước.
2. **Mega menu có block:** bổ sung vùng nội dung tùy chọn cho từng mục menu cấp cao; hỗ trợ điều khiển bằng bàn phím, focus, mobile drawer và fallback về submenu chuẩn.
3. **Tìm kiếm linh hoạt hơn:** bổ sung bố cục hàng/masonry/slider và tùy chọn sắp xếp sản phẩm; giữ cấu hình nguồn kết quả, debounce, SKU/tag và xử lý phản hồi cũ.
4. **Mẫu có ảnh xem trước:** thêm hình preview cho pattern và trang mẫu, giữ nguyên nội dung block native và hiển thị nhẹ trong editor.

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
