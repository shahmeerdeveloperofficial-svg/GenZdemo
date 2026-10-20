# M06 Student profile and document security

## Reuse and additive data

Existing `student_basic_infos`, `student_academic_infos`, language tests, work history, extracurricular records and service-order onboarding remain intact. `student_profiles` adds a user-level preference/profile record; `student_documents` adds a private file/review history that is independent of any one service order.

## Access rules

- Students read/update only their own study profile and upload/download only their own records.
- Admin/staff review is protected by the existing Manage Students / Manage Service abilities.
- Consultants can review/download only documents for students with an active service order assigned to that consultant.
- Student documents are never served by a public storage URL. New uploads are MIME/extension checked, capped at 10 MB, encrypted with the Laravel application key, stored on the private local disk under `storage/app/private`, and downloaded as private attachments after authorization.
- Approve/reject is atomic and only applies to pending records. Rejected re-uploads create a new linked document row; older file and decision history are retained.
- Passport numbers in new profile/onboarding writes are encrypted at the application layer. The legacy onboarding model reads old plaintext values for backward compatibility and encrypts them the next time saved; the existing database has not been batch-rewritten.

## Checklist and completeness

The profile completeness percentage is the count of eight displayed items (account name/email, DOB, gender, nationality, present address, one destination, intake, one uploaded document) divided by eight, rounded to the nearest integer. The displayed file categories are common examples only; users are told that exact requirements vary by institution/program/destination and need counselor confirmation.

## Operational/security caveats

Files use `APP_KEY` encryption; keep encrypted storage backups and do not rotate the application key without a planned re-encryption process. No malware scanner is configured in the repository, so files remain pending human review; add scanning before production uploads. Existing previously uploaded files that were written to the old public disk are not retroactively moved/deleted by this additive module. Migration `2026_10_13_000002_create_student_crm_profile_documents.php` has not been applied to the current `APP_ENV=production` database.
