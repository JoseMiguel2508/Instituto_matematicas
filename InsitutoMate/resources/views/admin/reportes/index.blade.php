@extends('layouts.app')
@section('title', 'Módulo de Reportes')
@section('page_title', 'Reportes y Estadísticas')
@section('page_description', 'Centro de generación de reportes del instituto')
@section('content')
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
    <div class="stat-card" style="cursor: pointer;" onclick="window.location='{{ route('admin.reportes.estudiantes') }}'">
        <div class="stat-icon" style="color: var(--accent-blue); background: rgba(99,102,241,0.15);">
            <i class="bi bi-people-fill" style="font-size: 28px;"></i>
        </div>
        <div>
            <div class="stat-label">Reporte de Estudiantes</div>
            <div class="stat-desc">Lista completa, filtrado por estado y datos personales.</div>
        </div>
    </div>
    <div class="stat-card" style="cursor: pointer;" onclick="window.location='{{ route('admin.reportes.financiero') }}'">
        <div class="stat-icon" style="color: var(--success); background: rgba(34,197,94,0.15);">
            <i class="bi bi-cash-stack" style="font-size: 28px;"></i>
        </div>
        <div>
            <div class="stat-label">Reporte Financiero</div>
            <div class="stat-desc">Ingresos, cobros y recaudación por rangos de fecha.</div>
        </div>
    </div>
</div>
@endsection
