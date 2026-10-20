# M16 — Assignments, grading and feedback

- Added course-scoped assignment authoring with draft/published state, instructions, due dates, maximum points, submission count, accept/reject-late policy and late penalty.
- Students can save drafts or submit written work and supported PDF/DOCX/TXT/image files (20 MB max). New files use the private disk and are served only through enrollment/submission-scoped download routes; no public storage URL is exposed.
- Submission versions and draft/submitted/late/graded states are persisted. Course assignment capacity, late rules and resubmission limits are checked server-side under a transaction.
- Course owners/instructors can view submissions, download private files, grade up to the assignment maximum, enter rubric/feedback, apply the configured late penalty and choose whether to release feedback. Students only see grade/feedback and rubric criteria after release. Private-file downloads also verify the file is owned by the submitting learner.
- Every grade creation/update records before/after data and actor in `lms_grade_history`.

## Verification and rollout

- At the M16 checkpoint: 32 tests, 138 assertions passed. Includes late submission acceptance/rejection, penalty calculation, result release and grade-history audit.
- `php artisan view:cache` passed. Migration `2026_10_13_000012_create_lms_assignments_grading.php` has not been applied to the configured populated database.

## Remaining

- No automatic antivirus scan or plagiarism checking is enabled. Supported file types are intentionally limited; upload and download streaming still need production storage-adapter testing. Multiple-choice-like rubric criteria are supported as JSON; configurable rubric templates, bulk grade import and assignment notifications are future work.
