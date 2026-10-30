# Hostinger shared: `quantumrealm.tech`

Hostinger puts each domain under your home directory like this:

```text
~/domains/quantumrealm.tech/
├── DO_NOT_UPLOAD_HERE/     ← Laravel application (not web-accessible)
└── public_html/            ← Document root (only this is served)
```

This repo’s `deploy/hostinger/` files match that layout so uploads stay predictable.

## Target layout on the server

| Local (quantum-app) | Remote |
|---------------------|--------|
| Everything except `public/` | `~/domains/quantumrealm.tech/DO_NOT_UPLOAD_HERE/` |
| `public/*` | `~/domains/quantumrealm.tech/public_html/` |
| `deploy/hostinger/public_html/index.php` | `~/domains/quantumrealm.tech/public_html/index.php` |

Do **not** put `app/`, `.env`, or `vendor/` inside `public_html`.

## First-time setup (SSH)

```bash
cd ~/domains/quantumrealm.tech/DO_NOT_UPLOAD_HERE
```

1. Copy environment file (after first deploy, or create manually):

   ```bash
   cp deploy/hostinger/.env.production.example .env
   nano .env   # DB + mail from hPanel; set APP_KEY via artisan
   php artisan key:generate
   ```

2. Create **MySQL** in hPanel → **Databases** and put credentials in `.env` (use MySQL on shared hosting, not SQLite).

3. Permissions:

   ```bash
   chmod -R ug+rwx storage bootstrap/cache
   ```

4. Migrate and cache:

   ```bash
   php artisan migrate --force
   php artisan storage:link
   ```

   If `storage:link` creates the link under `DO_NOT_UPLOAD_HERE/public/storage`, expose uploads in the web root:

   ```bash
   ln -sfn ../DO_NOT_UPLOAD_HERE/storage/app/public ~/domains/quantumrealm.tech/public_html/storage
   ```

5. Optimize:

   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

Set **PHP version** to 8.1+ in hPanel → **Advanced** → **PHP Configuration** for this domain.

## Deploy from your PC (Git Bash)

From `quantum-app/`:

```bash
chmod +x deploy/hostinger/deploy.sh
./deploy/hostinger/deploy.sh
```

Optional env overrides:

```bash
export HOSTINGER_SSH=u169091875@46.28.45.172
export HOSTINGER_SSH_PORT=65002
export HOSTINGER_REMOTE=domains/quantumrealm.tech
```

Requires `rsync` and `ssh` (Git Bash on Windows). Use `--skip-build` if assets are already built.

After each deploy, on the server:

```bash
cd ~/domains/quantumrealm.tech/DO_NOT_UPLOAD_HERE
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

## Manual upload (File Manager / FTP)

1. Zip the project locally; exclude `node_modules`, `.git`, `.env`, and `database/database.sqlite`.
2. Upload and extract into `DO_NOT_UPLOAD_HERE` (not into `public_html`).
3. Upload contents of `public/` into `public_html`.
4. Replace `public_html/index.php` with `deploy/hostinger/public_html/index.php`.
5. In hPanel **Terminal** or SSH: `composer install --no-dev` inside `DO_NOT_UPLOAD_HERE`, then steps in **First-time setup**.

## hPanel checklist

- [ ] SSL enabled for `quantumrealm.tech`
- [ ] PHP 8.1+
- [ ] MySQL database + user linked to `.env`
- [ ] Cron (optional queues): `* * * * * php ~/domains/quantumrealm.tech/DO_NOT_UPLOAD_HERE/artisan schedule:run`
