# Wasmer production / Render staging

Use this directory as the repository root. Do not push the parent workspace, SQL exports, `.env` files, or ZIP backups.

## Wasmer production

1. Replace `REPLACE_WITH_YOUR_WASMER_USERNAME` in `app.yaml` with the Wasmer account/namespace that will own the app.
2. Connect this GitHub repository to a Wasmer Edge app and deploy its production branch. Wasmer detects the Laravel app and installs the locked Composer dependencies.
3. In Wasmer's secret settings, set a fresh Laravel-compatible `APP_KEY`. Do not put it in Git or `app.yaml`. Set `APP_URL` to the assigned Wasmer URL after the app is created.
4. Import `F:\VPS\demo.sql` into the Wasmer-provisioned MySQL database. Wasmer's `DB_NAME` is supported by the Laravel MySQL config fallback in this release.

## Render staging

1. Connect the same GitHub repository to Render and create the Blueprint from `render.yaml`.
2. The Blueprint creates its own private MySQL service and a separate staging web service. Render generates independent database passwords; provide a fresh Laravel-compatible `APP_KEY` when prompted.
3. Import a separate copy of `F:\VPS\demo.sql` into `studylifter_staging`. Never point staging at the Wasmer production database.

The Blueprint deliberately does not run migrations on deploy. The dump contains 79 table definitions while the app has 100 migration files; those counts alone do not establish compatibility. Import and test the dump in staging, then review pending migrations before running any against production. Render disks are included for the MySQL data and Laravel storage; Render requires paid services for persistent disks, so review the dashboard's costs before creating the Blueprint.

After importing the demo-named SQL file, change any default/demo administrator password before making the production app public. Generate a Laravel key locally with PowerShell (do not paste it into chat):

```powershell
$bytes = New-Object byte[] 32
$rng = [System.Security.Cryptography.RandomNumberGenerator]::Create()
$rng.GetBytes($bytes)
"base64:$([Convert]::ToBase64String($bytes))"
$rng.Dispose()
```

Use separate keys for production and staging, and enter them only in the platform's secret settings.

The SQL export is not copied into this repository. Keep it backed up locally and import it only through the target platform's database connection.
