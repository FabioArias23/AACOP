# ----------------------------------------------------------------------------------
# ETAPA 1: BUILD (Compilar Backend y Frontend)
# ----------------------------------------------------------------------------------
FROM php:8.3-fpm-alpine AS builder

WORKDIR /app

# 1. Instalar dependencias del sistema necesarias para construir
RUN apk add --no-cache \
    git curl unzip zlib-dev libzip-dev libpng-dev libjpeg-turbo-dev \
    freetype-dev postgresql-dev linux-headers nodejs npm \
    autoconf g++ make

# 2. Extensiones PHP (necesarias para composer install si hay scripts)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_pgsql zip exif pcntl gd bcmath

# 3. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Copiar archivos de dependencias
COPY composer.json composer.lock ./
COPY package.json package-lock.json ./

# 5. Instalar dependencias Backend (Producción)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --prefer-dist

# 6. Instalar dependencias Frontend (TODAS, incluyendo devDependencies para Vite)
#    CORRECCIÓN: Quitamos --only=production para tener acceso a 'vite'
RUN npm ci

# 7. Copiar el resto del código fuente
COPY . .

# 8. COMPILAR ASSETS (Ahora sí funcionará porque Vite está instalado)
RUN npm run build

# 9. Limpieza: Borrar node_modules para no llevar basura a producción
RUN rm -rf node_modules

# 10. Dump autoload final
RUN composer dump-autoload --optimize

# ----------------------------------------------------------------------------------
# ETAPA 2: PRODUCCIÓN
# ----------------------------------------------------------------------------------
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

# 1. Dependencias Runtime (sin nodejs/npm, ya no se necesitan)
RUN apk add --no-cache \
    nginx supervisor libpng libjpeg-turbo freetype \
    libzip postgresql-libs icu-libs zlib bash

# 2. Instalar extensiones PHP en la imagen final
RUN set -ex \
    && apk add --no-cache --virtual .build-deps \
        autoconf g++ make zlib-dev libzip-dev libpng-dev \
        libjpeg-turbo-dev freetype-dev postgresql-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_pgsql zip exif pcntl gd bcmath \
    && apk del .build-deps

# 3. Crear estructura de directorios
RUN mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache/data \
    storage/logs \
    bootstrap/cache \
    public/storage

# 4. Copiar código compilado desde builder (trae /app completo sin node_modules)
COPY --from=builder --chown=www-data:www-data /app /var/www/html

# 5. Copiar configuraciones del servidor
COPY nginx.conf /etc/nginx/nginx.conf
COPY php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY php.ini /usr/local/etc/php/php.ini
COPY supervisor.conf /etc/supervisor/conf.d/supervisord.conf
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

# 6. Permisos
RUN chmod +x /usr/local/bin/entrypoint.sh \
    && chown -R www-data:www-data \
        /var/www/html/storage \
        /var/www/html/bootstrap/cache \
        /var/www/html/public \
    && chmod -R 775 \
        /var/www/html/storage \
        /var/www/html/bootstrap/cache

# 7. Puerto para Render
EXPOSE 10000

# 8. Health check (ajustado al puerto 10000)
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD wget --no-verbose --tries=1 --spider http://localhost:10000/ || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
