# Étape 1 : Build
FROM composer:2.6 AS build
WORKDIR /app
COPY composer.* ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Étape 2 : Runtime (PHP-FPM pur)
FROM php:8.3-fpm-alpine

# Extensions
RUN apk add --no-cache postgresql-client \
    && docker-php-ext-install pdo pdo_pgsql

# Copier code
WORKDIR /var/www/html
COPY --from=build /app/vendor ./vendor
COPY . .

# Permissions Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Script démarrage
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Exposer port FPM (Render redirige 80 → 9000)
EXPOSE 9000

CMD ["/start.sh"]