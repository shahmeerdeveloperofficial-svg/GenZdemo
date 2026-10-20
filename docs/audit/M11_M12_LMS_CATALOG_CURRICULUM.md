# M11–M12 — LMS catalog and curriculum

## M11 — Course domain

- Kept StudyLifter's existing LMS `courses` and `programs` tables and legacy enrollment compatibility; the separate GenZ `academic_programs` admissions/catalog domain remains separate.
- Added an additive migration for course publication state, difficulty, language, prerequisite course IDs and estimated study time. Existing active courses backfill to `published`; inactive courses backfill to `draft`.
- Free LMS courses now validate at price zero. Pricing remains an internal LMS field and all public price presentation remains disabled by `config('genz.public_prices')`.
- Admins can manage publication state; instructors' submissions are forced to review and are scoped to their own courses for listing/editing/deleting/enrollment management.
- Public course lists, detail pages, homepage cards and sitemap share the active + published visibility scope.

## M12 — Curriculum and learner access

- Added additive ordering/publication/preview metadata to legacy course lessons, lectures and resources, while preserving existing content as published. Added a revision-history table for lesson edits.
- Curriculum editing is now scoped through the owning course; nested lesson/lecture/resource IDs must belong to that course hierarchy. Instructors can edit their own courses but cannot publish their submissions.
- New local curriculum uploads are stored on the private disk and delivered through authenticated, enrollment-scoped routes. Existing legacy files are not moved or deleted; their original storage/public URLs may remain accessible and should be migrated under an explicitly planned maintenance window.
- Student course pages require an active enrollment, ignore arbitrary resource IDs outside that course, and only load published lesson → lecture → resource hierarchies. GET no longer records progress; an explicit POST records completion idempotently.
- Removed admin mutation forms from the student learning page and escaped lesson/course descriptions and embed URLs to avoid rendering stored content as arbitrary HTML.

## Verification and rollout

- `php artisan test --compact`: 26 passed, 99 assertions; includes public-course visibility, free pricing, ordered/published course content, enrollment/resource isolation, no GET progress mutation, repeated completion and lesson revision audit.
- `php artisan view:cache`: passed. LMS and student course routes register successfully.
- Local HTTP smoke test on `http://127.0.0.1:8067`: `/`, `/courses`, `/contact-us`, and `/sitemap.xml` returned 200.
- PHPUnit emits the existing `phpunit.xml` deprecated-schema warning; no test failures.
- Migration `2026_10_13_000007_extend_lms_curriculum_security.php` and M11 migration are not applied to the configured populated database. Apply them only to the separately provisioned demo/deployment database after backup and migration review.

## Remaining LMS rollout items

- M13 will define idempotent free/paid enrollment, course capacity/batches, expiry/refund enforcement, and learner progress rollups. Until then this module deliberately uses the legacy active enrollment status.
- Private-media streaming should be exercised against the production storage adapter and video range requests before deployment. Existing public lesson files are not automatically privatized.
- M10 CRM tasks/collaboration and later LMS modules remain in scope and will be handled in order.
