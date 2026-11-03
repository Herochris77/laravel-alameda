<x-app-layout>
    <div class="page-header">
        <div class="page-header-left">
            <div class="page-icon" style="background: linear-gradient(135deg, var(--oro-700) 0%, var(--oro-700) 100%);">
                <i class="bullhorn icon"></i>
            </div>
            <div>
                <h1 class="page-title">Comunicados</h1>
                <p class="page-subtitle">Comunicados importantes para el condominio</p>
            </div>
        </div>
    </div>

    @if(!empty($arr_comu))
        <div class="grid grid-3">
            @foreach($arr_comu as $comunicado)
                <div class="card" style="position: relative; overflow: hidden;">
                    <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, var(--brand-600) 0%, var(--brand-800) 100%);"></div>
                    <div class="card-body">
                        <div style="display: flex; align-items: flex-start; gap: 14px; margin-bottom: 16px;">
                            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--brand-600) 0%, var(--brand-800) 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="bullhorn icon" style="color: white; font-size: 1.2rem;"></i>
                            </div>
                            <div style="flex: 1;">
                                <span class="ui blue basic label" style="font-size: 0.75rem; margin-bottom: 6px; display: inline-block;">Comunicado</span>
                                <h3 style="margin: 0; font-size: 1.15rem; color: var(--ink);">{{ $comunicado['Titulo'] }}</h3>
                            </div>
                        </div>
                        
                        <div style="background: var(--crema-50); border-radius: 12px; padding: 14px; margin-bottom: 16px;">
                            <div style="display: flex; align-items: center; gap: 8px; color: var(--ink-3); font-size: 0.9rem;">
                                <i class="calendar icon" style="color: var(--brand-600);"></i>
                                <span>Vence: <strong>{{ $comunicado['Vencimiento'] }}</strong></span>
                            </div>
                        </div>
                        
                        <p style="color: var(--ink-2); line-height: 1.6; margin: 0;">{{ $comunicado['Comunicado'] }}</p>
                    </div>
                    <div style="padding: 14px 24px; border-top: 1px solid var(--crema-100); display: flex; align-items: center; gap: 8px; color: var(--ink-3); font-size: 0.85rem;">
                        <i class="clock outline icon"></i>
                        <span title="{{ $comunicado['ActualizadoExacto'] }}">Actualizado {{ $comunicado['Actualizado'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="empty-state">
                <div class="empty-state-icon" style="background: var(--crema-100);">
                    <i class="inbox icon" style="color: var(--ink-4);"></i>
                </div>
                <h3>No hay comunicados</h3>
                <p>No existen comunicados vigentes al día de hoy.</p>
            </div>
        </div>
    @endif
</x-app-layout>
