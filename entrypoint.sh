#!/bin/sh
set -e

echo "🚀 Iniciando contenedor en Producción..."

# 1. Asegurar permisos de escritura (CRUCIAL PARA ERROR 500)
# Esto arregla el error de logs y sesiones que no se pueden escribir
echo "🔧 Ajustando permisos de carpetas..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 2. Eliminar .env local si existe (para forzar uso de variables de Render)
if [ -f .env ]; then
    echo "🗑️ Eliminando .env residual..."
    rm .env
fi

# 3. Limpiar TODA la caché antes de nada
echo "🧹 Limpiando cachés antiguas..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# 4. Cachear configuración (Laravel leerá las variables de Render aquí)
echo "🔥 Generando nueva caché de configuración..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Ejecutar migraciones (Ahora usará la config cacheada correcta: PGSQL)
echo "📦 Ejecutando migraciones..."
php artisan migrate --force

echo "✅ Servidor listo. Iniciando Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
