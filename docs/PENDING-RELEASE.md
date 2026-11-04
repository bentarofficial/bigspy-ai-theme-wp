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
- **Kiểm thử:** mục này bổ sung ở phiên G-59 từ commit `5397952`; kết quả kiểm thử của phiên G-58 không được ghi lại ở đây. Test G-59 dùng lại `bst_pricing_quote` và cho kết quả đúng với ví dụ giặt là.
- **Khi phát hành cần bổ sung:** Có gì mới (bảng giá Form), hướng dẫn tab Pricing.

### G-59 — AI cấu hình Form và bảng giá

- **Đã thay đổi:** tool AI `form_config` (`ai-designer/includes/tools-form.php`): `read` đọc trường/quy tắc giá của khối Form; `check` chạy thử (chuẩn hoá schema, lỗi điều kiện, kiểm từng dòng giá: trường bị xoá/sai kiểu, lựa chọn không có, target % phải là dòng phía trên, công thức chỉ dùng trường và dòng phía trên), trả bảng quy tắc, ví dụ trước/sau và thay đổi, không ghi gì; `save` chỉ nhận đúng cấu hình đã check (token), giữ snapshot cho undo, không đổi trạng thái trang, trang không phải bản nháp thì cần thẻ duyệt (thẻ so sánh cấu hình Form cũ/mới). Phải khai báo nguồn giá `user`/`demo`; giá demo được gắn nhãn demo. Page tools không ghi được `fields`/`pricing` mới (giữ bản đã lưu, kể cả khi AI bỏ sót), form native mới tự có `formKey`. Kiến thức `get_knowledge(forms)` (attr, 7 kiểu dòng, mail-tag giá, mẫu theo ngành), một dòng prompt: hỏi khi thiếu giá/đơn vị, không bịa giá/thuế/chính sách, không đưa công thức vào code.
- **Kiểm thử:** `php -l`, `node --check`; test PHP cô lập 51/51 (stub WP + parser block thật của WordPress): ví dụ 3 kg = 54.000 ₫, có giao nhận + nhanh = 89.800 ₫ và server tính lại đúng từ khối đã lưu; giữ id theo tên, map tên → id; 13 loại lỗi; token sai/thiếu bị từ chối; check không ghi; mức duyệt nháp/đã đăng; page tools giữ/khôi phục cấu hình, khoá trùng; tắt giá. Thêm case vào `tools/tests/designer-integration.php` nhưng chưa chạy được (Playground lỗi `fetch failed` trên cloud); chưa thử với model AI thật.
- **Khi phát hành cần bổ sung:** Có gì mới (AI dựng Form tính giá), tài liệu AI Designer.


- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
