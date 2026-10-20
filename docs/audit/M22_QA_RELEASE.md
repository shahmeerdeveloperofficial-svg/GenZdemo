# M22 — QA, release readiness and operations

## Final verification snapshot

- `php artisan test --no-coverage`: 36 tests / 179 assertions passed after the live admin dashboard work. The PHPUnit XML schema emits an upstream deprecation warning; tests themselves are green.
- `php artisan view:cache`: passed.
- Routes verified: analytics, certificate issuance/revocation/list/detail/public verification, notification preferences, assignment workflow, and the authenticated live dashboard metrics endpoint are registered.
- Scheduler lists both CRM task outbox and general in-app notification outbox commands on a one-minute cadence.
- Local server on `http://127.0.0.1:8067`: homepage and course catalog returned HTTP 200 during smoke tests.
- Isolated demo server on `http://127.0.0.1:8068`: public homepage, catalog, sample course detail, contact page, sitemap, admin dashboard and live metrics endpoint returned HTTP 200 after local demo-admin sign-in. The dashboard returned database counts (8 students, 10 courses, 26 enrollments, 11 leads, 4 admissions, 3 free appointments and 3 pending consultation requests); its charts use enrollment, lead, admission and course-enrollment records and auto-refresh every 60 seconds.
- Demo admin, instructor and student logins reached their respective dashboards/course pages with HTTP 200. Demo login: `demo.admin@genzconsultants.example`, `demo.instructor@genzconsultants.example`, `demo.learner.amina@genzconsultants.example`; local-only password `GenZLocal!2026`.
- PHP syntax checks passed for demo seeder, analytics, admin order and student checkout controllers.
- `composer audit` could not run because Composer is not available on PATH in this environment; no dependency vulnerability claim is made.

## Database and rollout safety

- A separate MySQL database `genz_crm_lms_demo` received all 100 migrations plus repeatable synthetic seed data. The populated `.env` database `studylifter_local` was not migrated or seeded; the demo seeder now hard-refuses to run against any other database. No destructive database/Git command was run.
- Feature tests use isolated in-memory SQLite. The migrations were also executed successfully on the separate MySQL demo database, but this does not certify or alter the populated configured database.
- Before staging/production: take and verify a restorable database backup, review migration status against the intended target, run migrations on a staging clone, configure scheduler/queue workers, validate private storage, and exercise payment gateway sandbox callbacks and refund reconciliation.
- Keep `GENZ_DEMO_SEEDING` off outside local/demo and never use the demo seeder on production.

## Open launch risks and incomplete acceptance

- Browser responsive/accessibility testing and screenshots were not available in this shell workflow. A broad E2E suite and MySQL staging run remain required.
- No application migration was applied to the live local server, so newly added database-backed modules require their additive migrations before use. Controllers now fail clearly for several uninstalled modules, but this is not a substitute for staging rollout.
- External email/SMS/WhatsApp integrations, localized template editor, anti-virus scanning, actual video storage, Zoom/Meet APIs, PDF certificate templates, refund ledger/tax policy and affiliate payout reconciliation remain incomplete or require credentials/business policy.
- Composer dependency audit unavailable. Run it from an environment with Composer before release.
- Report limitations and per-module backlog are documented in the M16–M21 audit files.

## Definition of done

The repository is substantially implemented and test-green for the delivered slices, but this is **not production release approval** until the pending migrations and staging/security/browser checks above are completed.
