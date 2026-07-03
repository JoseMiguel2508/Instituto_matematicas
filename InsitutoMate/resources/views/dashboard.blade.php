@extends('layouts.app')

@section('title', 'Panel de Control | Instituto de Matemáticas UPDS')

@section('styles')
<style>
/* ── Docente Dashboard Styles ───────────────────────────── */
.docente-welcome {
    background: linear-gradient(135deg, rgba(6,182,212,0.12) 0%, rgba(99,102,241,0.12) 100%);
    border: 1px solid rgba(6,182,212,0.25);
    border-radius: var(--radius);
    padding: 28px 32px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 24px;
}
.docente-welcome-avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
    box-shadow: 0 0 24px rgba(6,182,212,0.3);
}
.docente-welcome-text h2 {
    margin: 0 0 4px;
    font-size: 1.3rem;
    color: var(--text-primary);
}
.docente-welcome-text p {
    margin: 0;
    font-size: 0.9rem;
    color: var(--text-secondary);
}
.docente-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}
.docente-stat-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: var(--radius);
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 18px;
    transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
}
.docente-stat-card:hover {
    transform: translateY(-3px);
    border-color: rgba(6,182,212,0.4);
    box-shadow: 0 8px 24px rgba(6,182,212,0.1);
}
.docente-stat-icon {
    width: 52px;
    height: 52px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}
.docente-stat-icon.cyan  { background: rgba(6,182,212,0.15);  color: var(--accent-cyan); }
.docente-stat-icon.blue  { background: rgba(99,102,241,0.15); color: var(--accent-blue); }
.docente-stat-icon.green { background: rgba(34,197,94,0.15);  color: var(--success); }
.docente-stat-label { font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 4px; }
.docente-stat-value { font-size: 2rem; font-weight: 700; color: var(--text-primary); line-height: 1; }
.carga-horaria-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
}
.carga-card {
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--card-border);
    border-radius: var(--radius-sm);
    padding: 18px 20px;
    transition: border-color 0.2s, transform 0.2s;
    position: relative;
    overflow: hidden;
}
.carga-card::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 3px;
    background: linear-gradient(to bottom, var(--accent-cyan), var(--accent-blue));
    border-radius: 3px 0 0 3px;
}
.carga-card:hover {
    border-color: rgba(6,182,212,0.35);
    transform: translateX(4px);
}
.carga-curso { font-size: 1rem; font-weight: 600; color: var(--text-primary); margin-bottom: 6px; }
.carga-meta  { font-size: 0.8rem; color: var(--text-secondary); display: flex; gap: 14px; flex-wrap: wrap; }
.carga-meta span { display: flex; align-items: center; gap: 4px; }
.carga-dia-badge {
    display: inline-block;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 4px;
    background: rgba(6,182,212,0.15);
    color: var(--accent-cyan);
    margin-bottom: 8px;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

/* ── Sidebar Grupos (Docente) ───────────────────────────── */
.grupo-sidebar-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    border-radius: var(--radius-sm);
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
}
.grupo-sidebar-item:hover {
    background: rgba(6,182,212,0.06);
    border-color: rgba(6,182,212,0.2);
}
.grupo-sidebar-item.active {
    background: rgba(6,182,212,0.1);
    border-color: rgba(6,182,212,0.35);
    box-shadow: inset 3px 0 0 var(--accent-cyan);
}
.grupo-detail-panel.hidden { display: none; }

/* ── Cajero Dashboard Styles ────────────────────────────── */
.cajero-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}
.cajero-stat-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: var(--radius);
    padding: 22px 24px;
    display: flex;
    align-items: center;
    gap: 18px;
    transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
}
.cajero-stat-card:hover {
    transform: translateY(-3px);
    border-color: rgba(99,102,241,0.4);
    box-shadow: 0 8px 24px rgba(99,102,241,0.1);
}
.cajero-stat-icon {
    width: 52px; height: 52px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; flex-shrink: 0;
}
.cajero-stat-icon.blue   { background: rgba(99,102,241,0.15); color: var(--accent-blue); }
.cajero-stat-icon.cyan   { background: rgba(6,182,212,0.15);  color: var(--accent-cyan); }
.cajero-stat-icon.green  { background: rgba(34,197,94,0.15);  color: var(--success); }
.cajero-stat-icon.orange { background: rgba(249,115,22,0.15); color: #f97316; }
.cajero-stat-label { font-size: 0.78rem; color: var(--text-secondary); margin-bottom: 4px; }
.cajero-stat-value { font-size: 1.8rem; font-weight: 700; color: var(--text-primary); line-height: 1; }
.cajero-stat-sub   { font-size: 0.75rem; color: var(--text-muted); margin-top: 3px; }

/* Deudas tabs */
.deuda-tabs { display: flex; gap: 0; margin-bottom: 0; border-radius: var(--radius-sm) var(--radius-sm) 0 0; overflow: hidden; }
.deuda-tab-btn {
    flex: 1; padding: 12px 16px;
    border: none; cursor: pointer;
    font-size: 0.85rem; font-weight: 600;
    transition: background 0.2s, color 0.2s;
    background: rgba(255,255,255,0.03);
    color: var(--text-secondary);
    border-bottom: 2px solid transparent;
}
.deuda-tab-btn.active-pendiente { background: rgba(239,68,68,0.1);  color: #ef4444; border-bottom-color: #ef4444; }
.deuda-tab-btn.active-pagada    { background: rgba(34,197,94,0.1);  color: var(--success); border-bottom-color: var(--success); }
.deuda-tab-panel { display: none; }
.deuda-tab-panel.active { display: block; }
.deuda-totales-bar {
    display: flex; gap: 20px; flex-wrap: wrap;
    padding: 14px 18px;
    background: rgba(255,255,255,0.02);
    border-bottom: 1px solid var(--card-border);
    align-items: center;
}
.deuda-total-item { display: flex; flex-direction: column; gap: 2px; }
.deuda-total-label { font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em; }
.deuda-total-value { font-size: 1.1rem; font-weight: 700; }
.deuda-total-value.danger  { color: #ef4444; }
.deuda-total-value.success { color: var(--success); }
</style>
@endsection

@section('page_title', 'Panel de Control')
@section('page_description',
    Auth::user()->hasRole('Docente') && !Auth::user()->hasAnyRole(['Administrador','Director'])
        ? 'Bienvenido a tu espacio de trabajo como Docente'
        : (Auth::user()->hasRole('Cajero') && !Auth::user()->hasAnyRole(['Administrador','Director'])
            ? 'Gestión de cobros, pagos y estado de deudas'
            : 'Métricas del Instituto de Matemáticas y estado actual del sistema')
)

@section('content')

{{-- ═══════════════════════════════════════════════════════════
     PANEL DOCENTE
═══════════════════════════════════════════════════════════ --}}
@if(Auth::user()->hasRole('Docente') && !Auth::user()->hasAnyRole(['Administrador','Director']))

    {{-- Saludo personalizado --}}
    <div class="docente-welcome">
        <div class="docente-welcome-avatar">
            {{ strtoupper(substr(Auth::user()->persona ? Auth::user()->persona->nombres : Auth::user()->username, 0, 2)) }}
        </div>
        <div class="docente-welcome-text">
            <h2>¡Bienvenido, {{ Auth::user()->persona ? Auth::user()->persona->nombres : Auth::user()->username }}!</h2>
            <p>Aquí tienes un resumen de tu actividad académica del período actual.</p>
        </div>
    </div>

    @if($faltanNotas)
    <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: var(--radius); padding: 16px 24px; margin-bottom: 24px; display: flex; align-items: center; gap: 16px; color: #ef4444;">
        <i class="bi bi-exclamation-triangle-fill" style="font-size: 1.5rem;"></i>
        <div>
            <h4 style="margin: 0 0 4px; font-size: 1rem; font-weight: 600;">Registro de Calificaciones Pendiente</h4>
            <p style="margin: 0; font-size: 0.85rem; color: var(--text-secondary);">Tienes alumnos inscritos en tus grupos actuales sin nota final registrada. Por favor, completa el registro.</p>
        </div>
    </div>
    @endif

    {{-- Tarjetas KPI del Docente --}}
    <div class="docente-stats">
        <div class="docente-stat-card">
            <div class="docente-stat-icon cyan">
                <i class="bi bi-collection-fill"></i>
            </div>
            <div>
                <div class="docente-stat-label">Mis Grupos Asignados</div>
                <div class="docente-stat-value">{{ $gruposCount }}</div>
            </div>
        </div>

        <div class="docente-stat-card">
            <div class="docente-stat-icon blue">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="docente-stat-label">Mis Alumnos Inscritos</div>
                <div class="docente-stat-value">{{ $studentsCount }}</div>
            </div>
        </div>

        <div class="docente-stat-card">
            <div class="docente-stat-icon green">
                <i class="bi bi-calendar2-check-fill"></i>
            </div>
            <div>
                <div class="docente-stat-label">Clases Programadas</div>
                <div class="docente-stat-value">{{ $misHorarios->count() }}</div>
            </div>
        </div>
    </div>

    {{-- Carga Horaria --}}
    <div class="grid-card" style="margin-bottom: 30px;">
        <div class="grid-card-title">
            <span><i class="bi bi-clock-history" style="color: var(--accent-cyan); margin-right: 8px;"></i> Mi Carga Horaria</span>
            <span style="font-size: 0.8rem; color: var(--text-secondary);">Horarios de clases activos</span>
        </div>

        @if($misHorarios->isEmpty())
            <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                <i class="bi bi-calendar-x" style="font-size: 2rem; margin-bottom: 12px; display: block;"></i>
                No tienes horarios registrados para el período actual.
            </div>
        @else
            <div class="carga-horaria-grid">
                @foreach($misHorarios as $h)
                    <div class="carga-card">
                        <div class="carga-dia-badge">{{ $h['dia'] }}</div>
                        <div class="carga-curso">{{ $h['curso'] }}</div>
                        <div class="carga-meta">
                            <span><i class="bi bi-hash"></i> Grupo {{ $h['grupo'] }}</span>
                            <span><i class="bi bi-geo-alt-fill"></i> Aula {{ $h['aula'] }}</span>
                            <span><i class="bi bi-clock-fill"></i> {{ $h['hora_inicio'] }} – {{ $h['hora_fin'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Tarjetas de Grupos --}}
    <div class="grid-card">
        <div class="grid-card-title" style="margin-bottom: 20px;">
            <span><i class="bi bi-collection-fill" style="color: var(--accent-cyan); margin-right: 8px;"></i> Mis Grupos Asignados</span>
            <span style="font-size: 0.8rem; color: var(--text-secondary);">Selecciona un grupo para calificar o ver el listado de alumnos</span>
        </div>

        @if(count($alumnosPorGrupo) === 0)
            <div style="text-align: center; padding: 50px 20px; color: var(--text-muted);">
                <i class="bi bi-journal-x" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
                <h3 style="color: var(--text-primary);">Sin grupos asignados</h3>
                <p>No se encontraron grupos vigentes asignados a tu cuenta en este período.</p>
            </div>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
                @foreach($alumnosPorGrupo as $idGrupo => $data)
                    <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--card-border); border-radius: var(--radius); padding: 20px; display: flex; flex-direction: column; justify-content: space-between; transition: all 0.2s ease; position: relative; overflow: hidden;"
                         onmouseover="this.style.borderColor='rgba(6,182,212,0.3)'; this.style.transform='translateY(-2px)';"
                         onmouseout="this.style.borderColor='var(--card-border)'; this.style.transform='none';">
                        
                        {{-- Decoración sutil --}}
                        <div style="position: absolute; right: -15px; top: -15px; font-size: 5rem; color: rgba(6,182,212,0.03); font-weight: 900; pointer-events: none; z-index: 1;">
                            {{ $data['grupo']->numero_grupo }}
                        </div>

                        <div style="position: relative; z-index: 2;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                <span class="badge badge-success" style="font-size: 0.65rem; padding: 2px 8px; font-weight: 600;">
                                    Grupo {{ $data['grupo']->numero_grupo }}
                                </span>
                                <span class="badge {{ $data['grupo']->estado === 'Abierto' ? 'badge-success' : 'badge-secondary' }}" style="font-size: 0.65rem; padding: 2px 8px; background: rgba(6,182,212,0.15); color: var(--accent-cyan);">
                                    {{ $data['grupo']->estado }}
                                </span>
                            </div>

                            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin: 0 0 10px 0; line-height: 1.3;">
                                {{ $data['grupo']->curso ? $data['grupo']->curso->nombre_curso : 'N/A' }}
                            </h3>

                            <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 20px; font-size: 0.8rem; color: var(--text-secondary);">
                                <span style="display: flex; align-items: center; gap: 6px;">
                                    <i class="bi bi-geo-alt-fill" style="color: var(--accent-cyan);"></i> Aula: {{ $data['grupo']->aula ? $data['grupo']->aula->codigo_aula : 'Sin Aula' }}
                                </span>
                                <span style="display: flex; align-items: center; gap: 6px;">
                                    <i class="bi bi-people-fill" style="color: var(--accent-blue);"></i> Alumnos Inscritos: <strong>{{ $data['alumnos']->count() }}</strong>
                                </span>
                            </div>
                        </div>

                        <div style="position: relative; z-index: 2;">
                            <a href="{{ route('notas.index', ['id_grupo' => $idGrupo]) }}" 
                               class="btn btn-primary" 
                               style="width: 100%; text-align: center; font-size: 0.825rem; font-weight: 600; padding: 8px 16px; display: flex; align-items: center; justify-content: center; gap: 6px;">
                                <i class="bi bi-journal-bookmark-fill"></i> Ver Alumnos y Notas
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

{{-- ═══════════════════════════════════════════════════════════
     PANEL CAJERO
═══════════════════════════════════════════════════════════ --}}
@elseif(Auth::user()->hasRole('Cajero') && !Auth::user()->hasAnyRole(['Administrador','Director']))

    {{-- Saludo --}}
    <div class="docente-welcome" style="background: linear-gradient(135deg, rgba(99,102,241,0.12) 0%, rgba(6,182,212,0.10) 100%); border-color: rgba(99,102,241,0.25);">
        <div class="docente-welcome-avatar" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">
            {{ strtoupper(substr(Auth::user()->persona ? Auth::user()->persona->nombres : Auth::user()->username, 0, 2)) }}
        </div>
        <div class="docente-welcome-text">
            <h2>¡Bienvenido, {{ Auth::user()->persona ? Auth::user()->persona->nombres : Auth::user()->username }}!</h2>
            <p>Aquí tienes el resumen de tus cobros del día y del mes.</p>
        </div>
    </div>

    {{-- Acciones Rápidas y Estado de Caja --}}
    <div style="display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap;">
        @if($sesionCajaActiva)
            <div style="flex: 1; background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); border-radius: var(--radius); padding: 18px 24px; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <i class="bi bi-unlock-fill" style="font-size: 1.8rem; color: var(--success);"></i>
                    <div>
                        <h4 style="margin: 0 0 4px; font-size: 1.1rem; font-weight: 700; color: var(--success);">Caja ABIERTA</h4>
                        <p style="margin: 0; font-size: 0.85rem; color: var(--text-secondary);">Sesión iniciada a las {{ \Carbon\Carbon::parse($sesionCajaActiva->fecha_apertura)->format('H:i') }}</p>
                    </div>
                </div>
                <a href="{{ route('pagos.index') }}" class="btn btn-success" style="font-weight: 600; padding: 10px 20px; font-size: 0.9rem;">
                    <i class="bi bi-plus-circle"></i> Nuevo Cobro
                </a>
            </div>
        @else
            <div style="flex: 1; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: var(--radius); padding: 18px 24px; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <i class="bi bi-lock-fill" style="font-size: 1.8rem; color: #ef4444;"></i>
                    <div>
                        <h4 style="margin: 0 0 4px; font-size: 1.1rem; font-weight: 700; color: #ef4444;">Caja CERRADA</h4>
                        <p style="margin: 0; font-size: 0.85rem; color: var(--text-secondary);">No puedes registrar cobros hasta aperturar tu caja.</p>
                    </div>
                </div>
                <form action="{{ route('caja.abrir') }}" method="POST" style="margin:0;">
                    @csrf
                    <input type="hidden" name="monto_inicial" value="0">
                    <button type="submit" class="btn btn-primary" style="font-weight: 600; padding: 10px 20px; font-size: 0.9rem;">
                        <i class="bi bi-key-fill"></i> Abrir Caja Ahora
                    </button>
                </form>
            </div>
        @endif
    </div>

    {{-- KPIs de Cajero --}}
    <div class="cajero-stats">
        <div class="cajero-stat-card">
            <div class="cajero-stat-icon blue"><i class="bi bi-receipt"></i></div>
            <div>
                <div class="cajero-stat-label">Cobros de Hoy</div>
                <div class="cajero-stat-value">{{ $pagosDia }}</div>
                <div class="cajero-stat-sub">transacciones</div>
            </div>
        </div>
        <div class="cajero-stat-card">
            <div class="cajero-stat-icon cyan"><i class="bi bi-cash-coin"></i></div>
            <div>
                <div class="cajero-stat-label">Recaudado Hoy</div>
                <div class="cajero-stat-value" style="color: var(--accent-cyan);">Bs. {{ number_format($montoDia, 0) }}</div>
                <div class="cajero-stat-sub">{{ \Carbon\Carbon::today()->format('d/m/Y') }}</div>
            </div>
        </div>
        <div class="cajero-stat-card">
            <div class="cajero-stat-icon green"><i class="bi bi-calendar-check-fill"></i></div>
            <div>
                <div class="cajero-stat-label">Cobros del Mes</div>
                <div class="cajero-stat-value">{{ $pagosMes }}</div>
                <div class="cajero-stat-sub">{{ ucfirst(\Carbon\Carbon::now()->isoFormat('MMMM')) }}</div>
            </div>
        </div>
        <div class="cajero-stat-card">
            <div class="cajero-stat-icon orange"><i class="bi bi-graph-up-arrow"></i></div>
            <div>
                <div class="cajero-stat-label">Total Mes</div>
                <div class="cajero-stat-value" style="color: #f97316;">Bs. {{ number_format($montoMes, 0) }}</div>
                <div class="cajero-stat-sub">acumulado</div>
            </div>
        </div>
    </div>

    {{-- Pagos Recientes --}}
    <div class="grid-card" style="margin-bottom: 30px;">
        <div class="grid-card-title">
            <span><i class="bi bi-credit-card-fill" style="color: var(--accent-blue); margin-right: 8px;"></i> Mis Transacciones de Hoy</span>
            <a href="{{ route('pagos.index') }}" class="btn btn-outline btn-sm" style="padding: 4px 10px; font-size: 0.75rem;">Ver todos</a>
        </div>
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Alumno</th>
                        <th>Nro Boleta</th>
                        <th>Método</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPayments as $p)
                        <tr>
                            <td style="font-weight: 500;">
                                {{ $p->estudiante && $p->estudiante->persona ? $p->estudiante->persona->nombre_completo : 'N/A' }}
                            </td>
                            <td><code>{{ $p->numero_comprobante }}</code></td>
                            <td>{{ $p->metodo_pago }}</td>
                            <td style="font-weight: 600; color: var(--accent-cyan);">Bs. {{ number_format($p->monto_total, 2) }}</td>
                            <td style="font-size: 0.8rem; color: var(--text-secondary);">
                                {{ \Carbon\Carbon::parse($p->fecha_pago)->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted);">No hay transacciones registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mensaje informativo para Cajero --}}
    <div style="background: rgba(99,102,241,0.06); border: 1px solid rgba(99,102,241,0.2); border-radius: var(--radius-sm); padding: 14px 18px; display: flex; align-items: center; gap: 12px; font-size: 0.85rem; color: var(--text-secondary);">
        <i class="bi bi-info-circle-fill" style="color: var(--accent-blue); font-size: 1.1rem;"></i>
        Para registrar nuevos cobros y ver el historial completo, dirígete a <a href="{{ route('pagos.index') }}" style="color: var(--accent-cyan); font-weight: 600;">Control de Pagos</a>.
    </div>

{{-- ═══════════════════════════════════════════════════════════
     PANEL ADMINISTRATIVO (Admin, Director, Secretaria, etc.)
═══════════════════════════════════════════════════════════ --}}
@else

    <!-- KPI Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div>
                <div class="stat-label">Estudiantes Activos</div>
                <div class="stat-value">{{ $studentsCount }}</div>
                <div class="stat-desc">Alumnos matriculados</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-people-fill" style="font-size: 24px;"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Docentes Activos</div>
                <div class="stat-value">{{ $teachersCount }}</div>
                <div class="stat-desc">Profesores contratados</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-person-badge-fill" style="font-size: 24px;"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Cursos Vigentes</div>
                <div class="stat-value">{{ $coursesCount }}</div>
                <div class="stat-desc">Asignaturas activas</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-book-half" style="font-size: 24px;"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Total Recaudado</div>
                <div class="stat-value" style="color: var(--accent-cyan);">Bs. {{ number_format($totalRevenue, 2) }}</div>
                <div class="stat-desc">Pagos registrados</div>
            </div>
            <div class="stat-icon" style="color: var(--accent-cyan); border-color: rgba(6, 182, 212, 0.2);">
                <i class="bi bi-cash-coin" style="font-size: 24px;"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Tasa Morosidad</div>
                <div class="stat-value" style="color: {{ $morosidadPorcentaje > 20 ? '#ef4444' : 'var(--warning)' }};">{{ $morosidadPorcentaje }}%</div>
                <div class="stat-desc">{{ $studentsWithDebt }} en riesgo</div>
            </div>
            <div class="stat-icon" style="color: {{ $morosidadPorcentaje > 20 ? '#ef4444' : 'var(--warning)' }}; border-color: rgba(239,68,68,0.2);">
                <i class="bi bi-exclamation-octagon" style="font-size: 24px;"></i>
            </div>
        </div>
    </div>

    <!-- Charts & Details Grid -->
    @php
        $hasRightSidebar = Auth::user()->hasAnyRole(['Administrador', 'Director', 'Secretaria']);
    @endphp
    <div class="dashboard-grid" style="{{ $hasRightSidebar ? '' : 'grid-template-columns: 1fr;' }}">
        <!-- Main Column: Revenue Chart & Payments -->
        <div style="display: flex; flex-direction: column; gap: 30px;">
            <!-- Revenue Chart -->
            <div class="grid-card">
                <div class="grid-card-title">
                    <span><i class="bi bi-graph-up-arrow" style="color: var(--accent-cyan); margin-right: 8px;"></i> Ingresos Recaudados</span>
                    <div>
                        <select id="revenueFilter" onchange="window.location.href='?filter=' + this.value" style="background: rgba(255,255,255,0.05); border: 1px solid var(--card-border); color: var(--text-secondary); border-radius: 4px; padding: 4px 8px; font-size: 0.75rem;">
                            <option value="6days" {{ request('filter') !== 'mes' ? 'selected' : '' }}>Últimos 6 Días</option>
                            <option value="mes" {{ request('filter') === 'mes' ? 'selected' : '' }}>Último Mes</option>
                        </select>
                    </div>
                </div>

                <div class="chart-container">
                    @foreach($revenueData as $item)
                        @php
                            $heightPct = ($item['value'] / $maxRevenue) * 80 + 5;
                        @endphp
                        <div class="chart-bar-wrapper">
                            <div class="chart-bar" style="height: {{ $heightPct }}%;">
                                <div class="chart-bar-tooltip">Bs. {{ number_format($item['value'], 2) }}</div>
                            </div>
                            <div class="chart-label">{{ $item['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Payments Table -->
            <div class="grid-card">
                <div class="grid-card-title">
                    <span><i class="bi bi-credit-card-fill" style="color: var(--accent-blue); margin-right: 8px;"></i> Últimas Transacciones</span>
                    <a href="{{ route('pagos.index') }}" class="btn btn-outline btn-sm" style="padding: 4px 10px; font-size: 0.75rem;">Ver todos</a>
                </div>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Alumno</th>
                                <th>Nro Boleta</th>
                                <th>Método</th>
                                <th>Monto</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $p)
                                <tr>
                                    <td style="font-weight: 500;">
                                        {{ $p->estudiante && $p->estudiante->persona ? $p->estudiante->persona->nombre_completo : 'N/A' }}
                                    </td>
                                    <td><code>{{ $p->numero_comprobante }}</code></td>
                                    <td>{{ $p->metodo_pago }}</td>
                                    <td style="font-weight: 600; color: var(--accent-cyan);">Bs. {{ number_format($p->monto_total, 2) }}</td>
                                    <td style="font-size: 0.8rem; color: var(--text-secondary);">
                                        {{ \Carbon\Carbon::parse($p->fecha_pago)->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; color: var(--text-muted);">No hay transacciones registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Sidebar Column: Log + Deudas + Cajas -->
        @if($hasRightSidebar)
        <div style="display: flex; flex-direction: column; gap: 24px;">
            
            {{-- Cajas: para Administrador y Secretaria --}}
            @if(Auth::user()->hasAnyRole(['Administrador', 'Secretaria', 'Director']))
            <div class="grid-card">
                <div class="grid-card-title">
                    <span><i class="bi bi-box-seam" style="color: var(--accent-blue); margin-right: 8px;"></i> Estado de Cajas</span>
                </div>
                <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 10px;">
                    @forelse($cajas as $caja)
                        <div style="background-color: rgba(255,255,255,0.02); border: 1px solid var(--card-border); padding: 12px; border-radius: var(--radius-sm); display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-weight: 500; font-size: 0.85rem; color: var(--text-primary);">
                                    {{ $caja->usuarioApertura && $caja->usuarioApertura->persona ? $caja->usuarioApertura->persona->nombres : 'Cajero' }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                                    Apertura: {{ \Carbon\Carbon::parse($caja->fecha_apertura)->format('d/m H:i') }}
                                </div>
                            </div>
                            <div>
                                @if($caja->estado === 'Abierta')
                                    <span class="badge badge-success" style="font-size: 0.7rem;">Abierta</span>
                                @else
                                    <span class="badge badge-secondary" style="font-size: 0.7rem;">Cerrada</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; color: var(--text-muted); font-size: 0.85rem;">
                            No hay registros de cajas.
                        </div>
                    @endforelse
                </div>
            </div>
            @endif

            {{-- Logs: solo visible para Administrador --}}
            @if(Auth::user()->hasRole('Administrador'))
            <div class="grid-card" style="display: flex; flex-direction: column;">
                <div class="grid-card-title">
                    <span><i class="bi bi-shield-lock-fill" style="color: var(--warning); margin-right: 8px;"></i> Registro de Logs</span>
                    <span class="badge badge-warning" style="font-size: 0.7rem; padding: 2px 8px;">Seguridad</span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 15px; flex-grow: 1; overflow-y: auto; max-height: 480px; padding-right: 5px;">
                    @forelse($logs as $log)
                        @php
                            $badgeClass = 'badge-secondary';
                            if ($log->accion === 'INSERT') $badgeClass = 'badge-success';
                            if ($log->accion === 'DELETE') $badgeClass = 'badge-danger';
                            if ($log->accion === 'LOGIN')  $badgeClass = 'badge-success';
                            if ($log->accion === 'LOGOUT') $badgeClass = 'badge-warning';
                            
                            $isJson = is_string($log->datos_nuevos) && is_array(json_decode($log->datos_nuevos, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
                            $mensaje = $isJson ? 'Datos registrados en el sistema' : $log->datos_nuevos;
                            if ($log->accion === 'UPDATE' && $isJson) {
                                $mensaje = 'Datos actualizados en el sistema';
                            }
                        @endphp
                        <div style="background-color: rgba(255,255,255,0.02); border: 1px solid var(--card-border); padding: 12px 15px; border-radius: var(--radius-sm);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <span class="badge {{ $badgeClass }}" style="font-size: 0.65rem; padding: 2px 6px;">{{ $log->accion }}</span>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">
                                    {{ \Carbon\Carbon::parse($log->fecha_hora)->diffForHumans() }}
                                </span>
                            </div>
                            <p style="font-size: 0.825rem; color: var(--text-primary); margin-bottom: 6px;">
                                {{ $mensaje }}
                            </p>
                            <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--text-secondary);">
                                <span><i class="bi bi-person"></i> {{ $log->usuario && $log->usuario->persona ? $log->usuario->persona->nombres : ($log->usuario ? $log->usuario->username : 'System') }}</span>
                                <span><i class="bi bi-folder2-open"></i> {{ $log->modulo }}</span>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; color: var(--text-muted); margin-top: 30px;">
                            No hay logs de actividad.
                        </div>
                    @endforelse
                </div>
                
                <div style="margin-top: 15px; text-align: center;">
                    <a href="#" class="btn btn-outline btn-sm" style="width: 100%; font-size: 0.8rem;">Ver historial de seguridad completo <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
            @endif

            {{-- Deudas: solo visible para Administrador y Director --}}
            @if(Auth::user()->hasAnyRole(['Administrador','Director']))
            <div class="grid-card">
                <div class="grid-card-title">
                    <span><i class="bi bi-wallet2" style="color: var(--warning); margin-right: 8px;"></i> Estado de Deudas</span>
                <span style="font-size: 0.8rem; color: var(--text-secondary);">
                    <i class="bi bi-circle-fill" style="color: #ef4444; font-size: 0.55rem;"></i> {{ $countPendientes }} pendientes &nbsp;
                    <i class="bi bi-circle-fill" style="color: var(--success); font-size: 0.55rem;"></i> {{ $countPagadas }} pagadas
                </span>
            </div>

            <div class="deuda-totales-bar">
                <div class="deuda-total-item">
                    <span class="deuda-total-label"><i class="bi bi-exclamation-triangle-fill"></i> Total Pendiente</span>
                    <span class="deuda-total-value danger">Bs. {{ number_format($totalDeudaPendiente, 2) }}</span>
                </div>
                <div style="width: 1px; background: var(--card-border); align-self: stretch;"></div>
                <div class="deuda-total-item">
                    <span class="deuda-total-label"><i class="bi bi-check-circle-fill"></i> Total Pagado</span>
                    <span class="deuda-total-value success">Bs. {{ number_format($totalDeudaPagada, 2) }}</span>
                </div>
            </div>

            <div class="deuda-tabs">
                <button class="deuda-tab-btn active-pendiente" id="tab-pendiente" onclick="switchDeudaTab('pendiente')">⚠ Pendientes ({{ $countPendientes }})</button>
                <button class="deuda-tab-btn" id="tab-pagada" onclick="switchDeudaTab('pagada')">✓ Pagadas ({{ $countPagadas }})</button>
            </div>

            <div class="deuda-tab-panel active" id="panel-pendiente">
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead><tr><th>Alumno</th><th>Concepto</th><th>Monto</th><th>Fecha</th></tr></thead>
                        <tbody>
                            @forelse($deudasPendientes as $d)
                                <tr>
                                    <td style="font-weight:500;">{{ $d->estudiante && $d->estudiante->persona ? $d->estudiante->persona->nombre_completo : 'N/A' }}</td>
                                    <td>{{ $d->concepto ? $d->concepto->nombre : 'N/A' }}</td>
                                    <td style="font-weight:600; color:#ef4444;">Bs. {{ number_format($d->monto, 2) }}</td>
                                    <td style="font-size:0.78rem; color:var(--text-secondary);">{{ \Carbon\Carbon::parse($d->fecha_generacion)->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="text-align:center; color:var(--text-muted); padding:20px;"><i class="bi bi-check-all" style="display:block; font-size:1.3rem; margin-bottom:6px;"></i>¡Sin deudas pendientes!</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="deuda-tab-panel" id="panel-pagada">
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead><tr><th>Alumno</th><th>Concepto</th><th>Monto</th><th>Fecha</th></tr></thead>
                        <tbody>
                            @forelse($deudasPagadas as $d)
                                <tr>
                                    <td style="font-weight:500;">{{ $d->estudiante && $d->estudiante->persona ? $d->estudiante->persona->nombre_completo : 'N/A' }}</td>
                                    <td>{{ $d->concepto ? $d->concepto->nombre : 'N/A' }}</td>
                                    <td style="font-weight:600; color:var(--success);">Bs. {{ number_format($d->monto, 2) }}</td>
                                    <td style="font-size:0.78rem; color:var(--text-secondary);">{{ \Carbon\Carbon::parse($d->fecha_generacion)->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="text-align:center; color:var(--text-muted); padding:20px;">No hay deudas pagadas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
        </div>
        @endif
    </div>

@endif
@endsection

@section('scripts')
<script>
/**
 * Tabs de deudas (Administrador)
 */
function switchDeudaTab(tab) {
    const panels  = document.querySelectorAll('.deuda-tab-panel');
    const buttons = document.querySelectorAll('.deuda-tab-btn');

    panels.forEach(p  => p.classList.remove('active'));
    buttons.forEach(b => b.classList.remove('active-pendiente', 'active-pagada'));

    document.getElementById('panel-' + tab).classList.add('active');
    const btn = document.getElementById('tab-' + tab);
    btn.classList.add('active-' + tab);
}
</script>
@endsection
