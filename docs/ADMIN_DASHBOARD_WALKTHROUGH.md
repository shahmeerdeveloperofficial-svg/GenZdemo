# Admin dashboard and dynamic website walkthrough

This guide follows the labels in the admin sidebar. Items are shown only when the signed-in account has the matching permission, so another role may see fewer tabs. The dashboard and public website use saved database records; they are not all static mockups.

## How a dynamic website works

1. An admin adds or edits a record in the dashboard (for example a university, program, event, or blog).
2. Laravel validates the form and saves the record to MySQL. Uploaded files are stored on the configured storage disk; private student files are encrypted and are not public URLs.
3. A controller reads the saved records and prepares a page or JSON response.
4. A Blade view or the public React website renders those records in the browser. A student or visitor submitting a form creates another database record, which staff can review in the dashboard.

In short: dashboard form -> Laravel route/controller -> validation -> database/storage -> public page or dashboard list. A page is dynamic only where it is wired to saved records; text/images in a built frontend can still be static.

## Sidebar tabs, one by one

### Workspace

- **Dashboard:** quick operational counts and links. Counts reflect saved records, not a separate source of data.

### CRM & Admissions

- **Lead CRM:** prospective students, contact details, pipeline stage, owner, notes, and follow-up tasks.
- **Consultation Requests:** enquiries submitted from the free-consultation form; staff can review and follow up.
- **Consultations:** configure availability slots and meeting platforms, manage consultant profiles and appointments, view consultation orders, and review feedback.
- **Reviews:** view student feedback on consultations; demo reviews are explicitly labeled as fictional and are not real endorsements.
- **Student Directory:** student accounts and their basic status/profile details.
- **Student Documents:** private student uploads awaiting review; filter the queue, open a student's document profile, securely download a file, and approve/reject it.

### Study Abroad Setup

- **Destinations:** countries displayed in the study catalogue and related forms.
- **Study Levels:** levels such as undergraduate or postgraduate, used to classify programs.
- **Universities:** institution records linked to a destination.
- **Academic Programs & Intakes:** course/program offerings connected to universities, countries, and application periods.
- **Scholarships:** funding opportunities shown in catalogue pages.
- **Subject Categories:** groups used to organize subject choices.
- **Subjects:** individual subject records used by search/forms and catalogue pages.

### Applications

- **Admissions:** a student's application pipeline. Staff update stages and deadlines; a university's actual decision remains external.

### Learning Management

- **Course Tracks:** groups or pathways for online courses.
- **Courses:** course descriptions, lessons/resources, batches, live sessions, quizzes, assignments, certificates, and enrollments.
- **Course Orders:** purchases/enrollments related to courses, separated from other order types.

### Services & Activities

- **Manage Services:** services the organization offers.
- **Service Orders & Tasks:** student service requests, assignments to staff, progress, notes, and related workflow.
- **Service Invoices:** invoices generated for service work.
- **Events:** create public events and manage event orders/registrations.

### Finance & Referrals

- **Transactions:** payment records and their status.
- **Affiliates:** referral partners and tracked referrals.
- **Affiliate Payouts:** payout requests and their processing status.

### Insights

- **Analytics & Reports:** date-filtered summaries calculated from saved leads, applications, enrollments, and confirmed payments. Payment totals are gross totals, not profit or net of refunds.

### Team & Access

- **Staff Directory:** staff accounts and active/inactive status.
- **Roles & Permissions:** assign named roles and permissions. Custom roles determine which protected tabs/actions a staff member can use; the built-in account roles should not be casually changed.

### Website & Content

- **Public Website Editor:** edit supported public-site content and media.
- **Landing Page:** configure landing sections such as banners, services, benefits, process, calls to action, and consultation content.
- **About Us:** manage organization information.
- **Blogs & News:** publish editorial content.
- **SEO Settings:** page metadata used by search engines and link previews.
- **Website Enquiries:** visitor contact/enquiry submissions.

### System

- **Configurations:** global application configuration such as supported currencies, languages, or integrations, depending on enabled settings.
- **Application Settings:** organization/app behavior and presentation settings.
- **System Update:** application update controls; use only after a backup and when the update package is trusted.

### My Account

- **Notifications:** alerts and workflow updates for the signed-in account.
- **Profile & Security:** personal profile, password, and security options.

## Suggested presentation order

1. Start at Dashboard and explain that it summarizes persisted database records.
2. Show the journey: Lead CRM -> Consultation Request -> Student Directory -> Student Documents -> Admissions.
3. Show the catalogue: Destinations -> Universities -> Programs/Intakes -> Subjects/Scholarships; explain these records feed public search and forms where connected.
4. Show Courses and Services as separate learning and operational workflows.
5. Finish with Analytics, Team & Access, and Website Content to explain reporting, permissions, and public-facing content.

## Local demo student documents

The `DemoStudentDocumentSeeder` creates clearly watermarked, fictional PDFs for demo student accounts only. They contain no real passport, academic, test, or financial evidence. Documents are stored encrypted on the private disk and appear as pending items in the review queue.

The `DemoAdmissionSeeder` creates sample application rows for those demo learners using the existing published sample programs. It records the initial stage in application history and does not claim that an application was actually sent to, or decided by, a university.

Run it only in the local demo app:

```bash
php artisan db:seed --class=DemoStudentDocumentSeeder
php artisan db:seed --class=DemoAdmissionSeeder
```

These seeders are safe to run again; they do not duplicate existing demo records. Never use demo documents as official evidence or seed demo records in production.
