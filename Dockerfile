# Étape 1 : Build
FROM composer:2.6 AS build
WORKDIR /app
COPY composer.* ./
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-req=ext-gd

# Étape 2 : Runtime
FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    postgresql-client \
    postgresql-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install pdo pdo_pgsql gd \
  && apk del postgresql-dev libpng-dev libjpeg-turbo-dev freetype-dev

WORKDIR /var/www/html
COPY --from=build /app /var/www/html

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 10000
CMD ["/start.sh"]
