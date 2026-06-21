# IT-GRC Portal — LAMP / Laravel Edition (build notes)

## What this is
A faithful port of the original IT-GRC portal (FastAPI + React + PostgreSQL,
`github.com/Krishcalin/IT-GRC`) to an idiomatic **Laravel 11 / LAMP** monolith so LAMP/Laravel
shops can adopt it. Same data model, frameworks, control catalogs and features; re-expressed with
server-rendered Blade views.

## Stack & deliberate choices
- **Laravel 11 + PHP 8.2 + MySQL 8 + Apache 2.4** (classic LAMP, MVC monolith).
- **Blade + Tailwind/Alpine/Chart.js via CDN** — *no Node/Vite build step* (keeps it pure LAMP and
  trivially adoptable; for production you may self-host or compile Tailwind).
- **UUID primary keys** everywhere (`HasUuids`), mirroring the source schema.
- **Auth:** session-based. The password column is **`hashed_password`** (not Laravel's default
  `password`) to match the source schema — `User::getAuthPassword()` is overridden accordingly.
- **RBAC:** `roles.permissions` JSON + `permission:<resource>:<action>` route middleware
  (`App\Http\Middleware\EnsurePermission`). Grammar: `*`, `controls:*`, `*:read`, `controls:read`,
  `controls:own`. Superusers bypass all checks. Six seeded roles (CISO, GRC_Manager, Risk_Owner,
  Control_Owner, Auditor, Viewer) match the source `DEFAULT_ROLES`.

## Status — complete
All 17 GRC modules + analytics are implemented (Phases 1–5). The app is feature-complete and
mirrors the original. A multi-agent adversarial review + a full-page smoke-test suite back it up.

## Conventions (how the code is organized)
- One controller + route group per module; resourceful route names (`controls.index`, `controls.show`,
  …). The sidebar in `resources/views/layouts/app.blade.php` renders each item only if `Route::has()`,
  so the nav reflects exactly what's wired.
- Eloquent models: `HasUuids`, `$fillable`, enum-ish strings kept as plain strings (validated in
  requests via `$rules`), accessors for derived fields (`Metric.rag`, `Task.overdue`,
  `Assessment.score/avg_maturity`, `TrainingCampaign.completion_rate`). Ref IDs (`RISK-001`,
  `DOC-001`, …) via `App\Support\Refs::next()`.
- **Support helpers** (`app/Support/`): `Scoring` (riskLevel / computeRag / aggregateScore /
  taskIsOverdue — ported 1:1, unit-tested), `Refs`, `Activity` (activity-log writes),
  `Posture` (headline ISMS scores + daily snapshot), `Analytics` (heatmap / framework coverage /
  my-work).
- **Blade components** (`resources/views/components/`, anonymous): `card`, `badge` (status/theme
  colour map), `field` (form input — list options are value=label; assoc options key=value via
  `array_is_list`), `owner-select`, `heatmap`. Every page `@extends('layouts.app')` with
  `@section('title')` + `@section('content')`. Index = filter bar + table; show = card layout;
  create/edit = `form.blade.php` (shared, keyed on `$item->exists`).
- Nested children (audit findings, assessment items, training records, control mappings, metric
  measurements) and special actions (task decision, policy acknowledge, assessment populate,
  evidence upload/download) are extra routes on the owning controller.

## Source-of-truth inventory
The complete model/route/page/seed inventory used to drive this port is the original repo's
`backend/app/models`, `backend/app/api`, `frontend/src/pages`, and `backend/app/seed/iso27001.py`.
Key counts: 93 Annex A + 12 ENR + 22 CSF + 13 SOC2 + 8 IEC 62443 = **148 controls**, 96 crosswalk
mappings, 30 ISMS clauses, 17 mandatory documents, 6 roles.

## Important constraints
- **No PHP/Composer/MySQL available on the dev box** — CI (`.github/workflows/ci.yml`) is the
  authoritative validator: `php -l` lint, `php artisan test` (SQLite), and `migrate --seed` +
  `migrate:fresh` against a MySQL 8 service.
- **IP:** all standard text (ISO/NIST/SOC 2/ISA) is **paraphrased**, never reproduced — same rule
  as the source repo.

## Commands
```bash
docker compose up --build -d        # full LAMP stack on :8000
php artisan migrate --seed          # provision + seed
php artisan test                    # unit + feature (SQLite)
php artisan make:migration ...      # schema changes
```

## Git workflow
Branch off `main` → commit (with `Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>`) →
`git fetch` → ff-merge to `main` → push to `origin` (`github.com/Krishcalin/IT-GRC-LAMP-Stack`).
Always `git fetch` before pushing.
```
