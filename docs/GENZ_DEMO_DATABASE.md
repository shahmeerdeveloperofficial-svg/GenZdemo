# Dedicated demo database setup

This project adds fictional sample records for the Lead CRM, student profiles and documents, study-abroad catalogue, admissions, consultation bookings, course progress, live sessions, quizzes, assignments and grading, certificates, notifications, and student registration.

Use a new, dedicated database for the demo. Do not run the demo seeder against a database containing real student records. The seed command refuses to run unless the selected database name matches `GENZ_DEMO_DATABASE` (default: `genz_crm_lms_demo`).

Demo seeding is additive: it creates separate example accounts for all eight application roles (Admin, Student, Staff, Consultant, Affiliate, Instructor, Finance, and CMS Editor). It does not reset existing user passwords, edit existing users or role assignments, or replace existing site settings, currencies, or languages. Existing accounts that use the reserved demo email addresses are reused without changing their profile or password. If an existing password is weak, rotate it separately through the account/security process.

## 1. Create the isolated MariaDB database

On the VPS, create a database and a restricted application account. Use a unique password stored in your password manager; do not paste it into chat or commit it to source control.

```sql
CREATE DATABASE genz_crm_lms_demo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'genz_app'@'localhost' IDENTIFIED BY 'REPLACE_WITH_A_UNIQUE_PASSWORD';
GRANT ALL PRIVILEGES ON genz_crm_lms_demo.* TO 'genz_app'@'localhost';
FLUSH PRIVILEGES;
```

Set the Laravel `.env` values to this database and account:

```dotenv
DB_CONNECTION=mysql
DB_DATABASE=genz_crm_lms_demo
DB_USERNAME=genz_app
DB_PASSWORD=REPLACE_WITH_A_UNIQUE_PASSWORD
GENZ_DEMO_DATABASE=genz_crm_lms_demo
GENZ_DEMO_SEEDING=false
```

Keep the production `APP_KEY` stable and private. Do not use the local validation key.

## 2. Finish the web installer, then prepare the demo

Complete the normal installer first. It imports the packaged legacy SQL after its initial migrations, so the new command reapplies any missing migrations and permissions before adding sample data.

From the Laravel project directory, run:

```bash
php artisan config:clear
php artisan genz:prepare-demo --force
```

The command is intentionally limited to the dedicated database. It does not run `migrate:fresh`, send email, or collect payments. Do not substitute `migrate:fresh` on a live or installed database.

## 3. Secure demo accounts and review public examples

New demo accounts receive random one-time passwords. The command writes them to `storage/app/private/genz-demo-credentials.json`, outside the public web root. Retrieve that file through a private server channel, change the passwords before handoff, and then securely remove the file. Never publish it or put it in a ZIP.

All names, admissions outcomes, document notes, assessments, registration details, and notifications are fictional. Demo course certificates are deliberately marked revoked. Sample programme, event, and service content is illustrative—not an approved offer, live tuition figure, funding promise, real appointment, or payment request. Replace or disable public demo content before accepting genuine applications.

For a real-data deployment, create/use a separate database and do not run `genz:prepare-demo` there.
