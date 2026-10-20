# M15 — Quiz engine and question bank

- Added course-scoped question banks, single- and multiple-choice questions/options, quizzes and question links, attempts and answer records. Staff/instructors can author questions, set time window, timer, attempt cap, shuffle order, pass mark and manual result release.
- Publishing requires at least one linked question and a correct option on each question. Student attempt pages receive only question text, points and option text/IDs; correctness and explanation fields are not selected into the payload.
- Start/resume and submit run in transactions with quiz/attempt row locks. The service enforces active enrollment, published quiz, open/close window, attempt limits, server deadline, question/option membership and single-choice rules. Scores are derived server-side from exact option-set matches.
- Results and answer review are owner-scoped. Auto-release quizzes expose score and answer keys only after submission; manual-release quizzes keep score/results hidden until staff explicitly releases them. No browser-calculated scores or remote proctoring.
- Quiz management is linked from the course curriculum page; learners can open course quizzes from their enrolled course.

## Verification and rollout

- Suite: 31 tests, 128 assertions passed. Includes hidden key payload, server scoring, manual result release, max-attempt behavior and exact deadline / after-deadline boundary checks.
- `php artisan view:cache` and route registration passed. Current port-8067 public smoke pages remain 200 on the pre-migration database.
- Migration `2026_10_13_000011_create_lms_quiz_engine.php` is additive and has not been applied to the configured populated database.

## Remaining

- Initial objective question types are single/multiple choice only. No essay/manual-answer grading, question imports, retake accommodation overrides, or randomized option order. Passing does not yet gate course completion/certification; later analytics/certificate modules can consume attempt results.
