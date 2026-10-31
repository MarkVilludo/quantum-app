# Hostinger shared: `quantumrealm.tech`

## Layouts (both supported)

**A — hPanel Git** (subfolder `quantum-app` under `public_html`):

```text
~/domains/quantumrealm.tech/public_html/
├── index.php              ← from deploy/hostinger/public_html/index.php
├── .htaccess, build/, …   ← copied from app public/
└── quantum-app/           ← Git deploy target (artisan, app/, .env)
```

**B — SSH clone / rsync** (app beside `public_html`):

```text
~/domains/quantumrealm.tech/
├── quantum-app/           ← Laravel root
└── public_html/           ← contents of public/ + index.php
```

Do **not** deploy the whole repo into `public_html/` with an empty subfolder — that causes **403**.

## hPanel Git deploy

1. **Websites → quantumrealm.tech → Advanced → GIT**
2. **Change root directory → Add subfolder:** `quantum-app` (not blank)
3. **Redeploy** after each push to `main`

Git does **not** run Composer or copy `public/` to the web root. After every redeploy, SSH:

```bash
ssh -p 65002 u169091875@46.28.45.172
cd ~/domains/quantumrealm.tech/public_html/quantum-app
chmod +x deploy/hostinger/post-deploy.sh
./deploy/hostinger/post-deploy.sh
```

Or run the same steps manually:

```bash
cd ~/domains/quantumrealm.tech/public_html/quantum-app
composer install --optimize-autoloader --no-interaction
cp -a public/. ..
cp deploy/hostinger/public_html/index.php ../index.php
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
chmod -R ug+rwx storage bootstrap/cache
```

Ensure **`.env`** exists in `public_html/quantum-app/` (MySQL from hPanel, `APP_URL=https://quantumrealm.tech`).

## Vite / `public_html`

**HostingerPaths** points Laravel at the real document root (`public_html`) so Vite uses `public_html/build/manifest.json`.

## First-time `.env`

```bash
cd ~/domains/quantumrealm.tech/public_html/quantum-app
cp deploy/hostinger/.env.production.example .env
nano .env
php artisan key:generate
```

Use **MySQL** (not SQLite). PHP **8.1+** in hPanel.

## Android app

`API_BASE_URL=https://quantumrealm.tech` in `QuantumRealm/gradle.properties`. Server `.env`: `APP_URL` and `CMS_URL` = `https://quantumrealm.tech`.

## Deploy from your PC (rsync layout B)

```bash
export HOSTINGER_APP_DIR=domains/quantumrealm.tech/quantum-app
./deploy/hostinger/deploy.sh
```

For Git layout A, prefer hPanel redeploy + `post-deploy.sh` on the server.

## hPanel checklist

- [ ] Git subfolder = `quantum-app` (not blank `public_html`)
- [ ] SSL, PHP 8.1+, MySQL in `.env`
- [ ] APK: Admin → Android app or `php artisan mobile-app:import-apk`
