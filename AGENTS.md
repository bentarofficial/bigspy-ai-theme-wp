# Shared coding-agent instructions

Applies to Codex, Claude Code, Gemini, and any other coding agent working in this repository.

Read the development/release policy at the top of [docs/roadmap-vi.md](docs/roadmap-vi.md) before changing the project. It is the canonical owner-approved policy and supersedes older repository instructions that imply releasing after every change.

- **Develop continuously; release in batches.** An owner request to “update”, “fix”, “add”, or “continue” authorizes the requested development and appropriate verification, not a product release.
- Complete the agreed scope, run focused checks and one appropriate regression pass, and save/commit the work. Reuse valid results; do not rebuild environments or run the whole product after each small edit without a concrete reason.
- Add a short internal note to [docs/PENDING-RELEASE.md](docs/PENDING-RELEASE.md): change, verification, and what release-facing documentation will need updating later.
- Defer About/product-guide maintenance, What is new, README Updates, changelog and release notes until an approved release. Exceptions: the owner specifically requested that content, or it is necessary for correct runtime behavior.
- At the release boundary, if no explicit authorization exists, ask once whether the owner wants the pending changes packaged and released now. Do not bump the release version, build a distribution ZIP, tag, publish/upload, deploy, or delete old releases before the applicable approval.
- If the owner declines or has not answered, retain the changes and keep accumulating work. Do not keep asking or publish small patches automatically. Internal workflow/documentation-only changes do not need a product-release proposal.
- When release is explicitly approved, consolidate the pending changes, update release-facing documentation together, run remaining necessary gates, and package/publish one new release. Old release/tag deletion and website deployment require the owner's applicable explicit authorization. A Git push is not release/deployment authorization.

Keep the canonical policy in the roadmap; `CLAUDE.md` and `GEMINI.md` are pointers to these shared instructions. Do not maintain conflicting release rules per agent.
