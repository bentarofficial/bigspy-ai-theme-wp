# Thay đổi chờ phát hành

Theo quy tắc trong [DEV-RULES.md](DEV-RULES.md), đây là ghi chú nội bộ ngắn cho các thay đổi đã triển khai. Nội dung này chưa phải changelog hoặc thông báo tính năng đã phát hành.

Chỉ khi owner đồng ý đóng gói và phát hành mới tổng hợp các mục bên dưới vào Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI và ghi chú Release cần thiết.

## Các thay đổi sản phẩm đang chờ

### G-65 — AI Designer giữ nguyên block không hỗ trợ

- **Yêu cầu/tính năng:** trang có Query Loop, synced pattern (`core/block`), block plugin bên thứ ba hoặc core block ngoài danh sách AI trước đây làm `update_page` từ chối cả trang.
- **Đã thay đổi:** `get_page` đánh dấu các block này `read_only`; AI chỉ được giữ (gửi lại `raw` không đổi), di chuyển hoặc bỏ. Server so dấu vân tay với chính trang đang sửa và ghi bản gốc của trang, không dùng văn bản model gửi; `raw` bị sửa, thiếu, đã mất khỏi trang hoặc chép sang trang khác bị từ chối trước khi ghi. Không mở quyền sửa các block này; quy tắc block/`editable_percent`/`bst/code` giữ nguyên. Cập nhật mô tả tool và prompt.
- **Kiểm thử:** `tools/playground-check.cjs` 216/216 đạt, gồm 8 kiểm tra G-65 (round trip, binding metadata, khoảng trắng, di chuyển/bỏ, raw giả, chép sang trang khác, chỉ đổi tiêu đề, synced pattern không bị sửa). Chạy trên WordPress 6.6-RC3 offline vì môi trường cloud chặn tải 7.1.3; nên chạy lại trên 7.1.3 khi có máy local.
- **Khi phát hành cần bổ sung:** Có gì mới và kiến thức AI Designer: AI giữ nguyên block chỉ đọc.
- **Git:** nhánh `ccr-8b690458-vk9zem`.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

### G-57/G-58 — Form native: ID trường ổn định và trường tính giá (bước lõi)

- **Yêu cầu/tính năng:** form báo giá tự tính tiền trước khi gửi, server tính lại; theo [FORMS-BOOKING-ROADMAP.md](FORMS-BOOKING-ROADMAP.md).
- **Đã thay đổi:** mỗi trường có `id` cố định (editor tự gán; form cũ không có id dùng id suy từ mail-tag, không ghi đè dữ liệu). Loại trường mới **Tổng giá (tính tự động)** với công thức giới hạn: trường số/lựa chọn, + - * / ( ), so sánh, min/max/round/ceil/floor/if; không eval/code. Lựa chọn có giá dạng `Nhãn | 18000`; khách chỉ thấy nhãn. Công thức lưu theo id nên đổi nhãn/mail-tag/thứ tự không đổi giá; editor hiện `[mail-tag]`, báo lỗi công thức và cảnh báo khi xoá trường đang được dùng. Thiếu trường bắt buộc → “Chọn thêm thông tin để xem giá”; vòng lặp, trường đã xoá, chia 0, số âm → “Cần xác nhận giá”, không hiện tổng giả. VND không lẻ, USD/EUR 2 số lẻ, làm tròn half-up giống nhau PHP/JS. Khi gửi, server bỏ giá trị tổng từ trình duyệt và tính lại; mail-tag `[tên-trường]`, entry và `_bst_quote` (dòng, trạng thái, mã phiên bản quy tắc, thời điểm) dùng cùng kết quả.
- **Kiểm thử:** `tools/form-calc-parity.cjs` 10 case PHP = JS (gồm demo 79.000/89.800 ₫, 1,5/1.5 kg, USD, chia 0, âm, vòng lặp, code); `features-check.cjs` 135 ok, 9 kiểm tra calc mới (render, config không lộ mail, đổi tên/thứ tự, form cũ, gửi AJAX bỏ tổng giả, snapshot entry, bắt buộc, option giả). Trình duyệt 375px: tổng cập nhật realtime, không tràn ngang. WordPress 6.6-RC3 offline (cloud chặn tải 7.1.3). Chưa thử trong editor thật bằng trình duyệt.
- **Chưa làm (bước sau của G-57/G-58):** điều kiện ẩn/hiện trường, nhiều bước, bảng tra theo gói/tuyến, preset giặt là/vệ sinh, xác nhận lại khi giá đổi, bảng chi tiết từng dòng cho khách. AI tạo form tính giá thuộc G-59.
- **Khi phát hành cần bổ sung:** Có gì mới, hướng dẫn Form (cú pháp `Nhãn | giá`, công thức), kiến thức AI về Form.
- **Git:** nhánh `ccr-8b690458-vk9zem`.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.

## Mẫu ghi chú cho mỗi đợt cập nhật

- **Yêu cầu/tính năng:** mô tả ngắn.
- **Đã thay đổi:** các phần chức năng liên quan.
- **Kiểm thử:** kết quả và giới hạn cần biết.
- **Khi phát hành cần bổ sung:** mục Giới thiệu/Có gì mới/tài liệu/kiến thức AI liên quan, hoặc “không cần”.
- **Git:** nhánh và commit/link đã xác minh trên remote; ghi rõ trở ngại nếu chưa push được.
- **Trạng thái:** đã triển khai, kiểm thử, commit và push; chờ owner yêu cầu phát hành.
