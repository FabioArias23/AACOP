#!/bin/sh
set -e

echo "🚀 Iniciando contenedor en Producción..."

# 1. Ajustar permisos
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 2. Limpiar cachés antiguos
echo "🧹 Limpiando cachés..."
rm -rf /var/www/html/bootstrap/cache/*.php
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true

# 3. Generar configuración
echo "🔥 Generando configuración..."
php artisan config:cache

# 4. Ejecutar migraciones
echo "📦 Ejecutando migraciones..."
php artisan migrate --force --no-interaction

# 5. Optimizaciones
echo "⚡ Optimizando aplicación..."
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Publicar assets de Livewire (NUEVO)
echo "🎨 Publicando assets de Livewire..."
php artisan livewire:publish --assets || true

# 7. Crear link simbólico para storage
php artisan storage:link || true

echo "✅ Aplicación lista. Iniciando servicios..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
