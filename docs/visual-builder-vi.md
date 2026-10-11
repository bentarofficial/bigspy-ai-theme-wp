# Thiết kế trang bằng các khối BIGSPY — phiên bản 1.18.0

## Mở trình chỉnh sửa

Chọn **Trang → Tất cả các trang → Sửa**, hoặc **Sửa trang** trên thanh quản trị khi xem trang. Bản 1.18.0 đã gỡ workspace BIGSPY AI Builder riêng và nút mở workspace đó.

## Dựng trang trong editor WordPress

- Bấm dấu **+**, tìm nhóm hoặc tên khối BIGSPY và chèn vào trang: Section, Row, Column, Banner, Slider, Text, Image, Buttons, Products và các khối khác.
- Mở **Cấu trúc nội dung** để chọn khối lồng nhau, di chuyển và sắp xếp bằng các thao tác WordPress.
- Chọn khối rồi chỉnh thiết lập trong thanh bên, gồm bố cục, responsive, hiển thị và hiệu ứng do khối hỗ trợ.
- Mở sidebar **BIGSPY Studio** để tìm và chèn mẫu trang, section, shop và mẫu đồng bộ đã lưu.
- Dùng **Xem trước**, **Lưu bản nháp**, **Cập nhật/Xuất bản**, hoàn tác và revisions của WordPress. Khi cập nhật trang đã xuất bản, thay đổi được áp dụng lên website.

Các trang đã tạo bằng workspace cũ vẫn lưu bằng block WordPress và có thể tiếp tục sửa trong editor chuẩn. Việc gỡ workspace không chuyển đổi hoặc xóa nội dung trang, shortcode hay thiết lập đã lưu.

## Header và footer bằng trang block

Tạo một trang, thiết kế bằng các khối BIGSPY và WordPress rồi xuất bản.

- Tùy chỉnh → Header → Header builder → **Header tự do bằng khối BIGSPY**.
- Tùy chỉnh → Footer → Footer widgets → **Footer thiết kế bằng khối BIGSPY**.

Chỉ trang công khai, đã xuất bản, không mật khẩu được sử dụng. Chọn Không để quay về bố cục mặc định. Header tự do không sử dụng thiết lập sticky/transparent của header hàng/cột. Footer tự do thay hai hàng widget; thanh bản quyền vẫn giữ.

## Trang mẫu sản phẩm

Trong Sửa trang, thiết kế trang mẫu bằng Section/Row/Column và **Product Part**: tên, ảnh, giá, rating, mô tả ngắn, form mua, meta, tabs/đánh giá, related và upsell. Nhập ID sản phẩm công khai trong từng phần để xem trước.

Chọn trang mẫu đã xuất bản trong Tùy chỉnh → WooCommerce → trang sản phẩm. Form mua, ảnh và tabs dùng WooCommerce. Chế độ catalog không hiển thị giá/form mua. Dùng mỗi form mua/tabs một lần để tránh trùng ID và handler của plugin.

## Phạm vi tương thích

Nội dung Flatsome shortcode cũ tiếp tục dùng lớp tương thích để hiển thị; chưa tự chuyển thành cây block native. Các khối và plugin bên thứ ba dùng editor WordPress và API riêng của chúng. Khung xem trước trong editor không thay thế việc xem website trên thiết bị thực.

Ưu tiên tiếp theo là tối ưu thao tác với các khối BIGSPY, thư viện mẫu có ảnh xem trước và chuyển đổi legacy có bản xem trước/hoàn tác. Xem [roadmap](roadmap-vi.md) để biết phạm vi hiện tại.