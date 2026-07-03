@extends('layouts.app')

@section('title', 'Control de Caja | Instituto de Matemáticas UPDS')
@section('page_title', 'Apertura y Cierre de Caja')
@section('page_description', 'Gestión de la sesión diaria y arqueo de caja')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li><i class="bi bi-exclamation-triangle-fill"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid" style="grid-template-columns: 2fr 1fr; gap: 25px; margin-bottom: 30px;">
        <!-- Panel Principal (Apertura/Cierre) -->
        <div class="grid-card">
            <div class="grid-card-title">
                <i class="bi bi-cash-stack" style="color: var(--accent-cyan); margin-right: 10px;"></i>
                Estado de Caja: {{ $caja->nombre }}
            </div>

            @if(!$sesionAbierta)
                <!-- Formulario de Apertura -->
                <div style="text-align: center; padding: 30px 10px;">
                    <i class="bi bi-lock-fill" style="font-size: 3rem; color: var(--danger); margin-bottom: 15px; display: block;"></i>
                    <h3 style="color: var(--text-color); margin-bottom: 5px;">La Caja está CERRADA</h3>
                    <p style="color: var(--text-secondary); margin-bottom: 25px;">Debes aperturar la caja para comenzar a registrar cobros en el sistema.</p>
                    
                    <form action="{{ route('caja.abrir') }}" method="POST" style="max-width: 400px; margin: 0 auto; text-align: left; background: rgba(0,0,0,0.1); padding: 20px; border-radius: 8px; border: 1px solid var(--card-border);">
                        @csrf
                        <input type="hidden" name="id_caja" value="{{ $caja->id_caja }}">
                        
                        <div class="form-group">
                            <label class="form-label" for="monto_inicial">Fondo Inicial (Bs.)</label>
                            <input type="number" step="0.01" min="0" id="monto_inicial" name="monto_inicial" class="form-control" placeholder="Ej. 200.00" required>
                            <small style="color: var(--text-muted); display: block; margin-top: 5px;">Monto físico en billetes y monedas con el que inicias el turno.</small>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label class="form-label" for="observaciones_apertura">Observaciones (Opcional)</label>
                            <input type="text" id="observaciones_apertura" name="observaciones_apertura" class="form-control" placeholder="Ej. Billetes de baja denominación">
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                            <i class="bi bi-unlock-fill"></i> Aperturar Caja
                        </button>
                    </form>
                </div>
            @else
                <!-- Formulario de Cierre -->
                <div style="padding: 10px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--card-border); padding-bottom: 15px;">
                        <div>
                            <h3 style="color: var(--success); margin: 0; display: flex; align-items: center; gap: 8px;">
                                <i class="bi bi-unlock-fill"></i> CAJA ABIERTA
                            </h3>
                            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 5px;">
                                Aperturada: {{ \Carbon\Carbon::parse($sesionAbierta->fecha_apertura)->format('d/m/Y h:i A') }}
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 0.85rem; color: var(--text-muted);">Fondo Inicial</div>
                            <div style="font-weight: 700; color: var(--text-color); font-size: 1.1rem;">Bs. {{ number_format($sesionAbierta->monto_inicial, 2) }}</div>
                        </div>
                    </div>

                    <!-- Resumen del día -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
                        <div style="background: rgba(34,197,94,0.05); border: 1px solid rgba(34,197,94,0.2); padding: 15px; border-radius: 8px;">
                            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 5px;"><i class="bi bi-cash"></i> Ingresos Efectivo</div>
                            <h4 style="margin: 0; color: var(--success); font-size: 1.5rem;">Bs. {{ number_format($ingresosEfectivo, 2) }}</h4>
                        </div>
                        <div style="background: rgba(6,182,212,0.05); border: 1px solid rgba(6,182,212,0.2); padding: 15px; border-radius: 8px;">
                            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 5px;"><i class="bi bi-phone"></i> Ingresos QR/Transf.</div>
                            <h4 style="margin: 0; color: var(--accent-cyan); font-size: 1.5rem;">Bs. {{ number_format($ingresosOnline, 2) }}</h4>
                        </div>
                    </div>

                    @php
                        $montoEsperado = $sesionAbierta->monto_inicial + $ingresosEfectivo;
                    @endphp

                    <div style="background: rgba(0,0,0,0.1); padding: 20px; border-radius: 8px; border: 1px solid var(--card-border);">
                        <h4 style="margin-top: 0; margin-bottom: 15px; color: var(--text-color);">Cierre y Arqueo (Solo Efectivo)</h4>
                        
                        <div style="display: flex; justify-content: space-between; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px dashed rgba(255,255,255,0.1);">
                            <span style="color: var(--text-secondary);">Monto Efectivo Esperado en Caja:</span>
                            <strong style="color: var(--text-color); font-size: 1.1rem;">Bs. {{ number_format($montoEsperado, 2) }}</strong>
                        </div>

                        <form action="{{ route('caja.cerrar') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id_sesion_caja" value="{{ $sesionAbierta->id_sesion_caja }}">
                            
                            <div class="form-group">
                                <label class="form-label" for="monto_final_real">Efectivo Real Contado (Bs.)</label>
                                <input type="number" step="0.01" min="0" id="monto_final_real" name="monto_final_real" class="form-control" placeholder="Ej. {{ number_format($montoEsperado, 2) }}" required style="font-weight: bold; font-size: 1.1rem;">
                                <small style="color: var(--text-muted); display: block; margin-top: 5px;">Cuenta el dinero físico e ingresa el monto total.</small>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="observaciones_cierre">Observaciones (Opcional)</label>
                                <input type="text" id="observaciones_cierre" name="observaciones_cierre" class="form-control" placeholder="Justificación si hay sobrantes o faltantes">
                            </div>

                            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; background: var(--danger); border-color: var(--danger);" onclick="return confirm('¿Estás seguro de que deseas cerrar la caja? Ya no podrás registrar más cobros hasta aperturar una nueva sesión.')">
                                <i class="bi bi-lock-fill"></i> Realizar Cierre de Caja
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <!-- Panel Lateral (Historial) -->
        <div class="grid-card">
            <div class="grid-card-title">
                <i class="bi bi-clock-history" style="color: var(--text-muted); margin-right: 10px;"></i>
                Últimas Sesiones
            </div>
            
            @if($historial->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    @foreach($historial as $sesion)
                        <div style="background: rgba(0,0,0,0.1); border: 1px solid var(--card-border); border-left: 3px solid {{ $sesion->estado === 'Abierta' ? 'var(--success)' : 'var(--text-muted)' }}; padding: 12px; border-radius: 4px;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 5px;">
                                <strong style="font-size: 0.9rem; color: var(--text-color);">
                                    {{ \Carbon\Carbon::parse($sesion->fecha_apertura)->format('d/m/Y') }}
                                </strong>
                                <span class="badge {{ $sesion->estado === 'Abierta' ? 'badge-success' : 'badge-secondary' }}" style="font-size: 0.65rem;">
                                    {{ $sesion->estado }}
                                </span>
                            </div>
                            
                            <div style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 5px;">
                                Apertura: {{ \Carbon\Carbon::parse($sesion->fecha_apertura)->format('h:i A') }}
                                @if($sesion->fecha_cierre)
                                    <br>Cierre: {{ \Carbon\Carbon::parse($sesion->fecha_cierre)->format('h:i A') }}
                                @endif
                            </div>
                            
                            @if($sesion->estado === 'Cerrada')
                                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-top: 8px; border-top: 1px dashed rgba(255,255,255,0.1); padding-top: 8px;">
                                    <span style="color: var(--text-muted);">Diferencia:</span>
                                    <strong style="color: {{ $sesion->diferencia == 0 ? 'var(--success)' : 'var(--danger)' }};">
                                        Bs. {{ number_format($sesion->diferencia, 2) }}
                                    </strong>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 20px 10px; color: var(--text-muted);">
                    <p style="font-size: 0.85rem;">No tienes historial de sesiones anteriores.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
