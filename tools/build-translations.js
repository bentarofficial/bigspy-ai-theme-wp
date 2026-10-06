/**
 * Builds the Vietnamese front-end translation of BIGSPY Theme.
 *
 * Front-end strings and the guided BIGSPY Studio admin workflow are translated.
 * Generic admin and developer-only labels are intentionally left out.
 * Run: node tools/build-translations.js
 * Output: bigspy-theme/languages/vi.po, vi.mo and vi.l10n.php
 */
const fs = require('fs');
const path = require('path');

const outDir = path.join(__dirname, '..', 'bigspy-theme', 'languages');

// msgid => msgstr. Plurals: [singular, plural] => [form] (Vietnamese has one plural form).
const strings = [
	['Site tools: 404 & maintenance', 'Công cụ website: 404 & bảo trì'],
	['Highlight menu links to page sections', 'Đánh dấu menu theo phần đang xem'],
	['Set an HTML anchor on a block, then link to #anchor in your menu. The active section is highlighted as you scroll.', 'Đặt neo HTML cho block, rồi thêm liên kết #neo vào menu. Mục tương ứng được đánh dấu khi cuộn trang.'],
	['Page design for 404 errors', 'Trang thiết kế cho lỗi 404'],
	['Only public, published pages without a password are shown. The response remains 404. Select no page to use the default design.', 'Chỉ hiển thị trang công khai đã xuất bản và không có mật khẩu. Phản hồi vẫn là 404. Không chọn trang để dùng thiết kế mặc định.'],
	['Show maintenance screen to visitors', 'Hiển thị màn hình bảo trì cho khách'],
	['Administrators can still view the site. Visitors receive HTTP 503. Login, admin and API endpoints remain available. Purge your page/CDN cache when switching this option.', 'Quản trị viên vẫn xem được website. Khách nhận phản hồi HTTP 503. Đăng nhập, quản trị và API vẫn hoạt động. Xóa cache trang/CDN khi bật hoặc tắt tùy chọn này.'],
	['Page design for maintenance', 'Trang thiết kế cho bảo trì'],
	['Build a published page with blocks. Its content is shown without the site header/footer. Select no page to use the default screen.', 'Thiết kế một trang bằng block và xuất bản. Nội dung hiển thị không kèm đầu/chân trang website. Không chọn trang để dùng màn hình mặc định.'],
	['Maintenance message', 'Thông báo bảo trì'],
	['Used on the default maintenance screen.', 'Dùng cho màn hình bảo trì mặc định.'],
	['We will be back soon', 'Chúng tôi sẽ trở lại sớm'],
	['Our website is being updated. Please check back soon.', 'Website đang được cập nhật. Vui lòng quay lại sau ít phút.'],
	['BIGSPY maintenance mode is enabled. Visitors cannot browse the storefront.', 'BIGSPY đang bật chế độ bảo trì. Khách chưa thể xem cửa hàng.'],
	['Manage maintenance mode', 'Quản lý chế độ bảo trì'],
	['Select a block in the page to edit its layout.', 'Chọn một block trên trang để chỉnh bố cục.'],
	['Duplicate', 'Nhân bản'],
	['Move up', 'Di chuyển lên'],
	['Move down', 'Di chuyển xuống'],
	['Copy appearance', 'Sao chép kiểu dáng'],
	['Paste appearance', 'Dán kiểu dáng'],
	['Use the block menu to create a reusable pattern. Saved patterns appear under Saved designs.', 'Dùng menu của block để tạo mẫu tái sử dụng. Các mẫu đã lưu nằm trong mục Mẫu đã lưu.'],
	['BIGSPY Studio', 'BIGSPY Studio'],
	['DESIGN YOUR WEBSITE', 'THIẾT KẾ WEBSITE CỦA BẠN'],
	['Start with a page. Make it yours.', 'Bắt đầu từ một trang mẫu, rồi biến nó thành website của bạn.'],
	['Choose a starting point. BIGSPY creates an unpublished draft and opens the editor, so your live website stays as it is while you design.', 'Chọn điểm bắt đầu. BIGSPY tạo bản nháp chưa xuất bản và mở trình soạn thảo để bạn thiết kế mà không ảnh hưởng website đang hoạt động.'],
	['Build from scratch', 'Tự dựng từ đầu'],
	['Business or service website', 'Website doanh nghiệp hoặc dịch vụ'],
	['A complete homepage with an introduction, services, about section, testimonials, pricing, FAQ and contact.', 'Trang chủ hoàn chỉnh gồm phần giới thiệu, dịch vụ, về chúng tôi, cảm nhận khách hàng, bảng giá, câu hỏi thường gặp và liên hệ.'],
	['Online shop', 'Cửa hàng trực tuyến'],
	['A shop homepage with a hero, benefits, categories, product collections and more.', 'Trang chủ cửa hàng gồm banner chính, lợi ích, danh mục, bộ sưu tập sản phẩm và nhiều phần khác.'],
	['Page name', 'Tên trang'],
	['Create draft and open editor', 'Tạo bản nháp và mở trình soạn thảo'],
	['Your design stays in draft until you publish it.', 'Thiết kế vẫn là bản nháp cho đến khi bạn xuất bản.'],
	['Replace the example text and images, adjust the layout, preview the page, then publish when it is ready.', 'Thay nội dung và hình ảnh mẫu, chỉnh bố cục, xem trước trang rồi xuất bản khi đã sẵn sàng.'],
	['Business homepage', 'Trang chủ doanh nghiệp'],
	['Online shop homepage', 'Trang chủ cửa hàng trực tuyến'],
	['Sorry, you are not allowed to create pages.', 'Bạn không có quyền tạo trang.'],
	['This starter page is not available.', 'Mẫu trang này hiện không khả dụng.'],
	['The selected BIGSPY starter pattern could not be found.', 'Không tìm thấy mẫu trang BIGSPY đã chọn.'],
	['All designs', 'Tất cả mẫu thiết kế'],
	['Full pages', 'Trang hoàn chỉnh'],
	['Sections', 'Các phần nội dung'],
	['Shop', 'Cửa hàng'],
	['Soft focus', 'Làm rõ nhẹ'],
	['Gentle rotate', 'Xoay nhẹ'],
	['Soft bounce', 'Nảy nhẹ'],
	['Duration (ms)', 'Thời lượng (ms)'],
	['Effect on hover', 'Hiệu ứng khi rê chuột'],
	['Lift gently', 'Nhấc nhẹ'],
	['Soft zoom', 'Phóng to nhẹ'],
	['Soft shadow', 'Bóng đổ nhẹ'],
	['Grayscale to color', 'Từ trắng đen sang màu'],
	['Preview animation', 'Xem thử hiệu ứng'],
	['Saved designs', 'Mẫu đã lưu'],
	['Untitled design', 'Mẫu chưa đặt tên'],
	['A reusable design saved in WordPress.', 'Mẫu có thể tái sử dụng đã lưu trong WordPress.'],
	['Added as a synced design. Changes to its source update every copy.', 'Đã thêm dưới dạng mẫu đồng bộ. Khi sửa mẫu gốc, các bản dùng mẫu này cũng được cập nhật.'],
	['No saved designs yet. In the editor, open a block’s options and choose Create pattern to save a design here.', 'Chưa có mẫu đã lưu. Trong trình soạn thảo, mở tùy chọn của block và chọn Tạo mẫu để lưu thiết kế tại đây.'],
	['Choose a ready-made design, then change its text, images and colors on the page.', 'Chọn mẫu có sẵn rồi thay nội dung, hình ảnh và màu sắc ngay trên trang.'],
	['Search designs', 'Tìm mẫu thiết kế'],
	['Try “hero”, “services” or “products”…', 'Thử tìm “banner”, “dịch vụ” hoặc “sản phẩm”…'],
	['Design categories', 'Nhóm mẫu thiết kế'],
	['This design could not be added. Please try another one.', 'Không thể chèn mẫu này. Vui lòng thử mẫu khác.'],
	['Added to your page. Edit the text and images in the preview.', 'Đã thêm vào trang. Bạn có thể sửa nội dung và hình ảnh trong vùng xem trước.'],
	['Add to page', 'Thêm vào trang'],
	['No designs found. Try another search.', 'Không tìm thấy mẫu phù hợp. Hãy thử từ khóa khác.'],
	['Adding a design does not publish the page. Use Preview, then Save draft or Publish when it is ready.', 'Thêm mẫu không xuất bản trang. Hãy xem trước, rồi lưu nháp hoặc xuất bản khi đã hoàn tất.'],
	['Skip to content', 'Chuyển đến nội dung'],
	['Search…', 'Tìm kiếm…'],
	['Search products…', 'Tìm sản phẩm…'],
	['Search for:', 'Tìm kiếm:'],
	['Submit search', 'Tìm'],
	['Close search', 'Đóng tìm kiếm'],
	['Open menu', 'Mở menu'],
	['Close menu', 'Đóng menu'],
	['Mobile menu', 'Menu di động'],
	['My account', 'Tài khoản'],
	['Login', 'Đăng nhập'],
	['Cart', 'Giỏ hàng'],
	['Previous', 'Trước'],
	['Next', 'Tiếp'],
	['Back to top', 'Lên đầu trang'],
	['Call now', 'Gọi ngay'],
	['Home', 'Trang chủ'],
	['Blog', 'Tin tức'],
	['Breadcrumb', 'Đường dẫn'],
	['Sidebar', 'Thanh bên'],
	['Page not found', 'Không tìm thấy trang'],
	['Search results for “%s”', 'Kết quả tìm kiếm cho “%s”'],
	['Share:', 'Chia sẻ:'],
	['Copy link', 'Sao chép liên kết'],
	['Copy', 'Sao chép'],
	['Copied!', 'Đã sao chép!'],
	['Related posts', 'Bài viết liên quan'],
	['Comments are closed.', 'Bình luận đã đóng.'],
	['Oops! That page can not be found.', 'Rất tiếc! Không tìm thấy trang này.'],
	['The page you are looking for may have been moved or deleted. Try searching instead.', 'Trang bạn tìm có thể đã được chuyển hoặc xóa. Hãy thử tìm kiếm.'],
	['Back to home', 'Về trang chủ'],
	['Nothing matched your search. Please try different keywords.', 'Không có kết quả phù hợp. Vui lòng thử từ khóa khác.'],
	['There is nothing here yet.', 'Chưa có nội dung.'],
	['Load more', 'Xem thêm'],
	['Read more', 'Đọc tiếp'],
	// Default option labels (see bst_translatable_defaults()).
	['Contact us', 'Liên hệ'],
	['Ready to start your project?', 'Bạn đã sẵn sàng bắt đầu?'],
	['Get a free consultation and a quote within 24 hours.', 'Nhận tư vấn miễn phí và báo giá trong vòng 24 giờ.'],
	['Copyright © [year] [site_title]. All rights reserved.', 'Bản quyền © [year] [site_title]. Bảo lưu mọi quyền.'],
	['Contact for price', 'Liên hệ để biết giá'],
	['Loading…', 'Đang tải…'],
	['Sold out', 'Hết hàng'],
	['Sale', 'Giảm giá'],
	['New', 'Mới'],
	['Hot', 'Hot'],
	['No results found.', 'Không tìm thấy kết quả.'],
	['View all results', 'Xem tất cả kết quả'],
	['No posts found.', 'Chưa có bài viết.'],
	['No products found.', 'Chưa có sản phẩm.'],
	['Open link', 'Mở liên kết'],
	['Call to action', 'Kêu gọi hành động'],
	['Rated %d out of 5', 'Được đánh giá %d trên 5'],
	[['%s comment', '%s comments'], ['%s bình luận']],
	[['%s product', '%s products'], ['%s sản phẩm']],
];

const header = [
	'Project-Id-Version: BIGSPY Theme',
	'Language: vi',
	'MIME-Version: 1.0',
	'Content-Type: text/plain; charset=UTF-8',
	'Content-Transfer-Encoding: 8bit',
	'Plural-Forms: nplurals=1; plural=0;',
	'X-Domain: bigspy-theme',
	'',
].join('\n');

const q = s => '"' + s.replace(/\\/g, '\\\\').replace(/"/g, '\\"').replace(/\n/g, '\\n') + '"';

// ---- .po
let po = 'msgid ""\nmsgstr ""\n' + header.split('\n').filter(Boolean).map(l => q(l + '\n')).join('\n') + '\n\n';
for (const [id, str] of strings) {
	if (Array.isArray(id)) {
		po += 'msgid ' + q(id[0]) + '\nmsgid_plural ' + q(id[1]) + '\n' + str.map((s, i) => 'msgstr[' + i + '] ' + q(s)).join('\n') + '\n\n';
	} else {
		po += 'msgid ' + q(id) + '\nmsgstr ' + q(str) + '\n\n';
	}
}
fs.writeFileSync(path.join(outDir, 'vi.po'), po);

// ---- .mo (GNU gettext binary, little endian, no hash table)
const entries = [['', header]].concat(strings.map(([id, str]) => Array.isArray(id) ? [id.join('\0'), str.join('\0')] : [id, str]));
entries.sort((a, b) => Buffer.compare(Buffer.from(a[0]), Buffer.from(b[0])));
const ids = entries.map(e => Buffer.from(e[0], 'utf8'));
const strs = entries.map(e => Buffer.from(e[1], 'utf8'));
const n = entries.length;
const headerSize = 28;
const idTable = headerSize;
const strTable = idTable + n * 8;
let offset = strTable + n * 8;
const mo = [];
const tables = Buffer.alloc(n * 16);
ids.forEach((b, i) => { tables.writeUInt32LE(b.length, i * 8); tables.writeUInt32LE(offset, i * 8 + 4); offset += b.length + 1; });
strs.forEach((b, i) => { tables.writeUInt32LE(b.length, n * 8 + i * 8); tables.writeUInt32LE(offset, n * 8 + i * 8 + 4); offset += b.length + 1; });
const head = Buffer.alloc(headerSize);
head.writeUInt32LE(0x950412de, 0);
head.writeUInt32LE(0, 4);
head.writeUInt32LE(n, 8);
head.writeUInt32LE(idTable, 12);
head.writeUInt32LE(strTable, 16);
head.writeUInt32LE(0, 20);
head.writeUInt32LE(offset, 24);
mo.push(head, tables);
ids.forEach(b => mo.push(b, Buffer.from([0])));
strs.forEach(b => mo.push(b, Buffer.from([0])));
fs.writeFileSync(path.join(outDir, 'vi.mo'), Buffer.concat(mo));

// ---- .l10n.php (WordPress 6.5+ native format)
const php = s => "'" + s.replace(/\\/g, '\\\\').replace(/'/g, "\\'") + "'";
let l10n = "<?php\nreturn array(\n\t'domain' => 'bigspy-theme',\n\t'plural-forms' => 'nplurals=1; plural=0;',\n\t'language' => 'vi',\n\t'messages' => array(\n";
for (const [id, str] of strings) {
	l10n += '\t\t' + php(Array.isArray(id) ? id[0] : id) + ' => ' + php(Array.isArray(str) ? str.join('\0') : str) + ',\n';
}
l10n += "\t),\n);\n";
fs.writeFileSync(path.join(outDir, 'vi.l10n.php'), l10n);

console.log('Built ' + strings.length + ' strings: vi.po, vi.mo, vi.l10n.php');
