# M00 — Schema Map

Schema source inspected: 85 Laravel migration files plus read-only MySQL `information_schema` for `studylifter_local`. All migrations are marked applied. This is an inventory and mapping, not a proposed destructive replacement schema.

## Existing domain groups

| Domain | Tables / relationships visible in current schema | Audit note |
|---|---|---|
| Identity & access | `users`, `roles`, `permissions`, `role_has_permissions`, `model_has_roles`, `model_has_permissions`, password reset/token tables, Sanctum tokens | `users.role` is a legacy numeric role; Spatie permissions coexist. Runtime DB has 11 permission rows but no role rows. Normalize/bridge authorization carefully; preserve IDs and current login. |
| Public geography/catalogue | `countries` → `universities`; `study_levels`; `subject_categories` → `subjects`; `programs`; `scholarships`; `services`; `events` | These are catalogue/CMS structures; no current lead/application lifecycle. Foreign keys are generally not declared. |
| Consultation | `consultation_slots` ↔ `users` through `consultation_slot_user`; `free_consultations`; `appointments` referencing user, consultant, slot, meeting provider; `meeting_platforms`; `consulter_feedback` | Existing booking primitives can be adapted; verify uniqueness/conflict and lifecycle semantics before extending. |
| Student profile & consultancy work | `student_basic_infos`, `student_academic_infos`, `student_work_experiences`, `student_language_proficiencies`, `student_extracurricular_activities`, `onboarding_form_settings` | Profile records optionally reference a `student_service_order_id`; a general-purpose, per-application secure document model is absent. |
| Service order/task operations | `student_service_orders` → invoices, assignees, files, notes, tasks; tasks → assignees, attachments, conversations, seen-state; `labels` ↔ tasks | Good workflow reuse for tasks/case notes, but it is service-order-centric rather than a complete admissions CRM pipeline. |
| LMS | `courses` → `course_lessons` → `course_lectures`/`course_resources`; `course_enrollments`; `course_lecture_finishes`; `subjects`, `subject_categories`, `certificate_types` | Course structure/enrolment/progress is present. No evident quizzes, graded assessments, assignment submissions/reviews, live sessions/attendance, or issued certificates. |
| CMS | `about_us`, `faqs`, `testimonials`, `blogs`, `blog_categories`, `blog_tags`, `blog_tag`, `metas`, `contact_us` | Reuse for public GenZ content; confirm SEO, publication, image/file behavior. |
| Payments & finance | `gateways`, `gateway_currencies`, `payments`, `transactions`, `cash_transactions`, `banks`, `beneficiaries`, `withdraws`, `user_paypals`, `affiliate_history` | Payment gateway breadth exists in code/config; finance ledger and reconciliation semantics need focused audit before CRM invoices/LMS purchase reuse. |
| Shared configuration & operations | `settings`, `currencies`, `languages`, `email_templates`, `notifications`, `notification_seens`, `file_managers`, `failed_jobs`, `migrations`, `designations` | Existing shared primitives, with many records/data/config concerns to preserve. |

## Relationship sketch

```text
User (role integer; also may receive Spatie role/permission)
 ├─ consultant availability ─ ConsultationSlot (many-to-many)
 ├─ FreeConsultation / Appointment ─ consultant, slot, meeting platform
 ├─ Student profile ─ academic/work/language/extracurricular details
 ├─ StudentServiceOrder ─ assignees, invoices, files, notes, tasks
 │    └─ Task ─ assignees, labels, attachments, conversation/seen state
 ├─ CourseEnrollment ─ Course ─ Lesson ─ Lecture / Resource
 └─ Payment / transaction / affiliate history

Country ─ University ─ Subject ─ StudyLevel / SubjectCategory
                       └─ Scholarship
Program ─ Course
```

The sketch captures model intent, not guaranteed referential constraints. Only five foreign-key constraints were found in the live local schema; verify every actual relationship before adding migrations.

## Runtime local data snapshot (counts)

- Users 5; settings 13; currencies 6; email templates 15; permissions 11.
- No role rows found; five users use numeric roles 1 through 5.
- Countries, universities, programs, subjects, scholarships, courses, services, blogs, consultation requests, and appointments: zero rows in the inspected local DB.
- All 85 migration records exist as applied. No migrations or seeders were run during this audit.

## CRM/LMS data additions likely required (design in later approved modules)

- CRM: lead/source/consent and pipeline transition/activity history; student-document metadata/private storage and expiry; application records per student + institution/program/intake with stage history and decisions; offer/visa/arrival milestones; structured follow-up tasks/reminders. Existing users, consultations, profile, order/task, university, subject, and study-level structures should be reused where semantics fit.
- LMS: course sections/lessons can extend existing curriculum; add durable assessment/quiz/question/attempt/answer/grading records, assignment submission/review/feedback, live-session attendance, and certificate issuance/verification. Link to existing `course_enrollments` and users; do not duplicate course identity or user accounts.
- Cross-cutting: explicit owner/team scope, audit events, notification delivery state, robust constraints/indexes, and private attachment authorization. Design migrations additively and document backfills/rollback before applying them.

Do not implement this proposed schema in M00. Each later migration needs a backup, ownership/PII review, local dedicated-database test, up/down verification, and non-destructive preservation checks.
