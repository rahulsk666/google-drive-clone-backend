#!/bin/sh

cd /var/www/html

# Wait for PostgreSQL to be ready
until pg_isready -h pgsql -U "$DB_USERNAME" -d "$DB_DATABASE"; do
  echo "Waiting for PostgreSQL to be ready..."
  sleep 2
done

# Install Composer dependencies if vendor directory doesn't exist
if [ ! -d "vendor" ]; then
  echo "Installing Composer dependencies..."
  composer install --no-interaction --optimize-autoloader
fi

# Set appropriate permissions
chmod -R 777 storage bootstrap/cache

# Run Laravel migrations
php artisan migrate --force

# Start PHP-FPM
exec php-fpm
