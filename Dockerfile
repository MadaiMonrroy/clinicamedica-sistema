# 1. Fijar versión exacta de PHP con Apache
FROM php:8.2.12-apache

# 2. Instalar dependencias del sistema y Cliente PostgreSQL
# (Se agregó xz-utils, necesario para descomprimir el instalador de Node.js)
RUN apt-get update && apt-get install -y \
    postgresql-client \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    zip \
    libzip-dev \
    unzip \
    git \
    curl \
    libonig-dev \
    xz-utils \
    && rm -rf /var/lib/apt/lists/*

# 3. Configurar e instalar extensiones principales de PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    gd \
    zip \
    bcmath \
    opcache \
    pcntl \
    exif \
    mbstring

# 4. Fijar versión exacta de Composer
COPY --from=composer:2.9.5 /usr/bin/composer /usr/bin/composer

# 5. Fijar versión exacta de Node.js
ENV NODE_VERSION=22.22.2
RUN curl -fsSLO --compressed "https://nodejs.org/dist/v$NODE_VERSION/node-v$NODE_VERSION-linux-x64.tar.xz" \
    && tar -xJf "node-v$NODE_VERSION-linux-x64.tar.xz" -C /usr/local --strip-components=1 --no-same-owner \
    && rm "node-v$NODE_VERSION-linux-x64.tar.xz" \
    && ln -s /usr/local/bin/node /usr/local/bin/nodejs

# 6. Configurar Apache para Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && a2enmod rewrite

# 7. Establecer el directorio de trabajo
WORKDIR /var/www/html
