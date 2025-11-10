# ----------------------------
# Étape 1 : Build
# ----------------------------
FROM composer:2.6 AS build

WORKDIR /app

# Copier le projet complet pour permettre l'exécution des scripts post-install
COPY . .

# Installer les dépendances PHP sans les dev
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-req=ext-gd

# ----------------------------
# Étape 2 : Runtime
# ----------------------------
FROM php:8.3-fpm-alpine

# Installer les dépendances système nécessaires pour GD, PostgreSQL et Imagick
RUN apk add --no-cache \
    postgresql-client \
    postgresql-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    imagemagick \
    imagemagick-dev \
    autoconf \
    make \
    bash \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install pdo pdo_pgsql gd \
  && pecl install imagick \
  && docker-php-ext-enable imagick \
  && apk del postgresql-dev libpng-dev libjpeg-turbo-dev freetype-dev imagemagick-dev autoconf make

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers du build
COPY --from=build /app /var/www/html

# Permissions Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copier et rendre exécutable le start script
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Exposer le port utilisé par PHP-FPM
EXPOSE 10000

# Commande de démarrage
CMD ["/start.sh"]
