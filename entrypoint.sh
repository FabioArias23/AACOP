#!/bin/sh
set -e

echo "🚀 Iniciando contenedor en Producción..."

# Si no existe la key, generarla (solo por seguridad, idealmente ya viene en ENV)
if [ -z "$APP_KEY" ]; then
    echo "Generando APP_KEY..."
    php artisan key:generate
fi

echo "📦 Ejecutando migraciones..."
php artisan migrate --force

echo "🔥 Optimizando Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "⚡ Iniciando Supervisor..."
# En Alpine, la ruta de supervisord suele ser esta:
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
