# M05 Lead CRM policy and implementation notes

## Source compatibility

Legacy `contact_us` and `free_consultations` remain the systems of record for their original screens and reporting. When the additive CRM migration is installed, new public submissions also create/update a CRM lead in the same transaction. Existing legacy rows are not backfilled or modified.

## Duplicate handling

- Normalize email to lowercase and trim whitespace; use it as the primary identity key.
- If no open lead matches email, normalize phone to digits plus an optional leading `+` and use it as fallback.
- Only open pipeline leads (`new`, `contacted`, `qualified`, `consultation`) are deduplicated. Converted/lost records remain distinct history.
- Repeat enquiries enrich only missing contact fields and merge preference data; they never reset stage, owner, tags, or create a student account. Every intake is appended to the activity timeline with its source.
- CSV import is preview-first, limited to 2 MB/500 rows, validates row content, marks duplicates, and only commits after explicit confirmation. Existing open leads merge using the same policy.

## Pipeline and privacy

`new -> contacted -> qualified -> consultation -> converted` is the normal path; a lead may be marked lost from active stages with a required reason. Lost can be reopened to contacted. Converted is terminal to prevent accidental duplicate student creation. Stage timestamps and transition activities are retained.

Internal notes are explicitly private and are shown only inside authorized CRM screens. Admins can see all records; staff can see leads assigned to or created by themselves; consultants can see assigned leads only. Follow-up completion is idempotent and owner-scoped.

## Operational constraints

Migration `2026_10_13_000001_create_crm_lead_tables.php` is additive and creates only CRM-owned tables. It has not been run against the project's existing database because `.env` reports `APP_ENV=production`; run it only against the isolated GenZ demo/development database after confirming its connection. No demo lead PII is seeded by this module.
