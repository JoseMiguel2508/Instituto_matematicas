<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante {{ $pago->numero_comprobante }} | MateFácil - Instituto de Matemáticas</title>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8fafc;
            color: #0f172a;
            padding: 20px;
        }
        .actions-bar {
            max-width: 700px;
            margin: 0 auto 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        @media print {
            .actions-bar {
                display: none;
            }
            body {
                background-color: white;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    
    <!-- Top actions bar for the browser -->
    <div class="actions-bar">
        <a href="{{ route('pagos.index') }}" class="btn btn-secondary btn-sm" style="background-color: #64748b; color: white;">
            <i class="bi bi-arrow-left"></i> Volver a Pagos
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm">
            <i class="bi bi-printer"></i> Imprimir Comprobante
        </button>
    </div>

    <!-- Printable Receipt Card -->
    <div class="printable-bill">
        <div class="printable-bill-header">
            <div class="printable-bill-logo" style="text-align: left;">
                <img src="{{ asset('img/logo.jpg') }}" alt="MateFácil Logo" style="max-height: 70px; margin-bottom: 8px; mix-blend-mode: multiply;">
                <h2 style="display: none;">MATEFÁCIL</h2>
                <p>Instituto de Matemáticas</p>
                <p style="font-size: 0.75rem; margin-top: 4px;">R.M. Nro. 248/01 - Gestión Académica Superior</p>
            </div>
            <div class="printable-bill-meta">
                <h3>{{ strtoupper($pago->tipo_comprobante) }}</h3>
                <h2 style="color: var(--accent-blue); font-size: 1.4rem; margin: 5px 0;">{{ $pago->numero_comprobante }}</h2>
                <p style="font-size: 0.8rem; color: #64748b;">Fecha: {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="printable-bill-info">
            <div>
                <h4>DATOS DEL ESTUDIANTE</h4>
                <p><strong>Nombre:</strong> {{ $pago->estudiante->persona->nombre_completo }}</p>
                <p><strong>Código Alumno:</strong> {{ $pago->estudiante->codigo_estudiante }}</p>
                <p><strong>Nro Documento:</strong> {{ $pago->estudiante->persona->tipo_documento }} {{ $pago->estudiante->persona->numero_documento }}</p>
                @if($pago->estudiante->persona->email)
                    <p><strong>Correo:</strong> {{ $pago->estudiante->persona->email }}</p>
                @endif
            </div>
            <div>
                <h4>INFORMACIÓN ACADÉMICA</h4>
                @if($pago->matricula)
                    <p><strong>Carrera/Especialidad:</strong> {{ $pago->matricula->especialidad->nombre }}</p>
                    <p><strong>Periodo:</strong> {{ $pago->matricula->periodo->nombre }} ({{ $pago->matricula->periodo->codigo }})</p>
                @else
                    <p><strong>Periodo:</strong> Periodo Vigente 2026-I</p>
                @endif
                <p><strong>Método Pago:</strong> {{ $pago->metodo_pago }}</p>
                <p><strong>Estado Comprobante:</strong> <span style="color: #10b981; font-weight: 600;">{{ $pago->estado }}</span></p>
            </div>
        </div>

        <table class="printable-bill-table">
            <thead>
                <tr>
                    <th>Código Concepto</th>
                    <th>Descripción Concepto</th>
                    <th style="text-align: right;">Importe (Bs.)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pago->detalles as $det)
                    <tr>
                        <td><code>{{ $det->concepto->codigo }}</code></td>
                        <td>{{ $det->descripcion }}</td>
                        <td style="text-align: right;">{{ number_format($det->monto_aplicado, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="printable-bill-total">
            Total Cancelado: Bs. {{ number_format($pago->monto_total, 2) }}
        </div>

        <div style="margin-top: 60px; display: flex; justify-content: space-around; font-size: 0.8rem; text-align: center;">
            <div style="border-top: 1px solid #94a3b8; width: 200px; padding-top: 8px; color: #475569;">
                Firma Alumno / Depositante
            </div>
            <div style="border-top: 1px solid #94a3b8; width: 200px; padding-top: 8px; color: #475569;">
                Caja / Auxiliar Administrativo
                <br>
                <span style="font-size: 0.7rem; color: #94a3b8;">Ref. {{ $pago->usuario->persona ? $pago->usuario->persona->nombres : $pago->usuario->username }}</span>
            </div>
        </div>

        <div class="printable-bill-footer">
            <p>Este comprobante de pago constituye constancia oficial de MateFácil - Instituto de Matemáticas.</p>
            <p style="margin-top: 5px;">¡Gracias por su puntualidad en sus compromisos financieros!</p>
        </div>
    </div>

    <script>
        // Trigger print dialog automatically when loaded in print workflow
        window.addEventListener('DOMContentLoaded', () => {
            // Optional auto print triggers can be disabled if irritating, 
            // but for receipts, prompting is very handy!
        });
    </script>
</body>
</html>
