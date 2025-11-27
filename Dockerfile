# ----------------------------------------------------------------------------------
# ETAPA 1: BUILD (Construir assets y dependencias)
# ----------------------------------------------------------------------------------
# CAMBIO: Subimos a PHP 8.3 para compatibilidad con tus dependencias
FROM php:8.3-fpm-alpine AS builder

WORKDIR /app

# 1. Instalar dependencias del sistema necesarias para compilar extensiones PHP
RUN apk add --no-cache \
    git \
    curl \
    unzip \
    zlib-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    postgresql-dev \
    linux-headers \
    nodejs \
    npm \
    autoconf \
    g++ \
    make

# 2. Configurar e instalar extensiones de PHP
# NOTA: En PHP 8.3, docker-php-ext-install se encarga de zlib si libzip-dev está presente
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        pdo_pgsql \
        zip \
        exif \
        pcntl \
        gd

# 3. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Copiar archivos de dependencias primero (Caché de Docker)
COPY composer.json composer.lock package.json package-lock.json ./

# 5. Instalar dependencias
# Agregamos --ignore-platform-reqs por si hay alguna librería rebelde, pero con PHP 8.3 debería ir bien
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# 6. Copiar el resto del código y compilar assets
COPY . .
RUN npm ci && npm run build

# ----------------------------------------------------------------------------------
# ETAPA 2: PRODUCCIÓN (Imagen final ligera)
# ----------------------------------------------------------------------------------
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

# 1. Instalar dependencias de ejecución (Runtime)
RUN apk add --no-cache \
    nginx \
    supervisor \
    libpng \
    libjpeg-turbo \
    freetype \
    libzip \
    postgresql-libs \
    icu-libs \
    zlib

# 2. Instalar dependencias de compilación TEMPORALES
RUN set -ex \
    && apk add --no-cache --virtual .build-deps \
        autoconf \
        g++ \
        make \
        zlib-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        postgresql-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        pdo_pgsql \
        zip \
        exif \
        pcntl \
        gd \
    && apk del .build-deps

# 3. Crear estructura de carpetas de Laravel
RUN mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache/data \
    storage/logs \
    bootstrap/cache

# 4. Copiar configuración base .env
COPY .env.example .env

# 5. Copiar código compilado desde la etapa builder
COPY --from=builder /app /var/www/html

# 6. Copiar configuraciones de Nginx y Supervisor
COPY nginx/nginx.conf /etc/nginx/nginx.conf
COPY nginx/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY nginx/supervisor.conf /etc/supervisor/conf.d/supervisord.conf

# 7. Copiar script de entrada
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

# 8. Permisos
RUN chmod +x /usr/local/bin/entrypoint.sh \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer puerto 80 para Render
EXPOSE 80

# Comando de inicio
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
