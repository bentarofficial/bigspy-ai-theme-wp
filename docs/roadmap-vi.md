# Roadmap BIGSPY AI Theme WP

## Quy tắc cập nhật và phát hành — owner chốt ngày 28-10-2026

Quy tắc này ưu tiên hơn các hướng dẫn cũ yêu cầu cập nhật thông tin sản phẩm hoặc phát hành sau mỗi thay đổi. **Hoàn thành cập nhật mã không đồng nghĩa với phát hành phiên bản mới.** Mục tiêu là gom đủ thay đổi có ích rồi mới làm một đợt phát hành, tránh tiêu hao hạn mức cho các thao tác lặp lại không cần thiết.

**Áp dụng cho mọi coding agent: Codex, Claude Code, Gemini và các agent khác.** Vẫn phát triển, sửa lỗi, kiểm thử và lưu/commit liên tục; nhiều đợt cập nhật mã có thể được gom vào một bản phát hành cho người dùng. Từ “update” hoặc “tiếp tục” không tự cấp quyền phát hành. Các tệp `AGENTS.md`, `CLAUDE.md` và `GEMINI.md` ở gốc repo trỏ về quy tắc chung này để các agent dùng cùng một chính sách.

### Khi owner yêu cầu sửa, thêm tính năng hoặc “update”

1. Chốt phạm vi theo yêu cầu; triển khai đầy đủ các phần liên quan và kiểm thử phù hợp. Không tự mở rộng sang việc khác trong roadmap.
2. Dùng kiểm tra nhỏ trong lúc làm để phát hiện lỗi sớm. Khi hoàn tất đợt sửa, chạy một lượt kiểm tra tổng hợp cho các thay đổi và chức năng có thể bị ảnh hưởng. Chỉ chạy lại phần lỗi và phần liên quan sau khi sửa; không mặc định dựng lại môi trường hoặc chạy toàn bộ sản phẩm sau mỗi sửa nhỏ.
3. Lưu/commit kết quả phát triển khi đã đạt kiểm tra. **Chưa tăng phiên bản phát hành, chưa tạo tag, build ZIP phân phối, tạo GitHub Release, upload asset, deploy hay xóa bản cũ.** Việc push mã cũng không được tự hiểu là quyền phát hành hoặc deploy.
4. Chỉ ghi chú ngắn trong `docs/PENDING-RELEASE.md`: tính năng/lỗi đã xử lý, kết quả kiểm thử và những nội dung cần bổ sung khi phát hành. Không viết lại phần Giới thiệu, Có gì mới, README Updates, changelog, ghi chú Release hoặc đồng bộ nội dung quảng bá sau từng sửa nhỏ.
5. Nếu chính yêu cầu là sửa trang Giới thiệu/tài liệu hoặc nội dung đó là phần bắt buộc để tính năng hoạt động đúng, thực hiện đúng phần được yêu cầu. Quy tắc hoãn nội dung phát hành không được dùng để bỏ sót lỗi chức năng hay tài liệu mà owner đang yêu cầu sửa.
6. Báo rõ “đã cập nhật và kiểm thử, đang chờ phát hành”. Khi có thay đổi sản phẩm sẵn sàng phát hành mà owner chưa yêu cầu rõ, hỏi một lần: **“Bạn có muốn đóng gói và phát hành các thay đổi đang chờ ngay không?”** Không làm trước rồi mới hỏi.
7. Nếu owner nói chưa, hoặc chưa trả lời, giữ nguyên mã và ghi chú đang chờ; tiếp tục tích lũy các yêu cầu tiếp theo. Không tự hỏi lại hoặc tự phát hành trong mỗi tin nhắn tiếp nối. Sửa quy tắc nội bộ/tài liệu quy trình đơn thuần không cần tạo thêm một đề nghị phát hành sản phẩm.

### Chỉ khi owner đồng ý đóng gói và phát hành

1. Gom toàn bộ thay đổi đã chốt trong `docs/PENDING-RELEASE.md` thành **một đợt phát hành**; xác nhận phạm vi và chọn phiên bản phù hợp.
2. Lúc này mới cập nhật đồng bộ các nội dung cần thiết: dữ liệu Giới thiệu, Có gì mới, README Updates/changelog, kiến thức AI liên quan, bản dịch và ghi chú Release. Chỉ mô tả tính năng thực sự có trong gói phát hành.
3. Chạy các cổng kiểm tra còn cần cho đợt phát hành; tái sử dụng kết quả hợp lệ đã có. Chỉ mở rộng kiểm thử khi có thay đổi hoặc rủi ro cụ thể.
4. Tăng phiên bản, build và xác minh ZIP một lần, commit phần chuẩn bị phát hành, tạo tag và GitHub Release, upload đúng asset rồi kiểm tra kết quả.
5. Chỉ xóa release/tag cũ khi owner đã chấp thuận rõ việc dọn bản cũ cho đợt phát hành. Không gộp ngầm quyền xóa vào yêu cầu sửa tính năng. Chính sách giữ bản mới nhất được áp dụng sau khi bước dọn bản cũ đã được cho phép.
6. Ghi nhận các mục đã phát hành và xóa chúng khỏi danh sách đang chờ. Việc triển khai lên website là bước riêng, theo quyền owner đã cấp cho đúng website.

Không tự quy đổi số lệnh, thời gian chạy test hoặc số token thành phần trăm hạn mức. Với đợt lớn, có thể ghi mức sử dụng đầu/cuối nếu công cụ cung cấp, nhưng phải nêu rõ đó là số liệu toàn tài khoản và không phải phép đo chi phí riêng từng lệnh.

**Mốc phát hành:** 1.18.1 · **Đối chiếu:** Flatsome Classic 3.20.11. Trang Home Fresh Mart được quan sát ở bản 3.20.10; UX-Themes công bố Flatsome Site Builder beta ngày 30-09-2026, là sản phẩm block/plugin riêng.
Roadmap này sắp xếp các khoảng trống đã ghi nhận theo tác động đến shop và người làm nội dung. Đây là thứ tự ưu tiên đề xuất, không phải cam kết ngày phát hành.

**Hướng dựng trang:** lấy màn hình **Sửa trang** của WordPress và các khối BIGSPY làm luồng chính. Workspace BIGSPY AI Builder riêng đã được gỡ ở 1.18.0; UX Builder/shortcode Flatsome chỉ cần được giữ để hiển thị nội dung cũ và hỗ trợ chuyển đổi có xem trước. Không cần tái tạo UX Builder thành một editor song song.

## Đã có đến 1.18.1

- Sửa lỗi lựa chọn trang thiết kế trống khiến nội dung trang chủ lặp lại trong footer; khôi phục footer widget mặc định và chặn trang tự làm footer của chính nó.

- Các khối BIGSPY và thư viện mẫu dùng trực tiếp trong Sửa trang WordPress; cây cấu trúc, sắp xếp, lưu và revisions dùng editor native. Trang block đã xuất bản vẫn có thể làm thiết kế header/footer/trang sản phẩm.
- Tùy chọn Buy Now, Quick View, Portfolio, countdown, mega menu nhiều cột, load more/infinite scroll cho archive classic.
- Swatch nhãn/màu/ảnh trên trang sản phẩm cho global WooCommerce attributes; mặc định tắt.
- Swatch màu/ảnh tùy chọn trên thẻ sản phẩm trang shop/danh mục; mở trang sản phẩm với giá trị được chọn sẵn.
- Điều hướng AJAX tùy chọn cho widget lọc WooCommerce native: danh mục, thuộc tính, đánh giá, giá và sắp xếp; có history, thông báo trạng thái và tải lại toàn trang khi không thể cập nhật an toàn.
- Gallery ảnh theo biến thể được dùng thông qua WooCommerce Variation Gallery native (từ WooCommerce 10.9, tùy phiên bản/cờ tính năng). Theme giữ nguyên gallery markup chuẩn WooCommerce và hỗ trợ các layout/zoom/lightbox/slider hiện có; không lưu thêm bộ ảnh biến thể riêng.

## Ưu tiên tiếp theo

### P0 — Hoàn thiện trải nghiệm shop

1. **Kiểm tra tương thích gallery biến thể:** xác nhận gallery native trên các phiên bản WooCommerce đang hỗ trợ, gồm đổi variation liên tiếp, reset, bố cục stacked/thumbnail-left, zoom và lightbox. Tính năng tạo/lưu ảnh thuộc WooCommerce; theme chỉ bổ sung adapter nếu tái hiện được lỗi ở gallery markup chuẩn.
2. **Kiểm tra tương thích lọc:** xác nhận widget native với phân trang, load more, lịch sử trình duyệt, bộ nhớ đệm và markup phổ biến. Tài liệu hóa cách plugin lọc bên thứ ba có thể nối sự kiện hoặc dùng điều hướng gốc; không giả định mọi plugin có cùng API.
3. **Tùy chọn checkout còn thiếu:** xem xét hiển thị ngưỡng miễn phí vận chuyển và vị trí coupon/nội dung bổ sung như lựa chọn giao diện, không thay đổi quy tắc tính phí hay thứ tự nghiệp vụ WooCommerce.

### P1 — Dựng trang và nội dung

1. **Hoàn thiện luồng block trong Sửa trang:** làm rõ điểm bắt đầu, giúp tìm/chèn block BIGSPY và mẫu bố cục nhanh, giữ cấu trúc block WordPress để sửa bằng editor chuẩn. Tập trung các cải tiến trong editor WordPress và các khối BIGSPY.
2. **Di chuyển nội dung UX Builder cũ:** công cụ xem trước các shortcode Flatsome nhận diện được, cho chọn phần cần chuyển sang block native, giữ bản gốc để hoàn tác và báo rõ shortcode/plugin không hỗ trợ. Không tự chuyển hoặc xóa nội dung khi chưa có bản xem trước.
3. **Mega menu có block:** bổ sung vùng nội dung tùy chọn cho từng mục menu cấp cao; hỗ trợ điều khiển bằng bàn phím, focus, mobile drawer và fallback về submenu chuẩn.
4. **Tìm kiếm và thư viện mẫu:** bổ sung bố cục tìm kiếm hàng/masonry/slider, tùy chọn sắp xếp sản phẩm và ảnh xem trước nhẹ cho pattern; giữ cấu hình nguồn kết quả và nội dung block native.

### P2 — Hiệu năng và tích hợp

1. **Quản lý tài nguyên:** xem xét công tắc tải CSS/media theo nhóm và trạng thái tương thích; mọi mục có giải thích, mặc định an toàn và cách khôi phục. Không tắt jQuery hoặc tài nguyên dùng chung chỉ để đạt một điểm benchmark.
2. **Site loader:** chỉ thêm khi có chế độ giảm chuyển động, không chặn thao tác bàn phím, có timeout/fallback và không che lỗi tải trang.
3. **Khối nội dung bổ sung:** đánh giá hotspot, Lottie và Instagram/feed theo nhu cầu thực tế; cấu hình nguồn rõ ràng, không nhúng token bí mật trong nội dung công khai và không thêm thư viện nếu khối native đáp ứng được.
4. **Adapter Countix:** chỉ làm sau khi chốt phiên bản plugin và markup thực tế. Không đưa bản vá ghi đè hàm jQuery `.html()` hoặc fix riêng child theme vào core.

## Điều kiện đưa tính năng vào bản phát hành

- Dùng API WordPress/WooCommerce và block native; không sao chép mã, asset hoặc thương hiệu Flatsome.
- Tính năng làm thay đổi hành vi shop mặc định tắt hoặc có fallback điều hướng chuẩn.
- Có trạng thái responsive, keyboard/focus và thông báo trợ năng phù hợp.
- Tài liệu nêu rõ plugin/phạm vi tương thích và những gì chưa hỗ trợ.
- Tạo bản xem trước/hoàn tác cho thao tác chuyển đổi nội dung hoặc cấu hình có thể gây mất dữ liệu.

## Phạm vi chưa cam kết

BIGSPY là theme riêng, không phải bản sao đầy đủ của UX Builder. Tương thích mọi shortcode Flatsome, mọi filter plugin và mọi tích hợp mạng xã hội cần adapter hoặc quyền/API riêng; chỉ đưa vào roadmap phát hành sau khi có phạm vi và cách kiểm tra cụ thể.
