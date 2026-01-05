# JournalOS Developer Docs

Welcome, fellow journal whisperer. This folder is the front door for contributors who want to ship features without shipping chaos.

Start here: [Coding guidelines](coding-guidelines.md) for how we like to build.

## Architecture in 60 seconds

JournalOS is a Laravel 12 monolith with three faces: web app, API, and marketing site. Core behavior lives in **Actions** (one user intent per class), view data is shaped by **Presenters**, and the UI is server-rendered with Blade. AlpineJS sprinkles interactivity; Tailwind handles styling. Background work rides the `low` queue.

## Languages & stack

- **PHP 8.4** with Laravel 12 for the application backbone.
- **Blade** templates for server-rendered views.
- **AlpineJS + Alpine Ajax** for light interactivity.
- **Tailwind CSS v4** for styling.

No heavyweight JS framework by design. The goal: fast pages, simple mental model, fewer moving parts.

## Why this architecture

- **Actions** keep business behavior small, testable, and easy to reuse.
- **Presenters** keep views clean by preparing data ahead of time.
- **Laravel-first** patterns mean onboarding is quick for PHP devs.
- **Monolith** keeps deployment, data, and performance predictable.

## Directory tour

- `app/Actions/` — single-responsibility classes that represent user actions.
- `app/View/Presenters/` — view-focused data preparation.
- `app/Http/Controllers/` — request handling and inline validation.
- `app/Models/` — Eloquent models and relationships.
- `app/Jobs/` — queued work (mostly `low` priority).
- `app/Mail/` — mailables.
- `app/Console/` — Artisan commands.
- `app/Helpers/` — shared helpers.
- `bootstrap/` — application bootstrapping and middleware registration.
- `config/` — configuration files.
- `database/` — migrations, factories, seeders.
- `public/` — front controller and public assets.
- `resources/` — Blade views, CSS, front-end assets.
- `routes/` — route files for web, API, auth, marketing, console.
- `tests/` — PHPUnit tests (controllers in `tests/Feature/Controllers`).
- `docs/` — you are here. Keep it tidy.

## Where to look first

- For behavior: `app/Actions/`
- For screens: `resources/views/`
- For data flow to views: `app/View/Presenters/`
- For routes: `routes/*.php`

Happy shipping.
