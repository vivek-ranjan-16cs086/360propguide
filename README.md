# 360propguide

Laravel 10 property portal for [360propguide.com](https://www.360propguide.com/).

## Local setup

1. Copy `.env.example` to `.env` and set your local database credentials.
2. Install dependencies and generate an app key:

```bash
composer install
npm install
php artisan key:generate
php artisan migrate
npm run dev
```

3. Serve the app with `php artisan serve`.

Do not commit `.env`. Production secrets stay on the Hostinger server.

## GitHub Actions CI/CD

Every push to `main` runs tests and a frontend build, then deploys to Hostinger over FTPS so [www.360propguide.com](https://www.360propguide.com/) stays up to date.

### Required GitHub secrets

In the GitHub repo go to **Settings → Secrets and variables → Actions** and add:

| Secret | Where to find it |
| --- | --- |
| `FTP_SERVER` | Hostinger hPanel → **Files → FTP Accounts** (hostname, often `ftp.360propguide.com`) |
| `FTP_USERNAME` | Same FTP account username |
| `FTP_PASSWORD` | Same FTP account password |
| `FTP_SERVER_DIR` | Optional. Defaults to `/public_html/` |

After the secrets are saved, run **Actions → CI/CD → Run workflow**, or push another commit to `main`.

The deploy job does **not** overwrite `.env`, Firebase credentials, or files under `storage/app/public` (project images, videos, and brochures already on the server).
