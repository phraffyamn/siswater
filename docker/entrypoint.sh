#!/bin/sh
set -e

# =========================================================================
# SISWA-TER container entrypoint
# Prepares persistent storage, runs migrations/seed, caches config, then
# hands control to FrankenPHP.
# =========================================================================

# Listen on the port the platform provides (Railway sets $PORT). Fallback 80.
export SERVER_NAME=":${PORT:-80}"

# --- Persistent storage on the mounted volume (Railway volume at /data) ---
# Uploaded warkah files live on the volume so they survive redeploys.
if [ -d /data ]; then
    mkdir -p /data/uploads
    rm -rf /app/storage/app/public
    ln -sfn /data/uploads /app/storage/app/public
    chown -R www-data:www-data /data 2>/dev/null || true
fi

# Ensure Laravel runtime directories exist
mkdir -p \
    storage/framework/cache \
    storage/framework/views \
    storage/framework/sessions \
    storage/logs \
    bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# Ensure the SQLite database file exists (path from DB_DATABASE, e.g. /data/database.sqlite)
DB_FILE="${DB_DATABASE:-/app/database/database.sqlite}"
mkdir -p "$(dirname "$DB_FILE")"
[ -f "$DB_FILE" ] || touch "$DB_FILE"
chown www-data:www-data "$DB_FILE" 2>/dev/null || true

# Public storage symlink (public/storage -> storage/app/public -> /data/uploads)
php artisan storage:link --force >/dev/null 2>&1 || true

# Database migrations
php artisan migrate --force

# Seed demo accounts/data only when the database is empty
USER_COUNT="$(php artisan tinker --execute='echo \App\Models\User::count();' 2>/dev/null | tail -n1 | tr -cd '0-9')"
if [ -z "$USER_COUNT" ] || [ "$USER_COUNT" = "0" ]; then
    echo "Database kosong — menjalankan seeder awal..."
    php artisan db:seed --force || true
fi

# Cache config / routes / views for performance
php artisan optimize || true

exec "$@"
