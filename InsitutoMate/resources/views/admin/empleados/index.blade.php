@extends('layouts.app')

@section('title', 'Gestión de Empleados | Instituto de Matemáticas UPDS')
@section('page_title', 'Gestión de Empleados')
@section('page_description', 'Administración de personal administrativo y creación de cuentas')

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
            <i class="bi bi-person-plus-fill"></i> Registrar Empleado
        </button>
    </div>

    <div class="grid-card">
        <div class="grid-card-title">
            <span><i class="bi bi-briefcase-fill" style="color: var(--accent-cyan);"></i> Empleados Registrados</span>
        </div>
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Doc. Identidad</th>
                        <th>Cargo</th>
                        <th>Fecha Contratación</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($empleados as $e)
                        <tr>
                            <td><code>{{ $e->codigo_empleado }}</code></td>
                            <td style="font-weight: 500;">{{ $e->persona ? $e->persona->nombres : 'N/A' }}</td>
                            <td style="font-weight: 500;">{{ $e->persona ? $e->persona->apellidos : 'N/A' }}</td>
                            <td>{{ $e->persona ? $e->persona->numero_documento : 'N/A' }}</td>
                            <td>{{ $e->cargo ? $e->cargo->nombre : 'N/A' }}</td>
                            <td>{{ $e->fecha_contratacion ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $e->estado === 'Activo' ? 'badge-success' : 'badge-danger' }}">
                                    {{ $e->estado }}
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-outline btn-sm" title="Editar Empleado" style="padding: 4px 8px; color: var(--accent-blue); border-color: var(--accent-blue);" data-record="{{ json_encode($e) }}" onclick="openEditModal(this)">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 30px;">No hay empleados registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Registrar Empleado -->
    <div class="modal-backdrop" id="register-modal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size: 1.25rem;"><i class="bi bi-person-plus-fill" style="color: var(--accent-cyan);"></i> Registrar Nuevo Empleado</h3>
                <button onclick="closeModal()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.5rem;"><i class="bi bi-x"></i></button>
            </div>
            <form action="{{ route('admin.empleados.store') }}" method="POST">
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
                    
                    <!-- Datos de Empleado -->
                    <h4 style="margin-bottom: 15px; font-size: 1rem; color: var(--text-secondary);">Datos Laborales</h4>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="codigo_empleado">Código Empleado</label>
                            <input type="text" id="codigo_empleado" name="codigo_empleado" class="form-control" placeholder="Ej: EMP-001" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="id_cargo">Cargo</label>
                            <select id="id_cargo" name="id_cargo" class="form-control" required>
                                <option value="">Seleccione un cargo</option>
                                @foreach($cargos as $cargo)
                                    <option value="{{ $cargo->id_cargo }}">{{ $cargo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="fecha_contratacion">Fecha Contratación</label>
                            <input type="date" id="fecha_contratacion" name="fecha_contratacion" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="tipo_contrato">Tipo Contrato</label>
                            <input type="text" id="tipo_contrato" name="tipo_contrato" class="form-control" placeholder="Ej: Fijo, Eventual">
                        </div>
                    </div>
                    
                    <hr style="border: none; border-top: 1px solid var(--border-color); margin: 20px 0;">
                    
                    <!-- Datos de Usuario -->
                    <h4 style="margin-bottom: 15px; font-size: 1rem; color: var(--text-secondary);">Acceso al Sistema</h4>
                    <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" id="crear_usuario" name="crear_usuario" value="1" onchange="toggleUsuarioFields()">
                        <label for="crear_usuario" style="margin: 0;">Crear cuenta de usuario para este empleado</label>
                    </div>
                    
                    <div id="usuario_fields" style="display: none;">
                        <div class="grid-2">
                            <div class="form-group">
                                <label class="form-label" for="username">Nombre de Usuario (Username)</label>
                                <input type="text" id="username" name="username" class="form-control" placeholder="Ej: j.perez">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="id_rol">Rol en el Sistema</label>
                                <select id="id_rol" name="id_rol" class="form-control">
                                    <option value="">Seleccione un rol</option>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id_rol }}">{{ $rol->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <small style="color: var(--text-muted); display: block;">La contraseña por defecto será su Número de Documento.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar Empleado</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal Editar Empleado -->
    <div class="modal-backdrop" id="edit-modal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size: 1.25rem;"><i class="bi bi-pencil-square" style="color: var(--accent-blue);"></i> Editar Empleado</h3>
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
                    
                    <!-- Datos de Empleado -->
                    <h4 style="margin-bottom: 15px; font-size: 1rem; color: var(--text-secondary);">Datos Laborales</h4>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="edit_codigo_empleado">Código Empleado</label>
                            <input type="text" id="edit_codigo_empleado" name="codigo_empleado" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit_id_cargo">Cargo</label>
                            <select id="edit_id_cargo" name="id_cargo" class="form-control" required>
                                @foreach($cargos as $cargo)
                                    <option value="{{ $cargo->id_cargo }}">{{ $cargo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="edit_fecha_contratacion">Fecha Contratación</label>
                            <input type="date" id="edit_fecha_contratacion" name="fecha_contratacion" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit_tipo_contrato">Tipo Contrato</label>
                            <input type="text" id="edit_tipo_contrato" name="tipo_contrato" class="form-control">
                        </div>
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
                <div class="modal-footer">
                    <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Empleado</button>
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
            document.getElementById('edit-form').action = '/admin/empleados/update/' + record.id_empleado;
            document.getElementById('edit_tipo_documento').value = persona.tipo_documento || 'CI';
            document.getElementById('edit_numero_documento').value = persona.numero_documento || '';
            document.getElementById('edit_nombres').value = persona.nombres || '';
            document.getElementById('edit_apellidos').value = persona.apellidos || '';
            document.getElementById('edit_fecha_nacimiento').value = persona.fecha_nacimiento ? persona.fecha_nacimiento.split(' ')[0] : '';
            document.getElementById('edit_telefono').value = persona.telefono || '';
            document.getElementById('edit_email').value = persona.email || '';
            document.getElementById('edit_direccion').value = persona.direccion || '';
            document.getElementById('edit_codigo_empleado').value = record.codigo_empleado || '';
            document.getElementById('edit_id_cargo').value = record.id_cargo || '';
            document.getElementById('edit_tipo_contrato').value = record.tipo_contrato || '';
            document.getElementById('edit_fecha_contratacion').value = record.fecha_contratacion ? record.fecha_contratacion.split(' ')[0] : '';
            document.getElementById('edit_estado').value = record.estado || 'Activo';
            document.getElementById('edit-modal').classList.add('open');
        }
        function closeEditModal() {
            document.getElementById('edit-modal').classList.remove('open');
        }
    </script>
@endsection
