# ==========================================
# ETAPE 1 : CONSTRUCTION DU FRONTEND
# ==========================================

FROM node:22-bookworm-slim AS frontend

WORKDIR /app

COPY package*.json ./

RUN npm install --no-audit --no-fund

COPY . .

RUN npm run build


# ==========================================
# ETAPE 2 : ENVIRONNEMENT PHP
# ==========================================

FROM php:8.3-apache

WORKDIR /var/www/html

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    && rm -rf /var/lib/apt/lists/*

# Configuration GD
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg

# Installation des extensions PHP
RUN docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    bcmath \
    exif \
    pcntl \
    gd \
    intl \
    zip \
    opcache \
    xml \
    curl

# Activation des modules Apache
RUN a2enmod rewrite headers

# ==========================================
# CONFIGURATION APACHE
# ==========================================

# Laravel doit être servi depuis /public
RUN sed -ri \
    's!/var/www/html!/var/www/html/public!g' \
    /etc/apache2/sites-available/*.conf

# Configuration des permissions Apache
RUN printf '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n' >> /etc/apache2/apache2.conf

# Configuration du port Render
RUN sed -i 's/Listen 80/Listen 10000/' \
    /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/' \
    /etc/apache2/sites-available/000-default.conf

EXPOSE 10000


# ==========================================
# INSTALLATION DE COMPOSER
# ==========================================

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


# ==========================================
# COPIE DU PROJET LARAVEL
# ==========================================

COPY . .

# Installation des dépendances PHP
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist


# ==========================================
# COPIE DES FICHIERS VITE
# ==========================================

COPY --from=frontend /app/public/build ./public/build


# ==========================================
# PERMISSIONS LARAVEL
# ==========================================

RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache


# ==========================================
# CONFIGURATION PRODUCTION
# ==========================================

ENV APP_ENV=production

CMD ["apache2-foreground"]
