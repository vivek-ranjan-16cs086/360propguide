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

Every push to `main` runs PHP tests, uploads code to Hostinger over SFTP, then runs `composer install --no-dev --optimize-autoloader` on the server so new PHP packages are installed on live. Node/npm is not used.

### Required GitHub secrets

In the GitHub repo go to **Settings → Secrets and variables → Actions → Secrets** (not the Variables tab) and add:

| Secret | Where to find it |
| --- | --- |
| `FTP_SERVER` | Hostinger hPanel → **Files → FTP Accounts** (hostname, often `ftp.360propguide.com`) |
| `FTP_USERNAME` | Same FTP account username |
| `FTP_PASSWORD` | Same FTP account password |
| `FTP_SERVER_DIR` | Optional. Defaults to `/public_html/` |
| `SSH_HOST` | Optional. Defaults to `FTP_SERVER`. Enable SSH in Hostinger hPanel → **Advanced → SSH Access** |
| `SSH_PORT` | Optional. Defaults to `65002` (Hostinger) |
| `SSH_USERNAME` / `SSH_PASSWORD` | Optional. Defaults to the FTP username and password |

The workflow also reads these names from **Variables** if Secrets are empty. Prefer **Secrets** for the password so it is not visible in the settings UI.

After the secrets are saved, run **Actions → CI/CD → Run workflow**, or push another commit to `main`.

The deploy job does **not** overwrite `.env`, Firebase credentials, or files under `storage/app/public` (project images, videos, and brochures already on the server).
