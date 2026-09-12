#!/bin/bash

# Script para optimizar el proyecto Laravel para producción
# Uso: bash optimize-for-production.sh

echo "=========================================="
echo "  Optimizando proyecto para producción"
echo "=========================================="

# 1. Limpiar carpetas innecesarias
echo ""
echo "[1/6] Limpiando carpetas innecesarias..."

rm -rf node_modules 2>/dev/null
echo "  ✓ node_modules eliminada"

rm -rf .git 2>/dev/null
echo "  ✓ .git eliminada"

rm -rf storage/logs/* 2>/dev/null
echo "  ✓ storage/logs limpiada"

rm -rf storage/framework/cache/* 2>/dev/null
echo "  ✓ storage/framework/cache limpiada"

rm -rf storage/framework/sessions/* 2>/dev/null
echo "  ✓ storage/framework/sessions limpiada"

touch storage/logs/.gitignore storage/framework/cache/.gitignore storage/framework/sessions/.gitignore
echo "  ✓ Archivos .gitignore creados"

# 2. Limpiar caché de Laravel
echo ""
echo "[2/6] Limpiando caché de Laravel..."
php artisan cache:clear 2>/dev/null
php artisan config:clear 2>/dev/null
php artisan route:clear 2>/dev/null
php artisan view:clear 2>/dev/null
echo "  ✓ Caché limpiada"

# 3. Optimizar Composer para producción
echo ""
echo "[3/6] Optimizando Composer para producción..."
composer dump-autoload --optimize --no-dev
echo "  ✓ Autoload optimizado"

# 4. Crear archivo de什么都不
echo ""
echo "[4/6] Creando estructura de storage..."
mkdir -p storage/app/public
mkdir -p storage/framework/testing
mkdir -p storage/framework/views
chmod -R 775 storage bootstrap/cache 2>/dev/null
echo "  ✓ Estructura de storage creada"

# 5. Eliminar archivos innecesarios
echo ""
echo "[5/6] Eliminando archivos innecesarios..."
rm -f .env.example
rm -f .env.backup
rm -f .phpunit.result.cache
rm -f phpunit.xml
rm -f phpunit.xml.dist
rm -f .styleci.yml
rm -f .editorconfig
find . -name "*.md" -type f -delete 2>/dev/null
find . -name ".DS_Store" -type f -delete 2>/dev/null
find . -name "Thumbs.db" -type f -delete 2>/dev/null
echo "  ✓ Archivos innecesarios eliminados"

# 6. Información final
echo ""
echo "[6/6] Información del proyecto optimizado..."
echo ""
echo "=========================================="
echo "  TAMAÑO POR CARPETA:"
echo "=========================================="
du -sh app bootstrap config database resources routes storage vendor public index.php artisan 2>/dev/null | sort -rh
echo ""
echo "TAMAÑO TOTAL:"
du -sh .
echo ""
echo "=========================================="
echo "  Archivos listos para subir:"
echo "=========================================="
echo "  - app/"
echo "  - bootstrap/"
echo "  - config/"
echo "  - database/"
echo "  - public/"
echo "  - resources/"
echo "  - routes/"
echo "  - storage/"
echo "  - vendor/"
echo "  - .env (configurar)"
echo "  - artisan"
echo "  - composer.json"
echo "  - composer.lock"
echo "=========================================="
echo ""
echo "✅ Optimización completada!"
echo ""
