# Bàn giao — G-59 (AI cấu hình Form và bảng giá)

Nhánh: `claude/pending-updates-1ts5u5`. Đang chờ phát hành (chưa phát hành, owner đã nói “chưa”): sửa ảnh URL/free shipping, G-57, G-58 — xem `docs/PENDING-RELEASE.md`. Không hỏi lại về phát hành.

## Đã có (đọc trước khi code)

- `bigspy-ai-theme-wp/inc/form-schema.php` (G-57): `bst_form_schema_normalize()` (id cố định, `n_<name>` cho form cũ), `bst_form_when_clean()`, `bst_form_schema_errors()` (missing/self/cycle), `bst_form_schema_used_by()`, `bst_form_visibility()`.
- `bigspy-ai-theme-wp/inc/form-pricing.php` (G-58): `bst_pricing_settings()`, `bst_pricing_quote()`, `bst_pricing_eval()` (biểu thức giới hạn), `bst_pricing_check_expr()`, `bst_pricing_revision()`, định dạng/mail text. Attr khối `bst/form`: `fields`, `pricing`, `schemaVersion`, `formKey`, `provider` = `bigspy` cho form native.
- Editor: `assets/js/blocks-editor.js` quanh `function NativeFormPanels` (tab Fields/Pricing; mẫu demo giặt là/vệ sinh trong `preset()`). JS phía khách: `assets/js/blocks.js` (`window.bstNF`, `window.bstPricing`).
- AI Designer: `ai-designer/includes/tools-design.php` (tool list ~dòng 880–940, `bst_aid_tool_block_schema`, `bst_aid_tool_update_page`), `ai-designer/knowledge/blocks.php:72` (mục `bst/form` chỉ có `provider, title`), `prompt.php` (quy tắc block phải đứng cuối — xem AGENTS.md).

## Phạm vi G-59 (đặc tả: docs/FORMS-BOOKING-ROADMAP.md mục 7 và bảng mục 8)

1. Tool AI đọc/ghi cấu hình Form theo schema: tạo/sửa `fields` + `pricing` của một khối `bst/form` có sẵn hoặc mới; chạy normalize + `bst_form_schema_errors` + kiểm tra từng dòng giá (`bst_pricing_check_expr`, trường bị xoá, target %) trước khi ghi; dry-run trả bảng quy tắc và ví dụ tính (`bst_pricing_quote` với dữ liệu thử) để admin duyệt.
2. Có snapshot/undo theo cơ chế sẵn có của AI Designer; bản nháp không tự công khai, không ghi đè giá đang dùng khi chưa duyệt.
3. Kiến thức AI: mô tả attr `fields/pricing`, 7 kiểu dòng, mail-tag giá, mẫu theo ngành (xem `docs/PRICING-MODELS-RESEARCH.md`, G-69 là việc riêng). AI phải hỏi khi thiếu giá/đơn vị; không bịa giá, thuế, chính sách; giá mẫu ghi demo.
4. Không chèn công thức/JS vào Code block; giữ quy tắc block của AGENTS.md.

## Kiểm tra

Playground không tải được WordPress trong cloud (`fetch failed`). Đã dùng test PHP cô lập (stub WP) + Playwright trên markup; các script nằm ở scratchpad phiên trước, không có trong repo. Ghi rõ giới hạn khi bàn giao.
