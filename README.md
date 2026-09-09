# 360propguide

Laravel 10 property portal for [360propguide.com](https://www.360propguide.com/).

## Local setup

1. Copy `.env.example` to `.env` and set your local database credentials.
2. Install dependencies and generate an app key:

```bash
composer install
php artisan key:generate
php artisan migrate
```

3. Serve the app with `php artisan serve`.

Do not commit `.env`. Production secrets stay on the Hostinger server.

## GitHub Actions CI/CD

Every push to `main` runs PHP tests, then uploads code to Hostinger over FTP on port 21. Node/npm is not used.

### Required GitHub secrets

In the GitHub repo go to **Settings → Secrets and variables → Actions → Secrets** (not the Variables tab) and add:

| Secret | Value |
| --- | --- |
| `FTP_SERVER` | `ftp.360propguide.com` |
| `FTP_USERNAME` | Hostinger FTP username |
| `FTP_PASSWORD` | Hostinger FTP password |
| `FTP_PORT` | `21` |
| `FTP_SERVER_DIR` | Do not set this to `public_html`. The FTP user already opens inside `public_html`, so the workflow uploads to `/`. |
| `SSH_HOST` | Optional. Only if you enable SSH in hPanel → **Advanced → SSH Access**. Needed to auto-run `composer install` on live. |

The workflow also reads these names from **Variables** if Secrets are empty. Prefer **Secrets** for the password so it is not visible in the settings UI.

After the secrets are saved, run **Actions → CI/CD → Run workflow**, or push another commit to `main`.

The deploy job does **not** overwrite `.env`, Firebase credentials, or files under `storage/app/public` (project images, videos, and brochures already on the server).
