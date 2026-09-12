# Instalación Web Push + Escalera comunitaria (cPanel sin terminal)

Este paquete se extrae **encima** de tu proyecto Laravel en el servidor (sobrescribe
archivos existentes y agrega nuevos). Respeta la estructura de carpetas.

## 1) Subir y extraer
1. cPanel → **File Manager** → entra a la carpeta raíz de tu proyecto (donde están
   `app/`, `vendor/`, `routes/`, etc. — normalmente `public_html`).
2. Sube `alameda-webpush.zip` y haz **Extract** ahí mismo.
3. Acepta sobrescribir cuando lo pregunte.

> El `sw.js` va dentro de la carpeta **public** (la que contiene `index.php`, que es
> tu *Document Root*). El zip ya lo coloca en `public/sw.js`. Verifica que quede
> accesible en `https://alameda-condominio.com.mx/sw.js`.

## 2) Agregar las llaves VAPID al .env del servidor
Edita el archivo `.env` (File Manager) y agrega al final:

```
VAPID_SUBJECT=mailto:avisos@alameda-condominio.com.mx
VAPID_PUBLIC_KEY=BGT6c25JmtAtUZBWOvTRJJDlpEwwlu_yr-pz1inuMjLuBoECpL6HcjD1zz6957WMkC4nhMHrGgZaZqwQcYEttCg
VAPID_PRIVATE_KEY=yNxDqtfyoH_sciHFYKMF_NKk1temGjnMQZ5Ci6cZGiQ
```

> Estas llaves son únicas de tu instalación. No las cambies una vez en uso o los
> dispositivos ya suscritos dejarían de recibir notificaciones.

## 3) Limpiar caché de configuración  (IMPORTANTE: hazlo ANTES de migrar)
Esto recarga el `.env` nuevo y la config de webpush. Abre en tu navegador:

```
https://alameda-condominio.com.mx/limpiar-cache/admin123
```

## 4) Ejecutar la migración (crea tabla push_subscriptions y amplía estacionamientos)
Abre en tu navegador:

```
https://alameda-condominio.com.mx/migrar/admin123
```

Debe mostrar el resultado de `migrate` (tablas creadas / migradas).

## 5) Requisitos del servidor (PHP)
En cPanel → **Select PHP Version** → pestaña **Extensions**, verifica que estén
activas (normalmente lo están): `curl`, `openssl`, `mbstring`, `json`.
No se requiere `gmp` ni `bcmath`.

## 6) Probar
1. Entra a la app en tu celular/PC → **Usuario → Perfil**.
2. Activa el switch **"Notificaciones del navegador"** y acepta el permiso.
3. Repite en otro dispositivo (cada dispositivo se suscribe por separado).
4. Genera cualquier notificación (ej. un comunicado) y verás el aviso del navegador
   aunque la app esté cerrada.

## Notas
- **Estacionamiento/Escalera**: ocupar y liberar ya NO envían notificaciones a nadie.
  El único aviso es el **recordatorio del día siguiente** (vía el cronjob ya existente
  `/cronjob/notificaciones-diarias`) si un cajón o la escalera siguen marcados como
  ocupados.
- La escalera comunitaria se crea sola al abrir la vista de estacionamiento.
- iOS: las notificaciones push web solo funcionan si el usuario **agrega la app a la
  pantalla de inicio** (PWA) en iOS 16.4+. En Android y escritorio funcionan directo.
- HTTPS es obligatorio para Web Push (tu sitio ya lo tiene).
