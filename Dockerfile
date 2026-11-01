# ==========================================
# BASE PHP 8.3 + APACHE
# ==========================================

FROM php:8.3-apache

WORKDIR /var/www/html

# ==========================================
# DEPENDANCES SYSTEME
# ==========================================

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

# ==========================================
# EXTENSIONS PHP
# ==========================================

RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg

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

# ==========================================
# COMPOSER
# ==========================================

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ==========================================
# APACHE
# ==========================================

RUN a2enmod rewrite headers

RUN sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf

RUN printf '<VirtualHost *:10000>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>\n' > /etc/apache2/sites-available/000-default.conf

EXPOSE 10000

# ==========================================
# COPIE DU PROJET
# ==========================================

COPY . .

# ==========================================
# INSTALLATION LARAVEL
# ==========================================

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist

# ==========================================
# PERMISSIONS
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
# PRODUCTION
# ==========================================

ENV APP_ENV=production

CMD ["apache2-foreground"]
