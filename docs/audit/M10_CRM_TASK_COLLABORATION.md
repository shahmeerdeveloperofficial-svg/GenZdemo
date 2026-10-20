# M10 — CRM task collaboration

- Audited existing task surfaces before adding anything: CRM lead follow-ups, admissions application tasks, and service-order tasks already existed, with the latter providing student-facing conversations and attachments. Those independent records are preserved.
- Extended CRM lead follow-ups with priorities and explicit open/completed status; completion is idempotent and remains scoped to leads the signed-in staff member can access. Staff/consultants can only comment on their assigned follow-ups.
- Added internal-only follow-up comments and activity feed entries; public/student surfaces do not expose lead follow-up comments. Existing service-order task conversations/attachments continue to be the student-visible collaboration channel.
- Added a deduplicated notification outbox with unique event keys, delayed due-soon reminder scheduling, attempt/error tracking and exponential backoff. Assignment notifications are queued immediately; reminder notifications are due 24 hours before the task (or immediately for tasks due sooner), and pending reminders are suppressed when a task is completed.
- Registered `genz:deliver-crm-task-notifications` and a once-per-minute scheduler entry. Production must run Laravel's scheduler (`php artisan schedule:work` locally, or the OS cron/Task Scheduler equivalent in deployment) for outbox delivery/retries.

## Verification and rollout

- Test suite: 27 passed, 104 assertions at this checkpoint. Includes duplicate notification enqueue/delivery checks and CRM/consultation/admission/LMS regressions.
- `php artisan view:cache` and custom Artisan command registration passed.
- Migration `2026_10_13_000008_extend_crm_follow_up_collaboration.php` is additive and has not been applied to the configured populated database.

## Remaining

- CRM follow-up attachments and cross-module unified task search are not added; service-order task attachments and admissions tasks remain their existing module-specific implementations. SLA reminders currently cover assignment and due-soon events, not escalations by role or missed-response analytics.
