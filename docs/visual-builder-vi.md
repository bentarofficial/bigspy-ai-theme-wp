# BIGSPY AI Builder — phiên bản 1.17.0

## Mở trình thiết kế

Trang → Tất cả các trang → **Thiết kế với BIGSPY AI**, hoặc thanh quản trị trên một trang → Sửa trang → Thiết kế với BIGSPY AI.

Đây là workspace riêng sử dụng BlockEditorProvider/BlockList/BlockInspector của WordPress, không nạp UX Builder hoặc mã Flatsome. Nội dung vẫn lưu bằng khối WordPress và có thể mở lại bằng Sửa trang.

- Thêm phần tử và mẫu thiết kế từ bảng bên trái.
- Bấm vào phần tử trong khung thiết kế hoặc cây cấu trúc để chọn và chỉnh.
- Kéo thứ tự các phần tử cùng cấp trong cây cấu trúc; nút lên/xuống hỗ trợ thao tác bằng bàn phím. Việc di chuyển dùng API của WordPress và kiểm tra khóa khối.
- Cấu trúc lồng nhau và toolbar khối vẫn do WordPress quản lý.
- Hoàn tác/làm lại nội dung trong phiên làm việc; lịch sử tối đa 60 bước. Tiêu đề nằm ngoài lịch sử khối.
- Xem mã chuyển sang mã khối WordPress. Rời ô mã để áp dụng trước khi lưu.
- Nút Lưu giữ nguyên trạng thái trang. Trang công khai được cập nhật ngay; bản nháp không tự xuất bản.
- Có cảnh báo khi rời trang với thay đổi chưa lưu. API kiểm tra quyền, post lock và thời gian sửa để tránh ghi đè phiên chỉnh sửa khác. Revision tuân theo cấu hình WordPress.

## Header và footer tự do

Tạo trang dùng làm thiết kế, thêm logo/menu/tìm kiếm hoặc cột/liên hệ/bản quyền, rồi xuất bản trang.

- Tùy chỉnh → Header → Header builder → Header tự do bằng BIGSPY AI Builder.
- Tùy chỉnh → Footer → Footer widgets → Footer thiết kế bằng BIGSPY AI Builder.

Chỉ trang công khai, đã xuất bản, không mật khẩu được sử dụng. Chọn Không để quay về bố cục mặc định. Header tự do không sử dụng thiết lập sticky/transparent của header hàng/cột. Footer tự do thay hai hàng widget; thanh bản quyền vẫn giữ.

## Trang sản phẩm tự do

Thiết kế trang mẫu bằng Section/Row/Column và **Product Part**: tên, ảnh, giá, rating, mô tả ngắn, form mua, meta, tabs/đánh giá, related, upsell. Nhập ID sản phẩm công khai trong từng phần để xem trước ở editor.

Chọn trang mẫu đã xuất bản trong Tùy chỉnh → WooCommerce → trang sản phẩm. Form mua, ảnh và tabs dùng WooCommerce. Chế độ catalog không hiển thị giá/form mua. Nên dùng mỗi form mua/tabs một lần để tránh trùng ID và handler của plugin.

## Các bổ sung khác

- Nút biến thể dạng chữ; BIGSPY 1.17.0 bổ sung cấu hình label/màu/ảnh theo thuộc tính toàn cục và term. Bật trong Tùy chỉnh → WooCommerce → BIGSPY: product cards & page; cấu hình tại Sản phẩm → Thuộc tính → Cấu hình terms.
- Mua ngay: sau khi WooCommerce thêm sản phẩm thành công, chuyển sang checkout; giữ các sản phẩm đang có trong giỏ.
- Điều hướng sản phẩm trước/sau.
- Quick View chỉ xem ảnh/giá/mô tả; mua trên trang sản phẩm đầy đủ. Không công khai sản phẩm nháp, ẩn hoặc có mật khẩu.
- AJAX sorting và form giá WooCommerce chuẩn. Không nhận mọi bộ lọc của plugin bên thứ ba.
- Load more và infinite scroll cho archive WooCommerce classic. Phân trang gốc vẫn giữ. Có fallback điều hướng đầy đủ khi không thể cập nhật an toàn.
- Countdown ngày cố định có múi giờ; không tự khởi động lại theo khách. Shortcode ux_countdown cơ bản được hỗ trợ nếu chưa có plugin khác cung cấp.
- Portfolio tùy chọn: loại nội dung dự án/danh mục, native archive và Query block; cần lưu lại permalink sau lần bật đầu tiên. Chưa có bộ lọc dự án AJAX riêng.
- Mega menu nhiều cột: bật trên mục menu cấp đầu trong Giao diện → Menu; dùng các mục con làm cột, giữ menu mobile mặc định. Chưa có block nội dung riêng trong menu.
- Gallery/video/social/navigation/query sử dụng các khối WordPress sẵn có.

## Giới hạn hiện tại

Đây là bản phát triển đầu của workspace, chưa tương đương toàn bộ UX Builder. Khung thiết bị mô phỏng độ rộng vùng chỉnh sửa, không phải kiểm tra website trong viewport thật; các media query dựa viewport và plugin phải kiểm tra trên frontend. Nội dung Flatsome shortcode cũ vẫn là nội dung legacy, chưa tự biến thành cây native blocks để kéo thả từng phần. Chưa hỗ trợ mọi block editor của plugin bên thứ ba.

Những mục còn cần làm: chuyển đổi legacy có xem trước/hoàn tác, gallery ảnh bổ sung theo biến thể, swatches trên thẻ sản phẩm archive, mega menu có nội dung block, hotspot/Lottie/feed Instagram có cấu hình nguồn, thư viện mẫu có ảnh preview, và adapter Countix dựa phiên bản thực tế. Swatches màu/ảnh cho trang sản phẩm đã có ở 1.17.0.

Nguồn API: https://developer.wordpress.org/block-editor/how-to-guides/platform/custom-block-editor/ và https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/.
