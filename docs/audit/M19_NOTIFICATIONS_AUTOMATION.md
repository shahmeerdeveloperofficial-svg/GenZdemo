# M19 — Notifications, preferences and delivery

- Reused the existing in-app `notifications` table and the CRM task-specific outbox; added a general notification outbox and per-user/event/channel preference table.
- LMS certificate issuance and released assignment grades enqueue in-app notifications inside the same database transaction as the underlying event. Delivery is deduplicated, row-locked and retried with capped exponential backoff; scheduler command runs once per minute.
- Learners can opt in/out of the supported LMS event types. No email/SMS/WhatsApp messages are emitted by this module; external channels remain disabled until consent, provider credentials and delivery policy are configured.
- Test suite: 34 tests / 149 assertions passed, including dedupe, repeat drain, and opt-out behavior. View cache and preference route listing passed.

## Remaining

- Add admin-editable localized templates, user-configurable channel consent for email/SMS, delivery-provider adapters, bounce/suppression handling and broader CRM/admissions event wiring. CRM follow-up notices continue using the pre-existing CRM outbox.
