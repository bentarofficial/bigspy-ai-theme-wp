# Rà soát ảnh hero, banner và tốc độ tải — G-70

03-11-2026 · Mã BIGSPY `0c10a2e`, phân tích tĩnh; chưa tái hiện, đo LCP/CLS hoặc thử website nháp. Tài liệu này làm rõ G-55 đã có, không tạo task sửa trùng. Đối chiếu theme cài đặt trước đó xem [SOURCE-AUDIT.md](SOURCE-AUDIT.md), không tự nhận đã benchmark đối thủ.

## Điều đã xác nhận trong mã

| Trường hợp | Mã hiện tại | Hệ quả cần kiểm chứng |
| --- | --- | --- |
| `bst/banner` dùng Media ID | `blocks/banner/render.php:47` gọi `wp_get_attachment_image` với `sizes=100vw`; WordPress quyết định thuộc tính tối ưu mặc định. Banner có `heightDesktop/Tablet/Mobile`, và chế độ theo tỷ lệ ảnh có thể dùng metadata. | Không khẳng định mọi Media ID đều lazy. Kiểm tra kích thước và ứng viên ảnh phù hợp với chiều rộng thực tế, cách WordPress và plugin tối ưu can thiệp. |
| Banner dùng URL | `blocks/banner/render.php:50` gắn `loading=lazy`, không xuất width/height cho `img`; lớp banner có thể vẫn đặt được chiều cao hoặc tỷ lệ. | Nếu ảnh đó thực sự là LCP và hiện ngay đầu trang, tải trễ có thể làm chậm; không tự kết luận gây CLS vì wrapper có thể giữ chỗ. Cần đo. |
| `bst/image` dùng URL | `blocks/image/render.php:28` cũng gắn lazy, không có width/height; Media ID đi qua API WordPress tại dòng 25. Khối có ratio và height tùy chọn. | Phân biệt ảnh đầu trang, ảnh trong nội dung và khung có tỷ lệ; kiểm chứng CLS thay vì đoán. |
| Ảnh di động | Banner hỗ trợ Media ID/URL riêng qua `<picture>` và breakpoint `max-width: 549px`, `sizes=100vw` (`render.php:57–65`). | Cần kiểm tra lựa chọn ảnh, network, layout và crop hai viewport; không preload cả hai nguồn dẫn tới tải thừa. |
| Logo header | `inc/header-builder.php:221` đặt `loading=false`, `fetchpriority=high` cho logo. | Có thể cạnh tranh tài nguyên với ảnh hero; chưa chứng minh ảnh hưởng xấu hay cần loại bỏ. Đếm ưu tiên và đo thực tế trước khi thay đổi. |
| Slider | `blocks/slider/render.php` bọc các inner block thành slide. | Ảnh slide đầu và slide ẩn phải được kiểm tra riêng, không gắn high cho tất cả; nguồn nào tải và khi nào phụ thuộc CSS/JS và trình duyệt. |

[WordPress](https://developer.wordpress.org/reference/functions/wp_get_loading_optimization_attributes/) nêu không nên đồng thời đặt `loading=lazy` và `fetchpriority=high`; API được giới thiệu từ WP 6.3, theme yêu cầu WP 6.5 trở lên (`style.css`). [web.dev](https://web.dev/articles/optimize-lcp) khuyến nghị không lazy-load ảnh thực sự là LCP. Cả hai là hướng dẫn kỹ thuật, không chứng minh ảnh banner của mọi trang là LCP.

## Brief chi tiết cho Claude — G-55

1. Trên fixture cố định, so baseline trước và sau: trang banner Media ID; banner URL; image URL; banner trong slider; nhiều banner dưới nếp gấp; ảnh mobile riêng; trang có logo header. Lặp mobile/desktop, thử theme không có plugin tối ưu và có một plugin cache/tối ưu thực cài trong site nháp nếu được phép; ghi phiên bản WP/plugin, trình duyệt, viewport, mạng, cache, số lần đo, phần tử LCP, LCP/CLS và waterfall. Không công bố cải thiện nếu không có đo so sánh.
2. Đề xuất giao diện block dễ hiểu như ưu tiên tải `Tự động / Ảnh đầu trang / Tải trễ`; trước tiên xác minh có thật sự cần control. Mặc định cũ tương thích; giá trị đầu trang chỉ tác động ảnh đã chọn. Làm rõ lưu ở block, preview, frontend và undo. Không dùng AI phán đoán LCP như thực tế đo được; AI chỉ gợi ý khi biết vị trí.
3. URL ngoài: nếu chủ động xác định ảnh đầu trang, tránh `loading=lazy` và chỉ gắn `fetchpriority=high` khi thật sự có lý do; các ảnh còn lại giữ lazy phù hợp. Không tự đánh dấu mọi banner đầu block là LCP. Media ID ưu tiên API WordPress và filter hiện có; không tạo hai bộ tối ưu mâu thuẫn. Kiểm tra logo đã high, plugin đổi thuộc tính, duplicate high và `lazy+high`; không tự thay đổi logo trước khi đo.
4. Tỷ lệ/kích thước: tận dụng metadata Media, `imgRatio`/height hiện có; URL ngoài chỉ dùng width/height do người dùng cung cấp hoặc đã biết, kiểm tra giá trị hợp lệ. Không server-fetch URL tùy ý để lấy kích thước; không bịa kích thước hay override kích thước cũ. Kiểm tra responsive `picture`, tỷ lệ crop và trường hợp kích thước hai nguồn khác nhau.
5. Test đầu trang, cuối trang, slider đầu/ẩn, desktop/mobile, nhiều block, không ảnh, URL lỗi, cache/plugin, chế độ JS tắt và layout trước/sau lưu. Bảo đảm không tải thừa mobile+desktop, không gây thay đổi bố cục/crop/alt/link, không can thiệp ảnh WooCommerce. Ghi kết quả từng fixture và rủi ro còn lại.

**Ranh giới:** theme kiểm soát markup block và lựa chọn ưu tiên; WordPress xử lý Media theo API, plugin cache/CDN có thể tối ưu thêm. Không tích hợp CDN hoặc thay plugin tối ưu khi chưa có lý do. Không tạo công việc sản phẩm ngoài G-55; G-41 tiếp tục đo tài nguyên toàn module.
