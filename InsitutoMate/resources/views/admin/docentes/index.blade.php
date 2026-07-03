@extends('layouts.app')

@section('title', 'Gestión de Docentes | Instituto de Matemáticas UPDS')
@section('page_title', 'Gestión de Docentes')
@section('page_description', 'Administración del cuerpo docente y creación de cuentas')

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

    <div style="display: flex; justify-content: flex-end; align-items: center; margin-bottom: 20px;">
        <button onclick="openModal()" class="btn btn-primary">
            <i class="bi bi-person-plus-fill"></i> Registrar Docente
        </button>
    </div>

    <div class="grid-card">
        <div class="grid-card-title">
            <span><i class="bi bi-person-video3" style="color: var(--accent-blue);"></i> Docentes Registrados</span>
        </div>
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Doc. Identidad</th>
                        <th>Grado Académico</th>
                        <th>Fecha Contratación</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($docentes as $d)
                        <tr>
                            <td><code>{{ $d->codigo_docente }}</code></td>
                            <td style="font-weight: 500;">{{ $d->persona ? $d->persona->nombres : 'N/A' }}</td>
                            <td style="font-weight: 500;">{{ $d->persona ? $d->persona->apellidos : 'N/A' }}</td>
                            <td>{{ $d->persona ? $d->persona->numero_documento : 'N/A' }}</td>
                            <td>{{ $d->grado_academico ?? 'N/A' }}</td>
                            <td>{{ $d->fecha_contratacion ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $d->estado === 'Activo' ? 'badge-success' : 'badge-danger' }}">
                                    {{ $d->estado }}
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-outline btn-sm" title="Editar Docente" style="padding: 4px 8px; color: var(--accent-blue); border-color: var(--accent-blue);" data-record="{{ json_encode($d) }}" onclick="openEditModal(this)">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 30px;">No hay docentes registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Registrar Docente -->
    <div class="modal-backdrop" id="register-modal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size: 1.25rem;"><i class="bi bi-person-plus-fill" style="color: var(--accent-cyan);"></i> Registrar Nuevo Docente</h3>
                <button onclick="closeModal()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.5rem;"><i class="bi bi-x"></i></button>
            </div>
            <form action="{{ route('admin.docentes.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Datos de la Persona -->
                    <h4 style="margin-bottom: 15px; font-size: 1rem; color: var(--text-secondary);">Datos Personales</h4>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="tipo_documento">Tipo Documento</label>
                            <select id="tipo_documento" name="tipo_documento" class="form-control" required>
                                <option value="CI">C.I.</option>
                                <option value="Pasaporte">Pasaporte</option>
                                <option value="CE">C.E.</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="numero_documento">Número Documento</label>
                            <input type="text" id="numero_documento" name="numero_documento" class="form-control" required>
                        </div>
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
                    
                    <hr style="border: none; border-top: 1px solid var(--border-color); margin: 20px 0;">
                    
                    <!-- Datos de Docente -->
                    <h4 style="margin-bottom: 15px; font-size: 1rem; color: var(--text-secondary);">Datos Académicos</h4>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="codigo_docente">Código Docente</label>
                            <input type="text" id="codigo_docente" name="codigo_docente" class="form-control" placeholder="Ej: DOC-001" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="grado_academico">Grado Académico</label>
                            <input type="text" id="grado_academico" name="grado_academico" class="form-control" placeholder="Lic., Ing., Mgr., etc.">
                        </div>
                    </div>
                    
                    <hr style="border: none; border-top: 1px solid var(--border-color); margin: 20px 0;">
                    
                    <!-- Datos de Usuario -->
                    <h4 style="margin-bottom: 15px; font-size: 1rem; color: var(--text-secondary);">Acceso al Sistema</h4>
                    <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" id="crear_usuario" name="crear_usuario" value="1" onchange="toggleUsuarioFields()">
                        <label for="crear_usuario" style="margin: 0;">Crear cuenta de usuario para este docente</label>
                    </div>
                    
                    <div id="usuario_fields" style="display: none;">
                        <div class="form-group">
                            <label class="form-label" for="username">Nombre de Usuario (Username)</label>
                            <input type="text" id="username" name="username" class="form-control" placeholder="Ej: j.perez">
                            <small style="color: var(--text-muted); display: block; margin-top: 5px;">La contraseña por defecto será su Número de Documento.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar Docente</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal Editar Docente -->
    <div class="modal-backdrop" id="edit-modal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size: 1.25rem;"><i class="bi bi-pencil-square" style="color: var(--accent-blue);"></i> Editar Docente</h3>
                <button onclick="closeEditModal()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.5rem;"><i class="bi bi-x"></i></button>
            </div>
            <form id="edit-form" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Datos de la Persona -->
                    <h4 style="margin-bottom: 15px; font-size: 1rem; color: var(--text-secondary);">Datos Personales</h4>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="edit_tipo_documento">Tipo Documento</label>
                            <select id="edit_tipo_documento" name="tipo_documento" class="form-control" required>
                                <option value="CI">C.I.</option>
                                <option value="Pasaporte">Pasaporte</option>
                                <option value="CE">C.E.</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit_numero_documento">Número Documento</label>
                            <input type="text" id="edit_numero_documento" name="numero_documento" class="form-control" required>
                        </div>
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
                    
                    <hr style="border: none; border-top: 1px solid var(--border-color); margin: 20px 0;">
                    
                    <!-- Datos de Docente -->
                    <h4 style="margin-bottom: 15px; font-size: 1rem; color: var(--text-secondary);">Datos Académicos</h4>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="edit_codigo_docente">Código Docente</label>
                            <input type="text" id="edit_codigo_docente" name="codigo_docente" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit_grado_academico">Grado Académico</label>
                            <input type="text" id="edit_grado_academico" name="grado_academico" class="form-control">
                        </div>
                    </div>
                    <div class="grid-2">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" for="edit_fecha_contratacion">Fecha Contratación</label>
                            <input type="date" id="edit_fecha_contratacion" name="fecha_contratacion" class="form-control">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" for="edit_estado">Estado</label>
                            <select id="edit_estado" name="estado" class="form-control" required>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                                <option value="Suspendido">Suspendido</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Docente</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function openModal() {
            document.getElementById('register-modal').classList.add('open');
        }

        function closeModal() {
            document.getElementById('register-modal').classList.remove('open');
        }
        
        function toggleUsuarioFields() {
            const isChecked = document.getElementById('crear_usuario').checked;
            document.getElementById('usuario_fields').style.display = isChecked ? 'block' : 'none';
        }

        function openEditModal(btn) {
            let record = JSON.parse(btn.getAttribute('data-record'));
            let persona = record.persona || {};
            document.getElementById('edit-form').action = '/admin/docentes/update/' + record.id_docente;
            document.getElementById('edit_tipo_documento').value = persona.tipo_documento || 'CI';
            document.getElementById('edit_numero_documento').value = persona.numero_documento || '';
            document.getElementById('edit_nombres').value = persona.nombres || '';
            document.getElementById('edit_apellidos').value = persona.apellidos || '';
            document.getElementById('edit_fecha_nacimiento').value = persona.fecha_nacimiento ? persona.fecha_nacimiento.split(' ')[0] : '';
            document.getElementById('edit_telefono').value = persona.telefono || '';
            document.getElementById('edit_email').value = persona.email || '';
            document.getElementById('edit_direccion').value = persona.direccion || '';
            document.getElementById('edit_codigo_docente').value = record.codigo_docente || '';
            document.getElementById('edit_grado_academico').value = record.grado_academico || '';
            document.getElementById('edit_fecha_contratacion').value = record.fecha_contratacion ? record.fecha_contratacion.split(' ')[0] : '';
            document.getElementById('edit_estado').value = record.estado || '';
            document.getElementById('edit-modal').classList.add('open');
        }
        function closeEditModal() {
            document.getElementById('edit-modal').classList.remove('open');
        }
    </script>
@endsection
