#!/bin/sh
set -e

if [ "$1" = "apache2-foreground" ]; then
    echo "Waiting for database ${DB_HOST:-127.0.0.1}:${DB_PORT:-3306}..."
    i=0
    until php -r 'new PDO(sprintf("mysql:host=%s;port=%s;dbname=%s", getenv("DB_HOST"), getenv("DB_PORT") ?: 3306, getenv("DB_DATABASE")), getenv("DB_USERNAME"), getenv("DB_PASSWORD"));' 2>/dev/null; do
        i=$((i + 1))
        if [ "$i" -ge 60 ]; then
            echo "Database not reachable after 60s, giving up" >&2
            exit 1
        fi
        sleep 1
    done

    runuser -u www-data -- php artisan migrate --force
    runuser -u www-data -- php artisan optimize
fi

exec "$@"
