#!/bin/sh

cd /var/www/html

# Wait for PostgreSQL to be ready
until pg_isready -h pgsql -U "$DB_USERNAME" -d "$DB_DATABASE"; do
  echo "Waiting for PostgreSQL to be ready..."
  sleep 2
done

# Run Laravel migrations
php artisan migrate --force

# Start PHP-FPM
exec php-fpm
