<p align="center">
  <img src="banner.svg" alt="IT-GRC Portal — LAMP / Laravel Edition" width="100%">
</p>

# IT-GRC Portal — LAMP / Laravel Edition

[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?logo=mysql&logoColor=white)](https://mysql.com)
[![Apache](https://img.shields.io/badge/Apache-2.4-D22128?logo=apache&logoColor=white)](https://httpd.apache.org)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![CI](https://github.com/Krishcalin/IT-GRC-LAMP-Stack/actions/workflows/ci.yml/badge.svg)](https://github.com/Krishcalin/IT-GRC-LAMP-Stack/actions/workflows/ci.yml)

A **comprehensive open-source IT Governance, Risk & Compliance (GRC) portal** for managing an
organization's **ISO/IEC 27001:2022** ISMS — rebuilt on the **LAMP stack with Laravel** so that
organizations standardized on **Linux · Apache · MySQL · PHP/Laravel** can adopt it natively.

This is a faithful re-implementation of the original
[IT-GRC portal](https://github.com/Krishcalin/IT-GRC) (FastAPI + React + PostgreSQL), preserving
the same data model, frameworks, control catalogs and features — re-expressed as an idiomatic
Laravel monolith with **server-rendered Blade views** (no separate frontend build step).

---

## Why a LAMP edition?

The original portal is a decoupled Python API + React SPA on PostgreSQL. Many enterprises run a
**LAMP** standard and a Laravel application platform. This edition delivers the identical GRC
capability as a single Laravel app that drops into that environment:

| | Original | LAMP Edition (this repo) |
|---|----------|--------------------------|
| Backend | Python 3.12 + FastAPI | **PHP 8.2 + Laravel 12** |
| Frontend | React 18 + TypeScript (SPA) | **Blade + Tailwind (server-rendered)** |
| Database | PostgreSQL 16 | **MySQL 8** |
| Web server | Uvicorn | **Apache 2.4** |
| Migrations | Alembic | **Laravel migrations** |
| Build step | Vite/npm | **none** (Tailwind/Alpine/Chart.js via CDN) |

---

## Frameworks & coverage (target parity with the original)

| Framework | Entries |
|-----------|---------|
| ISO/IEC 27001:2022 Annex A | 93 controls |
| ISO/IEC 27019:2024 (energy/OT) | 12 "ENR" controls |
| NIST CSF 2.0 | 22 categories |
| SOC 2 (Trust Services Criteria) | 13 criteria |
| ISA/IEC 62443-2-1:2024 (OT/IACS) | 8 Security Program Elements |
| **Total** | **148 controls** + cross-framework crosswalk |

Plus: ISMS Clauses 4–10 (30 requirements), Statement of Applicability, Risk Register (5×5),
Documented Information (Clause 7.5), Interested Parties (4.2), Objectives & KPI/KRI/KCI metrics
(6.2/9.1), Suppliers (5.19–5.23), Incidents (5.24–5.28), Awareness & Training (7.2/7.3), Audits &
Findings, Policies, Assets, Evidence, Workflow Tasks & Approvals, Assessments (CSA / maturity /
vendor questionnaires), Analytics (risk heatmap, posture trend, framework coverage) and RBAC.

---

## Build status — feature complete ✅

The port was delivered in five runnable, CI-validated milestones — all complete:

- [x] **Phase 1 — Platform shell**: Laravel 12 skeleton, MySQL config, Docker (LAMP), CI, session
      auth, 6-role RBAC, base layout + sidebar, helper scoring logic (+ unit tests).
- [x] **Phase 2 — Data layer**: 19 migrations, 24 Eloquent models, and seeders carrying the exact
      **148 controls, 96 crosswalk mappings, 30 ISMS clauses, 17 mandatory documents** + sample data.
- [x] **Phase 3 — Modules**: controllers, routes and Blade views for **all 17 GRC registers**.
- [x] **Phase 4 — Analytics**: posture scores + daily snapshots, risk heatmap, posture trend,
      framework-coverage matrix, CSV reports, cross-register reminders.
- [x] **Phase 5 — Docs & polish**, incl. a multi-agent adversarial code review and a full-page
      smoke-test suite.

### Modules

Controls (with cross-framework crosswalk) · Risk Register (5×5 inherent/residual) · ISMS Clauses
(4–10) · Statement of Applicability · Documented Information (7.5) · Policies (Markdown +
acknowledgments) · Suppliers (5.19–5.23) · Incidents (5.24–5.28) · Assets · Interested Parties
(4.2) · IS Objectives (6.2) · Metrics KPI/KRI/KCI (9.1, with trend chart) · Workflow Tasks &
Approvals · Assessments (CSA / maturity / vendor questionnaire, populate-from-framework) · Audits &
Findings · Awareness & Training · Evidence (file uploads) · Dashboard · Analytics · Frameworks ·
Reports · Reminders.

The left sidebar auto-reveals each module via `Route::has()`.

---

## Quick start (Docker — recommended)

```bash
git clone https://github.com/Krishcalin/IT-GRC-LAMP-Stack.git
cd IT-GRC-LAMP-Stack
docker compose up --build -d
```

On first boot the app container waits for MySQL, runs `php artisan migrate --seed`, then serves via
Apache. Open **http://localhost:8000**.

Default login (change immediately):
- **Email:** `admin@company.com`
- **Password:** `Admin@123`

## Manual setup (existing LAMP host)

```bash
cp .env.example .env          # set DB_* to your MySQL instance
composer install
php artisan key:generate
php artisan migrate --seed
# point Apache's DocumentRoot at ./public (see docker/000-default.conf for a vhost example)
php artisan serve             # or use Apache/nginx for production
```

**Requirements:** PHP 8.2+ (`pdo_mysql`, `mbstring`, `bcmath`, `intl`, `zip`), MySQL 8 (or
MariaDB 10.6+), Composer 2.

---

## Testing & CI

```bash
php artisan test            # Unit + Feature on in-memory SQLite
```

Test suite (`tests/`):
- **Unit** — `ScoringTest`: the ported scoring helpers (risk level, RAG, assessment score,
  task-overdue) asserted against the original FastAPI outputs.
- **Feature** — `AuthTest` (login + RBAC), `SeederTest` (the full seed loads exact counts —
  148 controls / 96 mappings / 30 clauses / 17 docs — and is idempotent), and **`SmokeTest`**
  (logs in and renders **every** page + key detail pages, asserting HTTP 200 — catches view /
  Blade-component / route-wiring regressions).

GitHub Actions (`.github/workflows/ci.yml`) on every push/PR:
- **Lint** — `php -l` across `app/ config/ database/ routes/ tests/`
- **Test** — `php artisan test` (Unit + Feature on SQLite)
- **Migrate & seed** — against a real **MySQL 8** service, then `migrate:fresh` to verify a clean
  build. CI is the authoritative validator for the MySQL path.

> Built without a local PHP runtime, so correctness was hardened by a **multi-agent adversarial
> code review** across routing, Blade, Eloquent, schema, PHP and CI dimensions (each finding
> independently verified) plus the full-page smoke suite above.

---

## License

MIT — see [LICENSE](LICENSE). Control/clause descriptions are paraphrased for the application and
are **not** reproductions of the ISO/IEC, NIST, AICPA or ISA standards; consult the authoritative
standards for normative text. This tool assists with ISO 27001 management but does not guarantee
certification.
