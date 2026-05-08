FROM php:8.4-cli

# =========================
# DEPENDENCIAS
# =========================
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libzip-dev \
    libssl-dev \
    pkg-config \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    zip \
    pdo \
    pdo_mysql \
    sockets \
    pcntl \
    gd


# =========================
# COMPOSER
# =========================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

RUN composer install    
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8000