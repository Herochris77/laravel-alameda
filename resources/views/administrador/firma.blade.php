<x-app-layout>

    <div class="page-header">
        <div class="page-header-left">
            <div class="page-icon" style="background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);">
                <i class="pen fancy icon"></i>
            </div>
            <div>
                <h1 class="page-title">Mi firma</h1>
                <p class="page-subtitle">
                    Aparece en los recibos que los vecinos descargan de los pagos que tú validaste.
                </p>
            </div>
        </div>
    </div>

    <div class="fr-columnas">

        {{-- ESTADO ACTUAL --}}
        <div class="card">
            <div class="card-body">

                <h3 class="fr-titulo">Firma registrada</h3>

                @if($urlFirma)
                    <div class="fr-lienzo">
                        <img src="{{ $urlFirma }}" alt="Firma registrada">
                        <div class="fr-linea"></div>
                        <div class="fr-cargo">
                            {{ auth()->user()->nombre }}<br>
                            <span>Tesorería · Mesa Directiva</span>
                        </div>
                    </div>

                    <p class="fr-nota">
                        Así se verá al pie del recibo.
                    </p>

                    <button class="btn btn-danger" id="btn-eliminar-firma" style="width:100%;">
                        <i class="trash icon"></i>
                        Eliminar firma
                    </button>
                @else
                    <div class="fr-vacio">
                        <i class="pen fancy icon"></i>
                        <p>Todavía no subes tu firma.</p>
                        <small>
                            Los recibos que descargan los vecinos están saliendo con la
                            línea en blanco.
                        </small>
                    </div>
                @endif

                @if($recibosFirmados > 0)
                    <div class="fr-alcance">
                        <i class="info circle icon"></i>
                        <strong>{{ $recibosFirmados }}</strong> recibo(s) que ya validaste
                        saldrán con esta firma cuando el vecino los descargue.
                    </div>
                @endif

            </div>
        </div>

        {{-- SUBIR --}}
        <div class="card">
            <div class="card-body">

                <h3 class="fr-titulo">{{ $urlFirma ? 'Reemplazar firma' : 'Subir firma' }}</h3>

                <form id="form-firma" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Imagen de la firma *</label>
                        <input type="file" name="firma" id="input-firma"
                               accept="image/png,image/jpeg" class="form-input" required>
                        <p class="fr-hint">PNG o JPG, máximo 2 MB.</p>
                    </div>

                    <div id="fr-previa" class="fr-previa" style="display:none;">
                        <span class="fr-previa-titulo">Vista previa</span>
                        <img id="fr-previa-img" alt="Vista previa de la firma">
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;">
                        <i class="upload icon"></i>
                        Guardar firma
                    </button>
                </form>

                <div class="fr-consejo">
                    <strong>Para que se vea bien</strong>
                    <ul>
                        <li>Firma con tinta negra en una hoja blanca y tómale foto de frente,
                            con buena luz.</li>
                        <li>Recorta la imagen justo alrededor de la firma, sin margen de
                            sobra.</li>
                        <li>Apaisada, más ancha que alta, se ve mejor al pie del documento.</li>
                        <li>Si puedes, <strong>PNG con fondo transparente</strong>: así no
                            queda un rectángulo blanco encima de la línea. Si el servidor no
                            lo admite, el sistema te avisa al guardar y basta con subirla
                            como <strong>JPG</strong>.</li>
                    </ul>
                </div>

                <div class="fr-aviso">
                    <i class="shield alternate icon"></i>
                    Tu firma es un dato personal. Solo se imprime en los recibos de pagos
                    que tú validaste y nadie más puede subirla ni reemplazarla: este módulo
                    es exclusivo de quien tiene el cargo de Tesorería.
                </div>

            </div>
        </div>

    </div>

    <style>
        .fr-columnas {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 18px;
            align-items: start;
        }

        .fr-titulo { margin: 0 0 14px; font-size: 1.02rem; }

        .fr-lienzo {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 22px 26px 16px;
            background: #fff;
            text-align: center;
        }

        .fr-lienzo img {
            max-width: 100%;
            max-height: 110px;
            display: block;
            margin: 0 auto 2px;
        }

        .fr-linea {
            border-top: 1px solid #334155;
            margin: 0 auto;
            max-width: 280px;
        }

        .fr-cargo {
            margin-top: 6px;
            font-size: .84rem;
            color: #0f172a;
            font-weight: 600;
        }

        .fr-cargo span { font-weight: 400; color: #64748b; font-size: .76rem; }

        .fr-nota {
            text-align: center;
            color: #94a3b8;
            font-size: .78rem;
            margin: 10px 0 16px;
        }

        .fr-vacio {
            text-align: center;
            padding: 34px 16px;
            color: #94a3b8;
            border: 2px dashed #e2e8f0;
            border-radius: 12px;
        }

        .fr-vacio i { font-size: 2.2rem; margin-bottom: 8px; }

        .fr-vacio p { margin: 0 0 4px; font-weight: 600; color: #64748b; }

        .fr-vacio small { font-size: .78rem; line-height: 1.45; display: block; }

        .fr-alcance {
            margin-top: 14px;
            padding: 10px 12px;
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 10px;
            color: #075985;
            font-size: .83rem;
        }

        .fr-hint { margin: 5px 0 0; color: #94a3b8; font-size: .77rem; }

        .fr-previa {
            margin: 14px 0;
            padding: 14px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            text-align: center;
            background: #f8fafc;
        }

        .fr-previa-titulo {
            display: block;
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .fr-previa img { max-width: 100%; max-height: 100px; }

        .fr-consejo {
            margin-top: 18px;
            padding: 13px 15px;
            background: #f8fafc;
            border-radius: 10px;
            font-size: .83rem;
            color: #475569;
        }

        .fr-consejo strong { display: block; margin-bottom: 6px; color: #0f172a; }

        .fr-consejo ul { margin: 0; padding-left: 18px; }

        .fr-consejo li { margin-bottom: 5px; line-height: 1.5; }

        .fr-aviso {
            margin-top: 14px;
            padding: 11px 13px;
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 10px;
            color: #92400e;
            font-size: .82rem;
            line-height: 1.5;
        }

        @media (max-width: 900px) {
            .fr-columnas { grid-template-columns: 1fr; }
        }
    </style>

    <script>
        $(function () {
            const BASE_URL = "{{ url('/') }}";

            // Vista previa antes de subir: evita descubrir que la firma
            // quedó torcida o con fondo blanco hasta que ya está en un recibo.
            $('#input-firma').on('change', function () {
                const archivo = this.files[0];

                if (!archivo) {
                    $('#fr-previa').hide();
                    return;
                }

                const lector = new FileReader();

                lector.onload = function (e) {
                    $('#fr-previa-img').attr('src', e.target.result);
                    $('#fr-previa').show();
                };

                lector.readAsDataURL(archivo);
            });

            $('#form-firma').on('submit', function (e) {
                e.preventDefault();

                const datos = new FormData(this);

                $.ajax({
                    url: `${BASE_URL}/administrador/firma/guardar`,
                    method: 'POST',
                    data: datos,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        alertify.alert(res.header, res.message, function () {
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        const r = xhr.responseJSON || {};
                        alertify.alert(r.header || 'Error', r.message || 'No se pudo guardar la firma.');
                    }
                });
            });

            $('#btn-eliminar-firma').on('click', function () {
                alertify.confirm(
                    'Eliminar firma',
                    'Los recibos que descarguen los vecinos volverán a salir con la línea en blanco. ¿Continuar?',
                    function () {
                        $.ajax({
                            url: `${BASE_URL}/administrador/firma/eliminar`,
                            method: 'DELETE',
                            data: { _token: $('input[name=_token]').first().val() },
                            success: function (res) {
                                alertify.alert(res.header, res.message, function () {
                                    location.reload();
                                });
                            },
                            error: function (xhr) {
                                const r = xhr.responseJSON || {};
                                alertify.alert(r.header || 'Error', r.message || 'No se pudo eliminar.');
                            }
                        });
                    },
                    function () {}
                );
            });
        });
    </script>

</x-app-layout>
