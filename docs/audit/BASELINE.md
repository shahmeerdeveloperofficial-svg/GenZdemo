# M00 — Project Audit & Foundation Baseline

Audit date: 2026-10-13 (Asia/Karachi)  
Application root: `source-code/` under the CodeCanyon project folder  
Scope: inspection only. No application code, schema, or database records were changed for M00.

## Executive summary

This is a working Laravel 10.50 StudyLifter application with public study-abroad listings, consultation booking, a service-order/student operations area, and a small course-delivery module. It is not yet a unified GenZ CRM + LMS. The current schema contains useful foundations but almost all catalogue and operational tables in the local database are empty. The default page and a few public pages respond successfully on `http://127.0.0.1:8067/`.

The highest-priority technical issue is that `php artisan route:list` fails while resolving `App\\Http\\Controllers\\AddonUpdateController`: that controller imports `App\\Http\\Services\\Logger`, but that class is absent. There is a different `App\\Http\\Controllers\\Logger` file, which is not the namespace the controller requests. There is no Git repository at the inspected application root, so there is no local commit history or known rollback point. Baseline tests pass, but only two trivial tests exist.

## 1. Architecture and inventory

- Framework/runtime: Laravel 10.50.0, PHP 8.2.12, MySQL (`studylifter_local`), Spatie Laravel Permission 6.24.0.
- Server: already responds on port 8067. `/`, `/universities-list`, and `/consultations` return HTTP 200. Unauthenticated `/admin/dashboard` and `/student/dashboard` redirect to `/login` (expected auth boundary).
- Frontend build: Laravel Mix 6, Bootstrap 5.1, Sass, Axios/Lodash, Laravel DataTables package. No React/Vue SPA detected in the inspected manifests.
- Backend dependencies include Sanctum, Socialite, activity log, Google API client, Zoom, Pusher, several payment gateways, S3/Flysystem, and installer/updater packages. Presence in Composer does not prove each integration is configured or operational.
- Inventory: 85 migration files; 68 Eloquent model files; 84 controller PHP files; 260 Blade templates; 9 route files. The route definitions contain about 414 explicit route declarations (admin 276, student 41, frontend 31, affiliate 27, consultant 19, web 17, API 3; count is static because `route:list` is currently broken).
- There is no `app/Policies` directory. Access is primarily divided by custom role middleware plus permission checks on selected admin routes.
- App lives in `source-code/`; the plan is at the parent CodeCanyon project root. `.env.example`, `.gitignore`, and `.git/` were not present at the inspected source root.

## 2. Existing functionality suitable for reuse

- Public content and catalogue: home/CMS pages, countries, universities, study levels, subjects/programs, scholarships, events, FAQs, testimonials, services, and blog categories/tags/posts.
- Consultancy: consultant users and availability slots; public consultation discovery/booking; free-consultation requests; appointments, meeting provider configuration, feedback/reviews.
- Student service operations: onboarding/basic and academic profile details, work experience, language proficiency, extracurricular activities, service orders, invoices, assignments, task boards, notes, attachments, and conversations.
- Existing LMS foundation: courses, lessons, lectures, course resources, enrolments, resource/lecture completion tracking, subjects/categories, and checkout/payment transactions.
- Shared platform: users, email templates, notifications, currencies, languages, settings, file manager, gateway/payment records, affiliate tracking/payout structures, profile screens, and common role-specific dashboards.
- Authentication supports conventional Laravel web auth, optional Google/Facebook login, Sanctum tokens, email verification routes, and optional Google 2FA. Keep the established user identities and role field rather than replacing authentication.

## 3. Current gaps against the GenZ CRM + LMS objective

- The existing student service-order workflow is not a full admissions CRM. There is no clear lead intake/pipeline and conversion history, application-per-institution/intake tracking, structured offer/visa/arrival milestones, or comprehensive student-document lifecycle/expiry workflow.
- Catalogue records exist but the local DB has zero countries, universities, courses, programs, services, or blogs. Consequently, catalogue browsing and the LMS cannot currently demonstrate realistic end-to-end journeys from this DB.
- Consultation booking is present but requires a polished end-to-end check for timezone handling, availability conflicts, confirmation/reminders, cancellation/reschedule, and payment/refund behavior.
- LMS has content hierarchy and enrolment/progress primitives, but no evident quiz/assessment, grading, assignment submission/review, live-class attendance, certificate issuance, or complete instructor/student learning workflow.
- Cross-cutting production features to assess/build include consistent permissions and tenant/team ownership, reliable audit trail, notification delivery, reporting, validated uploads/private document storage, accessibility/mobile behavior, automated tests, and safe repeatable demo fixtures.
- Current bundled brand assets are `public/assets/images/logo.png` and `logo-black.png`, both StudyLifter wordmarks. They are not the GenZ logo shown in the supplied reference. The GenZ original logo asset is not present in the checked application tree.

## 4. Database and local data baseline

- The `.env` points at a MySQL database named `studylifter_local` on `127.0.0.1:3306`; `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=http://127.0.0.1:8067`. This is a local database with production-mode application flags, not proof of a production deployment.
- All 85 migration entries report `Ran`. Do not run `migrate:fresh`, `db:wipe`, or re-seed the current DB.
- There are 5 users, 13 settings, 6 currencies, 15 email templates, and 11 permission records. The inspected local DB contains no role rows; five users have numeric role values 1–5. The standard catalogue and workflow tables checked (countries, universities, programs, subjects, courses, services, blogs, consultation requests, appointments) have zero records.
- The database contains 79 application/framework tables; schema inspection found only five declared foreign-key constraints, mostly in Spatie pivot tables and users. All three Spatie role/permission assignment pivots were empty in this local snapshot. Most application relationships rely on Eloquent conventions rather than database-enforced referential integrity.
- `DatabaseSeeder` currently comments out all calls. Individual seeders include default staff/student demo accounts with the shared weak password `123456`; these must not be treated as production credentials or run casually against the current DB.
- No demo database or additional sample data was created in M00. Proposed later approach: use a separately named local-only database (for example `genz_demo`), explicit environment guard, idempotent synthetic records, and a no-production safety assertion before Module M21.

## 5. Technical baseline and results

| Check | Result |
|---|---|
| `php artisan about` | Pass; Laravel 10.50.0, PHP 8.2.12; `APP_ENV=production`, debug off, MySQL, URL port 8067. |
| Public HTTP smoke checks | `/`, `/universities-list`, `/consultations`: HTTP 200. |
| Auth boundary smoke checks | `/admin/dashboard`, `/student/dashboard`: redirect to `/login` when unauthenticated. |
| `php artisan migrate:status` | Pass; all 85 migrations marked `Ran`. |
| `php artisan route:list --except-vendor` | Fail; missing `App\\Http\\Services\\Logger` from `AddonUpdateController.php:23`. |
| `php artisan test --no-coverage` | Pass: 2 tests / 2 assertions. One test only asserts `true`; the feature test checks `/` returns 200. PHPUnit config emits a deprecated-schema warning. |
| `composer audit` | Not run: Composer CLI is unavailable in this environment. `composer.lock` exists; run audit after Composer is installed/available. |
| Git status/history | Not available: no `.git` directory at the inspected source root or its parents. No destructive Git operation was run. |

## 6. Proposed implementation order

Keep the master plan’s module boundaries and acceptance criteria, but validate every step against these prerequisites. Both CRM and LMS remain first-class; after shared foundation/public shell, alternate CRM and LMS work so one product does not become a second-phase afterthought.

1. M00 audit (this report; approval gate before code work).
2. M01 brand/design foundation; establish GenZ logo file/source and tokens from the supplied navy/cyan/ice-blue direction.
3. M02 identity, roles, permissions, and safe authorization tests.
4. M03 shared admin shell/navigation; M04 public GenZ website shell/content.
5. CRM workflow foundation: M05 lead intake/pipeline → LMS catalogue foundation M11 → M06 student profile/documents → M12 curriculum/course authoring → M07 destinations/university discovery → M13 enrolment/progress → M08 admissions/application workflow → M14 live classes → M09 consultation booking → M15 quizzes → M10 CRM tasks/follow-ups → M16 assignments → M17 certificates.
6. Shared business operations: M18 finance, M19 notifications, M20 analytics/reporting.
7. M21 isolated demo DB/relational seed data; M22 end-to-end QA, responsive/accessibility/security checks, and production-readiness review.

No M01 or later implementation has started. Await user review/approval of this M00 report.

## 7. Recommended first files for M01 (not changed in M00)

Use the plan’s exact paths after checking current contents and preserving user changes:

- `config/genz.php` (new brand configuration)
- existing global theme stylesheet under `public/assets/` or the existing Mix entry point (`webpack.mix.js`, `resources/sass/`); prefer the existing pipeline
- shared Blade navigation/layout logo references under `resources/views/frontend/layouts/` and the admin/student/consultant layout templates
- `public/assets/images/` only for the supplied original GenZ logo asset; do not replace the StudyLifter originals until their references are mapped
- a narrow public/admin layout smoke test under `tests/Feature/`

The exact filenames should be finalized after M01 checks all shared layouts, stylesheets, logo references, Mix outputs, and current local edits.

## 8. Verification command baseline / safe next checks

Run from `source-code/`:

```powershell
& 'C:\xampp\php\php.exe' artisan about
& 'C:\xampp\php\php.exe' artisan route:list --except-vendor
& 'C:\xampp\php\php.exe' artisan migrate:status
& 'C:\xampp\php\php.exe' artisan test --no-coverage
composer audit --no-interaction
```

After resolving the route discovery issue in an approved implementation module, capture the route list and add real authorization/feature coverage. Use a separate, explicitly local demo DB later; never point demo seeders at an existing/production database.
