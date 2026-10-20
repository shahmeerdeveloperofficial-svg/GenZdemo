# Status addendum — 13 October 2026

This dated checkpoint supplements, but does not erase, the original M00 audit history and older M05–M22 checkpoint notes. Those documents record what was true at their own checkpoints; use this file for the latest verified status.

## Verification completed in this checkpoint

- Full isolated test suite after the backend navigation update: **43 passed, 275 assertions**, `php artisan test --compact --no-coverage` (74.16 seconds). PHPUnit XML deprecation warning is no longer present in the output.
- `php artisan route:list --path=admin --except-vendor`: passed; the admin route group reports **337 routes**, including student edit/update and CRM/LMS workflows.
- `php artisan view:cache`: passed.
- HTTP smoke check: `GET /login` on `127.0.0.1:8067` returned **200** (about 6.6 seconds). The PHP development server is still listening on 8067.
- A separate local demo server is running at `http://127.0.0.1:8068`, pointed only at `genz_crm_lms_demo` with mail set to log. Session login reached the real admin dashboard with HTTP 200, and the reordered menu labels were present.
- Read-only demo checks verified 13 users, 10 courses, 26 enrollments, 3 universities, 3 scholarships, 11 CRM leads, 3 events and 3 services. The active role-1 demo admin account and its local-only demo password were verified; other roles’ passwords were not re-checked.
- There are **100 migration files** in this checkout. The full migration chain has previously passed in the test suite’s disposable in-memory SQLite environment. This is not evidence that the configured MySQL database currently has all migrations applied.
- Recent student-management tests verify creation despite a welcome-email transport `TypeError`, edit-form loading, update validation, and preserving the existing password when the password field is blank.
- Sidebar now follows the recommended operational order: dashboard → CRM enquiry/consultations → student/documents → study-abroad catalogue → admissions → LMS → services/events → finance → analytics → team/content/system. The misleading consultation link formerly labelled “Leads” is correctly named “Consultation Requests”; no business module or database records were removed.
- `tests/Feature/SidebarFlowTest.php` verifies menu ordering and confirms that a non-admin staff account with `Manage Staffs` can see the staff directory but not the super-admin Roles & Permissions editor. The role link now aligns with the existing `superadmin` route middleware.
- `php artisan view:cache` passed after the navigation changes.

## Current database state and safe scope

- The first check found no listener on port 3306, but a later check found `mysqld.exe` running and port 3306 listening. Earlier local logs had shown MariaDB aborting while initializing replication metadata. No repair, file deletion, database reset, migration or seed was attempted.
- Read-only `migrate:status` against the configured application database reports **85 ran / 15 pending**. The configured target is production-marked `studylifter_local`; those 15 additive migrations were deliberately not applied.
- Read-only `migrate:status` against the isolated `genz_crm_lms_demo` database reports **100 ran / 0 pending**. Its synthetic demo counts and local admin login were then verified without running a seed or changing application records.
- The database service is reachable now, but the server on port 8067 uses the configured application target and its new CRM/LMS tables remain pending there. Live CRUD/DataTables for those new slices are not yet certified on 8067. Do not run migrations on `studylifter_local` as a shortcut.
- Existing M21/M22 text contains historical snapshots. Use the demo counts/admin login verified above; do not assume other role credentials or historical counts are current without checking them.

## Remediations represented by the current code/tests

- Student creation and editing now have regression coverage; email transport failure is isolated from the committed student record.
- Test setup uses an isolated SQLite DB with its own fixtures, and schema/permission seeding compatibility issues found by the suite were corrected.
- SQLite/MySQL-compatible name/date expressions and null-safe language layout handling are covered by the relevant feature tests.
- PHPUnit configuration warning and permission seeder’s composite uniqueness conflict were addressed.
- Route authorization remains server-side; hidden sidebar links are not the only permission control.

## Still required before calling the app fully healthy or production-ready

1. Confirm the XAMPP MariaDB instance/data directory stays stable and keep the demo service isolated from populated `studylifter_local`.
2. Complete representative CRUD/DataTables checks on the demo server; do not migrate the configured production-marked database as a shortcut.
3. Run a browser walkthrough for backend list sorting/actions, responsive layout, toast behavior and access-denied scenarios.
4. Run Composer’s dependency audit from an environment where Composer is available; test real payment/email/WhatsApp/meeting providers only with sandbox credentials.
5. Use a staging clone, backup/restore drill and reviewed additive migrations before any production rollout.

No destructive database or Git command was run during this checkpoint.
