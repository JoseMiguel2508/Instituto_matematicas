@extends('layouts.app')
@section('title', 'Reporte Financiero')
@section('page_title', 'Reporte Financiero')
@section('page_description', 'Listado de ingresos y pagos registrados')
@section('content')
<div class="grid-card printable-report">
    <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <form method="GET" action="{{ route('admin.reportes.financiero') }}" style="display: flex; gap: 10px; align-items: center;">
            <div>
                <label style="font-size: 0.8rem; color: var(--text-secondary);">Desde:</label>
                <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="form-control" style="width: auto; height: 36px;">
            </div>
            <div>
                <label style="font-size: 0.8rem; color: var(--text-secondary);">Hasta:</label>
                <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" class="form-control" style="width: auto; height: 36px;">
            </div>
            <button type="submit" class="btn btn-primary" style="align-self: flex-end; height: 36px;"><i class="bi bi-filter"></i> Filtrar</button>
        </form>
        
        <div style="text-align: right;">
            <h3 style="margin: 0; color: var(--success); font-weight: 700;">Total: Bs. {{ number_format($totalRecaudado, 2) }}</h3>
            <button onclick="window.print()" class="btn btn-outline btn-sm" style="margin-top: 8px;"><i class="bi bi-printer"></i> Imprimir Reporte</button>
        </div>
    </div>

    <!-- Header para impresión -->
    <div class="print-only" style="display: none; text-align: center; margin-bottom: 20px;">
        <h2 style="color: #0f172a; margin-bottom: 5px;">Reporte Financiero - Transacciones</h2>
        <h3 style="color: #10b981; margin: 5px 0;">Total Recaudado: Bs. {{ number_format($totalRecaudado, 2) }}</h3>
        <p style="color: #64748b; font-size: 0.9rem;">
            @if(request('fecha_inicio') || request('fecha_fin'))
                Periodo: {{ request('fecha_inicio') ?: 'Inicio' }} al {{ request('fecha_fin') ?: 'Actualidad' }}
            @else
                Histórico Completo
            @endif
        </p>
    </div>

    <div class="table-responsive">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Nro Comprobante</th>
                    <th>Fecha de Pago</th>
                    <th>Alumno</th>
                    <th>Cajero</th>
                    <th>Método</th>
                    <th>Monto Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pagos as $p)
                <tr>
                    <td><code>{{ $p->numero_comprobante }}</code></td>
                    <td style="font-size: 0.85rem;">{{ \Carbon\Carbon::parse($p->fecha_pago)->format('d/m/Y H:i') }}</td>
                    <td style="font-weight: 500;">{{ $p->estudiante && $p->estudiante->persona ? $p->estudiante->persona->nombre_completo : 'N/A' }}</td>
                    <td>{{ $p->usuario && $p->usuario->persona ? $p->usuario->persona->nombres : 'N/A' }}</td>
                    <td><span class="badge badge-secondary" style="font-size: 0.7rem;">{{ $p->metodo_pago }}</span></td>
                    <td style="font-weight: 700; color: var(--accent-cyan);">Bs. {{ number_format($p->monto_total, 2) }}</td>
                </tr>
                @endforeach
                @if($pagos->isEmpty())
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 20px;">No se encontraron pagos en este rango de fechas.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
