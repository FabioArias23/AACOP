#!/bin/sh
set -e

echo "🚀 Iniciando contenedor en Producción..."

# 1. Eliminar .env residual si existe
if [ -f .env ]; then
    rm .env
fi

# 2. Ajustar permisos
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 3. TRUCO IMPORTANTE: Borrar caché manualmente (sin usar artisan)
# Esto evita que artisan intente conectarse a la DB antes de tiempo
echo "🧹 Eliminando archivos de caché manualmente..."
rm -f /var/www/html/bootstrap/cache/*.php

# 4. Generar la configuración PRIMERO (para que lea las variables de Render)
echo "🔥 Generando configuración nueva..."
php artisan config:cache

# 5. AHORA SÍ podemos ejecutar comandos que usen la DB
echo "📦 Ejecutando migraciones..."
php artisan migrate --force

# 6. Resto de cachés
echo "⚡ Cacheando rutas y vistas..."
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "✅ Servidor listo. Iniciando Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
