{{--
    Estilos compartidos por las dos guías.

    Escritos para dompdf, que entiende CSS2 pero no flexbox ni grid: todo lo
    que necesite columnas va con <table>. Es menos elegante que el CSS de las
    pantallas, pero es lo único que imprime igual.
--}}
<style>
    @page { margin: 34px 40px 46px; }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 10.5px;
        line-height: 1.55;
        color: #1e293b;
    }

    /* ---------- Portada ---------- */

    .portada { text-align: center; padding-top: 120px; }

    .portada-logo { margin-bottom: 18px; }

    .portada-condominio {
        font-size: 15px;
        font-weight: bold;
        letter-spacing: .12em;
        color: #0f172a;
    }

    .portada-linea {
        width: 90px;
        border-top: 2px solid #667eea;
        margin: 14px auto;
    }

    .portada-titulo {
        font-size: 30px;
        font-weight: bold;
        color: #4c3a8f;
        margin: 6px 0 4px;
        line-height: 1.2;
    }

    .portada-sub { font-size: 12px; color: #64748b; }

    .portada-pie {
        margin-top: 46px;
        font-size: 9px;
        color: #94a3b8;
        line-height: 1.7;
    }

    /* ---------- Encabezado de página ---------- */

    .membrete {
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 7px;
        margin-bottom: 18px;
    }

    .membrete td { vertical-align: middle; }

    .membrete-nombre {
        font-size: 10px;
        font-weight: bold;
        letter-spacing: .08em;
        color: #0f172a;
    }

    .membrete-doc { font-size: 8.5px; color: #94a3b8; text-align: right; }

    /* ---------- Índice ---------- */

    .indice {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 14px 18px;
        margin-bottom: 22px;
    }

    .indice-titulo {
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #64748b;
        font-weight: bold;
        margin-bottom: 8px;
    }

    .indice td { padding: 2.5px 0; font-size: 10px; }

    .indice .num { width: 22px; color: #667eea; font-weight: bold; }

    /* ---------- Secciones ---------- */

    h2 {
        font-size: 14px;
        color: #4c3a8f;
        margin: 26px 0 3px;
        padding-bottom: 5px;
        border-bottom: 1px solid #ddd6fe;
    }

    h2 .h2-num {
        display: inline-block;
        background: #667eea;
        color: #fff;
        width: 19px;
        height: 19px;
        text-align: center;
        border-radius: 10px;
        font-size: 11px;
        margin-right: 6px;
    }

    h3 { font-size: 11.5px; color: #0f172a; margin: 16px 0 4px; }

    p { margin: 0 0 9px; }

    ul, ol { margin: 0 0 11px; padding-left: 17px; }

    li { margin-bottom: 4px; }

    strong { color: #0f172a; }

    /* ---------- Avisos ---------- */

    .tip, .ojo, .nota {
        padding: 9px 12px;
        border-radius: 7px;
        margin: 11px 0;
        font-size: 9.8px;
        line-height: 1.5;
    }

    .tip { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
    .ojo { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
    .nota { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }

    .tip strong, .ojo strong, .nota strong { color: inherit; }

    /* ---------- Pasos ---------- */

    .pasos { width: 100%; margin: 12px 0; }

    .pasos td { padding: 0 0 9px; vertical-align: top; }

    .paso-num {
        width: 26px;
    }

    .paso-bolita {
        width: 19px;
        height: 19px;
        background: #4c3a8f;
        color: #fff;
        border-radius: 10px;
        text-align: center;
        font-size: 10px;
        font-weight: bold;
        line-height: 19px;
    }

    .paso-texto { font-size: 10.3px; padding-left: 2px; }

    /* ---------- Ilustración: tarjeta de recibo ---------- */

    .maqueta {
        border: 1px solid #e2e8f0;
        border-radius: 9px;
        background: #fcfcfd;
        padding: 12px 14px;
        margin: 12px 0;
    }

    .maqueta-titulo {
        font-size: 8px;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: #94a3b8;
        margin-bottom: 8px;
    }

    .tarjeta {
        border: 1px solid #e2e8f0;
        border-left: 4px solid #cbd5e1;
        border-radius: 7px;
        background: #fff;
        padding: 9px 12px;
        margin-bottom: 7px;
    }

    .tarjeta.pendiente { border-left-color: #f59e0b; }
    .tarjeta.vencido { border-left-color: #ef4444; }
    .tarjeta.pagado { border-left-color: #10b981; }
    .tarjeta.rechazado { border-left-color: #64748b; }

    .tarjeta-concepto { font-weight: bold; font-size: 10.5px; }

    .tarjeta-meta { font-size: 9px; color: #64748b; margin-top: 2px; }

    .tarjeta-monto { font-weight: bold; font-size: 11px; text-align: right; }

    /* ---------- Etiquetas de estado ---------- */

    .etq {
        display: inline-block;
        padding: 1.5px 8px;
        border-radius: 9px;
        font-size: 8.5px;
        font-weight: bold;
    }

    .etq.am { background: #fef3c7; color: #92400e; }
    .etq.ro { background: #fee2e2; color: #991b1b; }
    .etq.ve { background: #dcfce7; color: #166534; }
    .etq.gr { background: #f1f5f9; color: #475569; }
    .etq.az { background: #dbeafe; color: #1e40af; }

    /* ---------- Botón dibujado ---------- */

    .boton {
        display: inline-block;
        background: #4c3a8f;
        color: #fff;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 9.5px;
        font-weight: bold;
    }

    .boton.claro { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }

    /* ---------- Tablas de contenido ---------- */

    .tabla { width: 100%; border-collapse: collapse; margin: 11px 0; font-size: 9.8px; }

    .tabla th {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        padding: 6px 8px;
        text-align: left;
        font-size: 8.8px;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: #475569;
    }

    .tabla td { border: 1px solid #e2e8f0; padding: 6px 8px; vertical-align: top; }

    /* ---------- Captura de pantalla opcional ---------- */

    .captura { margin: 12px 0; text-align: center; }

    .captura img {
        max-width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
    }

    .captura-pie { font-size: 8.5px; color: #94a3b8; margin-top: 4px; }

    /* ---------- Pie ---------- */

    .cierre {
        margin-top: 28px;
        padding-top: 12px;
        border-top: 1px solid #e2e8f0;
        font-size: 9px;
        color: #94a3b8;
        text-align: center;
        line-height: 1.6;
    }

    .salto { page-break-before: always; }

    .no-romper { page-break-inside: avoid; }
</style>
