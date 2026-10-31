#!/usr/bin/env bash
# Run on Hostinger via SSH after hPanel Git redeploy or git pull.
# Usage: ./deploy/hostinger/post-deploy.sh
set -euo pipefail

DOMAIN="${HOSTINGER_DOMAIN_DIR:-$HOME/domains/quantumrealm.tech}"
APP="${HOSTINGER_APP_DIR:-$DOMAIN/public_html/quantum-app}"
PUBLIC="${HOSTINGER_PUBLIC_DIR:-$DOMAIN/public_html}"

if [[ ! -f "$APP/artisan" ]]; then
  echo "Laravel app not found at: $APP"
  echo "Set HOSTINGER_APP_DIR if your path differs."
  exit 1
fi

cd "$APP"

echo "==> composer install"
composer install --optimize-autoloader --no-interaction

echo "==> sync public/ → public_html"
cp -a public/. "$PUBLIC/"
cp deploy/hostinger/public_html/index.php "$PUBLIC/index.php"

if [[ ! -f .env ]]; then
  echo "==> creating .env from example (edit before production use)"
  cp deploy/hostinger/.env.production.example .env
  php artisan key:generate
fi

chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

echo "==> migrate & cache"
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Done. Test: https://quantumrealm.tech/"
