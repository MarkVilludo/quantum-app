# Hostinger shared: `quantumrealm.tech`

Recommended layout (git clone or rsync):

```text
~/domains/quantumrealm.tech/
├── quantum-app/            ← Laravel root (git clone here)
└── public_html/            ← Document root (contents of public/)
```

Hostinger also creates `DO_NOT_UPLOAD_HERE/` on new sites; this app supports **either** `quantum-app` or `DO_NOT_UPLOAD_HERE` as the Laravel folder name.

Do **not** put `app/`, `.env`, or `vendor/` inside `public_html`.

## Vite / `public_html` split

The web server serves `public_html/`, but Laravel’s default `public/` path is inside `quantum-app/public/`. After you copy assets to `public_html`, **`HostingerPaths`** (in `AppServiceProvider`) points Laravel at sibling `../public_html` so Vite reads `public_html/build/manifest.json`.

You do **not** need a duplicate `build/` under `quantum-app/public/` when that code is deployed. Remove the extra top-level `~/domains/quantumrealm.tech/public/` folder if you created one by mistake—it is not used.

## Clone on the server

```bash
cd ~/domains/quantumrealm.tech
git clone https://github.com/MarkVilludo/quantum-app.git quantum-app
cp -a quantum-app/public/. public_html/
cp quantum-app/deploy/hostinger/public_html/index.php public_html/index.php
```

## First-time setup (SSH)

```bash
cd ~/domains/quantumrealm.tech/quantum-app
composer install --optimize-autoloader --no-interaction
cp deploy/hostinger/.env.production.example .env
nano .env   # DB + mail from hPanel
php artisan key:generate
chmod -R ug+rwx storage bootstrap/cache
php artisan migrate --force
php artisan storage:link   # link targets public_html when HostingerPaths is active
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

Use **MySQL** from hPanel (not SQLite). Run `composer install` **with** dev dependencies on shared hosting so Artisan has `fakerphp/faker` (avoid `--no-dev` unless you add Faker to production deps).

Set **PHP 8.1+** in hPanel → **PHP Configuration**.

## Android app (`QuantumRealm`)

In `QuantumRealm/gradle.properties`:

```properties
API_BASE_URL=https://quantumrealm.tech
```

Rebuild and reinstall the APK. Requests go to `https://quantumrealm.tech/api/...`. For local Laravel dev, switch back to `http://10.0.2.2:8000` (emulator) or your PC LAN IP.

Ensure server `.env` has `APP_URL=https://quantumrealm.tech` and `CMS_URL=https://quantumrealm.tech` so password-reset and verification links match the app base URL.

## After `git pull` on the server

```bash
cd ~/domains/quantumrealm.tech/quantum-app
git pull
composer install --optimize-autoloader --no-interaction
cp -a public/. ../public_html/
cp deploy/hostinger/public_html/index.php ../public_html/index.php
php artisan migrate --force
php artisan config:clear
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

If `quantum-app/public/*` was deleted locally after copying to `public_html`, restore tracked files without wiping the server web root:

```bash
git restore public/
cp -a public/. ../public_html/
```

## Deploy from your PC (Git Bash)

```bash
chmod +x deploy/hostinger/deploy.sh
./deploy/hostinger/deploy.sh
```

Optional:

```bash
export HOSTINGER_SSH=u169091875@46.28.45.172
export HOSTINGER_SSH_PORT=65002
export HOSTINGER_REMOTE=domains/quantumrealm.tech
export HOSTINGER_APP_DIR=domains/quantumrealm.tech/quantum-app
```

**Windows:** Stop `npm run dev` before building. Use `--skip-build` if `public/build` is already fresh.

## hPanel checklist

- [ ] SSL for `quantumrealm.tech`
- [ ] PHP 8.1+
- [ ] MySQL in `.env`
- [ ] Cron (optional): `* * * * * php ~/domains/quantumrealm.tech/quantum-app/artisan schedule:run`
