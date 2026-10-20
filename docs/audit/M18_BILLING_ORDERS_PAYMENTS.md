# M18 — Billing, orders and payments

- Reused the existing polymorphic `payments` ledger and `transactions` table, together with service-order invoices, consultation/event/course orders, configured gateway adapters and affiliate commission integration. No duplicate accounting schema or public price display was added.
- Hardened admin payment status updates: only pending/paid/cancelled values are accepted; the payment row is locked; repeat paid transitions return idempotently when a transaction already exists; transaction rows are first-created by payment ID. Course entitlements are still granted only on confirmed paid state and are revoked when a related paid order is reversed/cancelled.
- Hardened the student gateway return handler by locking and rechecking the payment before confirmation processing and making transaction creation idempotent. This closes the common repeated-return double-transaction path.
- Existing payments remain in the populated database and current migrations were not applied there.

## Verification and limits

- `php artisan test --compact`: 33 tests / 145 assertions passed after billing changes.
- `php artisan view:cache` passed. Certificate verification and assignment/certificate routes are registered. Public homepage and course catalog on local port 8067 both returned HTTP 200.
- Existing provider confirmation code remains responsible for gateway verification. This module does not add a new external webhook provider, formal partial-refund ledger, invoice tax policy, or affiliate payout workflow; those need provider/business-specific decisions before financial state mutation.
