@extends('layouts.app')
@section('title', 'Reporte de Notas y Cursos')
@section('page_title', 'Reporte Académico')
@section('page_description', 'Listado de estudiantes con sus cursos y calificaciones')
@section('content')
<div class="grid-card printable-report">
    <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <form method="GET" action="{{ route('admin.reportes.estudiantes') }}" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="text" name="ci" value="{{ request('ci') }}" placeholder="Buscar por CI" class="form-control" style="width: 150px; height: 36px;">
            <select name="estado" class="form-control" style="width: auto; height: 36px;">
                <option value="">Todos los Estados</option>
                <option value="Activo" {{ request('estado') == 'Activo' ? 'selected' : '' }}>Activo</option>
                <option value="Inactivo" {{ request('estado') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                <option value="Retirado" {{ request('estado') == 'Retirado' ? 'selected' : '' }}>Retirado</option>
            </select>
            <button type="submit" class="btn btn-primary" style="height: 36px;"><i class="bi bi-filter"></i> Filtrar</button>
        </form>
        <button onclick="window.print()" class="btn btn-outline"><i class="bi bi-printer"></i> Imprimir</button>
    </div>

    <!-- Header para impresión -->
    <div class="print-only" style="display: none; text-align: center; margin-bottom: 20px;">
        <h2 style="color: #0f172a; margin-bottom: 5px;">Reporte Académico de Estudiantes</h2>
        <p style="color: #64748b;">Instituto de Matemáticas UPDS - {{ date('d/m/Y') }}</p>
    </div>

    <div class="table-responsive">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>CI / Documento</th>
                    <th>Nombres y Apellidos</th>
                    <th>Curso</th>
                    <th>Grupo</th>
                    <th>Estado Alumno</th>
                    <th>Nota Final</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detalles as $d)
                @php
                    $estudiante = $d->inscripcion->matricula->estudiante;
                    $persona = $estudiante->persona;
                    $curso = $d->grupo->curso;
                @endphp
                <tr>
                    <td>{{ $persona ? $persona->numero_documento : 'N/A' }}</td>
                    <td style="font-weight: 500;">{{ $persona ? $persona->nombre_completo : 'N/A' }}</td>
                    <td>{{ $curso ? $curso->nombre_curso : 'N/A' }}</td>
                    <td>{{ $d->grupo->numero_grupo }}</td>
                    <td><span class="badge {{ $estudiante->estado == 'Activo' ? 'badge-success' : 'badge-secondary' }}">{{ $estudiante->estado }}</span></td>
                    <td style="font-weight: 700; color: {{ $d->notaFinal && $d->notaFinal->estado == 'Aprobado' ? 'var(--success)' : ($d->notaFinal ? 'var(--danger)' : 'var(--text-muted)') }}">
                        @if($d->notaFinal)
                            {{ number_format($d->notaFinal->nota, 2) }} ({{ $d->notaFinal->estado }})
                        @else
                            Sin nota
                        @endif
                    </td>
                </tr>
                @endforeach
                @if($detalles->isEmpty())
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 20px;">No se encontraron registros.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
