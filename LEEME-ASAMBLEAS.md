# Módulo de asambleas + Panel del comité

Se extrae **encima** de tu proyecto. No agrega dependencias de composer (el arrastrar
usa SortableJS por CDN y el PDF usa el dompdf que ya tienes). No toca Web Push,
estacionamiento ni encuestas.

## Pasos en cPanel
1. Sube el zip a la raíz del proyecto y **Extract** (sobrescribe/agrega).
2. Abre `https://alameda-condominio.com.mx/limpiar-cache/admin123`
3. Abre `https://alameda-condominio.com.mx/migrar/admin123`
   - Crea las 6 tablas de asamblea si no existen. Es idempotente y NO borra datos.

## Cómo funciona
### Administrador (Panel → Asambleas)
- Convoca una asamblea con su orden del día. Cada punto puede ser:
  - A favor / En contra / Abstención
  - Opciones (elegir una)
  - Ordenamiento (priorizar proyectos)
- Al convocar se notifica a los vecinos (push + campana).
- "Iniciar" pone la asamblea en curso y habilita la votación.
- **Pase de lista**: marca qué casas están presentes. El tablero (quórum y
  resultados) **se actualiza solo cada 8 segundos** mientras esté en curso.
- "Cerrar asamblea" fija los resultados. Botón **Acta (PDF)** para descargar el acta.

### Vecino (menú → Asambleas)
- Ve su "credencial de voto": qué casas ejerce y si su casa fue registrada presente.
- **Delegar**: el dueño puede dar su voto a su inquilino, o a otro vecino
  (representación). Puede revocarlo mientras la asamblea no esté cerrada.
- Vota cada punto (los de ordenamiento se arrastran). Al cerrarse, ve los resultados.

## Reglas de voto (así quedó implementado)
- **1 voto por casa.** El titular por defecto es el dueño.
- Un inquilino solo vota si **su** dueño le dio el poder.
- La representación vecino→vecino permite que un vecino cargue varios votos.
- **Control de asistencia (activado por defecto):** una casa solo puede votar si el
  administrador la registró como presente. Así, quien no asiste no puede votar.
  Puedes desactivarlo por asamblea al convocarla.
- Quórum = casas presentes/representadas confirmadas, contra el % requerido.

## Panel del comité (Panel → Panel del comité)
- Indicadores de cobranza del mes, casas al corriente, cartera vencida y participación.
- Recaudación de los últimos 6 meses, estado de las casas (al corriente / atrasadas)
  y pendientes. **Se actualiza solo cada 60 segundos.**

## Pruebas
Se incluye `tests/Feature/AsambleaTest.php`. El motor de votación y poderes fue
verificado con 19 aserciones y el flujo completo (crear→votar→acta→dashboard) con
14 pruebas de integración, todas en verde.
