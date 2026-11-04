# Roadmap form tính giá và tìm/đặt dịch vụ

Ngày nghiên cứu: 03-11-2026. G-56. **Trạng thái: đặc tả đề xuất để Claude Code triển khai; chưa có tính năng mới được lập trình hoặc thử trên hosting.**

## 1. Yêu cầu owner và ranh giới sản phẩm

- BIGSPY tập trung block kéo thả và AI trong theme. Owner muốn form tính giá tự động, hiện giá trước khi gửi; tìm tuyến/chuyến/xe rồi đặt dịch vụ, trải nghiệm tìm–lọc–xem giá–đặt như các nền tảng du lịch.
- **Codex:** nghiên cứu, đọc mã/tài liệu, đối chiếu, thiết kế roadmap và nghiệm thu/đánh giá bằng chứng. **Claude Code:** thực thi mã và kiểm thử. Không tự hiểu nghiên cứu này là lệnh triển khai tất cả ngay, chọn plugin trả phí hay kết nối dịch vụ thật.
- Theme sở hữu block, responsive, trình cấu hình trường/điều kiện/công thức, bảng giá hiển thị và AI hỗ trợ cấu hình. Phần tính ước giá tổng quát của Form có thể nằm cùng module Form hiện có, bật/tắt được và không phụ thuộc WooCommerce.
- Plugin/provider sở hữu dịch vụ/chuyến/xe/phòng thật, lịch và tài nguyên, giá bán có hiệu lực, giữ chỗ, số ghế, đơn hàng, thanh toán, huỷ/hoàn và đồng bộ kênh. Cấu hình giá/tồn chỗ nghiệp vụ phải được xuất/di chuyển và hoạt động độc lập với theme.
- Ưu tiên tích hợp plugin có sẵn nếu phù hợp. Chỉ đề xuất companion plugin riêng khi có bằng chứng khoảng trống; không nhét hệ thống điều hành vận tải/khách sạn vào theme.
- Booking.com/Trip.com là tham khảo luồng sử dụng. Nghiên cứu này chưa xác minh quyền truy cập API của site, chưa có dữ liệu nhà cung cấp, và không cam kết tích hợp hai dịch vụ đó.

## 2. Điểm xuất phát từ mã BIGSPY

- `blocks/form/block.json` có `provider`, `fields`, `formName`, `submitText`; cần giữ khả năng hiển thị form native/CF7 hiện có.
- `inc/form-native.php`: `bst_native_form_settings()` hợp nhất cấu hình, trường có `type/name/label/required/width`; `bst_native_field_types()` hiện có text/email/tel/url/number/date/textarea/select/radio/checkbox/acceptance/file.
- Có render, xác thực, mail và submission riêng tư. Chưa thấy type calculation/price/time-range, dependency graph, quote hoặc booking provider contract trong phần mã đã rà. Không coi Form hiện tại đã tính giá chỉ vì có trường number/date.
- Phát triển bổ sung schema có phiên bản; form cũ phải giữ nội dung, tên trường, mail-tag, cấu hình provider và hoạt động gửi. Dữ liệu entry cũ không bị chuyển đổi âm thầm.
- G-42/G-13 vẫn là việc độ bền lưu tệp khi mail lỗi, không được đánh dấu đã xong nhờ roadmap mới.

## 3. Kết quả nghiên cứu và cách áp dụng

| Nguồn chính thức | Điều đã xác nhận | Chọn lọc cho BIGSPY |
| --- | --- | --- |
| [CF7 form-tag](https://contactform7.com/tag-syntax/), [number fields](https://contactform7.com/number-fields/) | Tài liệu mô tả number/range và các loại trường gốc, không mô tả trình công thức báo giá trong danh sách đó. | Trải nghiệm CF7 của owner chưa đáp ứng nhu cầu. Không kết luận mọi addon CF7 đều không thể làm; không bắt người dùng viết shortcode/JS để có báo giá ở Form BIGSPY. |
| [Gravity Forms: calculations](https://docs.gravityforms.com/using-calculations/), [tính lại khi gửi](https://docs.gravityforms.com/troubleshooting-issues-with-calculation-results/) | Có công thức cho trường số/giá; thứ tự trường và trường bị ẩn có thể làm kết quả lệch. Kết quả được tính lại khi nhận submission. | Dùng field ID ổn định, đồ thị phụ thuộc và quy tắc trường ẩn rõ ràng; tính lại trên server, không tin tổng tiền từ trình duyệt. |
| [Woo Bookings: chi phí](https://woocommerce.com/document/introduction-to-woocommerce-bookings/woocommerce-bookings-store-manager-guide/creating-a-bookable-product/costs-for-bookable-products/) | Phân biệt base cost, cost theo thời lượng và giá “từ” để hiển thị; có quy tắc thay đổi giá. | Tách giá ước tính/giá từ/giá đặt thật; bảng chi tiết giải thích phần nào đang tính. Không thay giá plugin bằng phép tính riêng của theme. |
| [Woo Bookings: availability](https://woocommerce.com/document/introduction-to-woocommerce-bookings/woocommerce-bookings-store-manager-guide/availability-settings/), [resources](https://woocommerce.com/document/introduction-to-woocommerce-bookings/woocommerce-bookings-store-manager-guide/resources-in-bookings/) | Có công suất theo khung thời gian, cửa sổ đặt và buffer. Một booking dùng một resource được chọn/gán; persons có thể tiêu thụ quantity của resource đó. | Phù hợp để khảo sát lịch hẹn/thuê một tài nguyên. Không mặc định giải quyết đồng thời xe+tài xế, lịch tuyến hay ghế theo từng chặng. |
| [Woo Bookings: luồng khách](https://woocommerce.com/document/introduction-to-woocommerce-bookings/woocommerce-bookings-store-manager-guide/customer-booking-process/) | Khách chọn tuỳ chọn/khung giờ, xem giá rồi thêm giỏ; booking cần duyệt có luồng yêu cầu riêng. | CTA/trạng thái phải phản ánh đúng khả năng xác nhận; “gửi yêu cầu” khác “đã giữ chỗ”. |
| [Chauffeur Booking: thiết lập](https://quanticalabs.com/docs/chauffeur-booking-system/knowledge-base/post-installation-setup/), [FAQ hãng](https://quanticalabs.com/faq-chauffeur-taxi-booking-system-for-wordpress/) | Có nhóm Distance/Hourly/Flat Rate; Flat Rate dùng tuyến định sẵn; cấu hình xe và giá riêng. Hãng mô tả WooCommerce là tích hợp tuỳ chọn. | Ứng viên khảo sát cho xe riêng/đưa đón, không tự đồng nhất với bán từng ghế trên chuyến theo lịch. Cần đọc hook/API, thử phiên bản và quyền sử dụng trước khi chọn. |
| [Booking.com Demand API](https://developers.booking.com/demand/docs/getting-started/overview), [preview/create order](https://developers.booking.com/demand/docs/orders-api/order-preview-create) | Quyền/capability tuỳ dịch vụ và đối tác; preview và tạo đơn là các bước riêng. | Hợp đồng search → kiểm tra lại giá/chỗ → preview → đặt. Provider phải có quyền thực; dữ liệu cũ không được coi là còn chỗ. |

Đây là nghiên cứu tài liệu chính thức và mã Form BIGSPY, chưa phải đọc mã các plugin booking/calculation hoặc thử mua/đặt thật. Chưa chọn một backend booking duy nhất; phải qua G-60.

## 4. Ba chế độ người dùng cần phân biệt được

| Chế độ | Nguồn giá/chỗ | Nút cuối và kết quả |
| --- | --- | --- |
| Báo giá dịch vụ | Bảng giá/công thức do admin cấu hình | “Gửi yêu cầu báo giá”; lưu dữ liệu và bản tính, không giữ xe/phòng. |
| Yêu cầu đặt lịch | Tuỳ chọn dịch vụ, ngày mong muốn; có thể chưa có nguồn tồn chỗ | “Gửi yêu cầu đặt lịch”; trạng thái chờ đơn vị xác nhận, không in “còn chỗ” giả. |
| Đặt có tồn chỗ thật | Plugin/provider trả availability, quote và reservation | “Tiếp tục đặt/Thanh toán”; giữ/xác nhận theo backend, có mã tham chiếu và trạng thái thật. |

Không có backend thì được dùng hai chế độ đầu. Chế độ thứ ba phải bị vô hiệu hoá với thông báo thiếu kết nối rõ ràng. Dữ liệu demo chỉ trong preview và có nhãn demo.

## 5. Form tính giá — đặc tả block và trình cấu hình

Danh mục mô hình giá theo ngành, 16 khối quy tắc và task G-68/G-69: [PRICING-MODELS-RESEARCH.md](PRICING-MODELS-RESEARCH.md).

### Trải nghiệm người cấu hình

- Mở khối Form → thêm/sắp xếp trường → chọn “Tính giá”. Dùng ngay editor WordPress; không dựng một page builder riêng.
- Mỗi trường có ID bất biến và nhãn đổi được; giữ `name`/mail-tag cũ để tương thích. Công thức tham chiếu ID, nên đổi nhãn/kéo vị trí không làm sai giá. Đổi/xoá trường đang được tham chiếu phải báo nơi dùng và cho sửa công thức trước.
- Trường bổ sung theo giai đoạn: số lượng có đơn vị, lựa chọn có giá, checkbox nhiều dịch vụ, khoảng ngày/giờ, kết quả tính chỉ đọc, dòng chi tiết và tổng. Trường tuyến/địa điểm có ID, không lấy giá từ nhãn người dùng gõ.
- Giao diện tạo công thức bằng chọn trường, toán tử, hằng số và điều kiện; admin có thể xem biểu thức có giới hạn. Có nút kiểm tra, dữ liệu thử và giải thích kết quả từng dòng. Không yêu cầu lập trình.
- Hỗ trợ cộng/trừ/nhân/chia, min/max, làm tròn và if/else có giới hạn; bảng tra đơn giá theo gói/tuyến/loại xe; bậc số lượng/thời lượng, mức tối thiểu, phụ phí cố định/phần trăm. Ưu tiên quy tắc đầu tiên/theo thứ tự được hiển thị rõ, không phụ thuộc thứ tự DOM.
- Cấu hình áp phụ phí/giảm giá trên khoản nào và ở bước nào; tiền tệ, đơn vị đo, khoảng hiệu lực và ghi chú bao gồm/chưa bao gồm khoản nào. Thuế chính thức/coupon thanh toán của đơn bán hàng do backend xử lý.
- Điều kiện hiện/ẩn trường, bắt buộc khi thoả điều kiện, các bước và trang tóm tắt. Nền xử lý hỗ trợ nhiều bước nhưng có thể giao UI nhiều bước ở đợt sau để giữ MVP gọn.
- Preset: giặt là theo kg + giao nhận; vệ sinh theo diện tích/gói; thuê xe có tuyến/gói/giờ; lịch hẹn dịch vụ. Mọi giá mẫu ghi **demo**, yêu cầu admin thay trước khi công khai.

### Trải nghiệm khách

- Chọn lựa chọn/số lượng → giá cập nhật và có dòng phân tích → xem lại → gửi. Sửa thông tin ở bước trước thì tổng và tóm tắt được cập nhật đồng bộ.
- Chưa nhập đủ thì hiện “Chọn thêm thông tin để tính giá”, không mặc định tổng bằng 0. Ngoài vùng/bảng giá không có thì “Cần xác nhận giá”; không tự điền một mức giá.
- Hiện giá ước tính hay giá đã được backend xác nhận, tiền tệ, số lượng/đơn vị, phụ phí, giảm giá, tổng và phần chưa gồm. Tách tiền đặt cọc/còn lại khi backend hỗ trợ; báo giá không tự thu tiền.
- Có trạng thái đang tính/lỗi/tính lại; sửa nhanh không để phản hồi cũ ghi đè kết quả mới. Hỗ trợ bàn phím, nhãn lỗi liên kết đúng trường, thông báo tổng bằng live region có tiết chế, mobile 375px không tràn.
- JS lỗi/tắt: cho tính lại bằng máy chủ hoặc gửi yêu cầu chưa định giá; không hiện tổng giả và không cho thanh toán dựa trên giá chưa xác nhận.

### Quy tắc tính và lưu

- Công thức là schema/biểu thức giới hạn, không `eval`, PHP, JS tuỳ ý, gọi mạng hoặc truy cập dữ liệu ngoài allowlist. Cả quyền admin và AI đều chịu giới hạn này.
- Đồ thị phụ thuộc được kiểm tra vòng lặp, ID thiếu, loại không hợp lệ; sắp xếp tính theo phụ thuộc. Chặn chia 0, NaN, vô cực, giá trị vượt giới hạn và tổng âm do cấu hình sai. Trường bị ẩn không tiếp tục thu phí: chính sách mặc định bỏ khỏi phép tính, giá trị số đóng góp 0; trường bắt buộc chưa có dữ liệu làm quote chưa hoàn chỉnh.
- Tiền tính theo số thập phân xác định/đơn vị nhỏ nhất, tách khỏi chuỗi định dạng `1.234.567 ₫`; VND không phần lẻ, tiền khác theo cấu hình. Chốt một chính sách làm tròn được version hoá; không để JS/PHP lệch do float.
- Preview trình duyệt phục vụ phản hồi nhanh; khi gửi, server xác thực input và tính lại từ cấu hình đã lưu. Client không được tự đặt đơn giá, tổng, trạng thái ẩn hoặc policy phiên bản.
- Quote lưu input hợp lệ, các dòng giá, tiền tệ, rule revision, thời điểm/hạn hiệu lực và trạng thái ước tính/xác nhận. Giá đổi giữa xem và gửi thì báo tổng mới và yêu cầu khách chấp nhận lại trước khi đặt/thanh toán.
- Đầu ra mail, submission và màn hình khách dùng cùng bản tính server. Giá trị input có thể nhạy cảm phải theo quyền truy cập/retention của Form; quote không tạo một danh sách công khai chứa thông tin khách.
- Với native quote chỉ cần schema/công thức và submission hiện có. Khi chuyển sang giao dịch thật, backend sở hữu báo giá/đơn theo ID; theme giữ tham chiếu, không tạo bản tồn kho/đơn hàng thứ hai.

## 6. Luồng tìm tuyến/chuyến/xe và booking

### Giao diện block đề xuất

Các tên sau là tên mô tả, chưa đăng ký namespace: **Booking Search**, **Booking Results**, **Booking Filters**, **Booking Summary**. Tái sử dụng Form/Row/Column/Card/Button hiện có nơi phù hợp; không tạo thêm block chỉ để thay một preset.

- Search: loại dịch vụ, điểm đi/đến, một chiều/khứ hồi, ngày/giờ đi và về, khách/ghế, loại xe và tuỳ chọn cần thiết. Theo loại dịch vụ mới hiện đúng trường.
- Results: thẻ tên chuyến/xe/dịch vụ, hình từ nguồn, tuyến, giờ đi/đến và múi giờ, thời lượng, sức chứa, hành lý, giá/giá từ và đơn vị, điều kiện chính; có chọn và xem chi tiết. Chỉ hiện số chỗ/tình trạng còn nếu provider thực trả và còn hiệu lực.
- Filters/sort: ngày/giờ, mức giá, loại xe/sức chứa, điểm đón/trả; chỉ bật filter backend hỗ trợ. Giữ search state trên URL ở mức không chứa thông tin khách; quay lại không mất lựa chọn.
- Summary: lựa chọn, người dùng, phụ phí, tổng, điều kiện huỷ, hạn giữ chỗ nếu có; link sửa thông tin. Provider là nguồn chính sách và tổng phải trả.
- Phân biệt rõ danh sách rỗng, chưa tìm, đang tìm, provider lỗi và giá đã hết hạn. Không biến lỗi API thành “hết chuyến” hoặc tự hiện kết quả giả.

### Backend theo loại dịch vụ

| Nhu cầu | Hướng backend cần kiểm chứng |
| --- | --- |
| Báo giá giặt là/vệ sinh | Form native với giá cấu hình; không cần inventory hay WooCommerce. |
| Lịch hẹn spa/thuê một phòng/thiết bị theo giờ | Khảo sát WooCommerce Bookings hoặc plugin lịch hẹn có API/hook phù hợp. |
| Thuê xe riêng theo ngày/giờ | Plugin rental/resource; có thể khảo sát Chauffeur Booking cho distance/hourly/flat-rate theo tài liệu hãng. Kiểm chứng buffer, nhận/trả và chia sẻ xe. Xe kèm tài xế đồng thời cần cơ chế phối hợp hai tài nguyên, không giả định có sẵn. |
| Chuyến theo tuyến, giờ cố định, bán ghế | Plugin vận tải có tuyến/departure/capacity/seat; kiểm chứng từng chặng, chuyến khứ hồi và hành lý. Woo Bookings chưa được chốt là đáp ứng. |
| Phòng/khách sạn nhiều hạng giá và kênh bán | Plugin booking/channel manager hoặc API đối tác; kiểm chứng rate plan, occupancy và quyền phân phối. |
| Nguồn Booking.com/Trip.com | Chỉ khảo sát khi owner có quyền/API phù hợp; connector nằm ở plugin, giữ điều kiện sử dụng/phiên bản theo nhà cung cấp. Không coi giao diện giống họ là đã kết nối kho của họ. |

### Hợp đồng adapter cần đặc tả trước triển khai

- Khả năng khai báo: search, filter, quote, availability, hold, reserve, cancel, payment handoff. Backend không có khả năng nào thì UI không hứa khả năng đó; không giả lập giữ chỗ bằng timer frontend.
- Kết quả thống nhất: provider ID, service/resource/departure/offer ID, tiêu chí hợp lệ, ảnh/nhãn, price/line items/currency, availability, thời điểm và expiry, version. Dùng ID opaque từ provider, không suy giá từ URL/query người dùng sửa.
- Trước bước cuối: kiểm tra lại giá và công suất. Giữ chỗ có TTL thật từ backend; hết hạn thì chọn lại. Hai khách tranh suất cuối chỉ một reservation thành công bằng cơ chế nguyên tử/transaction của backend.
- Gửi lại do mạng yếu phải idempotent; timeout là “đang kiểm tra trạng thái”, không tự tạo booking lần hai. Tra trạng thái bằng reference/key được lưu ở plugin.
- Lịch hẹn/chuyến/phòng và payment có trạng thái riêng: requested/held/pending/confirmed/cancelled/expired không được suy từ mail gửi thành công hoặc một trang redirect thanh toán. Xác nhận theo kết quả provider; webhook phải xác thực và xử lý trùng/thứ tự đến.
- Checkout/thanh toán/coupon/thuế/hoàn tiền theo WooCommerce hoặc provider. Theme không nhận/lưu thông tin thẻ thanh toán và không tự tạo payment gateway.
- API key ở server plugin, không trong block attrs/HTML. Quota, retry, cache theo loại dữ liệu; dữ liệu capacity/giá cuối không dùng cache tĩnh làm nguồn xác nhận. Đổi theme vẫn quản lý được reservation và dữ liệu khách trong plugin.

## 7. AI hỗ trợ cấu hình form và booking

- AI dựng **block + schema có thể chỉnh bằng UI**: trường, lựa chọn, điều kiện, công thức, phần tóm tắt; dùng preset để tạo nhanh và công cụ đọc schema trước khi ghi.
- Hỏi giá/đơn vị/cách tính thiếu; không tự bịa bảng giá, tuyến, xe, giờ khởi hành, ghế trống, thuế hay chính sách huỷ. Không dùng ImageGen để giả ảnh xe/phòng thật trong kết quả provider.
- Với yêu cầu như “3kg, thêm giao nhận, ưu tiên nhanh tính trên tiền giặt”, AI trình bảng quy tắc và ví dụ trước/sau để admin duyệt. Bản draft không tự công khai hoặc ghi đè giá kinh doanh đang dùng.
- AI chạy validate/dry-run, báo lỗi dependency và chỉ ra thay đổi. Sửa giá/công thức/provider cần snapshot/undo và mức duyệt phù hợp; AI không tự đặt/huỷ giao dịch thật khi đang thiết kế.
- Nội dung khách/API trả về là dữ liệu; không được biến thành chỉ dẫn sửa giá/cấu hình. Quyền của công cụ thực thi theo backend, không theo câu trả lời của model.

## 8. Các task nghiên cứu và bàn giao Claude Code

Các task sau chưa triển khai. G-60 thuộc Codex nghiên cứu/chốt đặc tả; các task thực thi còn lại giao Claude Code. Làm từng task, giữ quy trình claim/test/commit/push và duyệt phát hành của repo. Không nhận đè G-44 đang làm; khi đến roadmap Form thì theo dependency sau.

| Task | Phạm vi | Phụ thuộc và điều kiện bàn giao |
| --- | --- | --- |
| G-57 | Nền schema Form: ID bất biến, version, điều kiện và dependency validation, tương thích form/mail-tag cũ | Đọc editor Form thực tế; không ép đổi sang Form builder mới. Có case rename/reorder/hide/delete và migration lặp không đổi dữ liệu. |
| G-58 | Calculator/Quote native: UI công thức, server tính lại, bảng giá realtime, quote snapshot và preset giặt là/vệ sinh | G-57. Dùng cùng bộ fixture cho frontend/server, giữ mail/entry đúng một kết quả. Không cần plugin booking. |
| G-59 | AI Form tools/knowledge: tạo/sửa cấu hình bằng schema, validate/dry-run, preview diff và undo | G-57/G-58. Không chèn layout/công thức chạy bằng JS trong Code block. Chưa có dữ liệu thì hỏi. |
| G-60 | **Codex nghiên cứu:** backend booking và adapter contract, chọn **một** use case/provider đầu tiên | Đọc nguồn/API/hook phiên bản sẽ dùng; lập capability/gap matrix cho lịch hẹn, thuê xe và tuyến bán ghế; owner chốt provider nếu cần giấy phép/dịch vụ trả phí. Bàn giao đặc tả cho Claude, không code reservation engine trong theme. |
| G-61 | Block search/results/filters/summary với adapter đã chốt và trạng thái fallback | G-60. Demo editor tách live; responsive/keyboard, URL state và không khai báo chỗ/giá khi thiếu dữ liệu. |
| G-62 | Plugin adapter + quote/availability/hold/reserve + checkout handoff | G-60/G-61. Booking/order/resource tồn tại độc lập theme; case tranh suất cuối, submit trùng, timeout, giá đổi và provider lỗi đạt. Tính năng không hỗ trợ phải báo rõ. |
| G-63 | Nghiệm thu end-to-end, preset và hướng dẫn sử dụng | Các bước trước. Kiểm thử trên site nháp, AI thật theo ngân sách được duyệt, xuất cấu hình và đổi theme giữ booking; ghi chính xác plugin/phiên bản được hỗ trợ. |

## 9. Bộ case nghiệm thu tối thiểu

1. **Bảng giá demo:** giặt 3 kg × 18.000 = 54.000, giao nhận 25.000, tổng 79.000 VND. Tuỳ chọn nhanh +20% **chỉ tiền giặt**: thêm 10.800, tổng 89.800. Đây là số liệu kiểm thử, không phải giá thị trường.
2. Nhập 1,5 kg/1.5 kg theo locale hợp lệ; chuẩn hoá cùng giá trị. Tiền VND không phần lẻ; thử thêm tiền tệ có hai chữ số lẻ.
3. Đổi nhãn, kéo trường và thêm bước không đổi công thức. Xoá trường đang dùng hoặc tạo vòng lặp phải bị chặn và chỉ rõ nơi lỗi.
4. Bỏ tuỳ chọn/ẩn trường thì hết khoản thu tương ứng; chưa chọn tuyến/ngày không hiện tổng miễn phí. Ngoài bảng giá chuyển sang cần xác nhận.
5. Sửa tổng/đơn giá trong request, gửi field ID lạ, số âm/quá lớn, chia 0 và công thức gọi script: server từ chối/tính lại theo cấu hình, không lưu tổng do client áp đặt.
6. Giá thay trong lúc khách xem; quote hết hạn; request tính chậm trả về sau lựa chọn mới: hiển thị đúng trạng thái và xác nhận lại, không thu theo tổng cũ.
7. Khoảng thời gian qua đêm, giờ kết thúc trước bắt đầu, múi giờ và ngày bị khoá; không coi thiếu calendar là còn trống.
8. Một xe/ghế cuối: hai session đặt đồng thời, chỉ một thành công; shared resource không bị trùng do thuộc hai sản phẩm/chuyến. Plugin không hỗ trợ thì không bật luồng này.
9. Click đôi/retry sau timeout/webhook lặp: một reservation/order, đối soát được. Mail lỗi không làm mất booking đã xác nhận và không đổi booking thành thất bại tuỳ tiện.
10. Mobile 375px, bàn phím, screen reader, JS tắt/lỗi, mạng chậm, provider mất kết nối; gửi yêu cầu và booking thật có nhãn/trạng thái khác nhau.
11. Form native/CF7 cũ, file upload, anti-spam và mail-tag không hồi quy. Hai form trên một trang không lấy nhầm biến/quote của nhau.
12. Xuất/nhập cấu hình không mang API secret/thông tin khách. Đổi theme vẫn thấy/quản lý đơn và giữ chỗ từ plugin; không âm thầm xoá data khi tắt block.

## 10. Các quyết định còn mở, không cản việc lập roadmap

- Use case booking đầu tiên, nguồn tuyến/xe/phòng, công suất/ghế theo chặng, plugin/provider và phiên bản được chọn ở G-60; chưa mặc định tích hợp mọi ngành cùng lúc.
- Thu tiền online hay nhận yêu cầu; giá gồm/chưa gồm gì, quy tắc phụ phí và giảm giá phải do admin cung cấp, không do AI tự quyết.
- Cần bản đồ/tính khoảng cách thật thì chọn dịch vụ routing, quota và chi phí riêng; không dùng khoảng cách đường thẳng để tự nhận là cước tuyến thực.
- Ngày phát hành, giấy phép plugin, tài khoản đối tác và phí API chưa được duyệt. Nghiên cứu roadmap không kích hoạt dịch vụ, không đặt thử thật và không phát hành sản phẩm.
