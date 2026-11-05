# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

### G-57 — Nền schema Form

- **Yêu cầu/tính năng:** Form native có ID trường bất biến, `schemaVersion` 2, điều kiện hiện/ẩn trường và kiểm tra phụ thuộc. Đây là nền cho G-58 (tính giá).
- **Đã thay đổi:** `inc/form-schema.php` (mới): chuẩn hoá ID, kiểm tra vòng lặp/tham chiếu thiếu/trùng tên/tự tham chiếu, tính hiện/ẩn. `form-native.php`: trường ẩn theo điều kiện thì bỏ qua bắt buộc/xác thực, giá trị rỗng, không vào mail/entry. Editor gán ID cho form cũ khi mở, có UI điều kiện, chặn xoá trường đang được tham chiếu và báo lỗi điều kiện. JS phía khách ẩn/hiện, khoá control ẩn. Form cũ giữ `name`/mail-tag; ID suy từ name nên di chuyển lặp lại không đổi dữ liệu.
- **Kiểm thử:** `php tools/tests/form-schema-check.php` 19/19 đạt (đổi nhãn/tên/thứ tự, xoá trường đang dùng, vòng lặp, migration lặp). Đã thử luồng gửi trên server với WP giả lập (form cũ, trường ẩn, gửi giá trị giả cho trường ẩn) và JS phía khách trong Chromium 375px. Chưa thử editor trong Playground vì môi trường không tải được WordPress.
- **Khi phát hành cần bổ sung:** Có gì mới (điều kiện trường Form); kiến thức AI về `id`/`conditions` để ở G-59.

### G-58 — Form tính giá/báo giá native

- **Yêu cầu/tính năng:** Form native hiện giá tạm tính ngay khi khách chọn. Khi gửi, server tính lại từ cấu hình đã lưu, mail/entry dùng cùng một bản tính.
- **Đã thay đổi:**
  - `inc/form-calc.php` (mới): bộ tính công thức giới hạn, không dùng eval. Cho phép số, `{id trường}`, `+ - * / ( )`, so sánh, `&& ||` và các hàm `min/max/round/ceil/floor/abs/if`. Chặn chia 0, NaN, vượt 1e12 và tổng âm. Làm tròn half-up theo số lẻ của tiền tệ (VND 0).
  - Có loại trường `calc` (dòng giá, có thể đánh dấu là tổng). Trường chọn lựa có `optionPrices`; trường số có min/max và nhận "1,5".
  - Bản JS tương ứng trong `blocks.js` cập nhật giá realtime, chống nhảy số khi gõ nhanh; ô tổng có live region.
  - Server không đọc giá trị dòng giá do trình duyệt gửi. Mail có tag `[_quote]`; entry lưu snapshot `_bst_quote` (dòng giá, tiền tệ, revision, thời điểm, trạng thái estimate/needs_confirmation).
  - Editor: soạn công thức, bấm để chèn trường, kiểm tra nhanh lỗi, toggle tổng, giá theo lựa chọn, tiền tệ/số lẻ/ghi chú, nút mẫu giặt là (giá demo). Công thức cũng tính là phụ thuộc trong schema G-57 (chặn xoá trường đang dùng, báo vòng lặp).
- **Kiểm thử:**
  - `php tools/tests/form-calc-check.php` và `node tools/tests/form-calc-check.cjs` cùng chạy 17 fixture trong `form-calc-fixtures.json`, PHP và JS cho cùng kết quả. Fixture gồm case nghiệm thu 79.000/89.800, 1,5 kg, thiếu dữ liệu, trường ẩn, chia 0, gọi script, số âm/quá lớn, USD 2 số lẻ.
  - Luồng gửi trên server với WP giả lập: gửi tổng giả bị bỏ qua, lựa chọn lạ và số âm bị từ chối.
  - Chromium 375px: giá cập nhật đúng, không tràn ngang.
  - G-57 không hồi quy.
  - Chưa thử editor trong Playground (môi trường chặn wordpress.org).
- **Giới hạn:** giá theo lựa chọn gắn với nhãn lựa chọn, nên đổi nhãn thì phải nhập lại giá. Chưa có hạn hiệu lực quote hay xác nhận lại khi giá đổi (phần đó thuộc luồng booking G-62). JS tắt thì form không gửi được, giống trước đây.
- **Khi phát hành cần bổ sung:** Có gì mới (form tính giá); hướng dẫn công thức; kiến thức AI để ở G-59.

### G-59 — AI cấu hình Form

- **Yêu cầu/tính năng:** AI Designer đọc và sửa cấu hình Form native bằng schema (trường, điều kiện, giá lựa chọn, công thức, tiền tệ) với dry-run, diff, ví dụ và undo.
- **Đã thay đổi:**
  - `ai-designer/includes/tools-forms.php` (mới), gồm hai công cụ:
    - `get_form`: đọc form kèm ID, các vấn đề còn lỗi và ghi chú với form plugin.
    - `form_config`: mặc định dry-run, trả về diff, các lỗi và dòng giá cho dữ liệu mẫu. Chỉ lưu khi `dry_run: false`. Giữ ID theo ID cũ hoặc tên mail-tag. Từ chối schema lỗi (thiếu trường, vòng lặp, hàm lạ), snapshot để undo cả task.
  - `permissions.php`: dry-run tính là đọc. Lưu thay đổi giá/công thức luôn cần duyệt, kể cả khi đi qua `update_page`/`page_sections`/`build_sections`/`ux_block`. Sửa form trên trang đã xuất bản cũng cần duyệt.
  - `knowledge/theme.md`: quy trình form/báo giá; không bịa giá, thiếu thì hỏi; báo giá không phải booking.
- **Kiểm thử:**
  - `php tools/tests/form-integration.php <wp>` đạt 20/20 trên WordPress 6.5 + SQLite chạy bằng PHP CLI. Gồm form 1.81 cũ không hồi quy, dry-run không ghi, diff/ví dụ, mức duyệt, từ chối schema lỗi/xoá trường đang dùng, lưu giữ ID, gửi form tính 79.000 ₫, trường ẩn, snapshot entry và undo task.
  - Các test G-57/G-58 vẫn đạt.
  - Chưa chạy model AI thật (cần ngân sách được duyệt).
  - Editor JS vẫn chưa thử trong trình duyệt.
- **Khi phát hành cần bổ sung:** Có gì mới (AI cấu hình form/báo giá).

### Thanh miễn phí vận chuyển khớp điều kiện WooCommerce (SOURCE-AUDIT §3, P1)

- **Yêu cầu/tính năng:** thanh chỉ báo "đủ điều kiện" khi WooCommerce thật sự cho Free shipping, và chỉ hiện "thêm X" khi chỉ cần mua thêm là được.
- **Đã thay đổi:** `inc/checkout-extras.php` có thêm hai hàm:
  - `bst_free_shipping_state()`: trạng thái "đủ điều kiện" lấy từ `is_available()` của phương thức Free shipping cho từng package. Tiền còn thiếu tính giống WooCommerce: xét `ignore_discounts`, thuế giảm giá và làm tròn theo số lẻ của shop. `either` có coupon thì đủ điều kiện. `coupon` hoặc `both` thiếu coupon thì không hiện gì. Có package ở zone không có Free shipping thì không hiện. Đủ số tiền mà WooCommerce vẫn không cho (do filter) thì không hứa.
  - `bst_free_shipping_cart_total()`: tính tổng giỏ hàng theo cách WooCommerce dùng.
  - Thanh tiến độ không chạm 100% trước khi đủ điều kiện. Số tiền nhập tay trong Customizer vẫn ưu tiên như trước.
  - Đã bỏ hàm `bst_free_shipping_min()` (không nơi nào còn gọi).
- **Kiểm thử:** `php tools/tests/free-shipping-check.php` đạt 18/18. Stub mô phỏng quyết định của `WC_Shipping_Free_Shipping::is_available()`. Chưa chạy với WooCommerce thật (môi trường không tải được plugin).
- **Giới hạn:** `both` khi đã có coupon nhưng thiếu tiền vẫn chưa hiện "thêm X" (giữ thận trọng). Cart/Checkout blocks vẫn chưa hỗ trợ, như trước.
- **Khi phát hành cần bổ sung:** Có gì mới (sửa thông báo miễn phí vận chuyển).

### Ảnh URL trực tiếp trong Banner/Image không còn luôn lazy (SOURCE-AUDIT P1)

- **Yêu cầu/tính năng:** ảnh hero dùng URL (không có Media ID) bị ghi cứng `loading="lazy"`, làm chậm LCP.
- **Đã thay đổi:**
  - `bst_url_img_loading()` trong `inc/blocks.php` dùng `wp_get_loading_optimization_attributes()` của WordPress, cùng bộ đếm với ảnh Media. Kết quả: ảnh nội dung đầu tiên có `fetchpriority="high"`, vài ảnh đầu không lazy, các ảnh sau lazy.
  - Kích thước giả định chỉ dùng để quyết định thứ tự, không in `width/height`.
  - Áp dụng cho `blocks/banner/render.php` và `blocks/image/render.php`. Ảnh có Media ID không đổi.
- **Kiểm thử:** `php tools/tests/image-loading-integration.php <wp>` đạt 6/6 trên WordPress 6.5 CLI. Chưa đo LCP/CLS trong trình duyệt. Các block khác dùng URL (gallery, icon-box…) vẫn lazy như cũ.
- **Khi phát hành cần bổ sung:** Có gì mới (tốc độ ảnh hero).

### G-67 — Responsive và bàn phím cho tabs/row/banner

- **Đã thay đổi:**
  - Tabs:
    - Panel không có phần tử focus được thì nhận `tabindex="0"`.
    - `aria-orientation` theo bố cục đang hiện (tabs dọc ≥ 850px).
    - Tắt fade khi bật giảm chuyển động.
  - AI `check_page` trả thêm cảnh báo theo đường dẫn block: `reverseMobile` có liên kết, tabs trống/trùng/dài, chữ trên ảnh banner overlay nhẹ, nút không có link. AI chỉ báo, không tự sửa.
- **Kiểm thử:** `tools/tests/g67/` — trình duyệt 6 viewport + bàn phím + JS tắt + reduced motion đạt; `check_page` 7/7. Chi tiết, môi trường và giới hạn: [VERIFICATION-G67.md](VERIFICATION-G67.md).
- **Khi phát hành cần bổ sung:** Có gì mới (trợ năng tabs, cảnh báo responsive của AI).

### G-41 — Giảm JS/CSS tải trên mọi trang

- **Đã thay đổi:**
  - `assets/js/form.js` (mới) tách code Form khỏi `blocks.js`, chỉ tải khi có block Form.
  - `legacy.css` bị bỏ khỏi trang không có class nào nó style (`inc/performance.php`, khi bật tách CSS).
  - Thêm 9 họ CSS cho block do PHP render trong `tools/split-blocks-css.py`; core CSS 67,8 → 52,3 KB.
- **Kiểm thử:** `tools/tests/g41/` (render + coverage Chromium): trang chữ JS 64,0 → 48,4 KB, CSS 139,2 → 109,1 KB; landing/form CSS −20%. Hành vi đạt; các test G-57/58/59/67 không hồi quy. Chi tiết: [VERIFICATION-G41.md](VERIFICATION-G41.md).
- **Khi phát hành cần bổ sung:** Có gì mới (trang nhẹ hơn). Cần thử trên site thật có nội dung Flatsome cũ để chắc `legacy.css` vẫn được tải ở đó.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
