# Nghiên cứu backend booking và adapter — G-60

03-11-2026. Đọc tài liệu chính thức và mã BIGSPY tại `d4a7c94`; chưa đọc mã plugin booking, chưa mua/cài plugin, chưa thử API hoặc đặt chỗ thật. Đây là quyết định **ca thử đầu và ứng viên**, không phải cam kết hỗ trợ hay lựa chọn mua sản phẩm. [Roadmap form/booking](FORMS-BOOKING-ROADMAP.md) vẫn là đặc tả tổng.

## Đối chiếu khả năng

| Nguồn | Phù hợp được tài liệu mô tả | Hạn chế và điều chưa chứng minh |
| --- | --- | --- |
| [WooCommerce Bookings](https://woocommerce.com/document/introduction-to-woocommerce-bookings/) | Extension bán lịch hẹn/thuê theo thời gian, có availability, persons, giá và resource. [Tài liệu tài nguyên](https://woocommerce.com/document/introduction-to-woocommerce-bookings/woocommerce-bookings-store-manager-guide/resources-in-bookings/) nói mỗi booking dùng một resource chọn hoặc tự gán, và tài nguyên có số lượng dùng chung. | Plugin trả phí, chưa xác minh license/ngân sách hoặc phiên bản site. Không coi một booking dùng một resource là chứng minh đặt đồng thời xe và tài xế. Không chứng minh bán ghế theo từng chặng. |
| [Bookings REST developer docs](https://woocommerce.com/documentation/products/extensions/woocommerce-bookings/developer-docs-bookings/) | Tài liệu API dành cho Bookings 1.14+ có mục slots với lọc product/category. [Hướng dẫn tạo booking bằng PHP](https://woocommerce.com/document/introduction-to-woocommerce-bookings/creating-bookings-programatically/) mô tả `create_wc_booking` và tham số `exact`. | Chưa xác minh route, quyền, bản plugin cụ thể, giá cuối, tính nguyên tử, giữ chỗ, idempotency hay frontend handoff. Không đưa REST key ra browser; không mặc định API quản trị là API khách công khai. |
| [Woo Bookings Availability](https://woocommerce.com/document/bookings-availability/) | Add-on hiển thị lịch/schedule bằng block Gutenberg. | Là add-on mua riêng; tài liệu nói có trường hợp khách được chuyển tới trang sản phẩm chọn ngày/giờ. [Tài liệu nhúng](https://woocommerce.com/document/introduction-to-woocommerce-bookings/embedding-bookings-calendar-on-a-custom-page-post/) nêu cách khác với One Page Checkout. Không giả định mọi hình thức nhúng chỉ cần extension gốc. |
| [Booking Calendar](https://wordpress.org/plugins/booking/) | Mô tả lịch và khung giờ, đặt lịch, block/shortcode và duyệt; hãng nêu free và premium. | Chưa tìm thấy hợp đồng công khai đủ chi tiết cho quote/reservation idempotent; không khẳng định không có, hoặc free đáp ứng mọi yêu cầu. Chưa kiểm tra source/version hay thử thực tế. |
| [Booking.com Demand](https://developers.booking.com/demand/docs/orders-api/order-preview-create) | Tài liệu mô tả search/availability → preview → create, cần quyền đối tác; preview trả tổng và điều kiện, order token có hạn. | Phụ thuộc quyền, thị trường và phiên bản API; không mặc định chủ site có quyền hay được giữ chỗ khi preview. Tài liệu phân biệt trạng thái xe đang xử lý với confirmed. Không đại diện cho API Trip.com. |

## Ca đầu đề xuất

**Lịch hẹn dịch vụ một tài nguyên**, ví dụ một kỹ thuật viên và một dịch vụ 60 phút, hiển thị ngày/giờ/giá theo nguồn backend; yêu cầu đặt hoặc chuyển tới giỏ theo đúng trạng thái provider. Đây là ca nhỏ để chứng minh giao diện block và handoff, không đại diện hoàn chỉnh cho tìm tuyến/tìm xe hoặc Booking.com.

Ứng viên ưu tiên khảo sát: **WooCommerce Bookings khi owner đã có hoặc tự chọn dùng license**. Lý do: có mô hình lịch/tài nguyên, form và luồng WooCommerce được tài liệu hóa, phù hợp theme đã tích hợp Woo. Không yêu cầu cài/mua trong task này; Booking Calendar là đối chứng chức năng, chưa đủ bằng chứng chọn làm adapter. Nếu không có plugin hợp lệ, tiếp tục với provider giả lập trong test và Form gửi yêu cầu, không gắn nhãn có chỗ hay đã đặt.

## Hợp đồng adapter dự kiến — cho G-61/G-62

1. Plugin adapter phiên bản hóa tự công bố `provider`, `version`, trạng thái khả dụng và capability: chọn dịch vụ, tra lịch, quote, chuyển checkout, hold, tạo, tra trạng thái, hủy. Mỗi capability phải có bằng chứng theo phiên bản và test; không có thì trả `unsupported`, không giả lập bằng frontend.
2. Theme chỉ dựng Search/Results/Summary và hiển thị trạng thái; server adapter xác thực/giới hạn request, che khóa, kiểm tra đầu vào, múi giờ, số người, currency và ID opaque, gọi backend. Không lộ endpoint quản trị, thông tin khách, API key trong URL/log/cache. Trả lỗi phân biệt chưa cấu hình, không hỗ trợ, hết chỗ, thay giá, hết hạn và lỗi nguồn.
3. Search/availability có thời điểm kiểm tra và timezone; giá hiển thị là thông tin từ provider, phân biệt giá từ/tạm tính/giá xác nhận, thuế và phụ phí. Revalidate ngay trước handoff. Nếu chỉ hỗ trợ form sản phẩm, hiển thị link chọn lịch theo backend thay vì tự dựng endpoint chưa kiểm chứng.
4. Hold chỉ hiện khi backend cung cấp hold và TTL thật; preview token không mặc định là hold. Tạo booking và thanh toán do provider: trạng thái `requested`, `held`, `pending`, `confirmed`, `cancelled`, `expired` được map theo kết quả thật, không suy từ email/redirect. Với timeout hiển thị đang kiểm tra; retry an toàn cần khả năng idempotency thực chứng minh; nếu backend không hỗ trợ, không tự quảng cáo có bảo đảm.
5. Handoff bảo toàn service/date/resource/giá/điều kiện theo API được chứng minh; nếu plugin chỉ hỗ trợ form riêng, chuyển về form với dữ liệu phù hợp được xác thực. Không gọi `create_wc_booking` trực tiếp như đường checkout mặc định: tài liệu cho thấy mặc định status confirmed và `exact=false` có thể chọn slot khác, nên cần thiết kế, quyền và kiểm thử riêng trước khi dùng.

## Việc Claude cần xác minh trước sản phẩm

- Khi owner có plugin/version hợp lệ: ghi version WP/Woo/Bookings và license; đọc source/hook công khai, route slots/auth, form giá, add-to-cart, xác nhận yêu cầu, trạng thái và expiry. Lập bảng supported/unsupported/unknown theo capability. Không cài hay mua plugin thay owner.
- Fixture: 1 dịch vụ, 1 resource, 2 khách tranh suất cuối; thay giá giữa tra và đặt; hết hạn giỏ, đổi múi giờ/ngày, DST, trùng click/request, timeout sau tạo, checkout cần duyệt, hủy và tắt plugin. Xác minh trạng thái, chỗ, tổng, chỉ một đơn hợp lệ và không báo thành công sai; kiểm tra plugin sở hữu dữ liệu sau đổi theme. Nếu backend không bảo đảm tính nguyên tử/idempotency thì ghi giới hạn, dừng tính năng tương ứng.
- G-61 chỉ xây UI kéo thả và trạng thái provider dựa trên contract; G-62 adapter và handoff sau kiểm chứng thực tế. G-57–59 giá form vẫn độc lập; giá form ước tính không thay giá bán của booking/checkout. AI chỉ cấu hình có kiểm tra và không tự bịa chuyến/giá/chỗ.
