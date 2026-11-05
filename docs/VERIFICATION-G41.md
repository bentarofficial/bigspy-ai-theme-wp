# Kiểm chứng G-41 — đo và giảm JS/CSS không dùng

05-11-2026 · Nhánh `ccr-8e8729e4-pb4hk1`.

## Cách đo

- WordPress 6.5.5 + SQLite chạy bằng PHP CLI, theme BIGSPY 1.81.0 + thay đổi chờ phát hành, không plugin. Chromium headless 375×800.
- Trang được render bằng `tools/tests/g41/render-page.php`, có chạy bộ lọc HTML tách CSS của theme như với khách thật. Ba trang mẫu:
  - `text`: bài viết chỉ có chữ;
  - `landing`: banner, tabs, accordion, counter;
  - `form`: form tính giá có điều kiện.
- `tools/tests/g41/measure.cjs` đo byte JS/CSS của theme (gồm file CSS sinh trong uploads) và phần không dùng theo coverage của Chromium, sau khi bấm tab, accordion và nhập form.
- "Không dùng" chỉ tính trên một lượt xem với các thao tác đó; hover, breakpoint khác hay trạng thái lỗi vẫn cần phần còn lại. Đây không phải số đo tốc độ tải.

## Kết quả (byte chưa nén)

| Trang | JS trước | JS sau | CSS trước | CSS sau | Hành vi |
| --- | --- | --- | --- | --- | --- |
| text | 64,0 KB | 48,4 KB (−24%) | 139,2 KB | 109,1 KB (−22%) | đạt |
| landing | 64,0 KB | 48,4 KB (−24%) | 149,1 KB | 119,1 KB (−20%) | đạt |
| form | 64,0 KB | 64,2 KB | 158,8 KB | 128,8 KB (−19%) | đạt (giá 54.000 ₫, điều kiện hiện trường) |

## Thay đổi

1. **`form.js` tách khỏi `blocks.js`:** code Form (redirect CF7, điều kiện G-57, giá G-58, gửi form native) chỉ tải khi block Form render (`wp_enqueue_script( 'bst-form' )` trong `blocks/form/render.php`).
2. **`legacy.css` chỉ tải khi cần:** bộ lọc HTML của tuỳ chọn "Load block styles only on pages that use them" bỏ link `legacy.css` khi không selector nào của file có thể khớp class trên trang. Selector được đọc từ chính file, cache theo thời gian sửa file. Class Flatsome thường (`text-center`, `hidden`…) vẫn giữ file.
3. **Thêm họ CSS cho block do PHP render:** `posts`, `image-box`, `icon-box`, `icon-list`, `product-list`, `shape`, `mx-bg`, `mx-shine`, `mx-reveal`. Phần "core" luôn tải giảm từ 67,8 KB xuống 52,3 KB. Không thêm class do JS tạo lúc chạy (`bs-lightbox`, `bs-cursor`…) để tránh thiếu CSS. Thứ tự cascade giữ nguyên theo `segments.json`.

## Hồi quy

- G-67: trình duyệt 6 viewport, bàn phím, JS tắt, reduced motion đạt.
- Đạt các test: `form-integration` 20/20, `form-calc` PHP/JS, `image-loading`, `check-page`.

## Chưa làm / giới hạn

- `main.css` (56 KB, ~90% không dùng trên các trang mẫu) và phần core còn lại dùng chung cho header/footer/nút. Tách thêm cần fixture header/menu/shop và WooCommerce thật.
- `blocks.js` vẫn gộp slider/tabs/accordion/counter… Tách tiếp theo block cần đo thêm trang có slider/popup.
- Chưa đo trong editor, chưa đo LCP/thời gian tải trên hosting.
