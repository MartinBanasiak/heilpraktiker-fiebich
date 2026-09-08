FROM php:7.4-apache

# Debian bullseye ist EOL -> Paketquellen auf das Archiv umbiegen
# (-security und -updates liegen dort nicht, fuer lokale Dev-Zwecke reicht main)
RUN sed -i -e 's|deb.debian.org|archive.debian.org|g' \
           -e '/-updates/d' \
           -e '/-security/d' /etc/apt/sources.list \
    && printf 'Acquire::Check-Valid-Until "false";\n' > /etc/apt/apt.conf.d/99no-check-valid-until

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        default-mysql-client \
        libicu-dev \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libxml2-dev \
        libbz2-dev \
    && apt-get clean

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        gd intl zip bz2 soap exif mysqli pdo_mysql opcache

RUN pecl install redis-5.3.7 && docker-php-ext-enable redis

RUN a2enmod rewrite headers expires deflate

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
