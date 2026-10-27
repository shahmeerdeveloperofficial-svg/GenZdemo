# GenZ / StudyLifter setup

## Requirements

- PHP 8.1 or newer with the PHP extensions required by Laravel and this project (including `zip`, `fileinfo`, `mbstring`, `openssl`, `pdo_mysql`, and `gd`)
- Composer
- MySQL 8 or MariaDB
- Node.js and npm (only needed to rebuild the public React site)

## 1. Get the project and install PHP dependencies

```bash
git clone https://github.com/bilal1211-sauce/gen-soft.git
cd gen-soft
composer install
```

On Windows, run the commands in PowerShell from the project folder.

## 2. Configure the database

Create an empty MySQL database, for example `genz_app`. Copy `.env.example` to `.env` if Composer did not create it, then set these values in `.env` to match your local MySQL account:

```dotenv
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=genz_app
DB_USERNAME=root
DB_PASSWORD=

STORAGE_DRIVER=public
```

Do not commit `.env` or real passwords. If your MySQL root account has a password, put it in `DB_PASSWORD` locally.

Generate the app key and apply all schema migrations:

```bash
php artisan key:generate
php artisan config:clear
php artisan migrate
```

The migrations in `database/migrations` create/update the database schema. On a database that already has the app's tables and data, back it up first and run `php artisan migrate` to apply only migrations not yet recorded.

## 3. Set up initial app data and demo catalog

For a brand-new installation, complete the application's normal installer/initial admin setup first, so the initial admin account and required base settings exist. The demo seeder does not create, replace, or modify user accounts or roles.

Then add the sample public listings and their images:

```bash
php artisan db:seed --class=PublicCatalogDemoSeeder
php artisan db:seed --class=DemoStudentPortraitSeeder
php artisan storage:link
```

The seeder is safe to run again; it adds clearly labelled demo scholarships, courses, events and sample academic-program data where available. It leaves the existing five main accounts and role assignments alone. To restore the real project database, import your authorized SQL backup into the configured database before running the app; migrations are not a replacement for that backup's real records.

## 4. Build the public frontend (optional if using committed build files)

```bash
cd resources/site
npm ci
npm run build
cd ../..
```

## 5. Run locally

```bash
php artisan optimize:clear
php artisan serve --host=127.0.0.1 --port=8000
```

Open <http://127.0.0.1:8000>. Uploaded files use Laravel's public storage disk; keep `STORAGE_DRIVER=public` and ensure `public/storage` is linked with `php artisan storage:link`.

## Existing database / SQL backup

For the supplied project dump, create the target database first, import the `.sql` backup into it with phpMyAdmin or the MySQL client, then set the matching `DB_*` values in `.env`. Avoid importing a dump over a database containing data you need to keep. After import, run:

```bash
php artisan config:clear
php artisan migrate
php artisan storage:link
```

Only run the demo seeder if you want the additional explicitly marked sample listings.
