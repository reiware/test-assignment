#!/bin/bash
set -e

cd /var/www/html

if [ "$WAIT_FOR_SETUP" = "true" ]; then
    echo "Waiting for application setup..."

    until [ -f vendor/autoload.php ] && [ -f public/build/manifest.json ]; do
        sleep 2
    done
fi

if [ "$RUN_SETUP" = "true" ]; then
    if [ ! -f vendor/autoload.php ]; then
        echo "Installing Composer dependencies..."
        composer install --no-interaction
    fi

    if [ ! -d node_modules ]; then
        echo "Installing NPM dependencies..."
        npm install
    fi

    if [ ! -f public/build/manifest.json ]; then
        echo "Building frontend assets..."
        npm run build
    fi
fi

mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/app/public
mkdir -p bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

if [ "$RUN_SETUP" = "true" ] && [ -f .env ]; then
    APP_KEY_VALUE=$(grep '^APP_KEY=' .env | cut -d '=' -f2- || true)

    if [ -z "$APP_KEY_VALUE" ]; then
        echo "Generating application key..."
        php artisan key:generate --force
    fi
fi

if [ "$RUN_MIGRATIONS" = "true" ]; then
    until nc -z -w30 "${DB_HOST:-mysql}" 3306; do
        echo "Waiting for database connection..."
        sleep 5
    done

    echo "Running migrations..."
    php artisan migrate --force
fi

exec "$@"
