@extends('layouts.app')

@section('title', 'Estudiantes y Docentes | Instituto de Matemáticas UPDS')
@section('page_title', 'Gestión de Personas')
@section('page_description', 'Administración de alumnos matriculados y cuerpo de profesores')

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

    <!-- Selection Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div style="display: flex; gap: 10px;">
            <button onclick="switchTab('tab-estudiantes', this)" class="btn btn-primary btn-sm" id="btn-tab-est">Estudiantes</button>
            <button onclick="switchTab('tab-docentes', this)" class="btn btn-outline btn-sm" id="btn-tab-doc">Docentes</button>
        </div>
        <button onclick="openModal()" class="btn btn-primary">
            <i class="bi bi-person-plus-fill"></i> Registrar Alumno
        </button>
    </div>

    <!-- TAB: ESTUDIANTES -->
    <div id="tab-estudiantes" class="grid-card">
        <div class="grid-card-title" style="display: flex; justify-content: space-between; align-items: center;">
            <span><i class="bi bi-people-fill" style="color: var(--accent-cyan);"></i> Alumnos Registrados</span>
            <form action="{{ route('estudiantes.index') }}" method="GET" style="display: flex; gap: 10px; max-width: 400px; margin: 0;">
                <input type="text" name="buscar_ci" class="form-control" placeholder="Buscar por CI (Doc.)..." value="{{ request('buscar_ci') }}" style="padding: 5px 10px; font-size: 0.85rem;">
                <button type="submit" class="btn btn-outline btn-sm">Buscar</button>
                @if(request('buscar_ci'))
                    <a href="{{ route('estudiantes.index') }}" class="btn btn-secondary btn-sm" title="Limpiar">X</a>
                @endif
            </form>
        </div>
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>DNI/Doc</th>
                        <th>Fecha Ingreso</th>
                        <th>Modalidad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($estudiantes as $e)
                        <tr>
                            <td><code>{{ $e->codigo_estudiante }}</code></td>
                            <td style="font-weight: 500;">{{ $e->persona ? $e->persona->nombres : 'N/A' }}</td>
                            <td style="font-weight: 500;">{{ $e->persona ? $e->persona->apellidos : 'N/A' }}</td>
                            <td>{{ $e->persona ? $e->persona->numero_documento : 'N/A' }}</td>
                            <td>{{ $e->fecha_ingreso }}</td>
                            <td>
                                <span class="badge {{ $e->modalidad_estudio === 'Especialidad' ? 'badge-primary' : 'badge-info' }}" style="background-color: {{ $e->modalidad_estudio === 'Especialidad' ? 'var(--accent-blue)' : 'var(--accent-cyan)' }}; color: white;">
                                    {{ $e->modalidad_estudio }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $badge = 'badge-success';
                                    if ($e->estado === 'Retirado') $badge = 'badge-danger';
                                    if ($e->estado === 'Egresado') $badge = 'badge-warning';
                                @endphp
                                <span class="badge {{ $badge }}">{{ $e->estado }}</span>
                            </td>
                            <td>
                                <form action="{{ route('estudiantes.update', $e->id_estudiante) }}" method="POST" style="display: inline-flex; gap: 5px; align-items: center; margin: 0;">
                                    @csrf
                                    <select name="estado" class="form-control" style="padding: 4px 8px; font-size: 0.75rem; width: auto;" onchange="this.form.submit()">
                                        <option value="Activo" {{ $e->estado === 'Activo' ? 'selected' : '' }}>Activo</option>
                                        <option value="Egresado" {{ $e->estado === 'Egresado' ? 'selected' : '' }}>Egresado</option>
                                        <option value="Retirado" {{ $e->estado === 'Retirado' ? 'selected' : '' }}>Retirado</option>
                                    </select>
                                </form>
                                @php
                                    $esp_id = $e->matriculas->first() ? $e->matriculas->first()->id_especialidad : 1;
                                @endphp
                                <button type="button" class="btn btn-outline btn-sm" title="Editar Estudiante" style="padding: 4px 8px; color: var(--accent-blue); border-color: var(--accent-blue);" data-record="{{ json_encode($e) }}" onclick="openEditModal(this)">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <a href="{{ route('estudiantes.certificado', ['id' => $e->id_estudiante, 'especialidad' => $esp_id]) }}" class="btn btn-outline btn-sm" title="Descargar Certificado" style="padding: 4px 8px;">
                                    <i class="bi bi-mortarboard-fill"></i>
                                </a>
                                @if(Auth::user()->hasRole('Administrador'))
                                    <form action="{{ route('estudiantes.destroy', $e->id_estudiante) }}" method="POST" style="display: inline-block; margin: 0;" onsubmit="return confirm('¿Está seguro de eliminar a este estudiante? Esto no se puede deshacer.')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline btn-sm" style="padding: 4px 8px; color: var(--danger); border-color: var(--danger);" title="Eliminar Estudiante">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 30px;">No hay estudiantes registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $estudiantes->links('pagination::bootstrap-4') }}
        </div>
    </div>

    <!-- TAB: DOCENTES (Hidden by default) -->
    <div id="tab-docentes" class="grid-card" style="display: none;">
        <div class="grid-card-title">
            <span><i class="bi bi-person-badge-fill" style="color: var(--accent-blue);"></i> Cuerpo de Docentes</span>
        </div>
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Grado Académico</th>
                        <th>Fecha Contratación</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($docentes as $d)
                        <tr>
                            <td><code>{{ $d->codigo_docente }}</code></td>
                            <td style="font-weight: 500;">{{ $d->persona ? $d->persona->nombres : 'N/A' }}</td>
                            <td style="font-weight: 500;">{{ $d->persona ? $d->persona->apellidos : 'N/A' }}</td>
                            <td>{{ $d->grado_academico }}</td>
                            <td>{{ $d->fecha_contratacion }}</td>
                            <td>
                                <span class="badge {{ $d->estado === 'Activo' ? 'badge-success' : 'badge-danger' }}">
                                    {{ $d->estado }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 30px;">No hay docentes registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Registrar Alumno Modal Backdrop -->
    <div class="modal-backdrop" id="register-modal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size: 1.25rem;"><i class="bi bi-person-plus-fill" style="color: var(--accent-cyan);"></i> Registrar Nuevo Alumno</h3>
                <button onclick="closeModal()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.5rem;"><i class="bi bi-x"></i></button>
            </div>
            <form action="{{ route('estudiantes.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="tipo_documento">Tipo Documento</label>
                        <select id="tipo_documento" name="tipo_documento" class="form-control" required>
                            <option value="DNI">DNI</option>
                            <option value="CE">C.E.</option>
                            <option value="Pasaporte">Pasaporte</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="numero_documento">Número Documento</label>
                        <input type="text" id="numero_documento" name="numero_documento" class="form-control" placeholder="Ingrese el nro de documento" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="modalidad_estudio">Modalidad de Estudio</label>
                        <select id="modalidad_estudio" name="modalidad_estudio" class="form-control" required>
                            <option value="Especialidad">Especialidad (Paga Matrícula y Pensión)</option>
                            <option value="Curso Libre">Curso Libre (Pago único por materia)</option>
                        </select>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="nombres">Nombres</label>
                            <input type="text" id="nombres" name="nombres" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="apellidos">Apellidos</label>
                            <input type="text" id="apellidos" name="apellidos" class="form-control" required>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="fecha_nacimiento">Fecha Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" value="2005-01-01">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="telefono">Teléfono</label>
                            <input type="text" id="telefono" name="telefono" class="form-control" placeholder="999888777">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="ejemplo@correo.com">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="direccion">Dirección</label>
                        <input type="text" id="direccion" name="direccion" class="form-control" placeholder="Dirección de domicilio">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Editar Alumno Modal -->
    <div class="modal-backdrop" id="edit-modal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size: 1.25rem;"><i class="bi bi-pencil-square" style="color: var(--accent-blue);"></i> Editar Estudiante</h3>
                <button onclick="closeEditModal()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.5rem;"><i class="bi bi-x"></i></button>
            </div>
            <form id="edit-form" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="edit_tipo_documento">Tipo Documento</label>
                        <select id="edit_tipo_documento" name="tipo_documento" class="form-control" required>
                            <option value="DNI">DNI</option>
                            <option value="CE">C.E.</option>
                            <option value="Pasaporte">Pasaporte</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit_numero_documento">Número Documento</label>
                        <input type="text" id="edit_numero_documento" name="numero_documento" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit_modalidad_estudio">Modalidad de Estudio</label>
                        <select id="edit_modalidad_estudio" name="modalidad_estudio" class="form-control" required>
                            <option value="Especialidad">Especialidad</option>
                            <option value="Curso Libre">Curso Libre</option>
                        </select>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="edit_nombres">Nombres</label>
                            <input type="text" id="edit_nombres" name="nombres" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit_apellidos">Apellidos</label>
                            <input type="text" id="edit_apellidos" name="apellidos" class="form-control" required>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="edit_fecha_nacimiento">Fecha Nacimiento</label>
                            <input type="date" id="edit_fecha_nacimiento" name="fecha_nacimiento" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit_telefono">Teléfono</label>
                            <input type="text" id="edit_telefono" name="telefono" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit_email">Correo Electrónico</label>
                        <input type="email" id="edit_email" name="email" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit_direccion">Dirección</label>
                        <input type="text" id="edit_direccion" name="direccion" class="form-control">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="edit_estado">Estado</label>
                        <select id="edit_estado" name="estado" class="form-control" required>
                            <option value="Activo">Activo</option>
                            <option value="Egresado">Egresado</option>
                            <option value="Retirado">Retirado</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function switchTab(tabId, btn) {
            document.getElementById('tab-estudiantes').style.display = 'none';
            document.getElementById('tab-docentes').style.display = 'none';
            
            document.getElementById('btn-tab-est').className = 'btn btn-outline btn-sm';
            document.getElementById('btn-tab-doc').className = 'btn btn-outline btn-sm';
            
            document.getElementById(tabId).style.display = 'block';
            btn.className = 'btn btn-primary btn-sm';
        }

        function openModal() {
            document.getElementById('register-modal').classList.add('open');
        }

        function closeModal() {
            document.getElementById('register-modal').classList.remove('open');
        }

        function openEditModal(btn) {
            let record = JSON.parse(btn.getAttribute('data-record'));
            let persona = record.persona || {};
            document.getElementById('edit-form').action = '/estudiantes/update/' + record.id_estudiante;
            document.getElementById('edit_tipo_documento').value = persona.tipo_documento || 'DNI';
            document.getElementById('edit_numero_documento').value = persona.numero_documento || '';
            document.getElementById('edit_nombres').value = persona.nombres || '';
            document.getElementById('edit_apellidos').value = persona.apellidos || '';
            document.getElementById('edit_fecha_nacimiento').value = persona.fecha_nacimiento ? persona.fecha_nacimiento.split(' ')[0] : '';
            document.getElementById('edit_telefono').value = persona.telefono || '';
            document.getElementById('edit_email').value = persona.email || '';
            document.getElementById('edit_direccion').value = persona.direccion || '';
            document.getElementById('edit_modalidad_estudio').value = record.modalidad_estudio || 'Especialidad';
            document.getElementById('edit_estado').value = record.estado || '';
            document.getElementById('edit-modal').classList.add('open');
        }
        function closeEditModal() {
            document.getElementById('edit-modal').classList.remove('open');
        }
    </script>
@endsection
