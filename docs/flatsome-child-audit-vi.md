# Đối chiếu theme cha và theme con đang kích hoạt

Ngày: 06/10/2026. Nguồn đọc: WordPress Theme File Editor và WP File Manager trên site tham chiếu do chủ site mở.

## Vì sao cần xem cả hai

Flatsome Child khai báo `Template: flatsome`, phiên bản riêng 3.0. Nó kế thừa tính năng và template của Flatsome, đồng thời thêm CSS/PHP của chủ website. Theme cha không đại diện đầy đủ cho trải nghiệm website này. Theme File Editor liệt kê hai tệp mã của child: `style.css` và `functions.php`; đã đọc toàn bộ cả hai, không sửa site tham chiếu. File Manager xác nhận thư mục child chỉ có ba tệp: hai tệp mã trên và `screenshot.png`, không có thư mục template ghi đè.

## Kết quả đọc mã child

| Nhóm | Có trong child | Cách áp dụng vào BIGSPY |
| --- | --- | --- |
| Flash sale | Gradient đỏ/vàng, khoảng cách, thẻ nền trắng, đồng hồ và icon riêng, responsive 550/820px | Thuộc nội dung/brand của Home Fresh Mart và plugin countdown. BIGSPY đã có Section/Products/animation; cần dựng mẫu promo riêng nếu dùng, không bật CSS toàn site |
| Tiêu đề sản phẩm | Cắt 3 dòng, chiều cao tối thiểu | 1.14.0 thêm CSS tích chọn cho `.bs-product__title`, dùng biến số dòng có sẵn. Tránh selector link toàn cục vốn có thể ảnh hưởng ảnh sản phẩm |
| Nút giỏ hàng | Căn giữa, điều chỉnh padding | BIGSPY đã có cấu hình và nút thẻ sản phẩm riêng; không thêm bản trùng |
| Coupon | Nút màu cam, ẩn coupon toggle | 1.14.0 thêm màu nút theo thương hiệu. Giữ toggle để khách vẫn có đường mở form coupon |
| Countix: chiều cao ảnh | JS đo ảnh để đặt chiều cao wrapper countdown, chạy lúc load/resize và hai timeout | Bản vá cho HTML Flatsome + Countix. Chưa chuyển vì markup BIGSPY khác; cần quan sát plugin trên BIGSPY trước khi viết adapter |
| Countix: checkout | CSS ẩn countdown layout 1 trong cart/checkout | 1.14.0 thêm tác vụ tùy chọn, phạm vi đúng hai trang, không thay đổi giá hay thời hạn |
| Countix: giá | Ghi đè `jQuery.fn.html` để ngăn thay HTML `.vi-sctv-price` | Chưa chuyển. Đây là can thiệp toàn bộ hàm jQuery; nên sửa bằng cấu hình/hook của plugin sau khi xác định lỗi và phiên bản plugin |

## Theme cha và BIGSPY

Đã đọc danh sách module của Flatsome, nhóm Advanced Options, `function-maintenance.php` và template single-page navigation. 1.14.0 bổ sung các ý tưởng bằng mã riêng: trang 404 từ page đã xuất bản, bảo trì 503 có admin notice, điều hướng anchor theo section. BIGSPY dùng page/block chuẩn, không cần UX Blocks hoặc chạy UX Builder.

Các phần vẫn thiếu: product template builder, lọc archive AJAX, infinite scroll, variation swatches/gallery mở rộng, portfolio/feed tích hợp. Việc đối chiếu này không có nghĩa là đã sao chép toàn bộ hoặc đạt đầy đủ tính năng của Flatsome.
