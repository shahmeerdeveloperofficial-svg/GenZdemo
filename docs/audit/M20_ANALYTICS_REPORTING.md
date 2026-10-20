# M20 — Analytics and reporting

- Added a permission-aware admin report page with from/to date filters (default last 30 days; maximum 366 days).
- CRM metrics group leads by current pipeline status and admissions applications by current status for records created during the selected window.
- LMS metric counts enrollments by course for records created during the window and excludes expired access where the LMS expiry column exists.
- Finance metric counts confirmed paid ledger entries and sums the stored system amount, grouped by paymentable type AND system currency to avoid misleading cross-currency totals. The UI explicitly labels this as gross, not net of refunds.
- Each section is rendered only for staff with its corresponding existing permission; report rows are aggregated and do not expose lead/student PII. No synthetic counters are added.
- Routes: `admin/analytics`; sidebar link appears to authorized course/student/transaction managers. Route listing, 34 tests / 149 assertions and Blade view compilation passed.

## Remaining

- Counselor-scoped reports, lead-source conversion funnels, aging/SLA analysis, detailed course completion/quiz outcomes, refunds/net revenue, logged CSV export and query performance benchmarking remain future work.
