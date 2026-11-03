# Hành vi người dùng và UX AI Designer — G-68

03-11-2026. Phạm vi: nghiên cứu nguồn thị trường công khai và rà mã BIGSPY tại `d395154`; không khảo sát khách hàng BIGSPY, không thu thập dữ liệu trên site. Số liệu bên dưới là khảo sát do nhà cung cấp công bố, không đại diện cho toàn bộ WordPress hay người dùng Việt Nam. [SOURCE-AUDIT.md](SOURCE-AUDIT.md), [BLOCK-AI-RESEARCH.md](BLOCK-AI-RESEARCH.md) và [RESPONSIVE-AI-RESEARCH.md](RESPONSIVE-AI-RESEARCH.md) lưu giới hạn đối chiếu mã các theme/plugin.

## Tín hiệu thị trường và điều có thể suy ra

| Nguồn và mẫu | Phát hiện được nguồn công bố | Hàm ý đề xuất, chưa được chứng minh ở BIGSPY |
| --- | --- | --- |
| [Framer State of Sites 2026](https://www.framer.com/state-of-sites-2026/), hơn 1.900 người làm web; trang công khai nói 53% công việc website là sửa/chỉnh chung. Không suy ra tỷ lệ này cho khách hàng theme. | Duy trì và chỉnh sửa là một phần công việc đáng kể trong mẫu. | Ưu tiên AI sửa một phần nhỏ, xác nhận phạm vi, xem thay đổi và phục hồi; đo nhiệm vụ sửa trang hiện hữu bên cạnh tạo trang mới. |
| [Crocoblock 2025–2026](https://crocoblock.com/blog/wordpress-developer-survey/), 382 chuyên gia, tuyển qua cộng đồng/email/quảng cáo, khoảng 86% là khách Crocoblock; 61% báo có dự án e-commerce, hơn 40% membership hoặc booking; có câu chọn nhiều đáp án. | Nhóm thiên về website dữ liệu động cũng dùng nhiều builder và loại dự án. | Ưu tiên block dữ liệu động và kết nối plugin trong G-37/38/40, G-60–62; không khẳng định tỷ lệ nhu cầu booking toàn thị trường hay tự xây engine đặt chỗ. |
| [Squarespace Circle 2026](https://pros.squarespace.com/web-design-industry-report), 1.034 nhà thiết kế trong khảo sát tự nguyện của cộng đồng Circle tháng 11/2025; nguồn nói 88% người đã dùng AI thiết kế đánh giá tác động tích cực. | Một bộ phận nhà thiết kế trong mẫu đánh giá AI hữu ích; không phải 88% mọi nhà thiết kế hoặc bằng chứng chất lượng AI BIGSPY. | Thiết kế quy trình AI cộng tác với người dùng: đưa lựa chọn và cho chỉnh thủ công, thay vì mặc định sản phẩm hoàn toàn tự động. |
| [Webflow 2026](https://webflow.com/resources/report/2026-state-of-the-website), 1.000 lãnh đạo marketing/công nghệ ở Mỹ, Anh, Canada. | Nguồn nhấn mạnh quản trị, cộng tác và độ phức tạp; mẫu doanh nghiệp không đồng nghĩa freelancer Việt Nam. | Với tác vụ ảnh hưởng toàn site, hiển thị phạm vi thay đổi, quyền và khôi phục; cần nghiên cứu người dùng thật trước khi xây chức năng cộng tác lớn. |
| [WordPress annual survey 2023](https://wordpress.org/news/2024/02/2023-annual-survey-results-and-next-steps/) là dữ liệu cũ, người tham gia nêu số lượng plugin, trải nghiệm chỉnh site, bảo mật và hiệu năng là các vấn đề. | Tín hiệu lịch sử, không phải xếp hạng hiện tại. | Tránh thêm plugin bắt buộc cho UX block/AI; giữ tích hợp chuyên sâu cho WooCommerce, Rank Math và booking ở plugin phù hợp. |

## BIGSPY hiện có — không xây lại từ số 0

- AI có `page_plan`, `build_sections`, catalog và recipes; cập nhật trang có kiểm tra quyền, khóa, xác nhận trang đã xuất bản và snapshot/undo (`ai-designer/includes/tools-design.php`). Cần kiểm tra mức độ dễ hiểu trong UI, không mặc định đã có visual diff đầy đủ cho mọi công cụ.
- `ai-designer/includes/context.php` đã cho lưu ngữ cảnh, kiểm tra version khi người dùng sửa và hoàn tác từng thay đổi AI nếu phiên bản chưa đổi. `tasks.php` có metrics theo lượt; đây là thời gian/token/chi phí, không phải nghiên cứu mức hài lòng hay hành vi thực tế.
- G-65 bảo toàn block hiện hữu; G-67 responsive/a11y; G-57–63 form/booking. Các đề xuất dưới đây dựa trên nền đó, không thay quyền dữ liệu của plugin, không nới quy tắc block/AI tại `AGENTS.md`.

## Giả thuyết để kiểm chứng trước khi ưu tiên

1. **Người tự làm website:** cần bắt đầu với ít câu hỏi và thấy được bản nháp có thể chỉnh. Chưa biết cách hỏi nào hiệu quả cho khách BIGSPY.
2. **Freelancer/agency:** quan tâm sửa đúng phần được yêu cầu, giữ phong cách và làm lại nhanh trên nhiều trang. Chưa có dữ liệu về mức độ cần thư viện dùng lại hay batch operation.
3. **Chủ dịch vụ:** muốn khách biết thông tin/giá trước khi liên hệ hoặc đặt lịch, nhưng giá, tồn chỗ và đơn cần nguồn tin cậy từ nhà cung cấp. Đây là giả thuyết theo yêu cầu owner, không phải số đo thị trường BIGSPY.
4. **Người dùng sau khi AI hoàn thành:** có thể cần biết điều gì đã thay đổi và cách phục hồi; cần quan sát để xác định điểm gây nhầm lẫn thật.

## Nghiên cứu người dùng — đề xuất trước khi code diện rộng

- Tuyển có chủ đích 9–12 người cho vòng đầu, cân bằng người mới, freelancer/agency và chủ site dịch vụ; ghi tuyển chọn, kinh nghiệm, thiết bị, ngôn ngữ và loại dự án. Mẫu nhỏ nhằm tìm vấn đề định tính, không ước lượng thị phần. Có thể thêm vòng khi kết quả chưa rõ.
- Dùng site nháp và dữ liệu giả; yêu cầu người tham gia thực hiện: tạo một section từ brief thiếu thông tin; chỉnh riêng CTA trên trang có block cũ; sửa layout mobile; hoàn tác; tạo mẫu form báo giá có bảng giá giả. Người điều phối không hướng dẫn trước, ghi điểm dừng, câu hỏi, lỗi và thời gian; sau đó phỏng vấn ngắn. Không dùng website khách thật hoặc dữ liệu cá nhân thật.
- Chỉ số định nghĩa trước: hoàn thành nhiệm vụ độc lập (người dùng đạt tiêu chí đã ghi), thời gian tới bản nháp có thể sửa, số lần cần trợ giúp, tỷ lệ chỉnh sai phạm vi hoặc không phục hồi được, nhận xét về tin tưởng. Ghi số người đạt trên tổng, trường hợp thất bại và môi trường; không so sánh tỷ lệ giữa mẫu quá nhỏ như kết luận thống kê.
- Xin đồng ý tham gia và ghi hình riêng; ưu tiên ghi chép ẩn danh, không ghi mật khẩu, nội dung khách hàng, prompt nguyên văn, URL riêng, doanh thu hay chi tiết booking. Chỉ ghi âm/video khi cần và được đồng ý; nêu thời hạn lưu, người có quyền xem, cách xóa. Không bật telemetry mặc định hoặc gửi dữ liệu ra ngoài.
- Xếp từng phát hiện theo tần suất, mức ảnh hưởng và độ tin cậy; kiểm tra lại sau chỉnh sửa trên cùng kịch bản. Khảo sát mỗi quý hoặc khi có thay đổi sản phẩm lớn là đề xuất quy trình, không phải số liệu đã xác thực. Không tự tạo hệ thống tracking chỉ vì cần đo.

## Brief cho Claude Code — G-69, sau khi xác nhận qua thử nghiệm

**Thiết kế UX sửa có kiểm soát:** tận dụng cơ chế preview/snapshot đã có; ở phạm vi công cụ đã xác minh, cho biết đối tượng, phần dự kiến thay đổi, nội dung được giữ, cảnh báo nếu thông tin thiếu, và đường xem lại/undo. Chỉ đề xuất visual diff khi xác minh có thể lấy trạng thái đáng tin cậy; block lạ theo G-65 phải an toàn trước. Việc trên trang xuất bản tiếp tục theo cơ chế xác nhận hiện hành.

**Nghiệm thu:** kịch bản chỉnh CTA chỉ sửa CTA đã chọn; thay đổi đồng thời/lock báo xung đột và không ghi đè; undo khôi phục đúng phạm vi khi không có thay đổi mới, nếu đã thay đổi thì báo rõ; việc bị từ chối không tạo cảm giác thành công; AI không bịa giá, ảnh, dữ liệu hoặc kết quả kiểm tra. Test quyền, trang nháp/đã xuất bản, nhiều block giống tên, block bên thứ ba và rollback; sau đó kiểm thử với người dùng trên dữ liệu giả. Không mở rộng telemetry, thu nội dung chat hay phá chính sách block.

G-69 là đề xuất, phụ thuộc kết quả nghiên cứu và G-65 cho các trang chứa block không hỗ trợ. Không đổi thứ tự AI Designer đã được owner chốt. Form/giá/booking tiếp tục theo đặc tả hiện có, SEO và nghiệp vụ thương mại thuộc plugin/provider.
