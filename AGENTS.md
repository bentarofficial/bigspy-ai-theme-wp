# Shared coding-agent instructions

Applies to Codex, Claude Code, Gemini, and any other coding agent in this repository. `CLAUDE.md` and `GEMINI.md` only point here.

## Read order

1. This file.
2. [docs/CODEMAP.md](docs/CODEMAP.md) to find the files for the task.
3. [docs/DEV-RULES.md](docs/DEV-RULES.md) only for the section you need: release steps, test matrix, or script notes.

## Always

- **Develop continuously; release in batches.** "Update", "fix", "add" or "continue" authorizes development and checks, not a release.
- Finish the agreed scope, run focused checks and one regression pass scaled to the impact.
- Commit only your scoped changes, push to the designated working branch, and verify the commit on the remote. Report branch and hash.
- If the push is blocked, keep the local commit and report branch, commit, blocker and next step. Never claim it is on GitHub without verifying.
- No force-push, no overwriting others' work, no merging a protected branch without authorization.
- Product changes get one short note in [docs/PENDING-RELEASE.md](docs/PENDING-RELEASE.md). Internal docs and tooling do not.

## Never without owner approval

Bump the version, build a distribution ZIP, tag, create or upload a Release, deploy, or delete old releases. A Git push is not release authorization. Defer About, What is new, README Updates, changelog and release notes until an approved release, unless the owner asked for that content or runtime depends on it.

At the release boundary, ask once whether to package the pending changes. If the answer is no or missing, keep accumulating and do not ask again.

## Context limits

- One task per session. Past 50% of context, hand off and open a new session.
- Search by file or function, then read only that range.
- **Never read these whole; use grep or a line range:** `languages/vi.po`, `languages/*.pot`, `languages/*.l10n.php`, `languages/*.mo`, `assets/css/blocks/segments.json`, `assets/css/blocks.css`, `assets/js/blocks-editor.js`.
- Do not split `blocks-editor.js` in one pass; extract only the part being edited.
- No unrelated refactors, dependency upgrades, bulk translation or asset regeneration, full audits, or long reports without a concrete need.
- No extra agents, worktrees or PRs unless the task requires them.
- Diagnose failures before retrying. Keep tool output and handoffs short.
- Efficiency never excuses skipping requested work, required CI or meaningful checks.

## Reusable tools

| Task | Command |
| --- | --- |
| Scaffold a block | `python tools/add-block.py <slug> "<Title>" --prefix bs-x` |
| Add translations | `python tools/add-strings.py strings.json` |
| Check release readiness | `python tools/release.py` |
| Test page in Playground | `node tools/playground-page.cjs` |

Use the scripts instead of rebuilding them in a scratchpad. See [docs/CODEMAP.md](docs/CODEMAP.md) for details.

Historical roadmap milestones are not the installed version; verify versions from source. Keep policy in `docs/DEV-RULES.md` and do not maintain conflicting rules per agent.
