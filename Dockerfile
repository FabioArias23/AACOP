# ----------------------------------------------------------------------------------
# ETAPA 1: BUILD (Compilar Backend y Frontend)
# ----------------------------------------------------------------------------------
FROM php:8.3-fpm-alpine AS builder

WORKDIR /app

# 1. Instalar dependencias del sistema para construir
RUN apk add --no-cache \
    git curl unzip zlib-dev libzip-dev libpng-dev libjpeg-turbo-dev \
    freetype-dev postgresql-dev linux-headers nodejs npm \
    autoconf g++ make

# 2. Extensiones PHP requeridas
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_pgsql zip exif pcntl gd bcmath

# 3. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Copiar archivos de definición de dependencias primero (para aprovechar caché de Docker)
COPY composer.json composer.lock ./
COPY package.json package-lock.json ./

# 5. Instalar dependencias Backend (Sin scripts aún)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --prefer-dist

# 6. Instalar dependencias Frontend
RUN npm ci

# 7. Copiar el resto del código de la aplicación
COPY . .

# 8. EJECUTAR VITE BUILD (Genera los assets en public/build)
RUN npm run build

# 9. Limpieza de node_modules (ya no se necesitan en prod, solo los assets compilados)
RUN rm -rf node_modules

# 10. Generar autoloader optimizado de Composer
RUN composer dump-autoload --optimize

# ----------------------------------------------------------------------------------
# ETAPA 2: PRODUCCIÓN (Imagen Final Ligera)
# ----------------------------------------------------------------------------------
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

# 1. Dependencias Runtime (Nginx, Supervisor, librerías gráficas)
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

# 3. Crear estructura de carpetas necesarias
RUN mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache/data \
    storage/logs \
    bootstrap/cache \
    public/storage

# 4. Copiar código compilado desde la etapa "builder"
COPY --from=builder --chown=www-data:www-data /app /var/www/html

# 5. Copiar configuraciones del servidor
COPY nginx.conf /etc/nginx/nginx.conf
COPY php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY php.ini /usr/local/etc/php/php.ini
COPY supervisor.conf /etc/supervisor/conf.d/supervisord.conf
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

# 6. Permisos finales y ejecución
RUN chmod +x /usr/local/bin/entrypoint.sh \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Puerto interno (coincide con tu nginx.conf)
EXPOSE 10000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
