/**
 * Builds the Vietnamese front-end translation of BIGSPY Theme.
 *
 * Only strings that visitors see are translated; the admin stays in English.
 * Strings that double as admin labels (e.g. "Search", "Main menu") are left out on purpose.
 * Run: node tools/build-translations.js
 * Output: bigspy-theme/languages/vi.po, vi.mo and vi.l10n.php
 */
const fs = require('fs');
const path = require('path');

const outDir = path.join(__dirname, '..', 'bigspy-theme', 'languages');

// msgid => msgstr. Plurals: [singular, plural] => [form] (Vietnamese has one plural form).
const strings = [
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
