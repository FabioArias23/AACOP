#!/bin/sh
set -e

echo "🚀 Iniciando contenedor en Producción..."

# --- FIX CRÍTICO ---
# Borramos el archivo .env físico para obligar a Laravel a usar
# EXCLUSIVAMENTE las variables de entorno configuradas en Render.
if [ -f .env ]; then
    echo "🗑️ Eliminando .env local para evitar conflictos con variables de Render..."
    rm .env
fi
# -------------------

# Si no existe la key en las variables de entorno (por seguridad)
if [ -z "$APP_KEY" ]; then
    echo "⚠️ ADVERTENCIA: APP_KEY no detectada en variables de entorno."
else
    echo "✅ APP_KEY detectada."
fi

echo "📦 Ejecutando migraciones..."
php artisan migrate --force

echo "🔥 Optimizando Laravel..."
# Limpiamos caches primero por si acaso
php artisan config:clear
php artisan cache:clear

# Generamos los caches de producción usando las variables de Render
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "⚡ Iniciando Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
