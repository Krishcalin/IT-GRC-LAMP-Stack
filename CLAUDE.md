# IT-GRC Portal — LAMP / Laravel Edition (build notes)

## What this is
A faithful port of the original IT-GRC portal (FastAPI + React + PostgreSQL,
`github.com/Krishcalin/IT-GRC`) to an idiomatic **Laravel 12 / LAMP** monolith so LAMP/Laravel
shops can adopt it. Same data model, frameworks, control catalogs and features; re-expressed with
server-rendered Blade views.

## Stack & deliberate choices
- **Laravel 12 + PHP 8.2 + MySQL 8 + Apache 2.4** (classic LAMP, MVC monolith).
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

## Architecture (request flow)
Browser (Blade HTML + Tailwind/Alpine/Chart.js via CDN) → **Apache** (`mod_rewrite`, docroot
`public/`) → `public/index.php` → **Laravel**: `routes/web.php` → session auth + `EnsurePermission`
(RBAC) → 17 module controllers → `App\Support` helpers → **Eloquent** (24 UUID models) → **MySQL**
(+ `storage/app/evidence` for uploads). Seeded by `migrate --seed` from
`database/seeders/data/*.json`. Diagram: [`docs/architecture.svg`](docs/architecture.svg); full
install/hosting guide in the README.

## Module map (controller · models · tables · special actions)
| Module | Controller | Model(s) | Table(s) | Beyond CRUD |
|---|---|---|---|---|
| Controls | `ControlController` | Control, ControlMapping | controls, control_mappings | crosswalk add/remove (both directions) |
| Risks | `RiskController` | Risk | risks, risk_controls | link/unlink controls; `recalculateLevels()` |
| ISMS Clauses | `ClauseController` | ClauseRequirement | clause_requirements | conformity update only |
| SoA | `SoaController` | SoaEntry | soa_entries | per-control upsert (`updateOrCreate`) |
| Documents | `DocumentController` | DocumentedInformation | documented_information | — |
| Policies | `PolicyController` | Policy, PolicyAcknowledgment | policies, policy_acknowledgments | acknowledge; approver/approved_at on Approve |
| Suppliers | `SupplierController` | Supplier | suppliers | — |
| Incidents | `IncidentController` | Incident | incidents | auto `resolved_at` |
| Assets | `AssetController` | Asset | assets, asset_risks | — |
| Interested Parties | `InterestedPartyController` | InterestedParty | interested_parties | param `interested_party` |
| Objectives | `ObjectiveController` | Objective | objectives | — |
| Metrics | `MetricController` | Metric, MetricMeasurement | metrics, metric_measurements | add measurement (updates current); `rag` accessor; trend chart |
| Tasks | `TaskController` | Task | tasks | `{id}/decision` (approval); `overdue` accessor |
| Assessments | `AssessmentController` | Assessment, AssessmentItem | assessments, assessment_items | `populate?framework=`; items CRUD; `score`/`avg_maturity` accessors |
| Audits | `AuditController` | Audit, AuditFinding | audits, audit_findings | findings CRUD; auto `closed_at` |
| Training | `TrainingController` | TrainingCampaign, TrainingRecord | training_campaigns, training_records | records CRUD; `completion_rate` accessor |
| Evidence | `EvidenceController` | Evidence | evidence | upload/download/delete (`evidence` disk) |
| Dashboard | `DashboardController` | — | — | `Posture::recordSnapshot()` on load |
| Analytics | `AnalyticsController` | PostureSnapshot | posture_snapshots | heatmap (inherent/residual), posture trend, my-work |
| Frameworks | `FrameworkController` | — | — | cross-framework coverage matrix |
| Reports | `ReportController` | — | — | CSV export: controls/risks/soa/findings/suppliers |
| Reminders | `ReminderController` | — | — | overdue/upcoming across registers |
| Auth | `Auth\LoginController` | User, Role | users, roles, role_user | session login/logout |

## Data model — tables
Framework: `users`, `roles`, `role_user`, `password_reset_tokens`, `sessions`, `cache`,
`cache_locks`, `jobs`, `job_batches`, `failed_jobs`.
Domain (19 migrations): `controls`, `control_mappings`, `clause_requirements`,
`documented_information`, `interested_parties`, `objectives`, `metrics`, `metric_measurements`,
`posture_snapshots`, `suppliers`, `risks`, `risk_controls`, `incidents`, `soa_entries`, `audits`,
`audit_findings`, `policies`, `policy_acknowledgments`, `assets`, `asset_risks`, `tasks`,
`assessments`, `assessment_items`, `evidence`, `training_campaigns`, `training_records`,
`activity_log`.

## Testing
- `tests/Unit/ScoringTest.php` — pure scoring helpers vs the original outputs.
- `tests/Feature/AuthTest.php` — login, inactive-user rejection, role seeding.
- `tests/Feature/SeederTest.php` — full `DatabaseSeeder` loads exact counts (148/96/30/17/…),
  framework spread (93/12/22/13/8), and is idempotent.
- `tests/Feature/SmokeTest.php` — acting-as admin, GETs **every** index page + key detail/show
  pages and asserts 200. Guard for view/component/route-wiring regressions (it's what would have
  caught the once-missing `<x-card>` component).
- Feature tests run on in-memory SQLite (`RefreshDatabase`); the MySQL path is validated by CI.
  When changing code, extend the smoke + seeder tests so CI keeps catching render/wiring breaks.

## Important constraints
- **No PHP/Composer/MySQL on the dev box** — CI (`.github/workflows/ci.yml`) is the authoritative
  validator: `php -l` lint, `php artisan test` (SQLite), `migrate --seed` + `migrate:fresh` against
  a MySQL 8 service. The finished build was additionally hardened with a **multi-agent adversarial
  review** (routing / Blade / Eloquent / schema / PHP / CI dimensions, each finding verified).
- **Toolchain:** Laravel 12 (11.x is blocked by Composer security advisories). CI uses
  `actions/checkout@v5` + `shivammathur/setup-php@v2` (Node 24).
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
