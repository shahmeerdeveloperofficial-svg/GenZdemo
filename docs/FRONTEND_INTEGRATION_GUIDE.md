# GenZ frontend integration

## Open the website

- Website: http://127.0.0.1:8000/
- Website editor: http://127.0.0.1:8000/admin/cms-setting/public-site
- Student study registration: http://127.0.0.1:8000/student-registration
- Account registration: http://127.0.0.1:8000/register
- Existing account login: http://127.0.0.1:8000/login

The supplied React homepage now runs through Laravel on the same website. The original folder at `E:\genz\frontend\GENZ project` is retained. Its integrated source lives in `resources/site`; the compiled assets live in `public/site`.

## Edit the website from admin

Sign in as an administrator with **Manage Cms Settings**, then open **Website & Content → Public Website Editor**.

1. Expand the group or section you want to edit.
2. Change text, headings, button labels, links, images or cards.
3. Use **Upload image / PDF** for media. An uploaded file becomes visible on the website after you save its content changes.
4. Use **Add item**, **Remove**, and the arrow buttons for repeatable lists.
5. Under **Sections**, use **Enabled** to show/hide a section. A lower **Order** number puts it higher on the page.
6. Click **Save website changes**, then refresh the public website.

CMS changes do not require an npm build.

### Content groups

| Group | Controls |
|---|---|
| Brand / Navigation | Logo, name, tagline, homepage title/description, favicon, navigation labels and destinations |
| Hero / Stats | Scroll stages, images, headings, statistics and labels |
| Platform / Welcome | Portal feature overview, introductory copy and cards |
| CEO / Leadership | Profiles, qualifications, achievements, biography text, photos and links |
| Philosophy / Benefits | Headings, icons and repeatable cards |
| Stories | Public testimonials or custom success stories |
| Campus / Admissions | Facility/activity copy, cards, images and the admissions explanation |
| Marquee / News | Scrolling values, section text and backend/custom news cards |
| Catalog | Services and country section headings and visibility |
| Call To Action | Registration/inquiry labels and an optional prospectus PDF |
| Contact / Footer | Branch addresses, phone/email, social links, footer links and newsletter text |
| Inquiry / Portal / Floating Actions | Form labels, program choices, portal labels and floating controls |

Statistics and imported editorial claims are editable website content, not calculated dashboard analytics.

## Where the data comes from

- **Services and destinations:** active records from the existing Services and Countries CMS.
- **News/events:** the three nearest upcoming active Events and three latest published active Blogs. Past events and future-dated blog posts are excluded.
- **Testimonials:** active existing Testimonials.
- **Stories / News source:** choose `backend` to use those records, or `custom` to manage cards in the website editor.
- **Additional visual sections:** the new website content document in `settings`, key `genz_public_site_content`.
- **Homepage metadata:** Brand **SEO source** defaults to **Existing SEO Settings**, so the current home SEO editor remains effective. Choose **Custom title and description below** to use the Brand page title/description instead. The favicon is controlled by Brand.
- **Branch choices for inquiries:** existing `config/genz.php` registration branches, shared with the current registration flow.

The editor provides shortcuts to existing record-management pages. Existing universities, programs, courses, consultations, policies and detail pages retain their original URLs and controllers.

## Forms and submissions

### Inquiry

The website inquiry form submits to `POST /site/inquiries`.

- A record appears in http://127.0.0.1:8000/admin/contact-us-list.
- The CRM lead appears in http://127.0.0.1:8000/admin/crm/leads with source **Website Inquiry**.
- Program, preferred branch, parent/guardian and event context are retained. The message is also stored as a lead note.
- The lead capture service deduplicates open leads by email/phone; a repeat inquiry retains the existing pipeline stage and owner.
- The CRM Branch filter supports both study registrations and website inquiry preferences.

### Newsletter

Subscriptions are stored after the visitor accepts the opt-in checkbox.

Admin list: http://127.0.0.1:8000/admin/cms-setting/public-site/subscribers

Search/filter subscribers, edit email/status, or delete a subscriber. This integration stores subscriptions; it does not send newsletters or add an email campaign service.

### Registration and login

**Student registration** opens the detailed `/student-registration` form, including its declaration and terms confirmation, fees, validations and CRM workflow. E-signature capture is not required.

**Create account** opens `/register`. **Portal** opens the existing Laravel login or the authenticated user's dashboard. The supplied frontend's simulated login has been replaced by these real account routes.

The original registration controllers, views and routes are preserved. Public form POSTs use CSRF protection, throttling and validation; configured contact reCAPTCHA is also applied to the new inquiry/newsletter forms.

## Run next time

1. Start MySQL in XAMPP.
2. Open PowerShell in `E:\genz\source-code`.
3. Run:

   ```powershell
   php artisan serve --host=127.0.0.1 --port=8000
   ```

4. Open http://127.0.0.1:8000/.

No separate React development server is required for normal use.

## Build after changing React source

```powershell
cd E:\genz\source-code\resources\site
npm ci
npm run build
```

`npm ci` is needed on first setup or after dependency changes. An ordinary source change only needs `npm run build`.

Vite generates versioned files and a manifest in `public/site`. Keep `resources/site/content/*.json` with the Laravel deployment: they define the default content and editor fields.

For a new installation, apply Laravel migrations. The integration adds `newsletter_subscribers` and widens `settings.option_value` to LONGTEXT so the full content document fits. The existing local `demo` database has these migrations applied.

Custom website uploads are stored in `public/uploads/public-site`, outside Vite's build output, so rebuilding the frontend preserves uploads. Include that folder and the database in backups.
