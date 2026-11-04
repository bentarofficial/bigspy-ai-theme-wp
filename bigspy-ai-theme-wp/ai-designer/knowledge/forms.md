# Built-in Form: fields and price rules

The Form block (`bst/form`) with `provider: "bigspy"` is the theme's own form: fields, mail, saved entries and an optional live price estimate. CF7 and WPForms forms keep their fields in those plugins and have no price rules.

## How to work

1. Place the form with page tools: a `bst/form` block with `provider: "bigspy"`, `title`, look attributes. Page tools never set `fields` or `pricing`; they keep what is saved.
2. `form_config` mode `read` (page id): the forms on the page, their `form_key`, fields and price rules.
3. Agree on the facts with the user: what the visitor enters, each price, its unit and when it applies, minimum order, rounding, currency. **Missing price or unit → ask.** Never invent prices, taxes, fees, discounts, delivery areas or cancellation policies. Sample prices only when the user asks for a demo: `price_source: "demo"` marks every line and the note as demo.
4. `form_config` mode `check` with the whole new `fields` and `pricing` and 2–4 `samples` (realistic visitor inputs). Show the user the `rules` table, the `examples` (before → after) and `changes`; mention `warnings` (missing option prices, lost mail tags). Nothing is saved.
5. After the user agrees: mode `save` with the same config, the same `price_source` and the `check` token. On a published page the server shows an approval card; the price on the live page only changes after a human approves. Save never publishes a page. Task undo restores the page.

Prices are data in the block, never JavaScript: do not put formulas, scripts or price tables into `bst/code`, `bst/text` or Custom CSS.

## Fields

`{ id, type, name, label, placeholder, required, width: half|full, options, default, when }`

- `type`: text, email, tel, url, number, date, textarea, select, radio, checkbox, acceptance, file.
- `name` is the mail tag `[name]` (a-z, 0-9, -). Keep the names of existing fields: mail templates use them.
- `id` never changes; price lines and conditions point to ids. A field sent without `id` keeps the id of the existing field with the same name. New fields get `f_<name>`.
- `options`: one choice per line (select, radio, checkbox). A checkbox with one option is an on/off extra.
- Quantities (kg, m², hours, people, nights) are `number` fields.
- `when` shows the field only if its rules match: `{ logic: all|any, rules: [ { field: <id>, op: filled|empty|is|not|gt|lt, value } ] }`. Hidden fields are not required and send nothing. No loops, no self reference.

## Price rules

`pricing: { enabled, currency (VND, USD…), decimals, minTotal, roundStep, note, lines: [...] }`. Lines run in order; each line `{ id, label, kind, when? }` plus:

| kind | keys | amount |
| --- | --- | --- |
| fixed | amount | a fixed fee (negative = discount) |
| qty | field, unit | number field × unit price |
| area | field, field2, unit, minQty? | width × height × unit price, at least minQty |
| option | field, prices { choice: price }, qtyField? | price of each chosen option (× qtyField) |
| tier | field, tiers [ {from, unit} ], mode all|step | all: one rate for the whole quantity; step: each band at its own rate |
| percent | pct, targets [line ids above] | % of the targets (all lines above when empty); negative = discount |
| formula | expr | numbers, {fieldId}, {lineId above}, + - * / ( ), > < >= <= == !=, min max round ceil floor if(c,a,b) |

`when` on a line applies it only when the condition matches. `minTotal` is the minimum charged, `roundStep` rounds the total (1000 = to the nearest 1,000 ₫). The server recomputes every quote from the saved block; the visitor never sets the price.

Mail tags: `[price-total]`, `[price-lines]`, `[price-status]` (Estimate / To be confirmed / Not priced). They also appear in saved entries.

## Example: laundry

"Washing per kg, pickup/delivery fee, express +20% of washing" (prices from the user):

- fields: name, phone, `kg` (number, required), `pickup` (checkbox "Pickup and delivery"), `express` (checkbox "Express"), `address` (when pickup is filled).
- lines: `l_wash` qty field f_kg unit 18000; `l_ship` fixed 25000 when f_pickup filled; `l_fast` percent 20 targets [l_wash] when f_express filled.
- samples: 3 kg without extras; 3 kg + pickup + express (18,000×3 = 54,000; +25,000; +10,800 = 89,800 ₫).

## Patterns by trade (ask the real numbers)

- Cleaning, painting, flooring: option (package per m²) with qtyField m², minTotal.
- Curtains, glass, signs: area width × height × unit, minQty.
- Printing, uniforms, wholesale: tier by quantity.
- Repairs: fixed visit fee + option per device + formula for materials.
- Rooms, rentals: qty nights/days × rate, option for room/car type, percent surcharges.
- Events, catering: qty guests × option menu, percent service fee; VAT only when the user gives the rate.
- Transport by distance needs a real route distance; do not guess km.
