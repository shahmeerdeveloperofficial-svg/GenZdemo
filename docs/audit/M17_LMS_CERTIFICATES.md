# M17 — Certificates and learning completion

- Added `lms_certificates` with one issuance per enrollment, unique certificate number and random verification token, issuer/revoker IDs, issue/revoke timestamps and revocation reason.
- Eligibility is computed at issue time by the server: enrollment must be active and unexpired, all required published course resources completed, every published quiz passed (and manually released where configured), and every published assignment graded and released.
- Course managers/instructors can inspect each active learner’s progress and unmet requirements, issue eligible certificates, and revoke with a required reason. Instructor views are limited to courses they own.
- Learners have an owned-certificate list and printable certificate page. Public verification is token-based and returns only status, course, issue date, certificate number and learner first name/last initial; revoked certificates verify as revoked.
- Issuance is idempotent per enrollment. Revocation is retained and does not delete certificate history.

## Verification

- In-memory SQLite suite: 33 tests / 145 assertions passed, including eligibility rejection before lesson completion, successful issue, idempotency and revocation.
- Migration `2026_10_13_000013_create_lms_certificates.php` has not been applied to the populated `.env` database.

## Remaining

- Certificate templates/PDF generation, renewal/reissue policy, analytics exports and audit-log integration remain future enhancements. No accreditation or external credential-recognition claim is made.
