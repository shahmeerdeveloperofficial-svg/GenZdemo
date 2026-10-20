# M09 — Consultation booking and availability

## Implemented

- Audited the existing consultant/slot/pivot/appointment system and routed all new student and staff booking writes through `ConsultationBookingService`.
- Booking validates an active consultant, an active slot actually assigned to them, supported consultation type, future date/time, consultant weekly days off, and optional blackout dates.
- Slot reservation locks the consultant row in a DB transaction, checks other paid/pending bookings and unexpired payment holds, then creates/reschedules the appointment. New unpaid checkout holds expire after 15 minutes. This avoids trusting the public slot-list query as the final source of truth.
- Added consultant timezone settings, surfaced the timezone beside the booking calendar, blackout-date management, booking/reschedule/cancel event history, and a 24-hour student self-service reschedule/cancellation cutoff.
- Student appointment details, review modal and cancellation/reschedule actions now enforce ownership. Duplicate reviews are rejected. Demo mode does not send appointment email notifications.

## Verification and limits

- The new migration and slot service are exercised in isolated SQLite tests, including ownership of a consultant slot, collision rejection, cancellation audit and slot reuse after cancellation.
- PHP syntax, route registration and full test suite pass at this checkpoint: 22 tests, 87 assertions.
- No migration was applied to the configured populated DB. Consultation booking writes on a deployment require the additive M09 migration. The calendar uses recurring consultant slots plus weekly off-days and blackout dates; it does not yet provide a rich weekly-hours editor, SMS integration or automated reminder delivery.
