# M14 — Live classes, attendance and calendar

- Added one-off live sessions linked to an LMS course or optional active batch, with time zone, duration, provider label, cancellation and recording link.
- Meeting and recording URLs are encrypted in the database. Student pages never render meeting URLs; the authenticated join action checks active enrollment, course/batch membership, scheduled status and a 15-minute early / session-end join window before redirecting to the meeting provider.
- Authenticated learner heartbeat requests update attendance from server-observed elapsed time, capped per request and counted only during the scheduled session. Attendance is marked attended after at least half the session (minimum five minutes). Staff can manually mark joined learners present.
- Staff/instructors can schedule, reschedule and cancel sessions from the course curriculum page. Rescheduling increments a revision and queues one deduplicated notification per currently eligible learner. Batch membership restricts both session listing and join/recording access.
- The provider field currently labels a manually configured HTTPS meeting URL. No Zoom/Google API credentials, OAuth, or meeting provisioning integration is enabled; no meeting credentials are exposed on public pages.

## Verification and rollout

- Suite: 29 tests, 119 assertions passed. Live-class tests verify encrypted URLs, matching-course enrollment checks and server-time attendance accumulation. `php artisan view:cache` and live-session route registration passed.
- Local site smoke checks on port 8067 returned 200 for `/`, `/courses`, `/contact-us`, and `/sitemap.xml`.
- Migration `2026_10_13_000010_create_lms_live_sessions_attendance.php` has not been run on the configured populated database.

## Remaining

- Sessions are one-off, not recurring. No provider API integration, automated cancellation notification, calendar sync/ICS, or recording ingestion exists. Attendance heartbeats measure presence in the authenticated course page, not verified human presence inside the third-party meeting.
