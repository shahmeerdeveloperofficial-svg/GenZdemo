# M13 — LMS enrollment, batches and learner progress

- Centralized new student enrollment writes in `LmsEnrollmentService`: free checkout, confirmed gateway payment, admin-recorded payment and admin manual enrollment are idempotent and capacity-checked under a course row lock. All active course enrollments now use this shared service.
- Free courses enroll without a fake checkout/payment. Paid courses still use the existing gateway flow; public course prices remain hidden. Confirmed course-payment reversals/cancellations revoke the enrollment linked to that payment where the migration is installed.
- Added optional access expiration and explicit revocation metadata. Student course lists, course pages, private media endpoints and progress writes reject expired/revoked enrollments. Legacy enrollment rows remain accessible unless their existing status says otherwise.
- Added course batches with timezone-aware start/end, seat capacity, active/draft/cancelled state and membership. Course and batch capacity are enforced transactionally; manual staff enrollment can place the learner into an active batch.
- Added resumable per-resource progress, time spent, completion events and legacy completion compatibility. Player progress posts are validated and time contribution is capped per request. Formula: completed required published resources ÷ total required published resources; a course with no required resources is 0% until content exists.
- Instructor-owned course scope continues to restrict their class/enrollment management; admins retain oversight.

## Verification and limits

- Full suite: 28 tests, 111 assertions passed, including idempotent free enrollment, capacity rejection, expiration access denial, curriculum hierarchy, repeat-safe progress and M10 notification deduplication.
- `php artisan view:cache` and route registration passed. Existing local app pages at port 8067 remain 200 using the pre-migration compatibility paths.
- Migration `2026_10_13_000009_create_lms_enrollment_progress_batches.php` is additive and has not been run against the populated configured database.
- Batch management is currently course-admin curriculum UI plus manual enrollment assignment; automatic learner batch choice, capacity waitlists, expiry policy defaults and refund-provider integrations are not yet present. Refund/cancel status updates revoke access for linked payment IDs; provider-specific refund execution remains external.
- Resumable player tracking needs staging verification with real media and network loss. Completion is server-authorized but media watch-time is progress telemetry, not exam-grade proof of attendance.
