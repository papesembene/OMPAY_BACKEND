#!/bin/sh

# On tente la connexion à la base avant de lancer les commandes
echo " Waiting for database to be ready..."
until php -r "new PDO(getenv('DB_CONNECTION') . ':host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));" 2>/dev/null; do
  echo "Database not ready yet, retrying in 3s..."
  sleep 3
done

echo " Database connection established!"

# Caches Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migration + Passport install (protégés par conditions)
php artisan migrate --force || echo " Migration failed (probably already done)"
php artisan passport:keys || php artisan passport:install --force

# Lance PHP-FPM
php-fpm
