# Comandos de mantenimiento para el proyecto Alameda

## Laravel Pint (Para formateo de código)
```bash
vendor/bin/pint
```

## PHPStan (Para análisis estático)
```bash
vendor/bin/phpstan analyse
```

## Laravel Pint y PHPStan juntos
```bash
vendor/bin/pint && vendor/bin/phpstan analyse
```

## Tests (si existen)
```bash
php artisan test
```

## Optimización de autoload de Composer
```bash
composer dump-autoload
```

## Limpiar caché de Laravel
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```