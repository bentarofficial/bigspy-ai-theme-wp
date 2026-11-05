# Đặc tả đo tài nguyên và hiệu năng — G-71, nối tiếp G-41

05-11-2026 · Đọc mã BIGSPY tại nhánh `feature/ai-designer` sau G-54 và đối chiếu hướng dẫn WordPress/web.dev. Đây là kế hoạch đo, chưa chạy benchmark; môi trường local đang thiếu dung lượng ổ C theo HANDOFF. Không có căn cứ kết luận BIGSPY hiện chậm hơn theme tham khảo.

## Nền hiện có và điểm cần đo

- `inc/performance.php` đã chia CSS theo HTML thực tế, cache CSS sinh; `inc/assets.php` vẫn nạp `assets/js/main.js` và `assets/js/blocks.js` chung ở frontend. `inc/woocommerce.php` và `inc/commerce-customization.php` có enqueue theo ngữ cảnh cho một số chức năng Woo.
- `inc/assets.php` đăng ký thêm scripts/styles cho editor; `inc/blocks.php` đăng ký module block theo dependency. Cần đo riêng post editor, pattern/theme customization và frontend, vì editor có thể nạp assets trong iframe.
- Các module được enqueue rải rác trong `inc/` (motion, countdown, slider, mega-menu, shop pagination, variation/cart, form, floating contact). Số lượng file không cho biết bao nhiêu byte, parse/execute cost hay requests thực sự xuất hiện.
- `repo/docs/SOURCE-AUDIT.md` ghi nhận GeneratePress, Hello Elementor và OceanWP có enqueue có điều kiện ở một số chức năng; điều đó không chứng minh toàn theme luôn nhẹ. Chưa có fixture đo đối đầu cùng host/page.

## Quy trình G-41

### Fixture cố định

Tạo bản sao local/test của cùng theme, cùng WP/Woo/plugins, PHP, dữ liệu, server, cache và viewport. So sánh commit baseline với commit thay đổi trên các trang: trang trắng; bài nội dung; landing có banner/ảnh; form; motion/slider; catalog Woo; trang sản phẩm; cart/checkout. Lặp mỗi loại với option/plugin liên quan bật/tắt. Không dùng site production hoặc dữ liệu khách thật.

### Ghi số đo

Trên cùng máy và mạng kiểm soát, đo warm-cache/cold-cache riêng, mobile và desktop. Thu network log: URL, loại resource, request count, transferred/compressed bytes, thời điểm bắt đầu/kết thúc, cache hit; CSS/JS handles và dependencies; tổng inline bytes riêng. Thu LCP và phần tử LCP, CLS, TBT/long tasks; thử tương tác mở menu, tabs, slider, form, quick view, filter/cart. Ghi console/error, chức năng đạt hay lỗi. Lưu raw report/network trace cùng phiên bản theme/WP/plugin, cấu hình, viewport, CPU/network profile, cache state và số lượt lặp.

Chạy nhiều lượt trong trạng thái cố định rồi báo median và khoảng dao động, không chỉ một lần hoặc điểm Lighthouse đơn lẻ. Lab dùng để so sánh trước/sau; nếu có RUM được owner duyệt thì đánh giá riêng. Lab không đo INP thật; TBT chỉ là proxy. CLS lúc load có thể bỏ qua dịch chuyển phát sinh sau thao tác. [web.dev](https://web.dev/articles/vitals-measurement-getting-started?hl=en) mô tả chênh lệch giữa lab và dữ liệu người thật.

### Cách chọn thay đổi

1. Tạo bảng asset→handle→owner→điều kiện enqueue→trang cần→dependency→dung lượng thực. Dùng WordPress hooks/conditional registration và block usage khi khả thi; trước khi dequeue asset, xác nhận không phải plugin hoặc block khác cần nó. Phân biệt asset frontend với editor iframe.
2. Chọn một nhóm JS có bằng chứng đang tải thừa trên fixture, ví dụ motion hoặc shop interactions. Đo baseline và một thay đổi mỗi lần. Không chuyển cả `main.js`/`blocks.js` sang nhiều file chỉ để giảm kích thước file nếu tổng bytes, thời gian parse/execution, requests, LCP/CLS/TBT không tốt hơn.
3. So cả lợi ích lẫn chi phí: bytes/request trước-sau cho từng fixture; chức năng; lỗi console; cacheability; duplicate dependency; mobile; editor; Woo interactions. Khi script chỉ xuất hiện ở trang dùng tính năng mà trang khác giảm tài nguyên, giữ thay đổi đó nếu không phá cache/chức năng và cải thiện vượt biến thiên phép đo.
4. Thêm fixture regression cho trang trắng/content/form/motion/shop; kiểm tra plugin bật/tắt, script defer ordering, fallback không JS nếu được hỗ trợ, reduced-motion, block render động, editor iframe. Không thêm cơ chế cache HTML mới trong phạm vi này.

## Điều kiện nghiệm thu cho Claude Code

- Báo trước/sau có cùng cấu hình và trace; bảng bytes/request/metric cho toàn fixture, median và độ dao động. Nếu chênh lệch nằm trong noise thì không tuyên bố tăng tốc.
- Frontend không tải module chỉ dùng ở backend; trang không có tính năng mục tiêu không yêu cầu tải script đó, nhưng mọi trang liên quan giữ hoạt động đúng.
- Editor, frontend, Woo shop/product/cart/checkout, menu, forms, motion và accessible/reduced-motion đều qua smoke fixture; không có lỗi JS/PHP hoặc request thất bại mới.
- Mỗi dequeue/conditional phải dựa trên capability/use thực, xét plugin khác có thể cần handle. Gỡ plugin/theme option/block phải không để asset phụ thuộc treo hoặc làm hỏng render.
- Khi không thể chạy vì thiếu tài nguyên máy, dừng ở việc chuẩn bị fixture/script, ghi nguyên nhân, không tạo số đo giả; tiếp tục sau khi máy đủ dung lượng.

**Nguồn kỹ thuật:** [WordPress enqueue assets trong editor](https://developer.wordpress.org/block-editor/how-to-guides/enqueueing-assets-in-the-editor/) mô tả `enqueue_block_assets` có thể vào cả editor iframe và frontend từ WP 6.3, cần xem dependency/ngữ cảnh. [web.dev Web Vitals](https://web.dev/articles/vitals?from=dout.dev) phân biệt LCP/CLS/TBT trong lab với INP thực địa.

## Phạm vi

G-71 bổ sung phương pháp và tiêu chí cho G-41; không phải kết quả benchmark, không thay đổi ưu tiên AI/block đã chốt. Theme điều phối assets; plugin sở hữu assets và hành vi của plugin. G-41 có thể thay một nhóm module sau khi đo, không cần một đợt rewrite tổng thể.
