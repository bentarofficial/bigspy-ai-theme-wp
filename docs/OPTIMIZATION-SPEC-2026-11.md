# Đặc tả triển khai G-107 → G-124

Tài liệu đi kèm mục “Đợt tối ưu sau rà soát mã nguồn 10-11-2026” trong [roadmap-vi.md](roadmap-vi.md). Roadmap nêu lý do và ưu tiên; tệp này nêu cách làm cho agent lập trình.

## Cách dùng

- Mỗi mã G là một phiên riêng. Chỉ đọc mục của mã đang làm, không đọc cả tệp.
- Số dòng lấy ở bản 1.86.1 (`c4d154b`). Trước khi sửa, grep lại tên hàm để lấy vị trí hiện tại.
- Đường dẫn tính từ thư mục theme `bigspy-ai-theme-wp/`, trừ khi bắt đầu bằng `tools/` hoặc `docs/`.
- Các đoạn mã dưới đây là mã gợi ý để chỉ rõ ý định. Phải đối chiếu với tệp thật và giữ phong cách của tệp (tab, tiền tố `bst_`, chú thích ngắn bằng tiếng Anh).
- Mục nào ghi “Sản phẩm: có” thì thêm một ghi chú vào `docs/PENDING-RELEASE.md` theo mẫu ở cuối tệp đó. “Sản phẩm: không” thì không ghi.
- Không tăng phiên bản, không build ZIP, không phát hành. Xong thì commit, push nhánh được giao và báo hash.
- Chuỗi giao diện mới viết bằng tiếng Anh trong `__()`, rồi thêm bản dịch bằng `python tools/add-strings.py strings.json`.
- Sau mọi sửa đổi `assets/css/blocks.css` phải chạy `python tools/split-blocks-css.py`.
- Bộ test Playground cần `BST_PLAYGROUND_MODULES` và `BST_PLAYWRIGHT` trỏ đến nơi đã cài (xem G-112). Nếu môi trường không chạy được, ghi rõ “chưa chạy” trong bàn giao, không ghi là đạt.

---

## G-124 · Cổng cú pháp trước khi đóng gói

**Ưu tiên:** P0 · **Sản phẩm:** không · **Tệp:** `tools/release.py`, `tools/build-theme.py`

**Hiện trạng.** `check()` trong `release.py` chỉ so phiên bản, kiểm tra cây làm việc sạch và độ mới của `segments.json`. Bản 1.85.0 và 1.86.0 phát hành với lỗi cú pháp trong `inc/welcome/data.php` (dấu nháy đơn chưa escape trong chuỗi `'…theme's…'`).

**Thay đổi.**

1. Tạo `tools/lint.py` với một hàm `problems()` trả về danh sách chuỗi lỗi, để hai script cùng gọi:

```python
"""Syntax checks shared by release.py and build-theme.py."""
import json, shutil, subprocess
from pathlib import Path

THEME = Path(__file__).resolve().parents[1] / 'bigspy-ai-theme-wp'

def problems():
	out = []
	php = shutil.which('php')
	if not php:
		out.append('php not found: cannot lint PHP files')
	else:
		for f in sorted(THEME.rglob('*.php')):
			r = subprocess.run([php, '-l', str(f)], capture_output=True, text=True)
			if r.returncode:
				out.append('PHP syntax: %s: %s' % (f.relative_to(THEME), (r.stdout + r.stderr).strip().splitlines()[0]))
	node = shutil.which('node')
	if not node:
		out.append('node not found: cannot check JS files')
	else:
		for f in sorted(list((THEME / 'assets' / 'js').glob('*.js')) + list((THEME / 'ai-designer' / 'assets').glob('*.js'))):
			r = subprocess.run([node, '--check', str(f)], capture_output=True, text=True)
			if r.returncode:
				out.append('JS syntax: %s' % f.relative_to(THEME))
	for f in sorted(list(THEME.rglob('block.json')) + [THEME / 'theme.json']):
		try:
			json.loads(f.read_text(encoding='utf-8'))
		except ValueError as e:
			out.append('JSON: %s: %s' % (f.relative_to(THEME), e))
	return out
```

2. `release.py`: trong `check()`, sau các kiểm tra hiện có, `problems.extend(lint.problems())`. Import bằng `sys.path.insert(0, str(Path(__file__).parent))` rồi `import lint`.
3. `release.py:45`: đổi chuỗi dò từ `'Chưa có thay đổi sản phẩm mới'` thành `'Không có thay đổi đang chờ'` (đúng câu đang có trong `docs/PENDING-RELEASE.md`).
4. `build-theme.py`: trước khi mở `zipfile.ZipFile(temporary, 'w', …)`, gọi `lint.problems()` và `raise SystemExit` kèm danh sách nếu khác rỗng.
5. Thêm một dòng vào bảng “Reusable tools” của `AGENTS.md`: `python tools/lint.py` (cho `lint.py` in kết quả và trả mã thoát khi chạy trực tiếp).

**Không làm.** Không thêm PHPCS, ESLint hay dependency nào. Workflow GitHub Actions là phần mở rộng, chỉ làm khi owner duyệt riêng; nếu được duyệt, tệp `.github/workflows/lint.yml` chạy `php -l` trên ma trận PHP 7.4 và 8.3 cho mỗi push, không có bước nào khác.

**Kiểm thử.**
- `python tools/release.py` trên cây sạch → `Ready.`, mã thoát 0.
- Sửa tạm `inc/welcome/data.php` thành có dấu nháy lỗi → `PROBLEM: PHP syntax: inc/welcome/data.php: …`, `Not ready.`, mã thoát 1. Hoàn nguyên bằng `git checkout`.
- Thêm tạm dấu phẩy thừa vào một `block.json` → báo `JSON: …`. Hoàn nguyên.

**Nghiệm thu.** Ba ca trên đúng như mô tả; `git status` sạch sau khi thử.

---

## G-107 · Form native: siết chống spam

**Ưu tiên:** P1 · **Sản phẩm:** có · **Tệp:** `inc/form-native.php`, `assets/js/blocks.js`, `tools/tests/features-integration.php`

**Hiện trạng** (`bst_native_form_submit()`, từ dòng 288):

```php
$ts   = isset( $_POST['bst_form_ts'] ) ? absint( $_POST['bst_form_ts'] ) : 0;
$rate = 'bst_nf_' . md5( $ip );
$hits = (int) get_transient( $rate );
if ( '' !== $hp || ( $ts && time() - $ts < 3 ) || $hits >= 5 ) { …spam… }
set_transient( $rate, $hits + 1, MINUTE_IN_SECONDS );
```

`data-ts` được in ở cuối `bst_native_form_markup()` (`time()` lúc render) và gửi đi tại `assets/js/blocks.js:985`.

**Thay đổi.**

1. **Dấu thời gian có chữ ký.** Thêm hai hàm:

```php
/** Signed render time: works with page caches because it is not tied to a visitor. */
function bst_native_form_token( $key, $ts ) {
	return substr( hash_hmac( 'sha256', $key . '|' . (int) $ts, wp_salt( 'nonce' ) ), 0, 20 );
}
function bst_native_form_token_ok( $key, $ts, $sig ) {
	$age = time() - (int) $ts;
	$max = (int) apply_filters( 'bst_form_ts_max_age', 0 ); // 0 = no limit (long-lived page caches).
	return $ts > 0 && $age >= 3 && ( ! $max || $age <= $max ) && hash_equals( bst_native_form_token( $key, $ts ), (string) $sig );
}
```

   - Trong markup: thêm `data-sig="…"` cạnh `data-ts`, tính từ cùng giá trị `time()` (lưu vào biến, không gọi `time()` hai lần).
   - Trong `blocks.js` cạnh dòng 985: `data.append('bst_form_sig', form.getAttribute('data-sig') || '');`.
   - Trong handler: đọc `bst_form_sig` bằng `sanitize_key`; điều kiện spam thành `'' !== $hp || ! bst_native_form_token_ok( $key, $ts, $sig ) || $hits >= $limit`.
2. **Chỉ đếm lượt đã hợp lệ.** Chuyển `set_transient( $rate, … )` xuống sau khối `if ( $errors ) { wp_send_json… }`, ngay trước `bst_native_form_send( 'mail', … )`.
3. **IP sau proxy.** Thay dòng lấy `$ip` bằng `$ip = (string) apply_filters( 'bst_form_client_ip', $ip );` sau khi đọc `REMOTE_ADDR`. Thêm `$limit = (int) apply_filters( 'bst_form_rate_limit', 5 );`. Không đọc `X-Forwarded-For` trong theme.
4. **Giới hạn Mail 2.** Trong `bst_native_form_send()`, khi `$prefix === 'mail2'`: với mỗi địa chỉ trong `$to`, khóa `'bst_nf2_' . md5( strtolower( $addr ) )`; nếu transient ≥ 3 thì bỏ địa chỉ đó; ngược lại tăng và đặt hạn `HOUR_IN_SECONDS`. Ngưỡng qua filter `bst_form_mail2_limit` (mặc định 3). Nếu không còn địa chỉ nào, trả `false` mà không báo lỗi cho khách.
5. **`redirectPass`.** Tại đoạn `add_query_arg( array_map( 'rawurlencode', $values ), $redirect )`: lọc `$values` trước, bỏ các trường có `type` là `textarea`, `file`, `acceptance` và các giá trị rỗng. Dựng danh sách loại từ `$s['fields']`. Không thêm tùy chọn giao diện mới ở đợt này.
6. Cập nhật chú thích “Spam guards: …” cho khớp hành vi mới.

**Không làm.** Không thêm CAPTCHA, không thêm nonce (cache trang làm hỏng nonce; chú thích hiện có đã nêu lý do).

**Kiểm thử.** Thêm vào nhóm `bst_t_forms` của `tools/tests/features-integration.php` (gọi thẳng các hàm, theo kiểu `bst_test( điều_kiện, 'nhãn' )` đang dùng):
- `bst_native_form_token_ok( 'k', time() - 10, bst_native_form_token( 'k', time() - 10 ) )` đúng.
- Sai chữ ký, `ts = 0`, và `ts = time()` (dưới 3 giây) đều sai.
- Markup chứa cả `data-ts` và `data-sig`.
- Với Mail 2: gọi gửi 4 lần tới cùng địa chỉ, lần thứ 4 trả `false` (dùng `pre_wp_mail` để không gửi thật).
- `redirectPass` với một trường textarea: URL kết quả không chứa tên trường đó.
- Chạy `node tools/features-check.cjs`.

**Nghiệm thu.** Test mới đạt; gửi form thật trên Playground vẫn thành công sau khi chờ 3 giây; request thiếu `bst_form_sig` nhận thông báo spam.

**Rủi ro.** Trang đang nằm trong cache của khách (HTML cũ không có `data-sig`) sẽ bị từ chối cho đến khi cache làm mới. Ghi điều này vào PENDING-RELEASE để ghi chú phát hành nhắc xóa cache trang sau khi cập nhật.

---

## G-108 · Per-page CSS không phụ thuộc mtime

**Ưu tiên:** P1 · **Sản phẩm:** có · **Tệp:** `tools/split-blocks-css.py`, `inc/performance.php`, `tools/release.py`, `tools/build-theme.py`, `inc/admin/theme-panel.php`

**Hiện trạng** (`inc/performance.php`, `bst_perf_split_on()`):

```php
return bst_mod( 'perf_split_css' ) && bst_perf_front() && file_exists( $segments )
	&& filemtime( $segments ) >= filemtime( BST_DIR . '/assets/css/blocks.css' ) && bst_perf_families();
```

`build-theme.py` sắp tệp theo đường dẫn nên `assets/css/blocks/segments.json` đứng trước `assets/css/blocks.css` trong ZIP; WordPress giải nén không giữ mtime.

**Thay đổi.**

1. `tools/split-blocks-css.py`, cuối `main()`: ghi thêm `assets/css/blocks/source.json` với nội dung `{"sha1": "<sha1 của blocks.css tính trên byte gốc>"}`. Vòng `for f in os.listdir(OUT): os.remove(…)` đã xóa tệp cũ nên không cần xử lý thêm. Không thêm khóa vào `families.json` (runtime lặp qua mọi khóa của tệp này làm tên họ CSS).
2. `inc/performance.php`: thêm hàm và thay điều kiện mtime:

```php
/** True when blocks/segments.json was built from the current blocks.css. Checked once per version. */
function bst_perf_split_fresh() {
	$css  = BST_DIR . '/assets/css/blocks.css';
	$key  = 'bst_split_fresh_' . md5( BST_VERSION . '|' . filemtime( $css ) . '|' . filesize( $css ) );
	$seen = get_transient( $key );
	if ( false === $seen ) {
		$meta = json_decode( (string) @file_get_contents( BST_DIR . '/assets/css/blocks/source.json' ), true ); // phpcs:ignore
		$seen = is_array( $meta ) && ! empty( $meta['sha1'] ) && sha1_file( $css ) === $meta['sha1'] ? 'yes' : 'no';
		set_transient( $key, $seen, WEEK_IN_SECONDS );
	}
	return 'yes' === $seen;
}
```

   `bst_perf_split_on()` thành: `bst_mod( 'perf_split_css' ) && bst_perf_front() && file_exists( $segments ) && bst_perf_split_fresh() && bst_perf_families()`. Sửa chú thích phía trên cho khớp.
3. `tools/release.py`: thay phép so `git log -1 --format=%ct` bằng so `sha1` của `blocks.css` với `source.json`; thông báo lỗi giữ nguyên câu “run tools/split-blocks-css.py”.
4. `tools/build-theme.py`: thêm assert trong khối kiểm tra ZIP: sha1 của `blocks.css` trong ZIP bằng giá trị trong `source.json` trong ZIP.
5. `inc/admin/theme-panel.php`, mảng kiểm tra hệ thống (cạnh dòng “Max execution time”): thêm dòng `__( 'Per-page block CSS', 'bigspy-ai-theme-wp' ) => array( nhãn, đạt )`, trong đó nhãn là `Active` / `Off in settings` / `Off: CSS map is out of date`, đạt khi không phải trường hợp thứ ba. Thêm ba chuỗi dịch.
6. Chạy `python tools/split-blocks-css.py` và commit `source.json`.

**Kiểm thử.**
- `python tools/release.py` → `Ready.`. Thêm một dấu cách vào `blocks.css` mà không chạy split → `Not ready.`. Hoàn nguyên.
- Trên Playground: `touch` lại `blocks.css` (mtime mới hơn `segments.json`) → trang chủ vẫn nạp `uploads/bigspy/css/blocks-*.css`, không nạp `assets/css/blocks.css`.
- Theme panel hiện dòng trạng thái mới.

**Nghiệm thu.** Per-page CSS hoạt động bất kể mtime; lệch nội dung thật thì tự tắt và Theme panel báo.

---

## G-109 · Tìm kiếm trực tiếp: giảm tải

**Ưu tiên:** P1 · **Sản phẩm:** có · **Tệp:** `inc/header-extras.php`, `assets/js/main.js`

**Hiện trạng.** `bst_live_search_ajax()` (từ dòng 171) chạy 1–2 `WP_Query`, thêm truy vấn `_sku LIKE` và truy vấn theo tag sản phẩm, mỗi lần gõ. `strlen( $term ) < 2` đếm byte. `assets/js/main.js:289-300` dùng bộ đếm `state.request` để bỏ qua câu trả lời cũ nhưng không hủy request.

**Thay đổi.**

1. Đầu hàm: `$len = function_exists( 'mb_strlen' ) ? mb_strlen( $term ) : strlen( $term );` và so `$len < 2`.
2. Sau khi tính `$scope`, `$products_only`, `$count`: tạo khóa cache và trả sớm nếu có:

```php
$cache_key = 'bst_ls_' . md5( wp_json_encode( array( BST_VERSION, strtolower( $term ), $scope, $products_only, $count, (bool) bst_mod( 'search_sku' ), (bool) bst_mod( 'search_tags' ), (bool) bst_mod( 'catalog_mode' ), get_locale() ) ) );
$cached    = get_transient( $cache_key );
if ( is_array( $cached ) ) {
	wp_send_json_success( array( 'items' => $cached ) );
}
```

   Ngay trước `wp_send_json_success( array( 'items' => $items ) )` ở cuối: `set_transient( $cache_key, $items, (int) apply_filters( 'bst_live_search_cache_ttl', MINUTE_IN_SECONDS ) );`. Nếu TTL lọc ra ≤ 0 thì bỏ qua cả đọc lẫn ghi.
   Giá sản phẩm theo vai trò/đăng nhập: chỉ dùng cache khi `! is_user_logged_in()`; người đăng nhập luôn truy vấn trực tiếp.
3. `assets/js/main.js`: trong `state` thêm `controller`. Trước `fetch`: `if (state.controller) { state.controller.abort(); }`, tạo `new AbortController()` nếu `window.AbortController` tồn tại, truyền `signal`. Trong `.catch`, bỏ qua lỗi có `name === 'AbortError'`. Giữ nguyên kiểm tra `state.request === request`.

**Không làm.** Không chuyển sang REST, không đổi cấu trúc JSON trả về, không xóa cache khi lưu bài (TTL 60 giây là đủ).

**Kiểm thử.**
- Từ khóa một ký tự có dấu (`ă`) trả mảng rỗng; hai ký tự trả kết quả.
- Gọi cùng URL hai lần khi chưa đăng nhập: lần hai không chạy `WP_Query` (đếm bằng `$wpdb->num_queries` trong test tích hợp, hoặc quan sát transient tồn tại).
- Trình duyệt: gõ nhanh 5 ký tự, tab Network cho thấy các request trước ở trạng thái canceled.
- `node tools/woo-check.cjs` nếu chạy được.

---

## G-110 · Updater khai báo yêu cầu hệ thống

**Ưu tiên:** P1 · **Sản phẩm:** có · **Tệp:** `inc/updater.php`, `tools/tests/updater-integration.php`, `style.css`, `readme.txt`

**Hiện trạng.** `bst_update_release()` trả `version`, `package`, `url`, `body`. Mảng `response` trong filter `pre_set_site_transient_update_themes` thiếu `requires` và `requires_php`, nên WordPress không so được với môi trường.

**Thay đổi.**

1. Trong `bst_update_release()`, sau khi xác định `$package`: tải `style.css` của đúng tag để đọc header, cùng thời hạn cache với release:

```php
$requires = array( 'wp' => '', 'php' => '' );
$head     = wp_remote_get( 'https://raw.githubusercontent.com/bentarofficial/bigspy-ai-theme-wp/' . rawurlencode( $data['tag_name'] ) . '/bigspy-ai-theme-wp/style.css', array( 'timeout' => 10, 'limit_response_size' => 8 * KB_IN_BYTES ) );
if ( 200 === wp_remote_retrieve_response_code( $head ) ) {
	$text = (string) wp_remote_retrieve_body( $head );
	if ( preg_match( '/^Requires at least:\s*([\d.]+)/mi', $text, $m ) ) { $requires['wp'] = $m[1]; }
	if ( preg_match( '/^Requires PHP:\s*([\d.]+)/mi', $text, $m ) ) { $requires['php'] = $m[1]; }
}
```

   Thêm `'requires' => $requires['wp'], 'requires_php' => $requires['php']` vào `$release`. Tải lỗi thì để rỗng và vẫn trả release (không chặn cập nhật vì thiếu thông tin).
2. Trong filter cập nhật: thêm hai khóa `requires`, `requires_php` vào mảng `response` khi khác rỗng. Trong `themes_api`: thêm `requires`, `requires_php` vào object trả về.
3. `Tested up to`: chỉ đổi trong `style.css` và `readme.txt` sau khi đã chạy bộ test trên bản WordPress đó; ghi số phiên bản đã thử vào bàn giao. Không đổi theo phỏng đoán.

**Kiểm thử.** Trong `tools/tests/updater-integration.php`: dùng filter `pre_http_request` trả `style.css` giả có `Requires PHP: 99.0`; kiểm tra `response['bigspy-ai-theme-wp']['requires_php'] === '99.0'`. Ca thứ hai: raw trả 404 → release vẫn có, hai khóa rỗng. Chạy `node tools/updater-playground-check.cjs`.

---

## G-111 · Giảm dung lượng tài nguyên front

**Ưu tiên:** P2 · **Sản phẩm:** có · **Tệp:** `inc/performance.php`, `inc/assets.php`, `assets/js/blocks.js`, các `blocks/*/render.php` liên quan

Làm thành ba phiên, theo thứ tự. Phiên nào cũng bắt đầu bằng đo.

**Bước 0 — đo.** Trên Playground, với trang chủ, trang shop và một trang nhiều block: ghi số request và tổng byte CSS/JS (transfer size trong DevTools hoặc `curl -s --compressed -w '%{size_download}'`). Lưu bảng vào ghi chú PENDING-RELEASE của mục này. Đây cũng là số liệu cho G-41.

**Bước 1 — nén CSS khi ghi cache.** Trong `bst_perf_block_css_file()`, ngay trước `file_put_contents`:

```php
// Segments are comment-free already (split-blocks-css.py strips them); only collapse whitespace outside strings.
$css = preg_replace( '/\s*([{};,>])\s*/', '$1', preg_replace( '/\s+/', ' ', $css ) );
```

   Trước khi dùng, xác nhận trên toàn bộ `blocks.css` rằng kết quả tương đương: không có chuỗi `content:"…"` hay `url(…)` chứa `{};,>` có khoảng trắng bao quanh (grep `content:` và data URI). Nếu có, bỏ ký tự gây rủi ro khỏi lớp ký tự thay vì cố xử lý chuỗi. Đổi hằng `'|url2|'` trong tên tệp thành `'|url3|'` để tạo tệp cache mới. Áp cùng phép nén cho CSS sinh ra ở khối “Generated CSS as files”.

**Bước 2 — `blocks.js` theo nhu cầu.** Tệp là một IIFE gồm các khối theo tính năng (slider, tabs, counter, lightbox, video modal, hotspot, popup, form…). Mỗi phiên tách **một** khối:
   - Chuyển khối sang `assets/js/blocks-<tên>.js`, tự bọc IIFE, tự khai báo biến dùng chung cần thiết (`doc`, `reduceMotion`).
   - Đăng ký handle trong `inc/assets.php` (`wp_register_script`, `strategy => defer`, `in_footer => true`), và `wp_enqueue_script` trong `render.php` của các block dùng nó, theo cách `blocks/lottie/render.php` đang làm.
   - Các nơi sinh markup ngoài block (shortcode Flatsome, widget, header) dùng cùng tính năng cũng phải enqueue; grep class/data-attribute của khối để tìm.
   - Thứ tự đề xuất theo kích thước và độ độc lập: form native (dòng ~930 trở đi), popup, lightbox + video modal, counter.
   - Không tách slider và tabs ở đợt đầu: nhiều nơi dùng chung.

**Bước 3 — tệp `.min` lúc đóng gói.** Chỉ làm khi owner duyệt thêm công cụ build. Khi đó: `build-theme.py` sinh `*.min.css`/`*.min.js` vào ZIP (không commit vào git), `bst_asset_version()` và các lệnh enqueue chọn `.min` khi tệp tồn tại và `SCRIPT_DEBUG` tắt.

**Kiểm thử.** `tools/tests/browser-check.cjs` không lệch; thao tác tay từng tính năng đã tách trên một trang có và một trang không có block tương ứng (trang không có thì không được nạp tệp mới); bảng số liệu trước/sau.

---

## G-112 · Chạy test được trên mọi máy

**Ưu tiên:** P2 · **Sản phẩm:** không · **Tệp:** `package.json` (mới), `.gitignore`, tám runner, `docs/DEV-RULES.md`, `docs/CODEMAP.md`

**Hiện trạng.** Các tệp sau mặc định về đường dẫn `C:/Users/DELL/...`: `tools/playground-check.cjs:4`, `tools/playground-page.cjs:4`, `tools/woo-check.cjs:6`, `tools/auto-detect-check.cjs:5`, `tools/updater-playground-check.cjs:15`, `tools/tests/browser-check.cjs:1`, `tools/tests/admin-browser-check.cjs:2`, `tools/tests/welcome-browser-check.cjs:2` (tệp cuối không có biến môi trường ghi đè). Chạy `grep -rn "C:/Users" tools` để lấy danh sách đầy đủ, gồm cả `features-check.cjs` và `block-manager-editor.cjs` nếu có.

**Thay đổi.**

1. Tạo `package.json` ở gốc repo: `"private": true`, không có `dependencies`, `devDependencies` gồm `@wp-playground/cli` và `playwright` ghim đúng phiên bản. Lấy phiên bản từ thư mục `node_modules` mà owner đang dùng (hỏi owner đường dẫn nếu không có trên máy); nếu không lấy được, dùng bản mới nhất tại thời điểm làm, chạy toàn bộ runner một lượt và ghi phiên bản đã thử vào bàn giao. Commit cả `package-lock.json`. `node_modules/` đã có trong `.gitignore`.
2. Tạo `tools/lib/env.cjs` xuất hai hàm:

```js
const path = require('path');
exports.playground = () => require(process.env.BST_PLAYGROUND_MODULES ? path.join(process.env.BST_PLAYGROUND_MODULES, '@wp-playground/cli') : '@wp-playground/cli');
exports.playwright = () => require(process.env.BST_PLAYWRIGHT || 'playwright');
```

   Trong mỗi runner, thay dòng `const base = …'C:/Users/…'` và `require(path.join(base, …))` bằng lời gọi hai hàm này. Giữ `BST_CHROME` như hiện tại; khi không đặt, để Playwright tự chọn trình duyệt.
3. Số worker: runner nào đang ghi cứng 6 hoặc 4 worker thì đọc `process.env.BST_WORKERS`, mặc định 1.
4. `tools/build-translations.js`: xóa tệp và bỏ câu nhắc đến nó trong `docs/CODEMAP.md`.
5. `docs/DEV-RULES.md`, mục “Dùng đúng các script hiện có”: thay đoạn nói về đường dẫn phụ thuộc máy và đoạn “Đề xuất chưa triển khai” bằng hướng dẫn mới (`npm ci`, ba biến môi trường tùy chọn, `BST_WORKERS`).

**Kiểm thử.** Clone mới vào thư mục tạm → `npm ci` → `node tools/playground-check.cjs` và `node tools/features-check.cjs` chạy đến hết mà không đặt biến môi trường. `grep -rn "C:/Users" tools` không còn kết quả.

---

## G-113 · Chuỗi giao diện viết cứng bằng tiếng Việt

**Ưu tiên:** P2 · **Sản phẩm:** có · **Tệp:** `inc/quick-view.php`, `assets/js/quick-view.js`, `inc/product-options.php`, `inc/admin/dashboard.php`

Thay từng chuỗi bằng msgid tiếng Anh trong `__()` / `esc_html__()` / `esc_attr__()` và đưa bản tiếng Việt gốc vào `strings.json` cho `tools/add-strings.py`.

| Vị trí | Chuỗi hiện tại | msgid tiếng Anh |
| --- | --- | --- |
| `quick-view.php:6` label | Xem nhanh thông tin sản phẩm | Product quick view |
| `quick-view.php:6` description | Mở ảnh, giá và mô tả trong cửa sổ; mua hàng trên trang sản phẩm đầy đủ. | Opens the image, price and description in a window; buying continues on the full product page. |
| `quick-view.php:10` | Xem nhanh | Quick view |
| `quick-view.php:21` lỗi REST | Sản phẩm không khả dụng. | This product is not available. |
| `quick-view.php:24` | Xem chi tiết sản phẩm | View product details |
| `quick-view.js` nút đóng | Đóng xem nhanh | Close quick view |
| `quick-view.js` đang tải | Đang tải sản phẩm… | Loading product… |
| `product-options.php:8` label | Chọn biến thể bằng nút (thay dropdown) | Choose variations with buttons (instead of dropdowns) |
| `product-options.php:8` description | Tùy chọn độc lập; mặc định tắt. Giữ luồng giỏ hàng và biến thể của WooCommerce. | Independent option, off by default. Keeps the WooCommerce cart and variation flow. |
| `product-options.php:9` label | Hiện swatch màu/ảnh trên thẻ sản phẩm | Show color/image swatches on product cards |
| `product-options.php:9` description | Hiện các lựa chọn của thuộc tính toàn cục… Mặc định tắt. | Shows the choices of global attributes with swatches under the product name. A click opens the product page with that value selected. Off by default. |
| `product-options.php:10` | Nút Mua ngay trên trang sản phẩm | Buy now button on the product page |
| `product-options.php:10` | Điều hướng sản phẩm trước / sau | Previous / next product navigation |
| `product-options.php:11` | Tùy chọn độc lập; mặc định tắt. | Independent option, off by default. |
| `product-options.php:44` aria-label | Điều hướng sản phẩm | Product navigation |
| `product-options.php:45` | ← Sản phẩm trước / Sản phẩm sau → | ← Previous product / Next product → |
| `dashboard.php:114` | Thư viện CSS · bật/tắt | CSS library · on/off |

- `quick-view.js`: hai chuỗi lấy từ `bstQuickView` (thêm khóa `close`, `loading` vào `wp_localize_script` ở `quick-view.php:15`). Tệp JS đang ở dạng một dòng; chỉ thay hai chuỗi, không định dạng lại.
- Sau đó grep chữ có dấu trong `inc/customizer/controls.php`, `inc/header-builder.php`, `inc/header-elements.php`, `inc/header-design.php`, `inc/mega-menu.php`, `inc/meta-boxes.php`, `inc/listing/meta-box.php`, `blocks/payment-icons/render.php`: `grep -nP "[àáảãạăâđêôơưếềệộờợữứ]" <tệp>`. Chỉ sửa chuỗi hiển thị cho người dùng nằm ngoài hàm dịch; bỏ qua chú thích, nội dung mẫu (pattern), tên riêng và từ khóa nhận diện.

**Kiểm thử.** Đặt ngôn ngữ theme là English (BIGSPY → Settings): Customizer mục Sản phẩm, thẻ sản phẩm và hộp Quick View không còn chữ tiếng Việt. Đặt Tiếng Việt: hiện đúng chuỗi cũ.

---

## G-114 · Dọn tài liệu điều hướng

**Ưu tiên:** P2 · **Sản phẩm:** không · **Tệp:** `docs/`, `TASKS.md`, `HANDOFF.md`

1. Tạo `docs/archive/`. Dùng `git mv` chuyển vào đó: `HANDOFF-1.73.md`, `VERIFICATION-1.73.md`, `VERIFICATION-1.73.3.md`, `VERIFICATION-1.74.md`. Grep tên từng tệp trong toàn repo và sửa liên kết trỏ đến.
2. `docs/roadmap-vi.md`: cắt các mục “Bối cảnh roadmap cũ”, “Đã có đến 1.18.1”, “Ưu tiên tiếp theo”, “Điều kiện đưa tính năng vào bản phát hành”, “Phạm vi chưa cam kết” sang `docs/archive/roadmap-1.18.md`, để lại một dòng liên kết. Riêng “Điều kiện đưa tính năng vào bản phát hành” vẫn là chính sách đang áp dụng: chuyển nguyên văn vào cuối `docs/DEV-RULES.md` thay vì lưu trữ.
3. `TASKS.md`, `HANDOFF.md` (0 byte): hỏi owner một lần là dùng hay bỏ. Nếu không có trả lời, xóa hai tệp và sửa câu trong roadmap “theo sổ nhận việc và TASKS.md của workspace” thành không nhắc đến `TASKS.md`.
4. Thêm vào đầu roadmap một bảng “Mã G → trạng thái” cho G-57 trở đi. Lấy trạng thái từ `git log --oneline --grep "G-"` và các tài liệu đang có; mã không tìm thấy dấu vết ghi “không rõ”, không suy đoán.

**Kiểm thử.** Mọi liên kết tương đối trong `docs/*.md`, `AGENTS.md`, `README.md` trỏ đến tệp tồn tại (script một dòng duyệt `](…md)`); `git diff --check`.

---

## G-115 · Tự host trình phát Lottie

**Ưu tiên:** P1 · **Sản phẩm:** có · **Tệp:** `blocks/lottie/render.php`, `assets/js/vendor/` (mới), `readme.txt`

**Hiện trạng** (`blocks/lottie/render.php:15`):

```php
wp_enqueue_script( 'bst-lottie-player', 'https://unpkg.com/@lottiefiles/lottie-player@2.0.8/dist/lottie-player.js', array(), '2.0.8', array( 'in_footer' => true, 'strategy' => 'defer' ) );
```

**Thay đổi.**

1. Tải đúng tệp `dist/lottie-player.js` của `@lottiefiles/lottie-player@2.0.8` từ gói npm (`npm pack @lottiefiles/lottie-player@2.0.8` rồi giải nén), đặt tại `assets/js/vendor/lottie-player.js`. Giữ nguyên nội dung, kể cả chú thích giấy phép ở đầu tệp. Ghi sha256 của tệp vào thông điệp commit.
2. Đổi dòng enqueue thành `BST_URI . '/assets/js/vendor/lottie-player.js'`, phiên bản `'2.0.8'`, giữ `defer` và `in_footer`.
3. Kiểm tra editor: grep `lottie` trong `assets/js/blocks-*.js` và `inc/`; nếu editor nạp trình phát từ URL ngoài, đổi sang cùng tệp.
4. `readme.txt`: thêm tên thư viện, phiên bản, giấy phép và URL nguồn vào phần ghi công/bản quyền (tạo mục `== Copyright ==` nếu chưa có). Xác nhận giấy phép từ `package.json` của gói, không ghi theo trí nhớ.
5. `tools/lint.py` (G-124) chỉ quét `assets/js/*.js` nên không đụng `vendor/`; giữ như vậy.
6. Ghi dung lượng tệp (gốc và gzip) vào PENDING-RELEASE. Nếu trên 400 KB gốc, nêu trong bàn giao để owner cân nhắc thư viện nhẹ hơn ở đợt sau; không tự đổi thư viện.

**Kiểm thử.** Trang có block Lottie: hoạt ảnh chạy, tab Network không có request tới `unpkg.com`. Trang không có block: không nạp `lottie-player.js`. `grep -rn "unpkg\|jsdelivr\|cdnjs" bigspy-ai-theme-wp --include=*.php --include=*.js` không còn kết quả.

---

## G-116 · Block Map: siết host của mã nhúng

**Ưu tiên:** P1 · **Sản phẩm:** có · **Tệp:** `blocks/map/render.php`, `assets/js/blocks-editor.js` (nếu có kiểm tra tương tự), `tools/tests/designer-integration.php` hoặc `features-integration.php`

**Hiện trạng** (dòng 18):

```php
if ( preg_match( '#^https://(www\.)?google\.[a-z.]+/maps/embed\?#i', $embed_raw ) || preg_match( '#^https://maps\.google\.[a-z.]+/maps\?.*output=embed#i', $embed_raw ) ) {
```

`google\.[a-z.]+` khớp `google.example.com`.

**Thay đổi.**

1. Thêm vào `inc/blocks.php` (hoặc `inc/helpers.php`):

```php
/** A Google Maps embed URL on a Google host, or ''. */
function bst_map_embed_url( $url ) {
	$url  = trim( (string) $url );
	$p    = wp_parse_url( $url );
	$host = strtolower( (string) ( $p['host'] ?? '' ) );
	if ( 'https' !== ( $p['scheme'] ?? '' ) || ! preg_match( '/^(www\.|maps\.)?google\.(com|[a-z]{2}|com?\.[a-z]{2})$/', $host ) ) {
		return '';
	}
	$path  = (string) ( $p['path'] ?? '' );
	$query = (string) ( $p['query'] ?? '' );
	if ( '/maps/embed' === $path && '' !== $query ) {
		return $url;
	}
	return ( '/maps' === $path && false !== strpos( $query, 'output=embed' ) ) ? $url : '';
}
```

2. Trong `render.php`: thay khối `if ( preg_match … ) { $embed_src = $embed_raw; }` bằng `$embed_src = bst_map_embed_url( $embed_raw );`.
3. `<iframe … title="…">`: khi `$address` rỗng, dùng `__( 'Map', 'bigspy-ai-theme-wp' )` (chuỗi đã có trong `.pot`).
4. Grep `maps/embed` trong `assets/js/blocks-editor.js`; nếu editor có biểu thức tương tự để xem trước, sửa cho cùng quy tắc host.

**Kiểm thử.** Thêm ca gọi thẳng `bst_map_embed_url()`:
- Hợp lệ: `https://www.google.com/maps/embed?pb=!1m18`, `https://www.google.com.vn/maps/embed?pb=x`, `https://maps.google.co.uk/maps?q=a&output=embed`.
- Trả rỗng: `https://google.example.com/maps/embed?pb=x`, `https://www.google.com.evil.io/maps/embed?pb=x`, `http://www.google.com/maps/embed?pb=x`, `https://www.google.com/maps/embed`, `https://www.google.com@evil.io/maps/embed?pb=x`.
- `render_block` với mã nhúng host giả và không có địa chỉ: đầu ra không chứa host đó.

---

## G-117 · Trợ năng của hộp thoại và slider

**Ưu tiên:** P2 · **Sản phẩm:** có · **Tệp:** `assets/js/main.js`, `assets/js/blocks.js`, `blocks/popup/render.php`, `inc/assets.php`

Quick View dùng `<dialog>.showModal()` nên trình duyệt đã giữ focus; không cần sửa. Bốn thành phần cần sửa: popup, lightbox, video modal (`blocks.js`), ngăn menu di động (`main.js`).

**Thay đổi.**

1. **Hàm giữ focus dùng chung.** `main.js` nạp trước `blocks.js` (cùng `defer`, đăng ký trước). Thêm vào `main.js` và gắn lên `window.bstTrapFocus`:

```js
/** Keep Tab inside `root` while it is open. Returns a function that removes the trap. */
window.bstTrapFocus = function (root) {
	var sel = 'a[href],button:not([disabled]),input:not([disabled]):not([type="hidden"]),select:not([disabled]),textarea:not([disabled]),iframe,[tabindex]:not([tabindex="-1"])';
	var onKey = function (e) {
		if (e.key !== 'Tab') { return; }
		var items = Array.prototype.filter.call(root.querySelectorAll(sel), function (el) { return el.offsetParent !== null || el === document.activeElement; });
		if (!items.length) { e.preventDefault(); return; }
		var first = items[0], last = items[items.length - 1];
		if (e.shiftKey && (document.activeElement === first || !root.contains(document.activeElement))) { e.preventDefault(); last.focus(); }
		else if (!e.shiftKey && (document.activeElement === last || !root.contains(document.activeElement))) { e.preventDefault(); first.focus(); }
	};
	document.addEventListener('keydown', onKey);
	return function () { document.removeEventListener('keydown', onKey); };
};
```

2. **Áp dụng.** Ở mỗi hàm mở, lưu hàm gỡ; ở hàm đóng, gọi nó:
   - `main.js` `openOffcanvas` / `closeOffcanvas` (quanh dòng 84–108).
   - `blocks.js` `openPopup` / `closePopup` (quanh dòng 783–803): trap trên `.bs-popup__dialog`.
   - `blocks.js` lightbox mở (dòng ~640) / `closeLightbox` (~646).
   - `blocks.js` video modal (dòng ~665–690), nơi đã có `onKey` cho Escape.
   Luôn gọi qua `window.bstTrapFocus && …` để `blocks.js` không lỗi nếu `main.js` không có mặt.
3. **Tên cho popup.** `blocks/popup/render.php`: thêm `aria-label` vào `.bs-popup__dialog`, lấy từ `$a['buttonText']` nếu có, ngược lại `__( 'Popup', 'bigspy-ai-theme-wp' )`.
4. **Chuỗi dịch cho JS front.** Trong `bst_front_assets()` (`inc/assets.php`), sau khi enqueue `bst-blocks`: `wp_localize_script( 'bst-blocks', 'bstBlocksL10n', array( 'close' => __( 'Close', … ), 'previous' => __( 'Previous', … ), 'next' => __( 'Next', … ), 'slide' => __( 'Go to slide %d', … ), 'pause' => __( 'Pause slideshow', … ), 'play' => __( 'Play slideshow', … ) ) )`. Trong `blocks.js`: đọc `var L = window.bstBlocksL10n || {};`, thay ba nhãn tiếng Anh viết cứng ở dòng 589–591 và nhãn chấm ở dòng 156 (`(L.slide || 'Go to slide %d').replace('%d', i + 1)`).
5. **Nút tạm dừng slider.** Trong khối autoplay (dòng ~194): khi `interval > 0 && !reduceMotion`, tạo `<button type="button" class="bs-slider__pause" aria-pressed="false">` với `aria-label` là `L.pause`, chèn vào `root`. Bấm thì đảo một cờ `stopped` (khác `paused` của hover) mà vòng autoplay kiểm tra, đổi `aria-pressed` và nhãn. Thêm CSS cho nút vào `assets/css/blocks.css` gần `.bs-slider__arrow` (góc dưới phải, 32 px, có `:focus-visible`), rồi chạy `python tools/split-blocks-css.py`.
6. **Phím mũi tên cho slider.** Trên `root` của slider: `keydown` với `ArrowLeft`/`ArrowRight` gọi cùng hàm mà nút mũi tên đang gọi, chỉ khi `e.target` không phải `input`, `textarea`, `select`.

**Kiểm thử** (tay, trên Playground, chỉ dùng bàn phím): mở từng thành phần, nhấn Tab 20 lần và Shift+Tab 20 lần, focus không rời hộp thoại; Escape đóng và focus về nút mở. Slider autoplay: Tab tới nút tạm dừng, Enter dừng, Enter chạy lại. Kiểm tra `prefers-reduced-motion`: không có nút tạm dừng vì autoplay đã tắt. `tools/tests/browser-check.cjs` không lệch.

---

## G-118 · Ảnh nền Section responsive

**Ưu tiên:** P2 · **Sản phẩm:** có · **Tệp:** `blocks/section/render.php`, `assets/css/blocks.css`, `assets/js/blocks-editor.js` (chỉ khi cần cho xem trước)

**Hiện trạng.** `render.php:16` đọc `bgImage` (URL), dòng ~52 dựng `background-image:url(…)` và in vào `<div class="bs-section__bg …" style="…">`. Block đã lưu `bgImageId`.

**Thay đổi.**

1. Tính cờ dùng thẻ ảnh:

```php
$bg_id   = absint( $a['bgImageId'] ?? 0 );
$bg_size = bst_choice( $a['bgSize'] ?? 'cover', array( 'cover', 'contain', 'auto' ), 'cover' );
$use_img = $image && $bg_id && wp_attachment_is_image( $bg_id ) && empty( $a['bgFixed'] ) && 'auto' !== $bg_size;
```

   `bgFixed` cần `background-attachment`, `auto` cần kích thước gốc/lặp: hai trường hợp này giữ nền CSS. Parallax vẫn dùng được với `<img>` vì hiệu ứng dịch chuyển phần tử `.bs-section__bg`.
2. Khi `$use_img`: phần tử `.bs-section__bg` không mang `background-image`; bên trong in

```php
echo wp_get_attachment_image( $bg_id, 'full', false, array(
	'class'         => 'bs-section__bg-img',
	'alt'           => '',
	'sizes'         => '100vw',
	'loading'       => $first ? 'eager' : 'lazy',
	'fetchpriority' => $first ? 'high' : false,
	'decoding'      => 'async',
	'style'         => 'object-fit:' . $bg_size . ';object-position:' . $position,
) );
```

   `filter:brightness(…)` giữ trên `.bs-section__bg` như hiện tại.
3. `$first`: section đầu tiên của trang. Xem `blocks/banner/render.php:52` và `blocks/image/render.php:29` để dùng đúng cơ chế hiện có (biến đếm tĩnh hoặc helper); không tạo cơ chế thứ hai.
4. CSS trong `blocks.css` cạnh `.bs-section__bg` (dòng ~217): `.bs-section__bg-img{position:absolute;inset:0;width:100%;height:100%;max-width:none}`. Chạy `python tools/split-blocks-css.py`.
5. Editor: `edit` của Section tự dựng xem trước bằng JS; không cần đổi trừ khi ảnh xem trước dựa vào markup của `render.php`.
6. Nội dung cũ không có `bgImageId` (ảnh ngoài, nội dung chuyển từ Flatsome): không đổi gì.

**Kiểm thử.** Một trang có bốn section: ảnh từ thư viện (cover), ảnh từ thư viện + `bgFixed`, ảnh URL ngoài, ảnh + parallax + blur + overlay. So ảnh chụp trước/sau ở 390 px và 1440 px: không lệch bố cục. HTML của section đầu có `srcset` và `fetchpriority="high"`; trên 390 px trình duyệt tải biến thể nhỏ hơn bản gốc. Ghi LCP trước/sau vào PENDING-RELEASE.

---

## G-119 · Lớp tương thích Flatsome thành tính năng tùy chọn

**Ưu tiên:** P2 · **Sản phẩm:** có · **Tệp:** `functions.php`, `inc/features.php`, `inc/features/flatsome.php` (mới), `inc/compat-flatsome*.php`, `tools/tests/features-integration.php`

**Hiện trạng.** `functions.php` nạp `inc/compat-flatsome.php`, `inc/compat-flatsome-elements.php`, `inc/compat-flatsome-settings.php` vô điều kiện. Hệ quả trên mọi site: đăng ký shortcode tên chung (`bst_legacy_tags()`), đăng ký post type `blocks`, enqueue `assets/css/legacy.css` (15 KB) trên mọi trang (`bst_legacy_assets()`), và kế thừa thiết lập Flatsome.

**Thay đổi.**

1. Xóa ba dòng `inc/compat-flatsome*.php` khỏi mảng `$bst_includes` trong `functions.php`.
2. Tạo `inc/features/flatsome.php` chỉ gồm ba `require_once` tới ba tệp trên. Thêm `'flatsome'` vào `bst_feature_ids()` và vào danh sách hiển thị của trang Features (tên: “Flatsome compatibility”; mô tả: “Shows pages built with Flatsome UX Builder shortcodes, keeps UX Blocks and inherits Flatsome design settings.”). Thêm bản dịch.
3. **Tự bật một lần khi nâng cấp.** Trong `inc/features.php`, trước vòng nạp tính năng:

```php
/** One-time: sites that already rely on Flatsome content keep the compatibility layer. */
function bst_feature_flatsome_migrate() {
	if ( get_option( 'bst_flatsome_checked' ) ) {
		return;
	}
	global $wpdb;
	$needs = (bool) get_option( 'theme_mods_flatsome' ) || (bool) get_option( 'theme_mods_flatsome-child' )
		|| (bool) $wpdb->get_var( "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'blocks' LIMIT 1" ) // phpcs:ignore
		|| (bool) $wpdb->get_var( "SELECT ID FROM {$wpdb->posts} WHERE post_status IN ('publish','draft','private') AND post_type NOT IN ('revision','nav_menu_item') AND ( post_content LIKE '%[section%' OR post_content LIKE '%[ux\\_%' OR post_content LIKE '%[row%' OR post_content LIKE '%[col %' ) LIMIT 1" ); // phpcs:ignore
	if ( $needs ) {
		update_option( 'bst_features', array_values( array_unique( array_merge( (array) get_option( 'bst_features', array() ), array( 'flatsome' ) ) ) ) );
	}
	update_option( 'bst_flatsome_checked', 1, true );
}
bst_feature_flatsome_migrate();
```

   Trước khi dùng, mở `inc/compat-flatsome-settings.php` để lấy đúng tên option mà tệp này đọc (tên child theme có thể khác `flatsome-child`) và dùng cùng cách dò.
   Hàm chạy lúc nạp theme, trước vòng `foreach ( bst_feature_ids() … )`, để ngay request đầu sau khi cập nhật lớp tương thích đã có mặt.
4. **Tìm nơi gọi hàm của lớp tương thích từ bên ngoài.** `grep -rn "bst_legacy_\|bst_inherit_flatsome" --include=*.php .`, loại trừ các hàm `bst_legacy_list_*`, `bst_legacy_home_*`, `bst_legacy_hero_*` (thuộc `inc/legacy-list-blocks.php` và `inc/listing/`, không liên quan). Mọi lời gọi còn lại nằm ngoài ba tệp compat phải bọc `function_exists()`. Kiểm tra riêng `inc/form-migrate.php`, `footer.php`, `inc/footer.php`, `inc/theme-builder.php` và AI Designer (`ai-designer/includes/tools-site.php` có nhắc post type `blocks`).
5. Hook `after_switch_theme → flush_rewrite_rules` nằm trong tệp compat; khi bật/tắt tính năng từ trang Features cũng cần flush một lần (xem cách tính năng `listing` xử lý khi bật để làm giống).

**Kiểm thử.** Thêm nhóm `bst_t_flatsome` vào `tools/tests/features-integration.php`:
- Site mới, không có nội dung Flatsome: sau khi nạp, `bst_feature_on( 'flatsome' )` sai, `shortcode_exists( 'ux_text' )` sai, `post_type_exists( 'blocks' )` sai, style `bst-legacy` không được enqueue.
- Xóa `bst_flatsome_checked`, chèn một trang có `[section][ux_text]x[/ux_text][/section]`, gọi lại `bst_feature_flatsome_migrate()`: tính năng bật.
- Với tính năng bật: `do_shortcode( '[ux_text]x[/ux_text]' )` cho ra markup như trước thay đổi.
- Chạy `node tools/features-check.cjs` và `node tools/woo-check.cjs`.
- Bài test số lượng tính năng hiện có (“page lists 5 unticked features”) phải sửa thành 6.

**Rủi ro.** Đây là mục dễ làm vỡ site đang chạy nhất trong đợt. Nếu bước dò ở mục 3 bỏ sót, trang Flatsome sẽ hiện shortcode thô. Ghi vào PENDING-RELEASE: sau khi cập nhật, kiểm tra một trang Flatsome cũ; nếu lỗi, bật “Flatsome compatibility” trong BIGSPY → Features.

---

## G-120 · WooCommerce: kiểm chứng rồi mới sửa

**Ưu tiên:** P2 · **Sản phẩm:** chỉ khi có sửa · **Tệp:** `assets/css/woocommerce.css`, `woocommerce/content-product.php`

**Bước 1 — kiểm chứng (bắt buộc, không sửa mã).**
- Chạy `node tools/playground-page.cjs --woo=<thư mục plugin WooCommerce>`. Tạo hai trang chứa block Cart và Checkout (mặc định của WooCommerce mới), thêm một sản phẩm vào giỏ.
- Chụp ở 390 px và 1440 px: trang Cart/Checkout dạng block, và cùng hai trang dạng shortcode (`[woocommerce_cart]`, `[woocommerce_checkout]`) để so.
- Ghi lại các điểm lệch cụ thể: font, màu nút chính, bo góc ô nhập, khoảng cách, màu liên kết.
- Mở **WooCommerce → Status**: xem mục Templates có báo `content-product.php` lỗi thời không; ghi phiên bản WooCommerce đã thử.

**Bước 2 — chỉ khi bước 1 thấy lệch rõ.** Thêm một khối ở cuối `assets/css/woocommerce.css`, giới hạn trong `.wc-block-cart`, `.wc-block-checkout`, `.wc-block-components-*`, chỉ gán biến sẵn có của theme (`var(--bs-primary)`, font, bán kính) cho nút chính, ô nhập và tiêu đề. Không ghi đè bố cục của block. Giới hạn 60 dòng; nếu cần nhiều hơn, dừng và báo owner.

**Bước 3 — template.** Nếu WooCommerce báo lỗi thời: so `woocommerce/content-product.php` với `templates/content-product.php` của bản WooCommerce đó, chuyển các thay đổi của bản gốc vào và nâng `@version`. Nếu không báo, không đổi.

**Nghiệm thu.** Bàn giao có ảnh trước/sau (hoặc kết luận “không lệch, không sửa”) và phiên bản WooCommerce đã thử.

---

## G-121 · Bổ sung bản dịch tiếng Việt

**Ưu tiên:** P1, cần owner duyệt phạm vi · **Sản phẩm:** có · **Tệp:** `tools/check-strings.py` (mới), `languages/vi.po` (chỉ qua `add-strings.py`), `tools/release.py`

**Hiện trạng.** `.pot` 3.837 chuỗi, `vi.po` 2.499, thiếu 1.960. Bảng theo tệp ở roadmap.

**Thay đổi.**

1. **Công cụ đo** `tools/check-strings.py` (làm trước, không cần duyệt dịch):
   - Đọc `vi.po` bằng bộ đọc ghép dòng nối tiếp (msgid/msgstr nhiều dòng), lấy tập msgid có msgstr khác rỗng.
   - Quét `*.php` và `*.js` trong theme (bỏ `languages/`, `assets/js/vendor/`) với mẫu: `(__|_e|esc_html__|esc_html_e|esc_attr__|esc_attr_e)\(\s*'(…)'\s*,\s*(T|'bigspy-ai-theme-wp')`; thêm biến thể chuỗi nháy kép và `_x`/`_n` nếu grep cho thấy mã có dùng.
   - `python tools/check-strings.py` in bảng `thiếu / tổng  tệp`, sắp giảm dần, và dòng tổng. `python tools/check-strings.py <tệp> --json` in mảng các msgid thiếu của tệp đó.
   - `release.py`: in một dòng `Note: N strings have no Vietnamese translation.`; không tính là `PROBLEM`.
2. **Dịch theo tệp**, mỗi phiên một tệp, theo thứ tự bảng trong roadmap, bắt đầu từ `assets/js/blocks-editor.js`:
   - `python tools/check-strings.py assets/js/blocks-editor.js --json > strings-src.json` (ghi vào thư mục tạm, không commit).
   - Tạo `strings.json` dạng `{"English": "Tiếng Việt"}`. Tệp trên 300 chuỗi thì chia nhiều lượt trong cùng phiên.
   - `python tools/add-strings.py strings.json` (script tự biên dịch `.mo` và `.l10n.php`).
   - Chạy lại `check-strings.py` cho tệp: còn 0 hoặc chỉ còn các chuỗi cố ý giữ nguyên.
3. **Quy ước dịch.**
   - Trước khi dịch, `grep -n 'msgid "…"' -A1 languages/vi.po` cho 20–30 thuật ngữ chính (Section, Row, Column, Padding, Margin, Overlay, Hover, Breakpoint…) để dùng đúng cách dịch đã có; không tạo cách dịch thứ hai cho cùng thuật ngữ.
   - Giữ nguyên: tên block và tên riêng (BIGSPY, WooCommerce, Flatsome), đơn vị (`px`, `vh`), giá trị CSS, placeholder `%s`, `%d`, `%1$s`.
   - Nhãn điều khiển ngắn gọn như nhãn tiếng Việt đang có trong Customizer; không thêm dấu chấm cuối nếu bản gốc không có.
   - Chuỗi trùng msgid với nơi khác dùng chung một bản dịch (gettext không phân biệt tệp); nếu nghĩa khác nhau theo ngữ cảnh, báo trong bàn giao, không tự thêm `_x()`.

**Kiểm thử.** Đặt ngôn ngữ theme Tiếng Việt, mở editor, chèn 5 block thuộc tệp vừa dịch, mở bảng thiết lập: không còn nhãn tiếng Anh ngoài tên riêng. `php -l languages/vi.l10n.php` đạt.

**Nghiệm thu mỗi phiên.** Số thiếu của tệp về 0 (hoặc danh sách ngoại lệ có lý do); tổng thiếu giảm đúng bằng số đã thêm.

---

## G-122 · Siêu dữ liệu phát hành

**Ưu tiên:** P2 · **Sản phẩm:** không (công cụ và metadata) · **Tệp:** `tools/release.py`, `readme.txt`, `languages/vi.po`, `docs/README-UPDATES.md`

1. `release.py`:
   - `versions()` đọc thêm `Stable tag:` trong `readme.txt`; ba giá trị lệch nhau là `PROBLEM: Version mismatch`.
   - Trong nhánh `--version`: thêm `bump( README, r'^Stable tag:\s*[\d.]+', 'Stable tag: ' + args.version )`.
   - Kiểm tra changelog: `readme.txt` có dòng `= <version> =` và `docs/README-UPDATES.md` có tiêu đề bắt đầu `### <version>`; thiếu thì in `Note:` (không chặn, vì nội dung này được viết ở bước phát hành).
2. `readme.txt:6`: sửa `Stable tag: 1.19.1` thành phiên bản hiện tại trong `style.css`. Đây là sửa metadata cho khớp, không phải tăng phiên bản.
3. `languages/vi.po:3`: `Project-Id-Version: BIGSPY Theme` → `BIGSPY AI Theme WP`. Sửa bằng lệnh thay thế một dòng (`sed`), không mở cả tệp; chạy `python tools/compile-po.py vi`.
4. `docs/README-UPDATES.md`: thêm mục 1.86.1, nội dung lấy từ mục `= 1.86.1 =` của `readme.txt`.
5. Sinh lại `.pot` từ mã: để lại cho đợt phát hành đã duyệt. Khi làm: dùng `wp i18n make-pot` nếu có WP-CLI; nếu không, mở rộng `tools/check-strings.py` (G-121) để xuất `.pot`. Sau đó đối chiếu: msgid trong `vi.po` không còn trong `.pot` được giữ lại (vô hại), không xóa.
6. `Tested up to`: theo G-110.

**Kiểm thử.** `python tools/release.py` → `Ready.` và in đúng ba phiên bản bằng nhau. Sửa tạm `Stable tag` → báo lệch. Hoàn nguyên.

---

## G-123 · Kiến thức AI và CODEMAP

**Ưu tiên:** P2 · **Sản phẩm:** có (phần kiến thức AI) · **Tệp:** `ai-designer/knowledge/theme.md`, `ai-designer/knowledge/blocks.php`, `tools/check-block-previews.js`, `docs/CODEMAP.md`

1. **Kiến thức AI.** Đọc `blocks/listing-elements/block.json`, `blocks/listing-search/block.json` và `inc/listing/blocks.php` để lấy thuộc tính thật. Thêm vào `ai-designer/knowledge/blocks.php` theo đúng cấu trúc các mục `listing-grid`, `listing-info` đang có. Thêm vào `theme.md`, mục “Other capabilities”, tối đa 4 dòng về BIGSPY AI List: chỉ dùng khi tính năng Listing bật; block nào cho trang nào; không tự bật tính năng. Xem `bst_aid_block_types()` (`ai-designer/includes/tools-design.php:76`) để chắc hai block này được phép khi `bst_aid_listing_on()` đúng; nếu chúng bị loại có chủ ý, không thêm vào knowledge mà ghi lý do vào bàn giao.
2. **Kiểm tra tự động.** Trong `tools/check-block-previews.js`: sau kiểm tra hiện có, đọc nối ba tệp trong `ai-designer/knowledge/` và báo lỗi cho mọi thư mục trong `blocks/` có tên không xuất hiện dưới dạng từ nguyên vẹn (biên là ký tự không thuộc `[a-z-]`). Cho phép một danh sách ngoại lệ ngắn có chú thích lý do ngay trong script.
3. **CODEMAP.** Thêm vào `docs/CODEMAP.md`, mỗi mục 1–2 dòng theo giọng văn hiện có:
   - `## Listing (BIGSPY AI List)`: `inc/features/listing.php` nạp `inc/listing/` (`core.php`, `blocks.php`, `sponsored.php`, `collections.php`, `schema.php`, `meta-box.php`, `single.php`); template `woocommerce/single-listing.php`, `content-listing.php`; CSS `assets/css/listing.css`; editor `assets/js/blocks-listing.js`; block cũ `legacy-list/`.
   - `## WooCommerce`: `inc/woocommerce.php` (chỉ nạp khi có WooCommerce), `inc/woocommerce-elements.php`, `inc/shop-options.php`, `inc/product-options.php`, `inc/quick-view.php`, `inc/variation-swatches.php`, `inc/checkout-extras.php`; template `woocommerce/content-product.php`; CSS `assets/css/woocommerce.css`; test `node tools/woo-check.cjs`.
   - `## Performance`: `inc/performance.php` (CSS theo trang, font tự host, CSS sinh ra thành tệp); tệp sinh ra ở `uploads/bigspy/`.
   - `## Forms`: `inc/form-native.php`, `inc/form-migrate.php`, `blocks/form/`; JS gửi form trong `assets/js/blocks.js`.
   Xác nhận từng đường dẫn tồn tại bằng `ls` trước khi ghi.

**Kiểm thử.** `node tools/check-block-previews.js` đạt; xóa tạm một tên block khỏi knowledge → script báo lỗi. `node tools/playground-check.cjs` (designer integration) nếu chạy được.
