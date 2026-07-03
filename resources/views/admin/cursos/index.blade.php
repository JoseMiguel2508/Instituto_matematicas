@extends('layouts.app')

@section('title', 'Cursos | Instituto de Matemáticas UPDS')
@section('page_title', 'Gestión de Cursos')
@section('page_description', 'Administración del plan de estudios y materias')

@section('content')
    @if(session('success'))
        <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error) <li><i class="bi bi-exclamation-triangle-fill"></i> {{ $error }}</li> @endforeach</ul></div>
    @endif

    <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
        <button onclick="openModal()" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Nuevo Curso</button>
    </div>

    <div class="grid-card">
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Especialidad</th>
                        <th>Nivel</th>
                        <th>Duración (Hrs)</th>
                        <th>Créditos</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cursos as $c)
                        <tr>
                            <td><code>{{ $c->codigo_curso }}</code></td>
                            <td style="font-weight: 500;">{{ $c->nombre_curso }}</td>
                            <td>{{ $c->especialidad ? $c->especialidad->nombre : 'N/A' }}</td>
                            <td>{{ $c->nivel ? $c->nivel->nombre : 'N/A' }}</td>
                            <td>{{ $c->duracion_horas ? $c->duracion_horas . ' hrs' : 'N/A' }}</td>
                            <td>{{ $c->creditos }}</td>
                            <td><span class="badge {{ $c->estado === 'Activo' ? 'badge-success' : 'badge-danger' }}">{{ $c->estado }}</span></td>
                            <td>
                                <form action="{{ route('admin.cursos.destroy', $c->id_curso) }}" method="POST" style="display: inline-block; margin: 0;" onsubmit="return confirm('¿Eliminar curso?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline btn-sm" style="color: var(--danger); border-color: rgba(239, 68, 68, 0.2);"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" style="text-align: center; color: var(--text-muted); padding: 30px;">No hay cursos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal-backdrop" id="register-modal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size: 1.25rem;"><i class="bi bi-plus-circle" style="color: var(--accent-cyan);"></i> Crear Curso</h3>
                <button onclick="closeModal()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.5rem;"><i class="bi bi-x"></i></button>
            </div>
            <!-- NOTE: Using standard form create, usually we would load especialidades and niveles from controller create() method into this view or pass them in index() -->
            <form action="{{ route('admin.cursos.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Especialidad</label>
                            <select name="id_especialidad" class="form-control" required>
                                @foreach(\App\Models\Especialidad::where('estado','Activa')->get() as $esp)
                                    <option value="{{ $esp->id_especialidad }}">{{ $esp->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nivel</label>
                            <select name="id_nivel" class="form-control" required>
                                @foreach(\App\Models\Nivel::all() as $niv)
                                    <option value="{{ $niv->id_nivel }}">{{ $niv->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Código</label>
                            <input type="text" name="codigo_curso" class="form-control" required placeholder="Ej: ALG-101">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nombre del Curso</label>
                            <input type="text" name="nombre_curso" class="form-control" required>
                        </div>
                    </div>
                    <div class="grid-3">
                        <div class="form-group">
                            <label class="form-label">Duración (Horas)</label>
                            <input type="number" name="duracion_horas" class="form-control" value="40" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Créditos</label>
                            <input type="number" name="creditos" class="form-control" value="3" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Estado</label>
                            <select name="estado" class="form-control">
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