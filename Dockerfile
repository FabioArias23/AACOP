# ----------------------------------------------------------------------------------
# ETAPA 1: BUILD (Compilar Backend y Frontend)
# ----------------------------------------------------------------------------------
FROM php:8.3-fpm-alpine AS builder

WORKDIR /app

# 1. Instalar dependencias del sistema
RUN apk add --no-cache \
    git curl unzip zlib-dev libzip-dev libpng-dev libjpeg-turbo-dev \
    freetype-dev postgresql-dev linux-headers nodejs npm \
    autoconf g++ make

# 2. Extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql pdo_pgsql zip exif pcntl gd

# 3. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Copiar archivos de dependencias
COPY composer.json composer.lock package.json package-lock.json ./

# 5. Instalar dependencias Backend
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# 6. Instalar dependencias Frontend
RUN npm ci

# 7. Copiar código fuente
COPY . .

# 8. COMPILAR ASSETS (Genera public/build/manifest.json)
RUN npm run build

# ----------------------------------------------------------------------------------
# ETAPA 2: PRODUCCIÓN
# ----------------------------------------------------------------------------------
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

# 1. Dependencias Runtime
RUN apk add --no-cache \
    nginx supervisor libpng libjpeg-turbo freetype \
    libzip postgresql-libs icu-libs zlib

# 2. Dependencias de compilación temporales para extensiones
RUN set -ex \
    && apk add --no-cache --virtual .build-deps \
        autoconf g++ make zlib-dev libzip-dev libpng-dev \
        libjpeg-turbo-dev freetype-dev postgresql-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql pdo_pgsql zip exif pcntl gd \
    && apk del .build-deps

# 3. Estructura de carpetas
RUN mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache/data \
    storage/logs \
    bootstrap/cache

# 4. Copiar código compilado (Incluye public/build generado en etapa 1)
COPY --from=builder /app /var/www/html

# 5. Copiar configuraciones
COPY nginx/nginx.conf /etc/nginx/nginx.conf
COPY nginx/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY nginx/supervisor.conf /etc/supervisor/conf.d/supervisord.conf
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

# 6. PERMISOS (CRUCIAL: Agregamos 'public' para que Nginx pueda leer los assets)
RUN chmod +x /usr/local/bin/entrypoint.sh \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
