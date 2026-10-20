# M07 — Destinations, universities, programs and scholarships

## Implemented

- Added an additive migration for verified destination/institution/scholarship metadata, a dedicated `academic_programs` domain, program intakes and student saved-option records. Academic programs remain separate from LMS `courses` and LMS marketing `programs`.
- Added public searchable and filterable academic-program cards/details, SEO metadata mapping, sitemap inclusion, a session-based comparison of up to three programs, and student-owned saved-option actions/list for destinations, universities, programs and scholarships.
- Added admin program create/edit/publish/archive workflow with validation, intake management and a publish gate requiring the official source and verification date. Tuition may be kept in the admin database but is omitted from public program pages and comparison.
- Extended destination, university and scholarship admin forms with source and verification metadata. Public detail pages show sources and eligibility conditions; active scholarships require a source and verification date.
- Public university, country, subject and scholarship detail lookups now exclude inactive records or return 404 for unavailable content. Public university filters validate their inputs and paginate active records.
- Added a distinct public LMS course catalogue route without public course prices. Public purchase/checkout remains the point where any payable amount must be disclosed before payment.

## Safety and verification

- The new catalog migration was exercised against the test suite's isolated in-memory SQLite database. No migration, seed, or write was run against the configured application database, which is marked production.
- A missing academic-program table degrades to an empty public catalog and omits catalog URLs from the sitemap, preserving behavior before the migration is run.
- Tests at this checkpoint: 20 passed, 76 assertions. Blade templates compiled. Local smoke requests on port 8067 returned HTTP 200 for home, programs, courses, sitemap, universities and scholarships.
- Before using admin program CRUD on a demo database, apply migrations in order. Populate only verified official sources; do not represent demo contact details or invented institutions as official facts.

## Outstanding M07 work

- Populate realistic catalog examples only in the separate development/demo database, with source/verification provenance.
- Add end-to-end tests against the eventual isolated demo database once it is provisioned.
