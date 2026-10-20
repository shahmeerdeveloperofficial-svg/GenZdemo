# M00 — Risk Register and First-Change Sequence

| ID | Severity | Evidence | Risk / response |
|---|---|---|---|
| R1 | Critical | `route:list` throws `Class "App\\Http\\Services\\Logger" not found` from `AddonUpdateController.php:23`; repo also has `app/Http/Controllers/Logger.php` | Route discovery and route middleware auditing are blocked. In an approved foundation fix, choose correct logger service/reference and add route-list smoke test. Do not quietly patch in M00. |
| R2 | High | No `.git/` at source root/parents; no `.gitignore` found | No repository history/rollback baseline and generated/local files may be unmanaged. Establish version control and ignore rules only after confirming project folder scope and preserving all user files; no Git mutation in M00. |
| R3 | High | Local `.env` has `APP_ENV=production`, debug off, MySQL `studylifter_local`, local URL port 8067 | Confusing local/deployed mode increases risk of running production-only behavior or seeding the wrong DB. M01/M21 must introduce explicit environment separation and a guarded separate demo DB; never seed current DB by assumption. |
| R4 | High | `VersionUpdateController::processUpdate` is a public `POST` route explicitly excluding `version.update`; it can trigger `Artisan::call('migrate', ['--force'=>true])` and later commands based on a remote response. | Review update endpoint authorization/CSRF/rate limiting, remote command trust, installer/updater lifecycle, and request signature before production use. Existing middleware protections do not replace review of this public updater surface. |
| R5 | High | `AddonUpdateController`/version updater invoke remote vendor endpoints and can install/migrate addons; multiple payment/social/meeting/storage integrations exist | External code/data trust and credential/configuration risks. Inventory active integrations, secrets, webhook verification, and update authenticity before enabling or deploying. |
| R6 | High | `UserSeeder` contains five predictable accounts using password `123456`; seeder calls are commented out but the local DB already has users | Never run seeders on the current database or production. Replace with environment-gated, idempotent synthetic fixtures and generated credentials in M21. |
| R7 | High | Legacy numeric `users.role` plus Spatie permission package; current DB has 11 permission rows but no roles and no user-role pivot rows | Authorization behavior may be inconsistent: role middleware permits user type, while permission-gated settings/staff routes may deny access without matching permission assignments. Audit every role/menu/route and protect object-level data in M02. |
| R8 | High | User schema stores `passport_number`, profile attachments and application-like information; file manager and upload paths exist | PII/private document exposure and retention risk. Require private storage, per-record authorization, safe file validation, access logging and retention policy before building document workflows. |
| R9 | Medium | Only five declared foreign keys across the inspected schema; most migrations use convention references | Orphaned records and concurrent workflow integrity risk. Add constraints/indexes only after auditing old data and planning a safe backfill. |
| R10 | Medium | 2 automated tests, one trivial; PHPUnit XML schema warning; Composer CLI/audit unavailable | Changes currently lack meaningful regression coverage and dependency advisory baseline. Add route/auth/workflow coverage early and run Composer audit in configured CI/developer environment. |
| R11 | Medium | 85 migrations all applied to local DB; core public/CRM/LMS catalogue tables empty | Environment is useful for runtime smoke checks but not a populated product demo. Build realistic relational samples only in the isolated, guarded M21 demo DB. |
| R12 | Medium | Repository assets are StudyLifter `logo.png` / `logo-black.png`; GenZ supplied brand mark not found | Do not present old branding as GenZ or fabricate a logo. Obtain/use original GenZ logo file in M01 and preserve old referenced files until reference migration is verified. |

## Recommended module-level change sequence

Follow the plan's M00–M22 acceptance criteria. Within the feature wave after shared foundation, interleave CRM and LMS work so the systems progress with equal importance:

1. M00 audit and approval gate.
2. M01 brand tokens/logo foundation; M02 identity/permissions; M03 shared admin shell; M04 public GenZ site.
3. CRM M05 leads → LMS M11 course foundation → CRM M06 student profile/documents → LMS M12 curriculum → CRM M07 destination discovery → LMS M13 enrolments/progress → CRM M08 admissions → LMS M14 live class → CRM M09 consultations → LMS M15 quizzes → CRM M10 tasks/follow-ups → LMS M16 assignments → LMS M17 certificates.
4. M18 finance → M19 notifications → M20 analytics.
5. M21 guarded demo database/data → M22 full QA.

The user wants to work tab-by-tab and may provide additional visual references. Treat each module as an approval/reviewable increment; do not delete existing modules just because the first screen is redesigned.

## First likely files in M01 (read before modifying)

1. New `config/genz.php` for brand identity, once exact logo and screenshot-derived tokens are agreed.
2. Existing Mix entry point (`webpack.mix.js`) and current Sass/CSS entrypoints under `resources/`/`public/assets/`; retain Mix and avoid new frameworks.
3. Shared public Blade layout/nav/footer (`resources/views/frontend/layouts/*`) and mapped admin/student/consultant shells where branding is shared.
4. Logo references and image settings; map every reference to `public/assets/images/logo.png`/`logo-black.png` before adding the supplied GenZ files.
5. Feature smoke tests for public landing and authenticated shell rendering.

Do not change these files until M00 is approved and their live references are reviewed. No implementation changes were made by this risk register.
