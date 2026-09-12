<x-app-layout>
    <style>
        .cajon-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .cajon-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        
        .cajon-card.disponible {
            border-color: #10b981;
        }
        
        .cajon-card.ocupado {
            border-color: #ef4444;
        }
        
        .cajon-card.reservado {
            border-color: #f59e0b;
        }
        
        .cajon-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        
        .cajon-nombre {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
        }
        
        .cajon-estado {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .cajon-estado.disponible {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }
        
        .cajon-estado.ocupado {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }
        
        .cajon-estado.reservado {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }
        
        .cajon-info {
            padding: 12px;
            background: #f8fafc;
            border-radius: 12px;
            margin-top: 12px;
        }
        
        .cajon-info-label {
            font-size: 0.75rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .cajon-info-valor {
            font-weight: 600;
            color: #1e293b;
            margin-top: 4px;
        }
        
        .cajon-ubicacion {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .cajon-ubicacion.entrada {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }
        
        .cajon-ubicacion.central {
            background: rgba(139, 92, 246, 0.1);
            color: #8b5cf6;
        }
        
        .btn-liberar {
            width: 100%;
            margin-top: 16px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="page-header">
        <div class="page-header-left">
            <div class="page-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                <i class="car icon"></i>
            </div>
            <div>
                <h1 class="page-title">Estacionamiento</h1>
                <p class="page-subtitle">Administra los cajones de estacionamiento</p>
            </div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card" style="text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: #10b981;" id="stats-disponibles">0</div>
            <div style="color: #64748b; font-size: 0.9rem;">Disponibles</div>
        </div>
        <div class="stat-card" style="text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: #f59e0b;" id="stats-reservados">0</div>
            <div style="color: #64748b; font-size: 0.9rem;">Reservados</div>
        </div>
        <div class="stat-card" style="text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: #ef4444;" id="stats-ocupados">0</div>
            <div style="color: #64748b; font-size: 0.9rem;">Ocupados</div>
        </div>
    </div>

    <div class="ui top attached tabular menu" style="margin-bottom: 16px;">
        <a class="active item" data-tab="entrada">
            <i class="sign-in icon"></i> Entrada (4)
        </a>
        <a class="item" data-tab="central">
            <i class="map marker icon"></i> Central (2)
        </a>
    </div>

    <div class="ui bottom attached active tab segment" data-tab="entrada">
        <div id="cajones-entrada" class="ui stackable grid"></div>
    </div>

    <div class="ui bottom attached tab segment" data-tab="central">
        <div id="cajones-central" class="ui stackable grid"></div>
    </div>

    <script>
    $(document).ready(function() {
        $('.menu .item').tab();
        cargarEstacionamientos();
        
        setInterval(cargarEstacionamientos, 30000);
    });

    function cargarEstacionamientos() {
        $.get('{{ route("admin.estacionamiento.obtener") }}', function(response) {
            const datos = response.data;
            
            let entradaHtml = '';
            let centralHtml = '';
            
            let disponibles = 0, reservados = 0, ocupados = 0;
            
            datos.forEach(function(cajon) {
                const cardClass = cajon.estado.toLowerCase();
                const estadoLabel = cajon.estado === 'disponible' ? 'Disponible' :
                                    cajon.estado === 'reservado' ? 'Reservado' : 'Ocupado';
                
                if (cajon.estado === 'disponible') disponibles++;
                else if (cajon.estado === 'reservado') reservados++;
                else ocupados++;
                
                let infoHtml = '';
                if (cajon.estado !== 'disponible' && cajon.usuario) {
                    infoHtml = `
                        <div class="cajon-info">
                            <div class="cajon-info-label">Reservado por</div>
                            <div class="cajon-info-valor">${cajon.usuario}</div>
                            <div class="cajon-info-label" style="margin-top: 8px;">Fechas</div>
                            <div class="cajon-info-valor">${cajon.fechas_html || '-'}</div>
                        </div>
                    `;
                }
                
                const btnLiberar = cajon.estado !== 'disponible' 
                    ? `<button class="ui green basic button btn-liberar" onclick="liberarCajon(${cajon.id})">
                        <i class="unlock icon"></i> Liberar cajón
                       </button>`
                    : '';
                
                const card = `
                    <div class="sixteen wide mobile eight wide tablet four wide computer column">
                        <div class="cajon-card ${cardClass}">
                            <div class="cajon-header">
                                <span class="cajon-nombre">${cajon.nombre}</span>
                                <span class="cajon-estado ${cardClass}">${estadoLabel}</span>
                            </div>
                            <span class="cajon-ubicacion ${cajon.ubicacion}">
                                <i class="${cajon.ubicacion === 'entrada' ? 'sign-in' : 'map marker'} icon"></i>
                                ${cajon.ubicacion === 'entrada' ? 'Entrada' : 'Central'}
                            </span>
                            ${infoHtml}
                            ${btnLiberar}
                        </div>
                    </div>
                `;
                
                if (cajon.ubicacion === 'Entrada') {
                    entradaHtml += card;
                } else {
                    centralHtml += card;
                }
            });
            
            $('#cajones-entrada').html(entradaHtml || '<div class="ui message">No hay cajones de entrada</div>');
            $('#cajones-central').html(centralHtml || '<div class="ui message">No hay cajones centrales</div>');
            
            $('#stats-disponibles').text(disponibles);
            $('#stats-reservados').text(reservados);
            $('#stats-ocupados').text(ocupados);
        });
    }
    
    window.liberarCajon = function(id) {
        alertify.confirm('Liberar cajón', '¿Estás seguro de liberar este cajón?', 
            function() {
                mostrarLoaderPantalla();
                setTimeout(() => {
                    $.ajax({
                        url: '{{ url("administrador/estacionamiento/liberar") }}/' + id,
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            if (response.success) {
                                alertify.success(response.message);
                                cargarEstacionamientos();
                            } else {
                                alertify.error(response.message);
                            }
                        },
                        complete: function() {
                            ocultarLoaderPantalla();
                        }
                    });
                }, 100);
            },
            function() {}
        );
    };
    </script>
</x-app-layout>