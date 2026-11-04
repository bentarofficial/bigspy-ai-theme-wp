# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

### Ảnh URL trực tiếp và thanh miễn phí vận chuyển (SOURCE-AUDIT P1)

- **Đã thay đổi:** Banner/Image dùng URL (không Media ID) không còn ghi cứng `loading="lazy"`; helper `bst_url_img_loading_attrs()` (`inc/blocks.php`) dùng chung bộ đếm và ngưỡng của WordPress, banner có thể nhận `fetchpriority="high"` một lần. Thanh free shipping (`inc/checkout-extras.php`) đọc `ignore_discounts`, coupon miễn phí ship cho `either`, ẩn với `both` khi chưa có coupon; số nhập tay giữ cách cũ.
- **Kiểm thử:** `php -l`; test cô lập với stub WP/Woo 11/11 đạt (giảm giá, ignore_discounts, either/both/coupon, số nhập tay; eager/lazy/fetchpriority theo ngưỡng). Playground không chạy được trong môi trường cloud (`fetch failed`); chưa thử trên giỏ hàng và trang thật, chưa đo LCP.
- **Khi phát hành cần bổ sung:** dòng Có gì mới; cần chạy Playground/site nháp có WooCommerce.

### G-57 — Nền schema Form native

- **Đã thay đổi:** `inc/form-schema.php` mới: mỗi trường có `id` cố định (form cũ nhận `n_<name>`, chuẩn hoá lặp không đổi dữ liệu), attr `schemaVersion`, điều kiện hiện/ẩn `when` (all/any; filled, empty, is, not, gt, lt), kiểm tra trường thiếu/tự tham chiếu/vòng lặp, danh sách nơi dùng. Server tính lại hiển thị khi gửi: trường ẩn không bắt buộc, giá trị/tệp bị bỏ, mail-tag trống; điều kiện lỗi bị bỏ qua (không ẩn trường). Front (`blocks.js`) ẩn/hiện trực tiếp và disable trường ẩn. Editor: tạo id, giao diện “Chỉ hiện khi…”, báo lỗi điều kiện, chặn xoá trường đang được điều kiện dùng, cảnh báo khi xoá trường có mail-tag đang dùng; đổi nhãn không tự đổi `name` nếu mail đang dùng tag đó. CF7/WPForms không đổi.
- **Kiểm thử:** `php -l`, `node --check`; test PHP cô lập 17/17 (migration lặp, id trùng, rename/reorder, ẩn dây chuyền, gt, vòng lặp/thiếu/tự tham chiếu, submit trường ẩn/hiện, form cũ không id); Chromium 5/5 trên markup thật (ẩn/hiện, gửi không kèm trường ẩn, không lỗi JS); hàm editor 4/4 khớp PHP. Chưa chạy được Playground (cloud `fetch failed`), chưa thử giao diện editor trong WordPress thật.
- **Khi phát hành cần bổ sung:** Có gì mới (điều kiện trường Form); kiến thức AI Form ở G-59.

### G-58 — Báo giá cho Form native

- **Đã thay đổi:** `inc/form-pricing.php`: 7 kiểu dòng giá (cố định, số lượng, kích thước, theo lựa chọn, bậc, %, công thức giới hạn) có điều kiện, tổng tối thiểu, làm tròn, tính bằng đơn vị nhỏ nhất. Form hiện giá ước tính trực tiếp; server tính lại khi gửi và yêu cầu khách đồng ý khi giá/quy tắc đổi. Mail-tag `[price-total]`, `[price-lines]`, `[price-status]`, lưu snapshot báo giá trong entry, tab Pricing trong editor, mẫu demo giặt là/vệ sinh.
- **Kiểm thử:** PHP engine 24/24 (giặt 79.000/89.800, 1,5 và 1.5 kg, USD 2 số lẻ, option/area/bậc/công thức/tối thiểu/làm tròn, chặn biểu thức nguy hiểm); submit 6/6 (mail có giá, client sửa tổng → xác nhận lại, đổi bảng giá → gửi cấu hình mới, client không áp đơn giá); Chromium 375px 13/13 (JS = PHP 6 tình huống, trạng thái, luồng giá đổi, không tràn, không lỗi JS). Chưa chạy Playground, chưa thử tab Pricing trong editor WordPress thật. Ô số `type=number` nên dấu phẩy phụ thuộc locale trình duyệt; server nhận cả hai.
- **Khi phát hành cần bổ sung:** Có gì mới (bảng giá Form), hướng dẫn tab Pricing.

### G-59 — AI cấu hình Form và bảng giá

- **Đã thay đổi:** tool AI `form_config` (`ai-designer/includes/tools-form.php`): `read` đọc trường/quy tắc giá của khối Form; `check` chạy thử (chuẩn hoá schema, lỗi điều kiện, kiểm từng dòng giá: trường bị xoá/sai kiểu, lựa chọn không có, target % phải là dòng phía trên, công thức chỉ dùng trường và dòng phía trên), trả bảng quy tắc, ví dụ trước/sau và thay đổi, không ghi gì; `save` chỉ nhận đúng cấu hình đã check (token), giữ snapshot cho undo, không đổi trạng thái trang, trang không phải bản nháp thì cần thẻ duyệt (thẻ so sánh cấu hình Form cũ/mới). Phải khai báo nguồn giá `user`/`demo`; giá demo được gắn nhãn demo. Page tools không ghi được `fields`/`pricing` mới (giữ bản đã lưu, kể cả khi AI bỏ sót), form native mới tự có `formKey`. Kiến thức `get_knowledge(forms)` (attr, 7 kiểu dòng, mail-tag giá, mẫu theo ngành), một dòng prompt: hỏi khi thiếu giá/đơn vị, không bịa giá/thuế/chính sách, không đưa công thức vào code.
- **Kiểm thử:** `php -l`, `node --check`; test PHP cô lập 51/51 (stub WP + parser block thật của WordPress): ví dụ 3 kg = 54.000 ₫, có giao nhận + nhanh = 89.800 ₫ và server tính lại đúng từ khối đã lưu; giữ id theo tên, map tên → id; 13 loại lỗi; token sai/thiếu bị từ chối; check không ghi; mức duyệt nháp/đã đăng; page tools giữ/khôi phục cấu hình, khoá trùng; tắt giá. Thêm case vào `tools/tests/designer-integration.php` nhưng chưa chạy được (Playground lỗi `fetch failed` trên cloud); chưa thử với model AI thật.
- **Khi phát hành cần bổ sung:** Có gì mới (AI dựng Form tính giá), tài liệu AI Designer.

### G-67 — Responsive và truy cập cho section/row/column, tabs, banner (mục 1–4, 6)

- **Đã thay đổi:** Tabs: `role="tablist"/"tab"` và `aria-selected` do `blocks.js` gắn (JS tắt: mọi panel hiện, tiêu đề là nút thường), chọn tab theo class, `aria-orientation="vertical"` khi tabs dọc đang xếp cột (cập nhật khi đổi kích thước); preset tab dùng class thay `[role=tab]`. Reduced motion: tắt hiệu ứng fade panel tab, zoom ảnh banner; slider dừng autoplay khi người xem bật reduced motion sau khi tải trang. Editor Row: gợi ý dưới “Đảo thứ tự cột trên di động” rằng Tab/trình đọc màn hình giữ thứ tự gốc. Không đổi DOM hay dữ liệu đã lưu.
- **Kiểm thử:** `node tools/tests/responsive-a11y-check.cjs` (fixture PHP stub, Chromium 153, 9 viewport 375–1440 kể cả sát breakpoint 549/550/849/850/1024/1025): trước 139/144, sau 144/144. Chưa chạy WordPress/editor/screen reader thật (Playground cloud `fetch failed`); chi tiết ở [RESPONSIVE-AI-RESEARCH.md](RESPONSIVE-AI-RESEARCH.md).
- **Khi phát hành cần bổ sung:** dòng Có gì mới (tabs/reduced motion); mục 5 (cảnh báo AI) chưa làm.

### G-65 — AI giữ nguyên block không hỗ trợ khi sửa trang

- **Đã thay đổi:** `get_page` trả Query Loop, synced pattern (`core/block`), block plugin (kể cả plugin đã tắt), block có binding `metadata` và block BIGSPY đã tắt dưới dạng chỉ đọc `{ keep, readonly, about }` (không còn `raw`), kèm `version` của trang. `update_page`/`page_sections`/UX Block lấy lại đúng block đã lưu trên chính trang theo token, không tin markup của model; thiếu, nhân bản, chèn từ trang khác hoặc trang đã đổi (`version`) thì dừng trước khi ghi và liệt kê từng block. Chỉ xoá khi người dùng yêu cầu (`drop_readonly`), kết quả báo `removed_readonly`. Synced pattern báo thiếu/đã xoá/riêng tư/vòng lặp, không lộ nội dung riêng tư, không bao giờ sửa pattern. `raw` của core block được phép giờ lưu bản gốc của trang (không lưu khoảng trắng do model đổi trong `<pre>`). `page_sections` kiểm tra khoá chỉnh sửa. Quy tắc block (raw, 90%, `bst/code`, giới hạn) giữ nguyên.
- **Kiểm thử:** trước khi sửa (PHP cô lập, parser block thật WordPress 6.8 + stub): gửi lại cây đầy đủ bị từ chối; bỏ node làm mất Query Loop/synced pattern/block plugin/binding; raw sửa khoảng trắng trong `<pre>` được lưu; đổi đoạn văn có binding làm mất binding. Sau khi sửa: 42/42 (round trip giữ nguyên byte các block chỉ đọc, sửa title riêng, bỏ/thay/nhân bản/đổi chỗ, token giả/từ trang khác, raw giả, version cũ, khoá, page_sections, pattern thiếu/xoá/riêng tư/vòng lặp, block BIGSPY tắt, quy tắc block) + 7/7 hồi quy raw cũ. Thêm case vào `tools/tests/designer-integration.php`, chưa chạy được (Playground cloud `fetch failed`); chưa thử editor thật, revision thật và model AI thật. Block BIGSPY được dựng lại vẫn đổi cách mã hoá attr (`<` → `\u003c`), nên chỉ cam kết giữ byte cho block chỉ đọc.
- **Khi phát hành cần bổ sung:** Có gì mới (AI sửa trang có Query Loop/synced pattern an toàn); thao tác có cấu trúc trên block chỉ đọc chưa mở.


- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

### Khối Code đa năng hơn cho AI

- **Đã thay đổi:** hồ sơ `bst/code` trong `ai-designer/knowledge/blocks.php` viết lại: chế độ html = minh họa thay ảnh (icon/bộ icon, sơ đồ quy trình, mockup thiết bị/app, biểu đồ từ số liệu thật, hình trang trí/sóng/nền, badge, animation CSS nhẹ) kèm ưu điểm; chế độ snippet = hiện mẫu code có nhãn ngôn ngữ và nút copy; khi nào dùng block khác thay (Counter/Progress, Icon Box/List, Video/Map/Shortcode); thêm nhóm nhu cầu features/process. Sửa `min` sai (`mode visual` không tồn tại → html/snippet). Prompt, quy tắc trong `tools-design.php`, `knowledge/theme.md`, mô tả `blocks/code/block.json` (bỏ “special layouts”, bản dịch vi) và câu Block rule trong `AGENTS.md` được cập nhật cho khớp. Giới hạn an toàn giữ nguyên (không JS, ≤ 8 khối/6 KB không cần duyệt, tối đa 12 khối/40 KB, không nhiều hơn khối khác).
- **Kiểm thử:** `php -l`, JSON hợp lệ, hồ sơ đủ 9 trường như các block khác. Chưa thử với model AI thật. Mã do AI viết trong chế độ snippet vẫn qua bộ lọc HTML an toàn, nên mẫu code HTML có script/sự kiện bị lược phần đó.
- **Khi phát hành cần bổ sung:** không cần mục Có gì mới riêng (cải tiến kiến thức AI).

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
