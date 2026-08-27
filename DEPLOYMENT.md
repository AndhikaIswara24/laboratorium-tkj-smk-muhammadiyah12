# Deployment guide — Laravel + Flask Naive Bayes API

This document describes steps to deploy the application to a fresh VPS (Ubuntu/Debian recommended).

Prerequisites:
- A non-root user with sudo
- Firewall allowing ports 22 (SSH), 80/443 (HTTP/HTTPS) and loopback access for internal services

1) System packages

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y python3 python3-venv python3-pip build-essential git nginx mysql-server \
    php php-fpm php-mbstring php-xml php-zip php-mysql php-cli unzip curl supervisor
```

2) MySQL
- Secure MySQL (mysql_secure_installation) and create a database and user for the app.
- Apply migrations with `php artisan migrate --force` after setting `.env.production`.

3) PHP / Composer

```bash
# As deploy user
cd /var/www/your-app
git clone ... .
composer install --no-dev --optimize-autoloader
cp .env.production .env
php artisan key:generate --show    # run locally then set APP_KEY in .env.production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

4) Node / frontend assets

```bash
npm ci
npm run build
# Copy public/build to webroot (already under public/build if using Vite)
```

5) Python / Flask API

```bash
cd /var/www/your-app/flask_api
python3 -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt
# Ensure FLASK_API_KEY and CORS_ALLOWED_ORIGIN are set in .env.production or in systemd EnvironmentFile
systemctl daemon-reload
sudo cp deploy/flask_naive_bayes.service /etc/systemd/system/flask_naive_bayes.service
sudo systemctl enable --now flask_naive_bayes.service
```

Notes:
- The Flask service template uses `gunicorn` to serve the WSGI app. Adjust the `ExecStart` if you prefer `uvicorn` or another runner.
- Make sure `FLASK_API_KEY` in Laravel `.env.production` matches the one in the Flask service environment.

6) Nginx configuration
- Proxy requests to PHP-FPM for Laravel and keep Flask on 127.0.0.1:5000 only accessible from the host.
- Example Nginx server block is out of scope here but standard Laravel Nginx config applies.

7) SSL (Let's Encrypt)

```bash
sudo snap install core; sudo snap refresh core
sudo snap install --classic certbot
sudo certbot --nginx -d your-production-domain.example
```

8) Supervisor/systemd for queue worker
- Copy `deploy/laravel_queue_worker.service` to `/etc/systemd/system/` and `systemctl enable --now laravel_queue_worker.service`.

9) Backups
- Setup cron using `mysqldump` to daily rotate backups to offsite or another disk.

Example cron (daily at 02:00):

```cron
0 2 * * * /usr/bin/mysqldump -u DB_USER -p'PASSWORD' DB_NAME | gzip > /var/backups/db_name-$(date +\%F).sql.gz
```

10) Environment variables checklist (required in `.env.production`):
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY` (generated via `php artisan key:generate --show`)
- `APP_URL` (https://...)
- DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
- `FLASK_API_URL` (if Flask is on same server, use http://127.0.0.1:5000)
- `FLASK_API_KEY` (random long secret)
- `CORS_ALLOWED_ORIGIN` (Laravel origin)
- Mail SMTP settings if needed

Security checklist reminders:
- Never commit `.env.production` to git.
- Ensure `.env` is in `.gitignore`.
- Use strong random secrets for `APP_KEY` and `FLASK_API_KEY`.

