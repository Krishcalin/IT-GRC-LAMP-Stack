#!/bin/bash
set -e
cd /var/www/html

# Ensure an environment file exists
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Generate an app key on first boot
if ! grep -q "^APP_KEY=base64:" .env; then
    php artisan key:generate --force
fi

# Wait for the database to accept connections
echo "Waiting for MySQL at ${DB_HOST:-db}:${DB_PORT:-3306} ..."
until php -r "try { new PDO('mysql:host='.getenv('DB_HOST').';port='.getenv('DB_PORT'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0);} catch (Throwable \$e) { exit(1);}" 2>/dev/null; do
    sleep 2
done
echo "MySQL is up."

# Apply migrations + seed (idempotent seeders)
php artisan migrate --force --seed
php artisan storage:link || true

exec "$@"
