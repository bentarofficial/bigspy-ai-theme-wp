# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

### G-57 — Nền schema Form native

- **Yêu cầu/tính năng:** ID trường bất biến, version schema, điều kiện hiện/bắt buộc theo trường khác và kiểm tra phụ thuộc; giữ form/mail-tag cũ.
- **Đã thay đổi:** `inc/form-schema.php` mới (ID `f_<name>` cho trường cũ, idempotent; luật `is/is_not/empty/not_empty/contains/gt/lt`, all/any; báo trùng tên, tham chiếu thiếu, tự tham chiếu, vòng lặp, toán tử lạ). Server tính lại trạng thái khi gửi: trường ẩn không bắt buộc và giá trị bị bỏ (kể cả request cố gửi). Markup render đúng trạng thái ban đầu; `blocks.js` cập nhật ẩn/hiện/bắt buộc khi nhập. Editor: mục “Hiện trường này” và “Chỉ bắt buộc khi…”, cảnh báo lỗi trên từng trường, chặn xoá trường đang được điều kiện dùng. Thuộc tính `formSchema`. Luật lỗi bị bỏ qua trên site (trường vẫn hiện, không mất dữ liệu).
- **Kiểm thử:** `php tools/tests/form-schema-check.php` 21/21 (rename/reorder/ẩn/xoá, migration lặp, vòng lặp). Harness PHP cho submit: 4 case đạt. Chromium chạy markup + `blocks.js`: ẩn/hiện/required đúng, không lỗi JS. **Chưa** chạy Playground/editor thật (wordpress.org bị chặn trong môi trường này); UI editor mới chỉ qua kiểm tra cú pháp.
- **Khi phát hành cần bổ sung:** Có gì mới (điều kiện trường Form), kiến thức AI về `show`/`requiredWhen`.
- **Git:** nhánh `ccr-6e0ee3d0-m48rjp`.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

### G-58 — Báo giá native cho Form

- **Yêu cầu/tính năng:** form hiện giá trước khi gửi; server tính lại; bảng giá realtime; lưu bản báo giá; preset giặt là/vệ sinh.
- **Đã thay đổi:** `inc/form-quote.php` mới: công thức giới hạn (số, + − × ÷, so sánh, min/max/round/ceil/floor/if, `{f_…}`, `{f_…=Lựa chọn}`, `{l_…}`), không eval; giá theo lựa chọn ("Lựa chọn = số tiền"); làm tròn theo dòng, chính sách v1; chặn chia 0, số âm, vượt 1e12, tổng âm, cú pháp lạ, vòng lặp dòng → “Cần xác nhận giá”; thiếu trường bắt buộc hoặc chưa có gì tính tiền → “Chọn thêm thông tin”, không hiện 0 ₫. Khi gửi: server tính lại, bỏ mọi số client gửi; revision bảng giá khác bản khách đang xem thì từ chối và báo xem lại. Mail-tag `[_quote]`, `[_quote_total]`; `[all-fields]` có thêm bảng giá; entry lưu `_bst_quote` (dòng, tổng, tiền tệ, rev, policy, thời điểm) và hiện trong màn hình entry. Front: hộp giá `aria-live` cập nhật khi nhập; JS lỗi/tắt thì báo giá tính khi gửi. JS engine dùng chung `assets/js/form-quote.js` (chỉ nạp khi form có giá). Editor: thẻ “Giá” (bật/tắt, ký hiệu, số lẻ, dòng giá, chèn trường, báo lỗi theo dòng, thử tính với dữ liệu nhập), ô “Giá theo lựa chọn”, chặn xoá trường đang dùng trong công thức, 2 preset giá demo. Form cũ không bật giá thì không đổi.
- **Kiểm thử:** `form-quote-check.php` 30/30 (case nghiệm thu 1, 2, 4, 5: 79.000 / 89.800, 1,5 kg, USD 2 số lẻ, input giả mạo, chia 0, script); `form-quote-parity.cjs` 126/126 JS = PHP; `form-schema-check.php` 21/21; harness submit (bỏ tổng giả, từ chối rev cũ); Chromium 375px không tràn, không lỗi JS. **Chưa** chạy WordPress/editor thật (wordpress.org bị chặn trong môi trường này); thẻ “Giá” trong editor mới qua kiểm tra cú pháp. Chưa làm: hạn hiệu lực quote, UI nhiều bước, bảng tra/bậc giá dạng bảng (hiện viết bằng `if`/`max`).
- **Khi phát hành cần bổ sung:** Có gì mới (form báo giá), kiến thức AI về `pricing`/`prices` (G-59).
- **Git:** nhánh `ccr-6e0ee3d0-m48rjp`.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
