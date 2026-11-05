# Kiểm chứng G-67 — responsive và bàn phím cho block

05-11-2026 · Nhánh `ccr-8e8729e4-pb4hk1`. Đặc tả: [RESPONSIVE-AI-RESEARCH.md](RESPONSIVE-AI-RESEARCH.md).

## Môi trường

- WordPress 6.5.5 (bản đóng gói `@wp-playground/wordpress-builds` qua npm) + SQLite, chạy bằng PHP 8.3 CLI. Không có web server: Chromium (Playwright) nhận trang qua route, file đọc từ thư mục WordPress.
- Theme BIGSPY 1.81.0 + thay đổi chờ phát hành; không plugin. Linux, Chromium headless; reduced motion giả lập bằng `reducedMotion`.
- Chưa có: WordPress 7.x, editor (bản npm thiếu JS/CSS lõi), screen reader thật, thiết bị thật, đo tương phản.

## Fixture và cách chạy

`tools/tests/g67/render-fixture.php` dựng trang full-width gồm:

- banner ảnh có H1, đoạn văn và nút;
- section với row `reverseMobile` hai cột có liên kết;
- tabs ngang 3 panel và tabs dọc 3 panel.

```
php tools/tests/g67/render-fixture.php <wp> > fixture.html
node tools/tests/g67/browser-check.cjs fixture.html <wp> <out>
php tools/tests/g67/check-page-integration.php <wp>
```

## Kết quả

| Kịch bản | Trước | Sau |
| --- | --- | --- |
| Tràn ngang 375/767/768/1023/1024/1440 px | đạt | đạt |
| CTA banner trong khung, ≥ 24×24 px | đạt | đạt |
| Tabs: id duy nhất (2 bộ), aria-controls/labelledby, ẩn panel, roving tabindex | đạt | đạt |
| Tabs: mũi tên, Home/End, vòng lại, bộ tabs kia không bị ảnh hưởng, giữ lựa chọn khi đổi viewport | đạt | đạt |
| Tab từ danh sách vào panel không có phần tử focus được (APG) | **lỗi** | đạt: panel nhận `tabindex="0"` |
| Tabs dọc báo `aria-orientation` | **lỗi**: thiếu | đạt: `vertical` khi nav xếp dọc (≥ 850px), `horizontal` khi xếp ngang |
| Reduced motion: hiệu ứng fade của panel | **lỗi**: vẫn chạy | đạt: tắt |
| JS tắt: nội dung 6 panel đọc được | đạt | đạt |
| `reverseMobile`: 375px hiện cột B trước, DOM/Tab bắt đầu ở cột A | ghi nhận | không đổi DOM; `check_page` cảnh báo kèm đường dẫn block |

`check_page` có thêm mục `blocks`, mỗi cảnh báo gồm path, block, viewport, loại (`structure` đọc từ thuộc tính, `suggestion` cần người xem) và gợi ý:

- `reverseMobile` có liên kết/nút;
- tab trống, tab không có tiêu đề hoặc trùng tiêu đề;
- tabs nhiều/dài trên mobile;
- chữ trên ảnh banner có overlay nhẹ (chưa đo tương phản);
- nút không có liên kết.

Công cụ không ghi gì vào trang.

## Còn lại

- Chưa thử trong editor, sau lưu/reload/undo trong editor, hay trên WordPress 7.x. Chưa thử screen reader thật.
- Không tuyên bố đạt WCAG toàn bộ.
- Chế độ tabs → accordion trên mobile chưa làm. Đặc tả yêu cầu thiết kế điều khiển rõ trước khi làm.
