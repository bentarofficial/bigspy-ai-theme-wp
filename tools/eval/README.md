# AI Designer eval (G-70)

Internal tool. Measures AI Designer output on 20 industry briefs so prompt, knowledge or model changes can be compared with numbers.

| Command | What it does | Cost |
| --- | --- | --- |
| `node tools/eval/eval.cjs selftest` | Scores the hand-made fixtures; good ones must pass everything, bad ones must hard-fail | free |
| `node tools/eval/eval.cjs score <runDir>` | Replay: scores saved results, writes `report.json` + `report.md` | free |
| `node tools/eval/eval.cjs compare <runA> <runB>` | Table of scores, refusals, tool calls, tokens, time, cost (`--out=x.md` also writes `x.json`) | free |
| `node tools/eval/eval.cjs live ...` | Runs the briefs on a real site with the paid model, then scores | **paid** |
| `node tools/eval/eval.cjs list` | Prints the cases | free |

Options: `--cases=01,06` (id prefixes), `--no-browser` (skip the 375px check).

## Files

- `cases/*.json`: brief (`prompt`), facts the brief gives (`prices`, `numbers`, `reviews`, `demo_prices_ok`), expectations (`blocks_all`, `blocks_any`, `blocks_forbid`, `sections_min/max`, `must_contain`, `form` with field types, pricing on/off and `quotes`), and `limits` (tool calls, refusals, seconds, cost). Quote fields are matched by a regex on the field label/name and options by a regex on the option text, because the AI picks the names.
- `score.php`: scorer, plain PHP without WordPress. It loads the real server rules by copying functions from `ai-designer/includes/tools-design.php` (`bst_aid_editable_percent`, `bst_aid_block_rule_error`, `bst_aid_text_has_layout`, `bst_aid_core_blocks`) and requires `inc/form-schema.php` and `inc/form-pricing.php` unchanged.
- `lib/wp.php`: WordPress stubs, block parser/serializer, function loader. `lib/render.php`: renders blocks through the real `blocks/*/render.php`.
- `eval.cjs`: runner, report, compare, Playwright 375px check.
- `fixtures/good`, `fixtures/bad`: hand-made results (not model output), regenerate with `python tools/eval/fixtures/make-fixtures.py`.
- Live runs: put them in `tools/eval/runs/<name>/` and commit the result JSON files; later runs replay them for free.

## Checks

Hard (a case with one of these failed is not usable): page saved, only known blocks, `editable_percent` ≥ 90, block rule (`bst/code` caps), no invented facts (money, %, statistics, ratings, phone numbers, testimonials, form prices not in the brief; demo prices only when the brief asks and every line is marked demo), Form valid (provider `bigspy`, field types, conditions, line field ids, formulas), no element wider than 375px.

Soft: no `core/html`, no layout HTML in `bst/text`, exactly one H1, required/section/forbidden blocks, section count, facts from the brief appear, form field types, pricing on/off, quotes equal the expected totals, server refusals (`tool-error` log events), tool calls, time, cost.

Score = weighted share of passed checks (0–100). Tokens, time and cost come from the task metrics the AI Designer already records (`_bst_aid_metrics`); no runtime code was changed.

## Limits

- Replay renders without WordPress: no Customizer CSS, no `render_block` filters (block design width/spacing), no database, so posts/products blocks render empty and icons may be missing. Unknown helper functions return `''`. Live runs use the site's `content.rendered` instead, still without the theme header/footer and Customizer CSS.
- Fact detection is pattern based (Vietnamese money units, `%`, `N+`, `N năm/khách/...`, `x/5`, phone numbers). Plain numbers without a unit are not checked.
- WordPress Playground cannot load in the cloud sandbox (fetch failed), so the harness was tested with fixtures, the isolated renderer and a mock REST server.

## Live run

Needs owner budget approval. A staging WordPress with this theme, AI Designer connected (API key), WooCommerce for cases 05 and 20, and an application password for an administrator:

```
node tools/eval/eval.cjs live --site=https://staging.example.com --user=admin \
  --app-password="xxxx xxxx xxxx xxxx" --model=<model id> --effort=medium \
  --out=tools/eval/runs/2026-11-<model> --max-cost=5 --yes [--cases=01,02,06]
```

Each case gets a new task: the brief is sent, steps run until the model stops, approvals are approved automatically, and a follow-up ("Đồng ý … làm luôn") is sent when no page was saved or the model asked a question (`--turns`, default 4). A case stops at 1.5× its `cost_max`; the run stops at `--max-cost`. Created pages are undone (moved to trash) unless `--keep`.

Cost estimate per case: about 15 model calls × ~25k input tokens ≈ 0.4M input + ~0.02M output tokens. 20 cases ≈ 8M input + 0.4M output: about 3 USD at 0.3/1.2 USD per million tokens, 10 USD at 1/4, 30 USD at 3/15, less with prompt caching. Start with a 3-case pilot (`--cases=01,02,06 --max-cost=2`) to measure real numbers before the full run.
