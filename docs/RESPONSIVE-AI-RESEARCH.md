# Nghiên cứu responsive và truy cập cho block/AI — G-66

03-11-2026 · Đối chiếu mã BIGSPY commit `641f587`, nguồn Elementor 4.3.5 thực cài và mẫu thị trường. Phân tích tĩnh, chưa đo hiệu năng hoặc kiểm thử giao diện/AI thật. Báo cáo nguồn sáu theme ở [SOURCE-AUDIT.md](SOURCE-AUDIT.md); các đợt trước chỉ đọc một phần mã của từng sản phẩm, không phải toàn bộ.

## Bằng chứng và cơ hội

| Nội dung | Tham khảo | BIGSPY hiện có | Việc cần kiểm chứng và đề xuất |
| --- | --- | --- | --- |
| Bố cục theo thiết bị | Trên site đối chiếu, Elementor `modules/nested-tabs/widgets/nested-tabs.php:195–453` có các control responsive cho hướng, căn chỉnh, chiều rộng; chọn breakpoint chuyển tabs sang bố cục accordion. [Elementor Containers](https://elementor.com/help/responsive-design-using-containers/) mô tả hướng và thứ tự theo breakpoint. | `blocks/row/block.json` có `reverseMobile`; `blocks/column/block.json` có span desktop/tablet/mobile; `blocks/section/block.json` có padding theo ba thiết bị. | Không kết luận BIGSPY thiếu responsive. Kiểm tra editor và frontend có khớp từng block, đặc biệt breakpoint và kế thừa giá trị; khảo sát thêm tabs chuyển layout mà không làm mất nội dung hoặc đổi thứ tự đọc. |
| Thiết kế nhanh và đồng nhất | [Kadence Blocks](https://wordpress.org/plugins/kadence-blocks/) công bố responsive controls, defaults và style library; đây là mô tả nhà cung cấp, chưa benchmark. [Spectra Blocks](https://wordpress.org/plugins/spectra-blocks/) mô tả AI tạo bản nháp bằng block trong editor, hỗ trợ responsive. | `ai-designer/knowledge/blocks.php` đã cung cấp gợi ý mobile cho section, row, banner, heading, gallery và buttons; đã có block catalog, section recipes và dựng section. | Tăng chất lượng bằng kiểm tra kết quả có thể thực thi: kiểm tra tràn, hình crop, CTA và thứ tự bàn phím; không chỉ thêm prompt. Không tạo hệ preset trùng G-39. |
| Truy cập tabs | `nested-tabs.php` có responsive controls và CSS selector dựa vào `aria-selected`; điều này chưa chứng minh toàn bộ UX bàn phím. | `blocks/tabs/render.php` có `tablist`, `tab`, `aria-selected`; `assets/js/blocks.js:251–300` liên kết ARIA và xử lý phím mũi tên. Motion JS/CSS có nhánh `prefers-reduced-motion`. | Đã có nền, cần kiểm thử thực tế các trạng thái tab/accordion, focus, nội dung ẩn và reduced motion thay vì tuyên bố thiếu hoặc đạt chuẩn. |
| Hiệu năng khi lồng nhiều block | [Elementor 4.2 developer update](https://developers.elementor.com/elementor-editor-4-2-developers-update/) mô tả cải thiện hiệu năng container lồng; [Kadence](https://wordpress.org/plugins/kadence-blocks/) nói tải tài nguyên theo block sử dụng. Đây là tuyên bố nhà cung cấp. | Mã BIGSPY đã có xử lý motion và assets; đánh giá trước ở SOURCE-AUDIT đề xuất đo theo fixture, chưa đo so sánh. | G-41 tiếp tục đo trước/sau theo cùng fixture, bao gồm editor lồng nhiều block; không tách script chỉ dựa vào số file, không hứa nhanh hơn khi chưa đo. |

## Đặc tả đề xuất cho Claude — G-67

**Mục tiêu:** tạo quy trình kiểm tra responsive và khả năng truy cập cho các block do người dùng hoặc AI dựng, bắt đầu bằng section/row/column, tabs và banner. Sau kiểm chứng mới sửa vấn đề cụ thể; giữ nguyên dữ liệu cũ và quy tắc an toàn AI.

1. Lập fixture có section với hai cột và liên kết, tabs có ít nhất ba panel và banner có ảnh thật. Chạy editor/preview/frontend cùng viewport 375, 768, 1024 và desktop; thêm các điểm sát breakpoint thực tế. Ghi giá trị đang kế thừa/ghi đè và so sánh hiển thị trước/sau lưu, reload, undo. Ghi lại phiên bản WordPress, trình duyệt, theme, plugin và OS motion.
2. Kiểm tra ngang dọc, tràn ngoài vùng nhìn, crop nội dung và khả năng dùng nút. Với `reverseMobile`, so thứ tự nhìn với thứ tự DOM và Tab: nếu mâu thuẫn gây khó hiểu, cảnh báo có vị trí block và gợi ý sửa nội dung/cấu trúc; không tự đảo DOM hoặc ép một layout, vì có thể thay ý nghĩa và ảnh hưởng trang cũ.
3. Tabs: kiểm tra có nhãn, id duy nhất và liên kết tab/panel, trạng thái chọn, ẩn panel, focus, Tab và phím mũi tên, Home/End theo mẫu tương tác chọn dùng; kiểm tra nhiều tabs trên một trang, nội dung panel lồng link, khi JS tắt và chuyển viewport. Đối chiếu [WAI-ARIA Tabs Pattern](https://www.w3.org/WAI/ARIA/apg/patterns/tabs/) và, nếu triển khai accordion, [Accordion Pattern](https://www.w3.org/WAI/ARIA/apg/patterns/accordion/). Bảo đảm không mất nội dung khi chuyển; chỉ bổ sung chế độ accordion sau khi có kiểm thử và thiết kế điều khiển rõ.
4. Test `prefers-reduced-motion` bật/tắt từ đầu và khi thay đổi nếu trình duyệt hỗ trợ; nội dung luôn đọc được, điều khiển hoạt động, carousel không ép chuyển động. Kiểm tra bằng bàn phím và screen reader ở phạm vi khả thi; công cụ tự động không thay thế thử thủ công.
5. AI: trả danh sách cảnh báo có đường dẫn block, loại vấn đề, viewport và đề xuất có thể xem trước; phân biệt lỗi đo được với gợi ý. Không tự khẳng định đã kiểm tra hình ảnh, màu tương phản hay viewport nếu chưa có phép đo; không tự ghi đè trang xuất bản. Thử lại sau sửa, cho undo và giữ nguyên block ngoài phạm vi. Không truyền nội dung riêng tư cho dịch vụ ngoài để kiểm tra.
6. Điều kiện nghiệm thu: fixture mới và trang cũ không thay đổi ngoài chủ đích; render/editor thống nhất trên các viewport; tương tác keyboard/focus và reduced motion đạt các kịch bản đã liệt kê; lưu kết quả trước/sau, lỗi còn lại và môi trường. Nếu có khác biệt/không thể kiểm chứng, báo rõ thay vì tuyên bố tuân thủ WCAG toàn bộ.

**Ranh giới và thứ tự:** cải tiến editor/block/AI thuộc theme. SEO chuyên sâu, commerce, đơn/booking/đồng bộ tồn kho tiếp tục do plugin tương ứng. G-67 là đề xuất mới, không đổi thứ tự AI Designer owner đã chốt; G-41 là task đo tài nguyên hiện có, không lặp lại trong G-67.

## Kết quả G-67 (mục 1–4, 6) — 04-11-2026

**Môi trường:** Linux cloud, Chromium 153 (Playwright), PHP 8.3, Node 22; không có WordPress (Playground `fetch failed`): `tools/tests/responsive-fixture.php` render `render.php` thật với stub (wrapper attributes, kses, icon, không dynamic CSS), nạp `blocks.css` + `main.css` + `blocks.js` đầy đủ; slider là bản chép tay của `bst_slider_markup()`. Motion: chạy cả `no-preference` và `reduce`, và đổi sang `reduce` khi đang chạy. Chạy lại: `node tools/tests/responsive-a11y-check.cjs [outDir]` (report.json).

**Fixture:** section + row 2 cột có liên kết; row `reverseMobile` 7/5; 3 tabs (underline, vertical, pills+hover) mỗi tabs 3 panel có link; banner ảnh JPEG 1600×900 có link toàn khối và CTA; slider autoplay. Viewport 375, 549, 550, 768, 849, 850, 1024, 1025, 1440.

| Kiểm tra | Trước | Sau |
| --- | --- | --- |
| Tràn ngang, banner crop, CTA nhận click, cột xếp/đứng cạnh đúng breakpoint 550, nút tab ≥ 24px | đạt ở mọi viewport | đạt |
| Tabs: id duy nhất giữa 3 tabs, tab↔tabpanel, roving tabindex, panel ẩn, mũi tên vòng/Home/End, Tab vào link panel, Shift+Tab, hover, đổi viewport giữ panel | đạt | đạt |
| Tabs dọc ≥ 850px thiếu `aria-orientation="vertical"` | lỗi | sửa |
| JS tắt: 9/9 panel hiện nhưng còn 12 thuộc tính `tablist/tab/aria-selected` không hoạt động | lỗi | sửa (role do JS gắn) |
| Reduce: fade panel tab (`bs-tab-in`) và zoom ảnh banner (0.8s) vẫn chạy | lỗi | sửa |
| Bật reduce khi đang xem: slider vẫn tự chuyển | lỗi | sửa |
| `reverseMobile` @375: nhìn Ảnh→Chữ, DOM/Tab Chữ→Ảnh | khác biệt có chủ đích | giữ DOM; thêm gợi ý trong editor |

**Còn lại / chưa kiểm chứng:** editor, preview, lưu/reload/undo trong WordPress thật; screen reader; tương phản màu. `reverseMobile` chỉ đưa cột đầu xuống cuối (3 cột → 2,3,1), không đảo toàn bộ; giữ nguyên để không đổi trang cũ. Không bật lại autoplay khi người xem tắt reduce. Slider chưa có nút tạm dừng (WCAG 2.2.2) — ngoài phạm vi phiên này. Tabs vẫn để class `is-active` cho nút đầu khi JS tắt (chỉ là kiểu hiển thị). Không chứng nhận tuân thủ WCAG toàn bộ. Mục 5 (cảnh báo AI) chưa làm.
