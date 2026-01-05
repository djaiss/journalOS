# Coding Guidelines (JournalOS Flavor)

This is the “how we build” guide for contributors. It’s based on repository config and the code you’ll actually touch. Follow it and you’ll blend in like a local.

## The big ideas

- **Actions are the unit of behavior.** One user intent per class in `app/Actions/`.
- **Presenters shape view data.** See `app/View/Presenters/`.
- **Controllers stay thin.** They orchestrate requests, run validation inline, and call Actions.
- **Queues are mostly `low`.** When dispatching jobs, use `->onQueue('low')` unless there’s a strong reason not to.
- **No heavy JS frameworks.** Blade + AlpineJS + Alpine Ajax + Tailwind v4 is the stack.

## PHP conventions (Laravel + this repo’s house rules)

- **PSR-12 baseline.** The repo enforces it via Pint and general code style.
- **4-space indentation in PHP.** (From `.editorconfig`.)
- **Always declare strict types.** The codebase expects `declare(strict_types=1);`.
- **Type hints everywhere.** Add parameter and return types. Prefer explicit types over PHPDoc.
- **Constructor promotion.** Use PHP 8 constructor property promotion; no empty `__construct()`.
- **Use curly braces** for control structures, even one-liners.
- **Prefer `final` / `readonly`** where appropriate. Presenters follow this pattern.
- **No Form Requests.** Use inline validation inside controllers instead.
- **Avoid new dependencies** unless explicitly approved.

## Validation & requests

- Controllers use inline validation (`$request->validate([...])`).
- Follow existing validation styles (array-based rules are common in this codebase).

## Actions

- Every user action is a dedicated class in `app/Actions/`.
- Keep Actions small, focused, and reusable.
- Many Actions dispatch background jobs; use the `low` queue unless you have a strong reason.

## Presenters

- Presenters in `app/View/Presenters/` build view-ready structures.
- They’re typically `final readonly` and return arrays/collections ready for Blade.
- Keep display logic out of Blade whenever possible.

## Jobs & queues

- Use queued jobs for expensive work (`app/Jobs/`).
- Default to `->onQueue('low')` for dispatches, consistent with the codebase.

## Eloquent & data

- Prefer Eloquent relationships and query builder patterns.
- Avoid direct `DB::` calls unless necessary.
- Use factories for test data (`database/factories/`).

## Frontend conventions

- **Blade** is the UI layer (`resources/views/`).
- **Tailwind CSS v4** only; avoid deprecated v3 utilities.
- **AlpineJS** handles interactivity. Keep it light and purposeful.

## Formatting & linting

**EditorConfig** (`.editorconfig`):
- PHP: 4 spaces.
- Blade / JS / CSS / JSON: 2 spaces.
- Markdown: do not trim trailing whitespace.

**Pint** (`pint.json`):
- Strict types, global namespace imports, `final` classes, strict comparisons, ordered class elements, and more.
- Run `vendor/bin/pint --dirty` before finalizing changes.

**Prettier** (`.prettierrc`):
- 2-space indentation.
- Single quotes.
- Trailing commas where possible.
- Blade parser enabled with `prettier-plugin-blade` and Tailwind class sorting.

**Static analysis**:
- `phpstan.neon` targets `app/`, `routes/`, and `database/` at level 5.
- `rector.php` enables Laravel-focused refactors and type improvements.

## Testing expectations

- Use PHPUnit.
- **Controllers tests** live in `tests/Feature/Controllers/`.
- Everything else goes in `tests/Unit/` following the same structure as the source.
- After tests pass, run `composer journalos:unit`.

## Where to look for examples

- Actions: `app/Actions/` (single responsibility, queues, helpers).
- Presenters: `app/View/Presenters/` (`final readonly`, view-friendly data).
- Controllers: `app/Http/Controllers/` (inline validation + actions).
- Jobs: `app/Jobs/` (low queue usage).

When in doubt: follow the existing patterns. This app is calm, consistent, and allergic to cleverness.
