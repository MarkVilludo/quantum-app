#!/usr/bin/env bash
# Deploy quantum-app to Hostinger shared (Git Bash / WSL / macOS / Linux).
# Usage: ./deploy/hostinger/deploy.sh [--skip-build|--fresh-node]
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
cd "$ROOT"

SSH_HOST="${HOSTINGER_SSH:-u169091875@46.28.45.172}"
SSH_PORT="${HOSTINGER_SSH_PORT:-65002}"
REMOTE_BASE="${HOSTINGER_REMOTE:-domains/quantumrealm.tech}"
REMOTE_APP="${HOSTINGER_APP_DIR:-${REMOTE_BASE}/public_html/quantum-app}"
REMOTE_PUBLIC="${REMOTE_BASE}/public_html"

RSYNC_SSH="ssh -p ${SSH_PORT} -o StrictHostKeyChecking=accept-new"

build_frontend() {
  if [[ ! -d node_modules ]]; then
    echo "==> npm install (no node_modules yet)"
    npm install
  fi
  echo "==> npm run build"
  npm run build
}

case "${1:-}" in
  --skip-build) ;;
  --fresh-node)
    echo "==> npm ci (stop npm run dev / close editors first on Windows)"
    npm ci
    npm run build
    ;;
  *)
    build_frontend
    ;;
esac

echo "==> composer install"
composer install --optimize-autoloader --no-interaction

echo "==> rsync app → ${REMOTE_APP}"
rsync -avz --delete -e "$RSYNC_SSH" \
  --exclude '.git/' \
  --exclude 'node_modules/' \
  --exclude 'public/' \
  --exclude 'tests/' \
  --exclude '.env' \
  --exclude '.env.*' \
  --exclude 'database/database.sqlite' \
  --exclude 'storage/logs/*' \
  --exclude 'storage/framework/cache/data/*' \
  --exclude 'storage/framework/sessions/*' \
  --exclude 'storage/framework/views/*' \
  --exclude '.phpunit.cache' \
  --exclude 'deploy/' \
  "$ROOT/" "${SSH_HOST}:${REMOTE_APP}/"

echo "==> rsync public → public_html"
rsync -avz --delete -e "$RSYNC_SSH" \
  "$ROOT/public/" "${SSH_HOST}:${REMOTE_PUBLIC}/"

echo "==> install Hostinger index.php + deploy notes on server"
rsync -avz -e "$RSYNC_SSH" \
  "$ROOT/deploy/hostinger/public_html/index.php" \
  "${SSH_HOST}:${REMOTE_PUBLIC}/index.php"
rsync -avz -e "$RSYNC_SSH" \
  "$ROOT/deploy/hostinger/.env.production.example" \
  "$ROOT/deploy/hostinger/README.md" \
  "${SSH_HOST}:${REMOTE_APP}/deploy/hostinger/"

echo ""
echo "Done. On the server, run:"
echo "  ssh -p ${SSH_PORT} ${SSH_HOST}"
echo "  cd ~/${REMOTE_APP}"
echo "  cp deploy/hostinger/.env.production.example .env   # first time: edit, then php artisan key:generate"
echo "  php artisan migrate --force"
echo "  php artisan config:cache && php artisan route:cache && php artisan view:cache"
echo "  chmod -R ug+rwx storage bootstrap/cache"
