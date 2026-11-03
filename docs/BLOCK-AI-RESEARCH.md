# Nghiên cứu block tái sử dụng, dữ liệu động và AI — G-64

Ngày 03-11-2026. Phạm vi: đọc mã BIGSPY tại `22a99f5`, bổ sung kiểm tra mã Elementor 4.3.5 thực cài qua WP File Manager và kế thừa bằng chứng sáu theme trong [SOURCE-AUDIT.md](SOURCE-AUDIT.md). Đây là phân tích mã tĩnh, chưa thử trên site nháp hay với AI thật. Không khẳng định đã đọc toàn bộ sản phẩm hay chứng minh BIGSPY vượt đối thủ.

## Đối chiếu có căn cứ

| Vấn đề | Nguồn tham khảo | BIGSPY hiện tại | Hướng đề xuất |
| --- | --- | --- | --- |
| Thành phần dùng lại | Elementor `modules/components/component-lock-manager.php`: khóa theo người sửa, gia hạn qua heartbeat, xử lý khóa hết hạn. `components-access-controller.php` phân tier core/Pro/expired; các thao tác tạo, xóa, thêm vào trang yêu cầu Pro trong mã này. Mô-đun còn phụ thuộc điều kiện bật: xem SOURCE-AUDIT. | Pattern Studio, preset/style và màu dùng chung đã có; không coi đó là hệ component đồng bộ đầy đủ. | Tận dụng synced pattern WordPress, cho biết nơi đang dùng, khác biệt cục bộ và phạm vi ảnh hưởng trước khi sửa; khóa/xung đột và hoàn tác. Không kết luận mọi người dùng Elementor đều có tính năng Pro. |
| Nội dung động | Nguồn Neve Magic Tags đã đọc trong SOURCE-AUDIT có dữ liệu bài viết, site và sản phẩm. [WordPress Block Bindings](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-bindings/) hỗ trợ nguồn và thuộc tính theo phiên bản; `core/post-meta` cần đăng ký REST, không dùng meta bảo vệ. | Đã có Product Part/template sản phẩm và shortcode động trong `inc/product-design.php`, `templates/product-design.php`, `inc/theme-builder.php`. Chưa có bằng chứng một bộ chọn binding tổng quát áp dụng mọi block. | G-37: chọn nguồn có kiểu và fallback, giữ context, chỉ công bố trường và block đã xác thực; dùng API WordPress khi có thể, kiểm tra phiên bản tối thiểu của theme trước khi quyết định fallback. |
| Danh sách bài động | [Query Loop WordPress](https://developer.wordpress.org/block-editor/reference-guides/core-blocks/core-blocks-theme/core-block-query/) đã có sẵn; hướng mở rộng chính thức trong [handbook](https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/extending-the-query-loop-block/). | Trong `ai-designer/includes/tools-design.php:66`, danh sách core AI được chấp nhận không chứa `core/query`, `core/post-template` hoặc `core/block`. Điều này không có nghĩa Gutenberg thiếu các block đó. | G-38: thiết kế thẻ bằng block trên Query Loop, tận dụng ngữ cảnh archive/search, phân trang, trạng thái rỗng và preview; không tạo engine truy vấn song song nếu không cần. |
| AI sửa trang hiện hữu | Theo `tools-design.php:500`, block ngoài BIGSPY được xuất ra `raw`; tại `:240` tên core bị kiểm tra allowlist trước khi kiểm tra raw, nên `core/query`/`core/block` được trả về nguyên dạng nhưng nếu gửi lại sẽ bị từ chối. `:482` trả lỗi khi có lỗi build; `:1320` trả lỗi trước `wp_update_post` trong đường update_page có blocks. | Phân tích tĩnh cho thấy giới hạn tương thích và đường từ chối trước ghi trong trường hợp nêu trên; chưa chạy fixture, chưa kết luận mọi đường chỉnh sửa an toàn hoặc gây mất dữ liệu. `:302` chuẩn hóa khoảng trắng khi tạo fingerprint: cần kiểm thử sự khác biệt có ý nghĩa. | G-65: trước hết cho AI giữ nguyên block đang có một cách an toàn, thông báo rõ block chỉ đọc; sau đó mới mở thao tác có cấu trúc. Không mở toàn bộ HTML/raw hoặc tự nới quy tắc AI hiện hành. |

## Đặc tả cho Claude Code

### G-65 — tương thích AI với block hiện hữu, ưu tiên an toàn

1. Tạo fixture xác minh `get_page → update_page` với Query Loop lồng Post Template, synced pattern `core/block`, binding `metadata`, block plugin bên thứ ba và các thuộc tính lạ. Đối chiếu trước/sau cả nội dung lưu, cấu trúc parse và render; kiểm tra trường hợp AI trả cây đầy đủ, bỏ node, thay node, cùng request chỉ sửa title. Ghi rõ test nào hiện lỗi, test nào có thể mất nội dung; không suy diễn chỉ từ allowlist.
2. Thiết kế chế độ giữ nguyên block không hỗ trợ: server xác định danh tính/nội dung từ chính trang và phiên bản được đọc, không tin raw/ID do model tự cung cấp; nếu thiếu, sửa trái phép, mơ hồ hoặc có thay đổi đồng thời thì dừng trước ghi, báo cụ thể và cho xem trước. Kiểm tra lock, revision và undo; tránh biến việc bảo toàn thành quyền chèn block bất kỳ từ trang khác.
3. Giữ đúng nguồn tham chiếu, override, thuộc tính và thứ tự; xử lý pattern thiếu, riêng tư, đã xóa, vòng lặp và plugin bị tắt bằng thông báo an toàn. Không tự sửa pattern đồng bộ khi người dùng chỉ yêu cầu sửa một trang. [WordPress mô tả](https://developer.wordpress.org/block-editor/reference-guides/data/data-core-block-editor/) synced pattern là thực thể riêng, nên cần kiểm tra quyền và phạm vi sửa riêng.
4. Giữ nguyên các ràng buộc `bst/code`, tỷ lệ block chỉnh sửa và các giới hạn AI hiện hành; đánh giá block tham chiếu có thể che nội dung vi phạm trước khi cho phép ghi. Có kiểm thử raw sửa khoảng trắng trong nội dung, forged raw, lặp/đổi vị trí và block lồng nhau. Không tuyên bố bảo toàn byte nếu serializer/sanitizer thay đổi dữ liệu; chọn tiêu chí bảo toàn rõ ràng và thử thực tế.

### G-37/G-38 — dữ liệu động và loop

- UI chọn nguồn → trường/thuộc tính tương thích → ngữ cảnh bài/sản phẩm → fallback khi thiếu; preview dữ liệu thực theo quyền, không phơi meta riêng tư. Không gửi dữ liệu riêng tư cho model nếu không cần; AI chỉ nhận schema và giá trị được phép.
- Hỗ trợ trước title, ảnh, excerpt, permalink và meta đăng ký hợp lệ; trường sản phẩm do WooCommerce cung cấp khi plugin hoạt động. Từng block/thuộc tính phải có bảng tương thích dựa trên WordPress tối thiểu thực hỗ trợ; không mặc định mọi block BIGSPY dùng được native bindings.
- Loop thiết kế card kéo thả, lựa chọn post type, taxonomy, sắp xếp, số lượng, phân trang; phân biệt query mới và inherit archive/search. Test context đúng từng item, 0 kết quả, pagination, bản dịch, mobile, tốc độ và plugin vắng mặt. AI tạo cấu hình đã kiểm tra thay vì PHP/SQL tùy ý.

### G-39 — thành phần và token

- Ưu tiên synced pattern/override có sẵn của WordPress; phân biệt mẫu độc lập, mẫu đồng bộ và nội dung ghi đè từng nơi. Trước khi sửa dùng lại: hiển thị phạm vi ảnh hưởng, khả năng xem trước và undo; xử lý cạnh tranh chỉnh sửa. Khảo sát usage map đáng tin cậy trước khi hứa thống kê toàn site.
- Kế thừa biến màu/preset hiện có, tránh tạo hai nguồn chân lý cho editor và AI; xác định thứ tự ưu tiên token toàn site, style component và override tại instance. Kiểm tra responsive, accessibility, di chuyển site và đổi theme.

## Ranh giới sản phẩm và thứ tự

Theme sở hữu UX block, style, preview, AI và tích hợp; WooCommerce/plugin/provider sở hữu dữ liệu sản phẩm, tồn kho, đơn hàng, thanh toán và logic booking; Rank Math tiếp tục sở hữu SEO chuyên sâu. Tích hợp bằng capability/API công khai, có trạng thái plugin vắng mặt. Không thay đổi thứ tự công việc AI Designer đã được owner chốt. G-65 là đề xuất độc lập, G-37/38/39 tiếp tục là các task đã có; không coi tài liệu này là tính năng đã triển khai.
