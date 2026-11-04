# Nghiên cứu mô hình tính giá theo ngành — bổ sung G-57/G-58

Ngày 04-11-2026. Tổng hợp mô hình giá phổ biến theo ngành để thiết kế **một pricing engine chung** cho Form, không làm form riêng cho từng ngành. Đây là nghiên cứu, chưa triển khai; chưa khảo sát plugin/đối thủ cụ thể. Hệ số mẫu (ví dụ chia 5000/6000 cho cân nặng quy đổi) khác nhau theo hãng, admin phải tự nhập.

Đặc tả gốc: [FORMS-BOOKING-ROADMAP.md](FORMS-BOOKING-ROADMAP.md). Tài liệu này mở rộng danh sách khối quy tắc và thêm G-68/G-69.

## 1. Cách tính giá theo lĩnh vực

| Lĩnh vực | Ví dụ | Yếu tố giá chính |
| --- | --- | --- |
| Vận tải | Taxi, đưa đón sân bay | km thật, phút chờ, loại xe, một chiều/khứ hồi, phụ phí đêm/lễ, cầu đường |
| | Chuyển nhà/văn phòng | km, m³ hoặc loại xe tải, tầng lầu, có thang máy, khoảng khiêng bộ, tháo lắp, đóng gói |
| | Giao hàng | kg, cân nặng quy đổi D×R×C÷hệ số, vùng, tốc độ giao, COD %, bảo hiểm |
| | Vé xe tuyến | tuyến cố định, loại ghế/giường, giờ khởi hành, giá trẻ em |
| | Thuê xe tự lái | số ngày, ngày thường/cuối tuần, giới hạn km + phụ phí vượt, cọc, bảo hiểm, giao nhận xe |
| Xây dựng, nội thất | Xây nhà trọn gói | m² quy đổi (móng/tầng/mái/sân theo % hệ số), gói vật tư, tỉnh |
| | Sơn, ốp lát, sàn | m²/m dài, vật liệu, hao hụt %, tháo dỡ |
| | Rèm, kính, nhôm, tủ bếp | rộng × cao, mẫu/chất liệu, phụ kiện, diện tích tối thiểu tính tiền |
| | Sửa chữa, điện nước | phí kiểm tra + vật tư + công, số thiết bị, di chuyển |
| | Vệ sinh, giặt sofa | m², số chỗ ngồi, mức bẩn, giờ × người |
| In ấn, sản xuất | In ấn | bậc số lượng, khổ, giấy, số màu, 1/2 mặt, gia công, số trang |
| | Biển hiệu, banner | m², chất liệu, LED, thi công trên cao |
| | Đồng phục, in áo | bậc số lượng, số vị trí/màu in, phụ phí size lớn |
| | Gia công, in 3D, laser | vật liệu, khối lượng/thể tích, thời gian máy, hoàn thiện |
| Lưu trú, sự kiện | Khách sạn, homestay | số đêm, giá theo thứ/mùa, phụ thu người, trẻ em theo tuổi, giảm ở dài |
| | Tour | người lớn/trẻ em/em bé, phụ thu phòng đơn, ngày khởi hành |
| | Tiệc, hội nghị | bàn/khách × suất, thực đơn, phí phòng, trang trí, VAT + phí phục vụ % |
| | Thuê sân/phòng/studio | giờ, khung cao điểm, block tối thiểu |
| Làm đẹp, sức khỏe, giáo dục | Spa, salon, nha khoa | dịch vụ, combo, gói nhiều buổi, độ dài tóc/số răng, cấp kỹ thuật viên |
| | Khám, xét nghiệm | gói + hạng mục thêm, phí tại nhà |
| | Gia sư, khóa học, gym | buổi × thời lượng, cấp lớp, online/offline, giá nhóm, gói tháng/năm |
| Dịch vụ chuyên môn | Web, app, logo | gói + tính năng thêm, số trang/phương án/lần sửa, gấp +% |
| | Dịch thuật | số từ/trang, cặp ngôn ngữ, chuyên ngành, công chứng, gấp |
| | Quay chụp, marketing | giờ/buổi, số sản phẩm, hậu kỳ, nhân sự, thiết bị |
| | Kế toán, pháp lý | quy mô (hóa đơn, nhân viên), theo tháng, phí cố định + lệ phí nhà nước |
| | Hosting, SaaS | tháng/năm (trả năm giảm), số người dùng, theo mức dùng |
| Tài chính, BĐS, năng lượng | Trả góp | số tiền, lãi, kỳ hạn, dư nợ giảm dần/lãi phẳng → bảng trả hàng tháng |
| | Bảo hiểm | tuổi, giá trị, mức bảo hiểm, hệ số rủi ro |
| | Bất động sản | m² × đơn giá, tầng, hướng, chiết khấu theo tiến độ |
| | Điện mặt trời | tiền điện/tháng → kWp → chi phí, thời gian hoàn vốn |
| Thực phẩm, bán lẻ | Bánh kem, đặt món | size, tầng, nhân, trang trí, giao hàng |
| | Hàng cân, bán sỉ | kg × giá theo hạng, bậc số lượng, nhóm khách (đại lý/CTV/lẻ) |
| | Suất ăn công nghiệp | suất/ngày × ngày, thực đơn, cam kết tối thiểu |

## 2. Khối quy tắc dùng chung (bổ sung cho G-58)

| # | Khối | Ghi chú |
| --- | --- | --- |
| 1 | Giá cố định/gói | |
| 2 | Số lượng × đơn giá | |
| 3 | Bậc thang | Hai chế độ: cả lượng theo bậc đạt được, hoặc lũy tiến từng bậc |
| 4 | Kích thước | dài, m², m³, kg, cân nặng quy đổi (lấy max thực/quy đổi), tối thiểu tính tiền |
| 5 | Thời gian | giờ/ngày/đêm/tháng/buổi, block tối thiểu, qua đêm |
| 6 | Lịch/mùa | thứ trong tuần, ngày lễ, mùa, khung giờ; có khoảng hiệu lực |
| 7 | Vị trí | vùng, tuyến cố định (G-58); km/phút thật qua adapter (G-68) |
| 8 | Đối tượng | người lớn/trẻ em theo tuổi, số khách, nhóm khách hàng |
| 9 | Biến thể/chất liệu | bảng tra đơn giá theo ID lựa chọn |
| 10 | Tùy chọn cộng thêm | + tiền, + %, × số lượng; chỉ định áp trên khoản nào |
| 11 | Hệ số % | gấp, cao cấp, mức khó |
| 12 | Giảm giá | đặt sớm, ở dài, combo, trả năm; coupon thanh toán thuộc backend |
| 13 | Thuế/phí hiển thị | VAT, phí phục vụ, COD %; thuế chính thức của đơn thuộc backend |
| 14 | Ràng buộc | min/max, làm tròn, giới hạn phục vụ, đặt cọc % |
| 15 | Công thức giới hạn | trả góp (PMT), hoàn vốn, m² quy đổi; không `eval` |
| 16 | Báo giá thủ công | ngưỡng “Cần xác nhận giá”, chỉ thu thông tin |

Thứ tự tính mặc định, hiển thị rõ và cho đổi có kiểm soát: đơn giá theo biến thể → × kích thước/số lượng/thời gian → bậc → lịch/mùa → tùy chọn → hệ số → giảm giá → thuế/phí → làm tròn, min/max → đặt cọc.

Dạng kết quả: tổng, khoảng giá (“từ … đến …”), bảng dòng chi tiết, bảng trả góp, “Cần xác nhận giá”. Bảng tra dài (vùng, tuyến, bậc, mùa) cần nhập/xuất CSV.

## 3. Tính theo quãng đường (G-68)

1. Trường **Địa chỉ** có gợi ý: gõ → REST endpoint của theme/plugin → nhà cung cấp (Goong, VietMap, Google Places, Mapbox). Lưu `place_id` + tọa độ, không chỉ chuỗi chữ.
2. Đủ điểm đi/đến/dừng → server gọi routing lấy **km và phút thật**; không dùng đường chim bay làm cước tuyến.
3. Khối quy tắc vị trí nhận `distance_km`, `duration_min` như biến; công thức ví dụ: mở cửa + km × đơn giá + phút chờ × đơn giá + phụ phí.
4. API key chỉ ở server; key autocomplete phía trình duyệt (nếu bắt buộc) phải giới hạn domain. Cache theo cặp điểm, debounce khi gõ, giới hạn quota.
5. Lỗi/hết quota/ngoài bán kính phục vụ → “Cần xác nhận giá”, không đoán.
6. Interface adapter `autocomplete / geocode / route`, đổi nhà cung cấp không sửa form. Nhà cung cấp, quota và chi phí do owner chốt trước khi code.

Adapter giao hàng (GHN, GHTK, Viettel Post…) là cùng dạng “nguồn giá ngoài”, khảo sát sau G-68.

## 4. Task đề xuất

| Task | Phạm vi | Phụ thuộc |
| --- | --- | --- |
| G-68 | Adapter địa chỉ/khoảng cách + trường Địa chỉ, biến `distance_km`/`duration_min` | G-58; owner chốt nhà cung cấp/chi phí |
| G-69 | Preset theo ngành dựng từ khối quy tắc (giặt là, vệ sinh, chuyển nhà, in ấn, rèm/m², lưu trú, spa gói buổi, trả góp); giá mẫu ghi **demo** | G-58; AI dùng ở G-59 |

G-58 nên đặc tả schema đủ 16 khối ở mục 2 ngay từ đầu (có thể giao UI theo đợt) để G-68/G-69 không phải đổi schema.
