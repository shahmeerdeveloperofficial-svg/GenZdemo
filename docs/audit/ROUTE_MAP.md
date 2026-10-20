# M00 — Route and View Map

`php artisan route:list --except-vendor` is currently blocked by a missing Logger class in `AddonUpdateController`. Counts below come from static route declarations, not a successful Laravel route registry; resolve the boot error in an approved module before treating this as a complete route inventory.

## Route files and route groups

| File | Approx. declarations | Scope |
|---|---:|---|
| `routes/web.php` | 17 | Auth, login/logout, password/email verification, social login, 2FA, public updater endpoints |
| `routes/frontend.php` | 31 | Public home/content, country/university/subject/course/scholarship/event/service discovery, contact, free consultation, booking, currency lookup |
| `routes/admin.php` | 276 | Admin dashboard/profile/settings, staff/roles, affiliates, CMS, geography/catalogue, consultations, service orders/tasks/invoices, student management, course administration, finance/configuration |
| `routes/student.php` | 41 | Student dashboard/profile, events/services, consultation bookings/reviews, checkout/transactions, onboarding, enrolled courses and notifications |
| `routes/consultant.php` | 19 | Consultant dashboard/profile, appointments/consultation work, service orders/tasks and related operational screens |
| `routes/affiliate.php` | 27 | Affiliate dashboard/profile, referrals/earnings/beneficiary and withdrawal flows |
| `routes/api.php` | 3 | Small API surface, rate-limited by the provider; inspect individual handlers before external exposure |
| `routes/channels.php`, `routes/console.php` | 0 | No explicit route declarations counted |

Provider middleware wraps public web pages in installed + web + version-update checks; role areas are prefixed `admin`, `consultant`, `student`, and `affiliate` and each requires auth plus a custom role guard, version-update, verification, and 2FA middleware. API routes use Laravel's API middleware and an IP/user-based 60-per-minute limiter.

## User-visible page inventory

- Public: marketing/CMS pages, study-abroad destination and university discovery, subject/course/program pages, scholarships, services, events, blogs, free consultation request and appointment availability booking.
- Admin: staff and role management; settings, languages, currencies, gateways and email templates; public CMS; catalogues; consultation providers/slots/appointments; student profiles and onboarding; service orders/invoices/task boards; courses/lessons/lectures/resources/enrolments; transactions, payouts, affiliates.
- Student: profile/onboarding, consultations, service orders/tasks and transactions, events/services, course catalogue/enrolment/progress.
- Consultant: consultation schedule and assigned work/appointments; consultant role is called “consulter” in portions of the code.
- Affiliate: referral/commission and payout operations.

## View inventory

There are 260 Blade templates. Major directories include admin, frontend, student, consultant, affiliate, auth, shared layouts/components, and the bundled installer/update UI. This is server-rendered Blade rather than a separate SPA frontend. Navigation and role-specific menu/layouts should be preserved/reworked in place during redesign rather than replacing route behavior wholesale.

## M00 verified HTTP checks

- `GET /` → 200
- `GET /universities-list` → 200 (no catalogue rows in DB)
- `GET /consultations` → 200
- `GET /admin/dashboard` unauthenticated → 302 `/login`
- `GET /student/dashboard` unauthenticated → 302 `/login`

The screenshots supplied by the user should guide later tab-level UX: responsive university discovery/filter cards; consultation calendar/time slots; premium public landing page; navy/cyan/white/ice-blue design system. The actual logo asset must come from the original GenZ source, not the StudyLifter logo or an invented replacement.
