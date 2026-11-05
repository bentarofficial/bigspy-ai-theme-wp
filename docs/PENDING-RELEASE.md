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

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
