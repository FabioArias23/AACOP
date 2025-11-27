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

# 3. Generar configuración con variables de Render
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

# 6. Crear link simbólico para storage (si no existe)
php artisan storage:link || true

echo "✅ Aplicación lista. Iniciando servicios..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
```

---

## ✅ 5. **`.dockerignore` - CREAR ESTE ARCHIVO**
```
.git
.env
node_modules
vendor
storage/logs/*
storage/framework/cache/*
storage/framework/sessions/*
storage/framework/views/*
bootstrap/cache/*
.phpunit.result.cache
*.log
.DS_Store
