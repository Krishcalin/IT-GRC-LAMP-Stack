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

## Conventions for porting modules (Phase 3)
- One resourceful controller + route group per module; route names `controls.index`, `controls.show`, …
  The sidebar in `resources/views/layouts/app.blade.php` renders each item only if `Route::has()`,
  so adding routes auto-populates the nav.
- Eloquent models: `HasUuids`, `$fillable`, enum-ish strings kept as plain strings (validated in
  requests), accessors for derived fields. Ref IDs (`RISK-001`, `DOC-001`, …) generated in the
  controller/model on create.
- Pure derivations live in `App\Support\Scoring` (ported 1:1 from the FastAPI models, unit-tested):
  `riskLevel`, `computeRag`, `aggregateScore`, `taskIsOverdue`. Reuse these in model accessors.
- Blade: every module page `@extends('layouts.app')`, `@section('title')`, `@section('content')`.
  Index = filter bar + table; detail = card layout; create/edit = modal or form view.

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
