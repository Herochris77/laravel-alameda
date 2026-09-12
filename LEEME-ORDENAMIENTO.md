# Encuestas de ordenamiento (ranking de prioridades)

Se extrae **encima** de tu proyecto (sobrescribe 7 archivos). No agrega dependencias
nuevas de composer; el arrastrar usa SortableJS por CDN.

## Archivos incluidos
- app/Models/Encuesta.php
- app/Models/EncuestaRespuesta.php
- app/Http/Controllers/Administrador/EncuestaController.php
- app/Http/Controllers/Usuario/EncuestaController.php
- routes/web.php
- resources/views/administrador/encuesta.blade.php
- resources/views/usuario/encuesta/index.blade.php

## Pasos en cPanel
1. Sube el zip a la raíz del proyecto y **Extract** (sobrescribe).
2. Abre `https://alameda-condominio.com.mx/limpiar-cache/admin123`
3. Abre `https://alameda-condominio.com.mx/migrar/admin123`
   - Agrega la columna `tipo` a `encuestas` y `posicion` a `encuesta_respuestas`.
   - Es idempotente y NO borra ni modifica datos existentes.

## Cómo se usa
- Admin → Encuestas → "Tipo de encuesta" = **Ordenamiento**. Captura los proyectos
  como opciones. Publica.
- Vecino → Encuestas: arrastra los proyectos para ordenarlos (#1 = mayor prioridad)
  y pulsa "Guardar mi orden". Puede actualizar su orden mientras la encuesta siga
  abierta.
- Resultados:
  - Admin (botón "Ver resultados"): orden ganador por puntos + matriz de cómo votaron
    + detalle del orden que eligió cada vecino (desplegable).
  - Vecino (pestaña Historial, al cerrarse la encuesta): orden ganador + matriz
    agregada + su propio orden.

## Método del ganador
Puntos de prioridad (Borda): con N proyectos, un #1 vale N puntos, #2 vale N-1, …,
#N vale 1. Se suman los puntos de todos los votantes; el mayor puntaje queda en #1.

## Notas
- Las encuestas de opción única/múltiple siguen funcionando igual que antes.
- Este paquete no toca nada de Web Push ni de estacionamiento.
