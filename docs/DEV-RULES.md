# Quy tắc phát triển và phát hành

Nguồn chính sách chuẩn của repo, chuyển nguyên văn từ `docs/roadmap-vi.md` ngày 30-10-2026. Chỉ đọc mục cần dùng.

## Quy tắc cập nhật và phát hành — owner chốt ngày 28-10-2026

Quy tắc này ưu tiên hơn các hướng dẫn cũ yêu cầu cập nhật thông tin sản phẩm hoặc phát hành sau mỗi thay đổi. **Hoàn thành cập nhật mã không đồng nghĩa với phát hành phiên bản mới.** Mục tiêu là gom đủ thay đổi có ích rồi mới làm một đợt phát hành, tránh tiêu hao hạn mức cho các thao tác lặp lại không cần thiết.

**Áp dụng cho mọi coding agent: Codex, Claude Code, Gemini và các agent khác.** Vẫn phát triển, sửa lỗi, kiểm thử và lưu/commit liên tục; nhiều đợt cập nhật mã có thể được gom vào một bản phát hành cho người dùng. Từ “update” hoặc “tiếp tục” không tự cấp quyền phát hành. Các tệp `AGENTS.md`, `CLAUDE.md` và `GEMINI.md` ở gốc repo trỏ về quy tắc chung này để các agent dùng cùng một chính sách.

### Khi owner yêu cầu sửa, thêm tính năng hoặc “update”

1. Chốt phạm vi theo yêu cầu; triển khai đầy đủ các phần liên quan và kiểm thử phù hợp. Không tự mở rộng sang việc khác trong roadmap.
2. Dùng kiểm tra nhỏ trong lúc làm để phát hiện lỗi sớm. Khi hoàn tất đợt sửa, chạy một lượt kiểm tra tổng hợp cho các thay đổi và chức năng có thể bị ảnh hưởng. Chỉ chạy lại phần lỗi và phần liên quan sau khi sửa; không mặc định dựng lại môi trường hoặc chạy toàn bộ sản phẩm sau mỗi sửa nhỏ.
3. **Bắt buộc commit và push lên Git sau khi hoàn thành và đạt kiểm tra**, không chỉ lưu local hoặc chờ owner nhắc. Push các thay đổi của đợt làm cùng ghi chú đang chờ lên nhánh làm việc được chỉ định, theo quy tắc nhánh/PR của repo; xác minh commit đã có trên remote trước khi báo hoàn tất. Chỉ đưa các thay đổi thuộc phạm vi mình thực hiện vào commit, không gom nhầm tệp cá nhân/thay đổi của người khác; không force-push, ghi đè công việc khác hoặc tự vượt quy tắc bảo vệ nhánh. **Chưa tăng phiên bản phát hành, chưa tạo tag, build ZIP phân phối, tạo GitHub Release, upload asset, deploy hay xóa bản cũ.** Commit/push mã là bước hoàn tất công việc thường xuyên, không phải phát hành sản phẩm.
   - Nếu không push được do xác thực, mạng, xung đột hoặc bảo vệ nhánh: giữ commit an toàn, báo rõ nhánh, mã commit, nguyên nhân và việc còn cần xử lý; không nói rằng đã đồng bộ GitHub, không âm thầm để thay đổi chỉ ở local.
4. Chỉ ghi chú ngắn trong `docs/PENDING-RELEASE.md`: tính năng/lỗi đã xử lý, kết quả kiểm thử và những nội dung cần bổ sung khi phát hành. Không viết lại phần Giới thiệu, Có gì mới, README Updates, changelog, ghi chú Release hoặc đồng bộ nội dung quảng bá sau từng sửa nhỏ.
5. Nếu chính yêu cầu là sửa trang Giới thiệu/tài liệu hoặc nội dung đó là phần bắt buộc để tính năng hoạt động đúng, thực hiện đúng phần được yêu cầu. Quy tắc hoãn nội dung phát hành không được dùng để bỏ sót lỗi chức năng hay tài liệu mà owner đang yêu cầu sửa.
6. Báo rõ “đã cập nhật, kiểm thử, commit và push; đang chờ phát hành”, kèm nhánh và mã commit/link Git để owner hoặc agent khác tiếp tục mà không cần truy cập máy local. Khi có thay đổi sản phẩm sẵn sàng phát hành mà owner chưa yêu cầu rõ, hỏi một lần: **“Bạn có muốn đóng gói và phát hành các thay đổi đang chờ ngay không?”** Không làm trước rồi mới hỏi.
7. Nếu owner nói chưa, hoặc chưa trả lời, giữ nguyên mã và ghi chú đang chờ; tiếp tục tích lũy các yêu cầu tiếp theo. Không tự hỏi lại hoặc tự phát hành trong mỗi tin nhắn tiếp nối. Sửa quy tắc nội bộ/tài liệu quy trình đơn thuần không cần tạo thêm một đề nghị phát hành sản phẩm.

### Giảm công việc lặp lại sau các cập nhật nhỏ

- **Đọc đúng phạm vi:** đầu phiên đọc `AGENTS.md`, rồi `docs/CODEMAP.md` khi cần tìm file; sau đó tìm mã, tài liệu và phụ thuộc liên quan. Không đọc lại toàn repo/lịch sử chat hay rà toàn tài liệu sau từng chỉnh sửa khi không có thông tin mới.
- **Kiểm thử theo tác động:** sửa tài liệu quy trình chỉ cần kiểm tra diff và liên kết liên quan; sửa nhãn/giao diện kiểm tra cú pháp và màn hình chịu ảnh hưởng; sửa logic chạy kiểm tra chức năng liên quan. Thay đổi nền tảng dùng chung, bảo mật, dữ liệu hoặc updater cần kiểm tra rộng hơn theo rủi ro. Không lấy số lượng test làm mục tiêu hoặc viết test chỉ lặp lại nội dung mã.
- **Tái sử dụng môi trường và kết quả:** không cài lại dependencies, dựng lại WordPress, mở thêm môi trường, chạy toàn bộ E2E hay kiểm tra lại trên nhiều website nếu thay đổi không đòi hỏi. Kết quả chỉ được dùng lại khi mã, cấu hình và phụ thuộc liên quan vẫn phù hợp; sửa phần liên quan thì chạy lại kiểm tra tương ứng. Không bỏ cổng CI bắt buộc.
- **Không tạo đầu việc phụ:** không tự refactor, nâng dependency, dọn định dạng toàn repo, dịch lại toàn bộ chuỗi, sinh lại asset hoặc sửa các mục roadmap khác. Chỉ làm phần cần để yêu cầu hoạt động đúng; ghi ngắn vấn đề ngoài phạm vi để owner cân nhắc sau. Bản dịch/asset cần cho chức năng đang sửa vẫn phải hoàn tất.
- **Bằng chứng vừa đủ:** không mặc định tạo báo cáo dài, ảnh chụp mọi màn hình, video, audit hiệu năng/SEO hoặc kiểm tra trên site thật sau mỗi sửa nhỏ. Chỉ dùng khi owner yêu cầu hoặc cần xác minh rủi ro cụ thể. Không tự tạo thêm agent, chat, worktree hay PR nếu quy trình và phạm vi không cần.
- **Tránh vòng lặp công cụ:** tìm kiếm và đọc gọn các phần cần thiết; không lặp lại cùng lệnh lỗi mà chưa có chẩn đoán hoặc thay đổi điều kiện. Không kiểm tra trạng thái/hạn mức liên tục khi không có biến động cần xử lý. Vẫn kiểm tra diff trước commit và xác minh remote sau push.
- **Bàn giao ngắn:** nêu kết quả, kiểm tra đã chạy, nhánh/commit trên GitHub và phần còn vướng. Chỉ ghi một mục đang chờ cho mỗi thay đổi sản phẩm; không nhân bản ghi chú vào nhiều tài liệu. Không cam kết mức tiết kiệm phần trăm khi chưa có phép đo.

Các quy tắc tiết kiệm không cho phép bỏ yêu cầu của owner, kiểm tra cần thiết hoặc để công việc hoàn tất chỉ ở local. Khi đủ thay đổi và được duyệt phát hành, mới chạy các bước chuẩn bị phát hành đã hoãn.

### Hướng dẫn thực hiện bắt buộc cho mọi agent

#### Quy trình một đợt cập nhật

1. Đọc `AGENTS.md` và chính sách trong tệp này khi cần; kiểm tra nhánh và trạng thái Git để tránh ghi đè công việc khác. `CLAUDE.md`/`GEMINI.md` chỉ là điểm vào, không phải chính sách riêng. Báo cáo phiên trước không thay thế việc xác minh trạng thái hiện tại.
2. Xác định kết quả owner muốn, phần mã chịu ảnh hưởng và cách chứng minh kết quả. Với sửa nhỏ, mô tả ngắn là đủ, không tạo thêm tệp kế hoạch/audit toàn dự án. Chỉ hỏi khi thiếu thông tin thực sự chặn công việc; không hỏi lại quyền đã cấp.
3. Gom các chỉnh sửa liên quan trong yêu cầu thành một đợt; kiểm tra nhỏ khi cần, rồi kiểm tra tổng hợp phù hợp lúc mã ổn định. Không tự gom tính năng ngoài yêu cầu. Sau khi sửa lỗi test, chạy lại phần lỗi và phần có thể bị tác động.
4. Xem diff cuối và chạy `git diff --check` trước commit. Nếu mã liên quan thay đổi sau test, chạy lại kiểm tra tương ứng; không chạy lại toàn bộ chỉ vì vừa commit hoặc sửa câu chữ trong ghi chú.
5. Ghi một mục ngắn cho thay đổi sản phẩm trong `docs/PENDING-RELEASE.md`; tài liệu quy trình đơn thuần không cần mục sản phẩm giả. Commit đúng phạm vi, push nhánh được chỉ định, xác minh commit trên remote và bàn giao ngắn. Không báo kiểm tra chưa chạy là đã đạt.

#### Ma trận chọn kiểm tra

| Loại thay đổi | Kiểm tra mặc định | Khi cần mở rộng |
| --- | --- | --- |
| Tài liệu nội bộ/roadmap | Diff, định dạng, đích liên kết/tệp tham chiếu mới hoặc sửa; không dựng WordPress chỉ để kiểm tra văn bản | Có thay đổi lệnh, cấu hình hoặc mã thực thi |
| Nhãn/menu/giao diện một màn hình | Cú pháp tệp thực thi đã sửa; màn hình, hành vi và bản dịch liên quan | CSS dùng chung, responsive, quyền truy cập hoặc điều hướng khác có thể bị ảnh hưởng |
| Logic một tính năng | Hành vi chính, trường hợp lỗi và tích hợp phụ thuộc trực tiếp | Thay đổi hợp đồng dùng chung, dữ liệu, quyền hoặc có kết quả bất thường |
| Ngôn ngữ | Chuỗi/catalog liên quan, mặc định và lựa chọn đã lưu ở ngữ cảnh chịu ảnh hưởng | Sửa bộ nạp locale/domain/fallback: kiểm tra thêm admin/front và việc không đổi locale WordPress/plugin |
| Updater/cấu trúc gói | Phiên bản, cache, nguồn tải, nâng cấp và cấu trúc gói trong môi trường cô lập | Owner yêu cầu luồng GitHub thật hoặc lỗi chỉ xuất hiện trên hosting; không thay bằng cài ZIP thủ công rồi tuyên bố luồng GitHub đã đạt |
| Bảo mật/bootstrap/dữ liệu/nền tảng dùng chung | Kiểm tra rộng các luồng chịu tác động, gồm quyền, trường hợp lỗi và hồi quy cần thiết | Rủi ro hoặc lỗi tái hiện yêu cầu thêm phạm vi |
| Phát hành đã duyệt | Phần tích lũy chưa có bằng chứng hợp lệ, metadata/phiên bản, gói cài và kết quả công bố | Mã/môi trường liên quan đổi khiến kết quả cũ không còn áp dụng |

Ma trận là hướng dẫn theo tác động, không phải giới hạn cứng. Giải thích ngắn khi cần mở rộng; không bỏ CI bắt buộc hoặc kiểm tra quan trọng vì tiết kiệm. Không bắt buộc viết test tự động mới cho sửa văn bản đơn giản; test mới phải kiểm tra hành vi/lỗi có ý nghĩa, không chỉ so khớp lại mã vừa viết.

#### Dùng đúng các script hiện có

- `tools/playground-check.cjs` hiện luôn chạy `tools/tests/designer-integration.php`. `--welcome` **thêm** `welcome-integration.php`, không chạy riêng Giới thiệu. Khi cần fixture này, chạy gộp một lần và ghi đúng phạm vi; không chạy hai lần do tưởng các nhóm độc lập.
- `--serve` giữ máy chủ Playground cho kiểm tra trình duyệt. Tái sử dụng máy chủ còn hợp lệ trong cùng đợt; kết thúc tiến trình mình tạo khi xong, không đóng tiến trình người khác hoặc giữ nhiều máy chủ dư thừa.
- `tools/updater-playground-check.cjs` sao chép theme và tạo ZIP fixture cục bộ. Chỉ chạy khi liên quan updater/gói hoặc cần kiểm tra phát hành. Fixture không phải ZIP phân phối, không được upload hay làm tăng phiên bản sản phẩm. Kiểm tra cô lập không chứng minh site thật đã cập nhật.
- `tools/build-theme.py` và `build.ps1` tạo gói phân phối, không phải lệnh lint/test mặc định: chỉ chạy khi đóng gói đã được duyệt. `build.ps1` còn dọn ZIP phiên bản khác trong `releases`; phải hiểu tác động trước khi chạy.
- Runner có đường dẫn runtime mặc định phụ thuộc máy/thư mục tạm. Kiểm tra cấu hình hiện có và dùng `BST_PLAYGROUND_MODULES`, `BST_PLAYWRIGHT`, `BST_CHROME` khi script hỗ trợ. Không mặc định đường dẫn cũ còn tồn tại; kiểm tra khả năng tái sử dụng trước khi tải/cài lại môi trường. Không ghi đường dẫn cá nhân mới thành yêu cầu chung cho mọi agent.
- Các runner WordPress hiện dùng 6 và 4 workers. Trên máy đã gặp thiếu bộ nhớ, mặc định chạy tuần tự; không mở đồng thời nhiều bộ WordPress. Lỗi bộ nhớ/dung lượng/port cần chẩn đoán và thay đổi điều kiện trước khi chạy lại.
- **Đề xuất chưa triển khai:** tách runner theo nhóm và chuẩn hóa cấu hình môi trường. Việc ghi quy tắc không tạo ra các tính năng này. Không tự nhận script có chế độ chạy riêng/tùy chọn workers nếu chưa có. Chỉ cải tiến công cụ khi được yêu cầu hoặc cần để hoàn thành phạm vi hiện tại.

#### Ngữ cảnh, công cụ và bằng chứng

- Tìm theo tệp/hàm rồi đọc đoạn cần thiết, tránh đổ toàn bộ nguồn/log vào chat. Gom các lần đọc độc lập khi thuận tiện; giữ thay đổi mã, kiểm tra, commit và push theo thứ tự phụ thuộc. Không che lỗi để giảm đầu ra.
- Không tự truy cập web/trình duyệt/site production khi bằng chứng local đã đủ. Vẫn tra nguồn chính thức khi cần xác nhận API hoặc thông tin có thể đổi. Không dùng site thật làm môi trường test mặc định; tuân thủ quyền thay đổi dữ liệu/deploy.
- Không nhờ nhiều agent cùng rà/sửa một phạm vi theo thói quen. Nếu được phép phân công, chia phạm vi rõ và kiểm tra tích hợp một lượt, tránh mọi agent chạy lại cùng bộ nặng.
- Không lặp nguyên lệnh lỗi với cùng điều kiện. Trước lần thử tiếp theo cần chẩn đoán hoặc thay đổi có lý do. Nếu thiếu đầu vào/quyền, báo điểm chặn và làm tiếp phần độc lập; không thử vô hạn hoặc bịa kết quả.
- Khi đổi agent/chat, bàn giao ngắn gồm nhánh/commit, phần xong, kiểm tra đã chạy, việc còn lại và môi trường cần dùng. Không tạo hệ thống báo cáo mới hoặc bắt đọc toàn bộ lịch sử nếu đã đủ thông tin. Không coi chuyển chat là cách đặt lại hạn mức.
- Phân biệt kiểm tra cú pháp, kiểm tra cô lập, kiểm tra trình duyệt và luồng site thật. Bằng chứng chỉ áp dụng cho mã/môi trường đã kiểm tra; giả thuyết cache/hosting chưa quan sát không phải nguyên nhân đã xác nhận.

### Chỉ khi owner đồng ý đóng gói và phát hành

1. Gom toàn bộ thay đổi đã chốt trong `docs/PENDING-RELEASE.md` thành **một đợt phát hành**; xác nhận phạm vi và chọn phiên bản phù hợp.
2. Lúc này mới cập nhật đồng bộ các nội dung cần thiết: dữ liệu Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI liên quan, bản dịch và ghi chú Release. Chỉ mô tả tính năng thực sự có trong gói phát hành.
3. Chạy các cổng kiểm tra còn cần cho đợt phát hành; tái sử dụng kết quả hợp lệ đã có. Chỉ mở rộng kiểm thử khi có thay đổi hoặc rủi ro cụ thể.
4. Tăng phiên bản, build và xác minh ZIP một lần, commit phần chuẩn bị phát hành, tạo tag và GitHub Release, upload đúng asset rồi kiểm tra kết quả.
5. Chỉ xóa release/tag cũ khi owner đã chấp thuận rõ việc dọn bản cũ cho đợt phát hành. Không gộp ngầm quyền xóa vào yêu cầu sửa tính năng. Chính sách giữ bản mới nhất được áp dụng sau khi bước dọn bản cũ đã được cho phép.
6. Ghi nhận các mục đã phát hành và xóa chúng khỏi danh sách đang chờ. Việc triển khai lên website là bước riêng, theo quyền owner đã cấp cho đúng website.

Không tự quy đổi số lệnh, thời gian chạy test hoặc số token thành phần trăm hạn mức. Với đợt lớn, có thể ghi mức sử dụng đầu/cuối nếu công cụ cung cấp, nhưng phải nêu rõ đó là số liệu toàn tài khoản và không phải phép đo chi phí riêng từng lệnh.
