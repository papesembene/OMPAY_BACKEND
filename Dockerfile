# Étape 1 : Build
FROM composer:2.6 AS build
WORKDIR /app
COPY composer.* ./
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Étape 2 : Runtime
FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    postgresql-client \
    postgresql-dev \
  && docker-php-ext-install pdo pdo_pgsql \
  && apk del postgresql-dev

WORKDIR /var/www/html
COPY --from=build /app /var/www/html

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 9000
CMD ["/start.sh"]
