# Guía para Subir Laravel a cPanel Básico

## Paso 1: Optimizar Proyecto Localmente

Ejecuta el script de optimización (en Windows usa Git Bash o WSL):

```bash
cd C:\xampp\htdocs\Alameda
bash optimizar-produccion.sh
```

O manualmente:

```bash
# 1. Eliminar node_modules
rmdir /s /q node_modules

# 2. Instalar solo dependencias de producción
composer install --optimize-autoloader --no-dev

# 3. Limpiar cachés
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 4. Eliminar archivos innecesarios
del /q .env.example 2>nul
del /q phpunit.xml 2>nul
del /q .phpunit.result.cache 2>nul
```

## Paso 2: Comprimir Archivos para Subir

**Archivos/carpetas a incluir:**
```
alameda/
├── app/                    ✓
├── bootstrap/               ✓
├── config/                  ✓
├── database/                ✓
├── public/                  ✓
├── resources/               ✓
├── routes/                  ✓
├── storage/                 ✓
├── vendor/                  ✓
├── .env                    ✓
├── artisan                 ✓
├── composer.json            ✓
├── composer.lock            ✓
```

**Archivos/carpetas a EXCLUIR (no subirlos):**
- ❌ node_modules/
- ❌ .git/
- ❌ .gitignore
- ❌ .env.example
- ❌ phpunit.xml
- ❌ *.md (README, CHANGELOG, etc.)
- ❌ .DS_Store
- ❌ Thumbs.db

## Paso 3: Comprimir desde Windows

1. Selecciona todas las carpetas/archivos listados arriba
2. Click derecho → "Enviar a" → "Carpeta comprimida (en zip)"
3. Nombra el archivo: `alameda-production.zip`

## Paso 4: Subir a cPanel

1. Entra a cPanel → **File Manager**
2. Navega a `public_html`
3. Sube el ZIP
4. Click derecho → **Extract**
5. Verifica que los archivos estén directamente en `public_html/` (no en subcarpeta)

## Paso 5: Configurar .env en Producción

En cPanel → File Manager, edita o crea el archivo `.env`:

```env
APP_NAME="Alameda"
APP_ENV=production
APP_KEY=base64:TU_KEY_AQUI
APP_DEBUG=false
APP_URL=https://tudominio.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=tu_usuario_alameda
DB_USERNAME=tu_usuario_db
DB_PASSWORD=tu_password_db

MAIL_MAILER=smtp
MAIL_HOST=smtp.tudominio.com
MAIL_PORT=465
MAIL_USERNAME=noreply@tudominio.com
MAIL_PASSWORD=tu_password_email
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@tudominio.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Para generar APP_KEY:**
```bash
php artisan key:generate
```

## Paso 6: Generar KEY y Permisos

Si tienes acceso SSH:
```bash
cd ~/public_html
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs storage/framework/cache storage/framework/sessions storage/framework/views
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Sin SSH**, establece permisos desde File Manager:
- `storage/` → 775
- `bootstrap/cache/` → 775

## Paso 7: Base de Datos

1. cPanel → **MySQL Databases**
2. Crea base de datos, usuario y contraseña
3. cPanel → **phpMyAdmin**
4. Importa tu archivo SQL exportado desde local

## Paso 8: Cambiar Document Root

cPanel → **Domains** → Tu dominio → Edit:
- **Document Root:** `public_html/public`

## Paso 9: SSL

cPanel → **SSL/TLS** → Instala Let's Encrypt

## Tamaño Esperado

| Componente | Tamaño aprox. |
|------------|---------------|
| vendor/ | ~60-80 MB |
| app/ | ~5 MB |
| resources/ | ~10-20 MB |
| public/ | ~5 MB |
| **TOTAL** | **~80-120 MB** |

El proyecto pasa de **3+ GB** a aproximadamente **100-150 MB**.

## Solución de Problemas

### Error 500 Internal Server Error
```bash
# Verificar permisos
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs storage/framework/cache storage/framework/sessions storage/framework/views
```

### Error "No application key"
```bash
php artisan key:generate
```

### Error de base de datos
Verificar credenciales en `.env`:
- DB_HOST debe ser `localhost`
- DB_DATABASE, DB_USERNAME, DB_PASSWORD deben coincidir exactamente

### Storage no funciona
```bash
php artisan storage:link
```
O crear enlace manualmente en `public/`:
```bash
ln -s ../storage/app/public storage
```
