# GenZ Consultants — Backend Presentation Guide

**Updated:** 13 October 2026  
**Scope:** Laravel backend, CRM/LMS workflows, database concepts and access control. Public-site redesign is intentionally out of scope.

## 1. One-minute introduction

“GenZ Consultants ka system ek Laravel application mein do connected products rakhta hai: Study Abroad CRM aur Online Learning Management System. CRM enquiries ko counsellor follow-up se student, documents aur university applications tak track karta hai. LMS courses, lessons, classes, quizzes, assignments, grades aur certificates manage karta hai. Dono same user identity, role/permission system, notifications, settings aur finance primitives reuse karte hain.”

Isay sirf dashboard ya static demo na samjhein: controllers validated requests ko database models/services ke zariye process karte hain. Demo records fictional hain; unhein real clients, universities, payments ya official contact details na kaha jaye.

## 2. Technical stack

- **Backend:** PHP 8.1+ and Laravel 10.
- **Database:** Laravel-supported MySQL/MariaDB for the configured local app; feature tests run on isolated in-memory SQLite.
- **Authentication and access:** Laravel web authentication, Sanctum API support, Spatie Laravel Permission, plus legacy numeric `users.role` compatibility.
- **Server-rendered UI:** Blade, Laravel routes/controllers, JavaScript/jQuery and Yajra DataTables in existing admin lists.
- **Other integrations available in the dependency set:** payment gateway adapters, social login, 2FA, Zoom-related package, S3-compatible storage, Pusher and QR-code support. A package being installed does **not** mean its account credentials or production integration are configured.
- **Local backend URL:** `http://127.0.0.1:8067` when the PHP development server is running.

## 3. How one backend request works

```text
Browser / portal
  → named route (routes/*.php)
  → installed + web + authenticated + role/verification/2FA middleware
  → permission check (menu visibility AND server-side route protection)
  → Form Request / controller validation
  → domain service and/or Eloquent model, often inside a DB transaction
  → MySQL/MariaDB records
  → Blade page, DataTables JSON, redirect, or JSON response
  → success/error feedback in the UI
```

For example, an administrator creating a student reaches the student store route. The server validates the fields, creates a user with the student role and hashed password, commits the database change, and attempts the welcome email separately. A mail transport failure should not erase a successfully created student. The recent regression tests cover that behavior and student edit/update.

For sensitive operations, do not rely on a hidden button: the route/controller also checks permission and record ownership. Examples include private student documents, assigned CRM leads, enrolled course content and student-owned applications.

## 4. Admin workspace, tab by tab

These are the main backend work areas. The exact sidebar entries shown to a user depend on their role and permissions.

| Area | What the team does | Main data/workflow |
|---|---|---|
| **Dashboard** | Review live database counts and operational charts; jump into work areas. | Aggregates existing records; it should not be presented as a fabricated forecast. |
| **CRM Leads** | Capture, search, assign and follow up on prospective students; update their stage; review source/activity; preview and commit validated imports. | Normal flow: `new → contacted → qualified → consultation → converted`; lost leads require a reason. Repeated enquiries can be deduplicated by normalized email, then phone. |
| **Students** | Create, edit and deactivate student accounts; inspect directory/course relationships. | Student is a user with role 2 plus related profile/academic/document records. Passwords are hashed; update can leave password unchanged. |
| **Student profiles & documents** | Collect study preferences and academic details; upload, review, approve/reject and download documents. | New sensitive files use private storage and authorization checks; reviewers’ access is role/assignment scoped. Passport values added through new protected flows are encrypted. |
| **Admissions** | Create applications against eligible published academic programs, track stages and visa progress, add internal/student-visible notes and counselor tasks. | Application stage transitions are validated and logged; active duplicate applications for the same program/intake are blocked. |
| **Universities & academic catalogue** | Maintain destinations, universities, study levels, subjects, academic programs, intakes and scholarships. | The admissions catalogue (`academic_programs`) is distinct from LMS courses and the legacy course-marketing “programs” concept. Public price display is disabled; stored admin tuition is not automatically a public price. |
| **Consultations** | Set consultant slots/availability, review free requests, book/reschedule/cancel appointments, and review feedback. | Booking checks consultant-slot ownership, active status, time conflicts, holds and cancellation history in a transaction. |
| **Services / service orders** | Sell/track consultancy services, invoices, assigned work, files, notes and task conversations. | This is a legacy operational workflow and remains separate from the newer admissions application entity. |
| **LMS courses** | Create courses; manage publishing, ownership, batches and enrollment. | Instructors are restricted to their owned courses; course visibility and learner access use publish/enrollment status. Public course pricing is intentionally hidden. |
| **Curriculum** | Organize lessons, lectures and resources; set publication/order/preview; maintain revisions. | Learners only load published curriculum for an active enrollment. New restricted resources are delivered through authenticated routes. |
| **Live sessions** | Schedule/cancel course sessions, track eligible attendees and attendance. | Meeting/recording links are encrypted; enrollment and timing are checked before joining. This is a manually supplied meeting URL, not proof of a live Zoom/Meet API integration. |
| **Quizzes** | Build question banks, questions and quizzes; configure attempts, timing, pass mark and result release. | Initial objective question types are single/multiple choice; score is computed server-side, not trusted from the browser. |
| **Assignments & grading** | Publish work, accept student submissions, download private files, grade and release feedback. | Submission states, late rules, grade history and feedback release are persisted. Virus scanning/plagiarism detection are not configured. |
| **Certificates** | Inspect eligibility, issue/revoke certificates and verify a certificate token. | Eligibility checks required published content, quiz results and released assignment grades. Certificate PDF/template generation remains a separate enhancement. |
| **Finance / orders / transactions** | Review payments and transactions, service/course/event orders and affiliate payout records. | Existing payment/transaction tables are reused. The code handles idempotent paid transitions and enrollment entitlement changes; refunds, tax and reconciliation still require business/provider-specific implementation. |
| **Team & permissions** | Create staff, assign roles, define permission bundles and deactivate accounts. | See Section 6. Numeric Admin role 1 is the global super-admin; staff role administration has an additional super-admin gate. |
| **CMS / events / SEO** | Maintain existing website content, blog taxonomy, FAQs, testimonials, events, metadata and SEO settings. | CMS editing is a separate role workspace; external SEO/search performance is not guaranteed by the admin CRUD screens. |
| **Settings / notifications** | Configure application settings, currencies, languages, gateways, mail templates and notification preferences. | In-app notification outboxes deduplicate/retry selected events. Email/SMS/WhatsApp sending needs consent, provider configuration and delivery testing. |
| **Analytics** | Filter CRM, admissions, LMS and finance aggregates by date range. | Permission-aware aggregates avoid exposing lead/student PII in report rows. Finance totals are gross paid amounts, not net revenue after refunds. |

## 5. Portal workspaces by role

The route prefixes are useful for a live presentation. A user signs in, then should be directed to the workspace appropriate to their account:

| Account type | Workspace | Typical responsibilities |
|---|---|---|
| Admin / staff | `/admin` | Cross-module operations. Admin has the global override; staff has only assigned permissions. |
| Consultant / counselor | `/consultant` | Assigned leads, assigned students/applications, consultations, availability and follow-ups. |
| Student | `/student` | Own profile/documents, saved options, applications, appointments, service history, courses, quizzes, assignments, certificates and notifications. |
| Instructor | `/instructor` | Own courses and learner teaching workflows. Admin can oversee all courses. |
| Finance | `/finance` | Finance workspace and assigned finance operations. |
| Affiliate | `/affiliate` | Their referred students, wallet, transaction/withdrawal and beneficiary history. |
| CMS editor | `/content` | CMS/SEO content operations allowed by their permissions. |

Role IDs from the application constants: Admin 1, Student 2, Staff 3, Consultant 4, Affiliate 5, Instructor 6, Finance 7, CMS Editor 8. These are application role identifiers, not proof that a particular login exists in the currently running database.

## 6. Permissions: how to explain and assign them

1. Sign in with an active Admin account (numeric role 1). Admin’s `Gate::before` rule grants all abilities. Do not convert an ordinary staff account into an Admin merely to make a menu appear.
2. In **Manage Staff**, create or edit the staff account and assign an existing active Spatie role. Staff accounts remain numeric role 3; the attached role supplies the detailed permission set.
3. Open **Manage Staff → Roles** (routes under `/admin/staffs/roles`). Create/edit a role and select only the required permission names, for example `Manage Leads`, `Manage Students`, `Manage Courses`, `Manage Transaction`, or `Manage Cms Settings`.
4. Save and sign in as a test account with that role. Its sidebar is filtered by Blade `@can` checks, while routes separately enforce `can`/`canany` middleware. A manually entered URL must still return 403 without the permission.
5. To remove a permission, uncheck it and save. Role update calls `syncPermissions`, so unchecked permissions—including all permissions—are revoked. A role cannot be deleted while it is assigned to users; reassign them first.

Important compatibility detail: legacy accounts with no Spatie role pivot may receive defaults from `config/genz.php`. Once a Spatie role is attached, that role’s explicit permission set becomes authoritative for the configured staff/consultant/instructor/finance/CMS roles. This can explain why a menu changes after a role assignment. A 403 can also mean wrong workspace/role, inactive account, or missing permission; it is not fixed safely by exposing every menu.

Default fallback permissions currently listed in configuration:

- Staff: students, services, consultations/free consultations, leads and admissions.
- Consultant: leads, student documents, admissions and consultations.
- Instructor: courses.
- Finance: transactions.
- CMS editor: CMS settings and events.
- Admin: all abilities via the super-admin gate.

Actual access should still be verified against the database’s role/permission pivots after MySQL is healthy; menu labels alone do not prove effective access.

## 7. Two end-to-end stories for the presentation

### Story A — enquiry to study-abroad application

1. A prospect submits an enquiry; the CRM records source/activity and matches an existing open lead where appropriate.
2. Admin assigns the lead; the consultant sees only assigned work and records follow-up activity.
3. After qualification, the person is registered as a student. The student completes their profile and uploads private documents.
4. The counsellor/admin reviews documents and helps select a verified destination, university, academic program and intake.
5. The student starts an application; backend checks active institution/program and prevents a duplicate active application for the same intake.
6. The counselor advances the application through allowed statuses, records reasons/history, tracks visa progress and adds tasks/notes.
7. Consultation appointments and existing service orders can support the case without being mistaken for the admission application itself.

### Story B — learner from course to certificate

1. Course manager creates a course, curriculum and optional batch, then publishes it; instructors can manage only owned courses.
2. Learner enrolls (free enrollment does not fabricate a payment; paid access requires confirmed payment).
3. Student opens published lessons/resources; the server records explicit progress and checks active, unexpired enrollment.
4. Student joins an eligible scheduled session, attempts a published quiz and submits an assignment.
5. Instructor grades the assignment and releases feedback. Quiz scoring is server-side; manual-release results remain hidden until released.
6. Course manager issues a certificate only when eligibility requirements pass. A public verifier reveals limited information and reports revocation accurately.

## 8. Database, privacy and safe-demo explanation

- Existing StudyLifter users, courses, enrollments, service orders, CMS, payments and consultation tables are reused wherever their meaning fits. GenZ CRM/LMS improvements are largely additive migrations, not a wholesale schema rewrite.
- Main data families: identity/permissions; destinations and academic catalogue; CRM leads/activity/follow-ups; student profiles/documents; admissions and status history; consultation bookings; LMS course/curriculum/enrollment/progress/assessment/certificate; finance; CMS/settings/notifications.
- Student documents and LMS submissions are private resources and require authorization for download. Passport values and selected meeting details are encrypted at the application layer. Keep `APP_KEY` protected and include it in secure backup/restore planning.
- Demo data must be fictional, use reserved `.example` emails and a dedicated isolated development/demo database. The `GenZDemoSeeder` is not part of the default seeder and refuses production/other database targets. Do not run seed or migrations against the populated `studylifter_local` database without a verified backup and explicit target review.
- The verified demo database currently contains 13 users, 10 courses, 26 enrollments, 3 universities, 3 scholarships, 11 CRM leads, 3 events and 3 services. Those are fictional development records, not real clients or official partner data.
- Verified local demo-admin sign-in: `demo.admin@genzconsultants.example` / `GenZLocal!2026`. Use it only at `http://127.0.0.1:8068`; it is not a production credential. Other role passwords were not re-verified.

## 9. Current verification and honest limits

At the time of this guide:

- The latest dated verification details are in [the status addendum](audit/STATUS_ADDENDUM_2026-10-13.md); older module reports are checkpoint history.
- The original PHP server remains at `http://127.0.0.1:8067`; a separate local demo server is now running at `http://127.0.0.1:8068` against the isolated demo database.
- Feature tests are configured for isolated in-memory SQLite and do not mutate the configured application MySQL database.
- Recent student-management checks passed for create despite a mail transport error, and for edit/update without changing an omitted password. Sidebar order/permission tests also pass.
- Read-only migration status reports 85 ran / 15 pending on production-marked `studylifter_local`, and 100 ran / 0 pending on `genz_crm_lms_demo`. MySQL is reachable now; no migration or seed was run.
- The server on 8067 uses the configured application target; do not apply pending migrations to `studylifter_local` as part of a presentation. The separate 8068 demo admin sign-in and dashboard were verified over HTTP, including the reordered sidebar.
- Composer security audit is not verified because Composer CLI is unavailable in this shell. No browser accessibility/responsive walkthrough or live third-party payment/email/WhatsApp/Zoom test is claimed.
- Old project audit files describe an earlier baseline. Read the latest addendum and current tests, not old baseline counts, as the current checkpoint.

Useful safe checks from the project root (these do not migrate or seed the configured DB):

```powershell
& 'C:\xampp\php\php.exe' artisan route:list
& 'C:\xampp\php\php.exe' artisan test --compact --no-coverage
& 'C:\xampp\php\php.exe' artisan view:cache
```

Do not run `migrate`, `db:seed`, `migrate:fresh`, or any reset command against the current database as part of the presentation. First recover/identify the DB service and verify the intended isolated demo DB.

## 10. Suggested live demo order and talk track

The sidebar now follows the same operational journey, so present it in this order:

1. **Dashboard:** “These are aggregates from database records. A zero means no matching record in the selected DB—not a hardcoded sample.”
2. **Lead CRM:** show an enquiry, its source/stage/owner/activity and assigned visibility.
3. **Consultation Requests → Consultations:** explain the free-request inbox, consultant availability, appointment conflict checks and booking history. Do not promise external calendar/meeting provisioning.
4. **Student Directory → Student Documents:** show a fictional student profile and private-document review; never expose real passport/identity data.
5. **Study Abroad Catalogue:** show destinations, universities, verified programs/intakes and scholarships that counselors can use for an application.
6. **Admissions:** follow the selected student application through its stage history, counselor tasks and visa tracking.
7. **Learning Management:** show course/track → curriculum → enrollment/progress → live session → quiz/assignment → grading → certificate eligibility.
8. **Services & Activities:** explain existing service orders/invoices separately from admissions applications, then show event management where relevant.
9. **Finance & Referrals:** explain payment state and course entitlement; identify gross analytics figures and do not claim refund reconciliation unless demonstrated.
10. **Insights:** review permission-filtered aggregates, not a replacement for audited finance statements.
11. **Team & Access → Website & Content → System:** demonstrate least-privilege roles, role-specific hidden menu items, and server-side 403 checks. Only the super-admin sees the role-permission editor. Never display passwords or secret configuration.
12. **Notifications/Profile:** distinguish in-app notifications and account security from external email/SMS/WhatsApp providers that still need credentials.
13. Close with the operational limits in Section 9: database recovery, isolated migration review, browser checks, Composer audit and staging verification remain before production.

The sidebar cleanup changed navigation labels/order only. A misleading duplicate consultation link formerly labelled “Leads” is now correctly named “Consultation Requests”; the underlying consultation functionality and route remain. The version-update action remains available to the Admin but is moved out of the daily business workflow. No database records or business modules were removed.

For every tab use the same explanation pattern: **purpose → who can access → what the user submits/changes → which records are created/updated → what validation/privacy guard runs → how to verify the result**.
