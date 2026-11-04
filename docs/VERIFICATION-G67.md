# Kiểm chứng G-67 — responsive và bàn phím (04-11-2026)

**Môi trường:** Playground CLI 3.1.62, WordPress 6.5 (bản zip trong `@wp-playground/wordpress-builds`; wordpress.org bị chặn trong container nên không chạy được 7.1.3), PHP 8.3, Chromium headless của Playwright, Linux, chặn request ra ngoài (font CDN). Theme tại commit `112af5b`.

**Fixture (trang `g67`):** `bst/section` > `bst/row` `reverseMobile` hai cột, mỗi cột có nút; `bst/tabs` ba panel (tiêu đề dài, panel 1 có link); `bst/banner` ảnh `hero.svg`, heading dài và nút.

**Frontend, viewport 320 / 375 / 549 / 550 / 768 / 849 / 850 / 1024 / 1440:**

| Kiểm tra | Kết quả |
| --- | --- |
| Cuộn ngang trang, phần tử tràn khỏi viewport | Không có ở mọi viewport |
| Row ≤549px | Xếp dọc, cột 1 hiển thị sau (`reverseMobile` đúng); ≥550px hai cột cùng hàng |
| Tabs nav | Không tràn; tiêu đề dài xuống dòng ở 375px, không bị cắt |
| Banner | Cao 340 / 420 / 500px theo mobile / tablet / desktop, nội dung không bị cắt (scrollHeight = height) |
| Màu chữ panel | `rgb(58,63,71)` sau khi fade xong |

**Bàn phím (375px):** thứ tự Tab là "Mua ngay hôm nay" rồi "Xem cửa hàng" trong khi trên màn hình "Xem cửa hàng" ở trên — đúng loại mâu thuẫn mà cảnh báo `reverseMobile` của AI Designer báo. Tabs chỉ chiếm một điểm dừng Tab, sau đó tới link trong panel; phím mũi tên/Home/End đã thử riêng (commit `fc04765`).

**Chưa kiểm:** editor (canvas) và so trước/sau lưu–reload–undo; WordPress 7.x; screen reader; JS tắt; đổi `prefers-reduced-motion` khi trang đang mở; trang cũ ngoài fixture. Không tuyên bố đạt WCAG toàn bộ.
