@extends('layouts.app')

@section('title', 'Tarifas | Instituto de Matemáticas UPDS')
@section('page_title', 'Conceptos de Cobro y Tarifas')
@section('page_description', 'Administración de los montos a cobrar por matrícula, pensiones, etc.')

@section('content')
    @if(session('success'))
        <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error) <li><i class="bi bi-exclamation-triangle-fill"></i> {{ $error }}</li> @endforeach</ul></div>
    @endif

    <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
        <button onclick="openModal()" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Nueva Tarifa</button>
    </div>

    <div class="grid-card">
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Concepto</th>
                        <th>Monto Base</th>
                        <th>Tipo</th>
                        <th>Obligatorio</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tarifas as $t)
                        <tr>
                            <td><code>{{ $t->codigo }}</code></td>
                            <td style="font-weight: 500;">{{ $t->nombre }}</td>
                            <td>S/ {{ number_format($t->monto_base, 2) }}</td>
                            <td>{{ $t->tipo }}</td>
                            <td>
                                @if($t->es_obligatorio)
                                    <span class="badge badge-warning">Sí</span>
                                @else
                                    <span class="badge badge-success" style="opacity: 0.7;">No</span>
                                @endif
                            </td>
                            <td><span class="badge {{ $t->estado === 'Activo' ? 'badge-success' : 'badge-danger' }}">{{ $t->estado }}</span></td>
                            <td>
                                <form action="{{ route('admin.tarifas.destroy', $t->id_concepto) }}" method="POST" style="display: inline-block; margin: 0;" onsubmit="return confirm('¿Eliminar tarifa?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline btn-sm" style="color: var(--danger); border-color: rgba(239, 68, 68, 0.2);"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 30px;">No hay tarifas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal-backdrop" id="register-modal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size: 1.25rem;"><i class="bi bi-plus-circle" style="color: var(--accent-cyan);"></i> Nueva Tarifa</h3>
                <button onclick="closeModal()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.5rem;"><i class="bi bi-x"></i></button>
            </div>
            <form action="{{ route('admin.tarifas.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Código</label>
                            <input type="text" name="codigo" class="form-control" required placeholder="Ej: MAT-01">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nombre del Concepto</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Monto Base (S/)</label>
                            <input type="number" step="0.01" name="monto_base" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tipo de Cobro</label>
                            <select name="tipo" class="form-control" required>
                                <option value="Unico">Único</option>
                                <option value="Mensual">Mensual</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Obligatorio</label>
                            <select name="es_obligatorio" class="form-control" required>
                                <option value="1">Sí (Aplica a todos)</option>
                                <option value="0">No (Opcional)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Estado</label>
                            <select name="estado" class="form-control" required>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function openModal() { document.getElementById('register-modal').classList.add('open'); }
        function closeModal() { document.getElementById('register-modal').classList.remove('open'); }
    </script>
@endsection