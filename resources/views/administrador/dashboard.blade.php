<x-app-layout>
    <style>
        .dash-wrap { max-width: 1100px; margin: 0 auto; padding: 20px; }
        .dash-head { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:18px; flex-wrap:wrap; }
        .dash-head h2 { margin:0; }
        .kpi-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:14px; margin-bottom:20px; }
        .kpi { background:#f8fafc; border-radius:12px; padding:16px; }
        .kpi .lbl { font-size:13px; color:#64748b; }
        .kpi .val { font-size:26px; font-weight:700; color:#0f172a; margin-top:4px; }
        .kpi .sub { font-size:12px; margin-top:4px; }
        .panel { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:18px; margin-bottom:16px; }
        .panel h3 { margin:0 0 14px; font-size:16px; }
        .panel-sub { font-weight:400; font-size:13px; color:#64748b; }
        .dbars { display:flex; align-items:flex-end; gap:12px; height:150px; }
        .dbar-col { flex:1; display:flex; flex-direction:column; align-items:center; gap:6px; }
        .dbar { width:100%; background:#99f6e4; border-radius:6px 6px 0 0; }
        .dbar.last { background:#0d9488; }
        .estado-row { margin-bottom:12px; }
        .estado-bar { height:10px; background:#f1f5f9; border-radius:20px; overflow:hidden; margin-top:4px; }
        .estado-fill { height:100%; }
        .pend { display:flex; align-items:center; gap:10px; font-size:14px; padding:8px 0; border-bottom:1px solid #f1f5f9; }
        .grid2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        @media (max-width:800px){ .grid2 { grid-template-columns:1fr; } }
    </style>

    <div class="dash-wrap">
        <div class="dash-head">
            <div style="display:flex; align-items:center; gap:10px;"><i class="chart bar icon big" style="color:#0d9488;"></i><h2>Panel del comité</h2></div>
            <span style="font-size:12px; color:#94a3b8;" id="dash-updated"></span>
        </div>

        <div class="kpi-grid" id="kpis"></div>

        <div class="grid2">
            <div class="panel"><h3>Recaudación · últimos 6 meses</h3><div class="dbars" id="bars"></div></div>
            <div class="panel"><h3>Estado de las casas</h3><div id="estado-casas"></div></div>
        </div>

        {{--
            Quiénes deben, con nombre y monto. Va aquí y NO en el módulo de
            transparencia: esa pantalla la ven todos los vecinos y el Aviso de
            Privacidad dice que los adeudos por vivienda los ve solo la mesa.
        --}}
        <div class="panel">
            <h3>Casas con adeudo <span id="deudores-resumen" class="panel-sub"></span></h3>
            <div id="deudores"></div>
        </div>

        <div class="panel"><h3>Pendientes del comité</h3><div id="pendientes"></div></div>
    </div>

    <script>
        // Cifras completas, con centavos: son montos de dinero, no estimados.
        const fmt = new Intl.NumberFormat('es-MX', {
            style: 'currency', currency: 'MXN',
            minimumFractionDigits: 2, maximumFractionDigits: 2
        });

        function cargar() {
            $.get('{{ route("admin.dashboard.datos") }}', function (r) {
                if (!r.success) return;
                pintar(r);
            });
        }

        function pintar(r) {
            $('#dash-updated').text('Actualizado ' + r.actualizado);

            const c = r.cobranza, casas = r.casas, part = r.participacion;
            $('#kpis').html(`
                <div class="kpi"><div class="lbl">Recaudado este mes</div><div class="val">${fmt.format(c.recaudado)}</div><div class="sub" style="color:#0d9488;">${c.pct}% de ${fmt.format(c.esperado)}</div></div>
                <div class="kpi"><div class="lbl">Casas al corriente</div><div class="val">${casas.al_corriente} / ${casas.total}</div><div class="sub" style="color:#64748b;">${casas.total ? Math.round(casas.al_corriente/casas.total*100) : 0}% del condominio</div></div>
                <div class="kpi"><div class="lbl">Cartera vencida</div><div class="val" style="color:#ef4444;">${fmt.format(casas.cartera_vencida)}</div><div class="sub" style="color:#ef4444;">${casas.atrasadas} casa(s) atrasada(s)</div></div>
                <div class="kpi"><div class="lbl">Participación última encuesta</div><div class="val">${part.votantes} / ${part.total}</div><div class="sub" style="color:#64748b;">${part.pct}% ${part.titulo ? 'votó' : 'sin encuestas'}</div></div>
            `);

            // Barras 6 meses
            const meses = r.recaudacion_6m || [];
            const max = Math.max(1, ...meses.map(m => m.total));
            $('#bars').html(meses.map((m, i) => {
                const h = Math.round((m.total / max) * 100);
                return `<div class="dbar-col"><div class="dbar ${i === meses.length - 1 ? 'last' : ''}" style="height:${Math.max(h,3)}%;" title="${fmt.format(m.total)}"></div><span style="font-size:12px; color:#64748b;">${m.label}</span></div>`;
            }).join(''));

            // Casas con adeudo
            const deu = r.deudores || { total: 0, monto: 0, lista: [] };

            $('#deudores-resumen').text(
                deu.total ? `· ${deu.total} casa(s) · ${fmt.format(deu.monto)}` : ''
            );

            $('#deudores').html(
                deu.lista.length
                    ? deu.lista.map(v => {
                        // El atraso distingue un despiste del mes de un rezago
                        // serio; es lo que decide a quién llamar primero.
                        const alerta = v.dias > 60 ? '#b91c1c' : (v.dias > 30 ? '#d97706' : '#64748b');
                        const atraso = v.dias > 0
                            ? `<span style="color:${alerta}; font-size:12px;">${v.dias} día(s) de atraso</span>`
                            : '<span style="color:#64748b; font-size:12px;">por vencer</span>';

                        return `
                            <a href="{{ url('/administrador/estado-cuenta/pdf') }}/${v.id}" target="_blank"
                               style="display:flex; align-items:center; justify-content:space-between; gap:10px;
                                      padding:9px 2px; border-bottom:1px solid #f1f5f9; text-decoration:none; color:inherit;">
                                <span style="min-width:0;">
                                    <span style="font-weight:600; font-size:13px;">Casa ${v.casa}</span>
                                    <span style="color:#64748b; font-size:13px;"> · ${v.nombre}</span>
                                    <br>${atraso}
                                </span>
                                <span style="white-space:nowrap; font-weight:700; color:#b91c1c;">
                                    ${fmt.format(v.pendiente)}
                                </span>
                            </a>`;
                    }).join('') +
                    (deu.total > deu.lista.length
                        ? `<div style="padding-top:9px; font-size:13px;">
                               <a href="{{ route('admin.estadoCuenta.index') }}" style="color:#6366f1;">
                                   Ver las ${deu.total} casas con adeudo
                               </a></div>`
                        : `<div style="padding-top:9px; font-size:13px;">
                               <a href="{{ route('admin.estadoCuenta.index') }}" style="color:#6366f1;">
                                   Ir a Estado de Cuenta
                               </a></div>`)
                    : '<div style="padding:18px 2px; color:#10b981; font-size:13px;">'
                      + '<i class="check circle icon"></i> Todas las casas al corriente.</div>'
            );

            // Estado casas
            const t = casas.total || 1;
            $('#estado-casas').html(`
                <div class="estado-row"><div style="display:flex; justify-content:space-between; font-size:13px;"><span><i class="check circle icon" style="color:#10b981;"></i> Al corriente</span><span>${casas.al_corriente}</span></div><div class="estado-bar"><div class="estado-fill" style="width:${casas.al_corriente/t*100}%; background:#10b981;"></div></div></div>
                <div class="estado-row"><div style="display:flex; justify-content:space-between; font-size:13px;"><span><i class="warning circle icon" style="color:#ef4444;"></i> Atrasadas</span><span>${casas.atrasadas}</span></div><div class="estado-bar"><div class="estado-fill" style="width:${casas.atrasadas/t*100}%; background:#ef4444;"></div></div></div>
            `);

            // Pendientes
            const pend = r.pendientes || [];
            $('#pendientes').html(pend.length ? pend.map(p => `<div class="pend"><i class="${p.icono} icon" style="color:#0d9488;"></i><span style="flex:1;">${p.texto}</span><span style="font-size:12px; color:#94a3b8;">${p.nota}</span></div>`).join('') : '<p style="color:#94a3b8;">Sin pendientes.</p>');
        }

        $(function () {
            cargar();
            setInterval(cargar, 60000);
        });
    </script>
</x-app-layout>
