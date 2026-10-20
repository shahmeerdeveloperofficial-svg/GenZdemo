# M08 — Admissions workflow

## Implemented

- Added an independent admissions domain rather than repurposing legacy service orders: application records, status history, linked approved private documents, student/internal notes, and counselor tasks.
- Student-owned applications are created only from published programs at active institutions/destinations. A locked student row plus an active-application query prevents duplicate active applications for the same program/intake.
- Stage changes use an explicit transition graph and write actor, old/new stage, timestamp and reason. Rejection/withdrawal require a reason; submission and closure timestamps are retained. Visa progress is tracked separately.
- Students can view only their applications, link only their own approved private documents, and send student-visible notes. Team views are available to admin/staff and assigned consultants; internal notes are not loaded into the student view.
- Added role-gated staff/consultant routes and portal navigation. Existing StudyLifter service-order history and onboarding records are not changed.

## Verification and limits

- The admissions migration and workflow are tested on isolated in-memory SQLite: legal stage progression, history audit entries, and duplicate active-application rejection.
- No migration was run against the populated application database. New portal operations require the isolated demo database to apply additive migrations first.
- Email/SMS automation, visa document processing, university API submission and university decisions are not claimed as implemented. Tasks are internal workflow items; due-date reminders are for a later automation module.
