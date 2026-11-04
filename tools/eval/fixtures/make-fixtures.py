"""Write the replay fixtures (hand-made AI Designer results, good and bad).

python tools/eval/fixtures/make-fixtures.py
They prove the scorer tells good from bad output; they are not model output.
"""
import json, os

HERE = os.path.dirname(os.path.abspath(__file__))

def b(name, attrs=None, *inner, html=None):
    n = {"name": name}
    if attrs: n["attrs"] = attrs
    if inner: n["inner"] = list(inner)
    if html is not None: n["html"] = html
    return n

def sec(*inner, **attrs): return b("bst/section", attrs or None, *inner)
def row(*cols): return b("bst/row", None, *cols)
def col(span, *inner): return b("bst/column", {"spanDesktop": span, "spanMobile": 12}, *inner)
def h(tag, text): return b("bst/heading", {"tag": tag, "text": text})
def t(html): return b("bst/text", {"html": html})
def btn(text, url="#"): return b("bst/buttons", None, b("bst/button", {"text": text, "url": url}))
def icon_box(title, text): return b("bst/icon-box", {"icon": "check", "title": title, "text": text})
def faq(*qa): return b("bst/accordion", None, *[b("bst/accordion-item", {"title": q}, t("<p>%s</p>" % a)) for q, a in qa])
def form(key, fields, pricing=None, **extra):
    a = {"provider": "bigspy", "formKey": key, "fields": fields, "submitText": "Gửi"}
    if pricing: a["pricing"] = pricing
    a.update(extra)
    return b("bst/form", a)

def ev(kind, tool, text=""): return {"kind": kind, "tool": tool, "text": text}
def log_ok(*tools): return [{"kind": "user", "text": "brief"}] + [ev("tool", x) for x in tools] + [{"kind": "assistant", "text": "Xong."}]
def metrics(calls, model_calls, tin, tout, sec, cost, tools):
    return {"tool_calls": calls, "model_calls": model_calls, "tokens_in": tin, "tokens_out": tout, "seconds": sec, "cost": cost, "tools": tools}

GOOD, BAD = {}, {}

# 01 Spa ------------------------------------------------------------------
spa_fields = [
    {"name": "ho-ten", "label": "Họ tên", "type": "text", "required": True},
    {"name": "dien-thoai", "label": "Số điện thoại", "type": "tel", "required": True},
    {"name": "ngay-hen", "label": "Ngày hẹn", "type": "date", "required": True},
    {"name": "dich-vu", "label": "Dịch vụ", "type": "select", "options": "Massage body 60 phút\nChăm sóc da mặt\nGội đầu dưỡng sinh"},
]
GOOD["01-spa-dat-lich"] = {
    "tree": [
        sec(h("h1", "Spa Hoa Sen – thư giãn giữa Quận 3"), t("<p>12 Trần Quốc Thảo, Quận 3, TP.HCM · Hotline <a href=\"tel:0901234567\">0901 234 567</a></p>"), btn("Đặt lịch ngay", "#dat-lich")),
        sec(h("h2", "Dịch vụ & bảng giá"), row(
            col(4, b("bst/price-table", {"title": "Massage body 60 phút", "price": "350.000đ", "period": "", "features": "Tinh dầu thiên nhiên", "buttonText": "Đặt lịch", "buttonUrl": "#dat-lich"})),
            col(4, b("bst/price-table", {"title": "Chăm sóc da mặt", "price": "450.000đ", "period": "", "features": "Làm sạch sâu", "buttonText": "Đặt lịch", "buttonUrl": "#dat-lich"})),
            col(4, b("bst/price-table", {"title": "Gội đầu dưỡng sinh", "price": "150.000đ", "period": "", "features": "Thảo dược", "buttonText": "Đặt lịch", "buttonUrl": "#dat-lich"})))),
        sec(h("h2", "Đặt lịch"), form("spa-booking", spa_fields), anchor="dat-lich"),
    ],
    "log": log_ok("get_knowledge", "block_catalog", "page_plan", "build_sections", "create_page"),
    "metrics": metrics(5, 6, 61000, 5200, 74.2, 0.071, {"get_knowledge": 1, "block_catalog": 1, "page_plan": 1, "build_sections": 1, "create_page": 1}),
}
BAD["01-spa-dat-lich"] = {
    "tree": [
        sec(h("h1", "Spa Hoa Sen"), {"raw": "<!-- wp:html --><div style=\"display:flex\"><h1>Thư giãn</h1><p>Hơn 5.000 khách hàng hài lòng</p></div><!-- /wp:html -->"}),
        sec(h("h2", "Dịch vụ"), t("<div class=\"grid\"><p>Massage 350.000đ</p><p>Combo VIP 990.000đ – giảm 20%</p></div>")),
        sec(b("bst/testimonial", {"name": "Chị Lan", "text": "Dịch vụ tuyệt vời!", "rating": 5}), b("bst/counter", {"number": "10", "suffix": "+", "label": "năm kinh nghiệm"})),
        sec(h("h2", "Liên hệ"), t("<p>Gọi 0908 111 222</p>")),
        {"raw": "<!-- wp:html --><section class=\"promo\"><h2>Ưu đãi</h2></section><!-- /wp:html -->"},
        {"raw": "<!-- wp:html --><div class=\"map\"></div><!-- /wp:html -->"},
    ],
    "log": [{"kind": "user", "text": "brief"}, ev("tool", "get_knowledge"), ev("tool-error", "create_page", "bst/text holds rich text only, not layout HTML."),
            ev("tool-error", "create_page", "new core/html blocks are not allowed."), ev("tool-error", "create_page", "Not saved: only 80% of this content could be edited"),
            ev("tool", "create_page"), {"kind": "assistant", "text": "Xong."}],
    "metrics": metrics(5, 9, 142000, 11800, 211.5, 0.168, {"get_knowledge": 1, "create_page": 4}),
}

# 02 Giặt là ---------------------------------------------------------------
gl_fields = [
    {"name": "dien-thoai", "label": "Số điện thoại", "type": "tel", "required": True},
    {"name": "so-kg", "label": "Số kg giặt sấy", "type": "number"},
    {"name": "so-vest", "label": "Số áo vest giặt hấp", "type": "number"},
    {"name": "giao-nhan", "label": "Giao nhận tận nơi", "type": "radio", "options": "Có\nKhông"},
]
gl_pricing = {"enabled": True, "currency": "VND", "decimals": 0, "lines": [
    {"id": "giat", "label": "Giặt sấy", "kind": "qty", "field": "n_so-kg", "unit": 25000},
    {"id": "vest", "label": "Giặt hấp vest", "kind": "qty", "field": "n_so-vest", "unit": 80000},
    {"id": "ship", "label": "Giao nhận", "kind": "fixed", "amount": 20000, "when": {"logic": "all", "rules": [{"field": "n_giao-nhan", "op": "is", "value": "Có"}]}},
]}
GOOD["02-giat-la-bao-gia"] = {
    "tree": [
        sec(h("h1", "Giặt là Sạch Thơm – báo giá ngay"), t("<p>Giặt sấy 25.000đ/kg, giặt hấp áo vest 80.000đ/cái, giao nhận tận nơi +20.000đ.</p>")),
        sec(h("h2", "Tính tiền tạm"), form("giat-la-quote", gl_fields, gl_pricing)),
    ],
    "log": log_ok("get_knowledge", "form_config", "form_config", "create_page"),
    "metrics": metrics(4, 6, 58000, 6100, 81.0, 0.069, {"get_knowledge": 1, "form_config": 2, "create_page": 1}),
}
bad_pricing = {"enabled": True, "currency": "VND", "lines": [
    {"id": "giat", "label": "Giặt sấy", "kind": "qty", "field": "kg", "unit": 30000},
    {"id": "vip", "label": "Phí gấp", "kind": "formula", "expr": "{giat} * {heso} + 15000"},
]}
BAD["02-giat-la-bao-gia"] = {
    "tree": [
        sec(h("h2", "Bảng giá giặt là"), t("<p>Chỉ từ 19k/kg!</p>")),
        sec(form("giat-la-quote", [{"name": "kg", "label": "Khối lượng", "type": "slider"}, {"label": "", "type": "text"}], bad_pricing)),
    ],
    "log": [{"kind": "user", "text": "brief"}, ev("tool-error", "form_config", "fields/0: unknown type"), ev("tool-error", "form_config", "line vip: formula"), ev("tool-error", "create_page", "x"), ev("tool", "create_page")],
    "metrics": metrics(4, 8, 120000, 9000, 160.0, 0.12, {"form_config": 2, "create_page": 2}),
}

# 03 Nha khoa ---------------------------------------------------------------
nk_fields = [
    {"name": "ho-ten", "label": "Họ tên", "type": "text", "required": True},
    {"name": "dien-thoai", "label": "Số điện thoại", "type": "tel", "required": True},
    {"name": "nhu-cau", "label": "Dịch vụ quan tâm", "type": "select", "options": "Niềng răng\nBọc răng sứ\nCạo vôi\nTẩy trắng"},
]
GOOD["03-nha-khoa-chua-co-gia"] = {
    "tree": [
        sec(h("h1", "Nha khoa Nụ Cười"), t("<p>Niềng răng, bọc răng sứ, cạo vôi và tẩy trắng. Bảng giá: [giá dịch vụ – điền sau].</p>")),
        sec(h("h2", "Dịch vụ"), row(col(6, icon_box("Niềng răng", "[mô tả]")), col(6, icon_box("Bọc răng sứ", "[mô tả]"))), row(col(6, icon_box("Cạo vôi", "[mô tả]")), col(6, icon_box("Tẩy trắng", "[mô tả]")))),
        sec(h("h2", "Câu hỏi thường gặp"), faq(("Niềng răng mất bao lâu?", "[câu trả lời của phòng khám]"), ("Có trả góp không?", "[câu trả lời của phòng khám]"))),
        sec(h("h2", "Đặt lịch tư vấn"), form("nk-booking", nk_fields)),
    ],
    "log": log_ok("block_catalog", "page_plan", "build_sections", "create_page"),
    "metrics": metrics(4, 5, 52000, 4800, 66.0, 0.06, {"block_catalog": 1, "page_plan": 1, "build_sections": 1, "create_page": 1}),
}
BAD["03-nha-khoa-chua-co-gia"] = {
    "tree": [
        sec(h("h1", "Nha khoa Nụ Cười"), t("<p>Hơn 15 năm kinh nghiệm, 98% khách hài lòng.</p>")),
        sec(h("h2", "Bảng giá"), row(col(6, b("bst/price-table", {"title": "Niềng răng"})), col(6, b("bst/price-table", {"title": "Tẩy trắng", "price": "2.500.000đ"})))),
        sec(h("h2", "Bác sĩ"), b("bst/team-member", None)),
        sec(h("h1", "Đặt lịch"), form("nk", [{"name": "ten", "label": "Tên", "type": "text"}])),
    ],
    "log": log_ok("create_page"),
    "metrics": metrics(1, 3, 30000, 5000, 40.0, 0.04, {"create_page": 1}),
}

# 11 In ấn (tier) and 14 Tiệc cưới (percent) --------------------------------
GOOD["11-in-an-bac-thang"] = {
    "tree": [
        sec(h("h1", "In danh thiếp In Tân Phát"), t("<p>1–4 hộp: 60.000đ/hộp. Từ 5 hộp: 50.000đ/hộp cho toàn bộ số hộp.</p>")),
        sec(form("in-quote", [
            {"name": "ho-ten", "label": "Họ tên", "type": "text"},
            {"name": "dien-thoai", "label": "Điện thoại", "type": "tel"},
            {"name": "so-hop", "label": "Số hộp", "type": "number"}],
            {"enabled": True, "currency": "VND", "lines": [{"id": "in", "label": "In danh thiếp", "kind": "tier", "field": "n_so-hop", "mode": "all", "tiers": [{"from": 1, "unit": 60000}, {"from": 5, "unit": 50000}]}]})),
    ],
    "log": log_ok("get_knowledge", "form_config", "form_config", "create_page"),
    "metrics": metrics(4, 6, 55000, 5000, 70.0, 0.06, {"get_knowledge": 1, "form_config": 2, "create_page": 1}),
}
GOOD["14-tiec-cuoi-phan-tram"] = {
    "tree": [
        sec(h("h1", "Tiệc cưới Trung tâm Hồng Phúc"), t("<p>Thực đơn 3.500.000đ/bàn 10 người, phí phục vụ 5%.</p>")),
        sec(form("tiec-quote", [
            {"name": "ho-ten", "label": "Họ tên", "type": "text"},
            {"name": "dien-thoai", "label": "Điện thoại", "type": "tel"},
            {"name": "ngay-cuoi", "label": "Ngày cưới", "type": "date"},
            {"name": "so-ban", "label": "Số bàn", "type": "number"}],
            {"enabled": True, "currency": "VND", "lines": [
                {"id": "menu", "label": "Thực đơn", "kind": "qty", "field": "n_so-ban", "unit": 3500000},
                {"id": "pv", "label": "Phí phục vụ", "kind": "percent", "pct": 5, "targets": ["menu"]}]})),
    ],
    "log": log_ok("get_knowledge", "form_config", "form_config", "create_page"),
    "metrics": metrics(4, 6, 57000, 5200, 72.0, 0.062, {"get_knowledge": 1, "form_config": 2, "create_page": 1}),
}

# 20 Trái cây (mobile) -----------------------------------------------------
GOOD["20-trai-cay-online"] = {
    "tree": [
        sec(h("h1", "Vườn Xanh – trái cây nhập khẩu"), t("<p>Miễn phí ship đơn từ 300.000đ · Đổi trả trong 24 giờ · Giao nhanh nội thành.</p>"), btn("Mua ngay", "/shop")),
        sec(h("h2", "Danh mục"), b("bst/product-categories", {"count": 6})),
        sec(h("h2", "Bán chạy"), b("bst/products", {"count": 8, "type": "best_selling"})),
        sec(row(col(4, icon_box("Đổi trả 24 giờ", "Lỗi là đổi.")), col(4, icon_box("Giao nhanh", "Nội thành.")), col(4, icon_box("Miễn phí ship", "Đơn từ 300.000đ.")))),
    ],
    "log": log_ok("site_info", "block_catalog", "build_sections", "create_page"),
    "metrics": metrics(4, 5, 49000, 4100, 58.3, 0.055, {"site_info": 1, "block_catalog": 1, "build_sections": 1, "create_page": 1}),
}
wide_svg = '<svg width="1200" height="200" viewBox="0 0 1200 200" role="img" aria-label="trang trí"><rect width="1200" height="200" fill="var(--bs-primary)"/></svg>'
BAD["20-trai-cay-online"] = {
    "tree": [
        sec(h("h1", "Vườn Xanh"), b("bst/code", {"code": wide_svg, "css": "selector svg{width:1200px;max-width:none;display:block}"})),
        sec(h("h2", "Ưu đãi"), b("bst/code", {"code": "<table style=\"width:900px\"><tr><td>Táo</td><td>Nho</td></tr></table>"}),
            b("bst/code", {"code": "<div style=\"width:800px\">Banner</div>"})),
        sec(b("bst/products", {"count": 8}), t("<p>Ship 0đ toàn quốc</p>")),
    ],
    "log": log_ok("create_page"),
    "metrics": metrics(1, 3, 25000, 4000, 30.0, 0.03, {"create_page": 1}),
}

def write(run, data, meta):
    d = os.path.join(HERE, run)
    os.makedirs(d, exist_ok=True)
    with open(os.path.join(d, "run.json"), "w", encoding="utf-8") as f:
        json.dump(meta, f, ensure_ascii=False, indent=2); f.write("\n")
    for cid, r in data.items():
        r = dict({"case": cid, "status": "done"}, **r)
        with open(os.path.join(d, cid + ".json"), "w", encoding="utf-8") as f:
            json.dump(r, f, ensure_ascii=False, indent=1); f.write("\n")

write("good", GOOD, {"run": "fixture-good", "mode": "fixture", "model": "hand-made (good)", "note": "Hand-written results that follow the block rule; not model output."})
write("bad", BAD, {"run": "fixture-bad", "mode": "fixture", "model": "hand-made (bad)", "note": "Hand-written results with typical mistakes; not model output."})
print("ok")
