# Đối chiếu trải nghiệm dựng trang: BIGSPY và Flatsome UX Builder

Ngày quan sát: 05/10/2026
Tham chiếu tại thời điểm quan sát: trang quản trị Home Fresh Mart do chủ website cung cấp, Flatsome 3.20.10, trang mẫu “1-Trang mẫu”. Chỉ quan sát; không cập nhật nội dung trang. Flatsome Classic 3.20.11 là mốc phát hành mới hơn; UX-Themes cũng công bố Site Builder beta riêng ngày 30-09-2026.

## Hướng phát triển cập nhật ngày 11/10/2026

Theo lựa chọn của chủ site, bản 1.18.0 gỡ workspace BIGSPY AI Builder riêng. Luồng chính là Sửa trang WordPress với các khối BIGSPY và thư viện mẫu. Những ghi nhận bên dưới là đối chiếu lịch sử, không phải kế hoạch tái tạo editor UX Builder.

## Kết luận

BIGSPY đã có nền tảng block và pattern để tạo trang, nhưng trải nghiệm hiện tại vẫn là Gutenberg có thêm block BIGSPY. UX Builder tạo cảm giác như một công cụ dựng trang riêng: bản xem trước chiếm trung tâm, danh sách phần tử có hình minh họa, chọn nội dung trực tiếp trên canvas và khu vực chỉnh sửa theo ngữ cảnh.

Mục tiêu phát triển không nên là đổi tên block hoặc tăng số lượng tuỳ chọn. Cần giảm số quyết định kỹ thuật người dùng phải tự đưa ra và giúp họ thấy ngay nơi mình đang sửa.

## Ghi nhận từ UX Builder

- Mở builder trực tiếp từ trang đang sửa; bản xem trước trang thực nằm ở trung tâm.
- Bảng “Add Content” có tìm kiếm, nhóm phần tử và ô minh hoạ trực quan; có tab nhập nội dung và lối vào thư viện Flatsome Studio.
- Các nhóm quan sát được gồm bố cục, nội dung và cửa hàng; người dùng có thể bắt đầu bằng Row, Section, Slider, Grid, Banner, ảnh, chữ, nút, sản phẩm, danh mục, biểu mẫu, FAQ, lời chứng thực và các phần tử khác.
- Phần tử đã đặt trên trang hiện ngay trong bản xem trước, nên người dùng có thể liên hệ thao tác với kết quả.
- Có nút cập nhật dễ nhận biết và điều khiển hoàn tác/làm lại, xem theo thiết bị ở cạnh canvas.
- Với trang mẫu được mở, canvas thể hiện header, lưới sản phẩm, giá và footer thực tế. Đây là ưu điểm trực quan; chất lượng hiển thị trang mẫu cũng cho thấy canvas cần phản hồi nhanh và giữ khả năng đọc khi ảnh hoặc nội dung chưa tải hoàn chỉnh.
- BIGSPY phát triển chuyển động bằng mã riêng, không dùng script, CSS hay thư viện của Flatsome. Hiệu ứng chạy khi phần tử vào khung nhìn, chỉ chạy một lần và tôn trọng cài đặt giảm chuyển động của hệ điều hành.

## BIGSPY hiện có

- Block editor Gutenberg với nhóm block BIGSPY như Section, Row, Column, Banner, Slider, Icon Box, Tabs, Accordion, Posts, Products và các block nội dung khác.
- Inspector Controls cho cài đặt block, thiết kế đáp ứng, animation và màu sắc.
- BIGSPY có 8 kiểu reveal cơ bản, độ trễ theo block, hover riêng cho ảnh/banner/sản phẩm và tùy chọn giảm chuyển động. Bản 1.6.0 bổ sung soft focus, gentle rotate, soft bounce và điều chỉnh thời lượng từng block.
- Quan sát UX Builder: toolbar có nút chạy thử animation; menu phần tử có Options, Duplicate, Copy options và Save as preset. BIGSPY 1.7.0 mở rộng chuyển động hover dùng chung cho mọi block; bản 1.8.0 có nút xem thử hiệu ứng cuộn tại từng block mà không lưu trang. Các thao tác tái sử dụng thiết kế được xây bằng pattern đồng bộ của WordPress.
- Patterns cho section và trang hoàn chỉnh; Header Builder kéo thả; thư viện CSS tích chọn.
- Đây là các nền móng có thể giữ lại. Khoảng cách chính nằm ở cách người dùng tìm, chọn, sắp xếp và chỉnh nội dung, không phải thiếu hoàn toàn các thành phần dựng trang.

## Khoảng cách trải nghiệm cần thu hẹp

| Tác vụ người dùng | UX Builder quan sát được | BIGSPY hiện tại | Hướng cải tiến |
| --- | --- | --- | --- |
| Bắt đầu trang | Thêm phần tử từ bảng có hình, nhóm và tìm kiếm | Tìm block/pattern trong giao diện Gutenberg | Màn hình bắt đầu BIGSPY: chọn loại trang, mẫu trang hoặc dựng từ đầu |
| Thêm section | Chọn loại bố cục ngay trong bảng | Chèn block và lồng block trong editor | Thư viện section có ảnh xem trước; chèn bằng một thao tác |
| Chọn phần cần sửa | Chọn trực tiếp trong canvas | Thường chọn block trong vùng nội dung hoặc List View | Chọn trên canvas, hiện thanh công cụ ngắn cạnh phần tử và thuộc tính phù hợp |
| Sửa bố cục | Bản xem trước trang là bề mặt làm việc | Gutenberg là bề mặt làm việc; bản xem trước kém trực tiếp hơn với block động | Canvas trực tiếp có chọn Section/Row/Column, kéo sắp xếp và viền chọn rõ |
| Tìm thành phần | Danh mục và tìm kiếm hiển thị cùng lúc | Inserter Gutenberg là nơi riêng | Tìm kiếm BIGSPY gồm tên dễ hiểu, ảnh minh hoạ, mô tả ngắn và nhóm theo mục đích |
| Tạo phần hoàn chỉnh | Flatsome Studio và import trong builder | Patterns WordPress | Thư viện mẫu có ảnh, bộ lọc theo ngành và khả năng xem trước trước khi chèn |
| Thiết bị nhỏ | Điều khiển đổi khung xem ở cạnh canvas | Thuộc tính responsive theo block | Thanh xem trước Desktop/Tablet/Mobile cố định; chỉnh giá trị tại thiết bị đang chọn |

## Nguyên tắc thiết kế cho BIGSPY

1. **Dùng từ ngữ theo mục đích:** “Phần giới thiệu”, “Danh sách sản phẩm”, “Nút liên hệ” thay cho việc bắt đầu bằng thuật ngữ Section/Row/Column.
2. **Một thao tác, một kết quả thấy được:** chèn mẫu phải xuất hiện ngay trên trang với nội dung thay thế được.
3. **Thuộc tính theo ngữ cảnh:** khi chọn ảnh chỉ hiện cài đặt ảnh; khi chọn section mới hiện nền, chiều rộng và khoảng cách.
4. **Mặc định đẹp và thích ứng:** phần lớn người dùng không cần đụng tới khoảng cách theo pixel, breakpoint hay CSS.
5. **Nâng dần độ phức tạp:** chế độ cơ bản trước; cài đặt nâng cao mở khi người dùng cần.
6. **An toàn khi thử:** hoàn tác/làm lại rõ ràng, lưu nháp tự động, xem trước và xác nhận trước khi cập nhật trang đang xuất bản.
7. **Không hy sinh khả năng tiếp cận:** mọi thao tác kéo thả phải có cách tương đương bằng nút/keyboard; trạng thái lựa chọn cần có nhãn rõ.

## Lộ trình đề xuất

### Giai đoạn 1 — Điểm vào và thư viện mẫu (đã triển khai trong 1.4.0–1.5.0)

- BIGSPY Studio có lựa chọn tạo trang doanh nghiệp/dịch vụ, cửa hàng (khi WooCommerce bật) hoặc dựng từ đầu.
- Trang bắt đầu tạo bản nháp toàn chiều rộng từ mẫu trang BIGSPY và mở editor; không xuất bản tự động.
- Editor có sidebar BIGSPY Studio với tìm kiếm và nhóm trang hoàn chỉnh, sections và shop; chọn mẫu sẽ chèn các block BIGSPY vào trang hiện tại.
- Người dùng có thể tạo mẫu đồng bộ bằng lệnh **Tạo mẫu** của WordPress và chèn lại từ nhóm **Mẫu đã lưu** trong BIGSPY Studio.
- Thư viện hiện dùng bản minh họa bố cục dựng bằng CSS; bước kế tiếp là thumbnail xem trước thật cho từng mẫu.

### Giai đoạn 2 — Điều khiển trên canvas

- Canvas xem trước ngay trong trình soạn thảo, chọn phần tử bằng click.
- Viền chọn và toolbar nhỏ: sửa, nhân bản, di chuyển, lưu vào thư viện, xoá.
- Kéo thả có điểm thả dễ nhìn; List View đồng bộ với canvas để điều hướng các phần tử lồng nhau.

### Giai đoạn 3 — Chỉnh theo tác vụ

- Inspector chia thành Nội dung, Bố cục, Kiểu dáng, Thiết bị; nhóm nâng cao thu gọn mặc định.
- Chỉnh màu, phông chữ, khoảng cách bằng điều khiển trực quan và preset; vẫn có trường số cho người cần độ chính xác.
- Chế độ responsive và preview gắn với canvas.

### Giai đoạn 4 — Bảo đảm chất lượng

- Autosave bản nháp, hoàn tác/làm lại, khôi phục và cảnh báo rời trang.
- Kiểm tra trang cơ bản: heading, liên kết thiếu, ảnh thiếu alt, tràn ngang trên mobile.
- Hoàn thiện bản dịch tiếng Việt, bàn phím, nhãn accessibility, hiệu năng editor và tài liệu hướng dẫn theo tác vụ.

## Phạm vi so sánh

Đây là đánh giá UX định tính từ một trang mẫu và giao diện thêm phần tử trong phiên quản trị được người dùng mở sẵn. Nó không đánh giá toàn bộ tính năng, mã nguồn, hiệu năng hoặc giấy phép của Flatsome; không dùng tài nguyên độc quyền của Flatsome trong BIGSPY.
