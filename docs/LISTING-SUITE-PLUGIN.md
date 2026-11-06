# Gộp 3 plugin listing thành một plugin

Owner yêu cầu ngày 06-11-2026: gộp BIGSPY AI LIST Generator, BIGSPY AI Post Writer và BigSpy Site Insights thành một plugin, đi cùng chế độ BIGSPY AI List của theme (xem `LISTING-MIGRATION.md`). Tài liệu này là nghiên cứu và đề xuất, chưa có mã.

## Hiện trạng

| Plugin | Phiên bản | Việc chính | Phụ thuộc |
| --- | --- | --- | --- |
| BIGSPY AI LIST Generator | 2.9.9, ~1.800 dòng | Tạo listing (sản phẩm WooCommerce) bằng AI qua Kyma API: trường listing, SEO Rank Math (title, description, focus keyword, danh mục chính), nghiên cứu web qua Serper, liên kết nội bộ, thống kê Popular | WooCommerce, ACF (tùy chọn), Rank Math (tùy chọn), hàm của theme cũ |
| BIGSPY AI Post Writer | 1.2.4, ~340 dòng | Viết bản nháp bài blog có nghiên cứu web và metadata ảnh | Bắt buộc List Generator (dùng chung API key, model, Serper) |
| BigSpy Site Insights | 0.3.0, 15 file | Thông tin website của listing: uptime, SSL, công nghệ, Core Web Vitals (Google CrUX); cột admin: rank Tranco, lượt truy cập ước tính, RDAP, Open PageRank | WP-Cron (15 phút, giờ, ngày), khoá Google/OpenPageRank tùy chọn |

Lưu ý nguồn mã: repo riêng của ba plugin (private) cập nhật lần cuối 29-09 đến 03-10-2026; bản trong `theme-bigspy-ai-list/plugin/` mới hơn (Generator 2.9.9). Khi gộp lấy bản trong repo theme làm gốc, đối chiếu repo riêng để không sót thay đổi.

## Các repo khác của bentarofficial (rà ngày 06-11-2026)

Ngoài ba plugin trên, không repo nào phụ thuộc theme BIGSPY AI LIST (theme cũ không gọi đến repo nào khác). Có một repo nên cân nhắc:

- **BigSpyAI-Customer-Accounts-Product-Reviews** (1.2.0, private): tài khoản khách hàng và đánh giá sản phẩm WooCommerce có ảnh, kiểm duyệt, phản hồi của cửa hàng. Làm cho cửa hàng (chuyển từ TT Orico), nhưng listing cũng là sản phẩm WooCommerce nên dùng được ngay làm "đánh giá của người dùng" cho listing, đúng khoảng trống so với đối thủ. Đề xuất: không gộp vào List Suite (nó phục vụ cả cửa hàng), mà để plugin riêng, theme hỗ trợ hiển thị đánh giá trên thẻ và trang listing.

**Owner chốt 06-11-2026: tạm gác plugin này, chưa nghiên cứu.** Phạm vi hiện tại chỉ gồm gộp theme và gộp ba plugin.

Owner hỏi (06-11-2026) có nên đưa thẳng vào theme. Khuyến nghị: **không đưa mã vào theme**, mà tích hợp chặt: theme có nút cài một lần trong cài đặt (cả chế độ Website lẫn List), tự nhận plugin và áp style, sao đánh giá trên thẻ sản phẩm/listing, khối "Đánh giá" có ổ khoá khi chưa cài. Lý do: plugin xử lý đăng ký tài khoản, dữ liệu cá nhân (số điện thoại, ảnh đại diện, đồng ý nhận marketing) và tải ảnh lên; theo quy tắc WordPress đây là việc của plugin, để đổi theme không làm mất tính năng và vá bảo mật được phát hành riêng, nhanh. Dữ liệu đánh giá nằm trong bình luận và meta chuẩn của WooCommerce, nên không bị khoá vào theme.

Không liên quan đến listing: bigspy-ai-media-cleaner-wp, bigspy-ai-scanner-wp, bigspyai-site-migrator-wp (công cụ WordPress chung), bigspyai-video-editor (rỗng), các repo BIGSPY AI Connect/Agents (ứng dụng máy tính) và repo cá nhân.

## Vì sao là plugin, không gộp vào theme

- Tạo nội dung, gọi API ngoài và cron là chức năng của plugin theo quy tắc theme WordPress; đổi theme không làm mất các tác vụ này.
- Theme nhẹ cho người không dùng listing.
- Phát hành và cập nhật độc lập với theme.

## Đề xuất: BIGSPY AI List Suite

Một plugin, ba mô-đun bật/tắt riêng trong một trang cài đặt:

1. **Listing Generator:** tạo và làm mới listing bằng AI, SEO, liên kết nội bộ, Popular.
2. **Post Writer:** viết bài blog hỗ trợ listing (so sánh, top list, hướng dẫn), tự chèn liên kết đến listing.
3. **Site Insights:** dữ liệu website của từng listing, hiển thị ở trang listing và cột admin.

### Kết nối với theme

- Theme ở chế độ BIGSPY AI List hiện thông báo gợi ý cài plugin (tải từ GitHub Releases, có cập nhật tự động như theme). Chế độ tắt thì không gợi ý.
- Plugin cũng chạy với theme khác, miễn có WooCommerce; khi đó hiển thị dùng shortcode/khối của plugin, kèm thông báo rằng giao diện đầy đủ cần BIGSPY AI Theme WP.
- **Một kết nối AI chung:** theme đã có client Kyma cho AI Designer (`ai-designer/includes/client.php`). Plugin dùng lại key và model của theme nếu có, chỉ hỏi key riêng khi dùng theme khác. Người dùng nhập key một lần.
- Khối Site Insights đăng ký là khối listing, nên cũng có ổ khoá khi chế độ tắt.

### Hợp nhất kỹ thuật

- Tiền tố mới `bsl_`; giữ hàm `bigspy_*` mà nơi khác đang gọi bằng `function_exists`, để site cũ không lỗi.
- Một trang cài đặt, một bảng option. Lần chạy đầu chuyển các option cũ (`bigspy_api_key`, `bigspy_api_url`, `bigspy_serper_api_key`, `bigspy_system_prompt`, `bigspy_web_research_*`, `bigspy_popular_*`, `bigspy_internal_link_history`, option `bsi_*`) sang cấu trúc mới, giữ bản cũ đến khi xác nhận.
- Meta listing giữ nguyên key, dùng chung với theme.
- Gộp lịch cron về một chỗ; tắt mô-đun thì gỡ lịch của mô-đun đó.
- Kích hoạt plugin mới thì tự tắt ba plugin cũ nếu đang bật, tránh trùng hàm.
- API key chỉ lưu trong option, không ghi vào repo hay log.

- Ràng buộc với theme đã đo (xem `LISTING-MIGRATION.md`): plugin chỉ cần `bigspy_dynamic_attributes` và `bigspy_theme_integration_version` từ theme; theme cần `bigspy_popular_query_args` từ plugin. Trang cài đặt cũ gắn vào tab "BIGSPY AI Customize" qua hook `bigspy_ai_admin_tabs_*`; plugin mới có trang riêng, và khi chạy với BIGSPY AI Theme WP thì thêm liên kết từ trang tính năng `listing`.

### Giá trị cộng thêm khi gộp

- Generator gọi Site Insights ngay sau khi tạo listing để điền domain, công nghệ, trạng thái.
- Post Writer chọn listing có thật trong site để viết bài top list/so sánh, chèn thẻ listing và liên kết nội bộ.
- AI Designer có thể gọi các tác vụ của plugin ("tạo 10 listing công cụ AI viết nội dung").
- Bảng điều khiển chung: số listing, listing thiếu dữ liệu, listing có website lỗi, bài cần cập nhật.

## Việc đề xuất

- `G-81` Đối chiếu mã ba repo riêng với bản trong repo theme, chốt nguồn gốc.
- `G-82` Khung plugin List Suite: trang cài đặt, bật/tắt mô-đun, chuyển option, tắt plugin cũ, cập nhật qua GitHub.
- `G-83` Mô-đun Listing Generator, dùng chung kết nối AI với theme.
- `G-84` Mô-đun Post Writer.
- `G-85` Mô-đun Site Insights và khối có ổ khoá.
- `G-86` Liên kết giữa các mô-đun và với AI Designer.
- Thứ tự: sau G-74 (công tắc chế độ), trước G-79 (chuyển bigspyai.com), để bigspyai.com chuyển một lần cả theme và plugin.

Cần owner quyết: tên plugin; repo mới hay đặt trong repo theme; plugin công khai GPL như theme hay giữ private.
