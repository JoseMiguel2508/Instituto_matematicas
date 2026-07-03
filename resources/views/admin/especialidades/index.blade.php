@extends('layouts.app')

@section('title', 'Especialidades | Instituto de Matemáticas UPDS')
@section('page_title', 'Gestión de Especialidades')
@section('page_description', 'Administración de los programas o especialidades académicas')

@section('content')
    @if(session('success'))
        <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error) <li><i class="bi bi-exclamation-triangle-fill"></i> {{ $error }}</li> @endforeach</ul></div>
    @endif

    <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
        <button onclick="openModal()" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Nueva Especialidad</button>
    </div>

    <div class="grid-card">
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($especialidades as $e)
                        <tr>
                            <td><code>{{ $e->codigo }}</code></td>
                            <td style="font-weight: 500;">{{ $e->nombre }}</td>
                            <td>{{ Str::limit($e->descripcion, 50) }}</td>
                            <td><span class="badge {{ $e->estado === 'Activa' ? 'badge-success' : 'badge-danger' }}">{{ $e->estado }}</span></td>
                            <td>
                                <form action="{{ route('admin.especialidades.update', $e->id_especialidad) }}" method="POST" style="display: inline-flex; gap: 5px; align-items: center; margin: 0;">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="codigo" value="{{ $e->codigo }}">
                                    <input type="hidden" name="nombre" value="{{ $e->nombre }}">
                                    <input type="hidden" name="descripcion" value="{{ $e->descripcion }}">
                                    <select name="estado" class="form-control" style="padding: 4px 8px; font-size: 0.75rem; width: auto;" onchange="this.form.submit()">
                                        <option value="Activa" {{ $e->estado === 'Activa' ? 'selected' : '' }}>Activa</option>
                                        <option value="Inactiva" {{ $e->estado === 'Inactiva' ? 'selected' : '' }}>Inactiva</option>
                                    </select>
                                </form>
                                <form action="{{ route('admin.especialidades.destroy', $e->id_especialidad) }}" method="POST" style="display: inline-block; margin: 0;" onsubmit="return confirm('¿Eliminar especialidad?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline btn-sm" style="color: var(--danger); border-color: rgba(239, 68, 68, 0.2);"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align: center; color: var(--text-muted); padding: 30px;">No hay especialidades registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal-backdrop" id="register-modal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size: 1.25rem;"><i class="bi bi-plus-circle" style="color: var(--accent-cyan);"></i> Registrar Especialidad</h3>
                <button onclick="closeModal()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.5rem;"><i class="bi bi-x"></i></button>
            </div>
            <form action="{{ route('admin.especialidades.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Código</label>
                        <input type="text" name="codigo" class="form-control" required placeholder="Ej: MAT-01">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-control">
                            <option value="Activa">Activa</option>
                            <option value="Inactiva">Inactiva</option>
                        </select>
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